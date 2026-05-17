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
 *
 * Note: Konfigurasi tes default disimpan di school_config_mappings dengan flag is_default = TRUE
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
            'name' => 'SMAN 1 Makassar',
            'npsn' => '40312010',
            'address' => 'JL. GUNUNG BAWAKARAENG NO. 53, Gaddong, Kec. Bontoala, Kota Makassar',
            'principal_name' => 'Sulihin',
            'contact' => '0411-3613670',
            'accreditation' => 'A'
        ],
        [
            'name' => 'SMAN 2 Makassar',
            'npsn' => '40311889',
            'address' => 'JL. BAJI GAU NO. 17, Baji Mappakasunggu, Kec. Mamajang, Kota Makassar',
            'principal_name' => 'Hj. Sitti Khadijah',
            'contact' => '0411-852963',
            'accreditation' => 'A'
        ],
        [
            'name' => 'SMAN 3 Makassar',
            'npsn' => '40311891',
            'address' => 'JL. BAJI ARENG NO. 18, Baji Mappakasunggu, Kec. Mamajang, Kota Makassar',
            'principal_name' => 'Nasriadi',
            'contact' => '0411-852964',
            'accreditation' => 'A'
        ],
        [
            'name' => 'SMAN 4 Makassar',
            'npsn' => '40311892',
            'address' => 'JL. CAKALANG NO. 3, Totaka, Kec. Ujung Tanah, Kota Makassar',
            'principal_name' => 'Supardin',
            'contact' => '0411-3615467',
            'accreditation' => 'A'
        ],
        [
            'name' => 'SMAN 5 Makassar',
            'npsn' => '40307392',
            'address' => 'JL. TAMAN MAKAM PAHLAWAN, Tello Baru, Kec. Panakkukang, Kota Makassar',
            'principal_name' => 'Sudirman',
            'contact' => '0411-449174',
            'accreditation' => 'A'
        ],
        [
            'name' => 'SMAN 6 Makassar',
            'npsn' => '40311893',
            'address' => 'JL. PROF. DR. IR. SUTAMI, NO.4, Bira, Kec. Tamalanrea, Kota Makassar',
            'principal_name' => 'Samsuddin',
            'contact' => '0411-510123',
            'accreditation' => 'A'
        ],
        [
            'name' => 'SMAN 7 Makassar',
            'npsn' => '40311894',
            'address' => 'JL. PERINTIS KEMERDEKAAN KOMP 18, Sudiang, Kec. Biringkanaya, Kota Makassar',
            'principal_name' => 'Anwar',
            'contact' => '0411-510456',
            'accreditation' => 'A'
        ],
        [
            'name' => 'SMAN 8 Makassar',
            'npsn' => '40314020',
            'address' => 'JL. ANDI MANGERANGI II NO.24, Bongaya, Kec. Tamalate, Kota Makassar',
            'principal_name' => 'Ruslan',
            'contact' => '0411-872123',
            'accreditation' => 'A'
        ],
        [
            'name' => 'SMAN 9 Makassar',
            'npsn' => '40311896',
            'address' => 'JL. KARUNRUNG RAYA NO.37, Karunrung, Kec. Rappocini, Kota Makassar',
            'principal_name' => 'Muh. Asrar',
            'contact' => '0411-861123',
            'accreditation' => 'A'
        ],
        [
            'name' => 'SMAN 10 Makassar',
            'npsn' => '40311885',
            'address' => 'JL. TAMANGAPA V NO.12, Tamangapa, Kec. Manggala, Kota Makassar',
            'principal_name' => 'Andi Umar',
            'contact' => '0411-491123',
            'accreditation' => 'A'
        ],
        [
            'name' => 'SMAN 11 Makassar',
            'npsn' => '40307375',
            'address' => 'JL. LETJEN POL. MAPPAOUDANG NO. 66, Bongaya, Kec. Tamalate, Kota Makassar',
            'principal_name' => 'Masita',
            'contact' => '0411-872456',
            'accreditation' => 'A'
        ],
        [
            'name' => 'SMAN 12 Makassar',
            'npsn' => '40312013',
            'address' => 'JL. MOHA LASULORO 57 ANTANG, Antang, Kec. Manggala, Kota Makassar',
            'principal_name' => 'Hamzah',
            'contact' => '0411-492123',
            'accreditation' => 'A'
        ],
        [
            'name' => 'SMAN 13 Makassar',
            'npsn' => '40312014',
            'address' => 'JL. TAMANGAPA RAYA III NO.37, Bangkala, Kec. Manggala, Kota Makassar',
            'principal_name' => 'Nursiah',
            'contact' => '0411-493123',
            'accreditation' => 'A'
        ],
        [
            'name' => 'SMAN 14 Makassar',
            'npsn' => '40311948',
            'address' => 'JL. BAJIMINASA NO.9, Tamarunang, Kec. Mariso, Kota Makassar',
            'principal_name' => 'Hj. Nurhidayah Masri',
            'contact' => '0411-872433',
            'accreditation' => 'A'
        ],
        [
            'name' => 'SMAN 15 Makassar',
            'npsn' => '40311949',
            'address' => 'JL. PROF. DR. IR. SUTAMI, Bulurokeng, Kec. Biringkanaya, Kota Makassar',
            'principal_name' => 'Bunyamin',
            'contact' => '0411-511123',
            'accreditation' => 'A'
        ],
        [
            'name' => 'SMAN 16 Makassar',
            'npsn' => '40311950',
            'address' => 'JL. AMANAGAPPA NO. 8, Baru, Kec. Ujung Pandang, Kota Makassar',
            'principal_name' => 'Yusuf',
            'contact' => '0411-361123',
            'accreditation' => 'A'
        ],
        [
            'name' => 'SMAN 17 Makassar',
            'npsn' => '40311951',
            'address' => 'JL. SUNU NO. 11, Suwangga, Kec. Tallo, Kota Makassar',
            'principal_name' => 'Asmar Achmad, S.Pd',
            'contact' => '0411-449175',
            'accreditation' => 'A'
        ],
        [
            'name' => 'SMAN 18 Makassar',
            'npsn' => '40311952',
            'address' => 'KOMP. MANGGA TIGA PERMAI, Paccerakang, Kec. Biringkanaya, Kota Makassar',
            'principal_name' => 'Muh. Aras',
            'contact' => '0411-512123',
            'accreditation' => 'A'
        ],
        [
            'name' => 'SMAN 19 Makassar',
            'npsn' => '40307376',
            'address' => 'JL. INSPEKSI PAM TIMUR NO. 19, Manggala, Kec. Manggala, Kota Makassar',
            'principal_name' => 'Muh. Tahir',
            'contact' => '0411-494123',
            'accreditation' => 'A'
        ],
        [
            'name' => 'SMAN 20 Makassar',
            'npsn' => '40307377',
            'address' => 'JL. BONTO BIRAENG, Barombong, Kec. Tamalate, Kota Makassar',
            'principal_name' => 'Mirdan Midding',
            'contact' => '0411-873123',
            'accreditation' => 'A'
        ],
        [
            'name' => 'SMAN 21 Makassar',
            'npsn' => '40311953',
            'address' => 'JL. TAMALANREA RAYA NO. 1A BTP, Tamalanrea, Kec. Tamalanrea, Kota Makassar',
            'principal_name' => 'Andi Erna',
            'contact' => '0411-513123',
            'accreditation' => 'A'
        ],
        [
            'name' => 'SMAN 22 Makassar',
            'npsn' => '40310219',
            'address' => 'JL. PAJJAIANG KOMP. KOR/KNPI SUDIANG, Laikang, Kec. Biringkanaya, Kota Makassar',
            'principal_name' => 'Suhardi',
            'contact' => '0411-514123',
            'accreditation' => 'A'
        ],
        [
            'name' => 'SMA Katolik Rajawali',
            'npsn' => '40307369',
            'address' => 'JL. LAMADUKELLENG NO. 07, Losari, Kec. Ujung Pandang, Kota Makassar',
            'principal_name' => 'Sr. Paulina',
            'contact' => '0411-3613259',
            'accreditation' => 'A'
        ],
        [
            'name' => 'SMA Islam Athirah',
            'npsn' => '40310213',
            'address' => 'JL. KAJAOLALIDO NO. 22, Baru, Kec. Ujung Pandang, Kota Makassar',
            'principal_name' => 'Tawakkal Kahar',
            'contact' => '0411-3624571',
            'accreditation' => 'A'
        ],
        [
            'name' => 'SMA Dian Harapan',
            'npsn' => '40310211',
            'address' => 'JL. GUNUNG AGUNG NO. 201, TANJUNG BUNGA, Tanjung Merdeka, Kec. Tamalate, Kota Makassar',
            'principal_name' => 'Yuliana',
            'contact' => '0411-8113710',
            'accreditation' => 'A'
        ],
        [
            'name' => 'SMA Nasional Makassar',
            'npsn' => '40311947',
            'address' => 'JL. DR. RATULANGI NO. 84, Mario, Kec. Mariso, Kota Makassar',
            'principal_name' => 'Hj. Andi Wahyuni',
            'contact' => '0411-872841',
            'accreditation' => 'A'
        ],
        [
            'name' => 'SMA Katolik Cenderawasih',
            'npsn' => '40311938',
            'address' => 'JL. OPU DAENG RISADJU NO. 61, Kunjung Mae, Kec. Mariso, Kota Makassar',
            'principal_name' => 'Sr. Maria',
            'contact' => '0411-873344',
            'accreditation' => 'A'
        ],
        [
            'name' => 'SMP Negeri 1 Makassar',
            'npsn' => '40313125',
            'address' => 'JL. BAJI ARENG NO. 17, Baji Mappakasunggu, Kec. Mamajang, Kota Makassar',
            'principal_name' => 'Suaib Ramli',
            'contact' => '0411-852034',
            'accreditation' => 'A'
        ],
        [
            'name' => 'SMP Negeri 3 Makassar',
            'npsn' => '40312436',
            'address' => 'JL. BAJI GAU NO. 11, Baji Mappakasunggu, Kec. Mamajang, Kota Makassar',
            'principal_name' => 'Kasman',
            'contact' => '0411-852035',
            'accreditation' => 'A'
        ],
        [
            'name' => 'SMP Negeri 6 Makassar',
            'npsn' => '40312441',
            'address' => 'JL. AHMAD YANI NO. 25, Baru, Kec. Ujung Pandang, Kota Makassar',
            'principal_name' => 'Munir',
            'contact' => '0411-3613654',
            'accreditation' => 'A'
        ],
        [
            'name' => 'SMP Telkom Makassar',
            'npsn' => '69938800',
            'address' => 'JL. ANDI PANGERAN PETTARANI NO. 4, Gunung Sari, Kec. Rappocini, Kota Makassar',
            'principal_name' => 'Muh. Idrus',
            'contact' => '0411-835566',
            'accreditation' => 'A'
        ],
        [
            'name' => 'SMP Islam Athirah Makassar',
            'npsn' => '40310198',
            'address' => 'JL. KAJAOLALIDO NO. 22, Baru, Kec. Ujung Pandang, Kota Makassar',
            'principal_name' => 'Nilam',
            'contact' => '0411-3624571',
            'accreditation' => 'A'
        ],
        [
            'name' => 'SMP Katolik Rajawali',
            'npsn' => '40307304',
            'address' => 'JL. ARIF RATE NO. 2, Losari, Kec. Ujung Pandang, Kota Makassar',
            'principal_name' => 'Sr. Maria',
            'contact' => '0411-3613259',
            'accreditation' => 'A'
        ],
        [
            'name' => 'SMP Negeri 2 Makassar',
            'npsn' => '40312434',
            'address' => 'JL. AMANAGAPPA NO. 4, Baru, Kec. Ujung Pandang, Kota Makassar',
            'principal_name' => 'Hj. Mercy',
            'contact' => '0411-3611234',
            'accreditation' => 'A'
        ],
        [
            'name' => 'SMP Negeri 12 Makassar',
            'npsn' => '40312428',
            'address' => 'JL. ABDULLAH DG. SIRUA NO. 132, Batua, Kec. Manggala, Kota Makassar',
            'principal_name' => 'Laode',
            'contact' => '0411-491234',
            'accreditation' => 'A'
        ],
        [
            'name' => 'SMP Negeri 18 Makassar',
            'npsn' => '40307323',
            'address' => 'JL. DAENG TATA BTN HARTACO, Barombong, Kec. Tamalate, Kota Makassar',
            'principal_name' => 'Muh. Guntur',
            'contact' => '0411-871234',
            'accreditation' => 'A'
        ],
        [
            'name' => 'SMP Negeri 24 Makassar',
            'npsn' => '40312432',
            'address' => 'JL. BTP BLOK M, Tamalanrea, Kec. Tamalanrea, Kota Makassar',
            'principal_name' => 'Hj. Rosdiana',
            'contact' => '0411-511234',
            'accreditation' => 'A'
        ],
        [
            'name' => 'SMP Negeri 30 Makassar',
            'npsn' => '40312438',
            'address' => 'JL. BTP BLOK AF, Tamalanrea, Kec. Tamalanrea, Kota Makassar',
            'principal_name' => 'Hj. Hijrah',
            'contact' => '0411-512234',
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

    /**
     * Get school with its default test configuration mapping
     */
    /**
     * @deprecated Use SchoolConfigMappingModel::getDefaultConfig() instead
     */
    public function findWithConfig(int|string $id): ?array
    {
        trigger_error('findWithConfig() is deprecated. Use SchoolConfigMappingModel::getDefaultConfig() instead.', E_USER_DEPRECATED);
        return $this->find($id);
    }

    /**
     * @deprecated Use SchoolConfigMappingModel::assignConfig() with is_default flag instead
     */
    public function setDefaultConfig(int $schoolId, int $mappingId): bool
    {
        trigger_error('setDefaultConfig() is deprecated. Use SchoolConfigMappingModel::assignConfig() with is_default flag instead.', E_USER_DEPRECATED);
        return false;
    }

    /**
     * Get all test configurations assigned to this school
     * Uses SchoolConfigMappingModel for many-to-many relationships
     */
    public function getConfigs(int $schoolId): array
    {
        $stmt = $this->getDb()->prepare("
            SELECT scm.*, tc.name as config_name, tc.test_type, tc.dimensions, tc.scoring_rules
            FROM school_config_mappings scm
            JOIN test_configurations tc ON scm.config_id = tc.id
            WHERE scm.school_id = :school_id
            ORDER BY scm.is_default DESC, tc.name ASC
        ");
        $stmt->execute(['school_id' => $schoolId]);
        return $stmt->fetchAll();
    }

    /**
     * Get default test configuration for this school
     */
    public function getDefaultConfig(int $schoolId): ?array
    {
        $stmt = $this->getDb()->prepare("
            SELECT scm.*, tc.name as config_name, tc.test_type, tc.dimensions, tc.scoring_rules
            FROM school_config_mappings scm
            JOIN test_configurations tc ON scm.config_id = tc.id
            WHERE scm.school_id = :school_id AND scm.is_default = TRUE
            LIMIT 1
        ");
        $stmt->execute(['school_id' => $schoolId]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    /**
     * Assign a test configuration to this school
     */
    public function assignConfig(int $schoolId, int $configId, bool $isDefault = false): int
    {
        // If setting as default, unset other defaults
        if ($isDefault) {
            $this->getDb()->query(
                "UPDATE school_config_mappings SET is_default = FALSE WHERE school_id = :school_id",
                ['school_id' => $schoolId]
            );
        }

        $stmt = $this->getDb()->prepare("
            INSERT INTO school_config_mappings (school_id, config_id, is_default, created_at)
            VALUES (:school_id, :config_id, :is_default, NOW())
            ON DUPLICATE KEY UPDATE is_default = :is_default
        ");
        $stmt->execute([
            'school_id' => $schoolId,
            'config_id' => $configId,
            'is_default' => $isDefault
        ]);

        $mappingId = (int) $this->getDb()->lastInsertId();

        // Note: Default configuration is now tracked via is_default flag in school_config_mappings
        // No need to update schools table

        return $mappingId;
    }

    /**
     * Remove a test configuration from this school
     */
    public function removeConfig(int $schoolId, int $configId): bool
    {
        return $this->getDb()->query(
            "DELETE FROM school_config_mappings WHERE school_id = :school_id AND config_id = :config_id",
            ['school_id' => $schoolId, 'config_id' => $configId]
        );
    }

    /**
     * Check if school has a specific configuration
     */
    public function hasConfig(int $schoolId, int $configId): bool
    {
        $stmt = $this->getDb()->prepare("
            SELECT COUNT(*) FROM school_config_mappings
            WHERE school_id = :school_id AND config_id = :config_id
        ");
        $stmt->execute([
            'school_id' => $schoolId,
            'config_id' => $configId
        ]);
        return (int) $stmt->fetchColumn() > 0;
    }
}
