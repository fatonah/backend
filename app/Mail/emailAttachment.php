<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class emailAttachment extends Mailable
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
        $address = $this->msgData['fromemail'];
        $name = $this->msgData['uname'];
        $subject = $this->msgData['subject'];
        $attachment = $this->msgData['attachment'];
        $file_name = $this->msgData['filename'];
        return $this->view('email.template_email_basic')
                ->with($this->msgData)
                ->from($address,$name)
                ->replyTo($address,$name)
                ->subject($subject)
                ->attach($attachment, [
                    'as' => $file_name, 
                    'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                ]);

    }
}
