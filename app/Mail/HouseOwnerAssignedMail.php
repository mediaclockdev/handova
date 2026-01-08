<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class HouseOwnerAssignedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $owner;
    public $builderId;

    public function __construct($owner, $builderId)
    {
        $this->owner = $owner;
        $this->builderId = $builderId;
    }

    public function build()
    {
        return $this->subject('Your Property Has Been Assigned')
            ->view('emails.house_owner_assigned');
    }
}
