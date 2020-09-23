<div class="container">
    <div clas="row align-content-center">
        <div class="col">

            @include("user.layouts.comments.commentsFeed",["comments"=>$comments])


            <div class="row align-content-center">
                <div class="col-2">
                    <img width="100px" height="100px"
                        src={{URL::to("/images/".(Auth::user()->foto?Auth::user()->id."/".Auth::user()->foto->ruta_foto:"default.jpg"))}}>
                    <p>{{Auth::user()->name}}</p>
                </div>
                <div class="col-4">
                    @if(isset($responseComment))
                    <p>Response to: {{$responseComment->id}}</p>
                    @endif
                    @if(isset($userComment))
                    <p>Modify: {{$userComment->id}}</p>
                    @endif
                    {!!Form::open(["id"=>(isset($userComment)?"update-form":(isset($responseComment)?"response-form":"save-form")),"class"=>"form-comment","method"=>"post","url"=>URL::to("/user/".Auth::user()->name."/comment/".$resourceType."/".$resourceName.(isset($userComment)?"/update/".$userComment->id:(isset($responseComment)?"/saveResponse/".$responseComment->id:"/save")))])!!}
                    {!!Form::label("comment","Texto:")!!}
                    {!!Form::textarea('comment',(isset($userComment)?$userComment->comment:(isset($responseComment)?'':'')),
                    ["rows"=>4,"cols"=>30])
                    !!}
                    {{--                     {!!Form::label("rating","Rating:")!!}
                    {!!Form::number("rating",isset($userComment)?$userComment->rating:'',["min"=>0,"max"=>10])!!} --}}
                    {!!Form::submit("Enviar",["class"=>"btn btn-primary"])!!}
                    {!!Form::close()!!}
                </div>
            </div>

        </div>
    </div>
</div>