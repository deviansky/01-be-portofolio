<?php

namespace App\Services;

use DOMDocument;
use DOMXPath;
use Stevebauman\Purify\Facades\Purify;

class ProjectHtmlSanitizer
{
    public static function clean(?string $html): ?string
    {
        if ($html === null || trim($html) === '') {
            return $html;
        }

        // 1. Purify basic allowlist filtering
        $cleaned = Purify::config('project')->clean($html);

        if (empty(trim($cleaned))) {
            return '';
        }

        // 2. DOM Document parsing for strict link & img rules
        libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $doc->loadHTML('<?xml encoding="utf-8" ?>' . $cleaned, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $xpath = new DOMXPath($doc);

        // Remove style, class, id attributes on all elements
        $allNodes = $xpath->query('//*');
        if ($allNodes) {
            foreach ($allNodes as $node) {
                if ($node instanceof \DOMElement) {
                    if ($node->hasAttribute('style')) {
                        $node->removeAttribute('style');
                    }
                    if ($node->hasAttribute('class')) {
                        $node->removeAttribute('class');
                    }
                    if ($node->hasAttribute('id')) {
                        $node->removeAttribute('id');
                    }
                }
            }
        }

        // Process <a> links: rel="noopener noreferrer" target="_blank"
        $links = $doc->getElementsByTagName('a');
        foreach ($links as $link) {
            if ($link instanceof \DOMElement) {
                $link->setAttribute('target', '_blank');
                $link->setAttribute('rel', 'noopener noreferrer');
            }
        }

        // Process <img> elements: remove if src is not local storage
        $images = [];
        foreach ($doc->getElementsByTagName('img') as $img) {
            if ($img instanceof \DOMElement) {
                $images[] = $img;
            }
        }

        $appUrl = config('app.url', 'http://localhost:8000');
        foreach ($images as $img) {
            $src = $img->getAttribute('src');
            $isLocalStorage = str_contains($src, '/storage/') || (
                !empty($appUrl) && str_starts_with($src, $appUrl)
            );

            if (!$isLocalStorage) {
                $img->parentNode?->removeChild($img);
            }
        }

        $result = $doc->saveHTML();
        $result = preg_replace('/^<\?xml encoding="utf-8" \?>\n?/', '', $result);
        return trim($result);
    }
}
