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
 * - psychological_tests: JSON psychological test results
 * - ai_analysis: JSON AI analysis results
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
        'extracurricular' => ['type' => 'json', 'nullable' => true], // [{name, role, year}]
        'achievements' => ['type' => 'json', 'nullable' => true], // [{title, level, year, certificate_url}]
        'psychological_tests' => ['type' => 'json', 'nullable' => true], // {test_id, scores, timestamps}
        'ai_analysis' => ['type' => 'json', 'nullable' => true], // {potentials, interests, talents, recommendations}
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
            'psychological_tests' => '[{"test_name": "Tes IQ Standar", "date": "2023-10-01", "result": "Diatas Rata-rata", "score": 115, "metrics": {"verbal": 110, "performance": 120}}, {"test_name": "Tes MBTI", "date": "2023-10-05", "result": "INTJ"}]',
            'ai_analysis' => null,
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
            'psychological_tests' => '[{"test_name": "Tes Gaya Belajar", "date": "2023-11-01", "result": "Visual"}]',
            'ai_analysis' => null,
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
        $stmt = $this->getDb()->prepare("
            SELECT sp.*, p.*, u.email, u.name as user_name
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
    public function updateAiAnalysis(int $profileId, array $aiAnalysis): bool
    {
        return $this->updateByProfileId($profileId, ['ai_analysis' => json_encode($aiAnalysis)]);
    }

    /**
     * Add psychological test result
     */
    public function addPsychologicalTest(int $profileId, array $testData): bool
    {
        $student = $this->findByProfileId($profileId);
        if (!$student) {
            return false;
        }

        $tests = json_decode($student['psychological_tests'] ?? '[]', true) ?? [];
        $tests[] = array_merge($testData, ['created_at' => date('Y-m-d H:i:s')]);

        return $this->updateByProfileId($profileId, ['psychological_tests' => json_encode($tests)]);
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
            'psychological_tests' => null,
            'ai_analysis' => null,
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
        if (isset($data['psychological_tests']) && $data['psychological_tests'] !== null) {
            $this->validatePsychologicalTests($data['psychological_tests']);
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

        $allowedKeys = ['name', 'role', 'year'];

        foreach ($data as $index => $item) {
            if (!is_array($item)) throw new \InvalidArgumentException("Item extracurricular index {$index} harus berupa object");

            foreach ($allowedKeys as $req) {
                if (!array_key_exists($req, $item)) {
                    throw new \InvalidArgumentException("Item extracurricular index {$index} harus memiliki '{$req}'");
                }
            }

            foreach ($item as $key => $value) {
                if (!in_array($key, $allowedKeys)) {
                    throw new \InvalidArgumentException("Key '{$key}' pada extracurricular index {$index} tidak diizinkan");
                }
                if (!is_string($value) && !is_numeric($value)) {
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

        $allowedKeys = ['title', 'level', 'year', 'certificate_url'];

        foreach ($data as $index => $item) {
            if (!is_array($item)) throw new \InvalidArgumentException("Item achievements index {$index} harus berupa object");

            if (!isset($item['title']) || !isset($item['level']) || !isset($item['year'])) {
                throw new \InvalidArgumentException("Item achievements index {$index} harus memiliki title, level, dan year");
            }

            foreach ($item as $key => $value) {
                if (!in_array($key, $allowedKeys)) {
                    throw new \InvalidArgumentException("Key '{$key}' pada achievements index {$index} tidak diizinkan");
                }
            }
        }
    }

    protected function validatePsychologicalTests(string|array $json): void
    {
        $data = is_string($json) ? json_decode($json, true) : $json;
        if (is_string($json) && json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('psychological_tests harus berupa JSON valid');
        }
        if (!is_array($data) || (!empty($data) && !array_is_list($data))) {
            throw new \InvalidArgumentException('psychological_tests harus berupa array of objects');
        }

        $allowedKeys = ['test_name', 'date', 'result', 'score', 'metrics', 'report_url'];

        foreach ($data as $index => $item) {
            if (!is_array($item)) throw new \InvalidArgumentException("Item psychological_tests index {$index} harus berupa object");

            if (!isset($item['test_name']) || !isset($item['date'])) {
                throw new \InvalidArgumentException("Item psychological_tests index {$index} harus memiliki test_name dan date");
            }

            foreach ($item as $key => $value) {
                if (!in_array($key, $allowedKeys)) {
                    throw new \InvalidArgumentException("Key '{$key}' pada psychological_tests index {$index} tidak diizinkan");
                }
            }

            if (isset($item['score']) && $item['score'] !== null && !is_numeric($item['score'])) {
                throw new \InvalidArgumentException("Nilai score pada psychological_tests index {$index} harus berupa angka atau null");
            }

            if (isset($item['metrics'])) {
                if (!is_array($item['metrics']) || array_is_list($item['metrics'])) {
                    throw new \InvalidArgumentException("metrics pada psychological_tests index {$index} harus berupa object (key-value)");
                }
                foreach ($item['metrics'] as $metKey => $metVal) {
                    if (!is_numeric($metVal) && !is_string($metVal)) {
                        throw new \InvalidArgumentException("Nilai metric '{$metKey}' pada psychological_tests index {$index} harus berupa angka atau string");
                    }
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

        $allowedKeys = ['summary', 'potential', 'interests', 'talents', 'recommendations', 'career_suggestions', 'generated_at', 'last_data_hash'];

        foreach ($data as $key => $value) {
            if (!in_array($key, $allowedKeys)) {
                throw new \InvalidArgumentException("Key '{$key}' pada ai_analysis tidak diizinkan");
            }

            // Validasi string keys
            if (in_array($key, ['summary', 'generated_at', 'last_data_hash'])) {
                if (!is_string($value)) {
                    throw new \InvalidArgumentException("Nilai '{$key}' pada ai_analysis harus berupa string");
                }
            } else {
                // Sisa keys harus array (bisa array of strings atau array of objects)
                if (!is_array($value)) {
                    throw new \InvalidArgumentException("Nilai '{$key}' pada ai_analysis harus berupa array");
                }
            }
        }
    }
}
