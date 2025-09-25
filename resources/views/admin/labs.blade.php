@extends('layouts.admin')

@section('title', 'Laboratories Mapping')

@section('body')
    <div class="flex flex-col w-full py-4 shadow-md items-center justify-center mb-5">
        <h1 class="text-center text-4xl uppercase font-bold mb-2">Laboratories</h1>
    </div>

    <div class="my-2 px-6">
        <label for="lab-selector" class="block text-sm font-medium text-gray-700">Pilih Laboratorium:</label>
        <select id="lab-selector"
                class="mt-1 block w-full md:w-1/3 pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
            
            <option value="" selected disabled>-- Pilih Lab --</option>
            
            @foreach ($labs as $lab)
                <option value="{{ $lab->id }}">{{ $lab->name }}</option>
            @endforeach
        </select>
    </div>

    <hr class="my-6">

    <div id="desk-grid-container" class="px-6 mt-4 w-full grid gap-4">
        <p class="col-span-10 text-gray-500">Silakan pilih laboratorium untuk menampilkan denah meja.</p>
    </div>
@endsection

@section('script')
<script>
    document.getElementById('labs').classList.add('bg-slate-100');

    document.addEventListener('DOMContentLoaded', function() {
        
        const labSelector = document.getElementById('lab-selector');
        const deskContainer = document.getElementById('desk-grid-container');

        labSelector.addEventListener('change', async function() {
            const selectedLabId = this.value;
            if (!selectedLabId) return;

            deskContainer.innerHTML = '<p class="text-center">Memuat data meja...</p>';
            deskContainer.style.gridTemplateColumns = '';

            try {
                const response = await fetch(`/admin/labs/${selectedLabId}/desks`);
                if (!response.ok) throw new Error('Network response was not ok');
                
                const desks = await response.json();
                
                deskContainer.innerHTML = ''; 

                if (desks.length === 0) {
                    deskContainer.innerHTML = '<p class="text-gray-500">Tidak ada data meja untuk lab ini.</p>';
                    return;
                }

                let maxCols = 0;
                desks.forEach(desk => {
                    const colNum = parseInt((desk.location.match(/\d+$/) || ['0'])[0], 10);
                    if (colNum > maxCols) {
                        maxCols = colNum;
                    }
                });

                deskContainer.style.gridTemplateColumns = `repeat(${maxCols}, minmax(0, 1fr))`;

                desks.sort((a, b) => a.location.localeCompare(b.location, undefined, { numeric: true }));

                desks.forEach(desk => {
                    const colNum = parseInt((desk.location.match(/\d+$/) || ['0'])[0], 10);
                    if(colNum === 0) return; // Lewati jika tidak ada nomor kolom

                    let bgColorClass = '';
                    if (desk.wall) {
                        bgColorClass = 'opacity-0';
                    } else if (desk.condition == 1) {
                        bgColorClass = 'bg-blue-100 border-blue-300';
                    } else {
                        bgColorClass = 'bg-red-200 border-red-400';
                    }

                    const conditionText = desk.wall 
                        ? '<span class="text-xs block">DINDING</span>' 
                        : `<span class="text-xs block">${desk.condition == 1 ? 'Bagus' : 'Rusak'}</span>`;
                    
                    // PERUBAHAN: Tambahkan style="grid-column-start: ..." untuk penempatan
                    const deskElementHTML = `
                        <div style="grid-column-start: ${colNum};" class="flex items-center justify-center p-4 border rounded-lg h-24 ${bgColorClass}">
                            <div class="text-center">
                                <span class="font-bold text-lg">${desk.location}</span>
                                ${conditionText}
                            </div>
                        </div>
                    `;
                    
                    deskContainer.innerHTML += deskElementHTML;
                });
                
            } catch (error) {
                console.error('Error fetching desks:', error);
                deskContainer.innerHTML = '<p class="text-red-500 text-center">Terjadi kesalahan saat mengambil data.</p>';
            }
        });
    });
</script>
@endsection