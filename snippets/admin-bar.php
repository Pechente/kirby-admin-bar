<?php
if (option('pechente.kirby-admin-bar.active') !== true) return;

use Kirby\Filesystem\F;
use Kirby\Panel\Menu;
use Kirby\Panel\Panel;

$user = kirby()->user();

$isInPreview = !!get('_preview');

$panelLanguage = $user->language();
$siteLanguage = kirby()->language();
kirby()->setCurrentTranslation($panelLanguage);
$roleTitle = $user->role()?->title();
$userName = $user->name()->or($user->username());
$avatar = $user->avatar();
$pageEditLink = $page->panelUrl()->or($page->panel()->url());
$menuEntries = [];

// keep separators but avoid leading or doubled ones
$addSeparator = function () use (&$menuEntries) {
    if ($menuEntries !== [] && end($menuEntries) !== '-') {
        $menuEntries[] = '-';
    }
};

// Instance-based Panel API (Kirby 6+): kirby()->panel() returns a Panel object
// and its menu yields Ui\Button objects. Otherwise (Kirby 4/5): static Panel + Menu::entry() arrays.
$hasPanelInstance = method_exists(kirby(), 'panel');

if ($hasPanelInstance) {
    $panel = kirby()->panel();
    $panelUrl = fn (string $path) => $panel->url($path);

    // pass '' as current area: the frontend is no Panel area, and user-defined
    // `current` callbacks must not mark any entry as active
    foreach ($panel->menu('')->items() as $item) {
        if ($item === '-') {
            $addSeparator();
            continue;
        }

        $props = $item->props();
        $id = $item->attrs['id'] ?? null;

        // the admin bar renders account/logout itself (dropdown);
        // skip hidden/disabled entries and ones without a link (dialogs/drawers)
        if (
            in_array($id, ['changes', 'account', 'logout'], true) ||
            empty($props['link']) ||
            ($props['disabled'] ?? false)
        ) {
            continue;
        }

        $menuEntries[] = $props;
    }
} else {
    $panelUrl = fn (string $path) => Panel::url($path);
    $permissions = $user->role()->permissions();

    // pass '' as current area: the frontend is no Panel area, and user-defined
    // `current` callbacks must not mark any entry as active
    $menu = new Menu(Panel::areas(), $permissions->toArray(), '');

    foreach ($menu->areas() as $area) {
        if ($area === '-') {
            $addSeparator();
            continue;
        }

        $entry = $menu->entry($area);

        // skip hidden/disabled entries and ones without a link (dialogs/drawers)
        if ($entry === false || empty($entry['link']) || ($entry['disabled'] ?? false)) {
            continue;
        }

        $menuEntries[] = $entry;
    }
}
// drop trailing separator
if (end($menuEntries) === '-') {
    array_pop($menuEntries);
}
$kirbyMajorVersion = substr(kirby()->version(), 0, 1);
$supportsDarkMode = $kirbyMajorVersion > 4;
// same choices and fallback as the Panel's own theme switcher
$themeOptions = [
    'light'  => ['icon' => 'sun', 'label' => t('theme.light')],
    'dark'   => ['icon' => 'moon', 'label' => t('theme.dark')],
    'system' => ['icon' => 'wand', 'label' => t('theme.automatic')],
];
$defaultTheme = $supportsDarkMode ? option('panel.theme', 'system') : 'light';
$nonce = option('pechente.kirby-admin-bar.nonce', true) ? kirby()->nonce() : null;
?>

<?php if (!$isInPreview): ?>
    <style<?= $nonce ? ' nonce="' . $nonce . '"' : '' ?>>
        <?= F::read(dirname(__DIR__) . '/assets/admin-bar.css') ?>
    </style>

    <div class="admin-bar">
        <div class="admin-bar__links">
            <?php if (!$page->disableEditButton()->toBool()): ?>
                <a href="<?= $pageEditLink ?>" class="admin-bar__link admin-bar__link--highlight">
                    <?php snippet('panel-icon', ['name' => 'edit']) ?>
                    <?= t('edit') ?>
                </a>
            <?php endif ?>
            <?php foreach ($menuEntries as $menuEntry): ?>
                <?php if ($menuEntry === '-'): ?>
                    <div class="admin-bar__separator"></div>
                <?php else: ?>
                    <a href="<?= $panelUrl($menuEntry['link']) ?>" class="admin-bar__link">
                        <?php if ($menuEntry['icon'] ?? null) snippet('panel-icon', ['name' => $menuEntry['icon']]) ?>
                        <?= $menuEntry['text'] ?>
                    </a>
                <?php endif ?>
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
                <?php if ($supportsDarkMode): ?>
                    <div class="admin-bar__theme" role="group" aria-label="<?= t('theme') ?>">
                        <?php foreach ($themeOptions as $value => $option): ?>
                            <button type="button"
                                    class="admin-bar__theme-option"
                                    data-theme="<?= $value ?>"
                                    aria-pressed="false"
                                    aria-label="<?= $option['label'] ?>"
                                    title="<?= $option['label'] ?>">
                                <?php snippet('panel-icon', ['name' => $option['icon']]) ?>
                            </button>
                        <?php endforeach ?>
                    </div>
                <?php endif ?>
                <a class="admin-bar__dropdown-link"
                   href="<?= kirby()->user()->panel()->url() ?>">
                    <?php snippet('panel-icon', ['name' => 'user']) ?>
                    <?= t('view.account') ?>
                </a>
                <?php foreach ($menuEntries as $menuEntry): ?>
                    <?php if ($menuEntry === '-'): ?>
                        <div class="admin-bar__dropdown-separator"></div>
                    <?php else: ?>
                        <a href="<?= $panelUrl($menuEntry['link']) ?>" class="admin-bar__dropdown-link">
                            <?php if ($menuEntry['icon'] ?? null) snippet('panel-icon', ['name' => $menuEntry['icon']]) ?>
                            <?= $menuEntry['text'] ?>
                        </a>
                    <?php endif ?>
                <?php endforeach; ?>
                <a class="admin-bar__dropdown-link"
                   href="<?= $panelUrl('logout') ?>">
                    <?php snippet('panel-icon', ['name' => 'logout']) ?>
                    <?= t('logout') ?>
                </a>
            </div>
        </div>
    </div>

    <script<?= $nonce ? ' nonce="' . $nonce . '"' : '' ?>>
        (() => {
            // shares the Panel's storage key, so a choice made here applies
            // in the Panel too (and vice versa)
            const storageKey = 'kirby$theme';
            const defaultTheme = <?= json_encode($defaultTheme) ?>;
            const adminBar = document.querySelector('.admin-bar');
            const options = adminBar.querySelectorAll('.admin-bar__theme-option');

            const read = () => {
                try {
                    return localStorage.getItem(storageKey) || defaultTheme;
                } catch (e) {
                    return defaultTheme;
                }
            };

            const apply = (setting) => {
                // CSS calls the system setting "auto"
                const themeClass = setting === 'system' ? 'auto' : setting;
                adminBar.classList.remove('admin-bar--theme-auto', 'admin-bar--theme-light', 'admin-bar--theme-dark');
                adminBar.classList.add(`admin-bar--theme-${themeClass}`);
                options.forEach((option) => {
                    option.setAttribute('aria-pressed', String(option.dataset.theme === setting));
                });
            };

            options.forEach((option) => {
                option.addEventListener('click', () => {
                    try {
                        localStorage.setItem(storageKey, option.dataset.theme);
                    } catch (e) {}
                    apply(option.dataset.theme);
                });
            });

            // stay in sync when the theme is changed in the Panel in another tab
            window.addEventListener('storage', (event) => {
                if (event.key === storageKey) apply(read());
            });

            apply(read());

            // fade out links cut off by the horizontal overflow, but only on
            // the side(s) where there is actually more to scroll to
            const links = adminBar.querySelector('.admin-bar__links');
            const updateFade = () => {
                const maxScroll = links.scrollWidth - links.clientWidth;
                links.classList.toggle('admin-bar__links--fade-start', links.scrollLeft > 1);
                links.classList.toggle('admin-bar__links--fade-end', maxScroll - links.scrollLeft > 1);
            };
            links.addEventListener('scroll', updateFade, {passive: true});
            // observe the children too: their widths change e.g. when web
            // fonts load or links get hidden, without the container resizing
            const resizeObserver = new ResizeObserver(updateFade);
            [links, ...links.children].forEach((el) => resizeObserver.observe(el));
            updateFade();
        })();
    </script>

    <?php kirby()->setCurrentTranslation($siteLanguage); ?>
<?php endif ?>
