<?php

namespace Addon\Models;

use App\Core\Database\Model;

/**
 * Test Configuration Model - Template Konfigurasi Tes Global
 *
 * Model ini mengelola template konfigurasi tes psikologi yang reusable.
 * Setiap sekolah dapat menggunakan konfigurasi yang sama tanpa duplikasi data.
 *
 * Fields:
 * - id: Primary key
 * - name: Nama konfigurasi (misal: "RIASEC Standar 42 Butir")
 * - test_type: Tipe tes (riasec, iq, learning_style, personality)
 * - dimensions: JSON konfigurasi dimensi
 * - scoring_rules: JSON aturan skoring khusus
 * - is_active: Status konfigurasi
 */
class TestConfigurationModel extends Model
{
    protected ?string $connection = 'mysql';
    protected string $table = 'test_configurations';
    protected bool $timestamps = true;

    protected array $schema = [
        'id' => ['type' => 'id', 'primary' => true, 'auto_increment' => true],
        'name' => ['type' => 'string', 'nullable' => false],
        'test_type' => ['type' => 'enum', 'values' => ['riasec', 'iq', 'learning_style', 'personality'], 'nullable' => false],
        'dimensions' => ['type' => 'json', 'nullable' => false],
        'scoring_rules' => ['type' => 'json', 'nullable' => true],
        'is_active' => ['type' => 'boolean', 'nullable' => false, 'default' => true]
    ];

    /**
     * Seed data untuk konfigurasi default RIASEC
     * Data JSON disimpan sebagai string JSON-encoded
     */
    protected array $seed = [
        [
            'name' => 'RIASEC Standar 42 Butir',
            'test_type' => 'riasec',
            'dimensions' => '{"R":{"label":"Realistic","color":"#3B6D11"},"I":{"label":"Investigative","color":"#185FA5"},"A":{"label":"Artistic","color":"#854F0B"},"S":{"label":"Social","color":"#3C3489"},"E":{"label":"Enterprising","color":"#993C1D"},"C":{"label":"Conventional","color":"#5F5E5A"}}',
            'scoring_rules' => '{"scale":4,"min_value":1,"max_value":4,"categories":[{"min":25,"max":28,"label":"Sangat Tinggi"},{"min":19,"max":24,"label":"Tinggi"},{"min":13,"max":18,"label":"Sedang"},{"min":7,"max":12,"label":"Rendah"}]}',
            'is_active' => true
        ],
        [
            'name' => 'RIASEC Singkat 24 Butir',
            'test_type' => 'riasec',
            'dimensions' => '{"R":{"label":"Realistic","color":"#3B6D11"},"I":{"label":"Investigative","color":"#185FA5"},"A":{"label":"Artistic","color":"#854F0B"},"S":{"label":"Social","color":"#3C3489"},"E":{"label":"Enterprising","color":"#993C1D"},"C":{"label":"Conventional","color":"#5F5E5A"}}',
            'scoring_rules' => '{"scale":4,"min_value":1,"max_value":4,"categories":[{"min":25,"max":28,"label":"Sangat Tinggi"},{"min":19,"max":24,"label":"Tinggi"},{"min":13,"max":18,"label":"Sedang"},{"min":7,"max":12,"label":"Rendah"}]}',
            'is_active' => true
        ]
    ];

    /**
     * Get all test configurations
     */
    public function all(): array
    {
        $stmt = $this->getDb()->prepare("SELECT * FROM {$this->table} ORDER BY name ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Find test configuration by ID
     */
    public function find(int|string $id): ?array
    {
        $stmt = $this->getDb()->prepare("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    /**
     * Find configuration by name
     */
    public function findByName(string $name): ?array
    {
        $stmt = $this->getDb()->prepare("SELECT * FROM {$this->table} WHERE name = :name LIMIT 1");
        $stmt->execute(['name' => $name]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    /**
     * Get active configuration by test type
     * 
     * @param string $testType Tipe tes (riasec, iq, dll)
     * @return array|null Konfigurasi atau NULL jika tidak ditemukan
     */
    public function getActiveConfig(string $testType): ?array
    {
        $stmt = $this->getDb()->prepare("
            SELECT * FROM {$this->table}
            WHERE test_type = :test_type 
              AND is_active = TRUE
            ORDER BY name ASC
        ");

        $stmt->execute(['test_type' => $testType]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    /**
     * Get all configurations by test type
     */
    public function findByTestType(string $testType): array
    {
        $stmt = $this->getDb()->prepare("
            SELECT * FROM {$this->table}
            WHERE test_type = :test_type
            ORDER BY name ASC
        ");
        $stmt->execute(['test_type' => $testType]);
        return $stmt->fetchAll();
    }

    /**
     * Get all active configurations
     */
    public function getActiveConfigs(): array
    {
        $stmt = $this->getDb()->prepare("
            SELECT * FROM {$this->table}
            WHERE is_active = TRUE
            ORDER BY test_type, name ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Create new test configuration
     */
    public function create(array $data): int
    {
        $validData = [];
        foreach ($data as $key => $value) {
            if (isset($this->schema[$key]) && $key !== 'id') {
                $validData[$key] = $value;
            }
        }

        // Encode JSON fields if array
        if (isset($validData['dimensions']) && is_array($validData['dimensions'])) {
            $validData['dimensions'] = json_encode($validData['dimensions']);
        }
        if (isset($validData['scoring_rules']) && is_array($validData['scoring_rules'])) {
            $validData['scoring_rules'] = json_encode($validData['scoring_rules']);
        }

        $columns = implode(', ', array_keys($validData));
        $placeholders = ':' . implode(', :', array_keys($validData));

        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";

        if ($this->getDb()->query($sql, $validData)) {
            return (int) $this->getDb()->lastInsertId();
        }

        throw new \PDOException('Gagal membuat test configuration');
    }

    /**
     * Update test configuration by ID
     */
    public function updateById(int|string $id, array $data): bool
    {
        if (empty($data)) {
            return false;
        }

        if (!isset($data['updated_at'])) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }

        // Encode JSON fields if array
        if (isset($data['dimensions']) && is_array($data['dimensions'])) {
            $data['dimensions'] = json_encode($data['dimensions']);
        }
        if (isset($data['scoring_rules']) && is_array($data['scoring_rules'])) {
            $data['scoring_rules'] = json_encode($data['scoring_rules']);
        }

        $setParts = [];
        foreach ($data as $column => $value) {
            $setParts[] = "{$column} = :{$column}";
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $setParts) . " WHERE id = :id";
        $data['id'] = $id;

        return $this->getDb()->query($sql, $data);
    }

    /**
     * Activate a configuration
     */
    public function activate(int $id): bool
    {
        return $this->getDb()->query(
            "UPDATE {$this->table} SET is_active = TRUE, updated_at = NOW() WHERE id = :id",
            ['id' => $id]
        );
    }

    /**
     * Deactivate a configuration
     */
    public function deactivate(int $id): bool
    {
        return $this->getDb()->query(
            "UPDATE {$this->table} SET is_active = FALSE, updated_at = NOW() WHERE id = :id",
            ['id' => $id]
        );
    }

    /**
     * Delete test configuration by ID
     */
    public function deleteById(int|string $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        return $this->getDb()->query($sql, ['id' => $id]);
    }

    /**
     * Decode JSON fields from database result
     */
    public function decodeJsonFields(?array $row): ?array
    {
        if ($row === null) {
            return null;
        }

        $decoded = $row;
        if (isset($row['dimensions'])) {
            $decoded['dimensions'] = json_decode($row['dimensions'], true) ?? [];
        }
        if (isset($row['scoring_rules'])) {
            $decoded['scoring_rules'] = json_decode($row['scoring_rules'], true) ?? [];
        }

        return $decoded;
    }
}
