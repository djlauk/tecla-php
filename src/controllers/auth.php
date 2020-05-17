<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------

$app->get("/login", function () use ($app) {
    $data = array('problem' => false);
    return $this->render("views/auth/login.php with views/layout.php", $data);
});

$app->post("/login", function () use ($app) {
    $auth = $app['auth'];
    $success = $auth->login($_POST['email'] ?? '', $_POST['password'] ?? '');

    if (!$success) {
        $data = array('problem' => true);
        return $this->render("views/auth/login.php with views/layout.php", $data);
    }
    $app->reroute('/');
});

$app->bind("/logout", function () use ($app) {
    $data = array();
    $app['auth']->logout();
    $app->reroute('/');
});
