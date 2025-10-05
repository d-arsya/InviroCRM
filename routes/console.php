<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('sheet:sync')->timezone('Asia/Jakarta')->everyThirtySeconds()->name('Sync Spreadsheet');
