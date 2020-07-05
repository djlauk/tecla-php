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
<h1>Users</h1>

<table class="fullwidth">
    <thead>
    <tr>
        <th scope="col">ID</th>
        <th scope="col">Display name</th>
        <th scope="col">Email</th>
        <th scope="col">Role</th>
        <th scope="col">Disabled on</th>
        <th scope="col">Locked until</th>
        <th scope="col"></th>
    </tr>
    </thead>
    <tbody>
  <?php foreach ($users as $user): ?>
    <?php
$link = $this->routeUrl("/users/view/{$user->id}");
$linkHistory = $this->routeUrl("/history/user/{$user->id}");
?>
    <tr>
        <td><a href="<?=$link?>"><?=htmlentities($user->id)?></a></td>
        <td><a href="<?=$link?>"><?=htmlentities($user->displayName)?></a></td>
        <td><a href="<?=$link?>"><?=htmlentities($user->email)?></a></td>
        <td><?=htmlentities($user->role)?></td>
        <td><?=is_null($user->disabledOn) ? '-' : viewFormatTimestamp($user->disabledOn)?></td>
        <td><?=is_null($user->lockedUntil) ? '-' : viewFormatTimestamp($user->lockedUntil)?></td>
        <td><a href="<?=$linkHistory?>">view history</a></td>
    </tr>
    <?php endforeach?>
  </tbody>
</table>

<div>
    <a class="button primary" href="<?=$this->routeUrl('/users/add')?>">Add user</a>
</div>
