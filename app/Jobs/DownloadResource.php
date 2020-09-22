<?php

namespace App\Jobs;

use App\Mail\EmailForDownloadQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class DownloadResource implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $title;
    protected $email;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($title, $email)
    {
        $this->title = $title;
        $this->email = $email;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $email = new EmailForDownloadQueue($this->title);
        Mail::to($this->email)->send($email);
    }
}
