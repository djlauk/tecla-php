<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------

namespace tecla\util;

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
    return viewFormatTimestamp($user->lastLoginOn) . ' from ' . ($user->lastLoginFrom ?? 'unkown address');
}

function viewFormatWeekday(\DateTimeImmutable $d)
{
    return \tecla\data\WEEKDAYS[$d->format('w')];
}

function viewFormatDateTime(\DateTimeImmutable $d)
{
    return $d->format('d.m.Y H:i');
}

function viewFormatDate(\DateTimeImmutable $d)
{
    return $d->format('d.m.Y');
}

function viewFormatTime(\DateTimeImmutable $d)
{
    return $d->format('H:i');
}

function viewFormatTimestamp(\DateTimeImmutable $d)
{
    return $d->format('d.m.Y H:i:s');
}

function viewFormatDateHomeList(\DateTimeImmutable $d)
{
    return $d->format('d. F');
}

function viewFormatDateHomeNextGame(\DateTimeImmutable $d)
{
    return $d->format('d. F H:i');
}

function viewGameStatusClass(\tecla\data\Game &$g)
{
    $statusClass = $g->status === GAME_AVAILABLE ? 'available' : 'taken';
    $statusClass .= '-' . $g->startTime->format('H');
    return $statusClass;
}
