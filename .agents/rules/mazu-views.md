---
trigger: always_on
---

---

name: mazu-views
description: Mazu Framework - View Patterns (Layouts, Auto-Discovery, SPA)

---

# Mazu Framework - View Patterns

## 🎨 `$response->renderPage()` Method

```php
public function renderPage(array $props = [], array $options = []): View
```

**Parameters:**

- `$props` (array) - Data yang dikirim ke view
- `$options` (array) - Opsi dengan keys:
  - `'path'` (string|null) - Custom path ke view file
  - `'meta'` (array) - SEO meta tags

## 📝 Usage Examples

### Basic Usage (Auto-detect path)

```php
// Route: /admin/schools → View: addon/Views/(app)/admin/schools/index.php
public function schools(Request $request, Response $response): View | RedirectResponse {
    return $response->renderPage(['schools' => $schools]);
}
```

### Dengan Meta Tags (SEO)

```php
public function index(Request $request, Response $response): View | RedirectResponse {
    return $response->renderPage(
        ['users' => $users],
        [
            'meta' => [
                'title' => 'Daftar Pengguna - Admin Panel',
                'description' => 'Halaman pengelolaan data pengguna sistem',
                'robots' => 'index, follow'
            ]
        ]
    );
}
```

### Lengkap (Props + Path + Meta)

```php
public function show(Request $request, Response $response): View | RedirectResponse {
    $id = $request->param('id');
    $user = $this->userModel->find($id);

    return $response->renderPage(
        ['user' => $user],
        [
            'path' => '(app)/admin/users/detail',
            'meta' => [
                'title' => e($user['name']) . ' - Detail Pengguna',
                'canonical' => getBaseUrl('/admin/users/' . $id)
            ]
        ]
    );
}
```

## 🏗️ Nested Layout System (Next.js Style)

```
addon/Views/
├── layout.php           # Root layout (HTML wrapper, head, body)
├── (app)/layout.php     # Group layout (sidebar, header, main)
├── (app)/index.php      # View: homepage
└── (app)/admin/
    ├── layout.php       # Layout khusus admin (opsional)
    └── schools/
        ├── index.php    # View: /admin/schools
        └── [id].php     # View: /admin/schools/:id
```

**Behavior:**

1. **Bottom-Up Rendering:** ViewService berjalan dari view file ke atas
2. **Variabel `$children`:** Setiap layout menerima HTML dari level bawah
3. **Variabel `$meta`:** PageMeta tersedia di semua layout
4. **SPA Negotiation:** Layout yang sudah ada di client tidak di-render ulang

## 🔍 View Auto-Discovery System

### CSS/JS Auto-Discovery

```
addon/Views/(app)/admin/schools/
├── index.php    → index.css, index.js (jika ada)
├── create.php   → create.css, create.js (jika ada)
└── style.css    → Auto-discover untuk semua view di folder ini
└── script.js    → Auto-discover untuk semua view di folder ini
```

**Climbing Discovery:**

- ViewService mencari CSS/JS dengan nama yang sama
- Kemudian naik ke atas mencari `style.css` / `script.js` di folder induk
- CSS/JS di-load dari parent ke child (urutan terbalik)

### Manual Register

```php
View::addStyle('(app)/admin/custom.css');
View::addScript('(app)/admin/custom.js');
```

## 🚀 SPA Navigation dengan `data-spa`

### Link Navigation

```html
<a data-spa href="/admin/schools">Daftar Sekolah</a>
```

### Form Submission

```html
<form data-spa action="/users" method="POST">
  <!-- form fields -->
</form>
```

**Behavior:**

1. Link/form dengan `data-spa` dicegat oleh SPA engine
2. Request dikirim dengan header `X-SPA-Request: true`
3. Server response JSON: `{ html, meta, layout, styles }`
4. Frontend update container tanpa reload penuh

**ProgressBar:** ID `#global-progress-bar`

## 📄 View File Standards

```php
<?php
/**
 * @var array $schools      // Type hint untuk props
 * @var string $keyword
 */
?>

<div class="page-container">
    <div class="page-header">
        <h1><?= e($title) ?></h1>
    </div>

    <div class="page-content">
        <!-- Your content here -->
    </div>
</div>
```

**Penting:**

- Gunakan `@var` comments untuk type hint props
- Selalu escape output dengan `e($value)` untuk XSS prevention
- View path otomatis dari route pattern jika tidak specified

## 🎯 Route dengan Parameter Dinamis - Format `[param]`

Untuk route `:id`, view path menggunakan format **`[param]`** seperti Next.js.

```
Route: /admin/schools/:id
View: addon/Views/(app)/admin/schools/[id].php

Route: /admin/schools/:id/edit
View: addon/Views/(app)/admin/schools/[id]/edit.php

Route: /users/:userId/posts/:postId
View: addon/Views/(app)/users/[userId]/posts/[postId].php
```

**Penting:**

- Route `:param` → View path `[param]`
- Bisa berupa file `[id].php` atau folder `[id]/index.php`
- CSS/JS auto-discovery tetap berfungsi: `[id].css`, `[id].js`

## 📋 PageMeta - Supported Fields

```php
[
    // Basic SEO
    'title' => 'Page Title',           // Required, default: APP_NAME
    'description' => 'Page description',
    'keywords' => 'keyword1, keyword2',
    'robots' => 'index, follow',       // default: 'index, follow'
    'canonical' => 'https://example.com/page',
    'image' => '/images/og-image.jpg',
    'type' => 'website',               // default: 'website'

    // Open Graph / Facebook
    'og:type' => 'article',
    'og:title' => 'Custom OG Title',
    'og:description' => 'Custom OG Description',
    'og:image' => '/images/og-image.jpg',
    'og:url' => 'https://example.com/page',
    'og:site_name' => 'Site Name',
    'og:locale' => 'id_ID',

    // Twitter Cards
    'twitter:card' => 'summary_large_image',
    'twitter:site' => '@twitter_handle',
    'twitter:creator' => '@creator_handle',
    'twitter:title' => 'Twitter Title',
    'twitter:description' => 'Twitter Description',
    'twitter:image' => '/images/twitter-image.jpg'
]
```

## 📚 Related Skills

- [`mazu-core`](../mazu-core/SKILL.md) - Core reference & CLI
- [`mazu-controller`](../mazu-controller/SKILL.md) - Controller patterns
