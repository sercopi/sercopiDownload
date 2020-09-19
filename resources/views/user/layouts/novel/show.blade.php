@extends("layouts.imports")
@section("cabecera")
@include("user.layouts.navbar")
@endsection
@section("contenido")
<div class="container">
    @if($resource)
    <div class="row">
        <div class="col">
            <h1 class="text-info">{{str_replace("-"," ",$resource->name)}}</h1>
        </div>
    </div>
    <h2>Rating: {{$resource->score}}</h2>
    <div class="row">
        <div class="col-5">
            <img alt="image" width="400px" height="600px" src={{"data:image/png;base64,".$resource->imageInfo}}>
        </div>
        <div class="col-5 ">
            <ul class="list">
                <li class="list-group-item list-background p-4 align-middle"><span>Author(s): </span>
                    {{$resource->author}}</li>
                <li class="list-group-item list-background p-4 align-middle"><span>Alternative Title(s): </span>
                    {{$resource->alternativeTitle}}</li>
                <li class="list-group-item list-background p-4 align-middle"><span>Artist(s): </span>
                    {{$resource->artist}}</li>
                <li class="list-group-item list-background p-4 align-middle"><span>Genre(s): </span>
                    {{$resource->genre}}</li>
                <li class="list-group-item list-background p-4 align-middle"><span>Status: </span> {{$resource->status}}
                </li>
                <li class="list-group-item list-background p-4 align-middle"><span>Last Added/Modified:
                    </span>{{$resource->updated_at?$resource->updated_at:$resource->created_at}}</li>
            </ul>
        </div>
    </div>
    <div class="row mt-2 mb-2">
        <div class="col">
            <h2>Synopsis</h2>
            <p>{{$resource->synopsis}}</p>
        </div>
    </div>
    <hr>
    <div class="row justify-content-center">
        <div class="col">
            @include("user.layouts.novel.versions",["resource"=>$resource])
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col">
            @include("user.layouts.comments.comments",["comments"=>$commentsFound,"resourceName"=>$resource->name,"commented"=>$commented])
        </div>
    </div>
    @else
    <div class="row">
        <div class="col">
            NO HAY RESULTADOS
        </div>
    </div>
    @endif
</div>
</div>
</div>
@endsection