<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class emailBasic extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $msgData;
    public function __construct($msgData)
    {
        $this->msgData = $msgData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $address = 'info@pinkexc.com';
        $name = 'Dorado Support';
        $subject = $this->msgData['subject'];
        return $this->view('email.template_email_basic')
                ->with($this->msgData)
                ->from($address,$name)
                ->replyTo($address,$name)
                ->subject($subject);

    }
}
