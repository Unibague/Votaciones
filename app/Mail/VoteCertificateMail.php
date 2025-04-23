<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Support\Str;

class VoteCertificateMail extends Mailable
{
    public $name;
    public $cid;
    public $imageData;

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
        // ✅ Generamos un Content-ID manual para usar en el <img src="cid:...">
        $this->cid = Str::uuid() . '@certificado';

        return $this->view('emails.vote-certificate')
            ->with([
                'name' => $this->name,
                'cid' => $this->cid,
            ])
            ->attachData(
                $this->imageData,
                'certificado-votacion.png',
                [
                    'as' => 'certificado-votacion.png',
                    'mime' => 'image/png',
                    'content_id' => $this->cid, // 👈🏻 CID manual
                    'disposition' => 'inline',  // 👈🏻 Mostrar en el cuerpo
                ]
            )
            ->subject('Certificado de votación');
    }
}
