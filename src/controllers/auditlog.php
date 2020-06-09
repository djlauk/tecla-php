<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------

$app->get("/auditlog/list/:page", function ($params) use ($app) {
    $auth = $app['auth'];
    $auth->requireRole('admin');

    $page = $params['page'];
    if ($page <= 0) {
        $page = 1;
    }
    $pageSize = $app['config.auditlog/pagesize'];
    $offset = ($page - 1) * $pageSize;
    $auditlogdao = $app['auditlogdao'];
    $entries = $auditlogdao->loadPage($pageSize, $offset);
    $numEntries = $auditlogdao->count();
    $numPages = ceil($numEntries / $pageSize);
    $data = array(
        'entries' => $entries,
        'pageSize' => $pageSize,
        'offset' => $offset,
        'numEntries' => $numEntries,
        'page' => $page,
        'numPages' => $numPages,
        'userLookup' => $app['userservice']->getUserLookupMap(),
    );
    return $this->render("views/auditlog/list.php with views/layout.php", $data);
});

$app->get("/auditlog/view/:id", function ($params) use ($app) {
    $auth = $app['auth'];
    $auth->requireRole('admin');

    $pageSize = $app['config.auditlog/pagesize'];
    $auditlogdao = $app['auditlogdao'];
    $entry = $auditlogdao->loadById($params['id']);

    $data = array(
        'entry' => $entry,
        'userLookup' => $app['userservice']->getUserLookupMap(),
    );
    return $this->render("views/auditlog/view.php with views/layout.php", $data);
});
