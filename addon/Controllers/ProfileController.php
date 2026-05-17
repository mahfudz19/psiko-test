<?php

namespace Addon\Controllers;

use Addon\Models\ProfileModel;
use Addon\Models\StudentProfileModel;
use Addon\Models\TeacherProfileModel;
use Addon\Models\StaffProfileModel;
use Addon\Models\UserModel;
use Addon\Models\SchoolModel;
use Addon\Models\PmbJourneyModel;
use Addon\Models\TestResultModel;
use Addon\Services\ScholarshipCalculator;
use App\Core\Http\RedirectResponse;
use App\Services\SessionService;
use App\Exceptions\AuthorizationException;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\View\View;

/**
 * Profile Controller - CRUD Profile + All Business Logic
 * 
 * Handles all profile-related operations for all roles:
 * - user (siswa): Personal data, academic data, psychotest results
 * - admin (guru BK): Managed students, counseling schedule
 * - super-admin: Full access to all profiles
 */
class ProfileController
{
    public function __construct(
        private SessionService $session,
        private ProfileModel $profileModel,
        private StudentProfileModel $studentModel,
        private TeacherProfileModel $teacherModel,
        private StaffProfileModel $staffModel,
        private UserModel $userModel,
        private SchoolModel $schoolModel,
        private PmbJourneyModel $pmbJourneyModel,
        private TestResultModel $testResultModel
    ) {}

    /**
     * Show profile page based on user role
     */
    public function show(Request $request, Response $response): View
    {
        $currentUser = $this->session->get('auth.user_id');
        $currentRole = $this->session->get('auth.user_role');
        $profileId = $request->param('id') ?? null;

        // Get current user's profile ID
        $userProfile = $this->profileModel->findByUserId($currentUser);
        if (!$userProfile) {
            // Create profile if not exists
            $profileId = $this->profileModel->createForUser($currentUser, $currentRole);
            $userProfile = $this->profileModel->find($profileId);

            // Create role-specific profile
            $this->createRoleSpecificProfile($profileId, $currentRole);
        }

        // If no profile ID specified, use current user's profile
        if (!$profileId) {
            $profileId = $userProfile['id'];
        }

        // Authorization check
        $this->authorizeAccess($profileId, $currentRole, $userProfile['id']);

        // Get profile data based on role
        $profile = $this->getProfileData($profileId, $currentRole);

        return $response->renderPage([
            'profile' => $profile,
            'role' => $currentRole
        ], ['path' => '/profile', 'meta' => ['title' => 'Profile | ' . env('APP_NAME')]]);
    }

    /**
     * Show edit profile form
     */
    public function edit(Request $request, Response $response): View | RedirectResponse
    {
        $currentUser = $this->session->get('auth.user_id');
        $currentRole = $this->session->get('auth.user_role');
        $profileId = $request->param('id') ?? null;

        $userProfile = $this->profileModel->findByUserId($currentUser);
        if (!$userProfile) {
            return $response->redirect('/profile?error=404&message=' . urlencode('Profile not found'));
        }

        if (!$profileId) {
            $profileId = $userProfile['id'];
        }

        // Authorization check
        $this->authorizeAccess($profileId, $currentRole, $userProfile['id']);

        $profile = $this->getProfileData($profileId, $currentRole);

        return $response->renderPage(
            ['profile' => $profile, 'role' => $currentRole],
            ['meta' => ['title' => 'Edit Profile | ' . env('APP_NAME')]]
        );
    }

    /**
     * Update profile data
     */
    public function update(Request $request, Response $response)
    {
        $currentUser = $this->session->get('auth.user_id');
        $currentRole = $this->session->get('auth.user_role');
        $profileId = $request->param('id') ?? null;

        $userProfile = $this->profileModel->findByUserId($currentUser);
        if (!$userProfile) {
            return $response->redirect('/profile/edit?error=404&message=' . urlencode('Profile not found'));
        }

        if (!$profileId) {
            $profileId = $userProfile['id'];
        }

        // Authorization check
        $this->authorizeAccess($profileId, $currentRole, $userProfile['id']);

        $data = $request->getBody();

        // Helper function untuk convert empty string ke null
        $clean = fn($value) => $value === '' ? null : $value;

        // Update base profile data
        $baseData = [
            'phone' => $clean($data['phone'] ?? null),
            'address' => $clean($data['address'] ?? null),
            'birth_place' => $clean($data['birth_place'] ?? null),
            'birth_date' => $clean($data['birth_date'] ?? null),
            'gender' => $clean($data['gender'] ?? null),
            'social_media' => !empty($data['social_media']) ? json_encode($data['social_media']) : null
        ];

        $this->profileModel->updateById($profileId, $baseData);

        // Update role-specific data
        $this->updateRoleSpecificData($profileId, $currentRole, $data);

        // Update user name if provided
        if (!empty($data['name'])) {
            $this->userModel->updateById($currentUser, ['name' => $data['name']]);
            $userEmail = $this->session->get('auth.user_email');
            $this->session->set('user', ['id' => $currentUser, 'name' => $data['name'], 'email' => $userEmail, 'role' => $currentRole]);
        }

        return $response->redirect('/profile?success=' . urlencode('Profile berhasil diperbarui'));
    }

    /**
     * Upload avatar
     */
    public function uploadAvatar(Request $request, Response $response)
    {
        $currentUser = $this->session->get('auth.user_id');
        $currentRole = $this->session->get('auth.user_role');

        $userProfile = $this->profileModel->findByUserId($currentUser);
        if (!$userProfile) {
            return $response->setStatusCode(404)->setContent(json_encode(['error' => 'Profile not found']));
        }

        $file = $request->file('avatar');
        if (!$file) {
            return $response->setStatusCode(400)->setContent(json_encode(['error' => 'No file uploaded']));
        }

        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file->getClientMimeType(), $allowedTypes)) {
            return $response->setStatusCode(400)->setContent(json_encode(['error' => 'File type not allowed']));
        }

        // Validate file size (max 2MB)
        if ($file->getSize() > 2 * 1024 * 1024) {
            return $response->setStatusCode(400)->setContent(json_encode(['error' => 'File size too large (max 2MB)']));
        }

        // Generate unique filename
        $extension = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
        $filename = 'avatar_' . $currentUser . '_' . time() . '.' . $extension;
        $uploadDir = __DIR__ . '/../../public/uploads/avatars';

        // Ensure directory exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Move file using UploadedFile's move() method
        if ($file->move($uploadDir, $filename)) {
            $avatarUrl = '/uploads/avatars/' . $filename;

            // Update profile
            $this->profileModel->updateById($userProfile['id'], ['avatar' => $avatarUrl]);

            $response->setStatusCode(200);
            $response->setHeader('Content-Type', 'application/json');
            return $response->setContent(json_encode(['success' => true, 'avatar_url' => $avatarUrl]));
        }

        return $response->setStatusCode(500)->setContent(json_encode(['error' => 'Failed to upload file']));
    }

    /**
     * Get student academic data
     */
    public function academic(Request $request, Response $response): View
    {
        $currentUser = $this->session->get('auth.user_id');
        $currentRole = $this->session->get('auth.user_role');

        if ($currentRole !== 'user') {
            throw new AuthorizationException('Forbidden');
        }

        $userProfile = $this->profileModel->findByUserId($currentUser);
        $studentProfile = $this->studentModel->findByProfileId($userProfile['id']);
        $schools = $this->schoolModel->all();

        return $response->renderPage([
            'profile' => $userProfile,
            'studentProfile' => $studentProfile,
            'schools' => $schools
        ], ['path' => '/profile/academic', 'meta' => ['title' => 'Data Akademik | ' . env('APP_NAME')]]);
    }

    /**
     * Update academic data
     */
    public function updateAcademic(Request $request, Response $response)
    {
        $data = $request->getBody();
        $currentUser = $this->session->get('auth.user_id');
        $currentRole = $this->session->get('auth.user_role');

        if ($currentRole !== 'user') {
            throw new AuthorizationException('Forbidden');
        }

        $userProfile = $this->profileModel->findByUserId($currentUser);


        // Helper function untuk convert empty string ke null
        $clean = fn($value) => $value === '' ? null : $value;

        // Handle academic scores dari input JSON hidden field
        $academicScoresJson = null;
        if (!empty($data['academic_scores_json'])) {
            $decoded = json_decode($data['academic_scores_json'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                // Konversi string numerik menjadi float/int agar lolos validasi is_numeric di model
                foreach ($decoded as &$semester) {
                    if (isset($semester['subjects']) && is_array($semester['subjects'])) {
                        foreach ($semester['subjects'] as &$subject) {
                            if (isset($subject['final_score']) && $subject['final_score'] !== '') {
                                $subject['final_score'] = $subject['final_score'] + 0;
                            } else {
                                unset($subject['final_score']);
                            }
                        }
                    }
                }
                $academicScoresJson = json_encode($decoded);
            }
        }

        $academicData = [
            'student_id' => $clean($data['student_id'] ?? null),
            'grade_level' => $clean($data['grade_level'] ?? null),
            'major' => $clean($data['major'] ?? null),
            'academic_scores' => $academicScoresJson,
            'parent_name' => $clean($data['parent_name'] ?? null),
            'parent_phone' => $clean($data['parent_phone'] ?? null),
            'parent_email' => $clean($data['parent_email'] ?? null)
        ];

        $studentProfile = $this->studentModel->findByProfileId($userProfile['id']);
        if ($studentProfile) {
            $this->studentModel->updateByProfileId($userProfile['id'], $academicData);
        } else {
            $this->studentModel->createForProfile($userProfile['id']);
            $this->studentModel->updateByProfileId($userProfile['id'], $academicData);
        }

        return $response->redirect('/profile/academic?success=' . urlencode('Data akademik berhasil diperbarui'));
    }

    /**
     * Get achievements data
     */
    public function achievements(Request $request, Response $response): View
    {
        $currentUser = $this->session->get('auth.user_id');
        $currentRole = $this->session->get('auth.user_role');

        if ($currentRole !== 'user') {
            throw new AuthorizationException('Forbidden');
        }

        $userProfile = $this->profileModel->findByUserId($currentUser);
        $studentProfile = $this->studentModel->findByProfileId($userProfile['id']);

        return $response->renderPage([
            'profile' => $userProfile,
            'studentProfile' => $studentProfile
        ], ['path' => '/profile/achievements', 'meta' => ['title' => 'Prestasi & Ekstrakurikuler | ' . env('APP_NAME')]]);
    }

    /**
     * Update achievements
     */
    public function updateAchievements(Request $request, Response $response)
    {
        $currentUser = $this->session->get('auth.user_id');
        $currentRole = $this->session->get('auth.user_role');

        if ($currentRole !== 'user') {
            throw new AuthorizationException('Forbidden');
        }

        $userProfile = $this->profileModel->findByUserId($currentUser);
        $data = $request->getBody();

        $studentProfile = $this->studentModel->findByProfileId($userProfile['id']);

        // Helper function to clean empty strings
        $clean = fn($value) => $value === '' ? null : $value;

        $achievementData = [];

        // Update extracurricular (Map array of columns to array of objects)
        if (!empty($data['extracurricular']['name'])) {
            $extra = [];
            foreach ($data['extracurricular']['name'] as $idx => $name) {
                if (!empty($name)) {
                    $extra[] = [
                        'name' => $name,
                        'position' => $clean($data['extracurricular']['position'][$idx] ?? null),
                        'year_start' => $clean($data['extracurricular']['year_start'][$idx] ?? null),
                        'year_end' => $clean($data['extracurricular']['year_end'][$idx] ?? null),
                        'description' => $clean($data['extracurricular']['description'][$idx] ?? null),
                    ];
                }
            }
            $achievementData['extracurricular'] = json_encode($extra);
        } else {
            $achievementData['extracurricular'] = json_encode([]);
        }

        // Update achievements (Map array of columns to array of objects)
        if (!empty($data['achievements']['name'])) {
            $ach = [];
            foreach ($data['achievements']['name'] as $idx => $name) {
                if (!empty($name)) {
                    $ach[] = [
                        'name' => $name,
                        'rank' => $clean($data['achievements']['rank'][$idx] ?? null),
                        'level' => $clean($data['achievements']['level'][$idx] ?? null),
                        'year' => $clean($data['achievements']['year'][$idx] ?? null),
                        'organizer' => $clean($data['achievements']['organizer'][$idx] ?? null),
                        'description' => $clean($data['achievements']['description'][$idx] ?? null),
                    ];
                }
            }
            $achievementData['achievements'] = json_encode($ach);
        } else {
            $achievementData['achievements'] = json_encode([]);
        }

        if ($studentProfile && !empty($achievementData)) {
            $this->studentModel->updateByProfileId($userProfile['id'], $achievementData);
        }

        return $response->redirect('/profile/achievements?success=' . urlencode('Data prestasi berhasil diperbarui'));
    }

    /**
     * Get RIASEC test results with AI analysis
     */
    public function results(Request $request, Response $response): View
    {
        $currentUser = $this->session->get('auth.user_id');
        $currentRole = $this->session->get('auth.user_role');

        if ($currentRole !== 'user') {
            throw new AuthorizationException('Forbidden');
        }

        $userProfile = $this->profileModel->findByUserId($currentUser);
        $studentProfile = $this->studentModel->findByProfileId($userProfile['id']);

        // Get latest RIASEC test result from TestResultModel
        $riasecResult = null;
        if ($studentProfile) {
            $riasecResult = $this->testResultModel->getLatestRiasecResult($studentProfile['id']);
        }

        return $response->renderPage(
            [
                'profile' => $userProfile,
                'studentProfile' => $studentProfile,
                'riasecResult' => $riasecResult
            ],
            ['meta' => ['title' => 'Hasil Tes RIASEC | ' . env('APP_NAME')]]
        );
    }

    /**
     * Generate AI Analysis using Gemini
     */
    public function generateAiAnalysis(Request $request, Response $response)
    {
        $currentUser = $this->session->get('auth.user_id');
        $currentRole = $this->session->get('auth.user_role');

        if ($currentRole !== 'user') {
            throw new AuthorizationException('Forbidden');
        }

        $userProfile = $this->profileModel->findByUserId($currentUser);
        $studentProfile = $this->studentModel->findByProfileId($userProfile['id']);

        if (!$studentProfile) {
            return $response->redirect('/profile/results?error=' . urlencode('Profil siswa tidak ditemukan'));
        }

        // Get latest RIASEC test result from TestResultModel (single source of truth)
        $riasecResult = $this->testResultModel->getLatestRiasecResult($studentProfile['id']);

        // Calculate Current Hash (without psychological_tests, use RIASEC result)
        $academic = $studentProfile['academic_scores'] ?? '';
        $achievements = $studentProfile['achievements'] ?? '';
        $riasecData = $riasecResult ? json_encode($riasecResult) : '';
        $currentHash = md5($academic . $achievements . $riasecData);

        $aiAnalysis = !empty($studentProfile['ai_analysis']) ? json_decode($studentProfile['ai_analysis'], true) : [];
        $lastHash = $aiAnalysis['last_data_hash'] ?? null;

        if ($currentHash === $lastHash) {
            return $response->redirect('/profile/results?success=' . urlencode('Analisis AI Anda sudah paling mutakhir berdasarkan data terbaru.'));
        }

        // Gather data for Gemini (RIASEC from TestResultModel, not psychological_tests)
        $studentData = [
            'academic_scores' => !empty($academic) ? json_decode($academic, true) : [],
            'achievements' => !empty($achievements) ? json_decode($achievements, true) : []
        ];

        // Add RIASEC result data if exists
        if ($riasecResult) {
            $studentData['riasec_result'] = [
                'holland_code' => $riasecResult['holland_code'],
                'scores' => json_decode($riasecResult['scores'], true),
                'percentages' => json_decode($riasecResult['percentages'], true),
                'categories' => json_decode($riasecResult['categories'], true),
                'ranked_dimensions' => json_decode($riasecResult['ranked_dimensions'], true),
                'holland_description' => $riasecResult['holland_description'] ?? ''
            ];
        }

        // Check MINIMUM data requirement: RIASEC test results
        if (!$riasecResult) {
            return $response->redirect('/profile/results?error=' . urlencode('Anda belum mengikuti tes RIASEC. Silakan ikuti tes terlebih dahulu untuk mendapatkan analisis AI.'));
        }

        // Check data completeness for user feedback
        $hasAcademic = !empty($studentData['academic_scores']);
        $hasAchievements = !empty($studentData['achievements']);
        $hasRiasec = !empty($studentData['riasec_result']);

        try {
            $gemini = new \Addon\Services\GeminiService();

            // Generate COMBINED AI Analysis (Student Profile + PMB Match in ONE CALL)
            $combinedResponse = $gemini->generateCombinedAnalysis($studentData);

            // Extract student_profile untuk ai_analysis
            $newAiAnalysis = $combinedResponse['student_profile'] ?? [];

            if (empty($newAiAnalysis)) {
                throw new \Exception('Respons AI tidak mengandung student_profile');
            }

            // Tambahkan metadata
            $newAiAnalysis['last_data_hash'] = $currentHash;
            $newAiAnalysis['generated_at'] = date('Y-m-d H:i:s');
            $newAiAnalysis['data_completeness'] = [
                'has_riasec' => $hasRiasec,
                'has_academic' => $hasAcademic,
                'has_achievements' => $hasAchievements
            ];

            // Ambil prompt yang digunakan untuk generate AI
            $aiPrompt = $gemini->getLastPrompt();

            // Simpan student_profile ke StudentProfileModel
            $updateData = [
                'ai_analysis' => json_encode($newAiAnalysis)
            ];
            if ($aiPrompt !== null) {
                $updateData['ai_prompt'] = $aiPrompt;
            }
            $this->studentModel->updateByProfileId($userProfile['id'], $updateData);

            // Extract pmb_match untuk PmbJourneyModel
            $pmbMatch = $combinedResponse['pmb_match'] ?? null;

            // Calculate scholarship eligibility using Rule-Based ScholarshipCalculator
            $scholarshipCalc = new ScholarshipCalculator();
            $scholarshipEligibility = $scholarshipCalc->calculateEligibility($studentData);

            // Save both pmb_match and scholarships to PmbJourneyModel
            try {
                $matchHash = md5(json_encode($studentData));

                // Save PMB match data
                if (!empty($pmbMatch)) {
                    $this->pmbJourneyModel->updateMatches(
                        $studentProfile['id'],
                        $pmbMatch,
                        $matchHash,
                        $aiPrompt
                    );
                }

                // Save scholarship eligibility data (Rule-Based, not AI)
                $this->pmbJourneyModel->updateScholarships(
                    $studentProfile['id'],
                    $scholarshipEligibility,
                    $matchHash
                );
            } catch (\Exception $pmbError) {
                // Log error tapi jangan gagalkan proses utama
                logger()->error('Gagal simpan PMB journey data: ' . $pmbError->getMessage());
            }

            // Build success message berdasarkan data completeness
            if ($hasAcademic && $hasAchievements) {
                $successMessage = 'Analisis AI berhasil dibuat dengan data lengkap!';
            } else {
                $missing = [];
                if (!$hasAcademic) $missing[] = 'nilai akademik';
                if (!$hasAchievements) $missing[] = 'prestasi';
                $successMessage = 'Analisis AI berhasil dibuat. Untuk hasil yang lebih akurat, lengkapi data: ' . implode(' dan ', $missing);
            }

            return $response->redirect('/profile/results?success=' . urlencode($successMessage));
        } catch (\Exception $e) {
            logger()->error('Gagal generate AI analysis: ' . $e->getMessage());
            return $response->redirect('/profile/results?error=' . urlencode('Gagal generate AI: ' . $e->getMessage()));
        }
    }

    /**
     * List students managed by teacher (for admin/guru BK)
     */
    public function listStudents(Request $request, Response $response): View
    {
        $currentUser = $this->session->get('auth.user_id');
        $currentRole = $this->session->get('auth.user_role');

        if ($currentRole !== 'admin' && $currentRole !== 'super-admin') {
            throw new AuthorizationException('Forbidden');
        }

        $userProfile = $this->profileModel->findByUserId($currentUser);
        $teacherProfile = $this->teacherModel->findByProfileId($userProfile['id']);

        $students = [];
        if ($teacherProfile && !empty($teacherProfile['managed_students'])) {
            $managedStudentIds = json_decode($teacherProfile['managed_students'], true) ?? [];
            foreach ($managedStudentIds as $studentProfileId) {
                $student = $this->studentModel->findByProfileId($studentProfileId);
                if ($student) {
                    $profile = $this->profileModel->find($studentProfileId);
                    $user = $this->userModel->find($profile['user_id']);
                    $student['user_name'] = $user['name'] ?? 'Unknown';
                    $student['email'] = $user['email'] ?? 'Unknown';

                    // Add RIASEC test status
                    $riasecResult = $this->testResultModel->getLatestRiasecResult($studentProfileId);
                    $student['has_riasec_test'] = !empty($riasecResult);
                    $student['riasec_holland_code'] = $riasecResult['holland_code'] ?? null;

                    $students[] = $student;
                }
            }
        }

        return $response->renderPage([
            'profile' => $userProfile,
            'teacherProfile' => $teacherProfile,
            'students' => $students
        ], ['path' => '/profile/students', 'meta' => ['title' => 'Siswa Bimbingan | ' . env('APP_NAME')]]);
    }

    /**
     * Get counseling schedule (for admin/guru BK)
     */
    public function schedule(Request $request, Response $response): View
    {
        $currentUser = $this->session->get('auth.user_id');
        $currentRole = $this->session->get('auth.user_role');

        if ($currentRole !== 'admin') {
            throw new AuthorizationException('Forbidden');
        }

        $userProfile = $this->profileModel->findByUserId($currentUser);
        $teacherProfile = $this->teacherModel->findByProfileId($userProfile['id']);

        return $response->renderPage([
            'profile' => $userProfile,
            'teacherProfile' => $teacherProfile
        ], ['path' => '/profile/schedule', 'meta' => ['title' => 'Jadwal Konseling | ' . env('APP_NAME')]]);
    }

    /**
     * Get staff permissions (for super-admin)
     */
    public function permissions(Request $request, Response $response): View
    {
        $currentUser = $this->session->get('auth.user_id');
        $currentRole = $this->session->get('auth.user_role');

        if ($currentRole !== 'super-admin') {
            throw new AuthorizationException('Forbidden');
        }

        $userProfile = $this->profileModel->findByUserId($currentUser);
        $staffProfile = $this->staffModel->findByProfileId($userProfile['id']);

        return $response->renderPage([
            'profile' => $userProfile,
            'staffProfile' => $staffProfile
        ], ['path' => '/profile/permissions', 'meta' => ['title' => 'Permissions | ' . env('APP_NAME')]]);
    }

    /**
     * Update staff permissions (for super-admin)
     */
    public function updatePermissions(Request $request, Response $response)
    {
        $currentUser = $this->session->get('auth.user_id');
        $currentRole = $this->session->get('auth.user_role');

        if ($currentRole !== 'super-admin') {
            throw new AuthorizationException('Forbidden');
        }

        $userProfile = $this->profileModel->findByUserId($currentUser);
        $data = $request->getBody();

        $staffProfile = $this->staffModel->findByProfileId($userProfile['id']);
        if ($staffProfile) {
            $this->staffModel->updateByProfileId($userProfile['id'], ['permissions' => json_encode($data)]);
        }

        return $response->redirect('/profile/permissions?success=' . urlencode('Permissions berhasil diperbarui'));
    }

    // ========================================================================
    // PRIVATE HELPER METHODS
    // ========================================================================

    /**
     * Authorize access to profile
     */
    private function authorizeAccess(int $profileId, string $currentRole, int $userProfileId): void
    {
        // User biasa hanya bisa akses profile sendiri
        if ($currentRole === 'user') {
            if ($profileId !== $userProfileId) {
                throw new AuthorizationException('Unauthorized');
            }
        }

        // Admin bisa akses profile sendiri atau siswa bimbingan
        if ($currentRole === 'admin') {
            if ($profileId !== $userProfileId) {
                $teacherProfile = $this->teacherModel->findByProfileId($userProfileId);
                $managedStudents = json_decode($teacherProfile['managed_students'] ?? '[]', true) ?? [];
                if (!in_array($profileId, $managedStudents)) {
                    throw new AuthorizationException('Unauthorized');
                }
            }
        }

        // Super admin bisa akses semua - no restriction
    }

    /**
     * Get profile data based on role
     */
    private function getProfileData(int $profileId, string $currentRole): ?array
    {
        $profile = $this->profileModel->find($profileId);
        if (!$profile) {
            return null;
        }

        $user = $this->userModel->find($profile['user_id']);
        $profile['user_name'] = $user['name'] ?? '';
        $profile['email'] = $user['email'] ?? '';
        $profile['role'] = $user['role'] ?? '';

        // Decode social media
        if (!empty($profile['social_media'])) {
            $profile['social_media'] = json_decode($profile['social_media'], true);
        }

        // Get role-specific data
        switch ($currentRole) {
            case 'user':
                $studentProfile = $this->studentModel->findByProfileId($profileId);
                $profile['role_data'] = $studentProfile;
                // Decode JSON fields (psychological_tests removed - use TestResultModel)
                if ($studentProfile) {
                    foreach (['academic_scores', 'extracurricular', 'achievements', 'ai_analysis'] as $field) {
                        if (!empty($studentProfile[$field])) {
                            $profile['role_data'][$field] = json_decode($studentProfile[$field], true);
                        }
                    }
                    // Get RIASEC results from TestResultModel
                    $profile['role_data']['riasec_results'] = $this->testResultModel->getLatestRiasecResult($studentProfile['id']);
                }
                break;
            case 'admin':
                $teacherProfile = $this->teacherModel->findByProfileId($profileId);
                $profile['role_data'] = $teacherProfile;
                // Decode JSON fields
                if ($teacherProfile) {
                    if (!empty($teacherProfile['managed_students'])) {
                        $profile['role_data']['managed_students'] = json_decode($teacherProfile['managed_students'], true);
                    }
                    if (!empty($teacherProfile['counseling_schedule'])) {
                        $profile['role_data']['counseling_schedule'] = json_decode($teacherProfile['counseling_schedule'], true);
                    }
                }
                break;
            case 'super-admin':
                $staffProfile = $this->staffModel->findByProfileId($profileId);
                $profile['role_data'] = $staffProfile;
                // Decode JSON fields
                if ($staffProfile && !empty($staffProfile['permissions'])) {
                    $profile['role_data']['permissions'] = json_decode($staffProfile['permissions'], true);
                }
                break;
        }

        return $profile;
    }

    /**
     * Create role-specific profile after registration
     */
    private function createRoleSpecificProfile(int $profileId, string $role): void
    {
        switch ($role) {
            case 'user':
                $this->studentModel->createForProfile($profileId);
                break;
            case 'admin':
                $this->teacherModel->createForProfile($profileId);
                break;
            case 'super-admin':
                $this->staffModel->createForProfile($profileId);
                break;
        }
    }

    /**
     * Update role-specific data
     */
    private function updateRoleSpecificData(int $profileId, string $currentRole, array $data): void
    {
        // Helper function untuk convert empty string ke null
        $clean = fn($value) => $value === '' ? null : $value;

        switch ($currentRole) {
            case 'user':
                $studentData = [
                    'student_id' => $clean($data['student_id'] ?? null),
                    'grade_level' => $clean($data['grade_level'] ?? null),
                    'major' => $clean($data['major'] ?? null),
                    'parent_name' => $clean($data['parent_name'] ?? null),
                    'parent_phone' => $clean($data['parent_phone'] ?? null),
                    'parent_email' => $clean($data['parent_email'] ?? null)
                ];
                $this->studentModel->updateByProfileId($profileId, $studentData);
                break;
            case 'admin':
                $teacherData = [
                    'teacher_id' => $clean($data['teacher_id'] ?? null),
                    'subject_specialty' => $clean($data['subject_specialty'] ?? null),
                    'certification' => $clean($data['certification'] ?? null)
                ];
                $this->teacherModel->updateByProfileId($profileId, $teacherData);
                break;
            case 'super-admin':
                $staffData = [
                    'employee_id' => $clean($data['employee_id'] ?? null),
                    'department' => $clean($data['department'] ?? null),
                    'position' => $clean($data['position'] ?? null)
                ];
                $this->staffModel->updateByProfileId($profileId, $staffData);
                break;
        }
    }
}
