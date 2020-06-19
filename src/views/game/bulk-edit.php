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

<label>Select games</label>
<table class="fullwidth">
    <thead>
    <tr>
        <th scope="col"></th>
        <th scope="col"></th>
        <th scope="col">ID</th>
        <th scope="col">Weekday</th>
        <th scope="col">Date</th>
        <th scope="col">Time</th>
        <th scope="col">Court</th>
        <th scope="col">Status</th>
        <th scope="col">Player 1</th>
        <th scope="col">Player 2</th>
        <th scope="col">Player 3</th>
        <th scope="col">Player 4</th>
        <th scope="col"></th>
    </tr>
    </thead>
    <tbody>
<?php foreach ($games as $g): ?>
    <?php
$statusClass = $g->status === GAME_AVAILABLE ? 'available' : 'taken';
$statusClass .= '-' . $g->startTime->format('H');
$linkHistory = $this->routeUrl("/history/game/{$g->id}");
?>
    <tr>
        <td><input type="checkbox" name="selectedGames[]" value="<?=$g->id?>"></td>
        <td><div class="tecla-list-item-icon small <?=$statusClass?>"></div></td>
        <td><a href="<?=$this->routeUrl("/game/edit/{$g->id}")?>"><?=$g->id?></a></td>
        <td><a href="<?=$this->routeUrl("/game/edit/{$g->id}")?>"><?=tecla\data\WEEKDAYS[$g->startTime->format('w')]?></a></td>
        <td><?=$g->startTime->format('Y-m-d')?></td>
        <td><?=$g->startTime->format('H:i')?> - <?=$g->endTime->format('H:i')?></td>
        <td><?=htmlentities($g->court)?></td>
        <td><?=$g->status?></td>
        <td><?=getPlayer($g->player1_id, $userLookup)?></td>
        <td><?=getPlayer($g->player2_id, $userLookup)?></td>
        <td><?=getPlayer($g->player3_id, $userLookup)?></td>
        <td><?=getPlayer($g->player4_id, $userLookup)?></td>
        <td><a href="<?=$linkHistory?>">view history</a></td>
    </tr>
<?php endforeach?>
    </tbody>
</table>
<div>
    <label for="operation">Select operation</label>
    <select name="operation" id="operation">
        <option value="cancel">Cancel games</option>
        <option value="block">Block slots</option>
        <option value="delete">Delete slots</option>
    </select>
</div>
<div class="form-buttons">
    <button class="button primary">Bulk change</button>
</div>
</form>
