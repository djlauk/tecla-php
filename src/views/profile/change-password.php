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

<h1>Change password</h1>
<?php if ($problem): ?>
<div class="error message">
<p><strong>Changing password failed. Sorry.</strong></p>

<p>Reason: <?=$problem?></p>
</div>
<?php endif?>

<form method="POST" action="<?=$this->routeUrl('/profile/change-password')?>">
    <?=formInput('password', 'Password', array('type' => 'password', 'required' => ''))?>
    <?=formInput('password2', 'Repeat password', array('type' => 'password', 'required' => ''))?>
    <div>
        <button class="button primary" type="submit">Change password</button>
    </div>
</form>
