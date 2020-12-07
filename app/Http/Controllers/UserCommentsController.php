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
    /*
        Function that resolves each type interaction with the comments
        It is used inside each function that is called for each interaction from the client
        It was necessary because in each interaction, the first and the last pieces of code were heavily repeated
    */
    function handleRequest($method, $request = false, $resourceType, $resourceName, $id = false)
    {
        //gets the resource using the function getResource
        $resource = $this->getResource($resourceType, $resourceName);
        //depending on the type of interaction, an action is taken
        switch ($method) {
                //this interaction is called when the user wants to save an answer to another comment 
            case ("saveResponse"):
                //the entry for the new comment is created
                $comment = Auth::user()->comments()->create(["comment" => $request->input("comment")]);
                //it is associated with its resource, in this case, a comment
                Comment::where("id", $id)->first()->comments()->save($comment);
                break;
                //this is called when the user wants to answer a comment, so the proper
                //sets the userComment variable, the comment that is going to be answered and that will be needed
                //later for the saveResponse to associate the comment
            case ("responseComment"):
                $userComment = comment::where("id", $id)->first();
                break;
                //will return the normal view, so it does nothing, it is set out of consistency
            case ("show"):
                break;
                //deletes the desired comment, returns nothing
            case ("delete"):
                Comment::where("user_id", Auth::user()->id)->where("id", $id)->first()->delete();
                break;
                //called when an user modifies a comment
            case ("update"):
                //al hacer where user primero, nos aseguramos de que no se puedan borrar comentarios de otro usuario por id 
                //the comment to update is selected
                $comment = Comment::where("user_id", Auth::user()->id)->where("id", $id)->first();
                //the comment is updated
                $comment->update(["comment" => $request->input("comment")]);
                break;
                //called to when an user requests to modify a comment
            case ("edit"):
                //sets the variable userComment, the comment that is going to be used later by the update, to update 
                $userComment = Comment::where("user_id", Auth::user()->id)->where("id", $id)->first();
                break;
                //called when the user wants to create a simple, new comment
            case ("save"):
                //Nota: he tenido que desmarcar como requeridos commentable type y comment_id para primero
                //asociar el usuario sin que esos dos campos estén y luego asociar los dos campos
                //the comment is created and saved
                $comment = Auth::user()->comments()->create(["comment" => $request->input("comment")]);
                $resource->comments()->save($comment);
                break;
        }
        //returns the partial view of the comments
        return [
            "resource" => $resource,
            "userComment" => ($method == "edit"  || $method == "responseComment") ? $userComment : null,
            "comments" => $resource->comments()->get(),
            "commented" => !is_null(Auth::user()->comments()->where("commentable_id", $resource->id)->first())
        ];
    }
    /*

    */
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

    /*
        Like and dislike functions, they set a like/dislike for a comment, or update an already given rating
    */
    public function like($nombre, $id, Request $request)
    {
        //selects the like that the user issued for the comment 
        $like = Like::where("user_id", Auth::user()->id)->where("comment_id", $id)->first();
        //if there was none, it is created with a 1 on the like column, meaning a positive remark
        //if it was already liked, then sets the like column to 0, for a neutral remark
        is_null($like) ? Like::create(["user_id" => Auth::user()->id, "comment_id" => $id, "like" => 1]) : $like->update(["like" => $like->like > 0 ? 0 : 1]);
        return response("liked", 200);
    }
    public function dislike($nombre, $id, Request $request)
    {
        //selects the like that the user issed for the comment
        $like = Like::where("user_id", Auth::user()->id)->where("comment_id", $id)->first();
        //if there was none, it is created with a -1 on the like column, meaning a negative remark
        //if it was already disliked, then sets the like column to 0, for a neutral remark
        is_null($like) ? Like::create(["user_id" => Auth::user()->id, "comment_id" => $id, "like" => -1]) : $like->update(["like" => $like->like < 0 ? 0 : -1]);
        return response("disliked", 200);
    }
}
