@extends("layouts.imports")
@section("cabecera")
@endsection
@section("contenido")
<div class="container">
    <div class="row">
        <div class="col">
            <img src={{URL::to("/images/404.webp")}} alt="404 error">
        </div>
    </div>
</div>

@endsection
@section("pie")
@endsection