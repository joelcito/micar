<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EnviaCorreo extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $number;
    public $date;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($nombre, $numero, $fecha){
        $this->name     = $nombre;
        $this->number   = $numero;
        $this->date     = $fecha;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'MI CAR AUTO LAVADO',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'mail.correoFactura',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
