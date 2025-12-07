# Sets Feature Implementation Instructions

## Completed:
1. ✅ SetController created with index(), getSetDetails(), and attachSetToDesk() methods
2. ✅ Routes added for sets management
3. ✅ sets.blade.php view created with filters, table, and modals
4. ✅ ItemsController updated to support desk attachment during set creation

## To Complete in items.blade.php:

### 1. Add Checkbox for Desk Attachment in Create Set Modal

In the "create-set-modal" form body (around line 1100), add this BEFORE the closing `</div>` of the modal body:

```html
{{-- Option to Attach to Desk --}}
<div class="bg-blue-50 rounded-xl shadow-sm border border-blue-200 p-6 md:p-8">
    <div class="flex items-center mb-4">
        <input type="checkbox" id="attach_to_desk_checkbox" class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
        <label for="attach_to_desk_checkbox" class="ml-2 block text-sm font-semibold text-gray-700">
            Pasang Set ke Meja Sekarang
        </label>
    </div>
    <div id="desk-attachment-section" class="hidden space-y-4">
        <div>
            <label for="set-lab-selector" class="block text-sm font-semibold text-gray-700 mb-2">Pilih Laboratorium</label>
            <select id="set-lab-selector" placeholder="Pilih Lab..."></select>
        </div>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <p class="text-sm text-yellow-800"><strong>Instruksi:</strong> Pilih 4 meja secara berurutan untuk 4 item (Monitor, Mouse, CPU, Keyboard).</p>
            <div id="set-selected-desks-display" class="mt-2 text-xs text-gray-600">Belum ada meja dipilih.</div>
        </div>
        <div id="set-desk-grid-container">
            <div class="text-center py-8 text-gray-500">Pilih lab untuk melihat denah.</div>
        </div>
    </div>
</div>
```

### 2. Add JavaScript Variables (around line 1300)

Add these variables to the global scope:

```javascript
let tomSelectSetLab;
let setLabDesks = [];
let setSelectedDeskLocations = [];
```

### 3. Update initializeCreateSetModal() Function

Add this code at the end of the `initializeCreateSetModal()` function (before the closing brace):

```javascript
// Initialize desk attachment checkbox
const attachCheckbox = document.getElementById('attach_to_desk_checkbox');
const deskSection = document.getElementById('desk-attachment-section');
const setLabSelectorEl = document.getElementById('set-lab-selector');

attachCheckbox.addEventListener('change', function() {
    if (this.checked) {
        deskSection.classList.remove('hidden');
        loadLabsForSetAttachment();
    } else {
        deskSection.classList.add('hidden');
        setSelectedDeskLocations = [];
        updateSetSelectedDesksDisplay();
    }
});

tomSelectSetLab = new TomSelect(setLabSelectorEl, {
    create: false,
    placeholder: 'Pilih Lab...',
    onChange: (labId) => {
        if (labId) {
            setSelectedDeskLocations = [];
            updateSetSelectedDesksDisplay();
            fetchDeskMapForSet(labId);
        }
    }
});
```

### 4. Add Helper Functions (before the DOMContentLoaded event)

```javascript
async function loadLabsForSetAttachment() {
    try {
        const response = await fetch("{{ route('admin.labs.list') }}");
        if (!response.ok) throw new Error('Gagal memuat daftar lab.');
        const labs = await response.json();
        
        if (tomSelectSetLab) {
            tomSelectSetLab.clearOptions();
            tomSelectSetLab.addOptions(labs.map(lab => ({ value: lab.id, text: lab.name })));
        }
    } catch (error) {
        showToast('Error', error.message, 'error');
    }
}

async function fetchDeskMapForSet(labId) {
    const container = document.getElementById('set-desk-grid-container');
    container.innerHTML = '<div class="flex items-center justify-center py-8"><div class="animate-spin rounded-full h-12 w-8 border-b-2 border-indigo-600"></div></div>';
    
    try {
        const response = await fetch(`/admin/labs/${labId}/desks`);
        if (!response.ok) throw new Error('Gagal memuat denah meja.');
        setLabDesks = await response.json();
        
        let maxRow = 5, maxCol = 10;
        if (setLabDesks.length > 0) {
            setLabDesks.forEach(d => {
                const row = d.location.charCodeAt(0) - 64;
                const col = parseInt(d.location.substring(1));
                if (row > maxRow) maxRow = row;
                if (col > maxCol) maxCol = col;
            });
        }
        renderDeskGridForSet(setLabDesks, maxRow, maxCol);
    } catch (error) {
        container.innerHTML = `<div class="text-center py-8 text-red-500">${error.message}</div>`;
    }
}

function renderDeskGridForSet(desks, maxRows, maxCols) {
    const container = document.getElementById('set-desk-grid-container');
    let html = `<div class="overflow-x-auto pb-4"><div class="grid gap-3 border-2 min-w-fit border-slate-300 p-6" style="grid-template-columns: repeat(${maxCols}, minmax(100px, 1fr)); grid-template-rows: repeat(${maxRows}, auto);">`;
    
    const occupiedSlots = new Set(desks.map(d => d.location));
    
    desks.forEach(desk => {
        const row = desk.location.charCodeAt(0) - 64;
        const col = parseInt(desk.location.substring(1));
        const isSelected = setSelectedDeskLocations.includes(desk.location);
        const selectionIndex = setSelectedDeskLocations.indexOf(desk.location);
        
        let bgColorClass = isSelected ? 'bg-indigo-200 border-indigo-500' : 'bg-gray-50 border-gray-300 hover:bg-gray-100';
        let iconColor = isSelected ? 'text-indigo-700' : 'text-gray-500';
        
        html += `<div data-desk-location="${desk.location}" style="grid-area: ${row} / ${col};" class="set-desk-item group transition-all duration-200 flex flex-col items-center justify-center p-3 border-2 rounded-lg min-h-24 ${bgColorClass} cursor-pointer">`;
        html += `<span class="font-bold text-sm text-gray-800 block select-none">${desk.location}</span>`;
        if (isSelected) html += `<span class="text-xs text-indigo-600 mt-1 inline-block select-none font-semibold">Item ${selectionIndex + 1}</span>`;
        html += `</div>`;
    });
    
    for (let r = 1; r <= maxRows; r++) {
        for (let c = 1; c <= maxCols; c++) {
            const location = `${String.fromCharCode(64 + r)}${c}`;
            if (!occupiedSlots.has(location)) {
                html += `<div style="grid-area: ${r} / ${c}; visibility: hidden;"></div>`;
            }
        }
    }
    
    html += '</div></div>';
    container.innerHTML = html;
    
    document.querySelectorAll('.set-desk-item').forEach(deskEl => {
        deskEl.addEventListener('click', () => {
            const location = deskEl.dataset.deskLocation;
            if (setSelectedDeskLocations.includes(location)) {
                setSelectedDeskLocations = setSelectedDeskLocations.filter(l => l !== location);
            } else {
                if (setSelectedDeskLocations.length < 4) {
                    setSelectedDeskLocations.push(location);
                } else {
                    showToast('Maksimal 4 Meja', 'Anda hanya bisa memilih 4 meja untuk 4 item.', 'warning');
                    return;
                }
            }
            updateSetSelectedDesksDisplay();
            renderDeskGridForSet(setLabDesks, maxRows, maxCols);
        });
    });
}

function updateSetSelectedDesksDisplay() {
    const display = document.getElementById('set-selected-desks-display');
    if (setSelectedDeskLocations.length === 0) {
        display.innerHTML = 'Belum ada meja dipilih.';
    } else {
        display.innerHTML = `Meja dipilih (${setSelectedDeskLocations.length}/4): ${setSelectedDeskLocations.join(', ')}`;
    }
}
```

### 5. Update submitCreateSetForm() Function

Replace the existing `submitCreateSetForm` function with this updated version:

```javascript
async function submitCreateSetForm(submitBtn, form) {
    const attachCheckbox = document.getElementById('attach_to_desk_checkbox');
    
    if (attachCheckbox.checked && setSelectedDeskLocations.length !== 4) {
        Swal.fire('Error', 'Anda harus memilih tepat 4 meja jika ingin memasang set ke meja.', 'error');
        return;
    }
    
    showLoading('Membuat Set Item...', 'Ini mungkin memakan waktu beberapa saat...');
    submitBtn.disabled = true;

    const formData = {
        set_name: document.getElementById('set_name').value,
        set_note: document.getElementById('set_note').value,
        _token: form.querySelector('input[name="_token"]').value,
        items: []
    };
    
    // Add desk attachment data if checkbox is checked
    if (attachCheckbox.checked) {
        formData.attach_to_desk = true;
        formData.lab_id = tomSelectSetLab.getValue();
        formData.desk_locations = setSelectedDeskLocations;
    }

    setItemTomInstances.forEach(itemInstance => {
        const itemRow = itemInstance.row;
        const itemData = {
            is_component: '0',
            name: itemRow.querySelector('.set-item-name').value,
            serial_code: itemRow.querySelector('.set-item-serial').value,
            condition: itemRow.querySelector('.set-item-condition:checked').value,
            produced_at: itemRow.querySelector('.set-item-produced-at').value,
            type: itemInstance.typeSelect.getValue(),
            specifications: [],
            new_components: []
        };

        itemInstance.mainSpecs.forEach(spec => {
            const attrVal = spec.attr.getValue();
            const valVal = spec.val.getValue();
            if (attrVal && valVal) {
                itemData.specifications.push({
                    attribute: attrVal,
                    value: valVal
                });
            }
        });

        itemInstance.newComponents.forEach(compInstance => {
            const compRow = compInstance.row;
            const componentData = {
                name: compRow.querySelector('.new-component-name').value,
                serial_code: compRow.querySelector('.new-component-serial').value,
                condition: compRow.querySelector('.new-component-condition:checked').value,
                type: compInstance.typeSelect.getValue(),
                produced_at: compRow.querySelector('.new-component-produced-at').value,
                specifications: []
            };

            compInstance.specInstances.forEach(spec => {
                const attrVal = spec.attr.getValue();
                const valVal = spec.val.getValue();
                if (attrVal && valVal) {
                    componentData.specifications.push({
                        attribute: attrVal,
                        value: valVal
                    });
                }
            });
            itemData.new_components.push(componentData);
        });

        formData.items.push(itemData);
    });

    formData.set_count = formData.items.length;

    try {
        const response = await fetch(form.dataset.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': formData._token
            },
            body: JSON.stringify(formData)
        });

        const data = await response.json();

        if (!response.ok) {
            if (response.status === 422) {
                throw new Error(data.message || 'Data tidak valid.');
            }
            throw new Error(data.message || 'Terjadi kesalahan.');
        }

        hideLoading();
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: data.message,
        }).then(() => {
            location.reload();
        });

    } catch (error) {
        hideLoading();
        Swal.fire('Gagal Membuat Set', error.message, 'error');
    } finally {
        submitBtn.disabled = false;
    }
}
```

## Testing Checklist:
- [ ] Create a new set with 4 items
- [ ] Test creating set WITHOUT desk attachment
- [ ] Test creating set WITH desk attachment (select 4 desks)
- [ ] View sets list at /admin/sets
- [ ] Filter sets by lab and status
- [ ] View set details
- [ ] Attach existing set to desks
- [ ] Verify all items in set are properly attached to selected desks

## Navigation:
Add link to sidebar in layouts/admin.blade.php:
```html
<li><a href="{{ route('admin.sets') }}" id="sets">Sets</a></li>
```
