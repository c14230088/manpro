@extends('layouts.admin')

@section('title', 'Software Management')

@section('style')
    <style>
        /* --- ANIMATIONS --- */
        .fade-in-up {
            animation: fadeInUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
            transform: translateY(20px);
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* --- TOMSELECT CUSTOMIZATION (UPDATED) --- */
        .ts-control {
            border-radius: 0.5rem;
            padding: 10px 12px;
            border-color: #d1d5db;
            box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            z-index: 10;
            position: relative;
        }

        .ts-control.focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
        }

        .ts-control .item {
            background-color: #eff6ff;
            color: #1e40af;
            border: 1px solid #dbeafe;
            border-radius: 9999px;
            padding: 2px 10px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
        }

        /* FIX: Dropdown Styling agar tidak terpotong & ada scroll */
        .ts-dropdown {
            border-radius: 0.5rem;
            box-shadow: 0 10px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
            background-color: white;
            border: 1px solid #e5e7eb;
            padding: 6px;
            z-index: 9999 !important; /* Force paling atas */
            max-height: 250px; /* Batasi tinggi dropdown */
            overflow-y: auto; /* Scroll jika item banyak */
        }

        .ts-dropdown .option {
            border-radius: 0.375rem;
            padding: 8px 12px;
        }

        .ts-dropdown .active {
            background-color: #f3f4f6;
            color: #4f46e5;
            font-weight: 600;
        }

        .ts-dropdown .ts-dropdown-content {
            max-height: none !important;
        }

        /* --- DATATABLES CUSTOM STYLING --- */
        .dataTables_wrapper .dataTables_filter {
            display: none;
        }

        .dataTables_wrapper .dataTables_length select {
            padding-right: 2rem;
            padding-left: 0.75rem;
            padding-top: 0.3rem;
            padding-bottom: 0.3rem;
            font-size: 0.875rem;
            border-radius: 0.5rem;
            border-color: #d1d5db;
            cursor: pointer;
        }

        .dataTables_wrapper .dataTables_length select:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
        }

        .dataTables_wrapper .dataTables_info {
            font-size: 0.875rem;
            color: #6b7280;
            padding-top: 0.5rem;
        }

        .dataTables_wrapper .dataTables_paginate {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            padding-top: 0.5rem;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.375rem 0.75rem;
            margin-left: 0;
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: 0.5rem;
            border: 1px solid #e5e7eb;
            background-color: white;
            color: #374151;
            cursor: pointer;
            transition: all 0.2s;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.disabled) {
            background-color: #f9fafb;
            color: #4f46e5;
            border-color: #c7d2fe;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            z-index: 10;
            background-color: #4f46e5 !important;
            color: white !important;
            border-color: #4f46e5 !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
            opacity: 0.5;
            cursor: not-allowed;
            background-color: #f9fafb;
        }

        table.dataTable.dtr-inline.collapsed>tbody>tr>td.dtr-control:before {
            background-color: #4f46e5;
        }
    </style>
@endsection

@section('body')
    <div class="min-h-screen flex flex-col pb-10" id="softwares">

        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8 fade-in-up">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-800 tracking-tight">Softwares and Operating System</h1>
                <p class="text-gray-500 text-sm mt-1">Manage Applications and Operating Systems distribution across labs.</p>
            </div>

            <button onclick="openModal('create')"
                class="group flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-semibold shadow-lg shadow-indigo-200 transition-all duration-300 transform hover:-translate-y-0.5">
                <svg class="w-5 h-5 transition-transform group-hover:rotate-90" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Add Software</span>
            </button>
        </div>

        {{-- Main Content Card --}}
        <div class="bg-white rounded-2xl p-0 border border-gray-200 shadow-sm overflow-hidden fade-in-up"
            style="animation-delay: 100ms;">

            {{-- Custom Toolbar (Filter & Search) --}}
            <div
                class="p-5 border-b border-gray-100 bg-gray-50/50 flex flex-col md:flex-row gap-4 justify-between items-center">

                {{-- Lab Filter --}}
                <div class="relative w-full md:w-64">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                            </path>
                        </svg>
                    </div>
                    {{-- Note: Value menggunakan name agar compatible dengan search Datatable --}}
                    <select id="labFilter"
                        class="block w-full pl-10 pr-4 py-2.5 text-sm border-gray-300 rounded-lg bg-white focus:ring-indigo-500 focus:border-indigo-500 shadow-sm cursor-pointer transition-shadow">
                        <option value="">All Laboratories</option>
                        @foreach ($labs as $lab)
                            <option value="{{ $lab->name }}">{{ $lab->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Search Box --}}
                <div class="relative w-full md:w-72">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" id="customSearch"
                        class="block w-full pl-10 pr-4 py-2.5 text-sm border-gray-300 rounded-lg bg-white focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition-shadow"
                        placeholder="Search software name...">
                </div>
            </div>

            {{-- Table --}}
            <div class="w-full">
                <table id="softwareTable" class="w-full text-sm pb-3 text-left text-gray-500 display responsive nowrap"
                    style="width:100%">
                    <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 font-bold tracking-wider">Software Name</th>
                            <th class="px-6 py-4 font-bold tracking-wider">Version</th>
                            <th class="px-6 py-4 font-bold tracking-wider">Assigned Labs</th>
                            <th class="px-6 py-4 font-bold tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($softwares as $software)
                            <tr class="hover:bg-gray-50 transition-colors group">

                                {{-- Name & Desc --}}
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-gray-800 text-sm">{{ $software->name }}</span>
                                        <span class="text-gray-400 text-xs mt-0.5 truncate max-w-xs"
                                            title="{{ $software->description }}">
                                            {{ $software->description ?? 'No description.' }}
                                        </span>
                                    </div>
                                </td>

                                {{-- Version --}}
                                <td class="px-6 py-4">
                                    @if ($software->version)
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200 font-mono">
                                            {{ $software->version }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-xs italic">N/A</span>
                                    @endif
                                </td>

                                {{-- Labs (EXPANDABLE LOGIC) --}}
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap items-center gap-2">
                                        {{-- Always show first 2 --}}
                                        @forelse($software->labs->take(2) as $lab)
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100 uppercase tracking-wide">
                                                {{ $lab->name }}
                                            </span>
                                        @empty
                                            <span class="text-xs text-gray-400 italic">Unassigned</span>
                                        @endforelse

                                        @if ($software->labs->count() > 2)
                                            {{-- Hidden Container for the rest --}}
                                            <span id="more-labs-{{ $software->id }}" class="hidden contents">
                                                @foreach ($software->labs->skip(2) as $lab)
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100 uppercase tracking-wide">
                                                        {{ $lab->name }}
                                                    </span>
                                                @endforeach
                                            </span>

                                            {{-- Toggle Button --}}
                                            <button
                                                onclick="toggleLabs('{{ $software->id }}', this, {{ $software->labs->count() - 2 }})"
                                                class="inline-flex items-center justify-center px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 border border-gray-200 text-gray-600 hover:bg-gray-200 hover:text-gray-800 transition-colors cursor-pointer select-none">
                                                +{{ $software->labs->count() - 2 }} more
                                            </button>
                                        @endif

                                        {{-- Hidden span for search filtering --}}
                                        <span class="hidden">{{ $software->labs->pluck('name')->implode(' ') }}</span>
                                    </div>
                                </td>

                                {{-- Actions --}}
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button onclick='openModal("edit", @json($software))'
                                            class="p-2 rounded-lg text-gray-400 hover:text-indigo-600 hover:bg-indigo-200 transition-colors"
                                            title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                        </button>
                                        <button onclick="deleteSoftware('{{ $software->id }}', '{{ $software->name }}')"
                                            class="p-2 rounded-lg text-gray-400 hover:text-rose-600 hover:bg-rose-200 transition-colors"
                                            title="Delete">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($softwares->isEmpty())
                <div class="flex flex-col items-center justify-center py-16 text-center">
                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">No Software Found</h3>
                    <p class="text-gray-500 text-sm mt-1">Start by adding softwares to manage lab assignments.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- MODAL (Fix for Dropdown Overflow) --}}
    <div id="software-modal" class="fixed inset-0 z-[2000] hidden" role="dialog" aria-modal="true">
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity opacity-0" id="modal-backdrop"
            onclick="closeModal()"></div>
        
        {{-- Wrapper with Scroll --}}
        <div class="fixed inset-0 overflow-y-auto pointer-events-none">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                
                {{-- Panel: overflow-visible penting agar dropdown bisa keluar --}}
                <div class="pointer-events-auto relative transform rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95 border border-gray-100 overflow-visible"
                    id="modal-panel">

                    <form id="software-form" onsubmit="submitSoftware(event)">
                        
                        {{-- Header (Rounded Top) --}}
                        <div class="bg-white p-6 rounded-t-2xl">
                            <div class="flex items-center justify-between mb-6">
                                <div class="flex items-center gap-3">
                                    <div class="bg-indigo-100 p-2 rounded-lg text-indigo-600">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4">
                                            </path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-900" id="modal-title">Add Software</h3>
                                        <p class="text-xs text-gray-500">Configure software details.</p>
                                    </div>
                                </div>
                                <button type="button" onclick="closeModal()"
                                    class="text-gray-400 hover:text-gray-500 transition-colors">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <input type="hidden" id="software_id" name="software_id">

                            {{-- Form Fields --}}
                            <div class="space-y-5">
                                {{-- Name & Version --}}
                                <div class="grid grid-cols-3 gap-4">
                                    <div class="col-span-2">
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Name
                                            <span class="text-rose-500">*</span></label>
                                        <input type="text" name="name" id="name" required
                                            class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 transition-shadow"
                                            placeholder="e.g. Adobe Photoshop">
                                    </div>
                                    <div class="col-span-1">
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Version</label>
                                        <input type="text" name="version" id="version"
                                            class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 transition-shadow"
                                            placeholder="v2024">
                                    </div>
                                </div>

                                {{-- Description --}}
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Description</label>
                                    <textarea name="description" id="description" rows="2"
                                        class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm transition-shadow resize-none"
                                        placeholder="Brief description..."></textarea>
                                </div>

                                {{-- Labs Input (TomSelect) --}}
                                <div class="relative">
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Assigned Labs</label>
                                    {{-- Wrapper Z-Index agar Select muncul di atas elemen lain --}}
                                    <div class="relative z-20"> 
                                        <select id="lab_ids" name="lab_ids[]" multiple placeholder="Select labs..." autocomplete="off">
                                            @foreach ($labs as $lab)
                                                <option value="{{ $lab->id }}">{{ $lab->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Footer (Rounded Bottom) --}}
                        <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-2 border-t border-gray-100 rounded-b-2xl">
                            <button type="submit" id="submit-btn"
                                class="inline-flex w-full justify-center items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-indigo-700 sm:w-auto transition-all">
                                <span>Save Changes</span>
                            </button>
                            <button type="button" onclick="closeModal()"
                                class="inline-flex w-full justify-center rounded-lg bg-white px-4 py-2.5 text-sm font-bold text-gray-700 border border-gray-300 shadow-sm hover:bg-gray-50 sm:w-auto transition-colors">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script>
        let table;
        let tomSelectInstance;
        const modal = document.getElementById('software-modal');
        const backdrop = document.getElementById('modal-backdrop');
        const panel = document.getElementById('modal-panel');
        const form = document.getElementById('software-form');
        let currentMode = 'create';

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('softwares').classList.add('bg-slate-100');
            document.getElementById('softwares').classList.add('active');

            // 1. DataTables Init 
            table = $('#softwareTable').DataTable({
                responsive: true,
                autoWidth: false,
                dom: 't<"flex flex-col md:flex-row justify-between items-center gap-4 px-6 py-6 border-t border-gray-100"<"flex items-center gap-4"li>p>',
                pageLength: 10,
                lengthMenu: [
                    [5, 10, 25, 50, -1],
                    [5, 10, 25, 50, "All"]
                ],
                language: {
                    lengthMenu: "Show _MENU_",
                    paginate: {
                        previous: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>',
                        next: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>'
                    }
                },
                columnDefs: [{
                    orderable: false,
                    targets: 3
                }],
            });

            // 2. Custom Search Binding
            $('#customSearch').on('keyup', function() {
                table.search(this.value).draw();
            });

            // 3. Custom Filter Binding (Column 2 = Assigned Labs)
            $('#labFilter').on('change', function() {
                table.column(2).search(this.value).draw();
            });

            // 4. TomSelect Init
            tomSelectInstance = new TomSelect('#lab_ids', {
                plugins: ['remove_button', 'dropdown_input'],
                maxItems: null,
                valueField: 'value',
                labelField: 'text',
                searchField: 'text',
                placeholder: 'Select labs...',
                render: {
                    item: function(data, escape) {
                        return '<div class="item">' + escape(data.text) + '</div>';
                    },
                    option: function(data, escape) {
                        return '<div class="option hover:bg-indigo-50 cursor-pointer">' + escape(data
                            .text) + '</div>';
                    }
                }
            });
        });

        // --- TOGGLE LABS FUNCTION ---
        function toggleLabs(id, btn, count) {
            const container = document.getElementById(`more-labs-${id}`);
            const isHidden = container.classList.contains('hidden');

            if (isHidden) {
                container.classList.remove('hidden');
                btn.innerText = 'Show Less';
                btn.classList.add('bg-gray-200', 'text-gray-800');
                btn.classList.remove('bg-gray-100', 'text-gray-600');
            } else {
                container.classList.add('hidden');
                btn.innerText = `+${count} more`;
                btn.classList.remove('bg-gray-200', 'text-gray-800');
                btn.classList.add('bg-gray-100', 'text-gray-600');
            }
        }

        // --- MODAL LOGIC ---
        function openModal(mode, data = null) {
            currentMode = mode;
            form.reset();
            tomSelectInstance.clear();

            const titleEl = document.getElementById('modal-title');
            const btnEl = document.getElementById('submit-btn');
            const idInput = document.getElementById('software_id');

            if (mode === 'edit' && data) {
                titleEl.innerText = 'Edit Software';
                btnEl.querySelector('span').innerText = 'Update Changes';
                idInput.value = data.id;

                document.getElementById('name').value = data.name;
                document.getElementById('version').value = data.version || '';
                document.getElementById('description').value = data.description || '';

                if (data.labs && data.labs.length > 0) {
                    tomSelectInstance.setValue(data.labs.map(l => l.id));
                }
            } else {
                titleEl.innerText = 'Add Software';
                btnEl.querySelector('span').innerText = 'Save Software';
                idInput.value = '';
            }

            modal.classList.remove('hidden');
            setTimeout(() => {
                backdrop.classList.remove('opacity-0');
                panel.classList.remove('opacity-0', 'translate-y-4', 'sm:translate-y-0', 'sm:scale-95');
            }, 10);
        }

        function closeModal() {
            backdrop.classList.add('opacity-0');
            panel.classList.add('opacity-0', 'translate-y-4', 'sm:translate-y-0', 'sm:scale-95');
            setTimeout(() => modal.classList.add('hidden'), 300);
        }

        async function submitSoftware(e) {
            e.preventDefault();
            const formData = new FormData(form);

            const btn = document.getElementById('submit-btn');
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML =
                '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Processing...';

            let url = currentMode === 'create' ?
                "{{ route('admin.softwares.create') }}" :
                "{{ route('admin.softwares.update', ':id') }}".replace(':id', formData.get('software_id'));

            Swal.fire({
                title: currentMode === 'create' ? 'Saving...' : 'Updating...',
                text: 'Please wait.',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => Swal.showLoading()
            });

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content'),
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    closeModal();
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: result.message,
                        timer: 1500,
                        showConfirmButton: false,
                    }).then(() => window.location.reload());
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: error.message
                });
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        }

        function deleteSoftware(id, name) {
            Swal.fire({
                title: 'Delete Software?',
                html: `Are you sure you want to remove <b>${name}</b>?<br><span class="text-sm text-gray-500">Assignments will also be removed.</span>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#9ca3af',
                confirmButtonText: 'Yes, Delete',
                reverseButtons: true,
                focusCancel: true
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        Swal.fire({
                            title: 'Deleting...',
                            text: 'Please wait.',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            didOpen: () => Swal.showLoading()
                        });
                        const url = "{{ route('admin.softwares.delete', ':id') }}".replace(':id', id);
                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content'),
                                'Accept': 'application/json'
                            }
                        });
                        const data = await response.json();

                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted',
                                text: data.message,
                                showConfirmButton: false,
                                timer: 1000
                            }).then(() => window.location.reload());
                        }
                    } catch (error) {
                        Swal.fire('Error', error.message, 'error');
                    }
                }
            });
        }
    </script>
@endsection