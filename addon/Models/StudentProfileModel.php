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
            'student_id' => 4,
            'grade_level' => '11',
            'major' => 'IPA',
            'academic_scores' => '{"math": 85, "indonesian": 90}',
            'extracurricular' => '[{"name": "Bulu Tangkis", "role": "Pemain", "year": "2022"}]',
            'achievements' => '[{"title": "Juara Lomba Olahraga", "level": "Sekolah", "year": "2022", "certificate_url": "https://example.com/certificate.jpg"}]',
            'psychological_tests' => '{"test_id": 1, "scores": [80, 75, 90], "timestamps": ["2022-01-01", "2022-02-01", "2022-03-01"]}',
            'ai_analysis' => '{"potentials": ["Math", "Science"], "interests": ["Sports", "Music"], "talents": ["Leadership", "Problem Solving"], "recommendations": ["Math", "Science"]}',
            'parent_name' => 'John Doe',
            'parent_phone' => '1234567890',
            'parent_email' => '5CtZ0@example.com',
        ],
        [
            'profile_id' => 4,
            'school_id' => 2,
            'student_id' => 5,
            'grade_level' => '12',
            'major' => 'IPS',
            'academic_scores' => '{"math": 80, "indonesian": 85}',
            'extracurricular' => '[{"name": "Bulu Tangkis", "role": "Pemain", "year": "2022"}]',
            'achievements' => '[{"title": "Juara Lomba Olahraga", "level": "Sekolah", "year": "2022", "certificate_url": "https://example.com/certificate.jpg"}]',
            'psychological_tests' => '{"test_id": 1, "scores": [80, 75, 90], "timestamps": ["2022-01-01", "2022-02-01", "2022-03-01"]}',
            'ai_analysis' => '{"potentials": ["Math", "Science"], "interests": ["Sports", "Music"], "talents": ["Leadership", "Problem Solving"], "recommendations": ["Math", "Science"]}',
            'parent_name' => 'Jane Doe',
            'parent_phone' => '9876543210',
            'parent_email' => '5CtZ0@example.com',
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
}
