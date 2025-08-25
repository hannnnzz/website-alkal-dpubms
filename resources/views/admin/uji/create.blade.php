<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Tambah Tipe Uji
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.uji-types.store') }}" method="POST"
                class="bg-white dark:bg-gray-800 shadow rounded px-6 py-4">
                @csrf

                {{-- Nama Uji --}}
                <div class="mb-4">
                    <label for="name" class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Nama Uji</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}"
                        placeholder="Masukkan nama jenis uji, contoh: Uji Nyali"
                        class="w-full border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200 dark:bg-gray-700 dark:text-white">
                </div>

                {{-- Harga --}}
                <div class="mb-4">
                    <label for="price" class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Harga (Rp)</label>
                    <input type="number" name="price" id="price" value="{{ old('price') }}"
                        placeholder="Masukkan harga uji, contoh: 150000"
                        class="w-full border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200 dark:bg-gray-700 dark:text-white">
                </div>

                {{-- Tombol Aksi --}}
                <div class="flex items-center space-x-3">
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
                        Simpan
                    </button>
                    <a href="{{ route('admin.uji-types.index') }}"
                        class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded shadow">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
