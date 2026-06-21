<?php

namespace Addon\Models;

use App\Core\Database\Model;

/**
 * School Config Mapping Model - Many-to-Many Mapping Sekolah & Konfigurasi Tes
 *
 * Model ini mengelola mapping antara sekolah dan konfigurasi tes.
 * Satu sekolah dapat menggunakan banyak konfigurasi, dan satu konfigurasi
 * dapat digunakan oleh banyak sekolah.
 *
 * Fields:
 * - id: Primary key
 * - school_id: Foreign key ke schools.id
 * - config_id: Foreign key ke test_configurations.id
 * - is_default: Apakah ini konfigurasi default untuk sekolah ini
 * - valid_from: Tanggal mulai berlaku (opsional)
 * - valid_until: Tanggal berakhir berlaku (opsional)
 */
class SchoolConfigMappingModel extends Model
{
    protected ?string $connection = 'mysql';
    protected string $table = 'school_config_mappings';
    protected bool $timestamps = true;

    protected array $schema = [
        'id' => ['type' => 'id', 'primary' => true, 'auto_increment' => true],
        'school_id' => ['type' => 'bigint', 'nullable' => false, 'unsigned' => true],
        'config_id' => ['type' => 'bigint', 'nullable' => false, 'unsigned' => true],
        'is_default' => ['type' => 'boolean', 'nullable' => false, 'default' => false],
        'valid_from' => ['type' => 'date', 'nullable' => true],
        'valid_until' => ['type' => 'date', 'nullable' => true]
    ];

    /**
     * Seed data untuk mapping default sekolah & konfigurasi RIASEC
     * Asumsi: school_id=1, config_id=1 (RIASEC Standar 42 Butir)
     */
    protected array $seed = [
        [
            'school_id' => 1,
            'config_id' => 1,
            'is_default' => true
        ],
        [
            'school_id' => 2,
            'config_id' => 1,
            'is_default' => true
        ]
    ];

    /**
     * Get all mappings
     */
    public function all(): array
    {
        $stmt = $this->getDb()->prepare("
            SELECT scm.*, tc.name as config_name, tc.test_type, s.name as school_name
            FROM {$this->table} scm
            JOIN test_configurations tc ON scm.config_id = tc.id
            JOIN schools s ON scm.school_id = s.id
            ORDER BY s.name, tc.name ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Find mapping by ID
     */
    public function find(int|string $id): ?array
    {
        $stmt = $this->getDb()->prepare("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    /**
     * Get all configurations for a school
     * 
     * @param int $schoolId ID sekolah
     * @return array List of configurations
     */
    public function getBySchoolId(int $schoolId): array
    {
        $stmt = $this->getDb()->prepare("
            SELECT scm.*, tc.name as config_name, tc.test_type, tc.dimensions, tc.scoring_rules
            FROM {$this->table} scm
            JOIN test_configurations tc ON scm.config_id = tc.id
            WHERE scm.school_id = :school_id
            ORDER BY scm.is_default DESC, tc.name ASC
        ");
        $stmt->execute(['school_id' => $schoolId]);
        return $stmt->fetchAll();
    }

    /**
     * Get all schools using a configuration
     * 
     * @param int $configId ID konfigurasi
     * @return array List of schools
     */
    public function getByConfigId(int $configId): array
    {
        $stmt = $this->getDb()->prepare("
            SELECT scm.*, s.name as school_name, s.npsn
            FROM {$this->table} scm
            JOIN schools s ON scm.school_id = s.id
            WHERE scm.config_id = :config_id
            ORDER BY s.name ASC
        ");
        $stmt->execute(['config_id' => $configId]);
        return $stmt->fetchAll();
    }

    /**
     * Get default configuration for a school
     * 
     * @param int $schoolId ID sekolah
     * @param string|null $testType Filter by test type (optional)
     * @return array|null Default configuration or NULL
     */
    public function getDefaultConfig(int $schoolId, ?string $testType = null): ?array
    {
        $sql = "
            SELECT scm.*, tc.name as config_name, tc.test_type, tc.dimensions, tc.scoring_rules
            FROM {$this->table} scm
            JOIN test_configurations tc ON scm.config_id = tc.id
            WHERE scm.school_id = :school_id AND scm.is_default = TRUE
        ";

        if ($testType) {
            $sql .= " AND tc.test_type = :test_type";
        }

        $sql .= " LIMIT 1";

        $stmt = $this->getDb()->prepare($sql);
        $params = ['school_id' => $schoolId];
        if ($testType) {
            $params['test_type'] = $testType;
        }

        $stmt->execute($params);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    /**
     * Check if a school has a configuration
     * 
     * @param int $schoolId ID sekolah
     * @param int $configId ID konfigurasi
     * @return bool True if mapping exists
     */
    public function hasConfig(int $schoolId, int $configId): bool
    {
        $stmt = $this->getDb()->prepare("
            SELECT COUNT(*) FROM {$this->table}
            WHERE school_id = :school_id AND config_id = :config_id
        ");
        $stmt->execute([
            'school_id' => $schoolId,
            'config_id' => $configId
        ]);
        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Assign a configuration to a school
     * 
     * @param int $schoolId ID sekolah
     * @param int $configId ID konfigurasi
     * @param bool $isDefault Set as default configuration
     * @param array $options Additional options (valid_from, valid_until)
     * @return int New mapping ID
     */
    public function assignConfig(int $schoolId, int $configId, bool $isDefault = false, array $options = []): int
    {
        // If setting as default, unset other defaults for this school
        if ($isDefault) {
            $this->getDb()->query(
                "UPDATE {$this->table} SET is_default = FALSE WHERE school_id = :school_id",
                ['school_id' => $schoolId]
            );
        }

        $data = [
            'school_id' => $schoolId,
            'config_id' => $configId,
            'is_default' => $isDefault ? 1 : 0  // Explicitly cast boolean to integer for MySQL
        ];

        if (isset($options['valid_from'])) {
            $data['valid_from'] = $options['valid_from'];
        }
        if (isset($options['valid_until'])) {
            $data['valid_until'] = $options['valid_until'];
        }

        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";

        if ($this->getDb()->query($sql, $data)) {
            return (int) $this->getDb()->lastInsertId();
        }

        throw new \PDOException('Gagal assign konfigurasi ke sekolah');
    }

    /**
     * Set a configuration as default for a school
     * 
     * @param int $schoolId ID sekolah
     * @param int $configId ID konfigurasi
     * @return bool True on success
     */
    public function setAsDefault(int $schoolId, int $configId): bool
    {
        // Unset other defaults first
        $this->getDb()->query(
            "UPDATE {$this->table} SET is_default = FALSE WHERE school_id = :school_id",
            ['school_id' => $schoolId]
        );

        // Set this one as default
        return $this->getDb()->query(
            "UPDATE {$this->table} SET is_default = TRUE WHERE school_id = :school_id AND config_id = :config_id",
            ['school_id' => $schoolId, 'config_id' => $configId]
        );
    }

    /**
     * Remove a configuration mapping from a school
     * 
     * @param int $schoolId ID sekolah
     * @param int $configId ID konfigurasi
     * @return bool True on success
     */
    public function removeConfig(int $schoolId, int $configId): bool
    {
        return $this->getDb()->query(
            "DELETE FROM {$this->table} WHERE school_id = :school_id AND config_id = :config_id",
            ['school_id' => $schoolId, 'config_id' => $configId]
        );
    }

    /**
     * Remove all configurations from a school
     * 
     * @param int $schoolId ID sekolah
     * @return bool True on success
     */
    public function removeAllConfigs(int $schoolId): bool
    {
        return $this->getDb()->query(
            "DELETE FROM {$this->table} WHERE school_id = :school_id",
            ['school_id' => $schoolId]
        );
    }

    /**
     * Get count of schools using a configuration
     * 
     * @param int $configId ID konfigurasi
     * @return int Count of schools
     */
    public function getSchoolCount(int $configId): int
    {
        $stmt = $this->getDb()->prepare("
            SELECT COUNT(*) FROM {$this->table} WHERE config_id = :config_id
        ");
        $stmt->execute(['config_id' => $configId]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Get count of configurations for a school
     * 
     * @param int $schoolId ID sekolah
     * @return int Count of configurations
     */
    public function getConfigCount(int $schoolId): int
    {
        $stmt = $this->getDb()->prepare("
            SELECT COUNT(*) FROM {$this->table} WHERE school_id = :school_id
        ");
        $stmt->execute(['school_id' => $schoolId]);
        return (int) $stmt->fetchColumn();
    }
}
