<?php
if (option('pechente.kirby-admin-bar.active') !== true) return;

use Kirby\Filesystem\F;
use Kirby\Panel\Panel;

$user = kirby()->user()
?>

<style>
<?= F::read(dirname(__DIR__) . '/assets/admin-bar.css') ?>
</style>

<div class="admin-bar">
    <?php
    /**
     * @var Kirby\Cms\Page $page
     */
    $panelLanguage = $user->language();
    $siteLanguage = kirby()->language();
    kirby()->setCurrentTranslation($panelLanguage);
    $roleTitle = $user->role()?->title();
    $userName = $user->name()->or($user->username());
    $avatar = $user->avatar();
    $pageEditLink = $page->panelUrl()->or($page->panel()->url());
    $permissions = $user->role()->permissions();
    $visiblePanelAreas = array_filter(Panel::areas(), function($panelArea) use ($permissions) {
        return $panelArea['menu'] && $permissions->for('access', $panelArea['id'], true);
    });
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
