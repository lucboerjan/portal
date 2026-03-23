<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Voorbeeld standaard command (zit er meestal al in)
Artisan::command('inspire', function () {
    $this->comment(\Illuminate\Foundation\Inspiring::quote());
})->purpose('Display an inspiring quote');


// =============================
// BACKUP CONDITIONAL SCHEDULER
// =============================
// Plan je custom artisan command "backup:conditional"
Schedule::command('backup:run')
    //everyMinute()          // elek minuut controleren of backup nodig is
    ->dailyAt('05:00')        // dagelijks om 05:00
    ->onOneServer()             // voorkomt dubbel uitvoeren in multi-server setup
    ->withoutOverlapping()      // start geen nieuwe job als vorige nog draait
    ->evenInMaintenanceMode();  // ook tijdens maintenance mode uitvoeren
//to run en debug chronical on laragon
//  .\Cronical.exe --console --debug    
// routes/console.php (Laravel 11)
Schedule::command('funds:fetch')
    //->everyFifteenMinutes()
    ->everyFifteenMinutes()
    ->between('06:00', '23:00');

Schedule::command('fin:rekening-standen-opslaan')->monthlyOn(1, '04:00');
Schedule::command('app:update-fund-fin-account')
    ->lastDayOfMonth()
    ->everyFifteenMinutes()
    ->between('20:00', '23:59');

Schedule::command('p1:fetch')
    ->everyFiveMinutes()
    ->withoutOverlapping();
