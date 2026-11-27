<?php
/**
 * Metode pembayaran (radio + panel instruksi pembayaran).
 *
 * Butuh:
 *   $metode_pembayaran_terpilih -> kunci metode yang aktif
 *   $daftar_bayar               -> daftar_pembayaran()
 *   $kirim                      -> 'diantar' atau lainnya (COD disembunyikan bila diantar)
 *   $pengaturan                  -> pengaturan_toko()
 */
$pkirih  = ($kirim ?? '') === 'diantar';

?>
<div class="pilih-metode">
    <?php foreach ($daftar_bayar as $kunci => $m): ?>
        <label class="pilih-kartu">
            <input type="radio" name="metode_pembayaran" value="<?= e($kunci) ?>"
                   class="form-check-input mt-1"
                   data-kunci="<?= e($kunci) ?>"
                   <?= ($metode_pembayaran_terpilih ?? '') === $kunci ? 'checked' : '' ?>
                   required>
            <div>
                <div class="fw-semibold"><i class="bi <?= e($m['Ikon']) ?>"></i> <?= e($m['Label']) ?></div>
                <div class="text-muted small"><?= e($m['Keterangan']) ?></div>
            </div>
        </label>
    <?php endforeach; ?>
</div>

<!-- Panel instruksi pembayaran, berubah sesuai metode yang dipilih -->
<div class="mt-3" id="panelPembayaran">

    <div class="alert alert-info d-none" data-panel="bayar_di_tempat">
        <i class="bi bi-info-circle"></i>
        <strong>Bayar di tempat.</strong> Siapkan uang tunai sejumlah
        <span class="jumlah-tagihan"><?= rupiah($total_bayar) ?></span>.
        Bayar ke kasir saat Anda mengambil barang di toko. Pesanan menunggu di kasir
        sampai Anda datang membayar.
    </div>

    <div class="alert alert-primary d-none" data-panel="transfer">
        <div class="fw-semibold mb-2"><i class="bi bi-bank"></i> Transfer ke rekening toko</div>
        <table class="table table-sm mb-2" style="max-width:420px">
            <tr><td class="text-muted small" style="width:110px">Bank</td><td class="small fw-semibold"><?= e($pengaturan['nama_bank'] ?? '-') ?></td></tr>
            <tr><td class="text-muted small">No. Rekening</td><td class="small fw-semibold"><code><?= e($pengaturan['nomor_rekening'] ?? '-') ?></code></td></tr>
            <tr><td class="text-muted small">Atas Nama</td><td class="small fw-semibold"><?= e($pengaturan['atas_nama_rekening'] ?? '-') ?></td></tr>
            <tr><td class="text-muted small">Jumlah</td><td class="small fw-semibold"><?= rupiah($total_bayar) ?></td></tr>
        </table>
        <div class="small mb-2">Unggah bukti transfer agar pesanan segera diverifikasi admin.</div>
        <input type="file" name="bukti_transfer" class="form-control form-control-sm" accept=".jpg,.jpeg,.png,.webp" style="max-width:340px">
        <div class="form-text">Format JPG/PNG, maksimal 2 MB.</div>
    </div>

    
</div>
