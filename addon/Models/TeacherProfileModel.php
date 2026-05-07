<?php

namespace Addon\Models;

use App\Core\Database\Model;

/**
 * Teacher Profile Model - Profile Spesifik untuk Guru BK (Role: admin)
 *
 * Fields:
 * - id: Primary key
 * - profile_id: Foreign key to profiles table (unique)
 * - school_id: Foreign key to schools table
 * - teacher_id: NIP/NIK
 * - subject_specialty: Subject specialty
 * - certification: Certification
 * - managed_students: JSON array of student profile IDs
 * - counseling_schedule: JSON counseling schedule
 */
class TeacherProfileModel extends Model
{
    protected ?string $connection = 'mysql';
    protected string $table = 'teacher_profiles';
    protected bool $timestamps = true;

    protected array $schema = [
        'id' => ['type' => 'id', 'primary' => true, 'auto_increment' => true],
        'profile_id' => ['type' => 'bigint', 'nullable' => false, 'unique' => true, 'foreign' => 'profiles.id', 'on_delete' => 'cascade', 'unsigned' => true],
        'school_id' => ['type' => 'bigint', 'nullable' => true, 'foreign' => 'schools.id', 'on_delete' => 'set null', 'unsigned' => true],
        'teacher_id' => ['type' => 'string', 'nullable' => true], // NIP/NIK
        'subject_specialty' => ['type' => 'string', 'nullable' => true],
        'certification' => ['type' => 'string', 'nullable' => true],
        'managed_students' => ['type' => 'json', 'nullable' => true], // [student_profile_ids]
        'counseling_schedule' => ['type' => 'json', 'nullable' => true]
    ];



    protected array $seed = [
        [
            'profile_id' => 1,
            'school_id' => 1,
            'teacher_id' => '1234567890',
            'subject_specialty' => 'Matematika',
            'certification' => 'Guru BK Bersertifikat',
            'managed_students' => '[1]',
            'counseling_schedule' => '[]'
        ],
        [
            'profile_id' => 2,
            'school_id' => 2,
            'teacher_id' => '9876543210',
            'subject_specialty' => 'Bahasa Indonesia',
            'certification' => 'Guru BK Bersertifikat',
            'managed_students' => '[2]',
            'counseling_schedule' => '[]'
        ]
    ];

    /**
     * Get all teacher profiles
     */
    public function all(): array
    {
        $stmt = $this->getDb()->prepare("SELECT * FROM {$this->table}");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Find teacher profile by ID
     */
    public function find(int|string $id): ?array
    {
        $stmt = $this->getDb()->prepare("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    /**
     * Find teacher profile by profile ID
     */
    public function findByProfileId(int $profileId): ?array
    {
        $stmt = $this->getDb()->prepare("SELECT * FROM {$this->table} WHERE profile_id = :profile_id LIMIT 1");
        $stmt->execute(['profile_id' => $profileId]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    /**
     * Find teacher profile by user ID (join with profiles and users)
     */
    public function findByUserId(int $userId): ?array
    {
        $stmt = $this->getDb()->prepare("
            SELECT tp.*, p.*, u.email, u.name as user_name
            FROM {$this->table} tp
            JOIN profiles p ON tp.profile_id = p.id
            JOIN users u ON p.user_id = u.id
            WHERE u.id = :user_id
            LIMIT 1
        ");
        $stmt->execute(['user_id' => $userId]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    /**
     * Create new teacher profile
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

            throw new \PDOException('Gagal membuat teacher profile');
        } catch (\PDOException $e) {
            // Check for duplicate entry (profile_id already exists)
            if ($e->getCode() === '23000' || str_contains($e->getMessage(), 'Duplicate entry')) {
                throw new \Exception('Teacher profile untuk profile ini sudah ada');
            }
            throw $e;
        }
    }

    /**
     * Update teacher profile by ID
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
     * Update teacher profile by profile ID
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
     * Delete teacher profile by ID
     */
    public function deleteById(int|string $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        return $this->getDb()->query($sql, ['id' => $id]);
    }

    /**
     * Delete teacher profile by profile ID
     */
    public function deleteByProfileId(int $profileId): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE profile_id = :profile_id";
        return $this->getDb()->query($sql, ['profile_id' => $profileId]);
    }

    /**
     * Get all teachers by school ID
     */
    public function findBySchoolId(int $schoolId): array
    {
        $stmt = $this->getDb()->prepare("
            SELECT tp.*, p.*, u.email, u.name as user_name
            FROM {$this->table} tp
            JOIN profiles p ON tp.profile_id = p.id
            JOIN users u ON p.user_id = u.id
            WHERE tp.school_id = :school_id
        ");
        $stmt->execute(['school_id' => $schoolId]);
        return $stmt->fetchAll();
    }

    /**
     * Add student to managed students list
     */
    public function addManagedStudent(int $profileId, int $studentProfileId): bool
    {
        $teacher = $this->findByProfileId($profileId);
        if (!$teacher) {
            return false;
        }

        $managedStudents = json_decode($teacher['managed_students'] ?? '[]', true) ?? [];
        if (!in_array($studentProfileId, $managedStudents)) {
            $managedStudents[] = $studentProfileId;
        }

        return $this->updateByProfileId($profileId, ['managed_students' => json_encode($managedStudents)]);
    }

    /**
     * Remove student from managed students list
     */
    public function removeManagedStudent(int $profileId, int $studentProfileId): bool
    {
        $teacher = $this->findByProfileId($profileId);
        if (!$teacher) {
            return false;
        }

        $managedStudents = json_decode($teacher['managed_students'] ?? '[]', true) ?? [];
        $managedStudents = array_filter($managedStudents, fn($id) => $id !== $studentProfileId);

        return $this->updateByProfileId($profileId, ['managed_students' => json_encode(array_values($managedStudents))]);
    }

    /**
     * Check if teacher manages a student
     */
    public function isManagedBy(int $teacherProfileId, int $studentProfileId): bool
    {
        $teacher = $this->findByProfileId($teacherProfileId);
        if (!$teacher) {
            return false;
        }

        $managedStudents = json_decode($teacher['managed_students'] ?? '[]', true) ?? [];
        return in_array($studentProfileId, $managedStudents);
    }

    /**
     * Update counseling schedule
     */
    public function updateCounselingSchedule(int $profileId, array $schedule): bool
    {
        return $this->updateByProfileId($profileId, ['counseling_schedule' => json_encode($schedule)]);
    }

    /**
     * Add counseling schedule entry
     */
    public function addCounselingSchedule(int $profileId, array $entry): bool
    {
        $teacher = $this->findByProfileId($profileId);
        if (!$teacher) {
            return false;
        }

        $schedules = json_decode($teacher['counseling_schedule'] ?? '[]', true) ?? [];
        $schedules[] = array_merge($entry, ['created_at' => date('Y-m-d H:i:s')]);

        return $this->updateByProfileId($profileId, ['counseling_schedule' => json_encode($schedules)]);
    }

    /**
     * Create teacher profile for new registration
     */
    public function createForProfile(int $profileId): int
    {
        $data = [
            'profile_id' => $profileId,
            'school_id' => null,
            'teacher_id' => null,
            'subject_specialty' => null,
            'certification' => null,
            'managed_students' => null,
            'counseling_schedule' => null
        ];

        return $this->create($data);
    }

    /**
     * Get school data for this teacher
     */
    public function getSchool(int $profileId): ?array
    {
        $teacher = $this->findByProfileId($profileId);
        if (!$teacher || empty($teacher['school_id'])) {
            return null;
        }

        $stmt = $this->getDb()->prepare("SELECT * FROM schools WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $teacher['school_id']]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    /**
     * Check if teacher belongs to a specific school
     */
    public function belongsToSchool(int $profileId, int $schoolId): bool
    {
        $teacher = $this->findByProfileId($profileId);
        return $teacher && $teacher['school_id'] === $schoolId;
    }
}
