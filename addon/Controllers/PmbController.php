<?php

namespace Addon\Controllers;

use Addon\Models\PmbJourneyModel;
use Addon\Models\ProfileModel;
use Addon\Models\StudentProfileModel;
use Addon\Services\GeminiService;
use App\Core\Http\RedirectResponse;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Services\SessionService;

/**
 * PMB Controller
 *
 * Handles PMB Journey, Simulation, and Scholarship features
 */
class PmbController
{
    public function __construct(
        private SessionService $session,
        private ProfileModel $profileModel,
        private StudentProfileModel $studentProfileModel,
        private PmbJourneyModel $pmbJourneyModel,
        private GeminiService $geminiService
    ) {}

    public function index(Request $request, Response $response): RedirectResponse
    {
        return $response->redirect('/pmb/journey');
    }

    /**
     * Display Match Score Dashboard (Journey page)
     */
    public function journey(Request $request, Response $response)
    {
        // Get current user profile
        $profile = $this->profileModel->findByUserId($this->session->get('auth.user_id') ?? 0);
        $studentProfile = $profile ? $this->studentProfileModel->findByProfileId($profile['id']) : null;

        // Generate or fetch cached AI data
        $result = $this->getOrGenerateAiData($studentProfile, 'matches');

        $matchScoreData = $result['data'];
        $journey = $result['journey'];
        $ai_error_message = $result['error'];

        // Dynamic Simulation Progress from Database
        $currentSimulationStep = $journey ? (int)($journey['simulation_step'] ?? 1) : 1;
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
            'ai_error_message' => $ai_error_message,
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
     * Display Scholarship Calculator
     *
     * Menggunakan AI untuk generate rekomendasi beasiswa berdasarkan profil siswa
     * Pattern sama seperti journey() - hash-based trigger untuk call AI
     */
    public function scholarship(Request $request, Response $response)
    {
        // Get current user profile
        $profile = $this->profileModel->findByUserId($this->session->get('auth.user_id') ?? 0);
        $studentProfile = $profile ? $this->studentProfileModel->findByProfileId($profile['id']) : null;

        // Initialize scholarship data
        $scholarshipData = [
            'eligible_scholarships' => [],
            'not_eligible_scholarships' => [],
            'average_score' => 0,
            'has_national_achievement' => false,
            'technology_interest_level' => 'unknown',
        ];

        $ai_error_message = null;

        if ($studentProfile) {
            // Use rule-based calculator (500x faster, zero API cost)
            $calculator = new \Addon\Services\ScholarshipCalculator();

            try {
                $scholarshipData = $calculator->calculateEligibility($studentProfile);
            } catch (\Exception $e) {
                $ai_error_message = "Terjadi kesalahan: " . $e->getMessage();
                // Fallback to empty data
            }
        }

        $data = [
            'profile' => $profile,
            'student_profile' => $studentProfile,
            'scholarships' => $scholarshipData,
            'ai_error_message' => $ai_error_message,
        ];
        // dd($data);

        return $response->renderPage($data, [
            'path' => '/pmb/scholarship',
            'meta' => ['title' => 'Kalkulator Beasiswa | ' . env('APP_NAME')],
        ]);
    }

    /**
     * Get or generate AI data (PMB matches) dengan hash-based caching
     *
     * Method ini menghandle pattern yang berulang untuk PMB matching:
     * 1. Hitung hash dari data siswa
     * 2. Cek cache di database
     * 3. Jika hash berbeda/belum ada, call AI untuk generate matches
     * 4. Fallback ke cached data jika AI gagal
     *
     * NOTE: Untuk scholarship, sekarang menggunakan ScholarshipCalculator (rule-based)
     * langsung di controller, tidak lagi menggunakan caching di database.
     *
     * @param array|null $studentProfile Data profil siswa
     * @param string $type Tipe data: 'matches' (scholarships deprecated)
     * @return array ['data' => array, 'journey' => array|null, 'error' => string|null]
     */
    private function getOrGenerateAiData(?array $studentProfile, string $type): array
    {
        $data = [];
        $journey = null;
        $error = null;

        if (!$studentProfile) {
            return ['data' => $data, 'journey' => $journey, 'error' => $error];
        }

        // Hitung hash dari data siswa
        $rawString = json_encode($studentProfile['academic_scores'] ?? []) .
            json_encode($studentProfile['psychological_tests'] ?? []) .
            json_encode($studentProfile['achievements'] ?? []) .
            json_encode($studentProfile['ai_analysis'] ?? []);
        $currentHash = md5($rawString);

        // Cek di DB
        $studentProfileId = $studentProfile['student_profile_id'] ?? $studentProfile['id'] ?? null;
        $journey = $this->pmbJourneyModel->findByStudentId($studentProfileId);

        // Tentukan field dan method berdasarkan type
        // NOTE: 'scholarships' deprecated, sekarang menggunakan ScholarshipCalculator
        $dbField = $type === 'matches' ? 'top_matches' : 'scholarships';
        $aiMethod = $type === 'matches' ? 'generatePmbMatch' : 'generateScholarshipRecommendations';
        $updateMethod = $type === 'matches' ? 'updateMatches' : 'updateScholarships';

        // Cek apakah perlu call AI
        $needsRefresh = !$journey ||
            $journey['last_data_hash'] !== $currentHash ||
            empty($journey[$dbField]);

        if ($needsRefresh) {
            try {
                // Call AI untuk generate data
                $generatedData = $this->geminiService->$aiMethod($studentProfile);
                $this->pmbJourneyModel->$updateMethod($studentProfileId, $generatedData, $currentHash);
                $data = $generatedData;
            } catch (\Exception $e) {
                // Fallback to existing if AI fails temporarily
                if ($journey && !empty($journey[$dbField])) {
                    $error = $e->getMessage();
                    $data = json_decode($journey[$dbField], true);
                } else {
                    // AI gagal dan belum ada data sebelumnya
                    throw new \Exception($e->getMessage());
                }
            }
        } else {
            // Data mutakhir, gunakan yang di DB
            $data = json_decode($journey[$dbField], true);
        }

        return ['data' => $data, 'journey' => $journey, 'error' => $error];
    }
}
