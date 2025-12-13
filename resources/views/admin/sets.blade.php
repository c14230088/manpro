@extends('layouts.admin')

@section('title', 'Manajemen Sets')

@section('body')
    <div class="flex flex-col md:flex-row w-full py-4 shadow-md items-center justify-center mb-5 px-6 md:px-4">
        <h1 class="text-center text-4xl uppercase font-bold mb-2 md:mb-0">Sets Management</h1>
    </div>

    {{-- Filter Section --}}
    <div class="max-w-7xl mx-auto px-6 pb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Filter Kontrol</h2>
            <div class="filter-select">
                <label for="filter_location" class="block text-sm font-semibold text-gray-700 mb-2">Lokasi</label>
                <select id="filter_location" placeholder="Semua Lokasi...">
                    <option value="unattached">Belum Terpasang</option>
                    @foreach ($labs as $lab)
                        <option value="{{ $lab->id }}">{{ $lab->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <div class="relative flex w-full">
                    <div class="relative flex w-full flex-wrap items-stretch">
                        <input id="datatable-search-input" type="search"
                            class="relative m-0 -mr-0.5 block w-[1px] min-w-0 flex-auto rounded-l border border-solid border-neutral-300 bg-transparent bg-clip-padding px-3 py-[0.6rem] text-base font-normal leading-[1.6] text-neutral-700 outline-none transition duration-200 ease-in-out focus:z-[3] focus:border-primary focus:text-neutral-700 focus:shadow-[inset_0_0_0_1px_rgb(59,113,202)] focus:outline-none"
                            placeholder="Cari nama Set, Item didalamnya, atau Lokasinya..." aria-label="Search" />
                        <button
                            class="relative z-[2] flex items-center rounded-r bg-primary px-6 py-2.5 text-xs font-medium uppercase leading-tight text-white shadow-md transition duration-150 ease-in-out hover:bg-primary-700 hover:shadow-lg focus:bg-primary-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-primary-800 active:shadow-lg"
                            type="button">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5">
                                <path fill-rule="evenodd"
                                    d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
                <button id="reset-filter-btn"
                    class="px-6 py-2 bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-300 transition-colors">
                    Reset Filter
                </button>
            </div>
        </div>
    </div>

    {{-- Table Section --}}
    <div class="max-w-7xl mx-auto px-6 pb-12">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">

            {{-- Tombol Tambah Set --}}
            <div class="flex justify-end mb-4">
                <button id="open-create-set-modal-btn" type="button"
                    class="px-6 py-3 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition-colors shadow-lg">
                    + Tambah Set Item
                </button>
            </div>

            {{-- Container Wrapper untuk Scroll & Sticky Header --}}
            <div id="datatable-container">
                <table class="min-w-full divide-y divide-gray-200" id="sets-datatable">
                    <thead class="bg-gray-50">
                        <tr>
                            {{-- PENTING: Hapus data-te-datatable-sortable="true" agar JS tidak bentrok --}}
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nama Set
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Items
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Lokasi
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($sets as $set)
                            @php
                                $allAttached = $set->items->every(fn($i) => $i->desk_id !== null);
                                $locations = $set->items
                                    ->map(
                                        fn($i) => $i->desk
                                            ? $i->desk->lab->name . ' - ' . $i->desk->location
                                            : 'Belum Terpasang',
                                    )
                                    ->unique();
                            @endphp
                            <tr
                                data-location="{{ $allAttached ? $set->items->first()->desk->lab_id ?? '' : 'unattached' }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">{{ $set->name }}</div>
                                    @if ($set->note)
                                        <div class="text-xs text-gray-600">{{ $set->note }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-600">
                                        @foreach ($set->items as $item)
                                            <div>{{ $item->type->name ?? 'N/A' }}: {{ $item->name }}</div>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if ($allAttached)
                                        @foreach ($locations as $loc)
                                            <div class="text-sm text-gray-600">{{ $loc }}</div>
                                        @endforeach
                                    @else
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-700">Belum
                                            Terpasang</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <button type="button"
                                        class="btn-open-action-set px-4 py-2 bg-indigo-600 text-gray-100 hover:text-gray-200 text-xs font-semibold rounded-md hover:bg-indigo-800 focus:outline-none"
                                        data-set-id="{{ $set->id }}" data-set-name="{{ $set->name }}"
                                        data-all-attached="{{ $allAttached ? '1' : '0' }}">
                                        Aksi
                                    </button>
                                </td>
                            </tr>
                        @empty
                            {{-- Baris kosong ini opsional karena TE punya noFoundMessage, tapi Baik untuk server-side render awal --}}
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500">Tidak ada data set.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MODALS SECTION (Tidak Berubah) --}}
    <div id="action-modal" class="hidden" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 z-[2000]"></div>
        <div id="action-modal-overlay" class="fixed inset-0 flex items-center justify-center p-4 z-[2001]">
            <div class="relative bg-white rounded-lg shadow-xl w-full max-w-lg flex flex-col max-h-[90vh]">
                <div class="flex justify-between items-center p-4 border-b border-gray-200 rounded-t">
                    <h3 id="action-modal-title" class="text-xl font-semibold text-gray-900">Aksi untuk Set</h3>
                    <button id="action-modal-close-btn" type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 14 14">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                    </button>
                </div>
                <div class="p-6 grid grid-cols-2 gap-4">
                    <button id="action-btn-details"
                        class="p-6 bg-green-500 hover:bg-green-600 text-white rounded-lg flex flex-col items-center justify-center transition-all shadow-lg">
                        <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                            </path>
                        </svg>
                        <span class="font-semibold">Lihat Detail</span>
                    </button>
                    <button id="action-btn-attach"
                        class="p-6 bg-blue-500 hover:bg-blue-600 text-white rounded-lg flex flex-col items-center justify-center transition-all shadow-lg">
                        <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z">
                            </path>
                        </svg>
                        <span class="font-semibold">Pasang ke Meja</span>
                    </button>
                    <button id="action-btn-attach-lab"
                        class="p-6 bg-indigo-500 hover:bg-indigo-600 text-white rounded-lg flex flex-col items-center justify-center transition-all shadow-lg">
                        <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <span class="font-semibold">Pasang ke Lab</span>
                    </button>
                    <button id="action-btn-detach-desk"
                        class="p-6 bg-red-500 hover:bg-red-600 text-white rounded-lg flex flex-col items-center justify-center transition-all shadow-lg">
                        <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <span class="font-semibold">Lepas dari Meja</span>
                    </button>
                    <button id="action-btn-detach-lab"
                        class="p-6 bg-indigo-500 hover:bg-indigo-600 text-white rounded-lg flex flex-col items-center justify-center transition-all shadow-lg">
                        <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <span class="font-semibold">Lepas dari Lab</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="detail-modal" class="hidden" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 z-[3000]"></div>
        <div id="detail-modal-overlay" class="fixed inset-0 flex items-center justify-center p-4 z-[3001]">
            <div class="relative bg-white rounded-lg shadow-xl w-full max-w-4xl flex flex-col max-h-[90vh]">
                <div class="flex justify-between items-center p-4 border-b border-gray-200 rounded-t">
                    <h3 class="text-xl font-semibold text-gray-900">Detail Set: <span id="detail-set-name"
                            class="text-indigo-600"></span></h3>
                    <button id="detail-modal-close-btn" type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 14 14">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                    </button>
                </div>
                <div id="detail-modal-content" class="p-6 space-y-4 overflow-y-auto">
                    <div class="flex items-center justify-center py-12">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="attach-desk-modal" class="hidden" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 z-[4000]"></div>
        <div id="attach-desk-modal-overlay" class="fixed inset-0 flex items-center justify-center p-4 z-[4001]">
            <div class="relative bg-white rounded-lg shadow-xl w-full max-w-6xl flex flex-col max-h-[90vh]">
                <div class="flex justify-between items-center p-4 border-b border-gray-200 rounded-t">
                    <h3 class="text-xl font-semibold text-gray-900">Pasang Set: <span id="attach-set-name"
                            class="text-indigo-600"></span></h3>
                    <button id="attach-desk-modal-close-btn" type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 14 14">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                    </button>
                </div>
                <div class="p-6 space-y-4 overflow-y-auto">
                    <div class="bg-gray-100 rounded-lg p-4">
                        <label for="lab-selector-modal" class="block text-sm font-semibold text-gray-700 mb-2">Pilih
                            Laboratorium</label>
                        <select id="lab-selector-modal" placeholder="Pilih Lab..."></select>
                    </div>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <p class="text-sm text-yellow-800"><strong>Instruksi:</strong> Pilih 1 meja untuk memasang semua 4
                            item dalam set ini.</p>
                        <div id="selected-desk-display" class="mt-2 text-xs text-gray-600">Belum ada meja dipilih.</div>
                    </div>
                    <div id="desk-grid-container-modal">
                        <div class="text-center py-12 text-gray-500">Pilih lab untuk melihat denah.</div>
                    </div>
                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <button id="attach-confirm-btn"
                            class="px-6 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition-colors disabled:bg-gray-400"
                            disabled>Pasang Set ke 1 Meja</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Create Set --}}
    <div id="create-set-modal" class="hidden" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 z-[5000]"></div>
        <div id="create-set-modal-overlay" class="fixed inset-0 flex items-center justify-center p-4 z-[5001]">
            <div class="relative bg-white rounded-lg shadow-xl w-full max-w-5xl flex flex-col max-h-[90vh]">

                {{-- PERBAIKAN: Gunakan data-action agar terbaca oleh form.dataset.action di JS --}}
                <form id="create-set-form" data-action="{{ route('admin.items.set.create') }}"
                    class="space-y-0 flex flex-col max-h-[90vh]">
                    @csrf
                    <div class="flex justify-between items-center p-4 border-b border-gray-200 rounded-t">
                        <h3 class="text-xl font-semibold text-gray-900">Buat Set Item Baru (4 Item)</h3>
                        <button id="create-set-modal-close-btn" type="button"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 14 14">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                            </svg>
                        </button>
                    </div>

                    {{-- Isi Modal (Sama seperti sebelumnya) --}}
                    <div class="p-6 space-y-6 overflow-y-auto bg-gray-50 flex-1">
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <h2 class="text-xl font-semibold text-gray-800 mb-6">Detail Set</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="set_name" class="block text-sm font-semibold text-gray-700 mb-2">Nama
                                        Set</label>
                                    <input type="text" id="set_name" name="set_name" required
                                        class="block w-full px-4 py-3 text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                        placeholder="cth: Set PC Meja A1">
                                </div>
                                <div>
                                    <label for="set_note" class="block text-sm font-semibold text-gray-700 mb-2">Catatan
                                        <span class="text-gray-400 font-normal">(Opsional)</span></label>
                                    <input type="text" id="set_note" name="set_note"
                                        class="block w-full px-4 py-3 text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                        placeholder="cth: Pembelian 2025">
                                </div>
                            </div>
                        </div>
                        <div id="set-items-container" class="space-y-6"></div>

                        {{-- Bagian Pasang ke Meja --}}
                        <div class="bg-blue-50 rounded-xl shadow-sm border border-blue-200 p-6">
                            <div class="flex items-center mb-4">
                                <input type="checkbox" id="attach_to_lab_checkbox"
                                    class="h-4 w-4 cursor-pointer text-indigo-600 border-gray-300 rounded">
                                <label for="attach_to_lab_checkbox"
                                    class="ml-2 block cursor-pointer text-sm font-semibold text-gray-700">Pasang Set ke Lab</label>
                            </div>
                            <div id="lab-selection-section" class="hidden space-y-4">
                                <div>
                                    <label for="set-lab-selector"
                                        class="block text-sm font-semibold text-gray-700 mb-2">Pilih Laboratorium</label>
                                    <select id="set-lab-selector" placeholder="Pilih Lab..."></select>
                                </div>
                                <div class="flex items-center mt-4">
                                    <input type="checkbox" id="attach_to_desk_checkbox"
                                        class="h-4 w-4 cursor-pointer text-indigo-600 border-gray-300 rounded">
                                    <label for="attach_to_desk_checkbox"
                                        class="ml-2 block cursor-pointer text-sm font-semibold text-gray-700">Pasang Set ke Meja</label>
                                </div>
                                <div id="desk-attachment-section" class="hidden space-y-4">
                                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                        <p class="text-sm text-yellow-800"><strong>Instruksi:</strong> Pilih 1 meja untuk
                                            memasang semua item dalam set ini.</p>
                                        <div id="set-selected-desks-display" class="mt-2 text-sm font-bold text-indigo-600">
                                            Belum ada meja dipilih.</div>
                                    </div>
                                    <div id="set-desk-grid-container">
                                        <div class="text-center py-8 text-gray-500">Pilih lab untuk melihat denah.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Footer Modal --}}
                    <div class="flex items-center justify-end p-4 space-x-3 border-t border-gray-200 rounded-b bg-gray-50">
                        <button id="create-set-modal-cancel-btn" type="button"
                            class="text-gray-700 bg-white hover:bg-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 border border-gray-300">Batal</button>
                        <button type="button" id="submit-set-btn"
                            class="text-white bg-green-600 hover:bg-green-700 font-medium rounded-lg text-sm px-5 py-2.5 w-48 h-[42px] flex items-center justify-center">
                            <span class="btn-text">Simpan Set</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Templates --}}
    <template id="spec-row-template">
        <div class="grid grid-cols-12 gap-4 spec-row p-4 border border-gray-200 rounded-lg bg-gray-50">
            <div class="col-span-5"><label class="block text-xs font-medium text-gray-600 mb-1">Attribute</label><select
                    class="spec-attribute" placeholder="Cari attribute..."></select></div>
            <div class="col-span-5"><label class="block text-xs font-medium text-gray-600 mb-1">Value</label><select
                    class="spec-value" placeholder="Pilih attribute dulu..."></select></div>
            <div class="col-span-2 flex items-end"><button type="button"
                    class="remove-spec-btn w-full px-4 py-3 bg-rose-50 text-rose-600 text-sm font-semibold rounded-lg hover:bg-rose-100">Hapus</button>
            </div>
        </div>
    </template>

    <template id="new-component-form-template">
        <div class="new-component-row border-2 border-purple-200 bg-purple-50 rounded-lg p-6 relative">
            <button type="button"
                class="remove-new-component-btn absolute -top-3 -right-3 w-8 h-8 bg-rose-500 hover:bg-rose-600 text-white rounded-full flex items-center justify-center shadow-lg"><svg
                    class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg></button>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div><label class="block text-sm font-semibold text-gray-700 mb-2">Nama Komponen</label><input
                        type="text"
                        class="new-component-name block w-full px-4 py-3 text-base border border-gray-300 rounded-lg"
                        placeholder="cth: RAM 16GB"></div>
                <div><label class="block text-sm font-semibold text-gray-700 mb-2">Serial Code</label><input
                        type="text"
                        class="new-component-serial block w-full px-4 py-3 text-base border border-gray-300 rounded-lg"
                        placeholder="cth: SN-RAM-001"></div>
                <div><label class="block text-sm font-semibold text-gray-700 mb-2">Tgl. Produksi Komponen</label><input
                        type="date"
                        class="new-component-produced-at block w-full px-4 py-3 text-base border border-gray-300 rounded-lg">
                </div>
                <div><label class="block text-sm font-semibold text-gray-700 mb-2">Kondisi Komponen</label>
                    <div class="flex items-center space-x-6 pt-3"><label class="flex items-center"><input type="radio"
                                class="new-component-condition" name="component_condition_NEW-INDEX" value="1"
                                class="h-4 w-4 text-purple-600" checked><span
                                class="ml-2 text-gray-700">Baik</span></label><label class="flex items-center"><input
                                type="radio" class="new-component-condition" name="component_condition_NEW-INDEX"
                                value="0" class="h-4 w-4 text-purple-600"><span
                                class="ml-2 text-gray-700">Rusak</span></label></div>
                </div>
                <div class="md:col-span-2"><label class="block text-sm font-semibold text-gray-700 mb-2">Type
                        Komponen</label><select class="new-component-type-select"
                        placeholder="Pilih Tipe Komponen..."></select></div>
            </div>
            <div class="mt-6 pt-6 border-t border-purple-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Spesifikasi Komponen</h3><button type="button"
                        class="btn-add-new-comp-spec px-3 py-1 bg-indigo-600 text-white text-xs font-semibold rounded-lg hover:bg-indigo-700">+
                        Tambah Spek</button>
                </div>
                <div class="new-component-specs-container space-y-4"></div>
            </div>
        </div>
    </template>

    <template id="set-item-template">
        <div class="set-item-row bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-6 set-item-title">Item 1</h3>
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <input type="hidden" class="set-item-is-component" value="0">
                    <div><label class="block text-sm font-semibold text-gray-700 mb-2">Nama Item</label><input
                            type="text"
                            class="set-item-name block w-full px-4 py-3 text-base border border-gray-300 rounded-lg"
                            placeholder="cth: Monitor LG" required></div>
                    <div><label class="block text-sm font-semibold text-gray-700 mb-2">Serial Code</label><input
                            type="text"
                            class="set-item-serial block w-full px-4 py-3 text-base border border-gray-300 rounded-lg"
                            placeholder="cth: MON-01" required></div>
                    <div><label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Produksi</label><input
                            type="date"
                            class="set-item-produced-at block w-full px-4 py-3 text-base border border-gray-300 rounded-lg">
                    </div>
                    <div><label class="block text-sm font-semibold text-gray-700 mb-2">Kondisi</label>
                        <div class="flex items-center space-x-6 pt-3"><label class="flex items-center"><input
                                    type="radio" class="set-item-condition" name="set_item_condition_INDEX"
                                    value="1" checked><span class="ml-2 text-gray-700">Baik</span></label><label
                                class="flex items-center"><input type="radio" class="set-item-condition"
                                    name="set_item_condition_INDEX" value="0"><span
                                    class="ml-2 text-gray-700">Rusak</span></label></div>
                    </div>
                    <div class="md:col-span-2"><label
                            class="block text-sm font-semibold text-gray-700 mb-2">Type</label><select
                            class="set-item-type-select" placeholder="Pilih Tipe..." required></select></div>
                </div>
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Spesifikasi Item</h3><button type="button"
                            class="btn-add-set-item-spec px-3 py-1 bg-indigo-600 text-white text-xs font-semibold rounded-lg hover:bg-indigo-700">+
                            Tambah Spek</button>
                    </div>
                    <div class="set-item-specs-container space-y-4"></div>
                </div>
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Komponen Bawaan</h3><button type="button"
                            class="btn-add-set-item-comp px-3 py-1 bg-purple-600 text-white text-xs font-semibold rounded-lg hover:bg-purple-700">+
                            Tambah Komponen</button>
                    </div>
                    <div class="set-item-components-container space-y-6"></div>
                </div>
            </div>
        </div>
    </template>
@endsection

@section('script')
    <style>
        .filter-select .ts-control {
            @apply block w-full px-4 py-3 text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white;
            padding-top: 0.6rem;
            padding-bottom: 0.6rem;
        }

        #create-set-modal .ts-control {
            @apply block w-full px-4 py-3 text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white;
            padding-top: 0.6rem;
            padding-bottom: 0.6rem;
        }

        /* FIX: CSS MANUAL UNTUK STICKY HEADER & SCROLL */
        #datatable-container {
            position: relative;
            height: 65vh;
            /* Tinggi tabel diset di sini, bisa 600px atau pake vh */
            overflow-y: auto;
            display: block;
        }

        #sets-datatable thead th {
            position: sticky;
            top: 0;
            z-index: 20;
            /* Lebih tinggi dari konten */
            background-color: #f9fafb;
            /* bg-gray-50 */
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .swal2-input {
            padding: 0 0.75rem !important;
        }
    </style>

    <script>
        document.getElementById('sets').classList.add('bg-slate-100');
        document.getElementById('sets').classList.add('active');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        let currentSetId = null;
        let currentSetName = null;
        let currentAllAttached = false;
        let tomSelectLabModal;
        let modalLabDesks = [];
        let selectedDeskLocations = [];
        let setsDatatableInstance = null; // Global variable

        // --- Helper Functions (Loading, Toast) ---
        function showLoading(title = 'Loading...', text = 'silakan tunggu...') {
            Swal.fire({
                title,
                text,
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
        }

        function hideLoading() {
            Swal.close();
        }
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        function showToast(title, message, icon = 'success') {
            Toast.fire({
                icon,
                title,
                text: message
            });
        }

        // --- Modal & Map Functions ---
        function checkModalState() {
            const isAnyOpen = !document.getElementById('action-modal').classList.contains('hidden') ||
                !document.getElementById('detail-modal').classList.contains('hidden') ||
                !document.getElementById('attach-desk-modal').classList.contains('hidden');
            document.body.style.overflow = isAnyOpen ? 'hidden' : '';
        }

        function initializeActionModal() {
            const modal = document.getElementById('action-modal');
            const closeBtn = document.getElementById('action-modal-close-btn');
            const overlay = document.getElementById('action-modal-overlay');
            const closeModal = () => {
                modal.classList.add('hidden');
                checkModalState();
            };
            closeBtn.addEventListener('click', closeModal);
            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) closeModal();
            });
            document.getElementById('action-btn-details').addEventListener('click', openDetailModal);
            document.getElementById('action-btn-attach').addEventListener('click', openAttachDeskModal);
            document.getElementById('action-btn-attach-lab').addEventListener('click', openAttachLabModal);
            document.getElementById('action-btn-detach-desk').addEventListener('click', detachSetFromDesk);
            document.getElementById('action-btn-detach-lab').addEventListener('click', detachSetFromLab);
        }

        async function openDetailModal() {
            const modal = document.getElementById('detail-modal');
            const nameEl = document.getElementById('detail-set-name');
            const contentEl = document.getElementById('detail-modal-content');
            nameEl.textContent = currentSetName;
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            contentEl.innerHTML =
                '<div class="flex items-center justify-center py-12"><div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div></div>';
            try {
                const response = await fetch(`/admin/sets/${currentSetId}/details`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                if (!response.ok) throw new Error('Gagal mengambil data.');
                const data = await response.json();
                populateDetailModal(data);
            } catch (error) {
                contentEl.innerHTML =
                    `<div class="text-center py-12 text-red-500"><h3 class="font-semibold text-lg">Gagal Memuat Data</h3><p class="text-sm">${error.message}</p></div>`;
            }
        }

        function populateDetailModal(data) {
            const contentEl = document.getElementById('detail-modal-content');
            let html =
                `<div class="space-y-6"><div><h4 class="font-semibold text-gray-800 mb-2">Nama Set:</h4><p class="text-gray-600">${data.name}</p></div>`;
            if (data.note) html +=
                `<div><h4 class="font-semibold text-gray-800 mb-2">Catatan:</h4><p class="text-gray-600">${data.note}</p></div>`;
            if (data.created_at) html +=
                `<div><h4 class="font-semibold text-gray-800 mb-2">Waktu Dicatat:</h4><p class="text-gray-600">${new Date(data.created_at).toLocaleDateString('id-ID')}</p></div>`;
            html +=
                `<div class="border-t pt-4"><h4 class="font-semibold text-gray-800 mb-4">Items dalam Set (${data.items.length}):</h4>`;
            data.items.forEach((item, idx) => {
                const condBadge = item.condition ?
                    '<span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800">Baik</span>' :
                    '<span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-800">Rusak</span>';
                const location = item.desk ? `${item.desk.lab.name} - Meja ${item.desk.location}` :
                    'Belum Terpasang';
                html +=
                    `<div class="bg-gray-50 rounded-lg p-4 mb-3"><div class="flex justify-between items-start mb-2"><h5 class="font-semibold text-gray-900">${idx+1}. ${item.name}</h5>${condBadge}</div><div class="text-sm text-gray-600 space-y-1"><div><strong>Serial:</strong> ${item.serial_code}</div><div><strong>Tipe:</strong> ${item.type ? item.type.name : 'N/A'}</div><div><strong>Lokasi:</strong> ${location}</div>`;
                if (item.spec_set_values?.length > 0) html +=
                    `<div><strong>Spesifikasi:</strong> ${item.spec_set_values.map(s => `${s.spec_attributes.name}: ${s.value}`).join(', ')}</div>`;
                if (item.components?.length > 0) {
                    html +=
                        `<div class="mt-2 pl-4 border-l-2 border-purple-300"><strong>Komponen (${item.components.length}):</strong><ul class="list-disc pl-4 mt-1">`;
                    item.components.forEach(comp => {
                        html += `<li>${comp.name} (${comp.type ? comp.type.name : 'N/A'})</li>`;
                    });
                    html += `</ul></div>`;
                }
                html += `</div></div>`;
            });
            html += `</div></div>`;
            contentEl.innerHTML = html;
        }

        function initializeDetailModal() {
            const modal = document.getElementById('detail-modal');
            const closeBtn = document.getElementById('detail-modal-close-btn');
            const overlay = document.getElementById('detail-modal-overlay');
            const closeModal = () => {
                modal.classList.add('hidden');
                checkModalState();
            };
            closeBtn.addEventListener('click', closeModal);
            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) closeModal();
            });
        }

        function initializeAttachDeskModal() {
            const modal = document.getElementById('attach-desk-modal');
            const closeBtn = document.getElementById('attach-desk-modal-close-btn');
            const overlay = document.getElementById('attach-desk-modal-overlay');
            const labSelectorEl = document.getElementById('lab-selector-modal');
            const confirmBtn = document.getElementById('attach-confirm-btn');
            tomSelectLabModal = new TomSelect(labSelectorEl, {
                create: false,
                placeholder: 'Pilih Lab...',
                onChange: (labId) => {
                    if (labId) {
                        selectedDeskLocations = [];
                        updateSelectedDesksDisplay();
                        fetchDeskMapForModal(labId);
                    }
                }
            });
            const closeModal = () => {
                modal.classList.add('hidden');
                if (tomSelectLabModal) tomSelectLabModal.clear();
                selectedDeskLocations = [];
                updateSelectedDesksDisplay();
                document.getElementById('desk-grid-container-modal').innerHTML =
                    '<div class="text-center py-12 text-gray-500">Pilih lab untuk melihat denah.</div>';
                checkModalState();
            };
            closeBtn.addEventListener('click', closeModal);
            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) closeModal();
            });
            confirmBtn.addEventListener('click', confirmAttachSetToDesks);
        }

        async function openAttachDeskModal() {
            if (currentAllAttached) {
                Swal.fire('Info', 'Set ini sudah terpasang semua di meja.', 'info');
                return;
            }
            const modal = document.getElementById('attach-desk-modal');
            document.getElementById('attach-set-name').textContent = currentSetName;
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            selectedDeskLocations = [];
            updateSelectedDesksDisplay();
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
                modal.classList.add('hidden');
                checkModalState();
            }
        }

        async function fetchDeskMapForModal(labId) {
            const container = document.getElementById('desk-grid-container-modal');
            container.innerHTML =
                '<div class="flex items-center justify-center py-12"><div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div></div>';
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
                showToast('Gagal Memuat Denah', error.message, 'error');
            }
        }

        function renderDeskGridModal(desks, maxRows, maxCols) {
            const container = document.getElementById('desk-grid-container-modal');
            let html =
                `<div class="overflow-x-auto pb-4"><div id="desk-grid-modal" class="grid gap-4 border-2 min-w-fit border-slate-300 p-8" style="grid-template-columns: repeat(${maxCols}, minmax(140px, 1fr)); grid-template-rows: repeat(${maxRows}, auto);">`;

            const occupiedSlots = new Set(desks.map(d => d.location));
            const requiredTypes = ['Monitor', 'Mouse', 'Keyboard', 'CPU'];

            desks.forEach(desk => {
                const row = desk.location.charCodeAt(0) - 64;
                const col = parseInt(desk.location.substring(1));

                // Cek status seleksi dan isi
                const isSelected = selectedDeskLocations.includes(desk.location);
                const hasRequiredType = desk.items && desk.items.some(item => item.type && requiredTypes.includes(
                    item.type.name));

                // LOGIKA WARNA & STYLE:
                // 1. Jika dipilih: Indigo (Prioritas visual tertinggi setelah klik)
                // 2. Jika terisi tapi belum dipilih: Merah
                // 3. Kosong: Abu-abu
                let bgColorClass;
                let iconColor;

                if (isSelected) {
                    bgColorClass =
                        'bg-indigo-200 border-indigo-500 ring-2 ring-indigo-500'; // Tambahkan ring agar jelas terpilih
                    iconColor = 'text-indigo-700';
                } else if (hasRequiredType) {
                    bgColorClass = 'bg-red-50 border-red-300';
                    iconColor = 'text-red-800';
                } else {
                    bgColorClass = 'bg-gray-50 border-gray-300 hover:bg-gray-100';
                    iconColor = 'text-gray-500';
                }

                // PERBAIKAN 1: Cursor selalu pointer, tidak pernah not-allowed
                let cursorClass = 'cursor-pointer';

                html +=
                    `<div data-desk-location="${desk.location}" data-has-required="${hasRequiredType}" style="grid-area: ${row} / ${col};" class="desk-item-modal group transition-all duration-200 flex flex-col items-center justify-center p-5 border-2 rounded-xl min-h-36 ${bgColorClass} ${cursorClass}">
                <div class="text-center pointer-events-none">
                    <div class="mb-2">
                        <svg class="w-8 h-8 mx-auto ${iconColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    </div>
                    <span class="font-bold text-lg text-gray-800 block select-none">${desk.location}</span>
                    ${(hasRequiredType && !isSelected) ? '<span class="text-xs text-red-800 mt-1 inline-block select-none font-semibold">Sudah Terisi Lengkap</span>' : ''}
                    ${isSelected ? '<span class="text-sm text-indigo-700 mt-1 inline-block select-none font-bold uppercase">Terpilih</span>' : ''}
                </div>
            </div>`;
            });

            for (let r = 1; r <= maxRows; r++) {
                for (let c = 1; c <= maxCols; c++) {
                    if (!occupiedSlots.has(`${String.fromCharCode(64 + r)}${c}`)) html +=
                        `<div class="empty-slot-modal" style="grid-area: ${r} / ${c}; visibility: hidden;"></div>`;
                }
            }
            html += '</div></div>';
            container.innerHTML = html;

            // EVENT LISTENER
            document.querySelectorAll('.desk-item-modal').forEach(deskEl => {
                deskEl.addEventListener('click', async () => {
                    const hasRequired = deskEl.dataset.hasRequired === 'true';

                    // Logika Konfirmasi
                    if (hasRequired) {
                        const result = await Swal.fire({
                            title: 'Meja Sudah Terisi Lengkap',
                            text: 'Meja ini sudah memiliki semua set PC Lengkap. Yakin ingin menimpa/menambah di sini?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Ya, Lanjutkan',
                            cancelButtonText: 'Batal',
                            confirmButtonColor: '#f59e0b'
                        });

                        // Jika user klik Batal, hentikan proses (return)
                        if (!result.isConfirmed) return;
                    }

                    // PERBAIKAN 2: Jika lolos (tidak ada required OR user klik Ya), jalankan logika select
                    selectedDeskLocations = [deskEl.dataset.deskLocation];
                    updateSelectedDesksDisplay();

                    // Re-render grid untuk memperbarui tampilan visual (Class Indigo)
                    renderDeskGridModal(modalLabDesks, maxRows, maxCols);
                });
            });
        }

        function updateSelectedDesksDisplay() {
            const display = document.getElementById('selected-desk-display');
            const confirmBtn = document.getElementById('attach-confirm-btn');
            if (selectedDeskLocations.length === 0) {
                display.innerHTML = 'Belum ada meja dipilih.';
                confirmBtn.disabled = true;
            } else {
                display.innerHTML = `Meja dipilih: ${selectedDeskLocations[0]}`;
                confirmBtn.disabled = false;
            }
        }

        async function confirmAttachSetToDesks() {
            if (selectedDeskLocations.length !== 1) {
                Swal.fire('Error', 'Anda harus memilih 1 meja.', 'error');
                return;
            }
            const result = await Swal.fire({
                title: 'Pasang Set ke Meja?',
                text: `Set '${currentSetName}' akan dipasang ke meja: ${selectedDeskLocations[0]}`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Pasang!',
                cancelButtonText: 'Batal'
            });
            if (!result.isConfirmed) return;
            showLoading('Memasang Set...');
            try {
                const response = await fetch(`/admin/sets/${currentSetId}/attach-desks`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        lab_id: tomSelectLabModal.getValue(),
                        desk_location: selectedDeskLocations[0]
                    })
                });
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Gagal memasang set.');
                hideLoading();
                showToast('Berhasil!', data.message, 'success');
                document.getElementById('attach-desk-modal').classList.add('hidden');
                document.getElementById('action-modal').classList.add('hidden');
                checkModalState();
                setTimeout(() => location.reload(), 1000);
            } catch (error) {
                hideLoading();
                Swal.fire('Gagal', error.message, 'error');
            }
        }



        // --- DATA FROM BLADE ---
        const allTypes = @json($types);
        const allSpecAttributes = @json($specification);
        let setItemTomInstances = [];
        let setItemComponentIndex = 0;
        let tomSelectSetLab;
        let setLabDesks = [];
        let setSelectedDeskLocations = [];

        // --- CREATE SET MODAL ---
        function initializeCreateSetModal() {
            const modal = document.getElementById('create-set-modal');
            const form = document.getElementById('create-set-form');
            const submitBtn = document.getElementById('submit-set-btn');
            const closeBtn = document.getElementById('create-set-modal-close-btn');
            const cancelBtn = document.getElementById('create-set-modal-cancel-btn');
            const overlay = document.getElementById('create-set-modal-overlay');
            const openBtn = document.getElementById('open-create-set-modal-btn');
            const itemsContainer = document.getElementById('set-items-container');
            const attachLabCheckbox = document.getElementById('attach_to_lab_checkbox');
            const attachDeskCheckbox = document.getElementById('attach_to_desk_checkbox');
            const labSection = document.getElementById('lab-selection-section');
            const deskSection = document.getElementById('desk-attachment-section');
            const setLabSelectorEl = document.getElementById('set-lab-selector');
            const prefilledTypes = ['Monitor', 'Mouse', 'CPU', 'Keyboard'];
            const closeModal = () => {
                modal.classList.add('hidden');
                checkModalState();
            };
            openBtn.addEventListener('click', () => {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                form.reset();
                itemsContainer.innerHTML = '';
                setItemTomInstances.forEach(item => {
                    item.typeSelect.destroy();
                    item.mainSpecs.forEach(spec => {
                        spec.attr.destroy();
                        spec.val.destroy();
                    });
                    item.newComponents.forEach(comp => {
                        comp.typeSelect.destroy();
                        comp.specInstances.forEach(spec => {
                            spec.attr.destroy();
                            spec.val.destroy();
                        });
                    });
                });
                setItemTomInstances = [];
                setItemComponentIndex = 0;
                for (let i = 0; i < 4; i++) addSetItemCard(i, prefilledTypes[i] || `Item ${i+1}`);
            });
            closeBtn.addEventListener('click', closeModal);
            cancelBtn.addEventListener('click', closeModal);
            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) closeModal();
            });
            submitBtn.addEventListener('click', async function() {
                await submitCreateSetForm(this, form);
            });
            
            attachLabCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    labSection.classList.remove('hidden');
                    loadLabsForSetAttachment();
                } else {
                    labSection.classList.add('hidden');
                    attachDeskCheckbox.checked = false;
                    deskSection.classList.add('hidden');
                    setSelectedDeskLocations = [];
                    updateSetSelectedDesksDisplay();
                    if (tomSelectSetLab) tomSelectSetLab.clear();
                }
            });
            
            attachDeskCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    const selectedLab = tomSelectSetLab.getValue();
                    if (!selectedLab) {
                        Swal.fire('Perhatian', 'Pilih laboratorium terlebih dahulu.', 'warning');
                        this.checked = false;
                        return;
                    }
                    deskSection.classList.remove('hidden');
                    fetchDeskMapForSet(selectedLab);
                } else {
                    deskSection.classList.add('hidden');
                    setSelectedDeskLocations = [];
                    updateSetSelectedDesksDisplay();
                }
            });
            tomSelectSetLab = new TomSelect(setLabSelectorEl, {
                create: false,
                placeholder: 'Pilih Lab...',
                onChange: (labId) => {
                    if (labId) {
                        setSelectedDeskLocations = [];
                        updateSetSelectedDesksDisplay();
                        if (attachDeskCheckbox.checked) {
                            fetchDeskMapForSet(labId);
                        }
                    }
                }
            });
        }

        function addSetItemCard(index, prefilledTypeName) {
            const itemsContainer = document.getElementById('set-items-container'),
                itemTemplate = document.getElementById('set-item-template'),
                newItemCard = itemTemplate.content.cloneNode(true).firstElementChild;
            newItemCard.dataset.itemIndex = index;
            newItemCard.querySelector('.set-item-title').textContent = `Item ${index + 1}: ${prefilledTypeName}`;
            newItemCard.querySelectorAll('.set-item-condition').forEach(radio => {
                radio.name = `set_item_condition_${index}`;
            });
            const typeSelectEl = newItemCard.querySelector('.set-item-type-select'),
                addSpecBtn = newItemCard.querySelector('.btn-add-set-item-spec'),
                specContainer = newItemCard.querySelector('.set-item-specs-container'),
                addCompBtn = newItemCard.querySelector('.btn-add-set-item-comp'),
                compContainer = newItemCard.querySelector('.set-item-components-container');
            const typeId = findTypeIdByName(prefilledTypeName);
            const typeTomSelect = new TomSelect(typeSelectEl, {
                options: allTypes.map(type => ({
                    value: type.id,
                    text: type.name
                })),
                plugins: ['dropdown_input', 'clear_button'],
                create: async function(input, callback) {
                    const isConfirmed = await confirmCreate(input, 'Type');
                    if (isConfirmed) callback({
                        value: `new::${input}`,
                        text: input
                    });
                    else callback();
                },
                render: {
                    option_create: (data, escape) =>
                        `<div class="create">Tambah type baru: <strong>${escape(data.input)}</strong>&hellip;</div>`
                }
            });
            if (typeId) typeTomSelect.setValue(typeId);
            const itemInstanceData = {
                itemIndex: index,
                row: newItemCard,
                typeSelect: typeTomSelect,
                mainSpecs: [],
                newComponents: []
            };
            addSpecBtn.addEventListener('click', () => {
                const newSpecRow = document.getElementById('spec-row-template').content.cloneNode(true)
                    .firstElementChild;
                specContainer.appendChild(newSpecRow);
                const specTomSelects = initializeSpecRow(newSpecRow);
                if (!specTomSelects) return;
                const specInstance = {
                    row: newSpecRow,
                    attr: specTomSelects.attr,
                    val: specTomSelects.val
                };
                itemInstanceData.mainSpecs.push(specInstance);
                newSpecRow.querySelector('.remove-spec-btn').addEventListener('click', () => {
                    specTomSelects.attr.destroy();
                    specTomSelects.val.destroy();
                    newSpecRow.remove();
                    itemInstanceData.mainSpecs = itemInstanceData.mainSpecs.filter(inst => inst.row !==
                        newSpecRow);
                });
            });
            addCompBtn.addEventListener('click', () => {
                const newCompRow = document.getElementById('new-component-form-template').content.cloneNode(true)
                    .firstElementChild,
                    compIndex = setItemComponentIndex++;
                newCompRow.dataset.compIndex = compIndex;
                newCompRow.querySelectorAll('.new-component-condition').forEach(radio => {
                    radio.name = `set_item_${index}_component_condition_${compIndex}`;
                });
                const compTypeSelectEl = newCompRow.querySelector('.new-component-type-select'),
                    addCompSpecBtn = newCompRow.querySelector('.btn-add-new-comp-spec'),
                    newCompSpecContainer = newCompRow.querySelector('.new-component-specs-container'),
                    removeCompBtn = newCompRow.querySelector('.remove-new-component-btn');
                const compTypeTomSelect = new TomSelect(compTypeSelectEl, {
                    options: allTypes.map(type => ({
                        value: type.id,
                        text: type.name
                    })),
                    plugins: ['dropdown_input', 'clear_button'],
                    create: async function(input, callback) {
                        const isConfirmed = await confirmCreate(input, 'Type');
                        if (isConfirmed) callback({
                            value: `new::${input}`,
                            text: input
                        });
                        else callback();
                    },
                    render: {
                        option_create: (data, escape) =>
                            `<div class="create">Tambah type baru: <strong>${escape(data.input)}</strong>&hellip;</div>`
                    }
                });
                const componentInstanceData = {
                    id: compIndex,
                    row: newCompRow,
                    typeSelect: compTypeTomSelect,
                    specInstances: []
                };
                itemInstanceData.newComponents.push(componentInstanceData);
                addCompSpecBtn.addEventListener('click', () => {
                    const newSpecRow = document.getElementById('spec-row-template').content.cloneNode(true)
                        .firstElementChild;
                    newCompSpecContainer.appendChild(newSpecRow);
                    const specTomSelects = initializeSpecRow(newSpecRow);
                    if (!specTomSelects) return;
                    const specInstance = {
                        row: newSpecRow,
                        attr: specTomSelects.attr,
                        val: specTomSelects.val
                    };
                    componentInstanceData.specInstances.push(specInstance);
                    newSpecRow.querySelector('.remove-spec-btn').addEventListener('click', () => {
                        specTomSelects.attr.destroy();
                        specTomSelects.val.destroy();
                        newSpecRow.remove();
                        componentInstanceData.specInstances = componentInstanceData.specInstances
                            .filter(inst => inst.row !== newSpecRow);
                    });
                });
                removeCompBtn.addEventListener('click', () => {
                    compTypeTomSelect.destroy();
                    componentInstanceData.specInstances.forEach(spec => {
                        spec.attr.destroy();
                        spec.val.destroy();
                    });
                    newCompRow.remove();
                    itemInstanceData.newComponents = itemInstanceData.newComponents.filter(inst => inst
                        .id !== compIndex);
                });
                compContainer.appendChild(newCompRow);
            });
            setItemTomInstances.push(itemInstanceData);
            itemsContainer.appendChild(newItemCard);
        }

        function initializeSpecRow(rowElement) {
            const attrSelectEl = rowElement.querySelector('.spec-attribute'),
                valSelectEl = rowElement.querySelector('.spec-value');
            if (!attrSelectEl || !valSelectEl) return null;
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
                    if (isConfirmed) callback({
                        value: `new::${input}`,
                        text: input,
                        spec_values: []
                    });
                    else callback();
                },
                render: {
                    option_create: (data, escape) =>
                        `<div class="create">Tambah attribute baru: <strong>${escape(data.input)}</strong>&hellip;</div>`
                },
                onChange: function(selectedAttrId) {
                    if (valueTomSelect) {
                        valueTomSelect.clear();
                        valueTomSelect.clearOptions();
                        valueTomSelect.disable();
                        if (!selectedAttrId) return;
                        let selectedValues = [];
                        if (!String(selectedAttrId).startsWith('new::')) {
                            const attributeData = this.options[selectedAttrId];
                            if (attributeData && attributeData.spec_values) selectedValues = attributeData
                                .spec_values.map(val => ({
                                    value: val.id,
                                    text: val.value
                                }));
                        }
                        valueTomSelect.addOptions(selectedValues);
                        valueTomSelect.enable();
                        valueTomSelect.open();
                    }
                }
            });
            valueTomSelect = new TomSelect(valSelectEl, {
                plugins: ['dropdown_input', 'clear_button'],
                create: async function(input, callback) {
                    const isConfirmed = await confirmCreate(input, 'Value');
                    if (isConfirmed) callback({
                        value: `new::${input}`,
                        text: input
                    });
                    else callback();
                },
                render: {
                    option_create: (data, escape) =>
                        `<div class="create">Tambah value baru: <strong>${escape(data.input)}</strong>&hellip;</div>`
                }
            });
            valueTomSelect.disable();
            return {
                attr: attrTomSelect,
                val: valueTomSelect
            };
        }
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

        function findTypeIdByName(name) {
            if (!name) return null;
            const normalizedName = name.trim().toLowerCase();
            const type = allTypes.find(t => t.name.toLowerCase() === normalizedName);
            return type ? type.id : null;
        }
        async function submitCreateSetForm(submitBtn, form) {
            const attachLabCheckbox = document.getElementById('attach_to_lab_checkbox');
            const attachDeskCheckbox = document.getElementById('attach_to_desk_checkbox');
            
            if (attachLabCheckbox.checked && !tomSelectSetLab.getValue()) {
                Swal.fire('Error', 'Pilih laboratorium terlebih dahulu.', 'error');
                return;
            }
            
            if (attachDeskCheckbox.checked && setSelectedDeskLocations.length !== 1) {
                Swal.fire('Error', 'Anda harus memilih 1 meja untuk memasang set.', 'error');
                return;
            }
            showLoading('Membuat Set Item...', 'Ini mungkin memakan waktu beberapa saat...');
            submitBtn.disabled = true;
            const formData = {
                set_name: document.getElementById('set_name').value,
                set_note: document.getElementById('set_note').value,
                _token: form.querySelector('input[name="_token"]').value,
                items: []
            };
            if (attachLabCheckbox.checked) {
                formData.lab_id = tomSelectSetLab.getValue();
                if (attachDeskCheckbox.checked) {
                    formData.attach_to_desk = true;
                    formData.desk_location = setSelectedDeskLocations[0];
                } else {
                    formData.attach_to_lab = true;
                }
            }
            setItemTomInstances.forEach(itemInstance => {
                const itemRow = itemInstance.row;
                const itemData = {
                    is_component: '0',
                    name: itemRow.querySelector('.set-item-name').value,
                    serial_code: itemRow.querySelector('.set-item-serial').value,
                    condition: itemRow.querySelector('.set-item-condition:checked').value,
                    produced_at: itemRow.querySelector('.set-item-produced-at').value,
                    type: itemInstance.typeSelect.getValue(),
                    specifications: [],
                    new_components: []
                };
                itemInstance.mainSpecs.forEach(spec => {
                    const attrVal = spec.attr.getValue();
                    const valVal = spec.val.getValue();
                    if (attrVal && valVal) itemData.specifications.push({
                        attribute: attrVal,
                        value: valVal
                    });
                });
                itemInstance.newComponents.forEach(compInstance => {
                    const compRow = compInstance.row;
                    const componentData = {
                        name: compRow.querySelector('.new-component-name').value,
                        serial_code: compRow.querySelector('.new-component-serial').value,
                        condition: compRow.querySelector('.new-component-condition:checked').value,
                        type: compInstance.typeSelect.getValue(),
                        produced_at: compRow.querySelector('.new-component-produced-at').value,
                        specifications: []
                    };
                    compInstance.specInstances.forEach(spec => {
                        const attrVal = spec.attr.getValue();
                        const valVal = spec.val.getValue();
                        if (attrVal && valVal) componentData.specifications.push({
                            attribute: attrVal,
                            value: valVal
                        });
                    });
                    itemData.new_components.push(componentData);
                });
                formData.items.push(itemData);
            });
            formData.set_count = formData.items.length;
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
                    if (response.status === 422) throw new Error(data.message || 'Data tidak valid.');
                    throw new Error(data.message || 'Terjadi kesalahan.');
                }
                hideLoading();
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message
                }).then(() => {
                    location.reload();
                });
            } catch (error) {
                hideLoading();
                Swal.fire('Gagal Membuat Set', error.message, 'error');
            } finally {
                submitBtn.disabled = false;
            }
        }
        async function loadLabsForSetAttachment() {
            try {
                const response = await fetch("{{ route('admin.labs.list') }}");
                if (!response.ok) throw new Error('Gagal memuat daftar lab.');
                const labs = await response.json();
                if (tomSelectSetLab) {
                    tomSelectSetLab.clearOptions();
                    tomSelectSetLab.addOptions(labs.map(lab => ({
                        value: lab.id,
                        text: lab.name
                    })));
                }
            } catch (error) {
                showToast('Error', error.message, 'error');
            }
        }
        async function fetchDeskMapForSet(labId) {
            const container = document.getElementById('set-desk-grid-container');
            container.innerHTML =
                '<div class="flex items-center justify-center py-8"><div class="animate-spin rounded-full h-12 w-8 border-b-2 border-indigo-600"></div></div>';
            try {
                const response = await fetch(`/admin/labs/${labId}/desks`);
                if (!response.ok) throw new Error('Gagal memuat denah meja.');
                setLabDesks = await response.json();
                let maxRow = 5,
                    maxCol = 10;
                if (setLabDesks.length > 0) {
                    setLabDesks.forEach(d => {
                        const row = d.location.charCodeAt(0) - 64;
                        const col = parseInt(d.location.substring(1));
                        if (row > maxRow) maxRow = row;
                        if (col > maxCol) maxCol = col;
                    });
                }
                renderDeskGridForSet(setLabDesks, maxRow, maxCol);
            } catch (error) {
                container.innerHTML = `<div class="text-center py-8 text-red-500">${error.message}</div>`;
            }
        }

        function renderDeskGridForSet(desks, maxRows, maxCols) {
            const container = document.getElementById('set-desk-grid-container');
            let html =
                `<div class="overflow-x-auto pb-4"><div class="grid gap-3 border-2 min-w-fit border-slate-300 p-6" style="grid-template-columns: repeat(${maxCols}, minmax(100px, 1fr)); grid-template-rows: repeat(${maxRows}, auto);">`;

            const occupiedSlots = new Set(desks.map(d => d.location));
            const requiredTypes = ['Monitor', 'Mouse', 'Keyboard', 'CPU'];

            desks.forEach(desk => {
                const row = desk.location.charCodeAt(0) - 64;
                const col = parseInt(desk.location.substring(1));

                const isSelected = setSelectedDeskLocations.includes(desk.location);
                const hasRequiredType = desk.items && desk.items.some(item => item.type && requiredTypes.includes(
                    item.type.name));

                let bgColorClass;

                if (isSelected) {
                    bgColorClass = 'bg-indigo-200 border-indigo-500 ring-2 ring-indigo-500';
                } else if (hasRequiredType) {
                    bgColorClass = 'bg-red-50 border-red-300';
                } else {
                    bgColorClass = 'bg-gray-50 border-gray-300 hover:bg-gray-100';
                }

                // PERBAIKAN: Selalu pointer
                let cursorClass = 'cursor-pointer';

                html +=
                    `<div data-desk-location="${desk.location}" data-has-required="${hasRequiredType}" style="grid-area: ${row} / ${col};" class="set-desk-item transition-all duration-200 flex flex-col items-center justify-center p-3 border-2 rounded-lg min-h-24 ${bgColorClass} ${cursorClass}">
                <span class="font-bold text-lg block select-none">${desk.location}</span>
                ${(hasRequiredType && !isSelected) ? '<span class="text-xs text-red-800 mt-1 inline-block select-none font-semibold">Sudah Terisi Lengkap</span>' : ''}
                ${isSelected ? '<span class="text-xs text-indigo-700 mt-1 inline-block select-none font-bold uppercase">Terpilih</span>' : ''}
            </div>`;
            });

            for (let r = 1; r <= maxRows; r++) {
                for (let c = 1; c <= maxCols; c++) {
                    if (!occupiedSlots.has(`${String.fromCharCode(64 + r)}${c}`)) html +=
                        `<div style="grid-area: ${r} / ${c}; visibility: hidden;"></div>`;
                }
            }
            html += '</div></div>';
            container.innerHTML = html;

            document.querySelectorAll('.set-desk-item').forEach(deskEl => {
                deskEl.addEventListener('click', async () => {
                    const hasRequired = deskEl.dataset.hasRequired === 'true';

                    if (hasRequired) {
                        const result = await Swal.fire({
                            title: 'Meja Sudah Terisi Lengkap',
                            text: 'Meja ini sudah memiliki semua set PC Lengkap. Yakin ingin melanjutkan?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Ya, Lanjutkan',
                            cancelButtonText: 'Batal',
                            confirmButtonColor: '#f59e0b'
                        });
                        if (!result.isConfirmed) return;
                    }

                    // Set data terpilih setelah konfirmasi
                    setSelectedDeskLocations = [deskEl.dataset.deskLocation];
                    updateSetSelectedDesksDisplay();

                    // Re-render agar visual berubah
                    renderDeskGridForSet(setLabDesks, maxRows, maxCols);
                });
            });
        }

        function updateSetSelectedDesksDisplay() {
            const display = document.getElementById('set-selected-desks-display');
            if (setSelectedDeskLocations.length === 0) display.innerHTML = 'Belum ada meja dipilih.';
            else display.innerHTML = `Meja dipilih: ${setSelectedDeskLocations[0]}`;
        }

        async function openAttachLabModal() {
            if (currentAllAttached) {
                Swal.fire('Info', 'Set ini sudah terpasang.', 'info');
                return;
            }
            const result = await Swal.fire({
                title: 'Pilih Laboratorium',
                html: '<select id="swal-lab-select" class="swal2-input" style="width:80%;padding:0.5rem"><option value="">-- Pilih Lab --</option></select>',
                showCancelButton: true,
                confirmButtonText: 'Pasang ke Lab',
                cancelButtonText: 'Batal',
                didOpen: async () => {
                    const select = document.getElementById('swal-lab-select');
                    try {
                        const response = await fetch("{{ route('admin.labs.list') }}");
                        const labs = await response.json();
                        labs.forEach(lab => {
                            const option = document.createElement('option');
                            option.value = lab.id;
                            option.textContent = lab.name;
                            select.appendChild(option);
                        });
                    } catch (error) {
                        console.error('Error loading labs:', error);
                    }
                },
                preConfirm: () => {
                    const labId = document.getElementById('swal-lab-select').value;
                    if (!labId) {
                        Swal.showValidationMessage('Silakan pilih laboratorium');
                        return false;
                    }
                    return labId;
                }
            });
            if (result.isConfirmed && result.value) {
                await attachSetToLab(result.value);
            }
        }

        async function attachSetToLab(labId) {
            showLoading('Memasang Set ke Lab...');
            try {
                const response = await fetch(`/admin/sets/${currentSetId}/attach-lab`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        lab_id: labId
                    })
                });
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Gagal memasang set ke lab.');
                hideLoading();
                showToast('Berhasil!', data.message, 'success');
                document.getElementById('action-modal').classList.add('hidden');
                checkModalState();
                setTimeout(() => location.reload(), 1000);
            } catch (error) {
                hideLoading();
                Swal.fire('Gagal', error.message, 'error');
            }
        }

        async function detachSetFromDesk() {
            const result = await Swal.fire({
                title: 'Lepas Set dari Meja?',
                text: `Semua item dalam set '${currentSetName}' akan dilepas dari meja.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Lepas!',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#ef4444'
            });
            if (!result.isConfirmed) return;
            
            showLoading('Melepas Set dari Meja...');
            try {
                const response = await fetch(`/admin/sets/${currentSetId}/detach-desks`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Gagal melepas set');
                hideLoading();
                showToast('Berhasil!', data.message, 'success');
                document.getElementById('action-modal').classList.add('hidden');
                checkModalState();
                setTimeout(() => location.reload(), 1000);
            } catch (error) {
                hideLoading();
                Swal.fire('Gagal', error.message, 'error');
            }
        }

        async function detachSetFromLab() {
            const result = await Swal.fire({
                title: 'Lepas Set dari Lab?',
                text: `Semua item dalam set '${currentSetName}' akan dilepas dari lemari lab.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Lepas!',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#6366f1'
            });
            if (!result.isConfirmed) return;
            
            showLoading('Melepas Set dari Lab...');
            try {
                const response = await fetch(`/admin/sets/${currentSetId}/detach-labs`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Gagal melepas set');
                hideLoading();
                showToast('Berhasil!', data.message, 'success');
                document.getElementById('action-modal').classList.add('hidden');
                checkModalState();
                setTimeout(() => location.reload(), 1000);
            } catch (error) {
                hideLoading();
                Swal.fire('Gagal', error.message, 'error');
            }
        }

        // --- MAIN INIT ---
        document.addEventListener('DOMContentLoaded', function() {
            initializeCreateSetModal();
            initializeActionModal();
            initializeDetailModal();
            initializeAttachDeskModal();

            // Init Filter
            const locationFilter = new TomSelect('#filter_location', {
                plugins: ['clear_button'],
                placeholder: 'Semua Lokasi...',
                allowEmptyOption: true
            });
            locationFilter.clear();

            // --- INIT DATATABLE ---
            const tableElement = document.getElementById('sets-datatable');

            try {
                // Konfigurasi ini sekarang PASTI jalan karena atribut data-te sudah dihapus dari HTML
                setsDatatableInstance = new te.Datatable(tableElement, {
                    bordered: true,
                    hover: true,
                    striped: true,
                    loading: false,
                    search: true, // Search bar akan muncul
                    pagination: true, // Pagination akan muncul
                    entries: 10,
                    entriesOptions: [5, 10, 25, 50, 100],
                    fixedHeader: false, // Kita handle via CSS (lebih stabil)
                    noFoundMessage: 'Tidak ada data set yang ditemukan.',
                });
                console.log("Datatable initialized successfully");
            } catch (e) {
                console.error('Gagal inisialisasi datatable:', e);
            }

            // Logic Filter
            locationFilter.on('change', (value) => {
                if (!setsDatatableInstance) return;
                if (!value) {
                    setsDatatableInstance.search('');
                    return;
                }
                let keyword = value === 'unattached' ? 'Belum Terpasang' : (locationFilter.getOption(value)
                    ?.textContent.trim() || '');
                setsDatatableInstance.search(keyword);
            });

            document.getElementById('reset-filter-btn').addEventListener('click', () => {
                document.getElementById('datatable-search-input').value = '';
                if (setsDatatableInstance) setsDatatableInstance.search('');
                locationFilter.clear();
            });

            document.getElementById('datatable-search-input').addEventListener('input', (e) => {
                setsDatatableInstance.search(e.target.value);
            });

            // Logic Actions (Event Delegation)
            // Menggunakan container wrapper untuk event listener agar tetap jalan saat pagination berubah
            document.getElementById('datatable-container').addEventListener('click', function(e) {
                const actionButton = e.target.closest('.btn-open-action-set');
                if (!actionButton) return;
                currentSetId = actionButton.dataset.setId;
                currentSetName = actionButton.dataset.setName;
                currentAllAttached = actionButton.dataset.allAttached === '1';
                document.getElementById('action-modal-title').innerHTML =
                    `Aksi untuk Set: <span class="font-bold text-indigo-600">${currentSetName}</span>`;
                const attachBtn = document.getElementById('action-btn-attach');
                if (currentAllAttached) {
                    attachBtn.classList.add('opacity-50', 'cursor-not-allowed');
                    attachBtn.disabled = true;
                } else {
                    attachBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    attachBtn.disabled = false;
                }
                document.getElementById('action-modal').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            });
        });
    </script>
@endsection
