@extends("layouts.imports")
@section("contenido")
@if(Session::has("borrar_usuario"))
<p class="bg-danger">{{session("borrar_usuario")}}</p>
@endif
<h1>Pagina de administrador principal</h1>
@if($users)
<table class="table">
    <thead>
        <tr>
            @foreach(Schema::getColumnListing("users") as $campo)
            @switch($campo)
            @case ($campo==="role_id")
            <th>role</th>
            @break
            @case($campo==="foto_id")
            <th>foto</th>
            @break
            @default
            <th>{{$campo}}</th>
            @endswitch

            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($users as $user)
        <tr>
            @foreach(Schema::getColumnListing("users") as $campo)
            @switch($campo)
            @case ($campo==="role_id")
            <td>{{$user->role?$user->role->nombre:"NO DATA"}}</td>
            @break
            @case($campo==="foto_id")
            <td><a href={{URL::to("/admin/users/".$user->id."/edit")}}><img
                        src={{$user->foto?URL::to("/images/".$user->foto->ruta_foto):URL::to("/images/default.jpg")}}
                        width="150px"></a></td>
            @break
            @default
            <td> {{$user->$campo}}</td>
            @endswitch
            @endforeach
        </tr>
        @endforeach
    </tbody>
</table>
@else
"No hay usuarios"
@endif
@endsection