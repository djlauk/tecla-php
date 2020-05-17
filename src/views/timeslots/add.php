<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------
?>
<h1>Add time slot</h1>

<form method="POST" action="<?=$this->routeUrl('/timeslots/create')?>">
    <div>
        <label for="weekday" required>Wochentag</label>
        <select id="weekday" name="weekday" required>
            <option value="1">Montag</option>
            <option value="2">Dienstag</option>
            <option value="3">Mittwoch</option>
            <option value="4">Donnerstag</option>
            <option value="5">Freitag</option>
            <option value="6">Samstag</option>
            <option value="0">Sonntag</option>
        </select>
    </div>
    <div>
        <label for="startTime" required>Beginn</label>
        <input id="startTime" name="startTime" placeholder="hh:mm" regex="\d\d:\d\d" required>
    </div>
    <div>
        <label for="endTime" required>Ende</label>
        <input name="endTime" placeholder="hh:mm" required>
    </div>
    <div>
        <label for="court" required>Platz</label>
        <input name="court" placeholder="Wimbledon" required>
    </div>
    <div class="form-buttons">
        <button class="button primary" type="submit">Speichern</button>
    </div>
</form>
