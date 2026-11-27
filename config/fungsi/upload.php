<?php
/**
 * Upload & hapus file gambar di folder /uploads.
 */

function upload_gambar(?string $fileField, ?string $lama = null): ?string
{
    if (!isset($_FILES[$fileField]) || $_FILES[$fileField]['error'] === UPLOAD_ERR_NO_FILE) {
        return $lama;
    }
    if ($_FILES[$fileField]['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Gagal mengunggah gambar.');
    }

    $maxBytes = 2 * 1024 * 1024;
    if ($_FILES[$fileField]['size'] > $maxBytes) {
        throw new RuntimeException('Ukuran gambar maksimal 2 MB.');
    }

    $ext = strtolower(pathinfo($_FILES[$fileField]['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
        throw new RuntimeException('Format gambar harus JPG, PNG, atau WEBP.');
    }

    $folder = __DIR__ . '/../../uploads';
    if (!is_dir($folder) && !mkdir($folder, 0755, true)) {
        throw new RuntimeException('Folder uploads tidak dapat dibuat.');
    }

    $nama = uniqid('img_', false) . '.' . $ext;
    if (!move_uploaded_file($_FILES[$fileField]['tmp_name'], $folder . '/' . $nama)) {
        throw new RuntimeException('Gagal menyimpan gambar ke server.');
    }
    return $nama;
}

function hapus_gambar(?string $nama): void
{
    if ($nama) {
        @unlink(__DIR__ . '/../../uploads/' . basename($nama));
    }
}
