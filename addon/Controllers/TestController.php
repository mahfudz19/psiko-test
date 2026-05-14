<?php

namespace Addon\Controllers;

use Addon\Models\TestConfigurationModel;
use Addon\Models\TestSessionModel;
use Addon\Models\TestStatementModel;
use Addon\Models\TestResponseModel;
use Addon\Models\TestResultModel;
use Addon\Models\StudentProfileModel;
use Addon\Models\ProfileModel;
use Addon\Models\SchoolConfigMappingModel;
use App\Core\Database\DatabaseManager;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\RedirectResponse;
use App\Core\View\View;

class TestController
{
  /**
   * Cooldown period dalam detik (30 hari)
   * User harus menunggu 30 hari sebelum bisa mengulang tes RIASEC
   */
  private const COOLDOWN_PERIOD = 2592000; // 30 * 24 * 60 * 60

  public function __construct(
    private DatabaseManager $db,
    private TestConfigurationModel $configModel,
    private TestSessionModel $sessionModel,
    private TestStatementModel $statementModel,
    private TestResponseModel $responseModel,
    private TestResultModel $resultModel,
    private StudentProfileModel $studentModel,
    private ProfileModel $profileModel,
    private SchoolConfigMappingModel $schoolConfigModel
  ) {}

  /**
   * Dashboard tes - redirect ke /profile/results
   */
  public function index(Request $request, Response $response): RedirectResponse
  {
    return $response->redirect('/profile/results');
  }

  /**
   * Halaman info tes RIASEC
   */
  public function riasecIndex(Request $request, Response $response): View | RedirectResponse
  {
    $userId = $_SESSION['auth.user_id'] ?? 0;
    $studentProfile = $this->studentModel->findByUserId($userId);

    if (!$studentProfile) {
      return $response->redirect('/profile?error=' . urlencode('Profil siswa tidak ditemukan.'));
    }

    // Cek apakah siswa memiliki school_id
    if (empty($studentProfile['school_id'])) {
      return $response->redirect('/profile?error=' . urlencode('Siswa tidak memiliki sekolah. Silakan hubungi administrator.'));
    }

    // Cek apakah sekolah user sudah memiliki konfigurasi RIASEC
    $schoolConfig = $this->schoolConfigModel->getDefaultConfig($studentProfile['school_id'], 'riasec');

    if (!$schoolConfig) {
      // Sekolah belum memiliki konfigurasi RIASEC
      return $response->renderPage(
        [
          'noConfig' => true,
          'studentProfile' => $studentProfile
        ],
        ['meta' => ['title' => 'Tes RIASEC | ' . env('APP_NAME')]]
      );
    }

    // Cek apakah sudah pernah mengerjakan tes RIASEC
    $latestResult = $this->resultModel->getLatestRiasecResult($studentProfile['student_profile_id']);

    // Hitung cooldown period jika sudah pernah tes
    $canRetake = true;
    $remainingDays = 0;
    $lastTestDate = null;

    if ($latestResult) {
      $lastTestDate = strtotime($latestResult['calculated_at']);
      $timeSinceLastTest = time() - $lastTestDate;

      if ($timeSinceLastTest < self::COOLDOWN_PERIOD) {
        $canRetake = false;
        $remainingDays = ceil((self::COOLDOWN_PERIOD - $timeSinceLastTest) / (24 * 60 * 60));
      }
    }

    // Hitung jumlah pernyataan RIASEC dari konfigurasi sekolah
    $statementCount = $this->statementModel->countByConfigId($schoolConfig['config_id']);

    return $response->renderPage(
      [
        'latestResult' => $latestResult,
        'config' => $schoolConfig,
        'statementCount' => $statementCount,
        'studentProfile' => $studentProfile,
        'canRetake' => $canRetake,
        'remainingDays' => $remainingDays,
        'lastTestDate' => $lastTestDate
      ],
      ['meta' => ['title' => 'Tes RIASEC | ' . env('APP_NAME')]]
    );
  }

  /**
   * Mulai tes baru - buat session
   */
  public function startTest(Request $request, Response $response): RedirectResponse
  {
    $userId = $_SESSION['auth.user_id'] ?? 0;
    $studentProfile = $this->studentModel->findByUserId($userId);

    if (!$studentProfile) {
      return $response->redirect('/profile?error=' . urlencode('Profil siswa tidak ditemukan.'));
    }

    // Cek apakah siswa memiliki school_id
    if (empty($studentProfile['school_id'])) {
      return $response->redirect('/profile?error=' . urlencode('Siswa tidak memiliki sekolah. Silakan hubungi administrator.'));
    }

    // Cek apakah sekolah user sudah memiliki konfigurasi RIASEC
    $schoolConfig = $this->schoolConfigModel->getDefaultConfig($studentProfile['school_id'], 'riasec');

    if (!$schoolConfig) {
      return $response->redirect('/tests/riasec?error=' . urlencode('Sekolah Anda belum memiliki konfigurasi tes RIASEC. Silakan hubungi guru BK.'));
    }

    // Cek cooldown period jika sudah pernah tes
    $latestResult = $this->resultModel->getLatestRiasecResult($studentProfile['student_profile_id']);

    if ($latestResult) {
      $lastTestDate = strtotime($latestResult['calculated_at']);
      $timeSinceLastTest = time() - $lastTestDate;

      if ($timeSinceLastTest < self::COOLDOWN_PERIOD) {
        $remainingDays = ceil((self::COOLDOWN_PERIOD - $timeSinceLastTest) / (24 * 60 * 60));
        return $response->redirect('/tests/riasec?error=' . urlencode(
          'Anda harus menunggu ' . $remainingDays . ' hari lagi sebelum dapat mengulang tes.'
        ));
      }
    }

    // Cek apakah ada session aktif
    $activeSession = $this->sessionModel->getActiveSession($studentProfile['student_profile_id'], 'riasec');

    if ($activeSession) {
      // Lanjutkan session yang sudah ada
      return $response->redirect('/tests/riasec/take?session=' . $activeSession['id']);
    }

    // Buat session baru dengan konfigurasi sekolah
    $sessionId = $this->sessionModel->createSession($studentProfile['student_profile_id'], $schoolConfig['config_id']);

    return $response->redirect('/tests/riasec/take?session=' . $sessionId);
  }

  /**
   * Interface pengerjaan tes
   */
  public function takeTest(Request $request, Response $response): View | RedirectResponse
  {
    $sessionId = $request->query['session'] ?? null;

    if (!$sessionId) {
      return $response->redirect('/tests/riasec');
    }

    $session = $this->sessionModel->getSessionWithDetails($sessionId);

    if (!$session) {
      return $response->redirect('/tests/riasec?error=' . urlencode('Session tes tidak ditemukan.'));
    }

    // Cek apakah session sudah selesai
    if ($session['status'] === 'completed') {
      return $response->redirect('/tests/riasec/results?session=' . $sessionId);
    }

    // Cek apakah session sudah expired (timeout 30 menit)
    $startedAt = strtotime($session['started_at']);
    $now = time();
    $timeout = 30 * 60; // 30 menit
    // dd($now - $startedAt > $timeout, [
    //   'now' => $now,
    //   'startedAt' => $startedAt,
    //   'timeout' => $timeout,
    //   '$now - $startedAt' => $now - $startedAt,
    //   "sessionId" => $sessionId
    // ]);

    if ($now - $startedAt > $timeout) {
      // Auto submit
      $autoSubmit = $this->sessionModel->updateById($sessionId, ['status' => 'expired']);
      if (!$autoSubmit) {
        return $response->redirect('/tests/riasec?error=400&message=' . urlencode('Gagal menyimpan hasil tes. Silakan coba lagi.'));
      }
      return $response->redirect('/tests/riasec/results?session=' . $sessionId . '&warning=' . urlencode('Waktu tes telah habis. Jawaban Anda telah disimpan.'));
    }

    // Ambil pernyataan
    $statements = $this->statementModel->getByConfigId($session['config_id']);

    // Ambil jawaban yang sudah diisi (jika ada)
    $existingResponses = $this->responseModel->getResponsesBySession($sessionId);
    $answers = [];
    foreach ($existingResponses as $resp) {
      $answers[$resp['statement_id']] = $resp['answer_value'];
    }

    // Hitung progress
    $answeredCount = count($answers);
    $totalCount = count($statements);
    $progress = $totalCount > 0 ? ($answeredCount / $totalCount) * 100 : 0;

    return $response->renderPage([
      'session' => $session,
      'statements' => $statements,
      'answers' => $answers,
      'progress' => $progress,
      'answeredCount' => $answeredCount,
      'totalCount' => $totalCount
    ], ['path' => '/tests/riasec/take', 'meta' => ['title' => 'Kerja Tes RIASEC | ' . env('APP_NAME')]]);
  }

  /**
   * Submit jawaban dan hitung skor
   *
   * Menerapkan bulk insert untuk efisiensi penyimpanan jawaban
   */
  public function submitTest(Request $request, Response $response): RedirectResponse
  {
    $sessionId = $request->post('session_id');

    if (!$sessionId) {
      return $response->redirect('/tests/riasec?error=' . urlencode('Session tes tidak ditemukan.'));
    }

    $session = $this->sessionModel->find($sessionId);

    if (!$session) {
      return $response->redirect('/tests/riasec?error=' . urlencode('Session tes tidak ditemukan.'));
    }

    // Cek apakah session sudah completed
    if ($session['status'] === 'completed') {
      return $response->redirect('/tests/riasec/results?session=' . $sessionId . '&warning=' . urlencode('Tes ini sudah selesai dikerjakan sebelumnya.'));
    }

    // Cek apakah session expired
    if ($session['status'] === 'expired') {
      return $response->redirect('/tests/riasec/results?session=' . $sessionId . '&warning=' . urlencode('Session tes telah expired.'));
    }

    // Ambil semua jawaban dari form
    $answers = $request->post('answers') ?? [];

    // Validasi: pastikan ada jawaban
    if (empty($answers)) {
      return $response->redirect('/tests/riasec/take?session=' . $sessionId . '&error=' . urlencode('Anda belum menjawab pertanyaan apapun.'));
    }

    // Ambil semua pernyataan untuk validasi
    $statements = $this->statementModel->getByConfigId($session['config_id']);
    $statementIds = array_column($statements, 'id');
    $totalStatements = count($statements);

    // Validasi: pastikan semua pertanyaan dijawab
    if (count($answers) < $totalStatements) {
      $remaining = $totalStatements - count($answers);
      return $response->redirect('/tests/riasec/take?session=' . $sessionId . '&error=' . urlencode('Anda belum menjawab ' . $remaining . ' pertanyaan. Pastikan semua terjawab sebelum mengirim!'));
    }

    // Validasi dan prepare data untuk bulk insert
    $responses = [];
    foreach ($answers as $statementId => $answerValue) {
      // Validasi statement_id exists dalam konfigurasi
      if (!in_array((int) $statementId, $statementIds)) {
        return $response->redirect('/tests/riasec?error=' . urlencode('Data jawaban tidak valid.'));
      }

      // Validasi answer_value harus 1-4 (Likert scale)
      $answerValue = (int) $answerValue;
      if ($answerValue < 1 || $answerValue > 4) {
        return $response->redirect('/tests/riasec?error=' . urlencode('Nilai jawaban tidak valid. Gunakan skala 1-4.'));
      }

      $responses[] = [
        'session_id' => (int) $sessionId,
        'statement_id' => (int) $statementId,
        'answer_value' => $answerValue
      ];
    }

    // Bulk insert semua jawaban sekaligus
    if (!empty($responses)) {
      $inserted = $this->responseModel->saveMany($responses);

      if (!$inserted) {
        return $response->redirect('/tests/riasec?error=' . urlencode('Gagal menyimpan jawaban. Silakan coba lagi.'));
      }
    }

    // Tandai session sebagai completed
    $completed = $this->sessionModel->completeSession($sessionId);

    if (!$completed) {
      return $response->redirect('/tests/riasec?error=' . urlencode('Gagal menyimpan status sesi. Silakan hubungi administrator.'));
    }

    // Ambil konfigurasi untuk scoring
    $config = $this->configModel->find($session['config_id']);

    if (!$config) {
      return $response->redirect('/tests/riasec?error=' . urlencode('Konfigurasi tes tidak ditemukan.'));
    }

    $scoringRules = json_decode($config['scoring_rules'], true);

    // Hitung skor
    $scoreResult = $this->resultModel->calculateScores($sessionId, $scoringRules);

    // Simpan hasil
    $resultData = [
      'session_id' => $sessionId,
      'test_type' => $config['test_type'],
      'scores' => json_encode($scoreResult['scores']),
      'categories' => json_encode($scoreResult['categories']),
      'holland_code' => $scoreResult['holland_code'],
      'holland_description' => $this->getHollandDescription($scoreResult['holland_code'])
    ];

    $resultId = $this->resultModel->saveResult($sessionId, $resultData);

    if (!$resultId) {
      return $response->redirect('/tests/riasec?error=' . urlencode('Gagal menyimpan hasil tes. Silakan coba lagi.'));
    }

    return $response->redirect('/tests/riasec/results?session=' . $sessionId);
  }

  /**
   * Tampilkan hasil tes
   */
  public function viewResults(Request $request, Response $response): View | RedirectResponse
  {
    $sessionId = $request->query['session'] ?? null;

    if (!$sessionId) {
      // Jika tidak ada session parameter, ambil hasil terbaru
      $userId = $_SESSION['auth.user_id'] ?? 0;
      $studentProfile = $this->studentModel->findByUserId($userId);
      if ($studentProfile) {
        $latestResult = $this->resultModel->getLatestRiasecResult($studentProfile['student_profile_id']);
        if ($latestResult) {
          $sessionId = $latestResult['session_id'];
        }
      }
    }

    if (!$sessionId) {
      return $response->redirect('/tests/riasec?error=' . urlencode('Anda belum pernah mengerjakan tes RIASEC.'));
    }

    $result = $this->resultModel->getResultWithDetails($sessionId);

    if (!$result) {
      return $response->redirect('/tests/riasec?error=' . urlencode('Hasil tes tidak ditemukan.'));
    }

    return $response->renderPage([
      'result' => $result,
      'session' => $result
    ], ['path' => '/tests/riasec/results', 'meta' => ['title' => 'Hasil Tes RIASEC | ' . env('APP_NAME')]]);
  }

  /**
   * Placeholder untuk IQ Test
   */
  public function iqIndex(Request $request, Response $response): View
  {
    return $response->renderPage(
      ['message' => 'IQ Test akan segera hadir.'],
      ['meta' => ['title' => 'IQ Test | ' . env('APP_NAME')]]
    );
  }

  /**
   * Dapatkan deskripsi Holland Code
   */
  private function getHollandDescription(string $hollandCode): string
  {
    $descriptions = [
      'R' => 'Realistic - Orang yang praktis, suka bekerja dengan tangan, alat, mesin, atau hewan.',
      'I' => 'Investigative - Orang yang analitis, intelektual, suka memecahkan masalah kompleks.',
      'A' => 'Artistic - Orang yang kreatif, ekspresif, suka seni dan kegiatan kreatif.',
      'S' => 'Social - Orang yang suka membantu, mengajar, dan bekerja dengan orang lain.',
      'E' => 'Enterprising - Orang yang ambisius, suka memimpin, dan mempengaruhi orang lain.',
      'C' => 'Conventional - Orang yang terorganisir, detail-oriented, suka bekerja dengan data.'
    ];

    $desc = [];
    for ($i = 0; $i < strlen($hollandCode); $i++) {
      $letter = $hollandCode[$i];
      if (isset($descriptions[$letter])) {
        $desc[] = $descriptions[$letter];
      }
    }

    return implode(' ', $desc);
  }
}
