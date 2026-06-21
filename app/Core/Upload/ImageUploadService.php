<?php

namespace App\Core\Upload;

use App\Core\Http\UploadedFile;
use App\Core\Upload\Validators\FileValidatorInterface;
use App\Core\Upload\Validators\MimeTypeValidator;
use App\Core\Upload\Validators\FileSizeValidator;
use App\Core\Upload\Validators\MagicByteValidator;
use App\Core\Upload\Validators\ImageDimensionValidator;
use App\Core\Upload\Storage\LocalFileSystemStorage;

/**
 * ImageUploadService - Service utama untuk upload gambar
 * 
 * Class ini menyediakan fungsi upload gambar yang robust, secure, dan reusable
 * dengan validasi berlapis, error handling yang komprehensif, dan logging untuk debugging.
 */
class ImageUploadService
{
    /**
     * @var array<FileValidatorInterface> Array validators yang akan digunakan
     */
    private array $validators = [];

    /**
     * Constructor untuk ImageUploadService
     * 
     * @param LocalFileSystemStorage $storage Storage handler untuk penyimpanan file
     */
    public function __construct(
        private LocalFileSystemStorage $storage
    ) {}

    /**
     * Upload file dengan konfigurasi tertentu
     * 
     * @param UploadedFile $file File yang akan diupload
     * @param UploadConfig $config Konfigurasi upload
     * @return UploadResult Hasil operasi upload
     */
    public function upload(UploadedFile $file, UploadConfig $config): UploadResult
    {
        // Step 1: Basic validation - cek apakah file diupload tanpa error
        if ($file->getError() !== UPLOAD_ERR_OK) {
            $errorMessage = $this->getUploadErrorMessage($file->getError());
            logger()->error('Upload error', [
                'error_code' => $file->getError(),
                'error_message' => $errorMessage,
                'file_name' => $file->getClientOriginalName()
            ]);
            return UploadResult::failure([$errorMessage]);
        }

        // Step 2: Initialize default validators
        $this->initializeDefaultValidators($config);

        // Step 3: Run validation pipeline
        $validationErrors = [];
        foreach ($this->validators as $validator) {
            if (!$validator->validate($file)) {
                $validationErrors[] = $validator->getErrorMessage();
                logger()->error('Validation failed', [
                    'validator' => get_class($validator),
                    'error' => $validator->getErrorMessage(),
                    'file_name' => $file->getClientOriginalName()
                ]);
            }
        }

        if (!empty($validationErrors)) {
            return UploadResult::failure($validationErrors);
        }

        // Step 4: Generate unique filename
        $filename = $this->generateFilename($file, $config);
        $destinationPath = $config->directory . '/' . $filename;

        // Step 5: Store file
        $storedPath = $this->storage->store($file, $destinationPath);

        if ($storedPath === false) {
            return UploadResult::failure(['Gagal menyimpan file ke storage']);
        }

        // Step 6: Delete old file jika dikonfigurasi (misal: avatar lama)
        if ($config->deleteOldFile && $config->oldFilePath !== null) {
            $this->storage->delete($config->oldFilePath);
        }

        // Step 7: Build public URL
        $publicUrl = '/' . $destinationPath;

        // Step 8: Gather metadata
        $metadata = $this->gatherMetadata($storedPath, $file);

        logger()->log('File berhasil diupload', [
            'filename' => $filename,
            'path' => $storedPath,
            'url' => $publicUrl,
            'size' => $file->getSize(),
            'upload_type' => $config->uploadType
        ]);

        return UploadResult::success($storedPath, $publicUrl, $metadata);
    }

    /**
     * Tambahkan custom validator ke pipeline
     * 
     * @param FileValidatorInterface $validator Validator yang akan ditambahkan
     * @return self
     */
    public function addValidator(FileValidatorInterface $validator): self
    {
        $this->validators[] = $validator;
        return $this;
    }

    /**
     * Hapus semua validators
     * 
     * @return self
     */
    public function clearValidators(): self
    {
        $this->validators = [];
        return $this;
    }

    /**
     * Initialize default validators berdasarkan config
     * 
     * @param UploadConfig $config Konfigurasi upload
     * @return void
     */
    private function initializeDefaultValidators(UploadConfig $config): void
    {
        $this->clearValidators();

        // MIME Type validation
        $this->addValidator(new MimeTypeValidator($config->allowedTypes));

        // File size validation
        $this->addValidator(new FileSizeValidator($config->maxSize));

        // Magic byte validation (security - mencegah file spoofing)
        $this->addValidator(new MagicByteValidator());

        // Image dimension validation (jika dikonfigurasi)
        if ($config->maxWidth !== null || $config->maxHeight !== null) {
            $this->addValidator(new ImageDimensionValidator($config->maxWidth, $config->maxHeight));
        }
    }

    /**
     * Generate unique filename untuk file yang diupload
     * 
     * @param UploadedFile $file File yang diupload
     * @param UploadConfig $config Konfigurasi upload
     * @return string Filename yang unik dan aman
     */
    private function generateFilename(UploadedFile $file, UploadConfig $config): string
    {
        if (!$config->generateUniqueName) {
            // Sanitize nama file asli
            return $this->sanitizeFilename($file->getClientOriginalName());
        }

        // Dapatkan extension dari file asli
        $extension = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);

        // Sanitize extension
        $extension = strtolower(preg_replace('/[^a-z0-9]/i', '', $extension));
        if (empty($extension)) {
            $extension = 'jpg'; // Default fallback
        }

        // Generate unique name: type_timestamp_random
        $uniqueId = bin2hex(random_bytes(8));
        $timestamp = time();

        return "{$config->uploadType}_{$timestamp}_{$uniqueId}.{$extension}";
    }

    /**
     * Sanitize filename untuk keamanan
     * 
     * @param string $filename Nama file yang akan disanitize
     * @return string Nama file yang aman
     */
    private function sanitizeFilename(string $filename): string
    {
        // Hapus karakter berbahaya
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);

        // Hapus multiple underscores
        $filename = preg_replace('/_+/', '_', $filename);

        // Hapus leading/trailing underscores dan dots
        $filename = trim($filename, '_.');

        // Limit panjang filename
        return substr($filename, 0, 255);
    }

    /**
     * Kumpulkan metadata file
     * 
     * @param string $filePath Path file yang disimpan
     * @param UploadedFile $file File yang diupload
     * @return array Metadata file
     */
    private function gatherMetadata(string $filePath, UploadedFile $file): array
    {
        $metadata = [
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'uploaded_at' => date('Y-m-d H:i:s')
        ];

        // Tambahkan dimensi gambar jika tersedia
        $imageInfo = @getimagesize($filePath);
        if ($imageInfo !== false) {
            $metadata['width'] = $imageInfo[0];
            $metadata['height'] = $imageInfo[1];
        }

        return $metadata;
    }

    /**
     * Dapatkan pesan error yang deskriptif berdasarkan error code
     * 
     * @param int $errorCode Error code dari PHP upload
     * @return string Pesan error yang deskriptif
     */
    private function getUploadErrorMessage(int $errorCode): string
    {
        return match ($errorCode) {
            UPLOAD_ERR_INI_SIZE => 'Ukuran file melebihi upload_max_filesize di php.ini',
            UPLOAD_ERR_FORM_SIZE => 'Ukuran file melebihi MAX_FILE_SIZE di form',
            UPLOAD_ERR_PARTIAL => 'File hanya terupload sebagian',
            UPLOAD_ERR_NO_FILE => 'Tidak ada file yang diupload',
            UPLOAD_ERR_NO_TMP_DIR => 'Folder temporary tidak ditemukan',
            UPLOAD_ERR_CANT_WRITE => 'Gagal menulis file ke disk',
            UPLOAD_ERR_EXTENSION => 'Ekstensi PHP menghentikan upload',
            default => 'Error upload tidak diketahui'
        };
    }
}
