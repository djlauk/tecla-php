<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------

namespace tecla;

class AuditlogService
{
    private $auditlogdao;
    private $limeApp;
    public function __construct(\tecla\data\AuditlogDAO &$auditlogdao, \Lime\App &$app)
    {
        $this->auditlogdao = $auditlogdao;
        $this->limeApp = $app;
    }

    public function logAction($action, $user_id, $object = null, $message = null)
    {
        $entry = \tecla\data\Auditlog::createFromArray(array(
            'action' => $action,
            'user_id' => $user_id,
            'object' => $object,
            'message' => $message,
        ));
        $this->auditlogdao->insert($entry);
    }

}

$app->service('auditlogservice', function () use ($app) {
    return new AuditlogService($app['auditlogdao'], $app);
});
