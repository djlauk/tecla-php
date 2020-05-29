<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------
?>
<h1>Edit template <?=$item->id?></h1>

<form method="POST" action="<?=$this->routeUrl('/templates/save')?>">
    <input name="id" type="hidden" value="<?=$item->id?>">
    <input name="metaVersion" type="hidden" value="<?=$item->metaVersion?>">
    <div>
        <label for="weekday">Wochentag</label>
        <select id="weekday" name="weekday">
            <?php foreach (tecla\data\WEEKDAYS as $num => $str): ?>
                <option value="<?=$num?>"<?=$item->weekday == $num ? ' selected' : ''?>><?=$str?></option>
            <?php endforeach?>
        </select>
    </div>
    <div>
        <label for="startTime">Beginn</label>
        <input name="startTime" value="<?=$item->startTime?>">
    </div>
    <div>
        <label for="endTime">Ende</label>
        <input name="endTime" value="<?=$item->endTime?>">
    </div>
    <div>
        <label for="court">Platz</label>
        <input name="court" value="<?=$item->court?>">
    </div>
    <div>
        <button class="button primary" type="submit">Speichern</button>
        <a class="button secondary" href="<?=$this->routeUrl("/templates/delete/{$item->id}")?>">Delete</a>
    </div>
</form>
