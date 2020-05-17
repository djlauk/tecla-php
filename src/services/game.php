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
    private $userdao;
    private $timeslotdao;
    public function __construct(\tecla\data\GameDAO &$gamedao, \tecla\data\TimeslotDAO &$timeslotdao)
    {
        $this->gamedao = $gamedao;
        $this->timeslotdao = $timeslotdao;
    }

    public function generateGames($firstDay, $lastDay)
    {
        $timeslotsByWeekday = array(
            0 => array(),
            1 => array(),
            2 => array(),
            3 => array(),
            4 => array(),
            5 => array(),
            6 => array(),
        );
        $timeslots = $this->timeslotdao->loadAll();
        foreach ($timeslots as $item) {
            $timeslotsByWeekday[$item->weekday][] = $item;
        }
        $count = 0;
        $oneDay = new \DateInterval('P1D');
        $t = \DateTime::createFromFormat('Y-m-d\\TH:i:s', "${firstDay}T00:00:00");
        $end = \DateTime::createFromFormat('Y-m-d\\TH:i:s', "${lastDay}T23:59:59")->getTimeStamp();
        while ($t->getTimeStamp() <= $end) {
            $weekday = strftime('%w', $t->getTimeStamp());
            $dateStr = $t->format('Y-m-d');
            foreach ($timeslotsByWeekday[$weekday] as $item) {
                $g = new \tecla\data\Game();
                $g->startTime = "${dateStr}T{$item->startTime}";
                $g->endTime = "${dateStr}T{$item->endTime}";
                $g->court = $item->court;
                $g->status = 'available';
                $newId = $this->gamedao->insert($g);
                $count++;
            }
            $t->add($oneDay);
        }
        // TODO: add audit log: generated $count games for range $firstDay - $lastDay
        return $count;
    }
}

$app->service('gameservice', function () use ($app) {
    return new GameService($app['gamedao'], $app['timeslotdao']);
});
