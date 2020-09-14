<form method="POST" action="{{URL::to('user/'.Auth::user()->name.'/download')}}"
  class="form-inline my-2 my-lg-0 container">
  {{csrf_field()}}
  <div class="row justify-content-center">

    @foreach($versions as $versionName=>$version)
    <div class="col">
      <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="{{'#'.$versionName}}"
        aria-expanded="false" aria-controls="{{$versionName}}">{{$versionName}}</button>
    </div>
    @endforeach
  </div>
  <input type="hidden" name="resourceName" value={{$resourceName}}>
  @foreach($versions as $versionName=>$version)
  <div class="row  multi-collapse" id="{{$versionName}}">
    <div class="col card card-body">
      @include("user.layouts.manga.versionTable",["chapters"=>$versions[$versionName]["chapters"],"versionName"=>$versionName])
    </div>
  </div>

  @endforeach
  <div class="row justify-content-center">
    <div class="col-3"><button class="btn btn-outline-success my-2 my-sm-0" type="submit">download</button></div>
  </div>

</form>