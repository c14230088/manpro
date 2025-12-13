@extends('layouts.admin')

@section('title', 'History Booking Labs')

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
<div class="w-full">
    <div class="flex flex-col md:flex-row w-full py-4 shadow-md items-center justify-center mb-5 px-6 md:px-4">
        <h1 class="text-center text-4xl uppercase font-bold mb-2 md:mb-0">History Booking Labs</h1>
    </div>


    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

        <div class="stat-card bg-white p-6 rounded-xl shadow-sm relative overflow-hidden">
            <div class="flex justify-between items-start z-10 relative">
                <div>
                    <p class="text-gray-500 text-sm font-medium mb-1">Total Peminjaman</p>
                    <h3 id="stat-total-bookings" class="text-3xl font-bold text-petra-blue">
                        {{ number_format($totalBookings) }}
                    </h3>
                    <p class="text-xs text-gray-400 mt-2 font-medium">
                        Sesuai filter terpilih
                    </p>
                </div>
                <div class="p-3 bg-blue-50 rounded-lg text-petra-blue">
                    <i class="fa-solid fa-file-invoice text-xl"></i>
                </div>
            </div>
        </div>

        <div class="stat-card bg-white p-6 rounded-xl shadow-sm relative overflow-hidden">
            <div class="flex justify-between items-start z-10 relative">
                <div class="w-full pr-2">
                    <p class="text-gray-500 text-sm font-medium mb-1">Lab Paling Sering Dipinjam</p>
                    <h3 id="stat-top-lab-name" class="text-3xl font-bold text-petra-blue truncate" title="{{ $topLabData->lab_name ?? '-' }}">
                        {{ $topLabData->lab_name ?? '-' }}
                    </h3>
                    <p class="text-xs text-gray-400 mt-2 font-medium">
                        Dipinjam <span id="stat-top-lab-count">{{ $topLabData->total ?? 0 }}</span> kali
                    </p>
                </div>
                <div class="p-3 bg-blue-50 rounded-lg text-petra-blue shrink-0">
                    <i class="fa-solid fa-flask text-xl"></i>
                </div>
            </div>
        </div>

        <div class="stat-card bg-white p-6 rounded-xl shadow-sm relative">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-500 text-sm font-medium mb-1">Rentang Data</p>
                    <h3 id="stat-filter-label" class="text-lg font-bold text-gray-800">
                        @if(request('date_start') || request('date_end'))
                        Filter Aktif
                        @else
                        Semua Waktu
                        @endif
                    </h3>
                    <p class="text-xs text-gray-500 mt-2">
                        Data ditampilkan berdasarkan filter
                    </p>
                </div>
                <div class="p-3 bg-yellow-50 rounded-lg text-yellow-600">
                    <i class="fa-solid fa-calendar-check text-xl"></i>
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
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">

                <div class="md:col-span-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Cari Peminjam</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-magnifying-glass text-gray-400"></i>
                        </div>
                        <input type="text" id="filter_search" placeholder="Ketik nama / email..."
                            class="pl-10 w-full rounded-lg border-gray-300 focus:border-petra-blue focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm placeholder-gray-400">
                    </div>
                </div>

                <div class="md:col-span-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Laboratorium</label>
                    <select id="filter_lab" class="w-full rounded-lg border-gray-300 focus:border-petra-blue focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm">
                        <option value="">Semua Laboratorium</option>
                        @foreach($labs as $lab)
                        <option value="{{ $lab->id }}">{{ $lab->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Periode Akademik</label>
                    <select id="filter_period" class="w-full rounded-lg border-gray-300 focus:border-petra-blue focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm">
                        <option value="">Semua Periode</option>
                        @foreach($periods as $period)
                        <option value="{{ $period->id }}">{{ $period->academic_year }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-3">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Dari Tanggal</label>
                    <input type="date" id="filter_date_start" class="w-full rounded-lg border-gray-300 focus:border-petra-blue focus:ring text-sm">
                </div>

                <div class="md:col-span-3">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Sampai Tanggal</label>
                    <input type="date" id="filter_date_end" class="w-full rounded-lg border-gray-300 focus:border-petra-blue focus:ring text-sm">
                </div>

                <div class="md:col-span-3">
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

                <div class="md:col-span-3 flex items-end">
                    <button type="button" id="btn-reset" class="w-full bg-gray-100 border border-gray-300 text-gray-600 px-4 py-2 rounded-lg hover:bg-gray-200 hover:text-gray-800 transition shadow-sm font-medium text-sm flex items-center justify-center gap-2 h-[42px]">
                        <i class="fa-solid fa-rotate-left"></i> Reset Filter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="history-data">
        @include('admin.partials.history_labs_table')
    </div>

</div>
@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('history-data');
        const loadingIndicator = document.getElementById('loading-indicator');
        const searchInput = document.getElementById('filter_search');
        
        const filters = {
            search: searchInput,
            lab_id: document.getElementById('filter_lab'),
            period_id: document.getElementById('filter_period'),
            date_start: document.getElementById('filter_date_start'),
            date_end: document.getElementById('filter_date_end'),
            status: document.getElementById('filter_status')
        };

        let debounceTimer;

        function fetchHistory(url = null) {
            loadingIndicator.classList.remove('hidden');
            container.style.opacity = '0.5';

            let fetchUrl = url || "{{ route('admin.historylabs') }}";

            const params = new URLSearchParams();
            
            const currentUrlObj = new URL(fetchUrl, window.location.origin);
            
            for (const key in filters) {
                if (filters[key].value) {
                    currentUrlObj.searchParams.set(key, filters[key].value);
                }
            }

            fetch(currentUrlObj.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
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
                alert('Gagal memuat data. Silakan coba lagi.');
            });
        }

        function updateStats(stats) {
            const elTotal = document.getElementById('stat-total-bookings');
            const elTopName = document.getElementById('stat-top-lab-name');
            const elTopCount = document.getElementById('stat-top-lab-count');
            const elFilterLabel = document.getElementById('stat-filter-label');

            if(elTotal) elTotal.textContent = stats.total_bookings;
            
            if(elTopName) {
                elTopName.textContent = stats.top_lab_name;
                elTopName.title = stats.top_lab_name;
            }
            
            if(elTopCount) elTopCount.textContent = stats.top_lab_count;

            if(elFilterLabel) {
                elFilterLabel.textContent = stats.is_filtered ? 'Filter Aktif' : 'Semua Waktu';
                if(stats.is_filtered) {
                    elFilterLabel.classList.add('text-petra-blue');
                    elFilterLabel.classList.remove('text-gray-800');
                } else {
                    elFilterLabel.classList.add('text-gray-800');
                    elFilterLabel.classList.remove('text-petra-blue');
                }
            }
        }


        ['lab_id', 'period_id', 'date_start', 'date_end', 'status'].forEach(key => {
            filters[key].addEventListener('change', () => fetchHistory());
        });

        searchInput.addEventListener('keyup', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                fetchHistory();
            }, 500); 
        });

        container.addEventListener('click', function(e) {
            const link = e.target.closest('.pagination a, a.page-link'); 
            
            if (e.target.closest('nav a')) { 
                e.preventDefault();
                const href = e.target.closest('nav a').getAttribute('href');
                if (href) {
                    fetchHistory(href);
                }
            }
        });

        document.getElementById('btn-reset').addEventListener('click', function() {
            for (const key in filters) {
                filters[key].value = '';
            }
            fetchHistory();
        });
    });
</script>
@endsection