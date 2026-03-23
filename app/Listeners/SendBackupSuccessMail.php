<?php

namespace App\Listeners;

use Spatie\Backup\Events\BackupWasSuccessful;
use Illuminate\Support\Facades\Mail;
use App\Mail\BackupCompletedMail;
use Illuminate\Support\Facades\Log;

class SendBackupSuccessMail
{
    public function handle(BackupWasSuccessful $event): void
    {
        // Haal de meest recente backup op
        $backup = $event->backupDestination->newestBackup();

        // Maak van de relatieve path een absolute path
        $absolutePath = $event->backupDestination
            ->disk()
            ->path($backup->path());

        $to = config('backup.notifications.mail.to');
        Mail::to($to)->send(new BackupCompletedMail($absolutePath));
    }
}
