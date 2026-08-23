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
$permissions = $user->role()->permissions();
$menu = new Menu(Panel::areas(), $permissions->toArray());
$menuEntries = [];
foreach ($menu->areas() as $area) {
    // keep separators but avoid leading or doubled ones
    if ($area === '-') {
        if ($menuEntries !== [] && end($menuEntries) !== '-') {
            $menuEntries[] = '-';
        }
        continue;
    }

    $entry = $menu->entry($area);

    // skip hidden/disabled entries and ones without a link (dialogs/drawers)
    if ($entry === false || empty($entry['link']) || ($entry['disabled'] ?? false)) {
        continue;
    }

    $menuEntries[] = $entry;
}
// drop trailing separator
if (end($menuEntries) === '-') {
    array_pop($menuEntries);
}
$kirbyMajorVersion = substr(kirby()->version(), 0, 1);
$supportsDarkMode = $kirbyMajorVersion > 4;
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
                    <a href="<?= Panel::url($menuEntry['link']) ?>" class="admin-bar__link">
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
                <a class="admin-bar__dropdown-link"
                   href="<?= kirby()->user()->panel()->url() ?>">
                    <?php snippet('panel-icon', ['name' => 'user']) ?>
                    <?= t('view.account') ?>
                </a>
                <?php foreach ($menuEntries as $menuEntry): ?>
                    <?php if ($menuEntry === '-'): ?>
                        <div class="admin-bar__dropdown-separator"></div>
                    <?php else: ?>
                        <a href="<?= Panel::url($menuEntry['link']) ?>" class="admin-bar__dropdown-link">
                            <?php if ($menuEntry['icon'] ?? null) snippet('panel-icon', ['name' => $menuEntry['icon']]) ?>
                            <?= $menuEntry['text'] ?>
                        </a>
                    <?php endif ?>
                <?php endforeach; ?>
                <a class="admin-bar__dropdown-link"
                   href="<?= Panel::url('logout') ?>">
                    <?php snippet('panel-icon', ['name' => 'logout']) ?>
                    <?= t('logout') ?>
                </a>
            </div>
        </div>
    </div>

    <script<?= $nonce ? ' nonce="' . $nonce . '"' : '' ?>>
        const theme = localStorage.getItem('kirby$theme') || '<?= $supportsDarkMode ? "auto" : "light" ?>';
        const adminBar = document.querySelector('.admin-bar');
        adminBar.classList.add(`admin-bar--theme-${theme}`);
    </script>

    <?php kirby()->setCurrentTranslation($siteLanguage); ?>
<?php endif ?>
