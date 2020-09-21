<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Comment;
use App\Manga;
use App\Novel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;


class UserCommentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware("user");
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveComment($nombre, $type, $resourceName, Request $request)
    {
        //incluir logica para preguntar si es novela o manga
        //Nota: he tenido que desmarcar como requeridos commentable type y comment_id para primero
        //asociar el usuario sin que esos dos campos esten y luego asociar los dos campos
        $comment = Auth::user()->comments()->create(["comment" => $request->input("comment"), "rating" => $request->input("rating")]);
        $resource = ($type == "manga") ? Manga::where("name", $resourceName)->first() : Novel::where("name", $resourceName)->first();
        $resource->comments()->save($comment);
        $score = DB::select(DB::raw("select CAST(AVG(comments.rating) AS DECIMAL(10,2)) as score from comments join " . $type . "s on comments.commentable_id=" . $type . "s.id where " . $type . "s.name=:resourceName group by " . $type . "s.name"), array(
            'resourceName' => $resourceName,
        ))[0]->score;
        $resource->update(["score" => $score]);
        return redirect()->route("show", ["nombre" => Auth::user()->name, "type" => $type, "resourceName" => $resourceName]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editComment($nombre, $type, $resourceName)
    {
        //codigo repetido en usercontroller, lo que nos esta diciendo que se 
        //debe de sacar a un servicio externo UserService, junto con otros.
        switch ($type) {
            case "manga":
                $resource = Manga::Where($type . "s.name", $resourceName)->first();
                break;
            case "novel":
                $resource = Novel::Where($type . "s.name", $resourceName)->first();
                break;
            default:
                abort(404);
                break;
        }
        $userComment = $resource->comments()->where("user_id", Auth::user()->id)->first();
        $commentsFound = $resource->comments()->where("user_id", "!=", Auth::user()->id)->get();
        $commented = !is_null(Auth::user()->comments()->where("commentable_id", $resource->id)->first());
        $resourceType = $type;
        return view("user.show", compact("resource", "commentsFound", "commented", "resourceType", "userComment"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateComment($nombre, $type, $resourceName, Request $request)
    {
        switch ($type) {
            case "manga":
                $resource = Manga::Where($type . "s.name", $resourceName)->first();
                break;
            case "novel":
                $resource = Novel::Where($type . "s.name", $resourceName)->first();
                break;
            default:
                abort(404);
                break;
        }
        $resource->comments()->where("user_id", Auth::user()->id)->first()->update(["comment" => $request->input("comment"), "rating" => $request->input("rating")]);
        $score = DB::select(DB::raw("select CAST(AVG(comments.rating) AS DECIMAL(10,2)) as score from comments join " . $type . "s on comments.commentable_id=" . $type . "s.id where " . $type . "s.name=:resourceName group by " . $type . "s.name"), array(
            'resourceName' => $resourceName,
        ))[0]->score;
        $resource->update(["score" => $score]);
        return redirect()->route("show", ["nombre" => Auth::user()->name, "type" => $type, "resourceName" => $resourceName]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteComment($nombre, $type, $resourceName)
    {
        switch ($type) {
            case "manga":
                $resource = Manga::Where($type . "s.name", $resourceName)->first();
                break;
            case "novel":
                $resource = Novel::Where($type . "s.name", $resourceName)->first();
                break;
            default:
                abort(404);
                break;
        }
        $resource->comments()->where("user_id", Auth::user()->id)->first()->delete();
        return redirect()->route("show", ["nombre" => Auth::user()->name, "type" => $type, "resourceName" => $resourceName]);
    }
}
