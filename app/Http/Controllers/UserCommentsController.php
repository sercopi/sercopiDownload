<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Comment;
use App\Manga;
use App\Novel;
use App\Like;
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

    public function responseComment($nombre, $resourceType, $resourceName, $id)
    {
        $data = $this->handleRequest("responseComment", false, $resourceType, $resourceName, $id);
        $comments = $data["comments"];
        $commented = $data["commented"];
        $responseComment = $data["userComment"];
        $view =  view("user.layouts.comments.comments", compact("resourceName", "comments", "commented", "resourceType", "responseComment"))->render();
        return json_encode($view);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveComment($nombre, $resourceType, $resourceName, Request $request)
    {
        $data = $this->handleRequest("save", $request, $resourceType, $resourceName);
        $comments = $data["comments"];
        $commented = $data["commented"];
        $view =  view("user.layouts.comments.comments", compact("resourceName", "comments", "commented", "resourceType"))->render();
        return json_encode($view);
    }

    public function saveResponseComment($nombre, $resourceType, $resourceName, $id, Request $request)
    {
        $data = $this->handleRequest("saveResponse", $request, $resourceType, $resourceName, $id);
        $comments = $data["comments"];
        $commented = $data["commented"];
        $view =  view("user.layouts.comments.comments", compact("resourceName", "comments", "commented", "resourceType"))->render();
        return json_encode($view);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showComment($nombre, $resourceType, $resourceName)
    {
        $data = $this->handleRequest("show", false, $resourceType, $resourceName);
        $comments = $data["comments"];
        $commented = $data["commented"];
        $view = view("user.layouts.comments.comments", compact("comments", "commented", "resourceType", "resourceName"))->render();
        return json_encode($view);
    }
    function handleRequest($method, $request = false, $resourceType, $resourceName, $id = false)
    {
        $resource = $this->getResource($resourceType, $resourceName);
        switch ($method) {
            case ("saveResponse"):
                $comment = Auth::user()->comments()->create(["comment" => $request->input("comment"), "rating" => $request->input("rating")]);
                Comment::where("id", $id)->first()->comments()->save($comment);
                break;
            case ("responseComment"):
                $userComment = comment::where("id", $id)->first();
                break;
            case ("show"):
                break;
            case ("delete"):
                Comment::where("user_id", Auth::user()->id)->where("id", $id)->first()->delete();
                break;
            case ("update"):
                //al hacer where user primero, nos aseguramos de que no se puedan borrar comentarios de otro usuario por id 
                $comment = Comment::where("user_id", Auth::user()->id)->where("id", $id)->first();
                $comment->update(["comment" => $request->input("comment"), "rating" => $request->input("rating")]);
                $score = DB::select(DB::raw("select CAST(AVG(comments.rating) AS DECIMAL(10,2)) as score from comments join " . $resourceType . "s on comments.commentable_id=" . $resourceType . "s.id where " . $resourceType . "s.name=:resourceName group by " . $resourceType . "s.name"), array(
                    'resourceName' => $resourceName,
                ))[0]->score;
                $comment->update(["score" => $score]);
                break;
            case ("edit"):
                $userComment = Comment::where("user_id", Auth::user()->id)->where("id", $id)->first();
                break;
            case ("save"):
                //Nota: he tenido que desmarcar como requeridos commentable type y comment_id para primero
                //asociar el usuario sin que esos dos campos esten y luego asociar los dos campos
                $comment = Auth::user()->comments()->create(["comment" => $request->input("comment"), "rating" => $request->input("rating")]);
                $resource->comments()->save($comment);
                $score = DB::select(DB::raw("select CAST(AVG(comments.rating) AS DECIMAL(10,2)) as score from comments join " . $resourceType . "s on comments.commentable_id=" . $resourceType . "s.id where " . $resourceType . "s.name=:resourceName group by " . $resourceType . "s.name"), array(
                    'resourceName' => $resourceName,
                ))[0]->score;
                $resource->update(["score" => $score]);
                break;
        }

        return [
            "resource" => $resource,
            "userComment" => ($method == "edit"  || $method == "responseComment") ? $userComment : null,
            "comments" => $resource->comments()->get(),
            "commented" => !is_null(Auth::user()->comments()->where("commentable_id", $resource->id)->first())
        ];
    }
    function getResource($resourceType, $resourceName, $id = false)
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
        return $resource;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editComment($nombre, $resourceType, $resourceName, $id)
    {
        $data = $this->handleRequest("edit", false, $resourceType, $resourceName, $id);
        $comments = $data["comments"];
        $commented = $data["commented"];
        $userComment = $data["userComment"];
        $view =  view("user.layouts.comments.comments", compact("resourceName", "comments", "commented", "resourceType", "userComment"))->render();
        return json_encode($view);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateComment($nombre, $resourceType, $resourceName, $id, Request $request)
    {
        $data = $this->handleRequest("update", $request, $resourceType, $resourceName, $id);
        $comments = $data["comments"];
        $commented = $data["commented"];
        $view =  view("user.layouts.comments.comments", compact("resourceName", "comments", "commented", "resourceType"))->render();
        return json_encode($view);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteComment($nombre, $resourceType, $resourceName, $id)
    {
        $data = $this->handleRequest("delete", false, $resourceType, $resourceName, $id);
        $comments = $data["comments"];
        $commented = $data["commented"];
        $view =  view("user.layouts.comments.comments", compact("resourceName", "comments", "commented", "resourceType"))->render();
        return json_encode($view);
    }
    public function like($nombre, $id, Request $request)
    {
        $like = Like::where("user_id", Auth::user()->id)->where("comment_id", $id)->first();
        is_null($like) ? Like::create(["user_id" => Auth::user()->id, "comment_id" => $id, "like" => 1]) : $like->update(["like" => $like->like > 0 ? 0 : 1]);
        return response("liked", 200);
    }
    public function dislike($nombre, $id, Request $request)
    {
        $like = Like::where("user_id", Auth::user()->id)->where("comment_id", $id)->first();
        is_null($like) ? Like::create(["user_id" => Auth::user()->id, "comment_id" => $id, "like" => -1]) : $like->update(["like" => $like->like < 0 ? 0 : -1]);
        return response("disliked", 200);
    }
}
