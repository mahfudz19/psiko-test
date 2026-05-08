<?php

namespace Addon\Models;

use App\Core\Database\Model;
use App\Core\Database\DatabaseManager;

/**
 * ChatMessageModel - Model untuk mengelola pesan dalam sesi konsultasi chat
 * 
 * Menyimpan semua pesan (user dan assistant) dalam sesi konsultasi AI
 */
class ChatMessageModel extends Model
{
    protected ?string $connection = null;
    protected string $table = 'chat_messages';
    protected bool $timestamps = false;

    /**
     * Definisi schema tabel chat_messages
     */
    protected array $schema = [
        'id' => ['type' => 'id', 'primary' => true, 'auto_increment' => true],
        'chat_consultation_id' => ['type' => 'bigint', 'unsigned' => true, 'foreign' => 'chat_consultations.id', 'on_delete' => 'cascade'],
        'role' => ['type' => 'enum', 'values' => ['user', 'assistant'], 'nullable' => false],
        'content' => ['type' => 'text', 'nullable' => false],
        'context_data' => ['type' => 'json', 'nullable' => true],
        'created_at' => ['type' => 'timestamp', 'default' => 'CURRENT_TIMESTAMP'],
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
     * Ambil semua pesan untuk sesi konsultasi tertentu
     * 
     * @param int $chatConsultationId ID sesi konsultasi
     */
    public function getByChatId(int $chatConsultationId): array
    {
        $stmt = $this->getDb()->prepare("
            SELECT * FROM {$this->table} 
            WHERE chat_consultation_id = :chat_consultation_id 
            ORDER BY created_at ASC
        ");
        $stmt->execute(['chat_consultation_id' => $chatConsultationId]);
        return $stmt->fetchAll();
    }

    /**
     * Ambil N pesan terakhir dari sesi konsultasi
     * 
     * @param int $chatConsultationId ID sesi konsultasi
     * @param int $limit Jumlah pesan yang diambil (default: 10)
     */
    public function getLastMessages(int $chatConsultationId, int $limit = 10): array
    {
        $stmt = $this->getDb()->prepare("
            SELECT * FROM {$this->table} 
            WHERE chat_consultation_id = :chat_consultation_id 
            ORDER BY created_at DESC 
            LIMIT :limit
        ");
        $stmt->execute([
            'chat_consultation_id' => $chatConsultationId,
            'limit' => $limit
        ]);
        return array_reverse($stmt->fetchAll());
    }

    /**
     * Hitung jumlah pesan dalam sesi konsultasi
     * 
     * @param int $chatConsultationId ID sesi konsultasi
     */
    public function countByChatId(int $chatConsultationId): int
    {
        $stmt = $this->getDb()->prepare("
            SELECT COUNT(*) as count FROM {$this->table} 
            WHERE chat_consultation_id = :chat_consultation_id
        ");
        $stmt->execute(['chat_consultation_id' => $chatConsultationId]);
        $result = $stmt->fetch();
        return (int) ($result['count'] ?? 0);
    }

    /**
     * Tambahkan pesan baru ke sesi konsultasi
     * 
     * @param int $chatConsultationId ID sesi konsultasi
     * @param string $role Role pengirim ('user' atau 'assistant')
     * @param string $content Isi pesan
     * @param array|null $contextData Data konteks tambahan (opsional)
     * @return int ID pesan yang baru dibuat
     */
    public function addMessage(int $chatConsultationId, string $role, string $content, ?array $contextData = null): int
    {
        $sql = "INSERT INTO {$this->table} (chat_consultation_id, role, content, context_data) 
                VALUES (:chat_consultation_id, :role, :content, :context_data)";

        $this->getDb()->query($sql, [
            'chat_consultation_id' => $chatConsultationId,
            'role' => $role,
            'content' => $content,
            'context_data' => $contextData ? json_encode($contextData) : null,
        ]);

        return (int) $this->getDb()->lastInsertId();
    }

    /**
     * Tambahkan pesan user
     */
    public function addUserMessage(int $chatConsultationId, string $content, ?array $contextData = null): int
    {
        return $this->addMessage($chatConsultationId, 'user', $content, $contextData);
    }

    /**
     * Tambahkan pesan assistant (AI)
     */
    public function addAssistantMessage(int $chatConsultationId, string $content, ?array $contextData = null): int
    {
        return $this->addMessage($chatConsultationId, 'assistant', $content, $contextData);
    }

    /**
     * Cari pesan berdasarkan ID
     */
    public function find(string|int $id): ?array
    {
        $stmt = $this->getDb()->prepare("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        if ($row === false) {
            return null;
        }

        // Decode context_data jika ada
        if (!empty($row['context_data'])) {
            $row['context_data'] = json_decode($row['context_data'], true);
        }

        return $row;
    }

    /**
     * Hapus pesan berdasarkan ID
     */
    public function deleteById(string|int $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        return $this->getDb()->query($sql, ['id' => $id]);
    }

    /**
     * Hapus semua pesan dalam sesi konsultasi
     */
    public function deleteByChatId(int $chatConsultationId): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE chat_consultation_id = :chat_consultation_id";
        return $this->getDb()->query($sql, ['chat_consultation_id' => $chatConsultationId]);
    }
}
