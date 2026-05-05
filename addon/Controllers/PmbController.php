<?php

namespace Addon\Controllers;

use Addon\Models\ProfileModel;
use Addon\Models\StudentProfileModel;
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
        private StudentProfileModel $studentProfileModel
    ) {}

    /**
     * Display Match Score Dashboard (Journey page)
     */
    public function journey(Request $request, Response $response)
    {
        // Dummy data untuk visualisasi
        $matchScoreData = [
            'top_match' => [
                'study_program' => 'Teknik Informatika',
                'match_percentage' => 92,
                'accreditation' => 'A',
                'degree_type' => 'S1',
                'skills_breakdown' => [
                    ['name' => 'Potensi Logika', 'score' => 85],
                    ['name' => 'Minat Coding', 'score' => 90],
                    ['name' => 'Kreativitas', 'score' => 75],
                    ['name' => 'Komunikasi', 'score' => 88],
                ],
                'career_paths' => [
                    [
                        'semester' => '1-2',
                        'title' => 'Fundamental',
                        'description' => 'Algoritma, Matematika, Basis Data',
                    ],
                    [
                        'semester' => '3-4',
                        'title' => 'Specialization',
                        'description' => 'Mobile Dev / Web Dev / AI',
                    ],
                    [
                        'semester' => '5-6',
                        'title' => 'Internship',
                        'description' => 'Partner: Google, Tokopedia, Gojek',
                    ],
                    [
                        'semester' => 'Graduation',
                        'title' => 'Software Engineer',
                        'description' => 'Expected salary: Rp 8-12 juta/month',
                    ],
                ],
                'partner_companies' => [
                    ['name' => 'Google', 'type' => 'Tech Giant'],
                    ['name' => 'Tokopedia', 'type' => 'E-Commerce'],
                    ['name' => 'Gojek', 'type' => 'Super App'],
                    ['name' => 'Traveloka', 'type' => 'Travel Tech'],
                ],
            ],
            'other_matches' => [
                ['study_program' => 'Sistem Informasi', 'match_percentage' => 87],
                ['study_program' => 'Desain Komunikasi Visual', 'match_percentage' => 82],
                ['study_program' => 'Manajemen Informatika', 'match_percentage' => 79],
            ],
            'scholarships' => [
                [
                    'name' => 'Beasiswa Akademis',
                    'discount' => 25,
                    'status' => 'eligible',
                    'requirement' => 'Nilai rata-rata > 85',
                ],
                [
                    'name' => 'Beasiswa Prestasi',
                    'discount' => 50,
                    'status' => 'check_eligibility',
                    'requirement' => 'Juara lomba bidang IT',
                ],
            ],
            'alumni_testimonials' => [
                [
                    'name' => 'Andi Pratama',
                    'high_school' => 'SMA Negeri 1',
                    'similarity' => 'Minat: Coding',
                    'testimonial' => 'Kuliah di Universal cocok banget untuk saya yang suka practical. Banyak proyek nyata dan internship di perusahaan top!',
                    'current_status' => 'Mahasiswa TI Semester 3',
                ],
                [
                    'name' => 'Siti Nurhaliza',
                    'high_school' => 'SMA Negeri 5',
                    'similarity' => 'Minat: Design',
                    'testimonial' => 'Dosennya berpengalaman industri, kurikulumnya update, dan fasilitasnya lengkap!',
                    'current_status' => 'Mahasiswa DKV Semester 2',
                ],
            ],
            'simulation_progress' => [
                'total_steps' => 5,
                'completed_steps' => 3,
                'steps' => [
                    ['name' => 'Data Pribadi', 'is_completed' => true],
                    ['name' => 'Nilai Akademik', 'is_completed' => true],
                    ['name' => 'Hasil Analisis AI', 'is_completed' => true],
                    ['name' => 'Upload Dokumen', 'is_completed' => false],
                    ['name' => 'Pembayaran', 'is_completed' => false],
                ],
            ],
        ];

        // Get current user profile
        $profile = $this->profileModel->findByUserId($this->session->get('auth.user_id') ?? 0);

        $studentProfile = null;
        if ($profile) {
            $studentProfile = $this->studentProfileModel->findByProfileId($profile['id']);
        }

        $data = [
            'profile' => $profile,
            'student_profile' => $studentProfile,
            'match_score' => $matchScoreData,
        ];

        return $response->renderPage($data, [
            'path' => '/pmb/journey',
            'meta' => ['title' => 'PMB Journey | Universitas Universal'],
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
                            ['bank' => 'BCA', 'account' => '1234567890', 'name' => 'Universitas Universal'],
                            ['bank' => 'Mandiri', 'account' => '0987654321', 'name' => 'Universitas Universal'],
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
            'meta' => ['title' => 'Simulasi PMB | Universitas Universal'],
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
        // Dummy data untuk scholarship
        $scholarshipData = [
            'available_scholarships' => [
                [
                    'id' => 1,
                    'name' => 'Beasiswa Akademis',
                    'type' => 'akademis',
                    'discount' => 25,
                    'requirements' => [
                        'Nilai rata-rata minimal 85',
                        'Lulus wawancara',
                    ],
                    'quota' => 100,
                    'deadline' => '2024-12-31',
                ],
                [
                    'id' => 2,
                    'name' => 'Beasiswa Prestasi',
                    'type' => 'prestasi',
                    'discount' => 50,
                    'requirements' => [
                        'Juara 1/2/3 lomba tingkat nasional',
                        'Sertifikat asli',
                    ],
                    'quota' => 50,
                    'deadline' => '2024-11-30',
                ],
                [
                    'id' => 3,
                    'name' => 'Beasiswa Tidak Mampu',
                    'type' => 'tidak_mampu',
                    'discount' => 100,
                    'requirements' => [
                        'Surat keterangan tidak mampu',
                        'Rekomendasi sekolah',
                    ],
                    'quota' => 30,
                    'deadline' => '2024-10-31',
                ],
            ],
            'user_eligibility' => [
                [
                    'scholarship_id' => 1,
                    'name' => 'Beasiswa Akademis',
                    'status' => 'eligible',
                    'reason' => 'Nilai rata-rata kamu 87.5 memenuhi syarat',
                ],
                [
                    'scholarship_id' => 2,
                    'name' => 'Beasiswa Prestasi',
                    'status' => 'check_eligibility',
                    'reason' => 'Silakan upload sertifikat prestasi',
                ],
                [
                    'scholarship_id' => 3,
                    'name' => 'Beasiswa Tidak Mampu',
                    'status' => 'not_checked',
                    'reason' => 'Belum mengajukan',
                ],
            ],
            'cost_estimation' => [
                'normal_fee' => 15000000,
                'eligible_discounts' => [
                    ['name' => 'Beasiswa Akademis 25%', 'amount' => 3750000],
                ],
                'final_fee' => 11250000,
            ],
        ];

        $data = [
            'scholarships' => $scholarshipData,
        ];

        return $response->renderPage($data, [
            'path' => '/pmb/scholarship',
            'meta' => ['title' => 'Kalkulator Beasiswa | Universitas Universal'],
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
                    'testimonial' => 'Kuliah di Universal cocok banget!',
                    'current_status' => 'Mahasiswa TI Semester 3',
                ],
            ],
        ]);
    }
}
