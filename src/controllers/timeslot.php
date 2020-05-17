<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------

$app->bind("/timeslots", function ($params) use ($app) {
    $dao = $app['timeslotdao'];
    $items = $dao->loadAll();
    $data = array(
        'items' => $items,
    );
    return $this->render("views/timeslots/list.php with views/layout.php", $data);
});

$app->bind("/timeslots/add", function ($params) use ($app) {
    $data = array();
    return $this->render("views/timeslots/add.php with views/layout.php", $data);
});

$app->get("/timeslots/edit/:id", function ($params) use ($app) {
    $dao = $app['timeslotdao'];
    $id = $params['id'];
    $item = $dao->loadById($id);

    $data = array(
        "id" => $id,
        "item" => $item,
    );

    return $this->render("views/timeslots/edit.php with views/layout.php", $data);
});

$app->post("/timeslots/create", function ($params) use ($app) {
    $item = tecla\data\Timeslot::createFromArray($_POST);
    $newId = $app['timeslotdao']->insert($item);
    $app->reroute('/timeslots');
});

$app->post("/timeslots/save", function ($params) use ($app) {
    $newItem = tecla\data\Timeslot::createFromArray($_POST);
    $dao = $app['timeslotdao'];
    $dao->update($newItem);
    $app->reroute('/timeslots');
});
