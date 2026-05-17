<div class="step-form-section">
    <div class="step-header">
        <h2>Pembayaran</h2>
        <p>Selesaikan pembayaran untuk menyelesaikan pendaftaran</p>
    </div>

    <div class="payment-section">
        <div class="payment-summary">
            <h3>Ringkasan Biaya</h3>
            <div class="summary-row">
                <span>Biaya Pendaftaran</span>
                <span>Rp <?= number_format($currentStepData['payment_info']['registration_fee'] ?? 500000, 0, ',', '.') ?></span>
            </div>
            <div class="summary-row discount">
                <span>Diskon</span>
                <span>- Rp <?= number_format($currentStepData['payment_info']['discount'] ?? 0, 0, ',', '.') ?></span>
            </div>
            <div class="summary-row total">
                <span>Total</span>
                <span>Rp <?= number_format($currentStepData['payment_info']['total'] ?? 500000, 0, ',', '.') ?></span>
            </div>
        </div>

        <div class="payment-methods">
            <h3>Transfer ke:</h3>
            <div class="bank-accounts">
                <?php foreach ($currentStepData['payment_info']['bank_accounts'] ?? [] as $bank): ?>
                    <div class="bank-card">
                        <div class="bank-header">
                            <span class="bank-name"><?= htmlspecialchars($bank['bank']) ?></span>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="copyAccount('<?= htmlspecialchars($bank['account']) ?>')">Copy</button>
                        </div>
                        <div class="bank-account-number" id="account-<?= htmlspecialchars($bank['account']) ?>">
                            <?= htmlspecialchars($bank['account']) ?>
                        </div>
                        <div class="bank-account-name"><?= htmlspecialchars($bank['name']) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="payment-confirmation">
            <h3>Konfirmasi Pembayaran</h3>
            <form class="simulation-form" id="step-form-3">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="sender_name">Nama Pengirim *</label>
                        <input type="text" id="sender_name" name="sender_name" required>
                    </div>
                    <div class="form-group">
                        <label for="transfer_date">Tanggal Transfer *</label>
                        <input type="date" id="transfer_date" name="transfer_date" required>
                    </div>
                    <div class="form-group full-width">
                        <label for="proof_upload">Bukti Transfer *</label>
                        <!-- File upload di-skip untuk simulasi -->
                        <input type="hidden" name="proof_upload" value="simulated_proof.jpg">
                        <span class="simulation-note">📝 Skip upload untuk simulasi</span>
                    </div>
                </div>

                <div class="checkbox-group">
                    <label>
                        <input type="checkbox" name="terms_accepted" required>
                        Saya menyetujui syarat dan ketentuan pendaftaran
                    </label>
                </div>
            </form>
        </div>
    </div>

    <div class="form-actions">
        <button type="button" class="btn btn-secondary" onclick="previousStep(2)">← Kembali</button>
        <button type="button" class="btn btn-secondary" onclick="saveDraft()">Simpan Draft</button>
        <button type="submit" class="btn btn-success" form="step-form-3">Konfirmasi Pembayaran</button>
    </div>
</div>