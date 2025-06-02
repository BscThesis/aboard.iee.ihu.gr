<?php

namespace App\Events\V3;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewAnnouncementWasCreatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $announcement;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($announcement)
    {
        $this->announcement = $announcement;
    }
}
