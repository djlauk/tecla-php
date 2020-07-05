<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------

use function \tecla\util\widgetSelect;
?>

<h1>Enable or disable user <?=$user->displayName?></h1>

<form method="POST" action="<?=$this->routeUrl('/users/enable')?>">
    <input name="id" type="hidden" value="<?=$user->id?>">
    <input name="metaVersion" type="hidden" value="<?=$user->metaVersion?>">
    <?=widgetSelect('Enabled', 'enabled', array('enabled' => 'enabled', 'disabled' => 'disabled'), array('value' => $isEnabled ? 'enabled' : 'disabled'))?>
    <div>
        <button class="button primary" type="submit">Save</button>
    </div>
</form>
