<?php
if (option('pechente.kirby-admin-bar.active') !== true) return;

use Kirby\Panel\Panel;

$user = kirby()->user()
?>

<style>
    :root {
        --admin-bar--font-family: -apple-system, "system-ui", "Segoe UI", Roboto, Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
        --admin-bar--base-font-size: 14px;
        --admin-bar--base-line-height: 1.2;
        --admin-bar--height: 48px;
        --admin-bar--z-index: 110;
        --admin-bar--color: #2b2b2b;
        --admin-bar--color--hover: #1b1b1b;
        --admin-bar--background-color: #F0F0F0;
        --admin-bar--border-color: #ddd;
        --admin-bar--avatar-size: 32px;
        --admin-bar--border-radius: 4px;
        --admin-bar--position: fixed;
        --admin-bar--padding: 0 16px;
        --admin-bar--icon-size: 18px;
        --admin-bar--dropdown-border-radius: 4px;
    }

    .admin-bar {
        display: flex;
        align-items: center;
        font-family: var(--admin-bar--font-family);
        font-size: var(--admin-bar--base-font-size);
        line-height: var(--admin-bar--base-line-height);
        position: var(--admin-bar--position);
        top: 0;
        left: 0;
        width: 100%;
        min-height: var(--admin-bar--height);
        color: var(--admin-bar--color);
        background: var(--admin-bar--background-color);
        z-index: var(--admin-bar--z-index);
        padding: var(--admin-bar--padding);
        box-sizing: border-box;
        border-bottom: 1px solid var(--admin-bar--border-color);

        a {
            text-decoration: none;
            color: inherit;
        }

        a:hover {
            color: var(--admin-bar--color--hover);
        }
    }

    .admin-bar__avatar {
        width: var(--admin-bar--avatar-size);
        border-radius: var(--admin-bar--border-radius);
        height: auto;
        aspect-ratio: 1;
        object-fit: cover;
    }

    .admin-bar__user {
        min-width: 140px;
        position: relative;
        display: flex;
        align-items: center;
        gap: 8px;
        align-self: stretch;
        background: var(--admin-bar--background-color);

        svg {
            width: var(--admin-bar--icon-size);
        }
    }

    .admin-bar__user-name {
        flex: 1;
    }

    .admin-bar__user:hover, .admin-bar__user:focus, .admin-bar__user:focus-within {
        .admin-bar__dropdown {
            display: flex;
        }
    }

    .admin-bar__user-role {
        font-size: 0.875em;
    }

    .admin-bar__links {
        flex: 1;
        overflow: auto;
        display: flex;
        gap: 16px;

        -ms-overflow-style: none;
        scrollbar-width: none;

        &::-webkit-scrollbar {
            display: none;
        }
    }

    .admin-bar__link, .admin-bar__dropdown-link {
        display: flex;
        gap: 8px;
        padding: 7px;
        border-radius: 4px;

        svg {
            width: var(--admin-bar--icon-size);
        }
    }

    .admin-bar__link--highlight {
        color: #26330C;
        background-color: #D1E9A3;

        svg {
            fill: #82AD2B;
        }
    }

    .admin-bar__link--highlight:hover {
        background-color: #CBE29E;
    }

    .admin-bar__dropdown {
        width: 100%;
        display: none;
        flex-direction: column;
        position: absolute;
        right: 0;
        top: 100%;
        border: 1px solid var(--admin-bar--border-color);
        border-top: 0;
        border-radius: 0 0 var(--admin-bar--dropdown-border-radius) var(--admin-bar--dropdown-border-radius);
        background: var(--admin-bar--background-color);
    }

    .admin-bar__dropdown-link:not(:last-child):not(:first-child) {
        display: none;
    }

    @media screen and (max-width: 600px) {
        .admin-bar__link:not(:first-child) {
            display: none;
        }

        .admin-bar__dropdown-link:not(:last-child):not(:first-child) {
            display: flex;
        }
    }
</style>

<div class="admin-bar">
    <?php
    $panelLanguage = $user->language();
    $siteLanguage = kirby()->language();
    kirby()->setCurrentTranslation($panelLanguage);
    $roleTitle = $user->role()?->title();
    $userName = $user->name()->or($user->username());
    $avatar = $user->avatar();
    $pageEditLink = $page->panelUrl()->or($page->panel()->url());
    $status = $page->status();
    $panelAreas = Panel::areas();
    $visiblePanelAreas = array_filter($panelAreas, fn($panelArea) => $panelArea['menu']);
    ?>
    <div class="admin-bar__links">
        <?php if (!$page->disableEditButton()->toBool()): ?>
            <a href="<?= $pageEditLink ?>" class="admin-bar__link admin-bar__link--highlight">
                <?php snippet('panel-icon', ['name' => 'edit']) ?>
                <?= t('edit') ?>
            </a>
        <?php endif ?>
        <?php foreach ($visiblePanelAreas as $panelArea): ?>
            <a href="<?= Panel::url($panelArea['link']) ?>" class="admin-bar__link">
                <?php snippet('panel-icon', ['name' => $panelArea['icon']]) ?>
                <?= $panelArea['label'] ?>
            </a>
        <?php endforeach; ?>
    </div>
    <div class="admin-bar__user" tabindex="0">
        <?php if ($avatar): ?>
            <img class="admin-bar__avatar"
                 src="<?= $avatar->thumb(['width' => 64, 'height' => 64, 'crop' => true, 'quality' => 90])->url() ?>"
                 alt="<?= $userName ?> Avatar">
        <?php endif ?>
        <div class="admin-bar__user-name">
            <?= $userName ?>
            <div class="admin-bar__user-role">
                <?= $roleTitle ?>
            </div>
        </div>
        <?php snippet('panel-icon', ['name' => 'angle-down']) ?>
        <div class="admin-bar__dropdown">
            <a class="admin-bar__dropdown-link"
               href="<?= kirby()->user()->panel()->url() ?>">
                <?php snippet('panel-icon', ['name' => 'user']) ?>
                <?= t('view.account') ?>
            </a>
            <?php foreach ($visiblePanelAreas as $panelArea): ?>
                <a href="<?= Panel::url($panelArea['link']) ?>" class="admin-bar__dropdown-link">
                    <?php snippet('panel-icon', ['name' => $panelArea['icon']]) ?>
                    <?= $panelArea['label'] ?>
                </a>
            <?php endforeach; ?>
            <a class="admin-bar__dropdown-link"
               href="<?= Panel::url('logout') ?>">
                <?php snippet('panel-icon', ['name' => 'logout']) ?>
                <?= t('logout') ?>
            </a>
        </div>
    </div>
</div>
<?php kirby()->setCurrentTranslation($siteLanguage); ?>
