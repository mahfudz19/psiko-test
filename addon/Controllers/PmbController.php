<?php

namespace Addon\Controllers;

use Addon\Models\PmbJourneyModel;
use Addon\Models\ProfileModel;
use Addon\Models\StudentProfileModel;
use Addon\Models\TestResultModel;
use App\Core\Http\RedirectResponse;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Services\SessionService;

/**
 * PMB Controller
 *
 * Handles PMB Journey, Simulation, and Scholarship features
 *
 * Best Practice: User-initiated AI Generation
 * - AI analysis generated via /profile/results/generate (user-initiated)
 * - PMB journey data fetched from database (no auto-generation)
 */
class PmbController
{
    public function __construct(
        private SessionService $session,
        private ProfileModel $profileModel,
        private StudentProfileModel $studentProfileModel,
        private PmbJourneyModel $pmbJourneyModel,
        private TestResultModel $testResultModel
    ) {}

    public function index(Request $request, Response $response): RedirectResponse
    {
        return $response->redirect('/pmb/journey');
    }

    /**
     * Display Match Score Dashboard (Journey page)
     *
     * Best Practice: User-initiated AI Generation
     * - Jika PMB journey data belum ada, redirect ke /profile/results
     * - User secara eksplisit klik "Generate AI Analysis" di halaman results
     * - Hindari API call otomatis yang bisa waste resource
     */
    public function journey(Request $request, Response $response)
    {
        // Get current user profile
        $profile = $this->profileModel->findByUserId($this->session->get('auth.user_id') ?? 0);
        $studentProfile = $profile ? $this->studentProfileModel->findByProfileId($profile['id']) : null;

        if (!$studentProfile) {
            return $response->redirect('/profile/results?error=' . urlencode('Profil siswa tidak ditemukan'));
        }

        // Check PREREQUISITE: RIASEC test results from TestResultModel (minimum requirement)
        $riasecResult = $this->testResultModel->getLatestRiasecResult($studentProfile['id']);

        if (empty($riasecResult)) {
            // User belum ikut RIASEC test - redirect dengan pesan
            return $response->redirect('/profile/results?error=' . urlencode('Anda belum mengikuti tes RIASEC. Silakan ikuti tes terlebih dahulu untuk mendapatkan rekomendasi program studi.'));
        }

        // Check if AI analysis exists
        if (empty($studentProfile['ai_analysis'])) {
            // User sudah ikut RIASEC tapi belum generate AI analysis
            // Redirect ke profile results dengan pesan untuk generate
            return $response->redirect('/profile/results?warning=' . urlencode('Silakan generate Analisis AI terlebih dahulu untuk melihat rekomendasi program studi yang sesuai.'));
        }

        // Fetch PMB journey data from database (NO auto-generation)
        $journey = $this->pmbJourneyModel->findByStudentId($studentProfile['id']);
        // dd($journey);

        // If journey data doesn't exist, redirect to /profile/results to generate AI analysis first
        if (!$journey || empty($journey['top_matches'])) {
            return $response->redirect('/profile/results?warning=' . urlencode('Silakan generate Analisis AI terlebih dahulu untuk mendapatkan rekomendasi program studi. Klik tombol "Generate Analisis AI" di halaman Results.'));
        }

        // Decode match data
        $matchScoreData = json_decode($journey['top_matches'], true) ?? [];

        // Dynamic Simulation Progress from Database
        $currentSimulationStep = (int)($journey['simulation_step'] ?? 1);
        $matchScoreData['simulation_progress'] = [
            'total_steps' => 5,
            'completed_steps' => max(0, $currentSimulationStep - 1),
            'steps' => [
                ['name' => 'Data Pribadi', 'is_completed' => $currentSimulationStep > 1],
                ['name' => 'Nilai Akademik', 'is_completed' => $currentSimulationStep > 2],
                ['name' => 'Hasil Analisis AI', 'is_completed' => $currentSimulationStep > 3],
                ['name' => 'Upload Dokumen', 'is_completed' => $currentSimulationStep > 4],
                ['name' => 'Pembayaran', 'is_completed' => $currentSimulationStep > 5],
            ],
        ];

        // Ensure scholarships array exists for the view to loop over without error
        $matchScoreData['scholarships'] = [];

        $data = [
            'profile' => $profile,
            'student_profile' => $studentProfile,
            'match_score' => $matchScoreData,
            'ai_error_message' => null,  // No AI error since we use cached data
        ];

        return $response->renderPage($data, [
            'meta' => ['title' => 'PMB Journey | ' . env('APP_NAME')],
        ]);
    }

    /**
     * Display PMB Simulation Wizard
     *
     * Fetches simulation data from database and pre-fills with user profile data
     */
    public function simulation(Request $request, Response $response)
    {
        // Get current user profile
        $profile = $this->profileModel->findByUserId($this->session->get('auth.user_id') ?? 0);
        $studentProfile = $profile ? $this->studentProfileModel->findByProfileId($profile['id']) : null;


        if (!$studentProfile) {
            return $response->redirect('/profile/results?error=' . urlencode('Profil siswa tidak ditemukan'));
        }

        // Get journey data from database
        $journey = $this->pmbJourneyModel->findByStudentId($studentProfile['id']);

        // Get saved simulation data
        $savedSimulationData = $journey && $journey['simulation_data'] ? json_decode($journey['simulation_data'], true) ?? [] : [];
        $currentStep = $journey ? (int)$journey['simulation_step'] : 1;
        $simulationStatus = $journey ? $journey['simulation_status'] : 'not_started';

        // Build steps with data from database and fallback to student profile (3 steps only)
        $steps = [];

        // Step 1: Data Pribadi
        $step1Data = $savedSimulationData[1] ?? [];
        if (empty($step1Data)) {
            // Pre-fill from student profile
            $step1Data = [
                'full_name' => $studentProfile['full_name'] ?? '',
                'email' => $profile['email'] ?? '',
                'phone' => $studentProfile['phone'] ?? '',
                'birth_place' => $studentProfile['birth_place'] ?? '',
                'birth_date' => $studentProfile['birth_date'] ?? '',
                'gender' => $studentProfile['gender'] ?? '',
                'address' => $studentProfile['address'] ?? '',
            ];
        }
        $steps[1] = [
            'id' => 1,
            'name' => 'Data Pribadi',
            'is_completed' => isset($savedSimulationData[1]),
            'data' => $step1Data,
        ];

        // Step 2: Upload Dokumen (previously Step 4)
        $step2Data = $savedSimulationData[2] ?? [];
        $documents = [
            ['name' => 'Ijazah/SKL', 'is_uploaded' => false, 'required' => true],
            ['name' => 'Transkrip Nilai', 'is_uploaded' => false, 'required' => true],
            ['name' => 'Foto 3x4', 'is_uploaded' => false, 'required' => true],
            ['name' => 'Portofolio', 'is_uploaded' => false, 'required' => false],
        ];
        // Mark uploaded documents
        if (!empty($step2Data['documents'])) {
            foreach ($documents as &$doc) {
                foreach ($step2Data['documents'] as $uploadedDoc) {
                    if ($uploadedDoc['name'] === $doc['name'] && $uploadedDoc['is_uploaded']) {
                        $doc['is_uploaded'] = true;
                        $doc['file_url'] = $uploadedDoc['file_url'] ?? null;
                        break;
                    }
                }
            }
        }
        $steps[2] = [
            'id' => 2,
            'name' => 'Upload Dokumen',
            'is_completed' => isset($savedSimulationData[2]),
            'documents' => $documents,
        ];

        // Step 3: Pembayaran (previously Step 5)
        $step3Data = $savedSimulationData[3] ?? [];
        $scholarshipData = $journey && !empty($journey['scholarships'])
            ? json_decode($journey['scholarships'], true)
            : ['average_score' => 0, 'eligible_scholarships' => []];

        // Calculate discount based on scholarships
        $maxDiscount = 0;
        foreach ($scholarshipData['eligible_scholarships'] ?? [] as $scholarship) {
            if ($scholarship['discount'] > $maxDiscount) {
                $maxDiscount = $scholarship['discount'];
            }
        }

        $registrationFee = 500000;
        $discount = $maxDiscount > 0 ? ($registrationFee * $maxDiscount / 100) : 0;
        $total = $registrationFee - $discount;

        $steps[3] = [
            'id' => 3,
            'name' => 'Pembayaran',
            'is_completed' => isset($savedSimulationData[3]),
            'payment_info' => [
                'registration_fee' => $registrationFee,
                'discount' => $discount,
                'discount_percentage' => $maxDiscount,
                'total' => $total,
                'bank_accounts' => [
                    ['bank' => 'BCA', 'account' => '1234567890', 'name' => env('APP_NAME')],
                    ['bank' => 'Mandiri', 'account' => '0987654321', 'name' => env('APP_NAME')],
                ],
            ],
        ];

        // Calculate progress (3 steps total)
        $completedSteps = count(array_filter($savedSimulationData, fn($key) => in_array($key, [1, 2, 3]), ARRAY_FILTER_USE_KEY));
        $progressPercentage = ($completedSteps / 3) * 100;

        // Get selected program from journey (top match)
        $selectedProgram = ['name' => '-', 'degree' => 'S1', 'accreditation' => '-'];
        if ($journey && !empty($journey['top_matches'])) {
            $topMatches = json_decode($journey['top_matches'], true) ?? [];
            if (!empty($topMatches['top_match'])) {
                $selectedProgram = [
                    'name' => $topMatches['top_match']['program_name'] ?? '-',
                    'degree' => $topMatches['top_match']['degree'] ?? 'S1',
                    'accreditation' => $topMatches['top_match']['accreditation'] ?? '-',
                ];
            }
        }

        $simulationData = [
            'current_step' => $currentStep,
            'total_steps' => 3,
            'progress_percentage' => $progressPercentage,
            'steps' => $steps,
            'selected_program' => $selectedProgram,
            'status' => $simulationStatus,
        ];

        return $response->renderPage(
            [
                'profile' => $profile,
                'student_profile' => $studentProfile,
                'simulation' => $simulationData,
                'journey' => $journey,
            ],
            ['meta' => ['title' => 'Simulasi PMB | ' . env('APP_NAME')]]
        );
    }

    /**
     * Calculate average grade from academic scores
     */
    private function calculateAverageGrade(array $scores): float
    {
        if (empty($scores)) return 0;

        $total = 0;
        $count = 0;

        foreach ($scores as $score) {
            if (is_numeric($score)) {
                $total += $score;
                $count++;
            }
        }

        return $count > 0 ? round($total / $count, 2) : 0;
    }

    /**
     * Save simulation step data
     *
     * Validates and saves each step of the PMB simulation wizard
     */
    public function saveSimulationStep(Request $request, Response $response)
    {
        $data = $request->getBody();
        $stepId = $data['step_id'] ?? null;
        $stepData = $data['data'] ?? [];

        // Get current user profile
        $profile = $this->profileModel->findByUserId($this->session->get('auth.user_id') ?? 0);
        $studentProfile = $profile ? $this->studentProfileModel->findByProfileId($profile['id']) : null;

        if (!$studentProfile) {
            return $response->json([
                'success' => false,
                'message' => 'Profil siswa tidak ditemukan',
            ], 404);
        }

        // Validate step_id (3 steps only)
        if (!$stepId || !in_array($stepId, [1, 2, 3])) {
            return $response->json([
                'success' => false,
                'message' => 'Step ID tidak valid',
            ], 400);
        }

        // Validate step data based on step_id
        $validationResult = $this->validateSimulationStep($stepId, $stepData);
        if (!$validationResult['valid']) {
            return $response->json([
                'success' => false,
                'message' => $validationResult['message'],
                'errors' => $validationResult['errors'] ?? [],
            ], 400);
        }

        // Get existing journey data
        $journey = $this->pmbJourneyModel->findByStudentId($studentProfile['id']);
        $existingData = [];
        if ($journey && !empty($journey['simulation_data'])) {
            $existingData = json_decode($journey['simulation_data'], true) ?? [];
        }

        // Merge with existing data
        $mergedData = array_merge($existingData, [$stepId => $stepData]);

        // Determine next step and status (3 steps total)
        $nextStep = $stepId + 1;
        $status = $stepId === 3 ? 'completed' : 'in_progress';

        // Save to database
        try {
            $this->pmbJourneyModel->updateSimulationProgress(
                $studentProfile['id'],
                $nextStep,
                $mergedData,
                $status
            );

            return $response->json([
                'success' => true,
                'message' => 'Data berhasil disimpan',
                'next_step' => $nextStep,
                'status' => $status,
            ]);
        } catch (\Exception $e) {
            return $response->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Validate simulation step data
     *
     * @param int $stepId Step ID (1-5)
     * @param array $data Data to validate
     * @return array Validation result ['valid' => bool, 'message' => string, 'errors' => array]
     */
    private function validateSimulationStep(int $stepId, array $data): array
    {
        $errors = [];
        $message = '';

        match ($stepId) {
            1 => [
                // Step 1: Data Pribadi
                'required' => ['full_name', 'email', 'phone', 'birth_place', 'birth_date', 'gender', 'address'],
                'rules' => [
                    'full_name' => 'required|min:3',
                    'email' => 'required|email',
                    'phone' => 'required|min:10',
                    'birth_place' => 'required',
                    'birth_date' => 'required|date',
                    'gender' => 'required|in:male,female',
                    'address' => 'required|min:10',
                ],
            ],
            2 => [
                // Step 2: Upload Dokumen (simulation - no actual file upload required)
                'required' => [],
                'rules' => [],
            ],
            3 => [
                // Step 3: Pembayaran (payment confirmation)
                'required' => ['sender_name', 'transfer_date', 'proof_upload', 'terms_accepted'],
                'rules' => [
                    'sender_name' => 'required|min:3',
                    'transfer_date' => 'required|date',
                    'proof_upload' => 'required',
                    'terms_accepted' => 'accepted',
                ],
            ],
        };

        $config = match ($stepId) {
            1 => ['required' => ['full_name', 'email', 'phone', 'birth_place', 'birth_date', 'gender', 'address'], 'rules' => ['full_name' => 'required|min:3', 'email' => 'required|email', 'phone' => 'required|min:10', 'birth_place' => 'required', 'birth_date' => 'required|date', 'gender' => 'required|in:male,female', 'address' => 'required|min:10']],
            2 => ['required' => [], 'rules' => []], // Step 2: Upload Dokumen (simulation - no validation)
            3 => ['required' => ['sender_name', 'transfer_date', 'proof_upload', 'terms_accepted'], 'rules' => ['sender_name' => 'required|min:3', 'transfer_date' => 'required|date', 'proof_upload' => 'required', 'terms_accepted' => 'accepted']],
            default => ['required' => [], 'rules' => []],
        };

        // Check required fields
        foreach ($config['required'] as $field) {
            if (!isset($data[$field]) || (is_string($data[$field]) && trim($data[$field]) === '')) {
                $errors[$field] = 'Field ' . $field . ' wajib diisi';
            }
        }

        // Validate rules
        foreach ($config['rules'] as $field => $rule) {
            if (!isset($data[$field])) continue;

            $value = $data[$field];

            // Skip validation for empty values (already handled in required check)
            if (is_string($value) && trim($value) === '') {
                continue;
            }

            $ruleParts = explode('|', $rule);

            foreach ($ruleParts as $rulePart) {
                if ($rulePart === 'required') continue; // Already checked

                if (str_starts_with($rulePart, 'min:')) {
                    $min = (int)substr($rulePart, 4);
                    if (is_numeric($value) && $value < $min) {
                        $errors[$field] = 'Nilai ' . $field . ' minimal ' . $min;
                    } elseif (is_string($value) && strlen($value) < $min) {
                        $errors[$field] = 'Panjang ' . $field . ' minimal ' . $min . ' karakter';
                    }
                }

                if (str_starts_with($rulePart, 'max:')) {
                    $max = (int)substr($rulePart, 4);
                    if (is_numeric($value) && $value > $max) {
                        $errors[$field] = 'Nilai ' . $field . ' maksimal ' . $max;
                    } elseif (is_string($value) && strlen($value) > $max) {
                        $errors[$field] = 'Panjang ' . $field . ' maksimal ' . $max . ' karakter';
                    }
                }

                if (str_starts_with($rulePart, 'in:')) {
                    $allowed = explode(',', substr($rulePart, 3));
                    if (!in_array($value, $allowed)) {
                        $errors[$field] = 'Nilai ' . $field . ' harus salah satu dari: ' . implode(', ', $allowed);
                    }
                }

                if ($rulePart === 'email') {
                    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $errors[$field] = 'Format email tidak valid';
                    }
                }

                if ($rulePart === 'date') {
                    if (!strtotime($value)) {
                        $errors[$field] = 'Format tanggal tidak valid';
                    }
                }

                if ($rulePart === 'numeric') {
                    if (!is_numeric($value)) {
                        $errors[$field] = 'Nilai harus berupa angka';
                    }
                }

                if ($rulePart === 'array') {
                    if (!is_array($value)) {
                        $errors[$field] = 'Nilai harus berupa array';
                    }
                }
            }
        }

        if (!empty($errors)) {
            return [
                'valid' => false,
                'message' => 'Validasi gagal',
                'errors' => $errors,
            ];
        }

        return [
            'valid' => true,
            'message' => 'Validasi berhasil',
        ];
    }

    /**
     * Complete simulation and show summary
     *
     * Validates all steps are completed before marking simulation as done
     */
    public function completeSimulation(Request $request, Response $response)
    {
        // Get current user profile
        $profile = $this->profileModel->findByUserId($this->session->get('auth.user_id') ?? 0);
        $studentProfile = $profile ? $this->studentProfileModel->findByProfileId($profile['id']) : null;

        if (!$studentProfile) {
            return $response->redirect('/profile/results?error=' . urlencode('Profil siswa tidak ditemukan'));
        }

        // Get journey data
        $journey = $this->pmbJourneyModel->findByStudentId($studentProfile['id']);

        if (!$journey) {
            return $response->redirect('/pmb/simulation?error=' . urlencode('Data simulasi tidak ditemukan. Silakan mulai simulasi dari awal.'));
        }

        // Check if all steps are completed (3 steps total)
        $simulationData = json_decode($journey['simulation_data'], true) ?? [];
        $requiredSteps = [1, 2, 3];
        $missingSteps = array_diff($requiredSteps, array_keys($simulationData));

        if (!empty($missingSteps)) {
            return $response->redirect('/pmb/simulation?step=' . min($missingSteps) . '&error=' . urlencode('Masih ada langkah yang belum dilengkapi. Silakan lengkapi semua langkah terlebih dahulu.'));
        }

        // Mark simulation as completed
        try {
            $this->pmbJourneyModel->updateSimulationProgress(
                $studentProfile['id'],
                6, // All steps completed
                $simulationData,
                'completed'
            );

            // Show success message
            return $response->redirect('/pmb/simulation?completed=1');
        } catch (\Exception $e) {
            return $response->redirect('/pmb/simulation?error=' . urlencode('Gagal menyelesaikan simulasi: ' . $e->getMessage()));
        }
    }

    /**
     * Convert simulation to real application
     *
     * Create actual PMB application from completed simulation data
     */
    public function convertToRealApplication(Request $request, Response $response)
    {
        // Get current user profile
        $profile = $this->profileModel->findByUserId($this->session->get('auth.user_id') ?? 0);
        $studentProfile = $profile ? $this->studentProfileModel->findByProfileId($profile['id']) : null;

        if (!$studentProfile) {
            return $response->redirect('/profile/results?error=' . urlencode('Profil siswa tidak ditemukan'));
        }

        // Get journey data
        $journey = $this->pmbJourneyModel->findByStudentId($studentProfile['id']);

        if (!$journey || $journey['simulation_status'] !== 'completed') {
            return $response->redirect('/pmb/simulation?error=' . urlencode('Simulasi belum selesai. Lengkapi semua step terlebih dahulu.'));
        }

        // Get simulation data
        $simulationData = json_decode($journey['simulation_data'], true) ?? [];

        // Check if all steps are completed (3 steps total)
        $requiredSteps = [1, 2, 3];
        $missingSteps = array_diff($requiredSteps, array_keys($simulationData));

        if (!empty($missingSteps)) {
            return $response->redirect('/pmb/simulation?step=' . min($missingSteps) . '&error=' . urlencode('Masih ada langkah yang belum dilengkapi.'));
        }

        try {
            // Generate registration number
            $registrationNumber = 'PMB-' . date('Ymd') . '-' . str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT);

            // TODO: Create actual PMB application record in database
            // For now, just redirect to success page with registration number
            return $response->redirect('/pmb/simulation?converted=1&reg_number=' . urlencode($registrationNumber));
        } catch (\Exception $e) {
            return $response->redirect('/pmb/simulation?error=' . urlencode('Gagal convert simulasi: ' . $e->getMessage()));
        }
    }

    /**
     * Display Scholarship eligibility
     *
     * Fetch scholarship data from pmb_journeys table (already calculated by generateAiAnalysis)
     * If data doesn't exist, redirect to /profile/results to generate first
     */
    public function scholarship(Request $request, Response $response)
    {
        // Get current user profile
        $profile = $this->profileModel->findByUserId($this->session->get('auth.user_id') ?? 0);
        $studentProfile = $profile ? $this->studentProfileModel->findByProfileId($profile['id']) : null;

        // Fetch PMB journey data (includes scholarships)
        $journey = $this->pmbJourneyModel->findByStudentId($studentProfile['id']);

        // Get scholarship data from journey (already calculated by generateAiAnalysis)
        $scholarshipData = null;
        $ai_error_message = null;

        if ($journey && !empty($journey['scholarships'])) {
            $scholarshipData = json_decode($journey['scholarships'], true);
        }

        // If no scholarship data, user needs to generate AI analysis first
        if (!$scholarshipData) {
            return $response->redirect('/profile/results?warning=' . urlencode('Silakan generate Analisis AI terlebih dahulu untuk mendapatkan rekomendasi beasiswa.'));
        }

        $data = [
            'profile' => $profile,
            'student_profile' => $studentProfile,
            'scholarships' => $scholarshipData, // Data beasiswa dari pmb_journeys.scholarships
            'journey' => $journey,
            'ai_error_message' => $ai_error_message,
        ];

        return $response->renderPage($data, [
            'path' => '/pmb/scholarship',
            'meta' => ['title' => 'Kalkulator Beasiswa | ' . env('APP_NAME')],
        ]);
    }
}
