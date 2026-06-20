<header class="bulk-scores-header">
    <h1>📊 Input Nilai Massal</h1>
    <p>Sekolah: <strong><?= htmlspecialchars($school['name'] ?? '') ?></strong></p>
</header>

<section class="bulk-scores-section">
    <article class="bulk-scores-card">
        <h2>📥 Upload CSV Nilai Siswa</h2>
        <p>Import nilai akademik untuk banyak siswa sekaligus menggunakan file CSV.</p>

        <?php if (isset($_SESSION['bulk_scores_errors']) && !empty($_SESSION['bulk_scores_errors'])): ?>
            <div class="bulk-scores-alert bulk-scores-alert-error">
                <h3>⚠️ Error Import</h3>
                <ul>
                    <?php foreach ($_SESSION['bulk_scores_errors'] as $error): ?>
                        <li><?= htmlspecialchars($error['identifier'] ?? '') ?>: <?= htmlspecialchars($error['error'] ?? '') ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php unset($_SESSION['bulk_scores_errors']); ?>
        <?php endif; ?>

        <form action="/admin/schools/<?= htmlspecialchars($school['id']) ?>/students/bulk-scores" method="POST" enctype="multipart/form-data" class="bulk-scores-form">
            <?= csrf_field() ?>

            <div class="bulk-scores-form-group">
                <label for="csv_file">File CSV *</label>
                <input
                    type="file"
                    id="csv_file"
                    name="csv_file"
                    accept=".csv"
                    required
                    class="bulk-scores-file-input">
                <small>Format file harus CSV (.csv)</small>
            </div>

            <div class="bulk-scores-actions">
                <button type="submit" class="bulk-scores-btn bulk-scores-btn-primary">
                    📤 Upload & Import Nilai
                </button>
                <a href="/admin/schools/<?= htmlspecialchars($school['id']) ?>/students" class="bulk-scores-btn bulk-scores-btn-secondary">
                    ← Batal
                </a>
            </div>
        </form>
    </article>

    <article class="bulk-scores-card">
        <h2>📋 Format CSV</h2>
        <p>File CSV harus memiliki header dan format sebagai berikut:</p>

        <div class="bulk-scores-code-block">
            <pre>identifier,semester,subject,final_score,pengetahuan,keterampilan
0012345678,Semester 1 Kelas 10,Matematika,85,80,90
0012345678,Semester 1 Kelas 10,Bahasa Indonesia,88,85,91
0012345678,Semester 1 Kelas 10,Bahasa Inggris,90,88,92
0012345679,Semester 1 Kelas 10,Matematika,92,90,94
0012345679,Semester 1 Kelas 10,Bahasa Indonesia,87,85,89
0012345679,Semester 1 Kelas 10,IPA,95,93,97</pre>
        </div>

        <h3>Keterangan Field:</h3>
        <ul class="bulk-scores-list">
            <li><strong>identifier</strong>: NIS/NISN atau nama siswa (sistem akan auto-detect)</li>
            <li><strong>semester</strong>: Format "Semester {N} Kelas {X}" (contoh: "Semester 1 Kelas 10")</li>
            <li><strong>subject</strong>: Nama mata pelajaran</li>
            <li><strong>final_score</strong>: Nilai akhir (0-100)</li>
            <li><strong>pengetahuan</strong>: Nilai pengetahuan (0-100, optional)</li>
            <li><strong>keterampilan</strong>: Nilai keterampilan (0-100, optional)</li>
        </ul>

        <div class="bulk-scores-download">
            <a href="/admin/schools/<?= htmlspecialchars($school['id']) ?>/students/bulk-scores/template" class="bulk-scores-btn bulk-scores-btn-outline" download>
                📥 Download Template CSV
            </a>
        </div>
    </article>

    <article class="bulk-scores-card">
        <h2>⚠️ Penting!</h2>
        <ul class="bulk-scores-list">
            <li>Pastikan <strong>identifier</strong> (NIS/NISN atau nama) sesuai dengan data siswa yang ada</li>
            <li>Siswa harus sudah terdaftar di sistem sebelum input nilai</li>
            <li>Nilai harus dalam range 0-100</li>
            <li>Format semester harus konsisten (contoh: "Semester 1 Kelas 10")</li>
            <li>Jika siswa sudah memiliki nilai untuk mata pelajaran yang sama, nilai akan di-update</li>
            <li>Data nilai untuk semester yang sama akan digabungkan</li>
        </ul>
    </article>
</section>