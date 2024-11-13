<?php
$name ??= null;
$panelIcons = svg($kirby->root('kirby') . '/panel/dist/img/icons.svg');

if ($panelIcons && $name) {
    if (preg_match('/<symbol[^>]*id="icon-' . $name . '"[^>]*viewBox="(.*?)"[^>]*>(.*?)<\/symbol>/s', $panelIcons, $matches)) {

        if (preg_match('/<use href="#icon-(.*?)"[^>]*?>/s', $matches[2], $use)) {
            return icon($use[1]);
        }


        echo '<svg class="k-icon" data-type="' . $name . '" xmlns="http://www.w3.org/2000/svg" viewBox="' . $matches[1] . '">' . $matches[2] . '</svg>';
    }
}
