@extends('layouts.user')

@section('title', 'Formulir Peminjaman')

@section('body')
<div class="w-full min-h-screen bg-slate-50 p-4">
    <div class="max-w-6xl mx-auto">
        <div class="bg-white rounded-xl shadow-2xl overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 p-6">
                <h2 class="text-3xl font-bold text-white text-center">Formulir Peminjaman</h2>
            </div>

            <form id="bookingForm" class="p-8 space-y-6">
                @csrf
                
                {{-- Booking Type Selection --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-3">Tipe Peminjaman</label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <label class="cursor-pointer">
                            <input type="radio" name="booking_type" value="lab" class="peer sr-only">
                            <div class="p-4 border-2 rounded-lg peer-checked:border-indigo-600 peer-checked:bg-indigo-50 text-center">
                                <span class="font-semibold">Laboratorium</span>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="booking_type" value="sets" class="peer sr-only">
                            <div class="p-4 border-2 rounded-lg peer-checked:border-indigo-600 peer-checked:bg-indigo-50 text-center">
                                <span class="font-semibold">Set Lengkap</span>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="booking_type" value="items" class="peer sr-only">
                            <div class="p-4 border-2 rounded-lg peer-checked:border-indigo-600 peer-checked:bg-indigo-50 text-center">
                                <span class="font-semibold">Item Individual</span>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Common Fields --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Mulai Pinjam</label>
                        <input type="datetime-local" name="borrowed_at" id="borrowed_at" required class="w-full border rounded p-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Selesai Pinjam</label>
                        <input type="datetime-local" name="return_deadline_at" id="return_deadline_at" required class="w-full border rounded p-2">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1">Nama Kegiatan</label>
                        <input type="text" name="event_name" required class="w-full border rounded p-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Nomor WhatsApp</label>
                        <input type="text" name="phone_number" required class="w-full border rounded p-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Tipe Penggunaan</label>
                        <select name="type" required class="w-full border rounded p-2">
                            <option value="0">Onsite</option>
                            <option value="1">Remote</option>
                            <option value="2">Keluar Lab</option>
                        </select>
                    </div>
                </div>

                {{-- Lab Booking --}}
                <div id="lab-section" class="hidden">
                    <label class="block text-sm font-medium mb-2">Pilih Laboratorium</label>
                    <select id="lab-select" class="w-full border rounded p-2">
                        <option value="">-- Pilih Lab --</option>
                    </select>
                </div>

                {{-- Set Booking --}}
                <div id="sets-section" class="hidden space-y-4">
                    <div class="flex gap-2">
                        <select id="set-lab-select" class="flex-1 border rounded p-2">
                            <option value="">-- Pilih Lab --</option>
                        </select>
                        <button type="button" id="add-set-lab-btn" class="px-4 py-2 bg-indigo-600 text-white rounded">Tambah</button>
                    </div>
                    <div id="set-labs-container" class="space-y-3"></div>
                </div>

                {{-- Items Booking --}}
                <div id="items-section" class="hidden space-y-4">
                    <div class="flex gap-2">
                        <select id="item-lab-select" class="flex-1 border rounded p-2">
                            <option value="">-- Pilih Lab --</option>
                        </select>
                        <button type="button" id="browse-items-btn" class="px-4 py-2 bg-indigo-600 text-white rounded">Browse Items</button>
                    </div>
                    <div id="cart-container" class="border rounded p-4">
                        <h3 class="font-bold mb-2">Keranjang (<span id="cart-count">0</span> items)</h3>
                        <div id="cart-items" class="space-y-2"></div>
                    </div>
                </div>

                <button type="submit" class="w-full py-3 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700">
                    Ajukan Peminjaman
                </button>
            </form>
        </div>
    </div>
</div>

{{-- Desk Map Modal --}}
<div id="desk-map-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-6xl w-full max-h-[90vh] overflow-auto">
        <div class="p-4 border-b flex justify-between items-center">
            <h3 class="text-xl font-bold">Pilih Items dari <span id="modal-lab-name"></span></h3>
            <button type="button" id="close-modal-btn" class="text-gray-500 hover:text-gray-700">&times;</button>
        </div>
        <div id="desk-grid-container" class="p-6"></div>
    </div>
</div>

{{-- Item Detail Modal --}}
<div id="item-detail-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-2xl w-full">
        <div class="p-4 border-b flex justify-between items-center">
            <h3 class="text-xl font-bold">Items di Meja <span id="desk-location"></span></h3>
            <button type="button" id="close-detail-modal-btn" class="text-gray-500 hover:text-gray-700">&times;</button>
        </div>
        <div id="item-list-container" class="p-6"></div>
    </div>
</div>

<script>
const csrfToken = '{{ csrf_token() }}';
let allLabs = [];
let cart = [];
let currentDeskMap = [];

document.addEventListener('DOMContentLoaded', async () => {
    await loadLabs();
    setupEventListeners();
});

async function loadLabs() {
    const response = await fetch('/get/labs');
    allLabs = await response.json();
    
    ['lab-select', 'set-lab-select', 'item-lab-select'].forEach(id => {
        const select = document.getElementById(id);
        select.innerHTML = '<option value="">-- Pilih Lab --</option>';
        allLabs.forEach(lab => {
            select.innerHTML += `<option value="${lab.id}">${lab.name}</option>`;
        });
    });
}

function setupEventListeners() {
    document.querySelectorAll('input[name="booking_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.getElementById('lab-section').classList.add('hidden');
            document.getElementById('sets-section').classList.add('hidden');
            document.getElementById('items-section').classList.add('hidden');
            
            if (this.value === 'lab') {
                document.getElementById('lab-section').classList.remove('hidden');
            } else if (this.value === 'sets') {
                document.getElementById('sets-section').classList.remove('hidden');
            } else if (this.value === 'items') {
                document.getElementById('items-section').classList.remove('hidden');
            }
        });
    });

    document.getElementById('add-set-lab-btn').addEventListener('click', addSetLab);
    document.getElementById('browse-items-btn').addEventListener('click', browseItems);
    document.getElementById('close-modal-btn').addEventListener('click', () => {
        document.getElementById('desk-map-modal').classList.add('hidden');
    });
    document.getElementById('close-detail-modal-btn').addEventListener('click', () => {
        document.getElementById('item-detail-modal').classList.add('hidden');
    });
    
    document.getElementById('bookingForm').addEventListener('submit', submitBooking);
}

async function addSetLab() {
    const labId = document.getElementById('set-lab-select').value;
    if (!labId) return alert('Pilih lab terlebih dahulu');
    
    const start = document.getElementById('borrowed_at').value;
    const end = document.getElementById('return_deadline_at').value;
    if (!start || !end) return alert('Isi tanggal peminjaman terlebih dahulu');
    
    const response = await fetch(`/labs/${labId}/available-sets?start=${start}&end=${end}`);
    const data = await response.json();
    
    const lab = allLabs.find(l => l.id === labId);
    const container = document.getElementById('set-labs-container');
    
    const div = document.createElement('div');
    div.className = 'border rounded p-4';
    div.innerHTML = `
        <div class="flex justify-between items-center mb-2">
            <span class="font-bold">${lab.name}</span>
            <button type="button" class="text-red-500" onclick="this.parentElement.parentElement.remove()">Hapus</button>
        </div>
        <div>
            <label class="text-sm">Jumlah Set (Tersedia: ${data.available_count})</label>
            <input type="number" min="1" max="${data.available_count}" class="w-full border rounded p-2" data-lab-id="${labId}" name="set_quantity">
        </div>
    `;
    container.appendChild(div);
}

async function browseItems() {
    const labId = document.getElementById('item-lab-select').value;
    if (!labId) return alert('Pilih lab terlebih dahulu');
    
    const start = document.getElementById('borrowed_at').value;
    const end = document.getElementById('return_deadline_at').value;
    if (!start || !end) return alert('Isi tanggal peminjaman terlebih dahulu');
    
    const response = await fetch(`/labs/${labId}/desk-map?start=${start}&end=${end}`);
    currentDeskMap = await response.json();
    
    const lab = allLabs.find(l => l.id === labId);
    document.getElementById('modal-lab-name').textContent = lab.name;
    
    renderDeskMap();
    document.getElementById('desk-map-modal').classList.remove('hidden');
}

function renderDeskMap() {
    const container = document.getElementById('desk-grid-container');
    let maxRow = 5, maxCol = 10;
    
    currentDeskMap.forEach(d => {
        const row = d.location.charCodeAt(0) - 64;
        const col = parseInt(d.location.substring(1));
        if (row > maxRow) maxRow = row;
        if (col > maxCol) maxCol = col;
    });
    
    let html = `<div class="grid gap-3" style="grid-template-columns: repeat(${maxCol}, minmax(100px, 1fr)); grid-template-rows: repeat(${maxRow}, auto);">`;
    
    currentDeskMap.forEach(desk => {
        const row = desk.location.charCodeAt(0) - 64;
        const col = parseInt(desk.location.substring(1));
        const hasAvailable = desk.items.some(i => i.available);
        
        html += `
            <div style="grid-area: ${row}/${col}" 
                 class="border-2 rounded p-3 text-center cursor-pointer ${hasAvailable ? 'bg-green-50 border-green-300 hover:bg-green-100' : 'bg-gray-100 border-gray-300'}"
                 onclick='showDeskItems(${JSON.stringify(desk)})'>
                <div class="font-bold">${desk.location}</div>
                <div class="text-xs">${desk.items.length} items</div>
            </div>
        `;
    });
    
    html += '</div>';
    container.innerHTML = html;
}

function showDeskItems(desk) {
    document.getElementById('desk-location').textContent = desk.location;
    
    let html = '<div class="space-y-2">';
    desk.items.forEach(item => {
        const inCart = cart.some(c => c.id === item.id);
        html += `
            <div class="border rounded p-3 ${item.available ? 'bg-white' : 'bg-gray-100'}">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="font-bold">${item.name}</div>
                        <div class="text-sm text-gray-600">${item.type} - ${item.serial_code}</div>
                        ${item.components.length > 0 ? `<div class="text-xs text-purple-600">${item.components.length} components</div>` : ''}
                    </div>
                    ${item.available && !inCart ? 
                        `<button onclick='addToCart(${JSON.stringify(item)})' class="px-3 py-1 bg-indigo-600 text-white rounded text-sm">Tambah</button>` :
                        inCart ? '<span class="text-green-600 text-sm">✓ Di keranjang</span>' :
                        '<span class="text-red-600 text-sm">Tidak tersedia</span>'
                    }
                </div>
            </div>
        `;
    });
    html += '</div>';
    
    document.getElementById('item-list-container').innerHTML = html;
    document.getElementById('item-detail-modal').classList.remove('hidden');
}

function addToCart(item) {
    if (cart.some(c => c.id === item.id)) return;
    cart.push(item);
    updateCart();
    document.getElementById('item-detail-modal').classList.add('hidden');
}

function updateCart() {
    document.getElementById('cart-count').textContent = cart.length;
    const container = document.getElementById('cart-items');
    
    if (cart.length === 0) {
        container.innerHTML = '<p class="text-gray-500 text-sm">Keranjang kosong</p>';
        return;
    }
    
    container.innerHTML = cart.map(item => `
        <div class="flex justify-between items-center border-b pb-2">
            <div>
                <div class="font-medium text-sm">${item.name}</div>
                <div class="text-xs text-gray-600">${item.type}</div>
            </div>
            <button onclick="removeFromCart('${item.id}')" class="text-red-500 text-sm">Hapus</button>
        </div>
    `).join('');
}

function removeFromCart(itemId) {
    cart = cart.filter(c => c.id !== itemId);
    updateCart();
}

async function submitBooking(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const bookingType = formData.get('booking_type');
    
    let payload = {
        booking_type: bookingType,
        event_name: formData.get('event_name'),
        borrowed_at: formData.get('borrowed_at'),
        return_deadline_at: formData.get('return_deadline_at'),
        phone_number: formData.get('phone_number'),
        type: formData.get('type'),
        event_started_at: formData.get('borrowed_at'),
        event_ended_at: formData.get('return_deadline_at'),
    };
    
    if (bookingType === 'lab') {
        payload.bookable_type = 'lab';
        payload.bookable_id = document.getElementById('lab-select').value;
    } else if (bookingType === 'sets') {
        const setInputs = document.querySelectorAll('input[name="set_quantity"]');
        payload.sets = Array.from(setInputs).map(input => ({
            lab_id: input.dataset.labId,
            quantity: parseInt(input.value)
        }));
    } else if (bookingType === 'items') {
        payload.items = cart.map(item => item.id);
    }
    
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
        
        if (data.success) {
            alert('Booking berhasil diajukan!');
            window.location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        alert('Terjadi kesalahan: ' + error.message);
    }
}
</script>
@endsection
