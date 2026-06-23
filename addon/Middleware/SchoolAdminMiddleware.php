<?php

namespace Addon\Middleware;

use App\Core\Interfaces\MiddlewareInterface;
use App\Core\Http\Request;
use App\Exceptions\AuthorizationException;
use App\Services\SessionService;
use App\Core\Database\DatabaseManager;
use App\Services\ConfigService;
use Addon\Models\TeacherProfileModel;

/**
 * School Admin Middleware - Validasi school-based authorization
 *
 * Middleware ini memastikan bahwa:
 * 1. Role super-admin bisa akses semua sekolah (bypass)
 * 2. Role admin hanya bisa akses sekolah yang mereka ajar (berdasarkan teacher_profiles)
 * 3. School_id disimpan di session untuk akses cepat
 */
class SchoolAdminMiddleware implements MiddlewareInterface
{
  public function __construct(private SessionService $session) {}

  public function handle($request, \Closure $next, array $params = [])
  {
    $userId = $this->session->get('auth.user_id');
    $userRole = $this->session->get('auth.user_role');

    // Super admin bisa akses semua - bypass validation
    if ($userRole === 'super-admin') {
      return $next($request);
    }

    $userRole = $this->session->get('auth.user_role');

    // Admin hanya bisa akses sekolah sendiri
    if ($userRole === 'admin') {
      $config = new ConfigService();
      $dbManager = new DatabaseManager($config);
      $teacherModel = new TeacherProfileModel($dbManager);
      $teacherProfile = $teacherModel->findByUserId($userId);

      if (!$teacherProfile) {
        $e = new AuthorizationException('Anda tidak terafiliasi dengan sekolah manapun. Hubungi administrator.');
        $e->hardRedirect();
        throw $e;
      }

      // Simpan school_id di session untuk akses cepat
      $_SESSION['auth.school_id'] = $teacherProfile['school_id'];
      $_SESSION['auth.teacher_profile_id'] = $teacherProfile['teacher_profile_id'];

      // Validasi jika ada parameter school_id di route
      $routeSchoolId = $request->param('id');
      if ($routeSchoolId && (int)$routeSchoolId === $teacherProfile['school_id']) {
        $e = new AuthorizationException('Anda hanya bisa mengelola sekolah sendiri.');
        $e->hardRedirect();
        throw $e;
      }

      return $next($request);
    }

    // Role lain tidak punya akses
    $e = new AuthorizationException('Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.');
    $e->hardRedirect();
    throw $e;
  }
}
