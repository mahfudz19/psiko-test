<?php

namespace Addon\Middleware;

use App\Core\Foundation\Container;
use App\Core\Interfaces\MiddlewareInterface;
use App\Core\Http\Request;
use App\Services\SessionService;
use App\Exceptions\HttpException;
use App\Core\Http\JsonResponse;
use Closure;

/**
 * CsrfMiddleware
 *
 * Middleware cerdas (Hybrid) untuk memproteksi aplikasi dari serangan CSRF.
 * Otomatis melewati pemeriksaan untuk request API yang menggunakan token Authorization (Bearer).
 *
 * Cara pakai di router:
 *
 *   // Terapkan pada route POST/PUT/DELETE yang membutuhkan proteksi
 *   $router->post('login', [AuthController::class, 'login'], ['csrf']);
 *
 *   // Bisa juga diterapkan ke group:
 *   $router->group(['middleware' => ['csrf']], function ($router) {
 *     $router->post('change-password', [AuthController::class, 'changePassword']);
 *     $router->post('delete-account', [AuthController::class, 'deleteAccount']);
 *   });
 */
class CsrfMiddleware implements MiddlewareInterface
{
  public function __construct(private Container $container) {}

  protected $except = [
    // Tambahkan URI yang dikecualikan dari proteksi CSRF di sini
    // '/api/*',
  ];

  public function handle($request, Closure $next, array $params = [])
  {
    if (
      $this->isReading($request) ||
      $this->inExceptArray($request) ||
      $this->isStatelessApi($request) ||
      $this->tokensMatch($request)
    ) {
      return $next($request);
    }

    if ($this->expectsJson($request)) {
      return new JsonResponse($this->container, [
        'status' => 'error',
        'message' => 'CSRF token mismatch. Silakan refresh halaman dan coba lagi.',
        'code' => 419
      ], 419);
    }

    throw new HttpException(419, 'CSRF token mismatch. Silakan refresh halaman dan coba lagi.');
  }

  protected function expectsJson(Request $request): bool
  {
    $accept = $request->server['HTTP_ACCEPT'] ?? '';
    $requestedWith = $request->server['HTTP_X_REQUESTED_WITH'] ?? '';

    return str_contains($accept, 'application/json') || $requestedWith === 'XMLHttpRequest';
  }

  protected function isReading(Request $request): bool
  {
    return in_array($request->getMethod(), ['HEAD', 'GET', 'OPTIONS']);
  }

  protected function isStatelessApi(Request $request): bool
  {
    if (isset($request->server['HTTP_AUTHORIZATION'])) {
      return true;
    }

    return false;
  }

  protected function inExceptArray(Request $request): bool
  {
    $currentPath = $request->getPath();
    foreach ($this->except as $except) {
      if ($except !== '/') {
        $except = trim($except, '/');
      }

      if ($this->matches($except, $currentPath)) {
        return true;
      }
    }
    return false;
  }

  protected function matches($pattern, $path)
  {
    $pattern = preg_quote($pattern, '#');
    $pattern = str_replace('\*', '.*', $pattern);
    return preg_match('#^' . $pattern . '\\z#u', $path) === 1;
  }

  protected function tokensMatch(Request $request): bool
  {
    $token = $this->getTokenFromRequest($request);
    $session = new SessionService();

    return $session->validateCsrfToken($token);
  }

  protected function getTokenFromRequest(Request $request): ?string
  {
    // Cek dari body request (sudah di-parse oleh Request constructor)
    if (isset($request->body['_token'])) {
      return $request->body['_token'];
    }

    // Cek langsung dari $_POST untuk FormData yang dikirim via fetch
    if (isset($_POST['_token'])) {
      return $_POST['_token'];
    }

    // Cek dari header X-CSRF-TOKEN
    if (isset($request->server['HTTP_X_CSRF_TOKEN'])) {
      return $request->server['HTTP_X_CSRF_TOKEN'];
    }

    return null;
  }
}
