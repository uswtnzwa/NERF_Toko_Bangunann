<?php
/**
 * Password: hash bcrypt + kompatibilitas dengan hash MD5 lama.
 *
 * Password lama di dump SQL masih MD5 (hash dari "admin").
 * Password baru disimpan dengan password_hash() (bcrypt).
 */

function hash_password(string $plain): string
{
    return password_hash($plain, PASSWORD_DEFAULT);
}

function cek_password(string $plain, string $tersimpan): bool
{
    if (password_verify($plain, $tersimpan)) {
        return true;
    }
    // Kompatibilitas dengan hash MD5 lama
    if (strlen($tersimpan) === 32 && hash_equals($tersimpan, md5($plain))) {
        return true;
    }
    return false;
}
