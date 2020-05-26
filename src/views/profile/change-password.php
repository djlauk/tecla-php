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

<?php if ($pwRules['enabled']): ?>
<h3>Password requirements:</h3>
<ul>
    <?php if ($pwRules['minlength'] > 0): ?><li>Minimum length: <?=$pwRules['minlength']?></li><?php endif?>
    <?php if ($pwRules['needsUppercase']): ?><li>Must contain upper case letter.</li><?php endif?>
    <?php if ($pwRules['needsLowercase']): ?><li>Must contain lower case letter.</li><?php endif?>
    <?php if ($pwRules['needsNumber']): ?><li>Must contain number.</li><?php endif?>
    <?php if ($pwRules['needsSpecial']): ?><li>Must contain special character.</li><?php endif?>
    <?php if ($pwRules['needsNumClasses'] > 0): ?><li>Must contain at least <?=$pwRules['needsNumClasses']?> different of these character classes: Upper case, lower case, numbers, special characters.</li><?php endif?>
</ul>
<?php endif?>

<form method="POST" action="<?=$this->routeUrl('/profile/change-password')?>">
    <?=formInput('oldpassword', 'Current password', array('type' => 'password', 'required' => ''))?>
    <?=formInput('newpassword', 'New password', array('type' => 'password', 'required' => ''))?>
    <?=formInput('newpassword2', 'Repeat new password', array('type' => 'password', 'required' => ''))?>
    <div>
        <button class="button primary" type="submit">Change password</button>
    </div>
</form>
