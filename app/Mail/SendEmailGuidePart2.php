<?php

namespace App\Mail;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendEmailGuidePart2 extends Mailable
{
    use Queueable, SerializesModels;

    public Application $app;

    public string $type;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Application $application, $type)
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
        if ($this->app->exchange_type == 'CAMPS & TOURS') {
            $fileName = $this->app->application_no.'-guide-part-2.pdf';
        } else {
            $fileName = $this->app->application_no.'-guide-part-2.pdf';
        }

        return $this->view('emails.send-email-guide-part-2')
            ->from('no-reply@youthexchange.org.uk', 'Rotary Youth Exchange')
            ->to($this->app->email_address)
            ->cc($this->app->parent1_email ? $this->app->parent1_email : '')
            ->subject('Guide (Part 2) to International '.$this->type.' - '.$this->app->full_name)
            ->attach(storage_path('app/'.$fileName), [
                'mime' => 'application/pdf',
            ]);
    }
}
