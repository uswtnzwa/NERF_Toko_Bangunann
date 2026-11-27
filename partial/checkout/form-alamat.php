<?php
/**
 * Form alamat pengiriman (muncul hanya kalau memilih "Diantar ke Alamat").
 *
 * Pelanggan bisa memilih salah satu alamat yang sudah tersimpan,
 * atau mengetik alamat baru. Alamat baru ikut disimpan ke
 * tabel alamat_pelanggan supaya bisa dipakai lagi.
 *
 * Toko hanya melayani satu kecamatan, jadi tidak ada pilihan kota:
 * pelanggan menulis alamat lengkap (jalan, nomor, RT/RW, kelurahan,
 * kecamatan) dan biaya antarnya sudah tetap.
 *
 * Butuh: $alamat_tersimpan (hasil query), $kirim,
 *        $data_alamat (nilai lama dari form sebelumnya).
 */
$perlu_alamat = ($kirim ?? '') === 'diantar';
$a = $data_alamat ?? [];
?>
<div id="panelAlamat" class="<?= $perlu_alamat ? '' : 'd-none' ?>">

    <?php if ($alamat_tersimpan): ?>
        <div class="mb-3">
            <label class="form-label fw-semibold small">Pakai alamat yang tersimpan</label>
            <?php foreach ($alamat_tersimpan as $al):
                $alamat_lengkap = trim($al['alamat']
                    . ($al['kode_pos'] ? ' ' . $al['kode_pos'] : '')); ?>
                <label class="pilih-kartu mb-2">
                    <input type="radio" name="id_alamat" value="<?= (int)$al['id_alamat'] ?>"
                           class="form-check-input mt-1"
                           data-nama="<?= e($al['nama_penerima'] ?: $al['nama_pelanggan'] ?? '') ?>"
                           data-hp="<?= e($al['nomor_hp']) ?>"
                           data-alamat="<?= e($al['alamat']) ?>">
                    <div>
                        <div class="fw-semibold">
                            <?= e($al['label'] ?? 'Alamat') ?>
                            <?php if ($al['utama'] === 'Ya'): ?>
                                <span class="badge bg-success">Utama</span>
                            <?php endif; ?>
                        </div>
                        <div class="text-muted small">
                            <?= e($al['nama_penerima'] ?? '') ?> &middot; <?= e($al['nomor_hp'] ?? '') ?><br>
                            <?= e($alamat_lengkap) ?>
                        </div>
                    </div>
                </label>
            <?php endforeach; ?>

            <label class="pilih-kartu mb-2">
                <input type="radio" name="id_alamat" value="0" class="form-check-input mt-1" data-baru="1" checked>
                <div>
                    <div class="fw-semibold"><i class="bi bi-pencil"></i> Pakai alamat baru</div>
                    <div class="text-muted small">Isi sendiri di bawah, otomatis disimpan untuk pesanan berikutnya.</div>
                </div>
            </label>
        </div>
    <?php else: ?>
        <input type="hidden" name="id_alamat" value="0">
    <?php endif; ?>

    <div class="row g-3 pt-1">
        <div class="col-md-6">
            <label class="form-label small fw-semibold">Nama Penerima <span class="text-danger">*</span></label>
            <input type="text" name="nama_penerima" class="form-control form-control-sm"
                   value="<?= e($a['nama_penerima'] ?? '') ?>" required>
        </div>
        <div class="col-md-6">
            <label class="form-label small fw-semibold">Nomor HP <span class="text-danger">*</span></label>
            <input type="text" name="nomor_hp" class="form-control form-control-sm"
                   value="<?= e($a['nomor_hp'] ?? '') ?>" placeholder="08xxxxxxxxxx" required>
            <div class="form-text">Kurir menghubungi nomor ini saat pengantaran.</div>
        </div>
        <div class="col-12">
            <label class="form-label small fw-semibold">Alamat Lengkap <span class="text-danger">*</span></label>
            <textarea name="alamat" rows="3" class="form-control form-control-sm" required
                      placeholder="Nama jalan, nomor rumah, RT/RW, kelurahan, kecamatan"><?= e($a['alamat'] ?? '') ?></textarea>
        </div>
        <div class="col-md-5">
            <label class="form-label small fw-semibold">Kode Pos</label>
            <input type="text" name="kode_pos" class="form-control form-control-sm"
                   value="<?= e($a['kode_pos'] ?? '') ?>" maxlength="10">
        </div>
        <div class="col-md-7 d-flex align-items-end">
            <div class="small text-muted">
                <i class="bi bi-geo-alt"></i> Pengiriman hanya dalam satu kecamatan.
                Ongkos antar <strong><?= rupiah($ongkir) ?></strong>.
            </div>
        </div>
        <div class="col-12">
            <label class="form-label small fw-semibold">Catatan untuk kurir (opsional)</label>
            <input type="text" name="catatan" class="form-control form-control-sm" maxlength="255"
                   value="<?= e($a['catatan'] ?? '') ?>"
                   placeholder="Contoh: titip ke pos ronda,-call dulu sebelum datang">
        </div>
    </div>
</div>
