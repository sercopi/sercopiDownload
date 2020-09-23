@foreach($comments as $comment)
@include("user.layouts.comments.comment",["comment"=>$comment])
<div class="row">
    <div class="col d-flex justify-content-center">
        <div class="w-100" id={{"accordion".$comment->id}}>
            <div class="card bg-transparent">
                <div class="header d-flex justify-content-center" id={{$comment->id}}>
                    <h5 class="mb-0 d-inline-center">
                        <button class="btn btn-link collapsed" data-toggle="collapse"
                            data-target={{"#collapse".$comment->id}} aria-expanded="true"
                            aria-controls={{"collapse".$comment->id}}>
                            show comments
                        </button>
                    </h5>
                </div>

                <div id={{"collapse".$comment->id}} class="collapse show bg-transparent"
                    aria-labelledby={{$comment->id}} data-parent={{"#accordion".$comment->id}}>
                    <div class="bg-transparent ml-5">
                        @foreach($comment->comments()->get() as $response)
                        @include("user.layouts.comments.comment",["comment"=>$response,"parent"=>$comment])
                        <hr>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<hr>
@endforeach