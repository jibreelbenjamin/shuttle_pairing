@extends('layouts.app')

@section('title', 'Buat Turnamen Baru')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h1 class="text-2xl font-bold text-gray-900">Buat Turnamen Baru</h1>
            <p class="mt-1 text-sm text-gray-500">Isi informasi turnamen badminton yang akan diselenggarakan.</p>
        </div>

        <form action="{{ route('tournament.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <div>
                <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">Nama Turnamen <span class="text-red-500">*</span></label>
                <input type="text" name="nama" id="nama" value="{{ old('nama') }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                    placeholder="Contoh: Kejuaraan Bulutangkis Antar Klub 2026">
                @error('nama')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="tempat" class="block text-sm font-medium text-gray-700 mb-1">Tempat</label>
                <input type="text" name="tempat" id="tempat" value="{{ old('tempat') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                    placeholder="Contoh: GOR Bulutangkis Jakarta">
                @error('tempat')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" id="tanggal_mulai" value="{{ old('tanggal_mulai') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                    @error('tanggal_mulai')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tanggal_selesai" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" id="tanggal_selesai" value="{{ old('tanggal_selesai') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                    @error('tanggal_selesai')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200">
                <a href="{{ route('tournament.index') }}" class="px-4 py-2 text-gray-700 hover:text-gray-900 transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
                    Buat Turnamen
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
