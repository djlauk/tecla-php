<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------

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
    $start = isset($_REQUEST['start']) ? new \DateTimeImmutable($_REQUEST['start']) : (new \DateTimeImmutable())->modify('first day of this month');
    $end = isset($_REQUEST['end']) ? new \DateTimeImmutable($_REQUEST['end']) : (new \DateTimeImmutable())->modify('last day of this month');
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
