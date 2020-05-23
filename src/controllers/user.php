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
