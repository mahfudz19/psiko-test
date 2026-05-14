<?php

namespace Addon\Models;

use App\Core\Database\Model;

/**
 * Test Result Model - Hasil Tes dan Analisis
 *
 * Model ini menyimpan hasil perhitungan skor tes, kategori, kode Holland,
 * dan rekomendasi AI untuk setiap sesi tes yang diselesaikan.
 *
 * Fields:
 * - id: Primary key
 * - session_id: Foreign key ke test_sessions.id (unique)
 * - scores: JSON skor per dimensi
 * - percentages: JSON persentase per dimensi
 * - categories: JSON kategori per dimensi
 * - holland_code: Kode Holland (2-3 huruf)
 * - ranked_dimensions: JSON urutan dimensi dari tertinggi ke terendah
 * - ai_prompt: Prompt yang digunakan untuk generate AI
 * - calculated_at: Waktu perhitungan hasil
 */
class TestResultModel extends Model
{
    protected ?string $connection = 'mysql';
    protected string $table = 'test_results';
    protected bool $timestamps = false;

    protected array $schema = [
        'id' => ['type' => 'id', 'primary' => true, 'auto_increment' => true],
        'session_id' => ['type' => 'bigint', 'nullable' => false, 'unique' => true, 'foreign' => 'test_sessions.id', 'on_delete' => 'cascade', 'unsigned' => true],
        'scores' => ['type' => 'json', 'nullable' => false],
        'percentages' => ['type' => 'json', 'nullable' => false],
        'categories' => ['type' => 'json', 'nullable' => false],
        'holland_code' => ['type' => 'string', 'length' => 3, 'nullable' => false],
        'ranked_dimensions' => ['type' => 'json', 'nullable' => false],
        'ai_prompt' => ['type' => 'text', 'nullable' => true],
        'calculated_at' => ['type' => 'timestamp', 'nullable' => false, 'default' => 'CURRENT_TIMESTAMP']
    ];

    /**
     * Save test result
     */
    public function saveResult(int $sessionId, array $resultData): int
    {
        $this->getDb()->query("
            INSERT INTO {$this->table} 
            (session_id, scores, percentages, categories, holland_code, ranked_dimensions, ai_prompt, calculated_at)
            VALUES (:session_id, :scores, :percentages, :categories, :holland_code, :ranked_dimensions, :ai_prompt, NOW())
        ", [
            'session_id' => $sessionId,
            'scores' => json_encode($resultData['scores']),
            'percentages' => json_encode($resultData['percentages']),
            'categories' => json_encode($resultData['categories']),
            'holland_code' => $resultData['holland_code'],
            'ranked_dimensions' => json_encode($resultData['ranked_dimensions']),
            'ai_prompt' => $resultData['ai_prompt'] ?? null
        ]);
        return (int) $this->getDb()->lastInsertId();
    }

    /**
     * Find result by ID
     */
    public function find(int|string $id): ?array
    {
        $stmt = $this->getDb()->prepare("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    /**
     * Get result by session ID
     */
    public function getResultBySessionId(int $sessionId): ?array
    {
        $stmt = $this->getDb()->prepare("SELECT * FROM {$this->table} WHERE session_id = :session_id LIMIT 1");
        $stmt->execute(['session_id' => $sessionId]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    /**
     * Get result by session ID with decoded JSON fields
     */
    public function getResultWithDetails(int $sessionId): ?array
    {
        $result = $this->getResultBySessionId($sessionId);
        if ($result === null) {
            return null;
        }

        return $this->decodeJsonFields($result);
    }

    /**
     * Get latest result for a student profile
     */
    public function getResultByStudentProfileId(int $studentProfileId): ?array
    {
        $stmt = $this->getDb()->prepare("
            SELECT tr.*, ts.student_profile_id, tc.test_type
            FROM {$this->table} tr
            JOIN test_sessions ts ON tr.session_id = ts.id
            JOIN test_configurations tc ON ts.config_id = tc.id
            WHERE ts.student_profile_id = :student_profile_id AND ts.status = 'completed'
            ORDER BY tr.calculated_at DESC
            LIMIT 1
        ");
        $stmt->execute(['student_profile_id' => $studentProfileId]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    /**
     * Get latest RIASEC result for a student profile
     */
    public function getLatestRiasecResult(int $studentProfileId): ?array
    {
        $stmt = $this->getDb()->prepare("
            SELECT tr.*, ts.student_profile_id
            FROM {$this->table} tr
            JOIN test_sessions ts ON tr.session_id = ts.id
            JOIN test_configurations tc ON ts.config_id = tc.id
            WHERE ts.student_profile_id = :student_profile_id 
              AND tc.test_type = 'riasec'
              AND ts.status = 'completed'
            ORDER BY tr.calculated_at DESC
            LIMIT 1
        ");
        $stmt->execute(['student_profile_id' => $studentProfileId]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    /**
     * Get all results for a student profile
     */
    public function getByStudentProfileId(int $studentProfileId): array
    {
        $stmt = $this->getDb()->prepare("
            SELECT tr.*, ts.student_profile_id, tc.test_type
            FROM {$this->table} tr
            JOIN test_sessions ts ON tr.session_id = ts.id
            JOIN test_configurations tc ON ts.config_id = tc.id
            WHERE ts.student_profile_id = :student_profile_id
            ORDER BY tr.calculated_at DESC
        ");
        $stmt->execute(['student_profile_id' => $studentProfileId]);
        return $stmt->fetchAll();
    }

    /**
     * Get results by test type
     */
    public function getByTestType(string $testType): array
    {
        $stmt = $this->getDb()->prepare("
            SELECT tr.*, ts.student_profile_id, sp.student_id
            FROM {$this->table} tr
            JOIN test_sessions ts ON tr.session_id = ts.id
            JOIN test_configurations tc ON ts.config_id = tc.id
            JOIN student_profiles sp ON ts.student_profile_id = sp.id
            WHERE tc.test_type = :test_type
            ORDER BY tr.calculated_at DESC
        ");
        $stmt->execute(['test_type' => $testType]);
        return $stmt->fetchAll();
    }

    /**
     * Get results by school
     */
    public function getBySchoolId(int $schoolId, ?string $testType = null): array
    {
        $typeClause = $testType ? "AND tc.test_type = :test_type" : "";
        $params = ['school_id' => $schoolId];
        if ($testType) {
            $params['test_type'] = $testType;
        }

        $stmt = $this->getDb()->prepare("
            SELECT tr.*, ts.student_profile_id, sp.student_id, tc.test_type
            FROM {$this->table} tr
            JOIN test_sessions ts ON tr.session_id = ts.id
            JOIN test_configurations tc ON ts.config_id = tc.id
            JOIN student_profiles sp ON ts.student_profile_id = sp.id
            WHERE sp.school_id = :school_id {$typeClause}
            ORDER BY tr.calculated_at DESC
        ");
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Update result by ID
     */
    public function updateById(int|string $id, array $data): bool
    {
        if (empty($data)) {
            return false;
        }

        // Encode JSON fields if array
        if (isset($data['scores']) && is_array($data['scores'])) {
            $data['scores'] = json_encode($data['scores']);
        }
        if (isset($data['percentages']) && is_array($data['percentages'])) {
            $data['percentages'] = json_encode($data['percentages']);
        }
        if (isset($data['categories']) && is_array($data['categories'])) {
            $data['categories'] = json_encode($data['categories']);
        }
        if (isset($data['ranked_dimensions']) && is_array($data['ranked_dimensions'])) {
            $data['ranked_dimensions'] = json_encode($data['ranked_dimensions']);
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
     * Delete result by ID
     */
    public function deleteById(int|string $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        return $this->getDb()->query($sql, ['id' => $id]);
    }

    /**
     * Delete result by session ID
     */
    public function deleteBySessionId(int $sessionId): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE session_id = :session_id";
        return $this->getDb()->query($sql, ['session_id' => $sessionId]);
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
        $jsonFields = ['scores', 'percentages', 'categories', 'ranked_dimensions'];

        foreach ($jsonFields as $field) {
            if (isset($row[$field]) && is_string($row[$field])) {
                $decoded[$field] = json_decode($row[$field], true) ?? [];
            }
        }

        return $decoded;
    }

    /**
     * Calculate scores from responses
     *
     * @param int $sessionId Session ID
     * @param array $scoringRules Aturan skoring dari konfigurasi
     * @return array Hasil perhitungan skor lengkap
     */
    public function calculateScores(int $sessionId, array $scoringRules): array
    {
        // Get responses grouped by dimension (query langsung)
        $stmt = $this->getDb()->prepare("
            SELECT ts.dimension, tr.answer_value
            FROM test_responses tr
            JOIN test_statements ts ON tr.statement_id = ts.id
            WHERE tr.session_id = :session_id
            ORDER BY ts.dimension, ts.display_order
        ");
        $stmt->execute(['session_id' => $sessionId]);
        $responses = $stmt->fetchAll();

        // Group by dimension
        $groupedResponses = [];
        foreach ($responses as $response) {
            $groupedResponses[$response['dimension']][] = $response;
        }

        $scores = [];
        $percentages = [];
        $categories = [];

        // Hitung skor per dimensi
        foreach (['R', 'I', 'A', 'S', 'E', 'C'] as $dimension) {
            $responses = $groupedResponses[$dimension] ?? [];
            $totalScore = 0;

            foreach ($responses as $response) {
                $totalScore += (int) $response['answer_value'];
            }

            $scores[$dimension] = $totalScore;

            // Hitung persentase
            $maxScore = $scoringRules['max_value'] * count($responses);
            $percentage = $maxScore > 0 ? ($totalScore / $maxScore) * 100 : 0;
            $percentages[$dimension] = round($percentage, 1);

            // Tentukan kategori
            $category = 'Rendah';
            foreach ($scoringRules['categories'] as $cat) {
                if ($totalScore >= $cat['min'] && $totalScore <= $cat['max']) {
                    $category = $cat['label'];
                    break;
                }
            }
            $categories[$dimension] = $category;
        }

        // Urutkan dimensi dari skor tertinggi ke terendah
        $sortedScores = $scores;
        arsort($sortedScores);
        $rankedDimensions = array_keys($sortedScores);

        // Ambil 2-3 dimensi teratas untuk kode Holland
        $hollandCode = implode('', array_slice($rankedDimensions, 0, 3));

        return [
            'scores' => $scores,
            'percentages' => $percentages,
            'categories' => $categories,
            'holland_code' => $hollandCode,
            'ranked_dimensions' => $rankedDimensions
        ];
    }
}
