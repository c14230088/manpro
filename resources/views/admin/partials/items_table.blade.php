<div class="relative">
    <div id="items-loading-overlay" class="absolute inset-0 bg-white/70 backdrop-blur-sm z-10 flex items-center justify-center hidden">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Item</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Spesifikasi</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kondisi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($items as $item)
                    <tr class="item-row hover:bg-gray-50 cursor-pointer" 
                        data-type="item" {{-- <-- PENTING --}}
                        data-item-id="{{ $item->id }}"
                        data-item-name="{{ $item->name }}" 
                        data-item-condition="{{ $item->condition }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-semibold text-gray-900">{{ $item->name }}</div>
                            <div class="text-xs text-gray-600 font-mono">{{ $item->serial_code }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if ($item->desk)
                                <div class="text-sm font-medium text-gray-900">{{ $item->desk->lab->name ?? 'N/A' }}</div>
                                <div class="text-sm text-gray-600">Meja {{ $item->desk->location }}</div>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-700">
                                    Belum Terpasang
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $item->type->name ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 max-w-xs truncate">
                            @foreach ($item->specSetValues as $spec)
                                <span class="font-semibold">{{ $spec->specAttributes->name }}:</span> {{ $spec->value }}@if (!$loop->last), @endif
                            @endforeach
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if ($item->condition)
                                <span class="condition-badge px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Baik</span>
                            @else
                                <span class="condition-badge px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Rusak</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            Tidak ada item yang cocok dengan filter Anda.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($items->hasPages())
        <div class="p-6 bg-gray-50 border-t border-gray-200">
            {{ $items->links() }}
        </div>
    @endif
</div>