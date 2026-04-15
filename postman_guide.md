# Panduan Melakukan Dokumentasi API Menggunakan Postman

Panduan ini berisi langkah-langkah untuk mendokumentasikan API Portal Inovasi Daerah menggunakan Postman.

## 1. Persiapan Awal
1. Download dan install aplikasi **Postman** (jika belum memilikinya), lalu buat akun dan login.
2. Pastikan server backend Laravel berjalan di lokal dengan menjalankan `php artisan serve` pada folder `backend/`. URL defaultnya adalah `http://127.0.0.1:8000`.

## 2. Membuat Workspace dan Collection
1. **Buat Workspace Baru:** Pada menu sebelah kiri Postman, klik `Workspaces` > `Create Workspace`. Beri nama misalnya **"Portal Inovasi Daerah"**.
2. **Buat Collection:** Di dalam workspace, klik tombol `+` (Create new Collection) di panel kiri. Beri nama **"API Inovasi Daerah"**.
3. *Opsional tetapi Disarankan:* Tambahkan deskripsi tentang Collection ini dengan mengklik teks *Add a description* di halaman Overview Collection.

## 3. Menyiapkan Environment Variables
Agar URL base dari API bisa diubah dengan mudah saat sudah tahap produksi (production), setup environment variables:
1. Klik menu **Environments** di sidebar kiri.
2. Buat Environment baru, beri nama **"Local Dev"**.
3. Tambahkan variable:
   - Variable: `base_url`
   - Initial & Current value: `http://127.0.0.1:8000/api`
   - Variable: `auth_token` (biarkan kosong dulu untuk menyimpan Bearer token nanti).
4. Simpan, pastikan switch Environment dropdown di pojok kanan atas ke **"Local Dev"**.

## 4. Daftar API Endpoint yang Perlu Dibuat di Postman
Untuk mencakup seluruh alur sistem, buatlah *folder* di dalam Collection Anda (klik `...` > `Add Folder`) untuk masing-masing kategori di bawah ini, lalu tambahkan *Request* (`Add Request`) ke dalamnya:

### A. Folder: Auth (Autentikasi)
1. **Login (`POST {{base_url}}/login`)**
   - **Body (raw JSON):** 
     ```json
     {
         "email": "admin@example.com",
         "password": "password123"
     }
     ```
   - *Catatan:* Copy `token` dari response API dan masukkan sebagai current value dari variabel `{{auth_token}}`.
2. **Register (`POST {{base_url}}/register`)**
   - **Body (raw JSON):** `{"name": "Budi", "email": "budi@example.com", "password": "password123", "role": "inisiator"}`
3. **Logout (`POST {{base_url}}/logout`)**
   - **Auth:** Bearer Token `{{auth_token}}`

### B. Folder: Super Admin
1. **Get All Admins (`GET {{base_url}}/superadmin/admins`)**
   - **Auth:** Bearer Token `{{auth_token}}` (hanya Super Admin)
2. **Create Admin (`POST {{base_url}}/superadmin/admins`)**
   - **Auth:** Bearer Token `{{auth_token}}`
   - **Body (raw JSON):** `{"name": "Admin Daerah", "email": "admin2@example.com", "password": "password", "level": "admin"}`

### C. Folder: Admin
1. **Get Submissions for Verification (`GET {{base_url}}/admin/products`)**
   - **Auth:** Bearer Token `{{auth_token}}`
2. **Verify / Update Status Produk (`PUT {{base_url}}/admin/products/{id_produk}/verify`)**
   - **Auth:** Bearer Token `{{auth_token}}`
   - **Body (raw JSON):** `{"status_kurasi": "approved"}` (Bisa *approved* atau *rejected*)

### D. Folder: Inisiator
1. **Submit Produk Inovasi (`POST {{base_url}}/inisiator/products`)**
   - **Auth:** Bearer Token `{{auth_token}}`
   - **Body (raw JSON):** 
     ```json
     {
         "nama_inovasi": "Aplikasi PBB Pintar", 
         "deskripsi": "Aplikasi cek pajak bumi dan bangunan", 
         "tahun_inovasi": "2024", 
         "id_opd": 1, 
         "id_bentuk": 2, 
         "id_tahapan": 1,
         "is_digital": true
     }
     ```
2. **Get My Products (`GET {{base_url}}/inisiator/products`)**
   - **Auth:** Bearer Token `{{auth_token}}`

### E. Folder: Public Portal
1. **Get Curated Products (`GET {{base_url}}/public/products`)**
   - **Auth:** Tidak perlu (No Auth).
   - *Catatan:* Menampilkan produk yang sudah di-verify.
2. **Get Product Detail (`GET {{base_url}}/public/products/{id_produk}`)**
   - **Auth:** Tidak perlu (No Auth).

## 5. Menambahkan Penjelasan dan Response Examples (Sangat Penting)
Untuk menjadikannya dokumentasi yang baik, Anda harus menyediakan contoh *Response* dan *Deskripsi* endpoint:
1. Buka request yang ada. Di panel kanannya ada tab **Description**. Tuliskan penjelasan API parameter disitu.
2. **Menyimpan Response:** Klik tombol **Send** untuk menjalankan endpoint (pastikan backend aktif). Saat response sukses diterima di bawah layar, klik tombol **Save Response** > **Save as example**.
3. Beri nama respon tersebut, misalnya **"Success Login Response"**. Dengan fitur example ini, pihak Frontend bisa melihat contoh JSON output tanpa harus menjalankan server backend-nya.

## 6. Publikasi Dokumentasi (Publishing)
Postman dapat meng-generate public web page berisi seluruh dokumentasi ini yang mudah dibaca:
1. Klik Collection Name **"API Inovasi Daerah"**.
2. Temukan dan klik tanda titik tiga `...` di samping tab Collection.
3. Pilih menu **View Documentation**. Halaman web internal akan terbuka.
4. Klik tombol **Publish** di pojok kanan atas.
5. Anda akan diarahkan ke browser, ikuti tahapan pengaturan warnanya bila ada, lalu klik **Publish Collection**.
6. Postman akan memunculkan sebuah **URL Public**. Bagikan URL tersebut ke tim Frontend sebagai referensi API resmi untuk proyek ini!
