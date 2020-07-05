<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------

use function \tecla\util\widgetInput;
use function \tecla\util\widgetSelect;
use function \tecla\util\widgetTextArea;
use function \tecla\util\widgetTimeInput;

?>
<h1>Edit template <?=$item->id?></h1>

<form method="POST" action="<?=$this->routeUrl('/templates/save')?>">
    <input name="id" type="hidden" value="<?=$item->id?>">
    <input name="metaVersion" type="hidden" value="<?=$item->metaVersion?>">
    <div>
        <?=widgetSelect('Weekday', 'weekday', \tecla\data\WEEKDAYS, array('required' => true, 'value' => $item->weekday))?>
    </div>
    <div>
        <?=widgetTimeInput('Start time', 'startTime', array('required' => true, 'value' => $item->startTime))?>
    </div>
    <div>
        <?=widgetTimeInput('End time', 'endTime', array('required' => true, 'value' => $item->endTime))?>
    </div>
    <div>
        <?=widgetInput('Court', 'court', array('required' => true, 'value' => $item->court, 'placeholder' => 'Wimbledon'))?>
    </div>
    <div>
        <?=widgetTextArea('Notes', 'notes', array('value' => $item->notes, 'placeholder' => 'Note to future self...'))?>
    </div>
    <div class="form-buttons">
        <button class="button primary" type="submit">Save template</button>
        <a class="button secondary" href="<?=$this->routeUrl("/templates/delete/{$item->id}")?>">Delete template</a>
    </div>
</form>
