<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------

namespace tecla;

define('ROLES', array(
    '' => 0,
    'guest' => 1,
    'member' => 10,
    'admin' => 100,
));

define('MAX_LOGIN_TRIES', 10); // maximum number of tries before we temporarily lock the account
define('LOCK_SECONDS', 300); // lock user for 5 minutes

class AuthService
{
    private $user = null;
    private $data;
    private $session;
    private $limeApp;
    public function __construct(\tecla\DataService &$data, \Lime\Session &$session, \Lime\App &$app)
    {
        $this->data = $data;
        $this->session = $session;
        if ($this->session->read('userid', false)) {
            $this->user = $this->data->loadUserById($this->session->read('userid'));
        }
        $this->limeApp = $app;
    }

    public function logAction($action, $object = null, $message = null, $user_id = null)
    {
        if (is_null($user_id)) {
            $user_id = $this->user->id;
        }
        $entry = \tecla\data\Auditlog::createFromArray(array(
            'action' => $action,
            'user_id' => $user_id,
            'object' => $object,
            'message' => $message,
        ));
        $this->data->insertAuditlog($entry);
    }

    public function login($email, $password)
    {
        $user = $this->data->loadUserByEmail($email);
        if (is_null($user)) {
            return false;
        }
        if (!is_null($user->disabledOn)) {
            return false;
        }
        // handle user locking (temporary disabling to slow down brute force attacks)
        if (!is_null($user->lockedUntil)) {
            // still locked?
            if ($user->lockedUntil->getTimestamp() >= time()) {
                return false;
            }
            // lock expired. Let's reset.
            $user->lockedUntil = null;
            $user->failedLogins = 0;
        }

        if (password_verify($password, $user->passwordHash) === false) {
            $this->logAction('LOGIN:FAIL', 'USER:' . $user->id, "failed login for user '$email' from {$_SERVER['REMOTE_ADDR']}", $user->id);
            $user->failedLogins++;
            if ($user->failedLogins >= MAX_LOGIN_TRIES) {
                $user->lockedUntil = \tecla\util\dbTime(time() + LOCK_SECONDS);
                $this->logAction('USER:LOCK', 'USER:' . $user->id, "locked user after exceeding maximum failures until {$user->lockedUntil}", $user->id);
            }
            $this->data->updateUser($user);
            sleep(1); // further delay of brute force attacks -- 1 sec is bearable for humans
            return false;
        }

        $this->logAction('LOGIN:SUCCESS', 'USER:' . $user->id, "user '$email' logged in from {$_SERVER['REMOTE_ADDR']}", $user->id);
        $user->lockedUntil = null;
        $user->failedLogins = 0;
        $user->lastLoginOn = \tecla\util\dbTime();
        $user->lastLoginFrom = $_SERVER['REMOTE_ADDR'];
        $this->data->updateUser($user);

        $this->user = $user;
        $this->session->write('userid', $user->id);
        return true;
    }

    public function isLoggedIn()
    {
        return !is_null($this->user);
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getRole()
    {
        return is_null($this->user) ? '' : $this->user->role;
    }

    public function hasRole($roleName)
    {
        $r = $this->getRole();
        return ROLES[$r] >= ROLES[$roleName];
    }

    /**
     * Convenience function for controllers
     */
    public function requireRole($roleName)
    {
        if ($this->hasRole($roleName)) {
            return;
        }

        die($this->limeApp->render('views/auth/no-permission.php with views/layout.php'));
    }

    /**
     * Convenience function for controllers
     */
    public function requireLogin()
    {
        if ($this->isLoggedIn()) {
            return;
        }

        die($this->limeApp->render('views/auth/no-permission.php with views/layout.php'));
    }

    /**
     * Convenience function for controllers
     */
    public function requireUser($id)
    {
        if ($this->user->id === $id) {
            return;
        }

        die($this->limeApp->render('views/auth/no-permission.php with views/layout.php'));
    }

    public function logout()
    {
        // already logged out
        if (!$this->isLoggedIn()) {
            return;
        }
        $this->logAction('LOGIN:LOGOUT', 'USER:' . $this->user->id, "user '{$this->user->email}' logged out from {$_SERVER['REMOTE_ADDR']}");
        $this->user = null;
        $this->session->destroy();
    }
}

$app->service('auth', function () use ($app) {
    $dataservice = $app['dataservice'];
    $session = $app('session');
    return new AuthService($dataservice, $session, $app);
});
