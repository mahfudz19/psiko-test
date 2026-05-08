<?php

namespace Addon\Models;

use App\Core\Database\Model;
use App\Core\Database\DatabaseManager;

/**
 * ChatConsultationModel - Model untuk mengelola sesi konsultasi chat siswa dengan AI
 * 
 * Menyimpan riwayat sesi konsultasi siswa terkait potensi, minat, dan bakat
 */
class ChatConsultationModel extends Model
{
    protected ?string $connection = null;
    protected string $table = 'chat_consultations';
    protected bool $timestamps = true;

    /**
     * Definisi schema tabel chat_consultations
     */
    protected array $schema = [
        'id' => ['type' => 'id', 'primary' => true, 'auto_increment' => true],
        'student_profile_id' => ['type' => 'bigint', 'unsigned' => true, 'foreign' => 'student_profiles.id', 'on_delete' => 'cascade'],
        'session_id' => ['type' => 'varchar', 'length' => 100, 'nullable' => false, 'index' => true],
        'topic' => ['type' => 'varchar', 'length' => 100, 'default' => 'potential_analysis'],
        'created_at' => ['type' => 'timestamp', 'default' => 'CURRENT_TIMESTAMP'],
        'updated_at' => ['type' => 'timestamp', 'default' => 'CURRENT_TIMESTAMP', 'on_update' => 'CURRENT_TIMESTAMP'],
    ];

    protected array $seed = [];

    /**
     * Constructor dengan dependency injection
     */
    public function __construct(DatabaseManager $dbManager)
    {
        parent::__construct($dbManager);
    }

    /**
     * Ambil semua sesi konsultasi
     */
    public function all(): array
    {
        $stmt = $this->getDb()->prepare("SELECT * FROM {$this->table} ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Cari sesi konsultasi berdasarkan ID
     */
    public function find(string|int $id): ?array
    {
        $stmt = $this->getDb()->prepare("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    /**
     * Ambil semua sesi konsultasi untuk siswa tertentu
     * 
     * @param int $studentProfileId ID profil siswa
     */
    public function getByStudentId(int $studentProfileId): array
    {
        $stmt = $this->getDb()->prepare("
            SELECT * FROM {$this->table} 
            WHERE student_profile_id = :student_profile_id 
            ORDER BY created_at DESC
        ");
        $stmt->execute(['student_profile_id' => $studentProfileId]);
        return $stmt->fetchAll();
    }

    /**
     * Cari sesi konsultasi berdasarkan session_id
     * 
     * @param string $sessionId ID sesi unik
     */
    public function findBySessionId(string $sessionId): ?array
    {
        $stmt = $this->getDb()->prepare("SELECT * FROM {$this->table} WHERE session_id = :session_id LIMIT 1");
        $stmt->execute(['session_id' => $sessionId]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    /**
     * Buat sesi konsultasi baru
     * 
     * @param array $data Data sesi (student_profile_id, session_id, topic)
     * @return int ID sesi yang baru dibuat
     */
    public function createWithSessionId(array $data): int
    {
        $sql = "INSERT INTO {$this->table} (student_profile_id, session_id, topic) 
                VALUES (:student_profile_id, :session_id, :topic)";

        $this->getDb()->query($sql, [
            'student_profile_id' => $data['student_profile_id'],
            'session_id' => $data['session_id'],
            'topic' => $data['topic'] ?? 'potential_analysis',
        ]);

        return (int) $this->getDb()->lastInsertId();
    }

    /**
     * Hapus sesi konsultasi berdasarkan ID
     */
    public function deleteById(string|int $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        return $this->getDb()->query($sql, ['id' => $id]);
    }

    /**
     * Hapus sesi konsultasi berdasarkan session_id
     */
    public function deleteBySessionId(string $sessionId): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE session_id = :session_id";
        return $this->getDb()->query($sql, ['session_id' => $sessionId]);
    }

    /**
     * Update topic sesi konsultasi
     */
    public function updateTopic(string|int $id, string $topic): bool
    {
        $sql = "UPDATE {$this->table} SET topic = :topic WHERE id = :id";
        return $this->getDb()->query($sql, ['id' => $id, 'topic' => $topic]);
    }
}
