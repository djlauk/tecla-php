<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------

$app->bind("/timeslots", function ($params) use ($app) {
    $auth = $app['auth'];
    $auth->requireRole('admin');

    $dao = $app['timeslotdao'];
    $items = $dao->loadAll();
    $data = array(
        'items' => $items,
    );
    return $this->render("views/timeslots/list.php with views/layout.php", $data);
});

$app->bind("/timeslots/add", function ($params) use ($app) {
    $auth = $app['auth'];
    $auth->requireRole('admin');

    $data = array();
    return $this->render("views/timeslots/add.php with views/layout.php", $data);
});

$app->get("/timeslots/edit/:id", function ($params) use ($app) {
    $auth = $app['auth'];
    $auth->requireRole('admin');

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
    $auth = $app['auth'];
    $auth->requireRole('admin');

    $item = tecla\data\Timeslot::createFromArray($_POST);
    $newId = $app['timeslotdao']->insert($item);
    $app->reroute('/timeslots');
});

$app->post("/timeslots/save", function ($params) use ($app) {
    $auth = $app['auth'];
    $auth->requireRole('admin');

    $newItem = tecla\data\Timeslot::createFromArray($_POST);
    $dao = $app['timeslotdao'];
    $dao->update($newItem);
    $app->reroute('/timeslots');
});

$app->get('/timeslots/generate-games', function () use ($app) {
    $auth = $app['auth'];
    $auth->requireRole('admin');

    $gamedao = $app['gamedao'];
    $g = $gamedao->getLastGame();
    $t = is_null($g) ? new \DateTimeImmutable() : \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $g->startTime);
    $data = array(
        'firstDay' => $t->modify('+1 day')->format('Y-m-d'),
        'lastDay' => $t->modify('+1 day')->modify('last day of this month')->format('Y-m-d'),
        'problem' => false,
    );
    return $this->render("views/timeslots/generate-games.php with views/layout.php", $data);
});

$app->post('/timeslots/generate-games', function () use ($app) {
    $auth = $app['auth'];
    $auth->requireRole('admin');

    $firstDay = $_POST['firstDay'];
    $lastDay = $_POST['lastDay'];
    $numGenerated = $app['gameservice']->generateGames($firstDay, $lastDay);
    $data = array(
        'firstDay' => $firstDay,
        'lastDay' => $lastDay,
        'count' => $numGenerated,
    );

    if ($numGenerated === false) {
        $data['problem'] = true;
        return $this->render("views/timeslots/generate-games.php with views/layout.php", $data);
    }
    return $this->render("views/timeslots/generate-success.php with views/layout.php", $data);
});

$app->get('/timeslots/delete/:id', function ($params) use ($app) {
    $auth = $app['auth'];
    $auth->requireRole('admin');

    $timeslotdao = $app['timeslotdao'];
    $id = $params['id'];
    $timeslot = $timeslotdao->loadById($id);
    $data = array(
        'item' => $timeslot,
    );
    return $this->render("views/timeslots/delete.php with views/layout.php", $data);
});

$app->post('/timeslots/delete', function () use ($app) {
    $auth = $app['auth'];
    $auth->requireRole('admin');

    $timeslotdao = $app['timeslotdao'];
    $id = $_POST['id'];
    $timeslot = $timeslotdao->loadById($id);
    $timeslot->fromArray($_POST);
    $timeslotdao->delete($timeslot);

    $this->reroute('/timeslots');
});
