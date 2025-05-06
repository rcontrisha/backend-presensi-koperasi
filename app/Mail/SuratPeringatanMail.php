<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SuratPeringatanMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $pdf;

    public function __construct($pdf, $data)
    {
        $this->pdf = $pdf;
        $this->data = $data;
    }

    public function build()
    {
        return $this->subject('Surat Peringatan')
            ->view('email.surat-peringatan') // boleh kosong / dummy view
            ->attachData($this->pdf->output(), 'SuratPeringatan.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}
