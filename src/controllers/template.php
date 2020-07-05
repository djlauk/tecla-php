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

    $data = $app['dataservice'];
    $items = $data->loadAllTemplates();
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

    $data = $app['dataservice'];
    $id = $params['id'];
    $item = $data->loadTemplateById($id);

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
    $newId = $app['dataservice']->insertTemplate($item);
    $auth->logAction('TEMPLATE:CREATE', "TEMPLATE:$newId");
    $app->reroute('/templates');
});

$app->post("/templates/save", function ($params) use ($app) {
    $auth = $app['auth'];
    $auth->requireRole('admin');

    $data = $app['dataservice'];
    $obj = $data->loadTemplateById($_POST['id']);
    $obj->fromArray($_POST);
    $data->updateTemplate($obj);
    $auth->logAction('TEMPLATE:UPDATE', "TEMPLATE:{$obj->id}");
    $app->reroute('/templates');
});

$app->get('/templates/generate-games', function () use ($app) {
    $auth = $app['auth'];
    $auth->requireRole('admin');

    $g = $app['dataservice']->getLastGame();
    $t = is_null($g) ? new \DateTimeImmutable() : $g->startTime;
    $data = array(
        'firstDay' => $t->modify('+1 day'),
        'lastDay' => $t->modify('+1 day')->modify('last day of this month'),
        'templates' => $app['dataservice']->loadAllTemplates(),
        'problem' => false,
    );
    return $this->render("views/template/generate-games.php with views/layout.php", $data);
});

$app->post('/templates/generate-games', function () use ($app) {
    $auth = $app['auth'];
    $auth->requireRole('admin');

    $firstDay = \tecla\util\viewParseDate($_POST['firstDay']);
    $lastDay = \tecla\util\viewParseDate($_POST['lastDay']);
    $templates = $_POST['templates'] ?? array();
    $numGenerated = $app['gameservice']->generateGames($firstDay, $lastDay, $templates);
    $data = array(
        'firstDay' => $firstDay,
        'lastDay' => $lastDay,
        'count' => $numGenerated,
        'templates' => $app['dataservice']->loadAllTemplates(),
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

    $data = $app['dataservice'];
    $id = $params['id'];
    $template = $data->loadTemplateById($id);
    $data = array(
        'item' => $template,
    );
    return $this->render("views/template/delete.php with views/layout.php", $data);
});

$app->post('/templates/delete', function () use ($app) {
    $auth = $app['auth'];
    $auth->requireRole('admin');

    $data = $app['dataservice'];
    $id = $_POST['id'];
    $template = $data->loadTemplateById($id);
    $template->fromArray($_POST);
    $data->deleteTemplate($template);
    $auth->logAction('TEMPLATE:DELETE', "TEMPLATE:$id");

    $this->reroute('/templates');
});
