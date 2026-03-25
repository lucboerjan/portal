<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class BackupCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $backupPath;
    public string $filename;
    public string $size;

    public function __construct(string $backupPath)
    {
        $this->backupPath = $backupPath;
        $this->filename   = basename($backupPath);
        $this->size       = file_exists($backupPath)
            ? round(filesize($backupPath) / 1048576, 2) . ' MB'
            : 'onbekend';
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '✅ Backup succesvol voltooid',
        );
    }

public function content(): Content
{
    return new Content(
        markdown: 'emails.backup_completed',
    );
}

    public function attachments(): array
    {
        return []; // Zip-bijlage verwijderd — SMTP blokkeert .zip bestanden
    }
}