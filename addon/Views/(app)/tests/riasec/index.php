<?php

/**
 * View: Tes RIASEC - Halaman Info
 *
 * @var array|null $latestResult Hasil tes terakhir (jika ada)
 * @var array|null $config Konfigurasi tes
 * @var int $statementCount Jumlah pernyataan
 * @var array $studentProfile Profil siswa
 * @var bool $noConfig Flag jika sekolah belum punya konfigurasi
 * @var bool $canRetake Apakah bisa mengerjakan ulang (cooldown sudah lewat)
 * @var int $remainingDays Sisa hari sebelum bisa retake
 * @var int|null $lastTestDate Timestamp tes terakhir
 */
?>

<div class="riasec-intro-page">
    <?php if (isset($noConfig) && $noConfig): ?>
        <div class="riasec-card riasec-card--warning">
            <div class="riasec-card-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z" />
                    <line x1="12" y1="9" x2="12" y2="13" />
                    <line x1="12" y1="17" x2="12.01" y2="17" />
                </svg>
            </div>
            <div class="riasec-card-content">
                <h3>Konfigurasi Tes Belum Tersedia</h3>
                <p>Sekolah Anda belum memiliki konfigurasi untuk Tes RIASEC. Silakan hubungi guru Bimbingan Konseling (BK) untuk informasi lebih lanjut.</p>
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-error">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10" />
                <line x1="12" y1="8" x2="12" y2="12" />
                <line x1="12" y1="16" x2="12.01" y2="16" />
            </svg>
            <span><?= htmlspecialchars($_GET['error']) ?></span>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['warning'])): ?>
        <div class="alert alert-warning">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z" />
                <line x1="12" y1="9" x2="12" y2="13" />
                <line x1="12" y1="17" x2="12.01" y2="17" />
            </svg>
            <span><?= htmlspecialchars($_GET['warning']) ?></span>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                <polyline points="22 4 12 14.01 9 11.01" />
            </svg>
            <span><?= htmlspecialchars($_GET['success']) ?></span>
        </div>
    <?php endif; ?>

    <div class="riasec-header">
        <h1 class="riasec-title">Tes Minat Karir RIASEC</h1>
        <p class="riasec-subtitle">Kenali minat dan kecenderungan karir Anda dengan instrumen RIASEC</p>
    </div>

    <?php if (isset($latestResult) && $latestResult): ?>
        <?php if (isset($canRetake) && !$canRetake): ?>
            <!-- Cooldown Alert -->
            <div class="riasec-card riasec-card--warning">
                <div class="riasec-card-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="8" x2="12" y2="12" />
                        <line x1="12" y1="16" x2="12.01" y2="16" />
                    </svg>
                </div>
                <div class="riasec-card-content">
                    <h3>Anda Sudah Pernah Mengerjakan Tes Ini</h3>
                    <p>Kode Holland Anda: <strong><?= htmlspecialchars($latestResult['holland_code'] ?? '-') ?></strong></p>
                    <p>Tanggal: <?= date('d M Y', strtotime($latestResult['calculated_at'])) ?></p>
                </div>
            </div>

            <div class="riasec-card riasec-card--info">
                <div class="riasec-card-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                        <line x1="16" y1="2" x2="16" y2="6" />
                        <line x1="8" y1="2" x2="8" y2="6" />
                        <line x1="3" y1="10" x2="21" y2="10" />
                    </svg>
                </div>
                <div class="riasec-card-content">
                    <h3>Cooldown Period</h3>
                    <p>Anda harus menunggu <strong><?= $remainingDays ?> hari lagi</strong> sebelum dapat mengulang tes RIASEC.</p>
                    <p class="cooldown-note">Tes minat dapat berubah seiring waktu. Masa tunggu 30 hari memastikan hasil yang lebih akurat.</p>
                </div>
            </div>
        <?php else: ?>
            <!-- Can Retake -->
            <div class="riasec-card riasec-card--success">
                <div class="riasec-card-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                        <polyline points="22 4 12 14.01 9 11.01" />
                    </svg>
                </div>
                <div class="riasec-card-content">
                    <h3>Anda Sudah Pernah Mengerjakan Tes Ini</h3>
                    <p>Kode Holland Anda: <strong><?= htmlspecialchars($latestResult['holland_code'] ?? '-') ?></strong></p>
                    <p>Tanggal: <?= date('d M Y', strtotime($latestResult['calculated_at'])) ?></p>
                    <a data-spa href="/tests/riasec/results?session=<?= $latestResult['session_id'] ?>" class="btn btn-primary">Lihat Hasil Lengkap</a>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php if (!isset($noConfig) || !$noConfig): ?>
        <div class="riasec-content">
            <div class="riasec-section">
                <h2>Apa itu RIASEC?</h2>
                <p>RIASEC adalah model asesmen minat karir yang dikembangkan oleh psikolog John L. Holland pada tahun 1959. Tes ini membantu Anda memahami kecenderungan minat Anda terhadap 6 dimensi karir:</p>

                <div class="riasec-dimensions">
                    <div class="riasec-dimension" style="--dimension-color: #3B6D11;">
                        <div class="riasec-dimension-icon">R</div>
                        <h3>Realistic</h3>
                        <p>Orang yang praktis, suka bekerja dengan tangan, alat, mesin, atau hewan. Cocok untuk karir teknik, mekanik, atau pertanian.</p>
                    </div>
                    <div class="riasec-dimension" style="--dimension-color: #1E5F74;">
                        <div class="riasec-dimension-icon">I</div>
                        <h3>Investigative</h3>
                        <p>Orang yang analitis, intelektual, suka memecahkan masalah kompleks. Cocok untuk karir penelitian, sains, atau teknologi.</p>
                    </div>
                    <div class="riasec-dimension" style="--dimension-color: #8B5CF6;">
                        <div class="riasec-dimension-icon">A</div>
                        <h3>Artistic</h3>
                        <p>Orang yang kreatif, ekspresif, suka seni dan kegiatan kreatif. Cocok untuk karir desain, musik, atau seni peran.</p>
                    </div>
                    <div class="riasec-dimension" style="--dimension-color: #F59E0B;">
                        <div class="riasec-dimension-icon">S</div>
                        <h3>Social</h3>
                        <p>Orang yang suka membantu, mengajar, dan bekerja dengan orang lain. Cocok untuk karir pendidikan, kesehatan, atau konseling.</p>
                    </div>
                    <div class="riasec-dimension" style="--dimension-color: #DC2626;">
                        <div class="riasec-dimension-icon">E</div>
                        <h3>Enterprising</h3>
                        <p>Orang yang ambisius, suka memimpin, dan mempengaruhi orang lain. Cocok untuk karir bisnis, manajemen, atau politik.</p>
                    </div>
                    <div class="riasec-dimension" style="--dimension-color: #6B7280;">
                        <div class="riasec-dimension-icon">C</div>
                        <h3>Conventional</h3>
                        <p>Orang yang terorganisir, detail-oriented, suka bekerja dengan data. Cocok untuk karir administrasi, akuntansi, atau analisis data.</p>
                    </div>
                </div>
            </div>

            <div class="riasec-section">
                <h2>Informasi Tes</h2>
                <div class="riasec-info-grid">
                    <div class="riasec-info-item">
                        <span class="riasec-info-label">Jumlah Pernyataan</span>
                        <span class="riasec-info-value"><?= $statementCount ?> butir</span>
                    </div>
                    <div class="riasec-info-item">
                        <span class="riasec-info-label">Waktu Pengerjaan</span>
                        <span class="riasec-info-value">30 menit</span>
                    </div>
                    <div class="riasec-info-item">
                        <span class="riasec-info-label">Skala Penilaian</span>
                        <span class="riasec-info-value">1-4 (Sangat Tidak Sesuai - Sangat Sesuai)</span>
                    </div>
                    <div class="riasec-info-item">
                        <span class="riasec-info-label">Output</span>
                        <span class="riasec-info-value">Kode Holland (3 huruf) + Rekomendasi</span>
                    </div>
                </div>
            </div>

            <div class="riasec-section">
                <h2>Cara Mengerjakan</h2>
                <ol class="riasec-steps">
                    <li>Baca setiap pernyataan dengan teliti</li>
                    <li>Pilih jawaban yang paling sesuai dengan diri Anda (bukan yang Anda inginkan)</li>
                    <li>Tidak ada jawaban benar atau salah</li>
                    <li>Jawablah dengan jujur sesuai kondisi Anda saat ini</li>
                    <li>Pastikan semua pertanyaan terjawab sebelum submit</li>
                </ol>
            </div>

            <div class="riasec-cta">
                <?php if (isset($canRetake) && !$canRetake && isset($latestResult) && $latestResult): ?>
                    <!-- Disabled button during cooldown -->
                    <button class="btn btn-primary btn-lg" disabled style="opacity: 0.6; cursor: not-allowed;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                            <line x1="16" y1="2" x2="16" y2="6" />
                            <line x1="8" y1="2" x2="8" y2="6" />
                            <line x1="3" y1="10" x2="21" y2="10" />
                        </svg>
                        Tunggu <?= $remainingDays ?> Hari Lagi
                    </button>
                <?php else: ?>
                    <form data-spa action="/tests/riasec/start" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <?php if (isset($latestResult) && $latestResult): ?>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 4.2" />
                                </svg>
                                Kerjakan Ulang Tes
                            <?php else: ?>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polygon points="5 3 19 12 5 21 5 3" />
                                </svg>
                                Mulai Tes RIASEC
                            <?php endif; ?>
                        </button>
                    </form>
                <?php endif; ?>
                <p class="riasec-cta-note">Tes ini gratis dan hasilnya akan tersimpan di profil Anda</p>
            </div>
        </div>
    <?php else: ?>
        <div class="riasec-content">
            <div class="riasec-section">
                <h2>Apa itu RIASEC?</h2>
                <p>RIASEC adalah model asesmen minat karir yang dikembangkan oleh psikolog John L. Holland pada tahun 1959. Tes ini membantu Anda memahami kecenderungan minat Anda terhadap 6 dimensi karir:</p>

                <div class="riasec-dimensions">
                    <div class="riasec-dimension" style="--dimension-color: #3B6D11;">
                        <div class="riasec-dimension-icon">R</div>
                        <h3>Realistic</h3>
                        <p>Orang yang praktis, suka bekerja dengan tangan, alat, mesin, atau hewan. Cocok untuk karir teknik, mekanik, atau pertanian.</p>
                    </div>
                    <div class="riasec-dimension" style="--dimension-color: #1E5F74;">
                        <div class="riasec-dimension-icon">I</div>
                        <h3>Investigative</h3>
                        <p>Orang yang analitis, intelektual, suka memecahkan masalah kompleks. Cocok untuk karir penelitian, sains, atau teknologi.</p>
                    </div>
                    <div class="riasec-dimension" style="--dimension-color: #F59E0B;">
                        <div class="riasec-dimension-icon">A</div>
                        <h3>Artistic</h3>
                        <p>Orang yang kreatif, ekspresif, imajinatif. Cocok untuk karir seni, desain, musik, atau penulisan.</p>
                    </div>
                    <div class="riasec-dimension" style="--dimension-color: #14B8A6;">
                        <div class="riasec-dimension-icon">S</div>
                        <h3>Social</h3>
                        <p>Orang yang suka membantu, mengajar, melayani orang lain. Cocok untuk karir pendidikan, kesehatan, atau pelayanan.</p>
                    </div>
                    <div class="riasec-dimension" style="--dimension-color: #DC2626;">
                        <div class="riasec-dimension-icon">E</div>
                        <h3>Enterprising</h3>
                        <p>Orang yang suka memimpin, mempengaruhi, bersaing. Cocok untuk karir bisnis, manajemen, atau penjualan.</p>
                    </div>
                    <div class="riasec-dimension" style="--dimension-color: #6B7280;">
                        <div class="riasec-dimension-icon">C</div>
                        <h3>Conventional</h3>
                        <p>Orang yang terorganisir, efisien, suka rutinitas. Cocok untuk karir administrasi, akuntansi, atau teknologi informasi.</p>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
    .riasec-intro-page {
        max-width: 900px;
        margin: 0 auto;
        padding: 2rem;
    }

    .riasec-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .riasec-title {
        font-size: 2rem;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 0.5rem;
    }

    .riasec-subtitle {
        font-size: 1.1rem;
        color: #666;
    }

    .riasec-card {
        background: #fff;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        display: flex;
        gap: 1rem;
        align-items: flex-start;
    }

    .riasec-card--success {
        border-left: 4px solid #10B981;
    }

    .riasec-card--warning {
        border-left: 4px solid #F59E0B;
    }

    .riasec-card--warning .riasec-card-icon {
        color: #F59E0B;
    }

    .riasec-card--info {
        border-left: 4px solid #3B82F6;
    }

    .riasec-card--info .riasec-card-icon {
        color: #3B82F6;
    }

    .cooldown-note {
        font-size: 0.85rem;
        font-style: italic;
        color: #666;
        margin-top: 0.5rem;
    }

    .riasec-card-icon {
        color: #10B981;
        flex-shrink: 0;
    }

    .riasec-card-content h3 {
        margin: 0 0 0.5rem 0;
        color: #1a1a1a;
    }

    .riasec-card-content p {
        margin: 0.25rem 0;
        color: #666;
    }

    .riasec-content {
        background: #fff;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .riasec-section {
        margin-bottom: 2rem;
    }

    .riasec-section h2 {
        font-size: 1.5rem;
        color: #1a1a1a;
        margin-bottom: 1rem;
    }

    .riasec-section p {
        color: #4a4a4a;
        line-height: 1.7;
    }

    .riasec-dimensions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
        margin-top: 1.5rem;
    }

    .riasec-dimension {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 1.5rem;
        border-top: 4px solid var(--dimension-color);
    }

    .riasec-dimension-icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: var(--dimension-color);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }

    .riasec-dimension h3 {
        font-size: 1.1rem;
        color: #1a1a1a;
        margin-bottom: 0.5rem;
    }

    .riasec-dimension p {
        font-size: 0.9rem;
        color: #666;
        margin: 0;
    }

    .riasec-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }

    .riasec-info-item {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 8px;
    }

    .riasec-info-label {
        display: block;
        font-size: 0.85rem;
        color: #666;
        margin-bottom: 0.25rem;
    }

    .riasec-info-value {
        display: block;
        font-size: 1rem;
        font-weight: 600;
        color: #1a1a1a;
    }

    .riasec-steps {
        padding-left: 1.5rem;
    }

    .riasec-steps li {
        margin-bottom: 0.5rem;
        color: #4a4a4a;
        line-height: 1.6;
    }

    .riasec-cta {
        text-align: center;
        padding-top: 2rem;
        border-top: 1px solid #e5e5e5;
    }

    .riasec-cta .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 1rem 2rem;
        font-size: 1.1rem;
    }

    .riasec-cta-note {
        margin-top: 1rem;
        font-size: 0.9rem;
        color: #666;
    }

    @media (max-width: 768px) {
        .riasec-intro-page {
            padding: 1rem;
        }

        .riasec-title {
            font-size: 1.5rem;
        }

        .riasec-content {
            padding: 1.5rem;
        }

        .riasec-dimensions {
            grid-template-columns: 1fr;
        }
    }

    /* Alert Messages */
    .alert {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem 1.25rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        font-size: 0.95rem;
    }

    .alert svg {
        flex-shrink: 0;
    }

    .alert-error {
        background-color: #FEF2F2;
        border: 1px solid #FECACA;
        color: #991B1B;
    }

    .alert-warning {
        background-color: #FFFBEB;
        border: 1px solid #FEE685;
        color: #92400E;
    }

    .alert-success {
        background-color: #F0FDF4;
        border: 1px solid #BBF7D0;
        color: #166534;
    }
</style>