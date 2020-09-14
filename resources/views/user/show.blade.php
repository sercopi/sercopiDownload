@extends("layouts.imports")
@section("cabecera")
@include("user.layouts.navbar")
@endsection
@section("contenido")
<div class="container">
    <div class="row">
        <div class="col">
            @if($resource)
            <h1 class="text-info">{{str_replace("-"," ",$resource->name)}}</h1>
            <h2>Rating: {{$resource->score}}</h2>
            <div class="row">
                <div class="col-5">
                    <img alt="image" width="400px" height="600px" src={{"data:image/png;base64,".$resource->imageInfo}}>
                </div>
                <div class="col-5">
                    <p>{{$resource->synopsis}}</p>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-12">
                    @include("user.layouts.manga.versions",["versions"=>json_decode($resource->chapters,true),"resourceName"=>$resource->name])
                </div>
            </div>
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