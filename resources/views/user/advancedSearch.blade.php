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
                        @foreach(App\Genre::get() as $genre)
                        @if($genre->genre!=="all")
                        <button type="button" class="genre-button btn btn-light">{{$genre->genre}}</button>
                        @endif
                        @endforeach
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
                        <input type="number" name="selection[score][included][]" step="1" min=0 max=5>
                        <label for="score">Rating</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-4 mb-2 row justify-content-center">
            <div class="col-8 text-center">
                @include("user.layouts.orderByMenu")
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
            const form = new FormData(document.getElementById("advancedSearchForm"));
            const order =Array.from(document.getElementsByClassName("order")).filter((element)=>element.checked)[0].value;
            form.append("order",order)
            let response = await fetch(event.target.href?event.target.href:window.location.href, {
                method: 'POST',
                body: form,
            }).then((response)=>response.json()).then( (json)=>{
                document.getElementById("searchContent").innerHTML=json;
                Array.from(document.getElementsByClassName("page-link")).forEach((element)=>element.addEventListener("click",(event)=>{addSubmit(event)}));
                })};
        const genres = {included:[],excluded:[]};

        const genrebuttons = document.querySelectorAll(".genre-button");
        Array.from(genrebuttons).forEach((value)=>value.addEventListener("click",(event)=>manageGenres(event.target)))
        const manageGenres = (button) => {
            if (genres.included.includes(button.textContent)){
                genres.included=genres.included.filter((value)=>value!==button.textContent);
                genres.excluded.push(button.textContent);
                button.className="genre-button btn btn-danger";
                return true;
            }
            if (genres.excluded.includes(button.textContent)){
                genres.excluded=genres.excluded.filter((value)=>value!==button.textContent);
                button.className="genre-button btn btn-light";
                return true;
            }
            genres.included.push(button.textContent);
            button.className="genre-button btn btn-success";
            return true;
        }
        document.querySelector("#advancedSearchForm").addEventListener("submit",(event)=>addSubmit(event));

    </script>
</div>
@endsection