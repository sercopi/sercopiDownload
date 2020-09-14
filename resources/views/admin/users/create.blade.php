@extends("layouts.imports")
@section("contenido")
    <h1>Pagina para agregar usuarios</h1>
    {!!Form::open(["method"=>"POST","action"=>"AdminUsersController@store","files"=>true])!!}
    <table>
        <tr>
                <td>{!!Form::label("email","email")!!}</td>

               <td>{!!Form::text("email")!!}</td>
        </tr>
        <tr>
                <td>{!!Form::label("password","password")!!}</td>

               <td>{!!Form::password("password")!!}</td>
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
                    @foreach($roles as $role)
                    <option value='{{$role->id}}'>{{$role->nombre}}</option>
                    @endforeach
                </select>
            </td>
        </tr>
        <tr>
                <td>{!!Form::file("ruta_foto")!!}</td>

               <td>{!!Form::label("foto","foto")!!}</td>
        </tr>
        <tr>
                <td>{!!Form::submit("Crear Usuario")!!}</td>

               <td>{!!Form::reset("Reset")!!}</td>
        </tr>
        
    </table>
    
    {!!Form::close()!!}

@endsection