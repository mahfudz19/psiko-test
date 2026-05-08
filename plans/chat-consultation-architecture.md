# Chat Consultation Architecture

## Overview

Dokumen ini merancang arsitektur untuk fitur **Chat Consultation** yang memungkinkan siswa konsultasi langsung dengan AI (Google Gemini) terkait hasil potensi, minat, dan bakat mereka.

**Gemini API Credentials:**

```
GEMINI_PROJECT_ID=gen-lang-client-0323602652
GEMINI_PROJECT_NAME=projects/446655408821
GEMINI_PROJECT_KEY=AIzaSyASWbenPOQ23MmpWXNnIUaKgz_spU_NY28
```

---

## Design Principles

1. **Contextual AI**: AI memiliki konteks lengkap tentang profil siswa
2. **Session-based**: Setiap sesi chat tersimpan untuk referensi masa depan
3. **Privacy-first**: Data sensitif tidak dikirim ke external API
4. **Real-time**: Chat UI yang responsif dengan loading indicator
5. **Educational Focus**: AI berperan sebagai konselor pendidikan

---

## Entity Relationship Diagram

```mermaid
erDiagram
    users ||--|| profiles : has
    profiles ||--|| student_profiles : has
    student_profiles ||--o{ chat_consultations : has
    chat_consultations ||--o{ chat_messages : contains

    student_profiles {
        bigint id PK
        bigint profile_id FK
        bigint school_id FK
        json academic_scores
        json psychological_tests
        json ai_analysis
        string grade_level
        string major
    }

    chat_consultations {
        bigint id PK
        bigint student_profile_id FK
        string session_id
        string topic
        datetime created_at
        datetime updated_at
    }

    chat_messages {
        bigint id PK
        bigint chat_consultation_id FK
        enum role
        text content
        json context_data
        datetime created_at
    }
```

---

## Database Schema

### 1. Tabel `chat_consultations`

```php
protected array $schema = [
    'id' => ['type' => 'id', 'primary' => true, 'auto_increment' => true],
    'student_profile_id' => ['type' => 'bigint', 'unsigned' => true, 'foreign' => 'student_profiles.id', 'on_delete' => 'cascade'],
    'session_id' => ['type' => 'varchar', 'length' => 100, 'nullable' => false, 'index' => true],
    'topic' => ['type' => 'varchar', 'length' => 100, 'default' => 'potential_analysis'],
    'created_at' => ['type' => 'timestamp', 'default' => 'CURRENT_TIMESTAMP'],
    'updated_at' => ['type' => 'timestamp', 'default' => 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'],
];
```

**Field penting:**

- `session_id` - UUID untuk identifikasi sesi (bisa digunakan untuk resume chat)
- `topic` - Topik konsultasi (potential_analysis, career_guidance, study_tips, dll)

### 2. Tabel `chat_messages`

```php
protected array $schema = [
    'id' => ['type' => 'id', 'primary' => true, 'auto_increment' => true],
    'chat_consultation_id' => ['type' => 'bigint', 'unsigned' => true, 'foreign' => 'chat_consultations.id', 'on_delete' => 'cascade'],
    'role' => ['type' => 'enum', 'values' => ['user', 'assistant'], 'nullable' => false],
    'content' => ['type' => 'text', 'nullable' => false],
    'context_data' => ['type' => 'json', 'nullable' => true], // Snapshot context saat pesan dikirim
    'created_at' => ['type' => 'timestamp', 'default' => 'CURRENT_TIMESTAMP'],
];
```

**Field penting:**

- `role` - Menentukan apakah pesan dari user atau AI assistant
- `context_data` - Snapshot data siswa saat pesan dikirim (untuk debugging/analytics)

---

## Component Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                        Student User                              │
│                           │                                      │
│                           ▼                                      │
│                    ┌─────────────┐                               │
│                    │   Chat UI   │                               │
│                    └─────────────┘                               │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                      Mazu Framework                              │
│                                                                  │
│  ┌──────────────────┐    ┌──────────────────┐                  │
│  │  ChatController  │───▶│  GeminiService   │                  │
│  └──────────────────┘    └──────────────────┘                  │
│         │                       │                                │
│         ▼                       ▼                                │
│  ┌──────────────────┐    ┌──────────────────┐                  │
│  │  ChatConsultation│    │  Context Builder │                  │
│  │  ChatMessage     │    └──────────────────┘                  │
│  └──────────────────┘                                          │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                      Google Gemini API                           │
│                                                                  │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │  System Prompt + Student Context + Chat History         │   │
│  │                        ▼                                 │   │
│  │  Gemini Pro Model                                        │   │
│  │                        ▼                                 │   │
│  │  AI Response                                             │   │
│  └─────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                         Database                                 │
│                                                                  │
│  ┌──────────────────┐    ┌──────────────────┐                  │
│  │ chat_consultations│   │  chat_messages   │                  │
│  └──────────────────┘    └──────────────────┘                  │
│                                                                  │
│  ┌──────────────────┐    ┌──────────────────┐                  │
│  │ student_profiles │    │    users         │                  │
│  └──────────────────┘    └──────────────────┘                  │
└─────────────────────────────────────────────────────────────────┘
```

---

## Implementation Details

### 1. GeminiService

**File:** `addon/Services/GeminiService.php`

```php
<?php

namespace Addon\Services;

/**
 * Service untuk integrasi dengan Google Gemini AI
 */
class GeminiService
{
    private string $apiKey;
    private string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta';

    public function __construct()
    {
        $this->apiKey = env('GEMINI_PROJECT_KEY');
    }

    /**
     * Chat dengan context profil siswa
     *
     * @param array $messages Array pesan chat history
     * @param array $studentContext Data konteks siswa
     * @return array Response dari AI
     */
    public function chat(array $messages, array $studentContext): array
    {
        $systemPrompt = $this->buildSystemPrompt($studentContext);
        $response = $this->callGeminiAPI($systemPrompt, $messages);

        return [
            'content' => $response['candidates'][0]['content']['parts'][0]['text'] ?? '',
            'usage' => $response['usageMetadata'] ?? null,
            'finishReason' => $response['candidates'][0]['finishReason'] ?? null,
        ];
    }

    /**
     * Build system prompt dengan data potensi, minat, bakat siswa
     */
    private function buildSystemPrompt(array $studentContext): string
    {
        $aiAnalysis = $studentContext['ai_analysis'] ?? [];
        $academicScores = $studentContext['academic_scores'] ?? [];
        $psychologicalTests = $studentContext['psychological_tests'] ?? [];
        $studentName = $studentContext['student']['name'] ?? 'Siswa';
        $gradeLevel = $studentContext['student']['grade_level'] ?? '';

        return <<<PROMPT
Anda adalah konselor pendidikan AI yang membantu siswa memahami hasil analisis potensi, minat, dan bakat mereka.

IDENTITAS SISWA:
- Nama: {$studentName}
- Kelas: {$gradeLevel}

PROFIL AKADEMIK:
{$this->formatScores($academicScores)}

HASIL ANALISIS AI:
- Potensi Teridentifikasi: {$this->formatArray($aiAnalysis['potentials'] ?? [])}
- Minat: {$this->formatArray($aiAnalysis['interests'] ?? [])}
- Bakat: {$this->formatArray($aiAnalysis['talents'] ?? [])}
- Rekomendasi Jurusan: {$this->formatArray($aiAnalysis['recommendations'] ?? [])}

HASIL TES PSIKOLOGI:
{$this->formatPsychologicalTests($psychologicalTests)}

TUGAS ANDA:
1. Bantu siswa memahami hasil analisis mereka dengan bahasa yang mudah dimengerti
2. Berikan insight tentang bagaimana potensi mereka bisa dikembangkan
3. Diskusikan kesesuaian minat dengan bakat yang teridentifikasi
4. Berikan saran konkret untuk pengembangan diri
5. Jawab pertanyaan tentang rekomendasi jurusan dan karir
6. Berikan motivasi dan dukungan positif

GAYA KOMUNIKASI:
- Gunakan bahasa Indonesia yang santai tapi profesional
- Bersifat mendukung dan memotivasi
- Hindari jargon teknis yang berlebihan
- Berikan contoh konkret ketika menjelaskan
- Gunakan emoji secukupnya untuk membuat percakapan lebih friendly
- Fokus pada kekuatan (strengths-based approach)

BATASAN:
- Jangan memberikan diagnosa medis atau psikologis
- Jangan membuat janji atau garansi tentang masa depan
- Arahkan ke guru BK/konselor manusia untuk isu serius
PROMPT;
    }

    /**
     * Call Gemini API dengan cURL
     */
    private function callGeminiAPI(string $systemPrompt, array $messages): array
    {
        $contents = [];

        // Add system prompt as first message
        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $systemPrompt]],
        ];
        $contents[] = [
            'role' => 'model',
            'parts' => [['text' => 'Baik, saya siap membantu siswa ini dengan konteks yang diberikan.']],
        ];

        // Add chat history
        foreach ($messages as $msg) {
            $role = $msg['role'] === 'user' ? 'user' : 'model';
            $contents[] = [
                'role' => $role,
                'parts' => [['text' => $msg['content']]],
            ];
        }

        $url = "{$this->baseUrl}/models/gemini-pro:generateContent?key={$this->apiKey}";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => 0.7,
                'topK' => 40,
                'topP' => 0.95,
                'maxOutputTokens' => 1024,
            ],
            'safetySettings' => [
                [
                    'category' => 'HARM_CATEGORY_HARASSMENT',
                    'threshold' => 'BLOCK_MEDIUM_AND_ABOVE',
                ],
                [
                    'category' => 'HARM_CATEGORY_HATE_SPEECH',
                    'threshold' => 'BLOCK_MEDIUM_AND_ABOVE',
                ],
            ],
        ]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new \Exception("Gemini API error: {$httpCode}");
        }

        return json_decode($response, true);
    }

    private function formatScores(array $scores): string
    {
        if (empty($scores)) {
            return '- Data tidak tersedia';
        }

        $lines = [];
        foreach ($scores as $subject => $score) {
            $lines[] = "  - {$subject}: {$score}";
        }
        return implode("\n", $lines);
    }

    private function formatArray(array $items): string
    {
        if (empty($items)) {
            return '- Data tidak tersedia';
        }
        return implode(', ', $items);
    }

    private function formatPsychologicalTests(array $tests): string
    {
        if (empty($tests)) {
            return '- Belum ada data tes psikologi';
        }

        $lines = [];
        foreach ($tests as $test) {
            $testName = $test['test_name'] ?? 'Unknown Test';
            $scores = is_array($test['scores'])
                ? json_encode($test['scores'])
                : $test['scores'];
            $date = $test['date'] ?? 'Unknown';
            $lines[] = "- {$testName} ({$date}): {$scores}";
        }
        return implode("\n", $lines);
    }
}
```

### 2. ChatConsultationModel

**File:** `addon/Models/ChatConsultationModel.php`

```php
<?php

namespace Addon\Models;

use App\Core\Database\Model;

/**
 * Model untuk chat consultation sessions
 */
class ChatConsultationModel extends Model
{
    protected string $table = 'chat_consultations';

    protected array $schema = [
        'id' => ['type' => 'id', 'primary' => true, 'auto_increment' => true],
        'student_profile_id' => ['type' => 'bigint', 'unsigned' => true, 'foreign' => 'student_profiles.id', 'on_delete' => 'cascade'],
        'session_id' => ['type' => 'varchar', 'length' => 100, 'nullable' => false, 'index' => true],
        'topic' => ['type' => 'varchar', 'length' => 100, 'default' => 'potential_analysis'],
        'created_at' => ['type' => 'timestamp', 'default' => 'CURRENT_TIMESTAMP'],
        'updated_at' => ['type' => 'timestamp', 'default' => 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'],
    ];

    /**
     * Get chat sessions by student profile ID
     */
    public function getByStudentId(int $studentProfileId): array
    {
        $stmt = $this->getDb()->prepare("
            SELECT * FROM chat_consultations
            WHERE student_profile_id = :student_profile_id
            ORDER BY updated_at DESC
        ");
        $stmt->execute(['student_profile_id' => $studentProfileId]);
        return $stmt->fetchAll();
    }

    /**
     * Find chat session by session ID
     */
    public function findBySessionId(string $sessionId): ?array
    {
        $stmt = $this->getDb()->prepare("SELECT * FROM chat_consultations WHERE session_id = :session_id LIMIT 1");
        $stmt->execute(['session_id' => $sessionId]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    /**
     * Create new chat session with UUID
     */
    public function createWithSessionId(int $studentProfileId, string $topic = 'potential_analysis'): int
    {
        $sessionId = $this->generateSessionId();
        return $this->create([
            'student_profile_id' => $studentProfileId,
            'session_id' => $sessionId,
            'topic' => $topic,
        ]);
    }

    /**
     * Generate unique session ID
     */
    private function generateSessionId(): string
    {
        return 'chat_' . bin2hex(random_bytes(16));
    }

    /**
     * Delete chat session
     */
    public function deleteById(int $id): bool
    {
        return $this->getDb()->execute("DELETE FROM chat_consultations WHERE id = :id", ['id' => $id]);
    }
}
```

### 3. ChatMessageModel

**File:** `addon/Models/ChatMessageModel.php`

```php
<?php

namespace Addon\Models;

use App\Core\Database\Model;

/**
 * Model untuk chat messages
 */
class ChatMessageModel extends Model
{
    protected string $table = 'chat_messages';

    protected array $schema = [
        'id' => ['type' => 'id', 'primary' => true, 'auto_increment' => true],
        'chat_consultation_id' => ['type' => 'bigint', 'unsigned' => true, 'foreign' => 'chat_consultations.id', 'on_delete' => 'cascade'],
        'role' => ['type' => 'enum', 'values' => ['user', 'assistant'], 'nullable' => false],
        'content' => ['type' => 'text', 'nullable' => false],
        'context_data' => ['type' => 'json', 'nullable' => true],
        'created_at' => ['type' => 'timestamp', 'default' => 'CURRENT_TIMESTAMP'],
    ];

    /**
     * Get messages by chat consultation ID
     *
     * @param int $chatId Chat consultation ID
     * @param int $limit Limit number of messages
     * @return array
     */
    public function getByChatId(int $chatId, int $limit = 50): array
    {
        $stmt = $this->getDb()->prepare("
            SELECT * FROM chat_messages
            WHERE chat_consultation_id = :chat_consultation_id
            ORDER BY created_at ASC
            LIMIT :limit
        ");
        $stmt->bindValue('chat_consultation_id', $chatId, \PDO::PARAM_INT);
        $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get last N messages for chat preview
     */
    public function getLastMessages(int $chatId, int $limit = 3): array
    {
        $stmt = $this->getDb()->prepare("
            SELECT * FROM chat_messages
            WHERE chat_consultation_id = :chat_consultation_id
            ORDER BY created_at DESC
            LIMIT :limit
        ");
        $stmt->bindValue('chat_consultation_id', $chatId, \PDO::PARAM_INT);
        $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return array_reverse($stmt->fetchAll());
    }

    /**
     * Count messages in a chat session
     */
    public function countByChatId(int $chatId): int
    {
        $stmt = $this->getDb()->prepare("SELECT COUNT(*) as count FROM chat_messages WHERE chat_consultation_id = :chat_consultation_id");
        $stmt->execute(['chat_consultation_id' => $chatId]);
        $row = $stmt->fetch();
        return (int) ($row['count'] ?? 0);
    }
}
```

### 4. ChatController

**File:** `addon/Controllers/ChatController.php`

```php
<?php

namespace Addon\Controllers;

use Addon\Models\StudentProfileModel;
use Addon\Models\ChatConsultationModel;
use Addon\Models\ChatMessageModel;
use Addon\Services\GeminiService;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\View\View;
use App\Exceptions\AuthorizationException;

/**
 * Controller untuk chat consultation dengan AI
 */
class ChatController
{
    public function __construct(
        private StudentProfileModel $studentModel,
        private ChatConsultationModel $chatModel,
        private ChatMessageModel $messageModel,
        private GeminiService $geminiService
    ) {}

    /**
     * Halaman utama chat consultation
     */
    public function index(Request $request, Response $response): View | RedirectResponse
    {
        $userId = $this->session->get('auth.user_id');
        $currentRole = $this->session->get('auth.user_role');

        if ($currentRole !== 'user') {
            throw new AuthorizationException('Forbidden');
        }

        $userProfile = $this->profileModel->findByUserId($userId);
        $studentProfile = $this->studentModel->findByProfileId($userProfile['id']);

        if (!$studentProfile) {
            return $response->redirect('/profile?error=404&message=' . urlencode('Profil siswa tidak ditemukan'));
        }

        // Get active chat sessions
        $chatSessions = $this->chatModel->getByStudentId($studentProfile['id']);

        // Add message count to each session
        foreach ($chatSessions as &$session) {
            $session['message_count'] = $this->messageModel->countByChatId($session['id']);
            $session['last_messages'] = $this->messageModel->getLastMessages($session['id'], 2);
        }

        return $response->renderPage([
            'studentProfile' => $studentProfile,
            'chatSessions' => $chatSessions,
        ], ['meta' => ['title' => 'Konsultasi AI']]);
    }

    /**
     * API endpoint untuk mengirim pesan
     */
    public function sendMessage(Request $request, Response $response): JsonResponse
    {
        $userId = $this->session->get('auth.user_id');
        $userProfile = $this->profileModel->findByUserId($userId);
        $studentProfile = $this->studentModel->findByProfileId($userProfile['id']);

        if (!$studentProfile) {
            return $response->json(['success' => false, 'error' => 'Profil siswa tidak ditemukan'], 404);
        }

        $data = $request->input();
        $message = trim($data['message'] ?? '');

        if (empty($message)) {
            return $response->json(['success' => false, 'error' => 'Pesan tidak boleh kosong'], 400);
        }

        // Get or create chat session
        $chatId = $data['chat_id'] ?? null;

        if (!$chatId) {
            // Create new session
            $chatId = $this->chatModel->createWithSessionId($studentProfile['id'], 'potential_analysis');
        }

        // Verify chat belongs to student
        $chat = $this->chatModel->find($chatId);
        if (!$chat || $chat['student_profile_id'] != $studentProfile['id']) {
            return $response->json(['success' => false, 'error' => 'Chat session tidak valid'], 403);
        }

        // Save user message
        $this->messageModel->create([
            'chat_consultation_id' => $chatId,
            'role' => 'user',
            'content' => $message,
        ]);

        // Build context for AI
        $context = $this->buildStudentContext($studentProfile, $userProfile);

        // Get chat history (last 10 messages for context)
        $messages = $this->messageModel->getByChatId($chatId, 10);

        // Call Gemini API
        try {
            $aiResponse = $this->geminiService->chat($messages, $context);
        } catch (\Exception $e) {
            return $response->json([
                'success' => false,
                'error' => 'Gagal terhubung ke AI: ' . $e->getMessage()
            ], 500);
        }

        // Save AI response
        $this->messageModel->create([
            'chat_consultation_id' => $chatId,
            'role' => 'assistant',
            'content' => $aiResponse['content'],
            'context_data' => json_encode($context),
        ]);

        return $response->json([
            'success' => true,
            'message' => $aiResponse['content'],
            'chat_id' => $chatId,
        ]);
    }

    /**
     * Get chat history for a session
     */
    public function getHistory(Request $request, Response $response): JsonResponse
    {
        $userId = $this->session->get('auth.user_id');
        $userProfile = $this->profileModel->findByUserId($userId);
        $studentProfile = $this->studentModel->findByProfileId($userProfile['id']);

        $chatId = $request->param('id');

        // Verify chat belongs to student
        $chat = $this->chatModel->find($chatId);
        if (!$chat || $chat['student_profile_id'] != $studentProfile['id']) {
            return $response->json(['success' => false, 'error' => 'Chat session tidak ditemukan'], 404);
        }

        $messages = $this->messageModel->getByChatId($chatId);

        return $response->json([
            'success' => true,
            'messages' => $messages,
            'chat' => $chat,
        ]);
    }

    /**
     * Create new chat session
     */
    public function createSession(Request $request, Response $response): JsonResponse
    {
        $userId = $this->session->get('auth.user_id');
        $userProfile = $this->profileModel->findByUserId($userId);
        $studentProfile = $this->studentModel->findByProfileId($userProfile['id']);

        if (!$studentProfile) {
            return $response->json(['success' => false, 'error' => 'Profil siswa tidak ditemukan'], 404);
        }

        $data = $request->input();
        $topic = $data['topic'] ?? 'potential_analysis';

        $chatId = $this->chatModel->createWithSessionId($studentProfile['id'], $topic);
        $chat = $this->chatModel->find($chatId);

        return $response->json([
            'success' => true,
            'chat' => $chat,
        ]);
    }

    /**
     * Delete chat session
     */
    public function deleteSession(Request $request, Response $response): JsonResponse
    {
        $userId = $this->session->get('auth.user_id');
        $userProfile = $this->profileModel->findByUserId($userId);
        $studentProfile = $this->studentModel->findByProfileId($userProfile['id']);

        $chatId = $request->param('id');

        // Verify chat belongs to student
        $chat = $this->chatModel->find($chatId);
        if (!$chat || $chat['student_profile_id'] != $studentProfile['id']) {
            return $response->json(['success' => false, 'error' => 'Chat session tidak ditemukan'], 404);
        }

        $this->chatModel->deleteById($chatId);

        return $response->json(['success' => true]);
    }

    /**
     * Build student context for AI
     */
    private function buildStudentContext(array $studentProfile, array $userProfile): array
    {
        // Decode JSON fields
        $academicScores = json_decode($studentProfile['academic_scores'] ?? '{}', true) ?? [];
        $aiAnalysis = json_decode($studentProfile['ai_analysis'] ?? '{}', true) ?? [];
        $psychologicalTests = json_decode($studentProfile['psychological_tests'] ?? '[]', true) ?? [];

        return [
            'student' => [
                'name' => $userProfile['name'] ?? 'Siswa',
                'grade_level' => $studentProfile['grade_level'] ?? '',
                'major' => $studentProfile['major'] ?? '',
            ],
            'academic_scores' => $academicScores,
            'ai_analysis' => $aiAnalysis,
            'psychological_tests' => $psychologicalTests,
        ];
    }
}
```

---

## Routes Structure

**File:** `addon/Router/index.php`

```php
// Chat Consultation Routes (require login, role: user/siswa)
$router->group(['middleware' => ['auth', 'role:user']], function () use ($router) {
    // Main chat page
    $router->get('/chat', [ChatController::class, 'index']);

    // API endpoints
    $router->post('/chat/send', [ChatController::class, 'sendMessage']);
    $router->get('/chat/history/:id', [ChatController::class, 'getHistory']);
    $router->post('/chat/session/create', [ChatController::class, 'createSession']);
    $router->post('/chat/session/:id/delete', [ChatController::class, 'deleteSession']);
});
```

---

## View Structure

```
addon/Views/(app)/chat/
├── index.php          # Main chat page
├── index.css          # Chat styles
└── index.js           # Chat interactions
```

### UI Components

1. **Sidebar Panel**
   - New Chat button
   - List of previous chat sessions
   - Session preview (last message + timestamp)
   - Delete session button

2. **Main Chat Area**
   - Chat header (topic/session title)
   - Message container with scroll
   - Message bubbles (user vs assistant)
   - Typing indicator

3. **Input Area**
   - Auto-resize textarea
   - Send button
   - Quick action buttons (preset questions)

### Quick Action Buttons

```javascript
const quickActions = [
  {
    label: "📊 Jelaskan hasil tes saya",
    query: "Bisa jelaskan hasil tes psikologi saya?",
  },
  {
    label: "🎯 Apa potensi terbaik saya?",
    query: "Apa potensi terbaik yang saya miliki?",
  },
  {
    label: "🎓 Rekomendasi jurusan",
    query: "Jurusan apa yang cocok untuk saya?",
  },
  {
    label: "💡 Cara mengembangkan bakat",
    query: "Bagaimana cara mengembangkan bakat saya?",
  },
];
```

---

## Student Context Builder

Context yang akan dikirim ke Gemini AI:

```json
{
  "student": {
    "name": "John Doe",
    "grade_level": "11",
    "major": "IPA"
  },
  "academic_scores": {
    "math": 85,
    "indonesian": 90,
    "english": 88
  },
  "ai_analysis": {
    "potentials": ["Mathematical Reasoning", "Scientific Thinking"],
    "interests": ["Sports", "Music", "Technology"],
    "talents": ["Leadership", "Problem Solving"],
    "recommendations": ["Computer Science", "Engineering"]
  },
  "psychological_tests": [
    {
      "test_name": "Multiple Intelligence",
      "scores": { "logical": 80, "linguistic": 75, "spatial": 90 },
      "date": "2024-01-15"
    }
  ]
}
```

---

## Security Considerations

1. **Authorization**: Hanya siswa yang bisa akses chat consultation mereka sendiri
2. **Session Validation**: Setiap chat session divalidasi ownership-nya
3. **Rate Limiting**: Batasi jumlah request ke Gemini API (misal 10 pesan/menit)
4. **Input Sanitization**: Clean user input sebelum dikirim ke AI
5. **Data Privacy**: Jangan kirim data sensitif (email, phone, address) ke external API
6. **Error Handling**: Handle API errors dengan graceful fallback

---

## Implementation Steps

### Step 1: Database Migration

- [ ] Buat migration untuk tabel `chat_consultations`
- [ ] Buat migration untuk tabel `chat_messages`
- [ ] Run migration

### Step 2: Create Models

- [ ] Buat `ChatConsultationModel.php`
- [ ] Buat `ChatMessageModel.php`

### Step 3: Create Services

- [ ] Buat `GeminiService.php`
- [ ] Test koneksi ke Gemini API

### Step 4: Create Controller

- [ ] Buat `ChatController.php`
- [ ] Implement semua methods

### Step 5: Add Routes

- [ ] Tambahkan routes di `addon/Router/index.php`

### Step 6: Create Views

- [ ] Buat folder `addon/Views/(app)/chat/`
- [ ] Buat `index.php` dengan layout chat
- [ ] Buat `index.css` dengan styling
- [ ] Buat `index.js` untuk interactions

### Step 7: Update Navigation

- [ ] Update sidebar menu di `addon/Views/(app)/layout.php`

### Step 8: Testing

- [ ] Test chat functionality
- [ ] Test error handling
- [ ] Test authorization

---

## Future Enhancements

1. **Export Chat to PDF**: Siswa bisa export riwayat chat
2. **Teacher Access**: Guru BK bisa akses chat siswa untuk monitoring
3. **Chat Analytics**: Dashboard untuk admin melihat usage statistics
4. **Multi-language**: Support bahasa Indonesia dan Inggris
5. **Voice Input**: Input suara dengan speech-to-text
6. **File Upload**: Upload dokumen untuk dianalisis AI
