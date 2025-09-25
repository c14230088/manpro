@extends('layouts.form')

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
                    <div>
                        <label for="item" class="block text-sm font-medium text-gray-700 mb-1">Pilih Item</label>
                        <select id="item" name="selectedBookableId_item"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="" disabled>-- Pilih Item --</option>
                        </select>
                    </div>

                    <div class="relative flex items-start">
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
                            <input type="text" id="supervisor_id" name="supervisor_id" placeholder="Pilih Dosen..."
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                    </div>

                    {{-- Opsi jika BUKAN UNTUK SKRIPSI (Selalu terlihat jika item dipilih) --}}
                    <div id="eventOptions">
                        <div>
                            <label for="event_name" class="block text-sm font-medium text-gray-700 mb-1">Keperluan
                                Peminjaman</label>
                            <input type="text" id="event_name" name="event_name"
                                placeholder="Contoh: Untuk Praktikum Jaringan Komputer"
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
            const bookingTypeSelect = document.getElementById('bookingType');
            const labOptionsDiv = document.getElementById('labOptions');
            const itemOptionsDiv = document.getElementById('itemOptions');
            const isForThesisCheckbox = document.getElementById('isForThesis');
            const thesisOptionsDiv = document.getElementById('thesisOptions');
            const eventOptionsDiv = document.getElementById('eventOptions');
            const bookingForm = document.getElementById('bookingForm');

            // 2. Event listener untuk dropdown utama
            bookingTypeSelect.addEventListener('change', async function() {
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
                        const response = await fetch('/get/labs');
                        if (!response.ok) throw new Error('Gagal mengambil data lab.');
                        const labs = await response.json();

                        const labSelect = document.getElementById('lab');
                        labSelect.innerHTML =
                            '<option value="" selected disabled>-- Pilih Laboratorium --</option>'; // Reset
                        labs.forEach(lab => {
                            labSelect.innerHTML +=
                                `<option value="${lab.id}">${lab.name}</option>`;
                        });

                        labOptionsDiv.classList.remove('hidden');
                    } else if (selection === 'item') {
                        const response = await fetch('/get/items');
                        if (!response.ok) throw new Error('Gagal mengambil data item.');
                        const items = await response.json();

                        const itemSelect = document.getElementById('item');
                        itemSelect.innerHTML =
                            '<option value="" selected disabled>-- Pilih Item --</option>'; // Reset
                        items.forEach(item => {
                            itemSelect.innerHTML +=
                                `<option value="${item.id}">${item.name}</option>`;
                        });

                        itemOptionsDiv.classList.remove('hidden');
                    }
                    Swal.close();
                } catch (error) {
                    Swal.fire('Error!', error.message, 'error');
                }
            });

            // 3. Event listener untuk checkbox skripsi
            isForThesisCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    thesisOptionsDiv.classList.remove('hidden');
                    eventOptionsDiv.classList.add('hidden');
                } else {
                    thesisOptionsDiv.classList.add('hidden');
                    eventOptionsDiv.classList.remove('hidden');
                }
            });

            // 4. Event listener untuk submit form
            bookingForm.addEventListener('submit', async function(event) {
                event.preventDefault(); // Mencegah form submit cara biasa

                const formData = new FormData(this);
                const bookingType = formData.get('bookingType');
                let details = {};
                let bookableId = '';

                const bookableTypeMap = {
                    'item': 'App\\Models\\Item',
                    'lab': 'App\\Models\\Lab'
                };

                // Membangun objek details berdasarkan pilihan
                if (bookingType === 'lab') {
                    bookableId = formData.get('selectedBookableId_lab');
                    details.attendee_count = formData.get('attendee_count');
                } else if (bookingType === 'item') {
                    bookableId = formData.get('selectedBookableId_item');
                    if (formData.get('isForThesis')) {
                        details.is_for_thesis = true;
                        details.thesis_title = formData.get('thesis_title');
                        details.supervisor_id = formData.get('supervisor_id');
                    } else {
                        details.is_for_thesis = false;
                        details.event_name = formData.get('event_name');
                    }
                }

                const payload = {
                    bookable_id: bookableId,
                    bookable_type: bookableTypeMap[bookingType],
                    details: JSON.stringify(details)
                    // Anda bisa tambahkan field lain di sini
                };

                if (!payload.bookable_id || !payload.bookable_type) {
                    Swal.fire('Oops...', 'Mohon lengkapi semua pilihan yang ada.', 'warning');
                    return;
                }

                Swal.fire({
                    title: 'Mengajukan Peminjaman...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                try {
                    const response = await fetch('/bookings', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content')
                        },
                        body: JSON.stringify(payload)
                    });

                    if (!response.ok) {
                        const errorData = await response.json();
                        throw new Error(errorData.message || 'Terjadi kesalahan.');
                    }

                    await Swal.fire('Berhasil!', 'Permintaan peminjaman Anda telah diajukan.',
                        'success');
                    this.reset(); // Reset form setelah berhasil
                    labOptionsDiv.classList.add('hidden');
                    itemOptionsDiv.classList.add('hidden');
                } catch (error) {
                    Swal.fire('Gagal!', error.message, 'error');
                }
            });
        });
    </script>
@endsection
