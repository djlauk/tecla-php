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
        'users' => $app['dataservice']->loadAllUsers(),
    );
    return $this->render("views/user/list.php with views/layout.php", $data);
});

$app->get("/users/view/:id", function ($params) use ($app) {
    $auth = $app['auth'];
    $auth->requireRole('admin');

    $id = $params['id'];
    $user = $app['dataservice']->loadUserById($id);

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
    $user = $app['dataservice']->loadUserById($id);

    $data = array(
        'id' => $id,
        'user' => $user,
    );
    return $this->render("views/user/edit.php with views/layout.php", $data);
});

$app->post("/users/save", function () use ($app) {
    $auth = $app['auth'];
    $auth->requireRole('admin');

    $data = $app['dataservice'];
    $id = $_POST['id'];
    $user = $data->loadUserById($id);
    // if email is updated, remove verification status
    if ($_POST['email'] !== $user->email) {
        $user->verifiedOn = null;

        // TODO: request verification
    }
    $user->fromArray($_POST);
    $data->updateUser($user);
    $auth->logAction('USER:UPDATE', "USER:$id");

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
    $newId = $app['dataservice']->insertUser($item);
    $auth->logAction('USER:CREATE', "USER:$newId");
    $app->reroute('/users');
});

$app->get("/users/enable/:id", function ($params) use ($app) {
    $auth = $app['auth'];
    $auth->requireRole('admin');

    $id = $params['id'];
    $user = $app['dataservice']->loadUserById($id);

    $data = array(
        'id' => $id,
        'user' => $user,
    );

    return $this->render("views/user/enable.php with views/layout.php", $data);
});

$app->post("/users/enable", function () use ($app) {
    $auth = $app['auth'];
    $auth->requireRole('admin');

    $data = $app['dataservice'];
    $id = $_POST['id'];
    $user = $data->loadUserById($id);
    $user->fromArray($_POST);
    if ($_POST['enabled'] === 'enabled') {
        $user->disabledOn = null;
        $action = 'USER:ENABLE';
    } else {
        $user->disabledOn = \tecla\util\dbTime();
        $action = 'USER:DISABLE';
    }
    $data->updateUser($user);
    $auth->logAction($action, "USER:$id");
    $app->reroute('/users');
});
