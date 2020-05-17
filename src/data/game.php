<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------

namespace tecla\data;

class Game
{
    // player1_id
    // player2_id
    // player3_id
    // player4_id
    // tournament_id
    // winner
    // status

    public $id = null;
    public $startTime = '';
    public $endTime = '';
    public $court = '';
    public $player1_id = null;
    public $player2_id = null;
    public $player3_id = null;
    public $player4_id = null;
    public $tournament_id = null;
    public $winner = null;
    public $status = '';
    public $notes = null;
    public $metaVersion = '';
    public $metaCreatedOn = '';
    public $metaUpdatedOn = '';

    public function toArray()
    {
        return array(
            'id' => $this->id,
            'startTime' => $this->startTime,
            'endTime' => $this->endTime,
            'court' => $this->court,
            'player1_id' => $this->player1_id,
            'player2_id' => $this->player2_id,
            'player3_id' => $this->player3_id,
            'player4_id' => $this->player4_id,
            'tournament_id' => $this->tournament_id,
            'winner' => $this->winner,
            'status' => $this->status,
            'notes' => $this->notes,
            'metaVersion' => $this->metaVersion,
            'metaCreatedOn' => $this->metaCreatedOn,
            'metaUpdatedOn' => $this->metaUpdatedOn,
        );
    }

    public function fromArray($arr)
    {
        $this->id = $arr['id'] ?? $this->id;
        $this->startTime = $arr['startTime'] ?? $this->startTime;
        $this->endTime = $arr['endTime'] ?? $this->endTime;
        $this->court = $arr['court'] ?? $this->court;
        $this->player1_id = $arr['player1_id'] ?? $this->player1_id;
        $this->player2_id = $arr['player2_id'] ?? $this->player2_id;
        $this->player3_id = $arr['player3_id'] ?? $this->player3_id;
        $this->player4_id = $arr['player4_id'] ?? $this->player4_id;
        $this->tournament_id = $arr['tournament_id'] ?? $this->tournament_id;
        $this->winner = $arr['winner'] ?? $this->winner;
        $this->status = $arr['status'] ?? $this->status;
        $this->notes = $arr['notes'] ?? $this->notes;
        $this->metaVersion = $arr['metaVersion'] ?? $this->metaVersion;
        $this->metaCreatedOn = $arr['metaCreatedOn'] ?? $this->metaCreatedOn;
        $this->metaUpdatedOn = $arr['metaUpdatedOn'] ?? $this->metaUpdatedOn;
    }

    public static function createFromArray($arr)
    {
        $obj = new Game();
        $obj->fromArray($arr);
        return $obj;
    }
}

class GameDAO
{
    private $db;
    public function __construct(DBAccess &$db)
    {
        $this->db = $db;
    }

    public function loadAllAfter($timestamp)
    {
        $results = array();
        $sql = <<<HERE
SELECT
    `id`,
    `startTime`,
    `endTime`,
    `court`,
    `player1_id`,
    `player2_id`,
    `player3_id`,
    `player4_id`,
    `tournament_id`,
    `winner`,
    `status`,
    `notes`,
    `metaVersion`,
    `metaCreatedOn`,
    `metaUpdatedOn`
FROM
    `games`
WHERE
    `startTime` >= :timestamp
ORDER BY
    `startTime` ASC,
    `court` ASC,
    `id` ASC
HERE;
        $rows = $this->db->query($sql, array('timestamp' => $timestamp));
        foreach ($rows as $row) {
            $results[] = Game::createFromArray($row);
        }
        return $results;
    }

    public function loadById($id)
    {
        $sql = <<<HERE
SELECT
    `id`,
    `startTime`,
    `endTime`,
    `court`,
    `player1_id`,
    `player2_id`,
    `player3_id`,
    `player4_id`,
    `tournament_id`,
    `winner`,
    `status`,
    `notes`,
    `metaVersion`,
    `metaCreatedOn`,
    `metaUpdatedOn`
FROM
    `games`
WHERE
    `id` = :id
HERE;
        $row = $this->db->querySingle($sql, array('id' => $id));
        return is_null($row) ? null : Game::createFromArray($row);
    }

    public function insert(&$obj)
    {
        $obj->metaVersion = 1;
        $arr = $obj->toArray();
        unset($arr['id']);
        unset($arr['metaUpdatedOn']);
        unset($arr['metaCreatedOn']);
        $fields = array();
        $placeholders = array();
        foreach ($arr as $key => $value) {
            $fields[] = "`$key`";
            $placeholders[] = ":$key";
        }
        $fields = implode(', ', $fields);
        $placeholders = implode(', ', $placeholders);
        $sql = "INSERT INTO `games` ($fields) VALUES ($placeholders)";
        $newId = $this->db->insert($sql, $arr);
        return $newId;
    }

    public function update(&$obj)
    {
        $oldItem = $this->loadById($obj->id);
        if (is_null($oldItem)) {
            throw new \Exception('Internal inconsistency!');
        }
        if ($oldItem->metaVersion != $obj->metaVersion) {
            throw new \Exception('Version mismatch!');
        }

        $obj->metaVersion++;
        $arr = $obj->toArray();
        unset($arr['metaUpdatedOn']);
        unset($arr['metaCreatedOn']);

        $fields = array();
        foreach ($arr as $key => $value) {
            if ($key == 'id') {
                continue;
            }
            $fields[] = "`$key` = :$key";
        }
        $fields = implode(', ', $fields);
        $arr['oldVersion'] = $oldItem->metaVersion;
        $sql = "UPDATE `games` SET $fields WHERE `id` = :id AND `metaVersion` = :oldVersion";
        $this->db->execute($sql, $arr);
    }
}
