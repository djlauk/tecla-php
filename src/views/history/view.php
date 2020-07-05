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

<h1>History of <?=strtoupper($type)?>:<?=$id?></h1>

<?php
$maxIdx = count($entries) - 1;
foreach ($entries as $idx => $e): ?>
<?php
$data = json_decode($e->data, true);
$prevData = $idx < $maxIdx ? json_decode($entries[$idx + 1]->data, true) : array();
?>
<h2>Version <?=$e->version?> (<?=viewFormatTimestamp($e->metaCreatedOn)?>)</h2>
<table>
<?php foreach ($data as $k => $v): ?>
    <tr class="<?=$prevData[$k] == $data[$k] ? 'unchanged' : 'changed'?>"><td><?=htmlentities($k)?></td><td><?=htmlentities($v)?></td></th>
<?php endforeach?>
</table>
<?php endforeach?>
