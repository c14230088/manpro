@extends('layouts.admin')

@section('title', 'History Booking Items and Components')

@section('style')
<style>
    :root {
        --petra-blue: #1a237e;
        --petra-yellow: #ffca28;
        --petra-gray: #f1f5f9;
    }

    .stat-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid #e2e8f0;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        border-color: var(--petra-blue);
    }

    .filter-section {
        background: linear-gradient(to right, #ffffff, #f8fafc);
    }

    /* Table Styling */
    .custom-table th {
        background-color: #f8fafc;
        color: #475569;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
    }

    .custom-table tr:hover td {
        background-color: #f8fafc;
    }
</style>
@endsection

@section('body')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    <div class="flex flex-col md:flex-row w-full py-4 shadow-md items-center justify-center mb-5 px-6 md:px-4">
        <h1 class="text-center text-4xl uppercase font-bold mb-2 md:mb-0">History Booking Items & Components</h1>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="stat-card bg-white p-6 rounded-xl shadow-sm relative overflow-hidden">
            <div class="flex justify-between items-start z-10 relative">
                <div>
                    <p class="text-gray-500 text-sm font-medium mb-1">Total Peminjaman</p>
                    <h3 id="stat-total-bookings" class="text-3xl font-bold text-petra-blue">
                        {{ number_format($totalBookings ?? $histories->total()) }}
                    </h3>
                    <p class="text-xs text-gray-400 mt-2 font-medium">
                        Sesuai filter terpilih
                    </p>
                </div>
            </div>
        </div>

        <div class="stat-card bg-white p-6 rounded-xl shadow-sm relative overflow-hidden">
            <div class="flex justify-between items-start z-10 relative">
                <div class="w-full pr-2">
                    <p class="text-gray-500 text-sm font-medium mb-1">Item Sering Dipinjam</p>
                    <h3 id="stat-top-item-name" class="text-2xl font-bold text-petra-blue truncate" title="{{ $topItemName ?? '-' }}">
                        {{ $topItemName ?? '-' }}
                    </h3>
                    <p class="text-xs text-gray-400 mt-2 font-medium">
                        Dipinjam <span id="stat-top-item-count">{{ $topItemCount ?? 0 }}</span> kali
                    </p>
                </div>
            </div>
        </div>

        <div class="stat-card bg-white p-6 rounded-xl shadow-sm relative">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-500 text-sm font-medium mb-1">Rentang Data</p>
                    <h3 id="stat-filter-label" class="text-lg font-bold text-petra-blue">
                        @if(request('date_start') || request('date_end'))
                        Filter Aktif
                        @else
                        Semua Waktu
                        @endif
                    </h3>
                    <p class="text-xs text-gray-400 mt-2">
                        Data ditampilkan berdasarkan filter
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
        <div class="p-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-gray-700 flex items-center gap-2">
                <i class="fa-solid fa-filter text-petra-blue"></i> Filter Data
            </h3>
            <div id="loading-indicator" class="hidden text-sm text-petra-blue font-medium flex items-center gap-2">
                <i class="fa-solid fa-circle-notch fa-spin"></i> Memuat data...
            </div>
        </div>

        <div class="p-6 filter-section rounded-b-xl">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Cari Peminjam</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-magnifying-glass text-gray-400"></i>
                        </div>
                        <input type="text" id="filter_search" placeholder="Nama / Email..."
                            class="pl-10 w-full rounded-lg border-gray-300 focus:border-petra-blue focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm placeholder-gray-400">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Laboratorium</label>
                    <select id="filter_lab" class="w-full rounded-lg border-gray-300 focus:border-petra-blue focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm">
                        <option value="">Semua Laboratorium</option>
                        @foreach($labs as $lab)
                        <option value="{{ $lab->id }}">{{ $lab->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tipe Barang</label>
                    <select id="filter_type" class="w-full rounded-lg border-gray-300 focus:border-petra-blue focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm">
                        <option value="">Semua Tipe</option>
                        @foreach($types as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Status</label>
                    <select id="filter_status" class="w-full rounded-lg border-gray-300 focus:border-petra-blue focus:ring text-sm">
                        <option value="">Semua Status</option>
                        <option value="approved">Disetujui (Active)</option>
                        <option value="rejected">Ditolak</option>
                        <option value="completed">Selesai (Returned)</option>
                        <option value="overdue">Terlambat</option>
                        <option value="pending">Menunggu (Pending)</option>
                    </select>
                </div>


                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Periode Akademik</label>
                    <select id="filter_period" class="w-full rounded-lg border-gray-300 focus:border-petra-blue focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm">
                        <option value="">Semua Periode</option>
                        @foreach($periods as $period)
                        <option value="{{ $period->id }}">{{ $period->academic_year }} - {{ $period->semester }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Dasar Tanggal</label>
                    <select id="filter_date_type" class="w-full rounded-lg border-gray-300 focus:border-petra-blue focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm bg-gray-50">
                        <option value="borrow_date">Tgl. Pinjam (Start)</option>
                        <option value="due_date">Tgl. Tenggat (Due)</option>
                        <option value="return_date">Tgl. Kembali (End)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Dari Tanggal</label>
                    <input type="date" id="filter_date_start" class="w-full rounded-lg border-gray-300 focus:border-petra-blue focus:ring text-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Sampai Tanggal</label>
                    <input type="date" id="filter_date_end" class="w-full rounded-lg border-gray-300 focus:border-petra-blue focus:ring text-sm">
                </div>

                <div class="md:col-span-4 flex justify-end mt-2">
                    <button type="button" id="btn-reset" class="bg-gray-100 border border-gray-300 text-gray-600 px-6 py-2 rounded-lg hover:bg-gray-200 hover:text-gray-800 transition shadow-sm font-medium text-sm flex items-center justify-center gap-2">
                        <i class="fa-solid fa-rotate-left"></i> Reset Filter
                    </button>
                </div>

            </div>
        </div>
    </div>

    <div id="table-container" class="min-h-[400px]">
        @include('admin.partials.history_itemscomp_table')
    </div>
</div>
{{-- -MODAL --}}
<div id="detail-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div id="modal-backdrop" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full font-sans">
            
            <div class="bg-white px-6 py-4 border-b border-gray-200 flex justify-between items-center sticky top-0 z-10">
                <h3 class="text-xl leading-6 font-semibold text-gray-900 truncate pr-4">
                    Detail: <span id="modal-header-name" class="text-petra-blue">Nama Barang</span>
                </h3>
                <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-500 focus:outline-none shrink-0">
                    <span class="sr-only">Close</span>
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="bg-white px-6 py-6">
                
                <div id="modal-loading" class="flex justify-center py-10">
                    <i class="fa-solid fa-circle-notch fa-spin text-3xl text-petra-blue"></i>
                </div>

                <div id="modal-content" class="hidden space-y-6 text-[15px] text-gray-900">

                    <div class="space-y-3">
                        <div class="flex">
                            <span class="w-40 font-bold text-gray-700 shrink-0">Nama</span>
                            <span id="modal-name" class="font-medium break-words">-</span>
                        </div>
                        <div class="flex">
                            <span class="w-40 font-bold text-gray-700 shrink-0">Serial Code</span>
                            <span id="modal-serial" class="font-mono">-</span>
                        </div>
                        <div class="flex">
                            <span class="w-40 font-bold text-gray-700 shrink-0">Tipe</span>
                            <span id="modal-type">-</span>
                        </div>
                        <div class="flex items-center">
                            <span class="w-40 font-bold text-gray-700 shrink-0">Kondisi</span>
                            <span id="modal-condition">
                                </span>
                        </div>
                        <div class="flex">
                            <span class="w-40 font-bold text-gray-700 shrink-0">Tgl. Produksi</span>
                            <span id="modal-produced">-</span>
                        </div>

                        <div id="modal-location-container" class="space-y-3">
                            </div>
                    </div>

                    <hr class="border-gray-200">

                    <div>
                        <h4 class="font-bold text-gray-900 mb-4">Spesifikasi:</h4>
                        <div id="modal-specs-grid" class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-8">
                            </div>
                        <p id="modal-no-specs" class="text-gray-500 text-sm italic hidden">Tidak ada data spesifikasi.</p>
                    </div>

                    <div id="modal-components-section" class="hidden pt-2">
                        <h4 class="font-bold text-gray-900 mb-3" id="modal-components-title">Komponen Terpasang:</h4>
                        <ul id="modal-components-list" class="list-disc list-inside space-y-2 text-gray-900 ml-2">
                            </ul>
                    </div>
                </div>
            </div>
            </div>
    </div>
</div>
@endsection

@section('script')
@section('script')
<script>
    const modal = document.getElementById('detail-modal');
    const modalLoading = document.getElementById('modal-loading');
    const modalContent = document.getElementById('modal-content');

    function closeModal() {
        modal.classList.add('hidden');
    }

    function openModal() {
        modal.classList.remove('hidden');
        modalLoading.classList.remove('hidden');
        modalContent.classList.add('hidden');
    }

    document.addEventListener('DOMContentLoaded', function() {
        
        const container = document.getElementById('table-container');
        const loadingIndicator = document.getElementById('loading-indicator');
        const searchInput = document.getElementById('filter_search');

        const filters = {
            search: searchInput,
            lab_id: document.getElementById('filter_lab'),
            period_id: document.getElementById('filter_period'),
            type_id: document.getElementById('filter_type'),
            date_type: document.getElementById('filter_date_type'),
            date_start: document.getElementById('filter_date_start'),
            date_end: document.getElementById('filter_date_end'),
            status: document.getElementById('filter_status')
        };

        let debounceTimer;

        function fetchHistory(url = null) {
            loadingIndicator.classList.remove('hidden');
            container.style.opacity = '0.5';

            let fetchUrl = url || "{{ route('admin.historyinventaris') }}";
            const currentUrlObj = new URL(fetchUrl, window.location.origin);

            for (const key in filters) {
                if (filters[key] && filters[key].value) {
                    currentUrlObj.searchParams.set(key, filters[key].value);
                }
            }

            fetch(currentUrlObj.toString(), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => response.json())
                .then(data => {
                    container.innerHTML = data.html;
                    updateStats(data.stats);
                    container.style.opacity = '1';
                    loadingIndicator.classList.add('hidden');
                })
                .catch(error => {
                    console.error('Error:', error);
                    loadingIndicator.classList.add('hidden');
                    container.style.opacity = '1';
                    alert('Gagal memuat data.');
                });
        }

        function updateStats(stats) {
            const elTotal = document.getElementById('stat-total-bookings');
            const elTopName = document.getElementById('stat-top-item-name');
            const elTopCount = document.getElementById('stat-top-item-count');
            const elFilterLabel = document.getElementById('stat-filter-label');

            if (elTotal) elTotal.textContent = stats.total_bookings;
            if (elTopName) {
                elTopName.textContent = stats.top_item_name;
                elTopName.title = stats.top_item_name;
            }
            if (elTopCount) elTopCount.textContent = stats.top_item_count;
            if (elFilterLabel) {
                elFilterLabel.textContent = stats.is_filtered ? 'Filter Aktif' : 'Semua Waktu';
                elFilterLabel.classList.toggle('text-petra-blue', stats.is_filtered);
                elFilterLabel.classList.toggle('text-gray-800', !stats.is_filtered);
            }
        }

        ['lab_id', 'period_id', 'date_start', 'date_end', 'status', 'date_type', 'type_id'].forEach(key => {
            if (filters[key]) filters[key].addEventListener('change', () => fetchHistory());
        });

        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => fetchHistory(), 500);
            });
        }

        container.addEventListener('click', function(e) {
            const link = e.target.closest('.pagination a, a.page-link');
            if (e.target.closest('nav a')) {
                e.preventDefault();
                const href = e.target.closest('nav a').getAttribute('href');
                if (href) fetchHistory(href);
            }
        });

        const btnReset = document.getElementById('btn-reset');
        if (btnReset) {
            btnReset.addEventListener('click', function() {
                for (const key in filters) {
                    if (filters[key]) filters[key].value = '';
                }
                fetchHistory();
            });
        }

        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.btn-detail-trigger');
            if (btn) {
                const id = btn.dataset.id;
                const type = btn.dataset.type; 
                fetchDetails(id, type);
            }

            if (e.target === document.getElementById('modal-backdrop')) {
                closeModal();
            }
        });

        function fetchDetails(id, fullType) {
            openModal();

            let url = '';
            let isItem = false;

            if (fullType.includes('Items')) {
                url = `{{ route('admin.items.details', ':id') }}`.replace(':id', id);
                isItem = true;
            } else if (fullType.includes('Components')) {
                url = `{{ route('admin.components.details', ':id') }}`.replace(':id', id);
                isItem = false;
            }

            fetch(url)
                .then(res => {
                    if (!res.ok) throw new Error('Network response was not ok');
                    return res.json();
                })
                .then(data => {
                    renderModalData(data, isItem);
                })
                .catch(err => {
                    console.error(err);
                    alert('Gagal mengambil data detail.');
                    closeModal();
                });
        }

        function renderModalData(data, isItem) {
            document.getElementById('modal-header-name').textContent = data.name;
            document.getElementById('modal-name').textContent = data.name;
            document.getElementById('modal-serial').textContent = data.serial_code;
            document.getElementById('modal-type').textContent = data.type ? data.type.name : '-';
            document.getElementById('modal-produced').textContent = data.produced_at ? new Date(data.produced_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '-';

            const badge = document.getElementById('modal-condition');
            if (data.condition == 1) {
                badge.className = "px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800";
                badge.textContent = "Baik";
            } else {
                badge.className = "px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800";
                badge.textContent = "Rusak";
            }

            const locationContainer = document.getElementById('modal-location-container');
            locationContainer.innerHTML = '';

            if (isItem) {
                let locValue = 'Gudang / Lepas';
                if (data.desk) {
                    locValue = `Meja ${data.desk.location} (${data.desk.lab?.name ?? '-'})`;
                } else if (data.lab) {
                    locValue = `Lab ${data.lab.name}`;
                }
                
                locationContainer.innerHTML = `
                    <div class="flex">
                        <span class="w-40 font-bold text-gray-700 shrink-0">Lokasi</span>
                        <span class="font-medium break-words">${locValue}</span>
                    </div>`;
            } else {
                if (data.item) {
                    const parentItemName = `${data.item.name} (${data.item.serial_code})`;
                    let parentLocValue = 'Gudang';
                    if(data.item.desk) parentLocValue = `Meja ${data.item.desk.location} (${data.item.desk.lab?.name ?? '-'})`;
                    else if(data.item.lab) parentLocValue = `Lab ${data.item.lab.name}`;

                    locationContainer.innerHTML = `
                        <div class="flex">
                            <span class="w-40 font-bold text-gray-700 shrink-0">Terpasang di Item</span>
                            <span class="font-medium break-words">${parentItemName}</span>
                        </div>
                        <div class="flex">
                            <span class="w-40 font-bold text-gray-700 shrink-0">Lokasi Item</span>
                            <span class="font-medium break-words">${parentLocValue}</span>
                        </div>`;
                } else {
                    let locValue = 'Gudang / Lepas';
                    if (data.lab) locValue = `Lab ${data.lab.name}`;
                    locationContainer.innerHTML = `
                        <div class="flex">
                            <span class="w-40 font-bold text-gray-700 shrink-0">Lokasi</span>
                            <span class="font-medium break-words">${locValue}</span>
                        </div>`;
                }
            }

            const specsGrid = document.getElementById('modal-specs-grid');
            const noSpecsMsg = document.getElementById('modal-no-specs');
            specsGrid.innerHTML = '';

            if (data.spec_set_values && data.spec_set_values.length > 0) {
                noSpecsMsg.classList.add('hidden');
                specsGrid.classList.remove('hidden');
                data.spec_set_values.forEach(spec => {
                    const attrName = spec.spec_attributes ? spec.spec_attributes.name : 'Unknown';
                    const specItem = `
                        <div>
                            <dt class="font-bold text-gray-700 text-xs uppercase tracking-wider">${attrName}</dt>
                            <dd class="mt-1 font-medium text-gray-900 break-words">${spec.value}</dd>
                        </div>
                    `;
                    specsGrid.insertAdjacentHTML('beforeend', specItem);
                });
            } else {
                specsGrid.classList.add('hidden');
                noSpecsMsg.classList.remove('hidden');
            }

            const compSection = document.getElementById('modal-components-section');
            const compList = document.getElementById('modal-components-list');
            const compTitle = document.getElementById('modal-components-title');
            compList.innerHTML = '';

            if (isItem && data.components && data.components.length > 0) {
                compSection.classList.remove('hidden');
                compTitle.textContent = `Komponen Terpasang (${data.components.length}):`;
                
                data.components.forEach(comp => {
                    const compTypeName = comp.type ? comp.type.name : 'Component';
                    const listItem = `
                        <li class="flex items-start gap-2">
                            <span class="font-medium">${comp.name}</span> 
                            <span class="text-gray-600 font-mono text-sm">(${comp.serial_code})</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800 uppercase tracking-wide ml-1">
                                ${compTypeName}
                            </span>
                        </li>`;
                    compList.insertAdjacentHTML('beforeend', listItem);
                });
            } else {
                compSection.classList.add('hidden');
            }

            modalLoading.classList.add('hidden');
            modalContent.classList.remove('hidden');
        }
    });
</script>
@endsection
@endsection