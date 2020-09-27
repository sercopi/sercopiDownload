<div class="container">
  <div class="row justify-content-center">
    <div class="col">
      <nav class="navbar navbar-expand-lg navbar-light bg-light mb-5  mt-3 border">
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav mr-auto">
            <li class="nav-item nav-brand dropdown">
              <a class=" dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <img width="50px;" height="50px;"
                  src={{URL::to("images/".(Auth::user()->foto?Auth::user()->id."/".Auth::user()->foto->ruta_foto:"default.jpg"))}}>
              </a>
              <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                                document.getElementById('logout-form').submit();">
                  {{ __('Logout') }}
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                  @csrf
                </form>
                <a class="dropdown-item" href="#">Another action</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#">Something else here</a>
              </div>
            </li>

            <li class="nav-item">
              <a class="nav-link" href={{URL::to("/user/".Auth::user()->name.'/history')}}>History</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href={{URL::to("/")}}><span><i class="fas fa-home"></i></span></a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href={{URL::to("/user/".Auth::user()->name."/advancedSearch")}}>Advanced Search</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href={{URL::to("/user/".Auth::user()->name."/follows")}}>follows</a>
            </li>
          </ul>
          <form method="GET" action="{{URL::to('user/'.Auth::user()->name.'/search')}}" class="form-inline ">
            <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search"
              name="seriesName">
            <button class="btn btn-outline-success my-2 my-sm-0" type="submit"><i class="fas fa-search"></i>
              Search</button>
          </form>
        </div>
      </nav>
    </div>
  </div>
</div>