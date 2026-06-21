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
                    Definisikan dimensi/bagian dari test dalam format JSON.
                    Setiap dimensi akan menjadi kategori scoring terpisah.
                </p>

                <div class="form-group">
                    <label for="dimensions" class="form-label">Dimensi (JSON) <span class="required">*</span></label>
                    <textarea
                        id="dimensions"
                        name="dimensions"
                        class="form-input form-textarea code-editor"
                        rows="10"
                        placeholder='{"KEY": "Deskripsi"}'
                        required><?= htmlspecialchars($config['dimensions_json'] ?? '{}') ?></textarea>
                    <small class="form-hint">
                        <strong>Format JSON:</strong> {"KEY": "Deskripsi"}.
                        KEY akan digunakan sebagai identifier dimensi (misal: R, I, A untuk RIASEC atau Verbal, Numerik, Logika untuk IQ).
                    </small>
                </div>

                <div class="json-example">
                    <h3>Contoh Format Dimensi:</h3>

                    <details>
                        <summary>🧠 IQ Test (4 Dimensi)</summary>
                        <pre>{
  "verbal": "Kemampuan verbal dan bahasa",
  "numerical": "Kemampuan numerik dan matematika",
  "logical": "Penalaran logis",
  "spatial": "Kemampuan visual-spasial"
}</pre>
                    </details>

                    <details>
                        <summary>🎨 Learning Style VARK (4 Dimensi)</summary>
                        <pre>{
  "V": "Visual - Belajar dengan gambar dan diagram",
  "A": "Auditory - Belajar dengan mendengar",
  "R": "Read/Write - Belajar dengan membaca dan menulis",
  "K": "Kinesthetic - Belajar dengan praktik"
}</pre>
                    </details>

                    <details>
                        <summary>🧑 Personality - Big Five (5 Dimensi)</summary>
                        <pre>{
  "O": "Openness - Keterbukaan terhadap pengalaman",
  "C": "Conscientiousness - Kesadaran dan keteraturan",
  "E": "Extraversion - EkstroverSI",
  "A": "Agreeableness - Keramahan",
  "N": "Neuroticism - Stabilitas emosional"
}</pre>
                    </details>
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
</style>

<script>
    // Initialize function for SPA navigation compatibility
    function initEditForm() {
        // Prevent duplicate initialization
        if (window.isEditFormInitialized) return;
        window.isEditFormInitialized = true;

        const dimensionsInput = document.getElementById('dimensions');
        const scoringRulesInput = document.getElementById('scoring_rules');
        const form = document.querySelector('.test-config-form');

        // Skip if elements don't exist
        if (!dimensionsInput || !scoringRulesInput || !form) return;

        function validateJSON(value, fieldName) {
            try {
                JSON.parse(value);
                return true;
            } catch (e) {
                alert(`Format JSON untuk ${fieldName} tidak valid. Pastikan syntax JSON benar.`);
                return false;
            }
        }

        form.addEventListener('submit', function(e) {
            const dimensions = dimensionsInput.value.trim();
            const scoringRules = scoringRulesInput.value.trim();

            if (!validateJSON(dimensions, 'Dimensi')) {
                e.preventDefault();
                dimensionsInput.focus();
                return false;
            }

            if (!validateJSON(scoringRules, 'Aturan Scoring')) {
                e.preventDefault();
                scoringRulesInput.focus();
                return false;
            }

            // Validate dimensions is object
            const dimensionsObj = JSON.parse(dimensions);
            if (typeof dimensionsObj !== 'object' || Array.isArray(dimensionsObj)) {
                e.preventDefault();
                alert('Dimensi harus berupa JSON Object (menggunakan kurung kurawal {})');
                dimensionsInput.focus();
                return false;
            }

            // Validate scoring_rules is array
            const scoringRulesObj = JSON.parse(scoringRules);
            if (!Array.isArray(scoringRulesObj)) {
                e.preventDefault();
                alert('Aturan Scoring harus berupa JSON Array (menggunakan kurung s [])');
                scoringRulesInput.focus();
                return false;
            }

            // Validate scoring rules have min, max, label
            for (let i = 0; i < scoringRulesObj.length; i++) {
                const rule = scoringRulesObj[i];
                if (!rule.hasOwnProperty('min') || !rule.hasOwnProperty('max') || !rule.hasOwnProperty('label')) {
                    e.preventDefault();
                    alert(`Aturan scoring ke-${i + 1} harus memiliki min, max, dan label`);
                    scoringRulesInput.focus();
                    return false;
                }
            }
        });
    }

    // Initialize on initial page load
    document.addEventListener('DOMContentLoaded', initEditForm);

    // Initialize on SPA navigation (Mazu custom event)
    window.addEventListener('spa:navigated', initEditForm);
</script>