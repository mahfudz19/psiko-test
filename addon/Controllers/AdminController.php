<?php

namespace Addon\Controllers;

use Addon\Models\SchoolModel;
use Addon\Models\TeacherProfileModel;
use Addon\Models\StudentProfileModel;
use Addon\Models\ProfileModel;
use Addon\Models\UserModel;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\RedirectResponse;
use App\Core\View\View;

class AdminController
{
  public function __construct(
    private SchoolModel $schoolModel,
    private TeacherProfileModel $teacherModel,
    private StudentProfileModel $studentModel,
    private ProfileModel $profileModel,
    private UserModel $userModel
  ) {}

  /**
   * Dashboard Admin - menampilkan statistik
   */
  public function index(Request $request, Response $response): View | RedirectResponse
  {
    try {
      $schools = $this->schoolModel->all();
      $teachers = $this->teacherModel->all();
      $students = $this->studentModel->all();

      $data = [
        'totalSchools' => count($schools),
        'totalTeachers' => count($teachers),
        'totalStudents' => count($students),
      ];

      return $response->renderPage($data);
    } catch (\Exception $e) {
      return $response->redirect('/admin?error=500&message=' . urlencode($e->getMessage()));
    }
  }

  /**
   * Daftar semua sekolah dengan pagination, search, sort, dan filter
   */
  public function schools(Request $request, Response $response): View | RedirectResponse
  {
    try {
      $params = [
        'page' => $request->input('page', 1),
        'per_page' => $request->input('per_page', 15),
        'search' => $request->input('search', ''),
        'sort_by' => $request->input('sort_by', 'name'),
        'sort_order' => $request->input('sort_order', 'ASC'),
        'accreditation' => $request->input('accreditation', ''),
        'min_students' => $request->input('min_students', ''),
        'max_students' => $request->input('max_students', ''),
      ];

      $result = $this->schoolModel->getPaginated($params);

      return $response->renderPage([
        'schools' => $result['data'],
        'pagination' => [
          'page' => $result['page'],
          'per_page' => $result['per_page'],
          'total' => $result['total'],
          'total_pages' => $result['total_pages'],
        ],
        'filters' => [
          'search' => $params['search'],
          'sort_by' => $params['sort_by'],
          'sort_order' => $params['sort_order'],
          'accreditation' => $params['accreditation'],
          'min_students' => $params['min_students'],
          'max_students' => $params['max_students'],
        ]
      ], ['meta' => ['title' => 'Daftar Sekolah']]);
    } catch (\Exception $e) {
      return $response->redirect('/admin?error=500&message=' . urlencode($e->getMessage()));
    }
  }

  /**
   * Form tambah sekolah
   */
  public function createSchool(Request $request, Response $response): View | RedirectResponse
  {
    try {
      return $response->renderPage([]);
    } catch (\Exception $e) {
      return $response->redirect('/admin/schools?error=500&message=' . urlencode($e->getMessage()));
    }
  }

  /**
   * Proses simpan sekolah baru
   */
  public function storeSchool(Request $request, Response $response): View | RedirectResponse
  {
    try {
      $inputData = $request->input();

      // Validasi sederhana
      $required = ['name', 'npsn', 'address', 'principal_name', 'contact', 'accreditation'];
      foreach ($required as $field) {
        if (empty($inputData[$field])) {
          return $response->redirect('/admin/schools/create?error=400&message=' . urlencode('Field ' . $field . ' wajib diisi'));
        }
      }

      // Cek NPSN unique
      $existing = $this->schoolModel->findByNpsn($inputData['npsn']);
      if ($existing) {
        return $response->redirect('/admin/schools/create?error=400&message=' . urlencode('NPSN sudah digunakan'));
      }

      // Filter data yang valid sesuai schema schools (hapus _token, dll)
      $validFields = ['name', 'npsn', 'address', 'principal_name', 'contact', 'accreditation'];
      $data = [];
      foreach ($validFields as $field) {
        if (isset($inputData[$field])) {
          $data[$field] = $inputData[$field];
        }
      }

      $schoolId = $this->schoolModel->create($data);

      return $response->redirect('/admin/schools/' . $schoolId);
    } catch (\Exception $e) {
      return $response->redirect('/admin/schools/create?error=500&message=' . urlencode($e->getMessage()));
    }
  }

  /**
   * Detail sekolah
   */
  public function showSchool(Request $request, Response $response): View | RedirectResponse
  {
    try {
      $id = $request->param('id');
      $school = $this->schoolModel->find($id);

      if (!$school) {
        return $response->redirect('/admin/schools?error=404&message=' . urlencode('Sekolah tidak ditemukan'));
      }

      $teachers = $this->teacherModel->findBySchoolId((int)$id);
      $students = $this->studentModel->findBySchoolId((int)$id);

      return $response->renderPage([
        'school' => $school,
        'teachers' => $teachers,
        'students' => $students,
      ]);
    } catch (\Exception $e) {
      return $response->redirect('/admin/schools?error=500&message=' . urlencode($e->getMessage()));
    }
  }

  /**
   * Form edit sekolah
   */
  public function editSchool(Request $request, Response $response): View | RedirectResponse
  {
    try {
      $id = $request->param('id');
      $school = $this->schoolModel->find($id);

      if (!$school) {
        return $response->redirect('/admin/schools?error=404&message=' . urlencode('Sekolah tidak ditemukan'));
      }

      return $response->renderPage(['school' => $school]);
    } catch (\Exception $e) {
      return $response->redirect('/admin/schools?error=500&message=' . urlencode($e->getMessage()));
    }
  }

  /**
   * Proses update sekolah
   */
  public function updateSchool(Request $request, Response $response): View | RedirectResponse
  {
    try {
      $id = $request->param('id');
      $school = $this->schoolModel->find($id);

      if (!$school) {
        return $response->redirect('/admin/schools?error=404&message=' . urlencode('Sekolah tidak ditemukan'));
      }

      $inputData = $request->input();

      // Validasi sederhana (sama seperti storeSchool)
      $required = ['name', 'npsn', 'address', 'principal_name', 'contact', 'accreditation'];
      foreach ($required as $field) {
        if (empty($inputData[$field])) {
          return $response->redirect('/admin/schools/' . $id . '/edit?error=400&message=' . urlencode('Field ' . $field . ' wajib diisi'));
        }
      }

      // Cek NPSN unique (kecuali NPSN sekolah ini sendiri)
      $existing = $this->schoolModel->findByNpsn($inputData['npsn']);

      if ($existing && $existing['id'] != $id) {
        return $response->redirect('/admin/schools/' . $id . '/edit?error=400&message=' . urlencode('NPSN sudah digunakan sekolah lain'));
      }

      // Filter data yang valid sesuai schema schools (hapus _token, dll)
      $validFields = ['name', 'npsn', 'address', 'principal_name', 'contact', 'accreditation'];
      $data = [];
      foreach ($validFields as $field) {
        if (isset($inputData[$field])) {
          $data[$field] = $inputData[$field];
        }
      }

      $this->schoolModel->updateById($id, $data);

      return $response->redirect('/admin/schools');
    } catch (\Exception $e) {
      return $response->redirect('/admin/schools/' . $request->param('id') . '/edit?error=500&message=' . urlencode($e->getMessage()));
    }
  }

  /**
   * Hapus sekolah
   */
  public function deleteSchool(Request $request, Response $response): View | RedirectResponse
  {
    try {
      $id = $request->param('id');
      $school = $this->schoolModel->find($id);

      if (!$school) {
        return $response->redirect('/admin/schools?error=404&message=' . urlencode('Sekolah tidak ditemukan'));
      }

      $this->schoolModel->deleteById($id);

      return $response->redirect('/admin/schools');
    } catch (\Exception $e) {
      return $response->redirect('/admin/schools?error=500&message=' . urlencode($e->getMessage()));
    }
  }

  /**
   * Daftar guru di sekolah tertentu
   */
  public function schoolTeachers(Request $request, Response $response): View | RedirectResponse
  {
    try {
      $schoolId = $request->param('id');
      $school = $this->schoolModel->find($schoolId);

      if (!$school) {
        return $response->redirect('/admin/schools?error=404&message=' . urlencode('Sekolah tidak ditemukan'));
      }

      $teachers = $this->teacherModel->findBySchoolId((int)$schoolId);

      return $response->renderPage([
        'school' => $school,
        'teachers' => $teachers,
      ]);
    } catch (\Exception $e) {
      return $response->redirect('/admin/schools?error=500&message=' . urlencode($e->getMessage()));
    }
  }

  /**
   * Form tambah guru untuk sekolah tertentu
   */
  public function createTeacher(Request $request, Response $response): View | RedirectResponse
  {
    try {
      $schoolId = $request->param('id');
      $school = $this->schoolModel->find($schoolId);

      if (!$school) {
        return $response->redirect('/admin/schools?error=404&message=' . urlencode('Sekolah tidak ditemukan'));
      }

      return $response->renderPage(['school' => $school]);
    } catch (\Exception $e) {
      return $response->redirect('/admin/schools?error=500&message=' . urlencode($e->getMessage()));
    }
  }

  /**
   * Proses simpan guru baru
   */
  public function storeTeacher(Request $request, Response $response): View | RedirectResponse
  {
    try {
      $schoolId = $request->param('id');
      $school = $this->schoolModel->find($schoolId);

      if (!$school) {
        return $response->redirect('/admin/schools?error=404&message=' . urlencode('Sekolah tidak ditemukan'));
      }

      $data = $request->input();

      // Validasi
      $required = ['name', 'email', 'password', 'teacher_id', 'subject_specialty'];
      foreach ($required as $field) {
        if (empty($data[$field])) {
          return $response->redirect('/admin/schools/' . $schoolId . '/teachers/create?error=400&message=' . urlencode('Field ' . $field . ' wajib diisi'));
        }
      }

      // Cek email unique
      $existingUser = $this->userModel->findByEmail($data['email']);
      if ($existingUser) {
        return $response->redirect('/admin/schools/' . $schoolId . '/teachers/create?error=400&message=' . urlencode('Email sudah digunakan'));
      }

      $db = $this->userModel->getDb();
      $db->beginTransaction();

      try {
        // 1. Buat user dengan role admin
        $userId = $this->userModel->create([
          'email' => $data['email'],
          'password' => $data['password'],
          'name' => $data['name'],
          'role' => 'admin',
          'is_active' => 1,
        ]);

        // 2. Buat profile
        $profileId = $this->profileModel->create([
          'user_id' => $userId,
          'phone' => $data['phone'] ?? '',
          'address' => $data['address'] ?? '',
        ]);

        // 3. Buat teacher_profiles
        $this->teacherModel->create([
          'profile_id' => $profileId,
          'school_id' => (int)$schoolId,
          'teacher_id' => $data['teacher_id'],
          'subject_specialty' => $data['subject_specialty'],
          'certification' => $data['certification'] ?? '',
        ]);

        $db->commit();

        return $response->redirect('/admin/schools/' . $schoolId . '/teachers');
      } catch (\Exception $e) {
        $db->rollBack();
        throw $e;
      }
    } catch (\Exception $e) {
      return $response->redirect('/admin/schools/' . $request->param('id') . '/teachers/create?error=500&message=' . urlencode($e->getMessage()));
    }
  }

  /**
   * Daftar siswa di sekolah tertentu
   */
  public function schoolStudents(Request $request, Response $response): View | RedirectResponse
  {
    try {
      $schoolId = $request->param('id');
      $school = $this->schoolModel->find($schoolId);

      if (!$school) {
        return $response->redirect('/admin/schools?error=404&message=' . urlencode('Sekolah tidak ditemukan'));
      }

      $students = $this->studentModel->findBySchoolId((int)$schoolId);

      return $response->renderPage([
        'school' => $school,
        'students' => $students,
      ]);
    } catch (\Exception $e) {
      return $response->redirect('/admin/schools?error=500&message=' . urlencode($e->getMessage()));
    }
  }

  /**
   * Form tambah siswa untuk sekolah tertentu
   */
  public function createStudent(Request $request, Response $response): View | RedirectResponse
  {
    try {
      $schoolId = $request->param('id');
      $school = $this->schoolModel->find($schoolId);

      if (!$school) {
        return $response->redirect('/admin/schools?error=404&message=' . urlencode('Sekolah tidak ditemukan'));
      }

      return $response->renderPage(['school' => $school]);
    } catch (\Exception $e) {
      return $response->redirect('/admin/schools?error=500&message=' . urlencode($e->getMessage()));
    }
  }

  /**
   * Proses simpan siswa baru
   */
  public function storeStudent(Request $request, Response $response): View | RedirectResponse
  {
    try {
      $schoolId = $request->param('id');
      $school = $this->schoolModel->find($schoolId);

      if (!$school) {
        return $response->redirect('/admin/schools?error=404&message=' . urlencode('Sekolah tidak ditemukan'));
      }

      $data = $request->input();

      // Validasi
      $required = ['name', 'email', 'password', 'student_id', 'grade_level', 'parent_name', 'parent_phone'];
      foreach ($required as $field) {
        if (empty($data[$field])) {
          return $response->redirect('/admin/schools/' . $schoolId . '/students/create?error=400&message=' . urlencode('Field ' . $field . ' wajib diisi'));
        }
      }

      // Cek email unique
      $existingUser = $this->userModel->findByEmail($data['email']);
      if ($existingUser) {
        return $response->redirect('/admin/schools/' . $schoolId . '/students/create?error=400&message=' . urlencode('Email sudah digunakan'));
      }

      $db = $this->userModel->getDb();
      $db->beginTransaction();

      try {
        // 1. Buat user dengan role user
        $userId = $this->userModel->create([
          'email' => $data['email'],
          'password' => $data['password'],
          'name' => $data['name'],
          'role' => 'user',
          'is_active' => 1,
        ]);

        // 2. Buat profile
        $profileId = $this->profileModel->create([
          'user_id' => $userId,
          'phone' => $data['phone'] ?? '',
          'address' => $data['address'] ?? '',
        ]);

        // 3. Buat student_profiles
        $this->studentModel->create([
          'profile_id' => $profileId,
          'school_id' => (int)$schoolId,
          'student_id' => $data['student_id'],
          'grade_level' => $data['grade_level'],
          'major' => $data['major'] ?? '',
          'parent_name' => $data['parent_name'],
          'parent_phone' => $data['parent_phone'],
          'parent_email' => $data['parent_email'] ?? '',
        ]);

        $db->commit();

        return $response->redirect('/admin/schools/' . $schoolId . '/students');
      } catch (\Exception $e) {
        $db->rollBack();
        throw $e;
      }
    } catch (\Exception $e) {
      return $response->redirect('/admin/schools/' . $request->param('id') . '/students/create?error=500&message=' . urlencode($e->getMessage()));
    }
  }
}
