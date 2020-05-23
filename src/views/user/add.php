<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------

function formInput($name, $label, $attrs = null)
{
    $safeName = htmlentities($name);
    $safeLabel = htmlentities($label);
    $safeAttrs = '';
    if (!is_null($attrs)) {
        foreach ($attrs as $k => $v) {
            $safeAttrs .= " $k=\"" . htmlentities($v) . '"';
        }
    }

    return <<<HERE
<div>
    <label for="$safeName">$safeLabel</label>
    <input name="$safeName" $safeAttrs>
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

<h1>Add user</h1>

<form method="POST" action="<?=$this->routeUrl('/users/create')?>">
    <?=formInput('displayName', 'Display name')?>
    <?=formInput('email', 'Email', array('placeholder' => "my.email@example.org"))?>
    <?=formInput('password', 'Initial password', array('type' => 'password', 'required' => ''))?>
    <?=formSelect('role', 'Role', $role, array('guest' => 'guest', 'member' => 'member', 'admin' => 'admin'))?>
    <div>
        <button class="button primary" type="submit">Create</button>
    </div>
</form>
