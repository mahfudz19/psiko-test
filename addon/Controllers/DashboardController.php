<?php

namespace Addon\Controllers;

use Addon\Models\ProfileModel;
use Addon\Models\StudentProfileModel;
use Addon\Models\PmbJourneyModel;
use Addon\Models\UserModel;
use Addon\Services\ScholarshipCalculator;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\View\View;
use App\Services\SessionService;

/**
 * DashboardController - Menangani tampilan dashboard untuk berbagai role
 */
class DashboardController
{
    public function __construct(
        private SessionService $session,
        private ProfileModel $profileModel,
        private StudentProfileModel $studentModel,
        private PmbJourneyModel $pmbJourneyModel,
        private UserModel $userModel,
        private ScholarshipCalculator $scholarshipCalculator,
        private \Addon\Models\SchoolModel $schoolModel,
        private \Addon\Models\TeacherProfileModel $teacherModel,
        private \Addon\Models\ChatConsultationModel $chatModel
    ) {}

    /**
     * Menampilkan halaman dashboard utama
     * 
     * @param Request $request
     * @param Response $response
     * @return View
     */
    public function index(Request $request, Response $response): View
    {
        $userId = $this->session->get('auth.user_id');
        $role = $this->session->get('auth.user_role');
        $userName = $this->session->get('auth.user_name');

        // Default data
        $data = [
            'userName' => $userName,
            'role' => $role,
        ];

        // Logic khusus untuk Student (role: user)
        if ($role === 'user') {
            $data = array_merge($data, $this->getStudentDashboardData($userId));
            return $response->renderPage($data, [
                'meta' => ['title' => 'Dashboard | ' . env('APP_NAME')]
            ]);
        }

        // Logic khusus untuk School Admin (role: admin)
        if ($role === 'admin') {
            $data = array_merge($data, $this->getSchoolAdminDashboardData($userId));
            return $response->renderPage($data, [
                'path' => '/dashboard/admin',
                'meta' => ['title' => 'Admin Dashboard | ' . env('APP_NAME')]
            ]);
        }

        // Logic khusus untuk Super Admin (role: super-admin)
        if ($role === 'super-admin') {
            $data = array_merge($data, $this->getSuperAdminDashboardData($userId));
            return $response->renderPage($data, [
                'path' => '/dashboard/super-admin',
                'meta' => ['title' => 'Super Admin Dashboard | ' . env('APP_NAME')]
            ]);
        }

        return $response->renderPage($data, [
            'meta' => ['title' => 'Dashboard | ' . env('APP_NAME')]
        ]);
    }

    /**
     * Mengambil data lengkap untuk dashboard student
     * 
     * @param int $userId
     * @return array
     */
    private function getStudentDashboardData(int $userId): array
    {
        $profile = $this->profileModel->findByUserId($userId);
        $studentProfile = $profile ? $this->studentModel->findByProfileId($profile['id']) : null;
        $pmbJourney = $studentProfile ? $this->pmbJourneyModel->findByStudentId($studentProfile['id']) : null;

        // 1. Hitung Profile Progress
        $progress = $this->calculateProfileProgress($profile, $studentProfile);

        // 2. Ambil Match Score & Top Major
        $matchScore = 0;
        $topMajor = 'Belum Dianalisis';
        if ($pmbJourney && !empty($pmbJourney['top_matches'])) {
            $matches = json_decode($pmbJourney['top_matches'], true);
            if (!empty($matches) && is_array($matches)) {
                $matchScore = $matches[0]['match_score'] ?? 0;
                $topMajor = $matches[0]['major_name'] ?? 'Belum Dianalisis';
            }
        }

        // 3. Ambil AI Recommendations
        $aiRecs = [];
        if ($studentProfile && !empty($studentProfile['ai_analysis'])) {
            $analysis = json_decode($studentProfile['ai_analysis'], true);
            $aiRecs = $analysis['recommendations'] ?? [];
        }

        // 4. PMB Status
        $pmbStatus = $pmbJourney['simulation_status'] ?? 'not_started';

        // 5. Scholarship Eligibility
        $scholarships = [];
        if ($studentProfile) {
            $eligibility = $this->scholarshipCalculator->calculateEligibility([
                'academic_scores' => $studentProfile['academic_scores'],
                'achievements' => $studentProfile['achievements'],
                'ai_analysis' => $studentProfile['ai_analysis']
            ]);
            $scholarships = $eligibility['eligible_scholarships'] ?? [];
        }

        return [
            'profileProgress' => $progress,
            'matchScore' => $matchScore,
            'topMajor' => $topMajor,
            'aiRecommendations' => array_slice($aiRecs, 0, 3), // Ambil 3 saja
            'pmbStatus' => $pmbStatus,
            'eligibleScholarshipsCount' => count($scholarships),
            'studentProfile' => $studentProfile,
            'profile' => $profile
        ];
    }

    /**
     * Menghitung persentase kelengkapan profil
     * 
     * @param array|null $profile
     * @param array|null $studentProfile
     * @return int
     */
    private function calculateProfileProgress(?array $profile, ?array $studentProfile): int
    {
        if (!$profile) return 0;

        $fields = [
            'phone' => $profile['phone'],
            'address' => $profile['address'],
            'birth_date' => $profile['birth_date'],
            'gender' => $profile['gender'],
        ];

        if ($studentProfile) {
            $fields = array_merge($fields, [
                'school_id' => $studentProfile['school_id'],
                'grade_level' => $studentProfile['grade_level'],
                'major' => $studentProfile['major'],
                'academic_scores' => $studentProfile['academic_scores'],
                'psychological_tests' => $studentProfile['psychological_tests'],
            ]);
        }

        $filled = 0;
        foreach ($fields as $value) {
            if (!empty($value)) $filled++;
        }

        return (int) (($filled / count($fields)) * 100);
    }

    /**
     * Mengambil data statistik untuk dashboard admin sekolah
     *
     * @param int $userId
     * @return array
     */
    private function getSchoolAdminDashboardData(int $userId): array
    {
        $profile = $this->profileModel->findByUserId($userId);
        $teacherProfile = $profile ? $this->teacherModel->findByProfileId($profile['id']) : null;
        $schoolId = $teacherProfile['school_id'] ?? 0;
        $school = $schoolId ? $this->schoolModel->find($schoolId) : null;

        if (!$school) {
            return ['error' => 'Sekolah tidak ditemukan'];
        }

        $students = $this->studentModel->findBySchoolId($schoolId);
        $teachers = $this->teacherModel->findBySchoolId($schoolId);

        // 1. Hitung Statistik Kelengkapan
        $totalStudents = count($students);
        $completedProfiles = 0;
        $completedPsychotests = 0;
        $completedAiAnalysis = 0;

        $majorDistribution = [];

        foreach ($students as $student) {
            // Cek kelengkapan profil (sederhana: jika ada phone & address)
            if (!empty($student['phone']) && !empty($student['address'])) {
                $completedProfiles++;
            }

            // Cek psikotes
            if (!empty($student['psychological_tests']) && $student['psychological_tests'] !== '[]') {
                $completedPsychotests++;
            }

            // Cek AI Analysis & Distribusi Jurusan
            if (!empty($student['ai_analysis'])) {
                $completedAiAnalysis++;
                $analysis = json_decode($student['ai_analysis'], true);
                if (!empty($analysis['recommendations'])) {
                    foreach ($analysis['recommendations'] as $rec) {
                        $major = $rec['field'] ?? 'Lainnya';
                        $majorDistribution[$major] = ($majorDistribution[$major] ?? 0) + 1;
                    }
                }
            }
        }

        // Sort major distribution
        arsort($majorDistribution);
        $topMajors = array_slice($majorDistribution, 0, 5);

        return [
            'school' => $school,
            'stats' => [
                'totalStudents' => $totalStudents,
                'totalTeachers' => count($teachers),
                'completionRate' => $totalStudents > 0 ? round(($completedProfiles / $totalStudents) * 100) : 0,
                'psychotestRate' => $totalStudents > 0 ? round(($completedPsychotests / $totalStudents) * 100) : 0,
                'aiAnalysisRate' => $totalStudents > 0 ? round(($completedAiAnalysis / $totalStudents) * 100) : 0,
            ],
            'topMajors' => $topMajors,
            'recentStudents' => array_slice($students, 0, 5) // 5 siswa terbaru
        ];
    }

    /**
     * Mengambil data statistik untuk dashboard super admin
     *
     * @param int $userId
     * @return array
     */
    private function getSuperAdminDashboardData(int $userId): array
    {
        // 1. Statistik Platform-wide
        $allUsers = $this->userModel->all();
        $allSchools = $this->schoolModel->all();
        $allStudents = $this->studentModel->all();
        $allTeachers = $this->teacherModel->all();

        $totalUsers = count($allUsers);
        $totalSchools = count($allSchools);
        $totalStudents = count($allStudents);
        $totalTeachers = count($allTeachers);

        // 2. Hitung user by role
        $usersByRole = ['user' => 0, 'admin' => 0, 'super-admin' => 0, 'staff' => 0];
        foreach ($allUsers as $user) {
            $role = $user['role'] ?? 'user';
            $usersByRole[$role] = ($usersByRole[$role] ?? 0) + 1;
        }

        // 3. Hitung AI Analysis Rate (siswa yang sudah dapat rekomendasi AI)
        $studentsWithAi = 0;
        foreach ($allStudents as $student) {
            if (!empty($student['ai_analysis']) && $student['ai_analysis'] !== '[]') {
                $studentsWithAi++;
            }
        }
        $aiAnalysisRate = $totalStudents > 0 ? round(($studentsWithAi / $totalStudents) * 100) : 0;

        // 4. Sekolah Terbaru (5 terbaru)
        $newestSchools = array_slice(array_reverse($allSchools), 0, 5);

        // 5. Pengguna Baru Registrasi (5 terbaru)
        $newestUsers = array_slice(array_reverse($allUsers), 0, 5);

        // 6. Hitung total konsultasi chat
        try {
            $allChats = $this->chatModel->all();
            $totalConsultations = count($allChats);
        } catch (\Throwable $e) {
            // Model mungkin belum ada, abaikan
            $totalConsultations = 0;
        }

        return [
            'stats' => [
                'totalUsers' => $totalUsers,
                'totalSchools' => $totalSchools,
                'totalStudents' => $totalStudents,
                'totalTeachers' => $totalTeachers,
                'totalConsultations' => $totalConsultations,
                'aiAnalysisRate' => $aiAnalysisRate,
                'usersByRole' => $usersByRole,
            ],
            'newestSchools' => $newestSchools,
            'newestUsers' => $newestUsers,
        ];
    }
}
