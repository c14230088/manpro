@extends('layouts.admin')

@section('title', 'Manajemen Item')

@section('body')
    <div class="flex flex-col md:flex-row w-full py-4 shadow-md items-center justify-center mb-5 px-6 md:px-4">
        <h1 class="text-center text-4xl uppercase font-bold mb-2 md:mb-0">Items</h1>
    </div>

    {{-- Container Tabel Utama --}}
    <div class="max-w-7xl mx-auto px-6 pb-12">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b">
                <div class="flex w-full justify-between items-center">
                    <h2 class="text-xl font-semibold text-gray-800">Item Tersedia (Belum Terpasang)</h2>
                    <button id="open-create-modal-btn" type="button"
                        class="px-6 py-3 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors shadow-lg">
                        + Tambah Item
                    </button>
                </div>
                <p class="text-sm text-gray-500 mt-1">Daftar item yang ada di inventaris namun belum terpasang di meja
                    manapun.</p>
            </div>

            {{-- Tabel --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama
                                Item</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Serial Code</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Spesifikasi</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Kondisi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($items as $item)
                            <tr class="item-row hover:bg-gray-50 cursor-pointer" data-item-id="{{ $item->id }}"
                                data-item-name="{{ $item->name }}" data-item-condition="{{ $item->condition }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">{{ $item->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-600 font-mono">{{ $item->serial_code }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $item->type->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 max-w-xs truncate">
                                    @foreach ($item->specSetValues as $spec)
                                        <span class="font-semibold">{{ $spec->specAttributes->name }}:</span>
                                        {{ $spec->value }}@if (!$loop->last)
                                            ,
                                        @endif
                                    @endforeach
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($item->condition)
                                        <span
                                            class="condition-badge px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Bagus</span>
                                    @else
                                        <span
                                            class="condition-badge px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Rusak</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    Tidak ada item tersedia (unaffiliated) yang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($items->hasPages())
                <div class="p-6 bg-gray-50 border-t border-gray-200">
                    {{ $items->links() }}
                </div>
            @endif
        </div>
    </div>


    {{-- ========================================================= --}}
    {{-- MODAL 1: Buat Item Baru (Form Anda sebelumnya) --}}
    {{-- ========================================================= --}}
    <div id="create-item-modal" class="hidden" role="dialog" aria-modal="true" aria-labelledby="create-item-modal-title">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 z-[2000]"></div>
        <div id="create-item-modal-overlay" class="fixed inset-0 flex items-center justify-center p-4 z-[2001]">
            <div id="create-item-modal-area"
                class="relative bg-white rounded-lg shadow-xl w-full max-w-4xl flex flex-col max-h-[90vh]">

                <form id="create-item-form" data-action="{{ route('admin.items.create') }}"
                    class="space-y-0 flex flex-col max-h-[90vh]">
                    @csrf
                    {{-- Modal Header --}}
                    <div class="flex justify-between items-center p-4 border-b border-gray-200 rounded-t">
                        <h3 id="create-item-modal-title" class="text-xl font-semibold text-gray-900">
                            Input Item Baru
                        </h3>
                        <button id="create-item-modal-close-btn" type="button"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                            <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>

                    {{-- Modal Body (Scrollable) --}}
                    <div class="p-6 space-y-6 overflow-y-auto">
                        {{-- Card untuk Info Dasar --}}
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">
                            <h2 class="text-xl font-semibold text-gray-800 mb-6">Detail Barang</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- 1. Jenis Barang --}}
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Jenis Barang</label>
                                    <div class="flex items-center space-x-6">
                                        <label class="flex items-center">
                                            <input type="radio" name="is_component" value="0"
                                                class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500"
                                                checked>
                                            <span class="ml-2 text-gray-700">Item</span>
                                            <span class="ml-2 text-xs text-gray-400">(cth: Monitor, CPU)</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" name="is_component" value="1"
                                                class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                            <span class="ml-2 text-gray-700">Component</span>
                                            <span class="ml-2 text-xs text-gray-400">(cth: RAM, SSD)</span>
                                        </label>
                                    </div>
                                </div>
                                {{-- 2. Name --}}
                                <div>
                                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Nama
                                        Barang</label>
                                    <input type="text" id="name" name="name" required
                                        class="block w-full px-4 py-3 text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                        placeholder="cth: Monitor LG 24MP59G">
                                </div>
                                {{-- 3. Serial Code --}}
                                <div>
                                    <label for="serial_code" class="block text-sm font-semibold text-gray-700 mb-2">Serial
                                        Code</label>
                                    <input type="text" id="serial_code" name="serial_code" required
                                        class="block w-full px-4 py-3 text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                        placeholder="cth: SN-847291-B">
                                </div>
                                {{-- 4. Kondisi --}}
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Kondisi</label>
                                    <div class="flex items-center space-x-6">
                                        <label class="flex items-center">
                                            <input type="radio" name="condition" value="1"
                                                class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500"
                                                checked>
                                            <span class="ml-2 text-gray-700">Bagus</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" name="condition" value="0"
                                                class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                            <span class="ml-2 text-gray-700">Rusak</span>
                                        </label>
                                    </div>
                                </div>
                                {{-- 5. Type --}}
                                <div class="md:col-span-2">
                                    <label for="type-selector"
                                        class="block text-sm font-semibold text-gray-700 mb-2">Type</label>
                                    <select id="type-selector" name="type"
                                        placeholder="Cari atau tambah Type baru..."></select>
                                </div>
                            </div>
                        </div>
                        {{-- Card untuk Spesifikasi --}}
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-xl font-semibold text-gray-800">Spesifikasi</h2>
                                <button type="button" id="add-spec-btn"
                                    class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition-colors">
                                    + Tambah Spesifikasi
                                </button>
                            </div>
                            <div id="specifications-container" class="space-y-4">
                                {{-- Baris spesifikasi akan ditambahkan di sini oleh JS --}}
                            </div>
                        </div>
                    </div>

                    {{-- Modal Footer --}}
                    <div class="flex items-center justify-end p-4 space-x-3 border-t border-gray-200 rounded-b bg-gray-50">
                        <button id="create-item-modal-cancel-btn" type="button"
                            class="text-gray-700 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-indigo-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center border border-gray-300">
                            Batal
                        </button>
                        <button type="button" id="submit-btn"
                            class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center w-40 h-[42px] flex items-center justify-center">
                            <span class="btn-text">Simpan Item</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- MODAL 2: Aksi untuk Item --}}
    {{-- ========================================================= --}}
    <div id="action-modal" class="hidden" role="dialog" aria-modal="true" aria-labelledby="action-modal-title">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 z-[2000]"></div>
        <div id="action-modal-overlay" class="fixed inset-0 flex items-center justify-center p-4 z-[2001]">
            <div id="action-modal-area"
                class="relative bg-white rounded-lg shadow-xl w-full max-w-lg flex flex-col max-h-[90vh]">
                {{-- Header --}}
                <div class="flex justify-between items-center p-4 border-b border-gray-200 rounded-t">
                    <h3 id="action-modal-title" class="text-xl font-semibold text-gray-900">
                        Aksi untuk [Nama Item]
                    </h3>
                    <button id="action-modal-close-btn" type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                {{-- Body --}}
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <button id="action-btn-attach"
                        class="p-6 bg-blue-500 hover:bg-blue-600 text-white rounded-lg flex flex-col items-center justify-center transition-all shadow-lg">
                        <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z">
                            </path>
                        </svg>
                        <span class="font-semibold">Pasang ke Meja</span>
                    </button>
                    <button id="action-btn-condition"
                        class="p-6 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg flex flex-col items-center justify-center transition-all shadow-lg">
                        <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="font-semibold">Ubah Kondisi</span>
                    </button>
                    <button id="action-btn-manage-comp"
                        class="p-6 bg-gray-700 hover:bg-gray-800 text-white rounded-lg flex flex-col items-center justify-center transition-all shadow-lg">
                        <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                            </path>
                        </svg>
                        <span class="font-semibold">Kelola Komponen</span>
                    </button>
                    <button id="action-btn-edit-comp"
                        class="p-6 bg-gray-400 hover:bg-gray-500 text-white rounded-lg flex flex-col items-center justify-center transition-all shadow-lg">
                        <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                            </path>
                        </svg>
                        <span class="font-semibold">Edit Komponen</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- MODAL 3: Pasang Item ke Meja (Modal di atas Modal) --}}
    {{-- ========================================================= --}}
    <div id="attach-desk-modal" class="hidden" role="dialog" aria-modal="true"
        aria-labelledby="attach-desk-modal-title">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 z-[3000]"></div>
        <div id="attach-desk-modal-overlay" class="fixed inset-0 flex items-center justify-center p-4 z-[3001]">
            <div id="attach-desk-modal-area"
                class="relative bg-white rounded-lg shadow-xl w-full max-w-5xl flex flex-col max-h-[90vh]">
                {{-- Header --}}
                <div class="flex justify-between items-center p-4 border-b border-gray-200 rounded-t">
                    <h3 id="attach-desk-modal-title" class="text-xl font-semibold text-gray-900">
                        Pasang Item: <span id="attach-item-name" class="text-indigo-600"></span>
                    </h3>
                    <button id="attach-desk-modal-close-btn" type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                {{-- Body (Konten Denah) --}}
                <div class="p-6 space-y-4 overflow-y-auto">
                    <div class="bg-gray-100 rounded-lg p-4">
                        <label for="lab-selector-modal" class="block text-sm font-semibold text-gray-700 mb-2">Pilih
                            Laboratorium</label>
                        <select id="lab-selector-modal" placeholder="Pilih Lab..."></select>
                    </div>
                    <div id="desk-grid-container-modal" class="mb-8">
                        {{-- Denah akan di-render di sini oleh JS --}}
                        <div class="text-center py-12 text-gray-500">Pilih lab untuk melihat denah.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- TEMPLATE untuk baris spesifikasi --}}
    <template id="spec-row-template">
        <div class="grid grid-cols-12 gap-4 spec-row p-4 border border-gray-200 rounded-lg bg-gray-50">
            <div class="col-span-12 md:col-span-5">
                <label class="block text-xs font-medium text-gray-600 mb-1">Attribute</label>
                <select class="spec-attribute" placeholder="Cari attribute..."></select>
            </div>
            <div class="col-span-12 md:col-span-5">
                <label class="block text-xs font-medium text-gray-600 mb-1">Value</label>
                <select class="spec-value" placeholder="Pilih attribute dulu..."></select>
            </div>
            <div class="col-span-12 md:col-span-2 flex items-end">
                <button type="button"
                    class="remove-spec-btn w-full px-4 py-3 bg-rose-50 text-rose-600 text-sm font-semibold rounded-lg hover:bg-rose-100 transition-colors">
                    Hapus
                </button>
            </div>
        </div>
    </template>

@endsection

@section('script')
    <script>
        // Set sidebar link aktif
        try {
            // [FIX] Ganti ID 'items' agar sesuai dengan sidebar Anda jika berbeda
            // Asumsi ID di sidebar Anda adalah 'items'
            document.getElementById('items').classList.add('bg-slate-100');
        } catch(e) { console.warn("Sidebar link 'items' not found or ID mismatch."); }
        
        // Data dari Blade (untuk modal Buat Item)
        const allTypes = @json($types);
        const allSpecAttributes = @json($specification);

        // =================================================================
        // Variabel Global
        // =================================================================
        let currentItemId = null; // Item yang dipilih dari tabel
        let currentItemName = null;
        let currentItemCondition = null;

        let tomSelectInstances = []; // Untuk spec form
        let specIndex = 0;
        let tomSelectType; // Untuk form Buat Item
        let tomSelectLabModal; // Untuk modal "Pasang Meja"

        let modalLabDesks = []; // Cache denah meja untuk modal
        let allSpecAttributesModal = []; // Cache spec untuk modal filter

        // =================================================================
        // Fungsi Utility (Loading)
        // =================================================================
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

        // =================================================================
        // Fungsi Konfirmasi
        // =================================================================
        async function confirmCreate(name, type) {
            const result = await Swal.fire({
                title: `Tambah ${type} Baru?`,
                text: `"${name}" tidak ditemukan. Apakah Anda ingin menambahkannya sebagai ${type} baru?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Tambahkan!',
                cancelButtonText: 'Batal'
            });
            return result.isConfirmed;
        }

        // =================================================================
        // MODAL 1: Logika "Buat Item Baru"
        // =================================================================
        function initializeCreateItemModal() {
            const modal = document.getElementById('create-item-modal');
            const form = document.getElementById('create-item-form');
            const submitBtn = document.getElementById('submit-btn');
            const addSpecBtn = document.getElementById('add-spec-btn');
            const closeBtn = document.getElementById('create-item-modal-close-btn');
            const cancelBtn = document.getElementById('create-item-modal-cancel-btn');
            const overlay = document.getElementById('create-item-modal-overlay');

            if (!modal || !form || !submitBtn || !addSpecBtn || !closeBtn || !cancelBtn || !overlay) {
                console.warn('Satu atau lebih elemen modal Buat Item tidak ditemukan.');
                return;
            }

            // --- Fungsi Buka/Tutup Modal ---
            window.openCreateItemModal = () => {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
            window.closeCreateItemModal = () => {
                modal.classList.add('hidden');
                if (document.getElementById('action-modal').classList.contains('hidden') && 
                    document.getElementById('attach-desk-modal').classList.contains('hidden')) {
                    document.body.style.overflow = '';
                }
            }

            closeBtn.addEventListener('click', window.closeCreateItemModal);
            cancelBtn.addEventListener('click', window.closeCreateItemModal);
            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) window.closeCreateItemModal();
            });

            // --- Inisialisasi TomSelect ---
            tomSelectType = new TomSelect('#type-selector', {
                options: allTypes.map(type => ({
                    value: type.id,
                    text: type.name
                })),
                plugins: ['dropdown_input', 'clear_button'],
                create: async function(input, callback) {
                    const isConfirmed = await confirmCreate(input, 'Type');
                    if (isConfirmed) {
                        const newValue = `new::${input}`;
                        callback({
                            value: newValue,
                            text: input
                        });
                    } else {
                        callback();
                    }
                },
                render: {
                    option_create: (data, escape) =>
                        `<div class="create">Tambah type baru: <strong>${escape(data.input)}</strong>&hellip;</div>`,
                }
            });

            // --- Logika Tambah Spec ---
            addSpecBtn.addEventListener('click', () => {
                const specContainer = document.getElementById('specifications-container');
                const specTemplate = document.getElementById('spec-row-template');
                if (!specContainer || !specTemplate) return;
                
                const newRow = specTemplate.content.cloneNode(true).firstElementChild;
                specContainer.appendChild(newRow);
                initializeSpecRow(newRow, specIndex);
                specIndex++;
            });

            // --- Logika Submit Form ---
            submitBtn.addEventListener('click', async function() {
                await submitCreateItemForm(this, form, tomSelectType);
            });
        }
        
        function initializeSpecRow(rowElement, index) {
            const attrSelectEl = rowElement.querySelector('.spec-attribute');
            const valSelectEl = rowElement.querySelector('.spec-value');
            
            if (!attrSelectEl || !valSelectEl) return;

            let valueTomSelect;

            const attrTomSelect = new TomSelect(attrSelectEl, {
                options: allSpecAttributes.map(attr => ({
                    value: attr.id,
                    text: attr.name,
                    spec_values: attr.spec_values
                })),
                plugins: ['dropdown_input', 'clear_button'],
                create: async function(input, callback) {
                    const isConfirmed = await confirmCreate(input, 'Attribute');
                    if (isConfirmed) {
                        const newValue = `new::${input}`;
                        callback({
                            value: newValue,
                            text: input,
                            spec_values: []
                        });
                    } else {
                        callback();
                    }
                },
                render: {
                    option_create: (data, escape) =>
                        `<div class="create">Tambah attribute baru: <strong>${escape(data.input)}</strong>&hellip;</div>`,
                },
                onChange: function(selectedAttrId) {
                    valueTomSelect.clear();
                    valueTomSelect.clearOptions();
                    valueTomSelect.disable();
                    if (!selectedAttrId) return;
                    let selectedValues = [];
                    if (String(selectedAttrId).startsWith('new::')) {
                        selectedValues = [];
                    } else {
                        const attributeData = this.options[selectedAttrId];
                        if (attributeData && attributeData.spec_values) {
                            selectedValues = attributeData.spec_values.map(val => ({
                                value: val.id,
                                text: val.value
                            }));
                        }
                    }
                    valueTomSelect.addOptions(selectedValues);
                    valueTomSelect.enable();
                    valueTomSelect.open();
                }
            });

            valueTomSelect = new TomSelect(valSelectEl, {
                plugins: ['dropdown_input', 'clear_button'],
                create: async function(input, callback) {
                    const isConfirmed = await confirmCreate(input, 'Value');
                    if (isConfirmed) {
                        const newValue = `new::${input}`;
                        callback({
                            value: newValue,
                            text: input
                        });
                    } else {
                        callback();
                    }
                },
                render: {
                    option_create: (data, escape) =>
                        `<div class="create">Tambah value baru: <strong>${escape(data.input)}</strong>&hellip;</div>`,
                },
            });
            valueTomSelect.disable();

            const rowInstance = {
                row: rowElement,
                attr: attrTomSelect,
                val: valueTomSelect
            };
            tomSelectInstances.push(rowInstance);

            rowElement.querySelector('.remove-spec-btn').addEventListener('click', () => {
                attrTomSelect.destroy();
                valueTomSelect.destroy();
                rowElement.remove();
                tomSelectInstances = tomSelectInstances.filter(inst => inst.row !== rowElement);
            });
        }
        
        async function submitCreateItemForm(submitBtn, form, typeSelect) {
            showLoading('Menyimpan Item...');
            submitBtn.disabled = true;

            const specs = [];
            tomSelectInstances.forEach(inst => {
                const attrVal = inst.attr.getValue();
                const valVal = inst.val.getValue();
                if (attrVal && valVal) {
                    specs.push({
                        attribute: attrVal,
                        value: valVal
                    });
                }
            });
            
            const csrfTokenEl = form.querySelector('input[name="_token"]');

            const formData = {
                is_component: form.querySelector('input[name="is_component"]:checked').value,
                name: document.getElementById('name').value,
                serial_code: document.getElementById('serial_code').value,
                condition: form.querySelector('input[name="condition"]:checked').value,
                type: typeSelect.getValue(),
                specifications: specs,
                _token: csrfTokenEl ? csrfTokenEl.value : ''
            };

            try {
                const response = await fetch(form.dataset.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': formData._token
                    },
                    body: JSON.stringify(formData)
                });
                const data = await response.json();
                if (!response.ok) {
                    throw new Error(data.message || 'Terjadi kesalahan.');
                }

                hideLoading();
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                }).then(() => {
                    location.reload(); 
                });

            } catch (error) {
                hideLoading();
                Swal.fire('Gagal Menyimpan', error.message, 'error');
            } finally {
                submitBtn.disabled = false;
            }
        }

        // =================================================================
        // MODAL 2: Logika "Aksi Item"
        // =================================================================
        function initializeActionModal() {
            const modal = document.getElementById('action-modal');
            const overlay = document.getElementById('action-modal-overlay');
            const closeBtn = document.getElementById('action-modal-close-btn');

            if (!modal || !overlay || !closeBtn) {
                console.warn('Elemen Modal Aksi tidak ditemukan');
                return;
            }

            const closeActionModal = () => {
                modal.classList.add('hidden');
                if (document.getElementById('create-item-modal').classList.contains('hidden') && 
                    document.getElementById('attach-desk-modal').classList.contains('hidden')) {
                    document.body.style.overflow = '';
                }
                currentItemId = null;
                currentItemName = null;
                currentItemCondition = null;
            }

            closeBtn.addEventListener('click', closeActionModal);
            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) closeActionModal();
            });
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeActionModal();
            });

            // --- Hook Tombol Aksi ---
            document.getElementById('action-btn-attach').addEventListener('click', openAttachDeskModal);
            document.getElementById('action-btn-condition').addEventListener('click', submitChangeCondition);

            document.getElementById('action-btn-manage-comp').addEventListener('click', () => {
                Swal.fire('Fitur Dalam Pengembangan',
                    'Fitur untuk mengelola komponen (attach/detach) sedang dibuat.', 'info');
            });
            document.getElementById('action-btn-edit-comp').addEventListener('click', () => {
                Swal.fire('Fitur Dalam Pengembangan',
                    'Fitur untuk mengubah kondisi komponen sedang dibuat.',
                    'info');
            });
        }

        async function submitChangeCondition() {
            const newConditionText = currentItemCondition ? "Rusak" : "Bagus";
            const result = await Swal.fire({
                title: `Ubah Kondisi?`,
                text: `Anda yakin ingin mengubah kondisi '${currentItemName}' menjadi ${newConditionText}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Ubah!',
                cancelButtonText: 'Batal'
            });

            if (!result.isConfirmed) return;

            showLoading('Mengubah Kondisi...');
            try {
                // [FIX] Ambil CSRF dari meta tag
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                const response = await fetch(`/admin/items/${currentItemId}/condition`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                });
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Gagal update.');

                hideLoading();
                showToast('Berhasil', data.message, 'success');

                // Update UI di tabel
                const row = document.querySelector(`.item-row[data-item-id="${currentItemId}"]`);
                if (row) {
                    row.dataset.itemCondition = data.new_condition ? '1' : '0'; // [FIX] Simpan 1 atau 0
                    const badge = row.querySelector('.condition-badge');
                    if (badge) {
                        if (data.new_condition) {
                            badge.textContent = 'Bagus';
                            badge.className =
                                'condition-badge px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800';
                        } else {
                            badge.textContent = 'Rusak';
                            badge.className =
                                'condition-badge px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800';
                        }
                    }
                }
                
                // Tutup modal aksi
                document.getElementById('action-modal').classList.add('hidden'); 
                if(document.getElementById('create-item-modal').classList.contains('hidden') && 
                   document.getElementById('attach-desk-modal').classList.contains('hidden')) {
                   document.body.style.overflow = '';
                }

            } catch (error) {
                hideLoading();
                Swal.fire('Gagal', error.message, 'error');
            }
        }

        // =================================================================
        // MODAL 3: Logika "Pasang ke Meja"
        // =================================================================
        function initializeDeskMapperModal() {
            const modal = document.getElementById('attach-desk-modal');
            const overlay = document.getElementById('attach-desk-modal-overlay');
            const closeBtn = document.getElementById('attach-desk-modal-close-btn');
            const labSelectorEl = document.getElementById('lab-selector-modal');

            if (!modal || !overlay || !closeBtn || !labSelectorEl) {
                console.warn('Elemen Modal Pasang Meja tidak ditemukan');
                return;
            }

            tomSelectLabModal = new TomSelect(labSelectorEl, {
                create: false,
                placeholder: 'Pilih Lab...',
                onChange: (labId) => {
                    if (labId) fetchDeskMapForModal(labId);
                }
            });

            const closeAttachModal = () => {
                modal.classList.add('hidden');
                if (tomSelectLabModal) {
                    tomSelectLabModal.clear();
                }
                const gridContainer = document.getElementById('desk-grid-container-modal');
                if (gridContainer) {
                    gridContainer.innerHTML =
                        '<div class="text-center py-12 text-gray-500">Pilih lab untuk melihat denah.</div>';
                }
                
                if (document.getElementById('action-modal').classList.contains('hidden')) {
                     document.body.style.overflow = '';
                }
            }

            closeBtn.addEventListener('click', closeAttachModal);
            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) closeAttachModal();
            });
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeAttachModal();
            });
        }

        async function openAttachDeskModal() {
            const itemNameEl = document.getElementById('attach-item-name');
            const modal = document.getElementById('attach-desk-modal');
            
            if (itemNameEl) itemNameEl.textContent = currentItemName || '';
            if (modal) modal.classList.remove('hidden');

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
                if (modal) modal.classList.add('hidden');
            }
        }

        async function fetchDeskMapForModal(labId) {
            const container = document.getElementById('desk-grid-container-modal');
            if (!container) return;
            
            container.innerHTML =
                `<div class="flex items-center justify-center py-12"><div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div></div>`;

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
            }
        }

        function renderDeskGridModal(desks, maxRows, maxCols) {
            const container = document.getElementById('desk-grid-container-modal');
            let containerHTML =
                `<div class="overflow-x-auto pb-4">
                    <div id="desk-grid-modal" class="grid gap-4 border-2 min-w-fit border-slate-300 p-8" 
                         style="grid-template-columns: repeat(${maxCols}, minmax(140px, 1fr)); grid-template-rows: repeat(${maxRows}, auto);">`;

            const occupiedSlots = new Set(desks.map(d => d.location));

            desks.forEach(desk => {
                const row = desk.location.charCodeAt(0) - 64;
                const col = parseInt(desk.location.substring(1));
                let bgColorClass, iconColor, conditionText;

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

                containerHTML += `
                <div data-desk-id="${desk.id}" data-desk-location="${desk.location}" 
                     style="grid-area: ${row} / ${col};" 
                     class="desk-item-modal group transition-all duration-200 flex flex-col items-center justify-center p-5 border-2 rounded-xl min-h-36 ${bgColorClass} cursor-pointer"
                     title="Klik untuk memasang item ke meja ${desk.location}">
                    <div class="text-center pointer-events-none">
                        <div class="mb-2"><svg class="w-8 h-8 mx-auto ${iconColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg></div>
                        <span class="font-bold text-lg text-gray-800 block select-none">${desk.location}</span>
                        <span class="text-sm text-gray-600 mt-1 inline-block select-none">${conditionText}</span>
                    </div>
                </div>`;
            });

            for (let r = 1; r <= maxRows; r++) {
                for (let c = 1; c <= maxCols; c++) {
                    const location = `${String.fromCharCode(64 + r)}${c}`;
                    if (!occupiedSlots.has(location)) {
                        containerHTML +=
                            `<div class="empty-slot-modal" style="grid-area: ${r} / ${c}; visibility: hidden;"></div>`;
                    }
                }
            }

            containerHTML += '</div></div>';
            container.innerHTML = containerHTML;

            document.querySelectorAll('.desk-item-modal').forEach(deskEl => {
                deskEl.addEventListener('click', () => {
                    confirmAttachToDesk(deskEl.dataset.deskId, deskEl.dataset.deskLocation);
                });
            });
        }

        async function confirmAttachToDesk(deskId, deskLocation) {
            const result = await Swal.fire({
                title: `Pasang item ke Meja ${deskLocation}?`,
                text: `Anda akan memasang '${currentItemName}' ke meja ${deskLocation}. Lanjutkan?`, // [FIX] Variabel typo
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Pasang!',
                cancelButtonText: 'Batal'
            });

            if (!result.isConfirmed) return;

            showLoading('Memasang Item...');

            try {
                // [FIX] Ambil CSRF dari meta tag
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                const response = await fetch(`/admin/items/${currentItemId}/attach-desk/${deskId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken 
                    },
                });

                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Gagal memasang item.');

                hideLoading();
                showToast('Berhasil!', data.message, 'success');

                // Tutup semua modal
                document.getElementById('attach-desk-modal').classList.add('hidden');
                document.getElementById('action-modal').classList.add('hidden');
                document.body.style.overflow = ''; 

                // Hapus item dari tabel utama
                const rowToRemove = document.querySelector(`.item-row[data-item-id="${currentItemId}"]`);
                if (rowToRemove) {
                    rowToRemove.style.transition = 'opacity 0.5s ease';
                    rowToRemove.style.opacity = '0';
                    setTimeout(() => {
                        rowToRemove.remove();
                        if (document.querySelectorAll('.item-row').length === 0) {
                            const tbody = document.querySelector('tbody');
                            if (tbody) {
                                tbody.innerHTML =
                                    `<tr><td colspan="5" class="px-6 py-12 text-center text-gray-500">Tidak ada item tersedia (unaffiliated) yang ditemukan.</td></tr>`;
                            }
                        }
                    }, 500);
                }

            } catch (error) {
                hideLoading();
                Swal.fire('Gagal', error.message, 'error');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            initializeCreateItemModal();
            initializeActionModal();
            initializeDeskMapperModal();

            const openCreateBtn = document.getElementById('open-create-modal-btn');
            if (openCreateBtn) {
                openCreateBtn.addEventListener('click', window.openCreateItemModal);
            } else {
                console.warn('Tombol #open-create-modal-btn tidak ditemukan.');
            }

            const actionModal = document.getElementById('action-modal');
            const actionModalTitle = document.getElementById('action-modal-title');
            
            if (actionModal && actionModalTitle) {
                document.querySelectorAll('.item-row').forEach(row => {
                    row.addEventListener('click', function() {
                        currentItemId = this.dataset.itemId;
                        currentItemName = this.dataset.itemName;
                        
                        currentItemCondition = this.dataset.itemCondition === '1'; 

                        actionModalTitle.innerHTML = `Aksi untuk <span class="font-bold text-indigo-600">${currentItemName}</span>`;
                        
                        actionModal.classList.remove('hidden');
                        document.body.style.overflow = 'hidden';
                    });
                });
            } else {
                 console.warn('Modal Aksi atau judulnya tidak ditemukan. Listener baris tabel tidak akan berfungsi.');
            }
        });
    </script>
@endsection