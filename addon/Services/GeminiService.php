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
