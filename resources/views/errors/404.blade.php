@extends("layouts.imports")
@section("cabecera")
@endsection

@section("contenido")
<div class="container">
    <div class="row d-flex justify-content-center">
        <div class="col d-flex justify-content-center">
            <img src={{URL::to("/images/404.jpg")}} alt="404 error">
        </div>
    </div>
</div>

@endsection
@section("pie")
@endsection