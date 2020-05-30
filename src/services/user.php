<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------

namespace tecla;

class UserService
{
    private $userdao;
    private $limeApp;
    public function __construct(\tecla\data\UserDAO &$userdao, \Lime\App &$app)
    {
        $this->userdao = $userdao;
        $this->limeApp = $app;
    }

    /**
     * checkPasswordRules will throw an exception if $password violates the
     * password complexity rules specified in the configuration.
     */
    public function checkPasswordRules($password)
    {
        $pwRules = $this->limeApp['config.passwordrules'];
        if (!$pwRules['enabled']) {
            return;
        }
        if ($pwRules['minlength'] > 0) {
            if (strlen($password) < $pwRules['minlength']) {
                throw new \Exception("Password too short (minimum {$pwRules['minlength']})");
            }
        }
        // extract character classes
        $containsUppercase = preg_match('/[A-Z]/', $password) === 1;
        $containsLowercase = preg_match('/[a-z]/', $password) === 1;
        $containsNumber = preg_match('/[0-9]/', $password) === 1;
        $containsSpecial = preg_match('/[^a-zA-Z0-9]/', $password) === 1;
        // count character classes
        $numClasses = 0;
        if ($containsUppercase) {
            $numClasses++;
        }
        if ($containsLowercase) {
            $numClasses++;
        }
        if ($containsNumber) {
            $numClasses++;
        }
        if ($containsSpecial) {
            $numClasses++;
        }
        // and compare them
        if ($pwRules['needsUppercase'] && !$containsUppercase) {
            throw new \Exception("Must contain upper case character");
        }
        if ($pwRules['needsLowercase'] && !$containsLowercase) {
            throw new \Exception("Must contain lower case character");
        }
        if ($pwRules['needsNumber'] && !$containsNumber) {
            throw new \Exception("Must contain number");
        }
        if ($pwRules['needsSpecial'] && !$containsSpecial) {
            throw new \Exception("Must contain special character");
        }
        // check restriction for number of character classes
        if ($pwRules['needsNumClasses'] > 0 && $numClasses < $pwRules['needsNumClasses']) {
            throw new \Exception("Need at least {$pwRules['needsNumClasses']} character classes ($numClasses given)");
        }
    }

    public function setPassword(\tecla\data\User &$user, $password)
    {
        $this->checkPasswordRules($password);
        $user->passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $this->userdao->update($user);
        // TODO: add audit log: password set for user
    }

    public function changePassword(\tecla\data\User &$user, $oldPassword, $password)
    {
        if (!password_verify($oldPassword, $user->passwordHash)) {
            throw new \Exception("Wrong password");
            // TODO: add audit log: wrong password for user when changing passwords
        }
        $this->checkPasswordRules($password);
        $user->passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $this->userdao->update($user);
        // TODO: add audit log: password changed for user
    }

    public function getUserLookupMap()
    {
        $users = array();
        foreach ($this->userdao->loadAll() as $u) {
            $users[$u->id] = $u;
        }
        return $users;
    }
}

$app->service('userservice', function () use ($app) {
    return new UserService($app['userdao'], $app);
});
