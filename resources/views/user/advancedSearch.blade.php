@extends("layouts.imports")
@section("cabecera")
@include("user.layouts.navbar")
@endsection
@section("contenido")
<div class="container">
    <form method="POST" id="advancedSearchForm" action={{URL::to("/user/".Auth::user()->name."/advancedSearch")}}>
        {{ csrf_field() }}
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header" id="headingOne">
                        <h5 class="mb-2">
                            Name
                        </h5>
                    </div>
                    <div class="card-body">
                        <input type="text" name="selection[name]">
                        <label for="author">Name</label>
                    </div>

                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header" id="headingOne">
                        <h5 class="mb-2">
                            Genres
                        </h5>
                    </div>
                    <div class="card-body">
                        <button type="button" class="genre-button btn btn-light">Action</button>
                        <button type="button" class="genre-button btn btn-light">Adventure</button>
                    </div>

                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header" id="headingOne">
                        <h5 class="mb-2">
                            Others
                        </h5>
                    </div>
                    <div class="card-body">
                        <input type="text" name="selection[author][included][]">
                        <label for="author">Author</label>
                        <hr>
                        <select name="selection[status][included][]">
                            <option value="" selected></option>

                            <option value="ongoing">ongoing</option>
                            <option value="completed">completed</option>
                            <option value="hiatus">hiatus</option>
                        </select>
                        <label for="status">Status</label>

                        <hr>
                        <input type="number" name="selection[score][included][]" step="0.1" min=0 max=10>
                        <label for="score">Score</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-4 mb-2 row justify-content-center">
            <div class="col-8 text-center">
                <input type="submit" class="btn btn-primary" value="SEARCH">
            </div>
        </div>
    </form>
    <div class="row">
        <div class="col" id="searchContent">
            {{-- @if($resources)
            @include()
            @else
            NO HAY RESULTADOS
            @endif --}}
        </div>
    </div>
    <script>
        const addSubmit = async (event)=>{
            event.preventDefault();
            Array.from(document.getElementsByClassName("hidden")).forEach((input)=>input.remove());
            genres.included.forEach((value)=>{
                const input = document.createElement("input");
                input.type="hidden";
                input.value=value;
                input.className="hidden";
                input.name="selection[genre][included][]";
                event.target.appendChild(input);
            });
            genres.excluded.forEach((value)=>{
                const input = document.createElement("input");
                input.type="hidden";
                input.value=value;
                input.className="hidden";
                input.name="selection[genre][excluded][]";
                event.target.appendChild(input);

            });
            let response = await fetch(event.target.href?event.target.href:window.location.href, {
                method: 'POST',
                body: new FormData(document.getElementById("advancedSearchForm"))
            }).then((response)=>response.json()).then( (json)=>{
                document.getElementById("searchContent").innerHTML=json;
                Array.from(document.getElementsByClassName("page-link")).forEach((element)=>element.addEventListener("click",(event)=>{addSubmit(event)}));
                console.log("default prevenido en links");
                })};
        const genres = {included:[],excluded:[]};

        const genrebuttons = document.querySelectorAll(".genre-button");
        Array.from(genrebuttons).forEach((value)=>value.addEventListener("click",(event)=>manageGenres(event.target)))
        const manageGenres = (button) => {
            if (genres.included.includes(button.textContent)){
                genres.included=genres.included.filter((value)=>value!==button.textContent);
                genres.excluded.push(button.textContent);
                button.className="genre-button btn btn-danger";
                console.log(genres);
                return true;
            }
            if (genres.excluded.includes(button.textContent)){
                genres.excluded=genres.excluded.filter((value)=>value!==button.textContent);
                button.className="genre-button btn btn-light";
                console.log(genres);
                return true;
            }
            genres.included.push(button.textContent);
            button.className="genre-button btn btn-success";
            console.log(genres);
            return true;
        }
        document.querySelector("#advancedSearchForm").addEventListener("submit",(event)=>addSubmit(event));

    </script>
</div>
@endsection