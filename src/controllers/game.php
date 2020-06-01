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
    $gamedao = $app['gamedao'];
    $userdao = $app['userdao'];
    $id = $params['id'];
    $game = $gamedao->loadById($id);
    $player1 = $userdao->loadById($game->player1_id);
    $player2 = $userdao->loadById($game->player2_id);
    $player3 = $userdao->loadById($game->player3_id);
    $player4 = $userdao->loadById($game->player4_id);

    $canBook = $auth->canBookGame($game);
    $canCancel = $auth->canCancelGame($game);
    $canEdit = $auth->hasRole('admin');
    $canDelete = $auth->hasRole('admin');
    $nextGames = $gamedao->loadFutureGamesForUser($user->id);
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
    $gamedao = $app['gamedao'];
    $userdao = $app['userdao'];
    $id = $params['id'];
    $game = $gamedao->loadById($id);
    $allUsers = $userdao->loadAllForBooking();

    $canBook = $auth->canBookGame($game);
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
    $gamedao = $app['gamedao'];
    $userdao = $app['userdao'];
    $id = $_POST['id'];
    $game = $gamedao->loadById($id);
    $game->fromArray($_POST);
    $canBook = $auth->canBookGame($game);
    if (!$canBook) {
        return $this->render("views/auth/no-permission.php with views/layout.php");
    }
    $game->player1_id = $user->id;
    $game->player2_id = $_POST['player2_id'] ?: null;
    $game->player3_id = $_POST['player3_id'] ?: null;
    $game->player4_id = $_POST['player4_id'] ?: null;
    $free = $app['gameservice']->isFreeGame($game);
    $game->status = $free ? GAME_FREE : GAME_REGULAR;
    try {
        $app['gameservice']->validatePlayers($game);
    } catch (\Exception $e) {
        $data = array(
            'id' => $id,
            'user' => $user,
            'allUsers' => $userdao->loadAllForBooking(),
            'game' => $game,
            'canBook' => $canBook,
            'problem' => $e->getMessage(),
        );
        return $this->render("views/game/book.php with views/layout.php", $data);
    }

    $gamedao->update($game);
    $this->reroute("/");
});

$app->get("/game/cancel/:id", function ($params) use ($app) {
    $auth = $app['auth'];
    $auth->requireRole('member');

    $user = $auth->getUser();
    $gamedao = $app['gamedao'];
    $userdao = $app['userdao'];
    $id = $params['id'];
    $game = $gamedao->loadById($id);
    $player1 = $userdao->loadById($game->player1_id);
    $player2 = $userdao->loadById($game->player2_id);
    $player3 = $userdao->loadById($game->player3_id);
    $player4 = $userdao->loadById($game->player4_id);

    $canCancel = $auth->canCancelGame($game);
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
    $gamedao = $app['gamedao'];
    $id = $_POST['id'];
    $game = $gamedao->loadById($id);

    $canCancel = $auth->canCancelGame($game);
    if (!$canCancel) {
        return $this->render("views/auth/no-permission.php with views/layout.php");
    }
    $game->status = 'available';
    $game->player1_id = null;
    $game->player2_id = null;
    $game->player3_id = null;
    $game->player4_id = null;
    $game->notes = null;
    $gamedao->update($game);
    $this->reroute("/");
});

$app->get("/game/edit/:id", function ($params) use ($app) {
    $auth = $app['auth'];
    $auth->requireRole('admin');

    $user = $auth->getUser();
    $gamedao = $app['gamedao'];
    $userdao = $app['userdao'];
    $id = $params['id'];
    $game = $gamedao->loadById($id);
    $allUsers = $userdao->loadAll();

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
    $gamedao = $app['gamedao'];
    $id = $_POST['id'];
    $game = $gamedao->loadById($id);
    $game->fromArray($_POST);
    $game->player1_id = $_POST['player1_id'] ?: null;
    $game->player2_id = $_POST['player2_id'] ?: null;
    $game->player3_id = $_POST['player3_id'] ?: null;
    $game->player4_id = $_POST['player4_id'] ?: null;
    if ($app['gameservice']->countPlayers($game) > 0 && $game->status === GAME_AVAILABLE) {
        $data = array(
            'id' => $id,
            'allUsers' => $app['userdao']->loadAll(),
            'game' => $game,
            'problem' => 'Game must not be available if there are players set.',
        );
        return $this->render("views/game/edit.php with views/layout.php", $data);
    }
    $gamedao->update($game);
    $this->reroute("/game/bulk-edit");
});

$app->get("/game/delete/:id", function ($params) use ($app) {
    $auth = $app['auth'];
    $auth->requireRole('admin');

    $gamedao = $app['gamedao'];
    $userdao = $app['userdao'];
    $id = $params['id'];
    $game = $gamedao->loadById($id);
    $player1 = $userdao->loadById($game->player1_id);
    $player2 = $userdao->loadById($game->player2_id);
    $player3 = $userdao->loadById($game->player3_id);
    $player4 = $userdao->loadById($game->player4_id);

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

    $gamedao = $app['gamedao'];
    $id = $_POST['id'];
    $game = $gamedao->loadById($id);
    $gamedao->delete($game);
    $this->reroute('/game/bulk-edit');
});

$app->get("/game/bulk-edit", function () use ($app) {
    $auth = $app['auth'];
    $auth->requireRole('admin');

    $today = strftime('%Y-%m-%d', time());
    $data = array(
        'games' => $app['gamedao']->loadAllAfter($today),
        'userLookup' => $app['userservice']->getUserLookupMap(),
        'problem' => false,
    );
    return $this->render("views/game/bulk-edit.php with views/layout.php", $data);
});

$app->post("/game/bulk-edit", function () use ($app) {
    $auth = $app['auth'];
    $auth->requireRole('admin');

    $operation = $_POST['operation'];
    $selectedGames = $_POST['selectedGames'];
    try {
        $app['gameservice']->bulkEdit($operation, $selectedGames);
    } catch (\Exception $e) {
        $today = strftime('%Y-%m-%d', time());
        $data = array(
            'games' => $app['gamedao']->loadAllAfter($today),
            'userLookup' => $app['userservice']->getUserLookupMap(),
            'problem' => $e->getMessage(),
        );
        return $this->render("views/game/bulk-edit.php with views/layout.php", $data);
    }
    $this->reroute('/game/bulk-edit');
});
