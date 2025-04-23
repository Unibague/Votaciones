<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class VoteCertificateMail extends Mailable
{
    public $name;
    public $cid;
    protected $imageData;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function withImage($imageData)
    {
        $this->imageData = $imageData;
        return $this;
    }

    public function build()
    {
        $cid = uniqid() . '@certificado';

        return $this->view('emails.vote-certificate')
            ->with([
                'name' => $this->name,
                'cid' => $cid,
            ])
            ->attachData(
                $this->imageData,
                'certificado-votacion.png',
                [
                    'as' => 'certificado-votacion.png',
                    'mime' => 'image/png',
                    'content_id' => $cid,  // 👈🏼 Esto es lo importante
                    'disposition' => 'inline', // 👈🏼 Así se incrusta y no como adjunto
                ]
            )
            ->subject('Certificado de votación');
    }

}
