@extends('layouts.admin')

@section('title', 'Laboratories Mapping')

@section('body')
    <div class="flex flex-col w-full py-4 shadow-md items-center justify-center mb-5">
        <h1 class="text-center text-4xl uppercase font-bold mb-2">Laboratories</h1>
    </div>

    <div class="max-w-7xl mx-auto px-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <label for="lab-selector" class="block text-sm font-semibold text-gray-700 mb-2">
                Pilih Laboratorium
            </label>
            <div class="relative">
                <select id="lab-selector"
                    class="block w-full md:w-2/3 lg:w-1/2 px-4 py-3 text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200 appearance-none bg-white cursor-pointer">
                    <option value="" selected disabled>-- Pilih Laboratorium --</option>
                    @foreach ($labs as $lab)
                        <option value="{{ $lab->id }}">{{ $lab->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div id="desk-grid-container" class="mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                        </path>
                    </svg>
                    <p class="text-gray-500 text-lg">Silakan pilih laboratorium untuk menampilkan denah meja</p>
                </div>
            </div>
        </div>
    </div>

    {{-- 
      =========================================================
      MODAL BARU: Tambah Item ke Meja
      =========================================================
    --}}
    <div id="add-item-modal" class="hidden" role="dialog" aria-modal="true" aria-labelledby="add-item-modal-title">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 z-[3000]"></div>
        
        {{-- 
          PERUBAHAN 1: Menambahkan ID 'add-item-modal-overlay' di sini
        --}}
        <div id="add-item-modal-overlay" class="fixed inset-0 flex items-center justify-center p-4 z-[3001]">
            <div id="add-item-modal-area"
                class="relative bg-white rounded-lg shadow-xl w-full max-w-5xl flex flex-col max-h-[90vh]">

                {{-- Modal Header --}}
                <div class="flex justify-between items-center p-4 border-b border-gray-200 rounded-t">
                    <h3 id="add-item-modal-title" class="text-xl font-semibold text-gray-900">
                        Tambah Item ke Meja
                    </h3>
                    <button id="add-item-modal-close-button" type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div id="add-item-modal-body" class="p-6 space-y-4 overflow-y-auto">
                    {{-- Filter --}}
                    <fieldset id="item-filter-fieldset"
                        class="border border-gray-300 rounded-lg p-4 transition-opacity duration-300">
                        <legend class="text-sm font-semibold text-gray-700 px-2">Filter Item (Hanya akan menampilkan item dengan kondisi Bagus yang masih belum ditaruh di meja lain)</legend>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="filter-type-select"
                                    class="block text-xs font-medium text-gray-600 mb-1">Berdasarkan Tipe</label>
                                <select id="filter-type-select" placeholder="Semua Tipe..."></select>
                            </div>
                            <div>
                                <label for="filter-attr-select"
                                    class="block text-xs font-medium text-gray-600 mb-1">Berdasarkan Atribut</label>
                                <select id="filter-attr-select" placeholder="Semua Atribut..."></select>
                            </div>
                            <div>
                                <label for="filter-value-select"
                                    class="block text-xs font-medium text-gray-600 mb-1">Berdasarkan Value</label>
                                <select id="filter-value-select" placeholder="Pilih Atribut dulu..." disabled></select>
                            </div>
                        </div>
                    </fieldset>

                    {{-- Dropdown Item --}}
                    <div>
                        <label for="item-select-dropdown" class="block text-sm font-semibold text-gray-700 mb-2">Pilih Item
                            (bisa lebih dari satu)</label>
                        <div id="item-select-loading"
                            class="flex items-center text-gray-500 p-3 border border-gray-200 rounded-lg">
                            <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-gray-400 mr-2"></div>
                            Memuat item...
                        </div>
                        <select id="item-select-dropdown" multiple placeholder="Pilih item..." class="hidden"></select>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="flex items-center justify-end p-4 space-x-3 border-t border-gray-200 rounded-b">
                    <button id="add-item-modal-footer-cancel-button" type="button"
                        class="text-gray-700 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-indigo-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center border border-gray-300">
                        Batal
                    </button>
                    <button id="add-item-modal-footer-save-button" type="button"
                        class="text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:outline-none focus:ring-indigo-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center w-36 h-[42px] flex items-center justify-center">
                        <span class="btn-text">Simpan</span>
                        {{-- <span class="btn-loading hidden animate-spin rounded-full h-5 w-5 border-b-2 border-white"></span> --}}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    {{-- CSS untuk mode edit dan drag-and-drop --}}
    <style>
        #desk-grid { transform-origin: top left; }
        .empty-slot { @apply bg-slate-100 border-2 border-dashed border-slate-300 rounded-xl min-h-36 flex items-center justify-center text-slate-500 font-semibold text-lg; transition: background-color 0.2s, border-color 0.2s; }
        .empty-slot.drag-over, .desk-item.drag-over { @apply bg-indigo-100 border-indigo-400 text-indigo-500; }
        .desk-item.dragging { @apply opacity-50 border-indigo-500 shadow-2xl scale-95; }
        #desk-grid:not(.edit-mode) .empty-slot { visibility: hidden; @apply bg-transparent border-transparent; }
        #desk-grid.edit-mode .desk-item { cursor: grab; }
        #desk-grid.edit-mode .desk-item:active { cursor: grabbing; }
        #item-filter-fieldset:disabled { @apply opacity-50 cursor-not-allowed; }
    </style>

    <script>
        document.getElementById('labs').classList.add('bg-slate-100');

        document.addEventListener('DOMContentLoaded', function() {
            const labSelector = document.getElementById('lab-selector');
            const deskContainer = document.getElementById('desk-grid-container');
            let desks = [];
            let newDesks = [];
            let isAddDeskMode = false;
            let labConfig = { maxRows: 5, maxCols: 10 };
            let currentZoom = 1.0;
            const maxZoom = 1.5, minZoom = 0.5, zoomStep = 0.1;
            let isMouseDown = false;
            let selectedSlots = new Set();
            let isDeletingMode = false;
            let slotsToCancel = new Set();
            let isMainDeleteMode = false;
            let isMouseDownMainDelete = false;
            let desksToMainDelete = new Set();

            let currentDeskId = null;
            let tomSelectType, tomSelectAttr, tomSelectValue, tomSelectItem;
            let allSpecAttributes = [];

            function showLoading(title = 'Loading...') {
                Swal.fire({
                    title: title,
                    text: 'Silakan tunggu...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            }

            function hideLoading() {
                Swal.close();
            }

            function applyZoom() {
                const deskGrid = document.getElementById('desk-grid');
                const zoomLevelDisplay = document.getElementById('zoom-level-display');
                if (deskGrid && zoomLevelDisplay) {
                    deskGrid.style.transform = '';
                    deskGrid.style.zoom = currentZoom;
                    zoomLevelDisplay.textContent = `${Math.round(currentZoom * 100)}%`;
                }
            }

            function autoZoomToFit() {
                const scroller = document.getElementById('grid-scroller');
                const grid = document.getElementById('desk-grid');
                if (!scroller || !grid) return;
                const scrollerWidth = scroller.clientWidth;
                const scrollerHeight = scroller.clientHeight;
                const gridWidth = grid.scrollWidth;
                const gridHeight = grid.scrollHeight;
                const zoomX = scrollerWidth / gridWidth;
                const zoomY = scrollerHeight / gridHeight;
                let newZoom = Math.min(zoomX, zoomY);
                newZoom = Math.max(minZoom, Math.min(maxZoom, newZoom));
                currentZoom = newZoom;
                applyZoom();
            }


            function cancelNewDesk(location) {
                if (!isAddDeskMode) return;
                const index = newDesks.findIndex(desk => desk.location === location);
                if (index > -1) {
                    newDesks.splice(index, 1);
                    renderDeskGrid(true);
                }
            }

            async function saveNewDesks() {
                if (newDesks.length === 0) {
                    return { success: true, newDesksSaved: 0 };
                }
                showLoading('Menyimpan Meja Baru...');
                const labId = labSelector.value;
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                try {
                    const response = await fetch(`/admin/labs/${labId}/desks/batch-create`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ desks: newDesks })
                    });
                    if (!response.ok) {
                        const errorData = await response.json();
                        throw new Error(errorData.message || 'Server response was not ok.');
                    }
                    const data = await response.json();
                    if (data.success && data.created_desks) {
                        desks = [...desks, ...data.created_desks];
                        newDesks = [];
                        hideLoading();
                        showToast('Berhasil!', data.message, 'success');
                        return { success: true, newDesksSaved: data.created_desks.length };
                    } else {
                        throw new Error(data.message || 'Gagal menyimpan meja baru.');
                    }
                } catch (error) {
                    hideLoading();
                    Swal.fire('Gagal', error.message, 'error');
                    return { success: false, error: error.message };
                }
            }

            document.addEventListener('mouseup', async () => {
                // Skenario 1: Selesai 'drag-to-add'
                if (isAddDeskMode && isMouseDown) {
                    isMouseDown = false;
                    if (selectedSlots.size > 0) {
                        selectedSlots.forEach(location => {
                            const isOccupied = [...desks, ...newDesks].some(d => d.location ===
                                location);
                            if (!isOccupied) {
                                newDesks.push({
                                    location: location,
                                    isNew: true,
                                });
                            }
                        });
                        selectedSlots.clear();
                        renderDeskGrid(true);
                    }
                }

                // Skenario 2: Selesai 'drag-to-delete' (new)
                if (isAddDeskMode && isDeletingMode) {
                    isDeletingMode = false;
                    if (slotsToCancel.size > 0) {
                        const count = slotsToCancel.size;
                        slotsToCancel.forEach(location => {
                            cancelNewDesk(location);
                        });
                        slotsToCancel.clear();
                        if (count > 1) renderDeskGrid(true);
                    }
                }

                // Skenario 3: Selesai 'drag-to-delete-EXISTING'
                if (isMainDeleteMode && isMouseDownMainDelete) {
                    isMouseDownMainDelete = false;
                    if (desksToMainDelete.size > 0) {
                        const count = desksToMainDelete.size;
                        const idsToDelete = [...desksToMainDelete];
                        const selectedDesks = desks.filter(d => idsToDelete.includes(String(d.id)));
                        const hasItems = selectedDesks.some(d => d.items && d.items.length > 0);
                        let deleteMode = null;
                        let isConfirmed = false;

                        if (hasItems) {
                            const result = await Swal.fire({
                                title: 'Hapus Meja/Item',
                                text: `Anda telah memilih ${count} meja (beberapa ada isinya). Apa yang ingin Anda lakukan?`,
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#d33',
                                cancelButtonColor: '#6B7280',
                                confirmButtonText: 'Lanjutkan',
                                cancelButtonText: 'Batal',
                                input: 'radio',
                                inputOptions: {
                                    'delete_items_only': 'Hapus ITEM SAJA (Meja tetap ada)',
                                    'delete_all': 'Hapus MEJA DAN SEMUA ITEM (Permanen)'
                                },
                                inputValidator: (value) => !value &&
                                    'Anda harus memilih salah satu opsi!'
                            });
                            if (result.isConfirmed && result.value) {
                                deleteMode = result.value;
                                isConfirmed = true;
                            }
                        } else {
                            const result = await Swal.fire({
                                title: `Hapus ${count} Meja Kosong?`,
                                text: "Meja ini akan dihapus permanen. Lanjutkan?",
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#d33',
                                confirmButtonText: 'Ya, Hapus',
                                cancelButtonText: 'Batal'
                            });
                            if (result.isConfirmed) {
                                deleteMode = 'delete_all';
                                isConfirmed = true;
                            }
                        }

                        if (isConfirmed && deleteMode) {
                            showLoading('Menghapus Meja...');
                            const labId = labSelector.value;
                            const csrfToken = document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content');
                            try {
                                const response = await fetch(
                                    `/admin/labs/${labId}/desks/batch-delete`, {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': csrfToken,
                                            'Accept': 'application/json',
                                        },
                                        body: JSON.stringify({
                                            ids: idsToDelete,
                                            delete_mode: deleteMode
                                        })
                                    });
                                if (!response.ok) {
                                    const errorData = await response.json();
                                    throw new Error(errorData.message ||
                                        'Gagal memproses permintaan.');
                                }
                                const data = await response.json();
                                if (data.success) {
                                    if (deleteMode === 'delete_all') {
                                        desks = desks.filter(d => !idsToDelete.includes(String(
                                            d.id)));
                                    } else {
                                        desks.forEach(desk => {
                                            if (idsToDelete.includes(String(desk.id))) {
                                                desk.items = [];
                                                desk.overall_condition = 'item_kosong';
                                            }
                                        });
                                    }
                                    desksToMainDelete.clear();
                                    renderDeskGrid(true);
                                    hideLoading();
                                    showToast('Berhasil!', data.message, 'success');
                                } else {
                                    throw new Error(data.message || 'Gagal menghapus.');
                                }
                            } catch (error) {
                                hideLoading();
                                Swal.fire('Gagal!', error.message, 'error');
                                desksToMainDelete.clear();
                                renderDeskGrid(true);
                            }
                        } else {
                            desksToMainDelete.clear();
                            renderDeskGrid(true);
                        }
                    }
                }
            });

            function renderDeskGrid(isEditMode = false) {
                const scroller = document.getElementById('grid-scroller');
                const scrollPos = {
                    left: scroller ? scroller.scrollLeft : 0,
                    top: scroller ? scroller.scrollTop : 0
                };
                if (!labSelector.value) return;

                const allDesks = [...desks, ...newDesks];
                const occupiedSlots = new Set(allDesks.map(d => d.location));

                const addDeskBtnText = isAddDeskMode ? 'Selesai Menambah' : '+ Tambah Meja';
                const addDeskBtnClass = isAddDeskMode ? 'bg-blue-600 text-white hover:bg-blue-700' :
                    'bg-gray-100 text-gray-600 hover:bg-gray-200';
                const deleteDeskBtnText = isMainDeleteMode ? 'Selesai Menghapus' : '- Delete';
                const deleteDeskBtnClass = isMainDeleteMode ? 'bg-rose-600 text-white hover:bg-rose-700' :
                    'bg-gray-100 text-gray-600 hover:bg-gray-200';

                let containerHTML =
                    `
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">
                    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-800">Denah Meja Laboratorium</h2>
                            <div class="flex items-center gap-4 text-sm mt-2 flex-wrap">
                                <div class="flex items-center gap-2"><span class="w-4 h-4 bg-emerald-100 border-2 border-emerald-400 rounded"></span><span class="text-gray-600">Bagus</span></div>
                                <div class="flex items-center gap-2"><span class="w-4 h-4 bg-amber-100 border-2 border-amber-400 rounded"></span><span class="text-gray-600">Tidak Lengkap</span></div>
                                <div class="flex items-center gap-2"><span class="w-4 h-4 bg-rose-100 border-2 border-rose-400 rounded"></span><span class="text-gray-600">Rusak</span></div>
                                <div class="flex items-center gap-2"><span class="w-4 h-4 bg-gray-100 border-2 border-gray-400 rounded"></span><span class="text-gray-600">Kosong</span></div>
                                ${isEditMode ? `<div class="flex items-center gap-2"><span class="w-4 h-4 bg-blue-100 border-2 border-dashed border-blue-400 rounded"></span><span class="text-gray-600">Baru</span></div>` : ''}
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                                <div class="flex items-center border border-gray-200 rounded-lg p-1 bg-gray-50">
                                    <button id="zoom-out-btn" class="p-2 rounded-md hover:bg-gray-200 transition-colors" title="Zoom Out"><svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg></button>
                                    <button id="zoom-reset-btn" class="px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-200 rounded-md transition-colors" title="Reset Zoom"><span id="zoom-level-display">100%</span></button>
                                    <button id="zoom-in-btn" class="p-2 rounded-md hover:bg-gray-200 transition-colors" title="Zoom In"><svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg></button>
                                </div>
                            
                            ${isEditMode ? `
                                <button id="add-desk-mode-btn" class="px-4 py-2.5 text-sm font-semibold rounded-lg transition-colors ${addDeskBtnClass}" title="Aktifkan mode tambah meja">${addDeskBtnText}</button>
                                <button id="delete-desk-mode-btn" class="px-4 py-2.5 text-sm font-semibold rounded-lg transition-colors ${deleteDeskBtnClass}" title="Aktifkan mode hapus meja">${deleteDeskBtnText}</button>
                                <button id="add-row-btn" class="px-4 py-2.5 text-sm font-semibold rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors" title="Tambah Baris">+ Baris</button>
                                <button id="add-col-btn" class="px-4 py-2.5 text-sm font-semibold rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors" title="Tambah Kolom">+ Kolom</button>
                            ` : ''}

                            <button id="edit-layout-btn" class="px-4 py-2.5 text-sm font-semibold rounded-lg transition-colors ${isEditMode ? 'bg-indigo-600 text-white hover:bg-indigo-700' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'}">
                                ${isEditMode ? 'Simpan & Keluar' : 'Edit Denah'}
                            </button>
                        </div>
                    </div>
                    
                    <div id="grid-scroller" class="overflow-x-auto pb-4">
                        <div id="desk-grid" class="grid gap-4 border-2 min-w-fit border-slate-500 p-8 ${isEditMode ? 'edit-mode' : ''}" style="grid-template-columns: repeat(${labConfig.maxCols}, minmax(140px, 1fr)); grid-template-rows: repeat(${labConfig.maxRows}, auto);">`;

                allDesks.forEach(desk => {
                    const row = desk.location.charCodeAt(0) - 64;
                    const col = parseInt(desk.location.substring(1));
                    let bgColorClass, iconColor, conditionText, draggableAttr;

                    if (desk.isNew) {
                        bgColorClass =
                            'bg-blue-50 border-blue-400 border-dashed hover:bg-blue-100 cursor-pointer';
                        iconColor = 'text-blue-600';
                        conditionText = 'Baru';
                        draggableAttr = '';
                    } else {
                        draggableAttr = isEditMode ? 'draggable="true"' : '';
                        switch (desk.overall_condition) {
                            case 'item_rusak':
                            case 'component_rusak':
                                bgColorClass = 'bg-rose-50 border-rose-300 hover:bg-rose-100';
                                iconColor = 'text-rose-600';
                                conditionText = 'Rusak';
                                break;
                            case 'item_tidak_lengkap':
                                bgColorClass = 'bg-amber-50 border-amber-300 hover:bg-amber-100';
                                iconColor = 'text-amber-600';
                                conditionText = 'Tidak Lengkap';
                                break;
                            case 'item_kosong':
                                bgColorClass = 'bg-gray-50 border-gray-300 hover:bg-gray-100';
                                iconColor = 'text-gray-500';
                                conditionText = 'Kosong';
                                break;
                            case 'bagus':
                            default:
                                bgColorClass = 'bg-emerald-50 border-emerald-300 hover:bg-emerald-100';
                                iconColor = 'text-emerald-600';
                                conditionText = 'Bagus';
                                break;
                        }
                    }

                    containerHTML += `
                    <div id="desk-${desk.id || desk.location}" data-desk-id="${desk.id || ''}" data-location="${desk.location}" style="grid-area: ${row} / ${col};" class="desk-item group transition-all duration-300 ease-in-out flex flex-col items-center justify-center p-5 border-2 rounded-xl min-h-36 ${bgColorClass} ${desk.isNew ? 'new-desk-item' : ''}" ${draggableAttr} title="${desk.isNew ? 'Klik dua kali untuk batal' : ''}">
                        <div class="text-center pointer-events-none">
                            <div class="mb-2"><svg class="w-8 h-8 mx-auto ${iconColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg></div>
                            <span class="font-bold text-lg text-gray-800 block  select-none">${desk.location}</span>
                            <span class="text-sm text-gray-600 mt-1 inline-block select-none">${conditionText}</span>
                        </div>
                    </div>`;
                });

                for (let r = 1; r <= labConfig.maxRows; r++) {
                    for (let c = 1; c <= labConfig.maxCols; c++) {
                        const location = `${String.fromCharCode(64 + r)}${c}`;
                        if (!occupiedSlots.has(location)) {
                            let slotClass = '';
                            let slotContent = '';
                            if (isEditMode) {
                                slotClass = isAddDeskMode ?
                                    'bg-slate-50 border-dashed border-slate-300 hover:bg-blue-100 hover:border-blue-400 cursor-pointer' :
                                    'bg-slate-100 border-dashed border-slate-200';
                                slotContent = isAddDeskMode ? `<span class="text-slate-400 text-3xl">+</span>` :
                                    `<span class="text-slate-300 font-semibold">${location}</span>`;
                            } else {
                                slotClass = 'empty-placeholder';
                                slotContent = '';
                            }
                            containerHTML +=
                                `<div class="empty-slot transition-all duration-300 ease-in-out flex flex-col items-center justify-center p-5 border-2 rounded-xl min-h-36 ${slotClass}" data-location="${location}" style="grid-area: ${r} / ${c};">${slotContent}</div>`;
                        }
                    }
                }

                containerHTML += '</div></div></div>';
                deskContainer.innerHTML = containerHTML;

                setupCommonListeners();
                if (isEditMode) {
                    setupDragDropListeners();
                    if (isAddDeskMode) {
                        setupAddDeskListeners();
                        setupDeleteNewDeskListeners();
                    }
                    if (isMainDeleteMode) {
                        setupMainDeleteListeners();
                    }
                }
                applyZoom();
                const newScroller = document.getElementById('grid-scroller');
                if (newScroller) {
                    newScroller.scrollLeft = scrollPos.left;
                    newScroller.scrollTop = scrollPos.top;
                }
            }

            function setupMainDeleteListeners() {
                const existingDesks = document.querySelectorAll('.desk-item:not(.new-desk-item)');
                desksToMainDelete.clear();
                existingDesks.forEach(desk => {
                    const innerDiv = desk.querySelector('.text-center');
                    if (!innerDiv) return;
                    if (!desk.dataset.originalHtml) {
                        desk.dataset.originalHtml = innerDiv.innerHTML;
                    }
                    const deskLocation = desk.dataset.location;
                    const deleteFeedbackHtml = `
                    <div classclass="text-center pointer-events-none p-4">
                        <svg class="w-12 h-12 mx-auto text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        <span class="font-bold text-sm text-rose-700 mt-2 block">Hapus ${deskLocation}?</span>
                    </div>`;
                    desk.addEventListener('mousedown', (e) => {
                        e.preventDefault();
                        if (isMouseDown) isMouseDown = false;
                        if (isDeletingMode) isDeletingMode = false;
                        isMouseDownMainDelete = true;
                        const deskId = desk.dataset.deskId;
                        if (!desksToMainDelete.has(deskId)) {
                            desksToMainDelete.add(deskId);
                            desk.classList.add('bg-rose-100', 'border-rose-400', 'border-dashed',
                                'opacity-90', 'scale-95');
                            innerDiv.innerHTML = deleteFeedbackHtml;
                        }
                    });
                    desk.addEventListener('mouseover', () => {
                        if (isMouseDownMainDelete) {
                            const deskId = desk.dataset.deskId;
                            if (!desksToMainDelete.has(deskId)) {
                                desksToMainDelete.add(deskId);
                                desk.classList.add('bg-rose-100', 'border-rose-400',
                                    'border-dashed', 'opacity-90', 'scale-95');
                                innerDiv.innerHTML = deleteFeedbackHtml;
                            }
                        }
                    });
                    desk.addEventListener('mouseleave', () => {
                        if (!isMouseDownMainDelete && !desksToMainDelete.has(desk.dataset.deskId)) {
                            desk.classList.remove('bg-rose-100', 'border-rose-400', 'border-dashed',
                                'opacity-90', 'scale-95');
                            innerDiv.innerHTML = desk.dataset.originalHtml;
                        }
                    });
                });
            }

            function setupCommonListeners() {
                const zoomInBtn = document.getElementById('zoom-in-btn');
                const zoomOutBtn = document.getElementById('zoom-out-btn');
                const zoomResetBtn = document.getElementById('zoom-reset-btn');
                zoomInBtn.addEventListener('click', () => {
                    if (currentZoom < maxZoom) {
                        currentZoom = Math.min(maxZoom, currentZoom + zoomStep);
                        applyZoom();
                    }
                });
                zoomOutBtn.addEventListener('click', () => {
                    if (currentZoom > minZoom) {
                        currentZoom = Math.max(minZoom, currentZoom - zoomStep);
                        applyZoom();
                    }
                });
                zoomResetBtn.addEventListener('click', () => {
                    autoZoomToFit();
                });
                document.getElementById('edit-layout-btn').addEventListener('click', async function() {
                    const deskGrid = document.getElementById('desk-grid');
                    const isCurrentlyEditMode = deskGrid && deskGrid.classList.contains(
                        'edit-mode');
                    if (isCurrentlyEditMode) {
                        const saveResult = await saveNewDesks();
                        if (!saveResult.success) {
                            return;
                        }
                        isAddDeskMode = false;
                        isMainDeleteMode = false;
                        renderDeskGrid(false);
                    } else {
                        renderDeskGrid(true);
                    }
                });
                const addRowBtn = document.getElementById('add-row-btn');
                const addColBtn = document.getElementById('add-col-btn');
                const addDeskModeBtn = document.getElementById('add-desk-mode-btn');
                const deleteDeskModeBtn = document.getElementById('delete-desk-mode-btn');
                if (addRowBtn) {
                    addRowBtn.addEventListener('click', () => {
                        labConfig.maxRows++;
                        renderDeskGrid(true);
                    });
                }
                if (addColBtn) {
                    addColBtn.addEventListener('click', () => {
                        labConfig.maxCols++;
                        renderDeskGrid(true);
                    });
                }
                if (addDeskModeBtn) {
                    addDeskModeBtn.addEventListener('click', async function() {
                        if (isAddDeskMode) {
                            const saveResult = await saveNewDesks();
                            if (saveResult.success) {
                                isAddDeskMode = false;
                                renderDeskGrid(true);
                            }
                        } else {
                            isAddDeskMode = true;
                            if (isAddDeskMode) isMainDeleteMode = false;
                            renderDeskGrid(true);
                        }
                    });
                }
                if (deleteDeskModeBtn) {
                    deleteDeskModeBtn.addEventListener('click', () => {
                        isMainDeleteMode = !isMainDeleteMode;
                        if (isMainDeleteMode) isAddDeskMode = false;
                        renderDeskGrid(true);
                    });
                }
            }

            function setupDragDropListeners() {
                if (isAddDeskMode || isMainDeleteMode) return;
                const draggables = document.querySelectorAll('.desk-item');
                const dropzones = document.querySelectorAll('.empty-slot, .desk-item');
                draggables.forEach(draggable => {
                    draggable.addEventListener('dragstart', () => {
                        draggable.classList.add('dragging');
                    });
                    draggable.addEventListener('dragend', () => {
                        draggable.classList.remove('dragging');
                    });
                });
                dropzones.forEach(zone => {
                    zone.addEventListener('dragover', e => {
                        e.preventDefault();
                        const draggingElement = document.querySelector('.dragging');
                        if (draggingElement !== zone) {
                            zone.classList.add('drag-over');
                        }
                    });
                    zone.addEventListener('dragleave', () => {
                        zone.classList.remove('drag-over');
                    });
                    zone.addEventListener('drop', async e => {
                        e.preventDefault();
                        zone.classList.remove('drag-over');
                        const draggingElement = document.querySelector('.dragging');
                        if (!draggingElement || draggingElement === zone) return;
                        const deskId = draggingElement.dataset.deskId;
                        const newLocation = zone.dataset.location;
                        const labId = labSelector.value;
                        const originalDesks = JSON.parse(JSON.stringify(desks));
                        const movingDesk = desks.find(d => d.id == deskId);
                        const targetDesk = desks.find(d => d.location === newLocation);
                        if (targetDesk) {
                            const movingDeskOldLocation = movingDesk.location;
                            movingDesk.location = newLocation;
                            targetDesk.location = movingDeskOldLocation;
                        } else {
                            movingDesk.location = newLocation;
                        }
                        renderDeskGrid(true);
                        
                        showLoading('Memindahkan Meja...');
                        try {
                            const csrfToken = document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content');
                            const response = await fetch(
                                `/admin/labs/${labId}/desks/update/location/${deskId}`, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': csrfToken,
                                        'Accept': 'application/json',
                                    },
                                    body: JSON.stringify({
                                        location: newLocation
                                    })
                                });
                            if (!response.ok) throw new Error('Server response was not ok.');
                            const data = await response.json();
                            hideLoading();
                            if (data.success) {
                                showToast('Berhasil', data.message ||
                                    `Posisi meja berhasil diperbarui.`, 'success');
                            } else {
                                throw new Error(data.message ||
                                    'Gagal memperbarui posisi meja.');
                            }
                        } catch (error) {
                            hideLoading();
                            console.error('Error updating desk location:', error);
                            desks = originalDesks;
                            renderDeskGrid(true);
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: error.message || 'Terjadi kesalahan jaringan.'
                            });
                        }
                    });
                });
            }

            function setupAddDeskListeners() {
                const emptySlots = document.querySelectorAll('.empty-slot');
                selectedSlots.clear();
                emptySlots.forEach(slot => {
                    slot.addEventListener('mousedown', (e) => {
                        e.preventDefault();
                        if (isDeletingMode) isDeletingMode = false;
                        isMouseDown = true;
                        const location = slot.dataset.location;
                        if (!selectedSlots.has(location)) {
                            selectedSlots.add(location);
                            slot.classList.add('bg-blue-200', 'border-blue-500');
                        }
                    });
                    slot.addEventListener('mouseover', () => {
                        if (isMouseDown) {
                            const location = slot.dataset.location;
                            if (!selectedSlots.has(location)) {
                                selectedSlots.add(location);
                                slot.classList.add('bg-blue-200', 'border-blue-500');
                            }
                        }
                    });
                });
            }

            function setupDeleteNewDeskListeners() {
                const newDeskItems = document.querySelectorAll('.new-desk-item');
                newDeskItems.forEach(desk => {
                    desk.addEventListener('mousedown', (e) => {
                        e.preventDefault();
                        if (isMouseDown) isMouseDown = false;
                        isDeletingMode = true;
                        const location = desk.dataset.location;
                        if (!slotsToCancel.has(location)) {
                            slotsToCancel.add(location);
                            desk.classList.add('bg-rose-200', 'border-rose-500',
                                'opacity-60');
                        }
                    });
                    desk.addEventListener('mouseover', () => {
                        if (isDeletingMode) {
                            const location = desk.dataset.location;
                            if (!slotsToCancel.has(location)) {
                                slotsToCancel.add(location);
                                desk.classList.add('bg-rose-200', 'border-rose-500',
                                    'opacity-60');
                            }
                        }
                    });
                });
            }

            labSelector.addEventListener('change', async function() {
                if (typeof hideLayoutModal === 'function') hideLayoutModal();
                
                showLoading('Memuat Denah Meja...');
                deskContainer.innerHTML =
                    `<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8"><div class="flex items-center justify-center py-12"><div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mr-4"></div><p class="text-gray-600 text-lg">Memuat data meja...</p></div></div>`;
                
                try {
                    const response = await fetch(`/admin/labs/${this.value}/desks`);
                    if (!response.ok) throw new Error('Network response was not ok');
                    desks = await response.json();
                    console.log(desks);
                    
                    let maxRow = 0,
                        maxCol = 0;
                    if (desks.length > 0) {
                        desks.forEach(d => {
                            const row = d.location.charCodeAt(0) - 64;
                            const col = parseInt(d.location.substring(1));
                            if (row > maxRow) maxRow = row;
                            if (col > maxCol) maxCol = col;
                        });
                    }
                    labConfig.maxRows = Math.max(5, maxRow);
                    labConfig.maxCols = Math.max(10, maxCol);
                    renderDeskGrid();
                    autoZoomToFit();
                } catch (error) {
                    console.error('Error fetching desks:', error);
                    deskContainer.innerHTML =
                        `<div class="bg-white rounded-xl shadow-sm border border-red-200 p-8"><div class="text-center py-12"><svg class="w-16 h-16 mx-auto text-red-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg><p class="text-red-600 text-lg font-medium">Terjadi kesalahan saat mengambil data</p><p class="text-gray-500 mt-2">Silakan coba lagi atau hubungi administrator</p></div></div>`;
                } finally {
                    hideLoading();
                }
            });

            function renderAdditionalInfo(spec) {
                if (!spec || !spec.set_values || spec.set_values.length === 0) {
                    return '';
                }
                let infoHtml = '<div class="mt-3 space-y-1">';
                for (const setValue of spec.set_values) {
                    if (!setValue.spec_attributes) continue;
                    const key = setValue.spec_attributes.name;
                    const value = setValue.value;
                    const formattedKey = key.charAt(0).toUpperCase() + key.slice(1).replace(/_/g, ' ');
                    infoHtml +=
                        `<div class="flex items-start gap-2 text-sm"><span class="text-gray-500 min-w-fit">${formattedKey}:</span><span class="text-gray-700 font-medium">${value}</span></div>`;
                }
                infoHtml += '</div>';
                return infoHtml;
            }

            deskContainer.addEventListener('click', function(event) {
                const deskGrid = document.getElementById('desk-grid');
                if (deskGrid && deskGrid.classList.contains('edit-mode')) return;
                const clickedDeskElement = event.target.closest('.desk-item');
                if (!clickedDeskElement) return;

                document.querySelectorAll('.desk-item').forEach(el => el.classList.remove('ring-4',
                    'ring-indigo-400', 'ring-opacity-50'));
                clickedDeskElement.classList.add('ring-4', 'ring-indigo-400', 'ring-opacity-50');

                const deskId = clickedDeskElement.dataset.deskId;
                const selectedDesk = desks.find(d => d.id == deskId);

                if (selectedDesk) {
                    const modalTitle = `Detail Inventaris Meja ${selectedDesk.location}`;
                    let modalBodyHTML = ``;

                    if (selectedDesk.items && selectedDesk.items.length > 0) {
                        modalBodyHTML += '<div class="space-y-4">';
                        selectedDesk.items.forEach((item) => {
                            const itemConditionText = item.condition == 1 ? 'Bagus' : 'Rusak';
                            const itemConditionClass = item.condition == 1 ?
                                'text-emerald-600 bg-emerald-50' :
                                'text-rose-600 bg-rose-50';
                            const itemBorderClass = item.condition == 1 ?
                                'border-emerald-200' :
                                'border-rose-200';
                            modalBodyHTML +=
                                `<div class="bg-gradient-to-br from-gray-50 to-white p-6 rounded-xl border ${itemBorderClass} transition-all duration-200"><div class="flex items-start justify-between mb-3"><div class="flex-1"><h4 class="font-bold text-lg text-gray-800">${item.name}</h4><p class="text-sm text-gray-500 font-mono mt-1">${item.serial_code}</p></div><span class="px-3 py-1 rounded-full text-sm font-semibold ${itemConditionClass}">${itemConditionText}</span></div>`;
                            
                            modalBodyHTML += renderAdditionalInfo(item.spec);

                            if (item.components && item.components.length > 0) {
                                modalBodyHTML +=
                                    `<div class="mt-4 pt-4 border-t border-gray-200"><div class="flex items-center gap-2 mb-3"><svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg><p class="text-sm font-bold text-gray-700">Komponen (${item.components.length})</p></div><div class="space-y-3">`;
                                item.components.forEach(component => {
                                    const compConditionText = component.condition == 1 ?
                                        'Bagus' : 'Rusak';
                                    const compConditionClass = component.condition == 1 ?
                                        'text-emerald-600 bg-emerald-50' :
                                        'text-rose-600 bg-rose-50';
                                    const compBgClass = component.condition == 1 ?
                                        'bg-emerald-50/50' : 'bg-rose-50/50';
                                    modalBodyHTML +=
                                        `<div class="${compBgClass} p-4 rounded-lg border border-gray-200"><div class="flex items-start justify-between mb-2"><div><p class="font-semibold text-gray-800">${component.name}</p><p class="text-xs text-gray-500 font-mono mt-1">${component.serial_code}</p></div><span class="px-2 py-1 rounded-full text-xs font-semibold ${compConditionClass}">${compConditionText}</span></div>`;
                                    modalBodyHTML += renderAdditionalInfo(component.spec);
                                    modalBodyHTML += `</div>`;
                                });
                                modalBodyHTML += '</div></div>';
                            }
                            modalBodyHTML += '</div>';
                        });
                        modalBodyHTML += '</div>';
                    } else {
                        modalBodyHTML +=
                            `<div class="bg-gray-50 rounded-xl p-12 text-center border-2 border-dashed border-gray-300"><svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg><p class="text-gray-500 text-lg">Tidak ada item yang terdaftar untuk meja ini</p></div>`;
                    }

                    if (typeof showLayoutModal === 'function') {
                        showLayoutModal(modalTitle, modalBodyHTML, deskId);
                    }
                }
            });

            deskContainer.addEventListener('dblclick', function(event) {
                const clickedDesk = event.target.closest('.new-desk-item');
                const deskGrid = document.getElementById('desk-grid');
                if (!clickedDesk || !deskGrid || !deskGrid.classList.contains('edit-mode')) {
                    return;
                }
                const location = clickedDesk.dataset.location;
                cancelNewDesk(location);
            });

            // =================================================================
            // Inisialisasi & Logika Modal (Global vs Lokal)
            // =================================================================
            
            const layoutModal = document.getElementById('layout-modal');
            const layoutModalArea = document.getElementById('layout-modal-area');
            const layoutModalTitle = document.getElementById('layout-modal-title');
            const layoutModalBody = document.getElementById('layout-modal-body');
            const layoutModalFooter = layoutModal.querySelector('.rounded-b');
            const overlay = document.getElementById('layout-modal-overlay');
            const closeButtonHeader = document.getElementById('layout-modal-close-button');
            const closeButtonFooter = document.getElementById('layout-modal-footer-close-button');

            // PERBAIKAN: Perbesar Modal Detail (Modal 1)
            if (layoutModalArea) {
                layoutModalArea.classList.remove('max-w-3xl');
                layoutModalArea.classList.add('max-w-5xl');
            }

            const addItemButton = document.createElement('button');
            addItemButton.id = 'layout-modal-add-item-button';
            addItemButton.type = 'button';
            addItemButton.className = 'text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center mr-auto';
            addItemButton.textContent = 'Tambah Item ke Meja Ini';
            layoutModalFooter.prepend(addItemButton);

            window.showLayoutModal = (title, bodyHTML, deskId) => {
                if (layoutModal && layoutModalTitle && layoutModalBody) {
                    layoutModalTitle.textContent = title;
                    layoutModalBody.innerHTML = bodyHTML;
                    addItemButton.dataset.deskId = deskId;
                    layoutModal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                }
            };
            
            window.hideLayoutModal = () => {
                if (layoutModal) {
                    layoutModal.classList.add('hidden');
                    if(document.getElementById('add-item-modal').classList.contains('hidden')) {
                         document.body.style.overflow = '';
                    }
                }
            };
            
            if (closeButtonHeader) closeButtonHeader.addEventListener('click', window.hideLayoutModal);
            if (closeButtonFooter) closeButtonFooter.addEventListener('click', window.hideLayoutModal);
            if (overlay) {
                overlay.addEventListener('click', function(event) {
                    // PERBAIKAN: Cek jika target adalah overlay, BUKAN area modal atau anaknya
                    if (event.target === overlay) { 
                        window.hideLayoutModal();
                    }
                });
            }
            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && layoutModal && !layoutModal.classList.contains('hidden')) {
                    window.hideLayoutModal();
                }
            });

            // =================================================================
            // FUNGSI BARU: Modal Tambah Item
            // =================================================================
            
            const addItemModal = document.getElementById('add-item-modal');
            const addItemModalOverlay = document.getElementById('add-item-modal-overlay'); // Ambil overlay-nya
            const addItemCloseBtn = document.getElementById('add-item-modal-close-button');
            const addItemCancelBtn = document.getElementById('add-item-modal-footer-cancel-button');
            const addItemSaveBtn = document.getElementById('add-item-modal-footer-save-button');
            const itemSelectLoading = document.getElementById('item-select-loading');
            const itemFilterFieldset = document.getElementById('item-filter-fieldset');

            function initializeAddItemModal() {
                tomSelectType = new TomSelect('#filter-type-select', {
                    create: false,
                    onChange: () => loadUnaffiliatedItems()
                });
                
                tomSelectAttr = new TomSelect('#filter-attr-select', {
                    create: false,
                    onChange: (attrId) => {
                        tomSelectValue.clear();
                        tomSelectValue.clearOptions();
                        if (attrId) {
                            const selectedAttr = allSpecAttributes.find(a => a.id == attrId);
                            if (selectedAttr && selectedAttr.spec_values) {
                                const valueOptions = selectedAttr.spec_values.map(v => ({ value: v.id, text: v.value }));
                                tomSelectValue.addOptions(valueOptions);
                                tomSelectValue.enable();
                            }
                        } else {
                            tomSelectValue.disable();
                        }
                        loadUnaffiliatedItems();
                    }
                });
                
                tomSelectValue = new TomSelect('#filter-value-select', {
                    create: false,
                    onChange: () => loadUnaffiliatedItems()
                });

                tomSelectItem = new TomSelect('#item-select-dropdown', {
                    create: false,
                    plugins: ['remove_button'],
                    render: {
                        no_results: function(data, escape) {
                            return '<div class="p-3 text-sm text-gray-500 text-center">Tidak ada item yang ditemukan. Coba ubah filter Anda.</div>';
                        }
                    }
                });

                addItemButton.addEventListener('click', openAddItemModal);
                addItemCloseBtn.addEventListener('click', closeAddItemModal);
                addItemCancelBtn.addEventListener('click', closeAddItemModal);
                addItemSaveBtn.addEventListener('click', submitAddItems);

                // PERBAIKAN 2: Tambahkan listener ke overlay modal KEDUA
                if (addItemModalOverlay) {
                    addItemModalOverlay.addEventListener('click', function(event) {
                        // Cek jika target adalah overlay, BUKAN area modal atau anaknya
                        if (event.target === addItemModalOverlay) { 
                            closeAddItemModal();
                        }
                    });
                }
                document.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape' && addItemModal && !addItemModal.classList.contains('hidden')) {
                        closeAddItemModal();
                    }
                });
            }

            async function openAddItemModal() {
                currentDeskId = this.dataset.deskId;
                if (!currentDeskId) return;
                addItemModal.classList.remove('hidden');

                showLoading('Memuat Filter & Item...');
                await loadFilterOptions();
                await loadUnaffiliatedItems();
                hideLoading();
            }

            function closeAddItemModal() {
                addItemModal.classList.add('hidden');
                tomSelectType.clear();
                tomSelectAttr.clear();
                tomSelectValue.clear();
                tomSelectItem.clear();
                tomSelectValue.disable();
                if(layoutModal.classList.contains('hidden')) {
                    document.body.style.overflow = '';
                }
            }
            
            async function loadFilterOptions() {
                itemFilterFieldset.disabled = true;
                try {
                    const response = await fetch("{{ route('admin.items.filters') }}");
                    const data = await response.json();
                    
                    if (!response.ok || !data.success) {
                        throw new Error(data.message || 'Gagal memuat data filter');
                    }
                    
                    const filterData = data.data; 
                    allSpecAttributes = filterData.specifications || [];
                    
                    tomSelectType.clearOptions();
                    tomSelectType.addOptions(filterData.types.map(t => ({ value: t.id, text: t.name })));
                    
                    tomSelectAttr.clearOptions();
                    tomSelectAttr.addOptions(allSpecAttributes.map(a => ({ value: a.id, text: a.name })));

                } catch (error) {
                    console.error(error);
                    showToast('Error Filter', error.message, 'error');
                } finally {
                    itemFilterFieldset.disabled = false;
                }
            }

            async function loadUnaffiliatedItems() {
                itemSelectLoading.classList.remove('hidden');
                tomSelectItem.wrapper.classList.add('hidden');
                tomSelectItem.clear();
                tomSelectItem.clearOptions();
                itemFilterFieldset.disabled = true; 

                const params = new URLSearchParams();
                if (tomSelectType.getValue()) params.append('type_id', tomSelectType.getValue());
                if (tomSelectValue.getValue()) params.append('spec_value_id', tomSelectValue.getValue());
                
                try {
                    const response = await fetch(`{{ route('admin.items.unaffiliated') }}?${params.toString()}`);
                    const data = await response.json(); 

                    if (!response.ok || !data.success) {
                        throw new Error(data.message || 'Gagal memuat data item');
                    }
                    
                    const items = data.data.items; 
                    tomSelectItem.addOptions(items.map(i => ({ value: i.id, text: `${i.name} (${i.serial_code})` })));

                } catch (error) {
                    console.error(error);
                    showToast('Error Item', error.message, 'error');
                } finally {
                    itemSelectLoading.classList.add('hidden');
                    tomSelectItem.wrapper.classList.remove('hidden');
                    itemFilterFieldset.disabled = false; 
                }
            }

            async function submitAddItems() {
                const itemIds = tomSelectItem.getValue();

                if (!itemIds || itemIds.length === 0) {
                    showToast('Peringatan', 'Anda belum memilih item sama sekali.', 'warning');
                    return;
                }

                const result = await Swal.fire({
                    title: `Tambahkan ${itemIds.length} item?`,
                    text: 'Item yang dipilih akan ditambahkan ke meja ini.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Tambahkan!',
                    cancelButtonText: 'Batal'
                });

                if (!result.isConfirmed) return;

                showLoading('Menambahkan Item...');
                addItemSaveBtn.disabled = true;

                try {
                    const response = await fetch(`/admin/desks/${currentDeskId}/items`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ item_ids: itemIds })
                    });
                    
                    const data = await response.json();
                    if (!response.ok) throw new Error(data.message || 'Gagal menyimpan data');

                    hideLoading();
                    showToast('Berhasil', data.message, 'success');
                    
                    closeAddItemModal();
                    window.hideLayoutModal();
                    labSelector.dispatchEvent(new Event('change')); // Memicu refresh denah lab

                } catch (error) {
                    hideLoading();
                    Swal.fire('Gagal', error.message, 'error');
                } finally {
                    addItemSaveBtn.disabled = false;
                }
            }
            
            // Inisialisasi modal tambah item
            initializeAddItemModal();

        }); // Akhir DOMContentLoaded
    </script>
@endsection