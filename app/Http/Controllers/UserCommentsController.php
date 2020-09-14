<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Comment;
use App\Manga;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


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
    public function store(Request $request)
    {
        //incluir logica para preguntar si es novela o manga
        //Nota: he tenido que desmarcar como requeridos commentable type y comment_id para primero
        //asociar el usuario sin que esos dos campos esten y luego asociar los dos campos
        $comment = Auth::user()->comments()->create(["comment" => $request->input("comment"), "rating" => $request->input("rating")]);
        $manga = Manga::where("name", $request->input("resourceName"))->first();
        $manga->comments()->save($comment);
        $score = DB::select(DB::raw("select CAST(AVG(comments.rating) AS DECIMAL(10,2)) as score from comments join mangas on comments.commentable_id=mangas.id where mangas.name=:resourceName group by mangas.name"), array(
            'resourceName' => $request->input("resourceName"),
        ))[0]->score;
        $manga->update(["score" => $score]);
        return redirect()->action("UserController@showManga", ["nombre" => Auth::user()->name, "typeSelected" => "on", "mangaName" => $request->input("resourceName")]);
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
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
