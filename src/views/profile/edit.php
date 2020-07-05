<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------

use function \tecla\util\widgetInput;
?>

<h1>Edit profile</h1>

<form method="POST" action="<?=$this->routeUrl('/profile/save')?>">
    <div><?=widgetInput('Email', 'email', array('value' => $user->email, 'placeholder' => "my.email@example.org"))?></div>
    <div>
        <button class="button primary" type="submit">Save</button>
    </div>
</form>
