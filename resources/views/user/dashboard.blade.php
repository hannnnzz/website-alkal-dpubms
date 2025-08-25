<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-[#D9D9D9] dark:bg-gray-900 text-gray-900 dark:text-gray-100">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl md:text-5xl font-bold text-yellow-800 dark:text-yellow-400 leading-tight">
                Layanan Pengujian
            </h2>
            <p class="mt-4 mb-12 text-xl text-gray-600 dark:text-gray-300">
                Solusi lengkap untuk pengujian dan penyewaan alat konstruksi<br>
                Dapatkan layanan terbaik hanya di sini.
            </p>
        </div>

        <div class="mt-10 max-w-7xl mx-auto flex flex-col md:flex-row md:space-x-8 space-y-6 md:space-y-0 px-6 justify-center">
            <!-- Kartu 1: Uji Konstruksi -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg flex flex-col items-center text-center w-full md:w-1/2 transition">
                <img src="/images/uji-konstruksi.png" alt="Uji Konstruksi" class="w-20 h-20 mb-4">
                <h3 class="text-xl font-semibold text-blue-600 dark:text-blue-400">Uji Konstruksi</h3>
                <p class="mt-2 text-gray-600 dark:text-gray-300 max-w-xl">
                    Layanan Uji Konstruksi untuk Mengetahui Kualitas Bangunan.
                </p>
                <a href="{{ route('user.order.createuji') }}"
                    class="mt-4 inline-block bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white px-4 py-2 rounded-full shadow-sm transition"
                    aria-label="Ajukan Uji">
                    Ajukan Uji
                </a>
            </div>

            <!-- Kartu 2: Sewa Alat -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg flex flex-col items-center text-center w-full md:w-1/2 transition">
                <img src="/images/uji-konstruksi.png" alt="Sewa Alat" class="w-20 h-20 mb-4">
                <h3 class="text-xl font-semibold text-blue-600 dark:text-blue-400">Sewa Alat</h3>
                <p class="mt-2 text-gray-600 dark:text-gray-300 max-w-xl">
                    Sewa Alat Berat untuk Keperluan Konstruksi.
                </p>
                <a href="{{ route('user.order.createsewa') }}"
                    class="mt-4 inline-block bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white px-4 py-2 rounded-full shadow-sm transition"
                    aria-label="Sewa Sekarang">
                    Sewa Sekarang
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
