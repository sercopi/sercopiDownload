<table class="table table-striped table-bordered table-sm versionTable" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th class="th-sm">{{$versionName}}</th>
            <th class="th-sm">Chapters</th>
        </tr>
    </thead>
    <tbody>
        @foreach($chapters as $chapter=>$chapterURL)
        <tr>
            <!--<div class="form-check">-->
            <td><input class="form-check-input m-0" name="{{"selection[".$versionName."][".$chapter."]"}}"
                    type="checkbox" value="{{$chapter}}" id="{{$versionName."-".$chapter}}"></td>
            <td>
                <label class="form-check-label m-0" for="{{$versionName."-".$chapter}}">
                    <a href="{{$chapterURL}}">{{$versionName."-".$chapter}}<a>
                </label>
            </td>
            <!--</div>-->
        </tr>
        @endforeach
    </tbody>
</table>