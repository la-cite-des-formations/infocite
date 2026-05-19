// 1. Définition des gestionnaires GLOBAUX (une seule fois)
const deleteHandler = () => {
    const editors = (window.tinymce.editors || window.tinymce.get() || []);
    editors.forEach(ed => { if (!ed.removed) ed.setContent(''); });
};

const insertHandler = (cleanHtml, atEnd = false) => {
    if (!cleanHtml) return;

    const editors = (window.tinymce.editors || window.tinymce.get() || []);
    console.log("Événement d'injection reçu pour " + editors.length + " éditeur(s)");

    editors.forEach(targetEditor => {
        if (targetEditor && !targetEditor.removed) {
            const editorId = targetEditor.id;
            // On utilise un délai pour laisser le DOM respirer (surtout après une modale)
            setTimeout(() => {
                console.log("Traitement injection pour : " + editorId);
                try {
                    let finalContent = "";
                    if (atEnd) {
                        finalContent = targetEditor.getContent() + cleanHtml;
                    } else {
                        // Insertion au curseur si possible, sinon à la fin
                        targetEditor.focus();
                        targetEditor.insertContent(cleanHtml);
                        finalContent = targetEditor.getContent();
                    }

                    // --- LA STRATÉGIE DE RELANCE PROPRE ---
                    // On détruit et on reconstruit pour garantir le layout CSS/Flexbox
                    tinymce.remove('#' + editorId);
                    const textarea = document.getElementById(editorId);
                    if (textarea) {
                        textarea.value = finalContent;
                    }
                    // On relance l'initialisation (définie plus bas)
                    initEditor();

                    console.log("Ré-initialisation réussie pour " + editorId);
                } catch (err) {
                    console.error("Erreur injection dans " + editorId, err);
                }
            }, 300);
        }
    });
};

// 2. Enregistrement des écouteurs Livewire (UNE SEULE FOIS au chargement du script)
if (typeof Livewire !== 'undefined') {
    Livewire.on('deleteContent', deleteHandler);
    Livewire.on('insertCleanContent', insertHandler);
}
window.addEventListener('tinymce-force-insert', (e) => {
    insertHandler(e.detail.content, e.detail.atEnd);
});


// 3. Fonction d'initialisation principale
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
        media_url_resolver: function (data, resolve) {
            var driveMatch = data.url.match(/drive\.google\.com\/file\/d\/([a-zA-Z0-9_-]+)/);
            if (driveMatch) {
                var embedUrl = 'https://drive.google.com/file/d/' + driveMatch[1] + '/preview';
                resolve({
                    html: '<iframe src="' + embedUrl + '" width="560" height="314" frameborder="0" allowfullscreen="allowfullscreen"></iframe>'
                });
            } else {
                resolve({ html: '' });
            }
        },
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
        paste_block_drop: true,
        paste_preprocess: function (plugin, args) {
            Livewire.emit('contentPaste', args.content);
            args.preventDefault();
        },
        setup: (editor) => {
            // Enregistrement des icônes
            editor.ui.registry.addIcon('mi-intro', '<svg width="24" height="24" viewBox="0 0 24 24"><path d="M21,4h-8h-1H7V2H5v2v10v8h2v-8h4h1h9l-2-5L21,4z M17.14,9.74l0.9,2.26H12h-1H7V6h5h1h5.05l-0.9,2.26L16.85,9L17.14,9.74z M14,9c0,1.1-0.9,2-2,2s-2-0.9-2-2s0.9-2,2-2S14,7.9,14,9z" fill="currentColor"/></svg>');
            editor.ui.registry.addIcon('mi-title', '<svg width="24" height="24" viewBox="0 0 24 24"><path d="M5 4v3h5.5v12h3V7H19V4H5z" fill="currentColor"/></svg>');
            editor.ui.registry.addIcon('mi-subtitle', '<svg width="24" height="24" viewBox="0 0 24 24"><path d="M5 4v3h5.5v12h3V7H19V4H5z" fill="currentColor"/></svg>');
            editor.ui.registry.addIcon('mi-paragraph', '<svg width="24" height="24" viewBox="0 0 24 24"><path d="M14 17H4v2h10v-2zm6-8H4v2h16V9zM4 15h16v-2H4v2zM4 15h16v-2H4v2zM4 5v2h16V5H4z" fill="currentColor"/></svg>');
            editor.ui.registry.addIcon('mi-columns', '<svg width="24" height="24" viewBox="0 0 24 24"><path d="M4,22H2V2h2V22z M22,2h-2v20h2V2z M13.5,7h-3v10h3V7z" fill="currentColor"/></svg>');
            editor.ui.registry.addIcon('mi-info', '<svg width="24" height="24" viewBox="0 0 24 24"><path d="M11 7h2v2h-2zm0 4h2v6h-2zm1-9C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z" fill="currentColor"/></svg>');
            editor.ui.registry.addIcon('mi-conclusion', '<svg width="24" height="24" viewBox="0 0 24 24"><path d="M11,6H9V4h2V6z M15,4h-2v2h2V4z M9,14h2v-2H9V14z M19,10V8h-2v2H19z M19,14v-2h-2v2H19z M13,14h2v-2h-2V14z M19,4h-2v2h2 V4z M13,8V6h-2v2H13z M7,10V8h2V6H7V4H5v16h2v-8h2v-2H7z M15,12h2v-2h-2V12z M11,10v2h2v-2H11z M9,8v2h2V8H9z M13,10h2V8h-2V10z M15,6v2h2V6H15z" fill="currentColor"/></svg>');
            editor.ui.registry.addIcon('mi-up', '<svg width="24" height="24" viewBox="0 0 24 24"><path d="M12 8l-6 6 1.41 1.41L12 10.83l4.59 4.58L18 14z" fill="currentColor"/></svg>');
            editor.ui.registry.addIcon('mi-down', '<svg width="24" height="24" viewBox="0 0 24 24"><path d="M16.59 8.59L12 13.17 7.41 8.59 6 10l6 6 6-6z" fill="currentColor"/></svg>');
            editor.ui.registry.addIcon('mi-frame', '<svg width="24" height="24" viewBox="0 0 24 24"><path d="M0,0h24v24H0V0z" fill="none"/><path d="M18,13c-3.31,0-6,2.69-6,6C15.31,19,18,16.31,18,13z M6,13c0,3.31,2.69,6,6,6C12,15.69,9.31,13,6,13z M8,11.03 c0,0.86,0.7,1.56,1.56,1.56c0.33,0,0.63-0.1,0.89-0.28l-0.01,0.12c0,0.86,0.7,1.56,1.56,1.56s1.56-0.7,1.56-1.56l-0.01-0.12 c0.25,0.17,0.56,0.28,0.89,0.28c0.86,0,1.56-0.7,1.56-1.56c0-0.62-0.37-1.16-0.89-1.41C15.63,9.38,16,8.84,16,8.22 c0-0.86-0.7-1.56-1.56-1.56c-0.33,0-0.63,0.1-0.89,0.28l0.01-0.12c0-0.86-0.7-1.56-1.56-1.56s-1.56,0.7-1.56,1.56l0.01,0.12 C10.2,6.76,9.89,6.66,9.56,6.66C8.7,6.66,8,7.36,8,8.22c0,0.62,0.37,1.16,0.89,1.41C8.37,9.87,8,10.41,8,11.03z M12,8.06 c0.86,0,1.56,0.7,1.56,1.56s-0.7,1.56-1.56,1.56s-1.56-0.7-1.56-1.56S11.14,8.06,12,8.06z M20,4v16H4V4H20 M20,2H4C2.9,2,2,2.9,2,4 v16c0,1.1,0.9,2,2,2h16c1.1,0,2-0.9,2-2V4C22,2.9,21.1,2,20,2z" fill="currentColor"/></svg>');

            const BLOCK_DEFINITIONS = [
                { id: 'intro', title: 'Introduction', icon: 'mi-intro', shortcut: 'alt+shift+i', template: '<div class="editor-block block-intro"><div class="block-content"><p><em>Rédigez l\'introduction ici...</em></p></div></div>' },
                { id: 'title', title: 'Titre principal', icon: 'mi-title', shortcut: 'alt+shift+t', template: '<div class="editor-block block-title"><div class="block-content"><h2>Titre principal</h2></div></div>' },
                { id: 'subtitle', title: 'Sous-titre', icon: 'mi-subtitle', shortcut: 'alt+shift+s', template: '<div class="editor-block block-subtitle"><div class="block-content"><h4>Sous-titre de section</h4></div></div>' },
                { id: 'paragraph', title: 'Paragraphe', icon: 'mi-paragraph', shortcut: 'alt+shift+p', template: '<div class="editor-block block-paragraph"><div class="block-content"><p>Saisissez votre texte ici...</p></div></div>' },
                { id: 'columns', title: 'Colonnes x2', icon: 'mi-columns', shortcut: 'alt+shift+c', template: '<div class="row"><div class="col-12 col-md-6 editor-block block-col-left"><div class="block-content"><p>&nbsp;</p></div></div><div class="col-12 col-md-6 editor-block block-col-right"><div class="block-content"><p>&nbsp;</p></div></div></div>' },
                { id: 'note', title: 'Note', icon: 'mi-info', shortcut: 'alt+shift+n', template: '<div class="editor-block block-note-info alert alert-info"><div class="block-content lc-mb-0"><p><strong>Note :</strong> Saisissez une note importante ici...</p></div></div>' },
                { id: 'frame', title: 'Cadre', icon: 'mi-frame', shortcut: 'alt+shift+f', template: '<div class="editor-block block-frame-simple"><div class="block-content lc-mb-0"><p>Saisissez le contenu du cadre ici...</p></div></div>' },
                { id: 'conclusion_title', title: 'Titre conclusif', icon: 'mi-title', template: '<div class="editor-block block-conclusion-title"><div class="block-content"><h2>Titre de conclusion</h2></div></div>' },
                { id: 'conclusion_full', title: 'Conclusion avec titre', icon: 'mi-conclusion', shortcut: 'alt+shift+z', template: '<div class="editor-block block-conclusion-title"><div class="block-content"><h2>Titre de conclusion</h2></div><div class="block-content"><p><em>Rédigez le mot de la fin ici...</em></p></div></div>' },
                { id: 'conclusion_content', title: 'Conclusion sans titre', icon: 'mi-conclusion', template: '<div class="editor-block block-conclusion-content"><div class="block-content"><p><em>Rédigez le mot de la fin ici...</em></p></div></div>' }
            ];

            const cleanTitleText = (html) => {
                const tmp = document.createElement('div');
                tmp.innerHTML = html;
                return tmp.textContent || tmp.innerText || "";
            };

            const findBestTarget = (node) => {
                if (!node || node.nodeName === 'BODY') return null;
                const list = editor.dom.getParent(node, 'ul, ol');
                if (list) return list;
                let current = editor.dom.getParent(node, 'p,h1,h2,h3,h4,h5,h6,blockquote,div.editor-block,div.row');
                if (!current) return node;
                if (current.classList.contains('editor-block')) {
                    const first = current.firstElementChild;
                    if (first && current.children.length === 1) return first;
                }
                return current;
            };

            const safeInsert = (templateHtml) => {
                const selection = editor.selection;
                const isCollapsed = selection.isCollapsed();
                const isTitleBlock = templateHtml.includes('block-title') || templateHtml.includes('block-subtitle') || templateHtml.includes('block-conclusion-title');

                editor.undoManager.transact(() => {
                    if (!isCollapsed) {
                        // --- LOGIQUE MULTI-BLOCS (SÉLECTION ACTIVE) ---
                        // On identifie les blocs de départ et de fin
                        let startBlock = findBestTarget(selection.getStart());
                        let endBlock = findBestTarget(selection.getEnd());

                        if (!startBlock || !endBlock) return;

                        // On collecte tous les blocs frères entre start et end
                        let collectedBlocks = [];
                        let current = startBlock;
                        while (current) {
                            collectedBlocks.push(current);
                            if (current === endBlock || !current.nextElementSibling) break;
                            current = current.nextElementSibling;
                        }

                        // Sécurité Titre : Interdire si plusieurs blocs ou si liste
                        const hasList = collectedBlocks.some(b => b.nodeName === 'UL' || b.nodeName === 'OL');
                        if (isTitleBlock && (collectedBlocks.length > 1 || hasList)) {
                            editor.notificationManager.open({
                                text: "Un bloc 'Titre' ne peut pas contenir de liste ou de sélection multi-blocs.",
                                type: 'warning',
                                timeout: 4000
                            });
                            return;
                        }

                        // Fusion du contenu HTML de tous les blocs collectés
                        const combinedHtml = collectedBlocks.map(b => b.outerHTML).join('');

                        const fragment = editor.dom.createFragment(templateHtml);
                        const newNodes = Array.from(fragment.childNodes);
                        const targetBlock = newNodes.reverse().find(n => n.nodeType === 1) || newNodes[0];
                        newNodes.reverse();

                        if (targetBlock) {
                            if (isTitleBlock) {
                                const titleTag = targetBlock.querySelector('h1, h2, h3, h4, h5, h6');
                                if (titleTag) titleTag.innerText = cleanTitleText(combinedHtml);
                            } else {
                                const contentTag = targetBlock.querySelector('p');
                                if (contentTag) {
                                    editor.dom.replace(editor.dom.createFragment(combinedHtml), contentTag);
                                }
                            }
                        }

                        // Remplacement : Insérer après le dernier bloc et supprimer les anciens
                        let ref = endBlock;
                        newNodes.forEach(n => {
                            editor.dom.insertAfter(n, ref);
                            ref = n;
                        });
                        collectedBlocks.forEach(b => editor.dom.remove(b));
                        editor.fire('change');

                    } else {
                        // --- LOGIQUE MONO-BLOC (CURSEUR SIMPLE) ---
                        const node = selection.getNode();
                        const target = findBestTarget(node);

                        if (target && target.nodeName !== 'BODY' && !target.classList.contains('editor-block') && !target.classList.contains('row')) {
                            // (Logique identique à la précédente, conservée pour le curseur simple)
                            const isColumnBlock = templateHtml.includes('class="row"');
                            const isList = target.nodeName === 'UL' || target.nodeName === 'OL';

                            if (isTitleBlock && isList) {
                                editor.notificationManager.open({
                                    text: "Un bloc 'Titre' ne peut pas contenir de liste.",
                                    type: 'warning',
                                    timeout: 4000
                                });
                                return;
                            }

                            const isEmpty = target.textContent.trim() === '' && !target.querySelector('img, iframe, video, table, .editor-block');
                            const fragment = editor.dom.createFragment(templateHtml);
                            const newNodes = Array.from(fragment.childNodes);
                            const firstBlock = newNodes.find(n => n.nodeType === 1);

                            if (!isEmpty && firstBlock) {
                                let contentHtml = isList ? target.outerHTML : target.innerHTML;
                                if (isTitleBlock) contentHtml = cleanTitleText(contentHtml);

                                if (isColumnBlock) {
                                    const leftCol = firstBlock.querySelector('.block-col-left');
                                    if (leftCol) leftCol.innerHTML = `<p>${contentHtml}</p>`;
                                } else {
                                    const contentTag = firstBlock.querySelector('p, h1, h2, h3, h4, h5, h6');
                                    if (contentTag) {
                                        if (isList) {
                                            editor.dom.replace(editor.dom.createFragment(contentHtml), contentTag);
                                        } else {
                                            contentTag.innerHTML = contentHtml;
                                        }
                                    }
                                }
                            }

                            let ref = target;
                            newNodes.forEach(newNode => {
                                editor.dom.insertAfter(newNode, ref);
                                ref = newNode;
                            });
                            editor.dom.remove(target);

                            const focusNode = firstBlock.querySelector('p, h2, h4') || firstBlock;
                            editor.selection.setCursorLocation(focusNode, 0);
                            editor.fire('change');
                        } else {
                            editor.insertContent(templateHtml);
                        }
                    }
                });
            };

            const injectControlButtons = () => {
                editor.dom.select('.editor-block, .row').forEach(el => {
                    if (el.classList.contains('block-col-left') || el.classList.contains('block-col-right')) return;
                    if (el.classList.contains('row') && !editor.dom.select('.editor-block', el).length) return;

                    if (!editor.dom.select(':scope > .delete-block-btn', el).length) {
                        editor.dom.add(el, 'span', { class: 'delete-block-btn material-icons-outlined', contenteditable: 'false', title: 'Supprimer tout' }, 'delete');
                    }
                    if (!editor.dom.select(':scope > .unwrap-block-btn', el).length) {
                        editor.dom.add(el, 'span', { class: 'unwrap-block-btn material-icons-outlined', contenteditable: 'false', title: 'Retirer le cadre (garder le contenu)' }, 'layers_clear');
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

                    // Bouton Palette de style (Pour les blocs Note et Cadre)
                    const isNote = el.classList.contains('block-note-info') || el.classList.contains('block-note-success') || el.classList.contains('block-note-warning') || el.classList.contains('block-note-alert');
                    const isFrame = el.classList.contains('block-frame-simple') || el.classList.contains('block-frame-modern') || el.classList.contains('block-frame-elegant') || el.classList.contains('block-frame-dashed');

                    if (isNote || isFrame) {
                        if (!editor.dom.select(':scope > .change-style-btn', el).length) {
                            editor.dom.add(el, 'span', { class: 'change-style-btn material-icons-outlined', contenteditable: 'false', title: 'Changer le style' }, 'palette');

                            // Injection dynamique du menu selon le type de bloc
                            let menuHtml = '';
                            if (isNote) {
                                menuHtml = `
                                    <div class="style-selector-menu" contenteditable="false">
                                        <div class="style-option" data-style="block-note-info"><span class="style-dot dot-info"></span> Information</div>
                                        <div class="style-option" data-style="block-note-success"><span class="style-dot dot-success"></span> Succès</div>
                                        <div class="style-option" data-style="block-note-warning"><span class="style-dot dot-warning"></span> Attention</div>
                                        <div class="style-option" data-style="block-note-alert"><span class="style-dot dot-alert"></span> Alerte</div>
                                    </div>
                                `;
                            } else if (isFrame) {
                                menuHtml = `
                                    <div class="style-selector-menu" contenteditable="false">
                                        <div class="style-option" data-style="block-frame-simple"><span class="style-dot dot-simple"></span> Simple</div>
                                        <div class="style-option" data-style="block-frame-modern"><span class="style-dot dot-modern"></span> Moderne</div>
                                        <div class="style-option" data-style="block-frame-elegant"><span class="style-dot dot-elegant"></span> Élégant</div>
                                        <div class="style-option" data-style="block-frame-dashed"><span class="style-dot dot-dashed"></span> Pointillé</div>
                                        <hr class="my-1">
                                        <div class="style-option" data-align-val="left"><i class="material-icons-outlined fs-6">format_align_left</i> Gauche</div>
                                        <div class="style-option" data-align-val="center"><i class="material-icons-outlined fs-6">format_align_center</i> Centrer</div>
                                        <div class="style-option" data-align-val="right"><i class="material-icons-outlined fs-6">format_align_right</i> Droite</div>
                                    </div>
                                `;
                            }
                            editor.dom.add(el, 'div', { class: 'menu-wrapper-safe', contenteditable: 'false' }, menuHtml);
                        }
                    }
                });
            };

            editor.ui.registry.addMenuButton('blocs', {
                text: 'Blocs',
                icon: 'visualblocks',
                fetch: (callback) => {
                    const items = BLOCK_DEFINITIONS.map(block => ({
                        type: 'menuitem',
                        text: block.title,
                        icon: block.icon,
                        shortcut: block.shortcut,
                        onAction: () => safeInsert(block.template)
                    }));
                    callback(items);
                }
            });

            editor.on('NodeChange SetContent', injectControlButtons);
            // Détection des images cassées (fichiers manquants)
            editor.on('init', () => {
                editor.getBody().addEventListener('error', (e) => {
                    if (e.target.tagName === 'IMG') {
                        e.target.classList.add('img-broken');
                    }
                }, true);
            });

            editor.on('click', (e) => {
                const target = e.target;

                // --- AIMANT À PARAGRAPHE (Correction de la ligne fantôme) ---
                // Si le curseur atterrit sur le DIV du cadre lui-même, on le renvoie dans le dernier paragraphe
                const node = editor.selection.getNode();
                if (node && node.classList && (node.classList.contains('block-frame-simple') || node.classList.contains('block-frame-modern') || node.classList.contains('block-frame-elegant') || node.classList.contains('block-frame-dashed'))) {
                    const lastP = node.querySelector('p:last-of-type');
                    if (lastP) {
                        // On force le curseur à la fin, après le dernier élément enfant (ex: après une vidéo)
                        editor.selection.setCursorLocation(lastP, lastP.childNodes.length);
                    }
                }
                // -------------------------------------------------------------

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
                if (target.classList.contains('unwrap-block-btn')) {
                    editor.undoManager.transact(() => {
                        // On récupère le contenu interne (classe block-content)
                        const fragment = editor.dom.createFragment(block.querySelector('.block-content').innerHTML);

                        // Insérer le contenu à la place du bloc
                        editor.dom.insertAfter(fragment, block);
                        editor.dom.remove(block);
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

                // Gestion du bouton Palette
                const paletteBtn = target.closest('.change-style-btn');
                if (paletteBtn) {
                    const targetBlock = paletteBtn.closest('.editor-block');
                    if (targetBlock) {
                        e.preventDefault();
                        // Fermer les autres menus ouverts
                        editor.dom.select('.show-style-menu').forEach(openBlock => {
                            if (openBlock !== targetBlock) openBlock.classList.remove('show-style-menu');
                        });
                        targetBlock.classList.toggle('show-style-menu');
                        return;
                    }
                }

                const option = target.closest('.style-option');
                if (option) {
                    const styleBlock = option.closest('.editor-block');
                    const newStyle = option.getAttribute('data-style');
                    const newAlign = option.getAttribute('data-align-val');

                    if (styleBlock) {
                        editor.undoManager.transact(() => {
                            if (newStyle) {
                                // Supprimer toutes les classes de style note, alert et frame possibles
                                styleBlock.classList.remove('block-note-info', 'block-note-success', 'block-note-warning', 'block-note-alert');
                                styleBlock.classList.remove('block-frame-simple', 'block-frame-modern', 'block-frame-elegant', 'block-frame-dashed');
                                styleBlock.classList.remove('alert', 'alert-info', 'alert-success', 'alert-warning', 'alert-danger');

                                // Ajouter la nouvelle
                                styleBlock.classList.add(newStyle);

                                // Si c'est une note, ajouter les classes Bootstrap alert
                                if (newStyle.startsWith('block-note-')) {
                                    styleBlock.classList.add('alert');
                                    if (newStyle === 'block-note-info') styleBlock.classList.add('alert-info');
                                    if (newStyle === 'block-note-success') styleBlock.classList.add('alert-success');
                                    if (newStyle === 'block-note-warning') styleBlock.classList.add('alert-warning');
                                    if (newStyle === 'block-note-alert') styleBlock.classList.add('alert-danger');
                                }

                                // Gérer la marge du paragraphe interne
                                const innerP = styleBlock.querySelector('p');
                                if (innerP) {
                                    if (newStyle.startsWith('block-note-')) {
                                        innerP.classList.add('mb-0');
                                    } else {
                                        innerP.classList.remove('mb-0');
                                    }
                                }
                            }

                            if (newAlign) {
                                styleBlock.style.textAlign = newAlign;
                            }

                            styleBlock.classList.remove('show-style-menu');
                        });
                        editor.fire('change');
                    }
                } else if (!target.closest('.style-selector-menu')) {
                    // Fermer le menu si on clique ailleurs
                    editor.dom.select('.show-style-menu').forEach(openBlock => {
                        openBlock.classList.remove('show-style-menu');
                    });
                }
            });

            // 7. Nettoyage final
            editor.on('GetContent', (e) => {
                if (e.content) {
                    const div = document.createElement('div');
                    div.innerHTML = e.content;
                    div.querySelectorAll('.delete-block-btn, .unwrap-block-btn, .add-newline-btn, .add-preline-btn, .move-up-btn, .move-down-btn, .change-style-btn, .style-selector-menu, .menu-wrapper-safe').forEach(btn => btn.remove());
                    div.querySelectorAll('img.img-broken').forEach(img => img.classList.remove('img-broken'));
                    div.querySelectorAll('.editor-block').forEach(block => {
                        // Retirer la classe de menu si présente
                        block.classList.remove('show-style-menu');

                        const hasText = block.textContent.trim().length > 0;
                        const hasMedia = block.querySelectorAll('img, iframe, video, audio, table, object').length > 0;

                        if (!hasText && !hasMedia) {
                            block.remove();
                        }
                    });

                    e.content = div.innerHTML;
                }
            });

            editor.on('change', () => { Livewire.emit('contentChange', editor.getContent()); });

            // Note: On n'enregistre plus de Livewire.on ici pour éviter les doublons lors des re-init
        }
    });
};

// 4. Lancement initial
initEditor();
addEventListener('initTinymce', () => { tinymce.remove(); initEditor(); });
