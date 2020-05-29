<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------

namespace tecla;

$app->service('db', function () use ($app) {
    $cnx = $app['config.pdo/dsn'];
    $user = $app['config.pdo/user'];
    $pass = $app['config.pdo/password'];
    try {
        $pdo = new \PDO($cnx, $user, $pass);
        $db = new \tecla\data\DBAccess($pdo);
        if ($app['config.debug']) {
            $db->enableDebug();
        }
        return $db;
    } catch (PDOException $e) {
        die('Cannot connect to database: ' . $e->getMessage());
    }
});

$app->service('gamedao', function () use ($app) {
    $db = $app['db'];
    return new \tecla\data\GameDAO($db, $app['config.maxgames']);
});

$app->service('templatedao', function () use ($app) {
    $db = $app['db'];
    return new \tecla\data\TemplateDAO($db);
});

$app->service('userdao', function () use ($app) {
    $db = $app['db'];
    return new \tecla\data\UserDAO($db);
});
