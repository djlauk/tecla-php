<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------

?>

<h1><?=htmlentities($user->displayName)?></h1>

<table>
    <tr><td>Email:</td><td><?=$user->email?></td></tr>
    <tr><td>Email verified on:</td><td><?=is_null($user->verifiedOn) ? 'not verified' : $user->verfiedOn->format('Y-m-d H:i:s')?></td></tr>
    <tr><td>Role:</td><td><?=$user->role?></td></tr>
    <tr><td>Last log in:</td><td><?=\tecla\util\viewFormatLastLogin($user)?></td></tr>
    <tr><td>Failed login attempts:</td><td><?=$user->failedLogins?></td></tr>
    <tr><td>Locked out:</td><td><?=is_null($user->lockedUntil) ? 'not locked' : $user->lockedUntil->format('Y-m-d H:i:s')?></td></tr>
    <tr><td>Disabled:</td><td><?=is_null($user->disabledOn) ? 'enabled' : $user->disabledOn->format('Y-m-d H:i:s')?></td></tr>
    <tr><td>Created on:</td><td><?=$user->metaCreatedOn->format('Y-m-d H:i:s')?></td></tr>
</table>

<div>
    <a class="button primary" href="<?=$this->routeUrl('/users/edit/' . $id)?>">Edit user</a>
    <a class="button secondary" href="<?=$this->routeUrl('/users/enable/' . $id)?>">Enable / disable user</a>
    <a class="button" href="<?=$this->routeUrl('/users/reset-password/' . $id)?>">Reset password</a>
</div>
