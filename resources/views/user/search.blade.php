@extends("layouts.imports")
@section("cabecera")
@include("user.layouts.navbar")
@endsection
@section("contenido")
<div class="container">
    <div id="accordion">
        <div class="card">
            <div class="card-header" id="headingOne">
                <h5 class="mb-2">
                    <button class="btn btn-primary resource-search-button" data-toggle="collapse"
                        data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        <span><i class="fas fa-arrow-circle-down"></i></span> Mangas
                    </button>
                </h5>
            </div>

            <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                <div class="card-body">
                    @if ($resources["mangas"]["resources"])
                    @include("user.layouts.pagination",["resourceType"=>"manga","seriesName"=>$seriesName,"totalPages"=>$resources["mangas"]["totalPages"],"currentPage"=>$resources["mangas"]["page"],"resources"=>$resources["mangas"]["resources"],"baseURL"=>URL::to("/user/".Auth::user()->name."/search?seriesName=".$seriesName."&pageManga=")])
                    @else
                    <p>NO HAY RESULTDOS</p>
                    @endif

                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header" id="headingTwo">
                <h5 class="mb-0">
                    <button class="btn btn-primary collapsed resource-search-button" data-toggle="collapse"
                        data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        <span><i class="fas fa-arrow-circle-up"></i></span> Novels
                    </button>
                </h5>
            </div>
            <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
                <div class="card-body">
                    @if ($resources["novels"]["resources"])
                    @include("user.layouts.pagination",["resourceType"=>"novel","seriesName"=>$seriesName,"totalPages"=>$resources["novels"]["totalPages"],"currentPage"=>$resources["novels"]["page"],"resources"=>$resources["novels"]["resources"],"baseURL"=>URL::to("/user/".Auth::user()->name."/search?seriesName=".$seriesName."&pageNovel=")])
                    @else
                    NO HAY RESULTADOS
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $($('.resource-search-button').click((event)=>$(event.currentTarget).find("span").html(!$(event.currentTarget).hasClass("collapsed")?'<i class="fas fa-arrow-circle-up">':'<i class="fas fa-arrow-circle-down">')));
</script>
@endsection