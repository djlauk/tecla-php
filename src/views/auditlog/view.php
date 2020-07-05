<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------

use function \tecla\util\viewFormatTimestamp;
?>

<h1>Audit log entry <?=$entry->id?></h1>

<table>
    <tr><td>Id:</td><td><?=$entry->id?></td></tr>
    <tr><td>Created:</td><td><?=viewFormatTimestamp($entry->metaCreatedOn)?></td></tr>
    <tr><td>User:</td><td><?=htmlentities($userLookup[$entry->user_id]->displayName)?></td></tr>
    <tr><td>Action:</td><td><?=htmlentities($entry->action)?></td></tr>
    <tr><td>Object:</td><td><?=htmlentities($entry->object)?></td></tr>
    <tr><td>Message:</td><td><?=str_replace("\n", "<br>", htmlentities($entry->message))?></td></tr>
</table>
