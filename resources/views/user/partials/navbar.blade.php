<nav class="fixed w-full z-50 top-0 transition-all duration-300 bg-white/90 backdrop-blur-md shadow-sm border-b border-gray-200" id="navbar">
    <div class="relative container mx-auto px-6 py-3 flex justify-between items-center">
        <a href="/" class="flex items-center gap-2 group">
            {{-- Ganti dengan logo UK Petra asli jika ada --}}
            <div class="w-10 h-10 bg-petra-blue rounded-md flex items-center justify-center shadow-sm">
                 <i class="fa-solid fa-building-columns text-petra-yellow text-xl"></i>
            </div>
            <div class="flex flex-col">
                <span class="text-lg font-bold text-petra-blue leading-tight tracking-wide">
                    LABORATORIUM
                </span>
                <span class="text-xs text-petra-darkgray font-medium tracking-wider">
                    GEDUNG P - UK PETRA
                </span>
            </div>
        </a>

        <div class="hidden md:flex items-center space-x-8 font-medium text-sm">
            <a href="#about" class="text-petra-darkgray hover:text-petra-blue transition-colors relative after:absolute after:bottom-[-5px] after:left-0 after:h-[2px] after:w-0 after:bg-petra-yellow after:transition-all hover:after:w-full">TENTANG</a>
            <a href="#services" class="text-petra-darkgray hover:text-petra-blue transition-colors relative after:absolute after:bottom-[-5px] after:left-0 after:h-[2px] after:w-0 after:bg-petra-yellow after:transition-all hover:after:w-full">LAYANAN</a>
            
            <a href="{{ route('user.booking.form') }}" 
               class="px-5 py-2 rounded text-sm font-bold bg-petra-blue text-white hover:bg-petra-yellow hover:text-petra-blue transition-all duration-300 shadow-sm">
               <i class="fa-regular fa-calendar-check mr-2"></i>BOOKING
            </a>
        </div>

        <button class="md:hidden text-petra-blue text-2xl focus:outline-none" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
            <i class="fa-solid fa-bars"></i>
        </button>
    </div>

    <div id="mobile-menu" class="hidden md:hidden relative bg-white border-b border-gray-200 shadow-lg">
        <div class="flex flex-col p-4 space-y-3 font-medium">
            <a href="#about" class="text-petra-darkgray hover:text-petra-blue py-2">TENTANG</a>
            <a href="#services" class="text-petra-darkgray hover:text-petra-blue py-2">LAYANAN</a>
            <a href="{{ route('user.booking.form') }}" class="text-white bg-petra-blue hover:bg-petra-yellow hover:text-petra-blue py-2 px-4 text-center rounded transition-all">
                BOOKING SEKARANG
            </a>
        </div>
    </div>
</nav>