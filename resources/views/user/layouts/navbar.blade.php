<div class="container">
  <div class="row justify-content-center">
    <div class="col">
      <nav class="navbar navbar-expand-lg navbar-light bg-light mb-5  mt-3 border">
        <div class="collapse navbar-collapse" id="navbarSupportedContent">

          <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
              <a class="navbar-brand" href="#">{{Auth::user()->name}}</a>
            </li>
            <li class="nav-item active">
              <a class="nav-link" href={{URL::to("user/".Auth::user()->name.'/history')}}>History</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#">Link</a>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                Dropdown
              </a>
              <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                <a class="dropdown-item" href="#">Action</a>
                <a class="dropdown-item" href="#">Another action</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#">Something else here</a>
              </div>
            </li>
            <li class="nav-item">
              <a class="nav-link disabled" href="#">Disabled</a>
            </li>
          </ul>
          <form method="GET" action="{{URL::to('user/'.Auth::user()->name.'/search')}}" class="form-inline ">
            {{csrf_field()}}

            <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search"
              name="seriesName">
            <div class="custom-control custom-switch">
              @if(Session::get("isManga"))
              <input type="checkbox" name="typeSelected" class="custom-control-input" id="type" checked>
              @else
              <input type="checkbox" name="typeSelected" class="custom-control-input" id="type">
              @endif
              <label class="custom-control-label" id="labelForType"
                for="type">{{Session::get("isManga")?"Manga":"Novel"}}</label>
              <script>
                $('#type').click((event) => event.target.checked?$('#labelForType').text("Manga"):$('#labelForType').text("Novel"));
                      console.log("se ejecutan el segundo");
              </script>
            </div>
            <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
          </form>
        </div>
      </nav>
    </div>
  </div>
</div>