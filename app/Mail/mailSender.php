<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class mailSender extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($template)
    {
        $this->data = array();
        $this->template = $template;
        $this->reciever_name = '';
        $this->subject = 'MHG Portal';
        $this->btnLink = 'https://portal.waterscoutingmhg.nl/';
    }

    public function setData($data)
    {
        $this->$data = $data;
    }

    public function setRecieverName($name)
    {
        $this->reciever_name = $name;
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    public function setBtnLink($link)
    {
        $this->btnLink = $link;
    } 

    public function generateMail()
    {
        return $this->markdown('emails.'.$this->template)
                    ->with(['data', $this->data], ['reciever_name', $this->reciever_name], ['btnLink', $this->btnLink])
                    ->subject($this->subject);
    }
}