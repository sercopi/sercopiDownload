<nav class="mt-3" aria-label="Page navigation">
    <ul class="pagination justify-content-center">
        <li class={{(($currentPage==1)?"page-item disabled":"page-item")}}>
            <a class="page-link" href="{{$baseURL."&page=".($currentPage-1)}}" tabindex="-1">Previous</a>
        </li>
        @foreach (range(1,$pages) as $page)
        <li class={{(($currentPage==$page)?"page-item disabled":"page-item")}}><a class="page-link"
                href={{$baseURL."&page=".$page}}>{{$page}}</a>
        </li>
        @endforeach
        <li class={{(($currentPage==$pages)?"page-item disabled":"page-item")}}>
            <a class="page-link" href={{$baseURL."&page=".($pages+1)}}>Next</a>
        </li>
    </ul>
</nav>