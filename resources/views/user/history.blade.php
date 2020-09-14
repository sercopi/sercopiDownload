@extends("layouts.imports")
@section("cabecera")
@include("user.layouts.navbar")
@endsection
@section("contenido")
@include("user.layouts.manga.pagination",["currentPage"=>$page,"pages"=>$totalPages,"baseURL"=>URL::to("/user/".Auth::user()->name."/history?")])
<div class="container">
    <div class="row">
        <div class="col">
            <table class="table table-striped table-bordered table-sm versionTable" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th class="th-sm">DATE</th>
                        <th class="th-sm">RESOURCE</th>
                        <th class="th-sm">DOWNLOAD</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($historyPageResults as $result)
                    <tr>
                        <td>
                            {{$result->pivot->created_at}}
                        </td>
                        <td>
                            <a
                                href={{URL::to("/user/".Auth::user()->name."/manga/".$result->name)}}>{{$result->name}}</a>
                        </td>
                        <td>
                            @if(!is_null($result->pivot->download))
                            <ul>
                                @foreach($result->pivot->download as $version=>$chapters)
                                <li>
                                    {{$version}}
                                    <ul>
                                        @foreach($chapters as $chapterTitle=>$chapter)
                                        <li>
                                            <a href={{$chapter}}>{{$chapterTitle}}</a>
                                        </li>
                                        @endforeach
                                    </ul>
                                </li>
                                @endforeach
                            </ul>
                            @else
                            No Download
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@include("user.layouts.manga.pagination",["currentPage"=>$page,"pages"=>$totalPages,"baseURL"=>URL::to("/user/".Auth::user()->name."/history?")])

@endsection
@section("pie")
@endsection