<?php

namespace Addon\Models;

use App\Core\Database\Model;

/**
 * Student Profile Model - Profile Spesifik untuk Siswa (Role: user)
 *
 * Fields:
 * - id: Primary key
 * - profile_id: Foreign key to profiles table (unique)
 * - school_id: Foreign key to schools table
 * - student_id: NIS/NISN
 * - grade_level: Grade level (10, 11, 12)
 * - major: Major (IPA, IPS, Bahasa, dll)
 * - academic_scores: JSON academic scores
 * - extracurricular: JSON extracurricular activities
 * - achievements: JSON achievements
 * - ai_analysis: JSON AI analysis results (refer to TestResultModel for test results)
 * - parent_name: Parent/guardian name
 * - parent_phone: Parent/guardian phone
 * - parent_email: Parent/guardian email
 */
class StudentProfileModel extends Model
{
    protected ?string $connection = 'mysql';
    protected string $table = 'student_profiles';
    protected bool $timestamps = true;

    protected array $schema = [
        'id' => ['type' => 'id', 'primary' => true, 'auto_increment' => true],
        'profile_id' => ['type' => 'bigint', 'nullable' => false, 'unique' => true, 'foreign' => 'profiles.id', 'on_delete' => 'cascade', 'unsigned' => true],
        'school_id' => ['type' => 'bigint', 'nullable' => true, 'foreign' => 'schools.id', 'on_delete' => 'set null', 'unsigned' => true],
        'student_id' => ['type' => 'string', 'nullable' => true], // NIS/NISN
        'grade_level' => ['type' => 'enum', 'values' => ['10', '11', '12'], 'nullable' => true],
        'major' => ['type' => 'string', 'nullable' => true], // IPA, IPS, Bahasa, dll
        'academic_scores' => ['type' => 'json', 'nullable' => true], // {math: 85, indonesian: 90, ...}
        'extracurricular' => ['type' => 'json', 'nullable' => true], // [{name, position, year_start, year_end, description}]
        'achievements' => ['type' => 'json', 'nullable' => true], // [{name, rank, level, year, organizer, description}]
        'ai_analysis' => ['type' => 'json', 'nullable' => true], // {potentials, interests, talents, recommendations}
        'ai_prompt' => ['type' => 'text', 'nullable' => true], // Prompt yang digunakan untuk generate AI analysis
        'parent_name' => ['type' => 'string', 'nullable' => true],
        'parent_phone' => ['type' => 'string', 'nullable' => true],
        'parent_email' => ['type' => 'string', 'nullable' => true]
    ];

    protected array $seed = [
        [
            'profile_id' => 3,
            'school_id' => 1,
            'student_id' => '0051234567',
            'grade_level' => '11',
            'major' => 'IPA',
            'academic_scores' => '[{"semester": "Semester 1 Kelas 10", "subjects": [{"name": "Matematika", "final_score": 85, "sub_scores": {"pengetahuan": 80, "keterampilan": 90}}, {"name": "Bahasa Indonesia", "final_score": 90}]}]',
            'extracurricular' => '[{"name": "Bulu Tangkis", "role": "Pemain", "year": "2022"}, {"name": "Pramuka", "role": "Anggota", "year": "2022"}]',
            'achievements' => '[{"title": "Juara 1 Lomba Olahraga", "level": "Sekolah", "year": "2022", "certificate_url": "https://example.com/certificate.jpg"}]',
            'ai_analysis' => null,
            'ai_prompt' => null,
            'parent_name' => 'John Doe',
            'parent_phone' => '1234567890',
            'parent_email' => 'parent1@example.com',
        ],
        [
            'profile_id' => 4,
            'school_id' => 2,
            'student_id' => '0059876543',
            'grade_level' => '12',
            'major' => 'IPS',
            'academic_scores' => '[{"semester": "Semester 3 Kelas 11", "subjects": [{"name": "Ekonomi", "final_score": 88}, {"name": "Sosiologi", "final_score": 92}]}]',
            'extracurricular' => '[{"name": "PMR", "role": "Ketua", "year": "2023"}]',
            'achievements' => '[{"title": "Juara Harapan 1 Debat", "level": "Kota", "year": "2023", "certificate_url": "https://example.com/cert.jpg"}]',
            'ai_analysis' => null,
            'ai_prompt' => null,
            'parent_name' => 'Jane Doe',
            'parent_phone' => '0987654321',
            'parent_email' => 'parent2@example.com',
        ],
    ];

    /**
     * Get all student profiles
     */
    public function all(): array
    {
        $stmt = $this->getDb()->prepare("SELECT * FROM {$this->table}");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Find student profile by ID
     */
    public function find(int|string $id): ?array
    {
        $stmt = $this->getDb()->prepare("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    /**
     * Find student profile by profile ID
     */
    public function findByProfileId(int $profileId): ?array
    {
        $stmt = $this->getDb()->prepare("SELECT * FROM {$this->table} WHERE profile_id = :profile_id LIMIT 1");
        $stmt->execute(['profile_id' => $profileId]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    /**
     * Find student profile by user ID (join with profiles and users)
     */
    public function findByUserId(int $userId): ?array
    {
        // sp.user_name,
        // sp.extracurriculars,
        // sp.counseling_notes,
        // p.full_name,
        // p.date_of_birth,
        // Note: psychological_tests removed - use TestResultModel::getLatestRiasecResult() instead
        $stmt = $this->getDb()->prepare("
            SELECT
                sp.id as student_profile_id,
                sp.profile_id,
                sp.school_id,
                sp.student_id,
                sp.grade_level,
                sp.major,
                sp.academic_scores,
                sp.achievements,
                sp.ai_analysis,
                sp.parent_name,
                sp.parent_phone,
                sp.parent_email,
                sp.created_at,
                sp.updated_at,
                p.id as profile_id,
                p.user_id,
                p.gender,
                p.phone,
                p.address,
                u.id as user_id,
                u.email,
                u.name as user_name
            FROM {$this->table} sp
            JOIN profiles p ON sp.profile_id = p.id
            JOIN users u ON p.user_id = u.id
            WHERE u.id = :user_id
            LIMIT 1
        ");
        $stmt->execute(['user_id' => $userId]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    /**
     * Create new student profile
     */
    public function create(array $data): int
    {
        try {
            // Filter data based on schema
            $validData = [];
            foreach ($data as $key => $value) {
                if (isset($this->schema[$key]) && $key !== 'id') {
                    $validData[$key] = $value;
                }
            }

            // Convert empty arrays to null for JSON fields
            $validData = $this->convertEmptyJsonArraysToNull($validData);

            // Validate JSON fields
            $this->validateJsonData($validData);

            // Build columns and placeholders
            $columns = implode(', ', array_keys($validData));
            $placeholders = ':' . implode(', :', array_keys($validData));

            // Build INSERT query
            $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";

            // Execute query
            if ($this->getDb()->query($sql, $validData)) {
                return (int) $this->getDb()->lastInsertId();
            }

            throw new \PDOException('Gagal membuat student profile');
        } catch (\PDOException $e) {
            // Check for duplicate entry (profile_id already exists)
            if ($e->getCode() === '23000' || str_contains($e->getMessage(), 'Duplicate entry')) {
                throw new \Exception('Student profile untuk profile ini sudah ada');
            }
            throw $e;
        }
    }

    /**
     * Update student profile by ID
     */
    public function updateById(int|string $id, array $data): bool
    {
        if (empty($data)) {
            return false;
        }

        // Auto-update updated_at if not provided
        if (!isset($data['updated_at'])) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }

        // Convert empty arrays to null for JSON fields
        $data = $this->convertEmptyJsonArraysToNull($data);

        // Validate JSON fields
        $this->validateJsonData($data);

        $setParts = [];
        foreach ($data as $column => $value) {
            $setParts[] = "{$column} = :{$column}";
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $setParts) . " WHERE id = :id";
        $data['id'] = $id;

        return $this->getDb()->query($sql, $data);
    }

    /**
     * Update student profile by profile ID
     */
    public function updateByProfileId(int $profileId, array $data): bool
    {
        if (empty($data)) {
            return false;
        }

        // Auto-update updated_at if not provided
        if (!isset($data['updated_at'])) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }

        // Convert empty arrays to null for JSON fields
        $data = $this->convertEmptyJsonArraysToNull($data);

        // Validate JSON fields
        $this->validateJsonData($data);

        $setParts = [];
        foreach ($data as $column => $value) {
            $setParts[] = "{$column} = :{$column}";
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $setParts) . " WHERE profile_id = :profile_id";
        $data['profile_id'] = $profileId;

        return $this->getDb()->query($sql, $data);
    }

    /**
     * Delete student profile by ID
     */
    public function deleteById(int|string $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        return $this->getDb()->query($sql, ['id' => $id]);
    }

    /**
     * Delete student profile by profile ID
     */
    public function deleteByProfileId(int $profileId): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE profile_id = :profile_id";
        return $this->getDb()->query($sql, ['profile_id' => $profileId]);
    }

    /**
     * Get all students by school ID
     */
    public function findBySchoolId(int $schoolId): array
    {
        $stmt = $this->getDb()->prepare("
            SELECT sp.*, p.*, u.email, u.name as user_name
            FROM {$this->table} sp
            JOIN profiles p ON sp.profile_id = p.id
            JOIN users u ON p.user_id = u.id
            WHERE sp.school_id = :school_id
        ");
        $stmt->execute(['school_id' => $schoolId]);
        return $stmt->fetchAll();
    }

    /**
     * Get all students by grade level
     */
    public function findByGradeLevel(string $gradeLevel): array
    {
        $stmt = $this->getDb()->prepare("
            SELECT sp.*, p.*, u.email, u.name as user_name
            FROM {$this->table} sp
            JOIN profiles p ON sp.profile_id = p.id
            JOIN users u ON p.user_id = u.id
            WHERE sp.grade_level = :grade_level
        ");
        $stmt->execute(['grade_level' => $gradeLevel]);
        return $stmt->fetchAll();
    }

    /**
     * Get all students by major
     */
    public function findByMajor(string $major): array
    {
        $stmt = $this->getDb()->prepare("
            SELECT sp.*, p.*, u.email, u.name as user_name
            FROM {$this->table} sp
            JOIN profiles p ON sp.profile_id = p.id
            JOIN users u ON p.user_id = u.id
            WHERE sp.major = :major
        ");
        $stmt->execute(['major' => $major]);
        return $stmt->fetchAll();
    }

    /**
     * Update AI analysis for student
     */
    public function updateAiAnalysis(int $profileId, array $aiAnalysis, ?string $prompt = null): bool
    {
        $data = ['ai_analysis' => json_encode($aiAnalysis)];
        if ($prompt !== null) {
            $data['ai_prompt'] = $prompt;
        }
        return $this->updateByProfileId($profileId, $data);
    }

    /**
     * Create student profile for new registration
     */
    public function createForProfile(int $profileId): int
    {
        $data = [
            'profile_id' => $profileId,
            'school_id' => null,
            'student_id' => null,
            'grade_level' => null,
            'major' => null,
            'academic_scores' => null,
            'extracurricular' => null,
            'achievements' => null,
            'ai_analysis' => null,
            'ai_prompt' => null,
            'parent_name' => null,
            'parent_phone' => null,
            'parent_email' => null
        ];

        return $this->create($data);
    }

    /**
     * Get school data for this student
     */
    public function getSchool(int $profileId): ?array
    {
        $student = $this->findByProfileId($profileId);
        if (!$student || empty($student['school_id'])) {
            return null;
        }

        $stmt = $this->getDb()->prepare("SELECT * FROM schools WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $student['school_id']]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    /**
     * Check if student belongs to a specific school
     */
    public function belongsToSchool(int $profileId, int $schoolId): bool
    {
        $student = $this->findByProfileId($profileId);
        return $student && $student['school_id'] === $schoolId;
    }

    /**
     * Validasi data JSON sebelum insert/update
     */
    /**
     * Convert empty arrays to null for JSON fields
     */
    protected function convertEmptyJsonArraysToNull(array $data): array
    {
        $jsonFields = ['academic_scores', 'extracurricular', 'achievements', 'ai_analysis'];

        foreach ($jsonFields as $field) {
            if (!isset($data[$field])) {
                continue;
            }

            $value = $data[$field];

            // Handle array kosong
            if (is_array($value) && empty($value)) {
                $data[$field] = null;
                continue;
            }

            // Handle string '[]' (empty JSON array)
            if (is_string($value) && $value === '[]') {
                $data[$field] = null;
                continue;
            }

            // Handle string JSON yang setelah decode adalah array kosong
            if (is_string($value) && !empty($value)) {
                $decoded = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded) && empty($decoded)) {
                    $data[$field] = null;
                }
            }
        }
        return $data;
    }

    protected function validateJsonData(array $data): void
    {
        if (isset($data['academic_scores']) && $data['academic_scores'] !== null) {
            $this->validateAcademicScores($data['academic_scores']);
        }
        if (isset($data['extracurricular']) && $data['extracurricular'] !== null) {
            $this->validateExtracurricular($data['extracurricular']);
        }
        if (isset($data['achievements']) && $data['achievements'] !== null) {
            $this->validateAchievements($data['achievements']);
        }
        if (isset($data['ai_analysis']) && $data['ai_analysis'] !== null) {
            $this->validateAiAnalysis($data['ai_analysis']);
        }
    }

    protected function validateAcademicScores(string|array $json): void
    {
        $data = is_string($json) ? json_decode($json, true) : $json;
        if (is_string($json) && json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('academic_scores harus berupa JSON valid');
        }
        if (!is_array($data) || (!empty($data) && !array_is_list($data))) {
            throw new \InvalidArgumentException('academic_scores harus berupa array of objects (multi-semester)');
        }

        foreach ($data as $semesterIndex => $semesterData) {
            if (!is_array($semesterData) || !isset($semesterData['semester']) || !isset($semesterData['subjects'])) {
                throw new \InvalidArgumentException("Data akademik index {$semesterIndex} harus memiliki 'semester' dan 'subjects'");
            }
            if (!is_array($semesterData['subjects']) || (!empty($semesterData['subjects']) && !array_is_list($semesterData['subjects']))) {
                throw new \InvalidArgumentException("'subjects' pada semester '{$semesterData['semester']}' harus berupa array");
            }

            foreach ($semesterData['subjects'] as $subjectIndex => $subject) {
                if (!is_array($subject) || !isset($subject['name'])) {
                    throw new \InvalidArgumentException("Item subject index {$subjectIndex} pada semester '{$semesterData['semester']}' harus berupa object dengan 'name'");
                }

                if (isset($subject['final_score']) && !is_numeric($subject['final_score'])) {
                    throw new \InvalidArgumentException("final_score untuk '{$subject['name']}' harus berupa angka");
                }

                if (isset($subject['sub_scores'])) {
                    if (!is_array($subject['sub_scores']) || array_is_list($subject['sub_scores'])) {
                        throw new \InvalidArgumentException("sub_scores untuk '{$subject['name']}' harus berupa object (key-value)");
                    }
                    foreach ($subject['sub_scores'] as $subKey => $subVal) {
                        if (!is_numeric($subVal)) {
                            throw new \InvalidArgumentException("Nilai sub_scores '{$subKey}' pada '{$subject['name']}' harus berupa angka");
                        }
                    }
                }
            }
        }
    }

    protected function validateExtracurricular(string|array $json): void
    {
        $data = is_string($json) ? json_decode($json, true) : $json;
        if (is_string($json) && json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('extracurricular harus berupa JSON valid');
        }
        if (!is_array($data) || (!empty($data) && !array_is_list($data))) {
            throw new \InvalidArgumentException('extracurricular harus berupa array of objects');
        }

        // Field yang diizinkan (semua optional)
        $allowedKeys = ['name', 'position', 'role', 'year', 'year_start', 'year_end', 'description'];

        foreach ($data as $index => $item) {
            if (!is_array($item)) throw new \InvalidArgumentException("Item extracurricular index {$index} harus berupa object");

            // Harus memiliki setidaknya name
            if (!isset($item['name']) || $item['name'] === '') {
                throw new \InvalidArgumentException("Item extracurricular index {$index} harus memiliki 'name'");
            }

            foreach ($item as $key => $value) {
                if (!in_array($key, $allowedKeys)) {
                    throw new \InvalidArgumentException("Key '{$key}' pada extracurricular index {$index} tidak diizinkan");
                }
                if (!is_string($value) && !is_numeric($value) && $value !== null) {
                    throw new \InvalidArgumentException("Nilai untuk '{$key}' pada extracurricular index {$index} harus string/angka");
                }
            }
        }
    }

    protected function validateAchievements(string|array $json): void
    {
        $data = is_string($json) ? json_decode($json, true) : $json;
        if (is_string($json) && json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('achievements harus berupa JSON valid');
        }
        if (!is_array($data) || (!empty($data) && !array_is_list($data))) {
            throw new \InvalidArgumentException('achievements harus berupa array of objects');
        }

        // Field yang diizinkan (semua optional kecuali name)
        $allowedKeys = ['name', 'title', 'rank', 'level', 'year', 'organizer', 'description', 'certificate_url'];

        foreach ($data as $index => $item) {
            if (!is_array($item)) throw new \InvalidArgumentException("Item achievements index {$index} harus berupa object");

            // Harus memiliki setidaknya name
            if (!isset($item['name']) || $item['name'] === '') {
                throw new \InvalidArgumentException("Item achievements index {$index} harus memiliki 'name'");
            }

            foreach ($item as $key => $value) {
                if (!in_array($key, $allowedKeys)) {
                    throw new \InvalidArgumentException("Key '{$key}' pada achievements index {$index} tidak diizinkan");
                }
                if (!is_string($value) && !is_numeric($value) && $value !== null) {
                    throw new \InvalidArgumentException("Nilai untuk '{$key}' pada achievements index {$index} harus string/angka");
                }
            }
        }
    }

    protected function validateAiAnalysis(string|array $json): void
    {
        $data = is_string($json) ? json_decode($json, true) : $json;
        if (is_string($json) && json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('ai_analysis harus berupa JSON valid');
        }
        if (!is_array($data) || (!empty($data) && array_is_list($data))) {
            throw new \InvalidArgumentException('ai_analysis harus berupa object');
        }

        $allowedKeys = [
            'summary',
            'potential',
            'interests',
            'talents',
            'recommendations',
            'career_suggestions',
            'generated_at',
            'last_data_hash',
            'prompt',
            'holland_code',
            'riasec_scores',
            'data_completeness'
        ];

        foreach ($data as $key => $value) {
            if (!in_array($key, $allowedKeys)) {
                throw new \InvalidArgumentException("Key '{$key}' pada ai_analysis tidak diizinkan");
            }

            // Validasi string keys
            if (in_array($key, ['summary', 'generated_at', 'last_data_hash', 'holland_code', 'prompt'])) {
                if (!is_string($value)) {
                    throw new \InvalidArgumentException("Nilai '{$key}' pada ai_analysis harus berupa string");
                }
            } elseif ($key === 'riasec_scores') {
                // Validasi riasec_scores harus object dengan key R,I,A,S,E,C
                if (!is_array($value) || array_is_list($value)) {
                    throw new \InvalidArgumentException("Nilai 'riasec_scores' pada ai_analysis harus berupa object (key-value)");
                }
                $allowedDimensions = ['R', 'I', 'A', 'S', 'E', 'C'];
                foreach ($value as $dimKey => $dimVal) {
                    if (!in_array($dimKey, $allowedDimensions)) {
                        throw new \InvalidArgumentException("Dimensi '{$dimKey}' pada riasec_scores tidak valid");
                    }
                    if (!is_numeric($dimVal)) {
                        throw new \InvalidArgumentException("Nilai skor untuk dimensi '{$dimKey}' harus berupa angka");
                    }
                }
            } else {
                // Sisa keys harus array
                if (!is_array($value)) {
                    throw new \InvalidArgumentException("Nilai '{$key}' pada ai_analysis harus berupa array");
                }
            }
        }
    }

    /**
     * Update RIASEC AI analysis
     *
     * @deprecated Use TestResultModel::getLatestRiasecResult() instead
     * @param int $profileId Profile ID siswa
     * @param array $analysis Data analisis AI
     * @param string|null $prompt Prompt yang digunakan (optional)
     * @return bool True jika berhasil
     */
    public function updateRiasecAiAnalysis(int $profileId, array $analysis, ?string $prompt = null): bool
    {
        $student = $this->findByProfileId($profileId);
        if (!$student) {
            return false;
        }

        $currentAnalysis = json_decode($student['ai_analysis'] ?? 'null', true) ?? [];

        // Merge dengan data existing
        $updatedAnalysis = array_merge($currentAnalysis, [
            'holland_code' => $analysis['holland_code'],
            'riasec_scores' => $analysis['scores'],
            'summary' => $analysis['summary'] ?? '',
            'potential' => $analysis['potential'] ?? [],
            'interests' => $analysis['interests'] ?? [],
            'recommendations' => $analysis['recommendations'] ?? [],
            'generated_at' => date('Y-m-d H:i:s'),
            'last_data_hash' => $this->generateDataHash($analysis)
        ]);

        if ($prompt !== null) {
            $updatedAnalysis['prompt'] = $prompt;
        }

        return $this->updateByProfileId($profileId, [
            'ai_analysis' => json_encode($updatedAnalysis)
        ]);
    }

    /**
     * Generate hash dari data untuk deteksi perubahan
     *
     * @param array $data Data untuk di-hash
     * @return string Hash MD5
     */
    private function generateDataHash(array $data): string
    {
        // Sort keys untuk konsistensi hash (gunakan nilai numeric 1 = JSON_SORT_KEYS)
        return md5(json_encode($data, 1));
    }

    /**
     * Find student by identifier (NIS/NISN or name)
     *
     * @param string|int $identifier NIS/NISN or student name
     * @param int $schoolId School ID to filter by
     * @return array|null Student profile data or null if not found
     */
    public function findByIdentifier(string|int $identifier, int $schoolId): ?array
    {
        // First, try exact match on student_id (NIS/NISN)
        $stmt = $this->getDb()->prepare("
            SELECT sp.*, p.*, u.email, u.name as user_name
            FROM {$this->table} sp
            JOIN profiles p ON sp.profile_id = p.id
            JOIN users u ON p.user_id = u.id
            WHERE sp.student_id = :identifier AND sp.school_id = :school_id
            LIMIT 1
        ");
        $stmt->execute(['identifier' => (string)$identifier, 'school_id' => $schoolId]);
        $row = $stmt->fetch();

        if ($row !== false) {
            return $row;
        }

        // If not found by student_id, try fuzzy match on name
        $stmt = $this->getDb()->prepare("
            SELECT sp.*, p.*, u.email, u.name as user_name
            FROM {$this->table} sp
            JOIN profiles p ON sp.profile_id = p.id
            JOIN users u ON p.user_id = u.id
            WHERE (p.full_name LIKE :name OR u.name LIKE :name) AND sp.school_id = :school_id
            LIMIT 1
        ");
        $stmt->execute(['name' => '%' . (string)$identifier . '%', 'school_id' => $schoolId]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    /**
     * Bulk update academic scores by identifier
     *
     * @param array $scoresData Array of score data from CSV
     * @param string $semester Semester identifier (e.g., "Semester 1 Kelas 10")
     * @param int $schoolId School ID
     * @return array Result summary with success, failed, and errors
     */
    public function bulkUpdateAcademicScoresByIdentifier(array $scoresData, string $semester, int $schoolId): array
    {
        $result = [
            'success' => 0,
            'failed' => 0,
            'errors' => []
        ];

        // Group scores by student identifier
        $groupedByStudent = [];
        foreach ($scoresData as $row) {
            $identifier = $row['identifier'];
            if (!isset($groupedByStudent[$identifier])) {
                $groupedByStudent[$identifier] = [];
            }
            $groupedByStudent[$identifier][] = [
                'subject' => $row['subject'],
                'final_score' => (float)$row['final_score'],
                'pengetahuan' => isset($row['pengetahuan']) ? (float)$row['pengetahuan'] : null,
                'keterampilan' => isset($row['keterampilan']) ? (float)$row['keterampilan'] : null
            ];
        }

        // Process each student
        foreach ($groupedByStudent as $identifier => $subjects) {
            try {
                // Find student by identifier
                $student = $this->findByIdentifier($identifier, $schoolId);

                if (!$student) {
                    $result['failed']++;
                    $result['errors'][] = [
                        'identifier' => $identifier,
                        'error' => 'Student tidak ditemukan dengan NIS/NISN atau nama tersebut'
                    ];
                    continue;
                }

                // Parse existing academic scores
                $existingScores = json_decode($student['academic_scores'] ?? 'null', true) ?? [];
                if (!is_array($existingScores)) {
                    $existingScores = [];
                }

                // Find or create semester entry
                $semesterIndex = -1;
                foreach ($existingScores as $index => $semData) {
                    if (($semData['semester'] ?? '') === $semester) {
                        $semesterIndex = $index;
                        break;
                    }
                }

                // Build new subjects array
                $newSubjects = [];
                foreach ($subjects as $subjectData) {
                    $subject = [
                        'name' => $subjectData['subject'],
                        'final_score' => $subjectData['final_score']
                    ];

                    // Add sub_scores if provided
                    $subScores = [];
                    if ($subjectData['pengetahuan'] !== null) {
                        $subScores['pengetahuan'] = $subjectData['pengetahuan'];
                    }
                    if ($subjectData['keterampilan'] !== null) {
                        $subScores['keterampilan'] = $subjectData['keterampilan'];
                    }

                    if (!empty($subScores)) {
                        $subject['sub_scores'] = $subScores;
                    }

                    $newSubjects[] = $subject;
                }

                // Merge with existing semester data
                if ($semesterIndex >= 0) {
                    // Update existing semester - merge subjects
                    $existingSubjects = $existingScores[$semesterIndex]['subjects'] ?? [];
                    $mergedSubjects = $this->mergeSubjects($existingSubjects, $newSubjects);
                    $existingScores[$semesterIndex]['subjects'] = $mergedSubjects;
                } else {
                    // Add new semester entry
                    $existingScores[] = [
                        'semester' => $semester,
                        'subjects' => $newSubjects
                    ];
                }

                // Update database
                $updated = $this->updateByProfileId((int)$student['profile_id'], [
                    'academic_scores' => json_encode($existingScores)
                ]);

                if ($updated) {
                    $result['success']++;
                } else {
                    $result['failed']++;
                    $result['errors'][] = [
                        'identifier' => $identifier,
                        'error' => 'Gagal mengupdate database'
                    ];
                }
            } catch (\Exception $e) {
                $result['failed']++;
                $result['errors'][] = [
                    'identifier' => $identifier,
                    'error' => $e->getMessage()
                ];
            }
        }

        return $result;
    }

    /**
     * Merge subjects - update existing or add new
     *
     * @param array $existing Existing subjects array
     * @param array $new New subjects to merge
     * @return array Merged subjects array
     */
    private function mergeSubjects(array $existing, array $new): array
    {
        $merged = [];
        $existingByName = [];

        // Index existing subjects by name
        foreach ($existing as $subject) {
            $name = $subject['name'] ?? '';
            if ($name !== '') {
                $existingByName[$name] = $subject;
            }
        }

        // Merge or add new subjects
        foreach ($new as $subject) {
            $name = $subject['name'] ?? '';
            if ($name === '') {
                continue;
            }

            if (isset($existingByName[$name])) {
                // Update existing subject - override with new values
                $merged[] = array_merge($existingByName[$name], $subject);
                unset($existingByName[$name]);
            } else {
                // Add new subject
                $merged[] = $subject;
            }
        }

        // Add remaining existing subjects that weren't updated
        foreach ($existingByName as $subject) {
            $merged[] = $subject;
        }

        return $merged;
    }
}
