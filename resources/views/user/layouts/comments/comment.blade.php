<div class="row">
    <div class="col-2">
        <p>ID>>{{$comment->id}}</p>
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
                        <form class="delete-comment" id={{"delete-".$comment->id}}
                            action="{{URL::to("user/".Auth::user()->name."/comment/".$resourceType."/".$resourceName."/delete/".$comment->id)}}">
                            @method('POST')
                            @csrf
                            <button class="btn btn-link delete-button" class="close" data-id={{$comment->id}}
                                data-dismiss="modal" aria-label="Close" type="button">Yes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <a class="edit-comment"
            href={{URL::to("user/".Auth::user()->name."/comment/".$resourceType."/".$resourceName."/edit/".$comment->id)}}>edit</a>
        @else
        <p>{{$comment->user->name}}</p>
        @endif
        @if(!isset($parent))
        <a class="response-button"
            href={{URL::to("user/".Auth::user()->name."/comment/".$resourceType."/".$resourceName."/response/".($comment->id))}}>comment</a>
        @endif

    </div>
    <div class="col-4">
        <div data-link="{{URL::to('user/'.Auth::user()->name.'/comment/'.$comment->id)}}"
            class="d-inline-block comment-block">
            <button
                class="{{(!is_null($comment->likes()->where('user_id',Auth::user()->id)->first()) && $comment->likes()->where('user_id',Auth::user()->id)->first()->like>0)?'like d-inline btn btn-link bg-success text-black ':'like d-inline btn btn-link bg-white text-black'}}"><i
                    class="fas fa-angle-up"></i></button>
            <button
                class="{{(!is_null($comment->likes()->where('user_id',Auth::user()->id)->first()) && $comment->likes()->where('user_id',Auth::user()->id)->first()->like<0)?'dislike d-inline btn btn-link bg-danger text-black ':'dislike d-inline btn btn-link bg-white text-black'}}"><i
                    class="fas fa-angle-down"></i></button>
            <div class="d-inline totalLikes">Likes:
                <span class="likes-total">{{$comment->getLikes()}}</span>
            </div>
        </div>

        <br>

        Rating: {{$comment->rating}}

        <div>
            Comentario:
            <br>
            @if(isset($parent))
            <b><i>>>>{{$comment->commentable()->first()->user->name}}</i></b>
            @endif
            {{$comment->comment}}
        </div>
        <div>
            <p><i>created at: {{$comment->created_at}}</i></p>
        </div>
    </div>

</div>