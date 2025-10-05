<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('sheet:sync')->timezone('Asia/Jakarta')->everyThirtySeconds()->name('Sync Spreadsheet');
Schedule::command('message:send')->timezone('Asia/Jakarta')->dailyAt('09.00')->name('Send message');
