<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Daftar Alat Sewa
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

                        <a href="{{ route('admin.alat-sewa-types.create') }}"
                            class="inline-block px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md shadow-sm">
                            + Tambah Alat Sewa
                        </a>
                    </div>

                    {{-- Tabel daftar alat --}}
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
                                @forelse($alats as $alat)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="border border-gray-300 dark:border-gray-700 px-4 py-2">
                                            {{ $alat->name }}
                                        </td>
                                        <td class="border border-gray-300 dark:border-gray-700 px-4 py-2">
                                            Rp {{ number_format($alat->price, 0, ',', '.') }}
                                        </td>
                                        <td class="border border-gray-300 dark:border-gray-700 px-4 py-2 text-center">
                                            {{-- status badge --}}
                                            @if($alat->is_locked)
                                                <span class="inline-block px-2 py-1 mr-2 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                    Nonaktif
                                                </span>
                                            @else
                                                <span class="inline-block px-2 py-1 mr-2 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                    Aktif
                                                </span>
                                            @endif

                                            {{-- Edit --}}
                                            <a href="{{ route('admin.alat-sewa-types.edit', $alat->id) }}"
                                            class="mx-1 text-yellow-500 hover:text-yellow-600 font-semibold">Edit</a>

                                            |

                                            {{-- Hapus --}}
                                            <form action="{{ route('admin.alat-sewa-types.destroy', $alat->id) }}"
                                                method="POST" class="inline mx-1">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="text-red-500 hover:text-red-600 font-semibold"
                                                        onclick="return confirm('Yakin hapus {{ addslashes($alat->name) }}?')">
                                                    Hapus
                                                </button>
                                            </form>

                                            |

                                            {{-- Toggle lock: tampilkan aksi (Aktifkan jika sedang dikunci; Nonaktifkan jika sedang aktif) --}}
                                            <form action="{{ route('admin.alat-sewa-types.toggleLock', $alat) }}"
                                                method="POST"
                                                class="inline-block ml-1"
                                                onsubmit="return confirm('{{ $alat->is_locked ? 'Aktifkan' : 'Nonaktifkan' }} alat {{ addslashes($alat->name) }}?')">
                                                @csrf
                                                <button type="submit"
                                                        class="px-2 py-1 rounded text-white text-xs font-semibold
                                                            {{ $alat->is_locked ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700' }}">
                                                    {{ $alat->is_locked ? 'Aktifkan' : 'Nonaktifkan' }}
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-4 text-center text-gray-500 dark:text-gray-400">
                                            Belum ada data alat sewa.
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
