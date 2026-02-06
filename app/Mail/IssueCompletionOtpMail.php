<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class IssueCompletionOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;
    public $issueReport;

    /**
     * Create a new message instance.
     */
    public function __construct($otp, $issueReport)
    {
        $this->otp = $otp;
        $this->issueReport = $issueReport;
    }

    public function build()
    {
        return $this->subject('OTP Verification for Service Completion')
            ->view('emails.issue-completion-otp');
    }
}
