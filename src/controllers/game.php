<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------

$app->get("/game/view/:id", function ($params) use ($app) {
    $auth = $app['auth'];
    $user = $auth->getUser();
    $data = $app['dataservice'];
    $gameService = $app['gameservice'];
    $id = $params['id'];
    $game = $data->loadGameById($id);
    $player1 = $data->loadUserById($game->player1_id);
    $player2 = $data->loadUserById($game->player2_id);
    $player3 = $data->loadUserById($game->player3_id);
    $player4 = $data->loadUserById($game->player4_id);

    $canBook = $gameService->canBookGame($game);
    $canCancel = $gameService->canCancelGame($game);
    $canEdit = $auth->hasRole('admin');
    $canDelete = $auth->hasRole('admin');
    $nextGames = is_null($user) ? array() : $gameService->loadFutureGamesForUser($user->id);
    $data = array(
        'id' => $id,
        'user' => $user,
        'game' => $game,
        'player1' => $player1,
        'player2' => $player2,
        'player3' => $player3,
        'player4' => $player4,
        'canBook' => $canBook,
        'canCancel' => $canCancel,
        'nextGames' => $nextGames,
    );
    return $this->render("views/game/view.php with views/layout.php", $data);
});

$app->get("/game/book/:id", function ($params) use ($app) {
    $auth = $app['auth'];
    $auth->requireRole('member');

    $user = $auth->getUser();
    $data = $app['dataservice'];
    $gameService = $app['gameservice'];
    $id = $params['id'];
    $game = $data->loadGameById($id);
    $allUsers = $data->loadAllUsersForBooking();

    $canBook = $gameService->canBookGame($game);
    if (!$canBook) {
        return $this->render("views/auth/no-permission.php with views/layout.php");
    }
    $data = array(
        'id' => $id,
        'user' => $user,
        'allUsers' => $allUsers,
        'game' => $game,
        'canBook' => $canBook,
        'problem' => false,
    );
    return $this->render("views/game/book.php with views/layout.php", $data);
});

$app->post("/game/book", function () use ($app) {
    $auth = $app['auth'];
    $auth->requireRole('member');

    $user = $auth->getUser();
    $data = $app['dataservice'];
    $gameService = $app['gameservice'];
    $id = $_POST['id'];
    $game = $data->loadGameById($id);
    $game->fromArray($_POST);
    $canBook = $gameService->canBookGame($game);
    if (!$canBook) {
        return $this->render("views/auth/no-permission.php with views/layout.php");
    }
    $game->player1_id = $user->id;
    $game->player2_id = $_POST['player2_id'] ?: null;
    $game->player3_id = $_POST['player3_id'] ?: null;
    $game->player4_id = $_POST['player4_id'] ?: null;
    $free = $gameService->isFreeGame($game);
    $game->status = $free ? GAME_FREE : GAME_REGULAR;
    try {
        $gameService->validatePlayers($game);
    } catch (\Exception $e) {
        $data = array(
            'id' => $id,
            'user' => $user,
            'allUsers' => $data->loadAllUsersForBooking(),
            'game' => $game,
            'canBook' => $canBook,
            'problem' => $e->getMessage(),
        );
        return $this->render("views/game/book.php with views/layout.php", $data);
    }

    $data->updateGame($game);
    $auth->logAction('GAME:BOOK', "GAME:$id");
    $this->reroute("/");
});

$app->get("/game/cancel/:id", function ($params) use ($app) {
    $auth = $app['auth'];
    $auth->requireRole('member');

    $user = $auth->getUser();
    $data = $app['dataservice'];
    $gameService = $app['gameservice'];
    $id = $params['id'];
    $game = $data->loadGameById($id);
    $player1 = $data->loadUserById($game->player1_id);
    $player2 = $data->loadUserById($game->player2_id);
    $player3 = $data->loadUserById($game->player3_id);
    $player4 = $data->loadUserById($game->player4_id);

    $canCancel = $gameService->canCancelGame($game);
    if (!$canCancel) {
        return $this->render("views/auth/no-permission.php with views/layout.php");
    }
    $data = array(
        'id' => $id,
        'user' => $user,
        'game' => $game,
        'player1' => $player1,
        'player2' => $player2,
        'player3' => $player3,
        'player4' => $player4,
        'canCancel' => $canCancel,
    );
    return $this->render("views/game/cancel.php with views/layout.php", $data);
});

$app->post("/game/cancel", function () use ($app) {
    $auth = $app['auth'];
    $auth->requireRole('member');

    $user = $auth->getUser();
    $data = $app['dataservice'];
    $gameService = $app['gameservice'];
    $id = $_POST['id'];
    $game = $data->loadGameById($id);

    $canCancel = $gameService->canCancelGame($game);
    if (!$canCancel) {
        return $this->render("views/auth/no-permission.php with views/layout.php");
    }
    $game->status = 'available';
    $game->player1_id = null;
    $game->player2_id = null;
    $game->player3_id = null;
    $game->player4_id = null;
    $game->notes = null;
    $data->updateGame($game);
    $auth->logAction('GAME:CANCEL', "GAME:$id");
    $this->reroute("/");
});

$app->get("/game/edit/:id", function ($params) use ($app) {
    $auth = $app['auth'];
    $auth->requireRole('admin');

    $user = $auth->getUser();
    $data = $app['dataservice'];
    $id = $params['id'];
    $game = $data->loadGameById($id);
    $allUsers = $data->loadAllUsers();

    $data = array(
        'id' => $id,
        'allUsers' => $allUsers,
        'game' => $game,
        'problem' => false,
    );
    return $this->render("views/game/edit.php with views/layout.php", $data);
});

$app->post("/game/save", function () use ($app) {
    $auth = $app['auth'];
    $auth->requireRole('admin');

    $user = $auth->getUser();
    $data = $app['dataservice'];
    $id = $_POST['id'];
    $game = $data->loadGameById($id);
    // form only has the H:i part, but fromArray() will expect the full time stamp
    $_POST['startTime'] = $game->startTime->format('Y-m-d') . 'T' . $_POST['startTime'] . ':00';
    $_POST['endTime'] = $game->startTime->format('Y-m-d') . 'T' . $_POST['endTime'] . ':00';
    $game->fromArray($_POST);
    $game->player1_id = $_POST['player1_id'] ?: null;
    $game->player2_id = $_POST['player2_id'] ?: null;
    $game->player3_id = $_POST['player3_id'] ?: null;
    $game->player4_id = $_POST['player4_id'] ?: null;
    if ($app['gameservice']->countPlayers($game) > 0 && $game->status === GAME_AVAILABLE) {
        $data = array(
            'id' => $id,
            'allUsers' => $data->loadAllUsers(),
            'game' => $game,
            'problem' => 'Game must not be available if there are players set.',
        );
        return $this->render("views/game/edit.php with views/layout.php", $data);
    }
    $data->updateGame($game);
    $auth->logAction('GAME:UPDATE', "GAME:$id");
    $this->reroute("/game/bulk-edit");
});

$app->get("/game/delete/:id", function ($params) use ($app) {
    $auth = $app['auth'];
    $auth->requireRole('admin');

    $data = $app['dataservice'];
    $id = $params['id'];
    $game = $data->loadGameById($id);
    $player1 = $data->loadUserById($game->player1_id);
    $player2 = $data->loadUserById($game->player2_id);
    $player3 = $data->loadUserById($game->player3_id);
    $player4 = $data->loadUserById($game->player4_id);

    $data = array(
        'id' => $id,
        'game' => $game,
        'player1' => $player1,
        'player2' => $player2,
        'player3' => $player3,
        'player4' => $player4,
    );
    return $this->render("views/game/delete.php with views/layout.php", $data);
});

$app->post("/game/delete", function () use ($app) {
    $auth = $app['auth'];
    $auth->requireRole('admin');

    $data = $app['dataservice'];
    $id = $_POST['id'];
    $game = $data->loadGameById($id);
    $data->deleteGame($game);
    $auth->logAction('GAME:DELETE', "GAME:$id");
    $this->reroute('/game/bulk-edit');
});

$app->get("/game/bulk-edit", function () use ($app) {
    $auth = $app['auth'];
    $auth->requireRole('admin');

    $data = array(
        'games' => $app['gameservice']->loadAllGamesAfterToday(),
        'userLookup' => $app['userservice']->getUserLookupMap(),
        'problem' => false,
    );
    return $this->render("views/game/bulk-edit.php with views/layout.php", $data);
});

$app->post("/game/bulk-edit", function () use ($app) {
    $auth = $app['auth'];
    $auth->requireRole('admin');

    $operation = $_POST['operation'];
    $selectedGames = $_POST['selectedGames'] ?? array();
    try {
        $app['gameservice']->bulkEdit($operation, $selectedGames);
    } catch (\Exception $e) {
        $data = array(
            'games' => $app['gameservice']->loadAllGamesAfterToday(),
            'userLookup' => $app['userservice']->getUserLookupMap(),
            'problem' => $e->getMessage(),
        );
        return $this->render("views/game/bulk-edit.php with views/layout.php", $data);
    }
    $this->reroute('/game/bulk-edit');
});
