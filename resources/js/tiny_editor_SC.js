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
        link_default_target: '_blank',
        style_formats: [
            {title: 'Titre', block: 'h3'},
            {title: 'Sous titre', block: 'h4'},
            {title: 'Paragraphe', block: 'p'},
        ],
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'pagebreak', 'searchreplace', 'wordcount', 'visualblocks',
            'visualchars', 'code', 'fullscreen', 'insertdatetime', 'media', 'nonbreaking',
            'save', 'table', 'directionality', 'wordcount', 'emoticons', 'template',
        ],
        image_advtab: true,
        contextmenu: false,
        toolbar: [
            "undo redo | styles | bold italic underline forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist hr table paste | link image media | emoticons charmap | searchreplace preview code fullscreen"
        ],
        entity_encoding: 'raw',
        video_template_callback: function(data) {
            return  '<div class="embed-responsive embed-responsive-4by3">' +
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
        paste_preprocess: function(plugin, args) {
            // Nettoyer le contenu collé
            let div = document.createElement('div');
            div.innerHTML = args.content;

            const walk = node => {
                if (node.nodeType === Node.ELEMENT_NODE) {
                    // Convertir b → strong et i → em
                    if (node.tagName.toLowerCase() === 'b') node.outerHTML = `<strong>${node.innerHTML}</strong>`;
                    if (node.tagName.toLowerCase() === 'i') node.outerHTML = `<em>${node.innerHTML}</em>`;

                    // Supprimer spans et liens mais garder le contenu
                    if (['span', 'a'].includes(node.tagName.toLowerCase())) {
                        let parent = node.parentNode;
                        while (node.firstChild) parent.insertBefore(node.firstChild, node);
                        parent.removeChild(node);
                    }

                    // Supprimer tous les attributs sauf pour les images
                    if (node.tagName.toLowerCase() !== 'img') {
                        [...node.attributes].forEach(attr => node.removeAttribute(attr.name));
                    } else {
                        const keep = ['src', 'alt', 'width', 'height'];
                        [...node.attributes].forEach(attr => {
                            if (!keep.includes(attr.name)) node.removeAttribute(attr.name);
                        });
                    }

                    // Parcourir récursivement les enfants
                    [...node.childNodes].forEach(walk);
                }
            };

            [...div.childNodes].forEach(walk);
            args.content = div.innerHTML;
        },
        setup : (editor) => {
            editor.on('change', () => {
                Livewire.emit('contentChange', editor.getContent());
            });
            Livewire.on('deleteContent', () => {
                editor.setContent('')
            });
        }
    });
};

initEditor();

addEventListener('initTinymce', () => {
    tinymce.remove();
    initEditor();
});
