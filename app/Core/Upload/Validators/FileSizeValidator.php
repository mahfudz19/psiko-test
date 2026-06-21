<?php

namespace App\Core\Upload\Validators;

use App\Core\Http\UploadedFile;

/**
 * FileSizeValidator - Validator untuk ukuran file
 * 
 * Validator ini memastikan bahwa file yang diupload tidak melebihi
 * ukuran maksimum yang ditentukan.
 */
class FileSizeValidator implements FileValidatorInterface
{
    /**
     * @var string Pesan error jika validasi gagal
     */
    private string $errorMessage = '';

    /**
     * Constructor untuk FileSizeValidator
     * 
     * @param int $maxSize Ukuran maksimum file dalam bytes
     */
    public function __construct(
        private int $maxSize = 2 * 1024 * 1024 // 2MB default
    ) {}

    /**
     * Validasi ukuran file
     * 
     * @param UploadedFile $file File yang akan divalidasi
     * @return bool True jika ukuran file tidak melebihi maksimum
     */
    public function validate(UploadedFile $file): bool
    {
        $fileSize = $file->getSize();

        if ($fileSize > $this->maxSize) {
            $maxSizeMB = round($this->maxSize / 1024 / 1024, 2);
            $actualSizeMB = round($fileSize / 1024 / 1024, 2);
            $this->errorMessage = "Ukuran file terlalu besar. Ukuran file: {$actualSizeMB}MB, Maksimum: {$maxSizeMB}MB";
            return false;
        }

        return true;
    }

    /**
     * Dapatkan pesan error
     * 
     * @return string Pesan error validasi
     */
    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
}
