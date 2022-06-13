<?php

declare(strict_types=1);

namespace Kaiju\Html2Text;

class Html2Text
{
    /**
     * @var callable
     */
    private $preprocessing = null;

    /**
     * @var callable
     */
    private $tagreplacement = null;

    /**
     * @var callable
     */
    private $postprocessing = null;

    public function convert(string $html): string
    {
        $text = $html;

        if (null !== $this->preprocessing && is_callable($this->preprocessing)) {
            $text = $this->preprocessing($text);
        }

        // replace line breaks
        $text = preg_replace("/\n|\r\n|\r/", ' ', $text);

        // replace spaces
        $text = preg_replace('/&nbsp;/i', ' ', $text);

        // remove content in script tags.
        $text = preg_replace('%<\s*script[^>]*>[\s\S]*?</script>%im', '', $text);

        // remove content in style tags.
        $text = preg_replace('%<\s*style[^>]*>[\s\S]*?</style>%im', '', $text);

        // remove content in comments.
        $text = preg_replace('/<!--.*?-->/m', '', $text);

        // remove !DOCTYPE
        $text = preg_replace('/<!DOCTYPE.*?>/i', '', $text);

        if (null !== $this->tagreplacement && is_callable($this->tagreplacement)) {
            $text = $this->tagreplacement($text);
        }

        $doubleNewlineTags = ['p', 'h[1-6]', 'dl', 'dt', 'dd', 'ol', 'ul',
            'dir', 'address', 'blockquote', 'center', 'hr', 'pre', 'form',
            'textarea', 'table',
        ];
        $singleNewlineTags = ['div', 'li', 'fieldset', 'legend', 'tr', 'th', 'caption',
            'thead', 'tbody', 'tfoot',
        ];

        foreach ($doubleNewlineTags as $tag) {
            $text = preg_replace('%</?\s*' . $tag . '[^>]*>%i', "\n\n", $text);
        }

        foreach ($singleNewlineTags as $tag) {
            $text = preg_replace('%<\s*' . $tag . '[^>]*>%i', "\n\n", $text);
        }

        // Replace <br> and <br/> with a single newline
        $text = preg_replace('%<\s*br[^>]*/?\s*>%i', "\n", $text);

        // Remove all remaining tags.
        $text = preg_replace('/(<([^>]+)>)/', '', $text);

        // Trim rightmost whitespaces for all lines
        $text = preg_replace("/([^\n\\S]+)\n/", "\n", $text);
        $text = preg_replace("/([^\n\\S]+)$/", '', $text);

        // Make sure there are never more than two consecutive linebreaks.
        $text = preg_replace("/\n{2,}/", "\n\n", $text);

        // Remove newlines at the beginning of the text.
        $text = preg_replace("/^\n+/", '', $text);

        // Remove newlines at the end of the text.
        $text = preg_replace("/\n+$/", '', $text);

        // Decode HTML entities.
        $text = preg_replace_callback('/&([^;]+);/', fn($m) => html_entity_decode($m[0]), $text);

        if (null !== $this->postprocessing && is_callable($this->postprocessing)) {
            $text = $this->postprocessing($text);
        }

        // return $text;
        return self::processWhitespaceNewlines($text);
    }

    /**
     * Unify newlines; in particular, \r\n becomes \n, and
     * then \r becomes \n. This means that all newlines (Unix, Windows, Mac)
     * all become \ns.
     */
    public static function fixNewlines(string $text): string
    {
        // replace \r\n to \n
        $text = str_replace("\r\n", "\n", $text);
        // remove \rs
        $text = str_replace("\r", "\n", $text);

        return $text;
    }

    /**
     * Remove leading or trailing spaces and excess empty lines from provided multiline text.
     */
    public static function processWhitespaceNewlines(string $text): string
    {
        // remove excess spaces around tabs
        $text = preg_replace("/ *\t */m", "\t", $text);

        // remove leading whitespace
        $text = ltrim($text);

        // remove leading spaces on each line
        $text = preg_replace("/\n[ \t]*/m", "\n", $text);

        // convert non-breaking spaces to regular spaces to prevent output issues,
        // do it here so they do NOT get removed with other leading spaces, as they
        // are sometimes used for indentation
        $text = static::renderText($text);

        // remove trailing whitespace
        $text = rtrim($text);

        // remove trailing spaces on each line
        $text = preg_replace("/[ \t]*\n/m", "\n", $text);

        // unarmor pre blocks
        $text = static::fixNewLines($text);

        // remove unnecessary empty lines
        $text = preg_replace("/\n\n+/m", "\n\n", $text);

        // merge blank spaces
        $text = preg_replace("/[ \t]{2,}/", ' ', $text);

        return $text;
    }

    public static array $nbspCodes = ["\xc2\xa0", '\\u00a0'];

    public static array $zwnjCodes = ["\xe2\x80\x8c", '\\u200c'];

    /**
     * Replace any special characters with simple text versions, to prevent output issues:
     * - Convert non-breaking spaces to regular spaces; and
     * - Convert zero-width non-joiners to '' (nothing).
     */
    public static function renderText($text): string
    {
        $text = str_replace(static::$nbspCodes, ' ', $text);
        $text = str_replace(static::$zwnjCodes, '', $text);

        return $text;
    }

    public function setPostprocessing(callable $postprocessing): void
    {
        $this->postprocessing = $postprocessing;
    }

    public function setTagreplacement(callable $tagreplacement): void
    {
        $this->tagreplacement = $tagreplacement;
    }

    public function setPreprocessing(callable $preprocessing): void
    {
        $this->preprocessing = $preprocessing;
    }
}
