<?php

namespace Addon\Models;

use App\Core\Database\Model;

/**
 * Test Session Model - Sesi Pengerjaan Tes
 *
 * Model ini mengelola sesi pengerjaan tes oleh siswa.
 * Setiap sesi memiliki status (in_progress, completed, abandoned) dan timestamp.
 * Config_id mengacu pada test_configurations yang reusable (bisa dipakai banyak sekolah).
 *
 * Fields:
 * - id: Primary key
 * - student_profile_id: Foreign key ke student_profiles.id
 * - config_id: Foreign key ke test_configurations.id (konfigurasi yang dipakai)
 * - status: Status sesi (in_progress, completed, abandoned)
 * - started_at: Waktu mulai tes
 * - completed_at: Waktu selesai tes
 */
class TestSessionModel extends Model
{
    protected ?string $connection = 'mysql';
    protected string $table = 'test_sessions';
    protected bool $timestamps = true;

    protected array $schema = [
        'id' => ['type' => 'id', 'primary' => true, 'auto_increment' => true],
        'student_profile_id' => ['type' => 'bigint', 'nullable' => false, 'foreign' => 'student_profiles.id', 'on_delete' => 'cascade', 'unsigned' => true],
        'config_id' => ['type' => 'bigint', 'nullable' => false, 'foreign' => 'test_configurations.id', 'on_delete' => 'cascade', 'unsigned' => true],
        'status' => ['type' => 'enum', 'values' => ['in_progress', 'completed', 'abandoned', 'expired'], 'nullable' => false, 'default' => 'in_progress'],
        'started_at' => ['type' => 'timestamp', 'nullable' => true],
        'completed_at' => ['type' => 'timestamp', 'nullable' => true]
    ];

    /**
     * Get all test sessions
     */
    public function all(): array
    {
        $stmt = $this->getDb()->prepare("SELECT * FROM {$this->table} ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Find test session by ID
     */
    public function find(int|string $id): ?array
    {
        $stmt = $this->getDb()->prepare("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    /**
     * Create new test session
     */
    public function createSession(int $studentProfileId, int $configId): int
    {
        $this->getDb()->query(
            "INSERT INTO {$this->table} (student_profile_id, config_id, status, started_at, created_at) 
             VALUES (:student_profile_id, :config_id, 'in_progress', NOW(), NOW())",
            ['student_profile_id' => $studentProfileId, 'config_id' => $configId]
        );
        return (int) $this->getDb()->lastInsertId();
    }

    /**
     * Complete a test session
     */
    public function completeSession(int $sessionId): bool
    {
        return $this->getDb()->query(
            "UPDATE {$this->table} SET status = 'completed', completed_at = NOW(), updated_at = NOW() WHERE id = :id",
            ['id' => $sessionId]
        );
    }

    /**
     * Abandon a test session
     */
    public function abandonSession(int $sessionId): bool
    {
        return $this->getDb()->query(
            "UPDATE {$this->table} SET status = 'abandoned', updated_at = NOW() WHERE id = :id",
            ['id' => $sessionId]
        );
    }

    /**
     * Get active session for a student and test type
     */
    public function getActiveSession(int $studentProfileId, string $testType): ?array
    {
        $stmt = $this->getDb()->prepare("
            SELECT ts.*, tc.test_type, tc.dimensions
            FROM {$this->table} ts
            JOIN test_configurations tc ON ts.config_id = tc.id
            WHERE ts.student_profile_id = :student_profile_id 
              AND tc.test_type = :test_type
              AND ts.status = 'in_progress'
            ORDER BY ts.created_at DESC
            LIMIT 1
        ");
        $stmt->execute([
            'student_profile_id' => $studentProfileId,
            'test_type' => $testType
        ]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    /**
     * Get session by ID with configuration and student info
     */
    public function getSessionWithDetails(int $sessionId): ?array
    {
        $stmt = $this->getDb()->prepare("
            SELECT ts.*, tc.test_type, tc.dimensions, sp.student_id
            FROM {$this->table} ts
            JOIN test_configurations tc ON ts.config_id = tc.id
            JOIN student_profiles sp ON ts.student_profile_id = sp.id
            WHERE ts.id = :id
            LIMIT 1
        ");
        $stmt->execute(['id' => $sessionId]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    /**
     * Get all sessions for a student
     */
    public function getByStudentProfileId(int $studentProfileId): array
    {
        $stmt = $this->getDb()->prepare("
            SELECT ts.*, tc.test_type
            FROM {$this->table} ts
            JOIN test_configurations tc ON ts.config_id = tc.id
            WHERE ts.student_profile_id = :student_profile_id
            ORDER BY ts.created_at DESC
        ");
        $stmt->execute(['student_profile_id' => $studentProfileId]);
        return $stmt->fetchAll();
    }

    /**
     * Get completed sessions for a student
     */
    public function getCompletedSessions(int $studentProfileId): array
    {
        $stmt = $this->getDb()->prepare("
            SELECT ts.*, tc.test_type
            FROM {$this->table} ts
            JOIN test_configurations tc ON ts.config_id = tc.id
            WHERE ts.student_profile_id = :student_profile_id AND ts.status = 'completed'
            ORDER BY ts.completed_at DESC
        ");
        $stmt->execute(['student_profile_id' => $studentProfileId]);
        return $stmt->fetchAll();
    }

    /**
     * Get sessions by test type
     */
    public function getByTestType(string $testType): array
    {
        $stmt = $this->getDb()->prepare("
            SELECT ts.*, sp.student_id
            FROM {$this->table} ts
            JOIN test_configurations tc ON ts.config_id = tc.id
            JOIN student_profiles sp ON ts.student_profile_id = sp.id
            WHERE tc.test_type = :test_type
            ORDER BY ts.created_at DESC
        ");
        $stmt->execute(['test_type' => $testType]);
        return $stmt->fetchAll();
    }

    /**
     * Get sessions by school
     */
    public function getBySchoolId(int $schoolId): array
    {
        $stmt = $this->getDb()->prepare("
            SELECT ts.*, tc.test_type, sp.student_id
            FROM {$this->table} ts
            JOIN test_configurations tc ON ts.config_id = tc.id
            JOIN student_profiles sp ON ts.student_profile_id = sp.id
            WHERE sp.school_id = :school_id
            ORDER BY ts.created_at DESC
        ");
        $stmt->execute(['school_id' => $schoolId]);
        return $stmt->fetchAll();
    }

    /**
     * Update session by ID
     */
    public function updateById(int|string $id, array $data): bool
    {
        if (empty($data)) {
            return false;
        }

        if (!isset($data['updated_at'])) {
            $data['updated_at'] = date('Y-m-d H:i:s');
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
     * Delete session by ID
     */
    public function deleteById(int|string $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        return $this->getDb()->query($sql, ['id' => $id]);
    }

    /**
     * Count sessions by status
     */
    public function countByStatus(string $status): int
    {
        $stmt = $this->getDb()->prepare("
            SELECT COUNT(*) FROM {$this->table} WHERE status = :status
        ");
        $stmt->execute(['status' => $status]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Count completed sessions for a student
     */
    public function countCompletedByStudent(int $studentProfileId): int
    {
        $stmt = $this->getDb()->prepare("
            SELECT COUNT(*) FROM {$this->table}
            WHERE student_profile_id = :student_profile_id AND status = 'completed'
        ");
        $stmt->execute(['student_profile_id' => $studentProfileId]);
        return (int) $stmt->fetchColumn();
    }
}
