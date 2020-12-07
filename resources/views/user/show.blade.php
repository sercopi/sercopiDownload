@extends("layouts.imports")
@section("cabecera")
@include("user.layouts.navbar")
@endsection
@section("contenido")
<script src="https://cdn.ckeditor.com/ckeditor5/22.0.0/classic/ckeditor.js"></script>

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
    @if ($resourceType=="novel")
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
        let ckeditor;
        /*
            Everytime the comments section is updated, the domModified function is run (also when the page is loaded too)
            to add SPA functionality to the comments section links
        */
        const domModified = (json) => {
            document.getElementsByClassName("comments-container")[0].innerHTML="";
            
              document.getElementsByClassName("comments-container")[0].innerHTML=json;
            //--------set up ckeditor
            data = document.getElementsByClassName("comments-container")[0];
            const scriptContainer = document.createElement("div");
            Array.from(data.getElementsByTagName("script")).forEach((script)=>{
                const newScript = document.createElement("script");
                newScript.innerHTML=script.innerHTML;
                scriptContainer.appendChild(newScript);
            })
            data.appendChild(scriptContainer);

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
              
              domModified(json);
              window.scrollTo(0, document.documentElement.scrollTop || document.body.scrollTop)
            })
            .catch(error => console.log(error));
                })
            })
            //-----delete functions
            Array.from(document.getElementsByClassName("delete-button")).forEach((element)=>{
                const form = document.getElementById("delete-"+element.dataset.id);
                element.addEventListener("click",(event)=>{
                           
                    fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form)
                    })
                    .then((response)=>response.json())
                    .then(json => {
                

                    domModified(json);
                    })
                    .catch(error => console.log(error));
                })
            })
            //-----------update & save functions
            const form = document.getElementsByClassName("form-comment");
                form[0].addEventListener("submit",(event)=>{
                event.preventDefault();
                const formData= new FormData(event.target)
                formData.append("comment",ckeditor.getData())
                const action = event.target.action;
                fetch(event.target.action, {
                    method: "POST",
                    body: formData
                })
                .then((response)=>response.json())
                .then(json => {

                        domModified(json);
                })
                .catch(error => console.log(error));
            })
            
            //----------response links functions
            Array.from(document.getElementsByClassName("response-button")).forEach((element)=>{
            element.addEventListener("click",(event)=>{
                    event.preventDefault();
                    const link = event.target.href;
                    fetch(link)
            .then(response => response.json())
            .then(json => {
               

              domModified(json);
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
           

              domModified(json);
            })
            .catch(error => console.log(error));
            };
            
    </script>
</div>
</div>
</div>
@endsection