<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;
use App\Services\UserService;
use Illuminate\Support\Facades\Crypt;


class SendPasswordResetEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $userId;
    public $newPassword;
    protected $encryptedPassword;

    /**
     * Create a new job instance.
     */
    public function __construct(int $userId, string $newPassword)
    {
        $this->userId = $userId;
        $this->encryptedPassword = Crypt::encryptString($newPassword);
    }

    /**
     * Execute the job.
     */
     public function handle(UserService $userService)
    {
        
        $user = $userService->find($this->userId); 
        
        if (!$user || !$user->email) { 
            return;
        }

        $email = $user->email;
        $newPassword = Crypt::decryptString($this->encryptedPassword);

        Mail::send([], [], function (Message $message) use ($email, $newPassword) {
            $message->to($email);
            $message->subject("Your password has been reset!");
            $message->text(
                "Your password has been reset.\n" .
                "Here is your new password: " . $newPassword .
                "\n\n" .
                "URL: " . env("APP_URL") .
                "\n\n" .
                "If you have any problem, please contact the administrator for support."
            );
        });
    }
}
