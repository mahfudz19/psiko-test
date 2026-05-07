<?php

namespace Addon\Models;

use App\Core\Database\Model;

/**
 * School Model - Master Data Sekolah
 *
 * Fields:
 * - id: Primary key
 * - name: School name
 * - npsn: National school identification number (unique)
 * - address: School address
 * - principal_name: Principal name
 * - contact: Contact information
 * - accreditation: School accreditation (A, B, C)
 */
class SchoolModel extends Model
{
    protected ?string $connection = 'mysql';
    protected string $table = 'schools';
    protected bool $timestamps = true;

    protected array $schema = [
        'id' => ['type' => 'id', 'primary' => true, 'auto_increment' => true],
        'name' => ['type' => 'string', 'nullable' => false],
        'npsn' => ['type' => 'string', 'nullable' => true, 'unique' => true],
        'address' => ['type' => 'text', 'nullable' => true],
        'principal_name' => ['type' => 'string', 'nullable' => true],
        'contact' => ['type' => 'string', 'nullable' => true],
        'accreditation' => ['type' => 'enum', 'values' => ['A', 'B', 'C'], 'nullable' => true]
    ];

    protected array $seed = [
        [
            'name' => 'SMA Negeri 1 Example',
            'npsn' => '12345678',
            'address' => 'Jl. Pendidikan No. 1, Example City',
            'principal_name' => 'Dr. John Doe, M.Pd',
            'contact' => '021-1234567',
            'accreditation' => 'A'
        ],
        [
            'name' => 'SMA Negeri 2 Example',
            'npsn' => '87654321',
            'address' => 'Jl. Ilmu No. 2, Example City',
            'principal_name' => 'Dr. Jane Smith, M.Pd',
            'contact' => '021-7654321',
            'accreditation' => 'A'
        ]
    ];

    /**
     * Get all schools
     */
    public function all(): array
    {
        $stmt = $this->getDb()->prepare("SELECT * FROM {$this->table} ORDER BY name ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Find school by ID
     */
    public function find(int|string $id): ?array
    {
        $stmt = $this->getDb()->prepare("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    /**
     * Find school by NPSN
     */
    public function findByNpsn(string $npsn): ?array
    {
        $stmt = $this->getDb()->prepare("SELECT * FROM {$this->table} WHERE npsn = :npsn LIMIT 1");
        $stmt->execute(['npsn' => $npsn]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    /**
     * Create new school
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

            throw new \PDOException('Gagal membuat school');
        } catch (\PDOException $e) {
            // Check for duplicate entry (npsn already exists)
            if ($e->getCode() === '23000' || str_contains($e->getMessage(), 'Duplicate entry')) {
                throw new \Exception('NPSN sudah terdaftar');
            }
            throw $e;
        }
    }

    /**
     * Update school by ID
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
     * Delete school by ID
     */
    public function deleteById(int|string $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        return $this->getDb()->query($sql, ['id' => $id]);
    }

    /**
     * Search schools by name
     */
    public function searchByName(string $keyword): array
    {
        $stmt = $this->getDb()->prepare("
            SELECT * FROM {$this->table} 
            WHERE name LIKE :keyword 
            ORDER BY name ASC
        ");
        $stmt->execute(['keyword' => "%{$keyword}%"]);
        return $stmt->fetchAll();
    }

    /**
     * Get schools by accreditation
     */
    public function findByAccreditation(string $accreditation): array
    {
        $stmt = $this->getDb()->prepare("
            SELECT * FROM {$this->table} 
            WHERE accreditation = :accreditation 
            ORDER BY name ASC
        ");
        $stmt->execute(['accreditation' => $accreditation]);
        return $stmt->fetchAll();
    }

    /**
     * Get count of students by school
     */
    public function getStudentCount(int $schoolId): int
    {
        $stmt = $this->getDb()->prepare("
            SELECT COUNT(*) as count 
            FROM student_profiles 
            WHERE school_id = :school_id
        ");
        $stmt->execute(['school_id' => $schoolId]);
        $row = $stmt->fetch();
        return (int) ($row['count'] ?? 0);
    }

    /**
     * Get count of teachers by school
     */
    public function getTeacherCount(int $schoolId): int
    {
        $stmt = $this->getDb()->prepare("
            SELECT COUNT(*) as count 
            FROM teacher_profiles 
            WHERE school_id = :school_id
        ");
        $stmt->execute(['school_id' => $schoolId]);
        $row = $stmt->fetch();
        return (int) ($row['count'] ?? 0);
    }

    /**
     * Get school with statistics
     */
    public function findWithStats(int|string $id): ?array
    {
        $school = $this->find($id);
        if (!$school) {
            return null;
        }

        $school['student_count'] = $this->getStudentCount($id);
        $school['teacher_count'] = $this->getTeacherCount($id);

        return $school;
    }

    /**
     * Get all students from this school
     */
    public function getStudents(int $schoolId): array
    {
        $stmt = $this->getDb()->prepare("
            SELECT sp.*, p.*, u.email, u.name as user_name
            FROM student_profiles sp
            JOIN profiles p ON sp.profile_id = p.id
            JOIN users u ON p.user_id = u.id
            WHERE sp.school_id = :school_id
        ");
        $stmt->execute(['school_id' => $schoolId]);
        return $stmt->fetchAll();
    }

    /**
     * Get all teachers from this school
     */
    public function getTeachers(int $schoolId): array
    {
        $stmt = $this->getDb()->prepare("
            SELECT tp.*, p.*, u.email, u.name as user_name
            FROM teacher_profiles tp
            JOIN profiles p ON tp.profile_id = p.id
            JOIN users u ON p.user_id = u.id
            WHERE tp.school_id = :school_id
        ");
        $stmt->execute(['school_id' => $schoolId]);
        return $stmt->fetchAll();
    }

    /**
     * Check if a student belongs to this school
     */
    public function hasStudent(int $schoolId, int $studentProfileId): bool
    {
        $stmt = $this->getDb()->prepare("
            SELECT COUNT(*) as count
            FROM student_profiles
            WHERE school_id = :school_id AND id = :student_id
        ");
        $stmt->execute([
            'school_id' => $schoolId,
            'student_id' => $studentProfileId
        ]);
        $row = $stmt->fetch();
        return (int) ($row['count'] ?? 0) > 0;
    }

    /**
     * Check if a teacher belongs to this school
     */
    public function hasTeacher(int $schoolId, int $teacherProfileId): bool
    {
        $stmt = $this->getDb()->prepare("
            SELECT COUNT(*) as count
            FROM teacher_profiles
            WHERE school_id = :school_id AND id = :teacher_id
        ");
        $stmt->execute([
            'school_id' => $schoolId,
            'teacher_id' => $teacherProfileId
        ]);
        $row = $stmt->fetch();
        return (int) ($row['count'] ?? 0) > 0;
    }

    /**
     * Assign a student to this school
     */
    public function assignStudent(int $schoolId, int $studentProfileId): bool
    {
        $sql = "UPDATE student_profiles SET school_id = :school_id WHERE profile_id = :profile_id";
        return $this->getDb()->query($sql, [
            'school_id' => $schoolId,
            'profile_id' => $studentProfileId
        ]);
    }

    /**
     * Assign a teacher to this school
     */
    public function assignTeacher(int $schoolId, int $teacherProfileId): bool
    {
        $sql = "UPDATE teacher_profiles SET school_id = :school_id WHERE profile_id = :profile_id";
        return $this->getDb()->query($sql, [
            'school_id' => $schoolId,
            'profile_id' => $teacherProfileId
        ]);
    }
}
