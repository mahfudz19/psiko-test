<div class="step-form-section">
    <div class="step-header">
        <h2>Upload Dokumen</h2>
        <p>Upload dokumen yang diperlukan dalam format PDF atau JPG (max 2MB)</p>
    </div>

    <form class="simulation-form" id="step-form-2">
        <div class="documents-list">
            <?php foreach ($currentStepData['documents'] ?? [] as $index => $doc): ?>
                <div class="document-item">
                    <div class="document-info">
                        <span class="document-name">
                            <?= $doc['is_uploaded'] ? '✅' : '⏳' ?>
                            <?= htmlspecialchars($doc['name']) ?>
                            <?php if ($doc['required']): ?>
                                <span class="required-badge">*</span>
                            <?php endif; ?>
                        </span>
                        <span class="document-status <?= $doc['is_uploaded'] ? 'uploaded' : 'pending' ?>">
                            <?= $doc['is_uploaded'] ? 'Sudah diupload' : 'Belum upload' ?>
                        </span>
                    </div>
                    <div class="document-upload">
                        <!-- File upload di-skip untuk simulasi -->
                        <input type="hidden" name="documents[<?= $index ?>][name]" value="<?= htmlspecialchars($doc['name']) ?>">
                        <input type="hidden" name="documents[<?= $index ?>][required]" value="<?= $doc['required'] ? '1' : '0' ?>">
                        <input type="hidden" name="documents[<?= $index ?>][is_uploaded]" value="1">
                        <span class="simulation-note">📝 Skip upload untuk simulasi</span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="upload-tips">
            <h4>💡 Tips Upload</h4>
            <ul>
                <li>Pastikan dokumen jelas dan terbaca</li>
                <li>Format: PDF atau JPG</li>
                <li>Ukuran maksimal: 2MB</li>
                <li>Kamu bisa lanjut nanti jika belum selesai</li>
            </ul>
        </div>

        <style>
            .simulation-note {
                font-size: 0.875rem;
                color: var(--text-secondary);
                font-style: italic;
            }
        </style>

        <div class="form-actions">
            <button type="button" class="btn btn-secondary" onclick="previousStep(1)">← Kembali</button>
            <button type="button" class="btn btn-secondary" onclick="saveDraft()">Simpan Draft</button>
            <button type="submit" class="btn btn-primary">Lanjut <span class="icon">→</span></button>
        </div>
    </form>
</div>