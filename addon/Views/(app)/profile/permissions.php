<?php

/**
 * Staff - Permissions View
 * 
 * @var array $profile Profile data
 * @var array|null $staffProfile Staff profile data
 */

// Decode permissions
$permissions = !empty($staffProfile['permissions']) ? json_decode($staffProfile['permissions'], true) : [];

// Define permission groups
$permissionGroups = [
    'user_management' => [
        'title' => 'Manajemen User',
        'icon' => '👥',
        'permissions' => [
            'users.view' => 'Lihat Daftar User',
            'users.create' => 'Buat User Baru',
            'users.edit' => 'Edit User',
            'users.delete' => 'Hapus User',
        ]
    ],
    'profile_management' => [
        'title' => 'Manajemen Profile',
        'icon' => '📋',
        'permissions' => [
            'profiles.view' => 'Lihat Profile',
            'profiles.edit' => 'Edit Profile',
            'profiles.all' => 'Akses Semua Profile',
        ]
    ],
    'academic_management' => [
        'title' => 'Manajemen Akademik',
        'icon' => '📚',
        'permissions' => [
            'academic.view' => 'Lihat Data Akademik',
            'academic.edit' => 'Edit Data Akademik',
            'schools.manage' => 'Kelola Sekolah',
        ]
    ],
    'test_management' => [
        'title' => 'Manajemen Test',
        'icon' => '📝',
        'permissions' => [
            'tests.view' => 'Lihat Test',
            'tests.create' => 'Buat Test Baru',
            'tests.edit' => 'Edit Test',
            'tests.delete' => 'Hapus Test',
            'tests.analyze' => 'Analisis Hasil Test',
        ]
    ],
    'report_management' => [
        'title' => 'Manajemen Laporan',
        'icon' => '📊',
        'permissions' => [
            'reports.view' => 'Lihat Laporan',
            'reports.export' => 'Export Laporan',
            'reports.generate' => 'Generate Laporan',
        ]
    ],
    'system_settings' => [
        'title' => 'Pengaturan Sistem',
        'icon' => '⚙️',
        'permissions' => [
            'settings.view' => 'Lihat Pengaturan',
            'settings.edit' => 'Edit Pengaturan',
            'permissions.manage' => 'Kelola Permissions',
        ]
    ],
];
?>

<div class="permissions-container">
    <div class="permissions-header">
        <div class="breadcrumb">
            <a data-spa href="/profile">Profile</a>
            <span class="separator">/</span>
            <span class="current">Permissions</span>
        </div>
        <h1>Permissions</h1>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($_GET['success']) ?>
        </div>
    <?php endif; ?>

    <!-- Current Role Info -->
    <div class="role-info-card">
        <div class="role-icon">🔐</div>
        <div class="role-content">
            <h3>Super Administrator</h3>
            <p>Anda memiliki akses penuh ke semua fitur sistem. Kelola permissions untuk staff lain dengan hati-hati.</p>
        </div>
    </div>

    <form id="permissions-form" class="permissions-form" method="POST">
        <?php foreach ($permissionGroups as $groupKey => $group): ?>
            <div class="permission-section">
                <div class="section-header">
                    <div class="section-title">
                        <span class="section-icon"><?= $group['icon'] ?></span>
                        <h2><?= $group['title'] ?></h2>
                    </div>
                    <label class="toggle-all">
                        <input type="checkbox" class="toggle-group" data-group="<?= $groupKey ?>">
                        <span class="toggle-label">Semua</span>
                    </label>
                </div>

                <div class="permission-grid">
                    <?php foreach ($group['permissions'] as $permKey => $permLabel): ?>
                        <label class="permission-item">
                            <input type="checkbox"
                                name="permissions[<?= $permKey ?>]"
                                value="1"
                                <?= !empty($permissions[$permKey]) ? 'checked' : '' ?>>
                            <span class="permission-label"><?= $permLabel ?></span>
                            <span class="permission-key"><?= $permKey ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="form-actions">
            <a data-spa href="/profile" class="btn btn-secondary">Kembali</a>
            <button type="submit" class="btn btn-primary">Simpan Permissions</button>
        </div>
    </form>
</div>

<style>
    .permissions-container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 24px;
    }

    .permissions-header {
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

    .permissions-header h1 {
        margin: 0;
        font-size: 28px;
        font-weight: 600;
    }

    /* Role Info Card */
    .role-info-card {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
        border-radius: 12px;
        padding: 20px;
        display: flex;
        gap: 16px;
        margin-bottom: 24px;
        color: white;
    }

    .role-icon {
        font-size: 32px;
    }

    .role-content h3 {
        margin: 0 0 8px 0;
        font-size: 16px;
        font-weight: 600;
    }

    .role-content p {
        margin: 0;
        font-size: 14px;
        opacity: 0.9;
        line-height: 1.5;
    }

    /* Permission Sections */
    .permission-section {
        background: var(--md-sys-color-surface-container-lowest, #ffffff);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 16px;
        border-bottom: 1px solid var(--md-sys-color-outline-variant, #e0e0e0);
        margin-bottom: 16px;
    }

    .section-title {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .section-icon {
        font-size: 24px;
    }

    .section-title h2 {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
        color: var(--md-sys-color-on-surface, #1a1a1a);
    }

    .toggle-all {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
    }

    .toggle-all input {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }

    .toggle-label {
        font-size: 14px;
        font-weight: 500;
        color: var(--md-sys-color-primary, #0066cc);
    }

    /* Permission Grid */
    .permission-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 12px;
    }

    .permission-item {
        display: flex;
        flex-direction: column;
        gap: 6px;
        padding: 12px;
        background: var(--md-sys-color-surface-container, #f5f5f5);
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .permission-item:hover {
        background: var(--md-sys-color-surface-container-highest, #e8e8e8);
    }

    .permission-item input {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: var(--md-sys-color-primary, #0066cc);
    }

    .permission-label {
        font-size: 14px;
        color: var(--md-sys-color-on-surface, #1a1a1a);
    }

    .permission-key {
        font-size: 11px;
        color: var(--md-sys-color-on-surface-variant, #666);
        font-family: monospace;
        background: var(--md-sys-color-surface, #ffffff);
        padding: 2px 6px;
        border-radius: 4px;
        align-self: flex-start;
    }

    /* Form Actions */
    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        padding-top: 24px;
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
        .permission-grid {
            grid-template-columns: 1fr;
        }

        .section-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
        }

        .form-actions {
            flex-direction: column;
        }

        .form-actions .btn {
            width: 100%;
        }
    }
</style>

<script>
    // Toggle all permissions in a group
    document.querySelectorAll('.toggle-group').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const group = this.dataset.group;
            const checkboxes = document.querySelectorAll(`input[name^="permissions[${group}]"]`);
            checkboxes.forEach(cb => {
                cb.checked = this.checked;
            });
        });
    });

    // Update toggle state when individual checkbox changes
    document.querySelectorAll('.permission-item input').forEach(input => {
        input.addEventListener('change', function() {
            const groupCard = this.closest('.permission-section');
            const groupToggle = groupCard.querySelector('.toggle-group');
            const groupCheckboxes = groupCard.querySelectorAll('.permission-grid input[type="checkbox"]');
            const allChecked = Array.from(groupCheckboxes).every(cb => cb.checked);
            groupToggle.checked = allChecked;
        });
    });

    // Form submission
    document.getElementById('permissions-form').addEventListener('submit', async function(e) {
        e.preventDefault();

        const form = this;
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;

        submitBtn.disabled = true;
        submitBtn.textContent = 'Menyimpan...';

        const formData = new FormData(form);

        try {
            const response = await fetch(form.action || window.location.href, {
                method: 'POST',
                body: formData
            });

            if (response.redirected) {
                window.location.href = response.url;
            } else if (response.ok) {
                window.location.href = '/profile/permissions';
            } else {
                const error = await response.text();
                alert('Gagal menyimpan: ' + error);
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        } catch (error) {
            alert('Terjadi kesalahan: ' + error.message);
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    });
</script>