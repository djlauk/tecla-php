<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------

function formField($name, $label, $value)
{
    $safeName = htmlentities($name);
    $safeLabel = htmlentities($label);
    $safeValue = htmlentities($value);

    return <<<HERE
<div>
    <label for="$safeName">$safeLabel</label>
    <input name="$safeName" value="$safeValue">
</div>
HERE;
}

function formSelect($name, $label, $value, $options)
{
    $safeName = htmlentities($name);
    $safeLabel = htmlentities($label);
    $safeValue = htmlentities($value);
    $optStr = '';
    foreach ($options as $k => $v) {
        $optStr .= "<option value=\"" . htmlentities($k) . "\"" . ($v === $value ? ' selected' : '') . ">" . htmlentities($v) . "</option>";
    }
    return <<<HERE
<div>
    <label for="$safeName">$safeLabel</label>
    <select name="$safeName" value="$safeValue">$optStr</select>
</div>
HERE;
}
?>

<h1>Edit <?=$user->displayName?></h1>

<form method="POST" action="<?=$this->routeUrl('/users/save')?>">
    <input name="id" type="hidden" value="<?=$user->id?>">
    <input name="metaVersion" type="hidden" value="<?=$user->metaVersion?>">
    <?=formField('displayName', 'Display name', $user->displayName)?>
    <?=formField('email', 'Email', $user->email)?>
    <?=formSelect('role', 'Role', $user->role, array('guest' => 'guest', 'member' => 'member', 'admin' => 'admin'))?>
    <div>
        <button class="button primary" type="submit">Save</button>
    </div>
</form>
