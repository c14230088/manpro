<footer class="bg-petra-blue pt-16 pb-8 text-gray-300">
    <div class="container mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
            <div class="md:col-span-1">
                <div class="flex items-center gap-2 mb-4">
                     <i class="fa-solid fa-building-columns text-petra-yellow text-2xl"></i>
                     <span class="text-xl font-bold text-white tracking-wide">LAB GEDUNG P</span>
                </div>
                <p class="text-sm leading-relaxed opacity-80">
                    Menyediakan fasilitas laboratorium dan peralatan terkini untuk menunjang kegiatan praktikum dan penelitian mahasiswa Universitas Kristen Petra.
                </p>
            </div>

            <div>
                <h4 class="text-white font-bold mb-4 text-base uppercase tracking-wider border-b-2 border-petra-yellow inline-block pb-1">Tautan</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="#about" class="hover:text-petra-yellow transition-colors">Tentang Kami</a></li>
                    <li><a href="#services" class="hover:text-petra-yellow transition-colors">Fasilitas & Layanan</a></li>
                    <li><a href="{{ route('user.booking.form') }}" class="hover:text-petra-yellow transition-colors font-semibold text-white">Formulir Booking</a></li>
                </ul>
            </div>

             <div>
                <h4 class="text-white font-bold mb-4 text-base uppercase tracking-wider border-b-2 border-petra-yellow inline-block pb-1">Hubungi Kami</h4>
                <ul class="space-y-3 text-sm">
                    <li class="flex items-start gap-3 opacity-80 hover:opacity-100 transition">
                        <i class="fa-solid fa-map-location-dot mt-1 text-petra-yellow"></i>
                        <span>Gedung P, Universitas Kristen Petra.<br>Siwalankerto, Surabaya.</span>
                    </li>
                    <li class="flex items-center gap-3 opacity-80 hover:opacity-100 transition">
                        <i class="fa-solid fa-envelope text-petra-yellow"></i>
                        <span>lab.gedungp@petra.ac.id</span>
                    </li>
                </ul>
            </div>

             <div>
                <h4 class="text-white font-bold mb-4 text-base uppercase tracking-wider border-b-2 border-petra-yellow inline-block pb-1">Jam Operasional</h4>
                <p class="text-sm opacity-80 mb-2"><span class="font-semibold text-white">Senin - Jumat:</span> 07.30 - 16.30 WIB</p>
                <p class="text-sm opacity-80"><span class="font-semibold text-white">Sabtu - Minggu:</span> Tutup</p>
            </div>
        </div>

        <div class="border-t border-blue-800/50 pt-8 flex flex-col md:flex-row justify-between items-center text-xs opacity-70">
            <p>&copy; {{ date('Y') }} Laboratorium Gedung P - UK Petra. All Rights Reserved.</p>
            <p class="mt-2 md:mt-0">Professional Lab Management System</p>
        </div>
    </div>
</footer>