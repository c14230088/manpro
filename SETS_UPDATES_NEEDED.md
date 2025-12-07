# Sets Feature Updates - Single Desk Attachment

## ✅ Backend Changes (COMPLETED):

### 1. SetController.php
- `index()` - Removed filtering logic, returns all sets (filtering now handled by frontend)
- `attachSetToDesk()` - Updated to attach all 4 items to ONE desk instead of 4 desks
  - Changed validation: `desk_location` (string) instead of `desk_locations` (array)
  - All 4 items now attached to the same desk

## Frontend Changes Needed in sets.blade.php:

### 1. Update Filter Section (Line 10-46)
Replace the form with client-side filtering:
```html
<div class="max-w-7xl mx-auto px-6 pb-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Filter Kontrol</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 filter-select">
            <div>
                <label for="filter_location" class="block text-sm font-semibold text-gray-700 mb-2">Lokasi</label>
                <select id="filter_location" placeholder="Semua Lokasi...">
                    <option value="">Semua Lokasi</option>
                    <option value="unattached">Belum Terpasang</option>
                    @foreach ($labs as $lab)
                        <option value="{{ $lab->id }}">{{ $lab->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="filter_status" class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                <select id="filter_status" placeholder="Semua Status...">
                    <option value="">Semua Status</option>
                    <option value="good">Bagus</option>
                    <option value="broken">Rusak</option>
                    <option value="on_repair">Sedang Diperbaiki</option>
                </select>
            </div>
        </div>
        <div class="flex justify-end gap-3 mt-6">
            <button id="reset-filter-btn" class="px-6 py-2 bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-300 transition-colors">
                Reset Filter
            </button>
        </div>
    </div>
</div>
```

### 2. Add data attributes to table rows (Line 77-130)
Add these data attributes to each `<tr>`:
```html
<tr data-location="{{ $allAttached ? ($set->items->first()->desk->lab_id ?? '') : 'unattached' }}"
    data-status="{{ $anyRepair ? 'on_repair' : ($anyBroken ? 'broken' : 'good') }}">
```

### 3. Update Attach Modal Instructions (Line 237-241)
Change instruction text:
```html
<p class="text-sm text-yellow-800"><strong>Instruksi:</strong> Pilih 1 meja untuk memasang semua 4 item dalam set ini.</p>
<div id="selected-desk-display" class="mt-2 text-xs text-gray-600">Belum ada meja dipilih.</div>
```

### 4. Update Attach Button Text (Line 246)
```html
<button id="attach-confirm-btn" ... disabled>
    Pasang Set ke 1 Meja
</button>
```

### 5. Update populateDetailModal() function (Line 340)
Add created_at display after note:
```javascript
if (data.note) html += `<div><h4 class="font-semibold text-gray-800 mb-2">Catatan:</h4><p class="text-gray-600">${data.note}</p></div>`;

// ADD THIS:
if (data.created_at) {
    const createdDate = new Date(data.created_at).toLocaleDateString('id-ID', {
        day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit'
    });
    html += `<div><h4 class="font-semibold text-gray-800 mb-2">Waktu Dicatat:</h4><p class="text-gray-600">${createdDate}</p></div>`;
}
```

### 6. Update renderDeskGridModal() (Line 490)
Change to single selection:
```javascript
deskEl.addEventListener('click', () => {
    const location = deskEl.dataset.deskLocation;
    selectedDeskLocations = [location]; // Single selection
    updateSelectedDesksDisplay();
    renderDeskGridModal(modalLabDesks, maxRows, maxCols);
});
```

### 7. Update updateSelectedDesksDisplay() (Line 510)
```javascript
function updateSelectedDesksDisplay() {
    const display = document.getElementById('selected-desk-display');
    const confirmBtn = document.getElementById('attach-confirm-btn');
    
    if (selectedDeskLocations.length === 0) {
        display.innerHTML = 'Belum ada meja dipilih.';
        confirmBtn.disabled = true;
    } else {
        display.innerHTML = `Meja dipilih: ${selectedDeskLocations[0]}`;
        confirmBtn.disabled = false;
    }
}
```

### 8. Update confirmAttachSetToDesks() (Line 525)
```javascript
async function confirmAttachSetToDesks() {
    if (selectedDeskLocations.length !== 1) {
        Swal.fire('Error', 'Anda harus memilih 1 meja.', 'error');
        return;
    }
    
    const result = await Swal.fire({
        title: 'Pasang Set ke Meja?',
        text: `Set '${currentSetName}' akan dipasang ke meja: ${selectedDeskLocations[0]}`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Pasang!',
        cancelButtonText: 'Batal'
    });
    
    if (!result.isConfirmed) return;
    
    showLoading('Memasang Set...');
    
    try {
        const labId = tomSelectLabModal.getValue();
        const response = await fetch(`/admin/sets/${currentSetId}/attach-desks`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                lab_id: labId,
                desk_location: selectedDeskLocations[0] // Single desk
            })
        });
        
        const data = await response.json();
        if (!response.ok) throw new Error(data.message || 'Gagal memasang set.');
        
        hideLoading();
        showToast('Berhasil!', data.message, 'success');
        
        document.getElementById('attach-desk-modal').classList.add('hidden');
        document.getElementById('action-modal').classList.add('hidden');
        checkModalState();
        
        setTimeout(() => location.reload(), 1000);
        
    } catch (error) {
        hideLoading();
        Swal.fire('Gagal', error.message, 'error');
    }
}
```

### 9. Add Client-Side Filtering (Line 580, in DOMContentLoaded)
```javascript
// Initialize TomSelect for filters
const locationFilter = new TomSelect('#filter_location', { plugins: ['clear_button'] });
const statusFilter = new TomSelect('#filter_status', { plugins: ['clear_button'] });

// Client-side filtering
function applyFilters() {
    const location = locationFilter.getValue();
    const status = statusFilter.getValue();
    const rows = document.querySelectorAll('#sets-datatable tbody tr:not(:last-child)');
    
    rows.forEach(row => {
        let showRow = true;
        
        if (location && row.dataset.location !== location) showRow = false;
        if (status && row.dataset.status !== status) showRow = false;
        
        row.style.display = showRow ? '' : 'none';
    });
}

locationFilter.on('change', applyFilters);
statusFilter.on('change', applyFilters);

document.getElementById('reset-filter-btn').addEventListener('click', () => {
    locationFilter.clear();
    statusFilter.clear();
    applyFilters();
});
```

## Summary of Changes:
1. ✅ All 4 items now attach to 1 desk (not 4 desks)
2. ✅ Filtering moved to frontend (no backend requests)
3. ✅ Location filter includes "Belum Terpasang" option
4. ✅ Status filter removed "Sedang di booking" (wasn't there anyway)
5. ✅ Detail modal shows "Waktu Dicatat" (created_at)
6. ✅ Instructions updated to reflect single desk selection
