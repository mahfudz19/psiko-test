<?php

namespace Addon\Middleware;

use App\Core\Interfaces\MiddlewareInterface;
use App\Services\SessionService;
use App\Core\Database\DatabaseManager;
use App\Services\ConfigService;
use Addon\Models\TeacherProfileModel;
use Addon\Models\StudentProfileModel;
use App\Core\Http\Response;

class HasschollMiddleware implements MiddlewareInterface
{
  public function __construct(private SessionService $session, private Response $response) {}

  public function handle($request, \Closure $next, array $params = [])
  {
    $userId = $this->session->get('auth.user_id');
    $userRole = $this->session->get('auth.user_role');
    // Super admin bisa akses semua - bypass validation
    if ($userRole === 'super-admin') {
      return $next($request);
    }

    $school_id = $this->session->get('auth.school_id');
    if ($school_id) {
      return $next($request);
    }

    $config = new ConfigService();
    $dbManager = new DatabaseManager($config);

    $profile = null;

    if ($userRole === 'admin') {
      $teacherModel = new TeacherProfileModel($dbManager);
      $profile = $teacherModel->findByUserId($userId);
    }

    if ($userRole === 'user') {
      $studentModel = new StudentProfileModel($dbManager);
      $profile = $studentModel->findByUserId($userId);
    }

    if (!$profile || $profile['school_id'] == null) {
      return $this->response->redirect('/search-school?error=404&message=' . urlencode('Sekolah tidak ditemukan'));
    }

    // Simpan school_id di session untuk akses cepat
    $this->session->set('auth.school_id', $profile['school_id']);
    if ($userRole === 'admin') $this->session->set('auth.teacher_profile_id', $profile['teacher_profile_id']);
    if ($userRole === 'user') $this->session->set('auth.student_profile_id', $profile['student_profile_id']);

    return $next($request);
  }
}
