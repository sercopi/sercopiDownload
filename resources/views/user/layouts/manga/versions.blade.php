<form method="POST" action="{{URL::to('user/'.Auth::user()->name.'/download')}}" class="form my-2 my-lg-0 container">
  {{csrf_field()}}
  <input type="hidden" name="resourceName" value={{$resourceName}}>
  @foreach($versions as $versionName=>$version)
  <div class="row justify-content-center mt-3 mb-3">
    <div class="col">
      <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="{{'#'.$versionName}}"
        aria-expanded="false" aria-controls="{{$versionName}}">{{$versionName}}</button>
    </div>
  </div>
  <div class="row  multi-collapse" id="{{$versionName}}">
    <div class="col card">
      <div class="card-body p-2">
        @include("user.layouts.manga.versionTable",["chapters"=>$versions[$versionName]["chapters"],"versionName"=>$versionName])
      </div>
    </div>
  </div>

  @endforeach
  <div class="row justify-content-center mt-3 mb-3">
    <div class="col-3"><button class="btn btn-outline-success my-2 my-sm-0" type="submit">Download Selection <i
          class="fa fa-download" aria-hidden="true"></i>
      </button></div>
  </div>

</form>