<?php

namespace Addon\Models;

use App\Core\Database\Model;

/**
 * Test Statement Model - Bank Pernyataan/Tes
 *
 * Model ini mengelola bank pernyataan (butir soal) untuk tes psikologi.
 * Setiap konfigurasi tes dapat memiliki pernyataan yang berbeda jumlahnya.
 * Pernyataan terikat pada konfigurasi (test_configurations) yang reusable.
 *
 * Fields:
 * - id: Primary key
 * - config_id: Foreign key ke test_configurations.id
 * - dimension: Dimensi pernyataan (R, I, A, S, E, C)
 * - statement_text: Teks pernyataan
 * - display_order: Urutan tampilan
 * - is_active: Status pernyataan
 */
class TestStatementModel extends Model
{
    protected ?string $connection = 'mysql';
    protected string $table = 'test_statements';
    protected bool $timestamps = true;

    protected array $schema = [
        'id' => ['type' => 'id', 'primary' => true, 'auto_increment' => true],
        'config_id' => ['type' => 'bigint', 'nullable' => false, 'foreign' => 'test_configurations.id', 'on_delete' => 'cascade', 'unsigned' => true],
        'dimension' => ['type' => 'enum', 'values' => ['R', 'I', 'A', 'S', 'E', 'C'], 'nullable' => false],
        'statement_text' => ['type' => 'text', 'nullable' => false],
        'display_order' => ['type' => 'int', 'nullable' => false],
        'is_active' => ['type' => 'boolean', 'nullable' => false, 'default' => true]
    ];

    /**
     * Seed data for 42 RIASEC statements (allocated to config_id = 1 "RIASEC Standar 42 Butir")
     * Note: Butir #38 dialokasikan ke Conventional (C) saja, dengan butir alternatif untuk Social (S)
     */
    protected array $seed = [
        // Realistic (R) - 7 butir
        ['config_id' => 1, 'dimension' => 'R', 'statement_text' => 'Aku suka mengulik peralatan', 'display_order' => 1, 'is_active' => true],
        ['config_id' => 1, 'dimension' => 'R', 'statement_text' => 'Aku suka bekerja mandiri (dengan tangan/alat)', 'display_order' => 2, 'is_active' => true],
        ['config_id' => 1, 'dimension' => 'R', 'statement_text' => 'Aku suka menyusun balok / LEGO', 'display_order' => 3, 'is_active' => true],
        ['config_id' => 1, 'dimension' => 'R', 'statement_text' => 'Aku suka memelihara binatang', 'display_order' => 4, 'is_active' => true],
        ['config_id' => 1, 'dimension' => 'R', 'statement_text' => 'Aku suka mencari tahu cara kerja sebuah alat', 'display_order' => 5, 'is_active' => true],
        ['config_id' => 1, 'dimension' => 'R', 'statement_text' => 'Aku suka merangkaikan atau merakit benda', 'display_order' => 6, 'is_active' => true],
        ['config_id' => 1, 'dimension' => 'R', 'statement_text' => 'Aku suka berkegiatan di luar ruangan', 'display_order' => 7, 'is_active' => true],

        // Investigative (I) - 7 butir
        ['config_id' => 1, 'dimension' => 'I', 'statement_text' => 'Aku suka mengerjakan puzzle', 'display_order' => 8, 'is_active' => true],
        ['config_id' => 1, 'dimension' => 'I', 'statement_text' => 'Aku suka melakukan percobaan / eksperimen', 'display_order' => 9, 'is_active' => true],
        ['config_id' => 1, 'dimension' => 'I', 'statement_text' => 'Aku suka sains', 'display_order' => 10, 'is_active' => true],
        ['config_id' => 1, 'dimension' => 'I', 'statement_text' => 'Aku suka mendapatkan tantangan baru', 'display_order' => 11, 'is_active' => true],
        ['config_id' => 1, 'dimension' => 'I', 'statement_text' => 'Aku suka mencari tahu penyebab suatu kejadian', 'display_order' => 12, 'is_active' => true],
        ['config_id' => 1, 'dimension' => 'I', 'statement_text' => 'Aku suka mempraktikkan hal-hal yang aku pelajari', 'display_order' => 13, 'is_active' => true],
        ['config_id' => 1, 'dimension' => 'I', 'statement_text' => 'Aku suka mengerjakan soal matematika atau grafik', 'display_order' => 14, 'is_active' => true],

        // Artistic (A) - 7 butir
        ['config_id' => 1, 'dimension' => 'A', 'statement_text' => 'Aku suka membaca buku tentang seni dan musik', 'display_order' => 15, 'is_active' => true],
        ['config_id' => 1, 'dimension' => 'A', 'statement_text' => 'Aku suka membuat karya berbentuk tulisan', 'display_order' => 16, 'is_active' => true],
        ['config_id' => 1, 'dimension' => 'A', 'statement_text' => 'Aku suka menghibur teman', 'display_order' => 17, 'is_active' => true],
        ['config_id' => 1, 'dimension' => 'A', 'statement_text' => 'Aku adalah orang yang kreatif', 'display_order' => 18, 'is_active' => true],
        ['config_id' => 1, 'dimension' => 'A', 'statement_text' => 'Aku suka memainkan alat musik atau bernyanyi', 'display_order' => 19, 'is_active' => true],
        ['config_id' => 1, 'dimension' => 'A', 'statement_text' => 'Aku suka bermain peran / drama', 'display_order' => 20, 'is_active' => true],
        ['config_id' => 1, 'dimension' => 'A', 'statement_text' => 'Aku suka menggambar', 'display_order' => 21, 'is_active' => true],

        // Social (S) - 7 butir (butir #38 diganti dengan alternatif yang lebih relevan)
        ['config_id' => 1, 'dimension' => 'S', 'statement_text' => 'Aku suka bekerja dalam kelompok', 'display_order' => 22, 'is_active' => true],
        ['config_id' => 1, 'dimension' => 'S', 'statement_text' => 'Aku suka menjelaskan sesuatu kepada teman', 'display_order' => 23, 'is_active' => true],
        ['config_id' => 1, 'dimension' => 'S', 'statement_text' => 'Aku suka membantu orang lain memecahkan persoalan', 'display_order' => 24, 'is_active' => true],
        ['config_id' => 1, 'dimension' => 'S', 'statement_text' => 'Aku suka mempelajari budaya berbagai daerah', 'display_order' => 25, 'is_active' => true],
        ['config_id' => 1, 'dimension' => 'S', 'statement_text' => 'Aku suka mendiskusikan hal-hal yang terjadi di sekitarku', 'display_order' => 26, 'is_active' => true],
        ['config_id' => 1, 'dimension' => 'S', 'statement_text' => 'Aku suka mendengarkan curhatan teman', 'display_order' => 27, 'is_active' => true],
        ['config_id' => 1, 'dimension' => 'S', 'statement_text' => 'Aku suka menolong orang', 'display_order' => 28, 'is_active' => true],

        // Enterprising (E) - 7 butir
        ['config_id' => 1, 'dimension' => 'E', 'statement_text' => 'Aku suka membuat target untuk diriku sendiri', 'display_order' => 29, 'is_active' => true],
        ['config_id' => 1, 'dimension' => 'E', 'statement_text' => 'Aku suka meyakinkan teman untuk mengikuti caraku', 'display_order' => 30, 'is_active' => true],
        ['config_id' => 1, 'dimension' => 'E', 'statement_text' => 'Aku tidak berkeberatan bekerja melebihi waktu yang ditentukan', 'display_order' => 31, 'is_active' => true],
        ['config_id' => 1, 'dimension' => 'E', 'statement_text' => 'Aku suka menjual sesuatu', 'display_order' => 32, 'is_active' => true],
        ['config_id' => 1, 'dimension' => 'E', 'statement_text' => 'Aku ingin membuka usaha sendiri suatu saat nanti', 'display_order' => 33, 'is_active' => true],
        ['config_id' => 1, 'dimension' => 'E', 'statement_text' => 'Aku suka memimpin kelompok atau kelas', 'display_order' => 34, 'is_active' => true],
        ['config_id' => 1, 'dimension' => 'E', 'statement_text' => 'Aku suka berbicara di depan umum', 'display_order' => 35, 'is_active' => true],

        // Conventional (C) - 7 butir (butir #38 dialokasikan ke C)
        ['config_id' => 1, 'dimension' => 'C', 'statement_text' => 'Aku suka merapikan barang-barang (buku, alat tulis, kamar)', 'display_order' => 36, 'is_active' => true],
        ['config_id' => 1, 'dimension' => 'C', 'statement_text' => 'Aku suka mengerjakan hal-hal dengan instruksi yang jelas', 'display_order' => 37, 'is_active' => true],
        ['config_id' => 1, 'dimension' => 'C', 'statement_text' => 'Aku suka memperhatikan detail', 'display_order' => 38, 'is_active' => true],
        ['config_id' => 1, 'dimension' => 'C', 'statement_text' => 'Aku suka merapikan catatan atau LKS', 'display_order' => 39, 'is_active' => true],
        ['config_id' => 1, 'dimension' => 'C', 'statement_text' => 'Aku suka merapikan kamarku', 'display_order' => 40, 'is_active' => true],
        ['config_id' => 1, 'dimension' => 'C', 'statement_text' => 'Aku suka berkegiatan di dalam ruangan dengan meja-kursi', 'display_order' => 41, 'is_active' => true],
        ['config_id' => 1, 'dimension' => 'C', 'statement_text' => 'Aku suka menghitung', 'display_order' => 42, 'is_active' => true],
    ];

    /**
     * Get all statements for a configuration
     */
    public function getByConfigId(int $configId): array
    {
        $stmt = $this->getDb()->prepare("
            SELECT * FROM {$this->table}
            WHERE config_id = :config_id AND is_active = TRUE
            ORDER BY display_order ASC
        ");
        $stmt->execute(['config_id' => $configId]);
        return $stmt->fetchAll();
    }

    /**
     * Get statements by dimension for a configuration
     */
    public function getByDimension(int $configId, string $dimension): array
    {
        $stmt = $this->getDb()->prepare("
            SELECT * FROM {$this->table}
            WHERE config_id = :config_id AND dimension = :dimension AND is_active = TRUE
            ORDER BY display_order ASC
        ");
        $stmt->execute(['config_id' => $configId, 'dimension' => $dimension]);
        return $stmt->fetchAll();
    }

    /**
     * Get all statements grouped by dimension
     */
    public function getGroupedByDimension(int $configId): array
    {
        $statements = $this->getByConfigId($configId);
        $grouped = [];

        foreach ($statements as $statement) {
            $grouped[$statement['dimension']][] = $statement;
        }

        return $grouped;
    }

    /**
     * Find statement by ID
     */
    public function find(int|string $id): ?array
    {
        $stmt = $this->getDb()->prepare("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    /**
     * Create new statement
     */
    public function create(array $data): int
    {
        $validData = [];
        foreach ($data as $key => $value) {
            if (isset($this->schema[$key]) && $key !== 'id') {
                $validData[$key] = $value;
            }
        }

        $columns = implode(', ', array_keys($validData));
        $placeholders = ':' . implode(', :', array_keys($validData));

        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";

        if ($this->getDb()->query($sql, $validData)) {
            return (int) $this->getDb()->lastInsertId();
        }

        throw new \PDOException('Gagal membuat test statement');
    }

    /**
     * Create multiple statements at once (bulk insert)
     */
    public function createMany(array $statements): int
    {
        if (empty($statements)) {
            return 0;
        }

        $columns = ['config_id', 'dimension', 'statement_text', 'display_order', 'is_active'];
        $values = [];
        $params = [];

        foreach ($statements as $index => $stmt) {
            $values[] = "(:config_id_{$index}, :dimension_{$index}, :statement_text_{$index}, :display_order_{$index}, :is_active_{$index})";
            $params["config_id_{$index}"] = $stmt['config_id'];
            $params["dimension_{$index}"] = $stmt['dimension'];
            $params["statement_text_{$index}"] = $stmt['statement_text'];
            $params["display_order_{$index}"] = $stmt['display_order'];
            $params["is_active_{$index}"] = $stmt['is_active'] ?? true;
        }

        $sql = "INSERT INTO {$this->table} (" . implode(', ', $columns) . ") VALUES " . implode(', ', $values);

        if ($this->getDb()->query($sql, $params)) {
            return (int) $this->getDb()->lastInsertId();
        }

        throw new \PDOException('Gagal membuat multiple test statements');
    }

    /**
     * Update statement by ID
     */
    public function updateById(int|string $id, array $data): bool
    {
        if (empty($data)) {
            return false;
        }

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
     * Activate a statement
     */
    public function activate(int $id): bool
    {
        return $this->getDb()->query(
            "UPDATE {$this->table} SET is_active = TRUE, updated_at = NOW() WHERE id = :id",
            ['id' => $id]
        );
    }

    /**
     * Deactivate a statement
     */
    public function deactivate(int $id): bool
    {
        return $this->getDb()->query(
            "UPDATE {$this->table} SET is_active = FALSE, updated_at = NOW() WHERE id = :id",
            ['id' => $id]
        );
    }

    /**
     * Delete statement by ID
     */
    public function deleteById(int|string $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        return $this->getDb()->query($sql, ['id' => $id]);
    }

    /**
     * Delete all statements for a configuration
     */
    public function deleteByConfigId(int $configId): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE config_id = :config_id";
        return $this->getDb()->query($sql, ['config_id' => $configId]);
    }

    /**
     * Get statement count by dimension
     */
    public function countByDimension(int $configId, string $dimension): int
    {
        $stmt = $this->getDb()->prepare("
            SELECT COUNT(*) FROM {$this->table}
            WHERE config_id = :config_id AND dimension = :dimension AND is_active = TRUE
        ");
        $stmt->execute(['config_id' => $configId, 'dimension' => $dimension]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Get total statement count for a configuration
     */
    public function countByConfigId(int $configId): int
    {
        $stmt = $this->getDb()->prepare("
            SELECT COUNT(*) FROM {$this->table}
            WHERE config_id = :config_id AND is_active = TRUE
        ");
        $stmt->execute(['config_id' => $configId]);
        return (int) $stmt->fetchColumn();
    }
}
