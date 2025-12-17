@extends('layouts.admin')

@section('title', 'Modules - ' . $matkul->nama)

@section('body')
    <div class="flex flex-col md:flex-row w-full py-4 shadow-md items-center justify-between mb-5 px-6 md:px-4">
        <div>
            <a href="{{ route('admin.matkul') }}" class="text-indigo-600 hover:text-indigo-800 text-sm mb-2 inline-block">
                ← Back to Matkul
            </a>
            <h1 class="text-center text-4xl uppercase font-bold">Modules - {{ $matkul->nama }}</h1>
        </div>
        <button id="btn-add-module" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
            + Add Module
        </button>
    </div>

    <div class="max-w-7xl mx-auto px-6 pb-12">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Module List</h2>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table id="modules-table" class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">File</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Author</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Workload (Hours)</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Last Edited</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($modules as $module)
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $module->file->original_name }}</div>
                                        <div class="text-xs text-gray-500">{{ $module->file->folder->full_path ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ $module->author->name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ $module->workload_hours }}</td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">{{ $module->last_edited_at->format('d/m/Y H:i') }}</div>
                                        <div class="text-xs text-gray-500">by {{ $module->lastEditor->name }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $module->active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $module->active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <button class="btn-view-detail px-3 py-1.5 bg-indigo-500 text-white hover:bg-indigo-600 text-xs font-semibold rounded-md mr-2"
                                            data-id="{{ $module->id }}">
                                            Details
                                        </button>
                                        <button class="btn-edit-module px-3 py-1.5 bg-yellow-500 text-white hover:bg-yellow-600 text-xs font-semibold rounded-md mr-2"
                                            data-id="{{ $module->id }}" data-file-id="{{ $module->file_id }}" 
                                            data-workload="{{ $module->workload_hours }}" data-active="{{ $module->active ? '1' : '0' }}">
                                            Edit
                                        </button>
                                        <button class="btn-delete-module px-3 py-1.5 bg-red-600 text-white hover:bg-red-700 text-xs font-semibold rounded-md"
                                            data-id="{{ $module->id }}">
                                            Delete
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
    </div>

    {{-- Modal Add/Edit Module --}}
    <div id="module-modal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-75 z-[3000] flex items-center justify-center p-4" onclick="if(event.target === this) this.classList.add('hidden')">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
            <div class="flex justify-between items-center p-4 border-b">
                <h3 id="modal-title" class="text-lg font-semibold">Add Module</h3>
                <button id="close-modal" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form id="module-form" class="p-6 space-y-4">
                <input type="hidden" id="module-id">
                
                <div id="file-field">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">File from Modules Folder</label>
                    <select id="module-file" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <option value="">Select file...</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Workload (Hours)</label>
                    <input type="number" id="module-workload" min="0" value="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>

                <div id="active-field" class="hidden">
                    <label class="flex items-center">
                        <input type="checkbox" id="module-active" class="rounded text-indigo-600">
                        <span class="ml-2 text-sm text-gray-700">Active</span>
                    </label>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" id="cancel-modal" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Save</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Module Details --}}
    <div id="detail-modal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-75 z-[3000] flex items-center justify-center p-4" onclick="if(event.target === this) this.classList.add('hidden')">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-3xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center p-4 border-b sticky top-0 bg-white">
                <h3 class="text-lg font-semibold">Module Details</h3>
                <button id="close-detail-modal" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-6 space-y-6">
                {{-- Active Version --}}
                <div id="active-section">
                    <h4 class="text-md font-semibold text-green-600 mb-3">Active Version</h4>
                    <div id="active-content" class="bg-green-50 p-4 rounded-lg"></div>
                </div>

                {{-- Toggle Button --}}
                <button id="toggle-versions" class="w-full px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    Show Older & Deleted Versions
                </button>

                {{-- Older Versions --}}
                <div id="older-section" class="hidden">
                    <h4 class="text-md font-semibold text-blue-600 mb-3">Older Versions</h4>
                    <div id="older-content"></div>
                </div>

                {{-- Deleted Versions --}}
                <div id="deleted-section" class="hidden">
                    <h4 class="text-md font-semibold text-red-600 mb-3">Deleted Versions</h4>
                    <div id="deleted-content"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <style>
        .ts-control {
            @apply block w-full px-4 py-3 text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white;
            padding-top: 0.6rem;
            padding-bottom: 0.6rem;
        }
    </style>

    <script>
        const matkulId = "{{ $matkul->id }}";
        const modulesFolderId = "{{ $modulesFolder?->id }}";
        let isEditMode = false;
        let fileSelect;

        document.addEventListener('DOMContentLoaded', async function() {
            await loadModulesFiles();
            
            fileSelect = new TomSelect('#module-file', {
                placeholder: 'Select file...',
                allowEmptyOption: true
            });

            const modal = document.getElementById('module-modal');
            const closeModal = () => modal.classList.add('hidden');

            document.getElementById('btn-add-module').addEventListener('click', () => {
                isEditMode = false;
                document.getElementById('modal-title').textContent = 'Add Module';
                document.getElementById('module-form').reset();
                document.getElementById('module-id').value = '';
                document.getElementById('active-field').classList.add('hidden');
                document.getElementById('file-field').style.display = 'block';
                modal.classList.remove('hidden');
            });

            document.getElementById('close-modal').addEventListener('click', closeModal);
            document.getElementById('cancel-modal').addEventListener('click', closeModal);

            document.body.addEventListener('click', (e) => {
                if (e.target.closest('.btn-edit-module')) {
                    const btn = e.target.closest('.btn-edit-module');
                    isEditMode = true;
                    document.getElementById('modal-title').textContent = 'Edit Module';
                    document.getElementById('module-id').value = btn.dataset.id;
                    document.getElementById('module-file').value = btn.dataset.fileId;
                    document.getElementById('module-workload').value = btn.dataset.workload;
                    document.getElementById('module-active').checked = btn.dataset.active === '1';
                    document.getElementById('active-field').classList.remove('hidden');
                    document.getElementById('file-field').style.display = 'none';
                    modal.classList.remove('hidden');
                }

                if (e.target.closest('.btn-view-detail')) {
                    const btn = e.target.closest('.btn-view-detail');
                    showModuleDetails(btn.dataset.id);
                }

                if (e.target.closest('.btn-delete-module')) {
                    const btn = e.target.closest('.btn-delete-module');
                    Swal.fire({
                        title: 'Delete Module?',
                        text: 'Are you sure?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete!'
                    }).then(async (result) => {
                        if (result.isConfirmed) {
                            await deleteModule(btn.dataset.id);
                        }
                    });
                }
            });

            document.getElementById('close-detail-modal')?.addEventListener('click', () => {
                document.getElementById('detail-modal').classList.add('hidden');
            });

            document.getElementById('toggle-versions')?.addEventListener('click', function() {
                const olderSection = document.getElementById('older-section');
                const deletedSection = document.getElementById('deleted-section');
                const isHidden = olderSection.classList.contains('hidden');
                
                if (isHidden) {
                    olderSection.classList.remove('hidden');
                    deletedSection.classList.remove('hidden');
                    this.textContent = 'Hide Older & Deleted Versions';
                } else {
                    olderSection.classList.add('hidden');
                    deletedSection.classList.add('hidden');
                    this.textContent = 'Show Older & Deleted Versions';
                }
            });

            document.getElementById('module-form').addEventListener('submit', async (e) => {
                e.preventDefault();
                const data = {
                    workload_hours: document.getElementById('module-workload').value,
                    active: document.getElementById('module-active').checked
                };
                
                if (!isEditMode) {
                    data.file_id = document.getElementById('module-file').value;
                }

                const id = document.getElementById('module-id').value;
                const url = isEditMode ? `/admin/matkul/${matkulId}/modules/${id}` : `/admin/matkul/${matkulId}/modules`;
                const method = isEditMode ? 'PUT' : 'POST';

                try {
                    showLoading(isEditMode ? 'Updating...' : 'Creating...');
                    const response = await fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(data)
                    });

                    const result = await response.json();
                    if (response.ok) {
                        Swal.fire('Success!', `Module ${isEditMode ? 'updated' : 'added'}`, 'success')
                            .then(() => location.reload());
                    } else {
                        throw new Error(result.message || 'Failed');
                    }
                } catch (error) {
                    Swal.fire('Error', error.message, 'error');
                }
            });
        });

        async function loadModulesFiles() {
            if (!modulesFolderId) return;
            
            try {
                const response = await fetch(`/admin/repository/folder/${modulesFolderId}/files`);
                const data = await response.json();
                
                fileSelect.clear();
                fileSelect.clearOptions();
                
                data.files.forEach(file => {
                    fileSelect.addOption({value: file.id, text: file.original_name});
                });
            } catch (error) {
                console.error('Error loading files:', error);
            }
        }

        async function deleteModule(id) {
            try {
                showLoading('Deleting...');
                const response = await fetch(`/admin/matkul/${matkulId}/modules/${id}`, {
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

        async function showModuleDetails(moduleId) {
            try {
                showLoading('Loading details...');
                const response = await fetch(`/admin/matkul/${matkulId}/modules/${moduleId}/details`);
                const data = await response.json();
                Swal.close();

                if (data.success) {
                    renderModuleDetails(data);
                    document.getElementById('detail-modal').classList.remove('hidden');
                } else {
                    throw new Error('Failed to load details');
                }
            } catch (error) {
                Swal.fire('Error', error.message, 'error');
            }
        }

        function renderModuleDetails(data) {
            const activeContent = document.getElementById('active-content');
            const olderContent = document.getElementById('older-content');
            const deletedContent = document.getElementById('deleted-content');

            // Active version
            activeContent.innerHTML = createModuleCard(data.active, true);

            // Older versions
            if (data.older.length > 0) {
                olderContent.innerHTML = data.older.map(m => createModuleCard(m, false)).join('');
            } else {
                olderContent.innerHTML = '<p class="text-gray-500 text-sm">No older versions</p>';
            }

            // Deleted versions
            if (data.deleted.length > 0) {
                deletedContent.innerHTML = data.deleted.map(m => createModuleCard(m, false, true)).join('');
            } else {
                deletedContent.innerHTML = '<p class="text-gray-500 text-sm">No deleted versions</p>';
            }

            // Reset toggle
            document.getElementById('older-section').classList.add('hidden');
            document.getElementById('deleted-section').classList.add('hidden');
            document.getElementById('toggle-versions').textContent = 'Show Older & Deleted Versions';
        }

        function createModuleCard(module, isActive, isDeleted = false) {
            const downloadBtn = isActive ? `
                <a href="/admin/repository/file/${module.file.id}/download" 
                   class="px-3 py-1.5 bg-blue-500 text-white hover:bg-blue-600 text-xs font-semibold rounded-md"
                   onclick="showLoading('Downloading...')">
                    Download File
                </a>
            ` : '';

            return `
                <div class="${isDeleted ? 'bg-red-50' : 'bg-white'} border border-gray-200 rounded-lg p-4 mb-3">
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div><span class="font-semibold">File:</span> ${module.file.original_name}</div>
                        <div><span class="font-semibold">Location:</span> ${module.file.folder?.full_path || '-'}</div>
                        <div><span class="font-semibold">Author:</span> ${module.author.name}</div>
                        <div><span class="font-semibold">Workload:</span> ${module.workload_hours}h</div>
                        <div><span class="font-semibold">Last Edited:</span> ${new Date(module.last_edited_at).toLocaleString('id-ID')}</div>
                        <div><span class="font-semibold">By:</span> ${module.last_editor.name}</div>
                        ${isDeleted ? `<div class="col-span-2"><span class="font-semibold">Deleted At:</span> ${new Date(module.deleted_at).toLocaleString('id-ID')}</div>` : ''}
                    </div>
                    ${downloadBtn ? `<div class="mt-3">${downloadBtn}</div>` : ''}
                </div>
            `;
        }
    </script>
@endsection
