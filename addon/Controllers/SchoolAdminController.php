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
use App\Services\SessionService;

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
        private SessionService $session,
        private UserModel $userModel
    ) {}

    /**
     * Get current admin's school ID dari session
     */
    private function getAdminSchoolId(): int
    {
        return $_SESSION['admin.school_id'] ?? 0;
    }

    private function isSuperAdmin(): bool
    {
        return $this->session->get('auth.user_role') === 'super-admin';
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
            $is_super_admin = $this->isSuperAdmin();

            $studentId = $is_super_admin ? $request->param('student_id') : $request->param('id');
            $student = $this->studentModel->findByUserId($studentId);

            if (!$student) {
                return $response->redirect('/admin/students?error=404&message=' . urlencode('Siswa tidak ditemukan'));
            }

            // Validasi bahwa siswa ini berada di sekolah admin
            $schoolId = $is_super_admin ? $request->param('id')  : $this->getAdminSchoolId();

            if ($student['school_id'] != $schoolId) {
                return $response->redirect('/admin/students?error=403&message=' . urlencode('Anda tidak memiliki akses ke siswa ini'));
            }

            return $response->renderPage(
                ['student' => $student, 'is_super_admin' => $is_super_admin],
                ['meta' => ['title' => 'Detail Siswa'], 'path' => '/admin/students/:id']
            );
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
            $is_super_admin = $this->isSuperAdmin();

            $studentId = $is_super_admin ? $request->param('student_id') : $request->param('id');
            $student = $this->studentModel->findByUserId($studentId);

            if (!$student) {
                return $response->redirect('/admin/students?error=404&message=' . urlencode('Siswa tidak ditemukan'));
            }

            // Validasi bahwa siswa ini berada di sekolah admin
            $schoolId = $is_super_admin ? $request->param('id') : $this->getAdminSchoolId();
            if ($student['school_id'] != $schoolId) {
                return $response->redirect('/admin/students?error=403&message=' . urlencode('Anda tidak memiliki akses ke siswa ini'));
            }

            return $response->renderPage(
                ['student' => $student, 'is_super_admin' => $is_super_admin],
                ['meta' => ['title' => 'Edit Siswa'], 'path' => '/admin/students/:id/edit']
            );
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
            $is_super_admin = $this->isSuperAdmin();

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
            if ($student['school_id'] != $schoolId && !$is_super_admin) {
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

            return $response->redirect($is_super_admin ? '/admin/schools/'  . $student['school_id'] . '/students/' . $studentId : '/admin/students/' . $studentId);
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
            $is_super_admin = $this->isSuperAdmin();

            $studentId = $is_super_admin ? $request->param('student_id') : $request->param('id');
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
            $schoolId = $is_super_admin ? $request->param('id') : $this->getAdminSchoolId();
            if ($student['school_id'] != $schoolId) {
                return $response->redirect('/admin/students?error=403&message=' . urlencode('Anda tidak memiliki akses ke siswa ini'));
            }

            $this->userModel->deleteById($user['id']);

            return $response->redirect($is_super_admin ? '/admin/schools/' . $schoolId . '/students' :  '/admin/students');
        } catch (\Exception $e) {
            return $response->redirect('/admin/students?error=500&message=' . urlencode($e->getMessage()));
        }
    }

    /**
     * Form bulk import siswa (CSV upload)
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
     * Proses upload CSV dan simpan banyak siswa sekaligus
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
                $_SESSION['bulk_import_errors'] = $errors;
                $_SESSION['bulk_import_data'] = $studentsData;
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
                            'school_id' => $schoolId,
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
                    $_SESSION['bulk_import_failed'] = $failedData;
                }

                return $response->redirect('/admin/schools/' . $schoolId . '/students?success=1&message=' . urlencode($message));
            } catch (\Exception $e) {
                $db->rollBack();
                return $response->redirect('/admin/schools/' . $schoolId . '/students/bulk-create?error=500&message=' . urlencode('Gagal mengimport data: ' . $e->getMessage()));
            }
        } catch (\Exception $e) {
            return $response->redirect('/admin/schools?error=500&message=' . urlencode($e->getMessage()));
        }
    }
}
