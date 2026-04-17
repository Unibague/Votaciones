<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class VoteCertificateMail extends Mailable
{
    public $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function build()
    {
        return $this->subject('Certificado de votacion')
            ->view('emails.vote-certificate')
            ->with([
                'name' => $this->name,
            ]);
    }
}
