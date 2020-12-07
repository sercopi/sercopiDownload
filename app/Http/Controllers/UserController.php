<?php

namespace App\Http\Controllers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use App\Mangapark;
use App\Manga;
use App\Rating;
use App\Novel;
use App\Follow;
use App\Comment;
use App\Lightnovelworld;
use App\Mail\EmailForDownloadQueue;
use App\Jobs\DownloadResource;
use App\Mangas_update_history;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware("user");
    }

    /*Dummy Function to test pieces of code when necessary*/

    public function test($nombre)
    {
        /* $data = ["name" => "sergio", "body" => "prueba"];
        Mail::send("mails.mail", $data, function ($message) {
            $message->to("sergiiosercopi@gmail.com", "artisan")->subject("prueba");
            $message->from("vpssergiocorderopino@gmail.com", "sergio");
        });
        echo "mensaje enviado"; */
        /* DownloadResource::dispatch("prueba queue", "sergiiosercopi@gmail.com");
        echo "job set"; */
        /* dd(Manga::where("name", "god-among-men-2")->first()->comments()->where("id", 59)->first()->likes()->select(DB::raw('SUM(likes.like) as total'))->get()->first()->total); */
        /* $comment = Auth::user()->comments()->create(["rating" => 2, "comment" => "example"]);
        Comment::where("id", 59)->first()->comments()->save($comment);
        return dd(Comment::where("user_id", 1)->where("id", 59)->first()->comments()->get()); */
        //return dd(Comment::where("id", 75)->first()->commentable()->first());
        //return dd(Manga::where("name", "god-among-men-2")->first()->comments()->where("user_id", 1)->where("id", 88)->first());
        /*         return dd(Comment::where("user_id", Auth::user()->id)->where("id", 99)->first()->delete());*/
        //return dd(Manga::where("id", 600)->first()->manga_rating_history()->get());

        /* $recentUpdates = DB::select(DB::raw("
        select * from (
            select 'manga' as resourceType,mangas.name,mangas.imageInfo,tabla.chapters_introduced,tabla.created_at
            from (
                (SELECT o.*
                FROM `mangas_update_history` o                
                LEFT JOIN `mangas_update_history` b          
                  ON o.manga_id = b.manga_id AND o.created_at < b.created_at
                WHERE b.created_at is NULL) tabla 
                join mangas on mangas.id = tabla.manga_id
                join follows on follows.followable_id = mangas.id 
                AND follows.follow=1 
                AND follows.user_id=:userIDmanga
                AND follows.followable_type=:mangaType
            ) 
            UNION ALL 
            select 'novel' as resourceType,novels.name,novels.imageInfo,tabla.chapters_introduced,tabla.created_at
            from (
                (SELECT o.*
                FROM `novels_update_history` o                
                LEFT JOIN `novels_update_history` b          
                  ON o.novel_id = b.novel_id AND o.created_at < b.created_at
                WHERE b.created_at is NULL) tabla 
                join novels on novels.id = tabla.novel_id
                join follows on follows.followable_id = novels.id 
                AND follows.follow=1 
                AND follows.user_id=:userIDnovel
                AND follows.followable_type=:novelType
            ) 
        ) final
        "), ["userIDmanga" => Auth::user()->id, "userIDnovel" => Auth::user()->id, "mangaType" => "App\Manga", "novelType" => "App\novel"]);
        return dd($recentUpdates); */
        return $_ENV["MAIL_USERNAME"];
        return dd(URL::to(""));
        return view("user.layouts.ckeditor");
    }


    /*
    Controls the follows, when the user interacts with a 
    resource by the follow/unfollow and notifications On/Off buttons

    The Logic:
    The user can follow a resource to get updates in his feed and turn on notifications
    to recieve periodic emaisl with updates (To Implement)

    Following a resource doesnt enable the notifications, but unfollowing disables it,
    as it doesnt make sense to recieve notifications from a resource you are not following
    In the same way, turning on notifications, enables the follow
    */
    public function follow($resourceType, $resourceName, Request $request)
    {
        //get the resource
        switch ($resourceType) {
            case ("novel"):
                $resource = Novel::where("name", $resourceName)->first();
                break;
            case ("manga"):
                $resource = Manga::where("name", $resourceName)->first();
                break;
            default:
                abort(404);
                break;
        }
        //check if the user already followed the resource
        $follow = $resource->follows()->where("user_id", Auth::user()->id)->first();
        //if the petition is of type follow
        if (!is_null($request->input("follow"))) {
            //and the user already followed the resource
            if (!is_null($follow)) {
                //turns off the follow and notifications
                $follow->update(["follow" => 0, "notifications" => 0]);
                return response("", 200);
            }
            //de nuevo desmarcar los campos followable_type y followable_id
            /*
            if the user didnt follow, turns on the follows and set notifications to 0
            */
            $newFollow = Auth::user()->follows()->create(["follow" => 1, "notifications" => 0]);
            $resource->follows()->save($newFollow);
            return response("", 200);
        }
        //if the petition is of type notifications
        if (!is_null($request->input("notifications"))) {
            //and the user already followed the resource
            if (!is_null($follow)) {
                //turns notifications and does nothing to the follow
                $follow->update(["notifications" => 1]);
                return response("", 200);
            }
            //de nuevo desmarcar los campos followable_type y followable_id
            //if the user didnt follow, it turns on the follows and the notifications
            $newFollow = Auth::user()->follows()->create(["follow" => 1, "notifications" => 1]);
            $resource->follows()->save($newFollow);
            return response("", 200);
        }
        return abort(400);
    }
    /*
    Controls the rating of a resource by the user 
    */
    public function rating($resourceType, $resourceName, Request $request)
    {
        /*
        get the resource
        */
        switch ($resourceType) {
            case ("novel"):
                $resource = Novel::where("name", $resourceName)->first();
                break;
            case ("manga"):
                $resource = Manga::where("name", $resourceName)->first();
                break;
            default:
                abort(404);
                break;
        }
        //if the user already rated the resource
        if ($rating = Auth::user()->ratings()->where("ratingable_id", $resource->id)->first()) {
            //updates it
            $rating->update(["rating" => $request->input("rating")]);
            //else
        } else {
            //creates it
            $rating = Auth::user()->ratings()->create(["rating" => $request->input("rating")]);
        }
        $resource->ratings()->save($rating);
        //And calls the updateRating function on the resource,
        // that calculates and updates the new rating, 
        //giving it back to pass to the client
        return $resource->updateRating();
    }

    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     */

    /*
     Index Function, return the index view
     */
    public function index()
    {
        //gets the most popular mangas, which are: 
        //the most searched mangas in the last 4 days
        $queryMostPopular = DB::select(DB::raw("select mangas.id,mangas.name,count(mangas.name) as searches from mangas inner join manga_user on mangas.id=manga_user.manga_id inner join users on users.id=manga_user.user_id where datediff(manga_user.created_at,CURRENT_TIMESTAMP())<4 group by mangas.name,mangas.id order by searches DESC limit 20"));
        /*
        The query doesnt return the resoruces as Eloquent objects,
         but as an array, so we iterate over them to cast the to a colleciton of eloquent objects
        */
        $twentyMostPopular = Collection::make();
        foreach ($queryMostPopular as $manga) {
            $twentyMostPopular->push(Manga::where("name", $manga->name)->first());
        }
        //si no hay suficientes resultados 
        //(la aplicacion acaba de empezar o no tiene busquedas en los ultimso 4 dias),
        // se llena el array hasta 20, que deberian ser randoms, pero obtener 20 randoms tarda
        //demasiado debido al volumend e registros, asi que los lleno con los 20 primeros.
        if (count($twentyMostPopular) < 20) {
            $randoms = Manga::limit(20 - count($twentyMostPopular))->get();
            $twentyMostPopular = $twentyMostPopular->merge($randoms);
        }

        $twentyLastAdded = Manga::orderBy("created_at", "DESC")->limit(20)->get();


        return view("user.index", compact("twentyMostPopular", "twentyLastAdded"));
    }
    /*
    Returns a rendered partial view of all the recent updates on the user's followed resources
    */
    public function followsUpdates($nombre, Request $request)
    {
        //most recent updates of both resource types followed by the user
        $recentUpdates = DB::select(DB::raw("
select * from (
    select 'manga' as resourceType,mangas.name,mangas.imageInfo,tabla.chapters_introduced,tabla.created_at
    from (
        (SELECT o.*
        FROM `mangas_update_history` o                
        LEFT JOIN `mangas_update_history` b          
          ON o.manga_id = b.manga_id AND o.created_at < b.created_at
        WHERE b.created_at is NULL) tabla 
        join mangas on mangas.id = tabla.manga_id
        join follows on follows.followable_id = mangas.id 
        AND follows.follow=1 
        AND follows.user_id=:userIDmanga
        AND follows.followable_type=:mangaType
    ) 
    UNION ALL 
    select 'novel' as resourceType,novels.name,novels.imageInfo,tabla.chapters_introduced,tabla.created_at
    from (
        (SELECT o.*
        FROM `novels_update_history` o                
        LEFT JOIN `novels_update_history` b          
          ON o.novel_id = b.novel_id AND o.created_at < b.created_at
        WHERE b.created_at is NULL) tabla 
        join novels on novels.id = tabla.novel_id
        join follows on follows.followable_id = novels.id 
        AND follows.follow=1 
        AND follows.user_id=:userIDnovel
        AND follows.followable_type=:novelType
    ) 
) final
"), ["userIDmanga" => Auth::user()->id, "userIDnovel" => Auth::user()->id, "mangaType" => "App\Manga", "novelType" => "App\novel"]);
        //turn the recent updates array into an Eloquent collection so it can paginate it
        $recentUpdates = Collection::make($recentUpdates);
        $currentPage = is_null($request->input("page")) ? 1 : $request->input("page");
        $totalPages = ceil($recentUpdates->count() / 28);
        $baseURL = URL::to("user/" . Auth::user()->name . "/followsUpdates?page=");
        $recentUpdates = $recentUpdates->skip(($currentPage - 1) * 28)->take(28);
        //builds the partail view with the paginated results
        return json_encode(view("user.layouts.followsUpdates", compact("recentUpdates", "baseURL", "totalPages", "currentPage"))->render());
    }
    //returns the main view of the user's followed resources
    public function followsView($nombre, Request $request)
    {
        return view("user.follows");
    }

    /*
        returns the rendered partial view of the users's followed resources paginated, 
        it's called after the main view has loaded to do a lazy load
    */
    public function allFollows($nombre, Request $request)
    {
        //get the followed mangas
        $mangas = Manga::join("follows", function ($join) {
            $join->where("followable_type", "=", "App\Manga");
            $join->where("follow", "=", 1);
            $join->where("user_id", "=", Auth::user()->id);
            $join->on("followable_id", "=", "mangas.id");
        })->get();
        //paginates them
        $currentPage = is_null($request->input("pageManga")) ? 1 : $request->input("pageManga");
        $totalPages = ceil($mangas->count() / 5);
        $baseURL = URL::to("/user/" . Auth::user()->name . "/followFeed?pageManga=");
        $resources = $mangas->skip(($currentPage - 1) * 5)->take(5);
        $resourceType = "manga";
        //render the partial view
        $viewManga =  view("user.layouts.followsFeed", compact("resourceType", "resources", "currentPage", "totalPages", "baseURL"))->render();

        //get the followed novels
        $novels = Novel::join("follows", function ($join) {
            $join->where("followable_type", "=", "App\Novel");
            $join->where("follow", "=", 1);
            $join->where("user_id", "=", Auth::user()->id);
            $join->on("followable_id", "=", "novels.id");
        })->get();
        //paginates them
        $pageNovel = is_null($request->input("pageNovel")) ? 1 : $request->input("pageNovel");
        $totalPages = ceil($novels->count() / 5);
        $resourceType = "novel";
        $baseURL = URL::to("/user/" . Auth::user()->name . "/followFeed?pageNovel=");
        $resources = $novels->skip(($currentPage - 1) * 5)->take(5);
        $resourceType = "novel";
        //render the partial view
        $viewNovel =  view("user.layouts.followsFeed", compact("resourceType", "resources", "currentPage", "totalPages", "baseURL"))->render();
        //returns the mangas and novels rendered views
        return json_encode("<h2>Mangas</h2>" . $viewManga . "<hr><h2>Novels</h2>" . $viewNovel);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param string $nombre
     * @return \Illuminate\Http\Response
     * 
     */
    /*
    Returns the total view of a resource page
    */
    public function show($nombre, $resourceType, $resourceName)
    {
        //gets the resource
        switch ($resourceType) {
            case ("manga"):
                $resource = Manga::where("name", $resourceName)->first();
                break;
            case ("novel");
                $resource = Novel::where("name", $resourceName)->first();
                break;
            default:
                abort(404);
                break;
        }
        //gets the comments for the resource
        $commentsFound = $resource->comments()->get();
        $commented = !is_null(Auth::user()->comments()->where("commentable_id", $resource->id)->first());
        //creates a register of the interaction
        $resource->users()->attach(Auth::user());
        return view("user.show", compact("resource", "commentsFound", "commented", "resourceType"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param string $nombre
     * @return \Illuminate\Http\Response
     * 
     */
    /*
    Handles a simple search by name, located at the top menu,
    Returns a total view of the search result
    */
    public function search($nombre, Request $request)
    {
        //gets the resources by name and values 
        //for the page number of each type of resource, to paginate them accordingly
        $seriesName = str_replace(" ", "-", $request->input("seriesName"));
        $pageManga = is_null($request->input("pageManga")) ? 1 : $request->input("pageManga");
        $pageNovel = is_null($request->input("pageNovel")) ? 1 : $request->input("pageNovel");
        //Uses Function searchResource to pull the resource by name
        $searchMangas = $this->searchResource($seriesName, 28, $pageManga, "manga");
        $resources["mangas"]["resources"] = $searchMangas["resources"];
        $resources["mangas"]["totalPages"] = ceil($searchMangas["total"] / 28);
        $resources["mangas"]["page"] = $pageManga;
        //Uses Function searchResource to pull the resource by name
        $searchNovels = $this->searchResource($seriesName, 28, $pageNovel, "novel");
        $resources["novels"]["resources"] = $searchNovels["resources"];
        $resources["novels"]["totalPages"] = ceil($searchNovels["total"] / 28);
        $resources["novels"]["page"] = $pageNovel;
        return view("user.search", compact("resources", "seriesName"));
    }

    /*
        Orders a collection of resources by a given parameter
    */
    public function orderByCallback($resources, $option = false)
    {
        switch ($option) {
            case ("alphReverse"):
                $resources = $resources->orderBy("name", "DESC");
                break;
            case ("rating"):
                $resources = $resources->orderBy("score", "DESC");
                break;
            default:
                $resources = $resources->orderBy("name", "ASC");
                break;
        }
        return $resources;
    }

    /*
        Handles the search of a resource, its pagination and ordenation based on some params
    */
    public function searchResource($resourceName, $resourceBatchNumber, $batchPage, $type, $orderByCallBack = false, $additionalParams = false)
    {
        //gets the resources of each type similar to the name given
        switch ($type) {
            case "manga":
                $resources = Manga::Where('name', 'like', $resourceName . '%');
                break;
            case "novel":
                $resources = Novel::Where("name", "like", $resourceName . "%");
                break;
        }
        //additional search params, if there are any, are handled on another function 
        if ($additionalParams) {
            $resources = $this->wherePipeConstructor($additionalParams, $resources);
        }
        //orders the result
        $resources =  $this->orderByCallback($resources, $orderByCallBack);
        $total = $resources->count();
        //returns the resulting collection of resources, paginated by params given to the function
        return ["resources" => $resources->skip(($batchPage - 1) * $resourceBatchNumber)->take($resourceBatchNumber)->get(), "total" => $total];
    }

    /*
        Adds conditions to a collection to filter by the given params and returns the result
    */
    public function wherePipeConstructor($params, $resources)
    {
        //iterates over an array of possible conditions
        foreach ($params as $columnName => $columnValues) {
            //if the user set values for that condition, it is processed
            if (!is_null($columnValues)) {
                //if the values are to be included in the search result...
                if (isset($columnValues["included"]) && !is_null($columnValues["included"][0])) {
                    //for each value, it iterates over the collection of resources,
                    // applying the condition and the value (which is to be included)
                    foreach ($columnValues["included"] as $value) {
                        $resources = $resources->Where($columnName, "like", "%" . $value . "%");
                    }
                }
                //if the values are to be excluded in the search result...
                if (isset($columnValues["excluded"]) && !is_null($columnValues["excluded"][0])) {
                    //for each value, it iterates over the collection of resources,
                    // applying the condition and the value (which is to be excluded)
                    foreach ($columnValues["excluded"] as $value) {
                        $resources = $resources->Where($columnName, "not like", "%" . $value . "%");
                    }
                }
            }
        }
        //returns the filtered collection
        return $resources;
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param string $nombre
     * @return \Illuminate\Http\Response
     */
    /*
        Returns a total view portaying the History of interactions
         the user had with the resources (searches and downloads)
    */
    public function history($nombre, Request $request)
    {
        $page = $request->input("page");
        $page = is_null($request->input("page")) ? 1 : $request->input("page");
        //gets all the mangas and novels the user had an interaction with as a single collection
        $totalResult = DB::select(DB::raw("select * from (
            (select 'manga' as resourceType,users.id as userID,manga_user.created_at,download,mangas.name as nombre 
                from users 
                join manga_user 
                on users.id=manga_user.user_id 
                join mangas 
                on mangas.id=manga_user.manga_id 
                where manga_user.created_at is not null)
            UNION ALL 
            (select 'novel' as resourceType,users.id as userID,novel_user.created_at,download,novels.name as nombre 
            from users 
            join novel_user 
            on users.id=novel_user.user_id 
            join novels 
            on novels.id=novel_user.novel_id 
            where novel_user.created_at is not null)
        ) table1 where userID = :userID order by created_at DESC"), ["userID" => Auth::user()->id]);
        //creates a collection with the results to use Eloquent's functions to paginate it
        $totalResult = Collection::make($totalResult);
        $totalPages = ceil($totalResult->count() / 30);
        $pageResults = $totalResult->skip(($page - 1) * 30)->take(30);
        //the information of each download, is a json array encoded in the DB as text, here it is parsed
        foreach ($pageResults as $result) {
            $result->download = json_decode($result->download, true);
        }
        //returns the view
        return view("user.history", compact("pageResults", "totalPages", "page"));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param string $nombre
     * @param string $seriesName
     * @return \Illuminate\Http\Response
     */
    /*
        Handles the Download Request
    */
    public function download($nombre, $resourceType, $resourceName, Request $request)
    {
        $selection = $request->input("selection");
        //if there is no selection to download, redirect with a custom error to the client
        if (!$selection) {
            return redirect()->back()->with("error", "please select something to download!");
        }
        //depending on the resource type, the download is handled differently
        switch ($resourceType) {
                //if its a manga
            case ("manga"):
                //select the manga
                $resource = Manga::where("name", $resourceName)->first();
                //decode the array containing the versions and the chapters of each version
                $resourceChapters = json_decode($resource->chapters, true);
                //iterate over the selection to add the URL stored in the DB to each chapter selected
                foreach ($selection as $version => $versionChapters) {
                    foreach ($versionChapters as $versionChapter) {
                        $selection[$version][$versionChapter] = $resourceChapters[$version]["chapters"][$versionChapter];
                    }
                }
                //add the interaction history of the download to the history table of the resource
                $resource->users()->attach(Auth::user(), ["download" => json_encode($selection)]);

                //tarda a razon de 13 segundos por capitulo, realizar seguimiento con chrono y optimizar...

                $data = ["selection" => $selection, "userID" => Auth::user()->id, "resourceName" => $resourceName, "path" =>  public_path() . "/users/" . Auth::user()->id];
                //TODO: SUSTITUIR POR EL CORREO DEL USUARIO CUANDO ESTE LISTO DEL TODO
                //Dispatch a job to enter the queue that will resolve later to do the download
                DownloadResource::dispatch($data, "manga", Auth::user()->email);
                break;
            case ("novel"):
                //select the novel
                $novel = Novel::where("name", $resourceName)->first();
                //get the chapters from the DB, that are in the array containing the chapters selected
                $chapters = $novel->novel_chapters->whereIn("number", $selection);
                //add the interaction history of the download to the history table of the resource
                $novel->users()->attach(Auth::user(), ["download" => json_encode($selection)]);
                $data = ["chapters" => $chapters, "userID" => Auth::user()->id, "resourceName" => $resourceName, "path" =>  public_path() . "/users/" . Auth::user()->id];
                //TODO: SUSTITUIR POR EL CORREO DEL USUARIO CUANDO ESTE LISTO DEL TODO
                //Dispatch a job to enter the queue that will resolve later to do the download
                DownloadResource::dispatch($data, "novel", Auth::user()->email);
                break;
            default:
                abort(404);
                break;
        }
        //return a custom message to the user
        return redirect()->back()->with("success", "your download will be send to your email!");
    }

    /*
        Returns the partial view of an advanced search with additional
        params for the search to be filtered and ordered
    */
    public function advancedSearch($nombre, Request $request)
    {
        //if there was nothing selected, returns back with an error stored in the session
        if (is_null($request->input("selection"))) {
            Session::flash("search error", "select something!");
            return view("user.advancedSearch");
        }
        //selection are all the parameters that determines the search
        $selection = $request->input("selection");
        $selectionName = str_replace(" ", "-", $selection["name"]);
        $currentPage = is_null($request->input("pageManga")) ? 1 : $request->input("pageManga");
        //uses searchResources Function with the selection and the order specified,
        // to get the collection of mangas that meets the criteria
        $searchMangas = $this->searchResource($selectionName, 28, $currentPage, "manga", $request->input("order"), $selection);
        //paginates the result
        $resources = $searchMangas["resources"];
        $totalPages = ceil($searchMangas["total"] / 28);
        $resourceType = "manga";
        $baseURL = URL::to("/user/" . Auth::user()->name . "/advancedSearch?pageManga=");
        //renders the view list of mangas found
        $viewManga =  view("user.layouts.pagination", compact("resources", "currentPage", "resourceType", "totalPages", "baseURL"))->render();
        $currentPage = is_null($request->input("pageNovel")) ? 1 : $request->input("pageNovel");
        //uses searchResources Function with the selection and the order specified,
        // to get the collection of novels that meets the criteria
        $searchNovels = $this->searchResource($selectionName, 28, $currentPage, "novel", $request->input("order"), $selection);
        //paginates the result
        $resources = $searchNovels["resources"];
        $totalPages = ceil($searchNovels["total"] / 28);
        $resourceType = "novel";
        $baseURL = URL::to("/user/" . Auth::user()->name . "/advancedSearch?pageNovel=");
        //renders the view list of novels found
        $viewNovel =  view("user.layouts.pagination", compact("resources", "currentPage", "resourceType", "totalPages", "baseURL"))->render();
        //adds the 2 rendered partial views and returns the result
        return json_encode("<h2>Mangas</h2>" . $viewManga . "<hr><h2>Novels</h2>" . $viewNovel);
    }
}
