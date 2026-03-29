<?php

namespace App\Listeners;

use Spatie\Backup\Events\BackupWasSuccessful;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Mail\BackupCompletedMail;

class SendBackupSuccessMail
{
    public function handle(BackupWasSuccessful $event): void
    {
        $disk = Storage::disk($event->diskName);
        
        // Haal alle bestanden op in de backup folder
        $files = $disk->files($event->backupName);
        
        // Meest recente = laatste in de lijst
        $latestBackup = collect($files)->sortDesc()->first();
        
        $absolutePath = $disk->path($latestBackup);

        $to = config('backup.notifications.mail.to');
        Mail::to($to)->send(new BackupCompletedMail($absolutePath));
    }
}