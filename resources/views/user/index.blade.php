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

    <div class="row pb-3">
        <h3>Last Updates on your Follows list:</h3>
        <hr>
    </div>
    <div class="row">
        <div class="col" id="updates-container">
        </div>
    </div>
    <script>
        window.onload=()=>{
            fetch({!!json_encode(URL::to("user/".Auth::user()->name."/followsUpdates"))!!}).then((json)=>json.json()).then((json)=>{
                prepareFollowsUpdates(json)
            }).catch()
        }
        const prepareFollowsUpdates = (json)=>{
            document.getElementById("updates-container").innerHTML="";
            document.getElementById("updates-container").innerHTML=json;
            Array.from(document.getElementsByClassName("page-link")).forEach((link)=>{
                link.addEventListener("click",()=>{
                    fetch(link.href).then((json)=>json.json()).then((json)=>{
                        prepareFollowsUpdates(json);
            }).catch()
                })
            })
        }
    </script>
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