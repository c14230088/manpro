@extends('layouts.admin')

@section('title', 'Repository Manager')

@section('style')
    <style>
        .no-select {
            user-select: none;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
        }

        .item-card.selected {
            background-color: #e0e7ff;
            border-color: #6366f1;
        }

        .item-card.dragging {
            opacity: 0.5;
            cursor: grabbing;
        }

        /* 1. SELECTION BOX: Absolute agar ikut koordinat dokumen */
        #selection-box {
            position: absolute;
            border: 2px dashed #6366f1;
            background: rgba(99, 102, 241, 0.2);
            pointer-events: none;
            z-index: 9999;
        }

        /* 2. DROP OVERLAY: Google Drive Style
                                                           Kita gunakan class Tailwind 'fixed' di HTML, tapi CSS transition disini */
        #drop-overlay {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            pointer-events: none;
            /* Penting: agar file bisa di-drop "tembus" ke container di bawahnya */
            z-index: 5000;
        }

        /* 3. CONTEXT MENU: Absolute
                                                           Perbaikan utama: Menggunakan absolute agar sinkron dengan pageY JS */
        #context-menu {
            position: absolute;
            /* Hapus top/left default, akan diatur via JS */
        }

        .item-card {
            cursor: pointer;
        }

        .item-card:active {
            cursor: grabbing;
        }

        /* Animasi halus untuk pill drop */
        .drop-active {
            transform: translate(-50%, 0) !important;
            opacity: 1 !important;
        }

        .drop-hidden {
            transform: translate(-50%, 20px) !important;
            opacity: 0 !important;
        }
    </style>
@endsection

@section('body')
    {{-- Selection Box (Wajib di luar container relative agar koordinat akurat) --}}
    <div id="selection-box" class="hidden"></div>

    {{-- Posisi fixed di bawah, centered, bentuk pill biru --}}
    <div id="drop-overlay"
        class="drop-hidden fixed bottom-10 left-1/2 bg-blue-600 text-white rounded-full px-8 py-4 shadow-2xl flex items-center gap-4 transform -translate-x-1/2">
        <div class="bg-white/20 p-2 rounded-full">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
            </svg>
        </div>
        <div class="flex flex-col">
            <span class="text-lg font-bold leading-tight">Drop files to upload</span>
            <span class="text-sm text-blue-100">Upload to this Folder</span>
        </div>
    </div>

    {{-- Header / Toolbar --}}
    <div class="flex flex-col md:flex-row w-full py-4 shadow-md items-center justify-between mb-5 px-6 md:px-4">
        <h1 class="text-center text-4xl uppercase font-bold mb-2 md:mb-0">Repository Manager</h1>
        <div class="flex gap-2">
            <div class="relative">
                <button id="btn-new"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    New
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd"></path>
                    </svg>
                </button>
                <div id="new-menu"
                    class="hidden absolute top-full mt-2 right-0 bg-white rounded-lg shadow-xl border border-gray-200 py-2 z-50 min-w-[200px]">
                    <button class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100" data-action="new-folder">
                        <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"></path>
                        </svg>
                        New Folder
                    </button>
                    <button class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100" data-action="upload-file">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                            </path>
                        </svg>
                        Upload Files
                    </button>
                    <button class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100" data-action="upload-folder">
                        <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"></path>
                        </svg>
                        Upload Folder
                    </button>
                </div>
            </div>
            <div class="relative">
                <button id="btn-view"
                    class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                        </path>
                    </svg>
                    View
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd"></path>
                    </svg>
                </button>
                <div id="view-menu"
                    class="hidden absolute top-full mt-2 right-0 bg-white rounded-lg shadow-xl border border-gray-200 py-2 z-50 min-w-[180px]">
                    <button class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100" data-view="grid-large">
                        Large Icons
                        <svg class="w-4 h-4 inline ml-auto hidden view-check" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </button>
                    <button class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100" data-view="grid-medium">
                        Medium Icons
                        <svg class="w-4 h-4 inline ml-auto hidden view-check" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </button>
                    <button class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100" data-view="list">
                        List
                        <svg class="w-4 h-4 inline ml-auto hidden view-check" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </button>
                    <button class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100" data-view="details">
                        Details
                        <svg class="w-4 h-4 inline ml-auto hidden view-check" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="relative">
                <button id="btn-sort"
                    class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path>
                    </svg>
                    Sort
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd"></path>
                    </svg>
                </button>
                <div id="sort-menu"
                    class="hidden absolute top-full mt-2 right-0 bg-white rounded-lg shadow-xl border border-gray-200 py-2 z-50 min-w-[180px]">
                    <button class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 flex items-center"
                        data-sort="name">
                        Name
                        <svg class="w-4 h-4 ml-auto hidden sort-check" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </button>
                    <button class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 flex items-center"
                        data-sort="date">
                        Date Modified
                        <svg class="w-4 h-4 ml-auto hidden sort-check" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </button>
                    <button class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 flex items-center"
                        data-sort="size">
                        Size
                        <svg class="w-4 h-4 ml-auto hidden sort-check" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </button>
                    <button class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 flex items-center"
                        data-sort="type">
                        Type
                        <svg class="w-4 h-4 ml-auto hidden sort-check" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-6 pb-12">
        {{-- Breadcrumb --}}
        <nav class="mb-4" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2 text-sm">
                <li>
                    <a href="{{ route('admin.repository') }}"
                        class="breadcrumb-link text-indigo-600 hover:text-indigo-800 focus:outline-none focus:underline">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z">
                            </path>
                        </svg>
                    </a>
                </li>
                @foreach ($breadcrumbs as $crumb)
                    <li class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('admin.repository', ['folder' => $crumb->id]) }}"
                            class="breadcrumb-link ml-2 text-indigo-600 hover:text-indigo-800 focus:outline-none focus:underline">
                            {{ $crumb->name }}
                        </a>
                    </li>
                @endforeach
            </ol>
        </nav>

        <input type="file" id="file-input" class="hidden" multiple webkitdirectory="" aria-label="File input">
        <input type="file" id="folder-input" class="hidden" webkitdirectory="" directory="" multiple
            aria-label="Folder input">

        {{-- Files & Folders Grid --}}
        <div id="items-container"
            class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 no-select relative min-h-[500px]">

            <div id="details-header"
                class="hidden grid grid-cols-6 gap-4 pb-3 mb-4 border-b border-gray-300 text-sm font-semibold text-gray-700">
                <div class="col-span-2">Name</div>
                <div>Type</div>
                <div>Size</div>
                <div>Creator</div>
                <div>Date Modified</div>
            </div>
            <div id="items-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4"
                role="list" data-view="grid-medium">
                @forelse($folders as $folder)
                    <div class="item-card folder-item group relative p-4 border-2 border-gray-200 rounded-lg hover:shadow-lg hover:border-indigo-400 transition-all cursor-grab focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        data-id="{{ $folder->id }}" data-type="folder" data-name="{{ $folder->name }}"
                        data-date="{{ $folder->updated_at }}" data-size="0" data-mime=""
                        data-creator="{{ $folder->creator->name ?? 'System' }}" data-created="{{ $folder->created_at }}"
                        draggable="true" tabindex="0" role="listitem" aria-label="Folder {{ $folder->name }}">
                        <div class="item-grid-view flex flex-col items-center pointer-events-none">
                            <svg class="w-16 h-16 text-yellow-500 mb-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"></path>
                            </svg>
                            <p class="text-sm text-center text-gray-800 font-medium truncate w-full"
                                title="{{ $folder->name }}">{{ $folder->name }}</p>
                        </div>
                        <div
                            class="item-details-view hidden grid grid-cols-6 gap-4 items-center text-sm pointer-events-none">
                            <div class="flex items-center gap-2 col-span-2">
                                <svg class="w-8 h-8 text-yellow-500 flex-shrink-0" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"></path>
                                </svg>
                                <span class="font-medium truncate">{{ $folder->name }}</span>
                            </div>
                            <div class="text-gray-600">Folder</div>
                            <div class="text-gray-600">-</div>
                            <div class="text-gray-600">{{ $folder->creator->name ?? 'System' }}</div>
                            <div class="text-gray-600">{{ $folder->updated_at->format('d/m/Y H:i') }}</div>
                        </div>
                        <button
                            class="item-menu-btn absolute top-2 right-2 opacity-0 group-hover:opacity-100 focus:opacity-100 p-1.5 rounded-full hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 pointer-events-auto"
                            aria-label="Folder options for {{ $folder->name }}">
                            <svg class="w-5 h-5 text-gray-700" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z">
                                </path>
                            </svg>
                        </button>
                    </div>
                @empty
                @endforelse

                @forelse($files as $file)
                    <div class="item-card file-item group relative p-4 border-2 border-gray-200 rounded-lg hover:shadow-lg hover:border-indigo-400 transition-all cursor-grab focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        data-id="{{ $file->id }}" data-type="file" data-name="{{ $file->original_name }}"
                        data-date="{{ $file->updated_at }}" data-size="{{ $file->size }}"
                        data-mime="{{ $file->mime_type }}" data-creator="{{ $file->creator->name ?? 'System' }}"
                        data-created="{{ $file->created_at }}" draggable="true" tabindex="0" role="listitem"
                        aria-label="File {{ $file->original_name }}">
                        <div class="item-grid-view flex flex-col items-center pointer-events-none">
                            <svg class="w-16 h-16 text-blue-500 mb-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <p class="text-sm text-center text-gray-800 font-medium truncate w-full"
                                title="{{ $file->original_name }}">{{ $file->original_name }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ number_format($file->size / 1024, 2) }} KB</p>
                        </div>
                        <div
                            class="item-details-view hidden grid grid-cols-6 gap-4 items-center text-sm pointer-events-none">
                            <div class="flex items-center gap-2 col-span-2">
                                <svg class="w-8 h-8 text-blue-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <span class="font-medium truncate">{{ $file->original_name }}</span>
                            </div>
                            <div class="text-gray-600 truncate">{{ $file->mime_type }}</div>
                            <div class="text-gray-600">{{ number_format($file->size / 1024, 2) }} KB</div>
                            <div class="text-gray-600">{{ $file->creator->name ?? 'System' }}</div>
                            <div class="text-gray-600">{{ $file->updated_at->format('d/m/Y H:i') }}</div>
                        </div>
                        <button
                            class="item-menu-btn absolute top-2 right-2 opacity-0 group-hover:opacity-100 focus:opacity-100 p-1.5 rounded-full hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 pointer-events-auto"
                            aria-label="File options for {{ $file->original_name }}">
                            <svg class="w-5 h-5 text-gray-700" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z">
                                </path>
                            </svg>
                        </button>
                    </div>
                @empty
                @endforelse

                @if ($folders->isEmpty() && $files->isEmpty())
                    <div class="col-span-full text-center py-12 text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                            </path>
                        </svg>
                        <p class="mt-2">This folder is empty</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Context Menu --}}
    {{-- Absolute positioning + z-index tinggi --}}
    <div id="context-menu"
        class="hidden absolute bg-white rounded-lg shadow-xl border border-gray-200 py-2 z-[9999] min-w-[180px]"
        role="menu" aria-orientation="vertical">
        <button
            class="context-menu-item w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 focus:bg-gray-100 focus:outline-none"
            data-action="open" role="menuitem">
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
            </svg>
            Open
        </button>
        <button
            class="context-menu-item w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 focus:bg-gray-100 focus:outline-none"
            data-action="rename" role="menuitem">
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                </path>
            </svg>
            Rename
        </button>
        <button
            class="context-menu-item w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 focus:bg-gray-100 focus:outline-none"
            data-action="download" role="menuitem">
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
            </svg>
            Download
        </button>
        <hr class="my-2">
        <button
            class="context-menu-item w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 focus:bg-red-50 focus:outline-none"
            data-action="delete" role="menuitem">
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                </path>
            </svg>
            Delete
        </button>
    </div>

    {{-- Modal New Folder --}}
    <div id="folder-modal"
        class="hidden fixed inset-0 bg-gray-900 bg-opacity-75 z-[3000] flex items-center justify-center p-4"
        role="dialog" aria-modal="true" aria-labelledby="folder-modal-title">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
            <div class="flex justify-between items-center p-4 border-b">
                <h3 id="folder-modal-title" class="text-lg font-semibold">New Folder</h3>
                <button id="close-folder-modal"
                    class="text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded"
                    aria-label="Close dialog">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
            <form id="folder-form" class="p-6">
                <label for="folder-name" class="block text-sm font-semibold text-gray-700 mb-2">Folder Name</label>
                <input type="text" id="folder-name"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    required aria-required="true">
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" id="cancel-folder"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500">Cancel</button>
                    <button type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">Create</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        const currentFolderId = "{{ $currentFolder?->id }}";
        let contextMenuTarget = null;
        let selectedItems = new Set();
        let isSelecting = false;
        let selectionStart = null;

        document.getElementById('repository').classList.add('bg-slate-100');
        document.getElementById('repository').classList.add('active');

        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('file-input');
            const folderInput = document.getElementById('folder-input');
            const contextMenu = document.getElementById('context-menu');
            const folderModal = document.getElementById('folder-modal');
            const itemsGrid = document.getElementById('items-grid');
            const selectionBox = document.getElementById('selection-box');
            const dropOverlay = document.getElementById('drop-overlay');
            const itemsContainer = document.getElementById('items-container');
            let dragCounter = 0;

            // Toolbar Menus
            setupToolbarMenus();

            // Drag & Drop Upload with Google Drive Style Overlay
            itemsContainer.addEventListener('dragenter', (e) => {
                e.preventDefault();
                e.stopPropagation();

                if (e.dataTransfer.types.includes('text/plain')) {
                    return;
                }

                dragCounter++;
                dropOverlay.classList.remove('drop-hidden');
                dropOverlay.classList.add('drop-active');
                itemsContainer.classList.remove('bg-white', 'shadow-sm', 'border-gray-200');
                itemsContainer.classList.add('bg-blue-50', 'shadow-md', 'shadow-blue-400/80', 'border-2',
                    'border-blue-500');
            });

            itemsContainer.addEventListener('dragleave', (e) => {
                e.preventDefault();
                e.stopPropagation();
                dragCounter--;
                if (dragCounter === 0) {
                    dropOverlay.classList.remove('drop-active');
                    dropOverlay.classList.add('drop-hidden');
                    itemsContainer.classList.remove('bg-blue-50', 'shadow-md', 'shadow-blue-400/80',
                        'border-2', 'border-blue-500');
                    itemsContainer.classList.add('bg-white', 'shadow-sm', 'border-gray-200');
                }
            });

            itemsContainer.addEventListener('dragover', (e) => {
                e.preventDefault();
                e.stopPropagation();
                e.dataTransfer.dropEffect = 'copy';
            });

            itemsContainer.addEventListener('drop', handleDrop);
            fileInput.addEventListener('change', (e) => handleFiles(e));
            folderInput.addEventListener('change', (e) => handleFiles(e));

            function handleDrop(e) {
                e.preventDefault();
                e.stopPropagation();
                dragCounter = 0;

                dropOverlay.classList.remove('drop-active');
                dropOverlay.classList.add('drop-hidden');
                itemsContainer.classList.remove('bg-blue-50', 'shadow-md', 'shadow-blue-400/80', 'border-2',
                    'border-blue-500');
                itemsContainer.classList.add('bg-white', 'shadow-sm', 'border-gray-200');

                if (e.dataTransfer.types.includes('text/plain')) {
                    return;
                }

                if (e.dataTransfer.items) {
                    handleDataTransferItems(e.dataTransfer.items);
                } else if (e.dataTransfer.files.length > 0) {
                    handleFiles({
                        target: {
                            files: e.dataTransfer.files
                        }
                    });
                }
            }

            async function handleDataTransferItems(items) {
                const entries = [];
                for (let i = 0; i < items.length; i++) {
                    const item = items[i].webkitGetAsEntry();
                    if (item) {
                        entries.push(item);
                    }
                }

                for (const entry of entries) {
                    if (entry.isFile) {
                        entry.file(file => uploadFile(file, currentFolderId));
                    } else if (entry.isDirectory) {
                        await traverseDirectory(entry, currentFolderId);
                    }
                }
            }

            async function traverseDirectory(dirEntry, parentFolderId) {
                try {
                    const response = await fetch('{{ route('admin.repository.folder.create') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            name: dirEntry.name,
                            parent_id: parentFolderId
                        })
                    });

                    if (!response.ok) {
                        const data = await response.json();
                        if (response.status === 409) {
                            showToast('Error', data.message ||
                                `Folder "${dirEntry.name}" already exists, skipping...`, 'error');
                            return;
                        }
                        showToast('Error', data.message || `Folder "${dirEntry.name}" already exists`, 'error');
                        throw new Error(data.message || 'Create failed');
                    }
                    const data = await response.json();
                    if (data.success) {
                        const newFolderId = data.folder.id;
                        const reader = dirEntry.createReader();

                        reader.readEntries(async (entries) => {
                            for (const entry of entries) {
                                if (entry.isFile) {
                                    entry.file(file => uploadFile(file, newFolderId));
                                } else if (entry.isDirectory) {
                                    await traverseDirectory(entry, newFolderId);
                                }
                            }
                        });

                        if (parentFolderId === currentFolderId) {
                            addFolderToGrid(data.folder);
                        }
                    } else {
                        showToast('Error', data.message || 'Error creating Folder', 'error');
                    }
                } catch (error) {
                    console.error('Error creating folder:', error);
                }
            }

            async function handleFiles(e) {
                const files = Array.from(e.target.files);
                const isFolder = e.target.id === 'folder-input';

                if (isFolder && files.length > 0) {
                    const folderStructure = {};

                    files.forEach(file => {
                        const path = file.webkitRelativePath || file.name;
                        const parts = path.split('/');

                        if (parts.length > 1) {
                            const folderName = parts[0];
                            if (!folderStructure[folderName]) {
                                folderStructure[folderName] = [];
                            }
                            folderStructure[folderName].push(file);
                        }
                    });
                    for (const folderName of Object.keys(folderStructure)) {
                        try {
                            const response = await fetch(
                                '{{ route('admin.repository.folder.create') }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector(
                                            'meta[name="csrf-token"]').content
                                    },
                                    body: JSON.stringify({
                                        name: folderName,
                                        parent_id: currentFolderId
                                    })
                                });

                            if (!response.ok) {
                                const data = await response.json();
                                showToast('Error', data.message || 'Create failed', 'error');
                                continue;
                            }

                            const data = await response.json();
                            if (data.success) {
                                const newFolderId = data.folder.id;
                                addFolderToGrid(data.folder);

                                await uploadFolderFiles(folderStructure[folderName], newFolderId,
                                    folderName);
                            }
                        } catch (error) {
                            console.error('Error creating folder:', error);
                            showToast('Error', error.message, 'error');
                        }
                    }
                } else {
                    files.forEach(file => uploadFile(file, currentFolderId));
                }
            }

            async function uploadFolderFiles(files, parentFolderId, basePath) {
                const subfolders = {};
                const directFiles = [];

                files.forEach(file => {
                    const path = file.webkitRelativePath || file.name;
                    const parts = path.split('/');
                    const relativeParts = parts.slice(1);

                    if (relativeParts.length === 1) {
                        directFiles.push(file);
                    } else {
                        const subfolderName = relativeParts[0];
                        if (!subfolders[subfolderName]) {
                            subfolders[subfolderName] = [];
                        }
                        subfolders[subfolderName].push(file);
                    }
                });

                directFiles.forEach(file => uploadFile(file, parentFolderId));

                for (const subfolderName of Object.keys(subfolders)) {
                    try {
                        const response = await fetch('{{ route('admin.repository.folder.create') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .content
                            },
                            body: JSON.stringify({
                                name: subfolderName,
                                parent_id: parentFolderId
                            })
                        });

                        if (!response.ok) {
                            const data = await response.json();
                            showToast('Error', data.message || 'Create failed', 'error');
                            continue;
                        }

                        const data = await response.json();
                        if (data.success) {
                            await uploadFolderFiles(subfolders[subfolderName], data.folder.id, basePath + '/' +
                                subfolderName);
                        }
                    } catch (error) {
                        console.error('Error creating subfolder:', error);
                        showToast('Error', error.message, 'error');
                    }
                }
            }

            async function uploadFile(file, folderId = null) {
                const formData = new FormData();
                formData.append('file', file);
                const targetFolderId = folderId !== null ? folderId : currentFolderId;
                if (targetFolderId !== null) {
                    formData.append('folder_id', targetFolderId);
                }

                showLoadingToast('Uploading file...');

                try {
                    const response = await fetch('{{ route('admin.repository.upload') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: formData
                    });

                    const data = await response.json();
                    Swal.close();

                    if (!response.ok) {
                        throw new Error(data.message || 'Upload failed');
                    }

                    if (data.success) {
                        showToast('Success', 'File uploaded', 'success');
                        addFileToGrid(data.file);
                    } else {
                        showToast('Error', data.message || 'Upload failed', 'error');
                    }
                } catch (error) {
                    Swal.close();
                    showToast('Error', error.message, 'error');
                }
            }

            function addFileToGrid(file) {
                const emptyMsg = document.querySelector('.col-span-full');
                if (emptyMsg) emptyMsg.remove();

                const fileCard = document.createElement('div');
                fileCard.className =
                    'item-card file-item group relative p-4 border-2 border-gray-200 rounded-lg hover:shadow-lg hover:border-indigo-400 transition-all cursor-grab focus:outline-none focus:ring-2 focus:ring-indigo-500';
                fileCard.dataset.id = file.id;
                fileCard.dataset.type = 'file';
                fileCard.dataset.name = file.original_name;
                fileCard.dataset.date = file.updated_at;
                fileCard.dataset.size = file.size;
                fileCard.dataset.mime = file.mime_type;
                fileCard.dataset.creator = '{{ auth()->user()->name }}';
                fileCard.dataset.created = file.created_at;
                fileCard.draggable = true;
                fileCard.tabIndex = 0;

                fileCard.innerHTML = `
            <div class="item-grid-view flex flex-col items-center pointer-events-none">
                <svg class="w-16 h-16 text-blue-500 mb-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                </svg>
                <p class="text-sm text-center text-gray-800 font-medium truncate w-full" title="${file.original_name}">${file.original_name}</p>
                <p class="text-xs text-gray-500 mt-1">${(file.size / 1024).toFixed(2)} KB</p>
            </div>
            <div class="item-details-view hidden grid grid-cols-6 gap-4 items-center text-sm pointer-events-none">
                <div class="flex items-center gap-2 col-span-2">
                    <svg class="w-8 h-8 text-blue-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-medium truncate">${file.original_name}</span>
                </div>
                <div class="text-gray-600 truncate">${file.mime_type}</div>
                <div class="text-gray-600">${(file.size / 1024).toFixed(2)} KB</div>
                <div class="text-gray-600">{{ auth()->user()->name }}</div>
                <div class="text-gray-600">${new Date(file.updated_at).toLocaleString('id-ID')}</div>
            </div>
            <button class="item-menu-btn absolute top-2 right-2 opacity-0 group-hover:opacity-100 focus:opacity-100 p-1.5 rounded-full hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 pointer-events-auto">
                <svg class="w-5 h-5 text-gray-700" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path>
                </svg>
            </button>
        `;

                itemsGrid.appendChild(fileCard);
                attachItemEvents(fileCard);
            }

            function addFolderToGrid(folder) {
                const emptyMsg = document.querySelector('.col-span-full');
                if (emptyMsg) emptyMsg.remove();

                const folderCard = document.createElement('div');
                folderCard.className =
                    'item-card folder-item group relative p-4 border-2 border-gray-200 rounded-lg hover:shadow-lg hover:border-indigo-400 transition-all cursor-grab focus:outline-none focus:ring-2 focus:ring-indigo-500';
                folderCard.dataset.id = folder.id;
                folderCard.dataset.type = 'folder';
                folderCard.dataset.name = folder.name;
                folderCard.dataset.date = folder.updated_at;
                folderCard.dataset.size = '0';
                folderCard.dataset.mime = '';
                folderCard.dataset.creator = '{{ auth()->user()->name }}';
                folderCard.dataset.created = folder.created_at;
                folderCard.draggable = true;
                folderCard.tabIndex = 0;

                folderCard.innerHTML = `
            <div class="item-grid-view flex flex-col items-center pointer-events-none">
                <svg class="w-16 h-16 text-yellow-500 mb-2" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"></path>
                </svg>
                <p class="text-sm text-center text-gray-800 font-medium truncate w-full" title="${folder.name}">${folder.name}</p>
            </div>
            <div class="item-details-view hidden grid grid-cols-6 gap-4 items-center text-sm pointer-events-none">
                <div class="flex items-center gap-2 col-span-2">
                    <svg class="w-8 h-8 text-yellow-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"></path>
                    </svg>
                    <span class="font-medium truncate">${folder.name}</span>
                </div>
                <div class="text-gray-600">Folder</div>
                <div class="text-gray-600">-</div>
                <div class="text-gray-600">{{ auth()->user()->name }}</div>
                <div class="text-gray-600">${new Date(folder.updated_at).toLocaleString('id-ID')}</div>
            </div>
            <button class="item-menu-btn absolute top-2 right-2 opacity-0 group-hover:opacity-100 focus:opacity-100 p-1.5 rounded-full hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 pointer-events-auto">
                <svg class="w-5 h-5 text-gray-700" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path>
                </svg>
            </button>
        `;

                itemsGrid.insertBefore(folderCard, itemsGrid.firstChild);
                attachItemEvents(folderCard);
            }

            function attachItemEvents(item) {
                item.addEventListener('click', handleItemClick);
                item.addEventListener('contextmenu', showContextMenu);
                item.addEventListener('dblclick', handleDoubleClick);
                item.addEventListener('dragstart', handleDragStart);
                item.addEventListener('dragend', handleDragEnd);
                item.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter') handleDoubleClick.call(item, e);
                    if (e.key === 'ContextMenu') showContextMenu.call(item, e);
                });
                item.querySelector('.item-menu-btn')?.addEventListener('click', (e) => {
                    e.stopPropagation();
                    showContextMenu.call(item, e);
                });

                if (item.dataset.type === 'folder') {
                    item.addEventListener('dragover', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        if (!item.classList.contains('dragging')) {
                            item.style.backgroundColor = '#e0e7ff';
                            item.style.borderColor = '#6366f1';
                        }
                    });

                    item.addEventListener('dragleave', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        item.style.backgroundColor = '';
                        item.style.borderColor = '';
                    });

                    item.addEventListener('drop', async (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        item.style.backgroundColor = '';
                        item.style.borderColor = '';

                        const targetFolderId = item.dataset.id;

                        if (e.dataTransfer.types.includes('text/plain') && e.dataTransfer.getData(
                                'text/plain') === 'internal-drag') {
                            showLoadingToast('Moving items...');

                            for (const selectedItem of selectedItems) {
                                const itemId = selectedItem.dataset.id;
                                const itemType = selectedItem.dataset.type;

                                if (itemId === targetFolderId) continue;

                                try {
                                    const url = itemType === 'folder' ?
                                        `/admin/repository/folder/${itemId}/move` :
                                        `/admin/repository/file/${itemId}/move`;

                                    const response = await fetch(url, {
                                        method: 'PUT',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector(
                                                'meta[name="csrf-token"]').content
                                        },
                                        body: JSON.stringify({
                                            parent_id: targetFolderId
                                        })
                                    });

                                    if (!response.ok) {
                                        const data = await response.json();
                                        Swal.close();
                                        showToast('Error', data.message || 'Move failed', 'error');
                                        continue;
                                    }

                                    const data = await response.json();
                                    if (data.success) {
                                        showToast('Success', 'Items moved successfully', 'success');
                                        selectedItem.remove();
                                    } else {
                                        showToast('Error', data.message || 'Move failed', 'error');
                                    }
                                } catch (error) {
                                    showToast('Error', error.message || 'Move failed', 'error');
                                }
                            }
                            clearSelection();
                        }
                    });
                }
            }

            // Item Selection & Interaction
            document.querySelectorAll('.item-card').forEach(item => {
                attachItemEvents(item);
            });

            function handleItemClick(e) {
                if (e.target.closest('.item-menu-btn')) return;

                e.stopPropagation();

                if (e.ctrlKey || e.metaKey) {
                    toggleSelection(this);
                } else if (e.shiftKey && selectedItems.size > 0) {
                    selectRange(this);
                } else {
                    clearSelection();
                    selectItem(this);
                }
            }

            function selectItem(item) {
                item.classList.add('selected');
                selectedItems.add(item);
            }

            function toggleSelection(item) {
                if (selectedItems.has(item)) {
                    item.classList.remove('selected');
                    selectedItems.delete(item);
                } else {
                    selectItem(item);
                }
            }

            function clearSelection() {
                selectedItems.forEach(item => item.classList.remove('selected'));
                selectedItems.clear();
            }

            function selectRange(endItem) {
                const items = Array.from(document.querySelectorAll('.item-card'));
                const lastSelected = Array.from(selectedItems).pop();
                const startIdx = items.indexOf(lastSelected);
                const endIdx = items.indexOf(endItem);
                const [start, end] = startIdx < endIdx ? [startIdx, endIdx] : [endIdx, startIdx];

                for (let i = start; i <= end; i++) {
                    selectItem(items[i]);
                }
            }

            // Mouse Selection Box Logic
            let tempSelected = new Set();

            itemsContainer.addEventListener('mousedown', (e) => {
                if (e.target === itemsContainer || e.target === itemsGrid || e.target.closest(
                        '#items-grid')) {
                    if (!e.target.closest('.item-card')) {
                        clearSelection();
                        isSelecting = true;
                        tempSelected.clear();

                        selectionStart = {
                            x: e.pageX,
                            y: e.pageY
                        };

                        selectionBox.style.left = e.pageX + 'px';
                        selectionBox.style.top = e.pageY + 'px';
                        selectionBox.style.width = '0';
                        selectionBox.style.height = '0';
                        selectionBox.classList.remove('hidden');

                        e.preventDefault();
                    }
                }
            });

            document.addEventListener('mousemove', (e) => {
                if (isSelecting) {
                    const width = Math.abs(e.pageX - selectionStart.x);
                    const height = Math.abs(e.pageY - selectionStart.y);
                    const left = Math.min(e.pageX, selectionStart.x);
                    const top = Math.min(e.pageY, selectionStart.y);

                    selectionBox.style.width = width + 'px';
                    selectionBox.style.height = height + 'px';
                    selectionBox.style.left = left + 'px';
                    selectionBox.style.top = top + 'px';

                    updateSelectionBox({
                        left,
                        top,
                        width,
                        height
                    });
                }
            });

            document.addEventListener('mouseup', () => {
                if (isSelecting) {
                    isSelecting = false;
                    selectionBox.classList.add('hidden');
                    tempSelected.clear();
                    selectionBox.style.width = '0';
                    selectionBox.style.height = '0';
                }
            });

            function updateSelectionBox(box) {
                const currentIntersecting = new Set();

                document.querySelectorAll('.item-card').forEach(item => {
                    const rect = item.getBoundingClientRect();
                    const itemBox = {
                        left: rect.left + window.scrollX,
                        top: rect.top + window.scrollY,
                        right: rect.right + window.scrollX,
                        bottom: rect.bottom + window.scrollY,
                        width: rect.width,
                        height: rect.height
                    };

                    if (boxesIntersect(box, itemBox)) {
                        currentIntersecting.add(item);
                        if (!tempSelected.has(item)) {
                            selectItem(item);
                            tempSelected.add(item);
                        }
                    }
                });

                tempSelected.forEach(item => {
                    if (!currentIntersecting.has(item)) {
                        item.classList.remove('selected');
                        selectedItems.delete(item);
                        tempSelected.delete(item);
                    }
                });
            }

            function boxesIntersect(box1, box2) {
                const b1 = {
                    left: box1.left,
                    right: box1.left + box1.width,
                    top: box1.top,
                    bottom: box1.top + box1.height
                };

                return !(b1.right < box2.left ||
                    b1.left > box2.right ||
                    b1.bottom < box2.top ||
                    b1.top > box2.bottom);
            }

            // Drag & Drop Items
            function handleDragStart(e) {
                this.classList.add('dragging');
                if (!selectedItems.has(this)) {
                    clearSelection();
                    selectItem(this);
                }
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('text/plain', 'internal-drag');

                dropOverlay.classList.add('drop-hidden');
                dropOverlay.classList.remove('drop-active');
            }

            function handleDragEnd(e) {
                this.classList.remove('dragging');
                document.querySelectorAll('.folder-item').forEach(f => {
                    f.style.backgroundColor = '';
                    f.style.borderColor = '';
                });
            }

            function showContextMenu(e) {
                e.preventDefault();
                contextMenuTarget = this;
                contextMenu.classList.remove('hidden');

                const menuWidth = 180;
                const menuHeight = 200;
                const windowWidth = window.innerWidth;
                const windowHeight = window.innerHeight;

                let left = e.pageX;
                let top = e.pageY;

                if (left + menuWidth > windowWidth) {
                    left = windowWidth - menuWidth - 55;
                }

                if (top + menuHeight > windowHeight + window.scrollY) {
                    top = windowHeight + window.scrollY - menuHeight - 10;
                }

                contextMenu.style.left = left + 'px';
                contextMenu.style.top = top + 'px';
                contextMenu.querySelector('[data-action="open"]').focus();

                const type = this.dataset.type;
                contextMenu.querySelector('[data-action="download"]').style.display = type === 'file' ? 'block' :
                    'none';
            }

            function handleDoubleClick(e) {
                const type = this.dataset.type;
                const id = this.dataset.id;

                if (type === 'folder') {
                    showLoading('Navigating...');
                    window.location.href = `{{ route('admin.repository') }}?folder=${id}`;
                    setTimeout(() => {
                        Swal.close(); // kalau user navigate back, harus close
                    }, 1500);
                }
            }

            document.addEventListener('click', () => contextMenu.classList.add('hidden'));

            document.querySelectorAll('.context-menu-item').forEach(item => {
                item.addEventListener('click', handleContextAction);
            });

            async function handleContextAction(e) {
                const action = this.dataset.action;
                const type = contextMenuTarget.dataset.type;
                const id = contextMenuTarget.dataset.id;
                const name = contextMenuTarget.dataset.name;

                contextMenu.classList.add('hidden');

                if (action === 'open' && type === 'folder') {
                    showLoading('Navigating...');
                    window.location.href = `{{ route('admin.repository') }}?folder=${id}`;
                    setTimeout(() => {
                        Swal.close(); // kalau user navigate back, harus close
                    }, 1500);
                } else if (action === 'rename') {
                    showRenameModal(type, id, name);
                } else if (action === 'download' && type === 'file') {
                    showLoading('Navigating...');
                    window.location.href = `/admin/repository/file/${id}/download`;
                    setTimeout(() => {
                        Swal.close(); // kalau user navigate back, harus close
                    }, 1500);
                } else if (action === 'delete') {
                    Swal.fire({
                        title: 'Delete Confirmation',
                        text: `Are you sure you want to delete "${name}"?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            deleteItem(type, id);
                        }
                    });
                }
            }

            async function renameItem(type, id, name) {
                const url = type === 'folder' ? `/admin/repository/folder/${id}/rename` :
                    `/admin/repository/file/${id}/rename`;

                showLoadingToast('Renaming...');

                try {
                    const response = await fetch(url, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            name
                        })
                    });

                    const data = await response.json();
                    Swal.close();

                    if (!response.ok) {
                        throw new Error(data.message || 'Rename failed');
                    }

                    if (data.success) {
                        showToast('Success', 'Renamed successfully', 'success');
                        const item = document.querySelector(`[data-id="${id}"]`);
                        if (item) {
                            item.dataset.name = name;
                            const nameEl = item.querySelector('.text-sm.text-center, .font-medium.truncate');
                            if (nameEl) {
                                nameEl.textContent = name;
                                nameEl.title = name;
                            }
                        }
                    } else {
                        showToast('Error', data.message || 'Rename failed', 'error');
                    }
                } catch (error) {
                    Swal.close();
                    showToast('Error', error.message, 'error');
                }
            }

            async function deleteItem(type, id) {
                const url = type === 'folder' ? `/admin/repository/folder/${id}` :
                    `/admin/repository/file/${id}`;

                showLoadingToast('Deleting...');

                try {
                    const response = await fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    const data = await response.json();
                    Swal.close();

                    if (!response.ok) {
                        throw new Error(data.message || 'Delete failed');
                    }

                    if (data.success) {
                        showToast('Success', 'Deleted successfully', 'success');
                        const item = document.querySelector(`[data-id="${id}"]`);
                        if (item) {
                            item.remove();
                            selectedItems.delete(item);

                            if (itemsGrid.children.length === 0) {
                                itemsGrid.innerHTML = `
                            <div class="col-span-full text-center py-12 text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                                <p class="mt-2">This folder is empty</p>
                            </div>
                        `;
                            }
                        }
                    } else {
                        showToast('Error', data.message || 'Delete failed', 'error');
                    }
                } catch (error) {
                    Swal.close();
                    showToast('Error', error.message, 'error');
                }
            }

            // New Folder
            document.getElementById('btn-new-folder')?.addEventListener('click', () => {
                folderModal.classList.remove('hidden');
                document.getElementById('folder-name').focus();
            });

            document.getElementById('close-folder-modal')?.addEventListener('click', () => folderModal.classList
                .add('hidden'));
            document.getElementById('cancel-folder')?.addEventListener('click', () => folderModal
                .classList.add(
                    'hidden'));

            folderModal?.addEventListener('click', (e) => {
                if (e.target === folderModal) {
                    folderModal.classList.add('hidden');
                }
            });

            // Rename Modal
            const renameModal = document.createElement('div');
            renameModal.id = 'rename-modal';
            renameModal.className =
                'hidden fixed inset-0 bg-gray-900 bg-opacity-75 z-[3000] flex items-center justify-center p-4';
            renameModal
                .innerHTML = `
                <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
                    <div class="flex justify-between items-center p-4 border-b">
                        <h3 class="text-lg font-semibold">Rename</h3>
                        <button id="close-rename-modal" class="text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    <form id="rename-form" class="p-6">
                        <label for="rename-input" class="block text-sm font-semibold text-gray-700 mb-2">New Name</label>
                        <input type="text" id="rename-input" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                        <div class="flex justify-end gap-3 mt-6">
                            <button type="button" id="cancel-rename" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">Rename</button>
                        </div>
                    </form>
                </div>
            `;
            document.body.appendChild(renameModal);

            let renameTargetType, renameTargetId;

            function showRenameModal(type, id, currentName) {
                renameTargetType = type;
                renameTargetId = id;
                document.getElementById('rename-input').value = currentName;
                renameModal.classList.remove('hidden');
                document.getElementById('rename-input').focus();
                document.getElementById('rename-input').select();
            }

            document.getElementById('close-rename-modal')?.addEventListener('click', () => renameModal.classList
                .add(
                    'hidden'));
            document.getElementById('cancel-rename')?.addEventListener('click', () => renameModal
                .classList.add('hidden'));

            renameModal.addEventListener('click', (e) => {
                if (e.target === renameModal) {
                    renameModal.classList.add('hidden');
                }
            });

            document.getElementById('rename-form').addEventListener('submit', async (e) => {
                e.preventDefault();
                const newName = document.getElementById('rename-input').value;
                renameModal.classList.add('hidden');
                await renameItem(renameTargetType, renameTargetId, newName);
            });

            document.getElementById('folder-form').addEventListener('submit', async (e) => {
                e.preventDefault();
                const name = document.getElementById('folder-name').value;

                showLoadingToast('Creating folder...');

                try {
                    const response = await fetch('{{ route('admin.repository.folder.create') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .content
                        },
                        body: JSON.stringify({
                            name,
                            parent_id: currentFolderId
                        })
                    });

                    const data = await response.json();
                    Swal.close();

                    if (!response.ok) {
                        throw new Error(data.message || 'Create folder failed');
                    }

                    if (data.success) {
                        showToast('Success', 'Folder created', 'success');
                        folderModal.classList.add('hidden');
                        document.getElementById('folder-name').value = '';
                        addFolderToGrid(data.folder);
                    } else {
                        showToast('Error', data.message || 'Create folder failed', 'error');
                    }
                } catch (error) {
                    Swal.close();
                    showToast('Error', error.message, 'error');
                }
            });

            // Toolbar Menus
            function setupToolbarMenus() {
                const menus = {
                    'btn-new': 'new-menu',
                    'btn-view': 'view-menu',
                    'btn-sort': 'sort-menu'
                };

                Object.entries(menus).forEach(([btnId, menuId]) => {
                    const btn = document.getElementById(btnId);
                    const menu = document.getElementById(menuId);

                    btn?.addEventListener('click', (e) => {
                        e.stopPropagation();
                        Object.values(menus).forEach(m => {
                            if (m !== menuId) document.getElementById(m)?.classList.add(
                                'hidden');
                        });
                        menu.classList.toggle('hidden');
                    });
                });

                document.addEventListener('click', (e) => {
                    if (!e.target.closest('#btn-new') && !e.target.closest('#btn-view') && !e.target
                        .closest('#btn-sort')) {
                        Object.values(menus).forEach(m => document.getElementById(m)?.classList.add(
                            'hidden'));
                    }
                });

                itemsContainer.addEventListener('click', () => {
                    Object.values(menus).forEach(m => document.getElementById(m)?.classList.add('hidden'));
                });

                // New Menu Actions
                document.querySelector('[data-action="new-folder"]')?.addEventListener('click', () => {
                    folderModal.classList.remove('hidden');
                    document.getElementById('folder-name').focus();
                });

                document.querySelector('[data-action="upload-file"]')?.addEventListener('click', () => {
                    fileInput.removeAttribute('webkitdirectory');
                    fileInput.removeAttribute('directory');
                    fileInput.setAttribute('multiple', '');
                    fileInput.value = '';
                    fileInput.click();
                });

                document.querySelector('[data-action="upload-folder"]')?.addEventListener('click', () => {
                    folderInput.value = '';
                    folderInput.click();
                });

                // View Menu Actions
                const detailsHeader = document.getElementById('details-header');
                let currentView = 'grid-medium';

                document.querySelectorAll('[data-view]').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const view = btn.dataset.view;
                        currentView = view;

                        // Update checkmarks
                        document.querySelectorAll('[data-view] .view-check').forEach(c => c
                            .classList.add('hidden'));
                        btn.querySelector('.view-check')?.classList.remove('hidden');

                        itemsGrid.className = 'grid gap-4';
                        itemsGrid.dataset.view = view;
                        detailsHeader.classList.add('hidden');

                        document.querySelectorAll('.item-grid-view').forEach(el => el.classList
                            .remove('hidden'));
                        document.querySelectorAll('.item-details-view').forEach(el => el.classList
                            .add('hidden'));
                        document.querySelectorAll('.item-card').forEach(card => {
                            card.classList.remove('p-2');
                            card.classList.add('p-4');
                        });

                        if (view === 'grid-large') {
                            itemsGrid.classList.add('grid-cols-2', 'sm:grid-cols-3',
                                'md:grid-cols-4');
                        } else if (view === 'grid-medium') {
                            itemsGrid.classList.add('grid-cols-2', 'sm:grid-cols-3',
                                'md:grid-cols-4', 'lg:grid-cols-6');
                        } else if (view === 'list') {
                            itemsGrid.classList.add('grid-cols-1');
                        } else if (view === 'details') {
                            itemsGrid.classList.add('grid-cols-1');
                            detailsHeader.classList.remove('hidden');
                            document.querySelectorAll('.item-grid-view').forEach(el => el.classList
                                .add('hidden'));
                            document.querySelectorAll('.item-details-view').forEach(el => el
                                .classList.remove('hidden'));
                            document.querySelectorAll('.item-card').forEach(card => {
                                card.classList.remove('p-4');
                                card.classList.add('p-2');
                            });
                        }
                    });
                });

                // Sort Menu Actions
                let currentSort = 'name';
                document.querySelectorAll('[data-sort]').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const sortBy = btn.dataset.sort;
                        currentSort = sortBy;

                        // Update checkmarks
                        document.querySelectorAll('[data-sort] .sort-check').forEach(c => c
                            .classList.add('hidden'));
                        btn.querySelector('.sort-check')?.classList.remove('hidden');

                        sortItems(sortBy);
                    });
                });

                // Set default checkmarks
                document.querySelector('[data-view="grid-medium"] .view-check')?.classList.remove('hidden');
                document.querySelector('[data-sort="name"] .sort-check')?.classList.remove('hidden');
            }

            function sortItems(by) {
                const items = Array.from(document.querySelectorAll('.item-card'));
                items.sort((a, b) => {
                    if (by === 'name') {
                        return a.dataset.name.localeCompare(b.dataset.name);
                    } else if (by === 'date') {
                        return new Date(b.dataset.date) - new Date(a.dataset.date);
                    } else if (by === 'size') {
                        return parseInt(b.dataset.size) - parseInt(a.dataset.size);
                    } else if (by === 'type') {
                        return a.dataset.type.localeCompare(b.dataset.type);
                    }
                });

                items.forEach(item => itemsGrid.appendChild(item));
            }

            // Breadcrumb Navigation Loading & Drop
            document.querySelectorAll('.breadcrumb-link').forEach(link => {
                link.addEventListener('click', (e) => {
                    showLoadingToast('Navigating...');
                });

                const folderId = new URL(link.href).searchParams.get('folder');
                if (folderId) {
                    link.addEventListener('dragover', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        link.style.backgroundColor = '#e0e7ff';
                        link.style.borderRadius = '0.375rem';
                    });

                    link.addEventListener('dragleave', (e) => {
                        e.preventDefault();
                        link.style.backgroundColor = '';
                    });

                    link.addEventListener('drop', async (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        link.style.backgroundColor = '';

                        if (e.dataTransfer.types.includes('text/plain') && e.dataTransfer
                            .getData('text/plain') === 'internal-drag') {
                            showLoadingToast('Moving items...');

                            for (const selectedItem of selectedItems) {
                                const itemId = selectedItem.dataset.id;
                                const itemType = selectedItem.dataset.type;

                                try {
                                    const url = itemType === 'folder' ?
                                        `/admin/repository/folder/${itemId}/move` :
                                        `/admin/repository/file/${itemId}/move`;

                                    const response = await fetch(url, {
                                        method: 'PUT',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector(
                                                'meta[name="csrf-token"]').content
                                        },
                                        body: JSON.stringify({
                                            parent_id: folderId
                                        })
                                    });

                                    if (!response.ok) {
                                        const data = await response.json();
                                        Swal.close();
                                        showToast('Error', data.message || 'Move failed',
                                            'error');
                                        continue;
                                    }

                                    const data = await response.json();
                                    if (data.success) {
                                        showToast('Success', 'Items moved successfully',
                                            'success');
                                        selectedItem.remove();
                                    } else {
                                        showToast('Error', data.message || 'Move failed',
                                            'error');
                                    }
                                } catch (error) {
                                    showToast('Error', error.message || 'Move failed',
                                        'error');
                                }
                            }
                            clearSelection();
                        }
                    });
                }
            });

            // Keyboard Navigation
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    contextMenu.classList.add('hidden');
                    folderModal.classList.add('hidden');
                    document.getElementById('rename-modal')?.classList.add('hidden');
                    clearSelection();
                }
                if (e.key === 'a' && (e.ctrlKey || e.metaKey)) {
                    e.preventDefault();
                    document.querySelectorAll('.item-card').forEach(selectItem);
                }
                if (e.key === 'Delete' && selectedItems.size > 0) {
                    Swal.fire({
                        title: 'Delete Confirmation',
                        text: `Are you sure you want to delete ${selectedItems.size} item(s)?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Yes, delete them!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            selectedItems.forEach(item => {
                                deleteItem(item.dataset.type, item.dataset.id);
                            });
                        }
                    });
                }
            });
        });
    </script>
@endsection
