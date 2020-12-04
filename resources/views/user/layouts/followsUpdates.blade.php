<div class="row">
    <div class="col">
        @include("user.layouts.paginationMenu",["baseURL"=>$baseURL,"pages"=>$totalPages,"currentPage"=>$currentPage])
    </div>
</div>
<div class="row mb-2 mt-2">
    @foreach($recentUpdates as $resource)
    <div class="col-4">
        <div class="card">
            <span class="notify-badge">{{$resource->resourceType}}</span>
            <img class="card-img-top" height="200px" src="{{"data:image/jpeg;base64, ".$resource->imageInfo}}"
                alt=" Card image">
            <div class="card-header " id={{"heading".$resource->name.$resource->resourceType}}>
                <a hred>{{str_replace("-"," ",$resource->name)}}</a>
                <footer class="blockquote-footer"><cite title="time">{{$resource->created_at}}</cite>
                </footer>
                <a href="{{URL::to("/user/".Auth::user()->name."/".$resource->resourceType."/".$resource->name)}}"
                    class="btn btn-primary">Go!</a>
                <button class="btn btn-link collapsed" data-toggle="collapse"
                    data-target={{"#collapse".$resource->name.$resource->resourceType}} aria-expanded="false"
                    aria-controls={{"collapse".$resource->name.$resource->resourceType}}>
                    show chapters
                </button>
            </div>

            <div id={{"collapse".$resource->name.$resource->resourceType}} class="collapsed show collapse"
                aria-labelledby={{"heading".$resource->name.$resource->resourceType}}>
                <div class="card-body">
                    @if($resource->resourceType =="manga")
                    <ul>
                        @foreach(json_decode($resource->chapters_introduced,true) as $version=>$versionInfo)
                        <li>
                            {{$version}}
                            <ul>
                                @foreach($versionInfo["chapters"] as $title=>$chapter)
                                <li><a href="{{$chapter}}">{{$title}}</a></li>
                                @break
                                @endforeach
                            </ul>
                        </li>
                        @endforeach
                    </ul>
                    @else
                    <ul>
                        @foreach(json_decode($resource->chapters_introduced) as $chapter)
                        <li>chapter: {{$chapter}}</li>
                        @endforeach
                    </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
<div class="row">
    <div class="col">
        @include("user.layouts.paginationMenu",["baseURL"=>$baseURL,"pages"=>$totalPages,"currentPage"=>$currentPage])
    </div>
</div>
<hr>