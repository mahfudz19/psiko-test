<?php

namespace App\Core\Upload;

/**
 * UploadConfig - Konfigurasi untuk proses upload gambar
 * 
 * Class ini berisi semua konfigurasi yang diperlukan untuk proses upload,
 * termasuk tipe file yang diperbolehkan, ukuran maksimum, dimensi, dan lainnya.
 */
class UploadConfig
{
    /**
     * Constructor untuk UploadConfig
     * 
     * @param string $uploadType Tipe upload (avatar, statement, logo, dll)
     * @param array $allowedTypes Array MIME types yang diperbolehkan
     * @param int $maxSize Ukuran maksimum file dalam bytes (default 2MB)
     * @param int|null $maxWidth Lebar maksimum gambar (null = no limit)
     * @param int|null $maxHeight Tinggi maksimum gambar (null = no limit)
     * @param string $directory Direktori penyimpanan relatif terhadap public/
     * @param bool $generateUniqueName Apakah akan generate nama file unik
     * @param bool $deleteOldFile Apakah akan hapus file lama (untuk replace)
     * @param string|null $oldFilePath Path file lama yang akan dihapus
     * @param bool $compressImage Apakah akan kompres gambar
     * @param int $jpegQuality Kualitas JPEG untuk kompresi (1-100)
     */
    public function __construct(
        public string $uploadType = 'default',
        public array $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'],
        public int $maxSize = 2 * 1024 * 1024, // 2MB default
        public ?int $maxWidth = null,
        public ?int $maxHeight = null,
        public string $directory = 'uploads',
        public bool $generateUniqueName = true,
        public bool $deleteOldFile = false,
        public ?string $oldFilePath = null,
        public bool $compressImage = false,
        public int $jpegQuality = 85
    ) {}

    /**
     * Dapatkan path lengkap direktori upload
     * 
     * @return string Path absolut ke direktori upload
     */
    public function getFullUploadPath(): string
    {
        return __DIR__ . '/../../../public/' . $this->directory;
    }
}
