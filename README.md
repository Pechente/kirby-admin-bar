# Kirby Admin Bar

Kirby Admin Bar provides a simple admin bar for your frontend. It allows logged-in users to quickly edit the current page and navigate to other important pages of the panel.

![](https://github.com/user-attachments/assets/c1e31edd-81dc-441e-88af-ab9d2b718f93)

Please note that this plugin might disable Kirby staticache since it renders different content for logged-in users and guests.

****

## Installation

### Download

Download and copy this repository to `/site/plugins/kirby-admin-bar`.

### Git submodule

```sh
git submodule add https://github.com/pechente/kirby-admin-bar.git site/plugins/kirby-admin-bar
```

### Composer

```sh
composer require pechente/kirby-admin-bar
```

## Setup

Install the plugin and enable it in your config.php:

```php
return [
    'ready' => function ($kirby) {
        return [
            'pechente.kirby-admin-bar' => [
                'active' => $kirby->user() !== null
            ]
        ];
    },
]
```

Enabling the plugin this way will automatically insert it before the closing body tag for logged-in users.

By default, the admin bar has a fixed position which might cause it to overlap your header / content. Kirby Admin Bar provides a CSS variable to offset your header accordingly, i.e.:

```css
.header {
  position: fixed;
  top: var(--admin-bar--height, 0);
  /* ... */
}
```

Additionally, Admin Bar relies on a bunch of CSS variables that you can overwrite to customize the look:

```css
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
```

### Dealing with virtual pages

The edit link will not work for virtual pages. In these cases, you can disable the edit button by returning the field `disableEditButton` from the page, i.e.:

```php
'routes' => [
    [
        'pattern' => 'virtual',
        'action' => function () {
            return Page::factory([
                // ...
                'content' => [
                    // ...
                    'disableEditButton' => true, // Will disable the edit button
                ]
            ]);
        }
    ]
]
```

Sometimes however, your virtual page is being fed by another page like a settings page. In this case, you can overwrite the URL of the edit link instead of disabling it completely, like so:

```php
'routes' => [
    [
        'pattern' => 'virtual',
        'action' => function () {
            return Page::factory([
                // ...
                'content' => [
                    // ...
                    'panelUrl' => Panel::url('settings') // Set a custom link here - could even be an external URL
                ]
            ]);
        }
    ]
]
```

Both `panelUrl` and `disableEditButton` are read as page fields and can therefore even be created as regular fields in a blueprint so can fill them out through the panel or create them as pre-filled hidden fields.

## License

[MIT License](LICENSE.md)

## Credits

- [René Henrich](https://github.com/Pechente)
