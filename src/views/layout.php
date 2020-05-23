<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------
?>
<!DOCTYPE HTML>
<html lang="en-US">
<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1, viewport-fit=cover">
        <title><?=$title ?? 'Tennis Club App'?></title>
        <link rel="stylesheet" href="/styles.css"></link>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,700|Roboto+Condensed:300,400,700|Roboto+Slab:300,400,700">
		<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
</head>
<body>
    <header id="topnav">
        <a href="<?=$this->routeUrl('/')?>">Home</a>
        <?php if ($this['auth']->hasRole('admin')): ?>
        <a href="<?=$this->routeUrl('/timeslots')?>">Time slots</a>
        <a href="<?=$this->routeUrl('/users')?>">Users</a>
        <?php endif?>
        <?php if ($this['auth']->isLoggedIn()): ?>
        <a href="<?=$this->routeUrl('/profile')?>">My account</a>
        <a href="<?=$this->routeUrl('/logout')?>">Log out</a>
        <?php else: ?>
        <a href="<?=$this->routeUrl('/login')?>">Log in</a>
        <?php endif?>
    </header>
    <main>
        <?php echo $content_for_layout; ?>
    </main>
</body>
</html>
