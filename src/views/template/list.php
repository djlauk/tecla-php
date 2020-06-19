<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------

?>
<h1>Templates</h1>

<table>
    <thead>
    <tr>
        <th scope="col">ID</th>
        <th scope="col">Weekday</th>
        <th scope="col">Time</th>
        <th scope="col">Court</th>
        <th scope="col"></th>
    </tr>
    </thead>
    <tbody>
  <?php foreach ($items as $item): ?>
    <?php
$link = $this->routeUrl("/templates/edit/{$item->id}");
$linkHistory = $this->routeUrl("/history/template/{$item->id}");
?>
    <tr>
        <td><a href="<?=$link?>"><?=$item->id?></a></td>
        <td><a href="<?=$link?>"><?=tecla\data\WEEKDAYS[$item->weekday]?></a></td>
        <td><a href="<?=$link?>"><?=$item->startTime?> - <?=$item->endTime?></a></td>
        <td><a href="<?=$link?>"><?=$item->court?></a></td>
        <td><a href="<?=$linkHistory?>">view history</a></td>
    </tr>
    <?php endforeach?>
  </tbody>
</table>

<div>
    <a class="button primary" href="<?=$this->routeUrl('/templates/add')?>">Add template</a>
    <a class="button secondary" href="<?=$this->routeUrl('/templates/generate-games')?>">Generate games</a>
</div>
