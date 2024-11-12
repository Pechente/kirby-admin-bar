<?php

Kirby::plugin('pechente/kirby-admin-bar', [
    'snippets' => [
        'admin-bar' => __DIR__ . '/snippets/admin-bar.php',
        'panel-icon' => __DIR__ . '/snippets/panel-icon.php',
    ],
    'options' => [
        'active' => false,
    ],
    'hooks' => [
        'page.render:after' => function ($contentType, $data, $html) {
            if ($contentType === 'html') {
                $html = str_replace('<body>', '<body>' . snippet('admin-bar', [], true), $html);
            }
            return $html;
        },
    ],
]);
