<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------

function getPlayer($userId, $userLookup)
{
    if (is_null($userId)) {return '';}
    return htmlentities($userLookup[$userId]->displayName);
}
?>
<h1>Bulk edit games</h1>
<?php if ($problem): ?>
<div class="error message">
<p><strong>Bulk operation failed. Sorry.</strong></p>

<p>Reason: <?=$problem?></p>
</div>
<?php endif?>
<form method="POST" action="<?=$this->routeUrl('/game/bulk-edit')?>">

<label>Games</label>
<table class="fullwidth">
    <tr>
        <th></th>
        <th></th>
        <th>Weekday</th>
        <th>Date</th>
        <th>Time</th>
        <th>Court</th>
        <th>Status</th>
        <th>Player 1</th>
        <th>Player 2</th>
        <th>Player 3</th>
        <th>Player 4</th>
    </tr>
<?php foreach ($games as $g): ?>
    <?php
$start = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $g->startTime);
$end = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $g->endTime);
$statusClass = $g->status === GAME_AVAILABLE ? 'available' : 'taken';
$statusClass .= '-' . $start->format('H');
?>
    <tr>
        <td><input type="checkbox" name="selectedGames[]" value="<?=$g->id?>"></td>
        <td><div class="tecla-list-item-icon small <?=$statusClass?>"></div></td>
        <td><a href="<?=$this->routeUrl("/game/edit/{$g->id}")?>"><?=tecla\data\WEEKDAYS[$start->format('w')]?></a></td>
        <td><?=$start->format('Y-m-d')?></td>
        <td><?=$start->format('H:i')?> - <?=$end->format('H:i')?></td>
        <td><?=htmlentities($g->court)?></td>
        <td><?=$g->status?></td>
        <td><?=getPlayer($g->player1_id, $userLookup)?></td>
        <td><?=getPlayer($g->player2_id, $userLookup)?></td>
        <td><?=getPlayer($g->player3_id, $userLookup)?></td>
        <td><?=getPlayer($g->player4_id, $userLookup)?></td>
    </tr>
<?php endforeach?>
</table>
<div>
    <label for="operation">Operation</label>
    <select name="operation" id="operation">
        <option value="cancel">Cancel games</option>
        <option value="block">Block slots</option>
    </select>
</div>
<div class="form-buttons">
    <button class="button primary">Bulk change</button>
</div>
</form>
