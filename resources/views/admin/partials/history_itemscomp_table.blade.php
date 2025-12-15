<div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full custom-table min-w-[1200px]">
            <thead>
                <tr class="border-b border-gray-200">
                    <th class="px-6 py-4 text-left whitespace-nowrap">Timestamp</th>
                    <th class="px-6 py-4 text-left whitespace-nowrap">Data Peminjam</th>
                    <th class="px-6 py-4 text-left whitespace-nowrap">Detail Barang</th>
                    <th class="px-6 py-4 text-left whitespace-nowrap">Tujuan</th>
                    <th class="px-6 py-4 text-left whitespace-nowrap">Booking Details</th>
                    <th class="px-6 py-4 text-left whitespace-nowrap">Approved By</th>
                    <th class="px-6 py-4 text-left whitespace-nowrap">Returned Date</th>
                    <th class="px-6 py-4 text-left whitespace-nowrap">Status Pinjam</th>
                    <th class="px-6 py-4 text-center whitespace-nowrap">Kondisi Akhir</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($histories as $history)
                <tr class="hover:bg-gray-50 transition-colors">

                    {{-- -timestamp --}}
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">
                            {{-- Mengambil dari kolom yang kita select alias tadi --}}
                            {{ \Carbon\Carbon::parse($history->booking_created_at)->format('d M Y') }}
                        </div>
                        <div class="text-xs text-gray-500">
                            {{ \Carbon\Carbon::parse($history->booking_created_at)->format('H:i') }} WIB
                        </div>
                    </td>
                    {{-- -pemimhjam --}}
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center text-petra-blue font-bold text-xs mr-3">
                                {{-- Akses: Item -> Booking -> Borrower -> Name --}}
                                {{ substr($history->booking->borrower->name ?? '?', 0, 2) }}
                            </div>
                            <div>
                                <div class="text-sm font-bold text-gray-800">
                                    {{ $history->booking->borrower->name ?? 'User Terhapus' }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $history->booking->borrower->email ?? '-' }}
                                </div>
                            </div>
                        </div>
                    </td>

                    {{--item dipinjam --}}

                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">
                            @if($history->bookable)
                            <button type="button"
                                class="hover:text-petra-blue hover:underline text-left focus:outline-none font-bold btn-detail-trigger"
                                data-id="{{ $history->bookable_id }}"
                                data-type="{{ $history->bookable_type }}">
                                {{ $history->bookable->name }}
                            </button>
                            @else
                            <span class="text-red-500 italic">Item Terhapus</span>
                            @endif
                        </div>
                        @if(str_contains($history->bookable_type, 'Component'))
                        <span class="text-xs text-amber-600 bg-amber-50 px-2 rounded border border-amber-100">Komponen</span>
                        @else
                        <span class="text-xs text-blue-600 bg-blue-50 px-2 rounded border border-blue-100">Item</span>
                        @endif

                        <div class="text-xs text-gray-500 mt-2 flex flex-col gap-0.5">
                            @php
                            $labName = 'Unknown Lab';
                            $deskName = null;
                            $bookable = $history->bookable;

                            // Logika Penentuan Lokasi (Cek Lab langsung dulu, baru cek via Meja)
                            if ($bookable instanceof \App\Models\Items) {
                            $labName = $bookable->lab->name ?? $bookable->desk->lab->name ?? 'Unknown Lab';
                            $deskName = $bookable->desk->location ?? null;
                            }
                            elseif ($bookable instanceof \App\Models\Components) {
                            $labName = $bookable->item->lab->name ?? $bookable->item->desk->lab->name ?? 'Unknown Lab';
                            $deskName = $bookable->item->desk->location ?? null;
                            }
                            @endphp

                            <div class="flex items-center gap-1.5">
                                <span class="font-medium text-gray-600">
                                    {{ $labName }}
                                    @if($deskName)
                                    <span class="text-gray-300 mx-1">•</span> {{ $deskName }}
                                    @endif
                                </span>
                            </div>
                        </div>

                    </td>

                    {{-- tujuan --}}
                    <td class="px-6 py-4">
                        <div class="flex flex-col gap-1 w-48">
                            <div class="text-sm font-medium text-gray-900 truncate" title="{{ $history->booking->event_name }}">
                                {{ $history->booking->event_name ?? '-' }}
                            </div>
                        </div>
                    </td>

                    {{-- booking details --}}
                    <td class="px-6 py-4">
                        <div class="flex flex-col gap-1 w-48">
                            <div class="text-xs text-gray-500 mb-1">Jadwal:</div>
                            <div class="text-sm font-bold text-gray-700">
                                {{ \Carbon\Carbon::parse($history->plan_borrowed_at)->format('d M H:i') }}
                            </div>
                            <div class="text-xs text-gray-400">
                                s.d. {{ \Carbon\Carbon::parse($history->return_deadline_at)->format('d M H:i') }}
                            </div>
                        </div>
                    </td>

                    {{-- approved by --}}
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($history->approver)
                        <div class="text-sm font-medium text-gray-800">
                            {{ $history->approver->name }}
                        </div>
                        <div class="text-xs text-green-600 flex items-center gap-1">
                            <i class="fa-solid fa-check-circle"></i> Approved
                        </div>
                        @elseif($history->approved === 0)
                        <div class="text-xs text-red-500 font-medium">Auto/Admin Reject</div>
                        @else
                        <span class="text-gray-400 text-xs italic">Menunggu Approval</span>
                        @endif
                    </td>

                    {{-- -returned date --}}
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($history->returned_at)
                        <div class="flex flex-col">
                            <div class="text-sm font-bold text-gray-700">
                                {{ \Carbon\Carbon::parse($history->returned_at)->format('d M Y') }}
                            </div>
                            <div class="text-xs text-green-600 font-medium">
                                {{ \Carbon\Carbon::parse($history->returned_at)->format('H:i') }} WIB
                            </div>
                        </div>
                        @else
                        <div class="text-center"> <span class="text-gray-400 text-xs italic text-center"> - </span>
                        </div>
                        @endif
                    </td>

                    {{-- -status sekarang --}}

                    <td class="px-6 py-4 text-center whitespace-nowrap">
                        @php
                        $statusClass = ''; $statusText = ''; $icon = '';
                        if ($history->returned_at) {
                        $statusClass = 'bg-green-100 text-green-800 border-green-200';
                        $statusText = 'Completed';
                        $icon = 'fa-check';
                        } elseif ($history->approved === 0) {
                        $statusClass = 'bg-red-100 text-red-800 border-red-200';
                        $statusText = 'Rejected';
                        $icon = 'fa-times';
                        } elseif ($history->approved === 1 && \Carbon\Carbon::now() > $history->return_deadline_at) {
                        $statusClass = 'bg-orange-100 text-orange-800 border-orange-200';
                        $statusText = 'Overdue';
                        $icon = 'fa-circle-exclamation';
                        } elseif ($history->approved === 1) {
                        $statusClass = 'bg-blue-100 text-blue-800 border-blue-200';
                        $statusText = 'Active';
                        $icon = 'fa-spinner fa-spin';
                        } else {
                        $statusClass = 'bg-gray-100 text-gray-600 border-gray-200';
                        $statusText = 'Pending';
                        $icon = 'fa-hourglass-start';
                        }
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border {{ $statusClass }}">
                            <i class="fa-solid {{ $icon }} mr-1.5"></i>
                            {{ $statusText }}
                        </span>
                    </td>

                    {{-- -kondisi akhir --}}
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($history->returned_status === 0)
                        <span class="text-red-500 text-xs font-bold">Rusak</span>
                        @elseif($history->returned_status === 1)
                        <span class="text-blue-600 text-xs font-bold">Normal</span>
                        @else
                        <div class="text-center"> <span class="text-gray-400 text-xs italic text-center"> - </span>
                        </div> @endif
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-6 py-10 text-center text-gray-500">
                        <div class="flex flex-col items-center justify-center">
                            <i class="fa-regular fa-folder-open text-4xl mb-3 text-gray-300"></i>
                            <p>Data tidak ditemukan sesuai filter.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
        {{ $histories->links() }}
    </div>
</div>