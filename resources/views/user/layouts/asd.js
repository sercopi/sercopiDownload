ClassicEditor.create(document.querySelector("#editor"))
    .then(editor => {
        editor.model.document.on("change:data", editor => {
            console.log(editor.getData());
        });
    })
    .catch(error => {
        console.error(error);
    });
