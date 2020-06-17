<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------

namespace tecla\util;

define('ISODATE', '%Y-%m-%d');
define('ISODATETIME', '%Y-%m-%dT%H:%M:%S');
define('ISOTIME', '%H:%M');

function dbParseDateTime($s)
{
    $d = \DateTimeImmutable::createFromFormat('Y-m-d\\TH:i:s', $s);
    return $d;
}

function dbFormatDateTime(\DateTimeImmutable $d)
{
    return $d->format('Y-m-d\\TH:i:s');
}

function dbTime($ts = null)
{
    $result = new \DateTimeImmutable();
    if (!is_null($ts)) {
        $result = $result->setTimestamp($ts);
    }
    return $result;
}

function viewFormatLastLogin(\tecla\data\User &$user)
{
    if (is_null($user->lastLoginOn)) {
        return '';
    }
    return $user->lastLoginOn->format('Y-m-d H:i:s') . ' from ' . ($user->lastLoginFrom ?? 'unkown address');
}
