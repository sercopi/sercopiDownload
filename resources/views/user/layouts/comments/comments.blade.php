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
                    <!-- Button trigger modal -->
                    <button type="button" class="btn btn-link" data-toggle="modal" data-target="#exampleModalLong">
                        delete
                    </button>
                    <!-- Modal -->
                    <div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">

                                <div class="modal-body justify-content-center">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <div class="alert alert-danger alert-dismissible">
                                        <strong>Delete review?</strong>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-link" data-dismiss="modal">No</button>
                                    <form
                                        action="{{URL::to("user/".Auth::user()->name."/comment/".$resourceType."/".$resourceName."/delete")}}"
                                        method="POST">
                                        @method('DELETE')
                                        @csrf
                                        <button class="btn btn-link delete-button" type="submit">Yes</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <a
                        href={{URL::to("user/".Auth::user()->name."/comment/".$resourceType."/".$resourceName."/edit")}}>update</a>
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
            @if(!$commented || isset($userComment))
            <div class="row align-content-center">
                <div class="col-2">
                    <img width="100px" height="100px"
                        src={{URL::to("/images/".(Auth::user()->foto?Auth::user()->id."/".Auth::user()->foto->ruta_foto:"default.jpg"))}}>
                    <p>{{Auth::user()->name}}</p>
                </div>
                <div class="col-4">
                    {!!Form::open(["method"=>(isset($userComment)?"put":"post"),"url"=>URL::to("/user/".Auth::user()->name."/comment/".$resourceType."/".$resourceName.(isset($userComment)?"/update":"/save"))])!!}
                    {!!Form::label("comment","Texto:")!!}
                    {!!Form::textarea('comment',isset($userComment)?$userComment->comment:'', ["rows"=>4,"cols"=>30])
                    !!}
                    {!!Form::label("rating","Rating:")!!}
                    {!!Form::number("rating",isset($userComment)?$userComment->rating:'',["min"=>0,"max"=>10])!!}
                    {!!Form::submit("Enviar",["class"=>"btn btn-primary"])!!}
                    {!!Form::close()!!}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>