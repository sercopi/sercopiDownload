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
        return dd($recentUpdates);
    }

    public function follow($nombre, $resourceType, $resourceName, Request $request)
    {
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
        $follow = $resource->follows()->where("user_id", Auth::user()->id)->first();
        if (!is_null($request->input("follow"))) {
            if (!is_null($follow)) {
                $follow->update(["follow" => $follow->follow ? 0 : 1, "notifications" => 0]);
                return response("", 200);
            }
            //de nuevo desmarcar los campos followable_type y followable_id
            $newFollow = Auth::user()->follows()->create(["follow" => 1, "notifications" => 0]);
            $resource->follows()->save($newFollow);
            return response("", 200);
        }
        if (!is_null($request->input("notifications"))) {
            if (!is_null($follow)) {
                $follow->update(["follow" => 1, "notifications" => $follow->notifications ? 0 : 1]);
                return response("", 200);
            }
            //de nuevo desmarcar los campos followable_type y followable_id
            $newFollow = Auth::user()->follows()->create(["follow" => 1, "notifications" => 1]);
            $resource->follows()->save($newFollow);
            return response("", 200);
        }
        return abort(400);
    }

    public function rating($nombre, $resourceType, $resourceName, Request $request)
    {
        //de nuevo hay que desmarcar como requeridos los campos que lo asocian al recurso

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
        if ($rating = Auth::user()->ratings()->where("ratingable_id", $resource->id)->first()) {
            $rating->update(["rating" => $request->input("rating")]);
        } else {
            $rating = Auth::user()->ratings()->create(["rating" => $request->input("rating")]);
        }
        $resource->ratings()->save($rating);
        return $resource->updateRating();
    }

    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $queryMostPopular = DB::select(DB::raw("select mangas.id,mangas.name,count(mangas.name) as searches from mangas inner join manga_user on mangas.id=manga_user.manga_id inner join users on users.id=manga_user.user_id where datediff(manga_user.created_at,CURRENT_TIMESTAMP())<4 group by mangas.name,mangas.id order by searches DESC limit 20"));
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
        $recentUpdates = Collection::make($recentUpdates);
        $currentPage = is_null($request->input("page")) ? 1 : $request->input("page");
        $totalPages = ceil($recentUpdates->count() / 28);
        $baseURL = URL::to("user/" . Auth::user()->name . "/followsUpdates?page=");
        $recentUpdates = $recentUpdates->skip(($currentPage - 1) * 28)->take(28);
        return json_encode(view("user.layouts.followsUpdates", compact("recentUpdates", "baseURL", "totalPages", "currentPage"))->render());
    }
    public function followsView($nombre, Request $request)
    {
        return view("user.follows");
    }

    public function allFollows($nombre, Request $request)
    {
        $mangas = Manga::join("follows", function ($join) {
            $join->where("followable_type", "=", "App\Manga");
            $join->where("follow", "=", 1);
            $join->where("user_id", "=", Auth::user()->id);
            $join->on("followable_id", "=", "mangas.id");
        })->get();

        $currentPage = is_null($request->input("pageManga")) ? 1 : $request->input("pageManga");
        $totalPages = ceil($mangas->count() / 5);
        $baseURL = "http://172.17.0.2/sercopiDownload/public/user/" . Auth::user()->name . "/followFeed?pageManga=";
        $resources = $mangas->skip(($currentPage - 1) * 5)->take(5);
        $resourceType = "manga";
        $viewManga =  view("user.layouts.followsFeed", compact("resourceType", "resources", "currentPage", "totalPages", "baseURL"))->render();

        $novels = Novel::join("follows", function ($join) {
            $join->where("followable_type", "=", "App\Novel");
            $join->where("follow", "=", 1);
            $join->where("user_id", "=", Auth::user()->id);
            $join->on("followable_id", "=", "novels.id");
        })->get();
        $pageNovel = is_null($request->input("pageNovel")) ? 1 : $request->input("pageNovel");
        $totalPages = ceil($novels->count() / 5);
        $resourceType = "novel";
        $baseURL = "http://172.17.0.2/sercopiDownload/public/user/" . Auth::user()->name . "/followFeed?pageNovel=";
        $resources = $novels->skip(($currentPage - 1) * 5)->take(5);
        $resourceType = "novel";
        $viewNovel =  view("user.layouts.followsFeed", compact("resourceType", "resources", "currentPage", "totalPages", "baseURL"))->render();
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
    public function show($nombre, $resourceType, $resourceName)
    {
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
        $commentsFound = $resource->comments()->get();
        $commented = !is_null(Auth::user()->comments()->where("commentable_id", $resource->id)->first());
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
    public function search($nombre, Request $request)
    {
        $seriesName = str_replace(" ", "-", $request->input("seriesName"));
        $pageManga = is_null($request->input("pageManga")) ? 1 : $request->input("pageManga");
        $pageNovel = is_null($request->input("pageNovel")) ? 1 : $request->input("pageNovel");
        $searchMangas = $this->searchResource($seriesName, 28, $pageManga, "manga");
        $resources["mangas"]["resources"] = $searchMangas["resources"];
        $resources["mangas"]["totalPages"] = ceil($searchMangas["total"] / 28);
        $resources["mangas"]["page"] = $pageManga;
        $searchNovels = $this->searchResource($seriesName, 28, $pageNovel, "novel");
        $resources["novels"]["resources"] = $searchNovels["resources"];
        $resources["novels"]["totalPages"] = ceil($searchNovels["total"] / 28);
        $resources["novels"]["page"] = $pageNovel;
        return view("user.search", compact("resources", "seriesName"));
    }

    public function orderByCallbackDefault($resources)
    {
        return $resources->orderBy("name", "ASC");
    }

    public function searchResource($resourceName, $resourceBatchNumber, $batchPage, $type, $orderByCallBack = false, $additionalParams = false)
    {
        switch ($type) {
            case "manga":
                $resources = Manga::Where('name', 'like', $resourceName . '%');
                break;
            case "novel":
                $resources = Novel::Where("name", "like", $resourceName . "%");
                break;
        }
        if ($additionalParams) {
            $resources = $this->wherePipeConstructor($additionalParams, $resources);
        }
        $resources = $orderByCallBack ? $orderByCallBack($resources) : $this->orderByCallbackDefault($resources);
        $total = $resources->count();
        return ["resources" => $resources->skip(($batchPage - 1) * $resourceBatchNumber)->take($resourceBatchNumber)->get(), "total" => $total];
    }
    public function wherePipeConstructor($params, $resources)
    {
        foreach ($params as $columnName => $columnValues) {
            if (!is_null($columnValues)) {
                if (isset($columnValues["included"]) && !is_null($columnValues["included"][0])) {
                    foreach ($columnValues["included"] as $value) {
                        $resources = $resources->Where($columnName, "like", "%" . $value . "%");
                    }
                }
                if (isset($columnValues["excluded"]) && !is_null($columnValues["excluded"][0])) {
                    foreach ($columnValues["excluded"] as $value) {
                        $resources = $resources->Where($columnName, "not like", "%" . $value . "%");
                    }
                }
            }
        }
        return $resources;
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param string $nombre
     * @return \Illuminate\Http\Response
     */
    public function history($nombre, Request $request)
    {
        $page = $request->input("page");
        $page = is_null($request->input("page")) ? 1 : $request->input("page");
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
        $totalResult = Collection::make($totalResult);
        $totalPages = ceil($totalResult->count() / 30);
        $pageResults = $totalResult->skip(($page - 1) * 30)->take(30);
        foreach ($pageResults as $result) {
            $result->download = json_decode($result->download, true);
        }
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
    public function download($nombre, $resourceType, $resourceName, Request $request)
    {
        $selection = $request->input("selection");
        if (!$selection) {
            return redirect()->back()->with("error", "please select something to download!");
        }
        switch ($resourceType) {
            case ("manga"):
                $resource = Manga::where("name", $resourceName)->first();
                $resourceChapters = json_decode($resource->chapters, true);
                foreach ($selection as $version => $versionChapters) {
                    foreach ($versionChapters as $versionChapter) {
                        $selection[$version][$versionChapter] = $resourceChapters[$version]["chapters"][$versionChapter];
                    }
                }
                $resource->users()->attach(Auth::user(), ["download" => json_encode($selection)]);

                //tarda a razon de 13 segundos por capitulo, realizar seguimiento con chrono y optimizar...
                $data = ["selection" => $selection, "userID" => Auth::user()->id, "resourceName" => $resourceName, "path" =>  public_path() . "/users/" . Auth::user()->id];
                //SUSTITUIR POR EL CORREO DEL USUARIO CUANDO ESTE LISTO DEL TODO
                DownloadResource::dispatch($data, "manga", "sergiiosercopi@gmail.com");
                break;
            case ("novel"):
                $novel = Novel::where("name", $resourceName)->first();
                $chapters = $novel->novel_chapters->whereIn("number", $selection);
                $novel->users()->attach(Auth::user(), ["download" => json_encode($selection)]);
                $data = ["chapters" => $chapters, "userID" => Auth::user()->id, "resourceName" => $resourceName, "path" =>  public_path() . "/users/" . Auth::user()->id];
                //SUSTITUIR POR EL CORREO DEL USUARIO CUANDO ESTE LISTO DEL TODO
                DownloadResource::dispatch($data, "novel", "sergiiosercopi@gmail.com");
                break;
            default:
                abort(404);
                break;
        }
        return redirect()->back()->with("success", "your download will be send to your email!");
    }
    public function advancedSearch($nombre, Request $request)
    {
        if (is_null($request->input("selection"))) {
            Session::flash("search error", "select something!");
            return view("user.advancedSearch");
        }
        $selection = $request->input("selection");
        $currentPage = is_null($request->input("pageManga")) ? 1 : $request->input("pageManga");
        $searchMangas = $this->searchResource($selection["name"], 28, $currentPage, "manga", false, $selection);
        $resources = $searchMangas["resources"];
        $totalPages = ceil($searchMangas["total"] / 28);
        $resourceType = "manga";
        $baseURL = "http://172.17.0.2/sercopiDownload/public/user/" . Auth::user()->name . "/advancedSearch?pageManga=";
        $viewManga =  view("user.layouts.pagination", compact("resources", "currentPage", "resourceType", "totalPages", "baseURL"))->render();
        $pageNovel = is_null($request->input("pageNovel")) ? 1 : $request->input("pageNovel");
        $searchNovels = $this->searchResource($selection["name"], 28, $currentPage, "novel", false, $selection);
        $resources = $searchNovels["resources"];
        $totalPages = ceil($searchNovels["total"] / 28);
        $resourceType = "novel";
        $baseURL = "http://172.17.0.2/sercopiDownload/public/user/" . Auth::user()->name . "/advancedSearch?pageNovel=";
        $viewNovel =  view("user.layouts.pagination", compact("resources", "currentPage", "resourceType", "totalPages", "baseURL"))->render();
        return json_encode("<h2>Mangas</h2>" . $viewManga . "<hr><h2>Novels</h2>" . $viewNovel);
    }
}
