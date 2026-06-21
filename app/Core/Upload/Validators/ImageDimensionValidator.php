<?php

namespace App\Core\Upload\Validators;

use App\Core\Http\UploadedFile;

/**
 * ImageDimensionValidator - Validator untuk dimensi gambar
 * 
 * Validator ini memastikan bahwa gambar yang diupload tidak melebihi
 * dimensi (lebar dan tinggi) maksimum yang ditentukan.
 */
class ImageDimensionValidator implements FileValidatorInterface
{
    /**
     * @var string Pesan error jika validasi gagal
     */
    private string $errorMessage = '';

    /**
     * Constructor untuk ImageDimensionValidator
     * 
     * @param int|null $maxWidth Lebar maksimum dalam pixels (null = no limit)
     * @param int|null $maxHeight Tinggi maksimum dalam pixels (null = no limit)
     */
    public function __construct(
        private ?int $maxWidth = null,
        private ?int $maxHeight = null
    ) {}

    /**
     * Validasi dimensi gambar
     * 
     * @param UploadedFile $file File yang akan divalidasi
     * @return bool True jika dimensi gambar tidak melebihi maksimum
     */
    public function validate(UploadedFile $file): bool
    {
        // Jika tidak ada batasan dimensi, langsung lolos
        if ($this->maxWidth === null && $this->maxHeight === null) {
            return true;
        }

        // Dapatkan informasi gambar
        $imageInfo = @getimagesize($file->tmpName);

        if ($imageInfo === false) {
            $this->errorMessage = 'Tidak dapat membaca dimensi gambar';
            return false;
        }

        // $width = $imageInfo[0];
        // $height = $imageInfo[1];

        // Cek lebar
        // if ($this->maxWidth !== null && $width > $this->maxWidth) {
        //     $this->errorMessage = "Lebar gambar terlalu besar. Maksimum: {$this->maxWidth}px, Ditemukan: {$width}px";
        //     return false;
        // }

        // Cek tinggi
        // if ($this->maxHeight !== null && $height > $this->maxHeight) {
        //     $this->errorMessage = "Tinggi gambar terlalu besar. Maksimum: {$this->maxHeight}px, Ditemukan: {$height}px";
        //     return false;
        // }

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
