<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lab Info | @if(isset($title)) {{ $title }} @else @yield('title', 'Gedung P Petra') @endif</title>
    <link rel="icon" href="{{ asset('assets/utils/icons/logoAja.ico') }}" type="image/x-icon" />

    {{-- Tailwind & Plugins --}}
    <script src="https://cdn.tailwindcss.com/3.4.5"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        petra: {
                            blue: '#003366',   // Biru Tua Profesional
                            yellow: '#FFC107', // Kuning Emas/Akademik
                            gray: '#F8F9FA',   // Abu-abu background terang
                            darkgray: '#343A40', // Abu-abu teks gelap
                            wood: '#D2B48C'    // Coklat Kayu Muda
                        }
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- Tom Select --}}
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

    {{-- Sweet Alert 2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- AOS --}}
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    {{-- GSAP --}}
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js"></script>

    {{-- Lenis (Smooth Scroll) --}}
    <script src="https://unpkg.com/lenis@1.1.13/dist/lenis.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/lenis@1.1.13/dist/lenis.css">

    <style>
        /* Base Setup */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        /* Asumsikan 'Pilot' adalah font yang paling bersih/profesional dari pilihan yang ada */
        @font-face { font-family: 'Pilot'; src: url('/assets/fonts/pilot.otf') format('truetype'); }
        .font-pilot { font-family: 'Pilot', sans-serif; }
        body {
            font-family: 'Pilot', sans-serif;
            background-color: #F8F9FA; /* Light Gray BG */
            color: #343A40; /* Dark Gray Text */
            overflow-x: hidden;
        }

        /* Scrollbar Styling (Professional Clean) */
        body::-webkit-scrollbar { width: 8px; background-color: #F8F9FA; }
        body::-webkit-scrollbar-thumb {
            background: #D2B48C; /* Wood accent scrollbar */
            border-radius: 4px;
        }

        /* SweetAlert Customization (Professional Clean) */
        div:where(.swal2-container) div:where(.swal2-popup) {
            background: #ffffff !important;
            border-radius: 8px;
            border-top: 4px solid #003366; /* Blue accent top border */
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            font-family: 'Pilot', sans-serif;
        }

        div:where(.swal2-title) {
            color: #003366 !important; /* Petra Blue */
            font-weight: 600;
        }

        div:where(.swal2-html-container) {
            color: #495057 !important;
        }

        /* Swal Buttons */
        div:where(.swal2-actions) button.swal2-confirm {
            background-color: #003366 !important; /* Petra Blue */
            box-shadow: none !important;
            border-radius: 4px !important;
        }
        
        div:where(.swal2-actions) button.swal2-cancel {
            background: transparent !important;
            border: 1px solid #dc3545 !important;
            color: #dc3545 !important;
            border-radius: 4px !important;
        }

        /* TomSelect Custom Professional Mode */
        .ts-control {
            background-color: #ffffff !important;
            border: 1px solid #ced4da !important;
            color: #495057 !important;
            border-radius: 0.375rem !important;
        }
        .ts-dropdown {
            background-color: #ffffff !important;
            border: 1px solid #ced4da !important;
            color: #495057 !important;
        }
        .ts-dropdown .active {
            background-color: #003366 !important;
            color: #ffffff !important;
        }
    </style>
</head>

<body class="antialiased">

    @yield('body')

    {{-- Global Scripts --}}
    <script>
        // Init Libraries
        AOS.init({ once: true, duration: 700, offset: 80 });
        gsap.registerPlugin(ScrollTrigger);

        // Smooth Scroll (Lenis) - Keep it enabled for good UX
        const lenis = new Lenis({ duration: 1.2 });
        function raf(time) { lenis.raf(time); requestAnimationFrame(raf); }
        requestAnimationFrame(raf);

        // SweetAlert Toast Mixin (Professional Clean)
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            background: '#fff',
            color: '#333',
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            },
            customClass: {
                popup: 'border-l-4 border-petra-blue shadow-md'
            }
        });
    </script>

    {{-- Flash Message Handlers --}}
    @if (session('success'))
        <script>
            Toast.fire({ icon: 'success', title: "{{ session('success') }}" });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({ icon: 'error', title: 'Kesalahan', text: "{{ session('error') }}" });
        </script>
    @endif

    @if ($errors->any())
        <script>
            Swal.fire({ icon: 'warning', title: 'Perhatian', text: "{{ $errors->first() }}" });
        </script>
    @endif
    
    @yield('scripts')
</body>
</html>