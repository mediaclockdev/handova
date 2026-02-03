<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class IssueAssignedToBuilder extends Mailable
{
    public function __construct(public $issueReport) {}

    public function build()
    {
        return $this
            ->subject('Issue Request Assigned to Service Provider')
            ->view('emails.issue_assigned_builder');
    }
}
