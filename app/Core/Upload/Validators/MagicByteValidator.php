<?php

namespace App\Core\Upload\Validators;

use App\Core\Http\UploadedFile;

/**
 * MagicByteValidator - Validator untuk verifikasi konten file sebenarnya
 * 
 * Validator ini memverifikasi bahwa konten file sesuai dengan
 * MIME type yang dideklarasikan dengan memeriksa magic bytes/file signature.
 * Ini adalah lapisan keamanan penting untuk mencegah file spoofing.
 */
class MagicByteValidator implements FileValidatorInterface
{
    /**
     * @var string Pesan error jika validasi gagal
     */
    private string $errorMessage = '';

    /**
     * Magic bytes untuk berbagai format gambar
     * 
     * @var array<string, array<string>>
     */
    private const MAGIC_BYTES = [
        'image/jpeg' => ["\xFF\xD8\xFF"],
        'image/png'  => ["\x89\x50\x4E\x47\x0D\x0A\x1A\x0A"],
        'image/gif'  => ["\x47\x49\x46\x38\x37\x61", "\x47\x49\x46\x38\x39\x61"],
        'image/webp' => ["\x52\x49\x46\x46"], // RIFF header
    ];

    /**
     * Validasi magic bytes file
     * 
     * @param UploadedFile $file File yang akan divalidasi
     * @return bool True jika magic bytes sesuai dengan MIME type
     */
    public function validate(UploadedFile $file): bool
    {
        $tmpPath = $file->tmpName;

        // Cek apakah file bisa dibaca
        if (!is_readable($tmpPath)) {
            $this->errorMessage = 'Tidak dapat membaca file untuk verifikasi';
            return false;
        }

        // Baca header file (12 bytes pertama)
        $fileHeader = file_get_contents($tmpPath, false, null, 0, 12);

        if ($fileHeader === false) {
            $this->errorMessage = 'Tidak dapat membaca header file';
            return false;
        }

        $mimeType = $file->getClientMimeType();

        // Cek apakah MIME type diketahui
        if (!isset(self::MAGIC_BYTES[$mimeType])) {
            $this->errorMessage = 'MIME type tidak dikenali untuk verifikasi';
            return false;
        }

        // Cek magic bytes
        foreach (self::MAGIC_BYTES[$mimeType] as $magicByte) {
            if (str_starts_with($fileHeader, $magicByte)) {
                return true;
            }
        }

        // Special handling untuk WebP (RIFF....WEBP)
        if ($mimeType === 'image/webp') {
            if (
                strlen($fileHeader) >= 12 &&
                substr($fileHeader, 0, 4) === "\x52\x49\x46\x46" &&
                substr($fileHeader, 8, 4) === "\x57\x45\x42\x50"
            ) {
                return true;
            }
        }

        $this->errorMessage = 'Konten file tidak sesuai dengan tipe file yang dideklarasikan - kemungkinan file spoofing terdeteksi';
        return false;
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
