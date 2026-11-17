<?php

namespace App\Mail;

use App\Models\ContactInquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly ContactInquiry $inquiry,
        public readonly string $replyText
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Re: Your inquiry to The Everbloom'
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: '<p>Dear ' . e($this->inquiry->name) . ',</p>'
                . '<p>' . nl2br(e($this->replyText)) . '</p>'
                . '<hr><p><small>Original message: ' . e($this->inquiry->message) . '</small></p>'
        );
    }
}

