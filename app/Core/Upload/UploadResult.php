<?php

namespace App\Core\Upload;

/**
 * UploadResult - Object hasil dari proses upload
 * 
 * Class ini membungkus hasil operasi upload termasuk status sukses/gagal,
 * path file, URL publik, error messages, dan metadata file.
 */
class UploadResult
{
    /**
     * Constructor untuk UploadResult
     * 
     * @param bool $success Status keberhasilan upload
     * @param string|null $filePath Path absolut file yang diupload
     * @param string|null $publicUrl URL publik untuk mengakses file
     * @param array $errors Array pesan error jika upload gagal
     * @param array $metadata Metadata file (ukuran, dimensi, dll)
     */
    public function __construct(
        public bool $success = false,
        public ?string $filePath = null,
        public ?string $publicUrl = null,
        public array $errors = [],
        public array $metadata = []
    ) {}

    /**
     * Buat UploadResult untuk operasi sukses
     * 
     * @param string $filePath Path absolut file
     * @param string $publicUrl URL publik file
     * @param array $metadata Metadata file
     * @return self
     */
    public static function success(string $filePath, string $publicUrl, array $metadata = []): self
    {
        return new self(
            success: true,
            filePath: $filePath,
            publicUrl: $publicUrl,
            metadata: $metadata
        );
    }

    /**
     * Buat UploadResult untuk operasi gagal
     * 
     * @param array $errors Array pesan error
     * @return self
     */
    public static function failure(array $errors): self
    {
        return new self(
            success: false,
            errors: $errors
        );
    }

    /**
     * Cek apakah upload berhasil
     * 
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * Cek apakah upload gagal
     * 
     * @return bool
     */
    public function isFailure(): bool
    {
        return !$this->success;
    }

    /**
     * Dapatkan pesan error pertama atau null jika tidak ada error
     * 
     * @return string|null
     */
    public function getFirstError(): ?string
    {
        return $this->errors[0] ?? null;
    }

    /**
     * Dapatkan semua error sebagai string terpisah koma
     * 
     * @return string
     */
    public function getErrorsAsString(): string
    {
        return implode(', ', $this->errors);
    }
}
