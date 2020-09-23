<div class="container d-flex flex-row">
    <div class="d-flex flex-row">
        <div class="starrating risingstar d-flex  flex-row-reverse">
            <input type="radio" class="radio-rating" id="star5" name="rating" value="5"
                {{(!is_null($userRating) && 5==round($userRating))?'checked':""}} /><label for="star5" title="5 star"><i
                    class="fas fa-star star"></i></label>
            <input type="radio" class="radio-rating" id="star4" name="rating" value="4"
                {{(!is_null($userRating) && 4==round($userRating))?'checked':""}} /><label for="star4" title="4 star"><i
                    class="fas fa-star star"></i></label>
            <input type="radio" class="radio-rating" id="star3" name="rating" value="3"
                {{(!is_null($userRating) && 3==round($userRating))?'checked':""}} /><label for="star3" title="3 star"><i
                    class="fas fa-star star"></i></label>
            <input type="radio" class="radio-rating" id="star2" name="rating" value="2"
                {{(!is_null($userRating) && 2==round($userRating))?'checked':""}} /><label for="star2" title="2 star"><i
                    class="fas fa-star star"></i></label>
            <input type="radio" class="radio-rating" id="star1" name="rating" value="1"
                {{(!is_null($userRating) && 1==round($userRating))?'checked':""}} /><label for="star1" title="1 star"><i
                    class="fas fa-star star"></i></label>
            <form id="rating-form">
                @csrf
            </form>
        </div>
    </div>
    <div class="ml-2">
        <h2><span id="score">{{$resource->score}}</span></h2>
    </div>
    <script>
        Array.from(document.getElementsByClassName("radio-rating")).forEach((element)=>{
            element.addEventListener("change",()=>{
                const value = Array.from(document.getElementsByClassName("radio-rating")).filter((input)=>input.checked)[0].value
                const formRating = new FormData(document.getElementById("rating-form"));
                formRating.append("rating",value);
                fetch({!! json_encode(URL::to("/user/".Auth::user()->name."/rate/".$resourceType."/".$resource->name))!!},{
                    method:"POST",
                    body:formRating
                }).then((json)=>json.json()).then((score)=>{
                    document.getElementById("score").innerHTML=parseFloat(score).toFixed(2);
                }).catch((error)=>console.log(error));
            })
        })
    </script>
</div>