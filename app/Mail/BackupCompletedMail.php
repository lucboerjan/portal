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

    public function __construct(string $backupPath)
    {
        $this->backupPath = $backupPath;
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
            view: 'emails.backup_completed',
        );
    }

    public function attachments(): array
    {
        return [
            \Illuminate\Mail\Mailables\Attachment::fromPath($this->backupPath)
                ->as(basename($this->backupPath))
                ->withMime('application/zip'),
        ];
    }
}
