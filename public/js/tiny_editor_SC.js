const initEditor = function () {
    tinymce.init({
        selector: ".tinymce",
        height: 500,
        min_height: 200,
        max_height: 750,
        resize: true,
        language: 'fr_FR',
        content_css: [
            '/css/app.css',
            '/css/style.css',
            '/css/custom.css',
            '/css/tinymce-templates.css',
        ],
        image_class_list: [
            { title: '100%', value: 'img-fluid w-100' },
            { title: '75%', value: 'img-fluid w-75' },
            { title: '50%', value: 'img-fluid w-50' },
            { title: '25%', value: 'img-fluid w-25' }
        ],
        target_list: [
            { title: 'Nouvelle fenêtre', value: '_blank' },
            { title: 'Même fenêtre', value: '' },
        ],
        link_default_target: '_blank',
        style_formats: [
            { title: 'Titre', block: 'h3' },
            { title: 'Sous titre', block: 'h4' },
            { title: 'Paragraphe', block: 'p' },
        ],
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'pagebreak', 'searchreplace', 'wordcount', 'visualblocks',
            'visualchars', 'code', 'fullscreen', 'insertdatetime', 'media', 'nonbreaking',
            'save', 'table', 'directionality', 'wordcount', 'emoticons'
        ],
        image_advtab: true,
        contextmenu: false,
        toolbar: [
            "undo redo | cadres | styles | bold italic underline forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist hr table paste | link image media | emoticons charmap | searchreplace preview code fullscreen"
        ],
        entity_encoding: 'raw',
        content_style: `.mce-content-body[data-mce-placeholder]:not(.mce-visualblocks)::before { display: block; width: 100%; text-align: center; padding-top: 40px; }`,
        video_template_callback: function (data) {
            return '<div class="embed-responsive embed-responsive-4by3">' +
                '<video frameborder="0" class="embed-responsive-item" controls="controls">' +
                '<source src="' + data.source1 + '"' + (data.source1mime ? ' type="' + data.source1mime + '"' : '') + ' />' +
                (data.source2 ? '<source src="' + data.source2 + '"' + (data.source2mime ? ' type="' + data.source2mime + '"' : '') + ' />' : '') +
                '</video>' +
                '</div>';
        },
        relative_urls: false,
        image_dimensions: false,
        menubar: false,
        browser_spellcheck: true,
        images_upload_url: '/upload',
        automatic_uploads: true,
        file_picker_types: 'image',
        file_picker_callback: (cb, value, meta) => {
            var input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/*');
            input.onchange = function () {
                var file = this.files[0];
                var reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = function () {
                    var id = 'blobid' + (new Date()).getTime();
                    var blobCache = tinymce.activeEditor.editorUpload.blobCache;
                    var base64 = reader.result.split(',')[1];
                    var blobInfo = blobCache.create(id, file, base64);
                    blobCache.add(blobInfo);
                    cb(blobInfo.blobUri(), { title: file.name });
                };
            };
            input.click();
        },

        // gestion du collage de contenu externe
        paste_block_drop: true, // glisser-déposer interdit
        paste_preprocess: function (plugin, args) {
            Livewire.emit('contentPaste', args.content);
            args.preventDefault(); // empêche TinyMCE de coller le HTML brut
        },

        setup: (editor) => {
            editor.ui.registry.addMenuButton('cadres', {
                text: 'Cadres',
                icon: 'visualblocks',
                fetch: (callback) => {
                    var items = [
                        {
                            type: 'menuitem',
                            text: 'Cadre Introduction',
                            onAction: () => editor.insertContent('<div class="editor-block block-intro"><p><em>Rédigez l\'introduction ici...</em></p></div>')
                        },
                        {
                            type: 'menuitem',
                            text: 'Cadre Titre',
                            onAction: () => editor.insertContent('<div class="editor-block block-title"><h2>Titre principal de la section</h2></div>')
                        },
                        {
                            type: 'menuitem',
                            text: 'Structure 2 colonnes',
                            onAction: () => editor.insertContent(
                                '<div class="row">' +
                                '<div class="col-12 col-md-6">' +
                                '<div class="editor-block block-col-left"><p><em>Contenu colonne gauche...</em></p></div>' +
                                '</div>' +
                                '<div class="col-12 col-md-6">' +
                                '<div class="editor-block block-subtitle"><h4>Sous-titre colonne droite</h4></div>' +
                                '<div class="editor-block block-col-right"><p><em>Contenu colonne droite...</em></p></div>' +
                                '</div>' +
                                '</div>'
                            )
                        },
                        {
                            type: 'menuitem',
                            text: 'Cadre Conclusion',
                            onAction: () => editor.insertContent(
                                '<div class="editor-block block-conclusion-title"><h2>Conclusion</h2></div>' +
                                '<div class="editor-block block-conclusion-content"><p><em>Rédigez le mot de la fin ici...</em></p></div>'
                            )
                        }
                    ];
                    callback(items);
                }
            });

            editor.on('change', () => {
                Livewire.emit('contentChange', editor.getContent());
            });
            Livewire.on('deleteContent', () => {
                editor.setContent('')
            });
            Livewire.on('insertCleanContent', (cleanHtml) => {
                editor.insertContent(cleanHtml);
            });
        }
    });
};

initEditor();

addEventListener('initTinymce', () => {
    tinymce.remove();
    initEditor();
});
