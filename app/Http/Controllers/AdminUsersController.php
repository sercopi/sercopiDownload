<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\User;
use App\Role;
use App\Foto;

class AdminUsersController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware("admin");
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $users =  User::all();
        return view("admin.users.index", compact(["users"]));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::all();
        return view("admin.users.create", compact("roles"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $entrada = $request->all();
        if ($archivo = $request->file("ruta_foto")) {
            $nombre = str_replace(" ", "_", $archivo->getClientOriginalName());
            $archivo->move("images", $nombre);
            $foto = Foto::create(["ruta_foto" => $nombre]);
            $entrada["foto_id"] = $foto->id;
        }
        $entrada["password"] = bcrypt($entrada["password"]);
        User::create($entrada);

        return redirect("admin/users");
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
        $user = User::findOrFail($id);
        return view("admin.users.edit", compact("user"));
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
        $entrada = $request->all();
        $user = User::findOrFail($id);
        $archivo = $request->file("ruta_foto");
        if ($archivo) {
            $nombre = str_replace(" ", "_", $archivo->getClientOriginalName());
            if ($user->foto && file_exists("images/" . $user->foto->ruta_foto)) unlink("images/" . $user->foto->ruta_foto);
            $archivo->move("images", $nombre);
            if ($user->foto) {
                $user->foto->update(["ruta_foto" => $nombre]);
            } else {
                $foto = Foto::create(["ruta_foto" => $nombre]);
                $user->foto_id = $foto->id;
            }
        }
        $user->update($entrada);
        return redirect("admin/users");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        Session::flash("borrar_usuario", "El Usuario Ha Sido Borrado");
        return redirect("admin/users");
    }
}
