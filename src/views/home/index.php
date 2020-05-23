<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------
?>
<h1>Tennis Club App</h1>

<?php if (!is_null($user)): ?>
<p>Welcome, <?=$user->displayName?>!</p>
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
$statusClass = $g->status === 'available' ? 'available' : 'taken';
$statusClass .= '-' . $start->format('H');
?>
    <li class="tecla-list-item">
        <div class="tecla-list-item-icon <?=$statusClass?>"></div>
        <div class="tecla-list-item-content">
            <div><?=tecla\data\WEEKDAYS[$start->format('w')]?>, <?=$start->format('Y-m-d')?></div>
            <div class="second-line"><?=$start->format('H:i')?> - <?=$end->format('H:i')?>, <?=$g->court?></div>
        </div>
    </li>
<?php endforeach?>
</ul>
<?php else: ?>
<p>No upcoming games scheduled right now. Check back later.</p>
<?php endif?>
