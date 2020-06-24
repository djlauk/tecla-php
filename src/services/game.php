<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------

namespace tecla;

class GameService
{
    private $data;
    private $auth;
    private $cfgMaxGames;
    private $cfgFreeGameSeconds;

    public function __construct(DataService &$data, AuthService &$auth, \Lime\App &$app)
    {
        $this->data = $data;
        $this->auth = $auth;
        $this->cfgMaxGames = $app['config.maxgames'];
        $this->cfgFreeGameSeconds = $app['config.freegame'];
    }

    public function loadAllGamesAfterToday()
    {
        $today = strftime('%Y-%m-%d', time());
        return $this->data->loadAllGamesAfter($today, $this->cfgMaxGames);
    }

    public function loadFutureGamesForUser($userId, $status = null)
    {
        return $this->data->loadFutureGamesForUser($userId, $status);
    }

    public function canBookGame(\tecla\data\Game &$game)
    {
        if (!$this->auth->hasRole('member')) {
            return false;
        }
        if ($game->status !== GAME_AVAILABLE) {
            return false;
        }
        $now = time();
        if ($game->startTime->getTimestamp() < $now) {
            return false;
        }
        $user = $this->auth->getUser();
        if ($this->isFreeGame($game)) {
            if ($this->isGameScheduledForUser($user->id, GAME_FREE)) {
                return false;
            }
        } else {
            if ($this->isGameScheduledForUser($user->id, GAME_REGULAR)) {
                return false;
            }
        }
        return true;
    }

    public function canCancelGame(\tecla\data\Game &$game)
    {
        if (!$this->auth->hasRole('member')) {
            return false;
        }
        $user = $this->auth->getUser();
        if ($game->player1_id !== $user->id
            && $game->player2_id !== $user->id
            && $game->player3_id !== $user->id
            && $game->player4_id !== $user->id) {
            return false;
        }
        $now = time();
        if ($game->startTime->getTimestamp() < $now) {
            return false;
        }
        return true;
    }

    public function generateGames($firstDay, $lastDay, $selectedTemplates)
    {
        $templatesByWeekday = array(
            0 => array(),
            1 => array(),
            2 => array(),
            3 => array(),
            4 => array(),
            5 => array(),
            6 => array(),
        );
        $templates = $this->data->loadAllTemplates();
        foreach ($templates as $item) {
            if (!in_array($item->id, $selectedTemplates)) {
                continue;
            }
            $templatesByWeekday[$item->weekday][] = $item;
        }
        $count = 0;
        $oneDay = new \DateInterval('P1D');
        $t = \tecla\util\dbParseDateTime("${firstDay}T00:00:00");
        $end = \tecla\util\dbParseDateTime("${lastDay}T23:59:59")->getTimestamp();
        while ($t->getTimestamp() <= $end) {
            $weekday = strftime('%w', $t->getTimestamp());
            $dateStr = $t->format('Y-m-d');
            foreach ($templatesByWeekday[$weekday] as $item) {
                $g = new \tecla\data\Game();
                $g->startTime = \tecla\util\dbParseDateTime("${dateStr}T{$item->startTime}");
                $g->endTime = \tecla\util\dbParseDateTime("${dateStr}T{$item->endTime}");
                $g->court = $item->court;
                $g->status = GAME_AVAILABLE;
                $newId = $this->data->insertGame($g);
                $count++;
            }
            $t = $t->add($oneDay);
        }
        $this->auth->logAction('GAME:GENERATE', null, "generated $count games for range $firstDay - $lastDay");

        return $count;
    }

    public function isFreeGame(\tecla\data\Game &$game)
    {
        $now = time();
        $s = $game->startTime->getTimestamp();
        return ($s > $now && $s - $now < $this->cfgFreeGameSeconds);
    }

    public function isGameScheduledForUser($userId, $gameStatus = null)
    {
        $games = $this->data->loadFutureGamesForUser($userId, $gameStatus);
        return count($games) > 0;
    }

    public function validatePlayers(\tecla\data\Game &$game)
    {
        $this->checkNumberOfPlayers($game);
        $this->checkUserCanBeBooked($game->player1_id, $game->status);
        $this->checkUserCanBeBooked($game->player2_id, $game->status);
        $this->checkUserCanBeBooked($game->player3_id, $game->status);
        $this->checkUserCanBeBooked($game->player4_id, $game->status);
    }

    public function countPlayers(\tecla\data\Game &$game)
    {
        $numPlayers = 0;
        if (!is_null($game->player1_id)) {$numPlayers++;}
        if (!is_null($game->player2_id)) {$numPlayers++;}
        if (!is_null($game->player3_id)) {$numPlayers++;}
        if (!is_null($game->player4_id)) {$numPlayers++;}
        return $numPlayers;
    }

    public function checkNumberOfPlayers(\tecla\data\Game &$game)
    {
        $numPlayers = $this->countPlayers($game);
        if ($numPlayers != 2 && $numPlayers != 4) {
            throw new \Exception("Number of players must be 2 or 4 (not $numPlayers)");
        }
    }

    public function checkUserCanBeBooked($userId, $gameType)
    {
        if (is_null($userId)) {return;}
        $u = $this->data->loadUserById($userId);
        if (!is_null($u->disabledOn)) {
            throw new \Exception("Player '{$u->displayName}' is disabled");
        }
        if ($u->role === 'guest') {return;} // guest users may be placeholders and used multiple times. they don't count.
        $games = $this->data->loadFutureGamesForUser($userId, $gameType);
        if (count($games) === 0) {return;}
        throw new \Exception("Player '{$u->displayName}' already has a {$games[0]->status} game on {$games[0]->startTime}");
    }

    public function bulkEdit($operation, $selectedGames)
    {
        switch ($operation) {
            case 'cancel':
                foreach ($selectedGames as $id) {
                    $g = $this->data->loadGameById($id);
                    $g->status = GAME_AVAILABLE;
                    $g->player1_id = null;
                    $g->player2_id = null;
                    $g->player3_id = null;
                    $g->player4_id = null;
                    $g->notes = null;
                    $this->data->updateGame($g);
                    $this->auth->logAction('GAME:BULKCANCEL', "GAME:$id", "admin canceled game");
                }
                break;
            case 'block':
                // preflight check: are all those games available?
                foreach ($selectedGames as $id) {
                    $g = $this->data->loadGameById($id);
                    if ($g->status !== GAME_AVAILABLE) {
                        throw new \Exception("Only available games can be blocked!");
                    }
                }
                $now = strftime('%Y-%m-%d %H:%M:%S', time());
                foreach ($selectedGames as $id) {
                    $g = $this->data->loadGameById($id);
                    $g->status = GAME_BLOCKED;
                    $g->player1_id = null;
                    $g->player2_id = null;
                    $g->player3_id = null;
                    $g->player4_id = null;
                    $g->notes = "Blocked by {$this->auth->getUser()->displayName} on $now";
                    $this->data->updateGame($g);
                    $this->auth->logAction('GAME:BULKBLOCK', "GAME:$id", "admin blocked game");
                }
                break;
            case 'delete':
                // preflight check: are all those games available?
                foreach ($selectedGames as $id) {
                    $g = $this->data->loadGameById($id);
                    if ($g->status !== GAME_AVAILABLE) {
                        throw new \Exception("Only available games can be deleted!");
                    }
                }
                foreach ($selectedGames as $id) {
                    $g = $this->data->loadGameById($id);
                    $this->data->deleteGame($g);
                    $this->auth->logAction('GAME:BULKDELETE', "GAME:$id", "admin deleted game");
                }
                break;

            default:
                throw new \Exception("Operation not supported: $operation");

        }
    }
}

$app->service('gameservice', function () use ($app) {
    $data = $app['dataservice'];
    $auth = $app['auth'];
    return new GameService($data, $auth, $app);
});
