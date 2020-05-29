<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------
?>
<h1>Add template</h1>

<form method="POST" action="<?=$this->routeUrl('/templates/create')?>">
    <div>
        <label for="weekday" required>Weekday</label>
        <select id="weekday" name="weekday">
            <?php foreach (tecla\data\WEEKDAYS as $num => $str): ?>
                <option value="<?=$num?>"><?=$str?></option>
            <?php endforeach?>
        </select>
    </div>
    <div>
        <label for="startTime" required>Start time</label>
        <input id="startTime" name="startTime" placeholder="hh:mm" regex="\d\d:\d\d" required>
    </div>
    <div>
        <label for="endTime" required>End time</label>
        <input name="endTime" placeholder="hh:mm" regex="\d\d:\d\d" required>
    </div>
    <div>
        <label for="court" required>Court</label>
        <input name="court" placeholder="Wimbledon" required>
    </div>
    <div>
        <label for="notes">Notes</label>
        <textarea name="notes" placeholder="Note to future self ..."></textarea>
    </div>
    <div class="form-buttons">
        <button class="button primary" type="submit">Add template</button>
    </div>
</form>
