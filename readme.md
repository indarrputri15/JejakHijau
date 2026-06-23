# JejakHijau

## Deskripsi
JejakHijau merupakan sebuah platform campaign lingkungan dan donasi penghijauan berbasis web yang bertujuan untuk membantu masyarakat berkontribusi dalam menjaga lingkungan melalui aksi nyata. Platform ini memungkinkan pengguna untuk melakukan donasi terhadap campaign penanaman pohon yang sedang berlangsung maupun membuat campaign sendiri.

## Alamat
Localhost

## Team Members and Responsibilities
| No | Nama Anggota | Role | Tanggung Jawab |
|---|---|---|---|
| 1 | Khofipah Indar Putri | Frontend / UI-UX | Membuat tampilan website, landing page, responsive design, navigation bar, hamburger menu, content slider, tampilan campaign, halaman donasi, halaman profile, serta mengatur keseluruhan desain antarmuka website menggunakan HTML, CSS, dan JavaScript. |
| 2 | Erlita Jannatul Aulia | Backend / Database | Membuat sistem login dan register, struktur database, sistem campaign, sistem donasi, session login, upload file, validasi form, serta integrasi backend menggunakan PHP dan MySQL. |
| 3 | Yunda Hayatus Soleha | Content / Dokumentasi | Menyusun konsep website, membuat isi konten campaign dan awareness lingkungan, melakukan testing website, membuat sitemap, dokumentasi project, laporan, serta menyiapkan bahan presentasi project. |

## NIM Anggota Kelompok
- Khofipah Indar Putri: F1D02410062
- Erlita Jannatul Aulia: F1D02410006
- Yunda Hayatus Soleha: F1D02410029

### Menu Utama

##  Aktor User
User merupakan pengguna website yang dapat melakukan donasi campaign lingkungan maupun membuat campaign penghijauan baru.

| No | Menu | Deskripsi |
|----|------|-----------|
| 1 | Register | Membuat akun baru |
| 2 | Login | Masuk ke akun yang sudah terdaftar |
| 3 | Beranda | Halaman utama berisi informasi platform |
| 4 | Campaign | Melihat daftar campaign lingkungan yang tersedia |
| 5 | Detail Campaign | Melihat informasi lengkap dan donatur campaign |
| 6 | Donasi | Mengisi form donasi dan upload bukti transfer |
| 7 | Buat Campaign | Membuat campaign penghijauan baru |
| 8 | Profil | Melihat data diri dan riwayat kampanye & donasi |
| 9 | Edit Profil | Mengubah data diri dan foto profil |
| 10 | Logout | Keluar dari akun |

##  Aktor Admin
Admin bertugas mengelola dan memantau seluruh aktivitas yang berjalan di platform.

| No | Menu | Deskripsi |
|----|------|-----------|
| 1 | Login Admin | Masuk ke dashboard khusus admin |
| 2 | Dashboard | Memantau statistik user, campaign, dan donasi |
| 3 | Kelola User | Melihat daftar dan detail data seluruh user |
| 4 | Kelola Campaign | Menyetujui atau menghapus campaign |
| 5 | Kelola Donasi | Memantau dan verifikasi donasi masuk |
| 6 | Logout | Keluar dari dashboard admin |

### SiteMap

JejakHijau/
├── index.php                  # Landing page
├── login.php                  # Login user
├── signup.php                 # Registrasi user
├── logout.php                 # Logout session
├── campaigns.php              # Daftar campaign
├── campaign-detail.php        # Detail campaign
├── donation.php               # Form & proses donasi
├── create-campaign.php        # Buat campaign baru
├── profile.php                # Profil user
├── edit-profile.php           # Edit profil user
├── admin-login.php            # Login admin
├── admin-dashboard.php        # Dashboard admin
├── users-detail.php           # Detail user (admin)
├── config.php                 # Koneksi DB & konfigurasi
├── session-check.php          # Helper session
├── validation.js              # Validasi form (JS)
├── main.js                    # Script utama (JS)
├── style.css                  # Stylesheet utama
├── database_jejakhijau.sql    # Struktur & data database
├── assets/                    # Gambar & media
└── uploads/                   # File upload user
    ├── campaigns/             # Gambar campaign
    └── donations/             # Bukti transfer


## Teknologi
- Frontend : HTML, JavaScript, CSS
- Backend : PHP
- Database : MySQL
- Local Server : XAMPP
- Desaign Support : Figma
- Version Control : Git/Github

## Requirement

Untuk menggunakan JejakHijau ini, anda harus menginstall dan konfigurasi berikut:
- XAMPP
- PHP 8+
- Browser
