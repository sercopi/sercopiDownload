@if ($pages>=1)
<!--<label for="pageSelect">Go To:</label>
<select name="pageSelect" onchange="{{'window.location.href= this.value'}}">
    @foreach(range(1,$pages) as $page)
    <option value="{{$baseURL.$page}}" {{$currentPage==$page?'selected':''}}>
        {{$page}}
    </option>
    @endforeach
</select>-->
<nav class="mt-3" aria-label="Page navigation">
    <ul class="pagination justify-content-center">
        <li class="{{(($currentPage==1)?'page-item disabled':'page-item')}}">
            <a class="page-link" href="{{$baseURL.'1'}}" tabindex="-1">
                <<</a> </li> <li class="{{(($currentPage==1)?'page-item disabled':'page-item')}}">
                    <a class="page-link" href="{{$baseURL.($currentPage-1)}}" tabindex="-1">Previous</a>
        </li>
        @foreach (range($currentPage-5>0?$currentPage-5:1,$currentPage+5<$pages?$currentPage+5:$pages) as $page) <li
            class="{{(($currentPage==$page)?'page-item disabled':'page-item')}}"><a class="page-link"
                href={{$baseURL.$page}}>{{$page}}</a>
            </li>
            @endforeach
            <li class="{{(($currentPage==$pages)?'page-item disabled':'page-item')}}">
                <a class=" page-link" href={{$baseURL.($currentPage+1)}}>Next</a>
            </li>
            <li class="{{(($currentPage==$pages)?'page-item disabled':'page-item')}}">
                <a class=" page-link" href={{$baseURL.$pages}}>>></a>
            </li>
    </ul>
</nav>
@endif