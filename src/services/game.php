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
    private $gamedao;
    private $templatedao;
    private $userdao;
    private $limeApp;
    public function __construct(\tecla\data\GameDAO &$gamedao, \tecla\data\TemplateDAO &$templatedao, \tecla\data\UserDAO &$userdao, \Lime\App &$app)
    {
        $this->gamedao = $gamedao;
        $this->templatedao = $templatedao;
        $this->userdao = $userdao;
        $this->limeApp = $app;
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
        $templates = $this->templatedao->loadAll();
        foreach ($templates as $item) {
            if (!in_array($item->id, $selectedTemplates)) {
                continue;
            }
            $templatesByWeekday[$item->weekday][] = $item;
        }
        $count = 0;
        $oneDay = new \DateInterval('P1D');
        $t = \DateTime::createFromFormat('Y-m-d\\TH:i:s', "${firstDay}T00:00:00");
        $end = \DateTime::createFromFormat('Y-m-d\\TH:i:s', "${lastDay}T23:59:59")->getTimeStamp();
        while ($t->getTimeStamp() <= $end) {
            $weekday = strftime('%w', $t->getTimeStamp());
            $dateStr = $t->format('Y-m-d');
            foreach ($templatesByWeekday[$weekday] as $item) {
                $g = new \tecla\data\Game();
                $g->startTime = "${dateStr}T{$item->startTime}";
                $g->endTime = "${dateStr}T{$item->endTime}";
                $g->court = $item->court;
                $g->status = GAME_AVAILABLE;
                $newId = $this->gamedao->insert($g);
                $count++;
            }
            $t->add($oneDay);
        }
        // TODO: add audit log: generated $count games for range $firstDay - $lastDay
        return $count;
    }

    public function isFreeGame(\tecla\data\Game &$game)
    {
        $now = time();
        $s = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $game->startTime)->getTimestamp();
        return ($s > $now && $s - $now < $this->limeApp['config.freegame']);
    }

    public function isGameScheduledForUser($userId, $gameStatus = null)
    {
        $games = $this->gamedao->loadFutureGamesForUser($userId, $gameStatus);
        return count($games) > 0;
    }

    public function validatePlayers(\tecla\data\Game &$game)
    {
        $this->checkNumberOfPlayers($game);
        $this->checkUserCanBeBooked($game->player1_id);
        $this->checkUserCanBeBooked($game->player2_id);
        $this->checkUserCanBeBooked($game->player3_id);
        $this->checkUserCanBeBooked($game->player4_id);
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

    public function checkUserCanBeBooked($userId)
    {
        if (is_null($userId)) {return;}
        $u = $this->userdao->loadById($userId);
        if (!is_null($u->disabledOn)) {
            throw new \Exception("Player '{$u->displayName}' is disabled");
        }
        if ($u->role === 'guest') {return;} // guest users may be placeholders and used multiple times. they don't count.
        $games = $this->gamedao->loadFutureGamesForUser($userId, GAME_REGULAR);
        if (count($games) === 0) {return;}
        throw new \Exception("Player '{$u->displayName}' already has a game on {$games[0]->startTime}");
    }

    public function bulkEdit($operation, $selectedGames)
    {
        switch ($operation) {
            case 'cancel':
                foreach ($selectedGames as $id) {
                    $g = $this->gamedao->loadById($id);
                    $g->status = GAME_AVAILABLE;
                    $g->player1_id = null;
                    $g->player2_id = null;
                    $g->player3_id = null;
                    $g->player4_id = null;
                    $g->notes = null;
                    $this->gamedao->update($g);
                    // TODO: Add audit log: admin canceled game $id
                }
                break;
            case 'block':
                // preflight check: are all those games available?
                foreach ($selectedGames as $id) {
                    $g = $this->gamedao->loadById($id);
                    if ($g->status !== GAME_AVAILABLE) {
                        throw new \Exception("Only available games can be blocked!");
                    }
                }
                $now = strftime('%Y-%m-%d %H:%M:%S', time());
                foreach ($selectedGames as $id) {
                    $g = $this->gamedao->loadById($id);
                    $g->status = GAME_BLOCKED;
                    $g->player1_id = null;
                    $g->player2_id = null;
                    $g->player3_id = null;
                    $g->player4_id = null;
                    $g->notes = "Blocked by {$this->limeApp['auth']->getUser()->displayName} on $now";
                    $this->gamedao->update($g);
                    // TODO: Add audit log: admin blocked game $id
                }
                break;

            default:
                throw new \Exception("Operation not supported: $operation");

        }
    }
}

$app->service('gameservice', function () use ($app) {
    return new GameService($app['gamedao'], $app['templatedao'], $app['userdao'], $app);
});
