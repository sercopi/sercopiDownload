<div id={{$id}} class="carousel slide" data-ride="carousel">
    <div class="carousel-inner">
        @for($i=0;$i<=count($fullBatch)-1;$i=$i+4) <div class="{{$i==0?'carousel-item  active':'carousel-item '}}">
            <div class="d-flex">
                @foreach($fullBatch->slice($i,4) as $batchSlice)
                <div class="col-3">
                    <a href={{URL::to("/user/".Auth::user()->name."/manga/".$batchSlice->name)}}>
                        <div class="card h-100 carousel-card">
                            <img src={{"data:image/png;base64,".$batchSlice->imageInfo}} class="card-img-top"
                                height="350px;" width="75px;" alt="image">
                            <div class="card-body">
                                <h5 class="card-title">
                                    {{str_replace("-"," ",\Illuminate\Support\Str::limit($batchSlice->name, $limit = 20, $end = '...'))}}
                                </h5>

                            </div>

                        </div>
                    </a>
                </div>
                @endforeach
            </div>
    </div>
    @endfor
    <a class="carousel-control-prev" href="{{'#'.$id}}" role="button" data-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
    </a>
    <a class="carousel-control-next" href="{{'#'.$id}}" role="button" data-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
    </a>
</div>
</div>