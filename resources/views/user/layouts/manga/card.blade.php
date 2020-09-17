<div class="col-3 card-group">
    <div class="card card-block mt-2 mb-2 card-border">
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
            <a href={{URL::to("/user/".Auth::user()->name."/manga/".$resource->name)}} class="btn btn-primary">Ir</a>
        </div>
    </div>
</div>