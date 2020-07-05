<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------

use function \tecla\util\viewFormatTime;

function formInput($name, $label, $attrs = null)
{
    $safeName = htmlentities($name);
    $safeLabel = htmlentities($label);
    $safeAttrs = '';
    if (!is_null($attrs)) {
        foreach ($attrs as $k => $v) {
            $safeAttrs .= " $k=\"" . htmlentities($v) . '"';
        }
    }

    return <<<HERE
<div>
    <label for="$safeName">$safeLabel</label>
    <input name="$safeName" $safeAttrs>
</div>
HERE;
}

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

function formSelectUsers($name, $label, $value, $allUsers)
{
    $options = array('' => '-- no one --');
    foreach ($allUsers as $u) {
        $options[$u->id] = $u->displayName;
    }
    return formSelect($name, $label, $value, $options);
}
?>

<h1>Edit game</h1>

<?php if ($problem): ?>
<div class="error message">
<p><strong>Updating game failed. Sorry.</strong></p>

<p>Reason: <?=$problem?></p>
</div>
<?php endif?>

<form method="POST" action="<?=$this->routeUrl('/game/save')?>">
    <input type="hidden" name="id" value="<?=$game->id?>">
    <input type="hidden" name="metaVersion" value="<?=$game->metaVersion?>">
    <?=formInput('startTime', 'Start time', array('value' => viewFormatTime($game->startTime), 'required' => '', 'placeholder' => 'hh:mm'))?>
    <?=formInput('endTime', 'End time', array('value' => viewFormatTime($game->endTime), 'required' => '', 'placeholder' => 'hh:mm'))?>
    <?=formInput('court', 'Court', array('value' => $game->court, 'required' => '', 'placeholder' => 'Wimbledon'))?>
    <?=formSelect('status', 'Status', $game->status, GAME_STATUS_VALUES)?>

<h2>Players</h2>

<form method="POST" action="<?=$this->routeUrl("/game/save")?>">
    <input type="hidden" name="id" value="<?=$id?>">
    <input type="hidden" name="metaVersion" value="<?=$game->metaVersion?>">
    <?=formSelectUsers('player1_id', 'Player 1', $game->player1_id, $allUsers)?>
    <?=formSelectUsers('player2_id', 'Player 2', $game->player2_id, $allUsers)?>
    <?=formSelectUsers('player3_id', 'Player 3', $game->player3_id, $allUsers)?>
    <?=formSelectUsers('player4_id', 'Player 4', $game->player4_id, $allUsers)?>
    <?=formTextArea('notes', 'Notes', $game->notes)?>
    <button class="button primary" type="submit">Save</button>
</form>
