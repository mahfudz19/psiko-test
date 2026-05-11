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

/**
 * School Admin Controller
 * 
 * Controller untuk role admin (guru BK) mengelola sekolah sendiri dan siswa di sekolahnya.
 * Berbeda dengan AdminController yang untuk super-admin, controller ini memiliki akses terbatas.
 */
class SchoolAdminController
{
    public function __construct(
        private SchoolModel $schoolModel,
        private TeacherProfileModel $teacherModel,
        private StudentProfileModel $studentModel,
        private ProfileModel $profileModel,
        private UserModel $userModel
    ) {}

    /**
     * Get current admin's school ID dari session
     */
    private function getAdminSchoolId(): int
    {
        return $_SESSION['admin.school_id'] ?? 0;
    }

    /**
     * Dashboard sekolah sendiri - menampilkan statistik
     */
    public function mySchool(Request $request, Response $response): View | RedirectResponse
    {
        try {
            $schoolId = $this->getAdminSchoolId();
            $school = $this->schoolModel->find($schoolId);

            if (!$school) {
                return $response->redirect('/admin/schools/my?error=404&message=' . urlencode('Sekolah tidak ditemukan'));
            }

            $teachers = $this->teacherModel->findBySchoolId($schoolId);
            $students = $this->studentModel->findBySchoolId($schoolId);

            $data = [
                'school' => $school,
                'teachers' => $teachers,
                'students' => $students,
                'totalTeachers' => count($teachers),
                'totalStudents' => count($students),
            ];

            return $response->renderPage($data, [
                'meta' => ['title' => 'Dashboard Sekolah']
            ]);
        } catch (\Exception $e) {
            return $response->redirect('/dashboard?error=500&message=' . urlencode($e->getMessage()));
        }
    }

    /**
     * Form edit sekolah sendiri
     */
    public function editMySchool(Request $request, Response $response): View | RedirectResponse
    {
        try {
            $schoolId = $this->getAdminSchoolId();
            $school = $this->schoolModel->find($schoolId);

            if (!$school) {
                return $response->redirect('/admin/schools/my?error=404&message=' . urlencode('Sekolah tidak ditemukan'));
            }

            return $response->renderPage(['school' => $school], [
                'meta' => ['title' => 'Edit Sekolah']
            ]);
        } catch (\Exception $e) {
            return $response->redirect('/admin/schools/my?error=500&message=' . urlencode($e->getMessage()));
        }
    }

    /**
     * Proses update sekolah sendiri
     */
    public function updateMySchool(Request $request, Response $response): View | RedirectResponse
    {
        try {
            $schoolId = $this->getAdminSchoolId();
            $school = $this->schoolModel->find($schoolId);

            if (!$school) {
                return $response->redirect('/admin/schools/my?error=404&message=' . urlencode('Sekolah tidak ditemukan'));
            }

            $data = $request->input();

            // Validasi field yang diperlukan
            $required = ['name', 'npsn', 'address', 'principal_name', 'contact', 'accreditation'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    return $response->redirect('/admin/schools/my/edit?error=400&message=' . urlencode('Field ' . $field . ' wajib diisi'));
                }
            }

            $this->schoolModel->updateById($schoolId, $data);

            return $response->redirect('/admin/schools/my');
        } catch (\Exception $e) {
            return $response->redirect('/admin/schools/my/edit?error=500&message=' . urlencode($e->getMessage()));
        }
    }

    /**
     * Daftar siswa di sekolah sendiri
     */
    public function students(Request $request, Response $response): View | RedirectResponse
    {
        try {
            $schoolId = $this->getAdminSchoolId();
            $keyword = $request->input('search', '');

            $students = $this->studentModel->findBySchoolId($schoolId);

            return $response->renderPage(
                ['students' => $students, 'keyword' => $keyword],
                ['meta' => ['title' => 'Daftar Siswa']]
            );
        } catch (\Exception $e) {
            return $response->redirect('/admin/schools/my?error=500&message=' . urlencode($e->getMessage()));
        }
    }

    /**
     * Form tambah siswa
     */
    public function createStudent(Request $request, Response $response): View | RedirectResponse
    {
        try {
            $schoolId = $this->getAdminSchoolId();
            $school = $this->schoolModel->find($schoolId);

            if (!$school) {
                return $response->redirect('/admin/students?error=404&message=' . urlencode('Sekolah tidak ditemukan'));
            }

            return $response->renderPage(['school' => $school], ['meta' => ['title' => 'Tambah Siswa']]);
        } catch (\Exception $e) {
            return $response->redirect('/admin/students?error=500&message=' . urlencode($e->getMessage()));
        }
    }

    /**
     * Proses simpan siswa baru
     */
    public function storeStudent(Request $request, Response $response): View | RedirectResponse
    {
        try {
            $schoolId = $this->getAdminSchoolId();
            $data = $request->input();

            // Validasi
            $required = ['name', 'email', 'password', 'student_id', 'grade_level', 'parent_name', 'parent_phone'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    return $response->redirect('/admin/students/create?error=400&message=' . urlencode('Field ' . $field . ' wajib diisi'));
                }
            }

            // Cek email unique
            $existingUser = $this->userModel->findByEmail($data['email']);
            if ($existingUser) {
                return $response->redirect('/admin/students/create?error=400&message=' . urlencode('Email sudah digunakan'));
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

                // 3. Buat student_profiles dengan school_id admin
                $this->studentModel->create([
                    'profile_id' => $profileId,
                    'school_id' => $schoolId,
                    'student_id' => $data['student_id'],
                    'grade_level' => $data['grade_level'],
                    'major' => $data['major'] ?? '',
                    'parent_name' => $data['parent_name'],
                    'parent_phone' => $data['parent_phone'],
                    'parent_email' => $data['parent_email'] ?? '',
                ]);

                $db->commit();

                return $response->redirect('/admin/students');
            } catch (\Exception $e) {
                $db->rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            return $response->redirect('/admin/students/create?error=500&message=' . urlencode($e->getMessage()));
        }
    }

    /**
     * Detail siswa
     */
    public function showStudent(Request $request, Response $response): View | RedirectResponse
    {
        try {
            $studentId = $request->param('id');
            $student = $this->studentModel->findByUserId($studentId);

            if (!$student) {
                return $response->redirect('/admin/students?error=404&message=' . urlencode('Siswa tidak ditemukan'));
            }

            // Validasi bahwa siswa ini berada di sekolah admin
            $schoolId = $this->getAdminSchoolId();
            if ($student['school_id'] != $schoolId) {
                return $response->redirect('/admin/students?error=403&message=' . urlencode('Anda tidak memiliki akses ke siswa ini'));
            }

            return $response->renderPage(['student' => $student], ['meta' => ['title' => 'Detail Siswa']]);
        } catch (\Exception $e) {
            return $response->redirect('/admin/students?error=500&message=' . urlencode($e->getMessage()));
        }
    }

    /**
     * Form edit siswa
     */
    public function editStudent(Request $request, Response $response): View | RedirectResponse
    {
        try {
            $studentId = $request->param('id');
            $student = $this->studentModel->findByUserId($studentId);

            if (!$student) {
                return $response->redirect('/admin/students?error=404&message=' . urlencode('Siswa tidak ditemukan'));
            }

            // Validasi bahwa siswa ini berada di sekolah admin
            $schoolId = $this->getAdminSchoolId();
            if ($student['school_id'] != $schoolId) {
                return $response->redirect('/admin/students?error=403&message=' . urlencode('Anda tidak memiliki akses ke siswa ini'));
            }

            return $response->renderPage(['student' => $student], ['meta' => ['title' => 'Edit Siswa']]);
        } catch (\Exception $e) {
            return $response->redirect('/admin/students?error=500&message=' . urlencode($e->getMessage()));
        }
    }

    /**
     * Proses update siswa
     */
    public function updateStudent(Request $request, Response $response): View | RedirectResponse
    {
        try {
            $studentId = $request->param('id');
            $profile = $this->profileModel->findByUserId($studentId);
            if (!$profile) {
                return $response->redirect('/admin/students?error=404&message=' . urlencode('Profile siswa tidak ditemukan'));
            }
            $student = $this->studentModel->findByProfileId($profile['id']);

            if (!$student) {
                return $response->redirect('/admin/students?error=404&message=' . urlencode('Siswa tidak ditemukan'));
            }

            // Validasi bahwa siswa ini berada di sekolah admin
            $schoolId = $this->getAdminSchoolId();
            if ($student['school_id'] != $schoolId) {
                return $response->redirect('/admin/students?error=403&message=' . urlencode('Anda tidak memiliki akses ke siswa ini'));
            }

            $data = $request->input();
            // Validasi field yang diperlukan
            $required = ['student_id', 'grade_level', 'parent_name', 'parent_phone'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    return $response->redirect('/admin/students/' . $studentId . '/edit?error=400&message=' . urlencode('Field ' . $field . ' wajib diisi'));
                }
            }

            $profileData = [
                'phone' => $data['phone'] ?? '',
                'address' => $data['address'] ?? '',
            ];

            $this->profileModel->updateById($profile['id'], $profileData);

            $studentData = [
                'student_id' => $data['student_id'],
                'grade_level' => $data['grade_level'],
                'major' => $data['major'] ?? '',
                'parent_name' => $data['parent_name'],
                'parent_phone' => $data['parent_phone'],
                'parent_email' => $data['parent_email'] ?? '',
            ];
            $this->studentModel->updateById($student['id'], $studentData);

            return $response->redirect('/admin/students/' . $studentId);
        } catch (\Exception $e) {
            return $response->redirect('/admin/students/' . $request->param('id') . '/edit?error=500&message=' . urlencode($e->getMessage()));
        }
    }

    /**
     * Hapus siswa
     */
    public function deleteStudent(Request $request, Response $response): View | RedirectResponse
    {
        try {
            $studentId = $request->param('id');
            $user = $this->userModel->find($studentId);
            if (!$user) {
                return $response->redirect('/admin/students?error=404&message=' . urlencode('Siswa tidak ditemukan'));
            }
            $profile = $this->profileModel->findByUserId($studentId);
            if (!$profile) {
                return $response->redirect('/admin/students?error=404&message=' . urlencode('Profile siswa tidak ditemukan'));
            }
            $student = $this->studentModel->findByProfileId($profile['id']);
            if (!$student) {
                return $response->redirect('/admin/students?error=404&message=' . urlencode('Siswa tidak ditemukan'));
            }

            // Validasi bahwa siswa ini berada di sekolah admin
            $schoolId = $this->getAdminSchoolId();
            if ($student['school_id'] != $schoolId) {
                return $response->redirect('/admin/students?error=403&message=' . urlencode('Anda tidak memiliki akses ke siswa ini'));
            }

            $this->userModel->deleteById($user['id']);

            return $response->redirect('/admin/students');
        } catch (\Exception $e) {
            return $response->redirect('/admin/students?error=500&message=' . urlencode($e->getMessage()));
        }
    }
}
