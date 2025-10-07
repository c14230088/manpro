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

        .empty-slot.drag-over, .desk-item.drag-over {
            @apply bg-indigo-100 border-indigo-400 text-indigo-500;
        }

        .desk-item.dragging {
            @apply opacity-50 border-indigo-500 shadow-2xl scale-95;
        }

        #desk-grid:not(.edit-mode) .empty-slot {
            display: none;
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
            let labConfig = {
                maxRows: 5,
                maxCols: 10
            };

            let currentZoom = 1.0;

            function applyZoom() {
                const deskGrid = document.getElementById('desk-grid');
                const zoomLevelDisplay = document.getElementById('zoom-level-display');
                if (deskGrid && zoomLevelDisplay) {
                    deskGrid.style.transform = `scale(${currentZoom})`;
                    zoomLevelDisplay.textContent = `${Math.round(currentZoom * 100)}%`;
                }
            }

            function renderDeskGrid(isEditMode = false) {
                const scroller = document.getElementById('grid-scroller');
                const scrollPos = {
                    left: scroller ? scroller.scrollLeft : 0,
                    top: scroller ? scroller.scrollTop : 0
                };

                const selectedLabId = labSelector.value;
                if (!selectedLabId) return;

                const occupiedSlots = new Set(desks.map(d => d.location));

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
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                             <div class="flex items-center border border-gray-200 rounded-lg p-1 bg-gray-50">
                                 <button id="zoom-out-btn" class="p-2 rounded-md hover:bg-gray-200 transition-colors" title="Zoom Out"><svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg></button>
                                 <button id="zoom-reset-btn" class="px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-200 rounded-md transition-colors" title="Reset Zoom"><span id="zoom-level-display">100%</span></button>
                                 <button id="zoom-in-btn" class="p-2 rounded-md hover:bg-gray-200 transition-colors" title="Zoom In"><svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg></button>
                             </div>
                            
                            ${isEditMode ? `
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

                desks.forEach(desk => {
                    const row = desk.location.charCodeAt(0) - 64;
                    const col = parseInt(desk.location.substring(1));
                    let bgColorClass, iconColor, conditionText;
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
                    containerHTML += `<div id="desk-${desk.id}" data-desk-id="${desk.id}" data-location="${desk.location}" style="grid-area: ${row} / ${col};" class="desk-item group transition-all duration-300 ease-in-out flex flex-col items-center justify-center p-5 border-2 rounded-xl min-h-36 ${bgColorClass}" ${isEditMode ? 'draggable="true"' : ''}>
                            <div class="text-center pointer-events-none">
                                <div class="mb-2"><svg class="w-8 h-8 mx-auto ${iconColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg></div>
                                <span class="font-bold text-lg text-gray-800 block">${desk.location}</span>
                                <span class="text-sm text-gray-600 mt-1 inline-block">${conditionText}</span>
                            </div>
                        </div>`;
                });

                if (isEditMode) {
                    for (let r = 1; r <= labConfig.maxRows; r++) {
                        for (let c = 1; c <= labConfig.maxCols; c++) {
                            const location = `${String.fromCharCode(64 + r)}${c}`;
                            if (!occupiedSlots.has(location)) {
                                containerHTML +=
                                    `<div class="empty-slot transition-all duration-300 ease-in-out flex flex-col items-center justify-center p-5 border-2 rounded-xl min-h-36 bg-slate-50 border-slate-300 hover:bg-slate-100" data-location="${location}" style="grid-area: ${r} / ${c};">${location}</div>`;
                            }
                        }
                    }
                }
                containerHTML += '</div></div></div>';
                deskContainer.innerHTML = containerHTML;

                setupCommonListeners();
                if (isEditMode) setupDragDropListeners();

                applyZoom();

                const newScroller = document.getElementById('grid-scroller');
                if (newScroller) {
                    newScroller.scrollLeft = scrollPos.left;
                    newScroller.scrollTop = scrollPos.top;
                }
            }

            function setupCommonListeners() {
                const zoomInBtn = document.getElementById('zoom-in-btn');
                const zoomOutBtn = document.getElementById('zoom-out-btn');
                const zoomResetBtn = document.getElementById('zoom-reset-btn');

                const maxZoom = 1.5,
                    minZoom = 0.5,
                    zoomStep = 0.1;

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
                    currentZoom = 1.0;
                    applyZoom();
                });

                document.getElementById('edit-layout-btn').addEventListener('click', function() {
                    const deskGrid = document.getElementById('desk-grid');
                    const isCurrentlyEditMode = deskGrid && deskGrid.classList.contains('edit-mode');
                    renderDeskGrid(!isCurrentlyEditMode);
                });

                const addRowBtn = document.getElementById('add-row-btn');
                const addColBtn = document.getElementById('add-col-btn');

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
            }

            function setupDragDropListeners() {
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
                        
                        const originalDesks = JSON.parse(JSON.stringify(desks)); // Deep copy for rollback
                        const movingDesk = desks.find(d => d.id == deskId);
                        const targetDesk = desks.find(d => d.location === newLocation);
                        
                        // --- Optimistic UI Update ---
                        if (targetDesk) { // Swapping positions
                            const movingDeskOldLocation = movingDesk.location;
                            movingDesk.location = newLocation;
                            targetDesk.location = movingDeskOldLocation;
                        } else { // Moving to an empty slot
                             movingDesk.location = newLocation;
                        }
                        renderDeskGrid(true); // Re-render with the new local state
                        
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
                                // [FIX] Tidak perlu `desks = data.desks` jika backend tidak mengirimkannya.
                                // UI sudah benar berkat optimistic update di atas.
                                // Jika backend MENGIRIMKAN data 'desks' yang sudah diupdate, baris di bawah bisa diaktifkan kembali.
                                // desks = data.desks;
                                // renderDeskGrid(true);
                            } else {
                                throw new Error(data.message || 'Gagal memperbarui posisi meja.');
                            }

                        } catch (error) {
                            console.error('Error updating desk location:', error);
                            // Revert UI on failure
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

            labSelector.addEventListener('change', async function() {
                if (typeof hideLayoutModal === 'function') hideLayoutModal();
                deskContainer.innerHTML =
                    `<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8"><div class="flex items-center justify-center py-12"><div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mr-4"></div><p class="text-gray-600 text-lg">Memuat data meja...</p></div></div>`;

                try {
                    const response = await fetch(`/admin/labs/${this.value}/desks`);
                    if (!response.ok) throw new Error('Network response was not ok');
                    desks = await response.json();

                    let maxRow = 0, maxCol = 0;
                    if(desks.length > 0) {
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
                } catch (error) {
                    console.error('Error fetching desks:', error);
                    deskContainer.innerHTML =
                        `<div class="bg-white rounded-xl shadow-sm border border-red-200 p-8"><div class="text-center py-12"><svg class="w-16 h-16 mx-auto text-red-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg><p class="text-red-600 text-lg font-medium">Terjadi kesalahan saat mengambil data</p><p class="text-gray-500 mt-2">Silakan coba lagi atau hubungi administrator</p></div></div>`;
                }
            });

            function renderAdditionalInfo(jsonString) {
                if (!jsonString) return '';
                try {
                    const info = JSON.parse(jsonString);
                    let infoHtml = '<div class="mt-3 space-y-1">';
                    for (const [key, value] of Object.entries(info)) {
                        const formattedKey = key.charAt(0).toUpperCase() + key.slice(1).replace(/_/g, ' ');
                        infoHtml +=
                            `<div class="flex items-start gap-2 text-sm"><span class="text-gray-500 min-w-fit">${formattedKey}:</span><span class="text-gray-700 font-medium">${value}</span></div>`;
                    }
                    infoHtml += '</div>';
                    return infoHtml;
                } catch (e) {
                    return `<p class="text-sm text-gray-600 mt-2">${jsonString}</p>`;
                }
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
                            modalBodyHTML += renderAdditionalInfo(item.additional_information);
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
                                    modalBodyHTML += renderAdditionalInfo(component
                                        .additional_information);
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
        });
    </script>
@endsection