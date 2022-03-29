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

<h1>Reset password for <?=htmlentities($user->displayName)?></h1>

<form method="POST" action="<?=$this->routeUrl('/users/reset-password')?>">
    <input name="id" type="hidden" value="<?=$user->id?>">
    <input name="metaVersion" type="hidden" value="<?=$user->metaVersion?>">
    <?=widgetInput('New password', 'password', array('required' => true))?>
    <div class="form-buttons">
        <button id="btnRandom" class="button">Generate random password</button>
        <button class="button primary" type="submit">Set password</button>
    </div>
</form>

<script type="module">
    const button = document.getElementById('btnRandom');
    const input = document.getElementById('password');
    const alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789~!@#$%^&*()-_+=/?<>,.[]{}';

    const randomPassword = () => {
        const PASSWORD_LENGTH = 12;
        let result = '';
        for (let i = 0; i < PASSWORD_LENGTH; i++) {
            result += alphabet[Math.floor(Math.random() * alphabet.length)];
        }
        return result;
    };

    button.addEventListener('click', event => {
        event.preventDefault();
        input.value = randomPassword();
    });
    button.click();
</script>
