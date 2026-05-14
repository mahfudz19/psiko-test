<?php

namespace Addon\Models;

use App\Core\Database\Model;

/**
 * Test Response Model - Jawaban Siswa per Butir
 *
 * Model ini menyimpan jawaban siswa untuk setiap butir pernyataan.
 * Setiap jawaban terkait dengan sesi tes dan pernyataan tertentu.
 *
 * Fields:
 * - id: Primary key
 * - session_id: Foreign key ke test_sessions.id
 * - statement_id: Foreign key ke test_statements.id
 * - answer_value: Nilai jawaban (1-4 untuk skala Likert)
 * - answered_at: Waktu menjawab
 */
class TestResponseModel extends Model
{
    protected ?string $connection = 'mysql';
    protected string $table = 'test_responses';
    public bool $timestamps = false;

    protected array $schema = [
        'id' => ['type' => 'id', 'primary' => true, 'auto_increment' => true],
        'session_id' => ['type' => 'bigint', 'nullable' => false, 'foreign' => 'test_sessions.id', 'on_delete' => 'cascade', 'unsigned' => true],
        'statement_id' => ['type' => 'bigint', 'nullable' => false, 'foreign' => 'test_statements.id', 'on_delete' => 'cascade', 'unsigned' => true],
        'answer_value' => ['type' => 'tinyint', 'nullable' => false],
        'answered_at' => ['type' => 'timestamp', 'nullable' => false, 'default' => 'CURRENT_TIMESTAMP']
    ];

    /**
     * Save or update a response
     */
    public function saveResponse(int $sessionId, int $statementId, int $answerValue): bool
    {
        // Upsert: insert or update if exists
        return $this->getDb()->query("
            INSERT INTO {$this->table} (session_id, statement_id, answer_value, answered_at)
            VALUES (:session_id, :statement_id, :answer_value, NOW())
            ON DUPLICATE KEY UPDATE answer_value = :answer_value, answered_at = NOW()
        ", [
            'session_id' => $sessionId,
            'statement_id' => $statementId,
            'answer_value' => $answerValue
        ]);
    }

    /**
     * Save multiple responses at once (bulk insert with upsert)
     *
     * Menggunakan ON DUPLICATE KEY UPDATE untuk handle insert or update
     * memerlukan unique constraint pada (session_id, statement_id)
     */
    public function saveMany(array $responses): bool
    {
        if (empty($responses)) {
            return false;
        }

        $columns = ['session_id', 'statement_id', 'answer_value', 'answered_at'];
        $values = [];
        $params = [];

        foreach ($responses as $index => $response) {
            $values[] = "(:session_id_{$index}, :statement_id_{$index}, :answer_value_{$index}, NOW())";
            $params["session_id_{$index}"] = $response['session_id'];
            $params["statement_id_{$index}"] = $response['statement_id'];
            $params["answer_value_{$index}"] = $response['answer_value'];
        }

        $sql = "INSERT INTO {$this->table} (" . implode(', ', $columns) . ") VALUES " . implode(', ', $values);

        // Add ON DUPLICATE KEY UPDATE for upsert functionality
        $sql .= " ON DUPLICATE KEY UPDATE
                   answer_value = VALUES(answer_value),
                   answered_at = VALUES(answered_at)";

        return $this->getDb()->query($sql, $params);
    }

    /**
     * Get all responses for a session
     */
    public function getResponsesBySession(int $sessionId): array
    {
        $stmt = $this->getDb()->prepare("
            SELECT tr.*, ts.statement_text, ts.dimension, ts.display_order
            FROM {$this->table} tr
            JOIN test_statements ts ON tr.statement_id = ts.id
            WHERE tr.session_id = :session_id
            ORDER BY ts.display_order ASC
        ");
        $stmt->execute(['session_id' => $sessionId]);
        return $stmt->fetchAll();
    }

    /**
     * Get responses grouped by dimension
     */
    public function getGroupedByDimension(int $sessionId): array
    {
        $responses = $this->getResponsesBySession($sessionId);
        $grouped = [];

        foreach ($responses as $response) {
            $grouped[$response['dimension']][] = $response;
        }

        return $grouped;
    }

    /**
     * Check if a statement has been answered in a session
     */
    public function hasAnswered(int $sessionId, int $statementId): bool
    {
        $stmt = $this->getDb()->prepare("
            SELECT COUNT(*) FROM {$this->table}
            WHERE session_id = :session_id AND statement_id = :statement_id
        ");
        $stmt->execute(['session_id' => $sessionId, 'statement_id' => $statementId]);
        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Get answer value for a specific statement
     */
    public function getAnswer(int $sessionId, int $statementId): ?int
    {
        $stmt = $this->getDb()->prepare("
            SELECT answer_value FROM {$this->table}
            WHERE session_id = :session_id AND statement_id = :statement_id
        ");
        $stmt->execute(['session_id' => $sessionId, 'statement_id' => $statementId]);
        $result = $stmt->fetchColumn();
        return $result === false ? null : (int) $result;
    }

    /**
     * Count answered statements in a session
     */
    public function countAnswered(int $sessionId): int
    {
        $stmt = $this->getDb()->prepare("
            SELECT COUNT(*) FROM {$this->table}
            WHERE session_id = :session_id
        ");
        $stmt->execute(['session_id' => $sessionId]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Get response statistics by dimension
     */
    public function getStatsByDimension(int $sessionId): array
    {
        $stmt = $this->getDb()->prepare("
            SELECT ts.dimension, COUNT(*) as count, SUM(tr.answer_value) as total, AVG(tr.answer_value) as average
            FROM {$this->table} tr
            JOIN test_statements ts ON tr.statement_id = ts.id
            WHERE tr.session_id = :session_id
            GROUP BY ts.dimension
        ");
        $stmt->execute(['session_id' => $sessionId]);
        return $stmt->fetchAll();
    }

    /**
     * Delete responses for a session
     */
    public function deleteBySession(int $sessionId): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE session_id = :session_id";
        return $this->getDb()->query($sql, ['session_id' => $sessionId]);
    }

    /**
     * Delete a specific response
     */
    public function deleteResponse(int $sessionId, int $statementId): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE session_id = :session_id AND statement_id = :statement_id";
        return $this->getDb()->query($sql, [
            'session_id' => $sessionId,
            'statement_id' => $statementId
        ]);
    }

    /**
     * Get all responses by statement (for analysis)
     */
    public function getByStatement(int $statementId): array
    {
        $stmt = $this->getDb()->prepare("
            SELECT tr.*, ts.dimension, sp.student_id
            FROM {$this->table} tr
            JOIN test_statements ts ON tr.statement_id = ts.id
            JOIN test_sessions tsess ON tr.session_id = tsess.id
            JOIN student_profiles sp ON tsess.student_profile_id = sp.id
            WHERE tr.statement_id = :statement_id
        ");
        $stmt->execute(['statement_id' => $statementId]);
        return $stmt->fetchAll();
    }

    /**
     * Get response distribution for a statement
     */
    public function getDistributionByStatement(int $statementId): array
    {
        $stmt = $this->getDb()->prepare("
            SELECT answer_value, COUNT(*) as count
            FROM {$this->table}
            WHERE statement_id = :statement_id
            GROUP BY answer_value
            ORDER BY answer_value ASC
        ");
        $stmt->execute(['statement_id' => $statementId]);
        return $stmt->fetchAll();
    }
}
