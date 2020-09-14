@extends("layouts.imports")
@section("contenido")
    <h1>Pagina para agregar usuarios</h1>
    {!!Form::model($user,["method"=>"PATCH","action"=>["AdminUsersController@update",$user->id],"files"=>true])!!}
    <table>
        <tr>
                <td>{!!Form::label("email","email")!!}</td>

               <td>{!!Form::text("email")!!}</td>
        </tr>
        <tr>
                <td>{!!Form::label("name","name")!!}</td>

               <td>{!!Form::text("name")!!}</td>
        </tr>
        <tr>
            <td>
                    {!!Form::label("role","role")!!}
            </td>
            <td>
                <select name="role_id">
                    @foreach(\App\Role::all() as $role)
                    <option value='{{$role->id}}'>{{$role->nombre}}</option>
                    @endforeach
                </select>
            </td>
        </tr>
        <tr>

                <td>
                        <img src={{$user->foto?URL::to("images/".$user->foto->ruta_foto):'juegosinfantiles.bosquedefantasias.com/wp-content/uploads/2019/11/signos-de-interrogacion-y-exclamacion.jpg'}} width="150px"></td>

                   <td> {!!Form::file("ruta_foto")!!}
                </td>

        </tr>
        <tr>
                <td>{!!Form::submit("Modificar Usuario")!!}</td>

               <td>{!!Form::reset("Reset")!!}</td>
        </tr>
    </table>
    {!!Form::close()!!}
    {!!Form::model($user,["method"=>"DELETE","action"=>["AdminUsersController@destroy",$user->id]])!!}
    {!!Form::submit("Eliminar Usuario")!!}
    {!!Form::close()!!}
@endsection