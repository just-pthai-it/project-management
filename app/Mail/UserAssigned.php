<?php

namespace App\Mail;

use App\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserAssigned extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    private Notification $notification;

    /**
     * Create a new message instance.
     *
     * @param Notification $notification
     */
    public function __construct (Notification $notification)
    {
        $this->notification = $notification;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope ()
    {
        return new Envelope(
            subject: 'User Assigned',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return Content
     */
    public function content () : Content
    {
        return new Content(
            view: 'mail-forms.user-assigned',
            with: ['content' => $this->notification->content],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments ()
    {
        return [];
    }
}
