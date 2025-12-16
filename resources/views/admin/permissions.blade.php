@extends('layouts.admin')

@section('title', 'Access Control Management')

@section('style')
    <style>
        /* --- ANIMATIONS (Diambil dari referensi Roles) --- */
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

        /* --- ACCORDION TRANSITIONS --- */
        .accordion-content {
            transition: grid-template-rows 0.3s ease-out, padding 0.3s ease;
        }

        /* --- TOGGLE SWITCH CUSTOMIZATION --- */
        .toggle-checkbox {
            position: absolute;
            left: 0;
            right: auto;
            border-color: #d1d5db;
        }

        .toggle-checkbox:checked {
            right: 0;
            left: auto;
            border-color: #68D391;
        }

        .toggle-checkbox:checked+.toggle-label {
            background-color: #68D391;
        }

        /* Disabled State */
        .toggle-checkbox:disabled {
            cursor: not-allowed;
            opacity: 0.6;
        }

        /* Checked AND Disabled (Inherited) - Grey */
        .toggle-checkbox:checked:disabled {
            border-color: #2563EB;
        }

        .toggle-checkbox:checked:disabled+.toggle-label {
            background-color: #94a3b8;
        }

        /* --- OVERRIDE BUTTON STYLE --- */
        .override-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6b7280;
            transition: all 0.2s;
            padding: 4px;
            border-radius: 6px;
            background-color: #f3f4f6;
            height: 28px;
            width: 28px;
            flex-shrink: 0;
            cursor: pointer;
            border: 1px solid #e5e7eb;
        }

        .override-btn:hover {
            color: #4f46e5;
            background-color: #e0e7ff;
            border-color: #c7d2fe;
            transform: translateY(-1px);
        }

        /* --- BADGE STYLE --- */
        .inherited-badge {
            display: flex;
            align-items: center;
            gap: 4px;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 11px;
            white-space: nowrap;
            font-weight: 600;
            flex-shrink: 1;
            height: 28px;
        }

        /* --- MODEL ITEM STATES --- */
        .model-item {
            transition: all 0.2s ease;
        }

        .model-item:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border-color: #e5e7eb;
            z-index: 10;
        }

        .model-item.active {
            background: linear-gradient(135deg, #eef2ff 0%, #ffffff 100%);
            border-color: #6366f1;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.15);
        }

        /* --- PERMISSION CARD HOVER --- */
        .permission-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .permission-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            border-color: #a5b4fc;
            /* Indigo 300 */
        }

        /* --- TOMSELECT FIXES --- */
        .ts-wrapper.multi .ts-control {
            padding-right: 2rem !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 16px 12px;
        }
    </style>
@endsection

@section('body')
    <div class="flex flex-col h-[calc(100vh-100px)]">
        {{-- Header --}}
        <div class="flex flex-col md:flex-row w-full py-4 shadow-md items-center justify-between mb-5 px-6 md:px-4">
            <h1 class="text-center md:text-left text-4xl uppercase font-bold mb-2 md:mb-0">Access Control</h1>
            
            <div class="flex gap-2">
                {{-- Disini kita taruh Badge Total sebagai elemen kanan --}}
                <div class="px-4 py-2 bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-lg font-bold text-sm shadow-sm flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    Total Permissions: {{ $permissions->count() }}
                </div>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-6 h-full overflow-hidden">
            {{-- LEFT COLUMN: LIST USERS/UNITS --}}
            <div class="w-full lg:w-4/12 flex flex-col bg-white border border-gray-200 rounded-2xl shadow-sm h-full fade-in-up"
                style="animation-delay: 100ms;">
                <div class="p-4 border-b border-gray-100 space-y-4 bg-gray-50/50 rounded-t-2xl z-10 backdrop-blur-sm">
                    {{-- Type Toggle --}}
                    <div class="bg-gray-200 p-1 rounded-xl flex text-sm font-bold text-gray-500">
                        <button onclick="filterModelType('ALL')" id="btn-type-all"
                            class="type-filter-btn flex-1 py-2 px-3 rounded-lg bg-white text-gray-800 shadow-sm transition-all duration-300">All</button>
                        <button onclick="filterModelType('USER')" id="btn-type-user"
                            class="type-filter-btn flex-1 py-2 px-3 rounded-lg hover:bg-white/60 transition-all duration-300">Users</button>
                        <button onclick="filterModelType('UNIT')" id="btn-type-unit"
                            class="type-filter-btn flex-1 py-2 px-3 rounded-lg hover:bg-white/60 transition-all duration-300">Units</button>
                    </div>

                    {{-- Search & Filters --}}
                    <div class="space-y-3">
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400 group-focus-within:text-indigo-500 transition-colors"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" id="search-model"
                                class="block w-full pl-10 pr-3 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-xl bg-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all shadow-sm"
                                placeholder="Search name or email...">
                        </div>
                        <select id="filter-unit-select" placeholder="Filter by Units" autocomplete="off" class="rounded-xl">
                            <option value="">All Units</option>
                            @foreach ($units as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                            @endforeach
                        </select>
                        <select id="filter-has-permission" placeholder="Must have these Permissions..." autocomplete="off"
                            multiple class="rounded-xl">
                            @foreach ($permissions as $perm)
                                <option value="{{ $perm->id }}">{{ $perm->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Model List --}}
                <div id="model-list-container" class="flex-1 overflow-y-auto custom-scrollbar p-3 space-y-2">
                    @foreach ($users as $user)
                        <div class="model-item group flex items-center p-3.5 rounded-xl border border-transparent bg-white cursor-pointer"
                            data-id="{{ $user->id }}" data-type="USER" data-name="{{ strtolower($user->name) }}"
                            data-email="{{ strtolower($user->email) }}" data-unit-id="{{ $user->unit_id }}"
                            data-direct-permissions='@json($user->permissions->pluck('id'))'
                            data-unit-permissions='@json($user->unit ? $user->unit->permissions->pluck('id') : [])'
                            data-unit-name="{{ $user->unit ? $user->unit->name : '' }}">
                            <div class="flex-shrink-0 mr-4 relative">
                                <div
                                    class="w-11 h-11 rounded-full bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center text-white font-bold text-lg shadow-md group-hover:shadow-lg transition-all">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p
                                    class="text-sm font-bold text-gray-900 truncate group-hover:text-indigo-600 transition-colors">
                                    {{ $user->name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ $user->email }}</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-gray-200 text-gray-800 border border-gray-300">
                                        {{ $user->unit ? $user->unit->name : 'No Unit' }}
                                    </span>
                                    <span class="text-[10px] font-bold text-gray-500">USER</span>
                                </div>
                            </div>
                            <svg class="w-5 h-5 text-gray-300 group-hover:text-indigo-400 transition-colors opacity-0 group-hover:opacity-100"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                </path>
                            </svg>
                        </div>
                    @endforeach
                    @foreach ($units as $unit)
                        <div class="model-item group flex items-center p-3.5 rounded-xl border border-transparent bg-white cursor-pointer"
                            data-id="{{ $unit->id }}" data-type="UNIT" data-name="{{ strtolower($unit->name) }}"
                            data-email="" data-unit-id="{{ $unit->id }}"
                            data-direct-permissions='@json($unit->permissions->pluck('id'))'>
                            <div class="flex-shrink-0 mr-4">
                                <div
                                    class="w-11 h-11 rounded-xl bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-white shadow-md group-hover:shadow-lg transition-all">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p
                                    class="text-sm font-bold text-gray-900 truncate group-hover:text-emerald-600 transition-colors">
                                    {{ $unit->name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ $unit->description ?? 'Unit Organization' }}
                                </p>
                                <div class="mt-1">
                                    <span class="text-[10px] font-bold text-gray-500">UNIT</span>
                                </div>
                            </div>
                            <svg class="w-5 h-5 text-gray-300 group-hover:text-emerald-400 transition-colors opacity-0 group-hover:opacity-100"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                </path>
                            </svg>
                        </div>
                    @endforeach
                    <div id="no-models-found"
                        class="hidden flex flex-col items-center justify-center py-10 text-gray-400">
                        <svg class="w-12 h-12 mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                        <p class="text-sm font-medium">No results found</p>
                    </div>
                </div>
            </div>

            {{-- RIGHT COLUMN: PERMISSIONS --}}
            <div class="w-full lg:w-8/12 flex flex-col bg-white border border-gray-200 rounded-2xl shadow-sm h-full relative fade-in-up"
                style="animation-delay: 200ms;">
                {{-- Empty State Overlay --}}
                <div id="permission-overlay"
                    class="absolute inset-0 bg-white/80 backdrop-blur-md z-50 rounded-2xl flex flex-col items-center justify-center text-center p-6">
                    <div
                        class="w-20 h-20 bg-indigo-50 rounded-full shadow-inner flex items-center justify-center mb-6 animate-pulse">
                        <svg class="w-10 h-10 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">Select to Manage</h3>
                    <p class="text-gray-500 max-w-sm mx-auto">Choose a User or Unit from the list to view and configure
                        their specific access rights.</p>
                </div>

                {{-- Right Header --}}
                <div
                    class="p-5 border-b border-gray-100 bg-gray-50/50 rounded-t-2xl flex flex-col md:flex-row gap-4 items-end md:items-center justify-between z-40">
                    <div class="flex-1 w-full">
                        <div class="flex items-center gap-2 mb-2">
                            <h2 id="selected-model-name" class="text-xl font-bold text-gray-800 tracking-tight">
                                Permissions</h2>
                            <span id="selected-model-badge"
                                class="hidden px-2.5 py-0.5 rounded-lg text-[10px] font-bold uppercase tracking-wide bg-gray-200 text-gray-600 shadow-sm border border-gray-300/50"></span>
                        </div>
                        <div class="relative w-full">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" id="search-permission"
                                class="block w-full pl-9 pr-3 py-2 text-xs text-gray-900 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                                placeholder="Search permission name, route or group...">
                        </div>
                    </div>
                    <div class="flex gap-2 w-full md:w-auto">
                        <div class="w-1/2 md:w-40"><select id="filter-perm-group" class="" placeholder="Groups">
                                <option value="">All Groups</option>
                                @foreach ($permissions->pluck('permission_group')->unique('id')->filter() as $group)
                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                @endforeach
                            </select></div>
                        <div class="w-1/2 md:w-32"><select id="filter-perm-action" class="" placeholder="Actions">
                                <option value="">All Actions</option>
                                <option value="GET">GET</option>
                                <option value="POST">POST</option>
                                <option value="PATCH">PATCH</option>
                                <option value="DELETE">DELETE</option>
                                <option value="VIEW">VIEW</option>
                            </select></div>
                    </div>
                </div>

                {{-- Permissions Grid (ACCORDION STYLE) --}}
                <div id="permission-grid" class="flex-1 overflow-y-auto custom-scrollbar p-5 space-y-4">
                    @php
                        $groupedPermissions = $permissions->groupBy(function ($item) {
                            return $item->permission_group ? $item->permission_group->name : 'Ungrouped Permissions';
                        });
                    @endphp

                    @foreach ($groupedPermissions as $groupName => $perms)
                        @php
                            $groupId = 'group-' . Str::slug($groupName) . '-' . $loop->index;
                            $firstPermId = $perms->first()->permission_group_id ?? 'ungrouped';
                        @endphp

                        <div class="permission-group-section border border-gray-200 rounded-xl overflow-hidden bg-white shadow-sm hover:shadow-md transition-shadow duration-300"
                            data-group-id="{{ $firstPermId }}">

                            {{-- Accordion Header --}}
                            <button onclick="toggleAccordion('{{ $groupId }}')"
                                class="w-full flex items-center justify-between p-4 bg-gray-50 hover:bg-indigo-50/50 transition-colors text-left group focus:outline-none">
                                <div class="flex items-center gap-3">
                                    <span
                                        class="w-1.5 h-1.5 rounded-full {{ $groupName === 'Ungrouped Permissions' ? 'bg-gray-400' : 'bg-indigo-500' }}"></span>
                                    <h3
                                        class="text-sm font-bold text-gray-700 uppercase tracking-wide group-hover:text-indigo-700 transition-colors">
                                        {{ $groupName }}
                                    </h3>
                                    <span
                                        class="bg-gray-200 text-gray-600 py-0.5 px-2 rounded text-[10px] font-bold group-hover:bg-indigo-100 group-hover:text-indigo-600 transition-colors">
                                        {{ $perms->count() }}
                                    </span>
                                </div>
                                <div class="transform transition-transform duration-300" id="icon-{{ $groupId }}">
                                    <svg class="w-5 h-5 text-gray-400 group-hover:text-indigo-500" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </button>

                            {{-- Accordion Content --}}
                            <div id="{{ $groupId }}" class="accordion-content hidden">
                                <div class="p-4 bg-white border-t border-gray-100">
                                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-2 2xl:grid-cols-3 gap-4">
                                        @foreach ($perms as $index => $perm)
                                            <div class="permission-card relative flex items-start justify-between p-4 bg-white border border-gray-200 rounded-xl hover:border-indigo-300 gap-3 fade-in-up"
                                                style="animation-delay: {{ $index * 30 }}ms"
                                                data-id="{{ $perm->id }}" data-name="{{ strtolower($perm->name) }}"
                                                data-route="{{ strtolower($perm->route) }}"
                                                data-action="{{ $perm->action }}"
                                                data-group-id="{{ $perm->permission_group_id }}">

                                                <div class="flex-1 min-w-0 flex flex-col gap-2">
                                                    <div>
                                                        <p class="text-sm font-bold text-gray-800 break-words leading-tight"
                                                            title="{{ $perm->name }}">
                                                            {{ $perm->name }}
                                                        </p>
                                                        <div class="flex items-center flex-wrap gap-2 mt-2">
                                                            @php
                                                                $badgeColor = match ($perm->action) {
                                                                    'GET',
                                                                    'VIEW'
                                                                        => 'bg-blue-50 text-blue-700 border-blue-100',
                                                                    'POST'
                                                                        => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                                                    'PATCH',
                                                                    'PUT'
                                                                        => 'bg-amber-50 text-amber-700 border-amber-100',
                                                                    'DELETE'
                                                                        => 'bg-rose-50 text-rose-700 border-rose-100',
                                                                    default
                                                                        => 'bg-gray-50 text-gray-700 border-gray-100',
                                                                };
                                                            @endphp
                                                            <span
                                                                class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $badgeColor }}">
                                                                {{ $perm->action }}
                                                            </span>
                                                            <code
                                                                class="text-[10px] text-gray-500 bg-gray-50 border border-gray-200 px-1.5 py-0.5 rounded break-all font-mono"
                                                                title="{{ $perm->route }}">
                                                                {{ $perm->route }}
                                                            </code>
                                                        </div>
                                                    </div>
                                                    {{-- Footer for Override/Inherited Badge --}}
                                                    <div
                                                        class="permission-footer flex items-center gap-2 mt-auto pt-2 empty:hidden">
                                                    </div>
                                                </div>

                                                <div class="toggle-wrapper self-start mt-0.5 shrink-0 z-10">
                                                    <div
                                                        class="relative inline-block w-10 align-middle select-none transition duration-200 ease-in toggle-container h-5">
                                                        <input type="checkbox" name="toggle"
                                                            id="toggle-{{ $perm->id }}"
                                                            class="toggle-checkbox block w-5 h-5 rounded-full bg-white border-4 appearance-none cursor-pointer transition-all duration-300 top-0 shadow-sm"
                                                            onclick="togglePermission(this, '{{ $perm->id }}')" />
                                                        <label for="toggle-{{ $perm->id }}"
                                                            class="toggle-label block overflow-hidden h-5 rounded-full bg-gray-200 cursor-pointer transition-colors duration-300"></label>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div id="no-permissions-found" class="hidden text-center py-12 text-gray-400">
                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-8 h-8 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <p class="font-medium">No permissions match your filter.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        // State Global
        let currentModelType = 'ALL';
        let selectedModelId = null;
        let selectedModelType = null;
        let tomSelectUnit, tomSelectPermission, tomSelectGroup, tomSelectAction;

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('permissions').classList.add('bg-slate-100');
            document.getElementById('permissions').classList.add('active');

            // Setup TomSelect
            const tomSelectConfig = {
                create: false,
                plugins: ['remove_button'],
                onDropdownOpen: function() {
                    this.dropdown.classList.add('fade-in-up');
                }
            };

            tomSelectUnit = new TomSelect('#filter-unit-select', {
                ...tomSelectConfig,
                maxItems: 1,
                onChange: filterList
            });
            tomSelectPermission = new TomSelect('#filter-has-permission', {
                ...tomSelectConfig,
                maxItems: null,
                onChange: filterList
            });
            tomSelectGroup = new TomSelect('#filter-perm-group', {
                ...tomSelectConfig,
                onChange: filterPermissions
            });
            tomSelectAction = new TomSelect('#filter-perm-action', {
                ...tomSelectConfig,
                maxItems: null,
                onChange: filterPermissions
            });

            document.getElementById('search-model').addEventListener('input', filterList);
            document.getElementById('search-permission').addEventListener('input', filterPermissions);

            updateTypeButtons();
            setupModelClickListeners();

            // Default Open First Accordion (Optional, remove if you want all closed)
            const groupBtn = document.querySelectorAll('.permission-group-section button');
            groupBtn.forEach(element => {
                element.click();
            });
        });

        // --- ACCORDION LOGIC ---
        function toggleAccordion(id) {
            const content = document.getElementById(id);
            const icon = document.getElementById('icon-' + id);

            if (content.classList.contains('hidden')) {
                // Open
                content.classList.remove('hidden');
                icon.classList.add('rotate-180');
            } else {
                // Close
                content.classList.add('hidden');
                icon.classList.remove('rotate-180');
            }
        }

        // Helper to force open accordion (used in search)
        function openAccordion(id) {
            const content = document.getElementById(id);
            const icon = document.getElementById('icon-' + id);
            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                icon.classList.add('rotate-180');
            }
        }

        // --- FILTERING MODELS ---
        function filterModelType(type) {
            currentModelType = type;
            updateTypeButtons();
            filterList();
        }

        function updateTypeButtons() {
            const btns = ['all', 'user', 'unit'];
            btns.forEach(t => {
                const btn = document.getElementById(`btn-type-${t}`);
                if (currentModelType === t.toUpperCase()) {
                    btn.classList.remove('bg-transparent', 'text-gray-500', 'hover:bg-white/60');
                    btn.classList.add('bg-white', 'text-gray-800', 'shadow-sm', 'font-bold');
                } else {
                    btn.classList.add('bg-transparent', 'text-gray-500', 'hover:bg-white/60');
                    btn.classList.remove('bg-white', 'text-gray-800', 'shadow-sm', 'font-bold');
                }
            });
        }

        function filterList() {
            const searchVal = document.getElementById('search-model').value.toLowerCase();
            const unitVal = tomSelectUnit.getValue();
            const permVals = tomSelectPermission.getValue();
            const items = document.querySelectorAll('.model-item');
            let visibleCount = 0;

            items.forEach(item => {
                const type = item.dataset.type;
                const name = item.dataset.name;
                const email = item.dataset.email;
                const unitId = item.dataset.unitId;

                let allPermissions = [];
                if (type === 'USER') {
                    const directPerms = JSON.parse(item.dataset.directPermissions || '[]');
                    const unitPerms = JSON.parse(item.dataset.unitPermissions || '[]');
                    allPermissions = [...new Set([...directPerms, ...unitPerms])];
                } else {
                    allPermissions = JSON.parse(item.dataset.directPermissions || '[]');
                }

                let isVisible = true;
                if (currentModelType !== 'ALL' && type !== currentModelType) isVisible = false;
                if (isVisible && searchVal) {
                    if (!name.includes(searchVal) && !email.includes(searchVal)) isVisible = false;
                }
                if (isVisible && unitVal && unitId !== unitVal) isVisible = false;

                // Logic AND: Must have ALL selected permissions
                if (isVisible && permVals.length > 0) {
                    const hasAllPermissions = permVals.every(filterId => allPermissions.includes(filterId));
                    if (!hasAllPermissions) isVisible = false;
                }

                if (isVisible) {
                    item.classList.remove('hidden');
                    visibleCount++;
                } else {
                    item.classList.add('hidden');
                }
            });

            const noResult = document.getElementById('no-models-found');
            visibleCount === 0 ? noResult.classList.remove('hidden') : noResult.classList.add('hidden');
        }

        function setupModelClickListeners() {
            const items = document.querySelectorAll('.model-item');
            items.forEach(item => {
                item.addEventListener('click', function() {
                    items.forEach(i => i.classList.remove('active'));
                    this.classList.add('active');

                    selectedModelId = this.dataset.id;
                    selectedModelType = this.dataset.type;
                    const name = this.querySelector('p.text-sm').innerText; // Corrected selector

                    let directPerms = JSON.parse(this.dataset.directPermissions || '[]');
                    let unitPerms = [];
                    let unitName = '';

                    if (selectedModelType === 'USER') {
                        unitPerms = JSON.parse(this.dataset.unitPermissions || '[]');
                        unitName = this.dataset.unitName;
                    }

                    selectModel(name, selectedModelType, directPerms, unitPerms, unitName);
                });
            });
        }

        // --- RENDER LOGIC ---
        function selectModel(name, type, directPerms, unitPerms = [], unitName = '') {
            document.getElementById('permission-overlay').classList.add('hidden');
            document.getElementById('selected-model-name').textContent = name;
            const badge = document.getElementById('selected-model-badge');
            badge.textContent = type;
            badge.className = `ml-2 px-2.5 py-0.5 rounded-lg text-[10px] font-bold uppercase tracking-wide border shadow-sm ${
                type === 'USER' 
                ? 'bg-indigo-50 text-indigo-700 border-indigo-100' 
                : 'bg-emerald-50 text-emerald-700 border-emerald-100'
            }`;
            badge.classList.remove('hidden');

            const cards = document.querySelectorAll('.permission-card');

            cards.forEach(card => {
                const permId = card.dataset.id;
                const cb = card.querySelector('.toggle-checkbox');
                const footer = card.querySelector('.permission-footer');

                // Reset State
                cb.checked = false;
                cb.disabled = false;
                footer.innerHTML = '';

                if (type === 'UNIT') {
                    if (directPerms.includes(permId)) cb.checked = true;
                } else if (type === 'USER') {
                    const hasInherited = unitPerms.includes(permId);
                    const hasDirect = directPerms.includes(permId);

                    if (hasInherited && hasDirect) {
                        // DOUBLE PERMISSION
                        cb.checked = true;
                        cb.disabled = false;
                        footer.innerHTML = `
                            <div class="inherited-badge bg-purple-100 border border-purple-200 text-purple-700 shadow-sm" title="Permission Ganda">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                <span>Direct + Unit</span>
                            </div>`;
                    } else if (hasInherited) {
                        // INHERITED ONLY (LOCKED)
                        cb.checked = true;
                        cb.disabled = true;
                        footer.innerHTML = `
                            <button type="button" onclick="forceGrantPermission('${permId}')" class="override-btn shadow-sm" title="Override: Add direct permission">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                            </button>
                            <div class="inherited-badge bg-slate-100 border border-slate-200 text-slate-600 shadow-sm" title="Inherited from ${unitName}">
                                <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                <span class="truncate max-w-[100px]">${unitName}</span>
                            </div>`;
                    } else if (hasDirect) {
                        // DIRECT ONLY
                        cb.checked = true;
                    }
                }
            });
        }

        function filterPermissions() {
            const searchVal = document.getElementById('search-permission').value.toLowerCase();
            const groupVal = tomSelectGroup.getValue();
            const actionVals = tomSelectAction.getValue();

            const cards = document.querySelectorAll('.permission-card');
            let visibleCount = 0;

            cards.forEach(card => {
                const name = card.dataset.name;
                const route = card.dataset.route;
                const groupId = card.dataset.groupId;
                const action = card.dataset.action;

                let isVisible = true;

                // Filter Logic
                if (searchVal && !name.includes(searchVal) && !route.includes(searchVal)) isVisible = false;
                if (groupVal && groupId !== groupVal) isVisible = false;
                if (actionVals.length > 0 && !actionVals.includes(action)) isVisible = false;

                if (isVisible) {
                    card.parentElement.classList.remove('hidden'); // Show wrapper if exists
                    card.classList.remove('hidden');
                    visibleCount++;

                    // --- SMART ACCORDION ---
                    // Jika user mencari sesuatu dan ketemu di dalam grup,
                    // Pastikan accordion parent-nya terbuka otomatis!
                    if (searchVal) {
                        const contentDiv = card.closest('.accordion-content');
                        if (contentDiv) {
                            openAccordion(contentDiv.id);
                        }
                    }
                } else {
                    card.classList.add('hidden');
                }
            });

            // Hide Empty Groups
            document.querySelectorAll('.permission-group-section').forEach(section => {
                const visibleCards = section.querySelectorAll('.permission-card:not(.hidden)');
                if (visibleCards.length === 0) {
                    section.classList.add('hidden');
                } else {
                    section.classList.remove('hidden');
                }
            });

            const noResult = document.getElementById('no-permissions-found');
            visibleCount === 0 ? noResult.classList.remove('hidden') : noResult.classList.add('hidden');
        }

        // --- AJAX ACTIONS ---
        async function togglePermission(checkbox, permissionId) {
            await processPermissionUpdate(permissionId, checkbox.checked, checkbox);
        }

        async function forceGrantPermission(permissionId) {
            await processPermissionUpdate(permissionId, true, null);
        }

        async function processPermissionUpdate(permissionId, isAdding, checkboxEl) {
            if (!selectedModelId || !selectedModelType) return;
            try {
                showLoadingToast('Updating Permission...');
                const response = await fetch("{{ route('admin.permissions.update') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        model_type: selectedModelType,
                        model_id: selectedModelId,
                        permission_id: permissionId
                    })
                });

                const data = await response.json();

                if (data.success) {
                    showToast('Success', data.message || 'Permission updated successfully', 'success');
                    // Update Local State without Reloading
                    const activeItem = document.querySelector(`.model-item[data-id="${selectedModelId}"]`);
                    if (activeItem) {
                        let directPerms = JSON.parse(activeItem.dataset.directPermissions || '[]');

                        if (isAdding) {
                            if (!directPerms.includes(permissionId)) directPerms.push(permissionId);
                        } else {
                            directPerms = directPerms.filter(id => id !== permissionId);
                        }

                        // Update DOM Data Attribute
                        activeItem.dataset.directPermissions = JSON.stringify(directPerms);

                        // Re-render Selection to update UI (Badges/Checks)
                        const name = activeItem.querySelector('p.text-sm').innerText;
                        let unitPerms = [];
                        let unitName = '';
                        if (selectedModelType === 'USER') {
                            unitPerms = JSON.parse(activeItem.dataset.unitPermissions || '[]');
                            unitName = activeItem.dataset.unitName;
                        }
                        selectModel(name, selectedModelType, directPerms, unitPerms, unitName);
                    }
                } else {
                    throw new Error(data.message || 'Update failed');
                }
            } catch (error) {
                if (checkboxEl) checkboxEl.checked = !isAdding; // Revert checkbox
                showToast('Error', error.message, 'error');
            }
        }
    </script>
@endsection
