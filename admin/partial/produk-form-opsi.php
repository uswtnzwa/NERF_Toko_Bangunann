<?php
/** Tabel harga & stok per pilihan (warna/ukuran). Butuh $opsi_edit. */
?>
<div class="col-12">
    <div class="fw-semibold small mb-1">
        <i class="bi bi-tags"></i> Harga &amp; stok per pilihan
    </div>
    <div class="form-text mb-2">
        Biarkan kosong untuk memakai harga dan stok utama produk.
        Kosongkan juga kolom <em>Stok</em> bila stok tidak dibedakan per pilihan.
    </div>

    <?php if (!$opsi_edit['Warna'] && !$opsi_edit['Ukuran']): ?>
        <div class="alert alert-secondary py-2 small mb-0">
            Isi kolom <strong>Pilihan Warna</strong> atau <strong>Pilihan Ukuran</strong> di atas,
            lalu tekan Simpan. Setelah tersimpan, harga per pilihan bisa diisi di sini.
        </div>
    <?php endif; ?>

    <?php foreach (['Warna', 'Ukuran'] as $kel): ?>
        <?php if (empty($opsi_edit[$kel])) {
            continue;
        } ?>
        <div class="mb-2">
            <div class="small text-muted mb-1"><?= e($kel) ?></div>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0" style="border:1px solid #e5eaf1">
                    <thead class="table-light">
                        <tr>
                            <th>Pilihan</th>
                            <th style="width:170px">Harga</th>
                            <th style="width:140px">Stok</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($opsi_edit[$kel] as $o):
                            $kunci = $o['kelompok'] . '|' . $o['nilai']; ?>
                            <tr>
                                <td class="small fw-semibold"><?= e($o['nilai']) ?></td>
                                <td>
                                    <input type="text" inputmode="numeric"
                                           name="opsi_harga[<?= e($kunci) ?>]"
                                           class="form-control form-control-sm"
                                           placeholder="Rp 0"
                                           value="<?= $o['harga_opsi'] !== null ? (int)$o['harga_opsi'] : '' ?>">
                                </td>
                                <td>
                                    <input type="number" min="0"
                                           name="opsi_stok[<?= e($kunci) ?>]"
                                           class="form-control form-control-sm"
                                           placeholder="(sama dgn produk)"
                                           value="<?= $o['stok_opsi'] !== null ? (int)$o['stok_opsi'] : '' ?>">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endforeach; ?>
</div>
