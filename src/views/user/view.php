<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------

?>

<h1><?=$user->displayName?></h1>

<table>
    <tr><td>Email:</td><td><?=$user->email?></td></tr>
    <tr><td>Email verified on:</td><td><?=$user->verifiedOn ?? 'not verified'?></td></tr>
    <tr><td>Role:</td><td><?=$user->role?></td></tr>
    <tr><td>Last log in:</td><td><?=$user->lastLoginOn?> (from <?=$user->lastLoginFrom ?? 'unknown address'?>)</td></tr>
    <tr><td>Failed login attempts:</td><td><?=$user->failedLogins?></td></tr>
    <tr><td>Locked out:</td><td><?=$user->lockedUntil ?? 'not locked'?></td></tr>
    <tr><td>Disabled:</td><td><?=$user->disabledOn ?? 'enabled'?></td></tr>
    <tr><td>Created on:</td><td><?=$user->metaCreatedOn?></td></tr>
</table>

<div>
    <a class="button primary" href="<?=$this->routeUrl('/users/edit/' . $id)?>">Edit user</a>
    <a class="button secondary" href="<?=$this->routeUrl('/users/enable/' . $id)?>">Enable / disable user</a>
</div>
