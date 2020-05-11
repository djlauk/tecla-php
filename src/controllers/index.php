<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------

namespace tecla\controllers;

require __DIR__ . '/timeslot.php';

$app->bind("/", function () use ($app) {
    $data = array();
    return $this->render("views/index.php with views/layout.php", $data);
});
