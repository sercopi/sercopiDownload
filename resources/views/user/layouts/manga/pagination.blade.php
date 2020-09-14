<nav class="mt-3" aria-label="Page navigation">
    <ul class="pagination justify-content-center">
        <li class={{"page-item ".($currentPage===1?"disabled":"")}}>
            <a class="page-link"
                href="{{URL::to("/user/".Auth::user()->name."/search?seriesName=".$seriesName."&typeSelected=on&page=".($currentPage-1))}}"
                tabindex="-1">Previous</a>
        </li>
        @foreach (range(1,$pages) as $page)
        <li class={{"page-item".($currentPage===$page?"disabled":"")}}><a class="page-link"
                href={{URL::to("/user/".Auth::user()->name."/search?seriesName=".$seriesName."&typeSelected=on&page=".$page)}}>{{$page}}</a>
        </li>
        @endforeach
        <li class={{"page-item".($currentPage===$pages?"disabled":"")}}>
            <a class="page-link"
                href={{URL::to("/user/".Auth::user()->name."/search?seriesName=".$seriesName."&typeSelected=on&page=".($pages+1))}}>Next</a>
        </li>
    </ul>
</nav>