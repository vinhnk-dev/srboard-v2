<?php

namespace App\Jobs;

use App\Mail\DefaultMailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendHtmlMail  implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $email;
    protected $title;
    protected $content;

    /**
     * Create a new job instance.
     */
    public function __construct($email, $title, $content)
    {
        $this->email = $email;
        $this->title = $title;
        $this->content = $content;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $email = $this->email;
        if ($email) {
            Mail::to($email)->send(new DefaultMailTemplate($this->title,$this->content));
        }
    }
}
