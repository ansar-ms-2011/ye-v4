<?php

namespace App\Mail;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentEmailCamps extends Mailable
{
    use Queueable, SerializesModels;
    public $app;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Application  $application)
    {
        $this->app = $application;
    }
    //maja1812@icloud.com
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.payment-email-camps')
            ->from('no-reply@youthexchange.org.uk', 'Rotary Youth Exchange')
            ->to($this->app->email_address)
            ->cc($this->app->parent1_email? $this->app->parent1_email: "")
            ->subject('Youth Exchange Camp Administration Fee');
    }
}
