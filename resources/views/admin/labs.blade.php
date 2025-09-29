@extends('layouts.admin')

@section('title', 'Laboratories Mapping')

@section('body')
    <div class="flex flex-col w-full py-4 shadow-md items-center justify-center mb-5">
        <h1 class="text-center text-4xl uppercase font-bold mb-2">Laboratories</h1>
    </div>

    <div class="max-w-7xl mx-auto px-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <label for="lab-selector" class="block text-sm font-semibold text-gray-700 mb-2">
                Pilih Laboratorium
            </label>
            <div class="relative">
                <select id="lab-selector"
                    class="block w-full md:w-2/3 lg:w-1/2 px-4 py-3 text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200 appearance-none bg-white cursor-pointer">
                    <option value="" selected disabled>-- Pilih Laboratorium --</option>
                    @foreach ($labs as $lab)
                        <option value="{{ $lab->id }}">{{ $lab->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div id="desk-grid-container" class="mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                        </path>
                    </svg>
                    <p class="text-gray-500 text-lg">Silakan pilih laboratorium untuk menampilkan denah meja</p>
                </div>
            </div>
        </div>

        <div id="item-details-container" class="hidden mb-8">
        </div>
    </div>

@endsection

@section('script')
    <script>
        document.getElementById('labs').classList.add('bg-slate-100');

        document.addEventListener('DOMContentLoaded', function() {
            const labSelector = document.getElementById('lab-selector');
            const deskContainer = document.getElementById('desk-grid-container');
            const itemDetailsContainer = document.getElementById('item-details-container');

            let desks = [];

            labSelector.addEventListener('change', async function() {
                const selectedLabId = this.value;
                if (!selectedLabId) return;

                itemDetailsContainer.classList.add('hidden');
                deskContainer.innerHTML = `
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                    <div class="flex items-center justify-center py-12">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mr-4"></div>
                        <p class="text-gray-600 text-lg">Memuat data meja...</p>
                    </div>
                </div>`;

                try {
                    const response = await fetch(`/admin/labs/${selectedLabId}/desks`);
                    if (!response.ok) throw new Error('Network response was not ok');

                    desks = await response.json();

                    if (desks.length === 0) {
                        deskContainer.innerHTML = `
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                            <div class="text-center py-12">
                                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                                <p class="text-gray-500 text-lg">Tidak ada data meja untuk laboratorium ini</p>
                            </div>
                        </div>`;
                        return;
                    }

                    let maxCols = 0;
                    desks.forEach(desk => {
                        const colNum = parseInt((desk.location.match(/\d+$/) || ['0'])[0], 10);
                        if (colNum > maxCols) maxCols = colNum;
                    });

                    let containerHTML = `
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                        <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
                            <h2 class="text-xl font-semibold text-gray-800">Denah Meja Laboratorium</h2>
                            <div class="flex items-center gap-4 text-sm">
                                <div class="flex items-center gap-2">
                                    <span class="w-4 h-4 bg-emerald-100 border-2 border-emerald-400 rounded"></span>
                                    <span class="text-gray-600">Bagus</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="w-4 h-4 bg-amber-100 border-2 border-amber-400 rounded"></span>
                                    <span class="text-gray-600">Tidak Lengkap</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="w-4 h-4 bg-rose-100 border-2 border-rose-400 rounded"></span>
                                    <span class="text-gray-600">Rusak</span>
                                </div>
                            </div>
                        </div>
                        <div id="desk-grid" class="grid gap-4" style="grid-template-columns: repeat(${maxCols}, minmax(0, 1fr));">`;

                    desks.forEach(desk => {
                        const colNum = parseInt((desk.location.match(/\d+$/) || ['0'])[0], 10);
                        if (colNum === 0) return;

                        let bgColorClass, iconColor, conditionText;
                        
                        switch (desk.overall_condition) {
                            case 'rusak':
                                bgColorClass = 'bg-rose-50 border-rose-300 hover:bg-rose-100';
                                iconColor = 'text-rose-600';
                                conditionText = 'Rusak';
                                break;
                            case 'tidak_lengkap':
                                bgColorClass = 'bg-amber-50 border-amber-300 hover:bg-amber-100';
                                iconColor = 'text-amber-600';
                                conditionText = 'Tidak Lengkap';
                                break;
                            default: // 'bagus'
                                bgColorClass = 'bg-emerald-50 border-emerald-300 hover:bg-emerald-100';
                                iconColor = 'text-emerald-600';
                                conditionText = 'Bagus';
                                break;
                        }

                        containerHTML += `
                        <div data-desk-id="${desk.id}" 
                             style="grid-column-start: ${colNum};" 
                             class="desk-item group cursor-pointer transition-all duration-300 ease-in-out hover:shadow-lg hover:scale-105 active:scale-95 flex flex-col items-center justify-center p-5 border-2 rounded-xl min-h-28 ${bgColorClass}">
                            <div class="text-center pointer-events-none">
                                <div class="mb-2">
                                    <svg class="w-8 h-8 mx-auto ${iconColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <span class="font-bold text-lg text-gray-800 block">${desk.location}</span>
                                <span class="text-sm text-gray-600 mt-1 inline-block">${conditionText}</span>
                            </div>
                        </div>`;
                    });

                    containerHTML += '</div></div>';
                    deskContainer.innerHTML = containerHTML;

                } catch (error) {
                    console.error('Error fetching desks:', error);
                    deskContainer.innerHTML = `
                    <div class="bg-white rounded-xl shadow-sm border border-red-200 p-8">
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 mx-auto text-red-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-red-600 text-lg font-medium">Terjadi kesalahan saat mengambil data</p>
                            <p class="text-gray-500 mt-2">Silakan coba lagi atau hubungi administrator</p>
                        </div>
                    </div>`;
                }
            });

            function renderAdditionalInfo(jsonString) {
                if (!jsonString) return '';
                try {
                    const info = JSON.parse(jsonString);
                    let infoHtml = '<div class="mt-3 space-y-1">';
                    for (const [key, value] of Object.entries(info)) {
                        const formattedKey = key.charAt(0).toUpperCase() + key.slice(1).replace(/_/g, ' ');
                        infoHtml += `
                        <div class="flex items-start gap-2 text-sm">
                            <span class="text-gray-500 min-w-fit">${formattedKey}:</span>
                            <span class="text-gray-700 font-medium">${value}</span>
                        </div>`;
                    }
                    infoHtml += '</div>';
                    return infoHtml;
                } catch (e) {
                    return `<p class="text-sm text-gray-600 mt-2">${jsonString}</p>`;
                }
            }

            deskContainer.addEventListener('click', function(event) {
                const clickedDeskElement = event.target.closest('.desk-item');
                if (!clickedDeskElement) return;

                document.querySelectorAll('.desk-item').forEach(el => {
                    el.classList.remove('ring-4', 'ring-indigo-400', 'ring-opacity-50');
                });
                clickedDeskElement.classList.add('ring-4', 'ring-indigo-400', 'ring-opacity-50');

                const deskId = clickedDeskElement.dataset.deskId;
                const selectedDesk = desks.find(d => d.id == deskId);

                if (selectedDesk) {
                    let detailsHTML = `
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 animate-fade-in">
                        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="bg-indigo-100 rounded-lg p-2">
                                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-2xl font-bold text-gray-800">Meja ${selectedDesk.location}</h3>
                                    <p class="text-sm text-gray-500">Detail inventaris dan komponen</p>
                                </div>
                            </div>
                        </div>`;

                    if (selectedDesk.items && selectedDesk.items.length > 0) {
                        detailsHTML += '<div class="space-y-4">';

                        selectedDesk.items.forEach((item, index) => {
                            const itemConditionText = item.condition == 1 ? 'Bagus' : 'Rusak';
                            const itemConditionClass = item.condition == 1 ?
                                'text-emerald-600 bg-emerald-50' : 'text-rose-600 bg-rose-50';
                            const itemBorderClass = item.condition == 1 ? 'border-emerald-200' :
                                'border-rose-200';

                            detailsHTML += `
                            <div class="bg-gradient-to-br from-gray-50 to-white p-6 rounded-xl border ${itemBorderClass} hover:shadow-md transition-all duration-200">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1">
                                        <h4 class="font-bold text-lg text-gray-800">${item.name}</h4>
                                        <p class="text-sm text-gray-500 font-mono mt-1">${item.serial_code}</p>
                                    </div>
                                    <span class="px-3 py-1 rounded-full text-sm font-semibold ${itemConditionClass}">
                                        ${itemConditionText}
                                    </span>
                                </div>`;

                            detailsHTML += renderAdditionalInfo(item.additional_information);

                            if (item.components && item.components.length > 0) {
                                detailsHTML += `
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <div class="flex items-center gap-2 mb-3">
                                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                        </svg>
                                        <p class="text-sm font-bold text-gray-700">Komponen (${item.components.length})</p>
                                    </div>
                                    <div class="space-y-3">`;

                                item.components.forEach(component => {
                                    const compConditionText = component.condition == 1 ?
                                        'Bagus' : 'Rusak';
                                    const compConditionClass = component.condition == 1 ?
                                        'text-emerald-600 bg-emerald-50' :
                                        'text-rose-600 bg-rose-50';
                                    const compBgClass = component.condition == 1 ?
                                        'bg-emerald-50/50' : 'bg-rose-50/50';

                                    detailsHTML += `
                                    <div class="${compBgClass} p-4 rounded-lg border border-gray-200">
                                        <div class="flex items-start justify-between mb-2">
                                            <div>
                                                <p class="font-semibold text-gray-800">${component.name}</p>
                                                <p class="text-xs text-gray-500 font-mono mt-1">${component.serial_code}</p>
                                            </div>
                                            <span class="px-2 py-1 rounded-full text-xs font-semibold ${compConditionClass}">
                                                ${compConditionText}
                                            </span>
                                        </div>`;

                                    detailsHTML += renderAdditionalInfo(component
                                        .additional_information);
                                    detailsHTML += `</div>`;
                                });

                                detailsHTML += '</div></div>';
                            }
                            detailsHTML += '</div>';
                        });
                        detailsHTML += '</div>';
                    } else {
                        detailsHTML += `
                        <div class="bg-gray-50 rounded-xl p-12 text-center border-2 border-dashed border-gray-300">
                            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                            </svg>
                            <p class="text-gray-500 text-lg">Tidak ada item yang terdaftar untuk meja ini</p>
                        </div>`;
                    }

                    detailsHTML += '</div>';
                    itemDetailsContainer.innerHTML = detailsHTML;
                    itemDetailsContainer.classList.remove('hidden');

                    setTimeout(() => {
                        itemDetailsContainer.scrollIntoView({
                            behavior: 'smooth',
                            block: 'nearest'
                        });
                    }, 100);
                }
            });
        });
    </script>

    <style>
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-fade-in {
            animation: fade-in 0.3s ease-out;
        }
    </style>
@endsection