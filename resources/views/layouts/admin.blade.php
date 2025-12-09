<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Script & CSS Utama --}}
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.css" />
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- TW Elements (hanya 1x) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tw-elements/dist/css/tw-elements.min.css" />

    {{-- TomSelect (CSS & JS) --}}
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

    <title>Admin | @if (isset($title))
            {{ $title }}
        @else
            @yield('title', 'Lab Infor')
        @endif
    </title>
    <link rel="icon" href="{{ asset('assets/logo/logo-robot.png') }}" type="image/svg+xml" />
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900&display=swap" rel="stylesheet" />

    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                fontFamily: {
                    sans: ["Open Sans", "sans-serif"],
                    body: ["Open Sans", "sans-serif"],
                    mono: ["ui-monospace", "monospace"],
                },
            },
        };
    </script>

    <style>
        .ts-control {
            display: block;
            width: 100%;
            padding-top: 0.80rem;
            padding-bottom: 0.80rem;
            padding-left: 1rem;
            padding-right: 1rem;

            font-size: 1rem;
            line-height: 1.5rem;
            color: #1f2937;
            background-color: #ffffff;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .ts-wrapper input {
            width: 100%;
        }

        .ts-wrapper.focus .ts-control {
            border-color: transparent !important;
            box-shadow: 0 0 0 2px #6366f1;
            outline: none;
        }

        .ts-wrapper.dropdown-active .ts-control {
            display: block;
            border-radius: 0.5rem !important;

            border: 2px solid black !important;
            box-shadow: 0 0 0 2px #6366f1 !important;
            outline: none !important;
        }

        /* untuk item/pill yang DIPILIH */
        .ts-wrapper.multi .ts-control>div,
        .ts-wrapper.single .ts-control .item {
            background-color: #e0e7ff;
            color: #4338ca;
            border-radius: 0.25rem;
            padding: 0.125rem 0.5rem;
            margin: 0.125rem 0.25rem;
        }

        .ts-wrapper.multi .ts-control {
            padding-right: 2rem !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 16px 12px;
        }

        .ts-control.dropdown-active {}

        .ts-dropdown {
            border: 1px solid #d1d5db;
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            z-index: 9000 !important;
        }

        .ts-dropdown .option {
            background-color: #ffffff;
            padding: 0.5rem 1rem;
        }

        .ts-dropdown .option.active,
        .ts-dropdown .option:hover {
            background-color: #e0e7ff;
        }

        .ts-dropdown .create {
            color: #2563eb;
        }

        /* ============================================ */
        /* CSS UNTUK RESIZER SIDEBAR (FIXED VERSION)    */
        /* ============================================ */
        :root {
            --sidebar-width: 240px;
        }

        #sidenav-8 {
            width: var(--sidebar-width) !important;
            transition: width 0s;
        }

        #main-content {
            transition: margin-left 0s;
        }

        @media (min-width: 768px) {
            #main-content {
                margin-left: var(--sidebar-width) !important;
            }
        }

        #sidenav-resizer {
            position: absolute;
            top: 0;
            right: 0;
            width: 6px;
            height: 100%;
            cursor: ew-resize;
            background: transparent;
            transition: background-color 0.2s;
            z-index: 1040;
        }

        #sidenav-resizer:hover,
        body.is-resizing #sidenav-resizer {
            background: linear-gradient(90deg, transparent, rgba(99, 102, 241, 0.6));
        }

        body.is-resizing {
            user-select: none;
            -webkit-user-select: none;
            -moz-user-select: none;
        }

        #sidenav-resizer::after {
            content: '';
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 40px;
            background: rgba(99, 102, 241, 0.3);
            border-radius: 2px;
            opacity: 0;
            transition: opacity 0.2s;
        }

        #sidenav-resizer:hover::after,
        body.is-resizing #sidenav-resizer::after {
            opacity: 1;
        }

        /* ============================================ */
        /* AKHIR CSS RESIZER SIDEBAR                    */
        /* ============================================ */
    </style>

    {{-- ========================================================= --}}
    {{-- STYLE UNTUK SWEETALERT & TOAST (MODERN COLORFUL) - FIXED  --}}
    {{-- ========================================================= --}}
    <style>
        /* Base popup style dengan glassmorphism effect */
        .swal2-container {
            z-index: 9997 !important;
        }

        .swal2-popup {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(255, 255, 255, 0.85)) !important;
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border-radius: 20px !important;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15),
                0 0 0 1px rgba(255, 255, 255, 0.1) inset !important;
            padding: 2rem !important;
        }

        /* Dark mode support */
        .dark .swal2-popup {
            background: linear-gradient(135deg, rgba(31, 41, 55, 0.95), rgba(31, 41, 55, 0.85)) !important;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* ============================================ */
        /* TOAST STYLING - WARNA SESUAI TYPE (FIXED)    */
        /* ============================================ */

        /* Base Toast Style */
        .swal2-toast-custom.swal2-popup {
            max-width: 400px !important;
            backdrop-filter: blur(12px) saturate(180%);
            -webkit-backdrop-filter: blur(12px) saturate(180%);
            border-radius: 16px !important;
            padding: 1rem 1.25rem !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1),
                0 0 0 1px rgba(255, 255, 255, 0.18) inset !important;
            border: 2px solid !important;
        }

        /* SUCCESS TOAST - Hijau */
        .swal2-toast-custom.swal2-popup.swal2-icon-success {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.95), rgba(5, 150, 105, 0.95)) !important;
            border-color: rgba(16, 185, 129, 0.5) !important;
        }

        .dark .swal2-toast-custom.swal2-popup.swal2-icon-success {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.9), rgba(5, 150, 105, 0.9)) !important;
        }

        /* ERROR TOAST - Merah */
        .swal2-toast-custom.swal2-popup.swal2-icon-error {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.95), rgba(220, 38, 38, 0.95)) !important;
            border-color: rgba(239, 68, 68, 0.5) !important;
        }

        .dark .swal2-toast-custom.swal2-popup.swal2-icon-error {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.9), rgba(220, 38, 38, 0.9)) !important;
        }

        /* WARNING TOAST - Kuning/Orange */
        .swal2-toast-custom.swal2-popup.swal2-icon-warning {
            background: linear-gradient(135deg, rgba(251, 146, 60, 0.95), rgba(249, 115, 22, 0.95)) !important;
            border-color: rgba(251, 146, 60, 0.5) !important;
        }

        .dark .swal2-toast-custom.swal2-popup.swal2-icon-warning {
            background: linear-gradient(135deg, rgba(251, 146, 60, 0.9), rgba(249, 115, 22, 0.9)) !important;
        }

        /* INFO TOAST - Biru */
        .swal2-toast-custom.swal2-popup.swal2-icon-info {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.95), rgba(37, 99, 235, 0.95)) !important;
            border-color: rgba(59, 130, 246, 0.5) !important;
        }

        .dark .swal2-toast-custom.swal2-popup.swal2-icon-info {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.9), rgba(37, 99, 235, 0.9)) !important;
        }

        /* NETWORK ERROR TOAST - Ungu/Purple */
        .swal2-toast-network.swal2-popup {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.95), rgba(124, 58, 237, 0.95)) !important;
            border-color: rgba(139, 92, 246, 0.5) !important;
        }

        .dark .swal2-toast-network.swal2-popup {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.9), rgba(124, 58, 237, 0.9)) !important;
        }

        /* Toast Title & Text - Selalu Putih untuk Toast Berwarna */
        .swal2-toast-custom .swal2-title {
            color: #ffffff !important;
            font-weight: 700 !important;
            font-size: 1.125rem !important;
            margin: 0 !important;
        }

        .swal2-toast-custom .swal2-html-container {
            color: rgba(255, 255, 255, 0.95) !important;
            font-size: 0.875rem !important;
            margin: 0.25rem 0 0 0 !important;
        }

        /* Toast Icon - Putih dan Lebih Kecil */
        .swal2-toast-custom .swal2-icon {
            width: 2rem !important;
            height: 2rem !important;
            margin: 0 0.75rem 0 0 !important;
            border-width: 2px !important;
            border-color: rgba(255, 255, 255, 0.8) !important;
        }

        .swal2-toast-custom.swal2-popup .swal2-icon .swal2-icon-content {
            color: #ffffff !important;
            font-size: 1.5rem !important;
        }

        .swal2-toast-custom.swal2-popup.swal2-icon-success .swal2-success-ring {
            border-color: rgba(255, 255, 255, 0.3) !important;
        }

        .swal2-toast-custom.swal2-popup.swal2-icon-success .swal2-success [class^='swal2-success-line'] {
            background-color: #ffffff !important;
        }

        .swal2-toast-custom.swal2-popup.swal2-icon-error .swal2-error [class^='swal2-x-mark-line'] {
            background-color: #ffffff !important;
        }

        /* Timer Progress Bar - Putih Semi-transparan */
        .swal2-toast-custom .swal2-timer-progress-bar {
            background: rgba(255, 255, 255, 0.5) !important;
        }

        /* Title styling (untuk Modal) */
        .swal2-title {
            color: #1f2937 !important;
            font-weight: 700 !important;
            font-size: 1.5rem !important;
            margin-bottom: 1rem !important;
        }

        .dark .swal2-title {
            color: #f9fafb !important;
        }

        /* HTML container & text (untuk Modal) */
        .swal2-html-container {
            color: #4b5563 !important;
            font-size: 1rem !important;
            line-height: 1.6 !important;
            margin: 0 !important;
        }

        .swal2-popup:not(.swal2-toast-custom) .swal2-html-container {
            margin: 1.25em 1.6em 0.3em !important;
        }

        .dark .swal2-html-container {
            color: #d1d5db !important;
        }

        /* Success icon (Modal) - Green */
        .swal2-icon.swal2-success {
            border-color: #10b981 !important;
        }

        .swal2-icon.swal2-success [class^='swal2-success-line'] {
            background-color: #10b981 !important;
        }

        .swal2-icon.swal2-success .swal2-success-ring {
            border-color: rgba(16, 185, 129, 0.3) !important;
        }

        /* Error icon (Modal) - Red */
        .swal2-icon.swal2-error {
            border-color: #ef4444 !important;
        }

        .swal2-icon.swal2-error [class^='swal2-x-mark-line'] {
            background-color: #ef4444 !important;
        }

        /* Warning icon (Modal) - Amber */
        .swal2-icon.swal2-warning {
            border-color: #f59e0b !important;
            color: #f59e0b !important;
        }

        /* Info icon (Modal) - Blue */
        .swal2-icon.swal2-info {
            border-color: #3b82f6 !important;
            color: #3b82f6 !important;
        }

        /* Confirm button - Indigo gradient */
        .swal2-confirm {
            background: linear-gradient(135deg, #6366f1, #4f46e5) !important;
            color: white !important;
            font-weight: 600 !important;
            padding: 0.75rem 2rem !important;
            border-radius: 12px !important;
            border: none !important;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4),
                0 0 0 1px rgba(255, 255, 255, 0.2) inset !important;
            transition: all 0.3s ease !important;
            font-size: 1rem !important;
        }

        .swal2-confirm:hover {
            background: linear-gradient(135deg, #4f46e5, #4338ca) !important;
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.5),
                0 0 0 1px rgba(255, 255, 255, 0.3) inset !important;
            transform: translateY(-2px);
        }

        /* Cancel button - Gray */
        .swal2-cancel {
            background: rgba(229, 231, 235, 0.8) !important;
            color: #374151 !important;
            font-weight: 600 !important;
            padding: 0.75rem 2rem !important;
            border-radius: 12px !important;
            border: 1px solid rgba(209, 213, 219, 0.8) !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1),
                0 0 0 1px rgba(255, 255, 255, 0.2) inset !important;
            transition: all 0.3s ease !important;
            font-size: 1rem !important;
        }

        .swal2-cancel:hover {
            background: rgba(209, 213, 219, 0.9) !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15),
                0 0 0 1px rgba(255, 255, 255, 0.3) inset !important;
            transform: translateY(-2px);
        }

        .dark .swal2-cancel {
            background: rgba(55, 65, 81, 0.8) !important;
            color: #f9fafb !important;
            border: 1px solid rgba(75, 85, 99, 0.8) !important;
        }

        /* Deny button */
        .swal2-deny {
            background: rgba(239, 68, 68, 0.1) !important;
            color: #dc2626 !important;
            font-weight: 600 !important;
            padding: 0.75rem 2rem !important;
            border-radius: 12px !important;
            border: 1px solid rgba(239, 68, 68, 0.3) !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.1) !important;
            transition: all 0.3s ease !important;
            font-size: 1rem !important;
        }

        .swal2-deny:hover {
            background: rgba(239, 68, 68, 0.2) !important;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2) !important;
            transform: translateY(-2px);
        }

        /* Actions container */
        .swal2-actions {
            gap: 0.75rem !important;
            margin-top: 1.5rem !important;
        }

        /* Timer progress bar (untuk Modal) */
        .swal2-timer-progress-bar {
            background: linear-gradient(90deg, #6366f1, #8b5cf6) !important;
            height: 4px !important;
        }

        /* Input fields */
        .swal2-input,
        .swal2-textarea {
            border: 2px solid rgba(209, 213, 219, 0.5) !important;
            border-radius: 12px !important;
            padding: 0.75rem 1rem !important;
            font-size: 1rem !important;
            transition: all 0.3s ease !important;
            background: rgba(255, 255, 255, 0.8) !important;
            backdrop-filter: blur(10px);
        }

        .swal2-input:focus,
        .swal2-textarea:focus {
            border-color: #6366f1 !important;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1) !important;
            outline: none !important;
        }

        .dark .swal2-input,
        .dark .swal2-textarea {
            background: rgba(31, 41, 55, 0.8) !important;
            color: #f9fafb !important;
            border-color: rgba(75, 85, 99, 0.5) !important;
        }

        .swal2-container.swal2-center {
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
        }

        /* Toast animations */
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .swal2-toast-custom.swal2-show {
            animation: slideInRight 0.3s ease-out;
        }

        /* Responsive adjustments */
        @media (max-width: 640px) {

            /* Target Modal SAJA, bukan toast */
            .swal2-popup:not(.swal2-toast-custom) {
                padding: 1.5rem !important;
                border-radius: 16px !important;
            }

            /* Target Ikon Modal SAJA */
            .swal2-popup:not(.swal2-toast-custom) .swal2-icon {
                width: 4rem !important;
                height: 4rem !important;
                margin: 1rem auto !important;
            }

            /* Target Title Modal SAJA */
            .swal2-popup:not(.swal2-toast-custom) .swal2-title {
                font-size: 1.25rem !important;
            }

            /* Buat TOAST full-width di mobile */
            .swal2-toast-custom.swal2-popup {
                width: auto !important;
                max-width: none !important;
                margin: 0.5em !important;
            }
        }
    </style>

    @yield('style')
</head>
@php
    $header = '';
@endphp

<body>
    {{-- Sidenav (Resizable) --}}
    <nav id="sidenav-8"
        class="fixed left-0 top-0 z-[1035] h-full min-h-[100vh] -translate-x-full overflow-hidden bg-white shadow-[0_4px_12px_0_rgba(0,0,0,0.07),_0_2px_4px_rgba(0,0,0,0.05)] data-[te-sidenav-hidden='false']:translate-x-0 dark:bg-zinc-800 invisible md:visible"
        data-te-sidenav-init data-te-sidenav-hidden="false" data-te-sidenav-position="fixed" data-te-sidenav-mode="side"
        data-te-sidenav-accordion="true">

        <a class="mb-3 flex flex-col items-center justify-center border-b-2 border-solid border-gray-100 py-6 outline-none"
            href="#" data-te-ripple-init data-te-ripple-color="primary">
            <div class="flex items-center justify-center space-x-3 mb-3">
                <img src="{{ asset('assets/logo/logo-robot.png') }}" class="h-8" alt="infor" loading="lazy" />
                <div class="border-l-2 border-gray-300 h-8"></div>
                <img src="{{ asset('assets/logo/logo-piciu.png') }}" class="h-8" alt="pcu" loading="lazy" />
            </div>
            <span class="text-center font-bold">Laboratorium Informatika <br>2025/2026</span>
        </a>
        <ul class="relative m-0 list-none px-[0.2rem] pb-12" data-te-sidenav-menu-ref>
            <li class="relative">
                <a id="overview"
                    class="flex cursor-pointer items-center truncate rounded-[5px] px-6 py-[0.45rem] text-[0.85rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10"
                    data-te-sidenav-link-ref href="{{ route('admin.dashboard') }}">
                    <span class="mr-4 [&>svg]:h-3.5 [&>svg]:w-3.5 [&>svg]:text-gray-400 dark:[&>svg]:text-gray-300">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                            class="h-3.5 w-3.5">
                            <path
                                d="M11.47 3.84a.75.75 0 011.06 0l8.69 8.69a.75.75 0 101.06-1.06l-8.689-8.69a2.25 2.25 0 00-3.182 0l-8.69 8.69a.75.75 0 001.061 1.06l8.69-8.69z" />
                            <path
                                d="M12 5.432l8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 01-.75-.75v-4.5a.75.75 0 00-.75-.75h-3a.75.75 0 00-.75.75V21a.75.75 0 01-.75.75H5.625a1.875 1.875 0 01-1.875-1.875v-6.198a2.29 2.29 0 00.091-.086L12 5.43z" />
                        </svg>
                    </span>
                    <span>Overview</span>
                </a>
            </li>
            <li class="relative pt-4">
                <span
                    class="px-6 py-4 text-[0.6rem] font-bold uppercase text-gray-600 dark:text-gray-400">Dashboards</span>
                <a id="labs"
                    class="flex cursor-pointer items-center truncate rounded-[5px] px-6 py-[0.45rem] text-[0.85rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10"
                    href="{{ route('admin.labs') }}" data-te-sidenav-link-ref>
                    <span class="mr-4 [&>svg]:h-3.5 [&>svg]:w-3.5 [&>svg]:text-gray-400 dark:[&>svg]:text-gray-300">
                        <svg class="w-[50px] h-[50px] fill-[#8e8e8e]" viewBox="0 0 576 512"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M264.5 5.2c14.9-6.9 32.1-6.9 47 0l218.6 101c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 149.8C37.4 145.8 32 137.3 32 128s5.4-17.9 13.9-21.8L264.5 5.2zM476.9 209.6l53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 277.8C37.4 273.8 32 265.3 32 256s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0l152-70.2zm-152 198.2l152-70.2 53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 405.8C37.4 401.8 32 393.3 32 384s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0z" />
                        </svg>
                    </span>
                    <span>Laboratories</span>
                </a>
                <a id="items"
                    class="flex cursor-pointer items-center truncate rounded-[5px] px-6 py-[0.45rem] text-[0.85rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10"
                    href="{{ route('admin.items') }}" data-te-sidenav-link-ref>
                    <span class="mr-4 [&>svg]:h-3.5 [&>svg]:w-3.5 [&>svg]:text-gray-400 dark:[&>svg]:text-gray-300">
                        <svg class="w-[50px] h-[50px] fill-[#8e8e8e]" viewBox="0 0 576 512"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M264.5 5.2c14.9-6.9 32.1-6.9 47 0l218.6 101c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 149.8C37.4 145.8 32 137.3 32 128s5.4-17.9 13.9-21.8L264.5 5.2zM476.9 209.6l53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 277.8C37.4 273.8 32 265.3 32 256s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0l152-70.2zm-152 198.2l152-70.2 53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 405.8C37.4 401.8 32 393.3 32 384s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0z" />
                        </svg>
                    </span>
                    <span>Inventaris</span>
                </a>
                <a id="sets"
                    class="flex cursor-pointer items-center truncate rounded-[5px] px-6 py-[0.45rem] text-[0.85rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10"
                    href="{{ route('admin.sets') }}" data-te-sidenav-link-ref>
                    <span class="mr-4 [&>svg]:h-3.5 [&>svg]:w-3.5 [&>svg]:text-gray-400 dark:[&>svg]:text-gray-300">
                        <svg class="w-[50px] h-[50px] fill-[#8e8e8e]" viewBox="0 0 576 512"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M264.5 5.2c14.9-6.9 32.1-6.9 47 0l218.6 101c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 149.8C37.4 145.8 32 137.3 32 128s5.4-17.9 13.9-21.8L264.5 5.2zM476.9 209.6l53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 277.8C37.4 273.8 32 265.3 32 256s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0l152-70.2zm-152 198.2l152-70.2 53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 405.8C37.4 401.8 32 393.3 32 384s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0z" />
                        </svg>
                    </span>
                    <span>Sets</span>
                </a>
                <a id="softwares"
                    class="flex cursor-pointer items-center truncate rounded-[5px] px-6 py-[0.45rem] text-[0.85rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10"
                    href="{{ route('admin.softwares') }}" data-te-sidenav-link-ref>
                    <span class="mr-4 [&>svg]:h-3.5 [&>svg]:w-3.5 [&>svg]:text-gray-400 dark:[&>svg]:text-gray-300">
                        <svg class="w-[50px] h-[50px] fill-[#8e8e8e]" viewBox="0 0 576 512"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M264.5 5.2c14.9-6.9 32.1-6.9 47 0l218.6 101c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 149.8C37.4 145.8 32 137.3 32 128s5.4-17.9 13.9-21.8L264.5 5.2zM476.9 209.6l53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 277.8C37.4 273.8 32 265.3 32 256s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0l152-70.2zm-152 198.2l152-70.2 53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 405.8C37.4 401.8 32 393.3 32 384s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0z" />
                        </svg>
                    </span>
                    <span>Softwares and OS</span>
                </a>
                <a id="repairs"
                    class="flex cursor-pointer items-center truncate rounded-[5px] px-6 py-[0.45rem] text-[0.85rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10"
                    href="{{ route('admin.repairs') }}" data-te-sidenav-link-ref>
                    <span class="mr-4 [&>svg]:h-3.5 [&>svg]:w-3.5 [&>svg]:text-gray-400 dark:[&>svg]:text-gray-300">
                        <svg class="w-[50px] h-[50px] fill-[#8e8e8e]" viewBox="0 0 576 512"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M264.5 5.2c14.9-6.9 32.1-6.9 47 0l218.6 101c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 149.8C37.4 145.8 32 137.3 32 128s5.4-17.9 13.9-21.8L264.5 5.2zM476.9 209.6l53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 277.8C37.4 273.8 32 265.3 32 256s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0l152-70.2zm-152 198.2l152-70.2 53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 405.8C37.4 401.8 32 393.3 32 384s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0z" />
                        </svg>
                    </span>
                    <span>Repairs</span>
                </a>
            </li>
            <li class="relative pt-4">
                <span class="px-6 py-4 text-[0.6rem] font-bold uppercase text-gray-600 dark:text-gray-400">Access
                    Controls</span>
                <a id="roles"
                    class="flex cursor-pointer items-center truncate rounded-[5px] px-6 py-[0.45rem] text-[0.85rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10"
                    href="{{ route('admin.roles') }}" data-te-sidenav-link-ref>
                    <span class="mr-4 [&>svg]:h-3.5 [&>svg]:w-3.5 [&>svg]:text-gray-400 dark:[&>svg]:text-gray-300">
                        <svg class="w-[50px] h-[50px] fill-[#8e8e8e]" viewBox="0 0 576 512"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M264.5 5.2c14.9-6.9 32.1-6.9 47 0l218.6 101c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 149.8C37.4 145.8 32 137.3 32 128s5.4-17.9 13.9-21.8L264.5 5.2zM476.9 209.6l53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 277.8C37.4 273.8 32 265.3 32 256s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0l152-70.2zm-152 198.2l152-70.2 53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 405.8C37.4 401.8 32 393.3 32 384s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0z" />
                        </svg>
                    </span>
                    <span>Manage Roles</span>
                </a>
                <a id="permissions"
                    class="flex cursor-pointer items-center truncate rounded-[5px] px-6 py-[0.45rem] text-[0.85rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10"
                    href="{{ route('admin.permissions') }}" data-te-sidenav-link-ref>
                    <span class="mr-4 [&>svg]:h-3.5 [&>svg]:w-3.5 [&>svg]:text-gray-400 dark:[&>svg]:text-gray-300">
                        <svg class="w-[50px] h-[50px] fill-[#8e8e8e]" viewBox="0 0 576 512"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M264.5 5.2c14.9-6.9 32.1-6.9 47 0l218.6 101c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 149.8C37.4 145.8 32 137.3 32 128s5.4-17.9 13.9-21.8L264.5 5.2zM476.9 209.6l53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 277.8C37.4 273.8 32 265.3 32 256s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0l152-70.2zm-152 198.2l152-70.2 53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 405.8C37.4 401.8 32 393.3 32 384s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0z" />
                        </svg>
                    </span>
                    <span>Manage Access</span>
                </a>
            </li>
            <li class="relative pt-4">
                <span class="px-6 py-4 text-[0.6rem] font-bold uppercase text-gray-600 dark:text-gray-400">Academics & Periods</span>
                <a id="periods"
                    class="flex cursor-pointer items-center truncate rounded-[5px] px-6 py-[0.45rem] text-[0.85rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10"
                    href="{{ route('admin.periods') }}" data-te-sidenav-link-ref>
                    <span class="mr-4 [&>svg]:h-3.5 [&>svg]:w-3.5 [&>svg]:text-gray-400 dark:[&>svg]:text-gray-300">
                        <svg class="w-[50px] h-[50px] fill-[#8e8e8e]" viewBox="0 0 576 512"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M264.5 5.2c14.9-6.9 32.1-6.9 47 0l218.6 101c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 149.8C37.4 145.8 32 137.3 32 128s5.4-17.9 13.9-21.8L264.5 5.2zM476.9 209.6l53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 277.8C37.4 273.8 32 265.3 32 256s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0l152-70.2zm-152 198.2l152-70.2 53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 405.8C37.4 401.8 32 393.3 32 384s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0z" />
                        </svg>
                    </span>
                    <span>Periods</span>
                </a>
            </li>
            <li class="relative pt-4">
                <span class="px-6 py-4 text-[0.6rem] font-bold uppercase text-gray-600 dark:text-gray-400">Reports and
                    Statistics</span>
                <a id="report_repairs"
                    class="flex cursor-pointer items-center truncate rounded-[5px] px-6 py-[0.45rem] text-[0.85rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10"
                    href="{{ route('admin.labs') }}" data-te-sidenav-link-ref>
                    <span class="mr-4 [&>svg]:h-3.5 [&>svg]:w-3.5 [&>svg]:text-gray-400 dark:[&>svg]:text-gray-300">
                        <svg class="w-[50px] h-[50px] fill-[#8e8e8e]" viewBox="0 0 576 512"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M264.5 5.2c14.9-6.9 32.1-6.9 47 0l218.6 101c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 149.8C37.4 145.8 32 137.3 32 128s5.4-17.9 13.9-21.8L264.5 5.2zM476.9 209.6l53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 277.8C37.4 273.8 32 265.3 32 256s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0l152-70.2zm-152 198.2l152-70.2 53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 405.8C37.4 401.8 32 393.3 32 384s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0z" />
                        </svg>
                    </span>
                    <span>Repairs Statistics</span>
                </a>
                <a id="report_bookings"
                    class="flex cursor-pointer items-center truncate rounded-[5px] px-6 py-[0.45rem] text-[0.85rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10"
                    href="{{ route('admin.items') }}" data-te-sidenav-link-ref>
                    <span class="mr-4 [&>svg]:h-3.5 [&>svg]:w-3.5 [&>svg]:text-gray-400 dark:[&>svg]:text-gray-300">
                        <svg class="w-[50px] h-[50px] fill-[#8e8e8e]" viewBox="0 0 576 512"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M264.5 5.2c14.9-6.9 32.1-6.9 47 0l218.6 101c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 149.8C37.4 145.8 32 137.3 32 128s5.4-17.9 13.9-21.8L264.5 5.2zM476.9 209.6l53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 277.8C37.4 273.8 32 265.3 32 256s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0l152-70.2zm-152 198.2l152-70.2 53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 405.8C37.4 401.8 32 393.3 32 384s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0z" />
                        </svg>
                    </span>
                    <span>Bookings Statistics</span>
                </a>
            </li>
        </ul>

        {{-- Resizer Handle --}}
        <div id="sidenav-resizer" title="Drag to resize sidebar"></div>
    </nav>

    {{-- Navbar Mobile --}}
    <nav
        class="flex-no-wrap relative flex w-full items-center justify-between bg-[#FBFBFB] py-2 shadow-md shadow-black/5 dark:bg-neutral-600 dark:shadow-black/10 lg:flex-wrap lg:justify-start lg:py-4 block md:hidden">
        <div class="flex w-full flex-wrap items-center justify-between px-3">
            <button
                class="block border-0 bg-transparent px-2 text-neutral-500 hover:no-underline hover:shadow-none focus:no-underline focus:shadow-none focus:outline-none focus:ring-0 dark:text-neutral-200 sm:hidden"
                type="button" data-te-collapse-init data-te-target="#navbarSupportedContent12"
                aria-controls="navbarSupportedContent12" aria-expanded="false" aria-label="Toggle navigation">
                <span class="[&>svg]:w-7">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-7 w-7">
                        <path fill-rule="evenodd"
                            d="M3 6.75A.75.75 0 013.75 6h16.5a.75.75 0 010 1.5H3.75A.75.75 0 013 6.75zM3 12a.75.75 0 01.75-.75h16.5a.75.75 0 010 1.5H3.75A.75.75 0 013 12zm0 5.25a.75.75 0 01.75-.75h16.5a.75.75 0 010 1.5H3.75a.75.75 0 01-.75-.75z"
                            clip-rule="evenodd" />
                    </svg>
                </span>
            </button>

            <div class="!visible hidden flex-grow basis-[100%] items-center sm:!flex sm:basis-auto"
                id="navbarSupportedContent12" data-te-collapse-item>
                <ul class="list-style-none mr-auto flex flex-col pl-0 sm:flex-row" data-te-navbar-nav-ref>
                    <li class="my-4 pl-2 sm:my-0 sm:pl-0 sm:pr-1" data-te-nav-item-ref>
                        <a class="text-neutral-500 transition duration-200 hover:text-neutral-700 hover:ease-in-out focus:text-neutral-700 disabled:text-black/30 motion-reduce:transition-none dark:text-neutral-200 dark:hover:text-neutral-300 dark:focus:text-neutral-300 sm:px-2 [&.active]:text-black/90 dark:[&.active]:text-zinc-400"
                            href="{{ route('admin.dashboard') }}" data-te-nav-link-ref>Overview</a>
                    </li>
                    <li class="relative pt-4">
                        <span
                            class="px-6 py-4 text-[0.6rem] font-bold uppercase text-gray-600 dark:text-gray-400">Dashboards</span>
                        <a class="flex cursor-pointer items-center truncate rounded-[5px] px-6 py-[0.45rem] text-[0.85rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10"
                            href="{{ route('admin.labs') }}" data-te-sidenav-link-ref>
                            <span
                                class="mr-4 [&>svg]:h-3.5 [&>svg]:w-3.5 [&>svg]:text-gray-400 dark:[&>svg]:text-gray-300">
                                <svg class="w-[50px] h-[50px] fill-[#8e8e8e]" viewBox="0 0 576 512"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M264.5 5.2c14.9-6.9 32.1-6.9 47 0l218.6 101c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 149.8C37.4 145.8 32 137.3 32 128s5.4-17.9 13.9-21.8L264.5 5.2zM476.9 209.6l53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 277.8C37.4 273.8 32 265.3 32 256s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0l152-70.2zm-152 198.2l152-70.2 53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 405.8C37.4 401.8 32 393.3 32 384s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0z">
                                    </path>
                                </svg>
                            </span>
                            <span>Laboratories</span>
                        </a>
                        <a class="flex cursor-pointer items-center truncate rounded-[5px] px-6 py-[0.45rem] text-[0.85rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10"
                            href="{{ route('admin.items') }}" data-te-sidenav-link-ref>
                            <span
                                class="mr-4 [&>svg]:h-3.5 [&>svg]:w-3.5 [&>svg]:text-gray-400 dark:[&>svg]:text-gray-300">
                                <svg class="w-[50px] h-[50px] fill-[#8e8e8e]" viewBox="0 0 576 512"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M264.5 5.2c14.9-6.9 32.1-6.9 47 0l218.6 101c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 149.8C37.4 145.8 32 137.3 32 128s5.4-17.9 13.9-21.8L264.5 5.2zM476.9 209.6l53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 277.8C37.4 273.8 32 265.3 32 256s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0l152-70.2zm-152 198.2l152-70.2 53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 405.8C37.4 401.8 32 393.3 32 384s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0z">
                                    </path>
                                </svg>
                            </span>
                            <span>Inventaris</span>
                        </a>
                        <a class="flex cursor-pointer items-center truncate rounded-[5px] px-6 py-[0.45rem] text-[0.85rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10"
                            href="{{ route('admin.sets') }}" data-te-sidenav-link-ref>
                            <span
                                class="mr-4 [&>svg]:h-3.5 [&>svg]:w-3.5 [&>svg]:text-gray-400 dark:[&>svg]:text-gray-300">
                                <svg class="w-[50px] h-[50px] fill-[#8e8e8e]" viewBox="0 0 576 512"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M264.5 5.2c14.9-6.9 32.1-6.9 47 0l218.6 101c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 149.8C37.4 145.8 32 137.3 32 128s5.4-17.9 13.9-21.8L264.5 5.2zM476.9 209.6l53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 277.8C37.4 273.8 32 265.3 32 256s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0l152-70.2zm-152 198.2l152-70.2 53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 405.8C37.4 401.8 32 393.3 32 384s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0z">
                                    </path>
                                </svg>
                            </span>
                            <span>Sets</span>
                        </a>
                        <a class="flex cursor-pointer items-center truncate rounded-[5px] px-6 py-[0.45rem] text-[0.85rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10"
                            href="{{ route('admin.softwares') }}" data-te-sidenav-link-ref>
                            <span
                                class="mr-4 [&>svg]:h-3.5 [&>svg]:w-3.5 [&>svg]:text-gray-400 dark:[&>svg]:text-gray-300">
                                <svg class="w-[50px] h-[50px] fill-[#8e8e8e]" viewBox="0 0 576 512"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M264.5 5.2c14.9-6.9 32.1-6.9 47 0l218.6 101c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 149.8C37.4 145.8 32 137.3 32 128s5.4-17.9 13.9-21.8L264.5 5.2zM476.9 209.6l53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 277.8C37.4 273.8 32 265.3 32 256s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0l152-70.2zm-152 198.2l152-70.2 53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 405.8C37.4 401.8 32 393.3 32 384s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0z">
                                    </path>
                                </svg>
                            </span>
                            <span>Softwares and OS</span>
                        </a>
                        <a class="flex cursor-pointer items-center truncate rounded-[5px] px-6 py-[0.45rem] text-[0.85rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10"
                            href="{{ route('admin.repairs') }}" data-te-sidenav-link-ref>
                            <span
                                class="mr-4 [&>svg]:h-3.5 [&>svg]:w-3.5 [&>svg]:text-gray-400 dark:[&>svg]:text-gray-300">
                                <svg class="w-[50px] h-[50px] fill-[#8e8e8e]" viewBox="0 0 576 512"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M264.5 5.2c14.9-6.9 32.1-6.9 47 0l218.6 101c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 149.8C37.4 145.8 32 137.3 32 128s5.4-17.9 13.9-21.8L264.5 5.2zM476.9 209.6l53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 277.8C37.4 273.8 32 265.3 32 256s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0l152-70.2zm-152 198.2l152-70.2 53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 405.8C37.4 401.8 32 393.3 32 384s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0z">
                                    </path>
                                </svg>
                            </span>
                            <span>Repairs</span>
                        </a>
                    </li>
                    <li class="relative pt-4">
                        <span
                            class="px-6 py-4 text-[0.6rem] font-bold uppercase text-gray-600 dark:text-gray-400">Access
                            Controls</span>
                        <a class="flex cursor-pointer items-center truncate rounded-[5px] px-6 py-[0.45rem] text-[0.85rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10"
                            href="{{ route('admin.roles') }}" data-te-sidenav-link-ref>
                            <span
                                class="mr-4 [&>svg]:h-3.5 [&>svg]:w-3.5 [&>svg]:text-gray-400 dark:[&>svg]:text-gray-300">
                                <svg class="w-[50px] h-[50px] fill-[#8e8e8e]" viewBox="0 0 576 512"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M264.5 5.2c14.9-6.9 32.1-6.9 47 0l218.6 101c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 149.8C37.4 145.8 32 137.3 32 128s5.4-17.9 13.9-21.8L264.5 5.2zM476.9 209.6l53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 277.8C37.4 273.8 32 265.3 32 256s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0l152-70.2zm-152 198.2l152-70.2 53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 405.8C37.4 401.8 32 393.3 32 384s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0z">
                                    </path>
                                </svg>
                            </span>
                            <span>Manage Roles</span>
                        </a>
                        <a class="flex cursor-pointer items-center truncate rounded-[5px] px-6 py-[0.45rem] text-[0.85rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10"
                            href="{{ route('admin.permissions') }}" data-te-sidenav-link-ref>
                            <span
                                class="mr-4 [&>svg]:h-3.5 [&>svg]:w-3.5 [&>svg]:text-gray-400 dark:[&>svg]:text-gray-300">
                                <svg class="w-[50px] h-[50px] fill-[#8e8e8e]" viewBox="0 0 576 512"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M264.5 5.2c14.9-6.9 32.1-6.9 47 0l218.6 101c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 149.8C37.4 145.8 32 137.3 32 128s5.4-17.9 13.9-21.8L264.5 5.2zM476.9 209.6l53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 277.8C37.4 273.8 32 265.3 32 256s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0l152-70.2zm-152 198.2l152-70.2 53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 405.8C37.4 401.8 32 393.3 32 384s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0z">
                                    </path>
                                </svg>
                            </span>
                            <span>Manage Access</span>
                        </a>
                    </li>
                    <li class="relative pt-4">
                        <span
                            class="px-6 py-4 text-[0.6rem] font-bold uppercase text-gray-600 dark:text-gray-400">Academic & Periods</span>
                        <a class="flex cursor-pointer items-center truncate rounded-[5px] px-6 py-[0.45rem] text-[0.85rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10"
                            href="{{ route('admin.periods') }}" data-te-sidenav-link-ref>
                            <span
                                class="mr-4 [&>svg]:h-3.5 [&>svg]:w-3.5 [&>svg]:text-gray-400 dark:[&>svg]:text-gray-300">
                                <svg class="w-[50px] h-[50px] fill-[#8e8e8e]" viewBox="0 0 576 512"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M264.5 5.2c14.9-6.9 32.1-6.9 47 0l218.6 101c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 149.8C37.4 145.8 32 137.3 32 128s5.4-17.9 13.9-21.8L264.5 5.2zM476.9 209.6l53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 277.8C37.4 273.8 32 265.3 32 256s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0l152-70.2zm-152 198.2l152-70.2 53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 405.8C37.4 401.8 32 393.3 32 384s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0z">
                                    </path>
                                </svg>
                            </span>
                            <span>Manage Periods</span>
                        </a>
                    </li>
                    <li class="relative pt-4">
                        <span
                            class="px-6 py-4 text-[0.6rem] font-bold uppercase text-gray-600 dark:text-gray-400">Reports
                            and Statistics</span>
                        <a class="flex cursor-pointer items-center truncate rounded-[5px] px-6 py-[0.45rem] text-[0.85rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10"
                            href="{{ route('admin.permissions') }}" data-te-sidenav-link-ref>
                            <span
                                class="mr-4 [&>svg]:h-3.5 [&>svg]:w-3.5 [&>svg]:text-gray-400 dark:[&>svg]:text-gray-300">
                                <svg class="w-[50px] h-[50px] fill-[#8e8e8e]" viewBox="0 0 576 512"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M264.5 5.2c14.9-6.9 32.1-6.9 47 0l218.6 101c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 149.8C37.4 145.8 32 137.3 32 128s5.4-17.9 13.9-21.8L264.5 5.2zM476.9 209.6l53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 277.8C37.4 273.8 32 265.3 32 256s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0l152-70.2zm-152 198.2l152-70.2 53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 405.8C37.4 401.8 32 393.3 32 384s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0z">
                                    </path>
                                </svg>
                            </span>
                            <span>Repairs Statistics</span>
                        </a>
                        <a class="flex cursor-pointer items-center truncate rounded-[5px] px-6 py-[0.45rem] text-[0.85rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10"
                            href="{{ route('admin.permissions') }}" data-te-sidenav-link-ref>
                            <span
                                class="mr-4 [&>svg]:h-3.5 [&>svg]:w-3.5 [&>svg]:text-gray-400 dark:[&>svg]:text-gray-300">
                                <svg class="w-[50px] h-[50px] fill-[#8e8e8e]" viewBox="0 0 576 512"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M264.5 5.2c14.9-6.9 32.1-6.9 47 0l218.6 101c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 149.8C37.4 145.8 32 137.3 32 128s5.4-17.9 13.9-21.8L264.5 5.2zM476.9 209.6l53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 277.8C37.4 273.8 32 265.3 32 256s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0l152-70.2zm-152 198.2l152-70.2 53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 405.8C37.4 401.8 32 393.3 32 384s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0z">
                                    </path>
                                </svg>
                            </span>
                            <span>Bookings Statistics</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>


    {{-- Konten Utama --}}
    <div id="main-content" class="px-3 md:px-8 py-2 md:py-3">
        <div class="mt-3">
            @yield('body')
        </div>
    </div>

    {{-- Custom Modal --}}
    <div id="layout-modal" class="hidden" role="dialog" aria-modal="true" aria-labelledby="layout-modal-title">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 z-[2000]"></div>
        <div id="layout-modal-overlay" class="fixed inset-0 flex items-center justify-center p-4 z-[2001]">
            <div id="layout-modal-area"
                class="relative bg-white rounded-lg shadow-xl w-full max-w-3xl flex flex-col max-h-[90vh]">
                <div class="flex justify-between items-center p-4 border-b border-gray-200 rounded-t">
                    <h3 id="layout-modal-title" class="text-xl font-semibold text-gray-900"></h3>
                    <button id="layout-modal-close-button" type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <div id="layout-modal-body" class="p-6 space-y-6 overflow-y-auto">
                </div>
                <div class="flex items-center justify-end p-4 space-x-2 border-t border-gray-200 rounded-b">
                    <button id="layout-modal-footer-close-button" type="button"
                        class="text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:outline-none focus:ring-indigo-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/tw-elements/dist/js/tw-elements.umd.min.js"></script>

    <script>
        // Setup CSRF untuk JQuery AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function showLoadingToast(message = 'Loading...') {
            const toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 10000
            });
            
            toast.fire({
                icon: 'info',
                title: message,
                didOpen: () => Swal.showLoading()
            });
        }

        function showToast(title, message = '', type = 'success', autoHideTimeout = 3000) {
            const iconMap = {
                success: 'success',
                error: 'error',
                warning: 'warning',
                info: 'info'
            };

            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: autoHideTimeout,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                },
                customClass: {
                    popup: 'swal2-popup swal2-toast-custom'
                }
            });

            Toast.fire({
                icon: iconMap[type] || 'success',
                title: title,
                text: message
            });
        }

        /**
         * -----------------------------------------------------------------
         * CONTOH PENGGUNAAN showToast()
         * -----------------------------------------------------------------
         * showToast('Berhasil!', 'Data telah disimpan', 'success');
         * showToast('Error!', 'Gagal menyimpan data', 'error');
         * showToast('Peringatan', 'Periksa data Anda', 'warning');
         * showToast('Info', 'Proses sedang berjalan', 'info');
         */

        // Event Listener untuk semua fungsionalitas layout
        document.addEventListener('DOMContentLoaded', function() {

            const layoutModal = document.getElementById('layout-modal');
            const layoutModalTitle = document.getElementById('layout-modal-title');
            const layoutModalBody = document.getElementById('layout-modal-body');
            const overlay = document.getElementById('layout-modal-overlay');
            const closeButtonHeader = document.getElementById('layout-modal-close-button');
            const closeButtonFooter = document.getElementById('layout-modal-footer-close-button');

            window.showLayoutModal = (title, bodyHTML) => {
                if (layoutModal && layoutModalTitle && layoutModalBody) {
                    layoutModalTitle.textContent = title;
                    layoutModalBody.innerHTML = bodyHTML;
                    layoutModal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                }
            };

            window.hideLayoutModal = () => {
                if (layoutModal) {
                    layoutModal.classList.add('hidden');
                    document.body.style.overflow = '';
                }
            };

            if (closeButtonHeader) closeButtonHeader.addEventListener('click', window.hideLayoutModal);
            if (closeButtonFooter) closeButtonFooter.addEventListener('click', window.hideLayoutModal);

            if (overlay) {
                overlay.addEventListener('click', function(event) {
                    if (event.target === overlay) {
                        window.hideLayoutModal();
                    }
                });
            }

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && layoutModal && !layoutModal.classList.contains('hidden')) {
                    window.hideLayoutModal();
                }
            });

            // ============================================
            // LOGIKA RESIZE SIDEBAR (FIXED VERSION)
            // ============================================
            const resizer = document.getElementById('sidenav-resizer');
            const sidebar = document.getElementById('sidenav-8');
            const mainContent = document.getElementById('main-content');

            const minWidth = 220; // Lebar minimal sidebar (px)
            const maxWidth = 500; // Lebar maksimal sidebar (px)

            // Load saved width dari localStorage
            const savedWidth = localStorage.getItem('sidebarWidth');
            if (savedWidth) {
                const newWidth = Math.max(minWidth, Math.min(parseInt(savedWidth), maxWidth));
                document.documentElement.style.setProperty('--sidebar-width', newWidth + 'px');
            }

            // Event handler saat drag
            const doResize = (e) => {
                const newWidth = Math.max(minWidth, Math.min(e.clientX, maxWidth));
                document.documentElement.style.setProperty('--sidebar-width', newWidth + 'px');
            };

            // Event handler saat selesai drag
            const stopResize = () => {
                document.removeEventListener('mousemove', doResize);
                document.removeEventListener('mouseup', stopResize);
                document.body.classList.remove('is-resizing');

                const currentWidth = document.documentElement.style.getPropertyValue('--sidebar-width');
                localStorage.setItem('sidebarWidth', parseInt(currentWidth));
            };

            // Event handler saat mulai drag
            if (resizer) {
                resizer.addEventListener('mousedown', (e) => {
                    e.preventDefault();
                    document.addEventListener('mousemove', doResize);
                    document.addEventListener('mouseup', stopResize);
                    document.body.classList.add('is-resizing');
                });
            }
        });
    </script>

    @yield('script')

    {{-- Script untuk Flash Session --}}
    @if (session('error-access'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Akses Ditolak',
                text: '{{ session('error-access') }}',
                confirmButtonText: 'OK'
            })
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: '{{ session('error') }}',
                confirmButtonText: 'OK'
            });
        </script>
    @endif

    @if (session('success'))
        <script>
            showToast('Berhasil!', '{{ session('success') }}', 'success');
        </script>
    @endif
</body>

</html>
