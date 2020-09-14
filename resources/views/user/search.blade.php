@extends("layouts.imports")
@section("cabecera")
@include("user.layouts.navbar")
@endsection
@section("contenido")
<div class="container">
    @if($resources)
    <div class="row">
        @foreach($resources as $resource)
        <div class="col-3 card-group">
            <div class="card card-block" style="width: 18rem;">
                <img class="card-img-top" src={{"data:image/png;base64,".$resource->imageInfo}} alt="Card image cap"
                    height="400px;" width="75px;">
                <div class="card-body">
                    <h5 class="card-title"><b>{{str_replace("-"," ",$resource->name)}}</b></h5>
                    <div class="collapse multi-collapse" id={{"sinopsis-".$resource->name}}>
                        <p class="card-text">{{$resource->synopsis}}</p>
                    </div>
                    <button class="btn btn-primary" type="button" data-toggle="collapse"
                        data-target={{"#sinopsis-".$resource->name}} aria-expanded="false"
                        aria-controls={{"sinopsis-".$resource->name}}>Show Synopsis</button>
                    <a href={{URL::to("/user/".Auth::user()->name."/manga/".$resource->name)}}
                        class="btn btn-primary">Ir</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="row">
        <div class="col">
            NO HAY RESULTADOS
        </div>
    </div>
    @endif
    @endsection