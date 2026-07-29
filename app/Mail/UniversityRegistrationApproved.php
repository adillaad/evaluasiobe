<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UniversityRegistrationApproved extends Mailable
{
    use Queueable, SerializesModels;

    public $university;
    public $credentials;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($university, $credentials)
    {
        $this->university = $university;
        $this->credentials = $credentials;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.university-registration-approved')
                    ->subject('Pendaftaran Universitas Disetujui');
    }
}
