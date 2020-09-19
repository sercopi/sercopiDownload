<table class="table table-striped table-bordered table-sm versionTable" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th class="th-sm"><input type="checkbox" value="1" class="select-all"></th>
            <th class="th-sm">chapter</th>
            <th class="th-sm">title</th>
        </tr>
    </thead>
    <tbody>
        @foreach($chapters as $chapter)
        <tr>
            <!--<div class="form-check">-->
            <td><input class="form-check-input m-0" name="{{"selection[]"}}" type="checkbox"
                    value="{{$chapter->number}}" id={{$chapter->id}}></td>
            <td>{{$chapter->number+1}}</td>
            <td>
                <label class="form-check-label m-0" for="{{$chapter->id}}">
                    <a
                        href="{{'https://www.lightnovelworld.com/novel/'.$resourceName.'/chapter-'.($chapter->number-1)}}">{{$chapter->title}}<a>
                </label>
            </td>
            <!--</div>-->
        </tr>
        @endforeach
    </tbody>
</table>