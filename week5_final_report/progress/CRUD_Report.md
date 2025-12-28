# Laporan Analisis CRUD pada Form Login dan Register

## Pendahuluan
Laporan ini menganalisis operasi CRUD (Create, Read, Update, Delete) dasar dalam konteks form login dan register pada aplikasi web PHP ini. Analisis dilakukan berdasarkan kode yang ada di file `src/Main_menu/Login_Page.php`, `src/Main_menu/Register_Page.php`, dan `src/Main_menu/Mapping.php`. Selain itu, laporan ini menggunakan SQL Injection (SQLi) sebagai basis untuk mengevaluasi keamanan kode, dengan fokus pada bagaimana operasi CRUD dapat rentan terhadap serangan SQLi jika tidak diimplementasikan dengan benar.

## Operasi CRUD dalam Form Login dan Register

### 1. Create (Registrasi)
- **Deskripsi**: Operasi Create terjadi saat pengguna mendaftar melalui form register. Data pengguna (nama, email, password, status) dimasukkan ke dalam tabel `User` di database.
- **Implementasi**:
  - Form register di `Register_Page.php` mengirim data ke `Mapping.php` via POST.
  - Di `Mapping.php`, method `register()` dalam class `Authentication` menangani proses:
    - Validasi input dasar.
    - Pengecekan duplikasi email dengan query SELECT.
    - Hashing password menggunakan `password_hash()`.
    - Insert data ke tabel `User` menggunakan prepared statement.
    - Jika status adalah organizer, data tambahan dimasukkan ke tabel `Organizer`.
- **Risiko SQLi**: Jika tidak menggunakan prepared statement, input pengguna bisa langsung disisipkan ke query SQL, memungkinkan serangan seperti `' OR '1'='1` untuk bypass validasi atau insert data berbahaya.

### 2. Read (Login)
- **Deskripsi**: Operasi Read terjadi saat login, di mana data pengguna dibaca dari tabel `User` untuk verifikasi kredensial.
- **Implementasi**:
  - Form login di `Login_Page.php` mengirim email dan password ke `Mapping.php`.
  - Method `login()` di class `Authentication`:
    - Query SELECT untuk mengambil data pengguna berdasarkan email.
    - Verifikasi password dengan `password_verify()`.
    - Jika berhasil, set session dan redirect berdasarkan status pengguna.
- **Risiko SQLi**: Tanpa prepared statement, email input bisa digunakan untuk inject SQL, misalnya `' UNION SELECT * FROM User --` untuk membaca data tambahan atau `' OR 1=1 --` untuk login tanpa password.

### 3. Update
- **Deskripsi**: Operasi Update tidak langsung terjadi di form login/register, tetapi ada dalam proses registrasi organizer di mana data sementara di session diupdate ke database.
- **Implementasi**: Di `addOrganizerDetails()`, data dari session dimasukkan ke tabel `Organizer` (sebenarnya lebih ke Create, tapi melibatkan update status registrasi).
- **Risiko SQLi**: Jika ada operasi update eksplisit (misalnya update password), input harus disanitasi untuk mencegah SQLi.

### 4. Delete
- **Deskripsi**: Tidak ada operasi Delete di form login/register. Delete mungkin terjadi di bagian lain aplikasi, seperti cancel submission.
- **Risiko SQLi**: Jika ada, query DELETE harus menggunakan prepared statement untuk mencegah penghapusan data tidak sah.

## Analisis Keamanan terhadap SQL Injection (SQLi)
SQL Injection adalah serangan di mana penyerang menyisipkan kode SQL berbahaya melalui input pengguna, memanfaatkan query yang tidak disanitasi. Dalam kode ini:

- **Keamanan Saat Ini**: Kode menggunakan prepared statements (`mysqli_prepare`, `bind_param`) untuk semua query database, yang secara efektif mencegah SQLi karena input di-treat sebagai data, bukan bagian query.
- **Contoh Risiko Jika Tidak Aman**:
  - Pada login: Jika query adalah `"SELECT * FROM User WHERE Email = '$email'"`, input `' OR '1'='1` akan membuat query selalu true.
  - Pada register: Input berbahaya bisa insert data palsu atau drop tabel.
- **Rekomendasi**: Selalu gunakan prepared statements. Hindari concatenating string ke query. Gunakan fungsi seperti `mysqli_real_escape_string` sebagai fallback, tapi prepared statements lebih aman.

## Kesimpulan
Operasi CRUD di form login dan register diimplementasikan dengan baik menggunakan prepared statements, sehingga aman dari SQLi. Operasi tambahan seperti Update dan Delete di User Dashboard juga menggunakan prepared statements untuk keamanan. Namun, untuk aplikasi yang lebih kompleks, pastikan semua interaksi database menggunakan praktik keamanan ini. Jika ada bagian kode lain yang tidak menggunakan prepared statements, segera diperbaiki untuk mencegah risiko keamanan.

## File yang Dianalisis
- `src/Main_menu/Login_Page.php`: Form login HTML.
- `src/Main_menu/Register_Page.php`: Form register HTML.
- `src/Main_menu/Mapping.php`: Logika autentikasi dengan class Authentication.
- `src/Configuration/config.php`: Konfigurasi koneksi database.
- `src/Dashboard/User/CancelSubmission.php`: Operasi Delete untuk pembatalan registrasi event.
- `src/Dashboard/User/UpdateSubmission.php`: Form untuk update submission.
- `src/Dashboard/User/UpdateSubmit.php`: Logika update submission.
- `src/Dashboard/User/Submit.php`: Operasi Create untuk submit registrasi event.
- `src/Dashboard/User/RecordSubmissions.php`: Tampilan daftar submission dengan opsi update dan cancel.
