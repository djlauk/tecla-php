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
    $user = $app['auth']->getUser();
    $userId = is_null($user) ? null : $user->id;
    $data = array(
        'user' => $user,
        'nextGames' => $app['gameservice']->loadFutureGamesForUser($userId),
        'games' => $app['gameservice']->loadAllGamesAfterToday(),
        'userLookup' => $app['userservice']->getUserLookupMap(),
    );
    return $this->render("views/home/index.php with views/layout.php", $data);
});
