# Academic Repository System

## 1. Gambaran Umum

Academic Repository System adalah sistem manajemen berkas terpusat yang menggabungkan konsep **file manager (seperti Google Drive)** dengan konteks akademik **Mata Kuliah (Matkul)**.

Sistem ini memungkinkan:

* Penyimpanan file dan folder secara hierarkis
* Setiap Matkul memiliki **Folder Utama (Root Folder)** sendiri
* File dapat digunakan oleh **lebih dari satu Matkul** (shared resources)
* Pengelolaan **Module akademik** sebagai file khusus dengan metadata tambahan
* Kontrol akses file berdasarkan kebijakan Matkul

Repository bersifat **generic dan reusable**, sedangkan Matkul hanya bertindak sebagai **owner/context**, bukan struktur penyimpanan fisik.

---

## 2. Prinsip Desain Utama

1. **Single Source of Truth**

   * File hanya disimpan dan didefinisikan sekali di repository
   * Tidak ada duplikasi path atau metadata file di tabel lain

2. **Decoupling Repository dan Akademik**

   * Struktur folder & file tidak bergantung langsung pada Matkul
   * Matkul hanya mereferensikan folder root dan file yang digunakan

3. **Scalable & Future-Proof**

   * Mendukung shared file lintas Matkul
   * Siap untuk versioning, permission granular, dan audit log

---

## 3. Komponen Utama Sistem

### 3.1 Repository Layer

Lapisan ini menangani **struktur penyimpanan file & folder** secara umum.

#### Folder

* Mewakili direktori hierarkis (tree)
* Mendukung nested folder tanpa batas
* Dapat dimiliki oleh konteks tertentu (Matkul/User)

Atribut kunci:

* `parent_id` → struktur tree
* `full_path` → optimasi query & mapping storage
* `is_root` → penanda folder utama
* `creator_id` → user yang membuat folder

#### File

* Mewakili satu entitas file fisik di storage
* Disimpan menggunakan nama aman (`stored_name` / UUID)
* Tidak terikat ke satu Matkul saja

Atribut kunci:

* `original_name` vs `stored_name`
* `folder_id` → lokasi di repository
* `mime_type` & `size` → keamanan dan validasi
* `creator_id` → user yang mengupload file

---

### 3.2 Academic Layer

Lapisan ini menangani konteks akademik dan relasinya terhadap repository.

#### Matkul

Mata Kuliah sebagai **konteks logis**, bukan struktur fisik.

Fungsi utama:

* Memiliki satu **Root Folder** di repository
* Menentukan kebijakan akses file (`open_file_access`)
* Mengelompokkan file yang relevan melalui relasi M:M

Atribut penting:

* `kode`, `nama`, `sks` (enum: 2 atau 3)
* `root_folder_id`
* `open_file_access`

#### Matkul Files (Pivot)

* Relasi Many-to-Many antara Matkul dan File
* Memungkinkan satu file digunakan di banyak Matkul

Use case:

* Modul bersama (misal: algoritma dasar)
* File referensi lintas mata kuliah

---

### 3.3 Module

Module adalah **spesialisasi dari File**, bukan file terpisah.

Karakteristik:

* Selalu menunjuk ke satu File
* Memiliki metadata akademik tambahan
* File dapat di-override tanpa mengganti identitas Module

Metadata Module:

* `author_id`
* `workload_hours`
* `last_edited_at`
* `last_edited_by`
* `active`

> Catatan: Module **tidak menyimpan path file sendiri** untuk menghindari inkonsistensi data.

---

## 4. Relasi Antar Entitas (Konseptual)

* Folder

  * Recursive (parent–child)
  * One-to-Many dengan File
  * Belongs to User (creator)

* Matkul

  * One-to-One ke Folder (root)
  * Many-to-Many ke File

* File

  * Belongs to Folder
  * Belongs to User (creator)
  * Many-to-Many ke Matkul
  * One-to-One / One-to-Many ke Module (opsional, tergantung kebijakan)

---

## 5. Alur Kerja Utama Sistem

### 5.1 Pembuatan Matkul

1. Matkul dibuat
2. Sistem otomatis membuat Root Folder di repository
3. `root_folder_id` disimpan di Matkul

### 5.2 Manajemen Folder

* Folder dapat dibuat bebas di bawah Root Folder Matkul
* Struktur folder sepenuhnya fleksibel

### 5.3 Upload File

1. File diupload ke folder tertentu
2. Sistem menyimpan file fisik ke storage
3. Metadata disimpan di tabel `files`
4. File dapat di-attach ke satu atau lebih Matkul

### 5.4 Module Update / Override

1. File lama di-replace secara fisik
2. Record File tetap (ID tidak berubah)
3. Metadata Module diperbarui (`last_edited_at`, `last_edited_by`)

---

## 6. Kontrol Akses

* `open_file_access` berada di level Matkul
* Bersifat default policy, bukan atribut file

Aturan umum:

* Jika `open_file_access = true` → file Matkul dapat diakses publik
* Jika `false` → akses melalui permission user

Pendekatan ini menjaga konteks akademik tetap konsisten.

---

## 7. Struktur Storage Fisik (Contoh)

```
storage/app/public/repository/
└── matkul/
    └── DAA/
        ├── modules/
        │   └── a8f7d2e9.pdf
        └── references/
            └── b91c2x1.png
```

Mapping antara DB dan storage dilakukan melalui `full_path` dan `stored_name`.

---

## 8. Batasan & Keputusan Desain

* File tidak disalin untuk setiap Matkul
* Tidak ada path ganda di lebih dari satu tabel
* Folder root Matkul bersifat wajib (is_root=true)
* Repository memiliki 1 Root Folder global (parent_id=null, is_root=false)
* Setiap file HARUS berada di dalam folder (folder_id required)
* Struktur folder repository:
  ```
  Repository (root)
  ├── Documents
  ├── Images
  ├── Videos
  ├── Reports
  ├── Manuals
  └── Matkuls
      ├── {nama_matkul_1} (is_root=true)
      ├── {nama_matkul_2} (is_root=true)
      └── ...
  ```
* Repository dapat diperluas ke konteks lain (User, Project, dll)

---

## 9. Potensi Pengembangan Lanjutan

* File versioning & rollback
* Audit log aktivitas repository
* Permission berbasis role (Dosen / Asisten / Mahasiswa)
* Quota storage per Matkul
* File preview & indexing

---

## 10. Implementasi File Manager UI

### 10.1 Fitur UI

* **Drag & Drop Upload**: File dan folder dengan Google Drive style overlay
* **Multiple Selection**: Ctrl+Click, Shift+Click, Click & Drag selection box
* **View Modes**: Large Icons, Medium Icons, List, Details
* **Context Menu**: Right-click untuk Open, Rename, Download, Delete
* **Breadcrumb Navigation**: Navigasi hierarki folder
* **Keyboard Shortcuts**: Ctrl+A (select all), Delete, Escape, Enter
* **Real-time Updates**: Tanpa page reload untuk semua operasi CRUD

### 10.2 Error Handling Pattern

```javascript
if (!response.ok) {
    throw new Error(data.message || 'Custom error message');
}

if (data.success) {
    // Success handling
} else {
    showToast('Error', data.message || 'Fallback message', 'error');
}
```

### 10.3 Validation Rules

* **folder_id**: required|exists:folders,id (file HARUS dalam folder)
* **file**: required|file|max:102400 (100MB)
* **name**: required|string|max:255
* **parent_id**: nullable|exists:folders,id (untuk create folder)

### 10.4 Storage Structure

```
storage/app/public/repository/
└── {uuid}.{extension}
```

File disimpan dengan UUID name, original name di database.

### 10.5 Root Folder Mechanism

* Root folder dibuat di FilesSeeder dengan parent_id=null
* Controller otomatis load root folder jika tidak ada parameter folder
* Query root folder: `Folders::whereNull('parent_id')->first()`
* Semua upload tanpa folder parameter akan masuk ke root folder
* Matkul folders berada di bawah `/Matkuls/` dengan is_root=true

---

## 11. Kesimpulan

Sistem ini dirancang untuk:

* Konsisten secara data
* Aman terhadap overwrite dan konflik
* Fleksibel terhadap kebutuhan akademik
* Modern file manager UX seperti Google Drive
* Siap digunakan pada skala production

Academic Repository System bukan sekadar penyimpanan file, tetapi **fondasi infrastruktur akademik yang scalable dan user-friendly**.
