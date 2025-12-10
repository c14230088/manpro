# Implementasi Fitur Lab Attachment untuk Items dan Components

## Overview
Fitur ini memungkinkan Items dan Components untuk di-attach langsung ke Lab (disimpan di "Lemari Lab") sebagai alternatif dari attach ke Desk. Ini memberikan fleksibilitas dalam manajemen inventaris.

## Business Rules
1. **Items**: 
   - Jika `desk_id` NULL, maka bisa memiliki `lab_id`
   - Jika `desk_id` ada, maka `lab_id` harus NULL
   - Tidak bisa attach ke desk dan lab secara bersamaan

2. **Components**:
   - Jika `item_id` ada (attached ke item), maka `lab_id` harus NULL
   - Jika `item_id` NULL, maka bisa memiliki `lab_id`
   - Tidak bisa attach ke item dan lab secara bersamaan

## Perubahan Database
Database migration sudah mendukung `lab_id` di tabel `items` dan `components`:
- `items.lab_id` (nullable, foreign key ke `labs.id`)
- `components.lab_id` (nullable, foreign key ke `labs.id`)

## Perubahan Backend

### 1. ItemsController.php
**Method Baru:**
- `attachToLab(Request $request, Items $item, Labs $lab)` - Attach item ke lab
- `detachFromLab(Request $request, Items $item)` - Lepas item dari lab

**Method Diupdate:**
- `attachToDesk()` - Sekarang clear `lab_id` saat attach ke desk

### 2. ComponentsController.php
**Method Baru:**
- `attachToLab(Request $request, Components $component, Labs $lab)` - Attach component ke lab
- `detachFromLab(Request $request, Components $component)` - Lepas component dari lab

### 3. LabsController.php
**Method Baru:**
- `getLabStorage(Labs $lab)` - Mengambil items dan components yang attached ke lab tertentu
  - Return: `{ items: [...], components: [...] }`
  - Filter: Items dengan `desk_id` NULL, Components dengan `item_id` NULL

### 4. AdminController.php
**Update Filter:**
- Filter `lab_id` sekarang mencari items/components yang ada di desk lab ATAU yang attached langsung ke lab
- Filter `status` (affiliated/unaffiliated) sekarang mempertimbangkan `lab_id` juga

### 5. Routes (web.php)
**Route Baru:**
```php
// Items
Route::post('/items/{item}/attach-lab/{lab}', [ItemsController::class, 'attachToLab']);
Route::post('/items/{item}/detach-lab', [ItemsController::class, 'detachFromLab']);

// Components
Route::post('/components/{component}/attach-lab/{lab}', [ComponentsController::class, 'attachToLab']);
Route::post('/components/{component}/detach-lab', [ComponentsController::class, 'detachFromLab']);

// Labs
Route::get('/labs/{lab}/storage', [LabsController::class, 'getLabStorage']);
```

## Perubahan Frontend

### 1. items.blade.php
**Tampilan Lokasi:**
- Kolom "Lokasi" sekarang menampilkan 3 kemungkinan:
  - Desk: "Lab Name - Meja A1" (hitam)
  - Lab: "Lab Name - Lemari Lab" (indigo)
  - Belum Terpasang (abu-abu)

**Modal Action:**
- Tambah tombol "Pasang ke Lab" (indigo) di modal action
- Handler JavaScript `openAttachLabModal()` dan `attachToLab()`

### 2. labs.blade.php
**Section Baru: "Lemari Lab"**
- Tampil di bawah denah meja
- Menampilkan 2 kolom: Items dan Components yang attached ke lab
- Auto-load saat lab dipilih
- Styling: Items (biru), Components (ungu)
- Menampilkan: nama, serial code, type, kondisi, dan spesifikasi

**JavaScript:**
- `loadLabStorage(labId)` - Load data lemari lab
- `renderLabStorage(items, components)` - Render UI lemari lab

## Cara Penggunaan

### Attach Item/Component ke Lab
1. Buka halaman Items & Components
2. Klik tombol "Aksi" pada item/component yang ingin di-attach
3. Klik tombol "Pasang ke Lab" (warna indigo)
4. Pilih laboratorium dari dropdown
5. Klik "Pasang"

### Melihat Lemari Lab
1. Buka halaman Labs
2. Pilih laboratorium dari dropdown
3. Scroll ke bawah setelah denah meja
4. Lihat section "Lemari Lab (Items & Components)"

### Filter Items/Components di Lab
1. Buka halaman Items & Components
2. Di filter "Laboratorium", pilih lab yang diinginkan
3. Sistem akan menampilkan:
   - Items yang ada di desk lab tersebut
   - Items yang attached langsung ke lab tersebut
   - Components yang ada di item di lab tersebut
   - Components yang attached langsung ke lab tersebut

## Testing Checklist
- [ ] Attach item ke lab (item tanpa desk_id)
- [ ] Attach component ke lab (component tanpa item_id)
- [ ] Validasi: Item dengan desk_id tidak bisa attach ke lab
- [ ] Validasi: Component dengan item_id tidak bisa attach ke lab
- [ ] Attach item ke desk akan clear lab_id
- [ ] Filter lab menampilkan items/components yang benar
- [ ] Tampilan "Lemari Lab" di labs.blade.php
- [ ] Tampilan lokasi "Lemari Lab" di items.blade.php

## Notes
- Fitur ini tidak mengubah struktur database yang sudah ada
- Backward compatible dengan data existing
- UI menggunakan warna indigo untuk membedakan "Lemari Lab" dari "Desk"
