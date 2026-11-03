<?php

namespace App\Support;

use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Throwable;

class RichText
{
    public static function html(mixed $body): string
    {
        if (blank($body)) {
            return '';
        }

        try {
            return (string) RichContentRenderer::make($body)->toHtml();
        } catch (Throwable) {
            return strip_tags((string) $body, '<p><br><mark><strong><em><a><ul><ol><li>');
        }
    }
}
