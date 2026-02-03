<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class IssueAssignedToServiceProvider extends Mailable
{
    public function __construct(
        public $issueReport,
        public $property
    ) {}

    public function build()
    {
        return $this
            ->subject('New Issue Assigned to You')
            ->view('emails.issue_assigned_service_provider');
    }
}
