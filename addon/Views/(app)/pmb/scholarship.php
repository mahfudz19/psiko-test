<?php

/**
 * PMB Scholarship View - Scholarship Calculator
 * 
 * @var array $scholarships Scholarship data with eligibility and cost estimation
 */
$scholarshipData = $scholarships ?? null;
?>

<div class="pmb-scholarship-container">
    <!-- Header -->
    <div class="scholarship-header">
        <div class="header-content">
            <h1>💰 Kalkulator Beasiswa</h1>
            <p class="header-subtitle">Universitas Universal</p>
            <p class="header-note">Temukan beasiswa yang cocok untuk kamu dan hitung estimasi biaya kuliah</p>
        </div>
    </div>

    <?php if ($scholarshipData): ?>
        <!-- Available Scholarships -->
        <div class="scholarships-list-section">
            <div class="section-header">
                <h2>Beasiswa yang Tersedia</h2>
                <p>Pilih beasiswa yang sesuai dengan kriteria kamu</p>
            </div>

            <div class="scholarships-grid">
                <?php foreach ($scholarshipData['available_scholarships'] as $scholarship): ?>
                    <div class="scholarship-card">
                        <div class="scholarship-badge-top">
                            <?php if ($scholarship['discount'] >= 50): ?>
                                <span class="badge-hot">🔥 Hot</span>
                            <?php endif; ?>
                        </div>
                        <div class="scholarship-header-card">
                            <div class="scholarship-icon">
                                <?php if ($scholarship['type'] === 'akademis'): ?>📚
                                <?php elseif ($scholarship['type'] === 'prestasi'): ?>🏆
                                <?php elseif ($scholarship['type'] === 'tidak_mampu'): ?>💝
                                <?php elseif ($scholarship['type'] === 'olahraga'): ?>⚽
                                <?php elseif ($scholarship['type'] === 'seni'): ?>🎨
                                <?php else: ?>🎓<?php endif; ?>
                            </div>
                            <h3><?= htmlspecialchars($scholarship['name']) ?></h3>
                        </div>
                        <div class="scholarship-discount">
                            <span class="discount-value"><?= $scholarship['discount'] ?>%</span>
                            <span class="discount-label">Discount</span>
                        </div>
                        <div class="scholarship-details">
                            <div class="detail-row">
                                <span class="detail-label">Tipe:</span>
                                <span class="detail-value"><?= htmlspecialchars(ucfirst($scholarship['type'])) ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Kuota:</span>
                                <span class="detail-value"><?= $scholarship['quota'] ?> slot</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Deadline:</span>
                                <span class="detail-value deadline"><?= date('d M Y', strtotime($scholarship['deadline'])) ?></span>
                            </div>
                        </div>
                        <div class="scholarship-requirements">
                            <h4>Syarat:</h4>
                            <ul>
                                <?php foreach ($scholarship['requirements'] as $req): ?>
                                    <li><?= htmlspecialchars($req) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <button class="btn btn-primary btn-block" onclick="checkEligibility(<?= $scholarship['id'] ?>)">
                            Cek Eligibility
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- User Eligibility -->
        <div class="eligibility-section">
            <div class="section-header">
                <h2>Status Eligibility Kamu</h2>
                <p>Beasiswa yang sudah kamu cek dan ajukan</p>
            </div>

            <div class="eligibility-cards">
                <?php foreach ($scholarshipData['user_eligibility'] as $eligibility): ?>
                    <div class="eligibility-card <?= $eligibility['status'] ?>">
                        <div class="eligibility-header">
                            <h4><?= htmlspecialchars($eligibility['name']) ?></h4>
                            <span class="status-badge status-<?= $eligibility['status'] ?>">
                                <?php if ($eligibility['status'] === 'eligible'): ?>✅ Eligible
                                <?php elseif ($eligibility['status'] === 'check_eligibility'): ?>⏳ Perlu Dicek
                                <?php else: ?>⚪ Belum Dicek<?php endif; ?>
                            </span>
                        </div>
                        <p class="eligibility-reason"><?= htmlspecialchars($eligibility['reason']) ?></p>
                        <?php if ($eligibility['status'] === 'check_eligibility'): ?>
                            <button class="btn btn-secondary btn-sm" onclick="checkEligibility(<?= $eligibility['scholarship_id'] ?>)">
                                Cek Sekarang
                            </button>
                        <?php elseif ($eligibility['status'] === 'eligible'): ?>
                            <button class="btn btn-primary btn-sm" onclick="applyScholarship(<?= $eligibility['scholarship_id'] ?>)">
                                Ajukan Beasiswa
                            </button>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Cost Estimation -->
        <div class="cost-estimation-section">
            <div class="section-header">
                <h2>Estimasi Biaya Kuliah</h2>
                <p>Perkiraan biaya setelah beasiswa</p>
            </div>

            <div class="cost-card">
                <div class="cost-breakdown">
                    <div class="cost-row">
                        <span class="cost-label">Biaya Normal</span>
                        <span class="cost-value">Rp <?= number_format($scholarshipData['cost_estimation']['normal_fee'], 0, ',', '.') ?></span>
                    </div>
                    <?php foreach ($scholarshipData['cost_estimation']['eligible_discounts'] as $discount): ?>
                        <div class="cost-row discount">
                            <span class="cost-label"><?= htmlspecialchars($discount['name']) ?></span>
                            <span class="cost-value">- Rp <?= number_format($discount['amount'], 0, ',', '.') ?></span>
                        </div>
                    <?php endforeach; ?>
                    <div class="cost-row total">
                        <span class="cost-label">Total Setelah Beasiswa</span>
                        <span class="cost-value highlight">Rp <?= number_format($scholarshipData['cost_estimation']['final_fee'], 0, ',', '.') ?></span>
                    </div>
                </div>
                <div class="cost-savings">
                    <div class="savings-badge">
                        💰 Hemat Rp <?= number_format($scholarshipData['cost_estimation']['normal_fee'] - $scholarshipData['cost_estimation']['final_fee'], 0, ',', '.') ?>
                    </div>
                </div>
            </div>

            <div class="cost-actions">
                <button class="btn btn-primary btn-lg" onclick="recalculate()">
                    🔄 Hitung Ulang
                </button>
                <a href="/pmb/simulation" class="btn btn-secondary btn-lg">
                    Lanjut ke Pendaftaran →
                </a>
            </div>
        </div>

    <?php else: ?>
        <!-- No Data -->
        <div class="no-data-section">
            <div class="no-data-content">
                <h2>💰 Informasi Beasiswa</h2>
                <p>Universitas Universal menyediakan berbagai jenis beasiswa untuk membantu biaya kuliah kamu.</p>
                <a href="/pmb/scholarship?load=1" class="btn btn-primary">Lihat Beasiswa Tersedia →</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
    /* Container */
    .pmb-scholarship-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 24px;
        display: flex;
        flex-direction: column;
        gap: 40px;
    }

    /* Header */
    .scholarship-header {
        background: linear-gradient(135deg, var(--md-sys-color-primary, #0066cc) 0%, var(--md-sys-color-primary-container, #e6f0ff) 100%);
        border-radius: 16px;
        padding: 40px;
        text-align: center;
        color: white;
    }

    .scholarship-header h1 {
        font-size: 32px;
        font-weight: 700;
        margin: 0 0 8px 0;
    }

    .header-subtitle {
        font-size: 18px;
        opacity: 0.9;
        margin: 0 0 16px 0;
    }

    .header-note {
        font-size: 14px;
        opacity: 0.85;
        margin: 0;
    }

    /* Section Headers */
    .section-header {
        margin-bottom: 24px;
    }

    .section-header h2 {
        font-size: 24px;
        font-weight: 600;
        color: var(--md-sys-color-on-surface, #1a1a1a);
        margin: 0 0 8px 0;
    }

    .section-header p {
        font-size: 14px;
        color: var(--md-sys-color-on-surface-variant, #666);
        margin: 0;
    }

    /* Scholarships List */
    .scholarships-list-section {
        background: var(--md-sys-color-surface, #ffffff);
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .scholarships-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
    }

    .scholarship-card {
        background: var(--md-sys-color-surface-container-lowest, #ffffff);
        border: 1px solid var(--md-sys-color-outline, #e0e0e0);
        border-radius: 12px;
        padding: 20px;
        position: relative;
        transition: all 0.2s;
    }

    .scholarship-card:hover {
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        transform: translateY(-2px);
    }

    .scholarship-badge-top {
        position: absolute;
        top: 12px;
        right: 12px;
    }

    .badge-hot {
        background: linear-gradient(135deg, #ff6b6b, #ee5a5a);
        color: white;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 700;
    }

    .scholarship-header-card {
        text-align: center;
        margin-bottom: 16px;
    }

    .scholarship-icon {
        font-size: 48px;
        margin-bottom: 8px;
    }

    .scholarship-header-card h3 {
        font-size: 18px;
        font-weight: 600;
        margin: 0;
        color: var(--md-sys-color-on-surface, #1a1a1a);
    }

    .scholarship-discount {
        text-align: center;
        padding: 16px;
        background: linear-gradient(135deg, var(--md-sys-color-primary, #0066cc), var(--md-sys-color-primary-container, #e6f0ff));
        border-radius: 8px;
        margin-bottom: 16px;
        color: white;
    }

    .discount-value {
        display: block;
        font-size: 36px;
        font-weight: 700;
    }

    .discount-label {
        font-size: 12px;
        opacity: 0.9;
    }

    .scholarship-details {
        margin-bottom: 16px;
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        font-size: 14px;
        border-bottom: 1px solid var(--md-sys-color-outline, #f0f0f0);
    }

    .detail-row:last-child {
        border-bottom: none;
    }

    .detail-label {
        color: var(--md-sys-color-on-surface-variant, #666);
    }

    .detail-value {
        font-weight: 600;
        color: var(--md-sys-color-on-surface, #1a1a1a);
    }

    .detail-value.deadline {
        color: var(--md-sys-color-error, #f44336);
    }

    .scholarship-requirements {
        margin-bottom: 16px;
    }

    .scholarship-requirements h4 {
        font-size: 14px;
        font-weight: 600;
        margin: 0 0 8px 0;
        color: var(--md-sys-color-on-surface, #1a1a1a);
    }

    .scholarship-requirements ul {
        margin: 0;
        padding-left: 20px;
        font-size: 13px;
        color: var(--md-sys-color-on-surface-variant, #666);
    }

    .scholarship-requirements li {
        margin-bottom: 4px;
    }

    .btn-block {
        width: 100%;
    }

    /* Eligibility Section */
    .eligibility-section {
        background: var(--md-sys-color-surface, #ffffff);
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .eligibility-cards {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 16px;
    }

    .eligibility-card {
        background: var(--md-sys-color-surface-container-low, #f5f5f5);
        border-radius: 12px;
        padding: 20px;
        border-left: 4px solid var(--md-sys-color-outline, #e0e0e0);
    }

    .eligibility-card.eligible {
        border-left-color: var(--md-sys-color-secondary, #4caf50);
        background: var(--md-sys-color-secondary-container, #e8f5e9);
    }

    .eligibility-card.check_eligibility {
        border-left-color: var(--md-sys-color-primary, #0066cc);
    }

    .eligibility-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }

    .eligibility-header h4 {
        font-size: 16px;
        font-weight: 600;
        margin: 0;
        color: var(--md-sys-color-on-surface, #1a1a1a);
    }

    .status-badge {
        font-size: 12px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 12px;
    }

    .status-eligible {
        background: var(--md-sys-color-secondary, #4caf50);
        color: white;
    }

    .status-check_eligibility {
        background: var(--md-sys-color-primary, #0066cc);
        color: white;
    }

    .eligibility-reason {
        font-size: 14px;
        color: var(--md-sys-color-on-surface-variant, #666);
        margin: 0 0 12px 0;
    }

    /* Cost Estimation */
    .cost-estimation-section {
        background: var(--md-sys-color-surface, #ffffff);
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .cost-card {
        background: linear-gradient(135deg, var(--md-sys-color-primary, #0066cc), var(--md-sys-color-primary-container, #e6f0ff));
        border-radius: 12px;
        padding: 24px;
        color: white;
        margin-bottom: 24px;
    }

    .cost-breakdown {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-bottom: 20px;
    }

    .cost-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    }

    .cost-row:last-child {
        border-bottom: none;
    }

    .cost-row.discount {
        color: #a5d6a7;
    }

    .cost-row.total {
        font-size: 18px;
        font-weight: 700;
        padding-top: 16px;
        border-bottom: none;
    }

    .cost-label {
        font-size: 14px;
        opacity: 0.9;
    }

    .cost-value {
        font-size: 16px;
        font-weight: 600;
    }

    .cost-value.highlight {
        font-size: 24px;
        color: #fff;
    }

    .cost-savings {
        text-align: center;
        padding-top: 16px;
        border-top: 1px solid rgba(255, 255, 255, 0.2);
    }

    .savings-badge {
        display: inline-block;
        background: rgba(255, 255, 255, 0.2);
        padding: 8px 20px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 600;
    }

    .cost-actions {
        display: flex;
        gap: 16px;
        justify-content: center;
        flex-wrap: wrap;
    }

    /* No Data Section */
    .no-data-section {
        background: var(--md-sys-color-surface, #ffffff);
        border-radius: 16px;
        padding: 48px;
        text-align: center;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .no-data-content h2 {
        font-size: 24px;
        font-weight: 600;
        margin: 0 0 16px 0;
        color: var(--md-sys-color-on-surface, #1a1a1a);
    }

    .no-data-content p {
        font-size: 16px;
        color: var(--md-sys-color-on-surface-variant, #666);
        margin: 0 0 24px 0;
    }

    /* Buttons */
    .btn {
        display: inline-block;
        padding: 12px 24px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-primary {
        background: var(--md-sys-color-primary, #0066cc);
        color: white;
    }

    .btn-primary:hover {
        background: var(--md-sys-color-on-primary, #0052a3);
    }

    .btn-secondary {
        background: var(--md-sys-color-secondary-container, #e6f0ff);
        color: var(--md-sys-color-on-secondary-container, #004c99);
    }

    .btn-secondary:hover {
        background: var(--md-sys-color-secondary, #0066cc);
        color: white;
    }

    .btn-sm {
        padding: 8px 16px;
        font-size: 13px;
    }

    .btn-lg {
        padding: 16px 32px;
        font-size: 16px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .pmb-scholarship-container {
            padding: 16px;
        }

        .scholarship-header {
            padding: 24px;
        }

        .scholarship-header h1 {
            font-size: 24px;
        }

        .scholarships-grid,
        .eligibility-cards {
            grid-template-columns: 1fr;
        }

        .cost-actions {
            flex-direction: column;
        }

        .cost-actions .btn {
            width: 100%;
        }
    }
</style>

<script>
    // Check eligibility
    function checkEligibility(scholarshipId) {
        // TODO: Implement actual eligibility check
        alert('Mengcheck eligibility untuk beasiswa ID: ' + scholarshipId);
    }

    // Apply scholarship
    function applyScholarship(scholarshipId) {
        if (confirm('Apakah kamu yakin ingin mengajukan beasiswa ini?')) {
            // TODO: Implement actual application
            alert('Pengajuan beasiswa berhasil dikirim!');
        }
    }

    // Recalculate
    function recalculate() {
        // TODO: Implement actual recalculation
        alert('Menghitung ulang beasiswa...');
    }
</script>