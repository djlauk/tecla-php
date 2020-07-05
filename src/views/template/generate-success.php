<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------

use function \tecla\util\viewFormatDate;
?>
<h1>Games generated successfully</h1>

<ul>
    <li>First day: <?=viewFormatDate($firstDay)?></li>
    <li>Last day: <?=viewFormatDate($lastDay)?></li>
    <li>Number of games: <?=$count?></li>
</ul>
