<form method="POST" action="{{URL::to('user/'.Auth::user()->name.'/download/novel/'.$resource->name)}}"
  class="form my-2 my-lg-0 container" id="form-versions">
  {{csrf_field()}}
  <input type="hidden" name="resourceName" value={{$resource->name}}>
  <div class="row justify-content-center mt-3 mb-3">
    <div class="col">
      <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="{{'#'.$resource->name}}"
        aria-expanded="false" aria-controls="{{$resource->name}}">light novel world</button>
    </div>
  </div>
  <div class="row  multi-collapse" id="{{$resource->name}}">
    <div class="col card">
      <div class="card-body p-2">
        @include("user.layouts.novel.versionTable",["resourceName"=>$resource->name,"chapters"=>$resource->novel_chapters])
      </div>
    </div>
  </div>

  <div class="row justify-content-center mt-3 mb-3">
    <div class="col-3"><button class="btn btn-outline-success my-2 my-sm-0" type="submit">Download Selection <i
          class="fa fa-download" aria-hidden="true"></i>
      </button></div>
  </div>

</form>