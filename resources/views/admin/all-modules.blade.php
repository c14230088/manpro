@extends('layouts.admin')

@section('title', 'All Modules')

@section('body')
    <div class="flex flex-col md:flex-row w-full py-4 shadow-md items-center justify-between mb-5 px-6 md:px-4">
        <h1 class="text-center text-4xl uppercase font-bold mb-2 md:mb-0">Modules</h1>
        <div class="flex gap-2">
            <button id="btn-view-table" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                Table View
            </button>
            <button id="btn-view-repository" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-100">
                Repository View
            </button>
        </div>
    </div>

    {{-- Table View --}}
    <div id="table-view" class="max-w-7xl mx-auto px-6 pb-12">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6">
                <table id="modules-table" class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Matkul</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">File</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Author</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Workload</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Last Edited</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($modules as $module)
                            <tr>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $module->matkul->nama }}</td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $module->file->original_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $module->file->folder->full_path ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $module->author->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $module->workload_hours }}h</td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $module->last_edited_at->format('d/m/Y H:i') }}
                                    </div>
                                    <div class="text-xs text-gray-500">by {{ $module->lastEditor->name }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $module->active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $module->active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button
                                        class="btn-view-detail-all px-3 py-1.5 bg-indigo-500 text-white hover:bg-indigo-600 text-xs font-semibold rounded-md"
                                        data-matkul-id="{{ $module->matkul_id }}" data-id="{{ $module->id }}">
                                        Details
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">No modules found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Repository View --}}
    <div id="repository-view" class="hidden max-w-7xl mx-auto px-6 pb-12">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div id="modules-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @forelse($modules as $module)
                    <div class="group relative p-4 border-2 border-gray-200 rounded-lg hover:shadow-lg hover:border-indigo-400 transition-all cursor-pointer"
                        data-matkul-id="{{ $module->matkul_id }}" data-id="{{ $module->id }}"
                        onclick="handleModuleClick(this)">
                        <div class="flex flex-col items-center">
                            <svg class="w-16 h-16 text-blue-500 mb-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <p class="text-sm text-center text-gray-800 font-medium truncate w-full"
                                title="{{ $module->file->original_name }}">
                                {{ $module->file->original_name }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">{{ $module->matkul->nama }}</p>
                            <span
                                class="mt-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $module->active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $module->active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12 text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                            </path>
                        </svg>
                        <p class="mt-2">No modules found</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('module').classList.add('bg-slate-100');
            document.getElementById('module').classList.add('active');

            const tableView = document.getElementById('table-view');
            const repositoryView = document.getElementById('repository-view');
            const btnTable = document.getElementById('btn-view-table');
            const btnRepository = document.getElementById('btn-view-repository');

            btnTable.addEventListener('click', () => {
                tableView.classList.remove('hidden');
                repositoryView.classList.add('hidden');
                btnTable.classList.add('bg-indigo-600', 'text-white');
                btnTable.classList.remove('bg-white', 'border', 'border-gray-300');
                btnRepository.classList.remove('bg-indigo-600', 'text-white');
                btnRepository.classList.add('bg-white', 'border', 'border-gray-300');
            });

            btnRepository.addEventListener('click', () => {
                tableView.classList.add('hidden');
                repositoryView.classList.remove('hidden');
                btnRepository.classList.add('bg-indigo-600', 'text-white');
                btnRepository.classList.remove('bg-white', 'border', 'border-gray-300');
                btnTable.classList.remove('bg-indigo-600', 'text-white');
                btnTable.classList.add('bg-white', 'border', 'border-gray-300');
            });

            document.body.addEventListener('click', async (e) => {
                if (e.target.closest('.btn-view-detail-all')) {
                    const btn = e.target.closest('.btn-view-detail-all');
                    const matkulId = btn.dataset.matkulId;
                    const moduleId = btn.dataset.id;

                    try {
                        showLoading('Loading details...');
                        const response = await fetch(
                            `/admin/matkul/${matkulId}/modules/${moduleId}/details`);
                        const data = await response.json();
                        Swal.close();

                        if (data.success) {
                            showModuleDetailsModal(data);
                        } else {
                            throw new Error('Failed to load details');
                        }
                    } catch (error) {
                        Swal.fire('Error', error.message, 'error');
                    }
                }
            });
        });

        function showModuleDetailsModal(data) {
            const matkulId = data.active.matkul_id;
            const moduleId = data.active.id;

            const downloadBtn = `<a href="/admin/repository/file/${data.active.file_id}/download" 
                class="px-3 py-1.5 bg-blue-500 text-white hover:bg-blue-600 text-xs rounded-md"
                onclick="showLoading('Downloading...')">Download</a>`;

            const editBtn = `<button onclick="editModuleAll('${matkulId}', '${moduleId}', ${data.active.workload_hours}, ${data.active.active ? 1 : 0})" 
                class="px-3 py-1.5 bg-yellow-500 text-white hover:bg-yellow-600 text-xs rounded-md">Edit</button>`;

            const deleteBtn = `<button onclick="deleteModuleAll('${matkulId}', '${moduleId}')" 
                class="px-3 py-1.5 bg-red-600 text-white hover:bg-red-700 text-xs rounded-md">Delete</button>`;

            let olderHtml = '<p class="text-gray-500 text-sm">No older versions</p>';
            if (data.older.length > 0) {
                olderHtml = data.older.map(m => `
                    <div class="bg-white border border-gray-200 rounded-lg p-3 mb-2 text-sm">
                        <p><strong>File:</strong> ${m.file.original_name}</p>
                        <p><strong>Workload:</strong> ${m.workload_hours}h</p>
                        <p><strong>Last Edited:</strong> ${new Date(m.last_edited_at).toLocaleString('id-ID')}</p>
                        <a href="/admin/repository/file/${m.file_id}/download" 
                           class="inline-block mt-2 px-3 py-1.5 bg-blue-500 text-white hover:bg-blue-600 text-xs rounded-md"
                           onclick="showLoading('Downloading...')">Download</a>
                    </div>
                `).join('');
            }

            let deletedHtml = '<p class="text-gray-500 text-sm">No deleted versions</p>';
            if (data.deleted.length > 0) {
                deletedHtml = data.deleted.map(m => `
                    <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-2 text-sm">
                        <p><strong>File:</strong> ${m.file.original_name}</p>
                        <p><strong>Deleted At:</strong> ${new Date(m.deleted_at).toLocaleString('id-ID')}</p>
                        <p><strong>Deleted By:</strong> ${m.deletor?.name || 'Unknown'}</p>
                    </div>
                `).join('');
            }

            Swal.fire({
                title: 'Module Details',
                html: `
                    <div class="text-left space-y-4">
                        <div class="bg-green-50 p-4 rounded-lg">
                            <h4 class="font-semibold text-green-600 mb-2">Active Version</h4>
                            <div class="text-sm space-y-1">
                                <p><strong>Matkul:</strong> ${data.active.matkul?.nama || 'N/A'}</p>
                                <p><strong>File:</strong> ${data.active.file.original_name}</p>
                                <p><strong>Workload:</strong> ${data.active.workload_hours}h</p>
                                <p><strong>Author:</strong> ${data.active.author.name}</p>
                            </div>
                            <div class="mt-3 flex gap-2">${downloadBtn} ${editBtn} ${deleteBtn}</div>
                        </div>
                        <button onclick="toggleVersions()" class="w-full px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                            Show Older & Deleted Versions
                        </button>
                        <div id="older-versions" class="hidden">
                            <h4 class="font-semibold text-blue-600 mb-2">Older Versions</h4>
                            ${olderHtml}
                        </div>
                        <div id="deleted-versions" class="hidden">
                            <h4 class="font-semibold text-red-600 mb-2">Deleted Versions</h4>
                            ${deletedHtml}
                        </div>
                    </div>
                `,
                width: '700px',
                showConfirmButton: false,
                showCloseButton: true
            });
        }

        async function handleModuleClick(element) {
            const matkulId = element.dataset.matkulId;
            const moduleId = element.dataset.id;

            try {
                showLoading('Loading details...');
                const response = await fetch(`/admin/matkul/${matkulId}/modules/${moduleId}/details`);
                const data = await response.json();
                Swal.close();

                if (data.success) {
                    showModuleDetailsModal(data);
                } else {
                    throw new Error('Failed to load details');
                }
            } catch (error) {
                Swal.fire('Error', error.message, 'error');
            }
        }

        function toggleVersions() {
            const older = document.getElementById('older-versions');
            const deleted = document.getElementById('deleted-versions');
            older.classList.toggle('hidden');
            deleted.classList.toggle('hidden');
        }

        async function editModuleAll(matkulId, moduleId, workload, active) {
            const {
                value: formValues
            } = await Swal.fire({
                title: 'Edit Module',
                html: `
                    <div class="text-left space-y-3">
                        <div>
                            <label class="block text-sm font-semibold mb-1">Workload (Hours)</label>
                            <input id="edit-workload" type="number" value="${workload}" min="0" class="w-full px-3 py-2 border rounded-lg">
                        </div>
                        <div>
                            <label class="flex items-center">
                                <input id="edit-active" type="checkbox" ${active ? 'checked' : ''} class="rounded">
                                <span class="ml-2 text-sm">Active</span>
                            </label>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Save',
                preConfirm: () => {
                    return {
                        workload_hours: document.getElementById('edit-workload').value,
                        active: document.getElementById('edit-active').checked
                    };
                }
            });

            if (formValues) {
                try {
                    showLoading('Updating...');
                    const response = await fetch(`/admin/matkul/${matkulId}/modules/${moduleId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(formValues)
                    });

                    const result = await response.json();
                    if (response.ok) {
                        Swal.fire('Success!', 'Module updated', 'success').then(() => location.reload());
                    } else {
                        throw new Error(result.message || 'Update failed');
                    }
                } catch (error) {
                    Swal.fire('Error', error.message, 'error');
                }
            }
        }

        async function deleteModuleAll(matkulId, moduleId) {
            const result = await Swal.fire({
                title: 'Delete Module?',
                text: 'Are you sure?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Yes, delete!'
            });

            if (result.isConfirmed) {
                try {
                    showLoading('Deleting...');
                    const response = await fetch(`/admin/matkul/${matkulId}/modules/${moduleId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    if (response.ok) {
                        Swal.fire('Deleted!', 'Module deleted', 'success').then(() => location.reload());
                    } else {
                        throw new Error('Delete failed');
                    }
                } catch (error) {
                    Swal.fire('Error', error.message, 'error');
                }
            }
        }
    </script>
@endsection
