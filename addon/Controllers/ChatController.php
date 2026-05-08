<?php

namespace Addon\Controllers;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\View\View;
use App\Core\Http\RedirectResponse;
use App\Core\Http\JsonResponse;
use App\Exceptions\AuthorizationException;
use App\Services\SessionService;
use Addon\Models\ChatConsultationModel;
use Addon\Models\ChatMessageModel;
use Addon\Models\StudentProfileModel;
use Addon\Services\GeminiService;

/**
 * ChatController - Controller untuk fitur chat consultation dengan AI
 *
 * Menangani permintaan chat siswa untuk konsultasi potensi, minat, dan bakat
 */
class ChatController
{
  /**
   * Constructor dengan dependency injection
   */
  public function __construct(
    private ChatConsultationModel $chatConsultationModel,
    private ChatMessageModel $chatMessageModel,
    private StudentProfileModel $studentProfileModel,
    private GeminiService $geminiService,
    private SessionService $session
  ) {}

  /**
   * Tampilkan daftar riwayat chat siswa
   */
  public function index(Request $request, Response $response): View | RedirectResponse | JsonResponse
  {
    try {
      $studentProfileId = $this->getStudentProfileId();

      if (!$studentProfileId) {
        return $response->redirect('/profile?error=403&message=' . urlencode('Anda belum memiliki profil siswa'));
      }

      $chatHistory = $this->chatConsultationModel->getByStudentId($studentProfileId);

      $data = [
        'chatHistory' => $chatHistory,
        'totalChats' => count($chatHistory),
      ];

      return $response->renderPage($data, [
        'path' => '(app)/profile/chat/index',
        'meta' => ['title' => 'Riwayat Chat Konsultasi']
      ]);
    } catch (\Exception $e) {
      dd('Error fetching chat history: ', $e->getMessage());
      return $response->redirect('/dashboard?error=500&message=' . urlencode($e->getMessage()));
    }
  }

  /**
   * Tampilkan halaman chat detail dengan session_id
   */
  public function show(Request $request, Response $response): View | RedirectResponse | JsonResponse
  {
    try {
      $studentProfileId = $this->getStudentProfileId();
      $sessionId = $request->param('session_id');

      if (!$sessionId) {
        return $response->redirect('/profile/chat?error=400&message=' . urlencode('Session ID tidak valid'));
      }

      $chatConsultation = $this->chatConsultationModel->findBySessionId($sessionId);

      if (!$chatConsultation) {
        return $response->redirect('/profile/chat?error=404&message=' . urlencode('Sesi chat tidak ditemukan'));
      }

      // Validasi ownership
      if ((int) $chatConsultation['student_profile_id'] !== $studentProfileId) {
        throw new AuthorizationException('Anda tidak memiliki akses ke sesi chat ini.');
      }

      $messages = $this->chatMessageModel->getByChatId((int) $chatConsultation['id']);

      $data = [
        'chat' => $chatConsultation,
        'messages' => $messages,
        'sessionId' => $sessionId,
      ];

      return $response->renderPage($data, [
        'meta' => ['title' => 'Chat Konsultasi']
      ]);
    } catch (\Exception $e) {
      return $response->redirect('/profile/chat?error=500&message=' . urlencode($e->getMessage()));
    }
  }

  /**
   * Buat sesi chat baru
   */
  public function create(Request $request, Response $response): View | RedirectResponse | JsonResponse
  {
    try {
      $studentProfileId = $this->getStudentProfileId();

      if (!$studentProfileId) {
        return $response->redirect('/profile?error=403&message=' . urlencode('Anda belum memiliki profil siswa'));
      }

      // Get student data for context
      $studentProfile = $this->studentProfileModel->findByProfileId($studentProfileId);

      $data = [
        'studentProfile' => $studentProfile,
      ];

      return $response->renderPage($data, [
        'path' => '(app)/profile/chat/create',
        'meta' => ['title' => 'Chat Konsultasi Baru']
      ]);
    } catch (\Exception $e) {
      return $response->redirect('/profile/chat?error=500&message=' . urlencode($e->getMessage()));
    }
  }

  /**
   * Store sesi chat baru dan mulai konsultasi
   */
  public function store(Request $request, Response $response): View | RedirectResponse | JsonResponse
  {
    try {
      $user_id = $this->session->get('auth.user_id');
      $studentProfile = $this->studentProfileModel->findByUserId($user_id);
      $studentProfileId = $studentProfile ? (int) $studentProfile['id'] : null;

      if (!$studentProfileId) {
        return $response->json(['error' => 'Anda belum memiliki profil siswa'], 403);
      }

      $topic = $request->post('topic') ?? 'potential_analysis';
      $initialMessage = $request->post('message') ?? 'Halo, saya ingin konsultasi tentang potensi saya.';

      // Generate unique session ID
      $sessionId = 'chat_' . bin2hex(random_bytes(16));

      // Create chat consultation session
      $chatId = $this->chatConsultationModel->createWithSessionId([
        'student_profile_id' => $studentProfileId,
        'session_id' => $sessionId,
        'topic' => $topic,
      ]);

      // Save initial user message
      $this->chatMessageModel->addUserMessage($chatId, $initialMessage);

      // Get student data for context
      $contextData = $this->buildContextData($studentProfile);

      // Send to Gemini AI
      $aiResponse = $this->geminiService->chat([
        ['role' => 'user', 'content' => $initialMessage]
      ], $contextData);

      // Save AI response
      $this->chatMessageModel->addAssistantMessage($chatId, $aiResponse['content'], $aiResponse['context_data']);

      return $response->json([
        'success' => true,
        'session_id' => $sessionId,
        'redirect' => '/profile/chat/' . $sessionId,
      ]);
    } catch (\Exception $e) {
      return $response->json(['error' => $e->getMessage()], 500);
    }
  }

  /**
   * Kirim pesan ke chat session
   */
  public function sendMessage(Request $request, Response $response): View | RedirectResponse | JsonResponse
  {
    try {
      $sessionId = $request->post('session_id');
      $message = $request->post('message');

      if (!$sessionId || !$message) {
        return $response->json(['error' => 'Session ID dan message wajib diisi'], 400);
      }

      $chatConsultation = $this->chatConsultationModel->findBySessionId($sessionId);

      if (!$chatConsultation) {
        return $response->json(['error' => 'Sesi chat tidak ditemukan'], 404);
      }

      // Validasi ownership
      $user_id = $this->session->get('auth.user_id');
      $studentProfile = $this->studentProfileModel->findByUserId($user_id);
      $studentProfileId = $studentProfile ? (int) $studentProfile['id'] : null;
      if ((int) $chatConsultation['student_profile_id'] !== $studentProfileId) {
        return $response->json(['error' => 'Akses ditolak'], 403);
      }

      // Save user message
      $this->chatMessageModel->addUserMessage((int) $chatConsultation['id'], $message);

      // Get conversation history (last 10 messages)
      $messages = $this->chatMessageModel->getLastMessages((int) $chatConsultation['id'], 10);

      $contextData = $this->buildContextData($studentProfile);

      // Send to Gemini AI
      $aiResponse = $this->geminiService->chat($messages, $contextData);

      // Save AI response
      $messageId = $this->chatMessageModel->addAssistantMessage(
        (int) $chatConsultation['id'],
        $aiResponse['content'],
        $aiResponse['context_data']
      );

      return $response->json([
        'success' => true,
        'message_id' => $messageId,
        'content' => $aiResponse['content'],
      ]);
    } catch (\Exception $e) {
      return $response->json(['error' => $e->getMessage()], 500);
    }
  }

  /**
   * Hapus sesi chat
   */
  public function delete(Request $request, Response $response): View | RedirectResponse | JsonResponse
  {
    try {
      $studentProfileId = $this->getStudentProfileId();
      $sessionId = $request->post('session_id');

      if (!$sessionId) {
        return $response->json(['error' => 'Session ID tidak valid'], 400);
      }

      $chatConsultation = $this->chatConsultationModel->findBySessionId($sessionId);

      if (!$chatConsultation) {
        return $response->json(['error' => 'Sesi chat tidak ditemukan'], 404);
      }

      // Validasi ownership
      if ((int) $chatConsultation['student_profile_id'] !== $studentProfileId) {
        return $response->json(['error' => 'Akses ditolak'], 403);
      }

      // Delete messages first (cascade will handle this, but explicit is better)
      $this->chatMessageModel->deleteByChatId((int) $chatConsultation['id']);

      // Delete consultation
      $this->chatConsultationModel->deleteBySessionId($sessionId);

      return $response->json([
        'success' => true,
        'redirect' => '/profile/chat',
      ]);
    } catch (\Exception $e) {
      return $response->json(['error' => $e->getMessage()], 500);
    }
  }

  /**
   * Get student profile ID from session
   */
  private function getStudentProfileId(): ?int
  {
    $user_id = $this->session->get('auth.user_id');

    $studentProfile = $this->studentProfileModel->findByUserId($user_id);

    return $studentProfile ? (int) $studentProfile['id'] : null;
  }

  /**
   * Build context data dari profil siswa untuk AI
   */
  private function buildContextData(?array $studentProfile): array
  {
    if (empty($studentProfile)) {
      return [];
    }

    $contextData = [
      'student_name' => $studentProfile['user_name'] ?? 'Siswa',
    ];

    // Add test results if available
    $aiAnalysis = $studentProfile['ai_analysis'] ?? null;
    if ($aiAnalysis && is_string($aiAnalysis)) {
      $aiAnalysis = json_decode($aiAnalysis, true);
    }

    if (!empty($aiAnalysis)) {
      $contextData['test_results'] = [
        'iq_score' => $aiAnalysis['iq_score'] ?? null,
        'multiple_intelligences' => $aiAnalysis['multiple_intelligences'] ?? null,
        'learning_style' => $aiAnalysis['learning_style'] ?? null,
        'personality_type' => $aiAnalysis['personality_type'] ?? null,
      ];

      $contextData['interests'] = $aiAnalysis['interests'] ?? null;
      $contextData['academic_performance'] = $aiAnalysis['academic_performance'] ?? null;
    }

    return $contextData;
  }
}
