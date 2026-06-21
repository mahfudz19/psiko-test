<?php

namespace App\Core\Upload\Validators;

use App\Core\Http\UploadedFile;

/**
 * FileValidatorInterface - Interface untuk validator file upload
 * 
 * Interface ini mendefinisikan kontrak untuk semua validator file
 * yang akan digunakan dalam upload pipeline.
 */
interface FileValidatorInterface
{
    /**
     * Validasi file yang diupload
     * 
     * @param UploadedFile $file File yang akan divalidasi
     * @return bool True jika validasi lolos, false jika gagal
     */
    public function validate(UploadedFile $file): bool;

    /**
     * Dapatkan pesan error dari validasi
     * 
     * @return string Pesan error yang deskriptif
     */
    public function getErrorMessage(): string;
}
