<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------

use function \tecla\util\viewFormatDate;
use function \tecla\util\viewFormatTime;

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

function formSelectUsers($name, $label, $allUsers, $value, \tecla\Data\User &$currentUser)
{
    $options = array('' => '-- no one --');
    foreach ($allUsers as $u) {
        if ($u->id === $currentUser->id) {
            continue;
        }
        $options[$u->id] = $u->displayName;
    }
    return formSelect($name, $label, $value, $options);
}
?>

<h1>Book game</h1>

<?php if ($problem): ?>
<div class="error message">
<p><strong>Booking game failed. Sorry.</strong></p>

<p>Reason: <?=$problem?></p>
</div>
<?php endif?>

<table>
    <tr><td>Date:</td><td><?=viewFormatDate($game->startTime)?></td></tr>
    <tr><td>Start:</td><td><?=viewFormatTime($game->startTime)?></td></tr>
    <tr><td>End:</td><td><?=viewFormatTime($game->endTime)?></td></tr>
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
    <?=formSelectUsers('player2_id', 'Player 2', $allUsers, $game->player2_id, $user)?>
    <?=formSelectUsers('player3_id', 'Player 3', $allUsers, $game->player3_id, $user)?>
    <?=formSelectUsers('player4_id', 'Player 4', $allUsers, $game->player4_id, $user)?>
    <?=formTextArea('notes', 'Notes', '')?>
    <button class="button primary" type="submit">Book</button>
</form>
