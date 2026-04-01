<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Log;

use Carbon\Carbon;

// Voorbeeld standaard command (zit er meestal al in)
Artisan::command('inspire', function () {
    $this->comment(\Illuminate\Foundation\Inspiring::quote());
})->purpose('Display an inspiring quote');


// =======================================
// BACKUP / CLEANUP CONDITIONAL SCHEDULER
// =======================================
// Plan je custom artisan command "backup:conditional"
Schedule::command('backup:run')
    //->everyMinute()          // elek minuut controleren of backup nodig is
    ->dailyAt('02:00')        // dagelijks om 05:00
    ->onOneServer()             // voorkomt dubbel uitvoeren in multi-server setup
    ->withoutOverlapping()      // start geen nieuwe job als vorige nog draait
    ->evenInMaintenanceMode();  // ook tijdens maintenance mode uitvoeren

Schedule::command('backup:clean')->dailyAt('03:00');

// =============================================
// Scrapen Funds en bijwerken belegginsrekenigen
// =============================================
Schedule::command('funds:fetch')
    ->everyFifteenMinutes()
    //->everyMinute()
    ->between('06:00', '23:00')
    ->withoutOverlapping();

Schedule::command('app:update-fund-fin-account')
    ->everyTwoMinutes()
    ->between('16:45', '23:59')
    ->when(function () {
        return now()->endOfMonth()->isToday();
    })
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/scheduler-fund.log'));

// ===============================================
// Rekeningstanden overzetten naar overzichtstabel
// ===============================================    
Schedule::command('fin:rekening-standen-opslaan')->dailyAt('06:00');

// ========================================
// Utilly Readings uitlezen en wegschrijven
// ========================================    
Schedule::command('p1:fetch')
    //->everyMinute()
    ->everyThirtyMinutes()
    ->between('08:05', '23:55')
    ->timezone('Europe/Brussels')
    ->withoutOverlapping();

//to run en debug chronical on laragon
//  .\Cronical.exe --console --debug    
