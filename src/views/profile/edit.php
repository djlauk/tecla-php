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
?>

<h1>Edit profile</h1>

<form method="POST" action="<?=$this->routeUrl('/profile/save')?>">
    <?=formInput('email', 'Email', array('value' => $user->email, 'placeholder' => "my.email@example.org"))?>
    <div>
        <button class="button primary" type="submit">Save</button>
    </div>
</form>
