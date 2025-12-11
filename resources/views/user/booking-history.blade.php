<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Riwayat Peminjaman' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'petra-blue': '#1a237e',
                        'petra-yellow': '#ffca28',
                        'petra-darkgray': '#424242',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-800">

    @include('user.partials.navbar')

    <div class="container mx-auto px-4 pt-24 pb-12 max-w-5xl">
      
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-petra-blue">Riwayat Peminjaman</h1>
                <p class="text-gray-500 mt-1">Pantau status pengajuan peminjaman aset laboratorium Anda.</p>
            </div>
            <a href="{{ route('user.booking.form') }}" class="group bg-petra-blue text-white px-5 py-2.5 rounded-lg shadow-md hover:bg-blue-800 transition-all flex items-center gap-2 font-medium">
                <i class="fa-solid fa-plus transition-transform group-hover:rotate-90"></i>
                Buat Peminjaman Baru
            </a>
        </div>

    
        @if($bookings->isEmpty())
        
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center flex flex-col items-center">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fa-regular fa-folder-open text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-700 mb-2">Belum ada riwayat</h3>
                <p class="text-gray-500 max-w-md mx-auto mb-6">Anda belum pernah mengajukan peminjaman. Mulai ajukan peminjaman untuk keperluan praktikum atau skripsi Anda.</p>
            </div>
        @else
            <div class="grid gap-6">
                @foreach($bookings as $booking)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-300">
                        
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex flex-col sm:flex-row justify-between sm:items-center gap-3">
                            <div class="flex items-center gap-2">
                                <i class="fa-regular fa-calendar-check text-petra-blue"></i>
                                <span class="text-sm font-semibold text-gray-600">
                                    Diajukan pada {{ \Carbon\Carbon::parse($booking->created_at)->translatedFormat('d F Y, H:i') }}
                                </span>
                            </div>
                            
                            @php
                                $statusClass = 'bg-yellow-100 text-yellow-700 border-yellow-200';
                                $statusIcon = 'fa-clock';
                                $statusText = 'Menunggu Persetujuan';

                                if($booking->approved === 1) {
                                    $statusClass = 'bg-green-100 text-green-700 border-green-200';
                                    $statusIcon = 'fa-check-circle';
                                    $statusText = 'Disetujui';
                                } elseif($booking->approved === 0) { 
                                    $statusClass = 'bg-red-100 text-red-700 border-red-200';
                                    $statusIcon = 'fa-times-circle';
                                    $statusText = 'Ditolak';
                                }
                            @endphp

                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border {{ $statusClass }}">
                                <i class="fa-solid {{ $statusIcon }}"></i> {{ $statusText }}
                            </span>
                        </div>

                        <div class="p-6">
                            <div class="grid md:grid-cols-3 gap-6">
                                
                                <div class="col-span-2">
                                    <h2 class="text-xl font-bold text-petra-blue mb-1">{{ $booking->event_name }}</h2>
                                    
                                    <div class="flex flex-col gap-2 text-sm text-gray-600 mt-3">
                                        <div class="flex items-center gap-2">
                                            <i class="fa-solid fa-clock text-gray-400 w-5 text-center"></i>
                                            <span>
                                                {{ \Carbon\Carbon::parse($booking->event_started_at)->format('d M H:i') }} 
                                                <span class="mx-1 text-gray-300">|</span> 
                                                {{ \Carbon\Carbon::parse($booking->event_ended_at)->format('d M H:i') }}
                                            </span>
                                        </div>

                                        @if($booking->thesis_title)
                                            <div class="flex items-start gap-2">
                                                <i class="fa-solid fa-graduation-cap text-gray-400 w-5 text-center mt-0.5"></i>
                                                <span>Judul Skripsi: <span class="font-medium text-gray-800 italic">"{{ $booking->thesis_title }}"</span></span>
                                            </div>
                                        @endif

                                        @if($booking->supervisor)
                                            <div class="flex items-center gap-2">
                                                <i class="fa-solid fa-user-tie text-gray-400 w-5 text-center"></i>
                                                <span>Pembimbing: <span class="font-medium text-gray-800">{{ $booking->supervisor->name }}</span></span>
                                            </div>
                                        @endif
                                    </div>

                                    @if($booking->booking_detail)
                                        <div class="mt-4 p-3 bg-blue-50 rounded-lg text-sm text-blue-800 border border-blue-100 flex gap-2">
                                            <i class="fa-solid fa-circle-info mt-0.5"></i>
                                            <div><span class="font-bold">Catatan:</span> {{ $booking->booking_detail }}</div>
                                        </div>
                                    @endif
                                </div>

                                <div class="col-span-1 bg-gray-50 rounded-lg p-4 border border-gray-100 h-fit">
                                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3 flex justify-between items-center">
                                        Item Dipinjam
                                        <span class="bg-gray-200 text-gray-600 py-0.5 px-2 rounded-full text-[10px]">{{ $booking->bookings_items->count() }}</span>
                                    </h4>
                                    
                                    <ul class="space-y-2">
                                        @foreach($booking->bookings_items as $index => $item)
                                            <li class="flex items-start gap-2 text-sm text-gray-700 {{ $index >= 3 ? 'hidden extra-item-'.$booking->id : '' }}">
                                                <i class="fa-solid fa-box text-petra-blue mt-1 text-xs"></i>
                                                <span class="leading-tight">
                                                    {{ $item->bookable->name ?? 'Unknown Item' }}
                                                    @if(str_contains($item->bookable_type, 'Labs'))
                                                        <span class="text-[10px] bg-indigo-100 text-indigo-700 px-1 rounded ml-1 font-bold">LAB</span>
                                                    @endif
                                                </span>
                                            </li>
                                        @endforeach
                                    </ul>
                                    
                                    @if($booking->bookings_items->count() > 3)
                                        <button onclick="toggleItems('{{ $booking->id }}', this)" 
                                            class="w-full mt-3 text-xs text-petra-blue font-bold hover:bg-blue-50 py-1 rounded transition-colors flex items-center justify-center gap-1 border border-dashed border-petra-blue/30">
                                            <span>+ {{ $booking->bookings_items->count() - 3 }} item lainnya</span>
                                            <i class="fa-solid fa-chevron-down"></i>
                                        </button>
                                    @endif
                                </div>

                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <footer class="bg-white border-t border-gray-200 mt-12 py-8">
        <div class="container mx-auto px-4 text-center text-gray-500 text-sm">
            &copy; {{ date('Y') }} Laboratorium Gedung P - UK Petra. All rights reserved.
        </div>
    </footer>

    <script>
        function toggleItems(bookingId, btn) {
            const hiddenItems = document.querySelectorAll(`.extra-item-${bookingId}`);
            
            hiddenItems.forEach(item => {
                item.classList.remove('hidden');
            });

            btn.style.display = 'none';
        }
    </script>

</body>
</html>