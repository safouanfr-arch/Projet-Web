<?php

function sanitize_article_html(string $html): string
{
    $html = trim($html);
    if ($html === '') {
        return '';
    }

    if (!class_exists('DOMDocument')) {
        return nl2br(htmlspecialchars($html, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));
    }

    $allowedTags = [
        'p' => [],
        'br' => [],
        'strong' => [],
        'b' => [],
        'em' => [],
        'i' => [],
        'ul' => [],
        'ol' => [],
        'li' => [],
        'h2' => [],
        'h3' => [],
        'h4' => [],
        'blockquote' => [],
        'a' => ['href', 'title', 'target', 'rel'],
        'img' => ['src', 'alt', 'title'],
    ];

    $previousErrors = libxml_use_internal_errors(true);

    $dom = new DOMDocument('1.0', 'UTF-8');
    $wrapper = '<div id="sanitizer-root">' . $html . '</div>';
    $dom->loadHTML(
        '<?xml encoding="UTF-8">' . $wrapper,
        LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
    );

    libxml_clear_errors();
    libxml_use_internal_errors($previousErrors);

    $root = $dom->getElementById('sanitizer-root');
    if (!$root instanceof DOMElement) {
        return nl2br(htmlspecialchars($html, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));
    }

    sanitize_article_html_node($root, $allowedTags);

    return sanitize_article_inner_html($root);
}

function sanitize_article_html_node(DOMNode $node, array $allowedTags): void
{
    for ($child = $node->firstChild; $child !== null;) {
        $next = $child->nextSibling;

        if ($child->nodeType === XML_COMMENT_NODE) {
            $node->removeChild($child);
            $child = $next;
            continue;
        }

        if ($child->nodeType !== XML_ELEMENT_NODE) {
            $child = $next;
            continue;
        }

        $tag = strtolower($child->nodeName);
        if (!array_key_exists($tag, $allowedTags)) {
            if (in_array($tag, ['script', 'style', 'iframe', 'object', 'embed', 'link', 'meta'], true)) {
                $node->removeChild($child);
                $child = $next;
                continue;
            }

            while ($child->firstChild !== null) {
                $node->insertBefore($child->firstChild, $child);
            }
            $node->removeChild($child);
            $child = $next;
            continue;
        }

        if ($child instanceof DOMElement) {
            sanitize_article_html_attributes($child, $allowedTags[$tag]);
        }

        sanitize_article_html_node($child, $allowedTags);
        $child = $next;
    }
}

function sanitize_article_html_attributes(DOMElement $element, array $allowedAttributes): void
{
    $toRemove = [];
    foreach ($element->attributes as $attribute) {
        $name = strtolower($attribute->nodeName);
        if (!in_array($name, $allowedAttributes, true)) {
            $toRemove[] = $name;
        }
    }

    foreach ($toRemove as $attributeName) {
        $element->removeAttribute($attributeName);
    }

    $tag = strtolower($element->tagName);

    if ($tag === 'a' && $element->hasAttribute('href')) {
        $href = trim($element->getAttribute('href'));
        if (!sanitize_article_is_safe_url($href, ['http', 'https', 'mailto'])) {
            $element->removeAttribute('href');
        } elseif ($element->getAttribute('target') === '_blank') {
            $element->setAttribute('rel', 'noopener noreferrer');
        }
    }

    if ($tag === 'img' && $element->hasAttribute('src')) {
        $src = trim($element->getAttribute('src'));
        if (!sanitize_article_is_safe_url($src, ['http', 'https'])) {
            $element->removeAttribute('src');
        }
    }
}

function sanitize_article_is_safe_url(string $url, array $allowedSchemes): bool
{
    if ($url === '') {
        return false;
    }

    if (
        str_starts_with($url, '/') ||
        str_starts_with($url, './') ||
        str_starts_with($url, '../') ||
        str_starts_with($url, '#')
    ) {
        return true;
    }

    $scheme = strtolower((string)parse_url($url, PHP_URL_SCHEME));
    if ($scheme === '') {
        return !preg_match('/^[a-z][a-z0-9+\-.]*:/i', $url);
    }

    return in_array($scheme, $allowedSchemes, true);
}

function sanitize_article_inner_html(DOMElement $element): string
{
    $html = '';
    foreach ($element->childNodes as $child) {
        $html .= $element->ownerDocument->saveHTML($child);
    }
    return $html;
}
