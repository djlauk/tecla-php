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
<h1>Add template</h1>

<form method="POST" action="<?=$this->routeUrl('/templates/create')?>">
    <div>
        <?=widgetSelect('Weekday', 'weekday', \tecla\data\WEEKDAYS, array('required' => true))?>
    </div>
    <div>
        <?=widgetTimeInput('Start time', 'startTime', array('required' => true))?>
    </div>
    <div>
        <?=widgetTimeInput('End time', 'endTime', array('required' => true))?>
    </div>
    <div>
        <?=widgetInput('Court', 'court', array('required' => true))?>
    </div>
    <div>
        <?=widgetTextArea('Notes', 'notes', array('placeholder' => 'Note to future self...'))?>
    </div>
    <div class="form-buttons">
        <button class="button primary" type="submit">Add template</button>
    </div>
</form>
