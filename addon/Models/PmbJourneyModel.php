<?php

namespace Addon\Models;

use App\Core\Database\Model;

class PmbJourneyModel extends Model
{
    protected ?string $connection = null;
    protected string $table = 'pmb_journeys';
    protected bool $timestamps = true;

    protected array $schema = [
        'id' => ['type' => 'id', 'primary' => true, 'auto_increment' => true],
        'student_profile_id' => [
            'type' => 'bigint',
            'unsigned' => true,
            'foreign' => 'student_profiles.id',
            'on_delete' => 'cascade'
        ],
        // Data hasil analisis AI untuk PMB
        'top_matches' => ['type' => 'json', 'nullable' => true],
        'scholarships' => ['type' => 'json', 'nullable' => true],
        'prompt' => ['type' => 'text', 'nullable' => true], // Prompt yang digunakan untuk generate AI

        // Tracking status simulasi pendaftaran
        'simulation_status' => [
            'type' => 'enum',
            'values' => ['not_started', 'in_progress', 'completed', 'converted'],
            'default' => 'not_started'
        ],
        'simulation_step' => ['type' => 'int', 'default' => 1],
        'simulation_data' => ['type' => 'json', 'nullable' => true], // Data form pendaftaran mentah

        // Hash untuk mendeteksi kapan perlu update data AI
        'last_data_hash' => ['type' => 'varchar', 'length' => 255, 'nullable' => true],

        'created_at' => ['type' => 'timestamp', 'default' => 'CURRENT_TIMESTAMP'],
        'updated_at' => ['type' => 'timestamp', 'default' => 'CURRENT_TIMESTAMP', 'on_update' => 'CURRENT_TIMESTAMP'],
    ];

    protected array $seed = [];

    public function all(): array
    {
        $stmt = $this->getDb()->prepare("SELECT * FROM {$this->table}");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function find(string|int $id): ?array
    {
        $stmt = $this->getDb()->prepare("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public function create(array $data): bool
    {
        if (empty($data)) return false;

        $columns = array_keys($data);
        $placeholders = array_map(fn($col) => ':' . $col, $columns);

        $sql = "INSERT INTO {$this->table} (" . implode(', ', $columns) . ")
                VALUES (" . implode(', ', $placeholders) . ")";

        return $this->getDb()->query($sql, $data);
    }

    public function updateById(string|int $id, array $data): bool
    {
        if (empty($data)) return false;

        $setParts = [];
        foreach ($data as $column => $value) {
            $setParts[] = "{$column} = :{$column}";
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $setParts) . " WHERE id = :id";
        $data['id'] = $id;

        return $this->getDb()->query($sql, $data);
    }

    public function deleteById(string|int $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        return $this->getDb()->query($sql, ['id' => $id]);
    }

    /**
     * Find PMB journey by student profile ID
     */
    public function findByStudentId(int $studentProfileId): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE student_profile_id = :student_id LIMIT 1";
        $stmt = $this->getDb()->prepare($sql);
        $stmt->execute(['student_id' => $studentProfileId]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    /**
     * Update PMB journey matches (AI generated)
     */
    public function updateMatches(int $studentProfileId, array $matches, string $hash, ?string $prompt = null): bool
    {
        $journey = $this->findByStudentId($studentProfileId);

        $data = [
            'top_matches' => json_encode($matches),
            'last_data_hash' => $hash
        ];

        if ($prompt !== null) {
            $data['prompt'] = $prompt;
        }

        if ($journey) {
            return $this->updateById($journey['id'], $data);
        } else {
            $data['student_profile_id'] = $studentProfileId;
            return (bool) $this->create($data);
        }
    }

    /**
     * Update scholarship eligibility data (calculated using ScholarshipCalculator)
     *
     * @param int $studentProfileId Student profile ID
     * @param array $scholarships Scholarship eligibility data from ScholarshipCalculator
     * @param string $hash Data hash for change detection
     * @return bool Success status
     */
    public function updateScholarships(int $studentProfileId, array $scholarships, string $hash): bool
    {
        $journey = $this->findByStudentId($studentProfileId);

        $data = [
            'scholarships' => json_encode($scholarships),
            'last_data_hash' => $hash
        ];

        if ($journey) {
            return $this->updateById($journey['id'], $data);
        } else {
            $data['student_profile_id'] = $studentProfileId;
            return (bool) $this->create($data);
        }
    }

    /**
     * Update simulation progress
     */
    public function updateSimulationProgress(int $studentProfileId, int $step, array $simulationData, string $status = 'in_progress'): bool
    {
        $journey = $this->findByStudentId($studentProfileId);

        $data = [
            'simulation_step' => $step,
            'simulation_status' => $status,
            'simulation_data' => json_encode($simulationData)
        ];

        if ($journey) {
            return $this->updateById($journey['id'], $data);
        } else {
            $data['student_profile_id'] = $studentProfileId;
            return (bool) $this->create($data);
        }
    }
}
