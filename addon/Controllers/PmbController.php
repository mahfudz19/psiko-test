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
     */
    public function simulation(Request $request, Response $response)
    {
        // Dummy data untuk simulation progress
        $simulationData = [
            'current_step' => 4,
            'total_steps' => 5,
            'progress_percentage' => 60,
            'steps' => [
                [
                    'id' => 1,
                    'name' => 'Data Pribadi',
                    'is_completed' => true,
                    'data' => [
                        'full_name' => 'Ahmad Rizki',
                        'email' => 'ahmad.rizki@email.com',
                        'phone' => '081234567890',
                        'birth_place' => 'Jakarta',
                        'birth_date' => '2005-05-15',
                        'gender' => 'male',
                        'address' => 'Jl. Merdeka No. 123, Jakarta Pusat',
                    ],
                ],
                [
                    'id' => 2,
                    'name' => 'Nilai Akademik',
                    'is_completed' => true,
                    'data' => [
                        'school_name' => 'SMA Negeri 1 Jakarta',
                        'major' => 'IPA',
                        'graduation_year' => '2024',
                        'average_grade' => 87.5,
                        'subjects' => [
                            'math' => 88,
                            'indonesian' => 85,
                            'english' => 90,
                            'physics' => 86,
                        ],
                    ],
                ],
                [
                    'id' => 3,
                    'name' => 'Hasil Analisis AI',
                    'is_completed' => true,
                    'data' => [
                        'potentials' => ['Logical Thinking', 'Problem Solving'],
                        'interests' => ['Programming', 'Technology'],
                        'talents' => ['Coding', 'Algorithm'],
                        'recommended_major' => 'Teknik Informatika',
                    ],
                ],
                [
                    'id' => 4,
                    'name' => 'Upload Dokumen',
                    'is_completed' => false,
                    'documents' => [
                        ['name' => 'Ijazah/SKL', 'is_uploaded' => false, 'required' => true],
                        ['name' => 'Transkrip Nilai', 'is_uploaded' => false, 'required' => true],
                        ['name' => 'Foto 3x4', 'is_uploaded' => false, 'required' => true],
                        ['name' => 'Portofolio', 'is_uploaded' => false, 'required' => false],
                    ],
                ],
                [
                    'id' => 5,
                    'name' => 'Pembayaran',
                    'is_completed' => false,
                    'payment_info' => [
                        'registration_fee' => 500000,
                        'discount' => 0,
                        'total' => 500000,
                        'bank_accounts' => [
                            ['bank' => 'BCA', 'account' => '1234567890', 'name' => '' . env('APP_NAME')],
                            ['bank' => 'Mandiri', 'account' => '0987654321', 'name' => '' . env('APP_NAME')],
                        ],
                    ],
                ],
            ],
            'selected_program' => [
                'name' => 'Teknik Informatika',
                'degree' => 'S1',
                'accreditation' => 'A',
            ],
        ];

        $data = [
            'simulation' => $simulationData,
        ];

        return $response->renderPage($data, [
            'path' => '/pmb/simulation',
            'meta' => ['title' => 'Simulasi PMB | ' . env('APP_NAME')],
        ]);
    }

    /**
     * Save simulation step data
     */
    public function saveSimulationStep(Request $request, Response $response)
    {
        $data = $request->getBody();
        $stepId = $data['step_id'] ?? null;

        // TODO: Implement actual save to database
        // For now, return success response

        return $response->json([
            'success' => true,
            'message' => 'Data berhasil disimpan',
        ]);
    }

    /**
     * Complete simulation and show summary
     */
    public function completeSimulation(Request $request, Response $response)
    {
        // TODO: Mark simulation as completed
        // Redirect to review page

        return $response->redirect('/pmb/simulation?completed=1');
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
