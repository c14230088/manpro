<div class="relative">
    <div id="components-loading-overlay" class="absolute inset-0 bg-white/70 backdrop-blur-sm z-10 flex items-center justify-center hidden">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Komponen</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Terpasang di Item</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Spesifikasi</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kondisi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($components as $comp)
                    <tr class="component-row hover:bg-gray-50 cursor-pointer" 
                        data-type="component" {{-- <-- PENTING --}}
                        data-item-id="{{ $comp->id }}"
                        data-item-name="{{ $comp->name }}" 
                        data-item-condition="{{ $comp->condition }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-semibold text-gray-900">{{ $comp->name }}</div>
                            <div class="text-xs text-gray-600 font-mono">{{ $comp->serial_code }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if ($comp->item)
                                <div class="text-sm font-medium text-gray-900">{{ $comp->item->name }}</div>
                                @if ($comp->item->desk)
                                    <div class="text-sm text-gray-600">({{ $comp->item->desk->lab->name }} - Meja {{ $comp->item->desk->location }})</div>
                                @else
                                     <div class="text-sm text-gray-600">(Item belum terpasang)</div>
                                @endif
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-700">
                                    Belum Terpasang
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                {{ $comp->type->name ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 max-w-xs truncate">
                             @foreach ($comp->specSetValues as $spec)
                                <span class="font-semibold">{{ $spec->specAttributes->name }}:</span> {{ $spec->value }}@if (!$loop->last), @endif
                            @endforeach
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if ($comp->condition)
                                <span class="condition-badge px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Bagus</span>
                            @else
                                <span class="condition-badge px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Rusak</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            Tidak ada komponen yang cocok dengan filter Anda.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($components->hasPages())
        <div class="p-6 bg-gray-50 border-t border-gray-200">
            {{ $components->links() }}
        </div>
    @endif
</div>