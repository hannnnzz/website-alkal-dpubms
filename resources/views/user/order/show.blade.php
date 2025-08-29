<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            Detail Pesanan
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden shadow-2xl sm:rounded-2xl">
                <!-- header card -->
                <div class="bg-gradient-to-r from-purple-600 via-indigo-600 to-violet-600 text-white p-6 sm:p-8 rounded-t-2xl">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <h3 class="text-lg sm:text-xl font-semibold">Order ID</h3>

                            <div class="mt-3 flex items-center gap-3 flex-wrap">
                                <code id="order-id" class="px-3 py-1 rounded bg-white/15 text-sm font-medium select-all">
                                    {{ $order->order_id }}
                                </code>

                                <button id="copy-order"
                                    class="inline-flex items-center gap-2 px-3 py-1 bg-white/10 hover:bg-white/20 rounded text-sm transition"
                                    title="Salin Order ID">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2" />
                                        <rect x="8" y="8" width="12" height="12" rx="2" ry="2" />
                                    </svg>
                                    <span class="hidden sm:inline">Salin</span>
                                </button>
                            </div>

                            <p class="mt-3 text-sm text-white/90 max-w-xl leading-relaxed">
                                Pesanan dibuat oleh <strong class="font-semibold">{{ $order->customer_name }}</strong>
                                — tanggal dibuat: <span class="font-medium">{{ $order->created_at->format('d M Y H:i') }}</span>
                            </p>
                        </div>

                        <div class="text-right flex-shrink-0">
                            @php
                                $status = strtoupper($order->status ?? '');
                                $badge = match($status) {
                                    'PAID' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-800', 'label' => 'Lunas'],
                                    'PENDING' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'label' => 'Menunggu Pembayaran'],
                                    'UNPAID' => ['bg' => 'bg-slate-100', 'text' => 'text-slate-800', 'label' => 'Belum Bayar'],
                                    'EXPIRED' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'label' => 'Transaksi Gagal'],
                                    'CANCELLED' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'label' => 'Dibatalkan'],
                                    default => ['bg' => 'bg-slate-100', 'text' => 'text-slate-800', 'label' => $status ?: '—'],
                                };
                            @endphp

                            <div class="flex items-center gap-3 justify-end">
                                <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold {{ $badge['bg'] }} {{ $badge['text'] }}">
                                    {{ $badge['label'] }}
                                </span>

                                <a href="{{ route('user.order.index') }}" class="inline-flex items-center gap-2 px-3 py-1 bg-white/10 hover:bg-white/20 rounded text-sm transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                                    </svg>
                                    <span class="hidden sm:inline">Kembali</span>
                                </a>
                            </div>

                            <p class="mt-3 text-sm text-white/90">
                                <span class="text-sm">Total:</span>
                                <span class="font-semibold text-lg">Rp {{ number_format($order->amount,0,',','.') }}</span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- content -->
                <div class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-b-2xl">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- left: main details -->
                        <div class="md:col-span-2 space-y-4">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ $order->provider_name }}</h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-300 mt-1">Kontak:
                                        <span class="font-medium text-gray-700 dark:text-gray-100">{{ $order->customer_contact }}</span>
                                    </p>
                                </div>

                                <div class="flex items-center gap-3">
                                    @if($order->file_upload_path)
                                        <a href="{{ Storage::url($order->file_upload_path) }}" target="_blank"
                                           class="inline-flex items-center gap-2 px-3 py-2 border border-gray-200 dark:border-gray-700 rounded hover:bg-gray-50 dark:hover:bg-gray-700 text-sm text-gray-700 dark:text-gray-100 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 2v7h7" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 14v6a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2h7" />
                                            </svg>
                                            <span>Lihat Surat</span>
                                        </a>
                                    @endif

                                    @if(in_array($status, ['UNPAID','PENDING']))
                                        <a href="{{ route('payment.qris', $order->id) }}"
                                           class="inline-flex items-center gap-2 px-3 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded text-sm transition">
                                            Bayar Sekarang
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-2">
                                <h5 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">Rincian Item</h5>

                                {{-- Rincian items with lokasi + inline edit --}}
                                <div class="space-y-2">
                                    @foreach($order->items as $idx => $item)
                                        @php
                                            // konsisten nama field lokasi (sesuaikan jika di model nama field berbeda)
                                            $lokasi = $item->location ?? $item->lokasi ?? '-';
                                            // route untuk update item lokasi (sesuaikan nama route di web.php jika berbeda)
                                            $updateRoute = route('user.order.item.update', ['id' => $order->id, 'item' => $item->id]);
                                        @endphp

                                        <details class="group bg-gray-50 dark:bg-gray-900/60 p-4 rounded-lg border border-gray-100 dark:border-gray-700">
                                            <summary class="flex items-center justify-between cursor-pointer">
                                                <div class="flex items-center gap-3 min-w-0">
                                                    <div class="w-9 h-9 rounded-full bg-purple-100 dark:bg-purple-900 flex items-center justify-center text-purple-700 dark:text-purple-300 shrink-0">
                                                        {{ strtoupper(substr($item->type,0,1)) }}
                                                    </div>
                                                    <div class="truncate">
                                                        <div class="text-sm font-medium text-gray-800 dark:text-gray-100 truncate">{{ $item->name }}</div>
                                                        <div class="text-xs text-gray-500 dark:text-gray-300">
                                                            {{ $item->type }} •
                                                            @if(($item->type ?? '') === 'Sewa')
                                                                Durasi:
                                                                @if($item->rental_start && $item->rental_end)
                                                                    {{ \Carbon\Carbon::parse($item->rental_start)->diffInDays(\Carbon\Carbon::parse($item->rental_end)) + 1 }} hari
                                                                @else
                                                                    -
                                                                @endif
                                                            @else
                                                                Jumlah: {{ $item->quantity }} pcs
                                                            @endif
                                                        </div>
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

                                            <div class="mt-3 text-sm text-gray-600 dark:text-gray-300 space-y-2">
                                                @if(($item->type ?? '') === 'Sewa')
                                                    <p><strong>Periode:</strong>
                                                        @if($item->rental_start && $item->rental_end)
                                                            {{ \Carbon\Carbon::parse($item->rental_start)->format('d M Y') }}
                                                            —
                                                            {{ \Carbon\Carbon::parse($item->rental_end)->format('d M Y') }}
                                                        @else
                                                            -
                                                        @endif
                                                    </p>
                                                    <p><strong>Durasi hari:</strong>
                                                        @if($item->rental_start && $item->rental_end)
                                                            {{ \Carbon\Carbon::parse($item->rental_start)->diffInDays(\Carbon\Carbon::parse($item->rental_end)) + 1 }}
                                                        @else
                                                            -
                                                        @endif
                                                    </p>

                                                    {{-- LOKASI: tampilkan + tombol edit --}}
                                                    <div class="flex flex-col md:flex-row md:items-center md:gap-4">
                                                        <div id="lokasi-view-{{ $idx }}" class="flex-1">
                                                            <p class="text-sm text-gray-700 dark:text-gray-200"><strong>Lokasi:</strong> <span class="lokasi-text">{{ $lokasi }}</span></p>
                                                        </div>

                                                        <div class="mt-2 md:mt-0 flex items-center gap-2">
                                                            <!-- Tombol edit (JS) -->
                                                            <button type="button"
                                                                class="edit-lokasi-btn inline-flex items-center px-2 py-1 text-xs border rounded-md hover:bg-gray-100 dark:hover:bg-gray-800"
                                                                data-route="{{ $updateRoute }}"
                                                                data-idx="{{ $idx }}"
                                                                aria-expanded="false"
                                                                title="Edit lokasi">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2v-5" />
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                                                </svg>
                                                                Edit Lokasi
                                                            </button>

                                                            <!-- Fallback form (non-JS). Hidden by default but works if submitted -->
                                                            <form id="lokasi-form-{{ $idx }}" action="{{ $updateRoute }}" method="POST" class="lokasi-edit hidden" data-idx="{{ $idx }}">
                                                                @csrf
                                                                @method('PATCH')
                                                                <div class="flex items-center gap-2">
                                                                    <input type="text" name="lokasi" value="{{ $lokasi }}" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-2 py-1 text-sm" />
                                                                    <button type="submit" class="px-2 py-1 bg-blue-600 text-white rounded-md text-sm">Simpan</button>
                                                                    <button type="button" class="cancel-lokasi-btn px-2 py-1 border rounded-md text-sm">Batal</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                @elseif(($item->type ?? '') === 'Uji')
                                                    @php
                                                        $itemUjiDate = $item->rental_start ?? $order->test_start ?? null;
                                                    @endphp
                                                    <p><strong>Tanggal Uji:</strong>
                                                        @if($itemUjiDate)
                                                            {{ \Carbon\Carbon::parse($itemUjiDate)->format('d M Y') }}
                                                        @else
                                                            -
                                                        @endif
                                                    </p>
                                                @else
                                                    <p>Tipe item: {{ $item->type ?? '-' }}.</p>
                                                @endif
                                            </div>
                                        </details>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- right: summary / actions -->
                        <div class="space-y-4">
                            <div class="p-4 rounded-lg border border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/60">
                                <h5 class="text-sm text-gray-500 dark:text-gray-300">Ringkasan</h5>
                                <div class="mt-3 space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600 dark:text-gray-300">Subtotal</span>
                                        <span class="font-semibold text-gray-800 dark:text-gray-100">Rp {{ number_format($order->amount,0,',','.') }}</span>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    @if($status === 'PAID')
                                        <div class="text-sm text-emerald-700 font-semibold">Pembayaran diterima</div>
                                    @elseif(in_array($status, ['UNPAID','PENDING']))
                                        <a href="{{ route('payment.qris', $order->id) }}" class="block text-center w-full px-4 py-2 mt-2 bg-purple-600 hover:bg-purple-700 text-white rounded">
                                            Bayar Sekarang
                                        </a>
                                    @else
                                        <div class="text-sm text-gray-600 dark:text-gray-300">Pembayaran Kadaluarsa.</div>
                                    @endif
                                </div>
                            </div>

                            <div class="p-4 rounded-lg border border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/60 text-sm text-gray-600 dark:text-gray-300">
                                <h6 class="text-xs text-gray-500 dark:text-gray-400">Informasi</h6>
                                <p class="mt-2"><strong class="text-gray-700 dark:text-gray-100">User:</strong> {{ $order->user->name ?? '-' }}</p>
                                <p class="mt-1"><strong class="text-gray-700 dark:text-gray-100">Pesanan Dibuat:</strong> {{ $order->created_at->format('d M Y H:i') }}</p>
                            </div>
                            <a href="{{ route('user.order.invoice.pdf', $order->id) }}" target="_blank" class="inline-flex items-center gap-2 px-3 py-2 border border-gray-200 dark:border-gray-700 rounded hover:bg-gray-50 dark:hover:bg-gray-800 text-sm text-gray-700 dark:text-gray-100 transition no-print">Cetak PDF</a>
                        </div>
                    </div>

                    {{-- actions at bottom --}}
                    <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            @if(in_array($status, ['UNPAID','PENDING']))
                                <a href="{{ route('payment.qris', $order->id) }}" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded">Bayar Sekarang</a>
                            @endif

                            <a href="{{ route('user.order.index') }}" class="px-4 py-2 border border-gray-200 dark:border-gray-700 rounded hover:bg-gray-50 dark:hover:bg-gray-800 text-gray-700 dark:text-gray-100">Kembali</a>
                        </div>

                        <div class="text-sm text-gray-600 dark:text-gray-300">
                            Butuh bantuan? <a href="https://wa.me/628985171866" class="text-purple-600 hover:underline">Hubungi kami</a>
                        </div>
                    </div>
                </div> <!-- end content -->
            </div>
        </div>
    </div>

    <script>
        // copy order id - improved label restore
        document.getElementById('copy-order')?.addEventListener('click', function() {
            const btn = this;
            const id = document.getElementById('order-id').innerText.trim();
            navigator.clipboard?.writeText(id).then(() => {
                const prev = btn.innerHTML;
                btn.innerHTML = 'Tersalin ✓';
                setTimeout(() => { btn.innerHTML = prev; }, 1800);
            }).catch(()=> {
                alert('Gagal menyalin. Silakan salin manual.');
            });
        });
    </script>

    {{-- Script untuk toggle + AJAX update lokasi per item --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const csrf = '{{ csrf_token() }}';

            // buka form edit inline
            document.querySelectorAll('.edit-lokasi-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const idx = btn.dataset.idx;
                    const form = document.getElementById('lokasi-form-' + idx);
                    const view = document.getElementById('lokasi-view-' + idx);
                    if (!form || !view) return;

                    // toggle visibility
                    form.classList.remove('hidden');
                    view.classList.add('hidden');
                    btn.setAttribute('aria-expanded','true');
                });
            });

            // cancel edit
            document.querySelectorAll('.cancel-lokasi-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const form = btn.closest('.lokasi-edit');
                    if (!form) return;
                    const idx = form.dataset.idx;
                    const view = document.getElementById('lokasi-view-' + idx);
                    form.classList.add('hidden');
                    if (view) view.classList.remove('hidden');
                });
            });

            // submit via AJAX (progressive enhancement)
            document.querySelectorAll('form.lokasi-edit').forEach(form => {
                form.addEventListener('submit', async function (ev) {
                    ev.preventDefault();
                    const idx = form.dataset.idx;
                    const route = form.action;
                    const input = form.querySelector('input[name="lokasi"]');
                    if (!input) return;
                    const newLokasi = input.value.trim();

                    // basic client-side validation
                    if (newLokasi === '') {
                        alert('Lokasi tidak boleh kosong');
                        return;
                    }

                    try {
                        const res = await fetch(route, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrf,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ lokasi: newLokasi })
                        });

                        if (!res.ok) {
                            // fallback: jika server menolak karena CSRF/500, submit form biasa
                            if (res.status === 419 || res.status === 500) {
                                form.removeEventListener('submit', arguments.callee);
                                form.submit();
                                return;
                            }
                            const err = await res.json().catch(()=>({ message: 'Gagal menyimpan.' }));
                            alert(err.message || 'Gagal menyimpan lokasi.');
                            return;
                        }

                        const data = await res.json().catch(()=>({ success: true, lokasi: newLokasi }));
                        // update UI
                        const view = document.getElementById('lokasi-view-' + idx);
                        if (view) {
                            const textEl = view.querySelector('.lokasi-text');
                            if (textEl) textEl.textContent = data.lokasi ?? newLokasi;
                            view.classList.remove('hidden');
                        }
                        form.classList.add('hidden');
                    } catch (err) {
                        console.error(err);
                        // fallback to normal form submit if network error
                        form.removeEventListener('submit', arguments.callee);
                        form.submit();
                    }
                });
            });
        });
    </script>
</x-app-layout>
