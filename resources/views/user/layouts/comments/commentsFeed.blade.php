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
        <a href={{URL::to("user/".Auth::user()->name."/comment/".$resourceType."/".$resourceName."/edit")}}>update</a>
        @else
        <p>{{$comment->user->name}}</p>
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
            {{$comment->comment}}
        </div>
    </div>
</div>
<script>
    Array.from(document.getElementsByClassName("comment-block")).forEach(
        element => {
            const link = element.dataset.link;
            const likeButton = element.getElementsByClassName("like")[0];
            const dislikeButton = element.getElementsByClassName("dislike")[0];
            const totalLikePosition = element.getElementsByClassName("likes-total")[0];
            let totalLikes = Number(totalLikePosition.textContent);
    
            likeButton.addEventListener("click", event => {
                const likeChecked = likeButton.className.includes("bg-success");
                const dislikeChecked=dislikeButton.className.includes("bg-danger");
                if (likeChecked) {
                    likeButton.className='like d-inline btn btn-link bg-white text-black';
                    totalLikes=totalLikes-1;
                } else {
                    likeButton.className='like d-inline btn btn-link bg-success text-black';
                    totalLikes=totalLikes+1;
                    if (dislikeChecked) {
                        dislikeButton.className='dislike d-inline btn btn-link bg-white text-black';
                        totalLikes=totalLikes+1;
                    }
                }
                totalLikePosition.innerHTML=totalLikes;

                fetch(link+"/like")
                    .then()
                    .catch(error => console.log(error));
            });
            dislikeButton.addEventListener("click", event => {
                const likeChecked = likeButton.className.includes("bg-success");
                const dislikeChecked=dislikeButton.className.includes("bg-danger");
                if (dislikeChecked) {
                    dislikeButton.className='dislike d-inline btn btn-link bg-white text-black';
                    totalLikes=totalLikes+1;
                } else {
                    dislikeButton.className='dislike d-inline btn btn-link bg-danger text-black';
                    totalLikes=totalLikes-1;
                    if (likeChecked) {
                        likeButton.className='like d-inline btn btn-link bg-white text-black';
                        totalLikes=totalLikes-1;
                    }
                }
                totalLikePosition.innerHTML=totalLikes;

                fetch(link+"/dislike")
                    .then()
                    .catch(error => console.log(error));
            });
        }
    );
                            
</script>
@endforeach