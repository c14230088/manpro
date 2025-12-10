# Dokumentasi Fitur Detachment & Set Lab Attachment

## Ringkasan Fitur Baru

### 1. Attach Set Items ke Lab
**Endpoint**: `POST /admin/sets/{set}/attach-lab`
**Controller**: `SetController@attachSetToLab`
**Fungsi**: Memindahkan semua items dalam suatu Set ke Lab Storage (Lemari Lab)

**Request Body**:
```json
{
  "lab_id": "uuid-lab"
}
```

**Business Logic**:
- Set harus memiliki 4 items
- Semua items dalam set akan dipindahkan ke lab_id yang dipilih
- desk_id dari semua items akan di-set NULL
- lab_id dari semua items akan di-set ke lab yang dipilih

**Cara Penggunaan**:
- Dari `sets.blade.php`: Tambahkan tombol "Pasang ke Lab" di action modal
- Saat membuat Set baru: Tambahkan opsi checkbox "Pasang ke Lab" (alternatif dari "Pasang ke Meja")

---

### 2. Detach Item dari Desk
**Endpoint**: `POST /admin/items/{item}/detach-desk`
**Controller**: `ItemsController@detachFromDesk`
**Fungsi**: Melepas item dari meja (set desk_id = NULL)

**Validasi**:
- Item harus memiliki desk_id (sudah terpasang di meja)

**Cara Penggunaan**:
- Dari `labs.blade.php`: Tombol "Lepas dari Meja" pada item di desk grid
- Dari `sets.blade.php`: Tombol "Lepas dari Meja" pada detail set
- Dari `items.blade.php`: Tombol "Lepas dari Meja" di action modal

---

### 3. Detach Component dari Item
**Endpoint**: `POST /admin/components/{component}/detach-item`
**Controller**: `ComponentsController@detachFromItem`
**Fungsi**: Melepas component dari item parent (set item_id = NULL)

**Validasi**:
- Component harus memiliki item_id (sudah terpasang di item)

**Cara Penggunaan**:
- Dari `labs.blade.php`: Tombol "Lepas dari Item" pada component di item detail
- Dari `sets.blade.php`: Tombol "Lepas dari Item" pada component detail
- Dari `items.blade.php`: Tombol "Lepas dari Item" di action modal component

---

### 4. Detach Item/Component dari Lab
**Endpoint (Item)**: `POST /admin/items/{item}/detach-lab`
**Endpoint (Component)**: `POST /admin/components/{component}/detach-lab`
**Controller**: `ItemsController@detachFromLab` / `ComponentsController@detachFromLab`
**Fungsi**: Melepas item/component dari Lab Storage (set lab_id = NULL)

**Validasi**:
- Item/Component harus memiliki lab_id (sudah terpasang di lab)

**Cara Penggunaan**:
- Dari `labs.blade.php`: Tombol "Lepas dari Lab" pada item/component di Lab Storage section
- Dari `sets.blade.php`: Tombol "Lepas dari Lab" pada detail set
- Dari `items.blade.php`: Tombol "Lepas dari Lab" di action modal

---

## Routes yang Ditambahkan

```php
// Items
Route::post('/items/{item}/detach-desk', [ItemsController::class, 'detachFromDesk'])->name('admin.items.detachFromDesk');

// Components
Route::post('/components/{component}/detach-item', [ComponentsController::class, 'detachFromItem'])->name('admin.components.detachFromItem');

// Sets
Route::post('/sets/{set}/attach-lab', [SetController::class, 'attachSetToLab'])->name('admin.sets.attachLab');
```

---

## Implementasi Frontend

### A. labs.blade.php
Tambahkan tombol detach pada:
1. **Desk Grid Items**: Tombol "Lepas dari Meja" (merah)
2. **Lab Storage Items**: Tombol "Lepas dari Lab" (indigo)
3. **Lab Storage Components**: Tombol "Lepas dari Lab" (indigo)

### B. sets.blade.php
Tambahkan di Action Modal:
1. Tombol "Pasang ke Lab" (indigo) - memanggil `attachSetToLab()`
2. Tombol "Lepas dari Meja" (merah) - untuk set yang sudah terpasang
3. Tombol "Lepas dari Lab" (indigo) - untuk set yang ada di lab storage

Tambahkan di Create Set Modal:
1. Radio button: "Pasang ke Meja" atau "Pasang ke Lab"
2. Jika "Pasang ke Lab" dipilih, tampilkan dropdown lab (tanpa desk grid)

### C. items.blade.php
Tambahkan di Action Modal:
1. Tombol "Lepas dari Meja" (merah) - untuk items yang sudah terpasang di desk
2. Tombol "Lepas dari Lab" (indigo) - untuk items yang ada di lab storage
3. Tombol "Lepas dari Item" (purple) - untuk components yang terpasang di item

---

## JavaScript Functions yang Perlu Ditambahkan

### 1. Untuk Sets (sets.blade.php)
```javascript
async function attachSetToLab(setId, labId) {
    const response = await fetch(`/admin/sets/${setId}/attach-lab`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ lab_id: labId })
    });
    // Handle response
}

async function openAttachSetToLabModal() {
    // Show Swal with lab dropdown
    // Call attachSetToLab() on confirm
}
```

### 2. Untuk Items (items.blade.php & labs.blade.php)
```javascript
async function detachItemFromDesk(itemId) {
    const response = await fetch(`/admin/items/${itemId}/detach-desk`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        }
    });
    // Handle response
}

async function detachItemFromLab(itemId) {
    const response = await fetch(`/admin/items/${itemId}/detach-lab`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        }
    });
    // Handle response
}
```

### 3. Untuk Components (items.blade.php & labs.blade.php)
```javascript
async function detachComponentFromItem(componentId) {
    const response = await fetch(`/admin/components/${componentId}/detach-item`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        }
    });
    // Handle response
}

async function detachComponentFromLab(componentId) {
    const response = await fetch(`/admin/components/${componentId}/detach-lab`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        }
    });
    // Handle response
}
```

---

## Testing Checklist

### Backend Testing
- [ ] Test `attachSetToLab()` dengan set yang memiliki 4 items
- [ ] Test `attachSetToLab()` dengan set yang tidak memiliki 4 items (harus error)
- [ ] Test `detachFromDesk()` pada item yang terpasang di desk
- [ ] Test `detachFromDesk()` pada item yang tidak terpasang di desk (harus error)
- [ ] Test `detachFromItem()` pada component yang terpasang di item
- [ ] Test `detachFromItem()` pada component yang tidak terpasang di item (harus error)
- [ ] Test `detachFromLab()` pada item/component yang terpasang di lab
- [ ] Test `detachFromLab()` pada item/component yang tidak terpasang di lab (harus error)

### Frontend Testing
- [ ] Test tombol "Pasang ke Lab" di sets.blade.php
- [ ] Test tombol "Lepas dari Meja" di labs.blade.php (desk grid)
- [ ] Test tombol "Lepas dari Lab" di labs.blade.php (lab storage)
- [ ] Test tombol "Lepas dari Item" di items.blade.php (component action modal)
- [ ] Test tombol "Lepas dari Meja" di items.blade.php (item action modal)
- [ ] Test tombol "Lepas dari Lab" di items.blade.php (item/component action modal)
- [ ] Test checkbox "Pasang ke Lab" saat membuat Set baru

### Integration Testing
- [ ] Attach set ke lab, lalu detach semua items dari lab
- [ ] Attach set ke desk, lalu detach semua items dari desk
- [ ] Attach item ke desk, lalu attach ke lab (harus clear desk_id)
- [ ] Attach component ke item, lalu detach dari item
- [ ] Attach component ke lab, lalu detach dari lab

---

## Catatan Penting

1. **Mutual Exclusivity**:
   - Item tidak bisa memiliki desk_id dan lab_id secara bersamaan
   - Component tidak bisa memiliki item_id dan lab_id secara bersamaan

2. **Set Behavior**:
   - Saat attach set ke desk: semua items di-set desk_id, lab_id di-clear
   - Saat attach set ke lab: semua items di-set lab_id, desk_id di-clear

3. **UI Convention**:
   - Warna Indigo: Lab Storage (Lemari Lab)
   - Warna Biru: Desk Attachment
   - Warna Merah: Detach/Remove actions
   - Warna Purple: Component-related actions

4. **Response Messages**:
   - Semua endpoint mengembalikan JSON dengan format:
     ```json
     {
       "success": true/false,
       "message": "Pesan sukses/error"
     }
     ```

---

## File yang Sudah Dimodifikasi

### Backend
1. `app/Http/Controllers/SetController.php` - Added `attachSetToLab()`
2. `app/Http/Controllers/ItemsController.php` - Added `detachFromDesk()`
3. `app/Http/Controllers/ComponentsController.php` - Added `detachFromItem()`
4. `routes/web.php` - Added 3 new routes

### Frontend (Perlu Implementasi)
1. `resources/views/admin/sets.blade.php` - Perlu tambah tombol & JS functions
2. `resources/views/admin/labs.blade.php` - Perlu tambah tombol detach
3. `resources/views/admin/items.blade.php` - Perlu tambah tombol detach di action modal

---

## Next Steps

1. Implementasi tombol-tombol detach di frontend (labs.blade.php, sets.blade.php, items.blade.php)
2. Implementasi JavaScript functions untuk memanggil endpoint detach
3. Implementasi opsi "Pasang ke Lab" saat membuat Set baru
4. Testing semua fitur secara menyeluruh
5. Update dokumentasi jika ada perubahan

