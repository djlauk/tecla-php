<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------

?>
<h1>Time slots</h1>

<p>Time slots are templates for generating new games.</p>

<ul class="tecla-list">
  <?php foreach ($items as $item): ?>
    <li class="tecla-list-item">
        <a href="<?=$this->routeUrl("/timeslots/edit/{$item->id}")?>">
            <div><?=tecla\data\WEEKDAYS[$item->weekday]?></div>
            <div class="second-line"><?=$item->startTime?> - <?=$item->endTime?>, <?=$item->court?></div>
        </a>
    </li>
	<?php endforeach?>
</ul>

<div>
    <a class="button primary" href="<?=$this->routeUrl('/timeslots/add')?>">Add timeslot</a>
    <a class="button secondary" href="<?=$this->routeUrl('/timeslots/generate-games')?>">Generate games</a>
</div>
