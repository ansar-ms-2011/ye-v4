<?php

namespace App\Mail;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendEmailGuideConfirm extends Mailable
{
    use Queueable, SerializesModels;
    public Application $app;
    public string $type;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Application  $application, $type)
    {
        $this->app = $application;
        $this->type = $type;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.confirm-email-guide-to-dyeo')
            ->from('no-reply@youthexchange.org.uk', 'Rotary Youth Exchange')
            ->to($this->app->dyeo->dyeo_email)
            ->cc($this->app->parent1_email? $this->app->parent1_email: "")
            ->subject('Guide to International '.$this->type.' - Confirmation');
    }
}
