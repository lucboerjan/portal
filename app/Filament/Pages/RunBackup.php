<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Artisan;
use Filament\Support\Icons\Heroicon;
use BackedEnum;

class RunBackup extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::ShieldCheck;
    protected string $view = 'filament.pages.run-backup';
    protected static ?string $navigationLabel = 'Backup uitvoeren';
    protected static ?string $title = 'Backup uitvoeren';

    public function runBackup()
    {
        Artisan::call('backup:run');

        Notification::make()
            ->title('Backup uitgevoerd')
            ->success()
            ->send();
    }
}
