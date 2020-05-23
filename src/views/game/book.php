<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------

$start = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $game->startTime);
$end = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $game->endTime);

function formTextArea($name, $label, $value)
{
    $safeName = htmlentities($name);
    $safeLabel = htmlentities($label);
    $safeValue = htmlentities($value);
    return <<<HERE
<div>
    <label for="$safeName">$safeLabel</label>
    <textarea name="$safeName">$safeValue</textarea>
</div>
HERE;
}

function formSelect($name, $label, $value, $options)
{
    $safeName = htmlentities($name);
    $safeLabel = htmlentities($label);
    $safeValue = htmlentities($value);
    $optStr = '';
    foreach ($options as $k => $v) {
        $optStr .= "<option value=\"" . htmlentities($k) . "\"" . ($k == $value ? ' selected' : '') . ">" . htmlentities($v) . "</option>";
    }
    return <<<HERE
<div>
    <label for="$safeName">$safeLabel</label>
    <select name="$safeName" value="$safeValue">$optStr</select>
</div>
HERE;
}

function formSelectUsers($name, $label, $allUsers, \tecla\Data\User &$currentUser)
{
    $options = array('' => '-- no one --');
    foreach ($allUsers as $u) {
        if ($u->id === $currentUser->id) {
            continue;
        }
        $options[$u->id] = $u->displayName;
    }
    return formSelect($name, $label, '', $options);
}
?>

<h1>Book game</h1>

<table>
    <tr><td>Date:</td><td><?=$start->format('Y-m-d')?></td></tr>
    <tr><td>Start:</td><td><?=$start->format('H:i')?></td></tr>
    <tr><td>End:</td><td><?=$end->format('H:i')?></td></tr>
    <tr><td>Court:</td><td><?=htmlentities($game->court)?></td></tr>
    <tr><td>Status:</td><td><?=htmlentities($game->status)?></td></tr>
</table>

<h2>Players</h2>

<form method="POST" action="<?=$this->routeUrl("/game/book")?>">
    <input type="hidden" name="id" value="<?=$id?>">
    <input type="hidden" name="metaVersion" value="<?=$game->metaVersion?>">
    <div>
        <label>Player 1</label>
        <div><?=$user->displayName?></div>
    </div>
    <?=formSelectUsers('player2_id', 'Player 2', $allUsers, $user)?>
    <?=formSelectUsers('player3_id', 'Player 3', $allUsers, $user)?>
    <?=formSelectUsers('player4_id', 'Player 4', $allUsers, $user)?>
    <?=formTextArea('notes', 'Notes', '')?>
    <button class="button primary" type="submit">Book</button>
</form>
