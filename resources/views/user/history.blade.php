@extends("layouts.imports")
@section("cabecera")
@include("user.layouts.navbar")
@endsection
@section("contenido")
<div class="container">
    <div class="row">
        <div class="col">
            @include("user.layouts.paginationMenu",["currentPage"=>$page,"pages"=>$totalPages,"baseURL"=>URL::to("/user/".Auth::user()->name."/history?page=")])

            <table class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th class="th-sm">DATE</th>
                        <th class="th-sm">RESOURCE TYPE</th>
                        <th class="th-sm">RESOURCE NAME</th>
                        <th class="th-sm">DOWNLOAD</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pageResults as $index=>$result)
                    <tr>
                        <td>
                            {{$result->created_at}}
                        </td>
                        <td>
                            {{$result->resourceType}}
                        </td>
                        <td>
                            @if ($result->resourceType=="manga")
                            <a
                                href={{URL::to("/user/".Auth::user()->name."/manga/".$result->nombre)}}>{{$result->nombre}}</a>
                            @else
                            <a
                                href={{URL::to("/user/".Auth::user()->name."/novel/".$result->nombre)}}>{{$result->nombre}}</a>
                            @endif
                        </td>
                        <td>
                            @if(!is_null($result->download) && $result->resourceType=="manga")
                            <ul>
                                @foreach($result->download as $version=>$chapters)
                                <ul>
                                    <div class="panel-group" role="tablist">
                                        <div class="panel panel-default">
                                            <div class="panel-heading" role="tab"
                                                id={{"collapseListGroupHeading".$index.$version}}>
                                                <div class="panel-title">
                                                    <a class="{{count($result->download)>10?'collapsed':''}}"
                                                        data-toggle="collapse"
                                                        href={{"#collapseListGroup".$index.$version}}
                                                        aria-expanded="false"
                                                        aria-controls={{"collapseListGroup".$index.$version}}>
                                                        {{$version}}
                                                    </a>
                                                </div>
                                            </div>
                                            <div id={{"collapseListGroup".$index.$version}}
                                                class="panel-collapse collapse" role="tabpanel"
                                                aria-labelledby={{"collapseListGroupHeading".$index.$version}}>
                                                <ul class="list-group">
                                                    @foreach($chapters as $chapterTitle=>$chapter)
                                                    <li>
                                                        <a href={{$chapter}}>{{$chapterTitle}}</a>
                                                    </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </ul>

                                @endforeach
                            </ul>
                            @elseif (!is_null($result->download) && $result->resourceType=="novel")
                            <ul>
                                <div class="panel-group" role="tablist">
                                    <div class="panel panel-default">
                                        <div class="panel-heading" role="tab" id={{"collapseListGroupHeading".$index}}>
                                            <div class="panel-title">
                                                <a class="{{count($result->download)>10?'collapsed':''}}"
                                                    data-toggle="collapse" href={{"#collapseListGroup".$index}}
                                                    aria-expanded="false" aria-controls={{"collapseListGroup".$index}}>
                                                    LightNovelWorld
                                                </a>
                                            </div>
                                        </div>
                                        <div id={{"collapseListGroup".$index}} class="panel-collapse collapse"
                                            role="tabpanel" aria-labelledby={{"collapseListGroupHeading".$index}}>
                                            <ul class="list-group">
                                                @foreach($result->download as $number)
                                                <li class="list-group-item">
                                                    <a
                                                        href="{{'https://www.lightnovelworld.com'}}">{{"chapter: ".($number+1)}}</a>
                                                </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                            </ul>
                            @else
                            No Download
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @include("user.layouts.paginationMenu",["currentPage"=>$page,"pages"=>$totalPages,"baseURL"=>URL::to("/user/".Auth::user()->name."/history?page=")])

        </div>
    </div>
</div>

@endsection
@section("pie")
@endsection