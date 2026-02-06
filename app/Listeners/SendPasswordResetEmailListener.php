<?php

namespace App\Listeners;

use App\Events\UserPasswordUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Jobs\SendPasswordResetEmail;

class SendPasswordResetEmailListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserPasswordUpdated $event): void
    {
        SendPasswordResetEmail::dispatch($event->userId, $event->newPassword);
    }
}
