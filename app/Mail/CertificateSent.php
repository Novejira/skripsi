<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class CertificateSent extends Mailable
{
    use Queueable, SerializesModels;

    public $participant;
    public $certificatePath;

    public function __construct($participant, $certificatePath)
    {
        $this->participant = $participant;
        $this->certificatePath = $certificatePath;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Sertifikat Anda dari CEDEC',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.certificate', // Ganti dengan view blade email kamu
            with: [
                'participant' => $this->participant,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->certificatePath)
                ->as('Sertifikat_TOEFL.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
