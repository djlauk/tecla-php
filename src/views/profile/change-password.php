<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------

use function \tecla\util\widgetInput;
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
    <?=widgetInput('Current password', 'oldpassword', array('type' => 'password', 'required' => true))?>
    <?=widgetInput('New password', 'newpassword', array('type' => 'password', 'required' => true))?>
    <?=widgetInput('Repeat new password', 'newpassword2', array('type' => 'password', 'required' => true))?>
    <div class="form-buttons">
        <button class="button primary" type="submit">Change password</button>
    </div>
</form>
