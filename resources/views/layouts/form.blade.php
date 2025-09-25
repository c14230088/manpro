<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Infor | @if(isset($title)) {{ $title }} @else @yield('title', 'Form') @endif</title>
    <link rel="icon" href="{{ asset('assets/utils/icons/logoAja.ico') }}" type="image/x-icon" />

    {{-- tailwindcss --}}
    <script src="https://cdn.tailwindcss.com/3.4.5"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tw-elements/dist/css/tw-elements.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tw-elements/css/tw-elements.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    {{-- Sweet Alert --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- aos --}}
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    {{-- gsap --}}
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/Flip.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/MotionPathPlugin.min.js"></script>
    <script src="https://unpkg.com/split-type"></script>

    {{-- Datatables --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.2/css/dataTables.dataTables.css" />

    {{-- JQuery --}}
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    {{-- Parallax JS --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/parallax/3.1.0/parallax.min.js"></script>

    {{-- Lenis --}}
    <script src="https://unpkg.com/lenis@1.1.13/dist/lenis.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/lenis@1.1.13/dist/lenis.css">

    {{-- Ably --}}
    <script src="https://cdn.jsdelivr.net/npm/ably@1.2.36"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @font-face {
            font-family: 'ReturnOfTheGrid';
            src: url('/assets/fonts/return-of-the-grid.otf') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'Karnivore';
            src: url('/assets/fonts/karnivore.ttf') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'Pilot';
            src: url('/assets/fonts/pilot.otf') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'Sofia';
            src: url('/assets/fonts/sofia.otf') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        @layer utilities {
            .font-return-grid {
                font-family: 'ReturnOfTheGrid', sans-serif;
            }

            .font-karnivore {
                font-family: 'Karnivore', sans-serif;
            }

            .font-sofia {
                font-family: 'Sofia', sans-serif;
            }

            .font-pilot {
                font-family: 'Pilot', sans-serif;
            }
        }

        @keyframes glowing {
            0% {
                text-shadow: 0 0 5px rgba(255, 255, 255, 0.8);
            }

            50% {
                text-shadow: 0 0 22px rgba(255, 255, 255, 0.9);
            }

            100% {
                text-shadow: 0 0 5px rgba(255, 255, 255, 0.8);
            }
        }

        .glowing-text {
            animation: glowing 4s infinite;
        }

        @keyframes neon-glowing {
            0% {
                text-shadow: 3px 2px 5px #b076a0;
            }

            50% {
                text-shadow: 3px 8px 13px #824D74;
            }

            100% {
                text-shadow: 3px 2px 5px #b076a0;
            }
        }

        .neon-glowing-text {
            animation: neon-glowing 4s infinite;
        }

        body::-webkit-scrollbar {
            width: 10px;
            background-color: #23314F;
        }

        body::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg,
                    #c4f3f6 0%,
                    #429da9 59%,
                    #c4f3f6 100%);
            border-radius: 10px;
            outline: 3px double rgba(61, 206, 222, 0.925);
            box-shadow: 10px 10px 10px #bf36ceb6, 10px 15px 10px #91c9e4;
        }

        body::-webkit-scrollbar-track {
            background: linear-gradient(180deg,
                    #7a31b3 0%,
                    #7232c5 100%);
        }

        .glowing-box {
            animation: boxGlowing 4s infinite;
        }

        @keyframes boxGlowing {
            0% {
                box-shadow: 0 0 6px rgba(100, 31, 154, 1);
            }

            50% {
                box-shadow: 0 0 23px rgba(191, 54, 206, 0.91);
            }

            100% {
                box-shadow: 0 0 6px rgba(100, 31, 154, 1);
            }
        }

        @keyframes shiny {

            0%,
            100% {
                text-shadow: 0 0 0.5px rgba(222, 219, 59, 0.95);
            }

            50% {
                text-shadow: 0 0 18px rgba(222, 219, 59, 0.825);
            }
        }

        .shiny {
            animation: shiny 4s infinite;
        }

        .text-shadow-white {
            text-shadow: 0 0 25px rgba(255, 255, 255, 0.9);
        }

        .box-shadow-white {
            box-shadow: 0 0 18px rgba(255, 255, 255, 0.9);
        }

        .text-shadow-black {
            text-shadow: 0 0 5px rgba(0, 0, 0, 0.7);
        }

        .box-shadow-black {
            box-shadow: 0 0 18px rgba(0, 0, 0, 0.9);
        }

        .text-shadow-neon {
            text-shadow: 0 0 5px rgba(191, 54, 206, 0.925);
        }

        .box-shadow-neon {
            box-shadow: 0 0 18px rgba(191, 54, 206, 0.959);
        }

        .drop-shadow-neon {
            -webkit-filter: drop-shadow(0 0 10px rgba(191, 54, 206, 0.9));
            filter: drop-shadow(0 0 10px rgba(191, 54, 206, 0.9));
        }

        .drop-shadow-white {
            -webkit-filter: drop-shadow(0 0 10px rgba(255, 255, 255, 0.9));
            filter: drop-shadow(0 0 10px rgba(255, 255, 255, 0.9));
        }

        .text-shadow-green {
            text-shadow: 2px 2px 5px rgba(49, 206, 127, 0.9), 0px 0 9px rgba(61, 219, 140, 0.925);
        }

        .text-shadow-yellow {
            text-shadow: 2px 2px 5px #A9A042, 0px 0 9px #dedb3bec;
        }

        .text-shadow-red {
            text-shadow: 2px 2px 5px #a95a42, 0px 0 9px rgba(222, 96, 61, 0.925);
        }

        .box-shadow-black-sm {
            box-shadow: 2px 2px 5px #5e5d5c, 0px 0 9px rgba(0, 0, 0, 0.925);
        }

        .text-shadow-blue {
            text-shadow: 2px 2px 5px #429da9, 0px 0 9px rgba(61, 206, 222, 0.925);
        }

        .text-shadow-pink {
            text-shadow: 2px 2px 5px #Ffd1dc, 0px 0 9px #fcb8c8;
        }
    </style>
</head>

<body>
    {{-- @include('loader') --}}
    <script>
        gsap.registerPlugin(ScrollTrigger);
        AOS.init();
        document.body.classList.add('overflow-x-hidden');
    </script>

    {{-- TW Elements --}}
    <script src="https://cdn.jsdelivr.net/npm/tw-elements/js/tw-elements.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tw-elements/dist/js/tw-elements.umd.min.js"></script>

    {{-- GSAP, ScrollTrigger --}}
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js"></script>

    {{-- SwiperJS --}}
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    {{-- Datatables --}}
    <script src="https://cdn.datatables.net/2.0.2/js/dataTables.js"></script>

    @yield('body')

    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '{{ session('error') }}'
            });
        </script>
    @endif
    @if (session('info'))
        <script>
            Swal.fire({
                icon: 'info',
                title: 'Info!',
                text: '{{ session('info') }}'
            });
        </script>
    @endif

    @if ($errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '{{ $errors->first() }}'
            });
        </script>
    @endif

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}'
            });
        </script>
    @endif

    <style>
        * {
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            -khtml-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        :root {
            --glow: #c4f3f6;
        }

        .swal2-popup {
            background-color: hsla(273, 30%, 24%, 0.1);
            background-image:
                radial-gradient(at 0% 0%, hsla(257, 42%, 16%, 0.1) 0px, transparent 50%),
                radial-gradient(at 80% 2%, hsla(266, 34%, 43%, 0.1) 0px, transparent 50%),
                radial-gradient(at 41% 28%, hsla(286, 25%, 30%, 0.1) 0px, transparent 50%),
                radial-gradient(at 0% 57%, hsla(319, 32%, 39%, 0.1) 0px, transparent 50%),
                radial-gradient(at 80% 58%, hsla(323, 37%, 34%, 0.1) 0px, transparent 50%),
                radial-gradient(at 80% 83%, hsla(317, 36%, 62%, 0.1) 0px, transparent 50%),
                radial-gradient(at 0% 100%, hsla(318, 30%, 50%, 0.1) 0px, transparent 50%);
            backdrop-filter: blur(13px);
            -webkit-backdrop-filter: blur(13px);
            box-shadow: 0 0 5px var(--glow), 0 0 10px var(--glow);
            filter: brightness(140%);
            -webkit-filter: brightness(140%);
            -ms-filter: brightness(140%);
            font-family: 'ReturnOfTheGrid', sans-serif;
        }

        .swal2-title,
        .swal2-html-container {
            text-shadow: none !important;
            /* background-clip: text; */
            color: white !important;
            font-weight: 600 !important;
            background: none !important;
            box-shadow: none !important;
            font-family: 'ReturnOfTheGrid', sans-serif;
        }

        .swal2-confirm {
            text-shadow: 0 0 10px rgba(61, 206, 222, 0.8);
            font-family: 'ReturnOfTheGrid', sans-serif;
            background: none !important;
            color: #DEFEC8 !important;
            font-weight: 600 !important;
            border: solid 2px 2px 5px #429da9 1.1px !important;
            width: 100px !important;
            box-shadow: 0 0 9px rgba(61, 219, 140, 0.8) !important;
        }

        .swal2-confirm:hover {
            box-shadow: 0 0 14px rgba(61, 219, 140, 0.95) !important;

        }

        .swal2-confirm:active {
            transform: scale(0.97);
        }

        .swal2-cancel {
            text-shadow: 0 0 10px rgba(61, 206, 222, 0.8);
            font-family: 'ReturnOfTheGrid', sans-serif;
            background: none !important;
            color: #F6C5C4 !important;
            font-weight: 600 !important;
            border: solid 2px 2px 5px #a95a42 1.1px !important;
            width: 100px !important;
            box-shadow: 0 0 9px rgba(222, 96, 61, 0.8) !important;
        }

        .swal2-cancel:hover {
            box-shadow: 0 0 14px rgba(222, 96, 61, 0.95) !important;

        }

        .swal2-cancel:active {
            transform: scale(0.97);
        }


        .swal2-icon,
        .swal2-success-circular-line-left,
        .swal2-success-circular-line-right,
        .swal2-success-fix {
            background: transparent !important;
        }
    </style>

</body>

</html>
