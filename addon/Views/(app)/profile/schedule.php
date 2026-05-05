<?php

/**
 * Teacher - Counseling Schedule View
 * 
 * @var array $profile Profile data
 * @var array|null $teacherProfile Teacher profile data
 */

// Decode counseling schedule
$schedule = !empty($teacherProfile['counseling_schedule']) ? json_decode($teacherProfile['counseling_schedule'], true) : [];

// Group schedule by day
$daysOfWeek = [
    'monday' => 'Senin',
    'tuesday' => 'Selasa',
    'wednesday' => 'Rabu',
    'thursday' => 'Kamis',
    'friday' => 'Jumat',
    'saturday' => 'Sabtu',
    'sunday' => 'Minggu'
];
?>

<div class="schedule-container">
    <div class="schedule-header">
        <div class="breadcrumb">
            <a href="/profile">Profile</a>
            <span class="separator">/</span>
            <span class="current">Jadwal Konseling</span>
        </div>
        <h1>Jadwal Konseling</h1>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($_GET['success']) ?>
        </div>
    <?php endif; ?>

    <!-- Schedule Info Card -->
    <div class="schedule-info-card">
        <div class="info-icon">📅</div>
        <div class="info-content">
            <h3>Jadwal Konseling Guru BK</h3>
            <p>Siswa dapat melakukan konseling sesuai jadwal yang tersedia. Untuk jadwal di luar jam yang ditentukan, silakan berkoordinasi langsung.</p>
        </div>
    </div>

    <!-- Weekly Schedule -->
    <div class="schedule-section">
        <h2>Jadwal Mingguan</h2>

        <div class="weekly-schedule">
            <?php foreach ($daysOfWeek as $dayKey => $dayName): ?>
                <div class="day-card <?= in_array($dayKey, ['saturday', 'sunday']) ? 'weekend' : '' ?>">
                    <div class="day-header">
                        <span class="day-name"><?= $dayName ?></span>
                        <?php if (!empty($schedule[$dayKey]) && !empty($schedule[$dayKey]['is_active'])): ?>
                            <span class="day-status active">Buka</span>
                        <?php else: ?>
                            <span class="day-status closed">Tutup</span>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($schedule[$dayKey]) && !empty($schedule[$dayKey]['is_active'])): ?>
                        <div class="day-schedule">
                            <?php if (!empty($schedule[$dayKey]['sessions'])): ?>
                                <?php foreach ($schedule[$dayKey]['sessions'] as $session): ?>
                                    <div class="time-slot">
                                        <span class="time-range">
                                            <?= htmlspecialchars($session['start'] ?? '-') ?> - <?= htmlspecialchars($session['end'] ?? '-') ?>
                                        </span>
                                        <?php if (!empty($session['location'])): ?>
                                            <span class="location">📍 <?= htmlspecialchars($session['location']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="no-schedule">Tidak ada jadwal</div>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="day-closed">
                            <span>Tidak tersedia</span>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Notes Section -->
    <?php if (!empty($schedule['notes'])): ?>
        <div class="notes-section">
            <h2>Catatan</h2>
            <div class="notes-content">
                <?= nl2br(htmlspecialchars($schedule['notes'])) ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
    .schedule-container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 24px;
    }

    .schedule-header {
        margin-bottom: 24px;
    }

    .breadcrumb {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        color: var(--md-sys-color-on-surface-variant, #666);
        margin-bottom: 8px;
    }

    .breadcrumb a {
        color: var(--md-sys-color-primary, #0066cc);
        text-decoration: none;
    }

    .breadcrumb a:hover {
        text-decoration: underline;
    }

    .breadcrumb .separator {
        color: var(--md-sys-color-on-surface-variant, #999);
    }

    .breadcrumb .current {
        color: var(--md-sys-color-on-surface, #1a1a1a);
    }

    .schedule-header h1 {
        margin: 0;
        font-size: 28px;
        font-weight: 600;
    }

    /* Info Card */
    .schedule-info-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
        padding: 20px;
        display: flex;
        gap: 16px;
        margin-bottom: 24px;
        color: white;
    }

    .info-icon {
        font-size: 32px;
    }

    .info-content h3 {
        margin: 0 0 8px 0;
        font-size: 16px;
        font-weight: 600;
    }

    .info-content p {
        margin: 0;
        font-size: 14px;
        opacity: 0.9;
        line-height: 1.5;
    }

    /* Schedule Section */
    .schedule-section {
        background: var(--md-sys-color-surface-container-lowest, #ffffff);
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .schedule-section h2 {
        margin: 0 0 20px 0;
        font-size: 20px;
        font-weight: 600;
        color: var(--md-sys-color-on-surface, #1a1a1a);
    }

    /* Weekly Schedule */
    .weekly-schedule {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 16px;
    }

    .day-card {
        background: var(--md-sys-color-surface-container, #f5f5f5);
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.2s;
    }

    .day-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .day-card.weekend {
        background: var(--md-sys-color-surface-container-highest, #e8e8e8);
    }

    .day-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px;
        background: var(--md-sys-color-surface-container-highest, #e8e8e8);
    }

    .day-name {
        font-size: 16px;
        font-weight: 600;
        color: var(--md-sys-color-on-surface, #1a1a1a);
    }

    .day-status {
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }

    .day-status.active {
        background: var(--md-sys-color-secondary-container, #e8f5e9);
        color: var(--md-sys-color-on-secondary-container, #2e7d32);
    }

    .day-status.closed {
        background: var(--md-sys-color-surface, #ffffff);
        color: var(--md-sys-color-on-surface-variant, #666);
    }

    .day-schedule {
        padding: 16px;
    }

    .time-slot {
        display: flex;
        flex-direction: column;
        gap: 4px;
        padding: 12px;
        background: var(--md-sys-color-surface, #ffffff);
        border-radius: 8px;
        margin-bottom: 8px;
        border-left: 3px solid var(--md-sys-color-primary, #0066cc);
    }

    .time-slot:last-child {
        margin-bottom: 0;
    }

    .time-range {
        font-size: 14px;
        font-weight: 600;
        color: var(--md-sys-color-primary, #0066cc);
    }

    .location {
        font-size: 13px;
        color: var(--md-sys-color-on-surface-variant, #666);
    }

    .no-schedule {
        text-align: center;
        padding: 20px;
        color: var(--md-sys-color-on-surface-variant, #666);
        font-size: 14px;
    }

    .day-closed {
        text-align: center;
        padding: 24px;
        color: var(--md-sys-color-on-surface-variant, #999);
    }

    /* Notes Section */
    .notes-section {
        background: var(--md-sys-color-surface-container-lowest, #ffffff);
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .notes-section h2 {
        margin: 0 0 16px 0;
        font-size: 20px;
        font-weight: 600;
        color: var(--md-sys-color-on-surface, #1a1a1a);
    }

    .notes-content {
        background: var(--md-sys-color-surface-container, #f5f5f5);
        border-radius: 8px;
        padding: 16px;
        font-size: 14px;
        line-height: 1.6;
        color: var(--md-sys-color-on-surface, #1a1a1a);
    }

    /* Alert */
    .alert {
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .alert-success {
        background: var(--md-sys-color-secondary-container, #e8f5e9);
        color: var(--md-sys-color-on-secondary-container, #2e7d32);
        border: 1px solid var(--md-sys-color-secondary, #4caf50);
    }

    @media (max-width: 768px) {
        .weekly-schedule {
            grid-template-columns: 1fr;
        }

        .schedule-info-card {
            flex-direction: column;
        }
    }
</style>