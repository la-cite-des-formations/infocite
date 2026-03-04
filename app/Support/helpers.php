<?php
/**
 * Fonctions d'aide (helpers) globales pour l'application.
 */

if (!function_exists('normalizeUnicodeString')) {
    /**
     * Normalise une chaîne Unicode :
     * - Convertit les caractères alphabétiques "stylisés" (gras, script, etc.) en ASCII.
     * - Convertit les caractères typographiques (tirets, apostrophes, etc.) en ASCII standard.
     * - Conserve les emojis.
     * - Supprime les autres caractères exotiques.
     */
    function normalizeUnicodeString(string $input): string
    {
        // =========================================================
        // Conversion des caractères typographiques en ASCII (apostrophes, guillemets, tirets et ellipse)
        // =========================================================
        $search = [

            // 1. Apostrophes et Guillemets Typographiques
            '’', '‘', // Apostrophes courbes
            '”', '“', // Guillemets doubles courbes
            '«', '»', // Guillemets français

            // 2. Tirets et Ellipses
            '—', // Tiret Cadratin (Em Dash)
            '–', // Tiret Demi-Cadratin (En Dash)
            '…', // Ellipse unique Unicode

            // 3. Espaces Spéciaux
            ' ', // Espace Insécable (NBSP)
            ' ', // Thin Space
            ' ', // Hair Space
            ' ', // Narrow No-Break Space
        ];

        $replace = [

            // 1. Remplacement des apostrophes et guillemets
            '\'', '\'',
            '"', '"',
            '"', '"',

            // 2. Remplacement des tirets et ellipses
            '-',
            '-',
            '...',

            // 3. Remplacement des espaces par l'espace standard ASCII (U+0020)
            ' ',
            ' ',
            ' ',
            ' ',
        ];

        $input = str_replace($search, $replace, $input);

        // =========================================================
        // Logique de conversion et de conservation (Maths, Emojis, ASCII)
        // =========================================================

        $blocks = [
            ['A' => 0x1D400, 'a' => 0x1D41A], // Mathematical Bold
            ['A' => 0x1D434, 'a' => 0x1D44E], // Mathematical Italic
            ['A' => 0x1D468, 'a' => 0x1D482], // Mathematical Bold Italic
            ['A' => 0x1D49C, 'a' => 0x1D4B6], // Mathematical Script
            ['A' => 0x1D4D0, 'a' => 0x1D4EA], // Mathematical Bold Script
            ['A' => 0x1D504, 'a' => 0x1D51E], // Mathematical Fraktur
            ['A' => 0x1D538, 'a' => 0x1D552], // Mathematical Double-Struck
            ['A' => 0x1D5A0, 'a' => 0x1D5BA], // Mathematical Sans-serif
            ['A' => 0x1D5D4, 'a' => 0x1D5EE], // Mathematical Sans-serif Bold
            ['A' => 0x1D670, 'a' => 0x1D68A], // Mathematical Monospace
        ];

        $output = '';
        $chars = preg_split('//u', $input, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($chars as $ch) {
            $code = IntlChar::ord($ch);
            $converted = null;

            foreach ($blocks as $block) {
                if ($code >= $block['A'] && $code < $block['A'] + 26) {
                    $converted = chr($code - $block['A'] + ord('A'));
                    break;
                }
                if ($code >= $block['a'] && $code < $block['a'] + 26) {
                    $converted = chr($code - $block['a'] + ord('a'));
                    break;
                }
            }

            if ($converted !== null) {
                $output .= $converted;
                continue;
            }

            // Emojis
            if (
                ($code >= 0x1F300 && $code <= 0x1FAFF) ||
                ($code >= 0x2600 && $code <= 0x26FF) ||
                ($code >= 0x2700 && $code <= 0x27BF)
            ) {
                $output .= $ch;
                continue;
            }

            // ASCII et accentués classiques
            if ($code <= 0x024F) {
                $output .= $ch;
                continue;
            }
        }

        return $output;
    }
}
