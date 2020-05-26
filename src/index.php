<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------

// Main file for tecla. The entry point. It works like this:
//
// 1.  Load the Lime framework and create a Lime app.
//
// 2.  Load all files that make up this application.
//     Each loaded file **can safely assume**, that there is a Lime
//     app in the global $app variable, and populate it accordingly.
//
// 3.  Run the app.

require_once __DIR__ . '/vendor/Lime/App.php';
$app = new Lime\App();

// Because of Lime's DI capabilities and lazy evalution the order
// doesn't really matter, but I still think, this is the right order...
require_once __DIR__ . '/config.php';
date_default_timezone_set($app['config.timezone']);
if ($app['config.debug']) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}

$app('session')->init();

require_once __DIR__ . '/data/index.php';
require_once __DIR__ . '/services/index.php';
require_once __DIR__ . '/controllers/index.php';

$app->run();
