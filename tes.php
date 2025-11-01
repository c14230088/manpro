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
@endsection

@section('script')
{{-- CSS untuk mode edit dan drag-and-drop --}}
<style>
    #desk-grid {
        transition: transform 0.3s ease-in-out;
        transform-origin: top left;
    }

    .empty-slot {
        @apply bg-slate-100 border-2 border-dashed border-slate-300 rounded-xl min-h-36 flex items-center justify-center text-slate-500 font-semibold text-lg;
        transition: background-color 0.2s, border-color 0.2s;
    }

    .empty-slot.drag-over,
    .desk-item.drag-over {
        @apply bg-indigo-100 border-indigo-400 text-indigo-500;
    }

    .desk-item.dragging {
        @apply opacity-50 border-indigo-500 shadow-2xl scale-95;
    }

    #desk-grid:not(.edit-mode) .empty-slot {
        /* 1. Sembunyikan, tapi JANGAN ubah layout (tetap makan tempat) */
        visibility: hidden; 
        
        /* 2. Hapus style card-nya agar benar-benar tidak terlihat */
        @apply bg-transparent border-transparent;    
    }

    #desk-grid.edit-mode .desk-item {
        cursor: grab;
    }

    #desk-grid.edit-mode .desk-item:active {
        cursor: grabbing;
    }
</style>

<script>
    document.getElementById('labs').classList.add('bg-slate-100');

    document.addEventListener('DOMContentLoaded', function() {
        const labSelector = document.getElementById('lab-selector');
        const deskContainer = document.getElementById('desk-grid-container');
        let desks = [];
        let newDesks = [];
        let isAddDeskMode = false;
        let labConfig = {
            maxRows: 5,
            maxCols: 10
        };

        let currentZoom = 1.0;

        const maxZoom = 1.5,
        minZoom = 0.5,
        zoomStep = 0.1;

        let isMouseDown = false;       // Untuk 'drag-to-add'
        let selectedSlots = new Set(); // Untuk 'drag-to-add'
        
        let isDeletingMode = false;    // Untuk 'drag-to-delete'
        let slotsToCancel = new Set(); // Untuk 'drag-to-delete'

        //Hapus meja lama
        let isMainDeleteMode = false;      // Mode Hapus Meja Lama (di-toggle oleh tombol)
        let isMouseDownMainDelete = false; // Status mousedown KHUSUS untuk hapus meja lama
        let desksToMainDelete = new Set(); // Menyimpan ID meja lama yang akan dihapus

        function applyZoom() {
            const deskGrid = document.getElementById('desk-grid');
            const zoomLevelDisplay = document.getElementById('zoom-level-display');
            if (deskGrid && zoomLevelDisplay) {
                deskGrid.style.transform = `scale(${currentZoom})`;
                zoomLevelDisplay.textContent = `${Math.round(currentZoom * 100)}%`;
            }
        }

        function autoZoomToFit() {
            const scroller = document.getElementById('grid-scroller');
            const grid = document.getElementById('desk-grid');

            if (!scroller || !grid) return;

            // 1. Dapatkan ukuran viewport (area yang terlihat)
            const scrollerWidth = scroller.clientWidth;
            const scrollerHeight = scroller.clientHeight;
            
            // 2. Dapatkan ukuran konten (seluruh grid)
            const gridWidth = grid.scrollWidth;
            const gridHeight = grid.scrollHeight;

            // 3. Hitung rasio zoom horizontal & vertikal
            const zoomX = scrollerWidth / gridWidth;
            const zoomY = scrollerHeight / gridHeight;

            // 4. Ambil rasio terkecil & beri sedikit padding
            let newZoom = Math.min(zoomX, zoomY) * 0.95; // 95% agar ada spasi

            // 5. Pastikan zoom tidak melebihi batas min/max
            newZoom = Math.max(minZoom, Math.min(maxZoom, newZoom));

            currentZoom = newZoom;
            applyZoom();
        }


        function cancelNewDesk(location) {
            // Hanya berjalan jika sedang dalam mode tambah meja
            if (!isAddDeskMode) return;

            // Cari index meja baru berdasarkan lokasinya
            const index = newDesks.findIndex(desk => desk.location === location);

            // Jika ditemukan, hapus dari array
            if (index > -1) {
                newDesks.splice(index, 1);
                renderDeskGrid(true); // Render ulang grid untuk menghilangkan meja
            }
        }

        async function saveNewDesks() {
            // 1. Jika tidak ada meja baru, tidak perlu simpan
            if (newDesks.length === 0) {
                return { success: true, newDesksSaved: 0 };
            }

            const labId = labSelector.value;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            try {
                // 2. Kirim request 'batch-create'
                const response = await fetch(`/admin/labs/${labId}/desks/batch-create`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        desks: newDesks
                    })
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Server response was not ok.');
                }

                const data = await response.json();

                if (data.success && data.created_desks) {
                    // 3. Jika berhasil, perbarui array 'desks' utama
                    desks = [...desks, ...data.created_desks];
                    // Kosongkan array 'newDesks'
                    newDesks = [];
                    
                    if (typeof showToast === 'function') {
                        showToast('Berhasil!', data.message, 'success');
                    }
                    return { success: true, newDesksSaved: data.created_desks.length };
                } else {
                    throw new Error(data.message || 'Gagal menyimpan meja baru.');
                }

            } catch (error) {
                console.error('Error creating desks:', error);
                alert('Gagal menyimpan meja baru: ' + error.message);
                return { success: false, error: error.message }; // Kembalikan info gagal
            }
        }


            document.addEventListener('mouseup', async () => {
        
            // ===== Skenario 1: Selesai 'drag-to-add' =====
            if (isAddDeskMode && isMouseDown) {
                isMouseDown = false; // Selalu reset state

                if (selectedSlots.size > 0) {
                    selectedSlots.forEach(location => {
                        const isOccupied = [...desks, ...newDesks].some(d => d.location === location);
                        if (!isOccupied) {
                            newDesks.push({
                                location: location,
                                isNew: true,
                            });
                        }
                    });
                    selectedSlots.clear();
                    renderDeskGrid(true); // Render ulang
                }
            }

            // ===== Skenario 2: Selesai 'drag-to-delete' (FITUR BARU) =====
            if (isAddDeskMode && isDeletingMode) {
                isDeletingMode = false; // Selalu reset state

                if (slotsToCancel.size > 0) {
                    const count = slotsToCancel.size; // Tetap simpan count

                    // LANGSUNG HAPUS TANPA KONFIRMASI
                    slotsToCancel.forEach(location => {
                        cancelNewDesk(location); // Panggil fungsi hapus
                    });
                    slotsToCancel.clear();

                    // Render ulang sekali saja jika menghapus banyak
                    if (count > 1) renderDeskGrid(true);
                }
            }
            
            // ===== SKENARIO 3: Selesai 'drag-to-delete-EXISTING' =====
            if (isMainDeleteMode && isMouseDownMainDelete) {
                isMouseDownMainDelete = false; // Reset state

                if (desksToMainDelete.size > 0) {
                    const count = desksToMainDelete.size;
                    const idsToDelete = [...desksToMainDelete]; 
                    
                    // 1. CEK APAKAH MEJA YANG DIPILIH ADA ISINYA
                    const selectedDesks = desks.filter(d => idsToDelete.includes(String(d.id)));
                    const hasItems = selectedDesks.some(d => d.items && d.items.length > 0);

                    let deleteMode = null;      // 'delete_items_only', 'delete_all'
                    let isConfirmed = false;    // Status konfirmasi

                    // 2. TAMPILKAN POPUP BERDASARKAN KONDISI
                    if (hasItems) {
                        // --- SKENARIO A: MEJA ADA ISI ---
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
                            inputValidator: (value) => !value && 'Anda harus memilih salah satu opsi!'
                        });

                        if (result.isConfirmed && result.value) {
                            deleteMode = result.value;
                            isConfirmed = true;
                        }

                    } else {
                        // --- SKENARIO B: SEMUA MEJA KOSONG ---
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
                            deleteMode = 'delete_all'; // Satu-satunya aksi yang mungkin
                            isConfirmed = true;
                        }
                    }
                    
                    // 3. JIKA USER KONFIRMASI (BAIK A ATAU B)
                    if (isConfirmed && deleteMode) {
                        const labId = labSelector.value;
                        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                        try {
                            // 4. KIRIM REQUEST (Controller tidak perlu tahu bedanya)
                            const response = await fetch(`/admin/labs/${labId}/desks/batch-delete`, {
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
                                throw new Error(errorData.message || 'Gagal memproses permintaan.');
                            }

                            const data = await response.json();
                            
                            // 5. UPDATE DATA LOKAL (JS)
                            if (data.success) {
                                if (deleteMode === 'delete_all') {
                                    desks = desks.filter(d => !idsToDelete.includes(String(d.id)));
                                } else {
                                    desks.forEach(desk => {
                                        if (idsToDelete.includes(String(desk.id))) {
                                            desk.items = [];
                                            desk.overall_condition = 'tidak_lengkap';
                                        }
                                    });
                                }
                                
                                desksToMainDelete.clear();
                                renderDeskGrid(true); // Render ulang
                                
                                if (typeof showToast === 'function') {
                                    showToast('Berhasil!', data.message, 'success');
                                }
                            } else {
                                throw new Error(data.message || 'Gagal menghapus.');
                            }

                        } catch (error) {
                            console.error('Error batch action:', error);
                            Swal.fire('Gagal!', error.message, 'error');
                            desksToMainDelete.clear();
                            renderDeskGrid(true); 
                        }
                        
                    } else {
                        // User klik 'Batal'
                        desksToMainDelete.clear();
                        renderDeskGrid(true); // Render ulang untuk mengembalikan UI asli
                    }
                }
            }

        }
        
    
        ); 

        function renderDeskGrid(isEditMode = false) {
            const scroller = document.getElementById('grid-scroller');
            const scrollPos = {
                left: scroller ? scroller.scrollLeft : 0,
                top: scroller ? scroller.scrollTop : 0
            };

            const selectedLabId = labSelector.value;
            if (!selectedLabId) return;

            const allDesks = [...desks, ...newDesks];
            const occupiedSlots = new Set(allDesks.map(d => d.location));

            const addDeskBtnText = isAddDeskMode ? 'Selesai Menambah' : '+ Tambah Meja';
            const addDeskBtnClass = isAddDeskMode ? 'bg-blue-600 text-white hover:bg-blue-700' : 'bg-gray-100 text-gray-600 hover:bg-gray-200';

            const deleteDeskBtnText = isMainDeleteMode ? 'Selesai Menghapus' : '- Delete';
            const deleteDeskBtnClass = isMainDeleteMode ? 'bg-rose-600 text-white hover:bg-rose-700' : 'bg-gray-100 text-gray-600 hover:bg-gray-200';

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
                <div id="desk-grid" class="grid gap-4 min-w-max ${isEditMode ? 'edit-mode' : ''}" style="grid-template-columns: repeat(${labConfig.maxCols}, minmax(140px, 1fr)); grid-template-rows: repeat(${labConfig.maxRows}, auto);">`;

            allDesks.forEach(desk => {
                const row = desk.location.charCodeAt(0) - 64;
                const col = parseInt(desk.location.substring(1));
                let bgColorClass, iconColor, conditionText, draggableAttr, dblClickAttr;

                if (desk.isNew) {
                    bgColorClass = 'bg-blue-50 border-blue-400 border-dashed hover:bg-blue-100 cursor-pointer'; // Tambah cursor-pointer
                    iconColor = 'text-blue-600';
                    conditionText = 'Baru';
                    draggableAttr = '';
                    // dblClickAttr = `ondblclick="cancelNewDesk('${desk.location}')" title="Klik dua kali untuk batal"`; // MODIFIKASI: Tambah event double click
                } else {
                    dblClickAttr = '';
                    draggableAttr = isEditMode ? 'draggable="true"' : '';
                    switch (desk.overall_condition) {
                        case 'rusak':
                            bgColorClass = 'bg-rose-50 border-rose-300 hover:bg-rose-100';
                            iconColor = 'text-rose-600';
                            conditionText = 'Rusak';
                            break;
                        case 'tidak_lengkap':
                            bgColorClass = 'bg-amber-50 border-amber-300 hover:bg-amber-100';
                            iconColor = 'text-amber-600';
                            conditionText = 'Tidak Lengkap';
                            break;
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
                    
                    // Cek apakah slot ini TIDAK ADA di daftar meja
                    if (!occupiedSlots.has(location)) {
                        
                        // Jika kosong, kita buat slotnya
                        let slotClass = '';
                        let slotContent = '';

                        if (isEditMode) {
                            // --- Di Mode Edit, slot terlihat ---
                            slotClass = isAddDeskMode ? 'bg-slate-50 border-dashed border-slate-300 hover:bg-blue-100 hover:border-blue-400 cursor-pointer' : 'bg-slate-100 border-dashed border-slate-200';
                            slotContent = isAddDeskMode ? `<span class="text-slate-400 text-3xl">+</span>` : `<span class="text-slate-300 font-semibold">${location}</span>`;
                        } else {
                            // --- Di Mode View, slot tidak terlihat (tapi ada) ---
                            slotClass = 'empty-placeholder'; // Akan disembunyikan oleh CSS
                            slotContent = ''; // Kosong
                        }

                            containerHTML += `<div class="empty-slot transition-all duration-300 ease-in-out flex flex-col items-center justify-center p-5 border-2 rounded-xl min-h-36 ${slotClass}" data-location="${location}" style="grid-area: ${r} / ${c};">${slotContent}</div>`;
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
                setupMainDeleteListeners(); // Untuk hapus meja 'lama'
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
            // Hanya pilih meja yang SUDAH ADA (bukan .new-desk-item)
            const existingDesks = document.querySelectorAll('.desk-item:not(.new-desk-item)');

            desksToMainDelete.clear(); // Kosongkan setiap render

            existingDesks.forEach(desk => {
                const innerDiv = desk.querySelector('.text-center');
                if (!innerDiv) return; // Lewati jika tidak ada konten

                // Simpan HTML asli untuk jaga-jaga (meski renderGrid akan menimpanya)
                if (!desk.dataset.originalHtml) {
                    desk.dataset.originalHtml = innerDiv.innerHTML;
                }

                const deskLocation = desk.dataset.location;

                // Konten UI Feedback "Hapus"
                const deleteFeedbackHtml = `
                    <div classclass="text-center pointer-events-none p-4">
                        <svg class="w-12 h-12 mx-auto text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        <span class="font-bold text-sm text-rose-700 mt-2 block">Hapus ${deskLocation}?</span>
                    </div>
                `;

                desk.addEventListener('mousedown', (e) => {
                    e.preventDefault();
                    if (isMouseDown) isMouseDown = false;
                    if (isDeletingMode) isDeletingMode = false;

                    isMouseDownMainDelete = true;
                    const deskId = desk.dataset.deskId;

                    if (!desksToMainDelete.has(deskId)) {
                        desksToMainDelete.add(deskId);
                        // Terapkan UI Feedback
                        desk.classList.add('bg-rose-100', 'border-rose-400', 'border-dashed', 'opacity-90', 'scale-95');
                        innerDiv.innerHTML = deleteFeedbackHtml;
                    }
                });

                desk.addEventListener('mouseover', () => {
                    if (isMouseDownMainDelete) {
                        const deskId = desk.dataset.deskId;
                        if (!desksToMainDelete.has(deskId)) {
                            desksToMainDelete.add(deskId);
                            // Terapkan UI Feedback
                            desk.classList.add('bg-rose-100', 'border-rose-400', 'border-dashed', 'opacity-90', 'scale-95');
                            innerDiv.innerHTML = deleteFeedbackHtml;
                        }
                    }
                });

                // Saat mouse keluar dari area, kembalikan UI (jika tidak dibatalkan)
                desk.addEventListener('mouseleave', () => {
                    if (!isMouseDownMainDelete && !desksToMainDelete.has(desk.dataset.deskId)) {
                        desk.classList.remove('bg-rose-100', 'border-rose-400', 'border-dashed', 'opacity-90', 'scale-95');
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
                const isCurrentlyEditMode = deskGrid && deskGrid.classList.contains('edit-mode');

                if (isCurrentlyEditMode) {
                    // --- User klik "Simpan & Keluar" ---
                    
                    // 1. Tampilkan loading
                    this.disabled = true;
                    this.innerHTML = `<div class="animate-spin rounded-full h-5 w-5 border-b-2 border-white mx-auto"></div>`;

                    // 2. Panggil fungsi simpan yang baru
                    const saveResult = await saveNewDesks();

                    if (!saveResult.success) {
                        // 3. Jika gagal simpan, batalkan & jangan keluar
                        this.disabled = false;
                        this.textContent = 'Simpan & Keluar';
                        return; // Jangan keluar dari mode edit
                    }
                    
                    // 4. Jika berhasil, keluar dari semua mode
                    isAddDeskMode = false;
                    isMainDeleteMode = false;
                    renderDeskGrid(false); // Render dalam mode non-edit

                } else {
                    // --- User klik "Edit Denah" ---
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
                addDeskModeBtn.addEventListener('click', async function() { // <-- Tambah async
                    if (isAddDeskMode) {
                        // --- User klik "Selesai Menambah" ---
                        
                        // 1. Tampilkan loading
                        this.disabled = true;
                        this.innerHTML = `<div class="animate-spin rounded-full h-5 w-5 border-b-2 border-white mx-auto"></div>`;

                        // 2. Panggil fungsi simpan
                        const saveResult = await saveNewDesks();
                        
                        if (saveResult.success) {
                            // 3. Jika berhasil, keluar dari mode tambah (tapi tetap di mode edit)
                            isAddDeskMode = false;
                            renderDeskGrid(true); // Render ulang
                        } else {
                            // 4. Jika gagal, kembalikan tombol & tetap di mode tambah
                            this.disabled = false;
                            this.textContent = 'Selesai Menambah';
                        }

                    } else {
                        // --- User klik "+ Tambah Meja" ---
                        isAddDeskMode = true;
                          if (isAddDeskMode) isMainDeleteMode = false;
                        renderDeskGrid(true);
                    }
                });
            }

            if (deleteDeskModeBtn) {
                deleteDeskModeBtn.addEventListener('click', () => {
                    isMainDeleteMode = !isMainDeleteMode;
                    if (isMainDeleteMode) isAddDeskMode = false; // <-- Tambahkan ini
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
                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        const response = await fetch(`/admin/labs/${labId}/desks/update/location/${deskId}`, {
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

                        if (data.success) {
                            showToast('Berhasil', data.message || `Posisi meja berhasil diperbarui.`, 'success');
                            
                        } else {
                            throw new Error(data.message || 'Gagal memperbarui posisi meja.');
                        }

                    } catch (error) {
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
                    if (isDeletingMode) isDeletingMode = false; // Reset mode lain
                    
                    isMouseDown = true; // Set state GLOBAL
                    const location = slot.dataset.location;
                    if (!selectedSlots.has(location)) {
                        selectedSlots.add(location);
                        slot.classList.add('bg-blue-200', 'border-blue-500');
                    }
                });

                slot.addEventListener('mouseover', () => {
                    if (isMouseDown) { // Cek state GLOBAL
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
                    // Pastikan kita tidak menjalankan mode 'add' dan 'delete' bersamaan
                    if (isMouseDown) isMouseDown = false;
                    
                    isDeletingMode = true; // Aktifkan mode hapus
                    const location = desk.dataset.location;

                    if (!slotsToCancel.has(location)) {
                        slotsToCancel.add(location);
                        // Beri feedback visual (warna merah)
                        desk.classList.add('bg-rose-200', 'border-rose-500', 'opacity-60');
                    }
                });

                desk.addEventListener('mouseover', () => {
                    if (isDeletingMode) { // Hanya jika mousedown dimulai dari item 'baru'
                        const location = desk.dataset.location;
                        if (!slotsToCancel.has(location)) {
                            slotsToCancel.add(location);
                            desk.classList.add('bg-rose-200', 'border-rose-500', 'opacity-60');
                        }
                    }
                });
            });
        }

        labSelector.addEventListener('change', async function() {
            if (typeof hideLayoutModal === 'function') hideLayoutModal();
            deskContainer.innerHTML =
                `<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8"><div class="flex items-center justify-center py-12"><div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mr-4"></div><p class="text-gray-600 text-lg">Memuat data meja...</p></div></div>`;

            try {
                const response = await fetch(`/admin/labs/${this.value}/desks`);
                if (!response.ok) throw new Error('Network response was not ok');
                desks = await response.json();

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
                            'text-emerald-600 bg-emerald-50' : 'text-rose-600 bg-rose-50';
                        const itemBorderClass = item.condition == 1 ? 'border-emerald-200' :
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
                    showLayoutModal(modalTitle, modalBodyHTML);
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

    });
</script>
@endsection