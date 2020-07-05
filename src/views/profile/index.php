<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------

use function \tecla\util\viewFormatLastLogin;
use function \tecla\util\viewFormatTimestamp;
?>

<h1>Account profile</h1>

<table>
    <tr><td>Display name:</td><td><?=$user->displayName?></td></tr>
    <tr><td>Email:</td><td><?=$user->email?></td></tr>
    <tr><td>Last log in:</td><td><?=viewFormatLastLogin($user)?></td></tr>
    <tr><td>Failed login attempts:</td><td><?=$user->failedLogins?></td></tr>
    <tr><td>Account created on:</td><td><?=viewFormatTimestamp($user->metaCreatedOn)?></td></tr>
</table>

<div>
    <a class="button primary" href="<?=$this->routeUrl('/profile/edit')?>">Edit profile</a>
    <a class="button secondary" href="<?=$this->routeUrl('/profile/change-password')?>">Change password</a>
</div>
