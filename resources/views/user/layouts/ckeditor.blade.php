<div class="m-3 h-100" id="display">{!!(isset($userComment)?$userComment->comment:"")!!}</div>
<script>
    ClassicEditor.defaultConfig = {
    height:'250px',
      toolbar: {
        items: [
          'heading',
          '|',
          'bold',
          'italic',
          '|',
          'bulletedList',
          'numberedList',
          '|',
          'link',
          '|',
          'insertTable',
          '|',
          'undo',
          'redo'
        ]
      },
      table: {
        contentToolbar: [ 'tableColumn', 'tableRow', 'mergeTableCells' ]
      },
      language: 'es'
    };
    ClassicEditor
    .create( document.querySelector( '#display' ), {
        filebrowserUploadUrl: "{{route('upload', ['nombre'=>Auth::user()->name,'_token' => csrf_token() ])}}",
    filebrowserUploadMethod: 'form'}).then((editor)=>{
        ckeditor=editor;
    })
    .catch(error => {
        console.error(error);
    }); 
//custom, shouldnt need it with laravel
 /*        class MyUploadAdapter {
    constructor( loader ) {
        // CKEditor 5's FileLoader instance.
        this.loader = loader;

        // URL where to send files.
        this.url = {!!json_encode(URL::to("user/".Auth::user()->name."/testPOST"))!!};
    }

    // Starts the upload process.
    upload() {
        return new Promise( ( resolve, reject ) => {
            this._initRequest();
            this._initListeners( resolve, reject );
            this._sendRequest();
        } );
    }

    // Aborts the upload process.
    abort() {
        if ( this.xhr ) {
            this.xhr.abort();
        }
    }

    // Example implementation using XMLHttpRequest.
    _initRequest() {
        const xhr = this.xhr = new XMLHttpRequest();

        xhr.open( 'POST', this.url, true );
        console.log("opened")
        xhr.responseType = 'json';
    }

    // Initializes XMLHttpRequest listeners.
    _initListeners( resolve, reject ) {
        const xhr = this.xhr;
        const loader = this.loader;
        const genericErrorText = 'Couldn\'t upload file:' + ` ${ loader.file.name }.`;

        xhr.addEventListener( 'error', () => reject( genericErrorText ) );
        xhr.addEventListener( 'abort', () => reject() );
        xhr.addEventListener( 'load', () => {
            const response = xhr.response;

            if ( !response || response.error ) {
                return reject( response && response.error ? response.error.message : genericErrorText );
            }

            // If the upload is successful, resolve the upload promise with an object containing
            // at least the "default" URL, pointing to the image on the server.
            resolve( {
                default: response.url
            } );
        } );

        if ( xhr.upload ) {
            xhr.upload.addEventListener( 'progress', evt => {
                if ( evt.lengthComputable ) {
                    loader.uploadTotal = evt.total;
                    loader.uploaded = evt.loaded;
                }
            } );
        }
    }

    // Prepares the data and sends the request.
    _sendRequest() {
        const data = new FormData();

        data.append( 'upload', this.loader.file );

        this.xhr.send( data );
    }
}

function MyCustomUploadAdapterPlugin( editor ) {
    editor.plugins.get( 'FileRepository' ).createUploadAdapter = ( loader ) => {
        return new MyUploadAdapter( loader );
    };
}


ClassicEditor
    .create( document.querySelector( '#editor' ), {
        extraPlugins: [ MyCustomUploadAdapterPlugin ],}).then((editor)=>{
      
        document.getElementById("getdata").addEventListener("click",()=>{
            document.getElementById("display").innerHMTL="";
            document.getElementById("display").innerHMTL=editor.getData();
            const container = document.createElement("div");
            container.innerHTML=editor.getData();
            document.getElementById("display").appendChild(container)
            console.log(editor.getData())

        })
    })
    .catch(error => {
        console.error(error);
    }); */

</script>