<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------

use function \tecla\util\viewFormatTime;
use function \tecla\util\widgetInput;
use function \tecla\util\widgetSelect;
use function \tecla\util\widgetSelectUsers;
use function \tecla\util\widgetTextArea;
use function \tecla\util\widgetTimeInput;
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
    <div><?=widgetTimeInput('Start time', 'startTime', array('value' => viewFormatTime($game->startTime)))?></div>
    <div><?=widgetTimeInput('End time', 'endTime', array('value' => viewFormatTime($game->endTime)))?></div>
    <div><?=widgetInput('Court', 'court', array('value' => $game->court, 'required' => true, 'placeholder' => 'Wimbledon'))?></div>
    <div><?=widgetSelect('Status', 'status', GAME_STATUS_VALUES, array('value' => $game->status))?></div>

<h2>Players</h2>

    <div><?=widgetSelectUsers('Player 1', 'player1_id', $allUsers, array('value' => $game->player1_id))?></div>
    <div><?=widgetSelectUsers('Player 2', 'player2_id', $allUsers, array('value' => $game->player2_id))?></div>
    <div><?=widgetSelectUsers('Player 3', 'player3_id', $allUsers, array('value' => $game->player3_id))?></div>
    <div><?=widgetSelectUsers('Player 4', 'player4_id', $allUsers, array('value' => $game->player4_id))?></div>
    <div><?=widgetTextArea('Notes', 'notes', array('value' => $game->notes))?></div>
    <div class="form-buttons">
        <button class="button primary" type="submit">Save</button>
    </div>
</form>
