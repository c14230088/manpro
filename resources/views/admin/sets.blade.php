@extends('layouts.admin')

@section('title', 'Manajemen Sets')

@section('body')
    <div class="flex flex-col md:flex-row w-full py-4 shadow-md items-center justify-center mb-5 px-6 md:px-4">
        <h1 class="text-center text-4xl uppercase font-bold mb-2 md:mb-0">Sets Management</h1>
    </div>

    {{-- Filter Section --}}
    <div class="max-w-7xl mx-auto px-6 pb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Filter Kontrol</h2>
            <div class="filter-select">
                <label for="filter_location" class="block text-sm font-semibold text-gray-700 mb-2">Lokasi</label>
                <select id="filter_location" placeholder="Semua Lokasi...">
                    <option value="unattached">Belum Terpasang</option>
                    @foreach ($labs as $lab)
                        <option value="{{ $lab->id }}">{{ $lab->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <div class="relative flex w-full">
                    <div class="relative flex w-full flex-wrap items-stretch">
                        <input id="datatable-search-input" type="search"
                            class="relative m-0 -mr-0.5 block w-[1px] min-w-0 flex-auto rounded-l border border-solid border-neutral-300 bg-transparent bg-clip-padding px-3 py-[0.6rem] text-base font-normal leading-[1.6] text-neutral-700 outline-none transition duration-200 ease-in-out focus:z-[3] focus:border-primary focus:text-neutral-700 focus:shadow-[inset_0_0_0_1px_rgb(59,113,202)] focus:outline-none"
                            placeholder="Cari nama Set, Item didalamnya, atau Lokasinya..." aria-label="Search" />
                        <button
                            class="relative z-[2] flex items-center rounded-r bg-primary px-6 py-2.5 text-xs font-medium uppercase leading-tight text-white shadow-md transition duration-150 ease-in-out hover:bg-primary-700 hover:shadow-lg focus:bg-primary-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-primary-800 active:shadow-lg"
                            type="button">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5">
                                <path fill-rule="evenodd"
                                    d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
                <button id="reset-filter-btn"
                    class="px-6 py-2 bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-300 transition-colors">
                    Reset Filter
                </button>
            </div>
        </div>
    </div>

    {{-- Table Section --}}
    <div class="max-w-7xl mx-auto px-6 pb-12">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">

            {{-- Container Wrapper untuk Scroll & Sticky Header --}}
            <div id="datatable-container">
                <table class="min-w-full divide-y divide-gray-200" id="sets-datatable">
                    <thead class="bg-gray-50">
                        <tr>
                            {{-- PENTING: Hapus data-te-datatable-sortable="true" agar JS tidak bentrok --}}
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nama Set
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Items
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Lokasi
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($sets as $set)
                            @php
                                $allAttached = $set->items->every(fn($i) => $i->desk_id !== null);
                                $locations = $set->items
                                    ->map(
                                        fn($i) => $i->desk
                                            ? $i->desk->lab->name . ' - ' . $i->desk->location
                                            : 'Belum Terpasang',
                                    )
                                    ->unique();
                            @endphp
                            <tr
                                data-location="{{ $allAttached ? $set->items->first()->desk->lab_id ?? '' : 'unattached' }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">{{ $set->name }}</div>
                                    @if ($set->note)
                                        <div class="text-xs text-gray-600">{{ $set->note }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-600">
                                        @foreach ($set->items as $item)
                                            <div>{{ $item->type->name ?? 'N/A' }}: {{ $item->name }}</div>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if ($allAttached)
                                        @foreach ($locations as $loc)
                                            <div class="text-sm text-gray-600">{{ $loc }}</div>
                                        @endforeach
                                    @else
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-700">Belum
                                            Terpasang</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <button type="button"
                                        class="btn-open-action-set px-4 py-2 bg-indigo-600 text-gray-100 hover:text-gray-200 text-xs font-semibold rounded-md hover:bg-indigo-800 focus:outline-none"
                                        data-set-id="{{ $set->id }}" data-set-name="{{ $set->name }}"
                                        data-all-attached="{{ $allAttached ? '1' : '0' }}">
                                        Aksi
                                    </button>
                                </td>
                            </tr>
                        @empty
                            {{-- Baris kosong ini opsional karena TE punya noFoundMessage, tapi bagus untuk server-side render awal --}}
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500">Tidak ada data set.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MODALS SECTION (Tidak Berubah) --}}
    <div id="action-modal" class="hidden" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 z-[2000]"></div>
        <div id="action-modal-overlay" class="fixed inset-0 flex items-center justify-center p-4 z-[2001]">
            <div class="relative bg-white rounded-lg shadow-xl w-full max-w-lg flex flex-col max-h-[90vh]">
                <div class="flex justify-between items-center p-4 border-b border-gray-200 rounded-t">
                    <h3 id="action-modal-title" class="text-xl font-semibold text-gray-900">Aksi untuk Set</h3>
                    <button id="action-modal-close-btn" type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 14 14">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                    </button>
                </div>
                <div class="p-6 grid grid-cols-2 gap-4">
                    <button id="action-btn-details"
                        class="p-6 bg-green-500 hover:bg-green-600 text-white rounded-lg flex flex-col items-center justify-center transition-all shadow-lg">
                        <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                            </path>
                        </svg>
                        <span class="font-semibold">Lihat Detail</span>
                    </button>
                    <button id="action-btn-attach"
                        class="p-6 bg-blue-500 hover:bg-blue-600 text-white rounded-lg flex flex-col items-center justify-center transition-all shadow-lg">
                        <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z">
                            </path>
                        </svg>
                        <span class="font-semibold">Pasang ke Meja</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="detail-modal" class="hidden" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 z-[3000]"></div>
        <div id="detail-modal-overlay" class="fixed inset-0 flex items-center justify-center p-4 z-[3001]">
            <div class="relative bg-white rounded-lg shadow-xl w-full max-w-4xl flex flex-col max-h-[90vh]">
                <div class="flex justify-between items-center p-4 border-b border-gray-200 rounded-t">
                    <h3 class="text-xl font-semibold text-gray-900">Detail Set: <span id="detail-set-name"
                            class="text-indigo-600"></span></h3>
                    <button id="detail-modal-close-btn" type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 14 14">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                    </button>
                </div>
                <div id="detail-modal-content" class="p-6 space-y-4 overflow-y-auto">
                    <div class="flex items-center justify-center py-12">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="attach-desk-modal" class="hidden" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 z-[4000]"></div>
        <div id="attach-desk-modal-overlay" class="fixed inset-0 flex items-center justify-center p-4 z-[4001]">
            <div class="relative bg-white rounded-lg shadow-xl w-full max-w-6xl flex flex-col max-h-[90vh]">
                <div class="flex justify-between items-center p-4 border-b border-gray-200 rounded-t">
                    <h3 class="text-xl font-semibold text-gray-900">Pasang Set: <span id="attach-set-name"
                            class="text-indigo-600"></span></h3>
                    <button id="attach-desk-modal-close-btn" type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 14 14">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                    </button>
                </div>
                <div class="p-6 space-y-4 overflow-y-auto">
                    <div class="bg-gray-100 rounded-lg p-4">
                        <label for="lab-selector-modal" class="block text-sm font-semibold text-gray-700 mb-2">Pilih
                            Laboratorium</label>
                        <select id="lab-selector-modal" placeholder="Pilih Lab..."></select>
                    </div>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <p class="text-sm text-yellow-800"><strong>Instruksi:</strong> Pilih 1 meja untuk memasang semua 4
                            item dalam set ini.</p>
                        <div id="selected-desk-display" class="mt-2 text-xs text-gray-600">Belum ada meja dipilih.</div>
                    </div>
                    <div id="desk-grid-container-modal">
                        <div class="text-center py-12 text-gray-500">Pilih lab untuk melihat denah.</div>
                    </div>
                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <button id="attach-confirm-btn"
                            class="px-6 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition-colors disabled:bg-gray-400"
                            disabled>Pasang Set ke 1 Meja</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <style>
        .filter-select .ts-control {
            @apply block w-full px-4 py-3 text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white;
            padding-top: 0.6rem;
            padding-bottom: 0.6rem;
        }

        /* FIX: CSS MANUAL UNTUK STICKY HEADER & SCROLL */
        #datatable-container {
            position: relative;
            height: 65vh;
            /* Tinggi tabel diset di sini, bisa 600px atau pake vh */
            overflow-y: auto;
            display: block;
        }

        #sets-datatable thead th {
            position: sticky;
            top: 0;
            z-index: 20;
            /* Lebih tinggi dari konten */
            background-color: #f9fafb;
            /* bg-gray-50 */
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }
    </style>

    <script>
        document.getElementById('sets').classList.add('bg-slate-100');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        let currentSetId = null;
        let currentSetName = null;
        let currentAllAttached = false;
        let tomSelectLabModal;
        let modalLabDesks = [];
        let selectedDeskLocations = [];
        let setsDatatableInstance = null; // Global variable

        // --- Helper Functions (Loading, Toast) ---
        function showLoading(title = 'Loading...', text = 'silakan tunggu...') {
            Swal.fire({
                title,
                text,
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
        }

        function hideLoading() {
            Swal.close();
        }
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        function showToast(title, message, icon = 'success') {
            Toast.fire({
                icon,
                title,
                text: message
            });
        }

        // --- Modal & Map Functions ---
        function checkModalState() {
            const isAnyOpen = !document.getElementById('action-modal').classList.contains('hidden') ||
                !document.getElementById('detail-modal').classList.contains('hidden') ||
                !document.getElementById('attach-desk-modal').classList.contains('hidden');
            document.body.style.overflow = isAnyOpen ? 'hidden' : '';
        }

        function initializeActionModal() {
            const modal = document.getElementById('action-modal');
            const closeBtn = document.getElementById('action-modal-close-btn');
            const overlay = document.getElementById('action-modal-overlay');
            const closeModal = () => {
                modal.classList.add('hidden');
                checkModalState();
            };
            closeBtn.addEventListener('click', closeModal);
            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) closeModal();
            });
            document.getElementById('action-btn-details').addEventListener('click', openDetailModal);
            document.getElementById('action-btn-attach').addEventListener('click', openAttachDeskModal);
        }

        async function openDetailModal() {
            const modal = document.getElementById('detail-modal');
            const nameEl = document.getElementById('detail-set-name');
            const contentEl = document.getElementById('detail-modal-content');
            nameEl.textContent = currentSetName;
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            contentEl.innerHTML =
                '<div class="flex items-center justify-center py-12"><div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div></div>';
            try {
                const response = await fetch(`/admin/sets/${currentSetId}/details`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                if (!response.ok) throw new Error('Gagal mengambil data.');
                const data = await response.json();
                populateDetailModal(data);
            } catch (error) {
                contentEl.innerHTML =
                    `<div class="text-center py-12 text-red-500"><h3 class="font-semibold text-lg">Gagal Memuat Data</h3><p class="text-sm">${error.message}</p></div>`;
            }
        }

        function populateDetailModal(data) {
            const contentEl = document.getElementById('detail-modal-content');
            let html =
                `<div class="space-y-6"><div><h4 class="font-semibold text-gray-800 mb-2">Nama Set:</h4><p class="text-gray-600">${data.name}</p></div>`;
            if (data.note) html +=
                `<div><h4 class="font-semibold text-gray-800 mb-2">Catatan:</h4><p class="text-gray-600">${data.note}</p></div>`;
            if (data.created_at) html +=
                `<div><h4 class="font-semibold text-gray-800 mb-2">Waktu Dicatat:</h4><p class="text-gray-600">${new Date(data.created_at).toLocaleDateString('id-ID')}</p></div>`;
            html +=
                `<div class="border-t pt-4"><h4 class="font-semibold text-gray-800 mb-4">Items dalam Set (${data.items.length}):</h4>`;
            data.items.forEach((item, idx) => {
                const condBadge = item.condition ?
                    '<span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800">Bagus</span>' :
                    '<span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-800">Rusak</span>';
                const location = item.desk ? `${item.desk.lab.name} - Meja ${item.desk.location}` :
                    'Belum Terpasang';
                html +=
                    `<div class="bg-gray-50 rounded-lg p-4 mb-3"><div class="flex justify-between items-start mb-2"><h5 class="font-semibold text-gray-900">${idx+1}. ${item.name}</h5>${condBadge}</div><div class="text-sm text-gray-600 space-y-1"><div><strong>Serial:</strong> ${item.serial_code}</div><div><strong>Tipe:</strong> ${item.type ? item.type.name : 'N/A'}</div><div><strong>Lokasi:</strong> ${location}</div>`;
                if (item.spec_set_values?.length > 0) html +=
                    `<div><strong>Spesifikasi:</strong> ${item.spec_set_values.map(s => `${s.spec_attributes.name}: ${s.value}`).join(', ')}</div>`;
                if (item.components?.length > 0) {
                    html +=
                        `<div class="mt-2 pl-4 border-l-2 border-purple-300"><strong>Komponen (${item.components.length}):</strong><ul class="list-disc pl-4 mt-1">`;
                    item.components.forEach(comp => {
                        html += `<li>${comp.name} (${comp.type ? comp.type.name : 'N/A'})</li>`;
                    });
                    html += `</ul></div>`;
                }
                html += `</div></div>`;
            });
            html += `</div></div>`;
            contentEl.innerHTML = html;
        }

        function initializeDetailModal() {
            const modal = document.getElementById('detail-modal');
            const closeBtn = document.getElementById('detail-modal-close-btn');
            const overlay = document.getElementById('detail-modal-overlay');
            const closeModal = () => {
                modal.classList.add('hidden');
                checkModalState();
            };
            closeBtn.addEventListener('click', closeModal);
            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) closeModal();
            });
        }

        function initializeAttachDeskModal() {
            const modal = document.getElementById('attach-desk-modal');
            const closeBtn = document.getElementById('attach-desk-modal-close-btn');
            const overlay = document.getElementById('attach-desk-modal-overlay');
            const labSelectorEl = document.getElementById('lab-selector-modal');
            const confirmBtn = document.getElementById('attach-confirm-btn');
            tomSelectLabModal = new TomSelect(labSelectorEl, {
                create: false,
                placeholder: 'Pilih Lab...',
                onChange: (labId) => {
                    if (labId) {
                        selectedDeskLocations = [];
                        updateSelectedDesksDisplay();
                        fetchDeskMapForModal(labId);
                    }
                }
            });
            const closeModal = () => {
                modal.classList.add('hidden');
                if (tomSelectLabModal) tomSelectLabModal.clear();
                selectedDeskLocations = [];
                updateSelectedDesksDisplay();
                document.getElementById('desk-grid-container-modal').innerHTML =
                    '<div class="text-center py-12 text-gray-500">Pilih lab untuk melihat denah.</div>';
                checkModalState();
            };
            closeBtn.addEventListener('click', closeModal);
            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) closeModal();
            });
            confirmBtn.addEventListener('click', confirmAttachSetToDesks);
        }

        async function openAttachDeskModal() {
            if (currentAllAttached) {
                Swal.fire('Info', 'Set ini sudah terpasang semua di meja.', 'info');
                return;
            }
            const modal = document.getElementById('attach-desk-modal');
            document.getElementById('attach-set-name').textContent = currentSetName;
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            selectedDeskLocations = [];
            updateSelectedDesksDisplay();
            showLoading('Memuat Daftar Lab...');
            try {
                const response = await fetch("{{ route('admin.labs.list') }}");
                if (!response.ok) throw new Error('Gagal memuat daftar lab.');
                const labs = await response.json();
                if (tomSelectLabModal) {
                    tomSelectLabModal.clearOptions();
                    tomSelectLabModal.addOptions(labs.map(lab => ({
                        value: lab.id,
                        text: lab.name
                    })));
                    hideLoading();
                    tomSelectLabModal.open();
                }
            } catch (error) {
                hideLoading();
                Swal.fire('Error', error.message, 'error');
                modal.classList.add('hidden');
                checkModalState();
            }
        }

        async function fetchDeskMapForModal(labId) {
            const container = document.getElementById('desk-grid-container-modal');
            container.innerHTML =
                '<div class="flex items-center justify-center py-12"><div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div></div>';
            try {
                const response = await fetch(`/admin/labs/${labId}/desks`);
                if (!response.ok) throw new Error('Gagal memuat denah meja.');
                modalLabDesks = await response.json();
                let maxRow = 5,
                    maxCol = 10;
                if (modalLabDesks.length > 0) {
                    modalLabDesks.forEach(d => {
                        const row = d.location.charCodeAt(0) - 64;
                        const col = parseInt(d.location.substring(1));
                        if (row > maxRow) maxRow = row;
                        if (col > maxCol) maxCol = col;
                    });
                }
                renderDeskGridModal(modalLabDesks, maxRow, maxCol);
            } catch (error) {
                container.innerHTML = `<div class="text-center py-12 text-red-500">${error.message}</div>`;
                showToast('Gagal Memuat Denah', error.message, 'error');
            }
        }

        function renderDeskGridModal(desks, maxRows, maxCols) {
            const container = document.getElementById('desk-grid-container-modal');
            let html =
                `<div class="overflow-x-auto pb-4"><div id="desk-grid-modal" class="grid gap-4 border-2 min-w-fit border-slate-300 p-8" style="grid-template-columns: repeat(${maxCols}, minmax(140px, 1fr)); grid-template-rows: repeat(${maxRows}, auto);">`;
            const occupiedSlots = new Set(desks.map(d => d.location));
            const requiredTypes = ['Monitor', 'Mouse', 'Keyboard', 'CPU'];
            desks.forEach(desk => {
                const row = desk.location.charCodeAt(0) - 64;
                const col = parseInt(desk.location.substring(1));
                const isSelected = selectedDeskLocations.includes(desk.location);
                const hasRequiredType = desk.items && desk.items.some(item => item.type && requiredTypes.includes(
                    item.type.name));
                let bgColorClass = hasRequiredType ? 'bg-red-50 border-red-300' : (isSelected ?
                    'bg-indigo-200 border-indigo-500' : 'bg-gray-50 border-gray-300 hover:bg-gray-100');
                let iconColor = hasRequiredType ? 'text-red-800' : (isSelected ? 'text-indigo-700' :
                    'text-gray-500');
                let cursorClass = hasRequiredType ? 'cursor-not-allowed' : 'cursor-pointer';
                html +=
                    `<div data-desk-location="${desk.location}" data-has-required="${hasRequiredType}" style="grid-area: ${row} / ${col};" class="desk-item-modal group transition-all duration-200 flex flex-col items-center justify-center p-5 border-2 rounded-xl min-h-36 ${bgColorClass} ${cursorClass}"><div class="text-center pointer-events-none"><div class="mb-2"><svg class="w-8 h-8 mx-auto ${iconColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg></div><span class="font-bold text-lg text-gray-800 block select-none">${desk.location}</span>${hasRequiredType ? '<span class="text-xs text-red-800 mt-1 inline-block select-none font-semibold">Sudah Terisi</span>' : (isSelected ? '<span class="text-sm text-indigo-600 mt-1 inline-block select-none font-semibold">Dipilih</span>' : '')}</div></div>`;
            });
            for (let r = 1; r <= maxRows; r++) {
                for (let c = 1; c <= maxCols; c++) {
                    if (!occupiedSlots.has(`${String.fromCharCode(64 + r)}${c}`)) html +=
                        `<div class="empty-slot-modal" style="grid-area: ${r} / ${c}; visibility: hidden;"></div>`;
                }
            }
            html += '</div></div>';
            container.innerHTML = html;
            document.querySelectorAll('.desk-item-modal').forEach(deskEl => {
                deskEl.addEventListener('click', () => {
                    if (deskEl.dataset.hasRequired === 'true') {
                        Swal.fire('Meja Tidak Tersedia',
                            'Meja ini sudah memiliki Monitor, Mouse, Keyboard, atau CPU. Pilih meja lain.',
                            'warning');
                        return;
                    }
                    selectedDeskLocations = [deskEl.dataset.deskLocation];
                    updateSelectedDesksDisplay();
                    renderDeskGridModal(modalLabDesks, maxRows, maxCols);
                });
            });
        }

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
                const response = await fetch(`/admin/sets/${currentSetId}/attach-desks`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        lab_id: tomSelectLabModal.getValue(),
                        desk_location: selectedDeskLocations[0]
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

        // --- MAIN INIT ---
        document.addEventListener('DOMContentLoaded', function() {
            initializeActionModal();
            initializeDetailModal();
            initializeAttachDeskModal();

            // Init Filter
            const locationFilter = new TomSelect('#filter_location', {
                plugins: ['clear_button'],
                placeholder: 'Semua Lokasi...',
                allowEmptyOption: true
            });
            locationFilter.clear();

            // --- INIT DATATABLE ---
            const tableElement = document.getElementById('sets-datatable');

            try {
                // Konfigurasi ini sekarang PASTI jalan karena atribut data-te sudah dihapus dari HTML
                setsDatatableInstance = new te.Datatable(tableElement, {
                    bordered: true,
                    hover: true,
                    striped: true,
                    loading: false,
                    search: true, // Search bar akan muncul
                    pagination: true, // Pagination akan muncul
                    entries: 10,
                    entriesOptions: [5, 10, 25, 50, 100],
                    fixedHeader: false, // Kita handle via CSS (lebih stabil)
                    noFoundMessage: 'Tidak ada data set yang ditemukan.',
                });
                console.log("Datatable initialized successfully");
            } catch (e) {
                console.error('Gagal inisialisasi datatable:', e);
            }

            // Logic Filter
            locationFilter.on('change', (value) => {
                if (!setsDatatableInstance) return;
                if (!value) {
                    setsDatatableInstance.search('');
                    return;
                }
                let keyword = value === 'unattached' ? 'Belum Terpasang' : (locationFilter.getOption(value)
                    ?.textContent.trim() || '');
                setsDatatableInstance.search(keyword);
            });

            document.getElementById('reset-filter-btn').addEventListener('click', () => {
                document.getElementById('datatable-search-input').value = '';
                if (setsDatatableInstance) setsDatatableInstance.search('');
                locationFilter.clear();
            });

            document.getElementById('datatable-search-input').addEventListener('input', (e) => {
                setsDatatableInstance.search(e.target.value);
            });

            // Logic Actions (Event Delegation)
            // Menggunakan container wrapper untuk event listener agar tetap jalan saat pagination berubah
            document.getElementById('datatable-container').addEventListener('click', function(e) {
                const actionButton = e.target.closest('.btn-open-action-set');
                if (!actionButton) return;
                currentSetId = actionButton.dataset.setId;
                currentSetName = actionButton.dataset.setName;
                currentAllAttached = actionButton.dataset.allAttached === '1';
                document.getElementById('action-modal-title').innerHTML =
                    `Aksi untuk Set: <span class="font-bold text-indigo-600">${currentSetName}</span>`;
                const attachBtn = document.getElementById('action-btn-attach');
                if (currentAllAttached) {
                    attachBtn.classList.add('opacity-50', 'cursor-not-allowed');
                    attachBtn.disabled = true;
                } else {
                    attachBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    attachBtn.disabled = false;
                }
                document.getElementById('action-modal').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            });
        });
    </script>
@endsection
