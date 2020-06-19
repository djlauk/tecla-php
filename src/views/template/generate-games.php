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
    <div>
        <label>Templates to use</label>
        <table>
            <tr>
                <th></th>
                <th>Weekday</th>
                <th>Time</th>
                <th>Court</th>
            </tr>
            <?php foreach ($templates as $item): ?>
            <tr>
                <td><input type="checkbox" name="templates[]" value="<?=$item->id?>" checked></td>
                <td><?=tecla\data\WEEKDAYS[$item->weekday]?></td>
                <td><?=$item->startTime?> - <?=$item->endTime?></td>
                <td><?=$item->court?></td>
            </tr>
            <?php endforeach?>
        </table>
    </div>
    <div class="form-buttons">
        <button class="button" id="markAll">Unselect all</button>
        <button class="button primary" type="submit">Generate</button>
    </div>
</form>

<script type="module">
    const button = document.getElementById('markAll');
    const checkboxes = Array.from(document.querySelectorAll('form input[type=checkbox]'));
    const areAllChecked = () => checkboxes.every(el => el.checked);
    const updateButtonText = (allChecked) => {
        button.innerText = allChecked ? 'Unselect all' : 'Select all';
    }

    button.addEventListener('click', (event) => {
        event.preventDefault();
        const checkboxes = Array.from(document.querySelectorAll('form input[type=checkbox]'));
        const allChecked = checkboxes.every(el => el.checked);
        checkboxes.forEach(el => { el.checked = !allChecked; });
        updateButtonText(!allChecked);
    });

    checkboxes.forEach(el => { el.addEventListener('change', (event) => {
        updateButtonText(event.target.checked && areAllChecked())
    })});
</script>
