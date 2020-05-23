<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------

namespace tecla;

$app->bind("/", function () use ($app) {
    $today = strftime('%Y-%m-%d', time());
    $user = $app['auth']->getUser();
    $userId = is_null($user) ? null : $user->id;
    $data = array(
        'user' => $user,
        'nextGames' => $app['gamedao']->loadFutureGamesForUser($userId),
        'games' => $app['gamedao']->loadAllAfter($today),
    );
    return $this->render("views/home/index.php with views/layout.php", $data);
});
