# Frontend Implementation Guide - Detachment Features

## File yang Sudah Dibuat

### 1. JavaScript Shared Functions
**File**: `public/js/detachment-functions.js`

Berisi 4 fungsi global yang bisa dipanggil dari mana saja:
- `detachItemFromDesk(itemId, csrfToken)`
- `detachItemFromLab(itemId, csrfToken)`
- `detachComponentFromItem(componentId, csrfToken)`
- `detachComponentFromLab(componentId, csrfToken)`

**Cara Include di Blade**:
```blade
@section('script')
    <script src="{{ asset('js/detachment-functions.js') }}"></script>
    <script>
        // Your existing code here
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Call detachment functions
        // Example: detachItemFromDesk('item-uuid', csrfToken);
    </script>
@endsection
```

---

## Implementasi Per File

### A. labs.blade.php

#### 1. Include Script
Tambahkan di bagian `@section('script')`:
```blade
<script src="{{ asset('js/detachment-functions.js') }}"></script>
```

#### 2. Tambah Tombol Detach di Desk Items
Di dalam fungsi `renderDeskGrid()` atau saat render item detail modal, tambahkan tombol:

```javascript
// Contoh: Di dalam modal detail meja
modalBodyHTML += `
    <button onclick="detachItemFromDesk('${item.id}', '${csrfToken}')" 
            class="mt-2 px-3 py-1 bg-red-500 text-white text-xs rounded hover:bg-red-600">
        Lepas dari Meja
    </button>
`;
```

#### 3. Tambah Tombol Detach di Lab Storage
Di dalam fungsi `renderLabStorage()`, tambahkan tombol untuk items dan components:

```javascript
// Untuk Items di Lab Storage
html += `
    <button onclick="detachItemFromLab('${item.id}', '${csrfToken}')" 
            class="mt-2 px-3 py-1 bg-indigo-500 text-white text-xs rounded hover:bg-indigo-600">
        Lepas dari Lab
    </button>
`;

// Untuk Components di Lab Storage
html += `
    <button onclick="detachComponentFromLab('${comp.id}', '${csrfToken}')" 
            class="mt-2 px-3 py-1 bg-indigo-500 text-white text-xs rounded hover:bg-indigo-600">
        Lepas dari Lab
    </button>
`;
```

---

### B. items.blade.php

#### 1. Include Script
```blade
<script src="{{ asset('js/detachment-functions.js') }}"></script>
```

#### 2. Update Action Modal Buttons
Di dalam fungsi `updateActionModalButtons()`, tambahkan tombol detach:

```javascript
function updateActionModalButtons(type = 'item') {
    const attachBtn = document.getElementById('action-btn-attach');
    const attachLabBtn = document.getElementById('action-btn-attach-lab');
    const detachDeskBtn = document.getElementById('action-btn-detach-desk'); // NEW
    const detachLabBtn = document.getElementById('action-btn-detach-lab'); // NEW
    const detachItemBtn = document.getElementById('action-btn-detach-item'); // NEW (for components)
    
    // Show/hide based on item type and current attachment status
    if (type === 'item') {
        // Logic untuk menampilkan tombol yang sesuai
        // Jika item.desk_id != null, show detachDeskBtn
        // Jika item.lab_id != null, show detachLabBtn
    } else {
        // Logic untuk component
        // Jika component.item_id != null, show detachItemBtn
        // Jika component.lab_id != null, show detachLabBtn
    }
}
```

#### 3. Tambah Tombol di Action Modal HTML
Di bagian action modal body, tambahkan tombol baru:

```blade
<button id="action-btn-detach-desk" style="display:none;"
    class="p-6 bg-red-500 hover:bg-red-600 text-white rounded-lg flex flex-col items-center justify-center transition-all shadow-lg">
    <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
    </svg>
    <span class="font-semibold">Lepas dari Meja</span>
</button>

<button id="action-btn-detach-lab" style="display:none;"
    class="p-6 bg-indigo-500 hover:bg-indigo-600 text-white rounded-lg flex flex-col items-center justify-center transition-all shadow-lg">
    <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
    </svg>
    <span class="font-semibold">Lepas dari Lab</span>
</button>

<button id="action-btn-detach-item" style="display:none;"
    class="p-6 bg-purple-500 hover:bg-purple-600 text-white rounded-lg flex flex-col items-center justify-center transition-all shadow-lg">
    <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
    </svg>
    <span class="font-semibold">Lepas dari Item</span>
</button>
```

#### 4. Tambah Event Listeners
```javascript
document.getElementById('action-btn-detach-desk').addEventListener('click', () => {
    detachItemFromDesk(currentItemId, csrfToken);
});

document.getElementById('action-btn-detach-lab').addEventListener('click', () => {
    if (currentItemType === 'item') {
        detachItemFromLab(currentItemId, csrfToken);
    } else {
        detachComponentFromLab(currentItemId, csrfToken);
    }
});

document.getElementById('action-btn-detach-item').addEventListener('click', () => {
    detachComponentFromItem(currentItemId, csrfToken);
});
```

---

### C. sets.blade.php (SUDAH SELESAI)

✅ Tombol "Pasang ke Lab" sudah ditambahkan di action modal
✅ Fungsi `attachSetToLab()` sudah diimplementasikan

---

## Testing Checklist

### Labs.blade.php
- [ ] Include `detachment-functions.js`
- [ ] Tambah tombol "Lepas dari Meja" di detail meja
- [ ] Tambah tombol "Lepas dari Lab" di Lab Storage (Items)
- [ ] Tambah tombol "Lepas dari Lab" di Lab Storage (Components)
- [ ] Test: Klik tombol detach item dari desk
- [ ] Test: Klik tombol detach item dari lab
- [ ] Test: Klik tombol detach component dari lab

### Items.blade.php
- [ ] Include `detachment-functions.js`
- [ ] Tambah 3 tombol baru di action modal HTML
- [ ] Update fungsi `updateActionModalButtons()` untuk show/hide tombol
- [ ] Tambah event listeners untuk 3 tombol baru
- [ ] Test: Detach item dari desk (item yang terpasang di meja)
- [ ] Test: Detach item dari lab (item di lab storage)
- [ ] Test: Detach component dari item (component yang terpasang di item)
- [ ] Test: Detach component dari lab (component di lab storage)

### Sets.blade.php
- [x] Tombol "Pasang ke Lab" sudah ada
- [x] Fungsi `attachSetToLab()` sudah ada
- [ ] Test: Attach set ke lab
- [ ] Test: Verifikasi semua items dalam set pindah ke lab storage

---

## Contoh Implementasi Lengkap (items.blade.php)

```javascript
// Di dalam initializeActionModal()
function initializeActionModal() {
    // ... existing code ...
    
    document.getElementById('action-btn-detach-desk').addEventListener('click', () => {
        detachItemFromDesk(currentItemId, csrfToken);
    });
    
    document.getElementById('action-btn-detach-lab').addEventListener('click', () => {
        if (currentItemType === 'item') {
            detachItemFromLab(currentItemId, csrfToken);
        } else {
            detachComponentFromLab(currentItemId, csrfToken);
        }
    });
    
    document.getElementById('action-btn-detach-item').addEventListener('click', () => {
        detachComponentFromItem(currentItemId, csrfToken);
    });
}

// Update fungsi updateActionModalButtons
function updateActionModalButtons(type = 'item') {
    const attachBtn = document.getElementById('action-btn-attach');
    const attachLabBtn = document.getElementById('action-btn-attach-lab');
    const detachDeskBtn = document.getElementById('action-btn-detach-desk');
    const detachLabBtn = document.getElementById('action-btn-detach-lab');
    const detachItemBtn = document.getElementById('action-btn-detach-item');
    const manageCompBtn = document.getElementById('action-btn-manage-comp');
    
    // Hide all detach buttons first
    detachDeskBtn.style.display = 'none';
    detachLabBtn.style.display = 'none';
    detachItemBtn.style.display = 'none';
    
    if (type === 'item') {
        attachBtn.style.display = 'flex';
        attachLabBtn.style.display = 'flex';
        manageCompBtn.style.display = 'flex';
        
        // Show detach buttons based on current attachment
        // You need to pass item data to determine this
        // Example: if item has desk_id, show detachDeskBtn
        // if item has lab_id, show detachLabBtn
        
    } else {
        attachBtn.style.display = 'none';
        manageCompBtn.style.display = 'none';
        attachLabBtn.style.display = 'flex';
        
        // For components
        // if component has item_id, show detachItemBtn
        // if component has lab_id, show detachLabBtn
    }
}
```

---

## Notes

1. **CSRF Token**: Semua fungsi detachment memerlukan CSRF token. Pastikan token tersedia di setiap halaman.

2. **Reload After Success**: Semua fungsi detachment akan otomatis reload halaman setelah sukses. Jika ingin behavior berbeda, edit file `detachment-functions.js`.

3. **Error Handling**: Semua error sudah di-handle dengan SweetAlert2. Pastikan SweetAlert2 sudah ter-load di halaman.

4. **Button Visibility Logic**: Tombol detach harus ditampilkan/disembunyikan berdasarkan status attachment item/component saat ini. Implementasikan logic ini di fungsi `updateActionModalButtons()`.

5. **Color Convention**:
   - Red (#ef4444): Detach from Desk
   - Indigo (#6366f1): Detach from Lab
   - Purple (#a855f7): Detach from Item

---

## Quick Implementation Steps

1. **Include Script** di semua blade files yang memerlukan detachment:
   ```blade
   <script src="{{ asset('js/detachment-functions.js') }}"></script>
   ```

2. **Tambah Tombol** di UI (HTML/JavaScript render)

3. **Call Function** saat tombol diklik:
   ```javascript
   onclick="detachItemFromDesk('${itemId}', '${csrfToken}')"
   ```

4. **Test** setiap fungsi untuk memastikan bekerja dengan baik

---

## Support

Jika ada error atau pertanyaan, cek:
1. Console browser untuk error JavaScript
2. Network tab untuk melihat response dari server
3. Laravel log untuk error backend
