@extends('layouts.admin')

@section('title', 'Roles & Unit Assignment')

@section('style')
    <style>
        /* --- CUSTOM SCROLLBAR --- */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 20px;
        }

        /* --- TOGGLE SWITCH --- */
        .toggle-checkbox {
            position: absolute;
            left: 0;
            right: auto;
            border-color: #d1d5db;
        }

        .toggle-checkbox:checked {
            right: 0;
            left: auto;
            border-color: #4f46e5;
        }

        .toggle-checkbox:checked+.toggle-label {
            background-color: #4f46e5;
        }

        .toggle-checkbox:disabled {
            cursor: not-allowed;
            opacity: 0.6;
        }

        /* --- ANIMATIONS --- */
        .fade-in {
            animation: fadeIn 0.3s ease-in-out forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* List Item Active State */
        .list-item.active {
            background-color: #EEF2FF;
            border-left: 4px solid #6366f1;
        }

        /* Permission Tag Base */
        .perm-tag {
            font-size: 10px;
            padding: 3px 8px;
            border-radius: 999px;
            background: #f3f4f6;
            color: #4b5563;
            border: 1px solid #e5e7eb;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: all 0.2s;
            cursor: help;
        }

        .perm-tag:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        /* --- TAG SOURCES COLORS --- */

        /* Direct Only (Purple) */
        .perm-tag.direct {
            background: #faf5ff;
            color: #6b21a8;
            border-color: #e9d5ff;
        }

        /* Inherited Only (Blue) */
        .perm-tag.inherited {
            background: #eff6ff;
            color: #1e40af;
            border-color: #dbeafe;
        }

        /* Combined / Both */
        .perm-tag.combined {
            background: #e8e2b2;
            color: #7d711a;
            border-color: #dfce50;
            font-weight: 600;
        }

        /* Unit Base View (Emerald) */
        .perm-tag.unit-base {
            background: #ecfdf5;
            color: #047857;
            border-color: #d1fae5;
        }

        /* Accordion Transition */
        .accordion-content {
            transition: max-height 0.3s ease-in-out, opacity 0.3s ease-in-out;
        }

        .accordion-icon {
            transition: transform 0.3s ease;
        }

        .group-header[aria-expanded="true"] .accordion-icon {
            transform: rotate(180deg);
        }

        /* --- GLOBAL TOOLTIP STYLE --- */
        #global-tooltip {
            position: fixed;
            z-index: 9999;
            pointer-events: none;
            opacity: 0;
            transform: scale(0.95);
            transition: opacity 0.15s ease-out, transform 0.15s ease-out;
            background: #1f2937;
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 500;
            white-space: nowrap;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            max-width: 300px;
            text-overflow: ellipsis;
            overflow: hidden;
        }

        #global-tooltip.visible {
            opacity: 1;
            transform: scale(1);
        }

        /* Triangle indicator for global tooltip */
        #global-tooltip::after {
            content: '';
            position: absolute;
            top: 100%;
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: #1f2937 transparent transparent transparent;
        }
    </style>
@endsection

@section('body')
    <div class="flex flex-col h-full min-h-[calc(100vh-100px)]">

        {{-- Header & Swap Control --}}
        <div class="flex flex-col md:flex-row w-full py-4 shadow-md items-center justify-between mb-5 px-6 md:px-4">
            <h1 class="text-center md:text-left text-4xl uppercase font-bold mb-2 md:mb-0">Roles Assignment</h1>

            <div class="flex flex-col md:flex-row gap-2">
                {{-- Create Unit Button --}}
                <button onclick="openCreateUnitModal()"
                    class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 flex items-center gap-2 transition-all shadow-sm justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Create Unit</span>
                </button>

                {{-- Swap Button --}}
                <button id="swap-mode-btn"
                    class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 flex items-center gap-2 transition-all shadow-sm justify-center">
                    <svg id="swap-icon" class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                    </svg>
                    <span id="mode-text-display">Switch Mode</span>
                </button>
            </div>
        </div>

        {{-- Main Two-Column Layout --}}
        <div class="flex flex-col lg:flex-row gap-6 h-auto lg:h-[calc(100vh-180px)] relative">

            {{-- LEFT PANEL (List Selection) --}}
            <div id="left-panel"
                class="w-full lg:w-4/12 flex flex-col bg-white border border-gray-200 rounded-xl shadow-sm h-[500px] lg:h-full z-10">
                <div class="p-4 border-b border-gray-100 bg-gray-50 rounded-t-xl flex flex-col gap-3">
                    <div class="flex justify-between items-center">
                        <h2 id="left-title" class="text-lg font-bold text-gray-800">Select Unit</h2>
                        <span id="left-count"
                            class="bg-gray-200 text-gray-600 text-xs font-bold px-2 py-0.5 rounded-full">0</span>
                    </div>
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" id="left-search"
                            class="block w-full p-2.5 pl-10 text-sm border border-gray-300 rounded-lg bg-white focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                            placeholder="Search...">
                    </div>
                </div>
                <div id="left-list" class="flex-1 overflow-y-auto custom-scrollbar p-2 space-y-1">
                    {{-- JS renders items here --}}
                </div>
            </div>

            {{-- RIGHT PANEL (Toggle List) --}}
            <div id="right-panel"
                class="w-full lg:w-8/12 flex flex-col bg-white border border-gray-200 rounded-xl shadow-sm h-[500px] lg:h-full relative overflow-hidden">

                {{-- Overlay for No Selection --}}
                <div id="right-overlay"
                    class="absolute inset-0 bg-gray-50/95 backdrop-blur-sm z-20 rounded-xl flex flex-col items-center justify-center text-center p-6 transition-opacity duration-300">
                    <div
                        class="w-20 h-20 bg-white rounded-full shadow-sm flex items-center justify-center mb-4 animate-pulse border border-gray-100">
                        <svg class="w-10 h-10 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122">
                            </path>
                        </svg>
                    </div>
                    <h3 id="overlay-text" class="text-xl font-bold text-gray-800">Select an item from the left</h3>
                    <p class="text-gray-500 mt-1">to manage role assignments</p>
                </div>

                <div
                    class="p-4 border-b border-gray-100 bg-gray-50 rounded-t-xl flex flex-col md:flex-row justify-between items-end md:items-center gap-3">
                    <div class="flex-1 w-full md:w-auto">
                        <div class="flex items-center gap-2 mb-1">
                            <h2 id="right-title" class="text-lg font-bold text-gray-800">Assign Users</h2>
                            <span id="selection-badge"
                                class="hidden px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide bg-indigo-100 text-indigo-700 truncate max-w-[150px]"></span>
                        </div>
                        <p id="right-subtitle" class="text-xs text-gray-500">Toggle to assign/unassign.</p>
                    </div>
                    <div class="w-full md:w-64 relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" id="right-search"
                            class="block w-full p-2 pl-9 text-xs border border-gray-300 rounded-lg bg-white focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="Filter list...">
                    </div>
                </div>

                <div id="right-list"
                    class="flex-1 overflow-y-auto custom-scrollbar p-4 grid grid-cols-1 xl:grid-cols-2 gap-3 content-start">
                    {{-- JS renders toggles here --}}
                </div>
            </div>
        </div>
    </div>

    {{-- Permissions Modal --}}
    <div id="permissions-modal" class="fixed inset-0 z-[2000] hidden" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-60 transition-opacity backdrop-blur-sm" onclick="closePermModal()">
        </div>
        <div class="fixed inset-0 z-10 overflow-y-auto pointer-events-none">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div
                    class="pointer-events-auto relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-gray-200 flex flex-col max-h-[90vh]">

                    {{-- Modal Header --}}
                    <div class="bg-white px-4 py-4 sm:px-6 border-b border-gray-100 shrink-0">
                        <div class="flex justify-between items-start">
                            <div class="flex items-center gap-3">
                                <div class="bg-indigo-100 p-2 rounded-lg">
                                    <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold leading-6 text-gray-900" id="modal-title">Permissions
                                        Detail</h3>
                                    <p class="text-xs text-gray-500">Access rights breakdown</p>
                                </div>
                            </div>
                            <button type="button" onclick="closePermModal()"
                                class="text-gray-400 hover:text-gray-500 transition-colors">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        {{-- Role & Count Info Block --}}
                        <div
                            class="mt-4 p-3 bg-indigo-50 border border-indigo-100 rounded-lg flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                            <div class="flex flex-col">
                                <span class="text-[10px] uppercase tracking-wider text-indigo-500 font-bold">Assigned Role
                                    (Unit)</span>
                                <span id="modal-role-name" class="font-bold text-gray-800 text-sm">Loading...</span>
                            </div>
                            <div
                                class="flex items-center gap-2 bg-white px-3 py-1.5 rounded-md border border-indigo-100 shadow-sm">
                                <span class="text-xs text-gray-500 font-medium">Unique Permissions:</span>
                                <span id="modal-perm-count" class="text-sm font-bold text-indigo-600">0</span>
                            </div>
                        </div>

                        {{-- Filter & Search Bar --}}
                        <div class="mt-4 flex gap-3">
                            <div class="relative flex-1">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <input type="text" id="perm-search-input"
                                    class="block w-full p-2 pl-9 text-xs border border-gray-300 rounded-lg bg-white focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="Search permissions...">
                            </div>
                            <div class="w-1/3 min-w-[120px]">
                                <select id="perm-filter-action"
                                    class="block w-full p-2 text-xs border border-gray-300 rounded-lg bg-white focus:ring-indigo-500 focus:border-indigo-500 text-gray-600">
                                    <option value="ALL">All Actions</option>
                                    {{-- Options injected by JS --}}
                                </select>
                            </div>
                        </div>

                        {{-- LEGEND COLOR (Hidden by default, shown for Users) --}}
                        <div id="modal-legend"
                            class="hidden mt-3 pt-2 border-t border-gray-50 flex flex-wrap items-center gap-4 text-[10px] font-medium text-gray-500">
                            <span class="uppercase tracking-wider opacity-70">Source:</span>
                            <div class="flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-[#faf5ff] border border-[#e9d5ff]"></span>
                                <span class="text-[#6b21a8]">Direct (User)</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-[#eff6ff] border border-[#dbeafe]"></span>
                                <span class="text-[#1e40af]">Inherited (Unit)</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-[#dfce50] border border-[#e8e2b2]"></span>
                                <span class="text-[#b6a453] font-bold">Combined (Both)</span>
                            </div>
                        </div>

                    </div>

                    {{-- Modal Body --}}
                    <div class="bg-white px-4 py-5 sm:p-6 overflow-y-auto custom-scrollbar flex-1">
                        <div id="modal-content" class="space-y-3">
                            {{-- Content Injected Here --}}
                        </div>
                    </div>

                    {{-- Modal Footer --}}
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-gray-100 shrink-0">
                        <button type="button" onclick="closePermModal()"
                            class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Create Unit Modal --}}
    <div id="create-unit-modal" class="fixed inset-0 z-[2000] hidden" aria-labelledby="create-unit-title" role="dialog"
        aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-60 transition-opacity backdrop-blur-sm"
            onclick="closeCreateUnitModal()"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto pointer-events-none">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div
                    class="pointer-events-auto relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-200">
                    <form id="create-unit-form" onsubmit="submitCreateUnit(event)">
                        <div class="bg-white px-4 py-4 sm:px-6 border-b border-gray-100">
                            <div class="flex justify-between items-start">
                                <div class="flex items-center gap-3">
                                    <div class="bg-emerald-100 p-2 rounded-lg">
                                        <svg class="h-6 w-6 text-emerald-600" fill="none" viewBox="0 0 24 24"
                                            stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                            </path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold leading-6 text-gray-900" id="create-unit-title">Create
                                            New Unit</h3>
                                        <p class="text-xs text-gray-500">Add a new organizational unit</p>
                                    </div>
                                </div>
                                <button type="button" onclick="closeCreateUnitModal()"
                                    class="text-gray-400 hover:text-gray-500 transition-colors">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="bg-white px-4 py-5 sm:p-6">
                            <div class="space-y-4">
                                <div>
                                    <label for="unit-name" class="block text-sm font-semibold text-gray-700 mb-2">Unit Name
                                        <span class="text-red-500">*</span></label>
                                    <input type="text" id="unit-name" name="name" required maxlength="255"
                                        class="block w-full px-4 py-2.5 text-sm border border-gray-300 rounded-lg bg-white focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                                        placeholder="e.g., IT Department">
                                </div>
                                <div>
                                    <label for="unit-description"
                                        class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                                    <textarea id="unit-description" name="description" rows="3" maxlength="500"
                                        class="block w-full px-4 py-2.5 text-sm border border-gray-300 rounded-lg bg-white focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                                        placeholder="Brief description of the unit"></textarea>
                                </div>
                            </div>
                        </div>

                        <div
                            class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-gray-100 gap-3">
                            <button type="submit"
                                class="inline-flex w-full justify-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 sm:w-auto transition-colors">
                                Create Unit
                            </button>
                            <button type="button" onclick="closeCreateUnitModal()"
                                class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- GLOBAL TOOLTIP ELEMENT --}}
    <div id="global-tooltip"></div>
@endsection

@section('script')
    <script>
        // --- DATA INITIALIZATION ---
        let usersData = @json($users);
        let unitsData = @json($units);

        // --- ACTION COLORS MAP ---
        const actionColors = {
            'view': 'text-blue-700 bg-blue-50 border-blue-200',
            'show': 'text-blue-700 bg-blue-50 border-blue-200',
            'list': 'text-blue-700 bg-blue-50 border-blue-200',
            'get': 'text-blue-700 bg-blue-50 border-blue-200',
            'create': 'text-emerald-700 bg-emerald-50 border-emerald-200',
            'store': 'text-emerald-700 bg-emerald-50 border-emerald-200',
            'add': 'text-emerald-700 bg-emerald-50 border-emerald-200',
            'post': 'text-emerald-700 bg-emerald-50 border-emerald-200',
            'put': 'text-orange-700 bg-orange-50 border-orange-200',
            'update': 'text-orange-700 bg-orange-50 border-orange-200',
            'patch': 'text-amber-700 bg-amber-50 border-amber-200',
            'edit': 'text-amber-700 bg-amber-50 border-amber-200',
            'delete': 'text-rose-700 bg-rose-50 border-rose-200',
            'destroy': 'text-rose-700 bg-rose-50 border-rose-200',
            'remove': 'text-rose-700 bg-rose-50 border-rose-200',
            'approve': 'text-indigo-700 bg-indigo-50 border-indigo-200',
            'reject': 'text-pink-700 bg-pink-50 border-pink-200',
            'export': 'text-teal-700 bg-teal-50 border-teal-200',
            'import': 'text-cyan-700 bg-cyan-50 border-cyan-200',
            'default': 'text-gray-600 bg-gray-100 border-gray-200'
        };

        // --- STATE MANAGEMENT ---
        const state = {
            mode: 'UNIT_TO_USER',
            selectedId: null,
            searchLeft: '',
            searchRight: '',
            // Modal States
            currentPerms: [],
            permSearch: '',
            permActionFilter: 'ALL'
        };

        // --- DOM ELEMENTS ---
        const els = {
            leftList: document.getElementById('left-list'),
            rightList: document.getElementById('right-list'),
            leftTitle: document.getElementById('left-title'),
            rightTitle: document.getElementById('right-title'),
            rightSubtitle: document.getElementById('right-subtitle'),
            leftCount: document.getElementById('left-count'),
            rightOverlay: document.getElementById('right-overlay'),
            overlayText: document.getElementById('overlay-text'),
            selectionBadge: document.getElementById('selection-badge'),
            swapBtn: document.getElementById('swap-mode-btn'),
            swapIcon: document.getElementById('swap-icon'),
            modeTextLeft: document.getElementById('mode-text-left'),
            modeTextRight: document.getElementById('mode-text-right'),
            // Modal Elements
            permissionsModal: document.getElementById('permissions-modal'),
            modalTitle: document.getElementById('modal-title'),
            modalRoleName: document.getElementById('modal-role-name'),
            modalPermCount: document.getElementById('modal-perm-count'),
            modalContent: document.getElementById('modal-content'),
            modalLegend: document.getElementById('modal-legend'),
            permSearchInput: document.getElementById('perm-search-input'),
            permActionFilter: document.getElementById('perm-filter-action'),
            // Tooltip
            globalTooltip: document.getElementById('global-tooltip')
        };

        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('roles').classList.add('bg-slate-100');
            document.getElementById('roles').classList.add('active');

            renderLeftPanel();

            // Main Lists Listeners
            document.getElementById('left-search').addEventListener('input', (e) => {
                state.searchLeft = e.target.value.toLowerCase();
                renderLeftPanel();
            });
            document.getElementById('right-search').addEventListener('input', (e) => {
                state.searchRight = e.target.value.toLowerCase();
                renderRightPanel();
            });
            els.swapBtn.addEventListener('click', swapMode);

            // Modal Listeners
            els.permSearchInput.addEventListener('input', (e) => {
                state.permSearch = e.target.value.toLowerCase();
                renderPermissionList();
            });
            els.permActionFilter.addEventListener('change', (e) => {
                state.permActionFilter = e.target.value;
                renderPermissionList();
            });

            // --- GLOBAL TOOLTIP LISTENERS ---
            document.addEventListener('mouseover', (e) => {
                const target = e.target.closest('[data-tooltip]');
                if (target) {
                    const text = target.getAttribute('data-tooltip');
                    if (text) showTooltip(target, text);
                }
            });
            document.addEventListener('mouseout', (e) => {
                const target = e.target.closest('[data-tooltip]');
                if (target) hideTooltip();
            });
        });

        // --- TOOLTIP LOGIC ---
        function showTooltip(target, text) {
            els.globalTooltip.innerText = text;
            els.globalTooltip.classList.add('visible');

            const rect = target.getBoundingClientRect();
            const tooltipRect = els.globalTooltip.getBoundingClientRect();

            let top = rect.top - tooltipRect.height - 8;
            let left = rect.left + (rect.width / 2) - (tooltipRect.width / 2);

            if (top < 0) top = rect.bottom + 8;
            if (left < 0) left = 10;
            if (left + tooltipRect.width > window.innerWidth) left = window.innerWidth - tooltipRect.width - 10;

            els.globalTooltip.style.top = `${top}px`;
            els.globalTooltip.style.left = `${left}px`;
        }

        function hideTooltip() {
            els.globalTooltip.classList.remove('visible');
        }

        // --- SWAP MODE LOGIC ---
        function swapMode() {
            state.selectedId = null;
            state.searchLeft = '';
            state.searchRight = '';
            document.getElementById('left-search').value = '';
            document.getElementById('right-search').value = '';
            els.rightOverlay.classList.remove('hidden');

            state.mode = (state.mode === 'UNIT_TO_USER') ? 'USER_TO_UNIT' : 'UNIT_TO_USER';

            if (state.mode === 'UNIT_TO_USER') {
                els.modeTextLeft.classList.add('font-bold', 'text-indigo-600');
                els.modeTextLeft.classList.remove('text-gray-400', 'font-medium');
                els.modeTextRight.classList.add('text-gray-400', 'font-medium');
                els.modeTextRight.classList.remove('font-bold', 'text-indigo-600');
                els.swapIcon.classList.remove('rotate-180');
                els.leftTitle.innerText = 'Select Unit';
                els.rightTitle.innerText = 'Assign Users';
                els.rightSubtitle.innerText = 'Select users to add to this Unit.';
                els.overlayText.innerText = 'Select a Unit';
            } else {
                els.modeTextRight.classList.add('font-bold', 'text-indigo-600');
                els.modeTextRight.classList.remove('text-gray-400', 'font-medium');
                els.modeTextLeft.classList.add('text-gray-400', 'font-medium');
                els.modeTextLeft.classList.remove('font-bold', 'text-indigo-600');
                els.swapIcon.classList.add('rotate-180');
                els.leftTitle.innerText = 'Select User';
                els.rightTitle.innerText = 'Assign Unit';
                els.rightSubtitle.innerText = 'User can only have ONE Unit.';
                els.overlayText.innerText = 'Select a User';
            }
            renderLeftPanel();
        }

        // --- RENDER LEFT PANEL ---
        function renderLeftPanel() {
            els.leftList.innerHTML = '';
            let data = (state.mode === 'UNIT_TO_USER') ? unitsData : usersData;

            const filtered = data.filter(item => {
                const term = state.searchLeft;
                if (!term) return true;
                return item.name.toLowerCase().includes(term) || (item.email && item.email.toLowerCase().includes(
                    term));
            });

            els.leftCount.innerText = filtered.length;

            if (filtered.length === 0) {
                els.leftList.innerHTML =
                    `<div class="p-8 text-center text-gray-400 text-sm flex flex-col items-center"><svg class="w-8 h-8 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>No items found.</div>`;
                return;
            }

            filtered.forEach(item => {
                const isActive = state.selectedId === item.id;
                const div = document.createElement('div');
                div.className =
                    `list-item group flex flex-col sm:flex-row items-start sm:items-center p-3 rounded-lg border border-transparent cursor-pointer transition-all duration-200 hover:bg-gray-50 ${isActive ? 'active' : ''} gap-2 sm:gap-0`;
                div.onclick = () => selectLeftItem(item.id);

                let iconHtml = '';
                let subText = '';
                let badgeHtml = '';

                if (state.mode === 'UNIT_TO_USER') {
                    // Item is Unit
                    iconHtml =
                        `<div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600 shrink-0 mr-3 shadow-sm"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg></div>`;
                    subText = item.description || 'Unit Organization';
                } else {
                    // Item is User
                    iconHtml =
                        `<div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-sm shrink-0 mr-3 shadow-sm border border-indigo-200">${item.name.charAt(0)}</div>`;
                    subText = item.email || '';
                    const unitName = item.unit ? item.unit.name : 'No Unit';
                    const badgeColor = item.unit ? 'bg-indigo-50 text-indigo-600 border-indigo-100' :
                        'bg-gray-100 text-gray-500 border-gray-200';
                    badgeHtml =
                        `<span class="text-[10px] px-2 py-0.5 rounded-md border ${badgeColor} font-semibold shrink-0">${unitName}</span>`;
                }

                div.innerHTML = `
                        <div class="flex items-center w-full min-w-0">
                            ${iconHtml}
                            <div class="flex-1 min-w-0 mr-2">
                                <p class="text-sm font-semibold text-gray-800 truncate group-hover:text-indigo-700 transition-colors" data-tooltip="${item.name}">${item.name}</p>
                                <p class="text-xs text-gray-500 truncate" data-tooltip="${subText}">${subText}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 w-full sm:w-auto justify-between sm:justify-end mt-1 sm:mt-0 ml-auto pl-[3.25rem] sm:pl-0">
                            ${badgeHtml}
                            <button onclick="event.stopPropagation(); showPermissions('${item.id}', '${state.mode === 'UNIT_TO_USER' ? 'UNIT' : 'USER'}')" class="flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-gray-100 text-gray-600 border border-gray-200 hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-200 transition-all text-xs font-medium shrink-0 shadow-sm" data-tooltip="View Permission Details">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"></path></svg>
                                <span>Perms</span>
                            </button>
                        </div>
                    `;
                els.leftList.appendChild(div);
            });
        }

        // --- SELECTION LOGIC ---
        function selectLeftItem(id) {
            state.selectedId = id;
            renderLeftPanel();
            els.rightOverlay.classList.add('hidden');

            const data = (state.mode === 'UNIT_TO_USER') ? unitsData : usersData;
            const item = data.find(u => u.id === id);

            els.selectionBadge.innerText = item.name;
            els.selectionBadge.classList.remove('hidden');
            renderRightPanel();
        }

        // --- RENDER RIGHT PANEL ---
        function renderRightPanel() {
            els.rightList.innerHTML = '';
            if (!state.selectedId) return;

            let itemsToRender = (state.mode === 'UNIT_TO_USER') ? usersData : unitsData;

            const filtered = itemsToRender.filter(item => {
                const term = state.searchRight;
                if (!term) return true;
                return item.name.toLowerCase().includes(term);
            });

            if (filtered.length === 0) {
                els.rightList.innerHTML =
                    `<div class="col-span-full p-8 text-center text-gray-400 flex flex-col items-center"><svg class="w-8 h-8 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>No matches found.</div>`;
                return;
            }

            filtered.forEach(targetItem => {
                let isChecked = false;
                if (state.mode === 'UNIT_TO_USER') {
                    isChecked = targetItem.unit_id === state.selectedId;
                } else {
                    const selectedUser = usersData.find(u => u.id === state.selectedId);
                    isChecked = selectedUser.unit_id === targetItem.id;
                }

                const card = document.createElement('div');
                card.className =
                    `flex flex-col sm:flex-row sm:items-center justify-between p-3 rounded-xl border ${isChecked ? 'bg-indigo-50 border-indigo-200' : 'bg-white border-gray-200'} hover:shadow-sm transition-all fade-in gap-3`;

                let iconHtml = '';
                if (state.mode === 'UNIT_TO_USER') {
                    // Right is Users
                    iconHtml =
                        `<div class="w-9 h-9 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 font-bold text-xs shrink-0 border border-gray-200">${targetItem.name.charAt(0)}</div>`;
                } else {
                    // Right is Units
                    iconHtml =
                        `<div class="w-9 h-9 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600 shrink-0 border border-emerald-100"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg></div>`;
                }

                card.innerHTML = `
                        <div class="flex items-center flex-1 min-w-0 gap-3">
                            ${iconHtml}
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-800 truncate" data-tooltip="${targetItem.name}">${targetItem.name}</p>
                                ${state.mode === 'UNIT_TO_USER' ? `<p class="text-[11px] text-gray-500 truncate" data-tooltip="${targetItem.email}">${targetItem.email}</p>` : ''}
                            </div>
                        </div>

                        <div class="flex items-center justify-between sm:justify-end gap-3 w-full sm:w-auto border-t sm:border-t-0 border-gray-100 pt-2 sm:pt-0">
                            <button onclick="showPermissions('${targetItem.id}', '${state.mode === 'UNIT_TO_USER' ? 'USER' : 'UNIT'}')" 
                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-white border border-gray-200 text-gray-600 hover:text-indigo-600 hover:border-indigo-300 shadow-sm transition-all text-xs font-medium"
                                data-tooltip="View Assigned Permissions">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"></path></svg>
                                View
                            </button>

                            <div class="relative inline-block w-10 align-middle select-none h-5 shrink-0">
                                <input type="checkbox" id="toggle-${targetItem.id}" 
                                    class="toggle-checkbox block w-5 h-5 rounded-full bg-white border-4 appearance-none cursor-pointer transition-all duration-300 top-0" 
                                    ${isChecked ? 'checked' : ''}
                                    onchange="handleToggle('${targetItem.id}', this)"
                                />
                                <label for="toggle-${targetItem.id}" class="toggle-label block overflow-hidden h-5 rounded-full bg-gray-300 cursor-pointer transition-colors duration-300"></label>
                            </div>
                        </div>
                    `;
                els.rightList.appendChild(card);
            });
        }

        // --- HANDLE TOGGLE (AJAX) ---
        async function handleToggle(targetId, checkbox) {
            let userId, unitId;
            if (state.mode === 'USER_TO_UNIT') {
                userId = state.selectedId;
                unitId = targetId;
            } else {
                userId = targetId;
                unitId = state.selectedId;
            }

            const originalState = checkbox.checked;

            try {
                // showLoadingToast('Updating role...'); // Optional if you have it
                const response = await fetch("{{ route('admin.roles.update') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        unit_id: unitId,
                        user_id: userId
                    })
                });

                const data = await response.json();

                if (data.success) {
                    showToast('Success', data.message, 'success');

                    const userIdx = usersData.findIndex(u => u.id === userId);
                    const unitObj = unitsData.find(u => u.id === unitId);

                    if (userIdx > -1) {
                        if (originalState) {
                            usersData[userIdx].unit_id = unitId;
                            usersData[userIdx].unit = unitObj;
                        } else {
                            usersData[userIdx].unit_id = null;
                            usersData[userIdx].unit = null;
                        }
                    }
                    renderLeftPanel();
                    renderRightPanel();

                } else {
                    throw new Error(data.message || 'Failed');
                }

            } catch (error) {
                checkbox.checked = !originalState;
                showToast('Error', error.message, 'error');
            }
        }

        // --- SHOW PERMISSIONS MODAL & LOGIC ---
        function showPermissions(id, type) {
            els.permissionsModal.classList.remove('hidden');
            els.modalContent.innerHTML =
                '<div class="text-center p-4"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mx-auto"></div></div>';

            // Reset filters
            state.permSearch = '';
            state.permActionFilter = 'ALL';
            els.permSearchInput.value = '';
            els.permActionFilter.innerHTML = '<option value="ALL">All Actions</option>';

            // Fetch Data
            let item;
            if (type === 'USER') {
                item = usersData.find(u => u.id === id);
                let direct = item.permissions || [];
                let inherited = (item.unit && item.unit.permissions) ? item.unit.permissions : [];

                // --- MERGE LOGIC FOR DUPLICATES ---
                const mergedMap = new Map();

                // 1. Process Inherited First
                inherited.forEach(p => {
                    mergedMap.set(p.id, {
                        ...p,
                        source: 'Inherited',
                        unitName: item.unit ? item.unit.name : 'Unknown'
                    });
                });

                // 2. Process Direct (Overwrite/Tag as Both if exists)
                direct.forEach(p => {
                    if (mergedMap.has(p.id)) {
                        // Collision! It exists in both.
                        const existing = mergedMap.get(p.id);
                        existing.source = 'Both'; // Mark as Both
                    } else {
                        mergedMap.set(p.id, {
                            ...p,
                            source: 'Direct',
                            unitName: null
                        });
                    }
                });

                // Convert Map to Array
                state.currentPerms = Array.from(mergedMap.values());

                els.modalTitle.innerText = item.name;
                els.modalRoleName.innerText = item.unit ? item.unit.name : 'No Unit Assigned';
                els.modalLegend.classList.remove('hidden');

            } else {
                item = unitsData.find(u => u.id === id);
                let perms = item.permissions || [];
                state.currentPerms = perms.map(p => ({
                    ...p,
                    source: 'Unit_Base',
                    unitName: item.name
                }));
                els.modalTitle.innerText = item.name;
                els.modalRoleName.innerText = 'Base Unit Role';
                els.modalLegend.classList.add('hidden');
            }
            els.modalPermCount.innerText = state.currentPerms.length;

            // Populate Filter Dropdown
            const uniqueActions = [...new Set(state.currentPerms.map(p => p.action))].filter(Boolean).sort();
            uniqueActions.forEach(act => {
                const opt = document.createElement('option');
                opt.value = act;
                opt.innerText = act.charAt(0).toUpperCase() + act.slice(1);
                els.permActionFilter.appendChild(opt);
            });

            renderPermissionList();
        }

        function renderPermissionList() {
            // Filter Data
            const filtered = state.currentPerms.filter(p => {
                const matchesSearch = p.name.toLowerCase().includes(state.permSearch);
                const matchesAction = state.permActionFilter === 'ALL' || p.action === state.permActionFilter;
                return matchesSearch && matchesAction;
            });

            if (filtered.length === 0) {
                els.modalContent.innerHTML =
                    `<div class="text-center text-gray-400 p-8 border border-dashed rounded-xl bg-gray-50 flex flex-col items-center gap-2"><svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>No permissions match current filters.</div>`;
                return;
            }

            // Group Data
            const groups = {};
            filtered.forEach(p => {
                const gName = (p.permission_group && p.permission_group.name) ? p.permission_group.name :
                    'Ungrouped';
                if (!groups[gName]) groups[gName] = [];
                groups[gName].push(p);
            });

            // Render Accordions
            let html = '';
            for (const [groupName, perms] of Object.entries(groups)) {
                html += `
                        <div class="border border-gray-200 rounded-lg mb-2">
                            <button onclick="toggleAccordion(this)" class="group-header w-full flex justify-between items-center p-3 bg-gray-50 hover:bg-gray-100 transition-colors" aria-expanded="true">
                                <span class="text-xs font-bold uppercase text-gray-600 tracking-wider">${groupName} <span class="text-gray-400 font-normal ml-1">(${perms.length})</span></span>
                                <svg class="accordion-icon w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div class="accordion-content bg-white">
                                <div class="p-3 flex flex-wrap gap-2">
                    `;

                perms.forEach(p => {
                    let badgeClass = '';
                    let tooltip = '';

                    if (p.source === 'Both') {
                        badgeClass = 'combined';
                        tooltip = `Combined Access: Directly assigned AND Inherited from Unit "${p.unitName}"`;
                    } else if (p.source === 'Direct') {
                        badgeClass = 'direct';
                        tooltip = 'Direct Permission (User Specific)';
                    } else if (p.source === 'Inherited') {
                        badgeClass = 'inherited';
                        tooltip = `Inherited from Unit "${p.unitName}"`;
                    } else {
                        badgeClass = 'unit-base';
                        tooltip = 'Base Permission of Unit';
                    }

                    let actionLower = p.action ? p.action.toLowerCase() : 'default';
                    let actionColorClass = actionColors[actionLower] || actionColors['default'];

                    html += `
                            <div class="perm-tag ${badgeClass}" data-tooltip='${tooltip}'>
                                <span class="font-medium">${p.name}</span>
                                <span class="text-[9px] uppercase border px-1 rounded ml-1 ${actionColorClass}">${p.action}</span>
                            </div>
                        `;
                });

                html += `   </div>
                            </div>
                        </div>`;
            }
            els.modalContent.innerHTML = html;
        }

        // --- ACCORDION LOGIC ---
        window.toggleAccordion = function (btn) {
            const content = btn.nextElementSibling;
            const isExpanded = btn.getAttribute('aria-expanded') === 'true';

            if (isExpanded) {
                content.style.maxHeight = '0px';
                content.style.opacity = '0';
                content.style.padding = '0';
                btn.setAttribute('aria-expanded', 'false');
            } else {
                content.style.maxHeight = content.scrollHeight + 50 + 'px';
                content.style.opacity = '1';
                content.style.padding = '';
                btn.setAttribute('aria-expanded', 'true');
            }
        }

        function closePermModal() {
            els.permissionsModal.classList.add('hidden');
        }

        function showToast(title, message, icon) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    icon: icon,
                    title: title,
                    text: message,
                    customClass: {
                        popup: 'swal2-toast-custom'
                    }
                });
            } else {
                alert(`${title}: ${message}`);
            }
        }

        // --- CREATE UNIT MODAL FUNCTIONS ---
        function openCreateUnitModal() {
            document.getElementById('create-unit-modal').classList.remove('hidden');
            document.getElementById('create-unit-form').reset();
        }

        function closeCreateUnitModal() {
            document.getElementById('create-unit-modal').classList.add('hidden');
        }

        async function submitCreateUnit(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            try {
                const response = await fetch("{{ route('admin.units.create') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    showToast('Success', result.message, 'success');
                    closeCreateUnitModal();

                    // Add new unit to unitsData
                    unitsData.push(result.unit);

                    // Re-render if in UNIT_TO_USER mode
                    if (state.mode === 'UNIT_TO_USER') {
                        renderLeftPanel();
                    } else {
                        renderRightPanel();
                    }
                } else {
                    throw new Error(result.message || 'Failed to create unit');
                }
            } catch (error) {
                showToast('Error', error.message, 'error');
            }
        }
    </script>
@endsection