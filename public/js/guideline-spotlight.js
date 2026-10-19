/**
 * Gestionnaire de surbrillance des éléments de page pour le Guide en ligne.
 *
 * Lorsqu'une modale de guide en ligne est ouverte avec un css_selector configuré,
 * cet événement est déclenché pour mettre en évidence l'élément cible dans la page.
 *
 * Mécanisme :
 * 1. Injection d'un overlay semi-transparent sur la page (backdrop).
 * 2. L'élément cible est placé au-dessus via une classe .guideline-spotlight.
 * 3. L'overlay et la classe sont supprimés à la fermeture de la modale.
 */

// Styles injectés dynamiquement pour le spotlight
const GUIDELINE_SPOTLIGHT_STYLE_ID = 'guideline-spotlight-styles';

function injectSpotlightStyles() {
    if (document.getElementById(GUIDELINE_SPOTLIGHT_STYLE_ID)) return;

    const style = document.createElement('style');
    style.id = GUIDELINE_SPOTLIGHT_STYLE_ID;
    style.textContent = `
        .guideline-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.55);
            z-index: 1040;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }
        .guideline-spotlight {
            position: relative;
            z-index: 1055 !important;
            border-radius: 6px;
            box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.9),
                        0 0 0 8px rgba(13, 110, 253, 0.6),
                        0 0 30px rgba(13, 110, 253, 0.4);
            transition: box-shadow 0.3s ease;
        }
    `;
    document.head.appendChild(style);
}

let backdropEl = null;
let spotlightEl = null;

function applySpotlight(cssSelector) {
    if (!cssSelector) return;

    const target = document.querySelector(cssSelector);
    if (!target) {
        console.warn('[Guide en ligne] Sélecteur CSS introuvable :', cssSelector);
        return;
    }

    injectSpotlightStyles();

    // Créer l'overlay backdrop
    backdropEl = document.createElement('div');
    backdropEl.classList.add('guideline-backdrop');
    document.body.appendChild(backdropEl);

    // Appliquer le spotlight à l'élément cible
    target.classList.add('guideline-spotlight');
    spotlightEl = target;

    console.log('[Guide en ligne] Spotlight appliqué sur :', cssSelector);
}

function removeSpotlight() {
    if (backdropEl) {
        backdropEl.remove();
        backdropEl = null;
    }
    if (spotlightEl) {
        spotlightEl.classList.remove('guideline-spotlight');
        spotlightEl = null;
    }
}

// Écoute de l'événement déclenché par la vue Blade guideline-post-modal
window.addEventListener('guideline-highlight', (event) => {
    const selector = event.detail?.selector;
    if (selector) {
        applySpotlight(selector);
    }
});

// Nettoyage automatique à la fermeture de la modale Bootstrap
document.addEventListener('hidden.bs.modal', () => {
    removeSpotlight();
});
