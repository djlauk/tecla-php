<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------

namespace tecla\data;

class User
{
    public $id = null;
    public $displayName = null;
    public $passwordHash = null;
    public $email = null;
    public $role = null;
    public $failedLogins = 0;
    public $lockedUntil = null;
    public $disabledOn = null;
    public $verifiedOn = null;
    public $lastLoginOn = null;
    public $lastLoginFrom = null;
    public $metaVersion = null;
    public $metaCreatedOn = null;
    public $metaUpdatedOn = null;

    public function toArray()
    {
        return array(
            'id' => $this->id,
            'displayName' => $this->displayName,
            'passwordHash' => $this->passwordHash,
            'email' => $this->email,
            'role' => $this->role,
            'failedLogins' => $this->failedLogins,
            'lockedUntil' => is_null($this->lockedUntil) ? null : \tecla\util\dbFormatDateTime($this->lockedUntil),
            'disabledOn' => is_null($this->disabledOn) ? null : \tecla\util\dbFormatDateTime($this->disabledOn),
            'verifiedOn' => is_null($this->verifiedOn) ? null : \tecla\util\dbFormatDateTime($this->verifiedOn),
            'lastLoginOn' => is_null($this->lastLoginOn) ? null : \tecla\util\dbFormatDateTime($this->lastLoginOn),
            'lastLoginFrom' => $this->lastLoginFrom,
            'metaVersion' => $this->metaVersion,
            'metaCreatedOn' => \tecla\util\dbFormatDateTime($this->metaCreatedOn),
            'metaUpdatedOn' => \tecla\util\dbFormatDateTime($this->metaUpdatedOn),
        );
    }

    public function fromArray($arr)
    {
        $this->id = $arr['id'] ?? $this->id;
        $this->displayName = $arr['displayName'] ?? $this->displayName;
        $this->passwordHash = $arr['passwordHash'] ?? $this->passwordHash;
        $this->email = $arr['email'] ?? $this->email;
        $this->role = $arr['role'] ?? $this->role;
        $this->failedLogins = $arr['failedLogins'] ?? $this->failedLogins;
        $this->lockedUntil = isset($arr['lockedUntil']) ? \tecla\util\dbParseDateTime($arr['lockedUntil']) : $this->lockedUntil;
        $this->disabledOn = isset($arr['disabledOn']) ? \tecla\util\dbParseDateTime($arr['disabledOn']) : $this->disabledOn;
        $this->verifiedOn = isset($arr['verifiedOn']) ? \tecla\util\dbParseDateTime($arr['verifiedOn']) : $this->verifiedOn;
        $this->lastLoginOn = isset($arr['lastLoginOn']) ? \tecla\util\dbParseDateTime($arr['lastLoginOn']) : $this->lastLoginOn;
        $this->lastLoginFrom = $arr['lastLoginFrom'] ?? $this->lastLoginFrom;
        $this->metaVersion = $arr['metaVersion'] ?? $this->metaVersion;
        $this->metaCreatedOn = isset($arr['metaCreatedOn']) ? \tecla\util\dbParseDateTime($arr['metaCreatedOn']) : $this->metaCreatedOn;
        $this->metaUpdatedOn = isset($arr['metaUpdatedOn']) ? \tecla\util\dbParseDateTime($arr['metaUpdatedOn']) : $this->metaUpdatedOn;
    }

    public static function createFromArray($arr)
    {
        $obj = new User();
        $obj->fromArray($arr);
        return $obj;
    }
}

class UserDAO
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
    `displayName`,
    `passwordHash`,
    `email`,
    `role`,
    `failedLogins`,
    DATE_FORMAT(`lockedUntil`, '%Y-%m-%dT%H:%i:%S') as `lockedUntil`,
    DATE_FORMAT(`disabledOn`, '%Y-%m-%dT%H:%i:%S') as `disabledOn`,
    DATE_FORMAT(`verifiedOn`, '%Y-%m-%dT%H:%i:%S') as `verifiedOn`,
    DATE_FORMAT(`lastLoginOn`, '%Y-%m-%dT%H:%i:%S') as `lastLoginOn`,
    `lastLoginFrom`,
    `metaVersion`,
    DATE_FORMAT(`metaCreatedOn`, '%Y-%m-%dT%H:%i:%S') as `metaCreatedOn`,
    DATE_FORMAT(`metaUpdatedOn`, '%Y-%m-%dT%H:%i:%S') as `metaUpdatedOn`
FROM `users`
ORDER BY `displayName` ASC
HERE;
        $rows = $this->db->query($sql);
        foreach ($rows as $row) {
            $results[] = User::createFromArray($row);
        }
        return $results;
    }

    public function loadAllForBooking()
    {
        $results = array();
        $sql = <<<HERE
SELECT
    `u`.`id`,
    CASE
        WHEN `g`.`gameid` IS NULL OR `u`.`role` = 'guest'
        THEN `u`.`displayName`
        ELSE CONCAT(`u`.`displayName`, ' (', `g`.`status`, ' on ', `g`.`date`, ')')
    END AS `displayName`,
    `passwordHash`,
    `email`,
    `role`,
    `failedLogins`,
    DATE_FORMAT(`lockedUntil`, '%Y-%m-%dT%H:%i:%S') as `lockedUntil`,
    DATE_FORMAT(`disabledOn`, '%Y-%m-%dT%H:%i:%S') as `disabledOn`,
    DATE_FORMAT(`verifiedOn`, '%Y-%m-%dT%H:%i:%S') as `verifiedOn`,
    DATE_FORMAT(`lastLoginOn`, '%Y-%m-%dT%H:%i:%S') as `lastLoginOn`,
    `lastLoginFrom`,
    `u`.`metaVersion`,
    DATE_FORMAT(`u`.`metaCreatedOn`, '%Y-%m-%dT%H:%i:%S') as `metaCreatedOn`,
    DATE_FORMAT(`u`.`metaUpdatedOn`, '%Y-%m-%dT%H:%i:%S') as `metaUpdatedOn`
FROM `users` AS `u`
LEFT JOIN (
    SELECT `users`.`id` AS `userid`, MIN(`games`.`startTime`) AS `startTime`, `games`.`id` AS `gameid`, `games`.`status`, DATE_FORMAT(`games`.`startTime`, '%Y-%m-%d %H:%i') as `date`
    FROM `users`
    LEFT JOIN `games` ON
            `games`.`startTime` >= CURRENT_TIMESTAMP()
        AND `games`.`status` IN ('regular', 'freegame')
        AND (
               `users`.`id` = `games`.`player1_id`
            OR `users`.`id` = `games`.`player2_id`
            OR `users`.`id` = `games`.`player3_id`
            OR `users`.`id` = `games`.`player4_id`
        )
        GROUP BY `users`.`id`
) AS `g` ON `g`.`userid` = `u`.`id`
WHERE
    `u`.`disabledOn` IS NULL
ORDER BY `displayName` ASC
HERE;
        $rows = $this->db->query($sql);
        foreach ($rows as $row) {
            $results[] = User::createFromArray($row);
        }
        return $results;
    }

    public function loadByEmail($email)
    {
        $sql = <<<HERE
SELECT
    `id`,
    `displayName`,
    `passwordHash`,
    `email`,
    `role`,
    `failedLogins`,
    DATE_FORMAT(`lockedUntil`, '%Y-%m-%dT%H:%i:%S') as `lockedUntil`,
    DATE_FORMAT(`disabledOn`, '%Y-%m-%dT%H:%i:%S') as `disabledOn`,
    DATE_FORMAT(`verifiedOn`, '%Y-%m-%dT%H:%i:%S') as `verifiedOn`,
    DATE_FORMAT(`lastLoginOn`, '%Y-%m-%dT%H:%i:%S') as `lastLoginOn`,
    `lastLoginFrom`,
    `metaVersion`,
    DATE_FORMAT(`metaCreatedOn`, '%Y-%m-%dT%H:%i:%S') as `metaCreatedOn`,
    DATE_FORMAT(`metaUpdatedOn`, '%Y-%m-%dT%H:%i:%S') as `metaUpdatedOn`
FROM `users`
WHERE LOWER(`email`) = LOWER(:email)
HERE;
        $row = $this->db->querySingle($sql, array('email' => $email));
        return is_null($row) ? null : User::createFromArray($row);
    }

    public function loadById($id)
    {
        if (is_null($id)) {
            return null;
        }

        $sql = <<<HERE
SELECT
    `id`,
    `displayName`,
    `passwordHash`,
    `email`,
    `role`,
    `failedLogins`,
    DATE_FORMAT(`lockedUntil`, '%Y-%m-%dT%H:%i:%S') as `lockedUntil`,
    DATE_FORMAT(`disabledOn`, '%Y-%m-%dT%H:%i:%S') as `disabledOn`,
    DATE_FORMAT(`verifiedOn`, '%Y-%m-%dT%H:%i:%S') as `verifiedOn`,
    DATE_FORMAT(`lastLoginOn`, '%Y-%m-%dT%H:%i:%S') as `lastLoginOn`,
    `lastLoginFrom`,
    `metaVersion`,
    DATE_FORMAT(`metaCreatedOn`, '%Y-%m-%dT%H:%i:%S') as `metaCreatedOn`,
    DATE_FORMAT(`metaUpdatedOn`, '%Y-%m-%dT%H:%i:%S') as `metaUpdatedOn`
FROM `users`
WHERE `id` = :id
HERE;
        $row = $this->db->querySingle($sql, array('id' => $id));
        return is_null($row) ? null : User::createFromArray($row);
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
        $sql = "INSERT INTO `users` ($fields) VALUES ($placeholders)";
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
        $sql = "UPDATE `users` SET $fields WHERE `id` = :id AND `metaVersion` = :oldVersion";
        $this->db->execute($sql, $arr);
    }
}
