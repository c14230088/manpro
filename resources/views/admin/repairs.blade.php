@extends('layouts.admin')

@section('title', 'Manajemen Perbaikan')

@section('body')
    <div class="flex flex-col md:flex-row w-full py-4 shadow-md items-center justify-center mb-5 px-6 md:px-4">
        <h1 class="text-center text-4xl uppercase font-bold mb-2 md:mb-0">Tracking Perbaikan</h1>
    </div>

    {{-- ========================================================= --}}
    {{-- Filter Form --}}
    {{-- ========================================================= --}}
    <div class="max-w-7xl mx-auto px-6 pb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Filter & Pencarian</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 filter-select">
                {{-- Status Perbaikan --}}
                <div>
                    <label for="filter_repair_status" class="block text-sm font-semibold text-gray-700 mb-2">Status
                        Perbaikan</label>
                    <select id="filter_repair_status" class="filter-input" placeholder="Semua Status...">
                        <option value="">Semua Status</option>
                        <option value="0">Pending</option>
                        <option value="1">In Progress</option>
                        <option value="2">Completed</option>
                        <option value="3">Terbawa (Parent)</option>
                    </select>
                </div>

                {{-- Lab --}}
                <div>
                    <label for="filter_lab_select" class="block text-sm font-semibold text-gray-700 mb-2">Laboratorium</label>
                    <select id="filter_lab_select" class="filter-input" placeholder="Semua Lab...">
                        <option value="">Semua Laboratorium</option>
                        @foreach ($labs as $lab)
                            <option value="{{ $lab->id }}">{{ $lab->name }}</option>
                        @endforeach
                        <option value="null">Belum Terpasang / Gudang</option>
                    </select>
                </div>

                {{-- Tipe --}}
                <div>
                    <label for="filter_type_select" class="block text-sm font-semibold text-gray-700 mb-2">Tipe
                        Barang</label>
                    <select id="filter_type_select" class="filter-input" placeholder="Semua Tipe...">
                        <option value="">Semua Tipe</option>
                        @foreach ($types as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Spesifikasi Attribute --}}
                <div>
                    <label for="filter_spec_attr" class="block text-sm font-semibold text-gray-700 mb-2">Atribut
                        Spesifikasi</label>
                    <select id="filter_spec_attr" class="filter-input" placeholder="Pilih Atribut...">
                        <option value="">Semua Atribut</option>
                        @foreach ($specification as $spec)
                            {{-- Simpan values di data-values untuk di-load JS ke dropdown value --}}
                            <option value="{{ $spec->id }}" data-values='@json($spec->specValues)'>
                                {{ $spec->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Spesifikasi Value --}}
                <div>
                    <label for="filter_spec_val" class="block text-sm font-semibold text-gray-700 mb-2">Nilai
                        Spesifikasi</label>
                    <select id="filter_spec_val" class="filter-input" placeholder="Pilih Atribut Dulu..." disabled>
                        {{-- Diisi oleh JS --}}
                    </select>
                </div>

                {{-- ⬇️ FILTER BARU: PELAPOR ⬇️ --}}
                <div>
                    <label for="filter_reporter" class="block text-sm font-semibold text-gray-700 mb-2">Pelapor</label>
                    <select id="filter_reporter" class="filter-input" placeholder="Semua Pelapor...">
                        <option value="">Semua Pelapor</option>
                        {{-- Ambil unique reporter dari collection repairs --}}
                        @foreach ($repairs->unique('reported_by') as $r)
                            @if ($r->reporter)
                                <option value="{{ $r->reporter->name }}">{{ $r->reporter->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                {{-- ⬆️ ---------------------- ⬆️ --}}

            </div>

            {{-- Tombol Aksi Filter --}}
            <div class="flex flex-col sm:flex-row justify-end gap-3 mt-6">
                {{-- Search Text --}}
                <div class="relative flex w-full">
                    <div class="relative flex w-full flex-wrap items-stretch">
                        <input id="datatable-search-input" type="search"
                            class="relative m-0 -mr-0.5 block w-[1px] min-w-0 flex-auto rounded-l border border-solid border-neutral-300 bg-transparent bg-clip-padding px-3 py-[0.6rem] text-base font-normal leading-[1.6] text-neutral-700 outline-none transition duration-200 ease-in-out focus:z-[3] focus:border-primary focus:text-neutral-700 focus:shadow-[inset_0_0_0_1px_rgb(59,113,202)] focus:outline-none"
                            placeholder="Cari nama barang, pelapor, atau masalah..." aria-label="Search" />
                        <button
                            class="relative z-[2] flex items-center rounded-r bg-primary px-6 py-2.5 text-xs font-medium uppercase leading-tight text-white shadow-md transition duration-150 ease-in-out hover:bg-primary-700 hover:shadow-lg focus:bg-primary-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-primary-800 active:shadow-lg"
                            type="button">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                class="h-5 w-5">
                                <path fill-rule="evenodd"
                                    d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Tombol Reset --}}
                <button id="reset-filter-btn" type="button"
                    class="px-6 py-2 bg-gray-200 w-full sm:w-auto text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-300 transition-colors">
                    Reset Filter
                </button>
            </div>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- Tabel Utama --}}
    {{-- ========================================================= --}}
    <div class="max-w-7xl mx-auto px-6 pb-12 space-y-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Daftar Laporan Perbaikan</h2>
            </div>
            <div class="p-6">
                <div id="repairs-datatable-wrapper" class="overflow-x-auto">
                    <table id="repairs-table" class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    data-te-datatable-sortable="true">
                                    Barang & Tipe</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    data-te-datatable-sortable="true">
                                    Lokasi / Lab</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Keluhan</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Pelapor</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                {{-- ⬇️ KOLOM BARU ⬇️ --}}
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    data-te-datatable-sortable="true">
                                    Waktu Pengerjaan</th>
                                {{-- ⬆️ ----------- ⬆️ --}}
                                <th scope="col"
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($repairs as $repair)
                                @php
                                    $item = $repair->itemable;
                                    $isItem = $repair->itemable_type === 'App\Models\Items';
                                    $itemName = $item->name ?? 'Unknown';
                                    $itemCode = $item->serial_code ?? '-';
                                    $itemTypeId = $item->type_id ?? '';
                                    $itemTypeName = $item->type->name ?? 'N/A';

                                    // Logic Lokasi & Lab ID untuk Filter
                                    $labId = 'null';
                                    $locationName = 'Gudang / Belum Terpasang';

                                    if ($isItem) {
                                        if ($item->desk) {
                                            $labId = $item->desk->lab_id;
                                            $locationName =
                                                $item->desk->lab->name . ' - Meja ' . $item->desk->location;
                                        }
                                    } else {
                                        // Component
                                        if ($item->item && $item->item->desk) {
                                            $labId = $item->item->desk->lab_id;
                                            $locationName =
                                                $item->item->desk->lab->name .
                                                ' - Meja ' .
                                                $item->item->desk->location;
                                        }
                                    }

                                    // Logic Spesifikasi untuk Filter (Array of Spec IDs)
                                    $specValueIds = [];
                                    if ($item->specSetValues) {
                                        $specValueIds = $item->specSetValues->pluck('id')->toArray();
                                    }
                                    $specJson = json_encode($specValueIds);

                                    // Badge Status
                                    $statusLabel = '';
                                    $statusClass = '';
                                    switch ($repair->status) {
                                        case 0:
                                            $statusLabel = 'Pending';
                                            $statusClass = 'bg-gray-100 text-gray-800';
                                            break;
                                        case 1:
                                            $statusLabel = 'In Progress';
                                            $statusClass = 'bg-blue-100 text-blue-800';
                                            break;
                                        case 2:
                                            $statusLabel = 'Completed';
                                            $statusClass = 'bg-green-100 text-green-800';
                                            break;
                                        case 3:
                                            $statusLabel = 'Terbawa';
                                            $statusClass = 'bg-purple-100 text-purple-800';
                                            break;
                                    }
                                @endphp

                                {{-- ⬇️ Tambahkan data-reporter untuk filter JS ⬇️ --}}
                                <tr class="repair-row" data-repair-status="{{ $repair->status }}"
                                    data-lab-id="{{ $labId }}" data-type-id="{{ $itemTypeId }}"
                                    data-specs="{{ $specJson }}"
                                    data-reporter="{{ $repair->reporter->name ?? '' }}"
                                    data-search-text="{{ strtolower($itemName . ' ' . $itemCode . ' ' . ($repair->reporter->name ?? '') . ' ' . $repair->issue_description) }}">

                                    {{-- Kolom 1: Barang --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-900">{{ $itemName }}</div>
                                        <div class="text-xs text-gray-500 font-mono">{{ $itemCode }}</div>
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-50 text-indigo-700 mt-1">
                                            {{ $isItem ? 'Item' : 'Comp' }}: {{ $itemTypeName }}
                                        </span>
                                    </td>

                                    {{-- Kolom 2: Lokasi --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-700">{{ $locationName }}</div>
                                    </td>

                                    {{-- Kolom 3: Masalah --}}
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-600 max-w-xs whitespace-normal break-words">
                                            {{ Str::limit($repair->issue_description, 50) }}
                                        </div>
                                    </td>

                                    {{-- Kolom 4: Pelapor --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $repair->reporter->name ?? 'System' }}</div>
                                        <div class="text-xs text-gray-500">Lapor:
                                            {{ $repair->reported_at ? \Carbon\Carbon::parse($repair->reported_at)->format('d M Y, H:i') : '-' }}
                                        </div>
                                    </td>

                                    {{-- Kolom 5: Status --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>

                                    {{-- ⬇️ Kolom 6: Waktu Pengerjaan (BARU) ⬇️ --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col gap-1">
                                            <div class="text-xs text-gray-600">
                                                <span class="font-semibold text-gray-500">Mulai:</span>
                                                {{ $repair->started_at ? \Carbon\Carbon::parse($repair->started_at)->format('d/m/y H:i') : '-' }}
                                            </div>
                                            <div class="text-xs text-gray-600">
                                                <span class="font-semibold text-gray-500">Selesai:</span>
                                                {{ $repair->completed_at ? \Carbon\Carbon::parse($repair->completed_at)->format('d/m/y H:i') : '-' }}
                                            </div>
                                        </div>
                                    </td>
                                    {{-- ⬆️ ------------------------------ ⬆️ --}}

                                    {{-- Kolom 7: Aksi --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <button type="button"
                                            class="btn-open-update-status px-4 py-2 bg-indigo-600 text-white hover:bg-indigo-700 text-xs font-semibold rounded-md shadow-sm focus:outline-none transition-colors"
                                            data-repair-id="{{ $repair->id }}"
                                            data-current-status="{{ $repair->status }}"
                                            data-item-name="{{ $itemName }}"
                                            data-issue="{{ $repair->issue_description }}">
                                            Update Status
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr id="no-data-row">
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                        Belum ada data perbaikan.
                                    </td>
                                </tr>
                            @endforelse
                            {{-- Row Khusus Jika Filter Tidak Menemukan Hasil --}}
                            <tr id="filter-no-result" class="hidden">
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    Tidak ada data yang cocok dengan filter Anda.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- MODAL: Update Status Perbaikan --}}
    {{-- ========================================================= --}}
    <div id="update-status-modal" class="hidden" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 z-[3000]"></div>
        <div id="update-status-overlay" class="fixed inset-0 flex items-center justify-center p-4 z-[3001]">
            <div class="relative bg-white rounded-lg shadow-xl w-full max-w-md flex flex-col">

                <div class="flex justify-between items-center p-4 border-b border-gray-200 rounded-t bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Update Status Perbaikan</h3>
                    <button id="close-modal-btn" type="button" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="p-6 space-y-4">
                    <div class="bg-blue-50 p-3 rounded-lg border border-blue-100">
                        <p class="text-sm text-blue-800 font-semibold mb-1">Item:</p>
                        <p id="modal-item-name" class="text-sm text-gray-700 font-bold">Nama Item Disini</p>
                        <p class="text-sm text-blue-800 font-semibold mt-2 mb-1">Masalah:</p>
                        <p id="modal-issue" class="text-sm text-gray-600 italic">Deskripsi masalah...</p>
                    </div>

                    <form id="update-status-form">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" id="modal-repair-id" name="repair_id">

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Status Baru</label>
                            <select id="modal-status-select" name="status" class="w-full">
                                <option value="0">Pending (Menunggu)</option>
                                <option value="1">In Progress (Sedang Dikerjakan)</option>
                                <option value="2">Completed (Selesai)</option>
                            </select>
                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" id="cancel-modal-btn"
                                class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">Batal</button>
                            <button type="submit" id="submit-status-btn"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700">Simpan
                                Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <style>
        .ts-control {
            @apply block w-full px-4 py-3 text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white;
            padding-top: 0.6rem;
            padding-bottom: 0.6rem;
        }

        .ts-dropdown {
            z-index: 3005;
        }
    </style>

    <script>
        // Set sidebar active link
        try {
            document.getElementById('repairs').classList.add('bg-slate-100');
        } catch (e) {}

        // Global Variables for Filters
        let tomSelects = {};
        let tomSelectStatusModal;
        let dataTableInstance;

        document.addEventListener('DOMContentLoaded', function() {

            // --- 1. INISIALISASI FILTER TOMSELECT ---
            tomSelects.status = new TomSelect('#filter_repair_status', { plugins: ['clear_button'] });
            tomSelects.lab = new TomSelect('#filter_lab_select', { plugins: ['clear_button'] });
            tomSelects.type = new TomSelect('#filter_type_select', { plugins: ['clear_button'] });
            tomSelects.reporter = new TomSelect('#filter_reporter', { plugins: ['clear_button'] }); // ⬅️ Filter Reporter

            // Logic Filter Spesifikasi Bertingkat
            tomSelects.specVal = new TomSelect('#filter_spec_val', { plugins: ['clear_button'] });
            tomSelects.specVal.disable();

            tomSelects.specAttr = new TomSelect('#filter_spec_attr', {
                plugins: ['clear_button'],
                onChange: (value) => {
                    if (!tomSelects.specVal) return;
                    tomSelects.specVal.clear();
                    tomSelects.specVal.clearOptions();

                    if (!value) {
                        tomSelects.specVal.disable();
                    } else {
                        const selectedOption = tomSelects.specAttr.getOption(value);
                        if (!selectedOption || !selectedOption.dataset.values) {
                            tomSelects.specVal.disable();
                            return;
                        }
                        try {
                            const values = JSON.parse(selectedOption.dataset.values);
                            tomSelects.specVal.addOptions(values.map(v => ({
                                value: v.id,
                                text: v.value
                            })));
                            tomSelects.specVal.enable();
                        } catch (e) {
                            console.error("Error parsing spec values", e);
                        }
                    }
                    applyClientSideFilter();
                }
            });

            // --- 2. INITIALIZE DATATABLES ---
             try {
                const tableEl = document.getElementById('repairs-datatable');
                dataTableInstance = new te.Datatable(tableEl, {
                    hover: true,
                    fixedHeader: true,
                    pagination: true,
                    entries: 10,
                    entriesOptions: [5, 10, 25, 50],
                    noFoundMessage: 'Belum ada data perbaikan.',
                });

                document.getElementById('datatable-search-input').addEventListener('input', (e) => {
                    // Gunakan fitur search global DataTables untuk pencarian teks
                    dataTableInstance.search(e.target.value);
                    // Panggil filter custom juga agar tetap sinkron (jika diperlukan kombinasi)
                    applyClientSideFilter(); 
                });

            } catch (e) {
                console.warn("DataTable init failed", e);
            }


            // --- 3. LOGIKA FILTER CLIENT-SIDE ---
            // Fungsi ini akan menyembunyikan/menampilkan baris tabel berdasarkan kriteria filter
            function applyClientSideFilter() {
                const statusVal = tomSelects.status.getValue();
                const labVal = tomSelects.lab.getValue();
                const typeVal = tomSelects.type.getValue();
                const reporterVal = tomSelects.reporter.getValue(); // ⬅️ Value Reporter
                
                // Filter Spesifikasi
                const specAttrVal = tomSelects.specAttr.getValue();
                const specValVal = tomSelects.specVal.getValue();
                
                // Search Text (optional jika ingin double check, tapi Datatable sudah handle search text global)
                // const searchVal = document.getElementById('datatable-search-input').value.toLowerCase();

                const rows = document.querySelectorAll('.repair-row');
                let visibleCount = 0;

                rows.forEach(row => {
                    // Ambil data attributes
                    const rowStatus = row.dataset.repairStatus;
                    const rowLab = row.dataset.labId;
                    const rowType = row.dataset.typeId;
                    const rowReporter = row.dataset.reporter; // ⬅️ Data Reporter
                    const rowSpecs = JSON.parse(row.dataset.specs || '[]');

                    // Cek Status
                    let matchStatus = (statusVal === "" || rowStatus === statusVal);
                    // Cek Lab
                    let matchLab = (labVal === "" || rowLab === labVal);
                    // Cek Tipe
                    let matchType = (typeVal === "" || rowType === typeVal);
                    // Cek Reporter
                    let matchReporter = (reporterVal === "" || rowReporter === reporterVal); // ⬅️ Logic Reporter

                    // Cek Spesifikasi Value
                    let matchSpec = true;
                    if (specValVal !== "") {
                        // SpecVal ID harus ada di dalam array rowSpecs
                        // rowSpecs adalah array integer [1, 5, 9], specValVal adalah string "5"
                        matchSpec = rowSpecs.includes(parseInt(specValVal));
                    }

                    // Final Decision (Gabungkan semua kondisi)
                    // Note: Search text dihandle otomatis oleh te.Datatable.search(), 
                    // tapi custom filter row hiding perlu manual class toggling.
                    // Masalahnya: Datatable mungkin me-render ulang row. 
                    // Untuk te.Datatable versi free, manipulasi DOM row manual kadang bentrok dengan pagination.
                    // Namun untuk filter sederhana ini biasanya aman jika pagination di-refresh atau dataset kecil.
                    
                    if (matchStatus && matchLab && matchType && matchReporter && matchSpec) {
                        row.style.display = ''; // Tampilkan
                        visibleCount++;
                    } else {
                        row.style.display = 'none'; // Sembunyikan
                    }
                });

                // Tampilkan pesan "Tidak ada data" jika hasil filter kosong
                const noResultRow = document.getElementById('filter-no-result');
                if (visibleCount === 0 && rows.length > 0) {
                    noResultRow.classList.remove('hidden');
                } else {
                    noResultRow.classList.add('hidden');
                }
            }

            // Event Listeners untuk Filter Change
            tomSelects.status.on('change', applyClientSideFilter);
            tomSelects.lab.on('change', applyClientSideFilter);
            tomSelects.type.on('change', applyClientSideFilter);
            tomSelects.reporter.on('change', applyClientSideFilter); // ⬅️ Listener Reporter
            tomSelects.specVal.on('change', applyClientSideFilter);

            // Reset Button
            document.getElementById('reset-filter-btn').addEventListener('click', () => {
                tomSelects.status.clear();
                tomSelects.lab.clear();
                tomSelects.type.clear();
                tomSelects.reporter.clear(); // ⬅️ Clear Reporter
                tomSelects.specAttr.clear();
                document.getElementById('datatable-search-input').value = '';
                
                // Reset search DataTable
                if(dataTableInstance) dataTableInstance.search('');
                
                applyClientSideFilter();
            });

            // --- 4. MODAL UPDATE STATUS LOGIC ---
            tomSelectStatusModal = new TomSelect('#modal-status-select', {
                create: false,
                plugins: ['dropdown_input'],
            });

            const modal = document.getElementById('update-status-modal');
            const closeModalBtns = [
                document.getElementById('close-modal-btn'),
                document.getElementById('cancel-modal-btn'),
                document.getElementById('update-status-overlay')
            ];

            document.querySelectorAll('.btn-open-update-status').forEach(btn => {
                btn.addEventListener('click', function() {
                    const repairId = this.dataset.repairId;
                    const currentStatus = this.dataset.currentStatus;
                    const itemName = this.dataset.itemName;
                    const issue = this.dataset.issue;

                    document.getElementById('modal-repair-id').value = repairId;
                    document.getElementById('modal-item-name').textContent = itemName;
                    document.getElementById('modal-issue').textContent = issue;
                    tomSelectStatusModal.setValue(currentStatus);
                    modal.classList.remove('hidden');
                });
            });

            const closeModal = () => {
                modal.classList.add('hidden');
            };
            closeModalBtns.forEach(el => {
                if (el) el.addEventListener('click', (e) => {
                    if (e.target === el) closeModal();
                });
            });

            const form = document.getElementById('update-status-form');
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                const btn = document.getElementById('submit-status-btn');
                const originalText = btn.textContent;

                btn.disabled = true;
                btn.textContent = 'Menyimpan...';

                const repairId = document.getElementById('modal-repair-id').value;
                const status = tomSelectStatusModal.getValue();
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute(
                    'content');

                try {
                    const response = await fetch(`/admin/repairs/${repairId}/status`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            status: status
                        })
                    });

                    const data = await response.json();

                    if (!response.ok) throw new Error(data.message || 'Gagal update status');

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Status perbaikan berhasil diperbarui',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });

                } catch (error) {
                    Swal.fire('Error', error.message, 'error');
                    btn.disabled = false;
                    btn.textContent = originalText;
                }
            });
        });
    </script>
@endsection