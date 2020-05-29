<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------
?>
<h1>Generate games</h1>

<?php if ($problem): ?>
<div class="error message">
<p><strong>Generating games failed. Sorry.</strong></p>
</div>
<?php endif?>

<form method="POST" action="<?=$this->routeUrl('/templates/generate-games')?>">
    <div>
        <label for="firstDay" required>First day</label>
        <input id="firstDay" name="firstDay" placeholder="yyyy-mm-dd" regex="\d\d\d\d-\d\d-\d\d" required value="<?=$firstDay?>">
    </div>
    <div>
        <label for="lastDay" required>Last day</label>
        <input name="lastDay" placeholder="yyyy-mm-dd" regex="\d\d\d\d-\d\d-\d\d" required value="<?=$lastDay?>">
    </div>
    <div class="form-buttons">
        <button class="button primary" type="submit">Generate</button>
    </div>
</form>
