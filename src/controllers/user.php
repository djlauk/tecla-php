<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------

namespace tecla;

$app->get("/users", function () use ($app) {
    $auth = $app['auth'];
    $auth->requireRole('admin');

    $data = array(
        'users' => $app['userdao']->loadAll(),
    );
    return $this->render("views/user/list.php with views/layout.php", $data);
});

$app->get("/users/view/:id", function ($params) use ($app) {
    $auth = $app['auth'];
    $auth->requireRole('admin');

    $id = $params['id'];
    $user = $app['userdao']->loadById($id);

    $data = array(
        'id' => $id,
        'user' => $user,
    );
    return $this->render("views/user/view.php with views/layout.php", $data);
});

$app->get("/users/edit/:id", function ($params) use ($app) {
    $auth = $app['auth'];
    $auth->requireRole('admin');

    $id = $params['id'];
    $user = $app['userdao']->loadById($id);

    $data = array(
        'id' => $id,
        'user' => $user,
    );
    return $this->render("views/user/edit.php with views/layout.php", $data);
});

$app->post("/users/save", function () use ($app) {
    $auth = $app['auth'];
    $auth->requireRole('admin');

    $dao = $app['userdao'];
    $id = $_POST['id'];
    $user = $dao->loadById($id);
    // if email is updated, remove verification status
    if ($_POST['email'] !== $user->email) {
        $user->verifiedOn = null;

        // TODO: request verification
    }
    $user->fromArray($_POST);
    $dao->update($user);

    $app->reroute("/users");
});

$app->get("/users/add", function () use ($app) {
    $auth = $app['auth'];
    $auth->requireRole('admin');

    $data = array(
        'role' => $app['config.defaultrole'],
    );
    return $this->render("views/user/add.php with views/layout.php", $data);
});

$app->post("/users/create", function () use ($app) {
    $auth = $app['auth'];
    $auth->requireRole('admin');

    $item = \tecla\data\User::createFromArray($_POST);
    $item->passwordHash = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $newId = $app['userdao']->insert($item);
    $app->reroute('/users');
});
