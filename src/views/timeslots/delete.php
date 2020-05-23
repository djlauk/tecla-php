<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------
?>
<h1>Delete time slot <?=$item->id?></h1>

<table>
    <tr><td>Start time:</td><td><?=$item->startTime?></td></tr>
    <tr><td>End time:</td><td><?=$item->endTime?></td></tr>
    <tr><td>Court:</td><td><?=$item->court?></td></tr>
    <tr><td>Notes:</td><td><?=str_replace("\n", "<br>", htmlentities($item->notes))?></td></tr>
</table>

<form method="POST" action="<?=$this->routeUrl('/timeslots/delete')?>">
    <input name="id" type="hidden" value="<?=$item->id?>">
    <input name="metaVersion" type="hidden" value="<?=$item->metaVersion?>">
    <div>
        <button class="button primary" type="submit">Delete</button>
    </div>
</form>
