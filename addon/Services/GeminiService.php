<?php

namespace Addon\Services;

use App\Services\ConfigService;

/**
 * GeminiService - Service untuk integrasi dengan Google Gemini AI
 * 
 * Menyediakan method untuk chat dengan AI terkait konsultasi potensi, minat, dan bakat siswa
 */
class GeminiService
{
    private string $apiKey;
    private string $apiUrl;
    private string $modelName;

    /**
     * Constructor - inisialisasi konfigurasi Gemini API
     */
    public function __construct()
    {
        $this->apiKey = env('GEMINI_PROJECT_KEY');
        $this->apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models';
        $this->modelName = 'gemini-3-flash-preview';
    }

    /**
     * Kirim pesan ke Gemini AI dan dapatkan response
     * 
     * @param array $messages Array pesan dengan format [{role: 'user'|'assistant', content: string}]
     * @param array|null $contextData Data konteks tambahan (hasil tes psikologi, dll)
     * @return array Response dari AI dengan format ['content' => string, 'context_data' => array|null]
     * @throws \Exception Jika request gagal
     */
    public function chat(array $messages, ?array $contextData = null): array
    {
        // Build system instruction berdasarkan context
        $systemInstruction = $this->buildSystemInstruction($contextData);

        // Format messages untuk Gemini API
        $formattedMessages = $this->formatMessages($messages);

        // Prepare request body
        $requestBody = [
            'contents' => $formattedMessages,
            'systemInstruction' => [
                'parts' => [
                    ['text' => $systemInstruction]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'topK' => 40,
                'topP' => 0.95,
                'maxOutputTokens' => 2048,
            ],
        ];

        // Send request
        $response = $this->sendRequest($requestBody);

        // Parse response
        $aiContent = $this->parseResponse($response);

        return [
            'content' => $aiContent,
            'context_data' => $contextData,
        ];
    }

    /**
     * Chat sederhana dengan single message
     * 
     * @param string $message Pesan dari user
     * @param array|null $contextData Data konteks tambahan
     * @return array Response dari AI
     */
    public function chatSimple(string $message, ?array $contextData = null): array
    {
        return $this->chat([['role' => 'user', 'content' => $message]], $contextData);
    }

    /**
     * Build system instruction berdasarkan context data
     */
    private function buildSystemInstruction(?array $contextData): string
    {
        $baseInstruction = <<<INSTRUCTION
Anda adalah asisten konselor sekolah yang ramah, suportif, dan profesional. 
Tugas Anda adalah membantu siswa memahami potensi, minat, dan bakat mereka berdasarkan hasil tes psikologi.

Pedoman respons:
1. Gunakan bahasa Indonesia yang santai namun tetap profesional
2. Berikan penjelasan yang mudah dipahami siswa SMA
3. Fokus pada kekuatan dan potensi siswa
4. Berikan saran yang actionable dan realistis
5. Hindari memberikan diagnosis atau label negatif
6. Dorong siswa untuk mengembangkan potensi mereka
INSTRUCTION;

        if (empty($contextData)) {
            return $baseInstruction;
        }

        // Add context-specific information
        $contextParts = [];

        if (!empty($contextData['student_name'])) {
            $contextParts[] = "Nama siswa: {$contextData['student_name']}";
        }

        if (!empty($contextData['test_results'])) {
            $testResults = $contextData['test_results'];
            $contextParts[] = "\nHasil Tes Psikologi:";

            if (!empty($testResults['iq_score'])) {
                $contextParts[] = "- IQ Score: {$testResults['iq_score']}";
            }

            if (!empty($testResults['multiple_intelligences'])) {
                $contextParts[] = "- Kecerdasan Majemuk:";
                foreach ($testResults['multiple_intelligences'] as $type => $score) {
                    $contextParts[] = "  • {$type}: {$score}%";
                }
            }

            if (!empty($testResults['learning_style'])) {
                $contextParts[] = "- Gaya Belajar: {$testResults['learning_style']}";
            }

            if (!empty($testResults['personality_type'])) {
                $contextParts[] = "- Tipe Kepribadian: {$testResults['personality_type']}";
            }
        }

        if (!empty($contextData['interests'])) {
            $interests = implode(', ', $contextData['interests']);
            $contextParts[] = "\nMinat Siswa: {$interests}";
        }

        if (!empty($contextData['academic_performance'])) {
            $performance = $contextData['academic_performance'];
            $contextParts[] = "\nPerforma Akademik:";
            if (!empty($performance['strong_subjects'])) {
                $contextParts[] = "- Mata pelajaran unggul: " . implode(', ', $performance['strong_subjects']);
            }
            if (!empty($performance['weak_subjects'])) {
                $contextParts[] = "- Mata pelajaran perlu perbaikan: " . implode(', ', $performance['weak_subjects']);
            }
        }

        $contextString = implode("\n", $contextParts);

        return $baseInstruction . "\n\n" . $contextString;
    }

    /**
     * Format messages untuk Gemini API
     */
    private function formatMessages(array $messages): array
    {
        $formatted = [];

        foreach ($messages as $message) {
            $role = $message['role'] ?? 'user';
            $content = $message['content'] ?? '';

            // Gemini hanya menerima 'user' dan 'model' sebagai role
            $geminiRole = $role === 'assistant' ? 'model' : 'user';

            $formatted[] = [
                'role' => $geminiRole,
                'parts' => [
                    ['text' => $content]
                ]
            ];
        }

        return $formatted;
    }

    /**
     * Send request to Gemini API
     */
    private function sendRequest(array $requestBody): array
    {
        $url = "{$this->apiUrl}/{$this->modelName}:generateContent?key={$this->apiKey}";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestBody));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new \Exception("Curl error: {$error}");
        }

        if ($httpCode !== 200) {
            $errorData = json_decode($response, true);
            $errorMessage = $errorData['error']['message'] ?? 'Unknown error';
            throw new \Exception("Gemini API error ({$httpCode}): {$errorMessage}");
        }

        return json_decode($response, true);
    }

    /**
     * Parse response dari Gemini API
     */
    private function parseResponse(array $response): string
    {
        if (empty($response['candidates'][0]['content']['parts'][0]['text'])) {
            return "Maaf, saya sedang mengalami kesulitan. Silakan coba lagi.";
        }

        return $response['candidates'][0]['content']['parts'][0]['text'];
    }

    /**
     * Generate analisis potensi siswa berdasarkan hasil tes
     * 
     * @param array $testResults Hasil tes psikologi
     * @return string Analisis potensi dalam format teks
     */
    public function generatePotentialAnalysis(array $testResults): string
    {
        $prompt = $this->buildAnalysisPrompt($testResults);
        $response = $this->chatSimple($prompt);

        return $response['content'];
    }

    /**
     * Build prompt untuk analisis hasil tes
     */
    private function buildAnalysisPrompt(array $testResults): string
    {
        $prompt = "Berdasarkan hasil tes psikologi berikut, berikan analisis potensi yang komprehensif:\n\n";

        if (!empty($testResults['iq_score'])) {
            $prompt .= "IQ Score: {$testResults['iq_score']}\n";
        }

        if (!empty($testResults['multiple_intelligences'])) {
            $prompt .= "\nKecerdasan Majemuk:\n";
            foreach ($testResults['multiple_intelligences'] as $type => $score) {
                $prompt .= "- {$type}: {$score}%\n";
            }
        }

        if (!empty($testResults['learning_style'])) {
            $prompt .= "\nGaya Belajar: {$testResults['learning_style']}\n";
        }

        if (!empty($testResults['personality_type'])) {
            $prompt .= "\nTipe Kepribadian: {$testResults['personality_type']}\n";
        }

        $prompt .= "\nBerikan analisis yang mencakup:\n";
        $prompt .= "1. Kekuatan utama siswa\n";
        $prompt .= "2. Potensi karir yang sesuai\n";
        $prompt .= "3. Saran pengembangan diri\n";
        $prompt .= "4. Rekomendasi jurusan kuliah (jika relevan)\n";

        return $prompt;
    }

    /**
     * Generate analisis profil siswa secara komprehensif (Akademik, Psikologi, Prestasi)
     * Format output: JSON String
     */
    public function generateProfileAnalysis(array $studentData): string
    {
        $prompt = $this->buildProfileAnalysisPrompt($studentData);
        $response = $this->chatSimple($prompt);

        // Membersihkan markdown block json jika ada (```json ... ```)
        $content = $response['content'];
        $content = preg_replace('/```json\s*/', '', $content);
        $content = preg_replace('/```\s*$/', '', $content);

        return trim($content);
    }

    /**
     * Build prompt untuk analisis profil lengkap (dengan output JSON)
     */
    private function buildProfileAnalysisPrompt(array $studentData): string
    {
        $prompt = "Berperanlah sebagai psikolog pendidikan dan konselor karir ahli. ";
        $prompt .= "Berdasarkan data siswa berikut (Akademik, Psikologi, Prestasi), berikan analisis potensi yang komprehensif.\n\n";

        if (!empty($studentData['academic_scores'])) {
            $prompt .= "=== DATA AKADEMIK (Nilai Rapor per Semester) ===\n";
            $prompt .= json_encode($studentData['academic_scores'], JSON_PRETTY_PRINT) . "\n\n";
        }

        if (!empty($studentData['psychological_tests'])) {
            $prompt .= "=== DATA TES PSIKOLOGI ===\n";
            $prompt .= json_encode($studentData['psychological_tests'], JSON_PRETTY_PRINT) . "\n\n";
        }

        if (!empty($studentData['achievements'])) {
            $prompt .= "=== DATA PRESTASI ===\n";
            $prompt .= json_encode($studentData['achievements'], JSON_PRETTY_PRINT) . "\n\n";
        }

        $prompt .= "=== INSTRUKSI OUTPUT ===\n";
        $prompt .= "Anda WAJIB memberikan respons HANYA dalam format JSON yang valid. Jangan tambahkan teks apa pun di luar JSON. Gunakan skema JSON berikut:\n";
        $prompt .= <<<JSON
{
  "summary": "Ringkasan eksekutif tentang profil siswa (1 paragraf kuat)",
  "potential": [
    "Potensi 1", "Potensi 2", "Potensi 3"
  ],
  "interests": [
    {"name": "Nama Minat 1", "level": 85},
    {"name": "Nama Minat 2", "level": 70}
  ],
  "talents": [
    {"name": "Bakat Utama", "icon": "⭐", "score": 90},
    {"name": "Bakat Tambahan", "icon": "🎨", "score": 80}
  ],
  "recommendations": [
    "Saran pengembangan diri 1",
    "Saran pengembangan diri 2"
  ],
  "career_suggestions": [
    "Profesi A", "Profesi B", "Profesi C"
  ]
}
JSON;

        return $prompt;
    }

    /**
     * Generate rekomendasi Program Studi spesifik untuk PMB Universal
     * Memaksa respons JSON murni
     */
    public function generatePmbMatch(array $studentData): array
    {
        $prompt = $this->buildPmbMatchPrompt($studentData);
        $payload = [
            'contents' => [
                ['role' => 'user', 'parts' => [['text' => $prompt]]]
            ],
            'generationConfig' => [
                'temperature' => 0.4,
                'responseMimeType' => 'application/json'
            ]
        ];

        try {
            $response = $this->sendRequest($payload);
            $responseText = $response['candidates'][0]['content']['parts'][0]['text'] ?? '';

            // Extract JSON
            $jsonStart = strpos($responseText, '{');
            $jsonEnd = strrpos($responseText, '}');
            if ($jsonStart !== false && $jsonEnd !== false) {
                $responseText = substr($responseText, $jsonStart, $jsonEnd - $jsonStart + 1);
            }

            $decoded = json_decode($responseText, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("Invalid JSON format dari Gemini");
            }
            return $decoded;
        } catch (\Exception $e) {
            error_log("Gemini PMB Match Error: " . $e->getMessage());
            throw new \Exception("Gagal menghubungi layanan AI: " . $e->getMessage());
        }
    }

    private function buildPmbMatchPrompt(array $studentData): string
    {
        $prompt = "Tugas Anda adalah sebagai Konsultan Penerimaan Mahasiswa Baru yang proaktif.\n";
        $prompt .= "Tujuan Utama: Arahkan siswa untuk mendaftar ke Program Studi di Universitas Univeral (Kampus ini hanya memiliki Fakultas: Teknologi/IT, Bisnis Digital, Desain Komunikasi Visual, Ilmu Komunikasi). Cocokkan dengan data mereka sebaik mungkin.\n";
        $prompt .= "Skenario Fallback: Jika potensi siswa SANGAT BERTOLAK BELAKANG (misal: murni ingin Kedokteran/Farmasi yang tidak ada di Universitas Univeral), Anda BISA merekomendasikan kampus eksternal, NAMUN tetap tawarkan 1 opsi terdekat di Universitas Univeral sebagai alternatif cadangan.\n\n";

        if (!empty($studentData['academic_scores'])) {
            $prompt .= "=== DATA AKADEMIK ===\n" . json_encode($studentData['academic_scores']) . "\n\n";
        }
        if (!empty($studentData['psychological_tests'])) {
            $prompt .= "=== DATA PSIKOLOGI ===\n" . json_encode($studentData['psychological_tests']) . "\n\n";
        }
        if (!empty($studentData['achievements'])) {
            $prompt .= "=== DATA PRESTASI ===\n" . json_encode($studentData['achievements']) . "\n\n";
        }
        if (!empty($studentData['ai_analysis'])) {
            $prompt .= "=== ANALISIS AI SEBELUMNYA ===\n" . json_encode($studentData['ai_analysis']) . "\n\n";
        }

        $prompt .= "Respons WAJIB dalam format JSON murni berikut (tanpa blok markdown):\n";
        $prompt .= <<<JSON
{
  "top_match": {
    "university": "Universitas Univeral",
    "study_program": "Nama Jurusan Terbaik",
    "degree_type": "S1/D3",
    "accreditation": "Unggul/A/B",
    "match_percentage": 95,
    "reason": "Alasan spesifik kenapa sangat cocok."
  },
  "other_matches": [
    {
      "university": "Nama Kampus",
      "study_program": "Nama Jurusan Alternatif",
      "match_percentage": 85,
      "reason": "..."
    }
  ],
  "career_paths": [
    {"semester": "1-2", "title": "Fundamental", "description": "..."},
    {"semester": "Graduation", "title": "Profesi Impian", "description": "..."}
  ],
  "partner_companies": [
    {"name": "Nama Perusahaan Top Terkait", "type": "Bidang Industri"}
  ]
}
JSON;
        return $prompt;
    }

    /**
     * Test koneksi ke Gemini API
     */
    public function testConnection(): bool
    {
        try {
            $response = $this->chatSimple('Halo, ini adalah test koneksi.');
            return !empty($response['content']);
        } catch (\Exception $e) {
            return false;
        }
    }
}
