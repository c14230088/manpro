@extends('layouts.user')

@section('title', 'Formulir Peminjaman')

@section('style')
    <style>
        /* --- 1. Custom Scrollbar --- */
        .custom-scroll {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 #f1f5f9;
        }
        .custom-scroll::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        .custom-scroll::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }
        .custom-scroll::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
            border: 2px solid #f1f5f9;
        }
        .custom-scroll::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* --- 2. Scroll Fix Helper --- */
        /* Class ini dikhususkan agar scroll hanya terjadi di dalam panel, tidak bocor ke body */
        .panel-scroll-area {
            overscroll-behavior: contain;
            overflow-y: auto;
            overflow-x: auto;
            height: 100%;
            width: 100%;
        }

        /* --- 3. Desk Grid Wrapper (Admin Style Logic) --- */
        .desk-grid-wrapper {
            display: grid;
            gap: 1rem;
            padding: 2rem;
            border: 2px solid #e2e8f0;
            border-radius: 0.75rem;
            background-color: #ffffff;
            min-width: fit-content;
        }
        
        /* Wrapper untuk Zooming */
        #desk-grid-scale-wrapper {
            transform-origin: center left;
            transition: transform 0.2s ease-out;
            margin: 0 auto;
            width: fit-content;
        }

        /* --- 4. Ghost Slots --- */
        .empty-slot {
            visibility: hidden;
            border: 2px dashed #f1f5f9;
            border-radius: 0.75rem;
        }

        /* --- 5. Desk Card Visuals --- */
        .desk-card {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 9rem;
            border-width: 2px;
            border-radius: 0.75rem;
            padding: 1rem;
            position: relative;
            background-color: white;
            user-select: none;
        }
        .desk-card.available {
            border-color: #cbd5e1;
            color: #334155;
        }
        .desk-card.available:hover {
            border-color: #6366f1;
            transform: translateY(-4px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            z-index: 10;
        }
        .desk-card.active {
            border-color: #4f46e5 !important;
            background-color: #eef2ff !important;
            box-shadow: 0 0 0 4px #c7d2fe;
            z-index: 20;
            transform: translateY(-4px);
        }
        .desk-card.full {
            background-color: #fff1f2;
            border-color: #fda4af;
            color: #be123c;
            cursor: not-allowed;
            opacity: 0.9;
        }
        .desk-card.empty {
            background-color: #f8fafc;
            border-color: #e2e8f0;
            color: #94a3b8;
            cursor: default;
        }

        /* --- 6. Item Cards --- */
        .item-card-default { @apply border-gray-200 bg-white hover:border-indigo-300; }
        .item-card-selected { @apply border-emerald-500 bg-emerald-50 ring-1 ring-emerald-500; }
        .item-card-booked { @apply border-gray-100 bg-gray-100 opacity-60 cursor-not-allowed; }

        /* --- 7. Resizer & Zoom Controls --- */
        .resizing {
            cursor: col-resize;
            user-select: none;
        }
        .zoom-controls {
            position: absolute;
            bottom: 1.5rem;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 0.5rem;
            background: rgba(255, 255, 255, 0.9);
            padding: 0.5rem;
            border-radius: 9999px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
            z-index: 40;
            backdrop-filter: blur(4px);
        }
        .zoom-btn {
            width: 2.5rem;
            height: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 9999px;
            background-color: white;
            color: #475569;
            border: 1px solid #e2e8f0;
            cursor: pointer;
            transition: all 0.2s;
            font-weight: bold;
        }
        .zoom-btn:hover {
            background-color: #f1f5f9;
            color: #4f46e5;
            border-color: #cbd5e1;
        }
        .zoom-btn:active {
            transform: scale(0.95);
        }
    </style>
@endsection

@section('body')
    {{-- Form Utama --}}
    <div class="w-full min-h-screen bg-slate-50 p-4">
        <div class="max-w-6xl mx-auto">
            <div class="bg-white rounded-xl shadow-2xl overflow-hidden" id="main-card">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-700 p-6">
                    <h2 class="text-3xl font-bold text-white text-center">Formulir Peminjaman</h2>
                </div>

                <div class="p-8 space-y-6">
                    {{-- Tipe Peminjaman --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-3">Tipe Peminjaman</label>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <label class="cursor-pointer booking-type-label">
                                <input type="radio" name="booking_type" value="lab" class="peer sr-only">
                                <div class="p-4 border-2 rounded-lg peer-checked:border-indigo-600 peer-checked:bg-indigo-50 text-center transition-all hover:shadow-md">
                                    <span class="font-semibold">Laboratorium</span>
                                </div>
                            </label>
                            <label class="cursor-pointer booking-type-label">
                                <input type="radio" name="booking_type" value="sets" class="peer sr-only">
                                <div class="p-4 border-2 rounded-lg peer-checked:border-indigo-600 peer-checked:bg-indigo-50 text-center transition-all hover:shadow-md">
                                    <span class="font-semibold">Set Lengkap</span>
                                </div>
                            </label>
                            <label class="cursor-pointer booking-type-label">
                                <input type="radio" name="booking_type" value="items" class="peer sr-only">
                                <div class="p-4 border-2 rounded-lg peer-checked:border-indigo-600 peer-checked:bg-indigo-50 text-center transition-all hover:shadow-md">
                                    <span class="font-semibold">Item Individual</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Form Fields --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Mulai Pinjam <span class="text-red-500">*</span></label>
                            <input type="datetime-local" id="borrowed_at" required class="w-full border rounded p-2 focus:ring-2 focus:ring-indigo-500 transition cursor-pointer">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Selesai Pinjam <span class="text-red-500">*</span></label>
                            <input type="datetime-local" id="return_deadline_at" required class="w-full border rounded p-2 focus:ring-2 focus:ring-indigo-500 transition cursor-pointer">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-1">Nama Kegiatan <span class="text-red-500">*</span></label>
                            <input type="text" id="event_name" required maxlength="255" class="w-full border rounded p-2 focus:ring-2 focus:ring-indigo-500 transition">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Nomor WhatsApp <span class="text-red-500">*</span></label>
                            <input type="text" id="phone_number" required pattern="^\d{10,20}$" placeholder="08123456789" class="w-full border rounded p-2 focus:ring-2 focus:ring-indigo-500 transition">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Tipe Penggunaan <span class="text-red-500">*</span></label>
                            <select id="type" required class="w-full border rounded p-2 focus:ring-2 focus:ring-indigo-500 transition cursor-pointer">
                                <option value="0">Onsite</option>
                                <option value="1">Remote</option>
                                <option value="2">Keluar Lab</option>
                            </select>
                        </div>
                    </div>

                    {{-- Dynamic Sections --}}
                    <div id="lab-section" class="hidden space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">Pilih Laboratorium <span class="text-red-500">*</span></label>
                            <select id="lab-select" class="w-full border rounded p-2 focus:ring-2 focus:ring-indigo-500 transition cursor-pointer">
                                <option value="">-- Pilih Lab --</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Jumlah Peserta <span class="text-red-500">*</span></label>
                            <input type="number" id="attendee_count" min="1" class="w-full border rounded p-2 focus:ring-2 focus:ring-indigo-500 transition">
                        </div>
                    </div>

                    <div id="sets-section" class="hidden space-y-4">
                        <div class="flex gap-2">
                            <select id="set-lab-select" class="flex-1 border rounded p-2 focus:ring-2 focus:ring-indigo-500 transition cursor-pointer">
                                <option value="">-- Pilih Lab --</option>
                            </select>
                            <button type="button" id="add-set-lab-btn" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition">Tambah</button>
                        </div>
                        <div id="set-labs-container" class="space-y-3"></div>
                    </div>

                    <div id="items-section" class="hidden space-y-4">
                        <div class="flex gap-2">
                            <select id="item-lab-select" class="flex-1 border rounded p-2 focus:ring-2 focus:ring-indigo-500 transition cursor-pointer">
                                <option value="">-- Pilih Lab --</option>
                            </select>
                            <button type="button" id="browse-items-btn" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition">Browse Items</button>
                        </div>
                        <div id="cart-container" class="border rounded p-4 bg-gray-50">
                            <div class="flex justify-between items-center mb-3">
                                <h3 class="font-bold text-gray-800">Keranjang Item</h3>
                                <span class="bg-indigo-100 text-indigo-800 text-xs font-medium px-2.5 py-0.5 rounded-full"><span id="cart-count">0</span> items</span>
                            </div>
                            <div id="cart-items" class="space-y-2 max-h-60 overflow-y-auto custom-scroll pr-2">
                                <p class="text-gray-500 text-sm text-center py-2">Keranjang masih kosong</p>
                            </div>
                        </div>
                    </div>

                    <button type="button" id="submit-btn" class="w-full py-3 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 transition shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        Ajukan Peminjaman
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- UNIFIED DESK MAP MODAL --}}
    <div id="desk-map-modal" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        {{-- Wrapper Modal dengan Fixed Height --}}
        <div class="bg-white rounded-xl w-full max-w-[95vw] h-[90vh] flex flex-col shadow-2xl overflow-hidden relative" id="desk-map-content">

            {{-- Header (Fixed Height) --}}
            <div class="bg-white border-b px-6 py-4 flex justify-between items-center z-20 shadow-sm shrink-0 select-none h-[80px]">
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Layout: <span id="modal-lab-name" class="text-indigo-600"></span></h3>
                    <p class="text-sm text-gray-500">Pilih meja untuk melihat item.</p>
                </div>

                <div class="flex items-center gap-4">
                    {{-- Legend --}}
                    <div class="hidden md:flex items-center gap-3 text-xs text-gray-600 bg-gray-50 px-3 py-1.5 rounded-lg border">
                        <div class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-emerald-500"></span> Tersedia</div>
                        <div class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-rose-400"></span> Penuh</div>
                        <div class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-indigo-600 border border-indigo-200"></span> Dipilih</div>
                        <div class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-slate-300"></span> Kosong</div>
                    </div>
                    <button type="button" id="close-modal-btn" class="text-gray-400 hover:text-gray-600 transition p-1 hover:bg-gray-100 rounded-full cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Split Layout: min-h-0 penting agar flex item bisa scroll sendiri --}}
            <div class="flex-1 min-h-0 flex overflow-hidden relative" id="split-container">

                {{-- LEFT PANEL: GRID MAP --}}
                <div id="left-panel" class="w-[65%] h-full flex flex-col min-w-[300px] relative bg-slate-100 group">
                    {{-- Scroll Area khusus panel kiri --}}
                    <div class="panel-scroll-area p-8 flex items-start justify-start" id="map-scroll-container">
                        {{-- Wrapper untuk Scaling/Zooming --}}
                        <div id="desk-grid-scale-wrapper">
                             <div id="desk-grid-container">
                                {{-- JS akan merender grid di sini --}}
                             </div>
                        </div>
                    </div>

                    {{-- Zoom Controls --}}
                    <div class="zoom-controls">
                        <button type="button" class="zoom-btn" onclick="zoomOut()" title="Zoom Out">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" /></svg>
                        </button>
                        <button type="button" class="zoom-btn text-xs font-mono" onclick="resetZoom()" title="Reset Zoom" id="zoom-level-text">
                            100%
                        </button>
                        <button type="button" class="zoom-btn" onclick="zoomIn()" title="Zoom In">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        </button>
                    </div>
                </div>

                {{-- RESIZER HANDLE --}}
                <div id="drag-handle" class="w-[6px] bg-gray-200 hover:bg-indigo-500 active:bg-indigo-600 cursor-col-resize flex items-center justify-center z-30 transition-colors shadow-sm select-none">
                    <div class="h-8 w-[2px] bg-gray-400 rounded"></div>
                </div>

                {{-- RIGHT PANEL: ITEM DETAILS --}}
                <div id="right-panel" class="flex-1 h-full flex flex-col bg-white min-w-[350px] shadow-[inset_4px_0_6px_-1px_rgba(0,0,0,0.05)] z-10">
                    <div class="p-4 bg-white border-b sticky top-0 z-10 shadow-sm">
                        <h4 class="font-bold text-gray-700 flex items-center gap-2 truncate">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            <span class="truncate">Item Meja: <span id="detail-desk-name" class="text-indigo-600">--</span></span>
                        </h4>
                    </div>

                    {{-- Scroll Area khusus panel kanan --}}
                    <div id="item-list-container" class="panel-scroll-area p-4 space-y-3 custom-scroll">
                        <div class="h-full flex flex-col items-center justify-center text-gray-400 text-center px-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mb-2 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <p class="text-sm">Klik meja di peta sebelah kiri untuk melihat item.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        const csrfToken = '{{ csrf_token() }}';
        let allLabs = [];
        let cart = [];
        let currentDeskMap = [];
        let selectedDeskId = null;

        // --- ZOOM VARIABLES ---
        let currentZoom = 1;
        const MIN_ZOOM = 0.4;
        const MAX_ZOOM = 2.0;

        document.addEventListener('DOMContentLoaded', async () => {
            gsap.from('#main-card', {
                duration: 0.6,
                y: 30,
                opacity: 0,
                ease: 'power3.out'
            });

            await loadLabs();
            setupEventListeners();
            setupKeyboardNav();
            setupFieldValidation();
            setupResizer();
            updateCart();
        });

        // --- ZOOM FUNCTIONS ---
        function updateZoomTransform() {
            const wrapper = document.getElementById('desk-grid-scale-wrapper');
            const textLabel = document.getElementById('zoom-level-text');
            if(wrapper) {
                wrapper.style.transform = `scale(${currentZoom})`;
            }
            if(textLabel) {
                textLabel.textContent = `${Math.round(currentZoom * 100)}%`;
            }
        }

        function zoomIn() {
            if (currentZoom < MAX_ZOOM) {
                currentZoom += 0.1;
                updateZoomTransform();
            }
        }

        function zoomOut() {
            if (currentZoom > MIN_ZOOM) {
                currentZoom -= 0.1;
                updateZoomTransform();
            }
        }

        function resetZoom() {
            currentZoom = 1;
            updateZoomTransform();
        }

        // --- CORE FUNCTIONS ---

        async function loadLabs() {
            try {
                const response = await fetch('/get/labs');
                if (!response.ok) throw new Error('Failed to load labs');
                allLabs = await response.json();
                ['lab-select', 'set-lab-select', 'item-lab-select'].forEach(id => {
                    const select = document.getElementById(id);
                    select.innerHTML = '<option value="">-- Pilih Lab --</option>';
                    allLabs.forEach(lab => {
                        select.innerHTML += `<option value="${lab.id}">${lab.name}</option>`;
                    });
                });
            } catch (error) {
                showToast('Gagal memuat data lab', '', 'error');
            }
        }

        function setupEventListeners() {
            document.querySelectorAll('input[name="booking_type"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    ['lab-section', 'sets-section', 'items-section'].forEach(id => {
                        const el = document.getElementById(id);
                        if (el.classList.contains('hidden')) return;
                        gsap.to(el, {
                            duration: 0.3,
                            opacity: 0,
                            height: 0,
                            onComplete: () => el.classList.add('hidden')
                        });
                    });
                    const targetId = this.value === 'lab' ? 'lab-section' : this.value === 'sets' ? 'sets-section' : 'items-section';
                    const target = document.getElementById(targetId);
                    target.classList.remove('hidden');
                    gsap.fromTo(target, { opacity: 0, height: 0 }, { duration: 0.3, opacity: 1, height: 'auto', clearProps: 'all' });
                });
            });
            document.getElementById('add-set-lab-btn').addEventListener('click', addSetLab);
            document.getElementById('browse-items-btn').addEventListener('click', browseItems);
            document.getElementById('submit-btn').addEventListener('click', submitBooking);
            document.getElementById('close-modal-btn').addEventListener('click', () => closeModal('desk-map-modal'));
            document.getElementById('desk-map-modal').addEventListener('click', (e) => {
                if (e.target.id === 'desk-map-modal') closeModal('desk-map-modal');
            });
        }

        function setupResizer() {
            const resizer = document.getElementById('drag-handle');
            const leftPanel = document.getElementById('left-panel');
            const container = document.getElementById('split-container');
            let isResizing = false;
            resizer.addEventListener('mousedown', () => {
                isResizing = true;
                document.body.classList.add('resizing');
                resizer.classList.add('bg-indigo-600');
            });
            document.addEventListener('mousemove', (e) => {
                if (!isResizing) return;
                const rect = container.getBoundingClientRect();
                const newW = e.clientX - rect.left;
                if (newW > 300 && newW < rect.width - 350) leftPanel.style.width = `${(newW / rect.width) * 100}%`;
            });
            document.addEventListener('mouseup', () => {
                isResizing = false;
                document.body.classList.remove('resizing');
                resizer.classList.remove('bg-indigo-600');
            });
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            const content = modal.querySelector('div[id$="-content"]');
            document.body.style.overflow = ''; // Restore body scroll
            gsap.to(content, {
                duration: 0.2,
                scale: 0.95,
                opacity: 0,
                onComplete: () => {
                    modal.classList.add('hidden');
                    gsap.set(content, { scale: 1, opacity: 1 });
                }
            });
        }

        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            const content = modal.querySelector('div[id$="-content"]');
            document.body.style.overflow = 'hidden'; // Prevent body scroll
            modal.classList.remove('hidden');
            gsap.fromTo(content, { scale: 0.95, opacity: 0 }, { duration: 0.3, scale: 1, opacity: 1, ease: 'back.out(1.2)' });
        }

        function setupKeyboardNav() {
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    if (!document.getElementById('desk-map-modal').classList.contains('hidden')) {
                        closeModal('desk-map-modal');
                    }
                }
            });
        }

        function setupFieldValidation() {
            const fields = ['borrowed_at', 'return_deadline_at', 'event_name', 'phone_number'];
            fields.forEach(id => {
                const field = document.getElementById(id);
                field.addEventListener('blur', () => validateField(field));
                field.addEventListener('input', () => validateField(field));
            });
        }

        function validateField(field) {
            const value = field.value.trim();
            let isValid = false;
            if (field.id === 'borrowed_at') {
                isValid = value && new Date(value) > new Date();
            } else if (field.id === 'return_deadline_at') {
                const borrowedAt = document.getElementById('borrowed_at').value;
                isValid = value && borrowedAt && new Date(value) > new Date(borrowedAt);
            } else if (field.id === 'event_name') {
                isValid = value.length > 0 && value.length <= 255;
            } else if (field.id === 'phone_number') {
                isValid = /^\d{10,20}$/.test(value);
            }
            field.classList.remove('border-red-500', 'border-green-500');
            if (value) field.classList.add(isValid ? 'border-green-500' : 'border-red-500');
            return isValid;
        }

        async function addSetLab() {
            const labId = document.getElementById('set-lab-select').value;
            if (!labId) return showToast('Pilih lab terlebih dahulu', '', 'warning');
            const start = document.getElementById('borrowed_at').value;
            const end = document.getElementById('return_deadline_at').value;
            if (!start || !end) return showToast('Isi tanggal peminjaman terlebih dahulu', '', 'warning');

            showLoadingToast('Memuat data set...');
            try {
                const response = await fetch(`/labs/${labId}/available-sets?start=${start}&end=${end}`);
                if (!response.ok) throw new Error('Failed to fetch');
                const data = await response.json();
                Swal.close();
                const lab = allLabs.find(l => l.id === labId);
                const container = document.getElementById('set-labs-container');
                const div = document.createElement('div');
                div.className = 'border rounded p-4 bg-white shadow-sm relative overflow-hidden';
                div.innerHTML = `
                <div class="flex justify-between items-center mb-2">
                    <span class="font-bold text-gray-800">${lab.name}</span>
                    <button type="button" class="text-red-500 hover:text-red-700 transition cursor-pointer p-1 rounded hover:bg-red-50" onclick="removeSetLab(this)">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </button>
                </div>
                <div>
                    <label class="text-sm text-gray-600 block mb-1">Jumlah Set (Tersedia: ${data.available_count})</label>
                    <input type="number" min="1" max="${data.available_count}" class="w-full border rounded p-2 focus:ring-2 focus:ring-indigo-500 transition" data-lab-id="${labId}" name="set_quantity" required>
                </div>`;
                container.appendChild(div);
                gsap.from(div, { duration: 0.3, x: -20, opacity: 0 });
                showToast('Lab ditambahkan', '', 'success');
            } catch (error) {
                Swal.close();
                showToast('Gagal memuat data set', '', 'error');
            }
        }

        function removeSetLab(btn) {
            const div = btn.closest('div.border');
            gsap.to(div, { duration: 0.3, x: 20, opacity: 0, height: 0, margin: 0, padding: 0, onComplete: () => div.remove() });
        }

        async function browseItems() {
            const labId = document.getElementById('item-lab-select').value;
            if (!labId) return showToast('Pilih lab terlebih dahulu', '', 'warning');
            const start = document.getElementById('borrowed_at').value;
            const end = document.getElementById('return_deadline_at').value;
            if (!start || !end) return showToast('Isi tanggal peminjaman terlebih dahulu', '', 'warning');

            showLoadingToast('Memuat layout lab...');
            try {
                const response = await fetch(`/labs/${labId}/desk-map?start=${start}&end=${end}`);
                if (!response.ok) throw new Error('Failed to fetch');
                currentDeskMap = await response.json();
                Swal.close();

                const lab = allLabs.find(l => l.id === labId);
                document.getElementById('modal-lab-name').textContent = lab.name;
                selectedDeskId = null;
                document.getElementById('detail-desk-name').textContent = "--";
                document.getElementById('item-list-container').innerHTML = `
                <div class="h-full flex flex-col items-center justify-center text-gray-400 text-center px-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mb-2 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <p class="text-sm">Klik meja di peta sebelah kiri untuk melihat item.</p>
                </div>`;
                
                renderDeskMap();
                openModal('desk-map-modal');
            } catch (error) {
                Swal.close();
                showToast('Gagal memuat desk map', '', 'error');
            }
        }

        // -------------------------------------------------------------
        // RENDER MAP LOGIC (ADMIN STYLE)
        // -------------------------------------------------------------
        function renderDeskMap() {
            const container = document.getElementById('desk-grid-container');
            container.innerHTML = '';

            if (!currentDeskMap || currentDeskMap.length === 0) {
                container.innerHTML = '<div class="text-center py-12 text-gray-400 italic">Data layout tidak tersedia.</div>';
                return;
            }

            let maxRow = 0, maxCol = 0;
            currentDeskMap.forEach(d => {
                if (!d.location || d.location.length < 2) return;
                const row = d.location.charCodeAt(0) - 64; 
                const col = parseInt(d.location.substring(1));
                if (row > maxRow) maxRow = row;
                if (col > maxCol) maxCol = col;
            });

            if (maxRow === 0 || maxCol === 0) {
                container.innerHTML = '<div class="text-center py-12 text-red-400">Format lokasi meja tidak valid.</div>';
                return;
            }

            let html = `<div class="overflow-x-auto pb-4 px-2">
                <div class="desk-grid-wrapper" 
                     style="grid-template-columns: repeat(${maxCol}, minmax(140px, 1fr)); 
                            grid-template-rows: repeat(${maxRow}, auto);">`;

            const occupiedSlots = new Set(currentDeskMap.map(d => d.location));

            currentDeskMap.forEach((desk, index) => {
                const row = desk.location.charCodeAt(0) - 64;
                const col = parseInt(desk.location.substring(1));
                
                const availableItems = desk.items.filter(i => i.available).length;
                const totalItems = desk.items.length;
                const deskJson = encodeURIComponent(JSON.stringify(desk));

                let cardTypeClass = 'available';
                let iconColorClass = 'text-gray-400 group-hover:text-indigo-500';
                let statusHtml = '';
                let clickHandler = `onclick="selectDesk(${index}, '${deskJson}')"`;
                let cursorClass = 'cursor-pointer';

                if (totalItems === 0) {
                    cardTypeClass = 'empty';
                    iconColorClass = 'text-slate-300';
                    statusHtml = '<span class="text-xs font-medium">Kosong</span>';
                    clickHandler = '';
                    cursorClass = 'cursor-default';
                } else if (availableItems === 0) {
                    cardTypeClass = 'full';
                    iconColorClass = 'text-rose-400';
                    statusHtml = '<span class="text-xs font-bold bg-rose-100 text-rose-700 px-2 py-0.5 rounded-full">Penuh</span>';
                } else {
                    statusHtml = `
                        <div class="flex items-center gap-1 bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded-full border border-emerald-100">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span class="text-xs font-bold">${availableItems} Tersedia</span>
                        </div>
                    `;
                }

                html += `
                    <div style="grid-area: ${row} / ${col};" 
                         id="desk-node-${index}"
                         class="desk-card ${cardTypeClass} ${cursorClass} group"
                         ${clickHandler}
                         title="Lokasi: ${desk.location}">
                        <div class="mb-3">
                             <svg class="w-10 h-10 ${iconColorClass} transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                             </svg>
                        </div>
                        <span class="font-extrabold text-xl mb-1 select-none text-gray-700">${desk.location}</span>
                        <div class="mt-1">${statusHtml}</div>
                    </div>
                `;
            });

            for (let r = 1; r <= maxRow; r++) {
                for (let c = 1; c <= maxCol; c++) {
                    const loc = String.fromCharCode(64 + r) + c;
                    if (!occupiedSlots.has(loc)) {
                        html += `<div class="empty-slot" style="grid-area: ${r} / ${c};"></div>`;
                    }
                }
            }

            html += '</div></div>';
            container.innerHTML = html;
            
            // RESET ZOOM SETIAP KALI RENDER MAP BARU
            resetZoom();

            gsap.from('#desk-grid-container .desk-card', {
                duration: 0.4,
                scale: 0.5,
                opacity: 0,
                stagger: { amount: 0.3, grid: [maxRow, maxCol], from: "start" },
                ease: 'back.out(1.2)',
                clearProps: 'all'
            });
        }

        function selectDesk(index, deskEncoded) {
            const desk = JSON.parse(decodeURIComponent(deskEncoded));
            if (desk.items.length === 0) return;

            document.querySelectorAll('.desk-card').forEach(el => el.classList.remove('active'));
            const currentEl = document.getElementById(`desk-node-${index}`);
            if (currentEl) currentEl.classList.add('active');

            selectedDeskId = index;
            document.getElementById('detail-desk-name').textContent = desk.location;
            populateItemPanel(desk);
        }

        function populateItemPanel(desk) {
            const listContainer = document.getElementById('item-list-container');
            let html = '<div class="space-y-3 pb-4">';
            
            desk.items.forEach((item, idx) => {
                const inCart = cart.some(c => c.id === item.id);
                const itemData = encodeURIComponent(JSON.stringify(item));
                let cardClass = '', statusBadge = '', actionBtn = '';

                if (!item.available) {
                    cardClass = 'item-card-booked';
                    statusBadge = '<span class="bg-gray-200 text-gray-600 text-[10px] font-bold px-2 py-0.5 rounded">FULL</span>';
                    actionBtn = '<span class="text-gray-400 text-xs italic font-medium">Booked</span>';
                } else if (inCart) {
                    cardClass = 'item-card-selected';
                    statusBadge = '<span class="bg-emerald-100 text-emerald-700 text-[10px] font-bold px-2 py-0.5 rounded">TERPILIH</span>';
                    actionBtn = `
                    <button onclick="removeFromCart('${item.id}')" class="p-2 bg-white border border-red-200 text-red-500 rounded-md hover:bg-red-50 hover:text-red-600 transition shadow-sm" title="Batalkan">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </button>`;
                } else {
                    cardClass = 'item-card-default';
                    statusBadge = '<span class="bg-blue-50 text-blue-600 text-[10px] font-bold px-2 py-0.5 rounded">TERSEDIA</span>';
                    actionBtn = `
                    <button onclick="addToCart('${itemData}')" class="px-3 py-1.5 bg-indigo-600 text-white rounded text-xs font-medium hover:bg-indigo-700 transition shadow-sm active:scale-95">
                        + Ambil
                    </button>`;
                }

                html += `
                <div class="border rounded-lg p-3 transition-all ${cardClass}">
                    <div class="flex justify-between items-start mb-2">
                        <div class="flex-1 pr-2">
                            <div class="flex items-center gap-2 mb-1"><span class="font-bold text-gray-800 text-sm">${item.name}</span>${statusBadge}</div>
                            <div class="text-xs text-gray-500 font-mono">${item.serial_code}</div>
                            ${item.components.length > 0 ? `<div class="text-xs text-indigo-500 mt-1 font-medium">+ ${item.components.length} components</div>` : ''}
                        </div>
                        <div class="flex items-center self-center">${actionBtn}</div>
                    </div>
                    <button onclick="toggleSpec('spec-${idx}', this)" class="text-xs text-gray-500 hover:text-indigo-600 flex items-center gap-1 w-full mt-2 pt-2 border-t border-dashed focus:outline-none"><span>Lihat Spesifikasi</span><svg class="w-3 h-3 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg></button>
                    <div id="spec-${idx}" class="hidden mt-2 text-xs bg-gray-50 p-2 rounded text-gray-600">
                        <p class="mb-1"><span class="font-semibold">Type:</span> ${item.type}</p>
                        <p class="mb-1"><span class="font-semibold">Specs:</span> ${item.specifications || '-'}</p>
                    </div>
                </div>`;
            });
            html += '</div>';
            listContainer.innerHTML = html;
            gsap.from('#item-list-container > div', { opacity: 0, y: 10, duration: 0.2 });
        }

        function toggleSpec(id, thiss) {
            const el = document.getElementById(id);
            if (el.classList.contains('hidden')) {
                gsap.to(thiss.lastElementChild, { rotate: 180, duration: .3 });
                el.classList.remove('hidden');
                gsap.from(el, { height: 0, opacity: 0, duration: 0.2 });
            } else {
                gsap.to(thiss.lastElementChild, { rotate: 0, duration: .3 });
                el.classList.add('hidden');
            }
        }

        function addToCart(itemEncoded) {
            const item = JSON.parse(decodeURIComponent(itemEncoded));
            if (cart.some(c => c.id === item.id)) return;
            cart.push(item);
            updateCart();
            refreshItemPanel();
            showToast(`${item.name} ditambahkan`, '', 'success');
        }

        function removeFromCart(itemId) {
            cart = cart.filter(c => c.id !== itemId);
            updateCart();
            refreshItemPanel();
        }

        function refreshItemPanel() {
            if (!document.getElementById('desk-map-modal').classList.contains('hidden') && selectedDeskId !== null) {
                const desk = currentDeskMap[selectedDeskId];
                populateItemPanel(desk);
            }
        }

        function updateCart() {
            const countEl = document.getElementById('cart-count');
            const container = document.getElementById('cart-items');
            if (countEl) countEl.textContent = cart.length;
            if (cart.length === 0) {
                container.innerHTML = '<p class="text-gray-500 text-sm text-center py-4 italic">Keranjang kosong</p>';
                return;
            }
            container.innerHTML = cart.map(item => `
            <div class="flex justify-between items-center border-b border-gray-100 pb-2 mb-2 last:border-0 last:mb-0 hover:bg-gray-50 p-1 rounded transition">
                <div class="overflow-hidden">
                    <div class="font-medium text-sm text-gray-800 truncate">${item.name}</div>
                    <div class="text-xs text-gray-500 font-mono">${item.serial_code}</div>
                </div>
                <button onclick="removeFromCart('${item.id}')" class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 p-1.5 rounded transition ml-2" title="Hapus">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                </button>
            </div>`).join('');
        }

        async function submitBooking() {
            const bookingType = document.querySelector('input[name="booking_type"]:checked')?.value;
            if (!bookingType) return showToast('Pilih tipe peminjaman', '', 'warning');
            const eventName = document.getElementById('event_name').value.trim();
            const borrowedAt = document.getElementById('borrowed_at').value;
            const returnDeadlineAt = document.getElementById('return_deadline_at').value;
            const phoneNumber = document.getElementById('phone_number').value.trim();
            const type = document.getElementById('type').value;

            if (!eventName || eventName.length > 255) return showToast('Nama kegiatan harus diisi (max 255 karakter)', '', 'warning');
            if (!borrowedAt || new Date(borrowedAt) <= new Date()) return showToast('Waktu mulai tidak valid', '', 'warning');
            if (!returnDeadlineAt || new Date(returnDeadlineAt) <= new Date(borrowedAt)) return showToast('Waktu selesai tidak valid', '', 'warning');
            if (!/^\d{10,20}$/.test(phoneNumber)) return showToast('Nomor WhatsApp harus 10-20 digit angka', '', 'warning');

            let payload = {
                booking_type: bookingType,
                event_name: eventName,
                borrowed_at: borrowedAt,
                return_deadline_at: returnDeadlineAt,
                phone_number: phoneNumber,
                type: type,
                event_started_at: borrowedAt,
                event_ended_at: returnDeadlineAt,
            };

            if (bookingType === 'lab') {
                const labId = document.getElementById('lab-select').value;
                const attendeeCount = document.getElementById('attendee_count').value;
                if (!labId) return showToast('Pilih laboratorium', '', 'warning');
                if (!attendeeCount || attendeeCount < 1) return showToast('Jumlah peserta minimal 1', '', 'warning');
                payload.bookable_type = 'lab';
                payload.bookable_id = labId;
                payload.attendee_count = parseInt(attendeeCount);
            } else if (bookingType === 'sets') {
                const setInputs = document.querySelectorAll('input[name="set_quantity"]');
                if (setInputs.length === 0) return showToast('Tambahkan minimal 1 lab', '', 'warning');
                payload.sets = Array.from(setInputs).map(input => ({
                    lab_id: input.dataset.labId,
                    quantity: parseInt(input.value) || 0
                }));
                if (payload.sets.some(s => s.quantity <= 0)) return showToast('Isi jumlah set', '', 'warning');
            } else if (bookingType === 'items') {
                if (cart.length === 0) return showToast('Keranjang kosong', '', 'warning');
                payload.items = cart.map(item => item.id);
            }

            const btn = document.getElementById('submit-btn');
            const originalText = btn.innerText;
            btn.disabled = true;
            btn.innerText = 'Sedang memproses...';
            showLoadingToast('Memproses peminjaman...');
            try {
                const response = await fetch('/booking', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify(payload)
                });
                const data = await response.json();
                Swal.close();
                if (data.success) {
                    await Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Booking berhasil diajukan', confirmButtonColor: '#4F46E5' });
                    window.location.reload();
                } else {
                    throw new Error(data.message || 'Booking gagal');
                }
            } catch (error) {
                btn.disabled = false;
                btn.innerText = originalText;
                Swal.fire({ icon: 'error', title: 'Gagal!', text: error.message, confirmButtonColor: '#4F46E5' });
            }
        }
    </script>
@endsection