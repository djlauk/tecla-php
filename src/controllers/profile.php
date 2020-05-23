<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------

namespace tecla;

$app->get("/profile", function ($params) use ($app) {
    $auth = $app['auth'];
    $auth->requireLogin();
    $user = $auth->getUser();

    $data = array(
        'user' => $user,
    );
    return $this->render("views/profile/index.php with views/layout.php", $data);
});

$app->get("/profile/edit", function ($params) use ($app) {
    $auth = $app['auth'];
    $auth->requireLogin();
    $user = $auth->getUser();

    $data = array(
        'user' => $user,
    );
    return $this->render("views/profile/edit.php with views/layout.php", $data);
});

$app->post("/profile/save", function () use ($app) {
    $auth = $app['auth'];
    $auth->requireLogin();
    $user = $auth->getUser();

    $data = array(
        'user' => $user,
    );

    $dao = $app['userdao'];
    // if email is updated, remove verification status
    if ($_POST['email'] !== $user->email) {
        $user->verifiedOn = null;

        // TODO: request verification
    }
    $user->fromArray($_POST);
    $dao->update($user);

    $app->reroute("/profile");
});
