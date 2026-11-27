<?php
/**
 * Konfigurasi Customer Service (WhatsApp) - NERF Toko Bangunan.
 *
 * =====================================================================
 *  TARUH LINK WHATSAPP ANDA DI BAWAH INI (baris $cs_wa_link)
 * =====================================================================
 * Contoh isi yang diperbolehkan:
 *
 *   $cs_wa_link = 'https://wa.me/6281234567890';
 *   $cs_wa_link = 'https://wa.me/6281234567890?text=Halo%20admin';
 *   $cs_wa_link = 'https://chat.whatsapp.com/XXXXXXXXXXXX'; // link grup
 *
 * Catatan: pesan otomatis ($cs_wa_pesan / ?pesan=) hanya ditambahkan ke
 * link chat perorangan (wa.me / api.whatsapp.com). Link undangan grup
 * dipakai apa adanya supaya parameter aslinya tidak berubah.
 *
 * Selama masih kosong (''), halaman customer-service.php tetap tampil,
 * tombol WA dinonaktifkan, dan ada pesan pengingat di halaman tersebut.
 */
$cs_wa_link = 'https://chat.whatsapp.com/GqT81GcbH1eBzsdhKZTv9i?s=cl&p=a&iam=2';

/**
 * Pesan yang otomatis terisi di kolom chat WhatsApp.
 * Bisa ditimpa lewat URL, misalnya: customer-service.php?pesan=Halo%20admin
 */
$cs_wa_pesan = 'Halo NERF Bangun, saya butuh bantuan. Boleh dibantu?';

/**
 * Alihkan pengunjung otomatis ke WhatsApp saat halaman CS dibuka.
 * Set false jika ingin pengunjung menekan tombol WA secara manual.
 *
 * Tambahkan ?preview=1 pada URL untuk melihat halaman tanpa alihkan otomatis,
 * misalnya: http://ERP_Kel7.test/customer-service.php?preview=1
 */
$cs_auto_alihkan = true;

/** Jeda sebelum alihkan otomatis (detik). */
$cs_jeda_alihkan = 2;
