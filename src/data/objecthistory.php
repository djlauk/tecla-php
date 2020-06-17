<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------

namespace tecla\data;

class ObjectHistory
{

    public $type = null;
    public $id = null;
    public $version = null;
    public $data = null;
    public $metaCreatedOn = '';

    public function toArray()
    {
        return array(
            'type' => $this->type,
            'id' => $this->id,
            'version' => $this->version,
            'data' => $this->data,
            'metaCreatedOn' => \tecla\util\dbFormatDateTime($this->metaCreatedOn),
        );
    }

    public function fromArray($arr)
    {
        $this->type = $arr['type'] ?? $this->type;
        $this->id = $arr['id'] ?? $this->id;
        $this->version = $arr['version'] ?? $this->version;
        $this->data = $arr['data'] ?? $this->data;
        $this->metaCreatedOn = isset($arr['metaCreatedOn']) ? \tecla\util\dbParseDateTime($arr['metaCreatedOn']) : $this->metaCreatedOn;
    }

    public static function createFromArray($arr)
    {
        $obj = new ObjectHistory();
        $obj->fromArray($arr);
        return $obj;
    }
}

class ObjectHistoryDAO
{
    private $db;
    public function __construct(DBAccess &$db)
    {
        $this->db = $db;
    }

    public function loadHistoryOfObject($type, $id)
    {
        $results = array();
        $sql = <<<HERE
SELECT
    `type`,
    `id`,
    `version`,
    `data`,
    DATE_FORMAT(`metaCreatedOn`, '%Y-%m-%dT%H:%i:%S') as `metaCreatedOn`
FROM
    `objecthistory`
WHERE
    `type` = :type
    AND `id` = :id
ORDER BY
    `version` DESC
HERE;
        $rows = $this->db->query($sql, array(
            'type' => $type,
            'id' => $id,
        ));
        foreach ($rows as $row) {
            $results[] = ObjectHistory::createFromArray($row);
        }
        return $results;
    }

    public function insert(ObjectHistory &$obj)
    {
        $obj->metaCreatedOn = \tecla\util\dbTime();
        $arr = $obj->toArray();
        $sql = "INSERT INTO `objecthistory` (`type`, `id`, `version`, `data`, `metaCreatedOn`) VALUES (:type, :id, :version, :data, :metaCreatedOn)";
        $newId = $this->db->insert($sql, $arr);
        $obj->id = $newId;
        return $newId;
    }
}
