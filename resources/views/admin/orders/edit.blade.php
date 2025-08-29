@php use Carbon\Carbon; @endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            Edit Invoice — {{ $order->order_id }}
        </h2>
    </x-slot>

    <x-slot name="head">
        {{-- Jika tidak perlu Litepicker, kosongkan atau hapus slot ini.
            Aku sisakan slot supaya konsisten dengan layout contoh. --}}
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if(session('success'))
                        <div class="mb-4 rounded-md bg-green-50 p-3 text-green-800">{{ session('success') }}</div>
                    @endif
                    @if($errors->any())
                        <div class="mb-4 rounded-md bg-red-50 p-3 text-red-800">
                            <ul class="list-disc pl-5">
                                @foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.orders.invoice.update', $order->id) }}" method="POST" enctype="multipart/form-data" class="space-y-8" id="invoice-form">
                        @csrf

                        <!-- SECTION 1: INFORMASI PENYEDIA & PEMESAN -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 border-b border-gray-200 dark:border-gray-600 pb-2">
                                Informasi Penyedia & Pemesan
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="provider_name" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Nama Perusahaan / Provider</label>
                                    <input type="text" id="provider_name" name="provider_name" value="{{ old('provider_name', $order->provider_name) }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white text-sm px-3 py-2">
                                </div>

                                <div>
                                    <label for="customer_name" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Nama Customer</label>
                                    <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name', $order->customer_name) }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white text-sm px-3 py-2">
                                </div>

                                <div class="md:col-span-2">
                                    <label for="customer_contact" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Kontak</label>
                                    <input type="text" id="customer_contact" name="customer_contact" value="{{ old('customer_contact', $order->customer_contact) }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white text-sm px-3 py-2">
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 2: INFORMASI SURAT PERMOHONAN -->
                        <div class="bg-green-50 dark:bg-gray-700 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 border-b border-green-200 dark:border-gray-600 pb-2">
                                Informasi Surat Permohonan
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label for="no_surat" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Nomor Surat</label>
                                    <input type="text" id="no_surat" name="no_surat" value="{{ old('no_surat', $order->no_surat) }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white text-sm px-3 py-2">
                                </div>

                                <div class="md:col-span-2">
                                    <label for="tanggal_surat" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Tanggal Surat</label>
                                    <input type="date" id="tanggal_surat" name="tanggal_surat"
                                        value="{{ old('tanggal_surat', $order->tanggal_surat ? Carbon::parse($order->tanggal_surat)->format('Y-m-d') : '') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white text-sm px-3 py-2">
                                </div>
                            </div>

                            <div class="mt-4">
                                <label for="alamat_pengirim" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Alamat Pengirim</label>
                                <textarea id="alamat_pengirim" name="alamat_pengirim" rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white text-sm px-3 py-2">{{ old('alamat_pengirim', $order->alamat_pengirim) }}</textarea>
                            </div>
                        </div>


                        <!-- SECTION 3: DESKRIPSI PAKET PEKERJAAN -->
                        <div class="bg-blue-50 dark:bg-gray-700 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 border-b border-blue-200 dark:border-gray-600 pb-2">
                                Deskripsi Paket Pekerjaan
                            </h3>

                            <div>
                                <label for="deskripsi_paket_pekerjaan" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Deskripsi Paket Pekerjaan</label>
                                <textarea id="deskripsi_paket_pekerjaan" name="deskripsi_paket_pekerjaan" rows="4"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white text-sm px-3 py-2">{{ old('deskripsi_paket_pekerjaan', $order->deskripsi_paket_pekerjaan) }}</textarea>
                            </div>
                        </div>

                        <!-- SECTION 4: RINGKASAN & ACTIONS -->
                        <div class="bg-yellow-50 dark:bg-gray-700 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 border-b border-yellow-200 dark:border-gray-600 pb-2">
                                Ringkasan & Tindakan
                            </h3>

                            <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">Periksa kembali data invoice. Klik <strong>Simpan Perubahan</strong> untuk menyimpan perubahan.</p>

                            <div class="flex items-center justify-between">
                                <div class="text-sm text-gray-700 dark:text-gray-200">
                                    <p>Invoice: <span class="font-medium">{{ $order->order_id }}</span></p>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Terakhir diubah: {{ $order->updated_at ? $order->updated_at->format('Y-m-d H:i') : '-' }}</p>
                                </div>

                                <div class="flex items-center gap-3">
                                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Simpan Perubahan</button>
                                    <a href="{{ route('admin.orders.invoice', $order->id) }}" class="px-4 py-2 border rounded-md text-sm">Batal</a>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
