@extends("layouts.imports")
@section("cabecera")
@include("user.layouts.navbar")
@endsection
@section("contenido")
<div class="container">
    <div class="row">
        <div class="col" id="follows-container">

        </div>
    </div>
</div>
<script>
    window.onload = ()=>{
        
        fetch ({!!json_encode(URL::to("/user/".Auth::user()->name."/followFeed"))!!}).then((json)=>json.json()).then((json)=>{
            prepareContent(json);
        }).catch()
    }
    const prepareContent=(json)=>{
        const container = document.createElement("div");
        document.getElementById("follows-container").innerHTML="";
            container.innerHTML=json;
            (container);
            const scriptContainer = document.createElement("div");
            Array.from(container.getElementsByTagName("script")).forEach((script)=>{
                const newScript = document.createElement("script");
                newScript.innerHTML=script.innerHTML;
                scriptContainer.appendChild(newScript);
            })
            container.appendChild(scriptContainer);
            document.getElementById("follows-container").appendChild(container);
            Array.from(document.getElementsByClassName("page-link")).forEach((link)=>{
                link.addEventListener("click",(event)=>{
                    event.preventDefault();
                    fetch(link.href).then((json)=>json.json()).then((json)=>prepareContent(json)).catch()
                })
            })
    }
    
</script>

@endsection
@section("pie")
@endsection