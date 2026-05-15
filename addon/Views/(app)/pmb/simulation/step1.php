<div class="step-form-section">
    <div class="step-header">
        <h2>Data Pribadi</h2>
        <p>Lengkapi informasi pribadi kamu dengan benar</p>
    </div>

    <form class="simulation-form" id="step-form-1">
        <div class="form-grid">
            <div class="form-group">
                <label for="full_name">Nama Lengkap *</label>
                <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($currentStepData['data']['full_name'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($currentStepData['data']['email'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="phone">No. Telepon *</label>
                <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($currentStepData['data']['phone'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="birth_place">Tempat Lahir *</label>
                <input type="text" id="birth_place" name="birth_place" value="<?= htmlspecialchars($currentStepData['data']['birth_place'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="birth_date">Tanggal Lahir *</label>
                <input type="date" id="birth_date" name="birth_date" value="<?= htmlspecialchars($currentStepData['data']['birth_date'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="gender">Jenis Kelamin *</label>
                <select id="gender" name="gender" required>
                    <option value="">Pilih</option>
                    <option value="male" <?= ($currentStepData['data']['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Laki-laki</option>
                    <option value="female" <?= ($currentStepData['data']['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Perempuan</option>
                </select>
            </div>
            <div class="form-group full-width">
                <label for="address">Alamat Lengkap *</label>
                <textarea id="address" name="address" rows="3" required><?= htmlspecialchars($currentStepData['data']['address'] ?? '') ?></textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="button" class="btn btn-secondary" onclick="saveDraft()">Simpan Draft</button>
            <button type="submit" class="btn btn-primary">Lanjut <span class="icon">→</span></button>
        </div>
    </form>
</div>