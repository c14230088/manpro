@extends('layouts.user')

@section('title', 'Formulir Peminjaman Multi-Lab')

@section('body')
    <div class="w-full min-h-screen flex justify-center items-center bg-slate-50 p-4">
        <div class="form-card bg-white rounded-xl shadow-2xl w-full max-w-4xl overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 p-6">
                <h2 class="text-3xl font-bold text-white text-center tracking-wide">Formulir Peminjaman</h2>
            </div>

            <form id="bookingForm" class="p-8 space-y-6">
                @csrf
                {{-- Input Hidden untuk menyimpan JSON Data Barang --}}
                <input type="hidden" name="items_payload" id="items_payload">

                {{-- TIPE BOOKING --}}
                <div class="form-element">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Saya ingin meminjam...</label>
                    <fieldset class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <input type="radio" name="bookingType" value="lab" id="bookingTypeLab" class="peer sr-only">
                            <label for="bookingTypeLab" class="block w-full text-center p-4 rounded-lg border-2 border-gray-300 cursor-pointer peer-checked:border-indigo-600 peer-checked:bg-indigo-50">
                                <span class="text-lg font-semibold text-gray-700">Ruang Laboratorium</span>
                            </label>
                        </div>
                        <div>
                            <input type="radio" name="bookingType" value="item" id="bookingTypeItem" class="peer sr-only">
                            <label for="bookingTypeItem" class="block w-full text-center p-4 rounded-lg border-2 border-gray-300 cursor-pointer peer-checked:border-indigo-600 peer-checked:bg-indigo-50">
                                <span class="text-lg font-semibold text-gray-700">Barang / Inventaris</span>
                            </label>
                        </div>
                    </fieldset>
                </div>

                {{-- OPSI JIKA MEMILIH LAB (Simple) --}}
                <div id="labOptions" class="hidden space-y-4">
                     <select id="lab_main" name="bookable_id" class="block w-full rounded-md border-gray-300 p-2 border">
                        <option value="">-- Pilih Lab --</option>
                     </select>
                </div>

                {{-- OPSI JIKA MEMILIH ITEM (COMPLEX MULTI-LAB) --}}
                <div id="itemOptions" class="hidden space-y-6">
                    
                    {{-- 1. Area Dropdown Tambah Lab --}}
                    <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-200 flex items-end gap-2">
                        <div class="flex-grow">
                            <label class="block text-sm font-bold text-indigo-800 mb-1">Tambah Sumber Lab</label>
                            <select id="labSelector" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 sm:text-sm">
                                <option value="" disabled selected>-- Pilih Lab penyedia barang --</option>
                                {{-- Options akan diisi via JS --}}
                            </select>
                        </div>
                        <button type="button" id="btnAddLab" class="bg-indigo-600 text-white px-4 py-2 rounded-md font-bold hover:bg-indigo-700 shadow-md">
                            + Tambah
                        </button>
                    </div>

                    {{-- 2. Container Kartu-Kartu Lab yang Dipilih --}}
                    <div id="selectedLabsContainer" class="space-y-4">
                        {{-- Kartu Lab akan muncul di sini secara dinamis --}}
                    </div>

                    <div id="emptyState" class="text-center py-8 text-gray-400 border-2 border-dashed border-gray-200 rounded-lg">
                        Belum ada lab yang dipilih. Silakan pilih lab di atas.
                    </div>
                </div>

                {{-- BAGIAN INPUT TANGGAL & DATA DIRI (Standard) --}}
                <div class="border-t pt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium">Mulai Pinjam</label>
                        <input type="datetime-local" name="borrowed_at" required class="w-full border rounded p-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Selesai Pinjam</label>
                        <input type="datetime-local" name="return_deadline_at" required class="w-full border rounded p-2">
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-sm font-medium">Nama Kegiatan</label>
                        <input type="text" name="event_name" required class="w-full border rounded p-2">
                    </div>
                     {{-- Hidden inputs lain seperti phone, dll anggap saja ada --}}
                     <input type="hidden" name="phone_number" value="08123456789"> 
                     <input type="hidden" name="type" value="0"> 
                </div>

                <div class="pt-6">
                    <button type="submit" class="w-full py-3 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 shadow-lg transition-all">
                        Ajukan Peminjaman
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- TEMPLATE KARTU LAB (Hidden, diclone via JS) --}}
    <template id="labCardTemplate">
        <div class="lab-card bg-white border border-gray-300 rounded-lg shadow-sm overflow-hidden relative transition-all hover:shadow-md">
            {{-- Header Lab --}}
            <div class="bg-gray-100 p-3 flex justify-between items-center border-b">
                <h3 class="font-bold text-gray-800 lab-name">Nama Lab</h3>
                <button type="button" class="text-red-500 hover:text-red-700 text-sm font-bold btn-remove-lab">Hapus</button>
            </div>
            
            {{-- Body Inventory --}}
            <div class="p-4 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                
                {{-- 1 SET --}}
                <div class="col-span-1 sm:col-span-2 bg-blue-50 p-3 rounded border border-blue-100">
                    <div class="flex justify-between items-center mb-1">
                        <label class="font-bold text-blue-800">1 Set Lengkap</label>
                        <span class="text-xs text-blue-600">(Monitor, PC, Key, Mouse)</span>
                    </div>
                    <input type="number" min="0" placeholder="0" class="input-qty w-full rounded border-gray-300" data-type="set">
                    <p class="text-xs text-red-500 mt-1 hidden error-msg"></p>
                </div>

                {{-- ITEMS SATUAN --}}
                {{-- Monitor --}}
                <div class="input-group relative">
                    <label class="text-sm font-medium text-gray-600 block">Monitor <span class="text-xs text-gray-400 stock-label"></span></label>
                    <input type="number" min="0" class="input-qty w-full rounded border-gray-300 text-sm" data-type="monitor">
                    <p class="text-xs text-red-500 absolute -bottom-4 left-0 hidden error-msg">Stok tidak cukup</p>
                </div>

                {{-- PC --}}
                <div class="input-group relative">
                    <label class="text-sm font-medium text-gray-600 block">PC <span class="text-xs text-gray-400 stock-label"></span></label>
                    <input type="number" min="0" class="input-qty w-full rounded border-gray-300 text-sm" data-type="pc">
                    <p class="text-xs text-red-500 absolute -bottom-4 left-0 hidden error-msg">Stok tidak cukup</p>
                </div>

                {{-- Keyboard --}}
                <div class="input-group relative">
                    <label class="text-sm font-medium text-gray-600 block">Keyboard <span class="text-xs text-gray-400 stock-label"></span></label>
                    <input type="number" min="0" class="input-qty w-full rounded border-gray-300 text-sm" data-type="keyboard">
                    <p class="text-xs text-red-500 absolute -bottom-4 left-0 hidden error-msg">Stok tidak cukup</p>
                </div>

                {{-- Mouse --}}
                <div class="input-group relative">
                    <label class="text-sm font-medium text-gray-600 block">Mouse <span class="text-xs text-gray-400 stock-label"></span></label>
                    <input type="number" min="0" class="input-qty w-full rounded border-gray-300 text-sm" data-type="mouse">
                    <p class="text-xs text-red-500 absolute -bottom-4 left-0 hidden error-msg">Stok tidak cukup</p>
                </div>

                 {{-- VR (Special Item) --}}
                 <div class="input-group relative container-vr hidden">
                    <label class="text-sm font-medium text-purple-600 block">VR Headset <span class="text-xs text-gray-400 stock-label"></span></label>
                    <input type="number" min="0" class="input-qty w-full rounded border-purple-200 bg-purple-50 text-sm" data-type="vr">
                    <p class="text-xs text-red-500 absolute -bottom-4 left-0 hidden error-msg">Stok tidak cukup</p>
                </div>

            </div>
        </div>
    </template>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // --- DATA STORE ---
            let allLabsData = []; // Menyimpan data lab + stok dari API
            let selectedLabs = []; // Menyimpan ID lab yang sudah dipilih user

            // --- DOM ELEMENTS ---
            const bookingTypeRadios = document.querySelectorAll('input[name="bookingType"]');
            const labOptionsDiv = document.getElementById('labOptions');
            const itemOptionsDiv = document.getElementById('itemOptions');
            const labSelector = document.getElementById('labSelector');
            const btnAddLab = document.getElementById('btnAddLab');
            const selectedLabsContainer = document.getElementById('selectedLabsContainer');
            const emptyState = document.getElementById('emptyState');
            const template = document.getElementById('labCardTemplate');
            const itemsPayloadInput = document.getElementById('items_payload');

            // --- 1. LOAD DATA LAB DARI API ---
            async function loadLabsData() {
                try {
                    // Panggil API (Pastikan controller sudah update seperti langkah 1)
                    const response = await fetch('/get/labs'); 
                    allLabsData = await response.json();
                    
                    // Render Options ke Dropdown
                    labSelector.innerHTML = '<option value="" disabled selected>-- Pilih Lab penyedia barang --</option>';
                    allLabsData.forEach(lab => {
                        labSelector.innerHTML += `<option value="${lab.id}">${lab.name}</option>`;
                    });
                } catch (error) {
                    console.error("Gagal load lab", error);
                }
            }

            // --- 2. SWITCH TYPE (LAB / ITEM) ---
            bookingTypeRadios.forEach(radio => {
                radio.addEventListener('change', async function() {
                    if (this.value === 'item') {
                        labOptionsDiv.classList.add('hidden');
                        itemOptionsDiv.classList.remove('hidden');
                        if (allLabsData.length === 0) await loadLabsData();
                    } else {
                        labOptionsDiv.classList.remove('hidden');
                        itemOptionsDiv.classList.add('hidden');
                    }
                });
            });

            // --- 3. TAMBAH LAB KE DAFTAR ---
            btnAddLab.addEventListener('click', () => {
                const labId = labSelector.value;
                if (!labId) return Swal.fire('Pilih Lab dulu', '', 'warning');
                
                if (selectedLabs.includes(labId)) {
                    return Swal.fire('Lab ini sudah ditambahkan', '', 'info');
                }

                // Ambil data lab lengkap (termasuk stok inventory)
                const labData = allLabsData.find(l => l.id == labId);
                renderLabCard(labData);
                
                selectedLabs.push(labId);
                emptyState.classList.add('hidden');
                labSelector.value = ""; // Reset dropdown
            });

            // --- 4. RENDER KARTU LAB & LOGIKA VALIDASI ---
            function renderLabCard(lab) {
                const clone = template.content.cloneNode(true);
                const card = clone.querySelector('.lab-card');
                
                // Set Info Lab
                card.dataset.labId = lab.id;
                card.querySelector('.lab-name').textContent = lab.name;

                // Setup Inventory Labels & Validasi VR
                const inventory = lab.inventory; 
                
                // Helper untuk setup input
                const setupInput = (type, stock) => {
                    const input = card.querySelector(`input[data-type="${type}"]`);
                    const label = input.closest('.input-group')?.querySelector('.stock-label');
                    
                    if (input && label) {
                        label.textContent = `(Sisa: ${stock})`;
                        input.dataset.max = stock; // Simpan stok maksimal di attribute data
                        if (stock <= 0) {
                            input.disabled = true;
                            input.placeholder = "Kosong";
                            input.classList.add('bg-gray-100', 'cursor-not-allowed');
                        }
                    }
                    return input;
                };

                setupInput('monitor', inventory.monitor);
                setupInput('pc', inventory.pc);
                setupInput('keyboard', inventory.keyboard);
                setupInput('mouse', inventory.mouse);

                // Handle VR khusus (Jika stok > 0, tampilkan input VR)
                const vrContainer = card.querySelector('.container-vr');
                if (inventory.vr > 0) {
                    vrContainer.classList.remove('hidden');
                    setupInput('vr', inventory.vr);
                }

                // --- 5. LOGIKA HITUNG STOK (REALTIME) ---
                // Event listener untuk semua input di kartu ini
                const inputs = card.querySelectorAll('input.input-qty');
                inputs.forEach(input => {
                    input.addEventListener('input', () => validateStock(card, inventory));
                });

                // Tombol Hapus Lab
                card.querySelector('.btn-remove-lab').addEventListener('click', () => {
                    card.remove();
                    selectedLabs = selectedLabs.filter(id => id != lab.id);
                    if (selectedLabs.length === 0) emptyState.classList.remove('hidden');
                    updatePayload(); // Update JSON akhir
                });

                selectedLabsContainer.appendChild(clone);
            }

            // --- 6. VALIDASI MATEMATIKA (SET + SATUAN) ---
            function validateStock(card, stockData) {
                // Ambil nilai input saat ini
                const getVal = (type) => parseInt(card.querySelector(`input[data-type="${type}"]`)?.value || 0);
                
                const qtySet = getVal('set');
                const qtyMon = getVal('monitor');
                const qtyPc = getVal('pc');
                const qtyKey = getVal('keyboard');
                const qtyMouse = getVal('mouse');
                const qtyVr = getVal('vr');

                // Hitung Total Kebutuhan (Set memakan stok satuan)
                const totalMon = qtySet + qtyMon;
                const totalPc = qtySet + qtyPc;
                const totalKey = qtySet + qtyKey;
                const totalMouse = qtySet + qtyMouse;

                // Fungsi helper tampilkan error
                const toggleError = (type, isError, msg = "Melebihi stok!") => {
                    const input = card.querySelector(`input[data-type="${type}"]`);
                    const errorP = input.parentElement.querySelector('.error-msg');
                    
                    if (isError) {
                        input.classList.add('border-red-500', 'focus:ring-red-500');
                        errorP.textContent = msg;
                        errorP.classList.remove('hidden');
                    } else {
                        input.classList.remove('border-red-500', 'focus:ring-red-500');
                        errorP.classList.add('hidden');
                    }
                    return isError;
                };

                let hasError = false;

                // Cek Stok vs Total Request
                if (toggleError('monitor', totalMon > stockData.monitor, `Butuh ${totalMon}, Sisa ${stockData.monitor}`)) hasError = true;
                if (toggleError('pc', totalPc > stockData.pc, `Butuh ${totalPc}, Sisa ${stockData.pc}`)) hasError = true;
                if (toggleError('keyboard', totalKey > stockData.keyboard)) hasError = true;
                if (toggleError('mouse', totalMouse > stockData.mouse)) hasError = true;
                if (toggleError('vr', qtyVr > stockData.vr)) hasError = true;
                
                // Cek Error pada input Set (jika salah satu komponen kurang)
                // Jika user minta 10 Set, tapi monitor cuma 5, error muncul di input Set juga
                const setFail = (qtySet > stockData.monitor || qtySet > stockData.pc || qtySet > stockData.keyboard || qtySet > stockData.mouse);
                toggleError('set', setFail, "Stok komponen tidak cukup untuk set ini");

                updatePayload(); // Update data hidden input
                return hasError;
            }

            // --- 7. UPDATE HIDDEN INPUT UNTUK DIKIRIM KE SERVER ---
            function updatePayload() {
                const payload = [];
                const cards = document.querySelectorAll('.lab-card');
                let globalError = false;

                cards.forEach(card => {
                    const labId = card.dataset.labId;
                    const getVal = (type) => parseInt(card.querySelector(`input[data-type="${type}"]`)?.value || 0);

                    // Cek apakah ada error visual di kartu ini
                    if (card.querySelector('.error-msg:not(.hidden)')) {
                        globalError = true;
                    }

                    const data = {
                        lab_id: labId,
                        qty_set: getVal('set'),
                        qty_monitor: getVal('monitor'),
                        qty_pc: getVal('pc'),
                        qty_keyboard: getVal('keyboard'),
                        qty_mouse: getVal('mouse'),
                        qty_vr: getVal('vr')
                    };

                    // Hanya masukkan jika ada minimal 1 barang dipinjam dari lab ini
                    if (Object.values(data).some(v => v > 0 && typeof v === 'number')) { // cek lab_id tidak terhitung
                        // Hapus lab_id dari check some, agak tricky, sederhananya:
                        if (data.qty_set + data.qty_monitor + data.qty_pc + data.qty_keyboard + data.qty_mouse + data.qty_vr > 0) {
                             payload.push(data);
                        }
                    }
                });

                itemsPayloadInput.value = JSON.stringify(payload);
                
                // Disable tombol submit jika ada error
                const btnSubmit = document.querySelector('button[type="submit"]');
                if (globalError) {
                    btnSubmit.disabled = true;
                    btnSubmit.classList.add('opacity-50', 'cursor-not-allowed');
                    btnSubmit.textContent = "Perbaiki Jumlah Barang Dulu";
                } else {
                    btnSubmit.disabled = false;
                    btnSubmit.classList.remove('opacity-50', 'cursor-not-allowed');
                    btnSubmit.textContent = "Ajukan Peminjaman";
                }
        }
        });
    </script>
@endsection