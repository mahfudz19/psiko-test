<?php

namespace Addon\Models;

use App\Core\Database\Model;

/**
 * Profile Model - Base Profile untuk Semua Role
 *
 * Fields:
 * - id: Primary key
 * - user_id: Foreign key to users table (unique)
 * - phone: Phone number
 * - address: Full address
 * - birth_place: Place of birth
 * - birth_date: Date of birth
 * - gender: Gender (male/female)
 * - avatar: Profile picture URL
 * - social_media: JSON social media links
 */
class ProfileModel extends Model
{
    protected ?string $connection = 'mysql';
    protected string $table = 'profiles';
    protected bool $timestamps = true;

    protected array $schema = [
        'id' => ['type' => 'id', 'primary' => true, 'auto_increment' => true],
        'user_id' => ['type' => 'bigint', 'nullable' => false, 'unique' => true, 'foreign' => 'users.id', 'on_delete' => 'cascade', 'unsigned' => true],
        'phone' => ['type' => 'string', 'nullable' => true],
        'address' => ['type' => 'text', 'nullable' => true],
        'birth_place' => ['type' => 'string', 'nullable' => true],
        'birth_date' => ['type' => 'date', 'nullable' => true],
        'gender' => ['type' => 'enum', 'values' => ['male', 'female'], 'nullable' => true],
        'avatar' => ['type' => 'string', 'nullable' => true],
        'social_media' => ['type' => 'json', 'nullable' => true] // {facebook, instagram, twitter, linkedin}
    ];

    protected array $seed = [
        [
            'user_id' => 2,
            'phone' => '08123456789',
            'address' => 'Jl. Contoh, Kota Contoh',
            'birth_place' => 'Contoh',
            'birth_date' => '2000-01-01',
            'gender' => 'male',
            'avatar' => 'https://example.com/avatar.jpg',
            'social_media' => '{"facebook": "https://www.facebook.com/example", "instagram": "https://www.instagram.com/example"}'
        ],
        [
            'user_id' => 3,
            'phone' => '08123456789',
            'address' => 'Jl. Contoh, Kota Contoh',
            'birth_place' => 'Contoh',
            'birth_date' => '2000-01-01',
            'gender' => 'female',
            'avatar' => 'https://example.com/avatar.jpg',
            'social_media' => '{"facebook": "https://www.facebook.com/example", "instagram": "https://www.instagram.com/example"}'
        ],
        [
            'user_id' => 4,
            'phone' => '08123456789',
            'address' => 'Jl. Contoh, Kota Contoh',
            'birth_place' => 'Contoh',
            'birth_date' => '2000-01-01',
            'gender' => 'male',
            'avatar' => 'https://example.com/avatar.jpg',
            'social_media' => '{"facebook": "https://www.facebook.com/example", "instagram": "https://www.instagram.com/example"}'
        ],
        [
            'user_id' => 5,
            'phone' => '08123456789',
            'address' => 'Jl. Contoh, Kota Contoh',
            'birth_place' => 'Contoh',
            'birth_date' => '2000-01-01',
            'gender' => 'female',
            'avatar' => 'https://example.com/avatar.jpg',
            'social_media' => '{"facebook": "https://www.facebook.com/example", "instagram": "https://www.instagram.com/example"}'
        ]
    ];

    /**
     * Get all profiles
     */
    public function all(): array
    {
        $stmt = $this->getDb()->prepare("SELECT * FROM {$this->table}");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Find profile by ID
     */
    public function find(int|string $id): ?array
    {
        $stmt = $this->getDb()->prepare("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    /**
     * Find profile by user ID
     */
    public function findByUserId(int $userId): ?array
    {
        $stmt = $this->getDb()->prepare("SELECT * FROM {$this->table} WHERE user_id = :user_id LIMIT 1");
        $stmt->execute(['user_id' => $userId]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    /**
     * Create new profile
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

            throw new \PDOException('Gagal membuat profile');
        } catch (\PDOException $e) {
            // Check for duplicate entry (user_id already exists)
            if ($e->getCode() === '23000' || str_contains($e->getMessage(), 'Duplicate entry')) {
                throw new \Exception('Profile untuk user ini sudah ada');
            }
            throw $e;
        }
    }

    /**
     * Update profile by ID
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
     * Update profile by user ID
     */
    public function updateByUserId(int $userId, array $data): bool
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

        $sql = "UPDATE {$this->table} SET " . implode(', ', $setParts) . " WHERE user_id = :user_id";
        $data['user_id'] = $userId;

        return $this->getDb()->query($sql, $data);
    }

    /**
     * Delete profile by ID
     */
    public function deleteById(int|string $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        return $this->getDb()->query($sql, ['id' => $id]);
    }

    /**
     * Delete profile by user ID
     */
    public function deleteByUserId(int $userId): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE user_id = :user_id";
        return $this->getDb()->query($sql, ['user_id' => $userId]);
    }

    /**
     * Get profile with user data
     */
    public function findWithUser(int|string $id): ?array
    {
        $stmt = $this->getDb()->prepare("
            SELECT p.*, u.email, u.name as user_name, u.role, u.is_active
            FROM {$this->table} p
            JOIN users u ON p.user_id = u.id
            WHERE p.id = :id
            LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    /**
     * Get profile by user ID with user data
     */
    public function findWithUserByUserId(int $userId): ?array
    {
        $stmt = $this->getDb()->prepare("
            SELECT p.*, u.email, u.name as user_name, u.role, u.is_active
            FROM {$this->table} p
            JOIN users u ON p.user_id = u.id
            WHERE p.user_id = :user_id
            LIMIT 1
        ");
        $stmt->execute(['user_id' => $userId]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    /**
     * Create profile for new user (auto-called after user registration)
     */
    public function createForUser(int $userId, string $role): int
    {
        $data = [
            'user_id' => $userId,
            'phone' => null,
            'address' => null,
            'birth_place' => null,
            'birth_date' => null,
            'gender' => null,
            'avatar' => null,
            'social_media' => null
        ];

        return $this->create($data);
    }
}
