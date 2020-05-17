<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------
?>
<h1>Login</h1>
<?php if ($problem): ?>
<div class="error message">
<p><strong>Login failed. Sorry.</strong></p>

<p>Possible reasons:</p>
<ul>
    <li>You mistyped your email address.</li>
    <li>You mistyped your password.</li>
    <li>Your account has been locked, because you tried too many times.</li>
    <li>Your account has been disabled by an administrator.</li>
</ul>
<p>We won&apos;t tell you, which of the above happened (although we know), because it would disclose this information to any potential attacker, too. Sorry.</p>
<p>If you think you may have been locked out, try again in a few minutes. If the problem persists contact an administrator.</p>
</div>
<?php endif?>

<form method="POST" action="<?=$this->routeUrl('/login')?>">
    <div>
        <label for="email" required>Email</label>
        <input id="email" name="email" placeholder="my.name@example.org" regex="\S+@\S+\.\S+" required value="<?=$_POST['email'] ?? ''?>">
    </div>
    <div>
        <label for="password" required>Password</label>
        <input name="password" type="password" required>
    </div>
    <div class="form-buttons">
        <button class="button primary" type="submit">Login</button>
    </div>
</form>
