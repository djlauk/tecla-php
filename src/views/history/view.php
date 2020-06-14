<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------
?>

<h1>History of <?=strtoupper($type)?>:<?=$id?></h1>

<?php foreach ($entries as $e): ?>
<h2>Version <?=$e->version?> (<?=$e->metaCreatedOn?>)</h2>
<pre><?=htmlentities($e->data)?></pre>
<?php endforeach?>
