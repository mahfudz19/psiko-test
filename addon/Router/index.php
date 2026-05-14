<?php

use Addon\Controllers\AuthController;
use Addon\Controllers\DashboardController;
use Addon\Controllers\ProfileController;
use Addon\Controllers\PmbController;
use Addon\Controllers\SettingsController;
use Addon\Controllers\AdminController;
use Addon\Controllers\SchoolAdminController;
use Addon\Controllers\ChatController;
use Addon\Controllers\TestController;

/** @var \App\Core\Routing\Router $router */

// Guest routes (login, register, password reset, OTP verification)
$router->group(['middleware' => ['guest']], function () use ($router) {
    // Login
    $router->get('/login', [AuthController::class, 'showLogin']);
    $router->post('/login', [AuthController::class, 'login'], ['csrf']);

    // Register
    $router->get('/register', [AuthController::class, 'showRegister']);
    $router->post('/register', [AuthController::class, 'register'], ['csrf']);

    // OTP Verification
    $router->get('/verify-otp', [AuthController::class, 'showVerifyOtp']);
    $router->post('/verify-otp', [AuthController::class, 'verifyOtp'], ['csrf']);
    $router->get('/resend-otp', [AuthController::class, 'resendOtp']);
    $router->get('/otp-sent', [AuthController::class, 'showOtpSent']);

    // Password reset
    $router->get('/password/forgot', [AuthController::class, 'showForgotPassword']);
    $router->post('/password/forgot', [AuthController::class, 'sendResetLink'], ['csrf']);
    $router->get('/password/reset', [AuthController::class, 'showResetPassword']);
    $router->post('/password/reset', [AuthController::class, 'resetPassword'], ['csrf']);

    // Google OAuth
    $router->get('/auth/google', [AuthController::class, 'authGoogle']);
    $router->get('/auth/callback', [AuthController::class, 'googleCallback']);
});

// Auth routes (require login)
$router->group(['middleware' => ['auth']], function () use ($router) {
    // Dashboard
    $router->get('/dashboard', [DashboardController::class, 'index']);

    // Logout
    $router->post('/logout', [AuthController::class, 'logout']);
});

// Home route
$router->get('/', [AuthController::class, 'redirectDashboard']);

// Profile routes (require login)
$router->group(['middleware' => ['auth']], function () use ($router) {
    // Main profile pages
    $router->get('/profile', [ProfileController::class, 'show']);
    $router->get('/profile/edit', [ProfileController::class, 'edit']);
    $router->post('/profile/update', [ProfileController::class, 'update'], ['csrf']);
    $router->post('/profile/avatar', [ProfileController::class, 'uploadAvatar'], ['csrf']);

    // Student routes
    $router->get('/profile/academic', [ProfileController::class, 'academic']);
    $router->post('/profile/academic', [ProfileController::class, 'updateAcademic'], ['csrf']);
    $router->get('/profile/achievements', [ProfileController::class, 'achievements']);
    $router->post('/profile/achievements', [ProfileController::class, 'updateAchievements'], ['csrf']);
    $router->get('/profile/results', [ProfileController::class, 'results']);
    $router->post('/profile/results/generate', [ProfileController::class, 'generateAiAnalysis'], ['csrf']);

    // Teacher routes
    $router->get('/profile/students', [ProfileController::class, 'listStudents']);
    $router->get('/profile/schedule', [ProfileController::class, 'schedule']);

    // Staff routes
    $router->get('/profile/permissions', [ProfileController::class, 'permissions']);
    $router->post('/profile/permissions', [ProfileController::class, 'updatePermissions'], ['csrf']);

    // Chat Consultation routes (untuk siswa)
    $router->get('/chat', [ChatController::class, 'index']);
    $router->get('/chat/create', [ChatController::class, 'create']);
    $router->post('/chat', [ChatController::class, 'store']);
    $router->get('/chat/:session_id', [ChatController::class, 'show']);
    $router->post('/chat/send', [ChatController::class, 'sendMessage']);
    $router->post('/chat/delete', [ChatController::class, 'delete']);
});

// PMB routes (require login, role: user/siswa)
$router->group(['middleware' => ['auth']], function () use ($router) {
    // Main PMB pages
    $router->get('/pmb', [PmbController::class, 'index']);
    $router->get('/pmb/journey', [PmbController::class, 'journey']);

    // Simulation
    $router->get('/pmb/simulation', [PmbController::class, 'simulation']);
    $router->post('/pmb/simulation/step', [PmbController::class, 'saveSimulationStep'], ['csrf']);
    $router->get('/pmb/simulation/complete', [PmbController::class, 'completeSimulation']);

    // Scholarship
    $router->get('/pmb/scholarship', [PmbController::class, 'scholarship']);
});

// Test routes (require login, role: user/siswa)
$router->group(['middleware' => ['auth']], function () use ($router) {
    // Test Dashboard (redirect ke /profile/results)
    $router->get('/tests', [TestController::class, 'index']);

    // RIASEC Test routes
    $router->get('/tests/riasec', [TestController::class, 'riasecIndex']);
    $router->post('/tests/riasec/start', [TestController::class, 'startTest'], ['csrf']);
    $router->get('/tests/riasec/take', [TestController::class, 'takeTest']);
    $router->post('/tests/riasec/submit', [TestController::class, 'submitTest'], ['csrf']);
    $router->get('/tests/riasec/results', [TestController::class, 'viewResults']);

    // IQ Test routes (placeholder untuk pengembangan selanjutnya)
    $router->get('/tests/iq', [TestController::class, 'iqIndex']);
});

$router->group(['middleware' => ['auth']], function () use ($router) {
    $router->get('/settings', [SettingsController::class, 'index']);
});

// Super Admin Routes
$router->group(['middleware' => ['auth', 'role:super-admin', 'csrf']], function () use ($router) {
    // Dashboard
    $router->get('/admin', [AdminController::class, 'index']);

    // Schools routes
    $router->get('/admin/schools', [AdminController::class, 'schools']);
    $router->get('/admin/schools/create', [AdminController::class, 'createSchool']);
    $router->post('/admin/schools', [AdminController::class, 'storeSchool']);
    $router->get('/admin/schools/:id', [AdminController::class, 'showSchool']);
    $router->get('/admin/schools/:id/edit', [AdminController::class, 'editSchool']);
    $router->post('/admin/schools/:id', [AdminController::class, 'updateSchool']);
    $router->post('/admin/schools/:id/delete', [AdminController::class, 'deleteSchool']);

    // Teachers routes (dalam konteks sekolah)
    $router->get('/admin/schools/:id/teachers', [AdminController::class, 'schoolTeachers']);
    $router->get('/admin/schools/:id/teachers/create', [AdminController::class, 'createTeacher']);
    $router->post('/admin/schools/:id/teachers', [AdminController::class, 'storeTeacher']);

    // Students routes (dalam konteks sekolah)
    $router->get('/admin/schools/:id/students', [AdminController::class, 'schoolStudents']);
    $router->get('/admin/schools/:id/students/create', [AdminController::class, 'createStudent']);
    $router->post('/admin/schools/:id/students', [AdminController::class, 'storeStudent']);
});

// School Admin Routes (untuk role admin - mengelola sekolah sendiri)
$router->group(['middleware' => ['auth', 'role:super-admin,admin', 'schooladmin', 'csrf']], function () use ($router) {
    // Dashboard sekolah sendiri
    $router->get('/admin/schools/my', [SchoolAdminController::class, 'mySchool']);
    $router->get('/admin/schools/my/edit', [SchoolAdminController::class, 'editMySchool']);
    $router->post('/admin/schools/my', [SchoolAdminController::class, 'updateMySchool']);

    // CRUD Students di sekolah sendiri
    $router->get('/admin/students', [SchoolAdminController::class, 'students']);
    $router->get('/admin/students/create', [SchoolAdminController::class, 'createStudent']);
    $router->post('/admin/students', [SchoolAdminController::class, 'storeStudent']);
    $router->get('/admin/students/:id', [SchoolAdminController::class, 'showStudent']);
    $router->get('/admin/students/:id/edit', [SchoolAdminController::class, 'editStudent']);
    $router->post('/admin/students/:id', [SchoolAdminController::class, 'updateStudent']);
    $router->post('/admin/students/:id/delete', [SchoolAdminController::class, 'deleteStudent']);
});
