<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------

namespace tecla;

class DataService
{
    private $db;
    private $auditlogdao;
    private $gamedao;
    private $objecthistorydao;
    private $templatedao;
    private $userdao;

    public function __construct(\tecla\data\DBAccess &$db)
    {
        $this->db = $db;
        $this->auditlogdao = new \tecla\data\AuditlogDAO($db);
        $this->gamedao = new \tecla\data\GameDAO($db);
        $this->objecthistorydao = new \tecla\data\ObjectHistoryDAO($db);
        $this->templatedao = new \tecla\data\TemplateDAO($db);
        $this->userdao = new \tecla\data\UserDAO($db);
    }

    private function recordHistory($type, &$obj)
    {
        $entry = new \tecla\data\ObjectHistory();
        $entry->type = $type;
        $entry->id = $obj->id;
        $entry->version = $obj->metaVersion;
        $entry->data = json_encode($obj->toArray(), JSON_PRETTY_PRINT);
        $this->objecthistorydao->insert($entry);
    }

    // ---------- auditlog ----------

    public function countAuditlogEntries()
    {
        return $this->auditlogdao->count();
    }

    public function loadAuditlogPage($pageSize = 100, $offset = 0)
    {
        return $this->auditlogdao->loadPage($pageSize, $offset);
    }

    public function loadAuditlogById($id)
    {
        return $this->auditlogdao->loadById($id);
    }

    public function insertAuditlog(\tecla\data\Auditlog &$obj)
    {
        return $this->auditlogdao->insert($obj);
    }

    // ---------- games ----------

    public function loadAllGamesAfter($timestamp, $maxgames)
    {
        return $this->gamedao->loadAllAfter($timestamp, $maxgames);
    }

    public function loadGameById($id)
    {
        return $this->gamedao->loadById($id);
    }

    public function loadFutureGamesForUser($userId, $status)
    {
        return $this->gamedao->loadFutureGamesForUser($userId, $status);
    }

    public function getLastGame()
    {
        return $this->gamedao->getLastGame();
    }

    public function loadGuestGames(\DateTimeImmutable $start, \DateTimeImmutable $end, $userId = null)
    {
        return $this->gamedao->loadGuestGames($start->format('Y-m-d') . 'T00:00:00', $end->format('Y-m-d') . 'T23:59:59', $userId);
    }

    public function insertGame(\tecla\data\Game &$obj)
    {
        $newId = $this->gamedao->insert($obj);
        $this->recordHistory('game', $obj);
        return $newId;
    }

    public function updateGame(\tecla\data\Game &$obj)
    {
        $this->gamedao->update($obj);
        $this->recordHistory('game', $obj);
    }

    public function deleteGame(\tecla\data\Game &$obj)
    {
        $this->gamedao->delete($obj);
    }

    // ---------- objecthistory ----------

    public function loadHistoryOfObject($type, $id)
    {
        return $this->objecthistorydao->loadHistoryOfObject($type, $id);
    }

    // ---------- templates ----------

    public function loadAllTemplates()
    {
        return $this->templatedao->loadAll();
    }

    public function loadTemplateById($id)
    {
        return $this->templatedao->loadById($id);
    }

    public function insertTemplate(\tecla\data\Template &$obj)
    {
        $newId = $this->templatedao->insert($obj);
        $this->recordHistory('template', $obj);
        return $newId;
    }

    public function updateTemplate(\tecla\data\Template &$obj)
    {
        $this->templatedao->update($obj);
        $this->recordHistory('template', $obj);
    }

    public function deleteTemplate(\tecla\data\Template &$obj)
    {
        $this->templatedao->delete($obj);
    }

    // ---------- users ----------

    public function loadAllUsers()
    {
        return $this->userdao->loadAll();
    }

    public function loadAllUsersForBooking()
    {
        return $this->userdao->loadAllForBooking();
    }

    public function loadUserByEmail($email)
    {
        return $this->userdao->loadByEmail($email);
    }

    public function loadUserById($id)
    {
        return $this->userdao->loadById($id);
    }

    public function insertUser(\tecla\data\User &$obj)
    {
        $newId = $this->userdao->insert($obj);
        $this->recordHistory('user', $obj);
        return $newId;
    }

    public function updateUser(\tecla\data\User $obj)
    {
        $this->userdao->update($obj);
        $this->recordHistory('user', $obj);
    }
}

$app->service('dataservice', function () use ($app) {
    $db = $app['db'];
    return new DataService($db);
});
