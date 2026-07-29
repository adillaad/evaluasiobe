<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UniversityRegistrationRejected extends Mailable
{
    use Queueable, SerializesModels;

    public $register;
    public $credentials;
    public $reason;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($register, $credentials, $reason)
    {
        $this->register = $register;
        $this->credentials = $credentials;
        $this->reason = $reason;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.university-registration-rejected')
            ->subject('Pendaftaran Universitas Ditolak');
    }
}
