<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------
function getUser($userId, $userLookup)
{
    if (is_null($userId)) {return '';}
    return htmlentities($userLookup[$userId]->displayName);
}
?>
<h1>Audit log</h1>
<h3>Page <?=$page?> of <?=$numPages?></h3>

<div class="form-buttons">
    <?php if ($page > 1): ?>
    <a class="button primary" href="<?=$this->routeUrl('/auditlog/list/1')?>">First page</a>
    <a class="button primary" href="<?=$this->routeUrl('/auditlog/list/' . ($page - 1))?>">Previous</a>
    <?php else: ?>
    <span disabled class="button">First page</span>
    <span disabled class="button">Previous</span>
    <?php endif?>
    <?php if ($page < $numPages): ?>
    <a class="button primary" href="<?=$this->routeUrl('/auditlog/list/' . ($page + 1))?>">Next</a>
    <a class="button primary" href="<?=$this->routeUrl('/auditlog/list/' . $numPages)?>">Last page</a>
    <?php else: ?>
    <span disabled class="button">Next</span>
    <span disabled class="button">Last page</span>
    <?php endif?>
</div>

<table class="fullwidth">
    <tr>
        <th>ID</th>
        <th>Created</th>
        <th>User</th>
        <th>Action</th>
        <th>Object</th>
    </tr>
<?php foreach ($entries as $e):
    if ($e->object) {
        $objParts = explode(':', $e->object, 2);
        $objType = strtolower($objParts[0]);
        $objId = $objParts[1];
        $historyLink = $this->routeUrl("/history/$objType/$objId");
    } else {
        $historyLink = '';
    }
    $viewLink = $this->routeUrl("/auditlog/view/{$e->id}");
    $userLink = $this->routeUrl("/users/view/{$e->user_id}");
    ?>
	<tr>
	    <td><a href="<?=$viewLink?>"><?=$e->id?></a></td>
	    <td><a href="<?=$viewLink?>"><?=$e->metaCreatedOn->format('Y-m-d H:i:s')?></a></td>
	    <td><a href="<?=$userLink?>"><?=getUser($e->user_id, $userLookup)?></a></td>
	    <td><?=htmlentities($e->action)?></td>
	    <td><?php if ($historyLink): ?><a href="<?=$historyLink?>"><?=htmlentities($e->object)?></a><?php endif?></td>
    </tr>
<?php endforeach?>
</table>

