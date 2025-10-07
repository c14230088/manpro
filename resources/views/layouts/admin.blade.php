<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.css" />
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.js"></script>

    <title>Admin | @if (isset($title))
            {{ $title }}
        @else
            @yield('title', 'Lab Infor')
        @endif
    </title>
    <link rel="icon" href="{{ asset('assets/logo/logo-robot.png') }}" type="image/svg+xml" />
    {{-- sweetalert cdn --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tw-elements/dist/css/tw-elements.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tw-elements/css/tw-elements.min.css" />
    <script src="https://cdn.tailwindcss.com/3.3.0"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            corePlugins: {
                preflight: false,
            },
        };
    </script>

    @yield('style')
</head>
@php
    $header = '';
@endphp

<body>
    <nav id="sidenav-8"
        class="fixed left-0 top-0 z-[1035] h-full min-h-[100vh] w-60 -translate-x-full overflow-hidden bg-white shadow-[0_4px_12px_0_rgba(0,0,0,0.07),_0_2px_4px_rgba(0,0,0,0.05)] data-[te-sidenav-hidden='false']:translate-x-0 dark:bg-zinc-800 invisible md:visible"
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
                <span class="px-6 py-4 text-[0.6rem] font-bold uppercase text-gray-600 dark:text-gray-400">View</span>
                <a id="labs"
                    class="flex cursor-pointer items-center truncate rounded-[5px] px-6 py-[0.45rem] text-[0.85rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10"
                    href="{{ route('admin.labs') }}" data-te-sidenav-link-ref>
                    <span class="mr-4 [&>svg]:h-3.5 [&>svg]:w-3.5 [&>svg]:text-gray-400 dark:[&>svg]:text-gray-300">
                        <svg class="w-[50px] h-[50px] fill-[#8e8e8e]" viewBox="0 0 576 512"
                            xmlns="http://www.w3.org/2000/svg">

                            <!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                            <path
                                d="M264.5 5.2c14.9-6.9 32.1-6.9 47 0l218.6 101c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 149.8C37.4 145.8 32 137.3 32 128s5.4-17.9 13.9-21.8L264.5 5.2zM476.9 209.6l53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 277.8C37.4 273.8 32 265.3 32 256s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0l152-70.2zm-152 198.2l152-70.2 53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 405.8C37.4 401.8 32 393.3 32 384s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0z">
                            </path>

                        </svg>
                    </span>
                    <span>Laboratories</span>
                </a>
            </li>

            {{-- <li class="relative pt-4">
                <span class="px-6 py-4 text-[0.6rem] font-bold uppercase text-gray-600 dark:text-gray-400">Rally
                    Games</span>
            </li> --}}

            {{-- <li class="relative pt-6">
                <span class="px-6 py-4 text-[0.6rem] font-bold uppercase text-gray-600 dark:text-gray-400">Logout</span>
                <a href="{{ route('admin.logout') }}"
                    class="flex cursor-pointer items-center truncate rounded-[5px] px-6 py-[0.45rem] text-[0.85rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10"
                    data-te-sidenav-link-ref>
                    <span class="mr-4 [&>svg]:h-3.5 [&>svg]:w-3.5 [&>svg]:text-gray-400 dark:[&>svg]:text-gray-300">
                        <svg class="w-[24px] h-[24px] fill-[#8e8e8e]" viewBox="0 0 512 512"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M377.9 105.9L500.7 228.7c7.2 7.2 11.3 17.1 11.3 27.3s-4.1 20.1-11.3 27.3L377.9 406.1c-6.4 6.4-15 9.9-24 9.9c-18.7 0-33.9-15.2-33.9-33.9l0-62.1-128 0c-17.7 0-32-14.3-32-32l0-64c0-17.7 14.3-32 32-32l128 0 0-62.1c0-18.7 15.2-33.9 33.9-33.9c9 0 17.6 3.6 24 9.9zM160 96L96 96c-17.7 0-32 14.3-32 32l0 256c0 17.7 14.3 32 32 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-64 0c-53 0-96-43-96-96L0 128C0 75 43 32 96 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32z">
                            </path>
                        </svg>
                    </span>
                    <span>Logout</span>
                </a>
            </li> --}}
        </ul>
    </nav>

    {{-- NAVBAR --}}
    <!-- Main navigation container -->
    <nav
        class="flex-no-wrap relative flex w-full items-center justify-between bg-[#FBFBFB] py-2 shadow-md shadow-black/5 dark:bg-neutral-600 dark:shadow-black/10 lg:flex-wrap lg:justify-start lg:py-4 block md:hidden">
        <div class="flex w-full flex-wrap items-center justify-between px-3">
            <!-- Hamburger button for mobile view -->
            <button
                class="block border-0 bg-transparent px-2 text-neutral-500 hover:no-underline hover:shadow-none focus:no-underline focus:shadow-none focus:outline-none focus:ring-0 dark:text-neutral-200 sm:hidden"
                type="button" data-te-collapse-init data-te-target="#navbarSupportedContent12"
                aria-controls="navbarSupportedContent12" aria-expanded="false" aria-label="Toggle navigation">
                <!-- Hamburger icon -->
                <span class="[&>svg]:w-7">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-7 w-7">
                        <path fill-rule="evenodd"
                            d="M3 6.75A.75.75 0 013.75 6h16.5a.75.75 0 010 1.5H3.75A.75.75 0 013 6.75zM3 12a.75.75 0 01.75-.75h16.5a.75.75 0 010 1.5H3.75A.75.75 0 013 12zm0 5.25a.75.75 0 01.75-.75h16.5a.75.75 0 010 1.5H3.75a.75.75 0 01-.75-.75z"
                            clip-rule="evenodd" />
                    </svg>
                </span>
            </button>

            <!-- Collapsible navigation container -->
            <div class="!visible hidden flex-grow basis-[100%] items-center sm:!flex sm:basis-auto"
                id="navbarSupportedContent12" data-te-collapse-item>
                <!-- Left navigation links -->
                <ul class="list-style-none mr-auto flex flex-col pl-0 sm:flex-row" data-te-navbar-nav-ref>
                    {{-- Dashboard --}}
                    <li class="my-4 pl-2 sm:my-0 sm:pl-0 sm:pr-1" data-te-nav-item-ref>
                        <a class="text-neutral-500 transition duration-200 hover:text-neutral-700 hover:ease-in-out focus:text-neutral-700 disabled:text-black/30 motion-reduce:transition-none dark:text-neutral-200 dark:hover:text-neutral-300 dark:focus:text-neutral-300 sm:px-2 [&.active]:text-black/90 dark:[&.active]:text-zinc-400"
                            href="{{ route('admin.dashboard') }}" data-te-nav-link-ref>Overview</a>
                    </li>


                    <li class="relative pt-4">
                        <span
                            class="px-6 py-4 text-[0.6rem] font-bold uppercase text-gray-600 dark:text-gray-400">View</span>
                        <a class="flex cursor-pointer items-center truncate rounded-[5px] px-6 py-[0.45rem] text-[0.85rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10"
                            href="{{ route('admin.labs') }}" data-te-sidenav-link-ref>
                            <span
                                class="mr-4 [&>svg]:h-3.5 [&>svg]:w-3.5 [&>svg]:text-gray-400 dark:[&>svg]:text-gray-300">
                                <svg class="w-[50px] h-[50px] fill-[#8e8e8e]" viewBox="0 0 576 512"
                                    xmlns="http://www.w3.org/2000/svg">

                                    <!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                    <path
                                        d="M264.5 5.2c14.9-6.9 32.1-6.9 47 0l218.6 101c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 149.8C37.4 145.8 32 137.3 32 128s5.4-17.9 13.9-21.8L264.5 5.2zM476.9 209.6l53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 277.8C37.4 273.8 32 265.3 32 256s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0l152-70.2zm-152 198.2l152-70.2 53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 405.8C37.4 401.8 32 393.3 32 384s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0z">
                                    </path>

                                </svg>
                            </span>
                            <span>Laboratories</span>
                        </a>
                    </li>

                    {{-- <li class="relative pt-4">
                        <span class="px-6 py-4 text-[0.6rem] font-bold uppercase text-gray-600 dark:text-gray-400">Rally
                            Games</span>
                    </li> --}}
                </ul>
            </div>

            <!-- Right elements -->
            {{-- <div class="relative flex items-center">
                <!-- Logout Icon -->
                <a class="pl-2 my-auto sm:mb-0 sm:mr-4 text-secondary-500 transition duration-200 hover:text-secondary-400 hover:ease-in-out focus:text-secondary-400 disabled:text-black/30 motion-reduce:transition-none"
                    href="{{ route('admin.logout') }}">
                    <span class="[&>svg]:w-5">
                        <svg class="w-[24px] h-[24px] fill-[#ff6b6b]" viewBox="0 0 512 512"
                            xmlns="http://www.w3.org/2000/svg">

                            <!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                            <path
                                d="M377.9 105.9L500.7 228.7c7.2 7.2 11.3 17.1 11.3 27.3s-4.1 20.1-11.3 27.3L377.9 406.1c-6.4 6.4-15 9.9-24 9.9c-18.7 0-33.9-15.2-33.9-33.9l0-62.1-128 0c-17.7 0-32-14.3-32-32l0-64c0-17.7 14.3-32 32-32l128 0 0-62.1c0-18.7 15.2-33.9 33.9-33.9c9 0 17.6 3.6 24 9.9zM160 96L96 96c-17.7 0-32 14.3-32 32l0 256c0 17.7 14.3 32 32 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-64 0c-53 0-96-43-96-96L0 128C0 75 43 32 96 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32z">
                            </path>
                        </svg>
                    </span>
                </a>
            </div> --}}
        </div>
    </nav>


    <div class="ml-0 md:ml-60 px-3 md:px-8 py-2 md:py-3">
        <div class="mt-3">
            @yield('body')
        </div>
    </div>

    <div id="toast-container"
        class="grid gap-4 w-96 z-10 fixed top-6 sm:right-4 right-[50%] sm:translate-x-0 translate-x-1/2 max-w-[95%] sm:max-w-full">
        <div class="pointer-events-auto mx-auto hidden w-full rounded-lg bg-danger-300 bg-clip-padding text-sm shadow-lg shadow-black/5 data-[te-toast-show]:block data-[te-toast-hide]:hidden dark:bg-neutral-600"
            id="static-example" role="alert" aria-live="assertive" aria-atomic="true" data-te-autohide="false"
            data-te-toast-init data-te-toast-hide>
            <div
                class="flex items-center justify-between rounded-t-lg border-b-2 border-neutral-100 border-opacity-100 bg-danger-300 bg-clip-padding px-4 pb-2 pt-2.5 dark:border-opacity-50 dark:bg-neutral-600">
                <p class="font-bold text-neutral-500 dark:text-neutral-200">
                    MDBootstrap
                </p>
                <div class="flex items-center">
                    <p class="text-xs text-neutral-600 dark:text-neutral-300">
                        11 mins ago
                    </p>
                    <button type="button"
                        class="ml-2 box-content rounded-none border-none opacity-80 hover:no-underline hover:opacity-75 focus:opacity-100 focus:shadow-none focus:outline-none"
                        data-te-toast-dismiss aria-label="Close">
                        <span
                            class="w-[1em] focus:opacity-100 disabled:pointer-events-none disabled:select-none disabled:opacity-25 [&.disabled]:pointer-events-none [&.disabled]:select-none [&.disabled]:opacity-25">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </span>
                    </button>
                </div>
            </div>
            <div
                class="break-words rounded-b-lg bg-danger-300 px-4 py-4 text-neutral-700 dark:bg-neutral-600 dark:text-neutral-200">
                Static Example
            </div>
        </div>
    </div>

    <div id="layout-modal" class="hidden" role="dialog" aria-modal="true" aria-labelledby="layout-modal-title">

        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 z-[2000]"></div>

        <div id="layout-modal-overlay" class="fixed inset-0 flex items-center justify-center p-4 z-[2001]">

            <div id="layout-modal-area"
                class="relative bg-white rounded-lg shadow-xl w-full max-w-3xl flex flex-col max-h-[90vh]">

                <div class="flex justify-between items-center p-4 border-b border-gray-200 rounded-t">
                    <h3 id="layout-modal-title" class="text-xl font-semibold text-gray-900">
                    </h3>
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

    <!-- Sidenav -->
    <script src="https://cdn.jsdelivr.net/npm/tw-elements/js/tw-elements.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tw-elements/dist/js/tw-elements.umd.min.js"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        const TOAST_CONTAINER_LIMIT = 2;

        function showToast(title, message, type = 'success', autoHideTimeout = 2000) {
            const TYPE = {
                success: {
                    bg: 'bg-success-100',
                    text: 'text-success-700',
                    border: 'border-success/20',
                    svg: '<path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd"></path>'
                },
                error: {
                    bg: 'bg-danger-100',
                    text: 'text-danger-700',
                    border: 'border-danger-200',
                    svg: '<path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zm-1.72 6.97a.75.75 0 10-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 101.06 1.06L12 13.06l1.72 1.72a.75.75 0 101.06-1.06L13.06 12l1.72-1.72a.75.75 0 10-1.06-1.06L12 10.94l-1.72-1.72z" clip-rule="evenodd"></path>'
                },
                warning: {
                    bg: 'bg-warning-100',
                    text: 'text-warning-700',
                    border: 'border-warning-200',
                    svg: '<path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003zM12 8.25a.75.75 0 01.75.75v3.75a.75.75 0 01-1.5 0V9a.75.75 0 01.75-.75zm0 8.25a.75.75 0 100-1.5.75.75 0 000 1.5z" clip-rule="evenodd"></path>'
                },
                info: {
                    bg: 'bg-primary-100',
                    text: 'text-primary-700',
                    border: 'border-primary-200',
                    svg: '<path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm8.706-1.442c1.146-.573 2.437.463 2.126 1.706l-.709 2.836.042-.02a.75.75 0 01.67 1.34l-.04.022c-1.147.573-2.438-.463-2.127-1.706l.71-2.836-.042.02a.75.75 0 11-.671-1.34l.041-.022zM12 9a.75.75 0 100-1.5.75.75 0 000 1.5z" clip-rule="evenodd"></path>'
                }
            }

            const toastTemplate = `
            <div
                class="toast max-w-full pointer-events-auto mx-auto mb-4 hidden w-96 rounded-lg ${TYPE[type].bg} bg-clip-padding text-sm ${TYPE[type].text} shadow-lg shadow-black/5 data-[te-toast-show]:block data-[te-toast-hide]:hidden"
                role="alert" aria-live="assertive" aria-atomic="true" data-te-autohide="false">
                <div
                    class="flex items-center justify-between rounded-t-lg border-b-2 ${TYPE[type].border} ${TYPE[type].bg} bg-clip-padding px-4 pb-2 pt-2.5 ${TYPE[type].text}">
                    <p class="flex items-center font-bold ${TYPE[type].text}">
                        <span class="mr-2 h-4 w-4">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                ${TYPE[type].svg}
                            </svg>
                        </span>
                        ${title}
                    </p>
                    <div class="flex items-center">
                        <button type="button"
                            class="ml-2 box-content rounded-none border-none opacity-80 hover:no-underline hover:opacity-75 focus:opacity-100 focus:shadow-none focus:outline-none"
                            data-te-toast-dismiss aria-label="Close">
                            <span
                                class="w-[1em] focus:opacity-100 disabled:pointer-events-none disabled:select-none disabled:opacity-25 [&.disabled]:pointer-events-none [&.disabled]:select-none [&.disabled]:opacity-25">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="h-6 w-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </span>
                        </button>
                    </div>
                </div>
                <div class="message break-words rounded-b-lg ${TYPE[type].bg} px-4 py-4 ${TYPE[type].text}">
                    ${message}
                </div>
            </div>
            `
            const toast = $(toastTemplate).prependTo('#toast-container');
            $('.toast').attr('data-te-toast-show', true);
            $('.toast').attr('data-te-toast-init', true);

            const alertCount = $('#toast-container').children().length;
            if (alertCount > TOAST_CONTAINER_LIMIT) {
                $('#toast-container').children().last().remove();
            }

            setTimeout(() => {
                toast.find('button').click();
                setTimeout(() => {
                    toast.remove();
                }, 400);
            }, autoHideTimeout);
        }

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
                    if (event.target === overlay && event.target != document.getElementById('layout-modal-area')) {
                        window.hideLayoutModal();
                    }
                });
            }
            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && layoutModal && !layoutModal.classList.contains('hidden')) {
                    window.hideLayoutModal();
                }
            });
        });
    </script>

    @yield('script')
    @if (session('error-access'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: '{{ session('error-access') }}',
            })
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: '{{ session('error') }}',
            });
        </script>
    @endif

    <style>
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
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            box-shadow: 0 0 5px var(--glow), 0 0 10px var(--glow);
            filter: brightness(135%);
            -webkit-filter: brightness(135%);
            -ms-filter: brightness(135%);
        }

        .swal2-title,
        .swal2-html-container {
            text-shadow: none !important;
            /* background-clip: text; */
            color: white !important;
            font-weight: 600 !important;
            background: none !important;
            box-shadow: none !important;
        }

        .swal2-confirm {
            text-shadow: 0 0 12px var(--glow);
            background: none !important;
            color: white !important;
            font-weight: 600 !important;
            border: solid var(--glow) 2.6px !important;
            width: 100px !important;
            box-shadow: 0 0 9px var(--glow) !important;
        }

        .swal2-confirm:hover {
            box-shadow: 0 0 14px var(--glow) !important;

        }

        .swal2-confirm:active {
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
