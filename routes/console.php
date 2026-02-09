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
    ->everyMinute()          // elek minuut controleren of backup nodig is
    //->dalyAt('05:00')        // dagelijks om 05:00
    ->onOneServer()             // voorkomt dubbel uitvoeren in multi-server setup
    ->withoutOverlapping()      // start geen nieuwe job als vorige nog draait
    ->evenInMaintenanceMode();  // ook tijdens maintenance mode uitvoeren
//to run en debug chronical on laragon
//  .\Cronical.exe --console --debug    
