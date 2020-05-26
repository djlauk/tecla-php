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

define('ISODATE', '%Y-%m-%d');
define('ISODATETIME', '%Y-%m-%dT%H:%M:%S');
define('ISOTIME', '%H:%M');

define('MAX_LOGIN_TRIES', 10); // maximum number of tries before we temporarily lock the account
define('LOCK_SECONDS', 300); // lock user for 5 minutes

class AuthService
{
    private $user = null;
    private $userdao;
    private $gameService;
    private $session;
    private $limeApp;
    public function __construct(\tecla\data\UserDao &$userdao, \tecla\GameService &$gameService, \Lime\Session &$session, \Lime\App &$app)
    {
        $this->userdao = $userdao;
        $this->gameService = $gameService;
        $this->session = $session;
        if ($this->session->read('userid', false)) {
            $this->user = $userdao->loadById($this->session->read('userid'));
        }
        $this->limeApp = $app;
    }

    public function login($email, $password)
    {
        $user = $this->userdao->loadByEmail($email);
        if (is_null($user)) {
            return false;
        }
        if (!is_null($user->disabledOn)) {
            return false;
        }
        // handle user locking (temporary disabling to slow down brute force attacks)
        if (!is_null($user->lockedUntil)) {
            // still locked?
            if ($user->lockedUntil >= strftime(ISODATETIME, time())) {
                return false;
            }
            // lock expired. Let's reset.
            $user->lockedUntil = null;
            $user->failedLogins = 0;
        }

        if (password_verify($password, $user->passwordHash) === false) {
            // TODO: add audit entry: failed login for user $email from $_SERVER['REMOTE_ADDR']
            $user->failedLogins++;
            if ($user->failedLogins >= MAX_LOGIN_TRIES) {
                $user->lockedUntil = strftime(ISODATETIME, time() + 300);
                // TODO: add audit entry: locking user $email for exceeding maximum attempts
            }
            $this->userdao->update($user);
            sleep(1); // further delay of brute force attacks -- 1 sec is bearable for humans
            return false;
        }

        // TODO: add audit entry: user $email logged in from $_SERVER['REMOTE_ADDR']
        $user->lockedUntil = null;
        $user->failedLogins = 0;
        $user->lastLoginOn = strftime(ISODATETIME);
        $user->lastLoginFrom = $_SERVER['REMOTE_ADDR'];
        $this->userdao->update($user);

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
        // TODO: add audit log: user logged out
        $this->user = null;
        $this->session->destroy();
    }

    public function canBookGame(\tecla\data\Game &$game)
    {
        if (!$this->hasRole('member')) {
            return false;
        }
        if ($game->status !== 'available') {
            return false;
        }
        $now = time();
        $start = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $game->startTime);
        if ($start->getTimestamp() < $now) {
            return false;
        }
        if ($this->gameService->isFreeGame($game)) {
            return true;
        }
        if ($this->gameService->isGameScheduledForUser($this->user->id)) {
            return false;
        }
        return true;
    }

    public function canCancelGame(\tecla\data\Game &$game)
    {
        if (!$this->hasRole('member')) {
            return false;
        }
        if ($game->player1_id !== $this->user->id) {
            return false;
        }
        $now = time();
        $start = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $game->startTime);
        if ($start->getTimestamp() < $now) {
            return false;
        }
        return true;
    }
}

$app->service('auth', function () use ($app) {
    $userdao = $app['userdao'];
    $gameservice = $app['gameservice'];
    $session = $app('session');
    return new AuthService($userdao, $gameservice, $session, $app);
});
