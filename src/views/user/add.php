<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------

use \tecla\util\widgetInput;
use \tecla\util\widgetSelect;
?>

<h1>Add user</h1>

<form method="POST" action="<?=$this->routeUrl('/users/create')?>">
    <?=widgetInput('Display name', 'displayName')?>
    <?=widgetInput('Email', 'email', array('placeholder' => "my.email@example.org"))?>
    <?=widgetInput('Initial password', 'password', array('type' => 'password', 'required' => true))?>
    <?=widgetSelect('Role', 'role', array('guest' => 'guest', 'member' => 'member', 'admin' => 'admin'), array('value' => $role))?>
    <div>
        <button class="button primary" type="submit">Create</button>
    </div>
</form>
