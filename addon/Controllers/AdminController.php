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
    $id = $request->param('id');
    try {
      $school = $this->schoolModel->find($id);

      if (!$school) {
        return $response->redirect('/admin/schools/' . $id . '?error=404&message=' . urlencode('Sekolah tidak ditemukan'));
      }

      // check if school has teachers
      $allTeachers = $this->teacherModel->findBySchoolId($id);
      if ($allTeachers) {
        return $response->redirect('/admin/schools/' . $id . '?error=400&message=' . urlencode('Sekolah ini masih memiliki guru'));
      }

      // check if school has students
      $allStudents = $this->studentModel->findBySchoolId($id);
      if ($allStudents) {
        return $response->redirect('/admin/schools/' . $id . '?error=400&message=' . urlencode('Sekolah ini masih memiliki siswa'));
      }

      $this->schoolModel->deleteById($id);

      return $response->redirect('/admin/schools');
    } catch (\Exception $e) {
      return $response->redirect('/admin/schools/' . $id . '?error=500&message=' . urlencode($e->getMessage()));
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

  public function showTeacher(Request $request, Response $response): View | RedirectResponse
  {
    try {
      $schoolId = $request->param('id');
      $userId = $request->param('user_id');

      $school = $this->schoolModel->find($schoolId);
      if (!$school) {
        return $response->redirect('/admin/schools?error=404&message=' . urlencode('Sekolah tidak ditemukan'));
      }

      $teacher = $this->teacherModel->findByUserId((int)$userId);
      if (!$teacher) {
        return $response->redirect('/admin/schools/' . $schoolId . '/teachers?error=404&message=' . urlencode('Guru tidak ditemukan di sekolah ini'));
      }

      return $response->renderPage([
        'school' => $school,
        'teacher' => $teacher,
      ]);
    } catch (\Exception $e) {
      return $response->redirect('/admin/schools/' . $request->param('id') . '/teachers?error=500&message=' . urlencode($e->getMessage()));
    }
  }

  /**
   * Form edit guru
   */
  public function editTeacher(Request $request, Response $response): View | RedirectResponse
  {
    try {
      $schoolId = $request->param('id');
      $userId = $request->param('user_id');

      $school = $this->schoolModel->find($schoolId);
      if (!$school) {
        return $response->redirect('/admin/schools?error=404&message=' . urlencode('Sekolah tidak ditemukan'));
      }

      $teacher = $this->teacherModel->findByUserId((int)$userId);
      if (!$teacher) {
        return $response->redirect('/admin/schools/' . $schoolId . '/teachers?error=404&message=' . urlencode('Guru tidak ditemukan di sekolah ini'));
      }

      return $response->renderPage([
        'school' => $school,
        'teacher' => $teacher,
      ]);
    } catch (\Exception $e) {
      return $response->redirect('/admin/schools/' . $request->param('id') . '/teachers?error=500&message=' . urlencode($e->getMessage()));
    }
  }

  /**
   * Proses update guru
   */
  public function updateTeacher(Request $request, Response $response): View | RedirectResponse
  {
    try {
      $schoolId = $request->param('id');
      $userId = $request->param('user_id');

      $school = $this->schoolModel->find($schoolId);
      if (!$school) {
        return $response->redirect('/admin/schools?error=404&message=' . urlencode('Sekolah tidak ditemukan'));
      }

      $teacher = $this->teacherModel->findByUserId((int)$userId);
      if (!$teacher) {
        return $response->redirect('/admin/schools/' . $schoolId . '/teachers?error=404&message=' . urlencode('Guru tidak ditemukan di sekolah ini'));
      }

      $data = $request->input();

      // Validasi
      $required = ['name', 'email', 'teacher_id', 'subject_specialty'];
      foreach ($required as $field) {
        if (empty($data[$field])) {
          return $response->redirect('/admin/schools/' . $schoolId . '/teachers/' . $userId . '/edit?error=400&message=' . urlencode('Field ' . $field . ' wajib diisi'));
        }
      }

      // Cek email unique (kecuali email yang sama)
      $existingUser = $this->userModel->findByEmail($data['email']);
      if ($existingUser && $existingUser['id'] != $userId) {
        return $response->redirect('/admin/schools/' . $schoolId . '/teachers/' . $userId . '/edit?error=400&message=' . urlencode('Email sudah digunakan'));
      }

      $db = $this->userModel->getDb();
      $db->beginTransaction();

      try {
        // 1. Update user
        $userData = [
          'email' => $data['email'],
          'name' => $data['name'],
        ];

        // Update password hanya jika diisi
        if (!empty($data['password'])) {
          $userData['password'] = $data['password'];
        }

        $this->userModel->updateById($userId, $userData);

        // 2. Update profile
        $this->profileModel->updateByUserId((int)$userId, [
          'phone' => $data['phone'] ?? '',
          'address' => $data['address'] ?? '',
          'gender' => $data['gender'] ?? null,
          'birth_place' => $data['birth_place'] ?? null,
          'birth_date' => $data['birth_date'] ?? null,
        ]);

        // 3. Update teacher_profiles
        $this->teacherModel->updateByProfileId((int)$teacher['profile_id'], [
          'teacher_id' => $data['teacher_id'],
          'subject_specialty' => $data['subject_specialty'],
          'certification' => $data['certification'] ?? '',
        ]);

        $db->commit();

        return $response->redirect('/admin/schools/' . $schoolId . '/teachers/' . $userId);
      } catch (\Exception $e) {
        $db->rollBack();
        throw $e;
      }
    } catch (\Exception $e) {
      return $response->redirect('/admin/schools/' . $request->param('id') . '/teachers/' . $request->param('user_id') . '/edit?error=500&message=' . urlencode($e->getMessage()));
    }
  }

  /**
   * Toggle status aktif/nonaktif guru (menggunakan transaction untuk menghindari race condition)
   */
  public function toggleTeacherStatus(Request $request, Response $response): RedirectResponse
  {
    try {
      $schoolId = $request->param('id');
      $userId = $request->param('user_id');

      $school = $this->schoolModel->find($schoolId);
      if (!$school) {
        return $response->redirect('/admin/schools?error=404&message=' . urlencode('Sekolah tidak ditemukan'));
      }

      $teacher = $this->teacherModel->findByUserId((int)$userId);
      if (!$teacher) {
        return $response->redirect('/admin/schools/' . $schoolId . '/teachers?error=404&message=' . urlencode('Guru tidak ditemukan di sekolah ini'));
      }

      // Gunakan transaction untuk menghindari race condition
      $db = $this->userModel->getDb();
      $db->beginTransaction();

      try {
        // Toggle status dengan query langsung untuk memastikan atomik
        $this->userModel->toggleActive((int)$userId);

        $db->commit();

        // Redirect kembali ke halaman detail guru
        return $response->redirect('/admin/schools/' . $schoolId . '/teachers/' . $userId);
      } catch (\Exception $e) {
        $db->rollBack();
        throw $e;
      }
    } catch (\Exception $e) {
      return $response->redirect('/admin/schools/' . $request->param('id') . '/teachers?error=500&message=' . urlencode($e->getMessage()));
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

  /**
   * Download template CSV untuk bulk input scores
   */
  public function downloadBulkScoresTemplate(Request $request, Response $response): void
  {
    $schoolId = $request->param('id');

    // CSV content - contoh dengan 3 nilai per siswa
    $csvContent = "identifier,semester,subject,final_score,pengetahuan,keterampilan\n";
    $csvContent .= "0012345678,Semester 1 Kelas 10,Matematika,85,80,90\n";
    $csvContent .= "0012345678,Semester 1 Kelas 10,Bahasa Indonesia,88,85,91\n";
    $csvContent .= "0012345678,Semester 1 Kelas 10,Bahasa Inggris,90,88,92\n";
    $csvContent .= "0012345679,Semester 1 Kelas 10,Matematika,92,90,94\n";
    $csvContent .= "0012345679,Semester 1 Kelas 10,Bahasa Indonesia,87,85,89\n";
    $csvContent .= "0012345679,Semester 1 Kelas 10,IPA,95,93,97\n";

    // Set headers for download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="template_input_nilai_massal.csv"');
    header('Content-Length: ' . strlen($csvContent));

    // Output CSV content
    echo $csvContent;
    exit;
  }

  /**
   * Form bulk import siswa (CSV upload) untuk superadmin
   * Method ini untuk super-admin yang ingin input banyak siswa sekaligus di sekolah tertentu
   */
  public function bulkCreateStudent(Request $request, Response $response): View | RedirectResponse
  {
    try {
      $schoolId = $request->param('id');
      $school = $this->schoolModel->find($schoolId);

      if (!$school) {
        return $response->redirect('/admin/schools?error=404&message=' . urlencode('Sekolah tidak ditemukan'));
      }

      return $response->renderPage(['school' => $school], ['meta' => ['title' => 'Import Banyak Siswa']]);
    } catch (\Exception $e) {
      return $response->redirect('/admin/schools?error=500&message=' . urlencode($e->getMessage()));
    }
  }

  /**
   * Proses upload CSV dan simpan banyak siswa sekaligus untuk superadmin
   * Format CSV: name,email,password,student_id,grade_level,major,phone,address,birth_place,birth_date,gender,parent_name,parent_phone,parent_email
   */
  public function storeBulkStudent(Request $request, Response $response): View | RedirectResponse
  {
    try {
      $schoolId = $request->param('id');
      $school = $this->schoolModel->find($schoolId);

      if (!$school) {
        return $response->redirect('/admin/schools?error=404&message=' . urlencode('Sekolah tidak ditemukan'));
      }

      // Validasi file upload
      $file = $request->file('csv_file');

      if (!$file) {
        return $response->redirect('/admin/schools/' . $schoolId . '/students/bulk-create?error=400&message=' . urlencode('File CSV wajib diupload'));
      }

      // Validasi upload error
      if ($file->getError() !== UPLOAD_ERR_OK) {
        return $response->redirect('/admin/schools/' . $schoolId . '/students/bulk-create?error=400&message=' . urlencode('Gagal upload file'));
      }

      // Validasi tipe file
      $allowedTypes = ['text/csv', 'text/plain', 'application/vnd.ms-excel'];
      if (!in_array($file->getClientMimeType(), $allowedTypes)) {
        return $response->redirect('/admin/schools/' . $schoolId . '/students/bulk-create?error=400&message=' . urlencode('File harus berformat CSV'));
      }

      // Pindahkan file ke temporary directory untuk diproses
      $tempDir = sys_get_temp_dir() . '/bulk_import';
      if (!is_dir($tempDir)) {
        mkdir($tempDir, 0755, true);
      }

      $tempFilename = 'bulk_' . time() . '_' . uniqid() . '.csv';
      $tempPath = $tempDir . '/' . $tempFilename;

      if (!$file->move($tempDir, $tempFilename)) {
        return $response->redirect('/admin/schools/' . $schoolId . '/students/bulk-create?error=500&message=' . urlencode('Gagal menyimpan file CSV'));
      }

      // Baca file CSV
      $csvData = file_get_contents($tempPath);

      // Hapus file temporary setelah dibaca
      unlink($tempPath);

      $lines = explode("\n", $csvData);

      // Skip header dan ambil data
      $headers = str_getcsv(array_shift($lines));
      $studentsData = [];
      $errors = [];

      foreach ($lines as $index => $line) {
        if (empty(trim($line))) {
          continue;
        }

        $row = str_getcsv($line);
        if (count($row) < count($headers)) {
          $errors[] = "Baris " . ($index + 2) . ": Jumlah kolom tidak sesuai";
          continue;
        }

        $studentData = array_combine($headers, $row);

        // Validasi required fields
        $required = ['name', 'email', 'password', 'student_id', 'grade_level', 'parent_name', 'parent_phone'];
        $missingFields = [];
        foreach ($required as $field) {
          if (empty($studentData[$field])) {
            $missingFields[] = $field;
          }
        }

        if (!empty($missingFields)) {
          $errors[] = "Baris " . ($index + 2) . ": Field wajib kosong - " . implode(', ', $missingFields);
          continue;
        }

        // Cek email unik
        $existingUser = $this->userModel->findByEmail($studentData['email']);
        if ($existingUser) {
          $errors[] = "Baris " . ($index + 2) . ": Email " . $studentData['email'] . " sudah digunakan";
          continue;
        }

        $studentsData[] = $studentData;
      }

      // Jika ada error validasi, kembalikan dengan error
      if (!empty($errors)) {
        return $response->redirect('/admin/schools/' . $schoolId . '/students/bulk-create?error=400&message=' . urlencode('Terdapat ' . count($errors) . ' error validasi'));
      }

      // Proses transaksi database
      $db = $this->userModel->getDb();
      $db->beginTransaction();

      try {
        $successCount = 0;
        $failedData = [];

        foreach ($studentsData as $studentData) {
          try {
            // 1. Buat user dengan role user
            $userId = $this->userModel->create([
              'email' => $studentData['email'],
              'password' => $studentData['password'],
              'name' => $studentData['name'],
              'role' => 'user',
              'is_active' => 1,
            ]);

            // 2. Buat profile
            $profileId = $this->profileModel->create([
              'user_id' => $userId,
              'phone' => $studentData['phone'] ?? '',
              'address' => $studentData['address'] ?? '',
              'birth_place' => $studentData['birth_place'] ?? '',
              'birth_date' => $studentData['birth_date'] ?? null,
              'gender' => $studentData['gender'] ?? '',
            ]);

            // 3. Buat student profile
            $this->studentModel->create([
              'profile_id' => $profileId,
              'school_id' => (int)$schoolId,
              'student_id' => $studentData['student_id'],
              'grade_level' => $studentData['grade_level'],
              'major' => $studentData['major'] ?? '',
              'parent_name' => $studentData['parent_name'],
              'parent_phone' => $studentData['parent_phone'],
              'parent_email' => $studentData['parent_email'] ?? '',
            ]);

            $successCount++;
          } catch (\Exception $e) {
            $failedData[] = [
              'data' => $studentData,
              'error' => $e->getMessage()
            ];
          }
        }

        $db->commit();

        // Set success message
        $message = "Berhasil mengimport {$successCount} siswa";
        if (!empty($failedData)) {
          $message .= " ({$successCount} berhasil, " . count($failedData) . " gagal)";
        }

        return $response->redirect('/admin/schools/' . $schoolId . '/students?success=' . urlencode($message));
      } catch (\Exception $e) {
        $db->rollBack();
        return $response->redirect('/admin/schools/' . $schoolId . '/students/bulk-create?error=500&message=' . urlencode('Gagal mengimport data: ' . $e->getMessage()));
      }
    } catch (\Exception $e) {
      return $response->redirect('/admin/schools?error=500&message=' . urlencode($e->getMessage()));
    }
  }

  /**
   * Form bulk input nilai untuk superadmin
   * Method ini untuk super-admin yang ingin input banyak nilai siswa sekaligus di sekolah tertentu
   */
  public function bulkInputScores(Request $request, Response $response): View | RedirectResponse
  {
    try {
      $schoolId = $request->param('id');
      $school = $this->schoolModel->find($schoolId);

      if (!$school) {
        return $response->redirect('/admin/schools?error=404&message=' . urlencode('Sekolah tidak ditemukan'));
      }

      return $response->renderPage(['school' => $school], ['meta' => ['title' => 'Input Nilai Massal']]);
    } catch (\Exception $e) {
      return $response->redirect('/admin/schools?error=500&message=' . urlencode($e->getMessage()));
    }
  }

  /**
   * Proses upload CSV dan simpan banyak nilai siswa sekaligus untuk superadmin
   * Format CSV: identifier,semester,subject,final_score,pengetahuan,keterampilan
   * identifier bisa NIS/NISN atau nama siswa
   */
  public function storeBulkScores(Request $request, Response $response): View | RedirectResponse
  {
    try {
      $schoolId = $request->param('id');
      $school = $this->schoolModel->find($schoolId);

      if (!$school) {
        return $response->redirect('/admin/schools?error=404&message=' . urlencode('Sekolah tidak ditemukan'));
      }

      // Validasi file upload
      $file = $request->file('csv_file');

      if (!$file) {
        return $response->redirect('/admin/schools/' . $schoolId . '/students/bulk-scores?error=400&message=' . urlencode('File CSV wajib diupload'));
      }

      // Validasi upload error
      if ($file->getError() !== UPLOAD_ERR_OK) {
        return $response->redirect('/admin/schools/' . $schoolId . '/students/bulk-scores?error=400&message=' . urlencode('Gagal upload file'));
      }

      // Validasi tipe file
      $allowedTypes = ['text/csv', 'text/plain', 'application/vnd.ms-excel'];
      if (!in_array($file->getClientMimeType(), $allowedTypes)) {
        return $response->redirect('/admin/schools/' . $schoolId . '/students/bulk-scores?error=400&message=' . urlencode('File harus berformat CSV'));
      }

      // Pindahkan file ke temporary directory untuk diproses
      $tempDir = sys_get_temp_dir() . '/bulk_import';
      if (!is_dir($tempDir)) {
        mkdir($tempDir, 0755, true);
      }

      $tempFilename = 'bulk_scores_' . time() . '_' . uniqid() . '.csv';
      $tempPath = $tempDir . '/' . $tempFilename;

      if (!$file->move($tempDir, $tempFilename)) {
        return $response->redirect('/admin/schools/' . $schoolId . '/students/bulk-scores?error=500&message=' . urlencode('Gagal menyimpan file CSV'));
      }

      // Baca file CSV
      $csvData = file_get_contents($tempPath);

      // Hapus file temporary setelah dibaca
      unlink($tempPath);

      $lines = explode("\n", $csvData);

      // Skip header dan ambil data
      $headers = str_getcsv(array_shift($lines));
      $scoresData = [];
      $errors = [];

      foreach ($lines as $index => $line) {
        if (empty(trim($line))) {
          continue;
        }

        $row = str_getcsv($line);
        if (count($row) < count($headers)) {
          $errors[] = "Baris " . ($index + 2) . ": Jumlah kolom tidak sesuai";
          continue;
        }

        $rowData = array_combine($headers, $row);

        // Validasi required fields
        $required = ['identifier', 'semester', 'subject', 'final_score'];
        $missingFields = [];
        foreach ($required as $field) {
          if (empty($rowData[$field])) {
            $missingFields[] = $field;
          }
        }

        if (!empty($missingFields)) {
          $errors[] = "Baris " . ($index + 2) . ": Field wajib kosong - " . implode(', ', $missingFields);
          continue;
        }

        // Validasi score format
        if (!is_numeric($rowData['final_score']) || $rowData['final_score'] < 0 || $rowData['final_score'] > 100) {
          $errors[] = "Baris " . ($index + 2) . ": final_score harus angka 0-100";
          continue;
        }

        if (isset($rowData['pengetahuan']) && !empty($rowData['pengetahuan'])) {
          if (!is_numeric($rowData['pengetahuan']) || $rowData['pengetahuan'] < 0 || $rowData['pengetahuan'] > 100) {
            $errors[] = "Baris " . ($index + 2) . ": pengetahuan harus angka 0-100";
            continue;
          }
        }

        if (isset($rowData['keterampilan']) && !empty($rowData['keterampilan'])) {
          if (!is_numeric($rowData['keterampilan']) || $rowData['keterampilan'] < 0 || $rowData['keterampilan'] > 100) {
            $errors[] = "Baris " . ($index + 2) . ": keterampilan harus angka 0-100";
            continue;
          }
        }

        $scoresData[] = $rowData;
      }

      // Jika ada error validasi, kembalikan dengan error
      if (!empty($errors)) {
        return $response->redirect('/admin/schools/' . $schoolId . '/students/bulk-scores?error=400&message=' . urlencode('Terdapat ' . count($errors) . ' error validasi'));
      }

      if (empty($scoresData)) {
        return $response->redirect('/admin/schools/' . $schoolId . '/students/bulk-scores?error=400&message=' . urlencode('Tidak ada data nilai untuk diproses'));
      }

      // Group data by semester
      $groupedBySemester = [];
      foreach ($scoresData as $row) {
        $semester = $row['semester'];
        if (!isset($groupedBySemester[$semester])) {
          $groupedBySemester[$semester] = [];
        }
        $groupedBySemester[$semester][] = $row;
      }

      // Process each semester batch
      $db = $this->studentModel->getDb();
      $db->beginTransaction();

      try {
        $totalSuccess = 0;
        $totalFailed = 0;
        $allErrors = [];

        foreach ($groupedBySemester as $semester => $semesterData) {
          $result = $this->studentModel->bulkUpdateAcademicScoresByIdentifier($semesterData, $semester, (int)$schoolId);

          $totalSuccess += $result['success'];
          $totalFailed += $result['failed'];
          $allErrors = array_merge($allErrors, $result['errors']);
        }

        $db->commit();

        // Set success message
        $message = "Berhasil mengupdate nilai {$totalSuccess} siswa";
        if ($totalFailed > 0) {
          $message .= " ({$totalSuccess} berhasil, {$totalFailed} gagal)";
        }

        // Store errors in session for display
        if (!empty($allErrors)) {
          $_SESSION['bulk_scores_errors'] = $allErrors;
        }

        return $response->redirect('/admin/schools/' . $schoolId . '/students?success=' . urlencode($message));
      } catch (\Exception $e) {
        $db->rollBack();
        return $response->redirect('/admin/schools/' . $schoolId . '/students/bulk-scores?error=500&message=' . urlencode('Gagal mengupdate data: ' . $e->getMessage()));
      }
    } catch (\Exception $e) {
      return $response->redirect('/admin/schools?error=500&message=' . urlencode($e->getMessage()));
    }
  }
}
