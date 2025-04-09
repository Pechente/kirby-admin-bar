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
            if ($contentType !== 'html') return;

            // find last occurrence of end tag
            $pos = strripos($html, '</body>');
            if ($pos === false) $pos = strripos($html, '</html>');

            // insert snippet before end tag
            if ($pos !== false) {
                $bar = snippet('admin-bar', [], true);
                return substr_replace($html, $bar, $pos, 0);
            }
        },
    ],
]);
