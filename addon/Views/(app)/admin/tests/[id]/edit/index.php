<?php

/**
 * Form edit konfigurasi test
 * @var array $config - Data konfigurasi yang akan diedit
 * @var array $testTypes - Daftar tipe test yang valid
 */
?>

<div class="test-config-form-page">
    <div class="page-header">
        <div>
            <a data-spa href="/admin/tests" class="back-link">
                ← Kembali ke Daftar Konfigurasi
            </a>
            <h1>Edit Konfigurasi Tes</h1>
            <p class="page-description">Ubah konfigurasi tes yang sudah ada</p>
        </div>
    </div>

    <div class="form-container">
        <form data-spa method="POST" action="/admin/tests/<?= $config['id'] ?>/update" class="test-config-form">
            <div class="form-section">
                <h2>Informasi Dasar</h2>

                <div class="form-group">
                    <label for="name" class="form-label">Nama Konfigurasi <span class="required">*</span></label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="form-input"
                        value="<?= htmlspecialchars($config['name']) ?>"
                        placeholder="Contoh: Test IQ Standard, Test Learning Style VARK"
                        required>
                    <small class="form-hint">Nama harus unik dan deskriptif</small>
                </div>

                <div class="form-group">
                    <label for="test_type" class="form-label">Tipe Test <span class="required">*</span></label>
                    <select id="test_type" name="test_type" class="form-input" required>
                        <option value="">Pilih Tipe Test</option>
                        <?php foreach ($testTypes as $type): ?>
                            <option value="<?= $type ?>" <?= $config['test_type'] === $type ? 'selected' : '' ?>>
                                <?= htmlspecialchars($type) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="form-hint">Pilih tipe test yang sesuai. Setiap tipe memiliki karakteristik berbeda.</small>
                </div>

                <div class="form-group">
                    <label for="description" class="form-label">Deskripsi</label>
                    <textarea
                        id="description"
                        name="description"
                        class="form-input form-textarea"
                        rows="3"
                        placeholder="Deskripsi singkat tentang test ini (opsional)"><?= htmlspecialchars($config['description'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="form-section">
                <h2>Dimensi Test <span class="required">*</span></h2>
                <p class="section-description">
                    Definisikan dimensi/bagian dari test. Setiap dimensi akan menjadi kategori scoring terpisah.
                </p>

                <!-- Dimensions Builder Toolbar -->
                <div class="dimensions-builder-toolbar">
                    <button type="button" class="btn btn-add-dimension" id="addDimensionBtn">
                        <span>➕</span> Tambah Dimensi
                    </button>
                    <div class="template-selector">
                        <label for="template-select">Load Template:</label>
                        <select id="template-select" class="form-input">
                            <option value="">Pilih Template...</option>
                            <option value="riasec">🧠 RIASEC (6 Dimensi)</option>
                            <option value="iq">📊 IQ Test (4 Dimensi)</option>
                            <option value="vark">🎨 VARK Learning Style (4 Dimensi)</option>
                            <option value="bigfive">🧑 Big Five Personality (5 Dimensi)</option>
                        </select>
                    </div>
                </div>

                <!-- Dimensions List -->
                <div class="dimensions-builder-list" id="dimensionsList">
                    <div class="empty-dimensions" id="emptyDimensions">
                        <div class="empty-icon">📭</div>
                        <p>Belum ada dimensi</p>
                        <p class="empty-hint">Klik "Tambah Dimensi" atau pilih template untuk memulai</p>
                    </div>
                    <!-- Dimension cards will be inserted here -->
                </div>

                <!-- Hidden input for JSON submit -->
                <input type="hidden" name="dimensions" id="dimensions-json" value='{}'>

                <!-- JSON Preview -->
                <div class="json-preview-section">
                    <label class="form-label">JSON Preview (Auto-generated)</label>
                    <div class="json-preview-container">
                        <pre id="jsonPreview">{}</pre>
                        <button type="button" class="btn-copy-json" id="copyJsonBtn" title="Copy JSON">
                            📋
                        </button>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h2>Aturan Scoring <span class="required">*</span></h2>
                <p class="section-description">
                    Definisikan kategori hasil berdasarkan range skor.
                    Setiap dimensi akan menggunakan aturan scoring yang sama.
                </p>

                <div class="form-group">
                    <label for="scoring_rules" class="form-label">Aturan Scoring (JSON) <span class="required">*</span></label>
                    <textarea
                        id="scoring_rules"
                        name="scoring_rules"
                        class="form-input form-textarea code-editor"
                        rows="10"
                        placeholder='[{"min": 0, "max": 100, "label": "Normal"}]'
                        required><?= htmlspecialchars($config['scoring_rules_json'] ?? '[]') ?></textarea>
                    <small class="form-hint">
                        <strong>Format JSON:</strong> Array of {min, max, label, description}.
                        Pastikan range tidak overlap dan mencakup semua kemungkinan skor.
                    </small>
                </div>

                <div class="json-example">
                    <h3>Contoh Format Scoring Rules:</h3>

                    <details>
                        <summary>📊 Skor Persentase (0-100)</summary>
                        <pre>[
  {
    "min": 0,
    "max": 40,
    "label": "Rendah",
    "description": "Perlu bimbingan lebih lanjut"
  },
  {
    "min": 41,
    "max": 70,
    "label": "Sedang",
    "description": "Cukup baik, terus tingkatkan"
  },
  {
    "min": 71,
    "max": 100,
    "label": "Tinggi",
    "description": "Sangat baik, pertahankan"
  }
]</pre>
                    </details>

                    <details>
                        <summary>🎯 Skor IQ (50-150)</summary>
                        <pre>[
  {
    "min": 50,
    "max": 84,
    "label": "Below Average",
    "description": "Di bawah rata-rata"
  },
  {
    "min": 85,
    "max": 114,
    "label": "Average",
    "description": "Rata-rata"
  },
  {
    "min": 115,
    "max": 129,
    "label": "Above Average",
    "description": "Di atas rata-rata"
  },
  {
    "min": 130,
    "max": 150,
    "label": "Superior",
    "description": "Sangat unggul"
  }
]</pre>
                    </details>
                </div>
            </div>

            <div class="form-section">
                <h2>Pengaturan</h2>

                <div class="form-group">
                    <label class="form-label checkbox-label">
                        <input
                            type="checkbox"
                            name="is_active"
                            value="1"
                            <?= $config['is_active'] ? 'checked' : '' ?>>
                        <span class="checkbox-text">Aktifkan konfigurasi ini setelah dibuat</span>
                    </label>
                    <small class="form-hint">Konfigurasi yang aktif dapat langsung digunakan oleh sekolah</small>
                </div>
            </div>

            <div class="form-actions">
                <a data-spa href="/admin/tests" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <span>💾</span> Update Konfigurasi
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Add/Edit Dimension Modal -->
<div class="modal-overlay" id="dimensionModal">
    <div class="modal-container">
        <div class="modal-header">
            <h3 id="modalTitle">Tambah Dimensi</h3>
            <button type="button" class="btn-close-modal" id="closeModalBtn">&times;</button>
        </div>
        <div class="modal-body">
            <form id="dimensionForm">
                <input type="hidden" id="editIndex" value="-1">

                <div class="form-group">
                    <label for="dimensionKey" class="form-label">Key <span class="required">*</span></label>
                    <input
                        type="text"
                        id="dimensionKey"
                        class="form-input"
                        maxlength="3"
                        placeholder="R"
                        required
                        style="text-transform: uppercase;">
                    <small class="form-hint">Key unik, maksimal 3 karakter (huruf kapital/angka)</small>
                </div>

                <div class="form-group">
                    <label for="dimensionLabel" class="form-label">Label <span class="required">*</span></label>
                    <input
                        type="text"
                        id="dimensionLabel"
                        class="form-input"
                        placeholder="Realistic"
                        required>
                    <small class="form-hint">Nama lengkap dimensi</small>
                </div>

                <div class="form-group">
                    <label for="dimensionColor" class="form-label">Color <span class="required">*</span></label>
                    <div class="color-input-group">
                        <input
                            type="color"
                            id="dimensionColorPicker"
                            class="color-picker"
                            value="#3B6D11">
                        <input
                            type="text"
                            id="dimensionColor"
                            class="form-input color-text-input"
                            placeholder="#3B6D11"
                            maxlength="7"
                            value="#3B6D11"
                            required>
                    </div>
                    <small class="form-hint">Format hex color (#RRGGBB)</small>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" id="cancelModalBtn">Batal</button>
            <button type="button" class="btn btn-primary" id="saveDimensionBtn">
                <span>💾</span> Simpan
            </button>
        </div>
    </div>
</div>

<style>
    .test-config-form-page {
        padding: 2rem;
        max-width: 900px;
        margin: 0 auto;
    }

    .page-header {
        margin-bottom: 2rem;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #6b7280;
        text-decoration: none;
        font-size: 14px;
        margin-bottom: 12px;
        transition: color 0.2s;
    }

    .back-link:hover {
        color: #374151;
    }

    .page-header h1 {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 0.5rem;
    }

    .page-description {
        color: #666;
        font-size: 0.95rem;
    }

    .form-container {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        padding: 2rem;
    }

    .form-section {
        margin-bottom: 2rem;
        padding-bottom: 2rem;
        border-bottom: 1px solid #e5e7eb;
    }

    .form-section:last-of-type {
        border-bottom: none;
    }

    .form-section h2 {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 0.5rem;
    }

    .section-description {
        color: #666;
        font-size: 0.9rem;
        margin-bottom: 1rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        font-weight: 500;
        color: #374151;
        margin-bottom: 0.5rem;
    }

    .required {
        color: #ef4444;
    }

    .form-input {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 0.95rem;
        transition: border-color 0.2s;
    }

    .form-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .form-textarea {
        font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
        font-size: 0.85rem;
        line-height: 1.5;
        resize: vertical;
    }

    .form-hint {
        display: block;
        margin-top: 0.5rem;
        color: #6b7280;
        font-size: 0.85rem;
    }

    .checkbox-label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
    }

    .checkbox-label input[type="checkbox"] {
        width: auto;
        cursor: pointer;
    }

    .checkbox-text {
        font-weight: 400;
    }

    .json-example {
        margin-top: 1.5rem;
        padding: 1rem;
        background: #f3f4f6;
        border-radius: 8px;
    }

    .json-example h3 {
        font-size: 1rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 1rem;
    }

    .json-example details {
        margin-bottom: 1rem;
    }

    .json-example details:last-child {
        margin-bottom: 0;
    }

    .json-example summary {
        cursor: pointer;
        font-weight: 500;
        color: #1a1a1a;
        padding: 0.5rem;
        background: #e5e7eb;
        border-radius: 4px;
        margin-bottom: 0.5rem;
    }

    .json-example summary:hover {
        background: #d1d5db;
    }

    .json-example pre {
        background: #1a1a1a;
        color: #10b981;
        padding: 1rem;
        border-radius: 6px;
        overflow-x: auto;
        font-size: 0.8rem;
        line-height: 1.6;
    }

    .form-actions {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        margin-top: 2rem;
    }

    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 6px;
        font-weight: 500;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        border: none;
    }

    .btn-secondary {
        background: #f3f4f6;
        color: #374151;
    }

    .btn-secondary:hover {
        background: #e5e7eb;
    }

    .btn-primary {
        background: #3b82f6;
        color: #fff;
    }

    .btn-primary:hover {
        background: #2563eb;
    }

    /* Dimensions Builder Styles */
    .dimensions-builder-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        padding: 1rem;
        background: #f9fafb;
        border-radius: 8px;
    }

    .btn-add-dimension {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.625rem 1.25rem;
        background: #10b981;
        color: #fff;
        border: none;
        border-radius: 6px;
        font-weight: 500;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-add-dimension:hover {
        background: #059669;
    }

    .template-selector {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .template-selector label {
        font-weight: 500;
        color: #374151;
        font-size: 0.9rem;
    }

    .template-selector select {
        width: 250px;
    }

    .dimensions-builder-list {
        min-height: 150px;
        margin-bottom: 1rem;
    }

    .empty-dimensions {
        text-align: center;
        padding: 3rem 1rem;
        color: #9ca3af;
        border: 2px dashed #e5e7eb;
        border-radius: 8px;
    }

    .empty-icon {
        font-size: 3rem;
        margin-bottom: 0.5rem;
        opacity: 0.5;
    }

    .empty-hint {
        font-size: 0.85rem;
        margin-top: 0.25rem;
    }

    .dimension-card {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        margin-bottom: 0.75rem;
        transition: all 0.2s;
    }

    .dimension-card:hover {
        border-color: #3b82f6;
        box-shadow: 0 2px 4px rgba(59, 130, 246, 0.1);
    }

    .dimension-key {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 40px;
        height: 40px;
        padding: 0 0.75rem;
        background: #3b82f6;
        color: #fff;
        font-weight: 700;
        font-size: 1rem;
        border-radius: 6px;
        text-transform: uppercase;
    }

    .dimension-info {
        flex: 1;
    }

    .dimension-label {
        font-weight: 600;
        color: #1a1a1a;
        font-size: 0.95rem;
    }

    .dimension-color-preview {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        margin-left: 0.75rem;
        font-size: 0.85rem;
        color: #6b7280;
    }

    .color-dot {
        width: 16px;
        height: 16px;
        border-radius: 4px;
        border: 1px solid rgba(0, 0, 0, 0.1);
    }

    .dimension-actions {
        display: flex;
        gap: 0.5rem;
    }

    .btn-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        background: #fff;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 1rem;
    }

    .btn-icon:hover {
        background: #f3f4f6;
    }

    .btn-edit:hover {
        border-color: #3b82f6;
    }

    .btn-delete:hover {
        background: #fee2e2;
        border-color: #fca5a5;
    }

    /* JSON Preview */
    .json-preview-section {
        margin-top: 1.5rem;
    }

    .json-preview-container {
        position: relative;
        background: #1a1a1a;
        border-radius: 8px;
        padding: 1rem;
    }

    #jsonPreview {
        color: #10b981;
        font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
        font-size: 0.8rem;
        line-height: 1.6;
        margin: 0;
        white-space: pre-wrap;
        word-wrap: break-word;
        max-height: 300px;
        overflow-y: auto;
    }

    .btn-copy-json {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        padding: 0.375rem 0.75rem;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 4px;
        color: #fff;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-copy-json:hover {
        background: rgba(255, 255, 255, 0.2);
    }

    /* Modal Styles */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }

    .modal-overlay.active {
        display: flex;
    }

    .modal-container {
        background: #fff;
        border-radius: 12px;
        width: 90%;
        max-width: 450px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.25rem;
        border-bottom: 1px solid #e5e7eb;
    }

    .modal-header h3 {
        font-size: 1.125rem;
        font-weight: 600;
        color: #1a1a1a;
        margin: 0;
    }

    .btn-close-modal {
        background: none;
        border: none;
        font-size: 1.5rem;
        color: #6b7280;
        cursor: pointer;
        padding: 0;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        transition: all 0.2s;
    }

    .btn-close-modal:hover {
        background: #f3f4f6;
        color: #1a1a1a;
    }

    .modal-body {
        padding: 1.25rem;
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        padding: 1.25rem;
        border-top: 1px solid #e5e7eb;
    }

    /* Color Input Group */
    .color-input-group {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }

    .color-picker {
        width: 50px;
        height: 42px;
        padding: 0.25rem;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        cursor: pointer;
        background: #fff;
    }

    .color-text-input {
        flex: 1;
        text-transform: uppercase;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .dimensions-builder-toolbar {
            flex-direction: column;
            gap: 1rem;
        }

        .template-selector {
            width: 100%;
        }

        .template-selector select {
            width: 100%;
        }

        .dimension-card {
            flex-wrap: wrap;
        }

        .dimension-actions {
            width: 100%;
            justify-content: flex-end;
        }
    }
</style>

<script>
    // Template presets
    const DIMENSION_TEMPLATES = {
        riasec: {
            "R": {
                "label": "Realistic",
                "color": "#3B6D11"
            },
            "I": {
                "label": "Investigative",
                "color": "#185FA5"
            },
            "A": {
                "label": "Artistic",
                "color": "#854F0B"
            },
            "S": {
                "label": "Social",
                "color": "#3C3489"
            },
            "E": {
                "label": "Enterprising",
                "color": "#993C1D"
            },
            "C": {
                "label": "Conventional",
                "color": "#5F5E5A"
            }
        },
        iq: {
            "V": {
                "label": "Verbal",
                "color": "#3B6D11"
            },
            "N": {
                "label": "Numerical",
                "color": "#185FA5"
            },
            "L": {
                "label": "Logical",
                "color": "#854F0B"
            },
            "S": {
                "label": "Spatial",
                "color": "#3C3489"
            }
        },
        vark: {
            "V": {
                "label": "Visual",
                "color": "#3B6D11"
            },
            "A": {
                "label": "Auditory",
                "color": "#185FA5"
            },
            "R": {
                "label": "Read/Write",
                "color": "#854F0B"
            },
            "K": {
                "label": "Kinesthetic",
                "color": "#3C3489"
            }
        },
        bigfive: {
            "O": {
                "label": "Openness",
                "color": "#3B6D11"
            },
            "C": {
                "label": "Conscientiousness",
                "color": "#185FA5"
            },
            "E": {
                "label": "Extraversion",
                "color": "#854F0B"
            },
            "A": {
                "label": "Agreeableness",
                "color": "#3C3489"
            },
            "N": {
                "label": "Neuroticism",
                "color": "#993C1D"
            }
        }
    };

    // State
    let dimensions = {};

    // Initialize on page load
    function initDimensionsBuilder() {
        if (window.isDimensionsBuilderInitialized) return;
        window.isDimensionsBuilderInitialized = true;

        // Get initial dimensions from PHP
        const initialDimensionsJson = '<?= htmlspecialchars(json_encode($config['dimensions'] ?? []), ENT_QUOTES, 'UTF-8') ?>';

        try {
            dimensions = JSON.parse(initialDimensionsJson) || {};
        } catch (e) {
            dimensions = {};
        }

        // DOM Elements
        const addDimensionBtn = document.getElementById('addDimensionBtn');
        const templateSelect = document.getElementById('template-select');
        const dimensionsList = document.getElementById('dimensionsList');
        const emptyDimensions = document.getElementById('emptyDimensions');
        const dimensionsJsonInput = document.getElementById('dimensions-json');
        const jsonPreview = document.getElementById('jsonPreview');
        const copyJsonBtn = document.getElementById('copyJsonBtn');

        // Modal Elements
        const dimensionModal = document.getElementById('dimensionModal');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const cancelModalBtn = document.getElementById('cancelModalBtn');
        const saveDimensionBtn = document.getElementById('saveDimensionBtn');
        const modalTitle = document.getElementById('modalTitle');
        const editIndexInput = document.getElementById('editIndex');
        const dimensionKeyInput = document.getElementById('dimensionKey');
        const dimensionLabelInput = document.getElementById('dimensionLabel');
        const dimensionColorInput = document.getElementById('dimensionColor');
        const dimensionColorPicker = document.getElementById('dimensionColorPicker');

        // Initial render
        renderDimensions();

        // Render dimensions list
        function renderDimensions() {
            const keys = Object.keys(dimensions);

            if (keys.length === 0) {
                emptyDimensions.style.display = 'block';
                // Remove all dimension cards
                const cards = dimensionsList.querySelectorAll('.dimension-card');
                cards.forEach(card => card.remove());
            } else {
                emptyDimensions.style.display = 'none';

                // Clear existing cards
                const cards = dimensionsList.querySelectorAll('.dimension-card');
                cards.forEach(card => card.remove());

                // Create cards for each dimension
                keys.forEach((key, index) => {
                    const dim = dimensions[key];
                    const card = document.createElement('div');
                    card.className = 'dimension-card';
                    card.innerHTML = `
                        <span class="dimension-key">${escapeHtml(key)}</span>
                        <div class="dimension-info">
                            <span class="dimension-label">${escapeHtml(dim.label)}</span>
                            <span class="dimension-color-preview">
                                <span class="color-dot" style="background-color: ${dim.color}"></span>
                                ${dim.color}
                            </span>
                        </div>
                        <div class="dimension-actions">
                            <button type="button" class="btn-icon btn-edit" onclick="editDimension(${index})" title="Edit">
                                ✏️
                            </button>
                            <button type="button" class="btn-icon btn-delete" onclick="deleteDimension('${escapeHtml(key)}')" title="Hapus">
                                🗑️
                            </button>
                        </div>
                    `;
                    dimensionsList.appendChild(card);
                });
            }

            // Update hidden input and preview
            updateJsonOutput();
        }

        // Update JSON output
        function updateJsonOutput() {
            const jsonStr = JSON.stringify(dimensions, null, 2);
            dimensionsJsonInput.value = jsonStr;
            jsonPreview.textContent = jsonStr;
        }

        // Open modal for add
        function openAddModal() {
            modalTitle.textContent = 'Tambah Dimensi';
            editIndexInput.value = '-1';
            dimensionKeyInput.value = '';
            dimensionLabelInput.value = '';
            dimensionColorInput.value = '#3B6D11';
            dimensionColorPicker.value = '#3B6D11';
            dimensionModal.classList.add('active');
            dimensionKeyInput.focus();
        }

        // Open modal for edit
        window.editDimension = function(index) {
            const keys = Object.keys(dimensions);
            const key = keys[index];
            const dim = dimensions[key];

            modalTitle.textContent = 'Edit Dimensi';
            editIndexInput.value = index;
            dimensionKeyInput.value = key;
            dimensionLabelInput.value = dim.label;
            dimensionColorInput.value = dim.color;
            dimensionColorPicker.value = dim.color;
            dimensionModal.classList.add('active');
            dimensionKeyInput.focus();
        };

        // Delete dimension
        window.deleteDimension = function(key) {
            if (confirm(`Hapus dimensi "${key}"? Data tidak bisa dikembalikan.`)) {
                delete dimensions[key];
                renderDimensions();
            }
        };

        // Close modal
        function closeModal() {
            dimensionModal.classList.remove('active');
        }

        // Save dimension from modal
        function saveDimension() {
            const key = dimensionKeyInput.value.trim().toUpperCase();
            const label = dimensionLabelInput.value.trim();
            const color = dimensionColorInput.value.trim().toUpperCase();
            const editIndex = parseInt(editIndexInput.value);

            // Validation
            const errors = [];

            if (!key) {
                errors.push('Key harus diisi');
            } else if (!/^[A-Z0-9_]+$/.test(key)) {
                errors.push('Key hanya boleh berisi huruf kapital, angka, dan underscore');
            } else if (key.length > 3) {
                errors.push('Key maksimal 3 karakter');
            }

            if (!label) {
                errors.push('Label harus diisi');
            }

            if (!color || !/^#[0-9A-Fa-f]{6}$/.test(color)) {
                errors.push('Color harus format hex valid (#RRGGBB)');
            }

            // Check duplicate key (only if adding new or changing key)
            if (editIndex === -1 && dimensions.hasOwnProperty(key)) {
                errors.push(`Key "${key}" sudah ada`);
            } else if (editIndex !== -1) {
                const keys = Object.keys(dimensions);
                const oldKey = keys[editIndex];
                if (key !== oldKey && dimensions.hasOwnProperty(key)) {
                    errors.push(`Key "${key}" sudah ada`);
                }
            }

            if (errors.length > 0) {
                alert('Error:\n\n' + errors.join('\n'));
                return;
            }

            // Save
            if (editIndex === -1) {
                // Add new
                dimensions[key] = {
                    label,
                    color
                };
            } else {
                // Edit existing
                const keys = Object.keys(dimensions);
                const oldKey = keys[editIndex];
                if (oldKey !== key) {
                    // Key changed, need to reorder
                    delete dimensions[oldKey];
                    dimensions[key] = {
                        label,
                        color
                    };
                } else {
                    dimensions[key] = {
                        label,
                        color
                    };
                }
            }

            renderDimensions();
            closeModal();
        }

        // Load template
        function loadTemplate(templateName) {
            if (!templateName || !DIMENSION_TEMPLATES[templateName]) return;

            if (Object.keys(dimensions).length > 0) {
                if (!confirm('Load template akan replace semua dimensi existing. Lanjutkan?')) {
                    templateSelect.value = '';
                    return;
                }
            }

            dimensions = JSON.parse(JSON.stringify(DIMENSION_TEMPLATES[templateName]));
            renderDimensions();
            templateSelect.value = '';
        }

        // Sync color picker and text input
        function syncColorInputs(source, target) {
            target.value = source.value;
        }

        // Copy JSON to clipboard
        function copyJson() {
            const jsonStr = dimensionsJsonInput.value;
            navigator.clipboard.writeText(jsonStr).then(() => {
                const originalText = copyJsonBtn.textContent;
                copyJsonBtn.textContent = '✅';
                setTimeout(() => {
                    copyJsonBtn.textContent = originalText;
                }, 1500);
            }).catch(err => {
                alert('Gagal copy JSON: ' + err);
            });
        }

        // Escape HTML
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Event Listeners
        addDimensionBtn.addEventListener('click', openAddModal);
        closeModalBtn.addEventListener('click', closeModal);
        cancelModalBtn.addEventListener('click', closeModal);
        saveDimensionBtn.addEventListener('click', saveDimension);
        templateSelect.addEventListener('change', function() {
            loadTemplate(this.value);
        });
        dimensionColorPicker.addEventListener('input', function() {
            syncColorInputs(this, dimensionColorInput);
        });
        dimensionColorInput.addEventListener('input', function() {
            syncColorInputs(this, dimensionColorPicker);
        });
        copyJsonBtn.addEventListener('click', copyJson);

        // Close modal on overlay click
        dimensionModal.addEventListener('click', function(e) {
            if (e.target === dimensionModal) {
                closeModal();
            }
        });

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && dimensionModal.classList.contains('active')) {
                closeModal();
            }
        });

        // Handle Enter key in modal
        dimensionForm.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                saveDimension();
            }
        });

        // Form submit validation
        const form = document.querySelector('.test-config-form');
        form.addEventListener('submit', function(e) {
            const keys = Object.keys(dimensions);

            if (keys.length === 0) {
                e.preventDefault();
                alert('Minimal 1 dimensi harus ditambahkan');
                addDimensionBtn.focus();
                return false;
            }

            // Validate scoring_rules JSON
            const scoringRulesInput = document.getElementById('scoring_rules');
            try {
                const scoringRules = JSON.parse(scoringRulesInput.value.trim());
                if (!Array.isArray(scoringRules)) {
                    e.preventDefault();
                    alert('Aturan Scoring harus berupa JSON Array (menggunakan kurung s [])');
                    scoringRulesInput.focus();
                    return false;
                }
                for (let i = 0; i < scoringRules.length; i++) {
                    const rule = scoringRules[i];
                    if (!rule.hasOwnProperty('min') || !rule.hasOwnProperty('max') || !rule.hasOwnProperty('label')) {
                        e.preventDefault();
                        alert(`Aturan scoring ke-${i + 1} harus memiliki min, max, dan label`);
                        scoringRulesInput.focus();
                        return false;
                    }
                }
            } catch (e) {
                e.preventDefault();
                alert('Format JSON untuk Aturan Scoring tidak valid. Pastikan syntax JSON benar.');
                scoringRulesInput.focus();
                return false;
            }
        });
    }

    // Initialize on DOMContentLoaded and SPA navigation
    document.addEventListener('DOMContentLoaded', initDimensionsBuilder);
    window.addEventListener('spa:navigated', initDimensionsBuilder);
</script>