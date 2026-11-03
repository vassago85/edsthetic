<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class RobotsController extends Controller
{
    public function __invoke(): Response
    {
        $lines = [
            '# Public pages may be indexed. The admin area may not.',
            '# LLM summary: '.url('/llms.txt'),
            '',
        ];

        foreach ([
            '*',
            'GPTBot',
            'ChatGPT-User',
            'OAI-SearchBot',
            'ClaudeBot',
            'Claude-SearchBot',
            'anthropic-ai',
            'PerplexityBot',
            'Google-Extended',
            'Applebot-Extended',
        ] as $agent) {
            $lines[] = 'User-agent: '.$agent;
            $lines[] = 'Allow: /';
            $lines[] = 'Disallow: /admin';
            $lines[] = '';
        }

        $lines[] = 'Sitemap: '.url('/sitemap.xml');
        $lines[] = '';

        return response(implode("\n", $lines), 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }
}
