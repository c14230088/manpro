@extends('layouts.user')

@section('title', 'Formulir Peminjaman')

@section('body')
    <div class="w-full min-h-screen flex justify-center items-center bg-slate-50 p-4">

        <div class="form-card bg-white rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 p-6">
                <h2 class="text-3xl font-bold text-white text-center tracking-wide">Formulir Peminjaman</h2>
            </div>

            <form id="bookingForm" class="p-8 space-y-6">

                {{-- LANGKAH 1: PILIH TIPE PEMINJAMAN DENGAN RADIO BUTTON --}}
                <div class="form-element">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Saya ingin meminjam...</label>
                    <fieldset class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <input type="radio" name="bookingType" value="lab" id="bookingTypeLab" class="peer sr-only">
                            <label for="bookingTypeLab"
                                class="block w-full text-center p-4 rounded-lg border-2 border-gray-300 cursor-pointer transition-all peer-checked:border-indigo-600 peer-checked:ring-2 peer-checked:ring-indigo-500 peer-checked:bg-indigo-50">
                                <span class="text-lg font-semibold text-gray-700">Ruang Laboratorium</span>
                            </label>
                        </div>
                        <div>
                            <input type="radio" name="bookingType" value="item" id="bookingTypeItem"
                                class="peer sr-only">
                            <label for="bookingTypeItem"
                                class="block w-full text-center p-4 rounded-lg border-2 border-gray-300 cursor-pointer transition-all peer-checked:border-indigo-600 peer-checked:ring-2 peer-checked:ring-indigo-500 peer-checked:bg-indigo-50">
                                <span class="text-lg font-semibold text-gray-700">Barang / Item</span>
                            </label>
                        </div>
                    </fieldset>
                </div>

                {{-- OPSI JIKA MEMILIH LAB (Awalnya disembunyikan) --}}
                <div id="labOptions" class="form-element space-y-4 hidden">
                    <div>
                        <label for="lab" class="block text-sm font-medium text-gray-700 mb-1">Pilih Lab</label>
                        <select id="lab" name="selectedBookableId_lab"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="" disabled>-- Pilih Laboratorium --</option>
                        </select>
                    </div>
                    <div>
                        <label for="attendee_count" class="block text-sm font-medium text-gray-700 mb-1">Jumlah
                            Pengguna</label>
                        <input type="number" id="attendee_count" name="attendee_count" placeholder="Contoh: 15"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                </div>

                {{-- OPSI JIKA MEMILIH ITEM (Awalnya disembunyikan) --}}
                <div id="itemOptions" class="form-element space-y-4 hidden">
                    {{-- LANGKAH 1: PILIH LAB --}}
                    <div>
                        <label for="lab_for_item" class="block text-sm font-medium text-gray-700 mb-1">Pinjam dari Lab</label>
                        <select id="lab_for_item" name="lab_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="" selected disabled>-- Pilih Laboratorium Dulu --</option>
                        </select>
                    </div>
                    
                    {{-- Tombol untuk booking 1 set, awalnya disembunyikan --}}
                    <div id="itemActions" class="hidden">
                        <button type="button" id="bookSetBtn" class="w-full sm:w-auto text-sm font-medium text-indigo-600 bg-indigo-100 hover:bg-indigo-200 py-2 px-4 rounded-md transition-all">
                            Pesan 1 Set (PC, Monitor, Keyboard, Mouse)
                        </button>
                    </div>
    
                    {{-- LANGKAH 2: DAFTAR ITEM (Akan diisi JavaScript) --}}
                    <div id="itemListContainer" class="space-y-3 pt-2">
                        <p id="itemPlaceholder" class="text-sm text-gray-500">Pilih lab untuk melihat item yang tersedia.</p>
                    </div>

                    <div class="form-element pt-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Peminjaman</label>
                        <fieldset class_x_="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <input type="radio" name="booking_type_option" value="0" id="typeOnsite" class="peer sr-only" checked>
                                <label for="typeOnsite"
                                    class="block w-full text-center p-3 rounded-lg border-2 border-gray-300 cursor-pointer transition-all peer-checked:border-indigo-600 peer-checked:ring-2 peer-checked:ring-indigo-500 peer-checked:bg-indigo-50">
                                    <span class="text-md font-semibold text-gray-700">Onsite</span>
                                </label>
                            </div>
                            <div>
                                <input type="radio" name="booking_type_option" value="1" id="typeRemote"
                                    class="peer sr-only">
                                <label for="typeRemote"
                                    class="block w-full text-center p-3 rounded-lg border-2 border-gray-300 cursor-pointer transition-all peer-checked:border-indigo-600 peer-checked:ring-2 peer-checked:ring-indigo-500 peer-checked:bg-indigo-50">
                                    <span class="text-md font-semibold text-gray-700">Remote</span>
                                </label>
                            </div>
                        </fieldset>
                    </div>

                    {{-- OPSI SKRIPSI --}}
                    <div class="relative flex items-start pt-4 border-t border-gray-200">
                        <div class="flex h-5 items-center">
                            <input id="isForThesis" name="isForThesis" type="checkbox"
                                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="isForThesis" class="font-medium text-gray-700">Digunakan untuk Skripsi?</label>
                        </div>
                    </div>

                    {{-- Opsi jika UNTUK SKRIPSI (Awalnya disembunyikan) --}}
                    <div id="thesisOptions" class="space-y-4 hidden">
                        <div>
                            <label for="thesis_title" class="block text-sm font-medium text-gray-700 mb-1">Judul
                                Skripsi</label>
                            <input type="text" id="thesis_title" name="thesis_title"
                                placeholder="Contoh: Sistem Inventaris Berbasis AI"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="supervisor_id" class="block text-sm font-medium text-gray-700 mb-1">Dosen
                                Pembimbing</label>
                            <input type="text" id="supervisor_id" name="supervisor_id" placeholder="Tuliskan Nama Dosennya"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                    </div>

                    {{-- Opsi jika BUKAN UNTUK SKRIPSI (Selalu terlihat jika item dipilih) --}}
                    <div id="eventOptions">
                        <div>
                            <label for="event_name" class="block text-sm font-medium text-gray-700 mb-1">Keperluan
                                Peminjaman</label>
                            <input type="text" id="event_name" name="event_name"
                                placeholder="Contoh: Untuk Acara IRGL"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                    </div>
                </div>

                <div class="pt-6">
                    <button type="submit"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-lg text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-transform transform hover:scale-105">
                        Ajukan Peminjaman
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Inisialisasi animasi GSAP
            gsap.from('.form-card', {
                duration: 0.8,
                y: 100,
                opacity: 0,
                ease: 'power3.out'
            });
            gsap.from('.form-element', {
                duration: 0.8,
                x: -50,
                opacity: 0,
                ease: 'power3.out',
                stagger: 0.1,
                delay: 0.3
            });

            // --- LOGIKA FORM DENGAN JAVASCRIPT MURNI ---

            // 1. Ambil semua elemen yang dibutuhkan
            const bookingTypeRadios = document.querySelectorAll('input[name="bookingType"]');
            const labOptionsDiv = document.getElementById('labOptions');
            const itemOptionsDiv = document.getElementById('itemOptions');
            const bookingForm = document.getElementById('bookingForm');

            const labForItemSelect = document.getElementById('lab_for_item');
            const itemListContainer = document.getElementById('itemListContainer');
            const itemPlaceholder = document.getElementById('itemPlaceholder');
            const itemActions = document.getElementById('itemActions');
            const bookSetBtn = document.getElementById('bookSetBtn');

            const isForThesisCheckbox = document.getElementById('isForThesis');
            const thesisOptionsDiv = document.getElementById('thesisOptions');
            const eventOptionsDiv = document.getElementById('eventOptions');
            const eventNameInput = document.getElementById('event_name');
            
            // 2. Event listener untuk dropdown utama
            bookingTypeRadios.forEach(radio => {
                radio.addEventListener('change', async function() {
                    const selection = this.value;

                     // Sembunyikan semua opsi terlebih dahulu
                    labOptionsDiv.classList.add('hidden');
                    itemOptionsDiv.classList.add('hidden');

                    if (!selection) return;

                    Swal.fire({
                        title: 'Memuat Data...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    try {
                        if (selection === 'lab') {
                            await populateSelectWithOptions('/get/labs', document.getElementById('lab'));
                            labOptionsDiv.classList.remove('hidden');
                        } else if (selection === 'item') {
                            await populateSelectWithOptions('/get/labs', labForItemSelect, '-- Pilih Laboratorium Dulu --');
                            itemOptionsDiv.classList.remove('hidden');
                        }
                        Swal.close();
                    } catch (error) {
                        Swal.fire('Error!', error.message, 'error');
                    }
                });
            });

            // 3. Event listener BARU saat lab untuk peminjaman item dipilih
            labForItemSelect.addEventListener('change', function() { // Hapus 'async'
                const labId = this.value; // ID dari lab, misal "uuid-123"
                const selectedOption = this.options[this.selectedIndex];
                const labName = selectedOption.text; // Nama lab, misal "Lab Multimedia"

                // Bersihkan dan reset
                itemListContainer.innerHTML = '';
                itemPlaceholder.textContent = 'Pilih lab untuk melihat item yang tersedia.';
                itemPlaceholder.classList.remove('hidden');
                itemActions.classList.add('hidden');

                if (!labId) return; // Jika memilih "--Pilih Lab--", hentikan

                // --- INI LOGIKA BARU ANDA ---
                // 1. Definisikan daftar item
                const standardSet = ['cpu', 'monitor', 'mouse', 'keyboard'];
                const multimediaSet = ['cpu', 'monitor', 'gpu', 'mouse', 'keyboard'];

                let itemTypes = [];

                // 2. Cek nama lab yang dipilih
                // Kita pakai .toLowerCase() dan .includes() agar lebih fleksibel
                if (labName.toLowerCase().includes('multimedia')) {
                    itemTypes = multimediaSet;
                } else {
                    itemTypes = standardSet;
                }

                // 3. Tampilkan item ke HTML (tanpa 'fetch'!)
                if (itemTypes.length > 0) {
                    itemPlaceholder.classList.add('hidden');
                    itemActions.classList.remove('hidden');

                    itemTypes.forEach(type => {
                        // Buat nama yang lebih cantik (misal: "cpu" -> "Cpu")
                        const displayName = type.charAt(0).toUpperCase() + type.slice(1);

                        const itemHtml = `
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border">
                                <div class="flex items-center">
                                    <input id="item_type_${type}" name="items[${type}][checked]" type="checkbox" value="${type}" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 item-checkbox">
                                    <label for="item_type_${type}" class="ml-3 block text-sm font-medium text-gray-700 capitalize">${displayName}</label>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <label for="quantity_${type}" class="text-sm text-gray-500">Jumlah:</label>
                                    <input type="number" name="items[${type}][quantity]" id="quantity_${type}" value="1" min="1" class="w-20 text-center rounded-md border-gray-300 shadow-sm sm:text-sm">
                                    {{-- Span untuk / count sudah dihapus --}}
                                </div>
                            </div>
                        `;
                        itemListContainer.insertAdjacentHTML('beforeend', itemHtml);
                    });
                } else {
                    itemPlaceholder.textContent = 'Tidak ada item yang tersedia di lab ini.';
                    itemPlaceholder.classList.remove('hidden');
                }
            });

            // 4. Event listener BARU untuk tombol "Pesan 1 Set"
            bookSetBtn.addEventListener('click', function() {
                const standardSet = ['cpu', 'monitor', 'keyboard', 'mouse']; // Sesuaikan 'type' jika perlu
                standardSet.forEach(type => {
                    const checkbox = document.getElementById(`item_type_${type}`);
                    if (checkbox) {
                        checkbox.checked = true;
                    }
                });
            });


            // 5. Event listener untuk checkbox skripsi
            isForThesisCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    // --- JIKA SKRIPSI DICENTANG ---
                    thesisOptionsDiv.classList.remove('hidden');
                    eventOptionsDiv.classList.add('hidden');
                    eventNameInput.required = false; // <-- Keperluan Peminjaman TIDAK WAJIB
                } else {
                    // --- JIKA SKRIPSI TIDAK DICENTANG ---
                    thesisOptionsDiv.classList.add('hidden');
                    eventOptionsDiv.classList.remove('hidden');
                    eventNameInput.required = true; // <-- Keperluan Peminjaman WAJIB DIISI
                }
            });

            // 6. Event listener untuk submit form
            bookingForm.addEventListener('submit', async function(event) {
                event.preventDefault();
                const formData = new FormData(this);
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                let payload = {
                    _token: csrfToken
                };
                let details = {}; // Ini akan menjadi JSON 'book_detail'
                const bookingType = formData.get('bookingType');

                if (bookingType === 'lab') {
                    // --- Payload untuk Peminjaman LAB ---
                    payload.bookable_id = formData.get('selectedBookableId_lab');
                    payload.bookable_type = 'App\\Models\\Lab'; 
                    payload.type = 0; // <-- PERUBAHAN: Lab selalu Onsite (0)

                    details.type = 'lab'; 
                    details.attendee_count = formData.get('attendee_count');

                } else if (bookingType === 'item') {
                    // --- Payload untuk Peminjaman ITEM ---
                    payload.bookable_id = formData.get('lab_id'); 
                    payload.bookable_type = 'App\\Models\\Lab';
                    payload.type = formData.get('booking_type_option'); // <-- PERUBAHAN: 0 atau 1

                    details.type = 'item'; 
                    details.is_for_thesis = formData.has('isForThesis');
                    
                    if (details.is_for_thesis) {
                        details.thesis_title = formData.get('thesis_title');
                        details.supervisor_id = formData.get('supervisor_id');
                    } else {
                        details.event_name = formData.get('event_name');
                    }

                    details.items = [];
                    const itemCheckboxes = itemListContainer.querySelectorAll('.item-checkbox:checked');
                    
                    if (itemCheckboxes.length === 0) {
                        Swal.fire('Oops...', 'Anda harus memilih minimal satu item untuk dipinjam.', 'warning');
                        return;
                    }
                    
                    itemCheckboxes.forEach(box => {
                        const type = box.value;
                        const quantity = formData.get(`items[${type}][quantity]`);
                        details.items.push({ type: type, quantity: quantity });
                    });

                } else {
                    Swal.fire('Oops...', 'Mohon pilih tipe peminjaman.', 'warning');
                    return;
                }

                // Tambahkan 'book_detail' (JSON) ke payload utama
                payload.book_detail = JSON.stringify(details);

                // --- Kirim data ke Server ---
                showLoading('Mengajukan Peminjaman...');
                
                try {
                    const response = await fetch('/booking', { 
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify(payload)
                    });

                    if (!response.ok) {
                        const errorData = await response.json();
                        throw new Error(errorData.message || 'Terjadi kesalahan.');
                    }

                    await Swal.fire('Berhasil!', 'Permintaan peminjaman Anda telah diajukan.', 'success');
                    this.reset();
                    labOptionsDiv.classList.add('hidden');
                    itemOptionsDiv.classList.add('hidden');
                    itemListContainer.innerHTML = '';
                    itemPlaceholder.textContent = 'Pilih lab untuk melihat item yang tersedia.';
                    itemPlaceholder.classList.remove('hidden');
                    itemActions.classList.add('hidden');

                } catch (error) {
                    Swal.fire('Gagal!', error.message, 'error');
                }
            });

            // --- FUNGSI BANTUAN ---
            async function populateSelectWithOptions(url, selectElement, defaultOptionText = '-- Pilih --') {
                if (selectElement.id !== 'lab_for_item') {
                    showLoading('Memuat Data...');
                }
                try {
                    const response = await fetch(url);
                    if (!response.ok) throw new Error('Gagal mengambil data.');
                    const data = await response.json();
                    selectElement.innerHTML = `<option value="" selected disabled>${defaultOptionText}</option>`;
                    data.forEach(item => {
                        selectElement.innerHTML += `<option value="${item.id}">${item.name}</option>`;
                    });
                    if (selectElement.id !== 'lab_for_item') {
                        Swal.close();
                    } else {
                        Swal.close();
                    }
                } catch (error) {
                    Swal.fire('Error!', error.message, 'error');
                }
            }
            function showLoading(title = 'Memuat...') {
                Swal.fire({
                    title: title,
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
            }
        });    
    </script>
@endsection
