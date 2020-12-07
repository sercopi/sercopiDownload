<div class="container d-flex flex-row">
    <div class="d-flex flex-row">
        <div class="starrating risingstar d-flex  flex-row-reverse">
            <input type="radio" class={{"radio-rating".$resource->name}} id={{"star5".$resource->name}} name="rating"
                value="5"
                {{(!is_null($resource->ratings()->where("user_id", Auth::user()->id)->first()) && 5==round($resource->ratings()->where("user_id", Auth::user()->id)->first()->rating))?'checked':""}} /><label
                for={{"star5".$resource->name}} title="5 star"><i class="fas fa-star star"></i></label>
            <input type="radio" class={{"radio-rating".$resource->name}} id={{"star4".$resource->name}} name="rating"
                value="4"
                {{(!is_null($resource->ratings()->where("user_id", Auth::user()->id)->first()) && 4==round($resource->ratings()->where("user_id", Auth::user()->id)->first()->rating))?'checked':""}} /><label
                for={{"star4".$resource->name}} title="4 star"><i class="fas fa-star star"></i></label>
            <input type="radio" class={{"radio-rating".$resource->name}} id={{"star3".$resource->name}} name="rating"
                value="3"
                {{(!is_null($resource->ratings()->where("user_id", Auth::user()->id)->first()) && 3==round($resource->ratings()->where("user_id", Auth::user()->id)->first()->rating))?'checked':""}} /><label
                for={{"star3".$resource->name}} title="3 star"><i class="fas fa-star star"></i></label>
            <input type="radio" class={{"radio-rating".$resource->name}} id={{"star2".$resource->name}} name="rating"
                value="2"
                {{(!is_null($resource->ratings()->where("user_id", Auth::user()->id)->first()) && 2==round($resource->ratings()->where("user_id", Auth::user()->id)->first()->rating))?'checked':""}} /><label
                for={{"star2".$resource->name}} title="2 star"><i class="fas fa-star star"></i></label>
            <input type="radio" class={{"radio-rating".$resource->name}} id={{"star1".$resource->name}} name="rating"
                value="1"
                {{(!is_null($resource->ratings()->where("user_id", Auth::user()->id)->first()) && 1==round($resource->ratings()->where("user_id", Auth::user()->id)->first()->rating))?'checked':""}} /><label
                for={{"star1".$resource->name}} title="1 star"><i class="fas fa-star star"></i></label>
            <form id={{"rating-form".$resource->name}}>
                @csrf
            </form>
        </div>
    </div>
    <div class="ml-2">
        <h2><span id={{"score".$resource->name}}>{{$resource->score?$resource->score:0.00}}</span></h2>
    </div>
    <script>
        Array.from(document.getElementsByClassName({!!json_encode("radio-rating".$resource->name)!!})).forEach((element)=>{
                element.addEventListener("change",()=>{
                    const value = Array.from(document.getElementsByClassName({!!json_encode("radio-rating".$resource->name)!!})).filter((input)=>input.checked)[0].value
                    const formRating = new FormData(document.getElementById({!!json_encode("rating-form".$resource->name)!!}));
                    formRating.append("rating",value);
                    fetch({!! json_encode(URL::to("/user/".Auth::user()->name."/rate/".$resourceType."/".$resource->name))!!},{
                        method:"POST",
                        body:formRating
                    }).then((json)=>json.json()).then((score)=>{
                        document.getElementById({!!json_encode("score".$resource->name)!!}).innerHTML=parseFloat(score).toFixed(2);
                    }).catch((error)=>console.log(error));
                })
            })
    </script>
</div>


<div class="container d-flex flex-row">
    <div class="row d-flex flex-row ml-2">
        <form id={{"follow-form".$resource->name}}>
            @csrf
            <button
                class="{{(!is_null($resource->follows()->where("user_id",Auth::user()->id)->first()) && $resource->follows()->where("user_id",Auth::user()->id)->first()->follow)?'btn btn-primary':'btn btn-secondary'}}"
                id={{"follow-check".$resource->name}} name="follow"
                data-toggle="toggle">{{(!is_null($resource->follows()->where("user_id",Auth::user()->id)->first()) && $resource->follows()->where("user_id",Auth::user()->id)->first()->follow)?"following":"not following"}}</button>
            <label for="notifications">Notifications: </label>
            <button
                class="{{(!is_null($resource->follows()->where("user_id",Auth::user()->id)->first()) && $resource->follows()->where("user_id",Auth::user()->id)->first()->notifications)?'btn btn-primary':'btn btn-secondary'}}"
                id={{"notifications-check".$resource->name}} name="notifications"
                data-toggle="toggle">{{(!is_null($resource->follows()->where("user_id",Auth::user()->id)->first()) && $resource->follows()->where("user_id",Auth::user()->id)->first()->notifications)?"On":"Off"}}</button>
        </form>
        <script>
            document.getElementById({!!json_encode("follow-check".$resource->name)!!}).addEventListener("click",(event)=>{
                    event.preventDefault();
                    const form = new FormData(document.getElementById({!!json_encode("follow-form".$resource->name)!!}));
                    form.append("follow","follow");
                    const following = event.target.innerHTML=="following";
                            event.target.className=following?"btn btn-secondary":"btn btn-primary";
                            event.target.innerHTML=following?"not following":"following";
                            const notifications = document.getElementById({!!json_encode("notifications-check".$resource->name)!!});
                            notifications.className="btn btn-secondary";
                            notifications.innerHTML="Off";
                      
                    fetch({!! json_encode(URL::to("/user/".Auth::user()->name."/follow/".$resourceType."/".$resource->name))!!},{
                        method:"POST",
                        body:form,
                    }).then().catch((error)=>console.log(error))
                })
                document.getElementById({!!json_encode("notifications-check".$resource->name)!!}).addEventListener("click",(event)=>{
                    event.preventDefault();
                    const form = new FormData(document.getElementById({!!json_encode("follow-form".$resource->name)!!}));
                    form.append("notifications","notifications");
                    const notificationChecked= event.target.innerHTML=="On";
                    event.target.className=notificationChecked?"btn btn-secondary":"btn btn-primary";
                    event.target.innerHTML=notificationChecked?"Off":"On";
                    const follow = document.getElementById({!!json_encode("follow-check".$resource->name)!!});
                    follow.className=notificationChecked?follow.className:"btn btn-primary";
                    follow.innerHTML=notificationChecked?follow.innerHTML:"following";
                    fetch({!! json_encode(URL::to("/user/".Auth::user()->name."/follow/".$resourceType."/".$resource->name))!!},{
                        method:"POST",
                        body:form,
                    }).then().catch((error)=>console.log(error))
                })
        </script>
    </div>
</div>