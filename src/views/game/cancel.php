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
?>

<h1>Cancel booking</h1>

<table>
    <tr><td>Date:</td><td><?=viewFormatDate($game->startTime)?></td></tr>
    <tr><td>Start:</td><td><?=viewFormatTime($game->startTime)?></td></tr>
    <tr><td>End:</td><td><?=viewFormatTime($game->endTime)?></td></tr>
    <tr><td>Court:</td><td><?=htmlentities($game->court)?></td></tr>
    <tr><td>Status:</td><td><?=htmlentities($game->status)?></td></tr>
    <tr><td>Notes:</td><td><?=str_replace("\n", "<br>", htmlentities($game->notes))?></td></tr>
</table>

<h2>Players</h2>
<table>
    <tr><td>Player 1:</td><td><?=is_null($player1) ? '' : $player1->displayName?></td></tr>
    <tr><td>Player 2:</td><td><?=is_null($player2) ? '' : $player2->displayName?></td></tr>
    <tr><td>Player 3:</td><td><?=is_null($player3) ? '' : $player3->displayName?></td></tr>
    <tr><td>Player 4:</td><td><?=is_null($player4) ? '' : $player4->displayName?></td></tr>
</table>

<form method="POST" action="<?=$this->routeUrl('/game/cancel')?>">
    <input type="hidden" name="id" value="<?=$game->id?>">
    <button class="button primary">Confirm cancellation</button>
</form>
