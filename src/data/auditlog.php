<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------

namespace tecla\data;

class Auditlog
{
    public $id = null;
    public $action = null;
    public $user_id = null;
    public $object = null;
    public $message = null;
    public $metaVersion = '';
    public $metaCreatedOn = '';
    public $metaUpdatedOn = '';

    public function toArray()
    {
        return array(
            'id' => $this->id,
            'action' => $this->action,
            'user_id' => $this->user_id,
            'object' => $this->object,
            'message' => $this->message,
            'metaVersion' => $this->metaVersion,
            'metaCreatedOn' => \tecla\util\dbFormatDateTime($this->metaCreatedOn),
            'metaUpdatedOn' => \tecla\util\dbFormatDateTime($this->metaUpdatedOn),
        );
    }

    public function fromArray($arr)
    {
        $this->id = $arr['id'] ?? $this->id;
        $this->action = $arr['action'] ?? $this->action;
        $this->user_id = $arr['user_id'] ?? $this->user_id;
        $this->object = $arr['object'] ?? $this->object;
        $this->message = $arr['message'] ?? $this->message;
        $this->metaVersion = $arr['metaVersion'] ?? $this->metaVersion;
        $this->metaCreatedOn = isset($arr['metaCreatedOn']) ? \tecla\util\dbParseDateTime($arr['metaCreatedOn']) : $this->metaCreatedOn;
        $this->metaUpdatedOn = isset($arr['metaUpdatedOn']) ? \tecla\util\dbParseDateTime($arr['metaUpdatedOn']) : $this->metaUpdatedOn;
    }

    public static function createFromArray($arr)
    {
        $obj = new Auditlog();
        $obj->fromArray($arr);
        return $obj;
    }
}

class AuditlogDAO
{
    private $db;
    public function __construct(DBAccess &$db)
    {
        $this->db = $db;
    }

    public function count()
    {
        $sql = 'SELECT COUNT(*) as `numEntries` FROM `auditlog`';
        $row = $this->db->querySingle($sql);
        return $row['numEntries'];
    }

    public function loadPage($pageSize = 100, $offset = 0)
    {
        $results = array();
        $sql = <<<HERE
SELECT
    `id`,
    `action`,
    `user_id`,
    `object`,
    `message`,
    `metaVersion`,
    DATE_FORMAT(`metaCreatedOn`, '%Y-%m-%dT%H:%i:%S') as `metaCreatedOn`,
    DATE_FORMAT(`metaUpdatedOn`, '%Y-%m-%dT%H:%i:%S') as `metaUpdatedOn`
FROM `auditlog`
ORDER BY `id` DESC
LIMIT $pageSize OFFSET $offset
HERE;
        $rows = $this->db->query($sql);
        foreach ($rows as $row) {
            $results[] = Auditlog::createFromArray($row);
        }
        return $results;
    }

    public function loadById($id)
    {
        if (is_null($id)) {
            return null;
        }

        $sql = <<<HERE
SELECT
    `id`,
    `action`,
    `user_id`,
    `object`,
    `message`,
    `metaVersion`,
    DATE_FORMAT(`metaCreatedOn`, '%Y-%m-%dT%H:%i:%S') as `metaCreatedOn`,
    DATE_FORMAT(`metaUpdatedOn`, '%Y-%m-%dT%H:%i:%S') as `metaUpdatedOn`
FROM `auditlog`
WHERE `id` = :id
HERE;
        $row = $this->db->querySingle($sql, array('id' => $id));
        return is_null($row) ? null : Auditlog::createFromArray($row);
    }

    public function insert(\tecla\data\Auditlog &$obj)
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
        $sql = "INSERT INTO `auditlog` ($fields) VALUES ($placeholders)";
        $newId = $this->db->insert($sql, $arr);
        $obj->id = $newId;
        return $newId;
    }
}
