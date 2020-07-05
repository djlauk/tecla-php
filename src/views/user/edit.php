<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------

use function \tecla\util\widgetInput;
use function \tecla\util\widgetSelect;
?>

<h1>Edit <?=$user->displayName?></h1>

<form method="POST" action="<?=$this->routeUrl('/users/save')?>">
    <input name="id" type="hidden" value="<?=$user->id?>">
    <input name="metaVersion" type="hidden" value="<?=$user->metaVersion?>">
    <?=widgetInput('Display name', 'displayName', array('value' => $user->displayName, 'required' => true))?>
    <?=widgetInput('Email', 'email', array('value' => $user->email, 'required' => true))?>
    <?=widgetSelect('Role', 'role', array('guest' => 'guest', 'member' => 'member', 'admin' => 'admin'), array('value' => $user->role, 'required' => true))?>
    <div class="form-buttons">
        <button class="button primary" type="submit">Save</button>
    </div>
</form>
