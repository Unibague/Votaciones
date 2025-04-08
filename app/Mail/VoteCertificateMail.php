<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VoteCertificateMail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $certificateUrl;

    protected $qrImageBinary;

    public function __construct($name, $certificateUrl, $qrImageBinary)
    {
        $this->name = $name;
        $this->certificateUrl = $certificateUrl;
        $this->qrImageBinary = $qrImageBinary;
    }

    public function build()
    {
        return $this->view('emails.vote-certificate')
            ->subject('Certificado de votación')
            ->with([
                'name' => $this->name,
                'certificateUrl' => $this->certificateUrl,
            ])
            ->attachData($this->qrImageBinary, 'qr_certificado.png', [
                'mime' => 'image/png',
            ]);
    }
}
