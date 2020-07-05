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
    global $app;
    return $d->format($app['config.dateformat/datetime']);
}

function viewFormatDate(\DateTimeImmutable $d)
{
    global $app;
    return $d->format($app['config.dateformat/date']);
}

function viewFormatTime(\DateTimeImmutable $d)
{
    global $app;
    return $d->format($app['config.dateformat/time']);
}

function viewFormatTimestamp(\DateTimeImmutable $d)
{
    global $app;
    return $d->format($app['config.dateformat/timestamp']);
}

function viewFormatDateHomeList(\DateTimeImmutable $d)
{
    global $app;
    return $d->format($app['config.dateformat/homelist']);
}

function viewFormatDateHomeNextGame(\DateTimeImmutable $d)
{
    global $app;
    return $d->format($app['config.dateformat/homenextgame']);
}

function viewGameStatusClass(\tecla\data\Game &$g)
{
    $statusClass = $g->status === GAME_AVAILABLE ? 'available' : 'taken';
    $statusClass .= '-' . $g->startTime->format('H');
    return $statusClass;
}

function viewParseDate($s)
{
    global $app;
    $d = \DateTimeImmutable::createFromFormat($app['config.dateformat/date'], $s);
    return $d;
}

function safeHtmlAttributes($attrs = null)
{
    if (is_null($attrs)) {
        return '';
    }

    $safeAttrs = '';
    foreach ($attrs as $k => $v) {
        // boolean attributes (e.g. required on an input)
        // are represented by just present or absent
        if ($v === true) { // real true, not just 'truthy'
            $safeAttrs .= ' ' . htmlentities($k);
        } elseif ($v === false) { // real false value, not just 'falsy'
            continue;
        } else { // other types of values are strings
            $safeAttrs .= ' ' . htmlentities($k) . '="' . htmlentities($v) . '"';
        }
    }
    return $safeAttrs;
}

function widgetInput($label, $id, $attrs = null)
{
    $safeAttrs = safeHtmlAttributes($attrs);
    $safeLabel = htmlentities($label);
    $labelRequired = isset($attrs['required']) && $attrs['required'] === true ? 'required' : '';
    $safeId = htmlentities($id);

    $html = <<<HERE
<label for="$safeId" $labelRequired>$safeLabel</label>
<input id="$safeId" name="$safeId"$safeAttrs>
HERE;
    return $html;
}

function widgetSelect($label, $id, $options, $attrs = null)
{
    $safeAttrs = safeHtmlAttributes($attrs);
    $safeLabel = htmlentities($label);
    $labelRequired = isset($attrs['required']) && $attrs['required'] === true ? 'required' : '';
    $safeId = htmlentities($id);
    $value = $attrs['value'] ?? '';
    $safeValue = htmlentities($value);
    $optStr = '';
    foreach ($options as $k => $v) {
        $optStr .= "<option value=\"" . htmlentities($k) . "\"" . ($k == $value ? ' selected' : '') . ">" . htmlentities($v) . "</option>";
    }
    return <<<HERE
<label for="$safeId" $labelRequired>$safeLabel</label>
<select id="$safeId" name="$safeId" value="$safeValue">$optStr</select>
HERE;
}

function widgetSelectUsers($label, $id, $allUsers, $attrs = null)
{
    $options = array('' => '-- no one --');
    foreach ($allUsers as $u) {
        $options[$u->id] = $u->displayName;
    }
    return widgetSelect($label, $id, $options, $attrs);
}

function widgetTextArea($label, $id, $attrs = null)
{
    $safeAttrs = safeHtmlAttributes($attrs);
    $safeLabel = htmlentities($label);
    $labelRequired = isset($attrs['required']) && $attrs['required'] === true ? 'required' : '';
    $safeId = htmlentities($id);
    $value = $attrs['value'] ?? '';
    $safeValue = htmlentities($value);
    return <<<HERE
<label for="$safeId" $labelRequired>$safeLabel</label>
<textarea id="$safeId" name="$safeId">$safeValue</textarea>
HERE;
}

function widgetTimeInput($label, $id, $attrs = null)
{
    $options = array();
    for ($i = 0; $i < 24; $i++) {
        $fullhour = ($i < 10 ? '0' . $i : $i) . ':00';
        $halfhour = ($i < 10 ? '0' . $i : $i) . ':30';
        $options[$fullhour] = $fullhour;
        $options[$halfhour] = $halfhour;
    }
    return widgetSelect($label, $id, $options, $attrs);
}
