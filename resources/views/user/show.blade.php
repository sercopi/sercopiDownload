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
    @if ($resourceType=="novel") {
    @include("user.layouts.novel.show",["resource"=>$resource])
    @else
    @include("user.layouts.manga.show",["resource"=>$resource])
    @endif
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