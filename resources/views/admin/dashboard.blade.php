<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Menu Kelola Data --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Kelola Data</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <a href="{{ route('admin.alat-sewa-types.index') }}"
                            class="flex items-center p-4 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-md transition duration-200">
                            {{-- Icon --}}
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 7h18M3 12h18M3 17h18" />
                            </svg>
                            <span class="font-medium">Kelola Alat Sewa</span>
                        </a>

                        <a href="{{ route('admin.uji-types.index') }}"
                            class="flex items-center p-4 bg-purple-600 hover:bg-purple-700 text-white rounded-lg shadow-md transition duration-200">
                            {{-- Icon --}}
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405M19 13V7a2 2 0 00-2-2h-4m0 0V3m0 2h-4m0 0V3m0 2H7a2 2 0 00-2 2v6m12 4v6m-8-6v6" />
                            </svg>
                            <span class="font-medium">Kelola Jenis Uji</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Order Terbaru: WRAP dengan Alpine x-data --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg" x-data="{ search: '', status: 'all', orderType: 'all' }">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold">Order Terbaru</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Menampilkan order terbaru dan detail sewa/penyewa</p>
                    </div>

                    {{-- Kontrol pencarian & filter --}}
                    <div class="flex gap-3 items-center mb-4">
                        <input
                            x-model.debounce.200ms="search"
                            type="text"
                            placeholder="Cari order id / nama penyewa / item..."
                            class="px-3 py-2 border rounded-md w-[320px] bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100"
                            />


                        <label class="text-sm text-gray-600 dark:text-gray-300">Tipe:</label>
                        <select x-model="orderType"
                                class="px-3 py-2 border rounded-md bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100">
                            <option value="all">Semua</option>
                            <option value="uji">Pengujian</option>
                            <option value="sewa">Penyewaan</option>
                        </select>

                        <select x-model="status"
                                class="px-3 py-2 border rounded-md bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100">
                            <option value="all">Semua status</option>
                            <option value="paid">PAID</option>
                            <option value="unpaid">UNPAID</option>
                            <option value="pending">PENDING</option>
                            <option value="cancelled">CANCELLED</option>
                            <option value="expired">EXPIRED</option>
                        </select>

			            <form method="GET" action="{{ route('admin.orders.export') }}" class="inline-flex items-center space-x-2">
                            <input type="hidden" name="q" :value="search">
                            <input type="hidden" name="status" :value="status">
                            <input type="hidden" name="type" :value="orderType">

                            <input type="hidden" name="date_field" value="{{ request('date_field', 'created_at') }}">
                            <label class="text-sm text-gray-600 dark:text-gray-300">Dari</label>
                            <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}"
                                class="px-2 py-1 border rounded-md bg-white dark:bg-gray-700 text-sm">
                            <label class="text-sm text-gray-600 dark:text-gray-300">Sampai</label>
                            <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}"
                                class="px-2 py-1 border rounded-md bg-white dark:bg-gray-700 text-sm">
                            <button type="submit"
                                class="ml-2 inline-flex items-center px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md text-sm shadow-sm transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 3v10m0 0l3-3m-3 3l-3-3M21 21H3" />
                                </svg>
                                Export Excel
                            </button>
                        </form>
                        <div class="text-sm text-gray-500 dark:text-gray-400"></div>
                    </div>

                    @if(isset($orders) && $orders->count())
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Order</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Penyewa</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Item (Sewa / Uji)</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Jumlah</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Status</th>
                                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($orders as $order)
                                        @php
                                            // -----------------------
                                            // Build data-type (mendukung campuran: 'sewa', 'uji' atau 'sewa,uji')
                                            // -----------------------
                                            $types = collect();

                                            // 1) dari disposisi (jika ada kata kunci)
                                            if(!empty($order->disposisi)) {
                                                $disp = strtolower($order->disposisi);
                                                if(str_contains($disp, 'laborat') || str_contains($disp, 'laboratorium')) {
                                                    $types->push('uji');
                                                }
                                                if(str_contains($disp, 'alat') || str_contains($disp, 'berat')) {
                                                    $types->push('sewa');
                                                }
                                            }

                                            // 2) dari semua items (agar mendukung campuran)
                                            if(isset($order->items) && $order->items->isNotEmpty()) {
                                                foreach($order->items as $it) {
                                                    $t = null;
                                                    if(isset($it->type) && trim($it->type) !== '') {
                                                        $t = strtolower(trim($it->type));
                                                    } elseif(isset($it->alat_id) && $it->alat_id) {
                                                        // indikasi penyewaan
                                                        $t = 'sewa';
                                                    }
                                                    if($t) {
                                                        if(str_contains($t, 'uji') || str_contains($t, 'pengujian')) $types->push('uji');
                                                        if(str_contains($t, 'sewa') || str_contains($t, 'rental') || str_contains($t, 'alat')) $types->push('sewa');
                                                    }
                                                }
                                            }

                                            // 3) fallback: jika kosong, gunakan test_date/test_start presence
                                            if($types->isEmpty()) {
                                                if(!empty($order->test_date) || !empty($order->test_start)) {
                                                    $types->push('uji');
                                                } else {
                                                    $types->push('sewa');
                                                }
                                            }

                                            // unique + CSV tanpa spasi
                                            $types = $types->unique()->values()->all();
                                            $dataTypeCsv = implode(',', $types);

                                            // build data untuk search/status
                                            $itemsString = collect($order->items ?? [])->pluck('name')->join(' ');
                                            $testRaw = $order->test_date ?? $order->test_start ?? '';
                                            $testFriendly = $testRaw ? \Carbon\Carbon::parse($testRaw)->format('d M Y') : '';
                                            $dataName = strtolower(
                                                trim(
                                                    ($order->order_id ?? '') . ' ' .
                                                    (optional($order->user)->name ?? ($order->customer_name ?? '')) . ' ' .
                                                    ($order->provider_name ?? '') . ' ' .
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
                                            data-type="{{ $dataTypeCsv }}"
                                            x-show="(search === '' || $el.dataset.name.includes(search.toLowerCase()))
                                                    && (status === 'all' || $el.dataset.status === status)
                                                    && (orderType === 'all' || ($el.dataset.type || '').split(',').map(s => s.trim().toLowerCase()).includes(orderType.toLowerCase()))"
                                            x-cloak
                                        >
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $order->order_id }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $order->created_at->format('Y-m-d H:i') }}</div>
                                            </td>

                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <div class="text-sm text-gray-900 dark:text-gray-100">{{ optional($order->user)->name ?? $order->customer_name }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $order->customer_contact }}</div>
                                            </td>

                                            <td class="px-4 py-3 max-w-sm">
                                                {{-- Tampilkan setiap item, bedakan Sewa dan Uji --}}
                                                @foreach($order->items as $item)
                                                    <div class="mb-2 p-2 rounded border border-gray-100 dark:border-gray-700">
                                                        <div class="flex items-start justify-between">
                                                            <div>
                                                                <div class="text-sm font-semibold">
                                                                    {{ $item->name }}
                                                                    <span class="text-xs text-gray-500">({{ $item->type }})</span>
                                                                </div>

                                                                {{-- SEWA --}}
                                                                @if(strtolower($item->type) === 'sewa' && !empty($item->rental_start))
                                                                    <div class="text-xs text-gray-500">
                                                                        Tgl:
                                                                        {{ $item->rental_start ? \Carbon\Carbon::parse($item->rental_start)->format('d M Y') : '-' }}
                                                                        @if(!empty($item->rental_end))
                                                                            &nbsp;—&nbsp;{{ \Carbon\Carbon::parse($item->rental_end)->format('d M Y') }}
                                                                        @endif
                                                                    </div>
                                                                @endif

                                                                {{-- ALAT --}}
                                                                @if(strtolower($item->type) === 'sewa' && isset($item->alat) && $item->alat)
                                                                    <div class="text-xs mt-1">
                                                                        Alat: <span class="font-medium">{{ $item->alat->name }}</span>
                                                                        @if($item->alat->is_locked)
                                                                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-600 text-white">TERKUNCI</span>
                                                                        @else
                                                                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-600 text-white">TERSEDIA</span>
                                                                        @endif
                                                                    </div>
                                                                @endif

                                                                {{-- UJI: tampilkan Tanggal Pengujian --}}
                                                                @if(strtolower($item->type) === 'uji')
                                                                    <div class="text-xs text-gray-500">
                                                                        <strong>Tgl Pengujian:</strong>
                                                                        {{
                                                                            $item->rental_start
                                                                                ? \Carbon\Carbon::parse($item->rental_start)->format('d M Y')
                                                                                : (
                                                                                    isset($order->test_date) && $order->test_date
                                                                                        ? \Carbon\Carbon::parse($order->test_date)->format('d M Y')
                                                                                        : '-'
                                                                                )
                                                                        }}
                                                                    </div>
                                                                @endif

                                                            </div>

                                                            {{-- harga per item (opsional) --}}
                                                            <div class="text-sm text-gray-900 dark:text-gray-100 text-right">
                                                                Rp {{ number_format($item->price ?? 0, 0, ',', '.') }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach

                                            </td>

                                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                                <div class="text-sm font-semibold">Rp {{ number_format($order->amount, 0, ',', '.') }}</div>
                                            </td>

                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium text-white
                                                    @if($order->status === 'PAID') bg-green-600
                                                    @elseif(in_array($order->status, ['PENDING','UNPAID'])) bg-yellow-400
                                                    @elseif($order->status === 'EXPIRED' || $order->status === 'CANCELLED') bg-red-500
                                                    @else bg-gray-300 @endif">
                                                    {{ $order->status }}
                                                </span>
                                            </td>

                                            <td class="px-4 py-3 text-center">
                                                <a href="{{ route('admin.orders.show', $order->id) }}" class="text-blue-600 hover:underline text-sm mr-2">Detail</a>

                                                {{-- Tombol cancel untuk user yang memesan --}}
                                                @if($order->user_id === auth()->id() && in_array($order->status, ['UNPAID','PENDING']))
                                                    <form action="{{ route('user.order.cancel', $order) }}" method="POST" class="inline-block" onsubmit="return confirm('Batalkan order ini?')">
                                                        @csrf
                                                        <button type="submit" class="text-sm px-2 py-1 bg-red-600 text-white rounded">Batal</button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- letakkan ini di tempat dimana sekarang ada {{ $orders->links() }} -->
                        <div class="mt-4 flex items-center justify-between">
                            {{-- Left: per-page selector --}}
                            <form method="GET" id="perPageForm" class="flex items-center space-x-2">
                                <label for="per_page" class="text-sm text-gray-600 dark:text-gray-300">Tampilkan</label>

                                <select name="per_page" id="per_page" onchange="this.form.submit()"
                                        class="px-2 py-1 border rounded-md bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-100 text-sm">
                                    <option value="3" {{ (int) request('per_page', 3) === 3 ? 'selected' : '' }}>3</option>
                                    <option value="10" {{ (int) request('per_page', 10) === 10 ? 'selected' : '' }}>10</option>
                                    <option value="25" {{ (int) request('per_page') === 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ (int) request('per_page') === 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ (int) request('per_page') === 100 ? 'selected' : '' }}>100</option>
                                </select>

                                <span class="text-sm text-gray-600 dark:text-gray-300">entri</span>

                                @foreach(request()->except('per_page', 'page') as $name => $value)
                                    @if(is_array($value))
                                        @foreach($value as $v)
                                            <input type="hidden" name="{{ $name }}[]" value="{{ $v }}">
                                        @endforeach
                                    @else
                                        <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                                    @endif
                                @endforeach
                            </form>

                            <!-- Right: pagination links + summary -->
                            <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-4 gap-2 sm:gap-0">
                                <!-- summary (explicit, agar mudah dikontrol spacing & warna) -->
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Menampilkan
                                    <span class="font-medium text-gray-800 dark:text-gray-100">{{ $orders->firstItem() }}</span>
                                    sampai
                                    <span class="font-medium text-gray-800 dark:text-gray-100">{{ $orders->lastItem() }}</span>
                                    dari
                                    <span class="font-medium text-gray-800 dark:text-gray-100">{{ $orders->total() }}</span>
                                    hasil
                                </p>

                                <!-- wrapper untuk links agar bisa diberi gap -->
                                <div class="flex items-center">
                                    {{-- Hapus bagian "Showing ... results" dari output links() supaya tidak dobel --}}
                                    {!! preg_replace('/<p[^>]*>.*?Showing.*?<\/p>/is', '', $orders->withQueryString()->links()) !!}
                                </div>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-gray-600 dark:text-gray-400">Belum ada order.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/litepicker/dist/css/litepicker.css"/>
    <script src="https://cdn.jsdelivr.net/npm/litepicker/dist/bundle.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const dateRangeEl = document.getElementById('dateRangeInput');
        // init only if element exists (keamanan: mencegah error bila tidak digunakan)
        if (dateRangeEl) {
            const picker = new Litepicker({
                element: dateRangeEl,
                singleMode: false,
                format: 'YYYY-MM-DD',
                setup: (picker) => {
                    picker.on('selected', (date1, date2) => {
                        const start = date1 ? date1.format('YYYY-MM-DD') : '';
                        const end = date2 ? date2.format('YYYY-MM-DD') : '';
                        const startField = document.getElementById('start_date');
                        const endField = document.getElementById('end_date');
                        if (startField) startField.value = start;
                        if (endField) endField.value = end;
                        dateRangeEl.value = (start && end) ? `${start} — ${end}` : '';
                    });
                }
            });
        }
    });
    </script>
</x-app-layout>
