<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------

?>
<h1>Users</h1>

<ul class="tecla-list">
  <?php foreach ($users as $user): ?>
    <li class="tecla-list-item">
        <a href="<?=$this->routeUrl("/users/view/{$user->id}")?>">
            <div><?=$user->displayName?></div>
            <div class="second-line"><?=$user->email?></div>
        </a>
    </li>
	<?php endforeach?>
</ul>

<div>
    <a class="button primary" href="<?=$this->routeUrl('/users/add')?>">Add user</a>
</div>
