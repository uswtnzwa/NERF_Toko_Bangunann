<?php
/**
 * Form preferensi notifikasi.
 * Butuh $preferensi dan PENGATURAN_NOTIF dari pengaturan-logic.php.
 */
$ikon = [
    'notif_email_pesanan'    => 'bi-truck',
    'notif_wa_pesanan'       => 'bi-whatsapp',
    'notif_email_promo'      => 'bi-tag',
    'notif_email_pembayaran' => 'bi-credit-card',
];
?>
<section id="notifikasi" class="pengaturan-kotak">
    <div class="pengaturan-judul">
        <span class="pengaturan-ikon"><i class="bi bi-bell"></i></span>
        <div>
            <h2>Preferensi Notifikasi</h2>
            <p>Pilih informasi yang ingin Anda terima dari NERF Toko Bangunan.</p>
        </div>
    </div>

    <form method="post">
        <input type="hidden" name="aksi" value="notifikasi">

        <div class="daftar-notifikasi">
            <?php foreach (PENGATURAN_NOTIF as $kunci => $label): ?>
                <?php $aktif = (int)($preferensi[$kunci] ?? 0) === 1; ?>
                <label class="notifikasi-item">
                    <span class="notifikasi-ikon"><i class="bi <?= $ikon[$kunci] ?>"></i></span>
                    <span class="flex-grow-1">
                        <span class="fw-semibold small d-block"><?= e($label) ?></span>
                        <span class="text-muted" style="font-size:12px">
                            <?= str_contains($kunci, 'wa_') ? 'Dikirim ke nomor HP Anda.' : 'Dikirim ke ' . e($pelanggan['email'] ?? 'email Anda') . '.' ?>
                        </span>
                    </span>
                    <span class="form-check form-switch m-0">
                        <input class="form-check-input" type="checkbox" name="<?= e($kunci) ?>"
                               value="1" <?= $aktif ? 'checked' : '' ?>>
                    </span>
                </label>
            <?php endforeach; ?>
        </div>

        <div class="pengaturan-aksi">
            <button class="btn btn-primary btn-sm"><i class="bi bi-check2-circle"></i> Simpan Preferensi</button>
        </div>
    </form>
</section>
