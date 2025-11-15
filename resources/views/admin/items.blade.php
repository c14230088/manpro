@extends('layouts.admin')

@section('title', 'Manajemen Item')

@section('body')
    <div class="flex flex-col md:flex-row w-full py-4 shadow-md items-center justify-center mb-5 px-6 md:px-4">
        <h1 class="text-center text-4xl uppercase font-bold mb-2 md:mb-0">Items & Components</h1>
    </div>

    {{-- ========================================================= --}}
    {{-- Filter Form (Tidak ada perubahan) --}}
    {{-- ========================================================= --}}
    <div class="max-w-7xl mx-auto px-6 pb-6">
        <form action="{{ route('admin.items') }}" method="GET" id="filter-form">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Filter Kontrol</h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 filter-select">
                    {{-- Lab --}}
                    <div>
                        <label for="filter_lab_select"
                            class="block text-sm font-semibold text-gray-700 mb-2">Laboratorium</label>
                        <select id="filter_lab_select" name="lab_id" placeholder="Semua Lab...">
                            <option value="">Semua Laboratorium</option>
                            @foreach ($labs as $lab)
                                <option value="{{ $lab->id }}" @selected(data_get($filters, 'lab_id') == $lab->id)>{{ $lab->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Tipe --}}
                    <div>
                        <label for="filter_type_select" class="block text-sm font-semibold text-gray-700 mb-2">Tipe</label>
                        <select id="filter_type_select" name="type_id" placeholder="Semua Tipe...">
                            <option value="">Semua Tipe</option>
                            @foreach ($types as $type)
                                <option value="{{ $type->id }}" @selected(data_get($filters, 'type_id') == $type->id)>{{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Status --}}
                    <div>
                        <label for="filter_status" class="block text-sm font-semibold text-gray-700 mb-2">Status
                            Pemasangan</label>
                        <select id="filter_status" name="status" placeholder="Semua Status...">
                            <option value="">Semua Status</option>
                            <option value="unaffiliated" @selected(data_get($filters, 'status') == 'unaffiliated')>Belum Terpasang</option>
                            <option value="affiliated" @selected(data_get($filters, 'status') == 'affiliated')>Sudah Terpasang</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Hanya berlaku untuk 'Item'.</p>
                    </div>

                    {{-- Kondisi --}}
                    <div>
                        <label for="filter_condition" class="block text-sm font-semibold text-gray-700 mb-2">Kondisi</label>
                        <select id="filter_condition" name="condition" placeholder="Semua Kondisi...">
                            <option value="">Semua Kondisi</option>
                            <option value="1" @selected(data_get($filters, 'condition') == '1')>Bagus</option>
                            <option value="0" @selected(data_get($filters, 'condition') == '0')>Rusak</option>
                        </select>
                    </div>

                    {{-- Spesifikasi --}}
                    <div>
                        <label for="filter_spec_attr" class="block text-sm font-semibold text-gray-700 mb-2">Atribut
                            Spesifikasi</label>
                        <select id="filter_spec_attr" name="spec_attribute_id" placeholder="Pilih Atribut...">
                            <option value="">Semua Atribut</option>
                            @foreach ($specification as $spec)
                                <option value="{{ $spec->id }}" data-values='@json($spec->specValues)'
                                    @selected(data_get($filters, 'spec_attribute_id') == $spec->id)>
                                    {{ $spec->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="filter_spec_val" class="block text-sm font-semibold text-gray-700 mb-2">Value
                            Spesifikasi</label>
                        <select id="filter_spec_val" name="spec_value_id" placeholder="Pilih Atribut Dulu..." disabled>
                            {{-- JS akan mengisi ini --}}
                        </select>
                    </div>
                </div>
                {{-- Tombol Aksi Filter --}}
                <div class="flex flex-col sm:flex-row justify-end gap-3">
                    <div class="relative flex w-full flex-wrap mt-6 sm:w-1/2 items-stretch">
                        <input id="datatable-search-input" type="search"
                            class="relative m-0 -mr-0.5 block w-[1px] min-w-0 flex-auto rounded-l border border-solid border-neutral-300 bg-transparent bg-clip-padding px-3 py-[0.25rem] text-base font-normal leading-[1.6] text-neutral-700 outline-none transition duration-200 ease-in-out focus:z-[3] focus:border-primary focus:text-neutral-700 focus:shadow-[inset_0_0_0_1px_rgb(59,113,202)] focus:outline-none dark:border-neutral-600 dark:text-neutral-200 dark:placeholder:text-neutral-200 dark:focus:border-primary"
                            placeholder="Search" aria-label="Search" aria-describedby="button-addon1" />
                        <button
                            class="relative z-[2] flex items-center rounded-r bg-primary px-6 py-2.5 text-xs font-medium uppercase leading-tight text-white shadow-md transition duration-150 ease-in-out hover:bg-primary-700 hover:shadow-lg focus:bg-primary-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-primary-800 active:shadow-lg"
                            type="button" id="advanced-search-button" data-te-ripple-init data-te-ripple-color="light">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5">
                                <path fill-rule="evenodd"
                                    d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                    <div class="flex justify-center sm:justify-end items-center gap-3 mt-6 w-full sm:w-1/2">
                        <a id="reset-filter-btn" href="{{ route('admin.items') }}"
                            class="px-6 py-2 bg-gray-200 w-1/2 sm:w-full flex justify-center items-center text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-300 transition-colors">
                            <span class="m-auto text-center">
                                Reset Filter
                            </span>
                        </a>
                        <button id="apply-filter-btn" type="submit"
                            class="px-6 py-2 bg-indigo-600 w-1/2 sm:w-full text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition-colors">
                            Terapkan Filter
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- ========================================================= --}}
    {{-- Container Tabel Utama (Tidak ada perubahan) --}}
    {{-- ========================================================= --}}
    <div class="max-w-7xl mx-auto px-6 pb-12 space-y-8">

        {{-- Tombol Tambah Item --}}
        <div class="flex justify-end">
            <button id="open-create-modal-btn" type="button"
                class="px-6 py-3 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors shadow-lg">
                + Tambah Item / Component
            </button>
        </div>

        {{-- Accordion untuk Tabel --}}
        <div id="accordion-items-components">

            {{-- Accordion Item 1: ITEMS --}}
            <div class="accordion-item bg-white rounded-xl shadow-sm border border-gray-200">
                <h2 class="accordion-header mb-0" id="items-heading">
                    <button
                        class="relative flex items-center w-full py-5 px-6 text-left text-xl font-semibold text-gray-800 bg-white border-0 rounded-xl transition focus:outline-none"
                        type="button" data-te-collapse-init data-te-target="#items-collapse" aria-expanded="true"
                        aria-controls="items-collapse">
                        <svg class="w-6 h-6 mr-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z">
                            </path>
                        </svg>
                        Daftar Items ({{ $items->count() }})
                        <span
                            class_pers="ml-auto h-5 w-5 shrink-0 rotate-[-180deg] transition-transform duration-200 ease-in-out motion-reduce:transition-none">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </span>
                    </button>
                </h2>
                <div id="items-collapse" class="!visible" data-te-collapse-item data-te-collapse-show
                    aria-labelledby="items-heading">
                    <div class="accordion-body p-6 border-t border-gray-200">
                        <div id="items-datatable" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" data-te-datatable-sortable="true"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nama Item</th>
                                        <th scope="col" data-te-datatable-sortable="true"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Lokasi</th>
                                        <th scope="col" data-te-datatable-sortable="true"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tipe</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Spesifikasi</th>
                                        <th scope="col" data-te-datatable-sortable="true"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Kondisi</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($items as $item)
                                        <tr class="item-row">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-semibold text-gray-900">{{ $item->name }}</div>
                                                <div class="text-xs text-gray-600 font-mono">{{ $item->serial_code }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if ($item->desk)
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $item->desk->lab->name ?? 'N/A' }}</div>
                                                    <div class="text-sm text-gray-600">Meja {{ $item->desk->location }}
                                                    </div>
                                                @else
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-700">
                                                        Belum Terpasang
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    {{ $item->type->name ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">
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
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <button type="button"
                                                    class="btn-open-action-item px-4 py-2 bg-indigo-600 text-gray-100 hover:text-gray-200 text-xs font-semibold rounded-md hover:bg-indigo-800 focus:outline-none"
                                                    data-item-id="{{ $item->id }}"
                                                    data-item-name="{{ $item->name }}"
                                                    data-item-condition="{{ $item->condition }}">
                                                    Aksi
                                                </button>
                                            </td>
                                        </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                                    Tidak ada item yang cocok dengan filter Anda.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Accordion Item 2: COMPONENTS --}}
                <div class="accordion-item bg-white rounded-xl shadow-sm border border-gray-200 mt-6">
                    <h2 class="accordion-header mb-0" id="components-heading">
                        <button
                            class="relative flex items-center w-full py-5 px-6 text-left text-xl font-semibold text-gray-800 bg-white border-0 rounded-xl transition focus:outline-none"
                            type="button" data-te-collapse-init data-te-collapse-collapsed
                            data-te-target="#components-collapse" aria-expanded="false" aria-controls="components-collapse">
                            <svg class="w-6 h-6 mr-3 text-purple-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                </path>
                            </svg>
                            Daftar Components ({{ $components->count() }})
                            <span
                                class="ml-auto h-5 w-5 shrink-0 rotate-[-180deg] transition-transform duration-200 ease-in-out motion-reduce:transition-none">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </span>
                        </button>
                    </h2>
                    <div id="components-collapse" class="!visible" data-te-collapse-item
                        aria-labelledby="components-heading">
                        <div class="accordion-body p-6 border-t border-gray-200">
                            <div id="components-datatable" class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" data-te-datatable-sortable="true"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Nama Komponen</th>
                                            <th scope="col" data-te-datatable-sortable="true"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Terpasang di Item</th>
                                            <th scope="col" data-te-datatable-sortable="true"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Tipe</th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Spesifikasi</th>
                                            <th scope="col" data-te-datatable-sortable="true"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Kondisi</th>
                                            <th scope="col"
                                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse ($components as $comp)
                                            <tr class="component-row">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-semibold text-gray-900">{{ $comp->name }}</div>
                                                    <div class="text-xs text-gray-600 font-mono">{{ $comp->serial_code }}
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @if ($comp->item)
                                                        <div class="text-sm font-medium text-gray-900">{{ $comp->item->name }}
                                                        </div>
                                                        @if ($comp->item->desk)
                                                            <div class="text-sm text-gray-600">
                                                                ({{ $comp->item->desk->lab->name }} - Meja
                                                                {{ $comp->item->desk->location }})
                                                            </div>
                                                        @else
                                                            <div class="text-sm text-gray-600">(Item Induk belum terpasang)
                                                            </div>
                                                        @endif
                                                    @else
                                                        <span
                                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-700">
                                                            Belum Terpasang
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                                        {{ $comp->type->name ?? 'N/A' }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">
                                                    @foreach ($comp->specSetValues as $spec)
                                                        <span class="font-semibold">{{ $spec->specAttributes->name }}:</span>
                                                        {{ $spec->value }}@if (!$loop->last)
                                                            ,
                                                        @endif
                                                    @endforeach
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @if ($comp->condition)
                                                        <span
                                                            class="condition-badge px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Bagus</span>
                                                    @else
                                                        <span
                                                            class="condition-badge px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Rusak</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                                    <button type="button"
                                                        class="btn-open-action-component px-4 py-2 bg-indigo-600 text-gray-100 hover:text-gray-200 text-xs font-semibold rounded-md hover:bg-indigo-800 focus:outline-none"
                                                        data-item-id="{{ $comp->id }}"
                                                        data-item-name="{{ $comp->name }}"
                                                        data-item-condition="{{ $comp->condition }}">
                                                        Aksi
                                                    </button>
                                                </td>
                                            </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                                        Tidak ada komponen yang cocok dengan filter Anda.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- ========================================================= --}}
            {{-- MODAL 1: Buat Item (Dengan Perubahan) --}}
            {{-- ========================================================= --}}
            <div id="create-item-modal" class="hidden" role="dialog" aria-modal="true"
                aria-labelledby="create-item-modal-title">
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
                                    Input Item / Component Baru
                                </h3>
                                <button id="create-item-modal-close-btn" type="button"
                                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                                    <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 14 14">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                    </svg>
                                </button>
                            </div>

                            {{-- Modal Body (Scrollable) --}}
                            <div class="p-6 space-y-6 overflow-y-auto">
                                {{-- Card untuk Info Dasar --}}
                                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">
                                    <h2 class="text-xl font-semibold text-gray-800 mb-6">Detail Barang</h2>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Jenis Barang</label>
                                            <div class="flex items-center space-x-6">
                                                <label class="flex items-center">
                                                    {{-- ⬇️ Tambahkan ID untuk radio button ⬇️ --}}
                                                    <input type="radio" id="radio-is-item" name="is_component" value="0"
                                                        class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500"
                                                        checked>
                                                    <span class="ml-2 text-gray-700">Item (Induk)</span>
                                                </label>
                                                <label class="flex items-center">
                                                    <input type="radio" id="radio-is-component" name="is_component"
                                                        value="1"
                                                        class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                                    <span class="ml-2 text-gray-700">Component (Satuan)</span>
                                                </label>
                                            </div>
                                        </div>
                                        <div>
                                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Nama
                                                Barang</label>
                                            <input type="text" id="name" name="name" required
                                                class="block w-full px-4 py-3 text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                                placeholder="cth: PC Rakitan Spek A">
                                        </div>
                                        <div>
                                            <label for="serial_code" class="block text-sm font-semibold text-gray-700 mb-2">Serial
                                                Code</label>
                                            <input type="text" id="serial_code" name="serial_code" required
                                                class="block w-full px-4 py-3 text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                                placeholder="cth: PC-LSC-A-01">
                                        </div>
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
                                        <div class="md:col-span-2">
                                            <label for="type-selector"
                                                class="block text-sm font-semibold text-gray-700 mb-2">Type</label>
                                            <select id="type-selector" name="type"
                                                placeholder="Cari atau tambah Type baru..."></select>
                                        </div>
                                    </div>
                                </div>

                                {{-- Card untuk Spesifikasi (Item Utama) --}}
                                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">
                                    <div class="flex items-center justify-between mb-6">
                                        <h2 class="text-xl font-semibold text-gray-800">Spesifikasi <span
                                                id="spec-owner-label">(Item Utama)</span></h2>
                                        <button type="button" id="add-spec-btn"
                                            class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition-colors">
                                            + Tambah Spesifikasi
                                        </button>
                                    </div>
                                    <div id="specifications-container" class="space-y-4">
                                        {{-- Baris spesifikasi item utama akan ditambahkan di sini oleh JS --}}
                                    </div>
                                </div>

                                {{-- ⬇️ CARD BARU UNTUK MEMBUAT KOMPONEN (HANYA UNTUK ITEM) ⬇️ --}}
                                <div id="components-section"
                                    class="bg-gray-50 rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">
                                    {{-- Dihapus: style="display: none;" --}}
                                    <div class="flex items-center justify-between mb-6">
                                        <h2 class="text-xl font-semibold text-gray-800">Komponen Baru (Bawaan Item)</h2>
                                        <button type="button" id="add-new-component-btn"
                                            class="px-4 py-2 bg-purple-600 text-white text-sm font-semibold rounded-lg hover:bg-purple-700 transition-colors">
                                            + Tambah Komponen Baru
                                        </button>
                                    </div>
                                    <div id="new-components-container" class="space-y-6">
                                        {{-- Template Komponen Baru akan di-clone di sini --}}
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
            {{-- MODAL 2 & 3 (Aksi & Pasang Meja) (Tidak ada perubahan) --}}
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
                        <div class="p-6 grid grid-cols-2 gap-4">
                            {{-- ⬇️ PERBAIKAN LIHAT DETAIL ⬇️ --}}
                            <button id="action-btn-details"
                                class="p-6 bg-green-500 hover:bg-green-600 text-white rounded-lg flex flex-col items-center justify-center transition-all shadow-lg">
                                <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-.36 1.22-1.03 2.36-1.91 3.36M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 16.057 7.523 19 12 19c4.478 0 8.268-2.943 9.542-7 .36-1.22 1.03-2.36 1.91-3.36">
                                    </path>
                                </svg>
                                <span class="font-semibold">Lihat Detail</span>
                            </button>

                            <button id="action-btn-condition"
                                class="p-6 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg flex flex-col items-center justify-center transition-all shadow-lg">
                                <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="font-semibold">Ubah Kondisi</span>
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
                                <div class="text-center py-12 text-gray-500">Pilih lab untuk melihat denah.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="detail-modal" class="hidden" role="dialog" aria-modal="true" aria-labelledby="detail-modal-title">
                <div class="fixed inset-0 bg-gray-900 bg-opacity-75 z-[4000]"></div>
                <div id="detail-modal-overlay" class="fixed inset-0 flex items-center justify-center p-4 z-[4001]">
                    <div id="detail-modal-area"
                        class="relative bg-white rounded-lg shadow-xl w-full max-w-2xl flex flex-col max-h-[90vh]">
                        {{-- Header --}}
                        <div class="flex justify-between items-center p-4 border-b border-gray-200 rounded-t">
                            <h3 id="detail-modal-title" class="text-xl font-semibold text-gray-900">
                                Detail: <span id="detail-item-name" class="text-indigo-600">Nama Item</span>
                            </h3>
                            <button id="detail-modal-close-btn" type="button"
                                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                                <svg class="w-4 h-4" aria-hidden="true"
                                    xmlns="[http://www.w3.org/2000/svg](http://www.w3.org/2000/svg)" fill="none"
                                    viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>
                        {{-- Body (Konten Detail) --}}
                        <div id="detail-modal-content" class="p-6 space-y-4 overflow-y-auto">
                            <div class="flex items-center justify-center py-12">
                                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TEMPLATE 1: untuk baris spesifikasi (Tidak berubah, tapi akan dipakai di 2 tempat) --}}
            <template id="spec-row-template">
                <div class="grid grid-cols-12 gap-4 spec-row p-4 border border-gray-200 rounded-lg bg-gray-50">
                    <div class="col-span-12 md:col-span-5">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Attribute</label>
                        {{-- ⬇️ Hapus 'name' agar bisa di-set oleh JS ⬇️ --}}
                        <select class="spec-attribute" placeholder="Cari attribute..."></select>
                    </div>
                    <div class="col-span-12 md:col-span-5">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Value</label>
                        {{-- ⬇️ Hapus 'name' agar bisa di-set oleh JS ⬇️ --}}
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

            {{-- ⬇️ TEMPLATE 2: BARU UNTUK FORM KOMPONEN BARU ⬇️ --}}
            <template id="new-component-form-template">
                <div class="new-component-row border-2 border-purple-200 bg-purple-50 rounded-lg p-6 relative">
                    {{-- Tombol Hapus Komponen --}}
                    <button type="button"
                        class="remove-new-component-btn absolute -top-3 -right-3 w-8 h-8 bg-rose-500 hover:bg-rose-600 text-white rounded-full flex items-center justify-center shadow-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Nama Komponen --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Komponen</label>
                            {{-- ⬇️ Class unik untuk JS ⬇️ --}}
                            <input type="text"
                                class="new-component-name block w-full px-4 py-3 text-base border border-gray-300 rounded-lg"
                                placeholder="cth: RAM 16GB">
                        </div>
                        {{-- Serial Code Komponen --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Serial Code</label>
                            {{-- ⬇️ Class unik untuk JS ⬇️ --}}
                            <input type="text"
                                class="new-component-serial block w-full px-4 py-3 text-base border border-gray-300 rounded-lg"
                                placeholder="cth: SN-RAM-001">
                        </div>
                        {{-- Tipe Komponen --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Type Komponen</label>
                            {{-- ⬇️ Class unik untuk JS ⬇️ --}}
                            <select class="new-component-type-select" placeholder="Pilih Tipe Komponen..."></select>
                        </div>
                        {{-- Kondisi Komponen --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Kondisi Komponen</label>
                            <div class="flex items-center space-x-6">
                                <label class="flex items-center">
                                    {{-- ⬇️ Class unik untuk JS ⬇️ --}}
                                    <input type="radio" class="new-component-condition" value="1"
                                        class="h-4 w-4 text-purple-600" checked>
                                    <span class="ml-2 text-gray-700">Bagus</span>
                                </label>
                                <label class="flex items-center">
                                    {{-- ⬇️ Class unik untuk JS ⬇️ --}}
                                    <input type="radio" class="new-component-condition" value="0"
                                        class="h-4 w-4 text-purple-600">
                                    <span class="ml-2 text-gray-700">Rusak</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Spesifikasi untuk Komponen ini --}}
                    <div class="mt-6 pt-6 border-t border-purple-200">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">Spesifikasi Komponen</h3>
                            <button type="button"
                                class="btn-add-new-comp-spec px-3 py-1 bg-indigo-600 text-white text-xs font-semibold rounded-lg hover:bg-indigo-700">
                                + Tambah Spek
                            </button>
                        </div>
                        <div class="new-component-specs-container space-y-4">
                            {{-- Template spek akan di-clone di sini --}}
                        </div>
                    </div>
                </div>
            </template>
        @endsection

    @section('script')
        {{-- Style untuk filter TomSelect --}}
        <style>
            .filter-select .ts-control {
                @apply block w-full px-4 py-3 text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white;
                padding-top: 0.6rem;
                padding-bottom: 0.6rem;
            }

            .filter-select .ts-input {
                @apply text-sm;
            }

            .filter-select .ts-wrapper.single .ts-control .item {
                @apply bg-gray-200 text-gray-800 rounded-md px-2 py-1;
            }
        </style>

        <script>
            // Set sidebar link aktif
            try {
                document.getElementById('items').classList.add('bg-slate-100');
            } catch (e) {
                showToast('Peringatan', 'Gagal menandai link sidebar aktif.', 'warning');
            }

            // Data dari Blade (untuk modal Buat Item)
            const allTypes = @json($types);
            const allSpecAttributes = @json($specification);
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // =================================================================
            // Variabel Global
            // =================================================================
            let currentItemId = null;
            let currentItemName = null;
            let currentItemCondition = null;
            let currentItemType = 'item'; // 'item' or 'component'

            // ⬇️ Ubah ini menjadi object untuk TomSelects di dalam repeater ⬇️
            let tomSelectInstances = {
                mainSpecs: [],
                newComponents: [] // Akan berisi { id: compIndex, row: el, typeSelect: tom, specInstances: [] }
            };

            let newComponentIndex = 0; // Index unik untuk setiap komponen baru

            let tomSelectType; // Untuk item utama
            let tomSelectLabModal;
            let modalLabDesks = [];
            let filterTomSelects = {};

            // =================================================================
            // Fungsi Utility (Loading & Toast)
            // =================================================================
            function showLoading(title = 'Loading...', text = 'silakan tunggu...') {
                Swal.fire({
                    title: title,
                    text: text,
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            }

            function hideLoading() {
                Swal.close();
            }

            // Buat fungsi showToast jika belum ada
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            function showToast(title, message, icon = 'success') {
                Toast.fire({
                    icon: icon,
                    title: title,
                    text: message
                });
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
            // MODAL 1: Logika "Buat Item Baru" (DIROMBAK)
            // =================================================================
            function initializeCreateItemModal() {
                const modal = document.getElementById('create-item-modal');
                const form = document.getElementById('create-item-form');
                const submitBtn = document.getElementById('submit-btn');
                const addSpecBtn = document.getElementById('add-spec-btn');
                const closeBtn = document.getElementById('create-item-modal-close-btn');
                const cancelBtn = document.getElementById('create-item-modal-cancel-btn');
                const overlay = document.getElementById('create-item-modal-overlay');
                const specificationsContainer = document.getElementById('specifications-container');

                // Ambil elemen-elemen baru
                const radioIsItem = document.getElementById('radio-is-item');
                const radioIsComponent = document.getElementById('radio-is-component');
                const componentsSection = document.getElementById('components-section');
                const newComponentsContainer = document.getElementById('new-components-container');
                const addNewComponentBtn = document.getElementById('add-new-component-btn');
                const specOwnerLabel = document.getElementById('spec-owner-label');

                if (!modal || !form || !submitBtn || !addSpecBtn || !closeBtn || !cancelBtn || !overlay || !radioIsItem ||
                    !componentsSection || !newComponentsContainer || !addNewComponentBtn || !specOwnerLabel) {
                    Swal.fire('Error Kritis', 'Gagal memuat komponen modal Buat Item. Silakan refresh halaman.', 'error');
                    return;
                }

                // Fungsi untuk Toggling Bagian Komponen
                function toggleComponentSection() {
                    if (radioIsItem.checked) {
                        componentsSection.style.display = 'block';
                        specOwnerLabel.textContent = '(Item Utama)';
                    } else {
                        componentsSection.style.display = 'none';
                        specOwnerLabel.textContent = '(Component)';
                        // Bersihkan komponen jika user berubah pikiran
                        newComponentsContainer.innerHTML = '';
                        // Hancurkan semua tom select di komponen lama
                        tomSelectInstances.newComponents.forEach(comp => {
                            comp.typeSelect.destroy();
                            comp.specInstances.forEach(spec => {
                                spec.attr.destroy();
                                spec.val.destroy();
                            });
                        });
                        tomSelectInstances.newComponents = [];
                        newComponentIndex = 0;
                    }
                }

                radioIsItem.addEventListener('change', toggleComponentSection);
                radioIsComponent.addEventListener('change', toggleComponentSection);

                // Logika Buka/Tutup Modal
                window.openCreateItemModal = () => {
                    modal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                    // Reset form ke kondisi awal
                    form.reset();
                    if (tomSelectType) tomSelectType.clear();
                    specificationsContainer.innerHTML = '';
                    newComponentsContainer.innerHTML = '';

                    // Hancurkan semua tom select lama
                    tomSelectInstances.mainSpecs.forEach(spec => {
                        spec.attr.destroy();
                        spec.val.destroy();
                    });
                    tomSelectInstances.newComponents.forEach(comp => {
                        comp.typeSelect.destroy();
                        comp.specInstances.forEach(spec => {
                            spec.attr.destroy();
                            spec.val.destroy();
                        });
                    });

                    tomSelectInstances = {
                        mainSpecs: [],
                        newComponents: []
                    };
                    newComponentIndex = 0;
                    radioIsItem.checked = true; // Set default ke Item
                    toggleComponentSection(); // Set tampilan awal
                }
                window.closeCreateItemModal = () => {
                    modal.classList.add('hidden');
                    if (document.getElementById('action-modal').classList.contains('hidden') &&
                        document.getElementById('attach-desk-modal').classList.contains('hidden') &&
                        document.getElementById('detail-modal').classList.contains('hidden')) {
                        document.body.style.overflow = '';
                    }
                }

                closeBtn.addEventListener('click', window.closeCreateItemModal);
                cancelBtn.addEventListener('click', window.closeCreateItemModal);
                overlay.addEventListener('click', (e) => {
                    if (e.target === overlay) window.closeCreateItemModal();
                });

                // TomSelect untuk Tipe Item Utama
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

                // Tombol "Tambah Spesifikasi" untuk ITEM UTAMA
                addSpecBtn.addEventListener('click', () => {
                    const newSpecRow = document.getElementById('spec-row-template').content.cloneNode(true)
                        .firstElementChild;
                    specificationsContainer.appendChild(newSpecRow);

                    // Inisialisasi spek baru dan simpan instansinya
                    const specTomSelects = initializeSpecRow(newSpecRow);
                    if (!specTomSelects) return; // Gagal inisialisasi

                    const specInstance = {
                        row: newSpecRow,
                        attr: specTomSelects.attr,
                        val: specTomSelects.val
                    };
                    tomSelectInstances.mainSpecs.push(specInstance);

                    // Tambahkan listener hapus
                    newSpecRow.querySelector('.remove-spec-btn').addEventListener('click', () => {
                        specTomSelects.attr.destroy();
                        specTomSelects.val.destroy();
                        newSpecRow.remove();
                        tomSelectInstances.mainSpecs = tomSelectInstances.mainSpecs.filter(inst => inst.row !==
                            newSpecRow);
                    });
                });

                // Tombol "Tambah Komponen Baru" (Repeater)
                addNewComponentBtn.addEventListener('click', () => {
                    const newCompRow = document.getElementById('new-component-form-template').content.cloneNode(true)
                        .firstElementChild;
                    const compIndex = newComponentIndex++; // Dapatkan index unik

                    newCompRow.dataset.compIndex = compIndex; // Tandai row dengan index-nya

                    const typeSelectEl = newCompRow.querySelector('.new-component-type-select');
                    const addCompSpecBtn = newCompRow.querySelector('.btn-add-new-comp-spec');
                    const newCompSpecContainer = newCompRow.querySelector('.new-component-specs-container');
                    const removeCompBtn = newCompRow.querySelector('.remove-new-component-btn');

                    // 1. Inisialisasi TomSelect Tipe untuk komponen ini
                    const compTypeTomSelect = new TomSelect(typeSelectEl, {
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

                    // 2. Simpan instance TomSelect komponen ini
                    const componentInstanceData = {
                        id: compIndex,
                        row: newCompRow,
                        typeSelect: compTypeTomSelect,
                        specInstances: []
                    };
                    tomSelectInstances.newComponents.push(componentInstanceData);

                    // 3. Tambahkan listener untuk tombol "+ Tambah Spek" di DALAM komponen ini
                    addCompSpecBtn.addEventListener('click', () => {
                        const newSpecRow = document.getElementById('spec-row-template').content.cloneNode(true)
                            .firstElementChild;
                        newCompSpecContainer.appendChild(newSpecRow);

                        const specTomSelects = initializeSpecRow(newSpecRow);
                        if (!specTomSelects) return;

                        // Simpan instance spek di dalam data komponennya
                        const specInstance = {
                            row: newSpecRow,
                            attr: specTomSelects.attr,
                            val: specTomSelects.val
                        };
                        componentInstanceData.specInstances.push(specInstance);

                        // Tambahkan listener hapus
                        newSpecRow.querySelector('.remove-spec-btn').addEventListener('click', () => {
                            specTomSelects.attr.destroy();
                            specTomSelects.val.destroy();
                            newSpecRow.remove();
                            componentInstanceData.specInstances = componentInstanceData.specInstances
                                .filter(inst => inst.row !== newSpecRow);
                        });
                    });

                    // 4. Tambahkan listener untuk "Hapus Komponen"
                    removeCompBtn.addEventListener('click', () => {
                        // Hancurkan semua TomSelect di dalamnya
                        compTypeTomSelect.destroy();
                        componentInstanceData.specInstances.forEach(spec => {
                            spec.attr.destroy();
                            spec.val.destroy();
                        });
                        // Hapus dari DOM
                        newCompRow.remove();
                        // Hapus dari array instance
                        tomSelectInstances.newComponents = tomSelectInstances.newComponents.filter(inst => inst
                            .id !== compIndex);
                    });

                    // 5. Tambahkan ke container utama
                    newComponentsContainer.appendChild(newCompRow);
                });

                // Submit Form
                submitBtn.addEventListener('click', async function() {
                    await submitCreateItemForm(this, form);
                });
            }

            /**
             * Fungsi Inisialisasi Spek (Generik)
             * Hanya menginisialisasi TomSelect dan mengembalikannya.
             */
            function initializeSpecRow(rowElement) {
                const attrSelectEl = rowElement.querySelector('.spec-attribute');
                const valSelectEl = rowElement.querySelector('.spec-value');
                if (!attrSelectEl || !valSelectEl) return null;

                let valueTomSelect; // Deklarasikan di sini

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
                        if (valueTomSelect) {
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

                return {
                    attr: attrTomSelect,
                    val: valueTomSelect
                };
            }

            /**
             * Fungsi Submit Form (DIROMBAK TOTAL)
             * Membangun data JSON secara manual dari form
             */
            async function submitCreateItemForm(submitBtn, form) {
                showLoading('Menyimpan Item...');
                submitBtn.disabled = true;

                // 1. Kumpulkan Data Item Utama
                const formData = {
                    is_component: form.querySelector('input[name="is_component"]:checked').value,
                    name: document.getElementById('name').value,
                    serial_code: document.getElementById('serial_code').value,
                    condition: form.querySelector('input[name="condition"]:checked').value,
                    type: tomSelectType.getValue(),
                    _token: form.querySelector('input[name="_token"]').value,
                    specifications: [],
                    new_components: []
                };

                // 2. Kumpulkan Spesifikasi Item Utama
                tomSelectInstances.mainSpecs.forEach(spec => {
                    const attrVal = spec.attr.getValue();
                    const valVal = spec.val.getValue();
                    if (attrVal && valVal) {
                        formData.specifications.push({
                            attribute: attrVal,
                            value: valVal
                        });
                    }
                });

                // 3. Kumpulkan Komponen Baru (jika ada)
                if (formData.is_component === '0') { // Hanya jika 'Item'
                    tomSelectInstances.newComponents.forEach(compInstance => {
                        const compRow = compInstance.row;
                        const componentData = {
                            name: compRow.querySelector('.new-component-name').value,
                            serial_code: compRow.querySelector('.new-component-serial').value,
                            condition: compRow.querySelector('.new-component-condition:checked').value,
                            type: compInstance.typeSelect.getValue(),
                            specifications: []
                        };

                        // 4. Kumpulkan Spesifikasi untuk Komponen ini
                        compInstance.specInstances.forEach(spec => {
                            const attrVal = spec.attr.getValue();
                            const valVal = spec.val.getValue();
                            if (attrVal && valVal) {
                                componentData.specifications.push({
                                    attribute: attrVal,
                                    value: valVal
                                });
                            }
                        });

                        formData.new_components.push(componentData);
                    });
                }

                // 5. Kirim data
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
                        // Jika error validasi, tampilkan pesan
                        if (response.status === 422) {
                            throw new Error(data.message || 'Data tidak valid.');
                        }
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
            // MODAL 2: Logika "Aksi Item" (Tidak ada perubahan)
            // =================================================================
            function initializeActionModal() {
                const modal = document.getElementById('action-modal');
                const overlay = document.getElementById('action-modal-overlay');
                const closeBtn = document.getElementById('action-modal-close-btn');

                if (!modal || !overlay || !closeBtn) {
                    Swal.fire('Error Kritis', 'Gagal memuat content modal Aksi. Silakan refresh halaman.', 'error');
                    return;
                }

                const closeActionModal = () => {
                    modal.classList.add('hidden');
                    document.getElementById('detail-modal').classList.add('hidden');
                    if (document.getElementById('create-item-modal').classList.contains('hidden') &&
                        document.getElementById('attach-desk-modal').classList.contains('hidden')) {
                        document.body.style.overflow = '';
                    }
                    currentItemId = null;
                    currentItemName = null;
                    currentItemCondition = null;
                    currentItemType = 'item'; // Reset
                }

                closeBtn.addEventListener('click', closeActionModal);
                overlay.addEventListener('click', (e) => {
                    if (e.target === overlay) closeActionModal();
                });
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeActionModal();
                });

                document.getElementById('action-btn-details').addEventListener('click', openDetailModal);
                document.getElementById('action-btn-attach').addEventListener('click', openAttachDeskModal);
                document.getElementById('action-btn-condition').addEventListener('click', submitChangeCondition);

                document.getElementById('action-btn-manage-comp').addEventListener('click', () => {
                    Swal.fire('Fitur Dalam Pengembangan',
                        'Fitur untuk mengelola komponen (attach/detach) sedang dibuat.', 'info');
                });
                document.getElementById('action-btn-edit-comp').addEventListener('click', () => {
                    Swal.fire('Fitur Dalam Pengembangan',
                        'Fitur untuk mengubah kondisi komponen sedang dibuat.', 'info');
                });

            }

            function updateActionModalButtons(type = 'item') {
                const attachBtn = document.getElementById('action-btn-attach');
                const manageCompBtn = document.getElementById('action-btn-manage-comp');
                const editCompBtn = document.getElementById('action-btn-edit-comp');
                const condBtn = document.getElementById('action-btn-condition');
                const detailBtn = document.getElementById('action-btn-details');
                if (!attachBtn || !manageCompBtn || !editCompBtn || !condBtn || !detailBtn) {
                    showToast('Peringatan', 'Gagal memuat semua tombol aksi di modal.', 'warning');
                    return;
                }
                detailBtn.style.display = 'flex';

                if (currentItemCondition) {
                    condBtn.innerHTML =
                        `<svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg><span class="font-semibold">Ubah ke Rusak</span>`;
                    condBtn.classList.remove('bg-green-500', 'hover:bg-green-600');
                    condBtn.classList.add('bg-yellow-500', 'hover:bg-yellow-600');
                } else {
                    condBtn.innerHTML =
                        `<svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg><span class="font-semibold">Ubah ke Bagus</span>`;
                    condBtn.classList.remove('bg-yellow-500', 'hover:bg-yellow-600');
                    condBtn.classList.add('bg-green-500', 'hover:bg-green-600');
                }

                if (type === 'item') {
                    attachBtn.style.display = 'flex';
                    manageCompBtn.style.display = 'flex';
                    editCompBtn.style.display = 'none';
                } else { // component
                    attachBtn.style.display = 'none';
                    manageCompBtn.style.display = 'none';
                    editCompBtn.style.display = 'flex';
                }
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
                    const url = (currentItemType === 'item') ?
                        `/admin/items/${currentItemId}/condition` :
                        `/admin/components/${currentItemId}/condition`;

                    const response = await fetch(url, {
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
                    const actionButton = (currentItemType === 'item') ?
                        document.querySelector(`.btn-open-action-item[data-item-id="${currentItemId}"]`) :
                        document.querySelector(`.btn-open-action-component[data-item-id="${currentItemId}"]`);

                    if (actionButton) {
                        const row = actionButton.closest('tr');
                        if (row) {
                            actionButton.dataset.itemCondition = data.new_condition ? '1' : '0';
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
                    }

                    document.getElementById('action-modal').classList.add('hidden');
                    document.getElementById('detail-modal').classList.add('hidden');
                    if (document.getElementById('create-item-modal').classList.contains('hidden') &&
                        document.getElementById('attach-desk-modal').classList.contains('hidden')) {
                        document.body.style.overflow = '';
                    }

                } catch (error) {
                    hideLoading();
                    Swal.fire('Gagal', error.message, 'error');
                }
            }

            // =================================================================
            // MODAL 3: Logika "Pasang ke Meja" (Tidak ada perubahan)
            // =================================================================
            function initializeDeskMapperModal() {
                const modal = document.getElementById('attach-desk-modal');
                const overlay = document.getElementById('attach-desk-modal-overlay');
                const closeBtn = document.getElementById('attach-desk-modal-close-btn');
                const labSelectorEl = document.getElementById('lab-selector-modal');

                if (!modal || !overlay || !closeBtn || !labSelectorEl) {
                    Swal.fire('Error Kritis', 'Gagal memuat komponen modal Pasang Meja. Silakan refresh halaman.', 'error');
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

                    if (document.getElementById('action-modal').classList.contains('hidden') &&
                        document.getElementById('detail-modal').classList.contains('hidden')) {
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
                    showToast('Gagal Memuat Denah', error.message, 'error');
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
                    text: `Anda akan memasang '${currentItemName}' ke meja ${deskLocation}. Lanjutkan?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Pasang!',
                    cancelButtonText: 'Batal'
                });

                if (!result.isConfirmed) return;

                showLoading('Memasang Item...');

                try {
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
                    document.getElementById('detail-modal').classList.add('hidden');
                    document.body.style.overflow = '';

                    // Hapus item dari tabel utama
                    const actionButton = document.querySelector(`.btn-open-action-item[data-item-id="${currentItemId}"]`);
                    if (actionButton) {
                        const rowToRemove = actionButton.closest('tr');
                        if (rowToRemove) {
                            rowToRemove.style.transition = 'opacity 0.5s ease';
                            rowToRemove.style.opacity = '0';
                            setTimeout(() => {
                                rowToRemove.remove();
                                // Refresh data di DataTables setelah hapus
                                try {
                                    const datatable = te.Datatable.getInstance(document.getElementById(
                                        'items-datatable'));
                                    if (datatable) {
                                        datatable.search(document.getElementById('datatable-search-input').value);
                                    }
                                } catch (e) {
                                    console.warn("Gagal refresh datatable instance:", e)
                                    showToast('Gagal refresh datatable instance', e, 'error');
                                }
                            }, 500);
                        }
                    }

                } catch (error) {
                    hideLoading();
                    Swal.fire('Gagal', error.message, 'error');
                }
            }

            function initializeDetailModal() {
                const modal = document.getElementById('detail-modal');
                const overlay = document.getElementById('detail-modal-overlay');
                const closeBtn = document.getElementById('detail-modal-close-btn');

                if (!modal || !overlay || !closeBtn) {
                    console.warn('Elemen Modal Detail tidak ditemukan');
                    return;
                }

                const closeDetailModal = () => {
                    modal.classList.add('hidden');
                    // Jangan reset body overflow jika modal aksi masih terbuka
                    if (document.getElementById('action-modal').classList.contains('hidden')) {
                        document.body.style.overflow = '';
                    }
                }

                closeBtn.addEventListener('click', closeDetailModal);
                overlay.addEventListener('click', (e) => {
                    if (e.target === overlay) closeDetailModal();
                });
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeDetailModal();
                });
            }

            async function openDetailModal() {
                const modal = document.getElementById('detail-modal');
                const titleEl = document.getElementById('detail-item-name');
                const contentEl = document.getElementById('detail-modal-content');
                if (!modal || !titleEl || !contentEl) return;

                // 1. Tampilkan modal & loading
                titleEl.textContent = currentItemName;
                modal.classList.remove('hidden');
                contentEl.innerHTML = `<div class="flex items-center justify-center py-12">
                                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
                                  </div>`;

                // 2. Tentukan endpoint
                const url = (currentItemType === 'item') ?
                    `/admin/items/${currentItemId}/details` :
                    `/admin/components/${currentItemId}/details`;

                try {
                    // 3. Fetch data
                    const response = await fetch(url, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });

                    if (!response.ok) {
                        throw new Error(`Gagal mengambil data. Status: ${response.status}`);
                    }

                    const data = await response.json();

                    // 4. Render data ke modal
                    populateDetailModal(data);

                } catch (error) {
                    console.error("Gagal fetch detail:", error);
                    contentEl.innerHTML = `<div class="text-center py-12 text-red-500">
                                        <h3 class="font-semibold text-lg">Gagal Memuat Data</h3>
                                        <p class="text-sm">${error.message}</p>
                                      </div>`;
                }
            }

            function populateDetailModal(data) {
                const contentEl = document.getElementById('detail-modal-content');
                if (!contentEl) return;

                // Helper untuk Status & Kondisi
                const conditionText = data.condition ?
                    '<span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800">Bagus</span>' :
                    '<span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-800">Rusak</span>';

                let locationHtml = '';
                let componentsHtml = '';

                // Tentukan Lokasi & Komponen berdasarkan Tipe
                if (currentItemType === 'item') {
                    // Ini adalah ITEM
                    if (data.desk) {
                        locationHtml = `
                        <dt class="col-span-1 font-semibold text-gray-800">Lokasi</dt>
                        <dd class="col-span-2 text-gray-600">${data.desk.lab.name} - Meja ${data.desk.location}</dd>
                    `;
                    } else {
                        locationHtml = `
                        <dt class="col-span-1 font-semibold text-gray-800">Lokasi</dt>
                        <dd class="col-span-2 text-gray-500 italic">Belum terpasang</dd>
                    `;
                    }

                    // Buat daftar komponen anak
                    if (data.components && data.components.length > 0) {
                        componentsHtml = `
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <h4 class="text-md font-semibold text-gray-800 mb-2">Komponen Terpasang (${data.components.length}):</h4>
                            <ul class="list-disc pl-5 space-y-1">
                                ${data.components.map(comp => `
                                                    <li class="text-sm text-gray-600">
                                                        <strong>${comp.name}</strong> (${comp.serial_code})
                                                        <span class="ml-2 px-1.5 py-0.5 text-xs font-medium rounded-full bg-purple-100 text-purple-800">${comp.type ? comp.type.name : 'N/A'}</span>
                                                    </li>
                                                `).join('')}
                            </ul>
                        </div>
                    `;
                    } else {
                        componentsHtml = `
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <h4 class="text-md font-semibold text-gray-800 mb-2">Komponen Terpasang:</h4>
                            <p class="text-sm text-gray-500 italic">Tidak ada komponen terpasang.</p>
                        </div>
                    `;
                    }

                } else {
                    // Ini adalah COMPONENT
                    if (data.item) {
                        let itemLocation = data.item.desk ?
                            `${data.item.desk.lab.name} - Meja ${data.item.desk.location}` :
                            'Item induk belum terpasang';
                        locationHtml = `
                        <dt class="col-span-1 font-semibold text-gray-800">Terpasang di Item</dt>
                        <dd class="col-span-2 text-gray-600">${data.item.name} (${data.item.serial_code})</dd>
                        <dt class="col-span-1 font-semibold text-gray-800">Lokasi Item</dt>
                        <dd class="col-span-2 text-gray-600">${itemLocation}</dd>
                    `;
                    } else {
                        locationHtml = `
                        <dt class="col-span-1 font-semibold text-gray-800">Lokasi</dt>
                        <dd class="col-span-2 text-gray-500 italic">Komponen bebas (belum terpasang ke item)</dd>
                    `;
                    }
                }

                // Buat daftar spesifikasi
                let specsHtml = '';
                if (data.spec_set_values && data.spec_set_values.length > 0) {
                    specsHtml = `
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <h4 class="text-md font-semibold text-gray-800 mb-2">Spesifikasi:</h4>
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-1">
                            ${data.spec_set_values.map(spec => `
                                                <div class="col-span-1">
                                                    <dt class="font-semibold text-gray-800 text-sm">${spec.spec_attributes ? spec.spec_attributes.name : 'N/A'}</dt>
                                                    <dd class="text-gray-600 text-sm pl-2">${spec.value}</dd>
                                                </div>
                                            `).join('')}
                        </dl>
                    </div>
                `;
                } else {
                    specsHtml = `
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <h4 class="text-md font-semibold text-gray-800 mb-2">Spesifikasi:</h4>
                        <p class="text-sm text-gray-500 italic">Tidak ada data spesifikasi.</p>
                    </div>
                `;
                }


                // Gabungkan semua HTML
                contentEl.innerHTML = `
                <div class="space-y-2">
                    <dl class="grid grid-cols-3 gap-x-4 gap-y-2">
                        <dt class="col-span-1 font-semibold text-gray-800">Nama</dt>
                        <dd class="col-span-2 text-gray-600">${data.name}</dd>
                        
                        <dt class="col-span-1 font-semibold text-gray-800">Serial Code</dt>
                        <dd class="col-span-2 text-gray-600 font-mono">${data.serial_code}</dd>
                        
                        <dt class="col-span-1 font-semibold text-gray-800">Tipe</dt>
                        <dd class="col-span-2 text-gray-600">${data.type ? data.type.name : 'N/A'}</dd>
                        
                        <dt class="col-span-1 font-semibold text-gray-800">Kondisi</dt>
                        <dd class="col-span-2">${conditionText}</dd>
                        
                        ${locationHtml}
                    </dl>
                    
                    ${specsHtml}
                    
                    ${componentsHtml}
                </div>
            `;
            }

            function initializePageFilters() {
                const filterForm = document.getElementById('filter-form');
                if (!filterForm) return;

                // Inisialisasi semua TomSelect untuk filter
                new TomSelect('#filter_lab_select', {
                    plugins: ['clear_button']
                });
                new TomSelect('#filter_type_select', {
                    plugins: ['clear_button']
                });
                new TomSelect('#filter_status', {
                    plugins: ['clear_button']
                });
                new TomSelect('#filter_condition', {
                    plugins: ['clear_button']
                });

                const specValSelect = new TomSelect('#filter_spec_val', {
                    plugins: ['clear_button']
                });
                specValSelect.disable();

                const specAttrSelect = new TomSelect('#filter_spec_attr', {
                    plugins: ['clear_button'],
                    onChange: (value) => {
                        specValSelect.clear();
                        specValSelect.clearOptions();
                        if (!value) {
                            specValSelect.disable();
                        } else {
                            const selectedOptionData = specAttrSelect.options[value];

                            if (!selectedOptionData || !selectedOptionData['values']) {
                                showToast('Gagal Memuat Filter',
                                    'Data spesifikasi (values) tidak ditemukan di element Dropdown Spesifikasi Attribute.',
                                    'error');
                                specValSelect.disable();
                                return;
                            }
                            try {
                                // TomSelect menyimpan atribut data- dengan prefixnya
                                const values = JSON.parse(selectedOptionData['values']);
                                specValSelect.addOptions(values.map(v => ({
                                    value: v.id,
                                    text: v.value
                                })));
                                specValSelect.enable();
                            } catch (e) {
                                console.error("Gagal parse values:", e, "Data string:", selectedOptionData[
                                    'values']);
                                showToast('Gagal Memuat Filter',
                                    'Data spesifikasi (values) di element Dropdown Spesifikasi Attribute rusak.',
                                    'error');
                                specValSelect.disable();
                            }
                        }
                    }
                });

                const initialAttrId = '{{ data_get($filters, 'spec_attribute_id') }}';
                const initialValId = '{{ data_get($filters, 'spec_value_id') }}';

                if (initialAttrId) {
                    specAttrSelect.setValue(initialAttrId, true); // silent
                    const selectedOption = specAttrSelect.options[initialAttrId];
                    if (selectedOption) {
                        try {
                            const values = JSON.parse(selectedOption['values']);
                            specValSelect.addOptions(values.map(v => ({
                                value: v.id,
                                text: v.value
                            })));
                            specValSelect.enable();
                            if (initialValId) {
                                specValSelect.setValue(initialValId, true); // silent
                            }
                        } catch (e) {
                            console.error("Gagal parse data-values saat init:", e);
                            showToast('Gagal Memuat Filter', 'Data spesifikasi (initialisasi spesifikasi value) rusak.',
                                'error');
                        }
                    } else if (!selectedOption || !selectedOption['values']) {
                        showToast('Gagal Memuat Filter',
                            'Data spesifikasi (values) tidak ditemukan di element Dropdown Spesifikasi Attribute, mohon reset Filter dan coba lakukan filter kembali.',
                            'error');
                        specValSelect.disable();
                        return;
                    }
                }
            }

            // =================================================================
            // DOMContentLoaded (Hanya memanggil fungsi)
            // =================================================================
            document.addEventListener('DOMContentLoaded', function() {

                // --- Inisialisasi Semua Modal ---
                initializeCreateItemModal();
                initializeActionModal();
                initializeDeskMapperModal();
                initializeDetailModal();
                
                // --- Inisialisasi Filter Halaman ---
                initializePageFilters();

                const resetFilterBtn = document.getElementById('reset-filter-btn');
                const applyFilterBtn = document.getElementById('apply-filter-btn');
                const filterForm = document.getElementById('filter-form');

                if (resetFilterBtn) {
                    resetFilterBtn.addEventListener('click', function(event) {
                        event.preventDefault();
                        showLoading('Loading...', 'Mereset filter...');

                        setTimeout(() => {
                            window.location.href = this.href;
                        }, 100);
                    });
                }

                if (applyFilterBtn && filterForm) {
                    filterForm.addEventListener('submit', function(event) {
                        event.preventDefault();
                        showLoading('Loading...', 'Menerapkan filter...');

                        setTimeout(() => {
                            this.submit();
                        }, 100);
                    });
                }

                // --- Inisialisasi DataTables ---
                try {
                    let itemDataTable = new te.Datatable(document.getElementById('items-datatable'), {
                        search: true,
                        bordered: true,
                        striped: true,
                        noFoundMessage: 'Tidak ada Item yang tercatat di Inventaris.',
                        loading: false,
                        fixedHeader: true,
                        pagination: true,
                        entries: 10,
                        entriesOptions: [5, 10, 25, 50, 100],
                    });

                    let componentDataTable = new te.Datatable(document.getElementById('components-datatable'), {
                        hover: true,
                        striped: true,
                        bordered: true,
                        noFoundMessage: 'Tidak ada Component yang tercatat di Inventaris.',
                        loading: false,
                        fixedHeader: true,
                        pagination: true,
                        entries: 10,
                        entriesOptions: [5, 10, 25, 50, 100],
                    });

                    document.getElementById('datatable-search-input').addEventListener('input', (e) => {
                        itemDataTable.search(e.target.value);
                        componentDataTable.search(e.target.value);
                    });

                } catch (e) {
                    showToast('Gagal Inisialisasi', 'Gagal memuat fungsionalitas tabel (sorting/pencarian).',
                        'warning');
                }

                // --- Inisialisasi Tombol Utama ---
                const openCreateBtn = document.getElementById('open-create-modal-btn');
                if (openCreateBtn) {
                    openCreateBtn.addEventListener('click', window.openCreateItemModal);
                } else {
                    Swal.fire('Error Kritis', 'Tombol "Tambah Item" tidak ditemukan. Silakan refresh halaman.',
                        'error');
                }

                // --- Inisialisasi Listener Tabel (Aksi) ---
                const itemsDatatableEl = document.getElementById('items-datatable');
                const componentsDatatableEl = document.getElementById('components-datatable');
                const actionModal = document.getElementById('action-modal');
                const actionModalTitle = document.getElementById('action-modal-title');

                // Listener untuk Tombol Aksi ITEMS
                if (itemsDatatableEl && actionModal && actionModalTitle) {
                    itemsDatatableEl.addEventListener('click', function(e) {
                        const actionButton = e.target.closest('.btn-open-action-item');
                        if (!actionButton) return;

                        currentItemId = actionButton.dataset.itemId;
                        currentItemName = actionButton.dataset.itemName;
                        currentItemCondition = actionButton.dataset.itemCondition === '1';
                        currentItemType = 'item';

                        actionModalTitle.innerHTML =
                            `Aksi untuk (Item): <span class="font-bold text-indigo-600">${currentItemName}</span>`;
                        updateActionModalButtons('item');

                        actionModal.classList.remove('hidden');
                        document.body.style.overflow = 'hidden';
                    });
                }

                // Listener untuk Tombol Aksi COMPONENTS
                if (componentsDatatableEl && actionModal && actionModalTitle) {
                    componentsDatatableEl.addEventListener('click', function(e) {
                        const actionButton = e.target.closest('.btn-open-action-component');
                        if (!actionButton) return;

                        currentItemId = actionButton.dataset.itemId;
                        currentItemName = actionButton.dataset.itemName;
                        currentItemCondition = actionButton.dataset.itemCondition === '1';
                        currentItemType = 'component';

                        actionModalTitle.innerHTML =
                            `Aksi untuk (Component): <span class="font-bold text-purple-600">${currentItemName}</span>`;
                        updateActionModalButtons('component');

                        actionModal.classList.remove('hidden');
                        document.body.style.overflow = 'hidden';
                    });
                }
            });
        </script>
    @endsection
