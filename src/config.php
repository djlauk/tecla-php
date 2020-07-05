<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------

// ======================================================================
//
// THIS IS A TEMPLATE CONFIGURATION !
//
// DO NOT DELETE THIS FILE!
//
// Instead, copy it to a file called `config.local.php` and make
// adjustments _there_.
//
// ======================================================================

// this DB configuration is setup for the local docker-compose LAMP stack
$app["config.pdo"] = array(
    'dsn' => 'mysql:host=db;dbname=tennis',
    'user' => 'root',
    'password' => 'my_secret_pw_shh',
);
$app["config.debug"] = false;

$app["config.defaultrole"] = 'member';

$app["config.freegame"] = 6 * 60 * 60; // 6 hours in seconds

// for the list of timezone identifiers see https://www.php.net/manual/en/timezones.php
$app["config.timezone"] = 'Europe/Zurich';

// maximum number of games on home screen
$app["config.maxgames"] = 300;

// password requirements
$app["config.passwordrules"] = array(
    'enabled' => true,
    'minlength' => 6, // set to 0 to disable length check
    'needsUppercase' => false,
    'needsLowercase' => false,
    'needsNumber' => false,
    'needsSpecial' => false,
    'needsNumClasses' => 3, // requires 3 out of 4 character classes
);

$app['config.auditlog'] = array(
    'pagesize' => 20,
);

// date formats for displays
// for formatting options see https://www.php.net/manual/en/function.date.php
$app['config.dateformat'] = array(
    'date' => 'd.m.Y',
    'datetime' => 'd.m.Y H:i',
    'time' => 'H:i',
    'timestamp' => 'd.m.Y H:i:s',
    'homenextgame' => 'd. F H:i',
    'homelist' => 'd. F',
);

// ======================================================================
//
// DO NOT REMOVE THIS PART BELOW!
//
// ======================================================================

// override with local config, which is deliberately **NOT** put under
// version control
if (file_exists(__DIR__ . '/config.local.php')) {
    require_once __DIR__ . '/config.local.php';
}
