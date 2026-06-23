<?php

namespace Addon\Controllers;

use Addon\Models\SchoolModel;
use Addon\Models\ProfileModel;
use Addon\Models\StudentProfileModel;
use Addon\Models\TeacherProfileModel;
use Addon\Models\UserModel;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\RedirectResponse;
use App\Core\View\View;
use App\Services\SessionService;

class SearchSchoolController
{
  public function __construct(
    private SchoolModel $schoolModel,
    private ProfileModel $profileModel,
    private StudentProfileModel $studentModel,
    private TeacherProfileModel $teacherModel,
    private UserModel $userModel,
    private SessionService $session,
  ) {}

  /**
   * Display search school page
   */
  public function index(Request $request, Response $response): View
  {
    $schools = $this->schoolModel->all();

    return $response->renderPage(
      ['schools' => $schools],
      ['meta' => ['title' => 'Cari Sekolah | ' . env('APP_NAME')]]
    );
  }

  /**
   * Handle school selection and assignment to user profile
   */
  public function select(Request $request, Response $response): RedirectResponse
  {
    try {
      // Get logged in user from session
      $userId = $this->session->get('auth.user_id');

      if (!$userId) {
        throw new \Exception('User belum login');
      }

      // Get user data
      $user = $this->userModel->find($userId);
      if (!$user) {
        throw new \Exception('User tidak ditemukan');
      }

      // Get school_id from POST data
      $schoolId = (int) $request->post('school_id');

      if (!$schoolId) {
        throw new \Exception('School ID tidak valid');
      }

      // Verify school exists
      $school = $this->schoolModel->find($schoolId);
      if (!$school) {
        throw new \Exception('Sekolah tidak ditemukan');
      }

      // Get user profile
      $profile = $this->profileModel->findByUserId($userId);
      if (!$profile) {
        $this->profileModel->create(['user_id' => $userId]);
        $profile = $this->profileModel->findByUserId($userId);
      }

      $profileId = (int) $profile['id'];

      // Assign school based on user role
      $success = false;

      if ($user['role'] === 'admin') {
        // For admin role (teacher)
        $success = $this->teacherModel->assignSchool($profileId, $schoolId);
      } elseif ($user['role'] === 'user') {
        // For user role (student)
        $success = $this->studentModel->assignSchool($profileId, $schoolId);
      } else {
        throw new \Exception('Role user tidak valid');
      }

      if (!$success) {
        throw new \Exception('Gagal mengupdate school_id');
      }


      return $response->redirect('/dashboard?success=' . urlencode('Berhasil mengaitkan sekolah ' . e($school['name'])));
    } catch (\Exception $e) {
      // Set error message
      return $response->redirect('/search-school?error=500&message' . urlencode($e->getMessage()));
    }
  }
}
