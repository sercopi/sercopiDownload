<div class="container">
    @if($resources)
    @include("user.layouts.paginationMenu",["baseURL"=>$baseURL,"pages"=>$totalPages,"currentPage"=>$currentPage])
    <div class="row">
        @foreach($resources as $resource)
        @include("user.layouts.card",["resource"=>$resource,"resourceType"=>$resourceType])
        @endforeach
    </div>
    @include("user.layouts.paginationMenu",["baseURL"=>$baseURL,"pages"=>$totalPages,"currentPage"=>$currentPage])
    @else
    <div class="row">
        <div class="col">
            NO HAY RESULTADOS
        </div>
    </div>
    @endif
</div>