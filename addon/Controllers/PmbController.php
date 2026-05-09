<?php

namespace Addon\Controllers;

use Addon\Models\PmbJourneyModel;
use Addon\Models\ProfileModel;
use Addon\Models\StudentProfileModel;
use Addon\Services\GeminiService;
use App\Core\Http\RedirectResponse;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\View\View;
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

        $studentProfile = null;
        if ($profile) {
            $studentProfile = $this->studentProfileModel->findByProfileId($profile['id']);
        }

        $matchScoreData = [];
        $journey = null;
        if ($studentProfile) {
            // Hitung hash dari data saat ini
            $rawString = json_encode($studentProfile['academic_scores'] ?? []) .
                json_encode($studentProfile['psychological_tests'] ?? []) .
                json_encode($studentProfile['achievements'] ?? []) .
                json_encode($studentProfile['ai_analysis'] ?? []);
            $currentHash = md5($rawString);

            // Cek di DB
            $journey = $this->pmbJourneyModel->findByStudentId($studentProfile['id']);

            if (!$journey || $journey['last_data_hash'] !== $currentHash) {
                try {
                    $generatedMatches = $this->geminiService->generatePmbMatch($studentProfile);
                    $this->pmbJourneyModel->updateMatches($studentProfile['id'], $generatedMatches, $currentHash);
                    $matchScoreData = $generatedMatches;
                } catch (\Exception $e) {
                    // Fallback to existing if AI fails temporarily
                    if ($journey && !empty($journey['top_matches'])) {
                        $ai_error_message = $e->getMessage();
                        $matchScoreData = json_decode($journey['top_matches'], true);
                    } else {
                        // AI gagal dan belum ada data sebelumnya (mencegah view error)
                        return $response->redirect('/profile?error=500&message=' . urlencode($e->getMessage()));
                    }
                }
            } else {
                // Data mutakhir, gunakan yang di DB
                $matchScoreData = json_decode($journey['top_matches'], true);
            }
        }

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
            'ai_error_message' => $ai_error_message ?? null,
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
     * Convert simulation to real application
     */
    public function convertToRealApplication(Request $request, Response $response)
    {
        // TODO: Create actual application record
        // Generate application number
        // Send notification

        return $response->json([
            'success' => true,
            'application_number' => 'PMB-' . date('Ymd') . '-001',
            'message' => 'Pendaftaran berhasil dibuat!',
        ]);
    }

    /**
     * Display Scholarship Calculator
     */
    public function scholarship(Request $request, Response $response)
    {
        $profile = $this->profileModel->findByUserId($this->session->get('auth.user_id') ?? 0);
        $studentProfile = null;
        if ($profile) {
            $studentProfile = $this->studentProfileModel->findByProfileId($profile['id']);
        }

        // Base Scholarship Master Data
        $scholarshipData = [
            'available_scholarships' => [
                [
                    'id' => 1,
                    'name' => 'Beasiswa Akademis',
                    'type' => 'akademis',
                    'discount' => 25,
                    'requirements' => ['Nilai rata-rata minimal 85', 'Lulus wawancara'],
                    'quota' => 100,
                    'deadline' => '2024-12-31',
                ],
                [
                    'id' => 2,
                    'name' => 'Beasiswa Prestasi Nasional',
                    'type' => 'prestasi',
                    'discount' => 50,
                    'requirements' => ['Juara lomba tingkat nasional', 'Sertifikat asli'],
                    'quota' => 50,
                    'deadline' => '2024-11-30',
                ],
                [
                    'id' => 3,
                    'name' => 'KIP Kuliah (Eksternal)',
                    'type' => 'eksternal',
                    'discount' => 100,
                    'requirements' => ['Terdaftar di DTKS Kemensos', 'Pendapatan ortu rendah'],
                    'quota' => 30,
                    'deadline' => '2024-10-31',
                ],
            ],
            'user_eligibility' => [],
            'cost_estimation' => [
                'normal_fee' => 15000000,
                'eligible_discounts' => [],
                'final_fee' => 15000000,
            ],
        ];

        // Rule-Based Engine
        if ($studentProfile) {
            $scores = is_string($studentProfile['academic_scores']) ? json_decode($studentProfile['academic_scores'], true) : ($studentProfile['academic_scores'] ?? []);
            $achievements = is_string($studentProfile['achievements']) ? json_decode($studentProfile['achievements'], true) : ($studentProfile['achievements'] ?? []);

            // 1. Cek Akademik (Rata-rata > 85)
            $totalScore = 0;
            $count = 0;
            if (is_array($scores)) {
                foreach ($scores as $semester) {
                    if (is_array($semester['scores'] ?? null)) {
                        foreach ($semester['scores'] as $val) {
                            $totalScore += (float)$val;
                            $count++;
                        }
                    }
                }
            }
            $avgScore = $count > 0 ? ($totalScore / $count) : 0;

            if ($avgScore >= 85) {
                $scholarshipData['user_eligibility'][] = [
                    'scholarship_id' => 1,
                    'name' => 'Beasiswa Akademis',
                    'status' => 'eligible',
                    'reason' => 'Rata-rata nilai ' . number_format($avgScore, 1) . ' memenuhi syarat (Minimal 85).'
                ];
                $discount = $scholarshipData['cost_estimation']['normal_fee'] * 0.25;
                $scholarshipData['cost_estimation']['eligible_discounts'][] = ['name' => 'Beasiswa Akademis 25%', 'amount' => $discount];
                $scholarshipData['cost_estimation']['final_fee'] -= $discount;
            } else {
                $scholarshipData['user_eligibility'][] = [
                    'scholarship_id' => 1,
                    'name' => 'Beasiswa Akademis',
                    'status' => 'not_eligible',
                    'reason' => 'Rata-rata nilai ' . number_format($avgScore, 1) . ' belum memenuhi (Minimal 85).'
                ];
            }

            // 2. Cek Prestasi (Punya Sertifikat Nasional)
            $hasNasional = false;
            if (is_array($achievements)) {
                foreach ($achievements as $ach) {
                    if (strpos(strtolower($ach['level'] ?? ''), 'nasional') !== false) {
                        $hasNasional = true;
                        break;
                    }
                }
            }

            if ($hasNasional) {
                $scholarshipData['user_eligibility'][] = [
                    'scholarship_id' => 2,
                    'name' => 'Beasiswa Prestasi Nasional',
                    'status' => 'eligible',
                    'reason' => 'Sistem mendeteksi riwayat prestasi tingkat nasional.'
                ];
                $discount = $scholarshipData['cost_estimation']['normal_fee'] * 0.50;
                $scholarshipData['cost_estimation']['eligible_discounts'][] = ['name' => 'Beasiswa Prestasi 50%', 'amount' => $discount];
                $scholarshipData['cost_estimation']['final_fee'] -= $discount;
            } else {
                $scholarshipData['user_eligibility'][] = [
                    'scholarship_id' => 2,
                    'name' => 'Beasiswa Prestasi Nasional',
                    'status' => 'check_eligibility',
                    'reason' => 'Jika Anda merasa punya sertifikat, silakan lengkapi profil.'
                ];
            }

            // 3. KIP Kuliah (Eksternal) -> Default info
            $scholarshipData['user_eligibility'][] = [
                'scholarship_id' => 3,
                'name' => 'KIP Kuliah (Eksternal)',
                'status' => 'check_eligibility',
                'reason' => 'Silakan cek langsung ke portal KIP Kuliah Kemdikbud.'
            ];
        }

        $data = [
            'scholarships' => $scholarshipData,
        ];

        return $response->renderPage($data, [
            'path' => '/pmb/scholarship',
            'meta' => ['title' => 'Kalkulator Beasiswa | ' . env('APP_NAME')],
        ]);
    }

    /**
     * Calculate scholarship eligibility
     */
    public function calculateScholarship(Request $request, Response $response)
    {
        $data = $request->getBody();

        // TODO: Implement actual calculation
        // For now, return dummy response

        return $response->json([
            'success' => true,
            'eligible_scholarships' => [
                [
                    'id' => 1,
                    'name' => 'Beasiswa Akademis',
                    'discount' => 25,
                    'estimated_amount' => 3750000,
                ],
            ],
            'final_fee' => 11250000,
        ]);
    }

    /**
     * Apply for scholarship
     */
    public function applyScholarship(Request $request, Response $response)
    {
        $data = $request->getBody();
        $scholarshipId = $data['scholarship_id'] ?? null;

        // TODO: Implement actual application

        return $response->json([
            'success' => true,
            'message' => 'Pengajuan beasiswa berhasil dikirim',
        ]);
    }

    /**
     * API: Get match score
     */
    public function getMatchScore(Request $request, Response $response)
    {
        // Dummy API response
        return $response->json([
            'success' => true,
            'match_score' => [
                'study_program' => 'Teknik Informatika',
                'match_percentage' => 92,
                'breakdown' => [
                    'logic_score' => 85,
                    'interest_score' => 90,
                    'skill_score' => 78,
                    'potential_score' => 88,
                ],
            ],
        ]);
    }

    /**
     * API: Get simulation progress
     */
    public function getSimulationProgress(Request $request, Response $response)
    {
        return $response->json([
            'success' => true,
            'progress' => [
                'total_steps' => 5,
                'completed_steps' => 3,
                'percentage' => 60,
            ],
        ]);
    }

    /**
     * API: Get similar students (alumni testimonials)
     */
    public function getSimilarStudents(Request $request, Response $response)
    {
        return $response->json([
            'success' => true,
            'similar_students' => [
                [
                    'name' => 'Andi Pratama',
                    'high_school' => 'SMA Negeri 1',
                    'similarity' => 'Minat: Coding',
                    'testimonial' => 'Kuliah di Univeral cocok banget!',
                    'current_status' => 'Mahasiswa TI Semester 3',
                ],
            ],
        ]);
    }
}
