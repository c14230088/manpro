@extends('layouts.admin')

@section('title', 'Academic Periods')

@section('style')
    <style>
        /* Animasi Masuk Halus */
        .fade-in-up {
            animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
            transform: translateY(20px);
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Active Card Highlighting */
        .active-card {
            background: linear-gradient(135deg, #ecfdf5 0%, #ffffff 100%);
            border-color: #10b981;
            box-shadow: 0 4px 20px -2px rgba(16, 185, 129, 0.15);
        }

        /* Modal Transition */
        #create-modal {
            transition: opacity 0.2s ease-in-out;
        }

        #create-modal.hidden {
            pointer-events: none;
            opacity: 0;
        }

        #create-modal:not(.hidden) {
            opacity: 1;
            pointer-events: auto;
        }

        /* Smooth Height for Year Groups */
        .year-group {
            transition: all 0.3s ease;
        }
    </style>
@endsection

@section('body')
    <div class="min-h-screen flex flex-col pb-10">

        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row w-full py-4 shadow-md items-center justify-between mb-5 px-6 md:px-4">
            <h1 class="text-center md:text-left text-4xl uppercase font-bold mb-2 md:mb-0">Academic Periods</h1>

            <div class="flex gap-2">
                <button onclick="openCreateModal()"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 flex items-center gap-2 transition-all shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>New Period</span>
                </button>
            </div>
        </div>

        {{-- Filter & Search Bar (Sticky & Glassmorphism) --}}
        <div class="sticky top-0 z-30 mb-8 -mx-4 px-4 md:mx-0 md:px-0">
            <div class="bg-white/90 backdrop-blur-md p-4 rounded-xl border border-gray-200/80 shadow-sm">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    {{-- Search --}}
                    <div class="sm:col-span-2 relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" id="searchFilter" placeholder="Search year or semester..."
                            class="block w-full p-2.5 pl-10 text-sm border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 transition-colors bg-white">
                    </div>

                    {{-- Filter Semester --}}
                    <div>
                        <select id="semesterFilter"
                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 cursor-pointer">
                            <option value="">All Semesters</option>
                            <option value="GASAL">Gasal</option>
                            <option value="GENAP">Genap</option>
                        </select>
                    </div>

                    {{-- Filter Status --}}
                    <div>
                        <select id="statusFilter"
                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 cursor-pointer">
                            <option value="">All Status</option>
                            <option value="active">Active Only</option>
                            <option value="inactive">Inactive Only</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Periods Grid --}}
        @if ($periods->isEmpty())
            <div
                class="flex flex-col items-center justify-center py-20 bg-white rounded-2xl border-2 border-dashed border-gray-300">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900">No Periods Found</h3>
                <p class="text-gray-500 text-sm mt-1">Start by adding a new academic period.</p>
            </div>
        @else
            @php
                $groupedPeriods = $periods->sortByDesc('academic_year')->groupBy('academic_year');
            @endphp

            <div id="periodsContainer" class="space-y-8">
                @foreach ($groupedPeriods as $year => $yearPeriods)
                    <div class="year-group fade-in-up" data-year="{{ $year }}">
                        {{-- Group Header --}}
                        <div class="flex items-center gap-4 mb-5">
                            <div class="h-px bg-gray-200 flex-1"></div>
                            <span
                                class="text-gray-500 font-bold text-base md:text-lg bg-gray-100/80 px-4 py-1.5 rounded-full border border-gray-200 shadow-sm tracking-wide font-mono">{{ $year }}</span>
                            <div class="h-px bg-gray-200 flex-1"></div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                            @foreach ($yearPeriods as $period)
                                <div class="period-card flex flex-col h-full relative bg-white rounded-2xl p-5 border transition-all duration-300 {{ $period->active ? 'active-card border-2' : 'border-gray-200 hover:border-indigo-300 hover:shadow-lg' }}"
                                    data-year="{{ $period->academic_year }}" data-semester="{{ $period->semester }}"
                                    data-status="{{ $period->active ? 'active' : 'inactive' }}">

                                    <div class="flex justify-between items-start mb-4">
                                        <div
                                            class="p-2.5 rounded-xl {{ $period->active ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-500' }}">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                        @if ($period->active)
                                            <span
                                                class="inline-flex items-center gap-1.5 bg-emerald-100 text-emerald-700 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wide shadow-sm">
                                                <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                                                Active
                                            </span>
                                        @endif
                                    </div>

                                    <div class="flex-grow">
                                        <h3 class="text-xl md:text-2xl font-bold text-gray-800 tracking-tight">
                                            {{ $period->academic_year }}
                                        </h3>
                                        <p
                                            class="text-sm font-medium uppercase tracking-wider mt-1 {{ $period->active ? 'text-emerald-600' : 'text-gray-500' }}">
                                            Semester {{ $period->semester }}
                                        </p>
                                    </div>

                                    <div class="mt-6 pt-4 border-t {{ $period->active ? 'border-emerald-200' : 'border-gray-100' }}">
                                        @if ($period->active)
                                            <button disabled
                                                class="w-full py-2.5 rounded-lg bg-emerald-50/50 text-emerald-600 font-semibold text-sm flex items-center justify-center gap-2 cursor-default select-none">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                Currently Active
                                            </button>
                                        @else
                                            <button
                                                onclick="activatePeriod('{{ $period->id }}', '{{ $period->academic_year }}', '{{ $period->semester }}')"
                                                class="w-full py-2.5 rounded-lg bg-white border border-gray-300 text-gray-700 font-semibold text-sm hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-300 transition-all duration-200 flex items-center justify-center gap-2 group active:scale-[0.98]">
                                                <span>Set as Active</span>
                                                <svg class="w-4 h-4 text-gray-400 group-hover:text-indigo-500 transition-colors" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                {{-- No Results Message --}}
                <div id="noSearchResults"
                    class="hidden flex-col items-center justify-center py-12 bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <p class="text-gray-500 font-medium">No matching periods found.</p>
                    <button onclick="resetFilters()"
                        class="mt-2 text-sm text-indigo-600 hover:text-indigo-800 font-semibold hover:underline">Clear
                        filters</button>
                </div>
            </div>
        @endif
    </div>

    {{-- Create Modal --}}
    <div id="create-modal" class="fixed inset-0 z-[2000] hidden" role="dialog" aria-modal="true">
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity opacity-0" id="modal-backdrop"
            onclick="closeCreateModal()"></div>

        <div class="fixed inset-0 z-10 overflow-y-auto pointer-events-none">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                {{-- Modal Panel --}}
                <div class="pointer-events-auto relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 w-full max-w-md opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    id="modal-panel">

                    <form id="create-period-form" onsubmit="submitCreatePeriod(event)">
                        <div class="bg-white px-6 pt-6 pb-6">
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900">Add Academic Period</h3>
                                    <p class="text-xs text-gray-500 mt-0.5">Create a new semester entry.</p>
                                </div>
                                <button type="button" onclick="closeCreateModal()"
                                    class="bg-gray-100 hover:bg-gray-200 text-gray-500 hover:text-gray-700 rounded-lg p-2 transition-colors">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <div class="space-y-5">
                                {{-- Academic Year Input --}}
                                <div>
                                    <label for="academic_year" class="block text-sm font-bold text-gray-700 mb-1.5">Academic
                                        Year</label>
                                    <div class="relative">
                                        <input type="text" id="academic_year" name="academic_year"
                                            placeholder="e.g. 2024/2025" maxlength="9"
                                            class="block w-full rounded-xl border-gray-300 pl-10 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-3 transition-all shadow-sm"
                                            oninput="formatAcademicYear(this, event)">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                </path>
                                            </svg>
                                        </div>
                                    </div>
                                    <p class="mt-1.5 text-xs text-gray-500 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Format: YYYY/YYYY (Slash is automatic)
                                    </p>
                                </div>

                                {{-- Semester Select --}}
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Semester</label>
                                    <div class="grid grid-cols-2 gap-3">
                                        <label class="cursor-pointer relative group">
                                            <input type="radio" name="semester" value="GASAL" class="peer sr-only" checked>
                                            <div
                                                class="p-3 rounded-xl border-2 border-gray-200 bg-white hover:bg-gray-50 peer-checked:border-indigo-600 peer-checked:bg-indigo-50/50 peer-checked:text-indigo-700 transition-all text-center shadow-sm group-hover:shadow-md">
                                                <span class="text-sm font-bold">GASAL</span>
                                            </div>
                                        </label>
                                        <label class="cursor-pointer relative group">
                                            <input type="radio" name="semester" value="GENAP" class="peer sr-only">
                                            <div
                                                class="p-3 rounded-xl border-2 border-gray-200 bg-white hover:bg-gray-50 peer-checked:border-indigo-600 peer-checked:bg-indigo-50/50 peer-checked:text-indigo-700 transition-all text-center shadow-sm group-hover:shadow-md">
                                                <span class="text-sm font-bold">GENAP</span>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                {{-- Active Checkbox --}}
                                <div class="flex items-start p-3 bg-gray-50 rounded-xl border border-gray-100">
                                    <div class="flex h-5 items-center">
                                        <input id="active" name="active" type="checkbox"
                                            class="h-5 w-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                    </div>
                                    <div class="ml-3">
                                        <label for="active" class="text-sm font-bold text-gray-800 cursor-pointer">Set
                                            as Active Immediately</label>
                                        <p class="text-gray-500 text-xs mt-0.5">This will deactivate the current active
                                            period.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div
                            class="bg-gray-50 px-6 py-4 flex flex-col-reverse sm:flex-row gap-3 rounded-b-2xl border-t border-gray-100">
                            <button type="button" onclick="closeCreateModal()"
                                class="w-full inline-flex justify-center rounded-xl bg-white px-4 py-2.5 text-sm font-bold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-colors">Cancel</button>
                            <button type="submit"
                                class="w-full inline-flex justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-bold text-white shadow-md hover:bg-indigo-700 hover:shadow-lg transition-all">Create
                                Period</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script>
        // --- MODAL LOGIC ---
        const modal = document.getElementById('create-modal');
        const backdrop = document.getElementById('modal-backdrop');
        const panel = document.getElementById('modal-panel');

        function openCreateModal() {
            modal.classList.remove('hidden');
            // Animation In
            setTimeout(() => {
                backdrop.classList.remove('opacity-0');
                panel.classList.remove('opacity-0', 'translate-y-4', 'sm:translate-y-0', 'sm:scale-95');
            }, 10);
        }

        function closeCreateModal() {
            // Animation Out
            backdrop.classList.add('opacity-0');
            panel.classList.add('opacity-0', 'translate-y-4', 'sm:translate-y-0', 'sm:scale-95');

            setTimeout(() => {
                modal.classList.add('hidden');
                document.getElementById('create-period-form').reset();
            }, 300);
        }

        // --- FILTER & SEARCH LOGIC ---
        const searchInput = document.getElementById('searchFilter');
        const semesterSelect = document.getElementById('semesterFilter');
        const statusSelect = document.getElementById('statusFilter');
        const noResults = document.getElementById('noSearchResults');

        function filterPeriods() {
            const searchTerm = searchInput.value.toLowerCase();
            const semesterTerm = semesterSelect.value;
            const statusTerm = statusSelect.value;

            const yearGroups = document.querySelectorAll('.year-group');
            let hasGlobalMatch = false;

            yearGroups.forEach(group => {
                const cards = group.querySelectorAll('.period-card');
                let hasVisibleCardInGroup = false;

                cards.forEach(card => {
                    const year = card.dataset.year.toLowerCase();
                    const semester = card.dataset.semester;
                    const status = card.dataset.status;
                    const fullText = year + ' ' + semester.toLowerCase() + ' ' + status;

                    let matchesSearch = fullText.includes(searchTerm);
                    let matchesSemester = semesterTerm === '' || semester === semesterTerm;
                    let matchesStatus = statusTerm === '' || status === statusTerm;

                    if (matchesSearch && matchesSemester && matchesStatus) {
                        card.style.display = 'block'; // Use block for card inside grid
                        hasVisibleCardInGroup = true;
                        hasGlobalMatch = true;
                    } else {
                        card.style.display = 'none';
                    }
                });

                if (hasVisibleCardInGroup) {
                    group.style.display = 'block';
                } else {
                    group.style.display = 'none';
                }
            });

            if (hasGlobalMatch) {
                noResults.classList.add('hidden');
                noResults.classList.remove('flex');
            } else {
                noResults.classList.remove('hidden');
                noResults.classList.add('flex');
            }
        }

        function resetFilters() {
            searchInput.value = '';
            semesterSelect.value = '';
            statusSelect.value = '';
            filterPeriods();
        }

        if (searchInput) {
            searchInput.addEventListener('input', filterPeriods);
            semesterSelect.addEventListener('change', filterPeriods);
            statusSelect.addEventListener('change', filterPeriods);
        }

        // --- INPUT FORMATTER (SMART BACKSPACE FIX) ---
        function formatAcademicYear(input, event) {
            // FIX: Jika user tekan Backspace atau Delete, JANGAN jalankan format otomatis
            if (event.inputType === 'deleteContentBackward' || event.inputType === 'deleteContentForward') {
                return;
            }

            let value = input.value.replace(/[^0-9]/g, ''); // Hapus semua kecuali angka

            // Logic: Otomatis tambah '/' setelah digit ke-4
            if (value.length > 3) {
                value = value.substring(0, 4) + '/' + value.substring(4, 8);
            }

            input.value = value;
        }

        // --- CREATE PERIOD ACTION ---
        async function submitCreatePeriod(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());
            data.active = form.querySelector('#active').checked;

            // BLOCKING LOADER
            Swal.fire({
                title: 'Creating Period...',
                text: 'Please wait while we process your request.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                const response = await fetch("{{ route('admin.periods.create') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    closeCreateModal();
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: result.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    throw new Error(result.message || 'Validation error');
                }

            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: error.message,
                    customClass: {
                        popup: 'swal2-popup-custom'
                    }
                });
            }
        }

        // --- ACTIVATE PERIOD ACTION ---
        function activatePeriod(id, year, semester) {
            Swal.fire({
                title: 'Activate this Period?',
                html: `Are you sure you want to set <b>${year} ${semester}</b> as the active academic period?<br><span class="text-sm text-gray-500">The current active period will be deactivated.</span>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#9ca3af',
                confirmButtonText: 'Yes, Activate',
                reverseButtons: true,
                focusCancel: true
            }).then(async (result) => {
                if (result.isConfirmed) {
                    // BLOCKING LOADER
                    Swal.fire({
                        title: 'Activating...',
                        text: 'Please wait.',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    try {
                        const url = "{{ route('admin.periods.updateActive', ':id') }}".replace(':id', id);

                        const response = await fetch(url, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content'),
                                'Accept': 'application/json'
                            }
                        });

                        const data = await response.json();

                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Activated!',
                                text: data.message,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            throw new Error(data.message || 'Failed to activate');
                        }

                    } catch (error) {
                        Swal.fire('Error', error.message, 'error');
                    }
                }
            });
        }
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('periods').classList.add('bg-slate-100');
            document.getElementById('periods').classList.add('active');
        });
    </script>
@endsection