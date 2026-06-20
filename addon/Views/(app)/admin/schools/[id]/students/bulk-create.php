<?php

/**
 * @var array $school
 * Halaman bulk import siswa
 */
?>

<div class="bulk-import-page">
    <div class="page-header">
        <div>
            <a data-spa href="/admin/schools/<?= $school['id'] ?>/students" class="back-link">
                ← Kembali ke Daftar Siswa
            </a>
            <h1>📥 Import Banyak Siswa</h1>
            <p class="page-description">Upload file CSV untuk menambahkan banyak siswa sekaligus ke <?= htmlspecialchars($school['name']) ?></p>
        </div>
    </div>

    <?php if (isset($_SESSION['bulk_import_errors']) && !empty($_SESSION['bulk_import_errors'])): ?>
        <div class="alert alert-error">
            <h3>⚠️ Error Validasi</h3>
            <ul>
                <?php foreach ($_SESSION['bulk_import_errors'] as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php
        unset($_SESSION['bulk_import_errors']);
        unset($_SESSION['bulk_import_data']);
        ?>
    <?php endif; ?>

    <div class="import-steps">
        <div class="step">
            <h3>📥 Langkah 1: Download Template CSV</h3>
            <p>Download template CSV untuk memastikan format data sesuai</p>
            <button type="button" class="btn btn-secondary" onclick="downloadTemplate()">
                📄 Download Template CSV
            </button>
        </div>

        <div class="step">
            <h3>📝 Langkah 2: Isi Data Siswa</h3>
            <p>Isi data siswa sesuai template dengan format:</p>
            <div class="format-info">
                <code>name,email,password,student_id,grade_level,major,phone,address,birth_place,birth_date,gender,parent_name,parent_phone,parent_email</code>
            </div>
            <div class="example-data">
                <h4>Contoh:</h4>
                <code>Ahmad Rizky,ahmad@student.com,password123,0012345678,10,IPA,08123456789,"Jl. Merdeka No. 1, Makassar",Makassar,2008-05-15,male,Budi Santoso,081234567890,budi@example.com</code>
            </div>
        </div>

        <div class="step">
            <h3>📤 Langkah 3: Upload File CSV</h3>
            <p>Pilih file CSV yang sudah diisi dan upload</p>
            <form action="/admin/schools/<?= $school['id'] ?>/students/bulk-create" method="POST" enctype="multipart/form-data" class="upload-form">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label for="csv_file">Pilih File CSV:</label>
                    <input
                        type="file"
                        id="csv_file"
                        name="csv_file"
                        accept=".csv,.txt"
                        required
                        class="file-input">
                    <small class="form-text">Format yang didukung: CSV (.csv, .txt)</small>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        📤 Upload dan Import
                    </button>
                    <a data-spa href="/admin/schools/<?= $school['id'] ?>/students" class="btn btn-secondary">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="important-notes">
        <h3>⚠️ Catatan Penting</h3>
        <ul>
            <li><strong>Email harus unik</strong> - Email siswa tidak boleh sama dengan yang sudah ada di sistem</li>
            <li><strong>Password minimal 6 karakter</strong> - Pastikan password cukup kuat</li>
            <li><strong>Grade_level</strong> - Gunakan format: 10, 11, atau 12</li>
            <li><strong>Gender</strong> - Gunakan: male atau female</li>
            <li><strong>Tanggal lahir</strong> - Format: YYYY-MM-DD (contoh: 2008-05-15)</li>
            <li><strong>Field wajib</strong> - name, email, password, student_id, grade_level, parent_name, parent_phone</li>
        </ul>
    </div>
</div>

<script>
    function downloadTemplate() {
        const headers = 'name,email,password,student_id,grade_level,major,phone,address,birth_place,birth_date,gender,parent_name,parent_phone,parent_email\n';
        const example = 'Ahmad Rizky,ahmad@student.com,password123,0012345678,10,IPA,08123456789,"Jl. Merdeka No. 1, Makassar",Makassar,2008-05-15,male,Budi Santoso,081234567890,budi@example.com\n';
        const example2 = 'Siti Nurhaliza,siti@student.com,password123,0012345679,11,IPS,08123456790,"Jl. Sudirman No. 2, Makassar",Makassar,2007-08-20,female,Ahmad Dahlan,081234567891,siti@example.com';

        const csvContent = headers + example + example2;
        const blob = new Blob([csvContent], {
            type: 'text/csv;charset=utf-8;'
        });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);

        link.setAttribute('href', url);
        link.setAttribute('download', 'template_siswa.csv');
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
</script>