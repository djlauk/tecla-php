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

<ul class="tecla-list">
  <?php foreach ($items as $item): ?>
    <li class="tecla-list-item">
        <a href="<?=$this->routeUrl("/templates/edit/{$item->id}")?>">
            <div><?=tecla\data\WEEKDAYS[$item->weekday]?></div>
            <div class="second-line"><?=$item->startTime?> - <?=$item->endTime?>, <?=$item->court?></div>
        </a>
    </li>
	<?php endforeach?>
</ul>

<div>
    <a class="button primary" href="<?=$this->routeUrl('/templates/add')?>">Add template</a>
    <a class="button secondary" href="<?=$this->routeUrl('/templates/generate-games')?>">Generate games</a>
</div>
