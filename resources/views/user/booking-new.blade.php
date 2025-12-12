@extends('layouts.user')

@section('title', 'Formulir Peminjaman')

@section('style')
<style>
    :root {
        --petra-blue: #1a237e;
        --petra-yellow: #ffca28;
        --petra-wood: #8b4513;
        --petra-darkgray: #424242;
        --petra-gray: #f1f5f9;
    }

    .booking-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 2px solid #e2e8f0;
        background: white;
    }

    .booking-card:hover {
        border-color: var(--petra-blue);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    }

    .booking-card.active {
        border-color: var(--petra-blue) !important;
        background-color: rgba(26, 35, 126, 0.05);
        box-shadow: 0 0 0 3px rgba(26, 35, 126, 0.1);
    }

    input[name="booking_type"]:checked+.type-card {
        border-color: var(--petra-blue);
        background: linear-gradient(135deg, rgba(26, 35, 126, 0.05), rgba(26, 35, 126, 0.02));
    }

    .desk-grid-container {
        display: grid;
        gap: 1rem;
        padding: 1.5rem;
        background: var(--petra-gray);
        border-radius: 0.75rem;
        border: 2px solid #e2e8f0;
        min-width: fit-content;
    }

    .desk-card {
        transition: all 0.2s ease;
        min-height: 100px;
        border: 2px solid #cbd5e1;
        border-radius: 0.5rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: white;
        cursor: pointer;
        user-select: none;
        position: relative;
    }

    .desk-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .desk-card.available {
        border-color: #10b981;
    }

    .desk-card.available:hover {
        border-color: var(--petra-blue);
    }

    .desk-card.full {
        border-color: #f87171;
        background-color: #fef2f2;
        cursor: not-allowed;
    }

    .desk-card.empty {
        border-color: #e2e8f0;
        background-color: #f8fafc;
        cursor: default;
    }

    .desk-card.selected {
        border-color: var(--petra-blue) !important;
        background-color: rgba(26, 35, 126, 0.1) !important;
        box-shadow: 0 0 0 3px rgba(26, 35, 126, 0.2);
        z-index: 10;
    }

    .item-card {
        border: 2px solid #e2e8f0;
        border-radius: 0.5rem;
        padding: 1rem;
        background: white;
        transition: all 0.2s;
    }

    .item-card:hover {
        border-color: var(--petra-blue);
        transform: translateY(-1px);
    }

    .item-card.selected {
        border-color: var(--petra-yellow);
        background-color: rgba(255, 202, 40, 0.1);
    }

    .item-card.unavailable {
        opacity: 0.6;
        background-color: #f1f5f9;
        cursor: not-allowed;
    }

    .cart-item {
        border-bottom: 1px solid #e2e8f0;
        padding: 0.75rem;
        transition: all 0.2s;
    }

    .cart-item:hover {
        background-color: #f8fafc;
    }

    .booking-section {
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .form-input {
        border: 2px solid #e2e8f0;
        border-radius: 0.5rem;
        padding: 0.75rem 1rem;
        transition: all 0.2s;
    }

    .form-input:focus {
        border-color: var(--petra-blue);
        box-shadow: 0 0 0 3px rgba(26, 35, 126, 0.1);
        outline: none;
    }

    @media (min-width: 1024px) {
        .split-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }
    }
    .overflow-auto::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    .overflow-auto::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    .overflow-auto::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    .overflow-auto::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>
@endsection

@section('body')
@include('user.partials.navbar')
<div class="min-h-screen bg-gray-50 pt-24 pb-12">
    <div class="container mx-auto px-4 max-w-6xl">

        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-petra-blue mb-4">Formulir Peminjaman</h1>
            <p class="text-petra-darkgray text-lg max-w-2xl mx-auto">
                Ajukan peminjaman fasilitas laboratorium untuk keperluan akademik Anda
            </p>
        </div>

        <!-- Main Form Card -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-200 mb-8">

            <!-- Form Header -->
            <div class="bg-gradient-to-r from-petra-blue to-blue-900 p-8 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold">Detail Peminjaman</h2>
                        <p class="text-blue-100 mt-1">Isi formulir sesuai kebutuhan Anda</p>
                    </div>
                    <div class="hidden md:block">
                        <div class="flex items-center gap-2 bg-blue-800/50 px-4 py-2 rounded-full">
                            <i class="fa-solid fa-calendar-check"></i>
                            <span>Sistem Reservasi Online</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Content -->
            <div class="p-8">

                <!-- Step 1: Tipe Peminjaman -->
                <div class="mb-12">
                    <h3 class="text-xl font-bold text-petra-blue mb-6 flex items-center gap-3">
                        <span
                            class="flex items-center justify-center w-8 h-8 rounded-full bg-petra-blue text-white text-sm">1</span>
                        Tipe Peminjaman
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Lab Booking -->
                        <label class="cursor-pointer">
                            <input type="radio" name="booking_type" value="lab" class="sr-only peer" checked>
                            <div class="type-card booking-card p-6 text-center peer-checked:border-petra-blue">
                                <div
                                    class="w-16 h-16 mx-auto mb-4 rounded-full bg-blue-100 flex items-center justify-center">
                                    <i class="fa-solid fa-building text-2xl text-petra-blue"></i>
                                </div>
                                <h4 class="font-bold text-lg text-petra-blue mb-2">Laboratorium</h4>
                                <p class="text-sm text-gray-600">Reservasi ruang lab untuk kegiatan kelompok</p>
                            </div>
                        </label>

                        <!-- Set Booking -->
                        <label class="cursor-pointer">
                            <input type="radio" name="booking_type" value="sets" class="sr-only peer">
                            <div class="type-card booking-card p-6 text-center peer-checked:border-petra-blue">
                                <div
                                    class="w-16 h-16 mx-auto mb-4 rounded-full bg-yellow-100 flex items-center justify-center">
                                    <i class="fa-solid fa-desktop text-2xl text-petra-yellow"></i>
                                </div>
                                <h4 class="font-bold text-lg text-petra-blue mb-2">Set Lengkap</h4>
                                <p class="text-sm text-gray-600">PC + Monitor untuk kebutuhan khusus</p>
                            </div>
                        </label>

                        <!-- Item Booking -->
                        <label class="cursor-pointer">
                            <input type="radio" name="booking_type" value="items" class="sr-only peer">
                            <div class="type-card booking-card p-6 text-center peer-checked:border-petra-blue">
                                <div
                                    class="w-16 h-16 mx-auto mb-4 rounded-full bg-green-100 flex items-center justify-center">
                                    <i class="fa-solid fa-microchip text-2xl text-green-600"></i>
                                </div>
                                <h4 class="font-bold text-lg text-petra-blue mb-2">Item Individual</h4>
                                <p class="text-sm text-gray-600">Pinjam peralatan elektronik spesifik</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Step 2: Detail Umum -->
                <div class="mb-12">
                    <h3 class="text-xl font-bold text-petra-blue mb-6 flex items-center gap-3">
                        <span
                            class="flex items-center justify-center w-8 h-8 rounded-full bg-petra-blue text-white text-sm">2</span>
                        Detail Umum
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div class="space-y-6">
                            <!-- Nama Kegiatan -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Nama Kegiatan <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="event_name" required placeholder="Contoh: Praktikum Jarkom"
                                    class="form-input w-full">
                            </div>

                            <!-- Tanggal Mulai -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Mulai Pinjam <span class="text-red-500">*</span>
                                </label>
                                <input type="datetime-local" id="borrowed_at" required class="form-input w-full">
                            </div>

                            <!-- Nomor WhatsApp -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    No. WhatsApp <span class="text-red-500">*</span>
                                </label>
                                <input type="tel" id="phone_number" required pattern="^\d{10,20}$"
                                    placeholder="08123456789" class="form-input w-full">
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-6">
                            <!-- Tipe Penggunaan -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Tipe Penggunaan <span class="text-red-500">*</span>
                                </label>
                                <select id="type" required class="form-input w-full">
                                    <option value="0">Onsite</option>
                                    <option value="1">Remote</option>
                                    <option value="2">Keluar Lab</option>
                                </select>
                            </div>

                            <!-- Tanggal Selesai -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Selesai Pinjam <span class="text-red-500">*</span>
                                </label>
                                <input type="datetime-local" id="return_deadline_at" required class="form-input w-full">
                            </div>

                            <!-- Detail Akademik (Opsional) -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Detail Akademik (Opsional)
                                </label>
                                <textarea id="academic_detail" placeholder="Keterangan tambahan..." rows="3"
                                    class="form-input w-full"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Detail Spesifik -->
                <div id="booking-details">
                    <!-- Lab Booking Section -->
                    <div id="lab-section" class="booking-section">
                        <h3 class="text-xl font-bold text-petra-blue mb-6 flex items-center gap-3">
                            <span
                                class="flex items-center justify-center w-8 h-8 rounded-full bg-petra-blue text-white text-sm">3</span>
                            Detail Laboratorium
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Pilih Lab -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Pilih Laboratorium <span class="text-red-500">*</span>
                                </label>
                                <select id="lab-select" class="form-input w-full">
                                    <option value="">-- Pilih Lab --</option>
                                </select>
                            </div>

                            <!-- Jumlah Peserta -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Jumlah Peserta <span class="text-red-500">*</span>
                                </label>
                                <input type="number" id="attendee_count" min="1" placeholder="Contoh: 20"
                                    class="form-input w-full">
                            </div>
                        </div>

                        <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-100">
                            <div class="flex items-start gap-3">
                                <i class="fa-solid fa-info-circle text-blue-600 mt-1"></i>
                                <div>
                                    <p class="text-sm text-blue-800">
                                        <span class="font-semibold">Catatan:</span> Peminjaman lab mencakup akses ke
                                        semua fasilitas dalam ruangan selama periode yang dipilih.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Set Booking Section -->
                    <div id="sets-section" class="booking-section hidden">
                        <h3 class="text-xl font-bold text-petra-blue mb-6 flex items-center gap-3">
                            <span
                                class="flex items-center justify-center w-8 h-8 rounded-full bg-petra-blue text-white text-sm">3</span>
                            Detail Set Lengkap
                        </h3>

                        <div class="mb-6">
                            <div class="flex gap-2">
                                <select id="set-lab-select" class="form-input flex-1">
                                    <option value="">-- Pilih Lab --</option>
                                </select>
                                <button type="button" id="add-set-lab-btn"
                                    class="px-6 bg-petra-yellow text-petra-blue font-semibold rounded-lg hover:bg-yellow-400 transition">
                                    Tambah Lab
                                </button>
                            </div>
                        </div>

                        <!-- Lab List Container -->
                        <div id="set-labs-container" class="space-y-4">
                            <!-- Dynamic content will be added here -->
                        </div>

                        <div class="mt-6 p-4 bg-yellow-50 rounded-lg border border-yellow-100">
                            <div class="flex items-start gap-3">
                                <i class="fa-solid fa-lightbulb text-yellow-600 mt-1"></i>
                                <div>
                                    <p class="text-sm text-yellow-800">
                                        <span class="font-semibold">Tip:</span> Set lengkap mencakup 1 unit PC +
                                        Monitor. Anda bisa meminjam dari beberapa lab sekaligus.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Item Booking Section -->
                    <div id="items-section" class="booking-section hidden">
                        <h3 class="text-xl font-bold text-petra-blue mb-6 flex items-center gap-3">
                            <span
                                class="flex items-center justify-center w-8 h-8 rounded-full bg-petra-blue text-white text-sm">3</span>
                            Pilih Item
                        </h3>

                        <!-- Lab Selection -->
                        <div class="mb-8">
                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                Pilih Laboratorium <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-3">
                                <select id="item-lab-select" class="form-input flex-1">
                                    <option value="">-- Pilih Lab --</option>
                                </select>
                                <button type="button" id="browse-items-btn"
                                    class="px-6 bg-petra-blue text-white font-semibold rounded-lg hover:bg-blue-800 transition">
                                    Tampilkan Denah
                                </button>
                            </div>
                        </div>

                        <!-- Split Layout for Desk Map and Items -->
                        <div id="item-selection-container" class="hidden">
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                <!-- Left: Desk Map -->
                                <div class="lg:col-span-2">
                                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                                        <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex justify-between items-center flex-wrap gap-2">
                                            <div>
                                                <h4 class="font-bold text-gray-700">
                                                    Denah & Ketersediaan
                                                    <span id="selected-lab-name" class="text-petra-blue ml-2"></span>
                                                </h4>
                                                <div class="flex flex-wrap gap-3 mt-1 text-xs text-gray-600">
                                                    <div class="flex items-center gap-1">
                                                        <div class="w-2 h-2 rounded-full bg-green-500"></div>Ada
                                                    </div>
                                                    <div class="flex items-center gap-1">
                                                        <div class="w-2 h-2 rounded-full bg-red-400"></div>Penuh
                                                    </div>
                                                    <div class="flex items-center gap-1">
                                                        <div class="w-2 h-2 rounded-full bg-blue-600"></div>Pilih
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="flex items-center bg-white rounded-lg border border-gray-300 shadow-sm p-1">
                                                <button type="button" onclick="adjustZoom(-0.1)" class="p-1.5 hover:bg-gray-100 rounded text-gray-600" title="Zoom Out">
                                                    <i class="fa-solid fa-magnifying-glass-minus"></i>
                                                </button>
                                                <span id="zoom-level-text" class="text-xs font-mono w-12 text-center text-gray-500 select-none">100%</span>
                                                <button type="button" onclick="adjustZoom(0.1)" class="p-1.5 hover:bg-gray-100 rounded text-gray-600" title="Zoom In">
                                                    <i class="fa-solid fa-magnifying-glass-plus"></i>
                                                </button>
                                                <div class="w-px h-4 bg-gray-300 mx-1"></div>
                                                <button type="button" onclick="resetZoom()" class="p-1.5 hover:bg-gray-100 rounded text-gray-600" title="Reset Zoom">
                                                    <i class="fa-solid fa-rotate"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="p-4 relative">
                                            <div class="overflow-auto overscroll-auto border border-gray-200 rounded-lg bg-gray-50 relative" id="map-scroll-container">

                                                <div id="map-zoom-wrapper" class="origin-top-left transition-transform duration-200 ease-out p-4 min-w-fit min-h-fit">
                                                    <div id="desk-map-container" class="min-w-max">
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right: Item List -->
                                <div class="lg:col-span-1">
                                    <div class="bg-white rounded-xl border border-gray-200 h-full flex flex-col">
                                        <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                                            <h4 class="font-bold text-gray-700 flex items-center justify-between">
                                                <span>Item pada Meja: <span id="selected-desk"
                                                        class="text-petra-blue">-</span></span>
                                                <span
                                                    class="text-xs font-normal bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                                                    <span id="available-count">0</span> tersedia
                                                </span>
                                            </h4>
                                        </div>
                                        <div id="item-list-container"
                                            class="flex-1 overflow-y-auto overscroll-auto p-4 space-y-3">
                                            <div class="text-center py-8 text-gray-400">
                                                <i class="fa-solid fa-table-cells-large text-3xl mb-3"></i>
                                                <p>Pilih meja untuk melihat item</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Cart Section -->
                            <div class="mt-8">
                                <div class="bg-white rounded-xl border border-gray-200">
                                    <div
                                        class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                                        <h4 class="font-bold text-gray-700">
                                            Keranjang Item
                                            <span id="cart-count"
                                                class="ml-2 bg-petra-blue text-white px-2 py-1 rounded-full text-xs">0</span>
                                        </h4>
                                        <button type="button" id="clear-cart-btn"
                                            class="text-sm text-red-600 hover:text-red-800">
                                            <i class="fa-solid fa-trash mr-1"></i> Kosongkan
                                        </button>
                                    </div>
                                    <div id="cart-container" class="p-4">
                                        <div id="cart-items" class="space-y-2">
                                            <div class="text-center py-8 text-gray-400">
                                                <i class="fa-solid fa-shopping-cart text-3xl mb-3"></i>
                                                <p>Belum ada item di keranjang</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="mt-12 pt-8 border-t border-gray-200">
                    <button type="button" id="submit-btn"
                        class="w-full md:w-auto px-12 py-4 bg-petra-blue text-white font-bold rounded-xl hover:bg-blue-800 transition-all shadow-lg hover:shadow-xl flex items-center justify-center gap-3 text-lg">
                        <i class="fa-solid fa-paper-plane"></i>
                        Ajukan Peminjaman
                    </button>

                    <p class="text-sm text-gray-500 mt-4 text-center md:text-left">
                        <i class="fa-solid fa-circle-info mr-1"></i>
                        Pastikan semua data yang Anda isi sudah benar sebelum mengajukan
                    </p>
                </div>
            </div>
        </div>

        <!-- Info Box -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                        <i class="fa-solid fa-headset text-xl text-petra-blue"></i>
                    </div>
                </div>
                <div>
                    <h4 class="font-bold text-lg text-petra-blue mb-2">Butuh Bantuan?</h4>
                    <p class="text-gray-600 mb-3">
                        Jika Anda mengalami kesulitan dalam mengisi formulir, silakan hubungi administrator
                        laboratorium:
                    </p>
                    <div class="flex flex-wrap gap-4 text-sm">
                        <span class="inline-flex items-center gap-2">
                            <i class="fa-solid fa-envelope text-petra-blue"></i>
                            <span>lab@petra.ac.id</span>
                        </span>
                        <span class="inline-flex items-center gap-2">
                            <i class="fa-solid fa-phone text-petra-blue"></i>
                            <span>(031) 1234-5678</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- @include('user.partials.footer') -->
@endsection

@section('script')
<script>
    const csrfToken = '{{ csrf_token() }}';
    let allLabs = [];
    let cart = [];
    let currentDeskMap = [];
    let selectedDeskIndex = null;
    let currentLabId = null;
    let currentZoom = 1;

    document.addEventListener('DOMContentLoaded', function() {
        initializeForm();
        setupEventListeners();
        loadLabs();

        // Set default dates
        const now = new Date();
        const tomorrow = new Date(now.getTime() + 24 * 60 * 60 * 1000);

        document.getElementById('borrowed_at').value = formatDateTime(now);
        document.getElementById('return_deadline_at').value = formatDateTime(tomorrow);
    });

    function formatDateTime(date) {
        return date.toISOString().slice(0, 16);
    }

    function initializeForm() {
        showSection('lab');

        document.querySelectorAll('input[name="booking_type"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const type = this.value;
                showSection(type);
            });
        });
    }

    function showSection(type) {
        ['lab', 'sets', 'items'].forEach(section => {
            const el = document.getElementById(`${section}-section`);
            if (el) {
                el.classList.add('hidden');
            }
        });

        const selectedSection = document.getElementById(`${type}-section`);
        if (selectedSection) {
            selectedSection.classList.remove('hidden');

            // Animate entrance
            gsap.fromTo(selectedSection, {
                opacity: 0,
                y: 20
            }, {
                opacity: 1,
                y: 0,
                duration: 0.3,
                ease: "power2.out"
            });
        }
    }


    function adjustZoom(delta) {
        const wrapper = document.getElementById('map-zoom-wrapper');
        const textDisplay = document.getElementById('zoom-level-text');

        if (!wrapper) return;

        currentZoom = Math.min(Math.max(currentZoom + delta, 0.5), 2.0);

        wrapper.style.transform = `scale(${currentZoom})`;

        textDisplay.textContent = `${Math.round(currentZoom * 100)}%`;

        if (currentZoom < 1) {
            wrapper.style.width = `${100 / currentZoom}%`;
        } else {
            wrapper.style.width = 'auto';
        }
    }

    function resetZoom() {
        const wrapper = document.getElementById('map-zoom-wrapper');
        const textDisplay = document.getElementById('zoom-level-text');

        if (!wrapper) return;

        currentZoom = 1;
        wrapper.style.transform = 'scale(1)';
        wrapper.style.width = 'auto';
        textDisplay.textContent = '100%';
    }

    async function loadLabs() {
        try {
            const response = await fetch('/get/labs');
            if (!response.ok) throw new Error('Failed to load labs');
            allLabs = await response.json();

            ['lab-select', 'set-lab-select', 'item-lab-select'].forEach(id => {
                const select = document.getElementById(id);
                if (select) {
                    select.innerHTML = '<option value="">-- Pilih Lab --</option>';
                    allLabs.forEach(lab => {
                        select.innerHTML += `<option value="${lab.id}">${lab.name}</option>`;
                    });
                }
            });
        } catch (error) {
            console.error('Error loading labs:', error);
            showToast('Gagal memuat data lab', '', 'error');
        }
    }

    function setupEventListeners() {
        document.getElementById('add-set-lab-btn')?.addEventListener('click', addSetLab);
        document.getElementById('browse-items-btn')?.addEventListener('click', browseItems);
        document.getElementById('clear-cart-btn')?.addEventListener('click', clearCart);
        document.getElementById('submit-btn')?.addEventListener('click', submitBooking);

        window.addEventListener('resize', syncListHeight);

        ['event_name', 'phone_number', 'borrowed_at', 'return_deadline_at'].forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.addEventListener('blur', validateField);
            }
        });
    }

=    function validateField(e) {
        const field = e.target;
        const value = field.value.trim();
        let isValid = false;

        switch (field.id) {
            case 'event_name':
                isValid = value.length >= 3 && value.length <= 255;
                break;
            case 'phone_number':
                isValid = /^\d{10,20}$/.test(value);
                break;
            case 'borrowed_at':
                isValid = value && new Date(value) > new Date();
                break;
            case 'return_deadline_at':
                const borrowedAt = document.getElementById('borrowed_at').value;
                isValid = value && borrowedAt && new Date(value) > new Date(borrowedAt);
                break;
        }

        if (value === '') {
            field.classList.remove('border-red-500', 'border-green-500');
        } else {
            field.classList.remove('border-red-500', 'border-green-500');
            field.classList.add(isValid ? 'border-green-500' : 'border-red-500');
        }

        return isValid;
    }

    async function addSetLab() {
        const labId = document.getElementById('set-lab-select').value;
        if (!labId) {
            showToast('Pilih lab terlebih dahulu', '', 'warning');
            return;
        }

        const start = document.getElementById('borrowed_at').value;
        const end = document.getElementById('return_deadline_at').value;
        if (!start || !end) {
            showToast('Isi tanggal peminjaman terlebih dahulu', '', 'warning');
            return;
        }

        showLoadingToast('Memuat data set...');

        try {
            const response = await fetch(`/labs/${labId}/available-sets?start=${start}&end=${end}`);
            if (!response.ok) throw new Error('Failed to fetch');
            const data = await response.json();

            Swal.close();

            const lab = allLabs.find(l => l.id == labId);
            const container = document.getElementById('set-labs-container');

            const div = document.createElement('div');
            div.className = 'bg-gray-50 rounded-lg p-4 border border-gray-200';
            div.innerHTML = `
                    <div class="flex justify-between items-center mb-3">
                        <div>
                            <span class="font-bold text-gray-800">${lab.name}</span>
                            <span class="ml-2 text-sm text-gray-500">(Tersedia: ${data.available_count} set)</span>
                        </div>
                        <button type="button" 
                                onclick="removeSetLab(this)"
                                class="text-red-500 hover:text-red-700 p-1 rounded hover:bg-red-50">
                            <i class="fa-solid fa-times"></i>
                        </button>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Jumlah Set</label>
                        <div class="flex items-center gap-3">
                            <button type="button" onclick="adjustSetQuantity(this, -1)" class="w-8 h-8 rounded-full border border-gray-300 hover:bg-gray-100">-</button>
                            <input type="number" 
                                   data-lab-id="${labId}"
                                   value="1"
                                   min="1"
                                   max="${data.available_count}"
                                   class="form-input w-20 text-center">
                            <button type="button" onclick="adjustSetQuantity(this, 1)" class="w-8 h-8 rounded-full border border-gray-300 hover:bg-gray-100">+</button>
                        </div>
                    </div>
                `;

            container.appendChild(div);
            gsap.from(div, {
                opacity: 0,
                y: -10,
                duration: 0.3
            });

            showToast('Lab ditambahkan', '', 'success');

        } catch (error) {
            Swal.close();
            showToast('Gagal memuat data set', '', 'error');
        }
    }

    function adjustSetQuantity(button, delta) {
        const container = button.closest('.flex.items-center');
        const input = container.querySelector('input');
        let value = parseInt(input.value) || 0;
        const max = parseInt(input.max) || 99;
        const newValue = Math.max(1, Math.min(max, value + delta));
        input.value = newValue;
    }

    function removeSetLab(button) {
        const container = button.closest('.bg-gray-50');
        gsap.to(container, {
            opacity: 0,
            height: 0,
            margin: 0,
            padding: 0,
            duration: 0.3,
            onComplete: () => container.remove()
        });
    }

    async function browseItems() {
        const labId = document.getElementById('item-lab-select').value;
        if (!labId) {
            showToast('Pilih lab terlebih dahulu', '', 'warning');
            return;
        }

        const start = document.getElementById('borrowed_at').value;
        const end = document.getElementById('return_deadline_at').value;
        if (!start || !end) {
            showToast('Isi tanggal peminjaman terlebih dahulu', '', 'warning');
            return;
        }

        showLoadingToast('Memuat denah lab...');

        try {
            const response = await fetch(`/labs/${labId}/desk-map?start=${start}&end=${end}`);
            if (!response.ok) throw new Error('Failed to fetch');

            currentDeskMap = await response.json();
            currentLabId = labId;

            Swal.close();

            const container = document.getElementById('item-selection-container');
            container.classList.remove('hidden');
            gsap.from(container, {
                opacity: 0,
                y: 20,
                duration: 0.3
            });

            const lab = allLabs.find(l => l.id == labId);
            document.getElementById('selected-lab-name').textContent = lab.name;

            renderDeskMap();

        } catch (error) {
            Swal.close();
            showToast('Gagal memuat denah lab', '', 'error');
        }
    }

    function renderDeskMap() {
        const container = document.getElementById('desk-map-container');
        if (!currentDeskMap || currentDeskMap.length === 0) {
            container.innerHTML = '<div class="text-center py-12 text-gray-400">Tidak ada data meja tersedia</div>';
            return;
        }

        resetZoom();

        let maxRow = 0,
            maxCol = 0;
        currentDeskMap.forEach(desk => {
            if (!desk.location || desk.location.length < 2) return;
            const row = desk.location.charCodeAt(0) - 64;
            const col = parseInt(desk.location.substring(1));
            if (row > maxRow) maxRow = row;
            if (col > maxCol) maxCol = col;
        });

        let gridHTML = `<div class="desk-grid-container" style="grid-template-columns: repeat(${maxCol}, minmax(120px, 1fr));">`;

        const occupiedSlots = new Set(currentDeskMap.map(d => d.location));

        currentDeskMap.forEach((desk, index) => {
            const row = desk.location.charCodeAt(0) - 64;
            const col = parseInt(desk.location.substring(1));

            const availableItems = desk.items.filter(i => i.available).length;
            const totalItems = desk.items.length;

            let statusClass = 'available';
            let statusText = '';
            let clickable = true;

            if (totalItems === 0) {
                statusClass = 'empty';
                statusText = '<span class="text-xs text-gray-400">Kosong</span>';
                clickable = false;
            } else if (availableItems === 0) {
                statusClass = 'full';
                statusText = '<span class="text-xs text-red-500 font-semibold">Penuh</span>';
            } else {
                statusText = `
                        <div class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">
                            ${availableItems} tersedia
                        </div>
                    `;
            }

            const deskData = encodeURIComponent(JSON.stringify(desk));
            const onClick = clickable ? `onclick="selectDesk(this, ${index}, '${deskData}')"` : '';
            const cursorClass = clickable ? 'cursor-pointer' : 'cursor-default';

            gridHTML += `
                    <div style="grid-area: ${row} / ${col}"
                         class="desk-card ${statusClass} ${cursorClass}"
                         ${onClick}>
                        <div class="text-lg font-bold mb-2">${desk.location}</div>
                        ${statusText}
                    </div>
                `;
        });

        for (let r = 1; r <= maxRow; r++) {
            for (let c = 1; c <= maxCol; c++) {
                const loc = String.fromCharCode(64 + r) + c;
                if (!occupiedSlots.has(loc)) {
                    gridHTML += `<div class="desk-card empty" style="grid-area: ${r} / ${c};"></div>`;
                }
            }
        }

        gridHTML += '</div>';
        container.innerHTML = gridHTML;

        setTimeout(syncListHeight, 50);

        // // Animate desks
        // gsap.from('.desk-card', {
        //     scale: 0.8,
        //     opacity: 0,
        //     duration: 0.3,
        //     stagger: 0.05,
        //     ease: "back.out(1.2)"
        // });
    }

    function selectDesk(element, index, deskEncoded) {
        const desk = JSON.parse(decodeURIComponent(deskEncoded));

        document.querySelectorAll('.desk-card').forEach(card => {
            card.classList.remove('selected');
        });

        if (element) {
            element.classList.add('selected');
        }

        document.getElementById('selected-desk').textContent = desk.location;
        selectedDeskIndex = index;

        populateItemList(desk);
    }

    function populateItemList(desk) {
        const container = document.getElementById('item-list-container');
        const availableCount = desk.items.filter(i => i.available).length;

        document.getElementById('available-count').textContent = availableCount;

        if (desk.items.length === 0) {
            container.innerHTML = `
                    <div class="text-center py-8 text-gray-400">
                        <i class="fa-solid fa-ban text-3xl mb-3"></i>
                        <p>Tidak ada item pada meja ini</p>
                    </div>
                `;
            return;
        }

        let itemsHTML = '<div class="space-y-3">';

        desk.items.forEach((item, idx) => {
            const isAvailable = item.available;
            const inCart = cart.some(c => c.id === item.id);
            const itemData = encodeURIComponent(JSON.stringify(item));

            let itemClass = 'item-card';
            let statusBadge = '';
            let actionButton = '';

            if (!isAvailable) {
                itemClass += ' unavailable';
                statusBadge = '<span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded-full">Dipakai</span>';
            } else if (inCart) {
                itemClass += ' selected';
                statusBadge = '<span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full">Dipilih</span>';
                actionButton = `
                        <button onclick="removeFromCart('${item.id}')" 
                                class="text-sm text-red-600 hover:text-red-800">
                            <i class="fa-solid fa-trash mr-1"></i> Hapus
                        </button>
                    `;
            } else {
                statusBadge = '<span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Tersedia</span>';
                actionButton = `
                        <button onclick="addToCart('${itemData}')" 
                                class="px-3 py-1 bg-petra-blue text-white rounded-lg hover:bg-blue-800 text-sm">
                            <i class="fa-solid fa-plus mr-1"></i> Tambah
                        </button>
                    `;
            }

            itemsHTML += `
                    <div class="${itemClass}">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <div class="font-semibold text-gray-800">${item.name}</div>
                                <div class="text-xs text-gray-500 font-mono mt-1">${item.serial_code || 'No Serial'}</div>
                            </div>
                            <div class="flex items-center gap-2">
                                ${statusBadge}
                                ${isAvailable && !inCart ? actionButton : ''}
                            </div>
                        </div>

                        ${item.specifications && item.specifications.length > 0 ? `
                            <div class="text-xs text-gray-600 mt-2">
                                <div class="font-semibold mb-1">Spesifikasi:</div>
                                ${item.specifications.map(spec =>
                    `<div>${spec.name}: ${spec.value}</div>`
                ).join('')}
                            </div>
                        ` : ''}

                        ${!isAvailable ? '<div class="text-xs text-red-500 mt-2">Item sedang dipinjam</div>' : ''}
                    </div>
                `;
        });

        itemsHTML += '</div>';
        container.innerHTML = itemsHTML;

        // Animate items
        // gsap.from('.item-card', {
        //     opacity: 0,
        //     y: 10,
        //     duration: 0.2,
        //     stagger: 0.05
        // });
    }

    function addToCart(itemEncoded) {
        const item = JSON.parse(decodeURIComponent(itemEncoded));

        if (cart.some(c => c.id === item.id)) {
            showToast('Item sudah ada di keranjang', '', 'info');
            return;
        }

        cart.push(item);
        updateCartDisplay();
        refreshItemList();

        showToast(`${item.name} ditambahkan ke keranjang`, '', 'success');
    }

    function removeFromCart(itemId) {
        cart = cart.filter(item => item.id !== itemId);
        updateCartDisplay();
        refreshItemList();

        showToast('Item dihapus dari keranjang', '', 'info');
    }

    function clearCart() {
        if (cart.length === 0) return;

        Swal.fire({
            title: 'Kosongkan Keranjang?',
            text: 'Semua item di keranjang akan dihapus',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#1a237e',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Kosongkan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                cart = [];
                updateCartDisplay();
                refreshItemList();
                showToast('Keranjang dikosongkan', '', 'success');
            }
        });
    }

    function updateCartDisplay() {
        const countElement = document.getElementById('cart-count');
        const container = document.getElementById('cart-items');

        if (countElement) {
            countElement.textContent = cart.length;
        }

        if (cart.length === 0) {
            container.innerHTML = `
                    <div class="text-center py-8 text-gray-400">
                        <i class="fa-solid fa-shopping-cart text-3xl mb-3"></i>
                        <p>Belum ada item di keranjang</p>
                    </div>
                `;
            return;
        }

        let cartHTML = '<div class="space-y-2">';

        cart.forEach(item => {
            cartHTML += `
                    <div class="cart-item flex justify-between items-center">
                        <div class="flex-1">
                            <div class="font-medium text-gray-800">${item.name}</div>
                            <div class="text-xs text-gray-500">${item.serial_code || 'No Serial'}</div>
                        </div>
                        <button onclick="removeFromCart('${item.id}')" 
                                class="text-red-500 hover:text-red-700 ml-2">
                            <i class="fa-solid fa-times"></i>
                        </button>
                    </div>
                `;
        });

        cartHTML += '</div>';
        container.innerHTML = cartHTML;
    }

    function refreshItemList() {
        if (selectedDeskIndex !== null && currentDeskMap[selectedDeskIndex]) {
            populateItemList(currentDeskMap[selectedDeskIndex]);
        }
    }

    async function submitBooking() {
        if (!validateForm()) return;

        const bookingType = document.querySelector('input[name="booking_type"]:checked').value;
        const payload = buildPayload(bookingType);

        if (!payload) return;

        const submitBtn = document.getElementById('submit-btn');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i> Memproses...';

        showLoadingToast('Mengajukan peminjaman...');

        try {
            const response = await fetch('/booking', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(payload)
            });

            const data = await response.json();
            Swal.close();

            if (data.success) {
                await Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Peminjaman berhasil diajukan',
                    confirmButtonColor: '#1a237e'
                });

                window.location.href = "{{ route('user.booking.history') }}";
            } else {
                throw new Error(data.message || 'Pengajuan gagal');
            }

        } catch (error) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;

            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: error.message,
                confirmButtonColor: '#1a237e'
            });
        }
    }

    function validateForm() {
        const requiredFields = [
            'event_name',
            'borrowed_at',
            'return_deadline_at',
            'phone_number'
        ];

        for (const fieldId of requiredFields) {
            const field = document.getElementById(fieldId);
            if (!field.value.trim()) {
                showToast(`Harap isi ${field.previousElementSibling?.textContent || 'field ini'}`, '', 'warning');
                field.focus();
                return false;
            }
        }

        const phone = document.getElementById('phone_number').value;
        if (!/^\d{10,20}$/.test(phone)) {
            showToast('Nomor WhatsApp harus 10-20 digit angka', '', 'warning');
            return false;
        }

        const start = new Date(document.getElementById('borrowed_at').value);
        const end = new Date(document.getElementById('return_deadline_at').value);

        if (start >= end) {
            showToast('Waktu selesai harus setelah waktu mulai', '', 'warning');
            return false;
        }

        const bookingType = document.querySelector('input[name="booking_type"]:checked').value;

        switch (bookingType) {
            case 'lab':
                if (!document.getElementById('lab-select').value) {
                    showToast('Pilih laboratorium', '', 'warning');
                    return false;
                }
                if (!document.getElementById('attendee_count').value ||
                    document.getElementById('attendee_count').value < 1) {
                    showToast('Isi jumlah peserta', '', 'warning');
                    return false;
                }
                break;

            case 'sets':
                const setInputs = document.querySelectorAll('input[data-lab-id]');
                if (setInputs.length === 0) {
                    showToast('Tambahkan minimal 1 lab', '', 'warning');
                    return false;
                }
                break;

            case 'items':
                if (cart.length === 0) {
                    showToast('Tambahkan minimal 1 item ke keranjang', '', 'warning');
                    return false;
                }
                break;
        }

        return true;
    }

    function buildPayload(bookingType) {
        const start = document.getElementById('borrowed_at').value;
        const end = document.getElementById('return_deadline_at').value;

        const basePayload = {
            booking_type: bookingType,
            event_name: document.getElementById('event_name').value.trim(),

            borrowed_at: start,
            return_deadline_at: end,

            event_started_at: start, 
            event_ended_at: end, 

            phone_number: document.getElementById('phone_number').value.trim(),
            type: document.getElementById('type').value,
            academic_detail: document.getElementById('academic_detail').value.trim()
        };

        switch (bookingType) {
            case 'lab':
                basePayload.bookable_type = 'lab';
                basePayload.bookable_id = document.getElementById('lab-select').value;
                basePayload.attendee_count = parseInt(document.getElementById('attendee_count').value);
                break;

            case 'sets':
                const setInputs = document.querySelectorAll('input[data-lab-id]');
                basePayload.sets = Array.from(setInputs).map(input => ({
                    lab_id: input.dataset.labId,
                    quantity: parseInt(input.value) || 0
                }));
                break;

            case 'items':
                basePayload.items = cart.map(item => item.id);
                break;
        }

        return basePayload;
    }

    function showToast(title, text = '', icon = 'success') {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: icon,
            title: title,
            text: text,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    }

    function showLoadingToast(title) {
        Swal.fire({
            title: title,
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });
    }

    function syncListHeight() {
        const denah = document.getElementById('denah');
        const listContainer = document.getElementById('item-list-container');

        const listHeader = listContainer ? listContainer.previousElementSibling : null;

        if (denah && listContainer) {
            const denahHeight = denah.offsetHeight;

            const headerHeight = listHeader ? listHeader.offsetHeight : 0;

            const finalHeight = denahHeight - headerHeight;


            listContainer.style.height = `${finalHeight}px`;
            listContainer.style.minHeight = `200px`;
            listContainer.style.maxHeight = `${finalHeight}px`;
        }
    }
</script>
@endsection