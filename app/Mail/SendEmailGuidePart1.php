<?php

namespace App\Mail;

use App\Models\Application;
use App\Models\SentEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendEmailGuidePart1 extends Mailable
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
        $fileName = $this->app->application_no . '-guide-part-1.pdf';

        return $this->view('emails.send-email-guide')
            ->from('no-reply@youthexchange.org.uk', 'Rotary Youth Exchange')
            ->to($this->app->email_address)
            ->cc($this->app->parent1_email? $this->app->parent1_email: "")
            ->subject('Guide (Part 1) to International ' . $this->type . ' - ' . $this->app->full_name)
            ->attach(storage_path('app/' . $fileName), [
                'mime' => 'application/pdf'
            ]);
    }
}
