@extends("layouts.imports")
@section("cabecera")
@include("user.layouts.navbar")
@endsection
@section("contenido")
<div class="container">
    @if (Session::has("error"))
    <div class="alert alert-danger" role="alert">
        {{Session::get("error")}}
    </div>
    <hr>
    @endif
    @if (Session::has("success"))
    <div class="alert alert-success" role="alert">
        {{Session::get("success")}}
    </div>
    <hr>
    @endif
    <div class="row">
        <div class="col">
            <h1 class="text-info">{{str_replace("-"," ",$resource->name)}}</h1>
        </div>
    </div>
    @if ($resourceType=="novel") {
    @include("user.layouts.novel.show",["resource"=>$resource])
    @else
    @include("user.layouts.manga.show",["resource"=>$resource])
    @endif
    <hr>
    <div class="row">
        <div class="col comments-container">
        </div>
    </div>
    <script>
        const domModified = () => {
            //--------------Likes Functions
            Array.from(document.getElementsByClassName("comment-block")).forEach(
                element => {
                    const link = element.dataset.link;
                    const likeButton = element.getElementsByClassName("like")[0];
                    const dislikeButton = element.getElementsByClassName(
                        "dislike"
                    )[0];
                    const totalLikePosition = element.getElementsByClassName(
                        "likes-total"
                    )[0];
                    let totalLikes = Number(totalLikePosition.textContent);
            
                    likeButton.addEventListener("click", event => {
                        const likeChecked = likeButton.className.includes(
                            "bg-success"
                        );
                        const dislikeChecked = dislikeButton.className.includes(
                            "bg-danger"
                        );
                        if (likeChecked) {
                            likeButton.className =
                                "like d-inline btn btn-link bg-white text-black";
                            totalLikes = totalLikes - 1;
                        } else {
                            likeButton.className =
                                "like d-inline btn btn-link bg-success text-black";
                            totalLikes = totalLikes + 1;
                            if (dislikeChecked) {
                                dislikeButton.className =
                                    "dislike d-inline btn btn-link bg-white text-black";
                                totalLikes = totalLikes + 1;
                            }
                        }
                        totalLikePosition.innerHTML = totalLikes;
            
                        fetch(link + "/like")
                            .then(()=>console.log(link))
                            .catch(error => console.log(error));
                    });
                    dislikeButton.addEventListener("click", event => {
                        const likeChecked = likeButton.className.includes(
                            "bg-success"
                        );
                        const dislikeChecked = dislikeButton.className.includes(
                            "bg-danger"
                        );
                        if (dislikeChecked) {
                            dislikeButton.className =
                                "dislike d-inline btn btn-link bg-white text-black";
                            totalLikes = totalLikes + 1;
                        } else {
                            dislikeButton.className =
                                "dislike d-inline btn btn-link bg-danger text-black";
                            totalLikes = totalLikes - 1;
                            if (likeChecked) {
                                likeButton.className =
                                    "like d-inline btn btn-link bg-white text-black";
                                totalLikes = totalLikes - 1;
                            }
                        }
                        totalLikePosition.innerHTML = totalLikes;
            
                        fetch(link + "/dislike")
                            .then()
                            .catch(error => console.log(error));
                    });
                }
            );
            //------edit functions
            Array.from(document.getElementsByClassName("edit-comment")).forEach((element)=>{
                element.addEventListener("click",(event)=>{
                    event.preventDefault();
                    const link = event.target.href;
                    fetch(link)
            .then(response => response.json())
            .then(json => {
                /* const observer = new MutationObserver(domModified);
            
                // define what element should be observed by the observer
                // and what types of mutations trigger the callback
                observer.observe(document.getElementsByClassName("comments-container")[0].parentNode, {
                    subtree: true,
                    attributes: true
                    //...
                });*/
                document.getElementsByClassName("comments-container")[0].innerHTML="";
            
              document.getElementsByClassName("comments-container")[0].innerHTML=json;
              domModified();
              window.scrollTo(0, document.documentElement.scrollTop || document.body.scrollTop)
            })
            .catch(error => console.log(error));
                })
            })
            //-----delete functions
            Array.from(document.getElementsByClassName("delete-button")).forEach((element)=>{
                element.addEventListener("click",(event)=>{
                    const form = document.getElementById("delete-"+event.target.dataset.id);       
                    fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form)
                    })
                    .then((response)=>response.json())
                    .then(json => {
                /* const observer = new MutationObserver(domModified);
            
                // define what element should be observed by the observer
                // and what types of mutations trigger the callback
                observer.observe(document.getElementsByClassName("comments-container")[0].parentNode, {
                    subtree: true,
                    attributes: true
                    //...
                });*/
                    document.getElementsByClassName("comments-container")[0].innerHTML="";
            
                    document.getElementsByClassName("comments-container")[0].innerHTML=json;
                    domModified();
                    })
                    .catch(error => console.log(error));
                })
            })
            //-----------update & save functions, si no existe el form, porque no se puede comentar
            const form = document.getElementsByClassName("form-comment");
            if (form[0] !== undefined) {
                form[0].addEventListener("submit",(event)=>{
                event.preventDefault();
                const action = event.target.action;
                fetch(event.target.action, {
                    method: "POST",
                    body: new FormData(event.target)
                })
                .then((response)=>response.json())
                .then(json => {
                        document.getElementsByClassName("comments-container")[0].innerHTML="";
            
                        document.getElementsByClassName("comments-container")[0].innerHTML=json;
                        domModified();
                })
                .catch(error => console.log(error));
            })
            }
            //----------response links functions
            Array.from(document.getElementsByClassName("response-button")).forEach((element)=>{
            element.addEventListener("click",(event)=>{
                    event.preventDefault();
                    const link = event.target.href;
                    fetch(link)
            .then(response => response.json())
            .then(json => {
                /* const observer = new MutationObserver(domModified);
            
                // define what element should be observed by the observer
                // and what types of mutations trigger the callback
                observer.observe(document.getElementsByClassName("comments-container")[0].parentNode, {
                    subtree: true,
                    attributes: true
                    //...
                });*/
                document.getElementsByClassName("comments-container")[0].innerHTML="";
            
              document.getElementsByClassName("comments-container")[0].innerHTML=json;
              domModified();
              window.scrollTo(0, document.documentElement.scrollTop || document.body.scrollTop)
            })
            .catch(error => console.log(error));
                })
            })
            
        };
            window.onload = function() {
            
            
            fetch({!! json_encode(URL::to("/user/".Auth::user()->name."/comment/".$resourceType."/".$resource->name."/show"))!!})
            .then(response => response.json())
            .then(json => {
            /*             const observer = new MutationObserver(domModified);
            
                // define what element should be observed by the observer
                // and what types of mutations trigger the callback
                observer.observe(document.getElementsByClassName("comments-container")[0].parentNode, {
                    subtree: true,
                    attributes: true
                    //...
                });*/
                document.getElementsByClassName("comments-container")[0].innerHTML="";
            
              document.getElementsByClassName("comments-container")[0].innerHTML=json;
              domModified();
            })
            .catch(error => console.log(error));
            };
            
    </script>
</div>
</div>
</div>
@endsection