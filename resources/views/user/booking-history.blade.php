@extends('layouts.user')

@section('title', 'Riwayat Peminjaman Saya')

@section('style')
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    'petra-blue': '#1a237e',
                    'petra-yellow': '#ffca28',
                    'petra-darkgray': '#424242',
                }
            }
        }
    }
</script>
<style>
    .card-hover:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    .booking-card {
        transition: all 0.3s ease-in-out;
    }

    .hidden-card {
        display: none;
    }
</style>
@endsection

@section('body')

@include('user.partials.navbar')

<div class="container mx-auto px-4 pt-24 pb-12 max-w-5xl">

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-petra-blue">Riwayat Peminjaman</h1>
            <p class="text-gray-500 mt-1">Pantau status pengajuan peminjaman aset laboratorium Anda.</p>
        </div>
        <a href="{{ route('user.booking.form') }}" class="group bg-petra-blue text-white px-5 py-2.5 rounded-lg shadow-md hover:bg-blue-800 transition-all flex items-center gap-2 font-medium">
            <i class="fa-solid fa-plus transition-transform group-hover:rotate-90"></i>
            <span class="hidden sm:inline">Buat Peminjaman</span>
            <span class="sm:hidden">Baru</span>
        </a>
    </div>

    @if(!$bookings->isEmpty())
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-8">
        <div class="flex flex-col xl:flex-row gap-4 items-start xl:items-center justify-between">

            <div class="relative w-full xl:w-1/3">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fa-solid fa-search text-gray-400"></i>
                </div>
                <input type="text" id="searchInput" placeholder="Cari judul kegiatan..."
                    class="pl-10 w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-petra-blue focus:border-petra-blue transition-colors shadow-sm"
                    onkeyup="applyFilters()">
            </div>

            <div class="flex flex-col sm:flex-row gap-3 w-full xl:w-auto flex-wrap">

                <div class="flex items-center gap-2 w-full sm:w-auto bg-gray-50 rounded-lg border border-gray-200 p-1">
                    <div class="relative flex-grow sm:flex-grow-0">
                        <input type="date" id="startDateFilter"
                            class="w-full sm:w-36 bg-transparent border-none text-sm focus:ring-0 text-gray-600 p-1"
                            placeholder="Dari"
                            title="Dari Tanggal"
                            onchange="applyFilters()">
                    </div>
                    <span class="text-gray-400 text-sm">-</span>
                    <div class="relative flex-grow sm:flex-grow-0">
                        <input type="date" id="endDateFilter"
                            class="w-full sm:w-36 bg-transparent border-none text-sm focus:ring-0 text-gray-600 p-1"
                            placeholder="Sampai"
                            title="Sampai Tanggal"
                            onchange="applyFilters()">
                    </div>
                </div>

                <div class="relative w-full sm:w-auto">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-sort text-gray-400"></i>
                    </div>
                    <select id="sortFilter"
                        class="pl-10 w-full sm:w-48 rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-petra-blue focus:border-petra-blue text-gray-600 shadow-sm appearance-none"
                        onchange="applyFilters()">
                        <option value="newest_submit">Diajukan: Terbaru</option>
                        <option value="oldest_submit">Diajukan: Terlama</option>
                        <option value="soonest_event">Acara: Terdekat</option>
                        <option value="latest_event">Acara: Terjauh</option>
                    </select>
                </div>

                <button onclick="resetFilters()" class="px-4 py-2 text-sm text-gray-500 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors border border-transparent hover:border-red-100 whitespace-nowrap">
                    Reset
                </button>
            </div>
        </div>
    </div>
    @endif

    @if($bookings->isEmpty())
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center flex flex-col items-center">
        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
            <i class="fa-regular fa-folder-open text-3xl text-gray-400"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-700 mb-2">Belum ada riwayat</h3>
        <p class="text-gray-500 max-w-md mx-auto mb-6">Anda belum pernah mengajukan peminjaman.</p>
    </div>
    @else
    <div class="grid gap-6" id="bookingList">
        @foreach($bookings as $booking)
        <div class="booking-card bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden transition-all duration-300 card-hover"
            data-title="{{ strtolower($booking->event_name) }}"
            data-submit-date="{{ $booking->created_at->timestamp }}"
            data-event-date="{{ \Carbon\Carbon::parse($booking->event_started_at)->timestamp }}"
            data-event-date-str="{{ \Carbon\Carbon::parse($booking->event_started_at)->format('Y-m-d') }}">

            <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex flex-col sm:flex-row justify-between sm:items-center gap-3">
                <div class="flex items-center gap-2">
                    <i class="fa-regular fa-calendar-check text-petra-blue"></i>
                    <span class="text-sm font-semibold text-gray-600">
                        Diajukan: {{ \Carbon\Carbon::parse($booking->created_at)->translatedFormat('d F Y, H:i') }}
                    </span>
                </div>

                @php
                $statusClass = 'bg-yellow-100 text-yellow-700 border-yellow-200';
                $statusIcon = 'fa-clock';
                $statusText = 'Menunggu Persetujuan';

                if($booking->approved === 1) {
                $statusClass = 'bg-green-100 text-green-700 border-green-200';
                $statusIcon = 'fa-check-circle';
                $statusText = 'Disetujui';
                } elseif($booking->approved === 0) {
                $statusClass = 'bg-red-100 text-red-700 border-red-200';
                $statusIcon = 'fa-times-circle';
                $statusText = 'Ditolak';
                }
                @endphp

                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border {{ $statusClass }}">
                    <i class="fa-solid {{ $statusIcon }}"></i> {{ $statusText }}
                </span>
            </div>

            <div class="p-6">
                <div class="grid md:grid-cols-3 gap-6">
                    <div class="col-span-2">
                        <h2 class="text-xl font-bold text-petra-blue mb-1">{{ $booking->event_name }}</h2>
                        <div class="flex flex-col gap-2 text-sm text-gray-600 mt-3">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-clock text-gray-400 w-5 text-center"></i>
                                <span>
                                    {{ \Carbon\Carbon::parse($booking->event_started_at)->format('d M H:i') }}
                                    <span class="mx-1 text-gray-300">|</span>
                                    {{ \Carbon\Carbon::parse($booking->event_ended_at)->format('d M H:i') }}
                                </span>
                            </div>
                            @if($booking->thesis_title)
                            <div class="flex items-start gap-2">
                                <i class="fa-solid fa-graduation-cap text-gray-400 w-5 text-center mt-0.5"></i>
                                <span>Judul Skripsi: <span class="font-medium text-gray-800 italic">"{{ $booking->thesis_title }}"</span></span>
                            </div>
                            @endif
                            @if($booking->supervisor)
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-user-tie text-gray-400 w-5 text-center"></i>
                                <span>Pembimbing: <span class="font-medium text-gray-800">{{ $booking->supervisor->name }}</span></span>
                            </div>
                            @endif
                        </div>
                        @if($booking->booking_detail)
                        <div class="mt-4 p-3 bg-blue-50 rounded-lg text-sm text-blue-800 border border-blue-100 flex gap-2">
                            <i class="fa-solid fa-circle-info mt-0.5"></i>
                            <div><span class="font-bold">Catatan:</span> {{ $booking->booking_detail }}</div>
                        </div>
                        @endif
                    </div>
                    <div class="col-span-1 bg-gray-50 rounded-lg p-4 border border-gray-100 h-fit">
                        <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3 flex justify-between items-center">
                            Item Dipinjam
                            <span class="bg-gray-200 text-gray-600 py-0.5 px-2 rounded-full text-[10px]">{{ $booking->bookings_items->count() }}</span>
                        </h4>
                        <ul class="space-y-2">
                            @foreach($booking->bookings_items as $index => $item)
                            <li class="flex items-start gap-2 text-sm text-gray-700 {{ $index >= 3 ? 'hidden extra-item-'.$booking->id : '' }}">
                                <i class="fa-solid fa-box text-petra-blue mt-1 text-xs"></i>
                                <span class="leading-tight">
                                    {{ $item->bookable->name ?? 'Unknown Item' }}
                                    @if(str_contains($item->bookable_type, 'Labs'))
                                    <span class="text-[10px] bg-indigo-100 text-indigo-700 px-1 rounded ml-1 font-bold">LAB</span>
                                    @endif
                                </span>
                            </li>
                            @endforeach
                        </ul>
                        @if($booking->bookings_items->count() > 3)
                        <button onclick="toggleItems('{{ $booking->id }}', this)"
                            class="w-full mt-3 text-xs text-petra-blue font-bold hover:bg-blue-50 py-1 rounded transition-colors flex items-center justify-center gap-1 border border-dashed border-petra-blue/30">
                            <span>+ {{ $booking->bookings_items->count() - 3 }} item lainnya</span>
                            <i class="fa-solid fa-chevron-down"></i>
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach

    </div> 
    <div id="loadMoreWrapper" class="text-center mt-8 hidden">
        <button onclick="loadMore()" class="group px-6 py-2.5 rounded-full border border-gray-300 text-gray-600 font-medium text-sm hover:border-petra-blue hover:text-petra-blue hover:bg-blue-50 transition-all shadow-sm">
            <span id="loadMoreText">Muat Lebih Banyak</span>
            <i class="fa-solid fa-chevron-down ml-2 text-xs transition-transform group-hover:translate-y-0.5"></i>
        </button>
        <p class="text-xs text-gray-400 mt-2">Menampilkan <span id="showingCount">0</span> dari <span id="totalCount">0</span> riwayat</p>
    </div>
</div>

<div id="noResultState" class="hidden mt-8 text-center py-12">
    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
        <i class="fa-solid fa-calendar-xmark text-gray-400 text-2xl"></i>
    </div>
    <h3 class="text-lg font-bold text-gray-700">Tidak ditemukan</h3>
    <p class="text-gray-500">Tidak ada peminjaman dalam rentang tanggal atau kata kunci tersebut.</p>
    <button onclick="resetFilters()" class="mt-4 text-petra-blue font-bold hover:underline">Reset Filter</button>
</div>
@endif
</div>

<footer class="bg-white border-t border-gray-200 mt-12 py-8">
    <div class="container mx-auto px-4 text-center text-gray-500 text-sm">
        &copy; {{ date('Y') }} Laboratorium Gedung P - UK Petra. All rights reserved.
    </div>
</footer>

@endsection

@section('script')
<script>
    const ITEMS_PER_PAGE = 5; 
    let currentVisibleCount = ITEMS_PER_PAGE;

    function toggleItems(bookingId, btn) {
        const hiddenItems = document.querySelectorAll(`.extra-item-${bookingId}`);
        hiddenItems.forEach(item => item.classList.remove('hidden'));
        btn.style.display = 'none';
    }

    function loadMore() {
        currentVisibleCount += ITEMS_PER_PAGE;
        applyFilters(true);
    }

    function applyFilters(isLoadingMore = false) {
        const searchVal = document.getElementById('searchInput').value.toLowerCase();
        const startDateVal = document.getElementById('startDateFilter').value;
        const endDateVal = document.getElementById('endDateFilter').value;
        const sortVal = document.getElementById('sortFilter').value;

        const isFiltering = (searchVal !== '' || startDateVal !== '' || endDateVal !== '');

        
        if (isFiltering && !isLoadingMore) {
            currentVisibleCount = 999999; 
        } else if (!isFiltering && !isLoadingMore) {
            currentVisibleCount = ITEMS_PER_PAGE;
        }

        const container = document.getElementById('bookingList');
        const cards = Array.from(container.getElementsByClassName('booking-card'));
        const noResult = document.getElementById('noResultState');
        const loadMoreBtn = document.getElementById('loadMoreWrapper');

        const sortedCards = cards.sort((a, b) => {
            const submitDateA = parseInt(a.getAttribute('data-submit-date'));
            const submitDateB = parseInt(b.getAttribute('data-submit-date'));
            const eventDateA = parseInt(a.getAttribute('data-event-date'));
            const eventDateB = parseInt(b.getAttribute('data-event-date'));

            switch (sortVal) {
                case 'newest_submit':
                    return submitDateB - submitDateA;
                case 'oldest_submit':
                    return submitDateA - submitDateB;
                case 'soonest_event':
                    return eventDateA - eventDateB;
                case 'latest_event':
                    return eventDateB - eventDateA;
                default:
                    return 0;
            }
        });

        cards.forEach(card => card.remove());
        sortedCards.forEach(card => container.appendChild(card));

        let totalMatchCount = 0;
        let currentShown = 0;

        sortedCards.forEach((card) => {
            const title = card.getAttribute('data-title');
            const eventDateStr = card.getAttribute('data-event-date-str');

            let matchesSearch = title.includes(searchVal);
            let matchesStart = startDateVal === "" || eventDateStr >= startDateVal;
            let matchesEnd = endDateVal === "" || eventDateStr <= endDateVal;

            if (matchesSearch && matchesStart && matchesEnd) {
                totalMatchCount++;

                
                if (totalMatchCount <= currentVisibleCount) {
                    card.classList.remove('hidden-card'); 
                    currentShown++;
                } else {
                    card.classList.add('hidden-card'); 
                }
            } else {
                card.classList.add('hidden-card'); 
            }
        });

        if (totalMatchCount === 0 && cards.length > 0) {
            noResult.classList.remove('hidden');
            loadMoreBtn.classList.add('hidden');
        } else {
            noResult.classList.add('hidden');

            if (!isFiltering && totalMatchCount > currentShown) {
                loadMoreBtn.classList.remove('hidden');
                document.getElementById('showingCount').innerText = currentShown;
                document.getElementById('totalCount').innerText = totalMatchCount;
            } else {
                loadMoreBtn.classList.add('hidden');
            }
        }
    }

    function resetFilters() {
        document.getElementById('searchInput').value = '';
        document.getElementById('startDateFilter').value = '';
        document.getElementById('endDateFilter').value = '';
        document.getElementById('sortFilter').value = 'newest_submit';

        currentVisibleCount = ITEMS_PER_PAGE;
        applyFilters();
    }

    document.addEventListener('DOMContentLoaded', () => {
        applyFilters();
    });
</script>
@endsection