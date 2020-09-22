<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailForDownloadQueue extends Mailable
{
    use Queueable, SerializesModels;
    protected $path;
    protected $resourceName;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($path, $resourceName)
    {
        $this->path = $path;
        $this->resourceName = $resourceName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from("vpssergiocorderopino@gmail.com", "sergio")->subject("descarga finalizada para: " . $this->resourceName)->view("mails.mail")->with(["path" => $this->path]);
    }
}
