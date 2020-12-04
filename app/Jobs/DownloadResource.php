<?php

namespace App\Jobs;

use App\Mail\EmailForDownloadQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\File;
use App\Mangapark;
use App\Lightnovelworld;


class DownloadResource implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $data;
    protected $type;
    protected $email;
    protected $downloadManager;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data, $type, $email)
    {
        $this->email = $email;
        $this->data = $data;
        $this->type = $type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //poner un maxexecution time y probar con un recurso que tarde bastante

        //ini_set('max_execution_time', 1800);

        $pathToDownload = "";
        switch ($this->type) {
            case ("manga"):
                $downloadManager = new Mangapark(public_path() . "/users");
                $downloadManager->downloadVersions($this->data["selection"], $this->data["userID"], $this->data["resourceName"]);
                File::deleteDirectory(public_path() . "/users/" . $this->data["userID"] . "/" . $this->data["resourceName"]);
                $pathToDownload = "http://172.17.0.2/sercopiDownload/public/users/" . $this->data["userID"] . "/" . $this->data["resourceName"] . "_" . date('m-d-Y_hia') . ".zip";

                break;
            case ("novel"):
                $downloadManager = new Lightnovelworld();
                $downloadManager->createBook($this->data["chapters"], $this->data["resourceName"], $this->data["path"]);
                $pathToDownload = "http://172.17.0.2/sercopiDownload/public/users/" . $this->data["userID"] . "/" . $this->data["resourceName"] . "_" . date('m-d-Y_hia') . ".pdf";
                break;
        }
        $data = ["path" => $pathToDownload];
        Mail::send("mails.mail", $data, function ($message) {
            $message->to("sergiiosercopi@gmail.com", "artisan")->subject("descarga");
            $message->from("vpssergiocorderopino@gmail.com", "sergio");
        });
        /* $email = new EmailForDownloadQueue($pathToDownload, $this->data["resourceName"]);
        Mail::to($this->email)->send($email); */
    }
}
