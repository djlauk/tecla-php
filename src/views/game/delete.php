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
?>

<h1>Delete game</h1>

<table>
    <tr><td>Date:</td><td><?=$start->format('Y-m-d')?></td></tr>
    <tr><td>Start:</td><td><?=$start->format('H:i')?></td></tr>
    <tr><td>End:</td><td><?=$end->format('H:i')?></td></tr>
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

<form method="POST" action="<?=$this->routeUrl('/game/delete')?>">
    <input type="hidden" name="id" value="<?=$game->id?>">
    <button class="button primary">Confirm deletion</button>
</form>
