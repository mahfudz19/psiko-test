<?php

use App\Core\Http\Request;
use App\Core\Http\Response;
use Addon\Controllers\AuthController;
use Addon\Controllers\ProfileController;
use Addon\Controllers\PmbController;
use Addon\Models\UserModel;
use App\Services\SessionService;

/** @var \App\Core\Routing\Router $router */

// Guest routes (login, register, password reset, OTP verification)
$router->group(['middleware' => ['guest']], function () use ($router) {
    // Login
    $router->get('/login', [AuthController::class, 'showLogin']);
    $router->post('/login', [AuthController::class, 'login']);

    // Register
    $router->get('/register', [AuthController::class, 'showRegister']);
    $router->post('/register', [AuthController::class, 'register']);

    // OTP Verification
    $router->get('/verify-otp', [AuthController::class, 'showVerifyOtp']);
    $router->post('/verify-otp', [AuthController::class, 'verifyOtp']);
    $router->get('/resend-otp', [AuthController::class, 'resendOtp']);
    $router->get('/otp-sent', [AuthController::class, 'showOtpSent']);

    // Password reset
    $router->get('/password/forgot', [AuthController::class, 'showForgotPassword']);
    $router->post('/password/forgot', [AuthController::class, 'sendResetLink']);
    $router->get('/password/reset', [AuthController::class, 'showResetPassword']);
    $router->post('/password/reset', [AuthController::class, 'resetPassword']);

    // Google OAuth
    $router->get('/auth/google', function (Request $request, Response $response) {
        $client = new \Google_Client();
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));
        $client->addScope('email');
        $client->addScope('profile');

        $authUrl = $client->createAuthUrl();
        return $response->redirect($authUrl);
    });
    $router->get('/auth/callback', [AuthController::class, 'googleCallback']);
});

// Auth routes (require login)
$router->group(['middleware' => ['auth']], function () use ($router) {
    // Dashboard
    $router->get('/dashboard', function (Request $request, Response $response) {
        return $response->renderPage([], ['path' => '/dashboard', 'meta' => ['title' => 'Dashboard | ' . env('APP_NAME')]]);
    });

    // Logout
    $router->post('/logout', [AuthController::class, 'logout']);
});

// Home route
$router->get('/', function (Request $request, Response $response) {
    return $response->redirect('/dashboard');
});

// Profile routes (require login)
$router->group(['middleware' => ['auth']], function () use ($router) {
    // Main profile pages
    $router->get('/profile', [ProfileController::class, 'show']);
    $router->get('/profile/edit', [ProfileController::class, 'edit']);
    $router->post('/profile/update', [ProfileController::class, 'update']);
    $router->post('/profile/avatar', [ProfileController::class, 'uploadAvatar']);

    // Student routes
    $router->get('/profile/academic', [ProfileController::class, 'academic']);
    $router->post('/profile/academic', [ProfileController::class, 'updateAcademic']);
    $router->get('/profile/achievements', [ProfileController::class, 'achievements']);
    $router->post('/profile/achievements', [ProfileController::class, 'updateAchievements']);
    $router->get('/profile/results', [ProfileController::class, 'results']);

    // Teacher routes
    $router->get('/profile/students', [ProfileController::class, 'listStudents']);
    $router->get('/profile/schedule', [ProfileController::class, 'schedule']);

    // Staff routes
    $router->get('/profile/permissions', [ProfileController::class, 'permissions']);
    $router->post('/profile/permissions', [ProfileController::class, 'updatePermissions']);
});

// PMB routes (require login, role: user/siswa)
$router->group(['middleware' => ['auth']], function () use ($router) {
    // Main PMB pages
    $router->get('/pmb', function (Request $request, Response $response) {
        return $response->redirect('/pmb/journey');
    });
    $router->get('/pmb/journey', [PmbController::class, 'journey']);
    $router->get('/pmb/simulation', [PmbController::class, 'simulation']);
    $router->post('/pmb/simulation/step', [PmbController::class, 'saveSimulationStep']);
    $router->get('/pmb/simulation/complete', [PmbController::class, 'completeSimulation']);
    $router->post('/pmb/convert-to-real', [PmbController::class, 'convertToRealApplication']);

    // Scholarship
    $router->get('/pmb/scholarship', [PmbController::class, 'scholarship']);
    $router->post('/pmb/scholarship/calculate', [PmbController::class, 'calculateScholarship']);
    $router->post('/pmb/scholarship/apply', [PmbController::class, 'applyScholarship']);

    // API endpoints (for AJAX)
    $router->get('/api/pmb/match-score', [PmbController::class, 'getMatchScore']);
    $router->get('/api/pmb/progress', [PmbController::class, 'getSimulationProgress']);
    $router->get('/api/pmb/similar-students', [PmbController::class, 'getSimilarStudents']);
});
$router->group(['middleware' => ['auth']], function () use ($router) {
    $router->get('/settings', function (Request $request, Response $response) {
        return $response->renderPage();
    });
});
