<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------

use function \tecla\util\viewParseDate;

$app->get("/reports", function () use ($app) {
    $auth = $app['auth'];
    $auth->requireRole('admin');
    $data = array();
    return $this->render("views/report/index.php with views/layout.php", $data);
});

$app->bind("/reports/guest-games", function () use ($app) {
    $auth = $app['auth'];
    $auth->requireRole('admin');

    $dataservice = $app['dataservice'];
    $gameservice = $app['gameservice'];
    $start = isset($_REQUEST['start']) ? viewParseDate($_REQUEST['start']) : (new \DateTimeImmutable())->modify('first day of last month');
    $end = isset($_REQUEST['end']) ? viewParseDate($_REQUEST['end']) : (new \DateTimeImmutable())->modify('last day of last month');
    $games = $dataservice->loadGuestGames($start, $end);
    $userLookup = $app['userservice']->getUserLookupMap();

    $data = array(
        'start' => $start,
        'end' => $end,
        'games' => $games,
        'userLookup' => $userLookup,
    );
    return $this->render("views/report/guest-games.php with views/layout.php", $data);
});

$app->bind("/reports/times-usage", function () use ($app) {
    $auth = $app['auth'];
    $auth->requireRole('admin');

    $gameservice = $app['gameservice'];
    $start = isset($_REQUEST['start']) ? viewParseDate($_REQUEST['start']) : (new \DateTimeImmutable())->modify('first day of last month');
    $end = isset($_REQUEST['end']) ? viewParseDate($_REQUEST['end']) : (new \DateTimeImmutable())->modify('last day of last month');
    $stats = $gameservice->getUsageStatistics($start, $end);

    $data = array(
        'start' => $start,
        'end' => $end,
        'stats' => $stats,
    );
    return $this->render("views/report/times-usage.php with views/layout.php", $data);
});
