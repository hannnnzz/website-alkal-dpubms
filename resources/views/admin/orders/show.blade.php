{{-- resources/views/admin/orders/invoice.blade.php --}}
@php
    use Carbon\Carbon;
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            Invoice — {{ $order->order_id }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden shadow-2xl sm:rounded-2xl">
                <!-- header card -->
                <div class="bg-gradient-to-r from-purple-600 via-indigo-600 to-violet-600 text-white p-6 sm:p-8 rounded-t-2xl">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            {{-- Nama perusahaan / provider dari DB --}}
                            <h3 class="text-lg sm:text-xl font-semibold">
                                {{ $order->provider_name ?? 'Nama Perusahaan / Lab' }}
                            </h3>

                            {{-- Info pemesan --}}
                            <p class="mt-1 text-sm text-white/90">
                                {{ $order->customer_name ?? ($order->user->name ?? '-') }}
                                • Telp: {{ $order->customer_contact ?? '-' }}
                            </p>

                            <div class="mt-3 flex items-center gap-3 flex-wrap">
                                <code class="px-3 py-1 rounded bg-white/15 text-sm font-medium select-all">Invoice</code>
                                <div class="text-sm text-white/90">Order ID: <strong>{{ $order->order_id }}</strong></div>
                            </div>
                        </div>

                        <div class="text-right flex-shrink-0">
                            <div class="text-sm text-white/90">Tanggal: {{ optional($order->created_at)->format('d M Y H:i') }}</div>
                            <div class="mt-3 text-sm">
                                <span class="text-sm">Total:</span>
                                <div class="font-semibold text-lg">Rp {{ number_format($order->amount ?? 0,0,',','.') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- content -->
                <div class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-b-2xl">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- left: invoice details -->
                        <div class="md:col-span-2 space-y-4">
                            <div>
                                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Dibayar oleh</h4>
                                <p class="mt-2 text-sm text-gray-800 dark:text-gray-100">
                                    {{ $order->customer_name ?? ($order->user->name ?? '-') }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-300">Kontak: {{ $order->customer_contact ?? '-' }}</p>
                            </div>

                            <div class="mt-2">
                                <h5 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">Rincian Item</h5>

                                <div class="space-y-2">
                                    @foreach($order->items as $idx => $item)
                                        <details class="group bg-gray-50 dark:bg-gray-900/60 p-4 rounded-lg border border-gray-100 dark:border-gray-700">
                                            <summary class="flex items-center justify-between cursor-pointer">
                                                <div class="flex items-center gap-3 min-w-0">
                                                    <div class="w-9 h-9 rounded-full bg-purple-100 dark:bg-purple-900 flex items-center justify-center text-purple-700 dark:text-purple-300 shrink-0">
                                                        {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($item->type ?? '-', 0, 1)) }}
                                                    </div>

                                                    <div class="truncate">
                                                        <div class="text-sm font-medium text-gray-800 dark:text-gray-100 truncate">{{ $item->name ?? '-' }}</div>
                                                        <div class="text-xs text-gray-500 dark:text-gray-300">{{ $item->type ?? '-' }} • Jumlah: {{ $item->quantity ?? 1 }}</div>
                                                    </div>
                                                </div>

                                                <div class="flex items-center gap-3 shrink-0">
                                                    @if(isset($item->price) && $item->price)
                                                        <div class="text-sm font-semibold text-gray-800 dark:text-gray-100">Rp {{ number_format($item->price,0,',','.') }}</div>
                                                    @endif

                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                </div>
                                            </summary>

                                            <div class="mt-3 text-sm text-gray-600 dark:text-gray-300">
                                                @if(strtolower($item->type ?? '') === 'sewa')
                                                    <p>
                                                        <strong>Periode:</strong>
                                                        {{ $item->rental_start ? Carbon::parse($item->rental_start)->format('d M Y') : '-' }}
                                                        — {{ $item->rental_end ? Carbon::parse($item->rental_end)->format('d M Y') : '-' }}
                                                    </p>
                                                    <p>
                                                        <strong>Durasi hari:</strong>
                                                        @if(!empty($item->rental_start) && !empty($item->rental_end))
                                                            {{ Carbon::parse($item->rental_start)->diffInDays(Carbon::parse($item->rental_end)) + 1 }}
                                                        @else
                                                            -
                                                        @endif
                                                    </p>
                                                @else
                                                    <p>
                                                        <strong>Tanggal Pengujian:</strong>
                                                        {{ $order->test_start ? \Carbon\Carbon::parse($order->test_start)->format('d M Y') : '-' }}
                                                        — {{ $order->test_end ? \Carbon\Carbon::parse($order->test_end)->format('d M Y') : '-' }}
                                                    </p>

                                                @endif
                                            </div>
                                        </details>
                                    @endforeach
                                </div>

                            </div>

                            {{-- Informasi pesanan lengkap yang diperbaiki --}}
                            <div class="mt-4 p-4 border border-gray-100 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-900/60">
                                <h5 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-3">Detail Pesanan</h5>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    {{-- Kolom kiri --}}
                                    <div class="space-y-3">
                                        @if($order->tanggal_masuk)
                                            <div class="flex flex-col">
                                                <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">Tanggal Masuk</span>
                                                <span class="text-sm text-gray-800 dark:text-gray-100">{{ Carbon::parse($order->tanggal_masuk)->format('d M Y') }}</span>
                                            </div>
                                        @endif

                                        @if($order->hari)
                                            <div class="flex flex-col">
                                                <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">Hari Masuk</span>
                                                <span class="text-sm text-gray-800 dark:text-gray-100 font-mono">{{ $order->hari }}</span>
                                            </div>
                                        @endif

                                        @if($order->no_surat)
                                            <div class="flex flex-col">
                                                <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">Nomor Surat</span>
                                                <span class="text-sm text-gray-800 dark:text-gray-100 font-mono">{{ $order->no_surat }}</span>
                                            </div>
                                        @endif

                                        @if($order->tanggal_surat)
                                            <div class="flex flex-col">
                                                <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">Tanggal Surat</span>
                                                <span class="text-sm text-gray-800 dark:text-gray-100">{{ Carbon::parse($order->tanggal_surat)->format('d M Y') }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="space-y-3">
                                        @if($order->perihal)
                                            <div class="flex flex-col">
                                                <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">Perihal</span>
                                                <span class="text-sm text-gray-800 dark:text-gray-100">{{ $order->perihal }}</span>
                                            </div>
                                        @endif

                                        @if($order->disposisi)
                                            <div class="flex flex-col">
                                                <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">Disposisi</span>
                                                <span class="text-sm text-gray-800 dark:text-gray-100">{{ $order->disposisi }}</span>
                                            </div>
                                        @endif

                                        @if($order->alamat_pengirim)
                                            <div class="flex flex-col">
                                                <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">Alamat Pengirim</span>
                                                <span class="text-sm text-gray-800 dark:text-gray-100 whitespace-pre-wrap">{{ $order->alamat_pengirim }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Deskripsi paket pekerjaan (full width jika ada) --}}
                                @if($order->deskripsi_paket_pekerjaan)
                                    <div class="mt-4 pt-3 border-t border-gray-200 dark:border-gray-600">
                                        <div class="flex flex-col">
                                            <span class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-1">Deskripsi Paket Pekerjaan</span>
                                            <span class="text-sm text-gray-800 dark:text-gray-100 whitespace-pre-wrap leading-relaxed">{{ $order->deskripsi_paket_pekerjaan }}</span>
                                        </div>
                                    </div>
                                @endif

                                {{-- File attachments --}}
                                @if($order->file_upload_path || $order->qr_image_path)
                                    <div class="mt-4 pt-3 border-t border-gray-200 dark:border-gray-600">
                                        <span class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-2 block">Lampiran</span>
                                        <div class="flex gap-2 flex-wrap">
                                            @if($order->file_upload_path)
                                                <a href="{{ url('/storage/' . $order->file_upload_path) }}"
                                                target="_blank"
                                                class="inline-flex items-center gap-1 px-2 py-1 bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded text-xs hover:bg-blue-100 dark:hover:bg-blue-900/50 transition">
                                                    <!-- icon SVG -->
                                                    Surat
                                                </a>
                                            @endif
                                            @if($order->qr_image_path)
                                                <a href="{{ Storage::url($order->qr_image_path) }}" target="_blank" class="inline-flex items-center gap-1 px-2 py-1 bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 rounded text-xs hover:bg-purple-100 dark:hover:bg-purple-900/50 transition">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                                                    </svg>
                                                    QR Code
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="p-4 rounded-lg border border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/60">
                                <h5 class="text-sm text-gray-500 dark:text-gray-300">Ringkasan</h5>
                                <div class="mt-3 space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-300">Subtotal</span>
                                        <span class="font-semibold text-gray-800 dark:text-gray-100">Rp {{ number_format($order->amount ?? 0,0,',','.') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="p-4 rounded-lg border border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/60 text-sm text-gray-600 dark:text-gray-300">
                                <h6 class="text-xs text-gray-500 dark:text-gray-400">Informasi</h6>
                                <p class="mt-2"><strong class="text-gray-700 dark:text-gray-100">Order ID:</strong> <span class="font-mono text-xs">{{ $order->order_id }}</span></p>
                                <p class="mt-1"><strong class="text-gray-700 dark:text-gray-100">Tanggal:</strong> {{ optional($order->created_at)->format('d M Y H:i') }}</p>

                                {{-- Status pesanan --}}
                                <p class="mt-1"><strong class="text-gray-700 dark:text-gray-100">Status:</strong>
                                    <span class="inline-block ml-2 px-2 py-1 text-xs font-medium rounded
                                        @if(strtoupper($order->status ?? '') === 'PAID') bg-green-100 text-green-800
                                        @elseif(in_array(strtoupper($order->status ?? ''), ['PENDING','UNPAID'])) bg-yellow-100 text-yellow-800
                                        @elseif(strtoupper($order->status ?? '') === 'CANCELLED' || strtoupper($order->status ?? '') === 'EXPIRED') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif
                                    ">
                                        {{ $order->status ?? '-' }}
                                    </span>
                                </p>

                                <div class="mt-3 flex flex-col gap-2">
                                    <a href="{{ route('admin.orders.invoice.edit', $order->id) }}" class="inline-flex items-center gap-2 px-3 py-2 border border-gray-200 dark:border-gray-700 rounded hover:bg-gray-50 dark:hover:bg-gray-800 text-sm text-gray-700 dark:text-gray-100 transition no-print">
                                        Edit Dokumen
                                    </a>

                                    <a href="{{ route('admin.orders.invoice.pdf', $order->id) }}" target="_blank"
                                    class="inline-flex items-center gap-2 px-3 py-2 border border-gray-200 dark:border-gray-700 rounded hover:bg-gray-50 dark:hover:bg-gray-800 text-sm text-gray-700 dark:text-gray-100 transition no-print">
                                        Cetak PDF
                                    </a>

                                    <button type="button" onclick="history.back()"
                                        class="inline-flex items-center gap-2 px-3 py-2 border border-gray-200 dark:border-gray-700 rounded hover:bg-gray-50 dark:hover:bg-gray-800 text-sm text-gray-700 dark:text-gray-100 transition">
                                        Kembali
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> <!-- end content -->
            </div>
        </div>
    </div>
</x-app-layout>
