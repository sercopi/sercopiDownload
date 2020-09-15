<div class="container">
    <div clas="row align-content-center">
        <div class="col">
            @foreach($comments as $comment)
            <div class="row">
                <div class="col-2">
                    <img width="100px" height="100px"
                        src={{URL::to("/images/".($comment->user->foto?$comment->user->id."/".$comment->user->foto->ruta_foto:"default.jpg"))}}>
                    @if($comment->user->name===Auth::user()->name)
                    <p><b>{{$comment->user->name}}</b></p>
                    @else
                    <p>{{$comment->user->name}}</p>
                    @endif
                </div>
                <div class="col-4">
                    Rating: {{$comment->rating}}
                    Comentario:
                    <div>
                        {{$comment->comment}}
                    </div>
                </div>
            </div>
            @endforeach
            @if(!$commented)
            <div class="row align-content-center">
                <div class="col-2">
                    <img width="100px" height="100px"
                        src={{URL::to("/images/".(Auth::user()->foto?Auth::user()->id."/".Auth::user()->foto->ruta_foto:"default.jpg"))}}>
                    <p>{{Auth::user()->name}}</p>
                </div>
                <div class="col-4">
                    {!!Form::open(["url"=>URL::to("/user/".Auth::user()->name."/comment")])!!}
                    {!!Form::label("comment","Texto:")!!}
                    {!! Form::textarea('comment', null, ["rows"=>4,"cols"=>30]) !!}
                    {!!Form::label("rating","Rating:")!!}
                    {!!Form::number("rating","",["min"=>0,"max"=>10])!!}
                    {!!Form::hidden("resourceName",$resourceName)!!}
                    {!!Form::submit("Enviar",["class"=>"btn btn-primary"])!!}
                    {!!Form::close()!!}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>