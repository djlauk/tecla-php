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
        <label for="weekday">Weekday</label>
        <select id="weekday" name="weekday">
            <?php foreach (tecla\data\WEEKDAYS as $num => $str): ?>
                <option value="<?=$num?>"<?=$item->weekday == $num ? ' selected' : ''?>><?=$str?></option>
            <?php endforeach?>
        </select>
    </div>
    <div>
        <label for="startTime">Start time</label>
        <input name="startTime" placeholder="hh:mm" regex="\d\d:\d\d" required value="<?=$item->startTime?>">
    </div>
    <div>
        <label for="endTime">End time</label>
        <input name="endTime" placeholder="hh:mm" regex="\d\d:\d\d" required value="<?=$item->endTime?>">
    </div>
    <div>
        <label for="court">Court</label>
        <input name="court" placeholder="Wimbledon" required value="<?=htmlentities($item->court)?>">
    </div>
    <div>
        <label for="notes" required>Notes</label>
        <textarea name="notes" placeholder="Note to future self ..."><?=htmlentities($item->notes)?></textarea>
    </div>
    <div>
        <button class="button primary" type="submit">Save template</button>
        <a class="button secondary" href="<?=$this->routeUrl("/templates/delete/{$item->id}")?>">Delete template</a>
    </div>
</form>
