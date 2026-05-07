<?php

namespace Addon\Controllers;

use Addon\Models\ProfileModel;
use Addon\Models\StudentProfileModel;
use Addon\Models\TeacherProfileModel;
use Addon\Models\StaffProfileModel;
use Addon\Models\UserModel;
use Addon\Models\SchoolModel;
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
        private SchoolModel $schoolModel
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
            return $response->redirect('/profile?error=Profile+not+found');
        }

        if (!$profileId) {
            $profileId = $userProfile['id'];
        }

        // Authorization check
        $this->authorizeAccess($profileId, $currentRole, $userProfile['id']);

        $profile = $this->getProfileData($profileId, $currentRole);

        return $response->renderPage([
            'profile' => $profile,
            'role' => $currentRole
        ], ['path' => '/profile/edit', 'meta' => ['title' => 'Edit Profile | ' . env('APP_NAME')]]);
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
            return $response->redirect('/profile');
        }

        if (!$profileId) {
            $profileId = $userProfile['id'];
        }

        // Authorization check
        $this->authorizeAccess($profileId, $currentRole, $userProfile['id']);

        $data = $request->getBody();

        // Update base profile data
        $baseData = [
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'birth_place' => $data['birth_place'] ?? null,
            'birth_date' => $data['birth_date'] ?? null,
            'gender' => $data['gender'] ?? null,
            'social_media' => !empty($data['social_media']) ? json_encode($data['social_media']) : null
        ];

        $this->profileModel->updateById($profileId, $baseData);

        // Update role-specific data
        $this->updateRoleSpecificData($profileId, $currentRole, $data);

        // Update user name if provided
        if (!empty($data['name'])) {
            $this->userModel->updateById($currentUser, ['name' => $data['name']]);
            $this->session->set('user', ['id' => $currentUser, 'name' => $data['name'], 'email' => $currentUser['email'], 'role' => $currentRole]);
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
        $currentUser = $this->session->get('auth.user_id');
        $currentRole = $this->session->get('auth.user_role');

        if ($currentRole !== 'user') {
            throw new AuthorizationException('Forbidden');
        }

        $userProfile = $this->profileModel->findByUserId($currentUser);

        $data = $request->getBody();

        // Handle academic scores dari smart textarea atau manual entry
        $academicScores = [];

        // Jika ada parsed_scores_data dari smart textarea
        if (!empty($data['parsed_scores_data'])) {
            try {
                $parsedScores = json_decode($data['parsed_scores_data'], true);
                if (is_array($parsedScores)) {
                    foreach ($parsedScores as $score) {
                        if (!empty($score['subject']) && isset($score['grade'])) {
                            $academicScores[] = [
                                'subject' => htmlspecialchars($score['subject']),
                                'grade' => (int)$score['grade']
                            ];
                        }
                    }
                }
            } catch (\Exception $e) {
                // Ignore parsing error, fallback to manual input
            }
        }

        // Jika tidak ada parsed scores, gunakan manual input (format lama)
        if (empty($academicScores) && !empty($data['academic_scores'])) {
            $subjects = $data['academic_scores']['subject'] ?? [];
            $grades = $data['academic_scores']['grade'] ?? [];

            foreach ($subjects as $index => $subject) {
                if (!empty($subject) && isset($grades[$index])) {
                    $academicScores[] = [
                        'subject' => htmlspecialchars($subject),
                        'grade' => (int)$grades[$index]
                    ];
                }
            }
        }

        $academicData = [
            'school_id' => $data['school_id'] ?? null,
            'student_id' => $data['student_id'] ?? null,
            'grade_level' => $data['grade_level'] ?? null,
            'major' => $data['major'] ?? null,
            'academic_scores' => !empty($academicScores) ? json_encode($academicScores, JSON_PRETTY_PRINT) : null,
            'parent_name' => $data['parent_name'] ?? null,
            'parent_phone' => $data['parent_phone'] ?? null,
            'parent_email' => $data['parent_email'] ?? null
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

        $achievementData = [];

        // Update extracurricular
        if (!empty($data['extracurricular'])) {
            $achievementData['extracurricular'] = json_encode($data['extracurricular']);
        }

        // Update achievements
        if (!empty($data['achievements'])) {
            $achievementData['achievements'] = json_encode($data['achievements']);
        }

        if ($studentProfile && !empty($achievementData)) {
            $this->studentModel->updateByProfileId($userProfile['id'], $achievementData);
        }

        return $response->redirect('/profile/achievements?success=' . urlencode('Data prestasi berhasil diperbarui'));
    }

    /**
     * Get psychotest results
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

        return $response->renderPage([
            'profile' => $userProfile,
            'studentProfile' => $studentProfile
        ], ['path' => '/profile/results', 'meta' => ['title' => 'Hasil Psykotest | ' . env('APP_NAME')]]);
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
                // Decode JSON fields
                if ($studentProfile) {
                    foreach (['academic_scores', 'extracurricular', 'achievements', 'psychological_tests', 'ai_analysis'] as $field) {
                        if (!empty($studentProfile[$field])) {
                            $profile['role_data'][$field] = json_decode($studentProfile[$field], true);
                        }
                    }
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
        switch ($currentRole) {
            case 'user':
                $studentData = [
                    'school_id' => $data['school_id'] ?? null,
                    'student_id' => $data['student_id'] ?? null,
                    'grade_level' => $data['grade_level'] ?? null,
                    'major' => $data['major'] ?? null,
                    'parent_name' => $data['parent_name'] ?? null,
                    'parent_phone' => $data['parent_phone'] ?? null,
                    'parent_email' => $data['parent_email'] ?? null
                ];
                $this->studentModel->updateByProfileId($profileId, $studentData);
                break;
            case 'admin':
                $teacherData = [
                    'school_id' => $data['school_id'] ?? null,
                    'teacher_id' => $data['teacher_id'] ?? null,
                    'subject_specialty' => $data['subject_specialty'] ?? null,
                    'certification' => $data['certification'] ?? null
                ];
                $this->teacherModel->updateByProfileId($profileId, $teacherData);
                break;
            case 'super-admin':
                $staffData = [
                    'employee_id' => $data['employee_id'] ?? null,
                    'department' => $data['department'] ?? null,
                    'position' => $data['position'] ?? null
                ];
                $this->staffModel->updateByProfileId($profileId, $staffData);
                break;
        }
    }
}
