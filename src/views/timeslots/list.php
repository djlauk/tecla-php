<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------

?>
<h1>Spielzeiten</h1>

<p>Spielzeiten sind die Vorlage, nach der neue Spiele generiert werden.</p>

<ul class="tecla-list">
  <?php foreach ($items as $item): ?>
    <li class="tecla-list-item">
        <a href="<?=$this->routeUrl("/admin/timeslots/edit/{$item->id}")?>">
            <div><?=tecla\data\WEEKDAYS[$item->weekday]?></div>
            <div class="second-line"><?=$item->startTime?> - <?=$item->endTime?>, <?=$item->court?></div>
        </a>
    </li>
	<?php endforeach?>
</ul>

<a class="button primary" href="<?=$this->routeUrl('/admin/timeslots/add')?>">Hinzufügen</a>
