<div class="container">
    @include("user.layouts.paginationMenu",["baseURL"=>$baseURL,"pages"=>$totalPages,"currentPage"=>$currentPage])
    <div class="row">
        <div class="col">
            @foreach($resources as $resource)

            <div class="card mt-2 mb-2">
                <div class="card-body">
                    <div class="row">

                        <div class="col-3 card-img">
                            <a href={{URL::to("user/".Auth::user()->name."/".$resourceType."/".$resource->name)}}>

                                <img class="card-img w-100 h-100"
                                    src="{{"data:image/jpeg;base64, ".$resource->imageInfo}}" alt="Card image cap">
                            </a>
                        </div>

                        <div class="col-9">
                            <a href={{URL::to("user/".Auth::user()->name."/".$resourceType."/".$resource->name)}}>

                                <h4 class="card-title">
                                    {{str_replace("-"," ",$resource->name)}}
                                </h4>
                            </a>
                            <h5>
                                Last Updated: {{$resource->updated_at}}
                            </h5>
                            @include("user.layouts.starRating",["resource"=>$resourceType=="manga"?App\Manga::where("name",$resource->name)->first():App\Novel::where("name",$resource->name)->first(),"resourceType"=>$resourceType])
                        </div>
                    </div>
                </div>
            </div>

            @endforeach
        </div>

    </div>
    @include("user.layouts.paginationMenu",["baseURL"=>$baseURL,"pages"=>$totalPages,"currentPage"=>$currentPage])

</div>