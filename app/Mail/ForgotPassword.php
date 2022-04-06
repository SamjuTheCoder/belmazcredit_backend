<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ForgotPassword extends Mailable
{
    use Queueable, SerializesModels;
    public $email, $transaction_code;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($email, $transaction_code)
    {
        //
        $this->email = $email;
        $this->transaction_code = $transaction_code;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('forgot-password');
    }
}
