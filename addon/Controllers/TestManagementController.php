<?php

namespace Addon\Controllers;

use Addon\Models\TestConfigurationModel;
use Addon\Models\TestStatementModel;
use Addon\Models\SchoolConfigMappingModel;
use Addon\Models\SchoolModel;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\RedirectResponse;
use App\Core\View\View;

/**
 * Test Management Controller
 * 
 * Mengelola konfigurasi tes psikologi (RIASEC, IQ, Learning Style, dll)
 * untuk Super Admin.
 */
class TestManagementController
{
  public function __construct(
    private TestConfigurationModel $configModel,
    private TestStatementModel $statementModel,
    private SchoolConfigMappingModel $schoolConfigModel,
    private SchoolModel $schoolModel
  ) {}

  /**
   * Halaman index - List semua konfigurasi tes
   */
  public function index(Request $request, Response $response): View | RedirectResponse
  {
    try {
      $configs = $this->configModel->all();

      // Hitung jumlah statements dan schools untuk setiap config
      foreach ($configs as &$config) {
        $config['statement_count'] = $this->statementModel->countByConfigId($config['id']);
        $config['school_count'] = $this->schoolConfigModel->getSchoolCount($config['id']);
      }

      return $response->renderPage([
        'configs' => $configs
      ], ['meta' => ['title' => 'Kelola Konfigurasi Tes']]);
    } catch (\Exception $e) {
      return $response->redirect('/admin?error=500&message=' . urlencode($e->getMessage()));
    }
  }

  /**
   * Form tambah konfigurasi tes baru
   */
  public function create(Request $request, Response $response): View | RedirectResponse
  {
    try {
      return $response->renderPage([
        'testTypes' => ['riasec', 'iq', 'learning_style', 'personality']
      ], ['meta' => ['title' => 'Tambah Konfigurasi Tes Baru']]);
    } catch (\Exception $e) {
      return $response->redirect('/admin/tests?error=500&message=' . urlencode($e->getMessage()));
    }
  }

  /**
   * Simpan konfigurasi tes baru
   */
  public function store(Request $request, Response $response): RedirectResponse
  {
    try {
      $data = $request->input();

      // Validasi required fields
      $required = ['name', 'test_type', 'dimensions', 'scoring_rules'];
      foreach ($required as $field) {
        if (empty($data[$field])) {
          return $response->redirect('/admin/tests/create?error=400&message=' . urlencode('Field ' . $field . ' wajib diisi'));
        }
      }

      // Validasi test_type
      $validTypes = ['riasec', 'iq', 'learning_style', 'personality'];
      if (!in_array($data['test_type'], $validTypes)) {
        return $response->redirect('/admin/tests/create?error=400&message=' . urlencode('Tipe tes tidak valid'));
      }

      // Validasi JSON dimensions
      $dimensions = json_decode($data['dimensions'], true);
      if (json_last_error() !== JSON_ERROR_NONE || !is_array($dimensions) || empty($dimensions)) {
        return $response->redirect('/admin/tests/create?error=400&message=' . urlencode('Format JSON dimensions tidak valid'));
      }

      // Validasi JSON scoring_rules
      $scoringRules = json_decode($data['scoring_rules'], true);
      if (json_last_error() !== JSON_ERROR_NONE || !is_array($scoringRules)) {
        return $response->redirect('/admin/tests/create?error=400&message=' . urlencode('Format JSON scoring_rules tidak valid'));
      }

      // Validate scoring_rules structure - must be array of categories
      if (empty($scoringRules)) {
        return $response->redirect('/admin/tests/create?error=400&message=' . urlencode('scoring_rules tidak boleh kosong'));
      }

      // Validate each category has min, max, and label
      foreach ($scoringRules as $index => $rule) {
        if (!isset($rule['min']) || !isset($rule['max']) || !isset($rule['label'])) {
          return $response->redirect('/admin/tests/create?error=400&message=' . urlencode('scoring_rules kategori ke-' . ($index + 1) . ' harus memiliki min, max, dan label'));
        }
      }

      // Cek nama unik
      $existing = $this->configModel->findByName($data['name']);
      if ($existing) {
        return $response->redirect('/admin/tests/create?error=400&message=' . urlencode('Nama konfigurasi sudah digunakan'));
      }

      // Create configuration
      $configId = $this->configModel->create([
        'name' => $data['name'],
        'test_type' => $data['test_type'],
        'dimensions' => $data['dimensions'], // Model akan auto-encode JSON
        'scoring_rules' => $data['scoring_rules'], // Model akan auto-encode JSON
        'is_active' => isset($data['is_active']) ? (bool) $data['is_active'] : true
      ]);

      return $response->redirect('/admin/tests/' . $configId . '/statements?success=' . urlencode('Konfigurasi berhasil dibuat. Silakan tambah butir soal.'));
    } catch (\Exception $e) {
      return $response->redirect('/admin/tests/create?error=500&message=' . urlencode($e->getMessage()));
    }
  }

  /**
   * Form edit konfigurasi tes
   */
  public function edit(Request $request, Response $response): View | RedirectResponse
  {
    try {
      $id = $request->param('id');
      $config = $this->configModel->find($id);

      if (!$config) {
        return $response->redirect('/admin/tests?error=404&message=' . urlencode('Konfigurasi tidak ditemukan'));
      }

      // Decode JSON fields untuk ditampilkan di form
      $config['dimensions_json'] = json_encode($this->configModel->decodeJsonFields($config)['dimensions'] ?? []);
      $config['scoring_rules_json'] = json_encode($this->configModel->decodeJsonFields($config)['scoring_rules'] ?? []);

      return $response->renderPage([
        'config' => $config,
        'testTypes' => ['riasec', 'iq', 'learning_style', 'personality']
      ], ['meta' => ['title' => 'Edit Konfigurasi Tes']]);
    } catch (\Exception $e) {
      return $response->redirect('/admin/tests?error=500&message=' . urlencode($e->getMessage()));
    }
  }

  /**
   * Update konfigurasi tes
   */
  public function update(Request $request, Response $response): RedirectResponse
  {
    try {
      $id = $request->param('id');
      $config = $this->configModel->find($id);

      if (!$config) {
        return $response->redirect('/admin/tests?error=404&message=' . urlencode('Konfigurasi tidak ditemukan'));
      }

      $data = $request->input();

      // Validasi required fields
      $required = ['name', 'test_type', 'dimensions', 'scoring_rules'];
      foreach ($required as $field) {
        if (empty($data[$field])) {
          return $response->redirect('/admin/tests/' . $id . '/edit?error=400&message=' . urlencode('Field ' . $field . ' wajib diisi'));
        }
      }

      // Validasi test_type
      $validTypes = ['riasec', 'iq', 'learning_style', 'personality'];
      if (!in_array($data['test_type'], $validTypes)) {
        return $response->redirect('/admin/tests/' . $id . '/edit?error=400&message=' . urlencode('Tipe tes tidak valid'));
      }

      // Validasi JSON dimensions
      $dimensions = json_decode($data['dimensions'], true);
      if (json_last_error() !== JSON_ERROR_NONE || !is_array($dimensions) || empty($dimensions)) {
        return $response->redirect('/admin/tests/' . $id . '/edit?error=400&message=' . urlencode('Format JSON dimensions tidak valid'));
      }

      // Validasi JSON scoring_rules
      $scoringRules = json_decode($data['scoring_rules'], true);
      if (json_last_error() !== JSON_ERROR_NONE || !is_array($scoringRules)) {
        return $response->redirect('/admin/tests/' . $id . '/edit?error=400&message=' . urlencode('Format JSON scoring_rules tidak valid'));
      }

      // Cek nama unik (kecuali nama config ini sendiri)
      $existing = $this->configModel->findByName($data['name']);
      if ($existing && $existing['id'] != $id) {
        return $response->redirect('/admin/tests/' . $id . '/edit?error=400&message=' . urlencode('Nama konfigurasi sudah digunakan'));
      }

      // Update configuration
      $this->configModel->updateById($id, [
        'name' => $data['name'],
        'test_type' => $data['test_type'],
        'dimensions' => $data['dimensions'],
        'scoring_rules' => $data['scoring_rules'],
        'is_active' => isset($data['is_active']) ? (bool) $data['is_active'] : true
      ]);

      return $response->redirect('/admin/tests?success=' . urlencode('Konfigurasi berhasil diupdate'));
    } catch (\Exception $e) {
      return $response->redirect('/admin/tests/' . $request->param('id') . '/edit?error=500&message=' . urlencode($e->getMessage()));
    }
  }

  /**
   * Halaman detail konfigurasi dengan list statements
   */
  public function manageStatements(Request $request, Response $response): View | RedirectResponse
  {
    try {
      $id = $request->param('id');
      $config = $this->configModel->find($id);

      if (!$config) {
        return $response->redirect('/admin/tests?error=404&message=' . urlencode('Konfigurasi tidak ditemukan'));
      }

      $statements = $this->statementModel->getByConfigId($id);

      // Get dimensions from config
      $dimensions = $this->configModel->decodeJsonFields($config)['dimensions'] ?? [];

      return $response->renderPage([
        'config' => $config,
        'statements' => $statements,
        'dimensions' => $dimensions
      ], ['meta' => ['title' => 'Kelola Butir Soal - ' . $config['name']]]);
    } catch (\Exception $e) {
      return $response->redirect('/admin/tests?error=500&message=' . urlencode($e->getMessage()));
    }
  }

  /**
   * Tambah butir soal baru
   */
  public function addStatement(Request $request, Response $response): RedirectResponse
  {
    try {
      $configId = $request->param('id');
      $config = $this->configModel->find($configId);

      if (!$config) {
        return $response->redirect('/admin/tests?error=404&message=' . urlencode('Konfigurasi tidak ditemukan'));
      }

      $data = $request->input();

      // Validasi required fields
      $required = ['dimension', 'statement_text', 'display_order'];
      foreach ($required as $field) {
        if (empty($data[$field])) {
          return $response->redirect('/admin/tests/' . $configId . '/statements?error=400&message=' . urlencode('Field ' . $field . ' wajib diisi'));
        }
      }

      // Validasi dimension exists in config
      $dimensions = $this->configModel->decodeJsonFields($config)['dimensions'] ?? [];
      if (!array_key_exists($data['dimension'], $dimensions)) {
        return $response->redirect('/admin/tests/' . $configId . '/statements?error=400&message=' . urlencode('Dimensi tidak valid untuk konfigurasi ini'));
      }

      // Create statement
      $this->statementModel->create([
        'config_id' => $configId,
        'dimension' => $data['dimension'],
        'statement_text' => $data['statement_text'],
        'display_order' => (int) $data['display_order'],
        'is_active' => true
      ]);

      return $response->redirect('/admin/tests/' . $configId . '/statements?success=' . urlencode('Butir soal berhasil ditambahkan'));
    } catch (\Exception $e) {
      return $response->redirect('/admin/tests/' . $request->param('id') . '/statements?error=500&message=' . urlencode($e->getMessage()));
    }
  }

  /**
   * Delete butir soal
   */
  public function deleteStatement(Request $request, Response $response): RedirectResponse
  {
    try {
      $configId = $request->param('id');
      $statementId = $request->param('statement_id');

      $config = $this->configModel->find($configId);
      if (!$config) {
        return $response->redirect('/admin/tests?error=404&message=' . urlencode('Konfigurasi tidak ditemukan'));
      }

      $statement = $this->statementModel->find($statementId);
      if (!$statement) {
        return $response->redirect('/admin/tests/' . $configId . '/statements?error=404&message=' . urlencode('Butir soal tidak ditemukan'));
      }

      $this->statementModel->deleteById($statementId);

      return $response->redirect('/admin/tests/' . $configId . '/statements?success=' . urlencode('Butir soal berhasil dihapus'));
    } catch (\Exception $e) {
      return $response->redirect('/admin/tests/' . $request->param('id') . '/statements?error=500&message=' . urlencode($e->getMessage()));
    }
  }

  /**
   * Form assign konfigurasi ke sekolah
   */
  public function assignToSchools(Request $request, Response $response): View | RedirectResponse
  {
    try {
      $id = $request->param('id');
      $config = $this->configModel->find($id);

      if (!$config) {
        return $response->redirect('/admin/tests?error=404&message=' . urlencode('Konfigurasi tidak ditemukan'));
      }

      // Get all schools for search panel
      $allSchools = $this->schoolModel->all();

      // Get assigned schools - just extract IDs and default
      $assignedMappings = $this->schoolConfigModel->getByConfigId($id);
      $assignedSchoolIds = array_column($assignedMappings, 'school_id');

      // Find default school ID
      $defaultSchoolId = null;
      foreach ($assignedMappings as $mapping) {
        if ($mapping['is_default']) {
          $defaultSchoolId = $mapping['school_id'];
          break;
        }
      }

      $props = [
        'config' => $config,
        'allSchools' => $allSchools,
        'assignedSchoolIds' => $assignedSchoolIds,  // Simplified: just IDs
        'defaultSchoolId' => $defaultSchoolId
      ];

      return $response->renderPage($props, ['meta' => ['title' => 'Assign Konfigurasi ke Sekolah']]);
    } catch (\Exception $e) {
      return $response->redirect('/admin/tests?error=500&message=' . urlencode($e->getMessage()));
    }
  }

  /**
   * Simpan assignment konfigurasi ke sekolah
   */
  public function saveAssignment(Request $request, Response $response): RedirectResponse
  {
    try {
      $configId = $request->param('id');
      $config = $this->configModel->find($configId);

      if (!$config) {
        return $response->redirect('/admin/tests?error=404&message=' . urlencode('Konfigurasi tidak ditemukan'));
      }

      $data = $request->input();
      $schoolIds = $data['schools'] ?? [];

      // Get currently assigned schools
      $currentlyAssigned = $this->schoolConfigModel->getByConfigId($configId);
      $currentSchoolIds = array_column($currentlyAssigned, 'school_id');

      // Find schools to add and remove
      $toAdd = array_diff($schoolIds, $currentSchoolIds);
      $toRemove = array_diff($currentSchoolIds, $schoolIds);

      // Remove assignments
      foreach ($toRemove as $schoolId) {
        $this->schoolConfigModel->removeConfig($schoolId, $configId);
      }

      // Add new assignments
      // Handle default_school - ensure it's a valid integer or null
      $defaultSchool = isset($data['default_school']) && $data['default_school'] !== ''
        ? (int) $data['default_school']
        : null;

      foreach ($toAdd as $schoolId) {
        // Explicitly cast to boolean to ensure MySQL gets 0 or 1
        $isDefault = ($defaultSchool !== null && (int) $schoolId === $defaultSchool);
        $this->schoolConfigModel->assignConfig((int) $schoolId, $configId, (bool) $isDefault);
      }

      // If default school changed, update it
      if ($defaultSchool !== null && in_array($defaultSchool, $schoolIds)) {
        $this->schoolConfigModel->setAsDefault($defaultSchool, $configId);
      }

      return $response->redirect('/admin/tests?success=' . urlencode('Assignment konfigurasi berhasil disimpan'));
    } catch (\Exception $e) {
      return $response->redirect('/admin/tests/' . $request->param('id') . '/assign?error=500&message=' . urlencode($e->getMessage()));
    }
  }

  /**
   * Delete konfigurasi dan semua statements terkait
   */
  public function delete(Request $request, Response $response): RedirectResponse
  {
    try {
      $id = $request->param('id');
      $config = $this->configModel->find($id);

      if (!$config) {
        return $response->redirect('/admin/tests?error=404&message=' . urlencode('Konfigurasi tidak ditemukan'));
      }

      // Delete all statements for this config first
      $this->statementModel->deleteByConfigId($id);

      // Delete all school mappings
      $mappings = $this->schoolConfigModel->getByConfigId($id);
      foreach ($mappings as $mapping) {
        $this->schoolConfigModel->removeConfig($mapping['school_id'], $id);
      }

      // Delete the configuration
      $this->configModel->deleteById($id);

      return $response->redirect('/admin/tests?success=' . urlencode('Konfigurasi dan semua butir soal terkait berhasil dihapus'));
    } catch (\Exception $e) {
      return $response->redirect('/admin/tests?error=500&message=' . urlencode($e->getMessage()));
    }
  }

  /**
   * Toggle active status konfigurasi
   */
  public function toggleActive(Request $request, Response $response): RedirectResponse
  {
    try {
      $id = $request->param('id');
      $config = $this->configModel->find($id);

      if (!$config) {
        return $response->redirect('/admin/tests?error=404&message=' . urlencode('Konfigurasi tidak ditemukan'));
      }

      // Toggle: convert boolean to integer (0/1) untuk MySQL
      $newStatus = $config['is_active'] ? 0 : 1;
      $this->configModel->updateById($id, ['is_active' => $newStatus]);

      $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
      return $response->redirect('/admin/tests?success=' . urlencode('Konfigurasi berhasil ' . $statusText));
    } catch (\Exception $e) {
      return $response->redirect('/admin/tests?error=500&message=' . urlencode($e->getMessage()));
    }
  }
}
