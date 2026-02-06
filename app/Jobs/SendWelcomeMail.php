<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\UserService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;

class SendWelcomeMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $userData;
    /**
     * Create a new job instance.
     */
    public function __construct(array $userData)
    {
        $this->userData = $userData;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $userData = $this->userData;
        $email = $userData['email'];
        
        Mail::send([], [], function (Message $message) use ($email, $userData) {
            $message->to($email);
            $message->subject("Welcome to SRboard");
            $message->text(
                "Welcome to MA-board, " .
                    $userData['name'] .
                    "\n\n" .
                    "Here is your account information to login:" .
                    "\n" .
                    "URL: " . env("APP_URL") .
                    "\n" .
                    "Username: " .
                    $userData['username'] .
                    "\n" .
                    "Password: " .
                    $userData['password'] .
                    "\r\n" .
                    "Remember to change your password." .
                    "\n" .
                    "~ Best regards ~"
            );
        });
    }
}
