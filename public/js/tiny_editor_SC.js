// Content
const initEditor = function () {
    tinymce.init({
        selector: ".tinymce",
        height: 500,
        min_height: 200,
        max_height: 750,
        resize: true,
        language: 'fr_FR',
        content_css: [
            '/css/style.css',
            '/css/custom.css',
        ],
        image_class_list: [
            {title: '100%', value: 'img-fluid w-100'},
            {title: '75%', value: 'img-fluid w-75'},
            {title: '50%', value: 'img-fluid w-50'},
            {title: '25%', value: 'img-fluid w-25'}
        ],
        target_list: [
            {title: 'Nouvelle fenêtre', value: '_blank'},
            {title: 'Même fenêtre', value: ''},
        ],
        style_formats: [
            {title: 'Titre', block: 'h3'},
            {title: 'Sous titre', block: 'h4'},
            {title: 'Paragraphe', block: 'p'},
        ],

        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'pagebreak', 'searchreplace', 'wordcount', 'visualblocks',
            'visualchars', 'code', 'fullscreen', 'insertdatetime', 'media', 'nonbreaking',
            'save', 'table', 'directionality', 'wordcount', /*'emoticons',*/ 'template',
        ],
        toolbar: [
            "undo redo | styles | bold italic underline forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist hr table paste | link image media  | emoticons charmap | searchreplace preview code fullscreen"
        ],
        video_template_callback: function(data) {
            console.log(data);
                return  '<div class="embed-responsive embed-responsive-4by3">' +
                            '<video frameborder="0" class="embed-responsive-item" controls="controls">\n' +
                                '<source src="' + data.source1 + '"' + (data.source1mime ? ' type="' + data.source1mime + '"' : '') + ' />\n' +
                                (data.source2 ? '<source src="' + data.source2 + '"' + (data.source2mime ? ' type="' + data.source2mime + '"' : '') + ' />\n' : '') +
                            '</video>' +
                        '</div>';
        },
        relative_urls: false,
        image_dimensions: false,
        menubar: false,
        browser_spellcheck: true,
        image_advtab: true,
        images_upload_url: '/upload',
        automatic_uploads: true,
        file_picker_types: 'image',
        file_picker_callback: (cb, value, meta) => {
            var input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/*');
            input.onchange = function() {
                var file = this.files[0];

                var reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = function () {
                    var id = 'blobid' + (new Date()).getTime();
                    var blobCache =  tinymce.activeEditor.editorUpload.blobCache;
                    var base64 = reader.result.split(',')[1];
                    var blobInfo = blobCache.create(id, file, base64);
                    blobCache.add(blobInfo);
                    cb(blobInfo.blobUri(), { title: file.name });
                };
            };
            input.click();
        },
        setup : (editor) => {
            editor.on('change', () => {
                Livewire.emit('contentChange', editor.getContent());
            })
        }
    });
};

initEditor();


addEventListener('initTinymce', () => {
    tinymce.remove();
    initEditor();
})
