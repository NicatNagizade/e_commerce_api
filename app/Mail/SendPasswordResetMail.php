<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendPasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    private $token;
    private $url;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($token, $url = null)
    {
        $this->token = $token;
        $this->url = $url;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if(!$this->url){
            $confirm_button_url = config('app.url').'/auth/reset/'.$this->token;
        }else{
            $confirm_button_url = $this->url.'/'.$this->token;
        }
        return $this->markdown('email.auth.reset',['url'=>$confirm_button_url]);
    }
}
