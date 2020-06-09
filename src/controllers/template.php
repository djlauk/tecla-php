<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------

$app->bind("/templates", function ($params) use ($app) {
    $auth = $app['auth'];
    $auth->requireRole('admin');

    $dao = $app['templatedao'];
    $items = $dao->loadAll();
    $data = array(
        'items' => $items,
    );
    return $this->render("views/template/list.php with views/layout.php", $data);
});

$app->bind("/templates/add", function ($params) use ($app) {
    $auth = $app['auth'];
    $auth->requireRole('admin');

    $data = array();
    return $this->render("views/template/add.php with views/layout.php", $data);
});

$app->get("/templates/edit/:id", function ($params) use ($app) {
    $auth = $app['auth'];
    $auth->requireRole('admin');

    $dao = $app['templatedao'];
    $id = $params['id'];
    $item = $dao->loadById($id);

    $data = array(
        "id" => $id,
        "item" => $item,
    );

    return $this->render("views/template/edit.php with views/layout.php", $data);
});

$app->post("/templates/create", function ($params) use ($app) {
    $auth = $app['auth'];
    $auth->requireRole('admin');

    $item = tecla\data\Template::createFromArray($_POST);
    $newId = $app['templatedao']->insert($item);
    $auth->logAction('TEMPLATE:CREATE', "TEMPLATE:$newId");
    $app->reroute('/templates');
});

$app->post("/templates/save", function ($params) use ($app) {
    $auth = $app['auth'];
    $auth->requireRole('admin');

    $newItem = tecla\data\Template::createFromArray($_POST);
    $dao = $app['templatedao'];
    $dao->update($newItem);
    $auth->logAction('TEMPLATE:UPDATE', "TEMPLATE:{$newItem->id}");
    $app->reroute('/templates');
});

$app->get('/templates/generate-games', function () use ($app) {
    $auth = $app['auth'];
    $auth->requireRole('admin');

    $gamedao = $app['gamedao'];
    $g = $gamedao->getLastGame();
    $t = is_null($g) ? new \DateTimeImmutable() : \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $g->startTime);
    $data = array(
        'firstDay' => $t->modify('+1 day')->format('Y-m-d'),
        'lastDay' => $t->modify('+1 day')->modify('last day of this month')->format('Y-m-d'),
        'templates' => $app['templatedao']->loadAll(),
        'problem' => false,
    );
    return $this->render("views/template/generate-games.php with views/layout.php", $data);
});

$app->post('/templates/generate-games', function () use ($app) {
    $auth = $app['auth'];
    $auth->requireRole('admin');

    $firstDay = $_POST['firstDay'];
    $lastDay = $_POST['lastDay'];
    $templates = $_POST['templates'];
    $numGenerated = $app['gameservice']->generateGames($firstDay, $lastDay, $templates);
    $data = array(
        'firstDay' => $firstDay,
        'lastDay' => $lastDay,
        'count' => $numGenerated,
        'templates' => $app['templatedao']->loadAll(),
    );

    if ($numGenerated === false) {
        $data['problem'] = true;
        return $this->render("views/template/generate-games.php with views/layout.php", $data);
    }
    return $this->render("views/template/generate-success.php with views/layout.php", $data);
});

$app->get('/templates/delete/:id', function ($params) use ($app) {
    $auth = $app['auth'];
    $auth->requireRole('admin');

    $templatedao = $app['templatedao'];
    $id = $params['id'];
    $template = $templatedao->loadById($id);
    $data = array(
        'item' => $template,
    );
    return $this->render("views/template/delete.php with views/layout.php", $data);
});

$app->post('/templates/delete', function () use ($app) {
    $auth = $app['auth'];
    $auth->requireRole('admin');

    $templatedao = $app['templatedao'];
    $id = $_POST['id'];
    $template = $templatedao->loadById($id);
    $template->fromArray($_POST);
    $templatedao->delete($template);
    $auth->logAction('TEMPLATE:DELETE', "TEMPLATE:$id");

    $this->reroute('/templates');
});
