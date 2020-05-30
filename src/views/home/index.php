<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------

function getPlayers(\tecla\data\Game &$game, $userLookup)
{
    $p = array();
    if ($game->player1_id) {$p[] = htmlentities($userLookup[$game->player1_id]->displayName);}
    if ($game->player2_id) {$p[] = htmlentities($userLookup[$game->player2_id]->displayName);}
    if ($game->player3_id) {$p[] = htmlentities($userLookup[$game->player3_id]->displayName);}
    if ($game->player4_id) {$p[] = htmlentities($userLookup[$game->player4_id]->displayName);}
    return implode(', ', $p);
}
?>
<h1>Tennis Club App</h1>

<?php if (!is_null($user)): ?>
<p>
    Welcome, <?=$user->displayName?>!
    <?php if (count($nextGames) > 0): ?>
    <h2>Next game</h2>
    <p>Your next game is on: <a href="<?=$this->routeUrl("/game/view/" . $nextGames[0]->id)?>"><?=$nextGames[0]->startTime?>: <?=getPlayers($nextGames[0], $userLookup)?></a></p>
    <?php endif?>
</p>
<?php endif?>

<?php if (count($games) > 0): ?>
    <?php
function printWeekHeader($weekNumber)
{
    echo '<h2 class="week-header">Week ' . $weekNumber . '</h2>';
}
$lastWeek = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $games[0]->startTime)->format('W');
printWeekHeader($lastWeek);
?>
<ul class="tecla-list">
<?php foreach ($games as $g): ?>
    <?php
$start = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $g->startTime);
$end = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $g->endTime);
$thisWeek = $start->format('W');
if ($thisWeek !== $lastWeek) {
    $lastWeek = $thisWeek;
    echo "</ul>";
    printWeekHeader($lastWeek);
    echo "<ul class=\"tecla-list\">";
}
$statusClass = $g->status === GAME_AVAILABLE ? 'available' : 'taken';
$statusClass .= '-' . $start->format('H');
?>
    <li class="tecla-list-item">
        <div class="tecla-list-item-icon <?=$statusClass?>"></div>
        <div class="tecla-list-item-content">
        <a href="<?=$this->routeUrl("/game/view/{$g->id}")?>">
            <div><?=tecla\data\WEEKDAYS[$start->format('w')]?>, <?=$start->format('Y-m-d')?> <?=$start->format('H:i')?> - <?=$end->format('H:i')?>, <?=$g->court?></div>
            <div class="second-line"><?=$g->status?><?php if (!is_null($user) && ($g->status !== GAME_AVAILABLE)): ?> &mdash; <?=getPlayers($g, $userLookup)?><?php endif?></div>
        </a>
        </div>
    </li>
<?php endforeach?>
</ul>
<?php else: ?>
<p>No upcoming games scheduled right now. Check back later.</p>
<?php endif?>
