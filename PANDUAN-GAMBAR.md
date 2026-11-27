# Panduan Mengganti Logo & Foto Produk

Semua gambar saat ini masih **placeholder** (abu-abu, ada tulisan "Ganti file ini...").
Anda cukup **timpa file-nya** dengan foto asli — tidak perlu edit kode, tidak perlu ubah database.

---

## 1. Logo Toko

| | |
|---|---|
| **Lokasi file** | `ERP_Kel7/TokoLogo.png` |
| **UkuranPlaceholder** | 512 x 512 px |
| **Ukuran yang disarankan** | 400 x 400 px, rasio 1:1 (persegi) |
| **Format** | PNG (transparan lebih bagus) atau JPG |

Dipakai di: navbar, hero, dan footer `index.php`.

> Kalau file ini dihapus, halaman **tidak error** — otomatis muncul teks "NERF".

---

## 2. Banner Toko (opsional)

| | |
|---|---|
| **Lokasi file** | `ERP_Kel7/Banner-Toko.png` |
| **Ukuran yang disarankan** | 1200 x 500 px, rasio 12:5 |
| **Format** | PNG atau JPG |

Belum dipakai di kode. Nanti tinggal dipakai untuk carousel promo di beranda.

---

## 3. Foto Produk

Semua foto produk ada di **satu folder**:

```
ERP_Kel7/uploads/
```

| File placeholder | Produk |
|---|---|
| `smn001.png` | Semen Tiga Roda 50 Kg |
| `cat001.png` | Cat Avian 5 Kg |
| `pip001.png` | Pipa PVC 1/2 Inch |
| `bsi001.png` | Besi Beton 10mm |
| `krm001.png` | Keramik Roman 60x60 |
| `kay001.png` | Kayu Balok 4x6 |

**Cara ganti:** timpa file dengan nama yang **sama persis**.

Contoh:
```
uploads/smn001.png   ->  ganti dengan  uploads/semen_tiga_roda_anda.png
                        (nama file BARU, hapus yang lama)
```

Atau kalau mau pakai nama lain, update kolom `gambar` di database:

```sql
USE NERF_TokoBangunan;
UPDATE produk SET gambar = 'semen_tiga_roda_anda.png' WHERE kode_produk = 'SMN001';
```

**Saran ukuran foto produk:**

| Bagian | Ukuran |
|---|---|
| Resolusi | 600 x 600 px (rasio 1:1) |
| Format | PNG atau JPG |
| Ukuran file | di bawah 2 MB |
| Latar | putih polos agar produk terlihat jelas |

Foto otomatis dikecilkan dengan `object-fit: contain`, jadi foto persegi paling rapi.

---

## 4. Menambah Produk Baru

Nanti setelah panel admin (`admin/produk.php`) siap, Anda bisa mengunggah foto
langsung lewat form **Tambah Produk** — tidak perlu file manual seperti di atas.
Batas ukuran upload: **2 MB**, format: JPG, PNG, WEBP.

---

## Ringkasan cepat

```
Ganti logo       -> timpa  TokoLogo.png
Ganti banner     -> timpa  Banner-Toko.png
Ganti foto item  -> timpa  file di dalam folder uploads/
```

Nama file **harus sama persis**, kalau tidak muncul beresi cari nama barunya di database.
