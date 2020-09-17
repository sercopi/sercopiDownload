<?php

namespace App\Http\Controllers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\File;
use App\Mangapark;
use App\Manga;
use App\Comment;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware("user");
    }
    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        Session::put("isManga", true);
        $queryMostPopular = DB::select(DB::raw("select mangas.id,mangas.name,count(mangas.name) as searches from mangas inner join manga_user on mangas.id=manga_user.manga_id inner join users on users.id=manga_user.user_id where datediff(manga_user.created_at,CURRENT_TIMESTAMP())<4 group by mangas.name order by searches DESC limit 20"));
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
    public function showManga($nombre, $mangaName)
    {
        //la logica de isManga deberia de ir en un middleware porque se repite siempre
        //$isManga = $request->has("typeSelected");
        Session::put("isManga", true);
        $resource = Manga::where("name", $mangaName)->first();
        $commentsFound = $resource->comments;
        $commented = !is_null(Auth::user()->comments()->where("commentable_id", $resource->id)->first());
        $resource->users()->attach(Auth::user());
        return view("user.show", compact("resource", "commentsFound", "commented"));
        //return ["resource" => $resource, "comments" => $resourceCommentsFormatted];
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
        $totalSearchResults = Manga::where('name', $seriesName)
            ->orWhere('name', 'like', '%' . $seriesName . '%')->count();
        $pageNumbersTotal = ceil($totalSearchResults / 28);
        $page = is_null($request->input("page")) ? 1 : $request->input("page");
        $isManga = $request->has("typeSelected");
        Session::put("isManga", $isManga);
        $resources =  $isManga ? Manga::where('name', $seriesName)
            ->orWhere('name', 'like', '%' . $seriesName . '%')->orderBy("mangas.name", "ASC")->skip(($page - 1) * 28)->take(28)->get() : "novel";
        return view("user.search", compact("resources", "seriesName", "pageNumbersTotal", "page"));
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
        $totalSearchResults = Auth::user()->mangas()->withPivot("download", "created_at")->whereNotNull("manga_user.created_at")->count();
        $totalPages = ceil($totalSearchResults / 30);
        $page = is_null($request->input("page")) ? 1 : $request->input("page");
        $historyPageResults = Auth::user()->mangas()->withPivot("download", "created_at")->whereNotNull("manga_user.created_at")->orderBy("manga_user.created_at", "DESC")->skip(($page - 1) * 30)->take(30)->get();
        foreach ($historyPageResults as $result) {
            $result->pivot->download = json_decode($result->pivot->download, true);
        }
        return view("user.history", compact("historyPageResults", "totalPages", "page"));
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * 
     */
    public function getNovel($request)
    {
    }
    /**
     * @param  \Illuminate\Http\Request  $request
     * 
     */
    public function getManga($request)
    {
        $seriesName = str_replace(" ", "-", $request->input("seriesName"));
        $scrapper = new Mangapark();
        $result = $scrapper->getSeriesInfo($seriesName, ["withImagen" => true]);
        $this->saveManga($result);
        return $result;
    }

    public function saveManga($data)
    {
        $formattedDataForTable = [
            "name" => $data["name"],
            "imageInfo" => $data["imageInfo"],
            "alternativeTitle" => $data["otherInfo"]["Alternative"],
            "author" => $data["otherInfo"]["Author(s)"],
            "artist" => $data["otherInfo"]["Artist(s)"],
            "genre" => $data["otherInfo"]["Genre(s)"],
            "type" => $data["otherInfo"]["Type"],
            "synopsis" => $data["synopsis"],
            "status" => $data["otherInfo"]["Status"],
            "chapters" => json_encode($data["versions"]),
        ];
        //si el manga existia, se updatea
        if ($manga = Manga::where("name", $data["name"])->first()) {
            $manga->update($formattedDataForTable);
            //si el usuario no lo tiene, se le asocia
            if (!Auth::user()->mangas()->where("name", $data["name"])->first()) {
                $manga->users()->attach(Auth::user());
            }
        } else {
            //si no existia, se crea y asocia a su usuario
            $manga = Manga::create($formattedDataForTable);
            Auth::user()->mangas()->attach($manga);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param string $nombre
     * @param string $seriesName
     * @return \Illuminate\Http\Response
     */
    public function download($nombre, Request $request)
    {
        $selection = $request->input("selection");
        if (!$selection) {
            //hacerlo con sesiones
            return "nothing chosen";
        }
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
}
