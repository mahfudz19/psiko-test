<?php

namespace App\Core\Upload\Validators;

use App\Core\Http\UploadedFile;

/**
 * MimeTypeValidator - Validator untuk MIME type file
 * 
 * Validator ini memastikan bahwa file yang diupload memiliki
 * MIME type yang sesuai dengan whitelist yang ditentukan.
 */
class MimeTypeValidator implements FileValidatorInterface
{
    /**
     * @var string Pesan error jika validasi gagal
     */
    private string $errorMessage = '';

    /**
     * Constructor untuk MimeTypeValidator
     * 
     * @param array $allowedTypes Array MIME types yang diperbolehkan
     */
    public function __construct(
        private array $allowedTypes = ['image/jpeg', 'image/png', 'image/webp']
    ) {}

    /**
     * Validasi MIME type file
     * 
     * @param UploadedFile $file File yang akan divalidasi
     * @return bool True jika MIME type diperbolehkan
     */
    public function validate(UploadedFile $file): bool
    {
        $mimeType = $file->getClientMimeType();

        if (!in_array($mimeType, $this->allowedTypes, true)) {
            $allowedList = implode(', ', $this->allowedTypes);
            $this->errorMessage = "File type '{$mimeType}' tidak diperbolehkan. Tipe yang diizinkan: {$allowedList}";
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
