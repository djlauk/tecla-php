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

?>
<script src="https://code.highcharts.com/highcharts.js"></script>

<h1>Times and usage for <?=viewFormatDate($start)?>
 - <?=viewFormatDate($end)?></h1>

<h2>Change time range</h2>
<form method="POST" action="<?=$this->routeUrl('reports/times-usage')?>">
    <div><?=widgetInput('Start', 'start', array('value' => viewFormatDate($start)))?></div>
    <div><?=widgetInput('End', 'end', array('value' => viewFormatDate($end)))?></div>
    <div class="form-buttons">
        <button class="button primary" type="submit">Change</button>
    </div>
</form>
<hr>


<ul>
    <li><a href="#chart-by-weekday">Chart by weekday</a></li>
    <li><a href="#breakdown-by-status">Breakdown by status</a></li>
    <li><a href="#breakdown-by-hour">Breakdown by hour</a></li>
    <li><a href="#breakdown-by-weekday">Breakdown by weekday</a></li>
    <li><a href="#breakdown-detailed">Detailed breakdown</a></li>
</ul>


<h2 id="chart-by-weekday">Chart by weekday</h2>

<div id="chart-by-weekday-container"></div>
<script type="module">
    const stats = <?=json_encode($stats)?>;
    const weekdays = <?=json_encode(\tecla\data\WEEKDAYS)?>;
    const statusTypes = Object.keys(stats["total_by_gamestatus"]);
    const chartTitle = "<?=viewFormatDate($start)?> - <?=viewFormatDate($end)?>";
    const series = statusTypes.map((status) => {
        const data = weekdays.map((_, wd) => {
            let sum = 0;
            for (let h=0; h<24; h++) {
                 sum += stats[wd][h][status];
            }
            return sum;
        })
        return { name: status, data }
    });
    const chart = Highcharts.chart("chart-by-weekday-container", {
        chart: { type: "column" },
        title: { text: chartTitle },
        xAxis: { categories: weekdays },
        yAxis: { title: { text: "Number of slots/games" }},
        plotOptions: { column: { stacking: "normal" } },
        series
    });
</script>

<h2 id="breakdown-by-status">Breakdown by status</h2>

<table class="data-table fullwidth">
<thead>
  <tr>
    <th>Status</th><th style="width:7ch">Total</th>
  </tr>
</thead>
<tbody>
<?php foreach ($stats['total_by_gamestatus'] as $status => $val): ?>
    <tr><td><?=htmlentities($status)?></td><td><?=htmlentities($val)?></td></tr>
<?php endforeach ?>
    <tr><td>TOTAL</td><td><?=htmlentities($stats['total'])?></td></tr>
</tbody>
</table>


<h2 id="breakdown-by-hour">Breakdown by hour</h2>

<table class="data-table fullwidth">
<thead>
  <tr>
    <th>Hour</th><th style="width:7ch">Total</th>
  </tr>
</thead>
<tbody>
<?php foreach ($stats['total_by_hour'] as $hour => $val): ?>
    <tr><td><?=htmlentities($hour)?></td><td><?=htmlentities($val)?></td></tr>
<?php endforeach ?>
    <tr><td>TOTAL</td><td><?=htmlentities($stats['total'])?></td></tr>
</tbody>
</table>


<h2 id="breakdown-by-weekday">Breakdown by weekday</h2>

<table class="data-table fullwidth">
<thead>
<tr>
    <th>Weekday</th><th style="width:7ch">Total</th>
</tr>
</thead>
<tbody>
<?php foreach ($stats['total_by_weekday'] as $weekday => $val): ?>
    <tr><td><?=htmlentities(\tecla\data\WEEKDAYS[$weekday])?></td><td><?=htmlentities($val)?></td></tr>
    <?php endforeach ?>
    <tr><td>TOTAL</td><td><?=htmlentities($stats['total'])?></td></tr>
</tbody>
</table>


<h2 id="breakdown-detailed">Detailed breakdown</h2>

<table class="data-table fullwidth">
<thead>
    <tr>
        <th></th>
        <th colspan="6">Sunday</th>
        <th colspan="6">Monday</th>
        <th colspan="6">Tuesday</th>
        <th colspan="6">Wednesday</th>
        <th colspan="6">Thursday</th>
        <th colspan="6">Friday</th>
        <th colspan="6">Saturay</th>
        <th></th>
    </tr>
    <tr>
        <th>Hour</th>
<?php for ($weekday = 0; $weekday < 7; $weekday++): ?>
        <th>available</th>
        <th>free</th>
        <th>regular</th>
        <th>training</th>
        <th>tournament</th>
        <th>blocked</th>
<?php endfor ?>
        <th>Hour total</th>
    </tr>
</thead>
<tbody>
<?php for ($hour = 0; $hour < 24; $hour++): ?>
    <tr>
        <td><?=$hour?></td>
<?php for ($weekday = 0; $weekday < 7; $weekday++): ?>
        <td><?=$stats[$weekday][$hour][GAME_AVAILABLE]?></td>
        <td><?=$stats[$weekday][$hour][GAME_FREE]?></td>
        <td><?=$stats[$weekday][$hour][GAME_REGULAR]?></td>
        <td><?=$stats[$weekday][$hour][GAME_TRAINING]?></td>
        <td><?=$stats[$weekday][$hour][GAME_TOURNAMENT]?></td>
        <td><?=$stats[$weekday][$hour][GAME_BLOCKED]?></td>
<?php endfor ?>
        <td><?=$stats['total_by_hour'][$hour]?></td>
    </tr>
<?php endfor ?>
    <tr>
        <td>TOTAL</td>
<?php for ($weekday = 0; $weekday < 7; $weekday++): ?>
        <td colspan="6"><?=$stats['total_by_weekday'][$weekday]?></td>
<?php endfor ?>
        <td><?=$stats['total']?></td>
    </tr>
</tbody>
</table>
