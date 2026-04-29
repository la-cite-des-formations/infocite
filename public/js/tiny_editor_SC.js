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
            "undo redo | blocs | styles | bold italic underline forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist hr table paste | link image media | emoticons charmap | searchreplace preview code fullscreen"
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
            // 1. Enregistrement des icônes
            editor.ui.registry.addIcon('mi-intro', '<svg width="24" height="24" viewBox="0 0 24 24"><path d="M21,4h-8h-1H7V2H5v2v10v8h2v-8h4h1h9l-2-5L21,4z M17.14,9.74l0.9,2.26H12h-1H7V6h5h1h5.05l-0.9,2.26L16.85,9L17.14,9.74z M14,9c0,1.1-0.9,2-2,2s-2-0.9-2-2s0.9-2,2-2S14,7.9,14,9z" fill="currentColor"/></svg>');
            editor.ui.registry.addIcon('mi-title', '<svg width="24" height="24" viewBox="0 0 24 24"><path d="M5 4v3h5.5v12h3V7H19V4H5z" fill="currentColor"/></svg>');
            editor.ui.registry.addIcon('mi-subtitle', '<svg width="24" height="24" viewBox="0 0 24 24"><path d="M5 4v3h5.5v12h3V7H19V4H5z" fill="currentColor"/></svg>');
            editor.ui.registry.addIcon('mi-paragraph', '<svg width="24" height="24" viewBox="0 0 24 24"><path d="M14 17H4v2h10v-2zm6-8H4v2h16V9zM4 15h16v-2H4v2zM4 5v2h16V5H4z" fill="currentColor"/></svg>');
            editor.ui.registry.addIcon('mi-columns', '<svg width="24" height="24" viewBox="0 0 24 24"><path d="M4,22H2V2h2V22z M22,2h-2v20h2V2z M13.5,7h-3v10h3V7z" fill="currentColor"/></svg>');
            editor.ui.registry.addIcon('mi-info', '<svg width="24" height="24" viewBox="0 0 24 24"><path d="M11 7h2v2h-2zm0 4h2v6h-2zm1-9C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z" fill="currentColor"/></svg>');
            editor.ui.registry.addIcon('mi-conclusion', '<svg width="24" height="24" viewBox="0 0 24 24"><path d="M11,6H9V4h2V6z M15,4h-2v2h2V4z M9,14h2v-2H9V14z M19,10V8h-2v2H19z M19,14v-2h-2v2H19z M13,14h2v-2h-2V14z M19,4h-2v2h2 V4z M13,8V6h-2v2H13z M7,10V8h2V6H7V4H5v16h2v-8h2v-2H7z M15,12h2v-2h-2V12z M11,10v2h2v-2H11z M9,8v2h2V8H9z M13,10h2V8h-2V10z M15,6v2h2V6H15z" fill="currentColor"/></svg>');
            editor.ui.registry.addIcon('mi-up', '<svg width="24" height="24" viewBox="0 0 24 24"><path d="M12 8l-6 6 1.41 1.41L12 10.83l4.59 4.58L18 14z" fill="currentColor"/></svg>');
            editor.ui.registry.addIcon('mi-down', '<svg width="24" height="24" viewBox="0 0 24 24"><path d="M16.59 8.59L12 13.17 7.41 8.59 6 10l6 6 6-6z" fill="currentColor"/></svg>');

            // 2. Fonction safeInsert (version originale + injection de contenu)
            const safeInsert = (html) => {
                let node = editor.selection.getNode();
                if (node && node.nodeName === 'P') {
                    const content = node.innerHTML.replace(/&nbsp;/g, '').replace(/\s/g, '').trim();
                    if (content === '' || content === '<br>' || content === '<br data-mce-bogus="1">') {
                        // Paragraphe vide : on le sélectionne pour le remplacer
                        editor.selection.select(node);
                    } else {
                        // Paragraphe avec texte : on injecte son contenu dans le template
                        const pInner = node.innerHTML;
                        html = html.replace(
                            /(<(?:p|h[1-6])[^>]*>)(.*?)(<\/(?:p|h[1-6])>)/i,
                            function(match, openTag, oldContent, closeTag) {
                                return openTag + pInner + closeTag;
                            }
                        );
                        editor.selection.select(node);
                    }
                } 
                else if (node && node.classList.contains('editor-block')) {
                    const inner = node.innerHTML.replace(/&nbsp;/g, '').replace(/\s/g, '').trim();
                    if (inner === '' || inner === '<br>' || inner === '<br data-mce-bogus="1">') {
                        editor.selection.select(node, true);
                    } else {
                        const firstChild = node.firstElementChild;
                        if (firstChild && firstChild.nodeName === 'P' && node.children.length === 1) {
                            const pContent = firstChild.innerHTML.replace(/&nbsp;/g, '').replace(/\s/g, '').trim();
                            if (pContent === '' || pContent === '<br>') {
                                editor.selection.select(firstChild);
                            }
                        }
                    }
                }
                editor.insertContent(html);
            };

            // 3. Raccourcis clavier
            editor.addShortcut('alt+shift+i', 'Introduction', () => safeInsert('<div class="editor-block block-intro"><p><em>Rédigez l\'introduction ici...</em></p></div>'));
            editor.addShortcut('alt+shift+t', 'Titre', () => safeInsert('<div class="editor-block block-title"><h2>Titre principal</h2></div>'));
            editor.addShortcut('alt+shift+s', 'Sous-titre', () => safeInsert('<div class="editor-block block-subtitle"><h4>Sous-titre de section</h4></div>'));
            editor.addShortcut('alt+shift+p', 'Paragraphe', () => safeInsert('<div class="editor-block block-paragraph"><p>Saisissez votre texte ici...</p></div>'));
            editor.addShortcut('alt+shift+c', 'Colonnes', () => safeInsert('<div class="row"><div class="col-12 col-md-6 editor-block block-col-left"><p>&nbsp;</p></div><div class="col-12 col-md-6 editor-block block-col-right"><p>&nbsp;</p></div></div>'));
            editor.addShortcut('alt+shift+n', 'Information', () => safeInsert('<div class="editor-block block-info"><p><strong>Note :</strong> Saisissez une information importante ici...</p></div>'));
            editor.addShortcut('alt+shift+z', 'Conclusion', () => safeInsert('<div class="editor-block block-conclusion-title"><h2>Titre de conclusion</h2></div><div class="editor-block block-conclusion-content"><p><em>Rédigez le mot de la fin ici...</em></p></div>'));

            // 4. Bouton Menu "Blocs"
            editor.ui.registry.addMenuButton('blocs', {
                text: 'Blocs',
                icon: 'visualblocks',
                fetch: (callback) => {
                    var items = [
                        { type: 'menuitem', text: 'Introduction', icon: 'mi-intro', onAction: () => safeInsert('<div class="editor-block block-intro"><p><em>Rédigez l\'introduction ici...</em></p></div>') },
                        { type: 'menuitem', text: 'Titre', icon: 'mi-title', onAction: () => safeInsert('<div class="editor-block block-title"><h2>Titre principal</h2></div>') },
                        { type: 'menuitem', text: 'Sous-titre', icon: 'mi-subtitle', onAction: () => safeInsert('<div class="editor-block block-subtitle"><h4>Sous-titre de section</h4></div>') },
                        { type: 'menuitem', text: 'Paragraphe', icon: 'mi-paragraph', onAction: () => safeInsert('<div class="editor-block block-paragraph"><p>Saisissez votre texte ici...</p></div>') },
                        { type: 'menuitem', text: 'Colonnes x2', icon: 'mi-columns', onAction: () => safeInsert('<div class="row"><div class="col-12 col-md-6 editor-block block-col-left"><p>&nbsp;</p></div><div class="col-12 col-md-6 editor-block block-col-right"><p>&nbsp;</p></div></div>') },
                        { type: 'menuitem', text: 'Information', icon: 'mi-info', onAction: () => safeInsert('<div class="editor-block block-info"><p><strong>Note :</strong> Saisissez une information importante ici...</p></div>') },
                        { type: 'menuitem', text: 'Titre conclusif', icon: 'mi-title', onAction: () => safeInsert('<div class="editor-block block-conclusion-title"><h2>Titre de conclusion</h2></div>') },
                        { type: 'menuitem', text: 'Conclusion avec titre', icon: 'mi-conclusion', onAction: () => safeInsert('<div class="editor-block block-conclusion-title"><h2>Titre de conclusion</h2></div><div class="editor-block block-conclusion-content"><p><em>Rédigez le mot de la fin ici...</em></p></div>') },
                        { type: 'menuitem', text: 'Conclusion sans titre', icon: 'mi-conclusion', onAction: () => safeInsert('<div class="editor-block block-conclusion-content"><p><em>Rédigez le mot de la fin ici...</em></p></div>') }
                    ];
                    callback(items);
                }
            });

            // 5. Logique des contrôles flottants
            const injectControlButtons = () => {
                editor.dom.select('.editor-block, .row').forEach(el => {
                    if (el.classList.contains('block-col-left') || el.classList.contains('block-col-right')) return;
                    if (el.classList.contains('row') && !editor.dom.select('.editor-block', el).length) return;

                    if (!editor.dom.select(':scope > .delete-block-btn', el).length) {
                        editor.dom.add(el, 'span', { class: 'delete-block-btn material-icons-outlined', contenteditable: 'false', title: 'Supprimer' }, 'delete');
                    }
                    if (!editor.dom.select(':scope > .add-newline-btn', el).length) {
                        editor.dom.add(el, 'span', { class: 'add-newline-btn material-icons-outlined', contenteditable: 'false', title: 'Ligne après' }, 'keyboard_return');
                    }
                    if (!editor.dom.select(':scope > .add-preline-btn', el).length) {
                        editor.dom.add(el, 'span', { class: 'add-preline-btn material-icons-outlined', contenteditable: 'false', title: 'Ligne avant' }, 'keyboard_return');
                    }
                    if (!editor.dom.select(':scope > .move-up-btn', el).length) {
                        editor.dom.add(el, 'span', { class: 'move-up-btn material-icons-outlined', contenteditable: 'false', title: 'Monter' }, 'expand_less');
                    }
                    if (!editor.dom.select(':scope > .move-down-btn', el).length) {
                        editor.dom.add(el, 'span', { class: 'move-down-btn material-icons-outlined', contenteditable: 'false', title: 'Descendre' }, 'expand_more');
                    }
                });
            };

            editor.on('NodeChange SetContent', injectControlButtons);

            // 6. Gestion globale des clics
            editor.on('click', (e) => {
                const target = e.target;
                const block = target.parentElement;

                if (target.classList.contains('delete-block-btn')) {
                    editor.undoManager.transact(() => {
                        const parentCol = block.parentElement;
                        editor.dom.remove(block);
                        // Si le parent est une colonne et qu'elle est maintenant vide, y injecter un P
                        if (parentCol && (parentCol.classList.contains('block-col-left') || parentCol.classList.contains('block-col-right'))) {
                            const remaining = parentCol.innerHTML.replace(/&nbsp;/g, '').replace(/\s/g, '').trim();
                            if (remaining === '' || remaining === '<br>' || remaining === '<br data-mce-bogus="1">') {
                                const p = editor.dom.create('p', {}, '&nbsp;');
                                parentCol.appendChild(p);
                                editor.selection.setCursorLocation(p, 0);
                            }
                        }
                    });
                    editor.fire('change');
                }
                if (target.classList.contains('add-newline-btn')) {
                    editor.undoManager.transact(() => {
                        const p = editor.dom.create('p', {}, '&nbsp;');
                        editor.dom.insertAfter(p, block);
                        editor.selection.setCursorLocation(p, 0);
                    });
                    editor.fire('change');
                }
                if (target.classList.contains('add-preline-btn')) {
                    editor.undoManager.transact(() => {
                        const p = editor.dom.create('p', {}, '&nbsp;');
                        block.parentNode.insertBefore(p, block);
                        editor.selection.setCursorLocation(p, 0);
                    });
                    editor.fire('change');
                }
                if (target.classList.contains('move-up-btn')) {
                    const prev = block.previousElementSibling;
                    if (prev && (prev.classList.contains('editor-block') || prev.classList.contains('row'))) {
                        editor.undoManager.transact(() => { block.parentNode.insertBefore(block, prev); });
                        editor.fire('change');
                    }
                }
                if (target.classList.contains('move-down-btn')) {
                    const next = block.nextElementSibling;
                    if (next && (next.classList.contains('editor-block') || next.classList.contains('row'))) {
                        editor.undoManager.transact(() => { block.parentNode.insertBefore(next, block); });
                        editor.fire('change');
                    }
                }
            });

            // 7. Nettoyage final
            editor.on('GetContent', (e) => {
                if (e.content) {
                    const div = document.createElement('div');
                    div.innerHTML = e.content;
                    div.querySelectorAll('.delete-block-btn, .add-newline-btn, .add-preline-btn, .move-up-btn, .move-down-btn').forEach(btn => btn.remove());
                    div.querySelectorAll('.editor-block').forEach(block => {
                        const hasText = block.textContent.trim().length > 0;
                        const hasImages = block.querySelectorAll('img').length > 0;
                        if (!hasText && !hasImages) {
                            block.remove();
                        }
                    });
                    e.content = div.innerHTML;
                }
            });

            editor.on('change', () => { Livewire.emit('contentChange', editor.getContent()); });
            Livewire.on('deleteContent', () => { editor.setContent(''); });
            Livewire.on('insertCleanContent', (cleanHtml) => { editor.insertContent(cleanHtml); });
        }
    });
};

initEditor();
addEventListener('initTinymce', () => { tinymce.remove(); initEditor(); });
