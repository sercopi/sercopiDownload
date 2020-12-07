<div class="container">
    <div clas="row align-content-center">
        <div class="col">

            @include("user.layouts.comments.commentsFeed",["comments"=>$comments])


            <div class="row align-content-center " style="height:400px;">
                <div class="col-2 h-100">
                    <img width="100px" height="100px"
                        src={{URL::to("/images/".(Auth::user()->foto?Auth::user()->id."/".Auth::user()->foto->ruta_foto:"default.jpg"))}}>
                    <p>{{Auth::user()->name}}</p>
                </div>
                <div class="col-7 h-100">
                    @if(isset($responseComment))
                    <p>Response to: {{$responseComment->id}}</p>
                    @endif
                    @if(isset($userComment))
                    <p>Modify: {{$userComment->id}}</p>
                    @endif
                    <form method="post"
                        id={{(isset($userComment)?"update-form":(isset($responseComment)?"response-form":"save-form"))}}
                        class="form-comment h-100"
                        action={{URL::to("/user/".Auth::user()->name."/comment/".$resourceType."/".$resourceName.(isset($userComment)?"/update/".$userComment->id:(isset($responseComment)?"/saveResponse/".$responseComment->id:"/save")))}}>
                        @csrf
                        @include("user.layouts.ckeditor")
                        <button type="submit" class="btn btn-primary m-4">send</button>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>