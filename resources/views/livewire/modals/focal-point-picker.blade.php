@extends('layouts.modal')

{{-- Taille par défaut Bootstrap (500px) — le masque y fait ~200px de large, --}}
{{-- suffisant pour un drag précis sans gonfler la perception du cadrage.     --}}

@section('modal-title')
    <i class="bx bx-crop me-2"></i>Cadrage de la vignette
@endsection

@section('modal-body')
    <p class="text-muted small mb-2">
        Faites glisser le cadre pour choisir la zone visible sur la vignette.
    </p>

    {{--
        Conteneur image.
        Position relative : le masque et les zones sombres s'y positionnent en absolu.
        overflow: hidden : les débordements du masque (impossible ici, mais sécurité) sont masqués.
        cursor move : signal visuel que tout est draggable.
    --}}
    <div id="fpp-container"
         class="position-relative w-100"
         style="user-select: none; cursor: move;">

        {{-- Image source affichée en pleine largeur --}}
        <img id="fpp-image"
             src="{{ dirname($imagePath) . '/' . rawurlencode(basename($imagePath)) }}"
             alt="Image à cadrer"
             class="img-fluid w-100 d-block"
             style="max-height: 380px; object-fit: contain;">

        {{--
            Calque d'assombrissement global (sous le masque).
            Il couvre toute l'image ; le masque "découpe" sa zone via clip-path en JS.
            Alternative plus simple : 4 bandes sombres autour du masque, gérées en JS.
            On choisit l'approche 4 bandes (top/right/bottom/left) car plus compatible.
        --}}
        <div id="fpp-shade-top"    style="position:absolute;left:0;right:0;top:0;    background:rgba(0,0,0,0.55);pointer-events:none;"></div>
        <div id="fpp-shade-bottom" style="position:absolute;left:0;right:0;bottom:0; background:rgba(0,0,0,0.55);pointer-events:none;"></div>
        <div id="fpp-shade-left"   style="position:absolute;top:0;bottom:0;left:0;   background:rgba(0,0,0,0.55);pointer-events:none;"></div>
        <div id="fpp-shade-right"  style="position:absolute;top:0;bottom:0;right:0;  background:rgba(0,0,0,0.55);pointer-events:none;"></div>

        {{-- Bordure du masque (rectangle de cadrage) --}}
        <div id="fpp-mask"
             style="
                position: absolute;
                box-sizing: border-box;
                border: 2px solid #FFD700;
                box-shadow: 0 0 0 1px rgba(0,0,0,0.5), 0 0 8px rgba(255,215,0,0.4);
                pointer-events: none;
                border-radius: 3px;
             ">
            {{-- Overlay gradient simulant le rendu tuile, visible dans le masque --}}
            <div style="
                position:absolute;inset:0;
                background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0.2) 60%, rgba(0,0,0,0.1) 100%);
                border-radius: 2px;
            "></div>
            {{-- Texte simulé en bas du masque --}}
            {{-- Texte simulé en bas du masque — tailles appliquées dynamiquement par JS --}}
            <div id="fpp-mask-text" style="position:absolute;bottom:0;left:0;right:0;color:#fff;z-index:2;box-sizing:border-box;">
                <div id="fpp-mask-title" style="font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;opacity:0.9;text-shadow:0 1px 2px rgba(0,0,0,0.8);">
                    @if($postIcon)
                        <i class="material-icons" style="vertical-align:middle;">{{ $postIcon }}</i>
                    @endif
                    {{ $postTitle ?: 'Titre de l\'article' }}
                </div>
                <div id="fpp-mask-excerpt" style="opacity:0.8;line-height:1.3;text-shadow:0 1px 2px rgba(0,0,0,0.8);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    {{ $postExcerpt ?: 'Extrait du contenu…' }}
                </div>
            </div>
        </div>
    </div>

    <div class="text-muted small mt-2">
        <i class="bx bx-info-circle me-1"></i>
        Position : <strong id="fpp-x-label">{{ $focalX }}</strong>% /
                   <strong id="fpp-y-label">{{ $focalY }}</strong>%
    </div>

    {{--
        Script dans modal-body pour rester dans l'élément racine Livewire 2.
        Logique :
          - Au show de la modale, on mesure l'image rendue et on calcule
            les dimensions du masque (ratio 306/180) et sa plage de déplacement.
          - focalX/focalY (0-100%) sont convertis en position px du masque,
            puis reconvertis en % à chaque déplacement.
          - Les 4 bandes sombres sont recalculées à chaque frame.
          - L'axe contraint (pas de rogné) est verrouillé à 0px de déplacement.
    --}}
    <script>
    (function () {
        // Ratio réel de la tuile sur la Une
        const TILE_W = 306;
        const TILE_H = 180;
        const TILE_RATIO = TILE_W / TILE_H; // ~1.7

        function initFocalPicker() {
            const container   = document.getElementById('fpp-container');
            const img         = document.getElementById('fpp-image');
            const mask        = document.getElementById('fpp-mask');
            const shadeTop    = document.getElementById('fpp-shade-top');
            const shadeBottom = document.getElementById('fpp-shade-bottom');
            const shadeLeft   = document.getElementById('fpp-shade-left');
            const shadeRight  = document.getElementById('fpp-shade-right');
            const xLabel      = document.getElementById('fpp-x-label');
            const yLabel      = document.getElementById('fpp-y-label');
            const maskTitle   = document.getElementById('fpp-mask-title');
            const maskExcerpt = document.getElementById('fpp-mask-excerpt');

            if (!container || !img || container.dataset.fppInit) return;
            container.dataset.fppInit = '1';

            // --- Dimensions calculées après rendu ---
            let imgW, imgH;          // dimensions px de l'image rendue
            let maskW, maskH;        // dimensions px du masque
            let maxOffsetX, maxOffsetY; // plage de déplacement (px)
            let offsetX = 0, offsetY = 0; // position courante du coin haut-gauche du masque

            function computeDimensions() {
                const rect = img.getBoundingClientRect();
                imgW  = rect.width;
                imgH  = rect.height;
                const imgRatio = imgW / imgH;

                if (imgRatio > TILE_RATIO) {
                    // Image plus large → rogné sur les côtés → masque calé en hauteur
                    maskH     = imgH;
                    maskW     = imgH * TILE_RATIO;
                    maxOffsetX = imgW - maskW;
                    maxOffsetY = 0;
                } else {
                    // Image plus haute → rogné en haut/bas → masque calé en largeur
                    maskW     = imgW;
                    maskH     = imgW / TILE_RATIO;
                    maxOffsetX = 0;
                    maxOffsetY = imgH - maskH;
                }

                // Positionne le masque à partir des % Livewire courants
                offsetX = (parseFloat(xLabel.textContent) / 100) * maxOffsetX;
                offsetY = (parseFloat(yLabel.textContent) / 100) * maxOffsetY;

                applyMask();
            }

            function applyMask() {
                // Clamp
                offsetX = Math.min(maxOffsetX, Math.max(0, offsetX));
                offsetY = Math.min(maxOffsetY, Math.max(0, offsetY));

                mask.style.left   = offsetX + 'px';
                mask.style.top    = offsetY + 'px';
                mask.style.width  = maskW   + 'px';
                mask.style.height = maskH   + 'px';

                // Bandes sombres
                shadeTop.style.height    = offsetY + 'px';
                shadeBottom.style.height = (imgH - offsetY - maskH) + 'px';
                shadeLeft.style.width    = offsetX + 'px';
                shadeLeft.style.top      = offsetY + 'px';
                shadeLeft.style.height   = maskH   + 'px';
                shadeRight.style.width   = (imgW - offsetX - maskW) + 'px';
                shadeRight.style.top     = offsetY + 'px';
                shadeRight.style.height  = maskH   + 'px';

                // Tailles de texte et espacements proportionnels à la tuile réelle
                // Tuile réelle : padding 12px côtés / 20px haut-bas, margin 4px sous titre et extrait
                const scale = maskW / TILE_W;
                const pad   = (12 * scale).toFixed(1);
                const padV  = (20 * scale).toFixed(1);
                const mgBot = (4  * scale).toFixed(1);

                maskTitle.style.fontSize     = (20 * scale).toFixed(1) + 'px';
                maskTitle.style.marginBottom = mgBot + 'px';
                maskExcerpt.style.fontSize   = (12 * scale).toFixed(1) + 'px';
                maskExcerpt.style.marginBottom = mgBot + 'px';

                const maskText = document.getElementById('fpp-mask-text');
                if (maskText) {
                    maskText.style.padding = padV + 'px ' + pad + 'px';
                }

                // Conversion en % pour les labels et Livewire
                const pctX = maxOffsetX > 0 ? (offsetX / maxOffsetX) * 100 : 50;
                const pctY = maxOffsetY > 0 ? (offsetY / maxOffsetY) * 100 : 50;
                xLabel.textContent = pctX.toFixed(1);
                yLabel.textContent = pctY.toFixed(1);
            }

            // --- Drag ---
            let isDragging = false;
            let dragStartX, dragStartY, dragStartOffX, dragStartOffY;

            function getClient(e) {
                const t = e.changedTouches ? e.changedTouches[0] : (e.touches ? e.touches[0] : null);
                return { cx: t ? t.clientX : e.clientX, cy: t ? t.clientY : e.clientY };
            }

            container.addEventListener('mousedown', (e) => {
                isDragging    = true;
                const { cx, cy } = getClient(e);
                dragStartX    = cx;
                dragStartY    = cy;
                dragStartOffX = offsetX;
                dragStartOffY = offsetY;
                e.preventDefault();
            });

            window.addEventListener('mousemove', (e) => {
                if (!isDragging) return;
                const { cx, cy } = getClient(e);
                offsetX = dragStartOffX + (cx - dragStartX);
                offsetY = dragStartOffY + (cy - dragStartY);
                applyMask();
            });

            window.addEventListener('mouseup', (e) => {
                if (!isDragging) return;
                isDragging = false;
                syncToLivewire();
            });

            container.addEventListener('touchstart', (e) => {
                isDragging    = true;
                const { cx, cy } = getClient(e);
                dragStartX    = cx;
                dragStartY    = cy;
                dragStartOffX = offsetX;
                dragStartOffY = offsetY;
            }, { passive: true });

            container.addEventListener('touchmove', (e) => {
                if (!isDragging) return;
                e.preventDefault();
                const { cx, cy } = getClient(e);
                offsetX = dragStartOffX + (cx - dragStartX);
                offsetY = dragStartOffY + (cy - dragStartY);
                applyMask();
            }, { passive: false });

            container.addEventListener('touchend', () => {
                isDragging = false;
                syncToLivewire();
            });

            function syncToLivewire() {
                const pctX = maxOffsetX > 0 ? (offsetX / maxOffsetX) * 100 : 50;
                const pctY = maxOffsetY > 0 ? (offsetY / maxOffsetY) * 100 : 50;
                window.livewire.emit(
                    'focalCoordsChanged',
                    parseFloat(Math.min(100, Math.max(0, pctX)).toFixed(2)),
                    parseFloat(Math.min(100, Math.max(0, pctY)).toFixed(2))
                );
            }

            // Calcul initial puis recalcul si l'image change de taille
            img.addEventListener('load', computeDimensions);
            if (img.complete) computeDimensions();
            window.addEventListener('resize', computeDimensions);

            // Après chaque re-render Livewire (suite à focalCoordsChanged),
            // le DOM est réinjecté : on recalcule pour restaurer le masque.
            document.addEventListener('livewire:update', () => {
                // Petit délai pour laisser Livewire finir l'injection DOM
                setTimeout(computeDimensions, 30);
            });
        }

        document.addEventListener('shown.bs.modal', initFocalPicker, { once: true });
    })();
    </script>
@endsection

@section('modal-footer')
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
    <button type="button" class="btn btn-primary" wire:click="confirm" data-bs-dismiss="modal">
        <i class="bx bx-check me-1"></i>Appliquer ce cadrage
    </button>
@endsection
