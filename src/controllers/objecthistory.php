<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------

namespace tecla;

$app->get("/history/:type/:id", function ($params) use ($app) {
    $auth = $app['auth'];
    $auth->requireRole('admin');

    $type = $params['type'];
    $id = $params['id'];
    $data = array(
        'type' => $type,
        'id' => $id,
        'entries' => $app['dataservice']->loadHistoryOfObject($type, $id),
    );
    return $this->render("views/history/view.php with views/layout.php", $data);
});
