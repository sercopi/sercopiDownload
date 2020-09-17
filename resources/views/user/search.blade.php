@extends("layouts.imports")
@section("cabecera")
@include("user.layouts.navbar")
@endsection
@section("contenido")
<div class="container">
    @include("user.layouts.manga.pagination",["baseURL"=>URL::to("/user/".Auth::user()->name."/search?seriesName=".$seriesName."&typeSelected=on"),"pages"=>$pageNumbersTotal,"currentPage"=>$page])

    @if($resources)
    <div class="row">
        @foreach($resources as $resource)
        @include("user.layouts.manga.card",["resource"=>$resource])
        @endforeach
    </div>
    @else
    <div class="row">
        <div class="col">
            NO HAY RESULTADOS
        </div>
    </div>
    @endif
</div>
@include("user.layouts.manga.pagination",["baseURL"=>URL::to("/user/".Auth::user()->name."/search?seriesName=".$seriesName."&typeSelected=on"),"pages"=>$pageNumbersTotal,"currentPage"=>$page])
@endsection