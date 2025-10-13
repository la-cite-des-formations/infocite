<?php

namespace App\Http\Livewire;

use DOMDocument;
use DOMXPath;

trait HandleTinymceContent
{
    public $modelProperty;
    public $contentAttribute;

    public function initTinymceContent(string $modelAttributeContentPath)
    {
        [$modelProperty, $contentAttribute] = explode('.', $modelAttributeContentPath);

        if (! $this->$modelProperty) {
            throw new \Exception("L'objet {$modelProperty} n'existe pas sur le composant Livewire.");
        }

        $this->modelProperty = $modelProperty;
        $this->contentAttribute = $contentAttribute;
    }

    public function contentChange($content) {
        $this->{$this->modelProperty}->{$this->contentAttribute} = $content;
    }

    public function contentPaste($content) {
        // Appelé après l'événement Livewire.emit('contentPaste', rawHtml)

        // 1. Nettoyage Complet Côté Serveur
        $cleanContent = $this->sanitizeContent($content);

        // 2. Réinsère le contenu nettoyé dans TinyMCE
        $this->emit('insertCleanContent', $cleanContent);
    }

    /**
     * Nettoie le contenu collé pour ne conserver que la sémantique et les attributs essentiels.
     *
     * @param string $content Contenu HTML brut
     * @return string Contenu nettoyé
     */
    protected function sanitizeContent(string $content): string
    {
        // =========================================================
        // 1. PRÉ-TRAITEMENT (Nettoyage de Chaîne)
        // =========================================================

        // Normalisation Unicode (suppression des polices unicode 'exotiques')
        $content = normalizeUnicodeString($content);

        // Retrait des balises XML propriétaires MS Office (w:, o:, v:) et des commentaires
        // Ceci doit se faire avant le chargement DOM pour éviter les erreurs de parsing
        $content = preg_replace('/<\/?\w+:[^>]*>/', '', $content);
        $content = preg_replace('//', '', $content);

        // =========================================================
        // 2. FILTRATION HTML VIA DOMDocument
        // =========================================================
        $dom = new DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);

        // Conversion de l'encodage pour garantir la compatibilité DOMDocument avec UTF-8
        $content = mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8');

        // Chargement du fragment HTML (permet de créer la structure <html>/<body> manquante)
        $dom->loadHTML('<?xml encoding="utf-8"?>'. $content, LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);

        // =========================================================
        // A. Retrait des Balises Spécifiques (Unwrapping : Balises supprimées, contenu conservé)
        // =========================================================
        // 1. GESTION SPÉCIFIQUE DES DIVS :
        foreach ($xpath->query('//div') as $node) {

            // Assertion de type : Indiquer à PHP et à l'IDE que nous traitons un DOMElement
            /** @var \DOMElement $node */

            // Si le div contient d'autres éléments de bloc (p, ul, table, etc.), on le déballe.
            // Requête XPath : cherche un enfant qui est un élément de bloc typique.
            if ($xpath->query('./*[self::p or self::ul or self::ol or self::table or self::h1 or self::h2 or self::h3]', $node)->length > 0) {
                // Déballer le div
                $fragment = $dom->createDocumentFragment();
                while ($node->hasChildNodes()) {
                    $fragment->appendChild($node->firstChild);
                }
                $node->parentNode->replaceChild($fragment, $node);

            }
            else {
                // Sinon (le div contient uniquement du texte ou est vide) : le convertir en <p>
                $newNode = $dom->createElement('p');
                // Déplacer les enfants (texte) vers le nouveau paragraphe
                while ($node->hasChildNodes()) {
                    $newNode->appendChild($node->firstChild);
                }
                // Remplacer le <div> par le <p>
                $node->parentNode->replaceChild($newNode, $node);
            }
        }

        // 2. Balises typiquement inutiles ou obsolètes après un collage
        $nodesToUnwrap = ['span', 'a', 'font', 'u', 'strike'];

        foreach ($nodesToUnwrap as $tagName) {
            foreach ($xpath->query("//{$tagName}") as $node) {
                $fragment = $dom->createDocumentFragment();
                while ($node->hasChildNodes()) {
                    $fragment->appendChild($node->firstChild);
                }
                $node->parentNode->replaceChild($fragment, $node);
            }
        }

        // =========================================================
        // B. Sémantisation des Balises (Conversion b/i en strong/em)
        // =========================================================

        // 1. Conversion <b> en <strong>
        foreach ($xpath->query('//b') as $node) {
            // Créer un nouvel élément <strong>
            $newNode = $dom->createElement('strong');
            // Transférer tous les enfants (contenu) dans le nouvel élément
            while ($node->hasChildNodes()) {
                $newNode->appendChild($node->firstChild);
            }
            // Remplacer l'ancienne balise par la nouvelle
            $node->parentNode->replaceChild($newNode, $node);
        }

        // 2. Conversion <i> en <em>
        foreach ($xpath->query('//i') as $node) {
            // Créer un nouvel élément <em>
            $newNode = $dom->createElement('em');
            // Transférer tous les enfants (contenu)
            while ($node->hasChildNodes()) {
                $newNode->appendChild($node->firstChild);
            }
            // Remplacer l'ancienne balise par la nouvelle
            $node->parentNode->replaceChild($newNode, $node);
        }

        // =========================================================
        // C. Retrait des Attributs Indésirables (avec exceptions pour le multimédia)
        // =========================================================

        // Attributs à retirer de TOUTES les balises (sauf exceptions ci-dessous)
        $attributesToRemove = [
            'class', 'id', 'style', 'dir', 'align', 'border',
            'valign', 'lang', 'scope', 'width', 'height', // width/height retirés des balises non-multimédia
        ];

        // Attributs vitaux à conserver (Liste Blanche)
        $immuneAttributes = [
            'src', 'alt', 'width', 'height', 'title',
            'controls', 'frameborder', 'allowfullscreen'
        ];

        // Balises multimédias pour lesquelles nous faisons des exceptions
        $multimediaTags = ['img', 'iframe', 'video'];

        foreach ($xpath->query('//*') as $node) {

            // Assertion de type : Indiquer à PHP et à l'IDE que nous traitons un DOMElement
            /** @var \DOMElement $node */

            $nodeName = strtolower($node->nodeName);
            $isMultimedia = in_array($nodeName, $multimediaTags);

            $attributes = iterator_to_array($node->attributes);

            foreach ($attributes as $attr) {
                $attrName = strtolower($attr->nodeName);

                if ($isMultimedia) {
                    // Pour le multimédia : Retirer UNIQUEMENT si ce n'est PAS un attribut immunisé et si ce n'est PAS data-*
                    if (!in_array($attrName, $immuneAttributes) &&!str_starts_with($attrName, 'data-')) {
                        $node->removeAttribute($attrName);
                    }
                } else {
                    // Pour les autres balises (p, h1, table, etc.) : Retirer si dans la liste noire OU si c'est un attribut data-*
                    if (in_array($attrName, $attributesToRemove) || str_starts_with($attrName, 'data-')) {
                        $node->removeAttribute($attrName);
                    }
                }
            }
        }

        // =========================================================
        // C. Récupération du HTML Propre
        // =========================================================

        // Accès au body de manière fiable pour extraire uniquement le contenu.
        $body = $dom->getElementsByTagName('body')->item(0);

        $cleanContent = '';
        if ($body) {
            // Itérer sur les enfants pour récupérer le fragment sans la balise <body>
            foreach ($body->childNodes as $child) {
                $cleanContent.= $dom->saveHTML($child);
            }
        }

        // Retour en UTF-8 pur (re-conversion des entités)
        return mb_convert_encoding($cleanContent, 'UTF-8', 'UTF-8');
    }
}
