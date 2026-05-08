---
name: mazu-fullstack-developer
description: Mazu Framework - Fullstack Developer Skill (Ringkas)
---

# Mazu Framework - Quick Reference

## 📁 Struktur Folder

```
project-root/
├── app/           # Core Engine - JANGAN MODIFIKASI
│   ├── Console/   # CLI Commands (php mazu)
│   └── Core/      # Framework Core
├── addon/         # Application Code - BOLEH DIMODIFIKASI
│   ├── Controllers/
│   ├── Middleware/
│   ├── Models/
│   ├── Providers/
│   ├── Router/index.php   # Route definitions
│   └── Views/             # View templates (nested layout)
└── config/
```

## 🛠️ CLI Commands - Gunakan Ini!

**Untuk membuat file baru, JANGAN manual - gunakan CLI:**

```bash
php mazu make:controller User    # Buat UserController di addon/Controllers/
php mazu make:model User         # Buat UserModel di addon/Models/
php mazu make:middleware Auth    # Buat AuthMiddleware di addon/Middleware/
php mazu make:job SendEmail      # Buat SendEmailJob
php mazu migrate                 # Jalankan migration
php mazu build                   # Build assets
php mazu serve                   # Dev server
```

**Template sudah ada di:** `app/Console/Commands/Make*.php`

## 🎯 Konsep Dasar

### 1. Routing (`addon/Router/index.php`)

**Format Route Dasar:**

```php
$router->get('/', fn(Request $r, Response $res) => $res->renderPage([]));
$router->get('/users', [UserController::class, 'index']);
$router->post('/users', [UserController::class, 'store'], ['auth', 'csrf']);
$router->group(['prefix' => 'api', 'middleware' => ['auth']], function($router) {
    $router->get('users', [ApiController::class, 'index']);
});
```

**Route dengan Parameter Dinamis:**

```php
// Gunakan format :param untuk parameter dinamis
$router->get('/users/:id', [UserController::class, 'show']);
$router->get('/users/:id/edit', [UserController::class, 'edit']);
$router->post('/users/:id', [UserController::class, 'update']);

// Multiple parameters
$router->get('/users/:userId/posts/:postId', [PostController::class, 'show']);

// Ambil parameter di controller menggunakan $request->param('id')
```

**Penting:**

- Gunakan format `:param` untuk parameter dinamis (contoh: `:id`, `:userId`)
- **BUKAN** format `{id}` atau `{param}`
- Parameter diambil di controller menggunakan `$request->param('paramName')`

### 2. Controller (gunakan `php mazu make:controller`)

**Struktur Dasar:**

```php
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\View\View;

class UserController {
    public function index(Request $request, Response $response): View | RedirectResponse {
        return $response->renderPage(['data' => $data], ['meta' => ['title' => 'Page']]);
    }
}
```

**Controller dengan Dependency Injection (Constructor Property Promotion):**

```php
use Addon\Models\UserModel;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\RedirectResponse;
use App\Core\View\View;

class UserController {
    public function __construct(
        private UserModel $userModel
    ) {}

    public function index(Request $request, Response $response): View | RedirectResponse {
        return $response->renderPage(['users' => $this->userModel->all()], ['meta' => ['title' => 'Page']]);
    }
}
```

**Mengambil Route Parameter:**

```php
public function show(Request $request, Response $response): View | RedirectResponse {
    // Gunakan $request->param() untuk mengambil parameter dinamis dari route
    $id = $request->param('id');
    $user = $this->userModel->find($id);

    if (!$user) {
        // Redirect dengan query string untuk error
        return $response->redirect('/users?error=404&message=' . urlencode('User tidak ditemukan'));
    }

    return $response->renderPage(['user' => $user], ['meta' => ['title' => 'Page']]);
}
```

**Transaction dengan Model:**

```php
public function store(Request $request, Response $response): View | RedirectResponse {
    try {
        $data = $request->input();

        // Validasi
        if (empty($data['name'])) {
            return $response->redirect('/users/create?error=400&message=' . urlencode('Nama wajib diisi'));
        }

        // Mulai transaksi dari model manapun
        $db = $this->userModel->getDb();
        $db->beginTransaction();

        try {
            $this->userModel->create($data);
            $this->profileModel->create([...]);

            $db->commit();
            return $response->redirect('/users');
        } catch (\Exception $e) {
            $db->rollBack();
            throw $e;
        }
    } catch (\Exception $e) {
        return $response->redirect('/users/create?error=500&message=' . urlencode($e->getMessage()));
    }
}
```

**Penting:**

- Setiap controller method **HANYA** memiliki 2 parameter: `(Request $request, Response $response)`
- Gunakan `$request->param('id')` untuk mengambil parameter dinamis dari route (BUKAN sebagai parameter function)
- Gunakan `$request->input()` untuk mendapatkan semua input data (BUKAN `all()`)
- Gunakan `$request->input('key')` untuk mendapatkan input spesifik
- Controller return `View | RedirectResponse` (tanpa namespace lengkap di type hint)
- Untuk redirect dengan error: `$response->redirect('/path?error=<code>&message=<pesan>')`
- Dependency injection melalui constructor property promotion
- Setiap method **WAJIB** menggunakan try-catch untuk error handling
- Untuk transaksi database, gunakan `$model->getDb()->beginTransaction()`, `commit()`, `rollBack()`

### 3. Model (gunakan `php mazu make:model`)

```php
class UserModel extends Model {
    protected string $table = 'users';
    protected array $schema = [...];
    public function findByEmail(string $email): ?array { ... }
}
```

### 4. Middleware (gunakan `php mazu make:middleware`)

**Cara Membuat:**

```bash
php mazu make:middleware Auth    # Buat AuthMiddleware
php mazu make:middleware Role    # Buat RoleMiddleware
php mazu make:middleware Csrf    # Buat CsrfMiddleware
```

**Struktur Dasar:**

```php
class AuthMiddleware implements MiddlewareInterface {
    public function handle($request, \Closure $next, array $params = []) {
        // Cek authorization
        if (!$authorized) {
            throw new AuthorizationException('Forbidden');
        }
        return $next($request);
    }
}
```

**Middleware dengan Dependency Injection:**

```php
class RoleMiddleware implements MiddlewareInterface {
    public function __construct(private SessionService $session) {}

    public function handle($request, \Closure $next, array $params = []) {
        // $params berisi array dari route: 'role:admin,user'
        $allowedRoles = $params;
        $userRole = $this->session->get('auth.user_role');

        if (!in_array($userRole, $allowedRoles)) {
            throw new AuthorizationException('Forbidden');
        }

        return $next($request);
    }
}
```

**Cara Kerja:**

1. **Auto-Discovery:** Middleware di-scan otomatis dari `addon/Middleware/` oleh `Kernel::getRouteMiddleware()`
2. **Alias Mapping:** `AuthMiddleware` → `auth`, `RoleMiddleware` → `role` (lowercase, tanpa suffix "Middleware")
3. **Pipeline:** Middleware dijalankan dalam pipeline (`array_reverse`) sebelum controller
4. **Parameter Parsing:** `role:admin,user` → alias=`role`, params=`['admin', 'user']`

**Konvensi Penamaan Alias:**

- Alias di-generate otomatis: lowercase + hapus suffix "Middleware"
- `AuthMiddleware` → `auth`
- `RoleMiddleware` → `role`
- `CsrfMiddleware` → `csrf`
- `MyCustomMiddleware` → `mycustom`

**PENTING:** Gunakan alias lowercase tanpa hyphen.

**Penggunaan di Route:**

```php
// Single middleware
$router->get('/admin', [AdminController::class, 'index'], ['auth']);

// Multiple middleware
$router->group(['middleware' => ['auth', 'role:admin', 'csrf']], function ($router) {
    $router->get('/dashboard', [DashboardController::class, 'index']);
});

// Parameter middleware
$router->get('/users/:id', [UserController::class, 'show'], ['auth', 'role:admin,user']);
```

**Best Practices:**

- Gunakan middleware untuk authorization, logging, rate limiting
- Middleware harus ringan - hindari query database berat
- Gunakan dependency injection untuk model/service
- Throw exception untuk error handling (AuthenticationException, AuthorizationException)

### CSRF Middleware (Opsional)

**Install:**

```bash
php mazu make:middleware csrf
```

**Cara Kerja:**

- Middleware ini **opsional** - hanya aktif jika ditambahkan ke route
- Otomatis skip untuk request GET, HEAD, OPTIONS (safe methods)
- Otomatis skip untuk API request dengan `Authorization: Bearer <token>` header
- Validasi token dari 3 sumber:
  1. `$request->body['_token']` - dari form input hidden
  2. `$_POST['_token']` - fallback untuk FormData
  3. `$request->server['HTTP_X_CSRF_TOKEN']` - dari header (digunakan spa.js)

**Penggunaan di Router:**

```php
// Proteksi route POST/PUT/DELETE
$router->post('/users', [UserController::class, 'store'], ['csrf']);
$router->put('/users/:id', [UserController::class, 'update'], ['csrf']);
$router->delete('/users/:id', [UserController::class, 'delete'], ['csrf']);

// Group middleware
$router->group(['middleware' => ['auth', 'csrf']], function ($router) {
    $router->post('/settings', [SettingsController::class, 'update']);
});
```

**Form dengan CSRF:**

**A. Form dengan `data-spa` (SPA Form):**

```html
<!-- TIDAK PERLU input hidden - spa.js otomatis kirim token via header -->
<form data-spa action="/users" method="POST">
  <!-- form fields -->
</form>
```

- ✅ **Tidak wajib** menambahkan `<input type="hidden" name="_token">`
- ✅ `spa.js` otomatis membaca token dari meta tag dan mengirim via header `X-CSRF-TOKEN`
- ✅ Meta tag CSRF otomatis ditambahkan oleh [`View.php`](app/Core/View/View.php:154-155)

**B. Form TANPA `data-spa` (Traditional Form):**

```html
<!-- WAJIB input hidden untuk form non-SPA -->
<form action="/users" method="POST">
  <input type="hidden" name="_token" value="<?= csrf_token() ?>" />
  <!-- atau gunakan helper -->
  <?= csrf_field() ?>

  <!-- form fields -->
</form>
```

- ⚠️ **Wajib** menambahkan `<input type="hidden" name="_token">`
- ⚠️ Form non-SPA tidak di-intercept oleh `spa.js`, jadi token harus dikirim via body

**Helper Functions:**

```php
csrf_token()   // Generate/get CSRF token dari session
csrf_field()   // Render hidden input: <input type="hidden" name="_token" value="...">
```

**Meta Tag CSRF:**

Meta tag `<meta name="csrf-token" content="...">` **otomatis ditambahkan** oleh [`View.php`](app/Core/View/View.php:154-155) saat render, tidak perlu manual menambahkan di layout.

**Response Error:**

- Non-AJAX: HTTP 419 dengan pesan "CSRF token mismatch"
- AJAX/JSON: `{ "status": "error", "message": "CSRF token mismatch", "code": 419 }`

**Exception (Bypass):**

Tambahkan URI ke `$except` di `CsrfMiddleware.php`:

```php
protected $except = [
    '/api/*',
    '/webhooks/*'
];
```

### 5. Nested Layout (Next.js style)

```
addon/Views/
├── layout.php         # Root layout
├── (app)/layout.php   # Group layout
└── (app)/dashboard/index.php
```

## 🔧 Helper Functions

```php
env('APP_NAME')           // Environment variable
getBaseUrl('/users')      // Base URL dengan subdirectory
asset('css/style.css')    // Versioned asset
csrf_token(), csrf_field() // CSRF helpers (opsional jika sudah menajalan kan php mazu make:middleware csrf)
e($value)                 // Escape HTML
dump($var)                // Debug
```

## 📦 Response & Request

```php
// Response
$response->renderPage(['data' => $data], ['meta' => ['title' => 'Page']]);
$response->json(['status' => 'success']);
$response->redirect('/users');

// Request
$request->input('name');      // Input
$request->getBody();           // POST/JSON body
$request->param('id');         // Route param
$request->get('search');       // Query param
$request->header('Accept');    // Header
```

## ⚠️ Important Rules

1. **JANGAN MODIFIKASI** folder `app/` - ini core engine
2. **GUNAKAN CLI** untuk membuat controller/model/middleware
3. Model harus extend `App\Core\Database\Model`
4. Middleware harus implement `MiddlewareInterface`
5. Controller return `View` via `$response->renderPage()`
6. CSS/JS auto-discovered (e.g., `index.php` → `index.css`)

## 🎨 View & renderPage()

### `$response->renderPage()` - Method Signature

```php
public function renderPage(array $props = [], array $options = []): View
```

**Parameter:**

- `$props` (array) - Data yang akan dikirim ke view
- `$options` (array) - Opsi tambahan dengan keys:
  - `'path'` (string|null) - Custom path ke view file (relative to addon/Views/)
  - `'meta'` (array) - Konfigurasi SEO meta tags

### Cara Penggunaan

**1. Basic Usage (Auto-detect path dari route):**

```php
// Route: /admin/schools → View: addon/Views/(app)/admin/schools/index.php
public function schools(Request $request, Response $response): View | RedirectResponse {
    return $response->renderPage(['schools' => $schools]);
}
```

**2. Dengan Custom Path:**

```php
// Gunakan path custom jika view tidak sesuai dengan route pattern
public function dashboard(Request $request, Response $response): View | RedirectResponse {
    return $response->renderPage(
        ['data' => $data],
        ['path' => 'custom/dashboard/view']
    );
}
```

**3. Dengan Meta Tags (SEO):**

```php
public function index(Request $request, Response $response): View | RedirectResponse {
    return $response->renderPage(
        ['users' => $users],
        [
            'meta' => [
                'title' => 'Daftar Pengguna - Admin Panel',
                'description' => 'Halaman pengelolaan data pengguna sistem',
                'keywords' => 'users, admin, management',
                'robots' => 'noindex, nofollow'
            ]
        ]
    );
}
```

**4. Lengkap (Props + Path + Meta):**

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
                'description' => 'Detail informasi pengguna ' . e($user['name']),
                'canonical' => getBaseUrl('/admin/users/' . $id)
            ]
        ]
    );
}
```

### PageMeta - Supported Fields

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

### View Auto-Discovery System

**CSS Auto-Discovery:**

- ViewService secara otomatis mencari file `style.css` di folder yang sama dengan view file
- File CSS akan di-include otomatis saat view di-render
- Bisa juga manual add dengan `View::addStyle('path/to/style.css')`

**JS Auto-Discovery:**

- ViewService secara otomatis mencari file `script.js` di folder yang sama dengan view file
- File JS akan di-include otomatis di akhir body
- Bisa juga manual add dengan `View::addScript('path/to/script.js')`

**Contoh Struktur Auto-Discovery:**

```
addon/Views/(app)/admin/schools/
├── index.php          ← View file
├── index.css          ← Auto-discovered & included
├── index.js           ← Auto-discovered & included
├── create.php
├── create.css
└── create.js
```

### Nested Layout System

**Struktur Layout Bertingkat:**

```
addon/Views/
├── layout.php           # Root layout (HTML wrapper, head, body)
├── (app)/layout.php     # Group layout (sidebar, header, main content)
├── (app)/index.php      # View: homepage
└── (app)/admin/
    ├── layout.php       # Layout khusus admin (opsional)
    └── schools/
        ├── index.php    # View: /admin/schools
        └── [id].php     # View: /admin/schools/:id
```

**Behavior:**

1. **Bottom-Up Rendering:** ViewService berjalan dari view file ke atas, mencari setiap `layout.php` di direktori induk
2. **Variabel `$children`:** Setiap layout menerima HTML dari level bawah melalui `$children`
3. **Variabel `$meta`:** Objek PageMeta tersedia di semua layout
4. **SPA Negotiation:** Jika request SPA, layout yang sudah ada di client tidak di-render ulang

**SPA Layout Headers:**

```
X-SPA-Request: true              // Request adalah SPA navigation
X-SPA-Target-Layout: (app)/layout.php   // Layout target yang diinginkan
X-SPA-Layouts: ["(app)/layout.php"]     // Array layout yang dimiliki client
```

### CSS/JS Auto-Discovery

**Pencarian Otomatis:**

```
addon/Views/(app)/admin/schools/
├── index.php    → index.css, index.js (jika ada)
├── create.php   → create.css, create.js (jika ada)
└── style.css    → Auto-discover untuk semua view di folder ini
└── script.js    → Auto-discover untuk semua view di folder ini
```

**Climbing Discovery:**

- ViewService mencari CSS/JS dengan nama yang sama (e.g., `index.php` → `index.css`)
- Kemudian naik ke atas mencari `style.css` / `script.js` di setiap folder induk
- CSS/JS di-load dari parent ke child (urutan terbalik)

**Manual Register:**

```php
View::addStyle('(app)/admin/custom.css');
View::addScript('(app)/admin/custom.js');
```

### SPA Navigation dengan `data-spa`

**Link Navigation:**

```html
<a data-spa href="/admin/schools">Daftar Sekolah</a>
```

**Form Submission:**

```html
<form data-spa action="/admin/schools" method="POST">
  <!-- form fields -->
</form>
```

**Behavior:**

1. Link/form dengan `data-spa` akan dicegat oleh SPA engine
2. Request dikirim dengan header `X-SPA-Request: true`
3. Server response JSON: `{ html, meta, layout, styles }`
4. Frontend update container tanpa reload penuh

**ProgressBar:**

- Global progress bar otomatis muncul saat navigasi SPA
- ID: `#global-progress-bar`

### View File Standards

**Struktur File View:**

```php
<?php
/**
 * @var array $schools      // Type hint untuk props
 * @var string $keyword
 */
?>

<div class="page-container">
    <!-- Page Header -->
    <div class="page-header">
        <h1><?= e($title) ?></h1>
    </div>

    <!-- Content -->
    <div class="page-content">
        <!-- Your content here -->
    </div>
</div>
```

**Penting:**

- Gunakan `@var` comments untuk type hint props yang diterima
- Selalu escape output dengan `e($value)` untuk mencegah XSS
- View path otomatis diturunkan dari route pattern jika tidak specified
- CSS/JS file akan auto-discover berdasarkan nama file view

### Route dengan Parameter Dinamis - Format `[param]`

Untuk route dengan parameter dinamis (e.g., `:id`, `:userId`), view path menggunakan format **`[param]`** seperti Next.js.

**Contoh:**

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

## 📚 Source of Truth

- **CLI Templates:** `app/Console/Commands/Make*.php`
- **Core Classes:** `app/Core/Http/Request.php`, `app/Core/Http/Response.php`
- **Base Model:** `app/Core/Database/Model.php`
- **View Engine:** `app/Services/ViewService.php`
