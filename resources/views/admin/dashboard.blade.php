@extends('layouts.admin')

@section('body')
<div class="flex flex-col w-full py-4 shadow-md items-center justify-center mb-5">
            <h1 class="text-center text-4xl uppercase font-bold mb-2">Dashboard</h1>
        </div>
        <div class="m-2">
            <div class=" flex">
                <h1 class="font-bold text-xl">Welcome</h1>
                <h1 class="font-bold text-xl uppercase">, {{ $name }}</h1>
            </div>
            <div>
                <h1 class=" text-gray-400 font-bold text-sm">Dari Unit  {{ $unit }}</h1>
            </div>
        </div>
@endsection

@section('script')
<script>
    document.getElementById('overview').classList.add('bg-slate-100')
    document.getElementById('overview').classList.add('active')
</script>
@endsection