<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Riwayat Pesanan Saya
        </h2>
    </x-slot>

    <div class="py-12 bg-[#D9D9D9] dark:bg-gray-900">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Jika tidak ada order --}}
            @if($orders->isEmpty())
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-700 dark:text-gray-200">
                    Anda belum memiliki pesanan.
                </div>
            @else
                {{-- Wrapper yang punya Alpine state untuk filter --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6" x-data="{ search: '', status: 'all', orderType: 'all' }">
                    {{-- Kontrol filter --}}
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 gap-3">
                        <div class="flex items-center gap-3 w-full md:w-2/3">
                            <input
                                x-model.debounce.200ms="search"
                                type="text"
                                placeholder="Cari Order ID / Provider Name / Customer Name / Tanggal Pengujian..."
                                class="w-full px-3 py-2 border rounded-md bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100"
                            />
                        </div>

                        <div class="flex items-center gap-3">
                            <label class="text-sm text-gray-600 dark:text-gray-300">Filter status:</label>
                            <select x-model="status"
                                    class="px-3 py-2 border rounded-md bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100">
                                <option value="all">Semua</option>
                                <option value="paid">PAID (Lunas)</option>
                                <option value="unpaid">UNPAID (Belum Bayar)</option>
                                <option value="pending">PENDING (Menunggu)</option>
                                <option value="cancelled">CANCELLED (Dibatalkan)</option>
                                <option value="expired">EXPIRED (Gagal/Expired)</option>
                            </select>

                            {{-- Filter Pengujian / Penyewaan --}}
                            <label class="text-sm text-gray-600 dark:text-gray-300">Tipe:</label>
                            <select x-model="orderType"
                                    class="px-3 py-2 border rounded-md bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100">
                                <option value="all">Semua</option>
                                <option value="uji">Pengujian</option>
                                <option value="sewa">Penyewaan</option>
                            </select>
                        </div>
                    </div>

                    {{-- Tabel --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Order ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Provider Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Customer Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Tanggal Pengujian</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Tanggal Order</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Total</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status &amp; Pembayaran</th>
                                    <th class="px-6 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($orders as $order)
                                    @php
                                        // 1) Normalisasi dari kolom disposisi (orders)
                                        $dataType = null;
                                        if(!empty($order->disposisi)) {
                                            $disp = strtolower($order->disposisi);
                                            if(str_contains($disp, 'laborat') || str_contains($disp, 'laboratorium')) {
                                                $dataType = 'uji';
                                            } elseif(str_contains($disp, 'alat') || str_contains($disp, 'berat')) {
                                                $dataType = 'sewa';
                                            }
                                        }

                                        // 2) Jika disposisi tidak membantu, coba cek order_items (relasi items)
                                        if(empty($dataType) && isset($order->items) && $order->items->isNotEmpty()) {
                                            // cari item pertama yang punya kolom 'type' atau 'alat_id'
                                            $found = $order->items->first(function($it){
                                                return isset($it->type) && trim($it->type) !== '';
                                            });
                                            if($found) {
                                                $t = strtolower(trim($found->type));
                                                // map beberapa kemungkinan ke 'uji'/'sewa'
                                                if(str_contains($t, 'uji') || str_contains($t, 'pengujian')) $dataType = 'uji';
                                                elseif(str_contains($t, 'sewa') || str_contains($t, 'rental')) $dataType = 'sewa';
                                            } else {
                                                // kalau tidak ada kolom type, coba lihat apakah ada alat_id -> indikasi penyewaan
                                                $anyAlat = $order->items->first(function($it){ return isset($it->alat_id) && $it->alat_id; });
                                                if($anyAlat) $dataType = 'sewa';
                                            }
                                        }

                                        // 3) Fallback: berdasarkan test_start
                                        if(empty($dataType)) {
                                            $dataType = $order->test_start ? 'uji' : 'sewa';
                                        }

                                        // pastikan lowercase dan aman
                                        $dataType = strtolower(trim($dataType));

                                        // data untuk search/status
                                        $itemsString = collect($order->items ?? [])->pluck('name')->join(' ');
                                        $testRaw = $order->test_start ? $order->test_start : '';
                                        $testFriendly = $order->test_start ? \Carbon\Carbon::parse($order->test_start)->format('d M Y') : '';
                                        $dataName = strtolower(
                                            trim(
                                                ($order->order_id ?? '') . ' ' .
                                                ($order->provider_name ?? '') . ' ' .
                                                ($order->customer_name ?? '') . ' ' .
                                                $testRaw . ' ' .
                                                $testFriendly . ' ' .
                                                $itemsString . ' ' .
                                                ($order->created_at ? $order->created_at->format('d M Y H:i') : '')
                                            )
                                        );
                                        $dataStatus = strtolower($order->status ?? '');
                                    @endphp

                                    <tr
                                        data-name="{{ $dataName }}"
                                        data-status="{{ $dataStatus }}"
                                        data-type="{{ $dataType }}"
                                        x-show="(search === '' || $el.dataset.name.includes(search.toLowerCase()))
                                                && (status === 'all' || $el.dataset.status === status)
                                                && (orderType === 'all' || (($el.dataset.type || '').toLowerCase() === orderType.toLowerCase()))"
                                        x-cloak
                                    >
                                        <!-- Order ID -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ $order->order_id }}
                                        </td>
                                        <!-- Provider Name -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ $order->provider_name }}
                                        </td>
                                        <!-- Customer Name -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ $order->customer_name }}
                                        </td>
                                        <!-- Tanggal Pengujian -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ $order->test_start ? \Carbon\Carbon::parse($order->test_start)->format('d M Y') : '-' }}
                                        </td>
                                        <!-- Tanggal Order -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ $order->created_at->format('d M Y H:i') }}
                                        </td>
                                        <!-- Total -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 dark:text-gray-100">
                                            Rp {{ number_format($order->amount, 0, ',', '.') }}
                                        </td>
                                        <!-- Status & Pembayaran -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center space-y-2">
                                            @if($order->status === 'PAID')
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                    Lunas
                                                </span>
                                            @elseif($order->status === 'PENDING')
                                                <a href="{{ route('payment.qris', $order->id) }}"
                                                    class="inline-block bg-purple-500 hover:bg-purple-600 text-white text-xs px-2 py-1 rounded">
                                                    Bayar via QRIS
                                                </a>
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    Menunggu Pembayaran
                                                </span>
                                            @elseif($order->status === 'UNPAID')
                                                <a href="{{ route('payment.qris', $order->id) }}"
                                                    class="inline-block bg-purple-500 hover:bg-purple-600 text-white text-xs px-2 py-1 rounded">
                                                    Bayar via QRIS
                                                </a>
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    Belum Bayar
                                                </span>
                                            @elseif($order->status === 'CANCELLED')
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    Dibatalkan
                                                </span>
                                            @elseif($order->status === 'EXPIRED')
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                    Transaksi Gagal
                                                </span>
                                            @else
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    {{ $order->status }}
                                                </span>
                                            @endif
                                        </td>
                                        <!-- Detail -->
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                            <a href="{{ route('user.order.show', $order->id) }}"
                                               class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200">
                                                Detail
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
