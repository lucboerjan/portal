<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Voorbeeld standaard command (zit er meestal al in)
Artisan::command('inspire', function () {
    $this->comment(\Illuminate\Foundation\Inspiring::quote());
})->purpose('Display an inspiring quote');


// =======================================
// BACKUP / CLEANUP CONDITIONAL SCHEDULER
// =======================================
// Plan je custom artisan command "backup:conditional"
Schedule::command('backup:run')
    //everyMinute()          // elek minuut controleren of backup nodig is
    ->dailyAt('02:00')        // dagelijks om 05:00
    ->onOneServer()             // voorkomt dubbel uitvoeren in multi-server setup
    ->withoutOverlapping()      // start geen nieuwe job als vorige nog draait
    ->evenInMaintenanceMode();  // ook tijdens maintenance mode uitvoeren

Schedule::command('backup:clean')->dailyAt('03:00');    

// =============================================
// Scrapen Funds en bijwerken belegginsrekenigen
// =============================================
Schedule::command('funds:fetch')
    //->everyFifteenMinutes()
    ->everyFifteenMinutes()
    ->between('06:00', '23:00');

Schedule::command('app:update-fund-fin-account')
    ->lastDayOfMonth()
    ->everyFifteenMinutes()
    ->between('20:00', '23:59');    

// ===============================================
// Rekeningstanden overzetten naar overzichtstabel
// ===============================================    
Schedule::command('fin:rekening-standen-opslaan')->monthlyOn(1, '04:00');

// ========================================
// Utilly Readings uitlezen en wegschrijven
// ========================================    
Schedule::command('p1:fetch')
    ->everyFiveMinutes()
    ->between('08:05', '23:00')
    ->timezone('Europe/Brussels')
    ->withoutOverlapping();



//to run en debug chronical on laragon
//  .\Cronical.exe --console --debug    
