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
use function \tecla\util\widgetSelectUsers;
use function \tecla\util\widgetTextArea;
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
    <div><?=widgetSelectUsers('Player 2', 'player2_id', $allUsers, array('value' => $game->player2_id))?></div>
    <div><?=widgetSelectUsers('Player 3', 'player3_id', $allUsers, array('value' => $game->player3_id))?></div>
    <div><?=widgetSelectUsers('Player 4', 'player4_id', $allUsers, array('value' => $game->player4_id))?></div>
    <div><?=widgetTextArea('Notes', 'notes', array('value' => $game->notes))?></div>
    <div class="form-buttons">
        <button class="button primary" type="submit">Book</button>
    </div>
</form>
