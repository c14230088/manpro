<div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full custom-table min-w-[1200px]"> <thead>
                <tr class="border-b border-gray-200">
                    <th class="px-6 py-4 text-left whitespace-nowrap">Timestamp</th>
                    <th class="px-6 py-4 text-left whitespace-nowrap">Data Peminjam</th>
                    <th class="px-6 py-4 text-left whitespace-nowrap">Lab & Periode</th>
                    <th class="px-6 py-4 text-left whitespace-nowrap">Tujuan</th>
                    <th class="px-6 py-4 text-left whitespace-nowrap">Booking Details</th> <th class="px-6 py-4 text-left whitespace-nowrap">Approved By</th>    <th class="px-6 py-4 text-left whitespace-nowrap">Returned Details</th> <th class="px-6 py-4 text-center whitespace-nowrap">Status</th>
                    <!-- <th class="px-6 py-4 text-center whitespace-nowrap">Aksi</th> -->
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($histories as $history)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">
                            {{ $history->created_at->format('d M Y') }}
                        </div>
                        <div class="text-xs text-gray-500">
                            {{ $history->created_at->format('H:i') }} WIB
                        </div>
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center text-petra-blue font-bold text-xs mr-3">
                                {{ substr($history->borrower->name ?? '?', 0, 2) }}
                            </div>
                            <div>
                                <div class="text-sm font-bold text-gray-800">
                                    {{ $history->borrower->name ?? 'User Terhapus' }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $history->borrower->email ?? '-' }}
                                </div>
                            </div>
                        </div>
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex flex-col gap-1">
                            <div class="text-sm font-bold text-petra-blue flex items-center gap-1">
                                <i class="fa-solid fa-desktop text-xs opacity-50"></i>
                                {{ $history->lab_name ?? 'Unknown Lab' }}
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 mt-2">
                            <i class="fa-solid fa-calendar-week mr-1.5 text-gray-400"></i>
                            {{ $history->period->academic_year ?? '-' }} - {{  $history->period->semester ?? '-' }}
                        </span>
                    </td>

                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-800 font-medium truncate w-32" title="{{ $history->event_name }}">
                            {{ $history->event_name ?? '-' }}
                        </div>
                    </td>

                    <td class="px-6 py-4">
                        <div class="flex flex-col gap-1 w-48">
                            <div class="text-xs text-gray-500 mb-1">Jadwal:</div>
                            <div class="text-sm font-bold text-gray-700">
                                {{ \Carbon\Carbon::parse($history->borrowed_at)->format('d M H:i') }}
                            </div>
                            <div class="text-xs text-gray-400">
                                s.d. {{ \Carbon\Carbon::parse($history->return_deadline_at)->format('d M H:i') }}
                            </div>
                            
                            @if(!empty($history->description) || !empty($history->reason))
                                <div class="mt-2 text-xs text-gray-600 bg-gray-50 p-1.5 rounded border border-gray-100 italic truncate" title="{{ $history->description ?? $history->reason }}">
                                    "{{ $history->description ?? $history->reason }}"
                                </div>
                            @endif
                        </div>
                    </td>

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
                            <div class="text-sm text-gray-400 italic">-</div>
                        @endif
                    </td>

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
                            <div class="text-sm text-gray-400 text-center">-</div>
                        @endif
                    </td>

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

                    <!-- <td class="px-6 py-4 text-center whitespace-nowrap">
                        <button class="text-gray-400 hover:text-petra-blue transition" title="Lihat Detail">
                            <i class="fa-solid fa-circle-info text-lg"></i>
                        </button>
                    </td> -->
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