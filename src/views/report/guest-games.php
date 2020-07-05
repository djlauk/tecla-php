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
use function \tecla\util\widgetInput;

function userHeader(\tecla\data\User &$u)
{
    $name = htmlentities($u->displayName);
    return <<<HERE
<h2>Guest games for: $name</h2>
HERE;
}

function tableStart()
{
    return <<<HERE
<table>
<thead>
    <tr>
        <th scope="col">Date</th>
        <th scope="col">Time</th>
        <th scope="col">Status</th>
        <th scope="col">Player 1</th>
        <th scope="col">Player 2</th>
        <th scope="col">Player 3</th>
        <th scope="col">Player 4</th>
        <th scope="col">Notes</th>
    </tr>
</thead>
<tbody>
HERE;
}

function tableEnd()
{
    return "</tbody></table>";
}

function subtotal($gameCount)
{
    return "<p><strong>Total: $gameCount</strong></p>";
}
?>
<h1>Guest games <?=viewFormatDate($start)?>
 - <?=viewFormatDate($end)?></h1>

<?php if (count($games) === 0): ?>
<p>No guest games in this time range.</p>
<?php else: ?>
<?php
$lastUser = $games[0]->player1_id;
$gameCount = 0;
echo userHeader($userLookup[$lastUser]);
echo tableStart();
?>
<?php foreach ($games as $g): ?>
<?php
if ($g->player1_id !== $lastUser) {
    echo tableEnd();
    echo subtotal($gameCount);
    $lastUser = $g->player1_id;
    $gameCount = 0;
    echo "<hr>";
    echo userHeader($userLookup[$lastUser]);
    echo tableStart();
}
$gameCount++;
?>
    <tr>
        <td><?=viewFormatDate($g->startTime)?></td>
        <td><?=viewFormatTime($g->startTime)?> - <?=viewFormatTime($g->endTime)?></td>
        <td><?=htmlentities($g->status)?></td>
        <td><?=is_null($g->player1_id) ? '' : htmlentities($userLookup[$g->player1_id]->displayName)?></td>
        <td><?=is_null($g->player2_id) ? '' : htmlentities($userLookup[$g->player2_id]->displayName)?></td>
        <td><?=is_null($g->player3_id) ? '' : htmlentities($userLookup[$g->player3_id]->displayName)?></td>
        <td><?=is_null($g->player4_id) ? '' : htmlentities($userLookup[$g->player4_id]->displayName)?></td>
        <td><?=str_replace("\n", '<br>', htmlentities($g->notes))?></td>
    </tr>
<?php endforeach?>
<?=tableEnd()?>
<?=subtotal($gameCount)?>
<?php endif?>
<hr>
<h2>Change time range</h2>
<form method="POST" action="<?=$this->routeUrl('reports/guest-games')?>">
    <div><?=widgetInput('Start', 'start', array('value' => viewFormatDate($start)))?></div>
    <div><?=widgetInput('End', 'end', array('value' => viewFormatDate($end)))?></div>
    <div class="form-buttons">
        <button class="button primary" type="submit">Change</button>
    </div>
</form>
