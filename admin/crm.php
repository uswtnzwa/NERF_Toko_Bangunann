<?php
/**
 * CRM (CUSTOMER RELATIONSHIP MANAGEMENT)
 *
 * Tiga bagian:
 *   1. Pengaturan leads/pelanggan -> kelola calon pelanggan (CRUD leads)
 *      + daftar pelanggan terdaftar dari tabel pelanggan
 *   2. Riwayat interaksi          -> catatan komunikasi dengan lead/pelanggan
 *   3. Analisis pelanggan         -> segmen & statistik pelanggan
 */
session_start();
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../config/fungsi.php';
butuh_role(['Super Admin', 'Admin', 'Customer Service']);

$judul = 'CRM';

/* ================== AKSI LEADS ================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = $_POST['aksi'] ?? '';
    $anchor = 'leads';

    if ($aksi === 'lead_simpan') {
        $id     = (int)($_POST['id_lead'] ?? 0);
        $nama   = trim($_POST['nama'] ?? '');
        $email  = trim($_POST['email'] ?? '');
        $hp     = trim($_POST['nomor_hp'] ?? '');
        $sumber = trim($_POST['sumber'] ?? 'Website');
        $status = $_POST['status'] ?? 'Baru';
        $ket    = trim($_POST['keterangan'] ?? '');

        $status_valid = ['Baru', 'Dihubungi', 'Tertarik', 'Pelanggan', 'Ditolak'];
        if ($nama === '') {
            [$tipe, $pesan] = ['danger', 'Nama lead wajib diisi.'];
        } elseif ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            [$tipe, $pesan] = ['danger', 'Format email tidak valid.'];
        } elseif (!in_array($status, $status_valid, true)) {
            [$tipe, $pesan] = ['danger', 'Status lead tidak valid.'];
        } elseif ($id > 0) {
            $s = $koneksi->prepare(
                'UPDATE leads SET nama=?, email=?, nomor_hp=?, sumber=?, status=?, keterangan=?, diubah_pada=NOW()
                 WHERE id_lead=?'
            );
            $s->bind_param('ssssssi', $nama, $email, $hp, $sumber, $status, $ket, $id);
            $s->execute();
            [$tipe, $pesan] = ['success', 'Lead berhasil diperbarui.'];
        } else {
            $s = $koneksi->prepare(
                'INSERT INTO leads (nama, email, nomor_hp, sumber, status, keterangan)
                 VALUES (?,?,?,?,?,?)'
            );
            $s->bind_param('ssssss', $nama, $email, $hp, $sumber, $status, $ket);
            $s->execute();
            [$tipe, $pesan] = ['success', 'Lead baru berhasil ditambahkan.'];
        }
    } elseif ($aksi === 'lead_hapus') {
        $id = (int)($_POST['id_lead'] ?? 0);
        $h = $koneksi->prepare('DELETE FROM interaksi WHERE subjek = ? AND id_subjek = ?');
        $tipe = 'Lead';
        $h->bind_param('si', $tipe, $id);
        $h->execute();
        $s = $koneksi->prepare('DELETE FROM leads WHERE id_lead = ?');
        $s->bind_param('i', $id);
        $s->execute();
        [$tipe, $pesan] = ['success', 'Lead beserta riwayat interaksinya dihapus.'];
    } elseif ($aksi === 'interaksi_simpan') {
        $subjek    = $_POST['subjek'] ?? 'Lead';
        $id_subjek = (int)($_POST['id_subjek'] ?? 0);
        $jenis     = $_POST['jenis'] ?? 'Telepon';
        $catatan   = trim($_POST['catatan'] ?? '');
        $petugas   = trim($_POST['petugas'] ?? 'Admin');

        $jenis_valid = ['Telepon', 'WhatsApp', 'Email', 'Chat', 'Kunjungan'];
        if (!in_array($subjek, ['Lead', 'Pelanggan'], true) || $id_subjek <= 0) {
            [$tipe, $pesan] = ['danger', 'Subjek interaksi tidak valid.'];
        } elseif (!in_array($jenis, $jenis_valid, true)) {
            [$tipe, $pesan] = ['danger', 'Jenis interaksi tidak valid.'];
        } elseif ($catatan === '') {
            [$tipe, $pesan] = ['danger', 'Catatan interaksi wajib diisi.'];
        } else {
            $s = $koneksi->prepare(
                'INSERT INTO interaksi (subjek, id_subjek, jenis, catatan, petugas)
                 VALUES (?,?,?,?,?)'
            );
            $s->bind_param('sisss', $subjek, $id_subjek, $jenis, $catatan, $petugas);
            $s->execute();
            [$tipe, $pesan] = ['success', 'Interaksi berhasil dicatat.'];
        }
        $anchor = 'interaksi';
    } elseif ($aksi === 'interaksi_hapus') {
        $id = (int)($_POST['id_interaksi'] ?? 0);
        $s = $koneksi->prepare('DELETE FROM interaksi WHERE id_interaksi = ?');
        $s->bind_param('i', $id);
        $s->execute();
        [$tipe, $pesan] = ['success', 'Catatan interaksi dihapus.'];
        $anchor = 'interaksi';
    } else {
        [$tipe, $pesan] = ['danger', 'Aksi tidak dikenal.'];
    }

    set_flash($tipe, $pesan);
    header('Location: crm.php#' . $anchor);
    exit;
}

/* ================== DATA LEADS ================== */
$leads = $koneksi->query('SELECT * FROM leads ORDER BY dibuat_pada DESC, id_lead DESC')
    ->fetch_all(MYSQLI_ASSOC);

$edit_lead = null;
if (isset($_GET['edit_lead'])) {
    $s = $koneksi->prepare('SELECT * FROM leads WHERE id_lead = ?');
    $id = (int)$_GET['edit_lead'];
    $s->bind_param('i', $id);
    $s->execute();
    $edit_lead = $s->get_result()->fetch_assoc() ?: null;
}

/* Ringkasan status lead. */
$status_lead = ['Baru', 'Dihubungi', 'Tertarik', 'Pelanggan', 'Ditolak'];
$lead_per_status = [];
foreach ($koneksi->query('SELECT status, COUNT(*) jml FROM leads GROUP BY status')->fetch_all(MYSQLI_ASSOC) as $r) {
    $lead_per_status[$r['status']] = (int)$r['jml'];
}

/* ================== DATA PELANGGAN ================== */
$pelanggan = $koneksi->query(
    "SELECT pl.id_pelanggan, pl.nama_lengkap, pl.email, pl.nomor_hp, pl.status, pl.dibuat_pada,
            COUNT(ps.id_pesanan) AS jml_pesanan, COALESCE(SUM(ps.total), 0) AS total_belanja
     FROM pelanggan pl
     LEFT JOIN pesanan ps ON ps.id_pelanggan = pl.id_pelanggan
        AND ps.status_pesanan NOT IN ('Menunggu Pembayaran','Dibatalkan','Retur')
     GROUP BY pl.id_pelanggan
     ORDER BY total_belanja DESC"
)->fetch_all(MYSQLI_ASSOC);

/* ================== RIWAYAT INTERAKSI ================== */
$filter_subjek = in_array($_GET['subjek'] ?? '', ['Lead', 'Pelanggan'], true) ? $_GET['subjek'] : '';
if ($filter_subjek !== '') {
    $s = $koneksi->prepare('SELECT * FROM interaksi WHERE subjek = ? ORDER BY tanggal DESC, id_interaksi DESC LIMIT 50');
    $s->bind_param('s', $filter_subjek);
    $s->execute();
    $interaksi = $s->get_result()->fetch_all(MYSQLI_ASSOC);
} else {
    $interaksi = $koneksi->query('SELECT * FROM interaksi ORDER BY tanggal DESC, id_interaksi DESC LIMIT 50')
        ->fetch_all(MYSQLI_ASSOC);
}

/* Daftar subjek untuk dropdown form interaksi. */
$opsi_subjek = [];
foreach ($leads as $l) {
    $opsi_subjek[] = ['tipe' => 'Lead', 'id' => (int)$l['id_lead'], 'label' => $l['nama'] . ' (Lead)'];
}
foreach ($pelanggan as $p) {
    $opsi_subjek[] = ['tipe' => 'Pelanggan', 'id' => (int)$p['id_pelanggan'], 'label' => $p['nama_lengkap'] . ' (Pelanggan)'];
}

/* ================== ANALISIS PELANGGAN ================== */
$jumlah_pelanggan = count($pelanggan);
$pelanggan_aktif = 0;
$total_belanja_semua = 0.0;
$segmen = ['VIP' => 0, 'Reguler' => 0, 'Baru' => 0];
foreach ($pelanggan as $p) {
    $belanja = (float)$p['total_belanja'];
    $pesan = (int)$p['jml_pesanan'];
    $total_belanja_semua += $belanja;
    if ($p['status'] === 'Aktif') {
        $pelanggan_aktif++;
    }
    if ($belanja >= 500000 || $pesan >= 3) {
        $segmen['VIP']++;
    } elseif ($pesan > 0) {
        $segmen['Reguler']++;
    } else {
        $segmen['Baru']++;
    }
}
$rata_belanja = $jumlah_pelanggan ? $total_belanja_semua / $jumlah_pelanggan : 0;
$nilai_lead = 0;
foreach ($lead_per_status as $status => $jml) {
    if ($status !== 'Ditolak') {
        $nilai_lead += $jml;
    }
}

include __DIR__ . '/layout/header.php';
?>

<!-- ===================== RINGKASAN ===================== -->
<div class="row g-3 mb-3">
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="ikon" style="background:#1473e6"><i class="bi bi-person-plus"></i></div>
            <div><div class="fs-4 fw-bold lh-1"><?= count($leads) ?></div><small class="text-muted">Total Leads</small></div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="ikon" style="background:#16a06a"><i class="bi bi-people"></i></div>
            <div><div class="fs-4 fw-bold lh-1"><?= $jumlah_pelanggan ?></div><small class="text-muted">Pelanggan</small></div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="ikon" style="background:#f59e0b"><i class="bi bi-chat-dots"></i></div>
            <div><div class="fs-4 fw-bold lh-1"><?= count($interaksi) ?></div><small class="text-muted">Interaksi Tercatat</small></div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="ikon" style="background:#8b5cf6"><i class="bi bi-cash-stack"></i></div>
            <div><div class="fs-4 fw-bold lh-1"><?= rupiah($total_belanja_semua) ?></div><small class="text-muted">Total Nilai Pelanggan</small></div>
        </div>
    </div>
</div>

<!-- ===================== 1. PENGATURAN LEADS / PELANGGAN ===================== -->
<div class="row g-3 mb-3">
    <!-- Form lead -->
    <div class="col-lg-4">
        <div class="card-panel p-3" id="leads">
            <h6 class="fw-bold mb-3">
                <i class="bi <?= $edit_lead ? 'bi-pencil-square' : 'bi-person-plus' ?> text-primary"></i>
                <?= $edit_lead ? 'Edit Lead' : 'Tambah Lead Baru' ?>
            </h6>
            <form method="post">
                <input type="hidden" name="aksi" value="lead_simpan">
                <input type="hidden" name="id_lead" value="<?= (int)($edit_lead['id_lead'] ?? 0) ?>">
                <div class="mb-2">
                    <label class="form-label small fw-semibold">Nama *</label>
                    <input type="text" name="nama" class="form-control form-control-sm"
                           value="<?= e($edit_lead['nama'] ?? '') ?>" required>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control form-control-sm"
                               value="<?= e($edit_lead['email'] ?? '') ?>">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">No. HP</label>
                        <input type="text" name="nomor_hp" class="form-control form-control-sm"
                               value="<?= e($edit_lead['nomor_hp'] ?? '') ?>">
                    </div>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Sumber</label>
                        <select name="sumber" class="form-select form-select-sm">
                            <?php foreach (['Website', 'Instagram', 'WhatsApp', 'Referral', 'Walk-in', 'Iklan'] as $s): ?>
                                <option value="<?= $s ?>" <?= ($edit_lead['sumber'] ?? '') === $s ? 'selected' : '' ?>><?= $s ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Status</label>
                        <select name="status" class="form-select form-select-sm">
                            <?php foreach ($status_lead as $s): ?>
                                <option value="<?= $s ?>" <?= ($edit_lead['status'] ?? '') === $s ? 'selected' : '' ?>><?= $s ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Keterangan</label>
                    <textarea name="keterangan" rows="2" class="form-control form-control-sm"><?= e($edit_lead['keterangan'] ?? '') ?></textarea>
                </div>
                <button class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-save"></i> <?= $edit_lead ? 'Simpan Perubahan' : 'Simpan Lead' ?>
                </button>
                <?php if ($edit_lead): ?>
                    <a href="crm.php#leads" class="btn btn-link btn-sm w-100 text-muted">Batal</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Daftar lead -->
    <div class="col-lg-8">
        <div class="card-panel p-3 h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="fw-bold mb-0">Daftar Leads / Calon Pelanggan</h6>
                <div class="d-flex gap-1 flex-wrap">
                    <?php
                    $warna_status = [
                        'Baru' => 'primary', 'Dihubungi' => 'info', 'Tertarik' => 'warning',
                        'Pelanggan' => 'success', 'Ditolak' => 'secondary',
                    ];
                    foreach ($status_lead as $s): ?>
                        <span class="badge bg-<?= $warna_status[$s] ?>">
                            <?= $s ?>: <?= $lead_per_status[$s] ?? 0 ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Nama</th><th>Kontak</th><th>Sumber</th>
                            <th>Status</th><th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!$leads): ?>
                            <tr><td colspan="5" class="text-center text-muted py-4">Belum ada leads.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($leads as $l): ?>
                            <tr>
                                <td>
                                    <div class="fw-semibold small"><?= e($l['nama']) ?></div>
                                    <small class="text-muted"><?= date('d/m/Y', strtotime($l['dibuat_pada'])) ?></small>
                                </td>
                                <td class="small text-muted">
                                    <?= e($l['email'] ?: '-') ?><br><?= e($l['nomor_hp'] ?: '') ?>
                                </td>
                                <td class="small"><?= e($l['sumber']) ?></td>
                                <td><span class="badge bg-<?= $warna_status[$l['status']] ?? 'secondary' ?>"><?= e($l['status']) ?></span></td>
                                <td class="text-center">
                                    <a href="crm.php?edit_lead=<?= (int)$l['id_lead'] ?>#leads"
                                       class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                                    <form method="post" class="d-inline"
                                          onsubmit="return confirm('Hapus lead <?= e($l['nama']) ?>?')">
                                        <input type="hidden" name="aksi" value="lead_hapus">
                                        <input type="hidden" name="id_lead" value="<?= (int)$l['id_lead'] ?>">
                                        <button class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Daftar pelanggan terdaftar -->
<div class="card-panel p-3 mb-3">
    <h6 class="fw-bold mb-2"><i class="bi bi-people text-success"></i> Pelanggan Terdaftar</h6>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Nama</th><th>Email</th><th>No. HP</th>
                    <th class="text-center">Pesanan</th><th class="text-end">Total Belanja</th>
                    <th>Segmen</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pelanggan as $p):
                    $belanja = (float)$p['total_belanja'];
                    $pesan = (int)$p['jml_pesanan'];
                    $seg = ($belanja >= 500000 || $pesan >= 3) ? ['VIP', 'warning']
                         : ($pesan > 0 ? ['Reguler', 'info'] : ['Baru', 'secondary']); ?>
                    <tr>
                        <td class="fw-semibold small"><?= e($p['nama_lengkap']) ?></td>
                        <td class="small text-muted"><?= e($p['email']) ?></td>
                        <td class="small text-muted"><?= e($p['nomor_hp'] ?: '-') ?></td>
                        <td class="text-center"><span class="badge bg-secondary"><?= $pesan ?></span></td>
                        <td class="text-end fw-semibold"><?= rupiah($belanja) ?></td>
                        <td><span class="badge bg-<?= $seg[1] ?>"><?= $seg[0] ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ===================== 2. RIWAYAT INTERAKSI ===================== -->
<div class="card-panel p-3 mb-3" id="interaksi">
    <div class="row g-3">
        <div class="col-lg-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-plus-circle text-primary"></i> Catat Interaksi Baru</h6>
            <form method="post">
                <input type="hidden" name="aksi" value="interaksi_simpan">
                <div class="mb-2">
                    <label class="form-label small fw-semibold">Subjek *</label>
                    <select name="subjek" id="subjekPilih" class="form-select form-select-sm" required>
                        <option value="Lead">Lead</option>
                        <option value="Pelanggan">Pelanggan</option>
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-semibold">Nama *</label>
                    <select name="id_subjek" id="subjekId" class="form-select form-select-sm" required>
                        <?php foreach ($opsi_subjek as $o): ?>
                            <option value="<?= $o['id'] ?>" data-tipe="<?= $o['tipe'] ?>"><?= e($o['label']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Jenis *</label>
                        <select name="jenis" class="form-select form-select-sm">
                            <?php foreach (['Telepon', 'WhatsApp', 'Email', 'Chat', 'Kunjungan'] as $j): ?>
                                <option value="<?= $j ?>"><?= $j ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Petugas</label>
                        <input type="text" name="petugas" class="form-control form-control-sm"
                               value="<?= e($admin['nama_lengkap'] ?? 'Admin') ?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Catatan *</label>
                    <textarea name="catatan" rows="3" class="form-control form-control-sm"
                              placeholder="Contoh: Menjelaskan harga dan mengirim penawaran" required></textarea>
                </div>
                <button class="btn btn-primary btn-sm w-100"><i class="bi bi-save"></i> Simpan Interaksi</button>
            </form>
        </div>

        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="fw-bold mb-0">Riwayat Interaksi Terbaru</h6>
                <form method="get" class="d-flex gap-1">
                    <select name="subjek" class="form-select form-select-sm" style="width:auto">
                        <option value="">Semua</option>
                        <?php foreach (['Lead', 'Pelanggan'] as $s): ?>
                            <option value="<?= $s ?>" <?= $filter_subjek === $s ? 'selected' : '' ?>><?= $s ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button class="btn btn-sm btn-outline-secondary">Filter</button>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Tanggal</th><th>Subjek</th><th>Jenis</th>
                            <th>Catatan</th><th>Petugas</th><th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!$interaksi): ?>
                            <tr><td colspan="6" class="text-center text-muted py-4">Belum ada interaksi tercatat.</td></tr>
                        <?php endif; ?>
                        <?php
                        $ikon_jenis = [
                            'Telepon' => 'bi-telephone', 'WhatsApp' => 'bi-whatsapp', 'Email' => 'bi-envelope',
                            'Chat' => 'bi-chat-dots', 'Kunjungan' => 'bi-shop',
                        ];
                        foreach ($interaksi as $i): ?>
                            <tr>
                                <td class="text-muted small"><?= date('d/m/Y H:i', strtotime($i['tanggal'])) ?></td>
                                <td class="small">
                                    <span class="badge bg-<?= $i['subjek'] === 'Lead' ? 'primary' : 'success' ?>"><?= e($i['subjek']) ?></span>
                                    #<?= (int)$i['id_subjek'] ?>
                                </td>
                                <td class="small">
                                    <i class="bi <?= $ikon_jenis[$i['jenis']] ?? 'bi-chat' ?>"></i>
                                    <?= e($i['jenis']) ?>
                                </td>
                                <td class="small"><?= e($i['catatan']) ?></td>
                                <td class="small text-muted"><?= e($i['petugas']) ?></td>
                                <td class="text-center">
                                    <form method="post" class="d-inline"
                                          onsubmit="return confirm('Hapus catatan interaksi ini?')">
                                        <input type="hidden" name="aksi" value="interaksi_hapus">
                                        <input type="hidden" name="id_interaksi" value="<?= (int)$i['id_interaksi'] ?>">
                                        <button class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ===================== 3. ANALISIS PELANGGAN ===================== -->
<div class="card-panel p-3" id="analisis">
    <h6 class="fw-bold mb-3"><i class="bi bi-bar-chart text-primary"></i> Analisis Pelanggan</h6>
    <div class="row g-3">
        <div class="col-md-4">
            <div class="border rounded p-3 h-100">
                <h6 class="small fw-bold text-muted text-uppercase">Segmen Pelanggan</h6>
                <?php
                $warna_segmen = ['VIP' => 'warning', 'Reguler' => 'info', 'Baru' => 'secondary'];
                foreach ($segmen as $nama => $jml): ?>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span><span class="badge bg-<?= $warna_segmen[$nama] ?>"><?= $nama ?></span></span>
                        <strong><?= $jml ?> pelanggan</strong>
                    </div>
                <?php endforeach; ?>
                <small class="text-muted d-block mt-2">
                    VIP: belanja &ge; Rp 500.000 atau &ge; 3 pesanan.
                </small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="border rounded p-3 h-100">
                <h6 class="small fw-bold text-muted text-uppercase">Ringkasan Nilai</h6>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="small text-muted">Total pelanggan</span><strong><?= $jumlah_pelanggan ?></strong>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="small text-muted">Pelanggan aktif</span><strong><?= $pelanggan_aktif ?></strong>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="small text-muted">Rata-rata belanja</span><strong><?= rupiah($rata_belanja) ?></strong>
                </div>
                <div class="d-flex justify-content-between py-2">
                    <span class="small text-muted">Total nilai pelanggan</span><strong class="text-success"><?= rupiah($total_belanja_semua) ?></strong>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="border rounded p-3 h-100">
                <h6 class="small fw-bold text-muted text-uppercase">Funnel Leads</h6>
                <?php foreach ($status_lead as $s): ?>
                    <div class="mb-2">
                        <div class="d-flex justify-content-between small">
                            <span><?= $s ?></span><strong><?= $lead_per_status[$s] ?? 0 ?></strong>
                        </div>
                        <div class="progress" style="height:6px">
                            <div class="progress-bar bg-<?= $warna_status[$s] ?>"
                                 style="width:<?= count($leads) ? round(($lead_per_status[$s] ?? 0) / count($leads) * 100) : 0 ?>%"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <small class="text-muted d-block mt-2">Lead potensial (tidak ditolak): <?= $nilai_lead ?>.</small>
            </div>
        </div>
    </div>
</div>

<script>
// Sesuaikan daftar nama dengan jenis subjek yang dipilih.
(function () {
    const pilih = document.getElementById('subjekPilih');
    const daftar = document.getElementById('subjekId');
    if (!pilih || !daftar) return;
    const opsi = Array.from(daftar.options);
    function sesuaikan() {
        opsi.forEach(function (o) { o.hidden = o.dataset.tipe !== pilih.value; });
        const terpilih = opsi.find(function (o) { return !o.hidden; });
        if (terpilih) daftar.value = terpilih.value;
    }
    pilih.addEventListener('change', sesuaikan);
    sesuaikan();
})();
</script>

<?php include __DIR__ . '/layout/footer.php'; ?>
