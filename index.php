<?php

/**
 * Kirby Admin Bar
 *
 * Adds an admin bar to the frontend in Kirby CMS so you can easily
 * edit the current page or access common panel pages 
 *
 * @package   Kirby Admin Bar
 * @author    René Henrich <contact@rene-henrich.de>
 * @link      https://github.com/Pechente/kirby-admin-bar
 * @copyright René Henrich
 * @license   https://opensource.org/licenses/MIT
 */

use Kirby\Cms\App as Kirby;

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
                $html = str_replace('</body>', snippet('admin-bar', [], true), $html) . '</body>';
            }
            return $html;
        },
    ],
]);
