## Login Form reCAPTCHA (PHP + MySQL) 🇮🇩

> Form login sederhana namun lengkap: dilindungi Google reCAPTCHA v2, terhubung ke database MySQL, mendukung migrasi dari password plaintext ke bcrypt, dan mudah di‑deploy di XAMPP / hosting shared.

### ✨ Fitur Utama
- Proteksi bot dengan Google reCAPTCHA v2 ("I'm not a robot")
- Autentikasi user melalui tabel MySQL (PDO, aman & prepared statement)
- Script migrasi: otomatis membuat kolom `password_hash` dan meng‑hash password lama
- Mode transisi: sementara masih bisa pakai password plaintext sampai migrasi selesai
- Dump SQL siap impor: `database_import.sql` (10 akun demo dengan berbagai peran)
- Struktur kode bersih & mudah dikembangkan

### 🗂 Struktur Berkas Penting
| File | Fungsi |
|------|--------|
| `index.php` | Logika utama: proses form, verifikasi reCAPTCHA, cek kredensial |
| `form.php` | Tampilan form login |
| `success.php` | Halaman setelah login sukses (placeholder area terproteksi) |
| `config.php` | Konfigurasi koneksi database (PDO) |
| `migrate_app_users.php` | Jalankan sekali untuk meng‑hash password lama |
| `database_import.sql` | Script pembuatan + pengisian awal database |

### 💻 Prasyarat
- PHP 7.4+ (disarankan 8.x) dengan ekstensi PDO MySQL
- MySQL / MariaDB
- Key Google reCAPTCHA v2 (Checkbox)

### 🚀 Cara Cepat (XAMPP Local)
1. Clone / salin proyek ke folder `htdocs`.
2. Impor database:
	```powershell
	"C:\xampp\mysql\bin\mysql.exe" -u root < database_import.sql
	```
3. Sesuaikan `config.php` bila perlu (DB default: `ssdlc`).
4. Buat site + secret key di: https://www.google.com/recaptcha/admin dan taruh ke variabel `$publicKey` & `$secretKey` di `index.php`.
5. (Opsional tapi disarankan) Buka `migrate_app_users.php` sekali untuk membuat bcrypt hash.
6. Akses di browser: `http://localhost/simple-recaptcha-login/`.
7. Login pakai salah satu akun demo (lihat tabel di bawah).

### 👥 Akun Demo (Sebelum Migrasi Hash)
| Username        | Password        | Peran (Ringkas)                            |
|-----------------|-----------------|--------------------------------------------|
| admin_ssdlc     | A5j!K9@Lp$3q    | Admin penuh akses                          |
| dev_lead        | s#D8fG!zX2vP    | Pimpinan pengembang                        |
| data_analyst    | 7*cVbN@mK4hJ    | Analis data (baca/tulis)                   |
| report_user     | R#9pT!qW3eY&    | Pengguna laporan (read only)               |
| backup_op       | Z@x1C2v$bN*m    | Operasi backup (read + lock)               |
| guest_access    | password123     | Tamu (read)                                |
| test_user1      | ssdlc_pass      | Testing (CRUD)                             |
| viewer_01       | lihatsaja       | Viewer (read)                              |
| dev_junior      | dev12345        | Developer junior (CRUD)                    |
| marketing_user  | marketing       | Marketing (read)                           |

Setelah menjalankan `migrate_app_users.php`, kolom `password_hash` terisi dan Anda bisa aman menghapus kolom plaintext:
```sql
ALTER TABLE app_users DROP COLUMN password;
```

### 🔐 Catatan Keamanan Penting
- Jangan biarkan kolom plaintext `password` di produksi – hapus setelah migrasi.
- Pindahkan kredensial & key reCAPTCHA ke environment variable (.env) atau server config.
- Aktifkan HTTPS (Let’s Encrypt / reverse proxy) di server publik.
- Tambahkan rate limiting / delay pada percobaan login berulang.
- Regenerasi session ID setelah login sukses (belum diterapkan di contoh ini).
- Audit & logging: simpan jejak login gagal (tanpa password) untuk analitik keamanan.

### 📦 Impor / Reset Ulang
Bangun ulang database dari nol:
```powershell
"C:\xampp\mysql\bin\mysql.exe" -u root < database_import.sql
```
Migrasi / buat hash ulang bila perlu:
```powershell
php migrate_app_users.php
```

### 🔄 Alur Kerja Sederhana
1. User kirim form (username, password, token reCAPTCHA)
2. Server verifikasi token ke Google API
3. Ambil baris user → cek `password_hash` (fallback plaintext hanya sementara)
4. Jika valid → tampilkan `success.php`

### 🛠 Ide Pengembangan Lanjut
- Session & logout + halaman terproteksi nyata
- CSRF token tambahan (selain reCAPTCHA)
- Fitur ubah / reset password (email / token satu kali pakai)
- Kebijakan password kuat + expiry
- Lockout / captcha tambahan setelah X kegagalan
- Logging terstruktur (JSON) + monitoring (ELK / Loki)

### 📄 Lisensi
Lihat berkas `LICENSE` (atau tambahkan lisensi pilihan Anda, misalnya MIT).

### ⚠️ Disclaimer
Proyek ini untuk pembelajaran/demonstrasi. Sebelum produksi: hapus akun demo, gunakan password kuat, harden konfigurasi server, dan lakukan audit keamanan.

Selamat bereksperimen & semoga membantu! 🚀
