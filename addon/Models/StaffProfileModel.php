<?php

namespace Addon\Models;

use App\Core\Database\Model;

/**
 * Staff Profile Model - Profile Spesifik untuk Staff/Super Admin (Role: super-admin)
 *
 * Fields:
 * - id: Primary key
 * - profile_id: Foreign key to profiles table (unique)
 * - employee_id: Employee ID
 * - department: Department (Engineering, Product, Operations)
 * - position: Position
 * - permissions: JSON permissions
 */
class StaffProfileModel extends Model
{
    protected ?string $connection = 'mysql';
    protected string $table = 'staff_profiles';
    protected bool $timestamps = true;

    protected array $schema = [
        'id' => ['type' => 'id', 'primary' => true, 'auto_increment' => true],
        'profile_id' => ['type' => 'bigint', 'nullable' => false, 'unique' => true, 'foreign' => 'profiles.id', 'on_delete' => 'cascade', 'unsigned' => true],
        'employee_id' => ['type' => 'string', 'nullable' => true],
        'department' => ['type' => 'string', 'nullable' => true], // Engineering, Product, Operations
        'position' => ['type' => 'string', 'nullable' => true],
        'permissions' => ['type' => 'json', 'nullable' => true] // {can_edit_users, can_view_analytics, ...}
    ];

    /**
     * Get all staff profiles
     */
    public function all(): array
    {
        $stmt = $this->getDb()->prepare("SELECT * FROM {$this->table}");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Find staff profile by ID
     */
    public function find(int|string $id): ?array
    {
        $stmt = $this->getDb()->prepare("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    /**
     * Find staff profile by profile ID
     */
    public function findByProfileId(int $profileId): ?array
    {
        $stmt = $this->getDb()->prepare("SELECT * FROM {$this->table} WHERE profile_id = :profile_id LIMIT 1");
        $stmt->execute(['profile_id' => $profileId]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    /**
     * Find staff profile by user ID (join with profiles and users)
     */
    public function findByUserId(int $userId): ?array
    {
        $stmt = $this->getDb()->prepare("
            SELECT
                sp.id as staff_profile_id,
                sp.profile_id,
                sp.user_name,
                sp.position,
                sp.department,
                sp.phone,
                sp.address,
                sp.created_at,
                sp.updated_at,
                p.id as profile_id,
                p.user_id,
                p.full_name,
                p.date_of_birth,
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
     * Create new staff profile
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

            throw new \PDOException('Gagal membuat staff profile');
        } catch (\PDOException $e) {
            // Check for duplicate entry (profile_id already exists)
            if ($e->getCode() === '23000' || str_contains($e->getMessage(), 'Duplicate entry')) {
                throw new \Exception('Staff profile untuk profile ini sudah ada');
            }
            throw $e;
        }
    }

    /**
     * Update staff profile by ID
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
     * Update staff profile by profile ID
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
     * Delete staff profile by ID
     */
    public function deleteById(int|string $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        return $this->getDb()->query($sql, ['id' => $id]);
    }

    /**
     * Delete staff profile by profile ID
     */
    public function deleteByProfileId(int $profileId): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE profile_id = :profile_id";
        return $this->getDb()->query($sql, ['profile_id' => $profileId]);
    }

    /**
     * Get all staff by department
     */
    public function findByDepartment(string $department): array
    {
        $stmt = $this->getDb()->prepare("
            SELECT sp.*, p.*, u.email, u.name as user_name
            FROM {$this->table} sp
            JOIN profiles p ON sp.profile_id = p.id
            JOIN users u ON p.user_id = u.id
            WHERE sp.department = :department
        ");
        $stmt->execute(['department' => $department]);
        return $stmt->fetchAll();
    }

    /**
     * Check if staff has specific permission
     */
    public function hasPermission(int $profileId, string $permission): bool
    {
        $staff = $this->findByProfileId($profileId);
        if (!$staff) {
            return false;
        }

        $permissions = json_decode($staff['permissions'] ?? '{}', true) ?? [];
        return $permissions[$permission] ?? false;
    }

    /**
     * Update permissions
     */
    public function updatePermissions(int $profileId, array $permissions): bool
    {
        return $this->updateByProfileId($profileId, ['permissions' => json_encode($permissions)]);
    }

    /**
     * Grant permission
     */
    public function grantPermission(int $profileId, string $permission): bool
    {
        $staff = $this->findByProfileId($profileId);
        if (!$staff) {
            return false;
        }

        $permissions = json_decode($staff['permissions'] ?? '{}', true) ?? [];
        $permissions[$permission] = true;

        return $this->updateByProfileId($profileId, ['permissions' => json_encode($permissions)]);
    }

    /**
     * Revoke permission
     */
    public function revokePermission(int $profileId, string $permission): bool
    {
        $staff = $this->findByProfileId($profileId);
        if (!$staff) {
            return false;
        }

        $permissions = json_decode($staff['permissions'] ?? '{}', true) ?? [];
        $permissions[$permission] = false;

        return $this->updateByProfileId($profileId, ['permissions' => json_encode($permissions)]);
    }

    /**
     * Create staff profile for new registration
     */
    public function createForProfile(int $profileId): int
    {
        $data = [
            'profile_id' => $profileId,
            'employee_id' => null,
            'department' => null,
            'position' => null,
            'permissions' => null
        ];

        return $this->create($data);
    }

    /**
     * Validasi data JSON sebelum insert/update
     */
    protected function validateJsonData(array $data): void
    {
        if (isset($data['permissions']) && $data['permissions'] !== null) {
            $this->validatePermissions($data['permissions']);
        }
    }

    protected function validatePermissions(string|array $json): void
    {
        $data = is_string($json) ? json_decode($json, true) : $json;
        if (is_string($json) && json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('permissions harus berupa JSON valid');
        }
        if (!is_array($data) || (!empty($data) && array_is_list($data))) {
            throw new \InvalidArgumentException('permissions harus berupa object (key-value)');
        }

        $allowedKeys = ['can_manage_users', 'can_manage_schools', 'can_view_analytics', 'can_manage_settings'];

        foreach ($data as $key => $value) {
            // 1. Validasi Key (Tolak jika tidak ada di skema)
            if (!in_array($key, $allowedKeys)) {
                throw new \InvalidArgumentException("Key permission '{$key}' tidak terdaftar dalam skema.");
            }

            // 2. Validasi Value Type
            if (!is_bool($value)) {
                throw new \InvalidArgumentException("Nilai permission untuk '{$key}' harus berupa boolean.");
            }
        }
    }
}
