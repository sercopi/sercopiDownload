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
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;

use App\Lightnovelworld;
use App\Mangapark;


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


        //ini_set('max_execution_time', 1800);

        $pathToDownload = "";
        //execute the download and preparation of the resource for the user
        switch ($this->type) {
            case ("manga"):
                /*
                the mangas are too heavy to store in my DB, 
                so here we are only storing the links to each chapter 
                as mentioned in the documentation.

                That means, the functions of the scrapping class are used to extract the information (the images)
                */
                //first create the scrapper object
                $downloadManager = new Mangapark(public_path() . "/users");
                //execute downloadVersions, the function scrapes the content given by the selection and the resourceName and 
                //settles in a Folder named as the resource under a folder named after the user's ID inside the Users folder.
                //each chapter is done as a pdf and then they are all converted to a .zip file which is the final product named
                //after the resourceName and the time of the download
                $downloadManager->downloadVersions($this->data["selection"], $this->data["userID"], $this->data["resourceName"]);
                //eliminates the folder containing the pdfs used to create the .zip file that are no longer needed
                File::deleteDirectory(public_path() . "/users/" . $this->data["userID"] . "/" . $this->data["resourceName"]);
                //sets the path to the .zip product that is going to be delivered through the email
                $pathToDownload = URL::to("/users/" . $this->data["userID"] . "/" . $this->data["resourceName"] . "_" . date('m-d-Y_hia') . ".zip");
                break;
            case ("novel"):
                //since the novels are stored in the DB as a whole, 
                //here the chapters are already provided, so we create an instance of the novel Scrapper
                //to use the createBook function
                //this function creates a single pdf file containing all the chapters and settles it on a path that is
                //a folder under the users folder in the public area, named after the user's ID in which the pdf is going to be
                //stored using the resourceName and the time of the download
                $downloadManager = new Lightnovelworld();
                $downloadManager->createBook($this->data["chapters"], $this->data["resourceName"], $this->data["path"]);
                //sets the path to the pdf product that is going to be delivered through the email
                $pathToDownload = URL::to("/users/" . $this->data["userID"] . "/" . $this->data["resourceName"] . "_" . date('m-d-Y_hia') . ".pdf");
                break;
        }
        $data = ["path" => $pathToDownload];
        //send the email
        Mail::send("mails.mail", $data, function ($message) {
            $message->to($this->email, "artisan")->subject("su descarga ha sido procesada para: " . $this->data["resourceName"]);
            $message->from($_ENV["MAIL_USERNAME"], "sercopiDownload");
        });
        /* $email = new EmailForDownloadQueue($pathToDownload, $this->data["resourceName"]);
        Mail::to($this->email)->send($email); */
    }
}
