<?php

namespace App\Http\Controllers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use App\Mangapark;
use App\Manga;
use App\Novel;
use App\Comment;
use App\Lightnovelworld;
use App\Mail\EmailForDownloadQueue;
use App\Jobs\DownloadResource;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware("user");
    }

    public function email($nombre)
    {
        /* $data = ["name" => "sergio", "body" => "prueba"];
        Mail::send("mails.mail", $data, function ($message) {
            $message->to("sergiiosercopi@gmail.com", "artisan")->subject("prueba");
            $message->from("vpssergiocorderopino@gmail.com", "sergio");
        });
        echo "mensaje enviado"; */
        DownloadResource::dispatch("prueba queue", "sergiiosercopi@gmail.com");
        echo "job set";
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
        $twentyBestRated = Manga::orderBy("score", "DESC")->limit(20)->get();
        if (count($twentyBestRated) < 20) {
            $randoms = Manga::limit(20 - count($twentyBestRated))->get();
            $twentyBestRated = $twentyBestRated->merge($randoms);
        }
        $twentyLastAdded = Manga::orderBy("created_at", "DESC")->limit(20)->get();
        return view("user.index", compact("twentyMostPopular", "twentyBestRated", "twentyLastAdded"));
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
                $resources = Novel::Where("name", "like",  $resourceName . "%");
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
        ini_set('max_execution_time', 1800);
        $selection = $request->input("selection");
        if (!$selection) {
            //hacerlo con sesiones
            Session::flash("error", "Nothing Chosen");
            return "nothing chosen";
        }
        switch ($resourceType) {
            case ("manga"):
                $download = $this->downloadManga($resourceName, $selection);
                break;
            case ("novel"):
                //return str_replace("<p>", "", str_replace("</p>", "\n\n", Novel::where("name", $resourceName)->first()->novel_chapters->where("number", 0)->first()->content));
                $novel = Novel::where("name", $resourceName)->first();
                $chapters = $novel->novel_chapters->whereIn("number", $selection);
                $novel->users()->attach(Auth::user(), ["download" => json_encode($selection)]);
                $lightNovelWorld = new Lightnovelworld();
                $lightNovelWorld->createBook($chapters, $resourceName, public_path() . "/users/" . Auth::user()->id);
                $download = public_path() . "/users/" . Auth::user()->id . "/" . $resourceName . ".pdf";
                break;
            default:
                abort(404);
                break;
        }
        return response()->download($download);


        $resource = Manga::where("name", $request->input("resourceName"))->first();
        $resourceChapters = json_decode($resource->chapters, true);
        foreach ($selection as $version => $versionChapters) {
            foreach ($versionChapters as $versionChapter) {
                $selection[$version][$versionChapter] = $resourceChapters[$version]["chapters"][$versionChapter];
            }
        }
        //si existe una descarga anterior para ese recurso y fue 
        //del mismo tipo, entonces se descarga, si no, se scrappea y elimina la descarga anterior.
        $finalName = public_path() . "/users/" . Auth::user()->id . "/" . $request->input("resourceName") . ".zip";
        $lastDownload = Auth::user()->mangas()->withPivot("download", "created_at")->where("mangas.name", $request->input("resourceName"))->whereNotNull("download")->orderBy("manga_user.created_at", "DESC")->first();
        $resource->users()->attach(Auth::user(), ["download" => json_encode($selection)]);
        if (file_exists($finalName) && !is_null($lastDownload) && $lastDownload->pivot->download === json_encode($selection)) {
            return response()->download($finalName);
        }
        File::deleteDirectory(public_path() . "/users/" . Auth::user()->id);
        $scrapper = new Mangapark(public_path() . "/users");
        //tarda a razon de 13 segundos por capitulo, realizar seguimiento con chrono y optimizar...
        $scrapper->downloadVersions($selection, Auth::user(), $request->input("resourceName"));
        File::deleteDirectory(public_path() . "/users/" . Auth::user()->id . "/" . $request->input("resourceName"));
        return response()->download($finalName);
    }
    public function downloadNovel($resourceName, $selection)
    {
        $chapters = Novel::where("name", $resourceName)->novel_chapters->whereIn("novel_chapter.id", $selection);

        return $chapters;
    }
    public function downloadManga($resourceName, $selection)
    {
        $resource = Manga::where("name", $resourceName)->first();
        $resourceChapters = json_decode($resource->chapters, true);
        foreach ($selection as $version => $versionChapters) {
            foreach ($versionChapters as $versionChapter) {
                $selection[$version][$versionChapter] = $resourceChapters[$version]["chapters"][$versionChapter];
            }
        }
        //si existe una descarga anterior para ese recurso y fue 
        //del mismo tipo, entonces se descarga, si no, se scrappea y elimina la descarga anterior.
        $finalName = public_path() . "/users/" . Auth::user()->id . "/" . $resourceName . ".zip";
        $lastDownload = Auth::user()->mangas()->withPivot("download", "created_at")->where("mangas.name", $resourceName)->whereNotNull("download")->orderBy("manga_user.created_at", "DESC")->first();
        $resource->users()->attach(Auth::user(), ["download" => json_encode($selection)]);
        if (file_exists($finalName) && !is_null($lastDownload) && $lastDownload->pivot->download === json_encode($selection)) {
            return response()->download($finalName);
        }
        File::deleteDirectory(public_path() . "/users/" . Auth::user()->id);
        $scrapper = new Mangapark(public_path() . "/users");
        //tarda a razon de 13 segundos por capitulo, realizar seguimiento con chrono y optimizar...
        $scrapper->downloadVersions($selection, Auth::user(), $resourceName);
        File::deleteDirectory(public_path() . "/users/" . Auth::user()->id . "/" . $resourceName);
        return $finalName;
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
        /* $searchNovels = $this->searchResource($seriesName, 28, $pageNovel, "novel");
        $novels = $resources["novels"]["resources"] = $searchNovels["resources"];
        $resources["novels"]["totalPages"] = ceil($searchNovels["total"] / 28);
        $resources["novels"]["page"] = $pageNovel; */
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
