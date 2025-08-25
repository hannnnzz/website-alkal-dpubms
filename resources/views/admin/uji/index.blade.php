{{-- resources/views/admin/uji/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Daftar Jenis Uji
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    {{-- Tombol kiri & kanan --}}
                    <div class="flex justify-between items-center mb-4">
                        <a href="{{ route('dashboard') }}"
                            class="inline-block px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md shadow-sm">
                            ← Kembali ke Dashboard
                        </a>

                        <a href="{{ route('admin.uji-types.create') }}"
                            class="inline-block px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md shadow-sm">
                            + Tambah Jenis Uji
                        </a>
                    </div>

                    {{-- Tabel daftar jenis uji --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-300 dark:border-gray-700 rounded-lg overflow-hidden">
                            <thead class="bg-gray-100 dark:bg-gray-700">
                                <tr>
                                    <th class="border border-gray-300 dark:border-gray-700 px-4 py-2 text-left">Nama</th>
                                    <th class="border border-gray-300 dark:border-gray-700 px-4 py-2 text-left">Harga</th>
                                    <th class="border border-gray-300 dark:border-gray-700 px-4 py-2 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ujiTypes as $ujiType)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="border border-gray-300 dark:border-gray-700 px-4 py-2">
                                            {{ $ujiType->name }}
                                        </td>
                                        <td class="border border-gray-300 dark:border-gray-700 px-4 py-2">
                                            Rp {{ number_format($ujiType->price, 0, ',', '.') }}
                                        </td>
                                        <td class="border border-gray-300 dark:border-gray-700 px-4 py-2 text-center">
                                            <a href="{{ route('admin.uji-types.edit', $ujiType->id) }}"
                                                class="text-yellow-500 hover:text-yellow-600 font-semibold">Edit</a>
                                            |
                                            <form action="{{ route('admin.uji-types.destroy', $ujiType->id) }}"
                                                method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-red-500 hover:text-red-600 font-semibold"
                                                    onclick="return confirm('Yakin ingin menghapus?')">
                                                    Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-4 text-center text-gray-500 dark:text-gray-400">
                                            Belum ada data jenis uji.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
