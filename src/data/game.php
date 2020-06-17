<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------

namespace tecla\data;

define('GAME_AVAILABLE', 'available');
define('GAME_FREE', 'freegame');
define('GAME_REGULAR', 'regular');
define('GAME_TRAINING', 'training');
define('GAME_TOURNAMENT', 'tournament');
define('GAME_BLOCKED', 'blocked');

define('GAME_STATUS_VALUES', array(
    GAME_AVAILABLE => GAME_AVAILABLE,
    GAME_FREE => GAME_FREE,
    GAME_REGULAR => GAME_REGULAR,
    GAME_TRAINING => GAME_TRAINING,
    GAME_TOURNAMENT => GAME_TOURNAMENT,
    GAME_BLOCKED => GAME_BLOCKED,
));

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
    public $startTime = null;
    public $endTime = null;
    public $court = null;
    public $player1_id = null;
    public $player2_id = null;
    public $player3_id = null;
    public $player4_id = null;
    public $tournament_id = null;
    public $winner = null;
    public $status = null;
    public $notes = null;
    public $metaVersion = null;
    public $metaCreatedOn = null;
    public $metaUpdatedOn = null;

    public function toArray()
    {
        return array(
            'id' => $this->id,
            'startTime' => \tecla\util\dbFormatDateTime($this->startTime),
            'endTime' => \tecla\util\dbFormatDateTime($this->endTime),
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
            'metaCreatedOn' => \tecla\util\dbFormatDateTime($this->metaCreatedOn),
            'metaUpdatedOn' => \tecla\util\dbFormatDateTime($this->metaUpdatedOn),
        );
    }

    public function fromArray($arr)
    {
        $this->id = $arr['id'] ?? $this->id;
        $this->startTime = isset($arr['startTime']) ? \tecla\util\dbParseDateTime($arr['startTime']) : $this->startTime;
        $this->endTime = isset($arr['endTime']) ? \tecla\util\dbParseDateTime($arr['endTime']) : $this->endTime;
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
        $this->metaCreatedOn = isset($arr['metaCreatedOn']) ? \tecla\util\dbParseDateTime($arr['metaCreatedOn']) : $this->metaCreatedOn;
        $this->metaUpdatedOn = isset($arr['metaUpdatedOn']) ? \tecla\util\dbParseDateTime($arr['metaUpdatedOn']) : $this->metaUpdatedOn;
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

    private function _sqlSelect()
    {
        $sql = <<<HERE
SELECT
    `id`,
    DATE_FORMAT(`startTime`, '%Y-%m-%dT%H:%i:%S') as `startTime`,
    DATE_FORMAT(`endTime`, '%Y-%m-%dT%H:%i:%S') as `endTime`,
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
    DATE_FORMAT(`metaCreatedOn`, '%Y-%m-%dT%H:%i:%S') as `metaCreatedOn`,
    DATE_FORMAT(`metaUpdatedOn`, '%Y-%m-%dT%H:%i:%S') as `metaUpdatedOn`
FROM
    `games`
HERE;
        return $sql;
    }

    public function loadAllAfter($timestamp, $maxgames = 100)
    {
        $results = array();
        $sql = $this->_sqlSelect() . <<<HERE
WHERE
    `startTime` >= :timestamp
ORDER BY
    `startTime` ASC,
    `court` ASC,
    `id` ASC
HERE;
        // this cannot be passed as a parameter, so let's build the sql through concatenation
        $sql .= " LIMIT $maxgames";
        $rows = $this->db->query($sql, array('timestamp' => $timestamp));
        foreach ($rows as $row) {
            $results[] = Game::createFromArray($row);
        }
        return $results;
    }

    public function loadById($id)
    {
        $sql = $this->_sqlSelect() . <<<HERE
WHERE
    `id` = :id
HERE;
        $row = $this->db->querySingle($sql, array('id' => $id));
        return is_null($row) ? null : Game::createFromArray($row);
    }

    public function loadFutureGamesForUser($userId, $status = null)
    {
        if (is_null($userId)) {
            return array();
        }
        $results = array();
        $params = array('userid' => $userId);
        $sql = $this->_sqlSelect() . <<<HERE
WHERE
    `startTime` >= CURRENT_TIMESTAMP()
    AND (
           `player1_id` = :userid
        OR `player2_id` = :userid
        OR `player3_id` = :userid
        OR `player4_id` = :userid
    )
HERE;
        if (!is_null($status)) {
            $sql .= ' AND `status` = :status ';
            $params['status'] = $status;
        }
        $sql .= <<<HERE
ORDER BY
    `startTime` ASC
HERE;
        $rows = $this->db->query($sql, $params);
        foreach ($rows as $row) {
            $results[] = Game::createFromArray($row);
        }
        return $results;
    }

    public function getLastGame()
    {
        $sql = $this->_sqlSelect() . <<<HERE
ORDER BY
    `startTime` DESC,
    `court` DESC,
    `id` DESC
LIMIT 1
HERE;
        $row = $this->db->querySingle($sql);
        return is_null($row) ? null : Game::createFromArray($row);
    }

    public function insert(&$obj)
    {
        $obj->metaVersion = 1;
        $obj->metaUpdatedOn = \tecla\util\dbTime();
        $obj->metaCreatedOn = $obj->metaUpdatedOn;
        $arr = $obj->toArray();
        unset($arr['id']);
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
        $obj->id = $newId;
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
        $obj->metaUpdatedOn = \tecla\util\dbTime();
        $arr = $obj->toArray();
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

    public function delete(&$obj)
    {
        $oldItem = $this->loadById($obj->id);
        if (is_null($oldItem)) {
            throw new \Exception('Internal inconsistency!');
        }
        if ($oldItem->metaVersion != $obj->metaVersion) {
            throw new \Exception('Version mismatch!');
        }

        $arr = array(
            'id' => $obj->id,
            'oldVersion' => $oldItem->metaVersion,
        );
        $sql = "DELETE FROM `games` WHERE `id` = :id AND `metaVersion` = :oldVersion";
        $this->db->execute($sql, $arr);
    }
}
