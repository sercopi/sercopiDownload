@extends("layouts.imports")
@section("cabecera")
@include("user.layouts.navbar")

@endsection
@section("contenido")
<div class="container">
    <div class="row">
        <div class="col ml-3 mb-2 mt-2">
            <h1>Most Popular</h1>
        </div>
    </div>
    <div class="row">
        <div class="col">
            @include("user.layouts.carousel",["fullBatch"=>$twentyMostPopular,"id"=>"mostPopular"])
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col ml-3 mb-2 mt-2">
            <h1>Best Rated</h1>
        </div>
    </div>
    <div class="row">
        <div class="col">
            @include("user.layouts.carousel",["fullBatch"=>$twentyMostPopular,"id"=>"bestRated"])
        </div>
    </div>
    <hr>
    <div class="row pb-3">
        <h3>Last Updates on your Follows list:</h3>
        <hr>
    </div>
    <div class="row">
        @include("user.layouts.followsUpdates",["recentUpdates"=>$recentUpdates])
    </div>
    <hr>
    <div class="row mt-3">
        <div class="col ml-3 mb-2 mt-2">
            <h1>Last Added</h1>
        </div>
    </div>
    <div class="row">
        <div class="col">
            @include("user.layouts.carousel",["fullBatch"=>$twentyLastAdded,"id"=>"lastAdded"])
        </div>
    </div>

</div>

@endsection
@section("pie")
@endsection