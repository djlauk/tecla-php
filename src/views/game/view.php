<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------

?>

<h1>Game details</h1>

<table>
    <tr><td>Date:</td><td><?=$game->startTime->format('Y-m-d')?></td></tr>
    <tr><td>Start:</td><td><?=$game->startTime->format('H:i')?></td></tr>
    <tr><td>End:</td><td><?=$game->endTime->format('H:i')?></td></tr>
    <tr><td>Court:</td><td><?=htmlentities($game->court)?></td></tr>
    <tr><td>Status:</td><td><?=htmlentities($game->status)?></td></tr>
    <tr><td>Notes:</td><td><?=str_replace("\n", "<br>", htmlentities($game->notes))?></td></tr>
</table>

<?php /* only display players to logged in users, not to the world wide web at large */
if ($this['auth']->isLoggedIn()): ?>
<h2>Players</h2>
<table>
    <tr><td>Player 1:</td><td><?=is_null($player1) ? '' : $player1->displayName?></td></tr>
    <tr><td>Player 2:</td><td><?=is_null($player2) ? '' : $player2->displayName?></td></tr>
    <tr><td>Player 3:</td><td><?=is_null($player3) ? '' : $player3->displayName?></td></tr>
    <tr><td>Player 4:</td><td><?=is_null($player4) ? '' : $player4->displayName?></td></tr>
</table>
<?php endif?>
<div>
    <?php if ($canBook): ?><a class="button primary" href="<?=$this->routeUrl("/game/book/$id")?>">Book game</a><?php endif?>
    <?php if ($canCancel): ?><a class="button primary" href="<?=$this->routeUrl("/game/cancel/$id")?>">Cancel game</a><?php endif?>
    <?php if (!$canBook && $game->status === GAME_AVAILABLE && $this['auth']->hasRole('member')): ?>
        <?php if ($game->startTime->getTimestamp() < time()): ?>
        <div class="info message"><strong>You can&apos;t book this game.</strong><br>This game started in the past.</div>
        <?php elseif (count($nextGames) > 0): ?>
        <div class="info message"><strong>You can&apos;t book this game.</strong><br>Your next games is: <a href="<?=$this->routeUrl('/game/view/' . $nextGames[0]->id)?>"><?=$nextGames[0]->startTime->format('Y-m-d H:i')?></a></div>
        <?php endif?>
    <?php endif?>
</div>
