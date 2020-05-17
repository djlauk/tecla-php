<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------

namespace tecla\data;

class Timeslot
{
    public $id = null;
    public $weekday = null;
    public $startTime = '';
    public $endTime = '';
    public $court = '';
    public $notes = '';
    public $metaVersion = '';
    public $metaCreatedOn = '';
    public $metaUpdatedOn = '';

    public function toArray()
    {
        return array(
            'id' => $this->id,
            'weekday' => $this->weekday,
            'startTime' => $this->startTime,
            'endTime' => $this->endTime,
            'court' => $this->court,
            'notes' => $this->notes,
            'metaVersion' => $this->metaVersion,
            'metaCreatedOn' => $this->metaCreatedOn,
            'metaUpdatedOn' => $this->metaUpdatedOn,
        );
    }

    public function fromArray($arr)
    {
        $this->id = $arr['id'] ?? $this->id;
        $this->weekday = $arr['weekday'] ?? $this->weekday;
        $this->startTime = $arr['startTime'] ?? $this->startTime;
        $this->endTime = $arr['endTime'] ?? $this->endTime;
        $this->court = $arr['court'] ?? $this->court;
        $this->notes = $arr['notes'] ?? $this->notes;
        $this->metaVersion = $arr['metaVersion'] ?? $this->metaVersion;
        $this->metaCreatedOn = $arr['metaCreatedOn'] ?? $this->metaCreatedOn;
        $this->metaUpdatedOn = $arr['metaUpdatedOn'] ?? $this->metaUpdatedOn;
    }

    public static function createFromArray($arr)
    {
        $obj = new Timeslot();
        $obj->fromArray($arr);
        return $obj;
    }
}

class TimeslotDAO
{
    private $db;
    public function __construct(DBAccess &$db)
    {
        $this->db = $db;
    }

    public function loadAll()
    {
        $results = array();
        $sql = <<<HERE
SELECT
    `id`,
    `weekday`,
    `startTime`,
    `endTime`,
    `court`,
    `notes`,
    `metaVersion`,
    `metaCreatedOn`,
    `metaUpdatedOn`
FROM
    `time_slots`
ORDER BY
    `weekday` ASC,
    `startTime` ASC,
    `court` ASC,
    `id` ASC
HERE;
        $rows = $this->db->query($sql);
        foreach ($rows as $row) {
            $results[] = TimeSlot::createFromArray($row);
        }
        return $results;
    }

    public function loadById($id)
    {
        $sql = <<<HERE
SELECT
    `id`,
    `weekday`,
    `startTime`,
    `endTime`,
    `court`,
    `notes`,
    `metaVersion`,
    `metaCreatedOn`,
    `metaUpdatedOn`
FROM
    `time_slots`
WHERE
    `id` = :id
HERE;
        $row = $this->db->querySingle($sql, array('id' => $id));
        return is_null($row) ? null : TimeSlot::createFromArray($row);
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
        $sql = "INSERT INTO `time_slots` ($fields) VALUES ($placeholders)";
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
        $sql = "UPDATE `time_slots` SET $fields WHERE `id` = :id AND `metaVersion` = :oldVersion";
        $this->db->execute($sql, $arr);
    }
}
