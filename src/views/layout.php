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
        <a href="<?=$this->routeUrl('/admin/timeslots')?>">Spielzeiten</a>
    </header>
    <main>
        <?php echo $content_for_layout; ?>
    </main>
</body>
</html>
