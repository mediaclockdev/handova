<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class IssueCompletedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $issueReport;

    /**
     * Create a new message instance.
     */
    public function __construct($issueReport)
    {
        $this->issueReport = $issueReport;
    }


    public function build()
    {
        return $this->subject('OTP Verification for Service Completion')
            ->view('emails.issue-completed');
    }
}
