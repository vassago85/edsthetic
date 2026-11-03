<?php

$html = file_get_contents('C:/Users/pchar/Downloads/esthetic-renovations-v2.html');
$html = preg_replace('/src="data:[^"]+"/', 'src="[IMAGE]"', $html);

// Extract CSS block length and key selectors
preg_match('/<style>(.*?)<\/style>/s', $html, $style);
$css = $style[1] ?? '';
file_put_contents(__DIR__.'/v2-style.css', $css);

// Body structure without images
$body = preg_replace('/<style>.*?<\/style>/s', '', $html);
$body = preg_replace('/<script>.*?<\/script>/s', '', $body);
$dom = new DOMDocument();
libxml_use_internal_errors(true);
$dom->loadHTML($body);
libxml_clear_errors();

$out = [];
$walk = function (DOMNode $node, int $depth = 0) use (&$walk, &$out): void {
    if ($node instanceof DOMElement) {
        $tag = strtolower($node->tagName);
        if (in_array($tag, ['section', 'header', 'footer', 'main', 'nav', 'aside', 'dialog'], true)
            || ($tag === 'div' && $node->hasAttribute('class') && preg_match('/\b(hero|owner|process|contact|care|form|facts|photo)/', $node->getAttribute('class')))) {
            $id = $node->getAttribute('id');
            $class = $node->getAttribute('class');
            $out[] = str_repeat('  ', $depth).'<'.$tag.($id ? ' id="'.$id.'"' : '').($class ? ' class="'.$class.'"' : '').'>';
        }
        if (in_array($tag, ['h1', 'h2', 'h3', 'p', 'li', 'summary', 'dt', 'dd', 'button', 'label', 'a'], true)) {
            $text = trim(preg_replace('/\s+/', ' ', $node->textContent));
            if ($text !== '' && strlen($text) < 220) {
                $out[] = str_repeat('  ', $depth).strtoupper($tag).': '.$text;
            }
            if (in_array($tag, ['h1', 'h2', 'h3', 'summary', 'dt', 'button', 'a', 'label'], true)) {
                return;
            }
            if ($tag === 'p' || $tag === 'li' || $tag === 'dd') {
                return;
            }
        }
    }
    foreach ($node->childNodes ?? [] as $child) {
        $walk($child, $depth + ($node instanceof DOMElement ? 1 : 0));
    }
};

$walk($dom->getElementsByTagName('body')->item(0));
file_put_contents(__DIR__.'/v2-structure.txt', implode("\n", $out));
echo 'css: '.strlen($css)." bytes\n";
echo 'lines: '.count($out)."\n";
