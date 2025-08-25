{{-- resources/views/admin/orders/show.blade.php --}}
@php use Illuminate\Support\Carbon; @endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            Detail Pesanan (Admin)
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
                                Pesanan dibuat oleh <strong class="font-semibold">{{ $order->customer_name ?? ($order->user->name ?? '-') }}</strong>
                                — tanggal dibuat: <span class="font-medium">{{ optional($order->created_at)->format('d M Y H:i') }}</span>
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

                                <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center gap-2 px-3 py-1 bg-white/10 hover:bg-white/20 rounded text-sm transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                                    </svg>
                                    <span class="hidden sm:inline">Kembali</span>
                                </a>
                            </div>

                            <p class="mt-3 text-sm text-white/90">
                                <span class="text-sm">Total:</span>
                                <span class="font-semibold text-lg">Rp {{ number_format($order->amount ?? 0,0,',','.') }}</span>
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
                                    <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ $order->provider_name ?? '-' }}</h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-300 mt-1">Kontak:
                                        <span class="font-medium text-gray-700 dark:text-gray-100">{{ $order->customer_contact ?? '-' }}</span>
                                    </p>
                                </div>

                                <div class="flex items-center gap-3">
                                    @if(!empty($order->file_upload_path))
                                        <a href="{{ Storage::url($order->file_upload_path) }}" target="_blank"
                                           class="inline-flex items-center gap-2 px-3 py-2 border border-gray-200 dark:border-gray-700 rounded hover:bg-gray-50 dark:hover:bg-gray-700 text-sm text-gray-700 dark:text-gray-100 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 2v7h7" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 14v6a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2h7" />
                                            </svg>
                                            <span>Lihat Surat</span>
                                        </a>
                                    @endif

                                    @if(!empty($order->qr_image_path))
                                        <a href="{{ Storage::url($order->qr_image_path) }}" target="_blank"
                                           class="inline-flex items-center gap-2 px-3 py-2 border border-gray-200 dark:border-gray-700 rounded hover:bg-gray-50 dark:hover:bg-gray-700 text-sm text-gray-700 dark:text-gray-100 transition">
                                           <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                               <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M3 12h18M3 17h18" />
                                           </svg>
                                           <span>Lihat QR</span>
                                        </a>
                                    @endif

                                    @if(in_array($status, ['UNPAID','PENDING']))
                                        <a href="{{ route('payment.qris', $order->order_id) }}"
                                           class="inline-flex items-center gap-2 px-3 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded text-sm transition">
                                            Bayar (QRIS)
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="p-4 border border-gray-100 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-900/60">
                                    <h5 class="text-sm text-gray-500 dark:text-gray-300">Tanggal Mulai (test_start)</h5>
                                    <p class="mt-2 text-sm font-medium text-gray-800 dark:text-gray-100">
                                        @if(!empty($order->test_start))
                                            {{ Carbon::parse($order->test_start)->format('d M Y') }}
                                        @else
                                            -
                                        @endif
                                    </p>
                                </div>

                                <div class="p-4 border border-gray-100 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-900/60">
                                    <h5 class="text-sm text-gray-500 dark:text-gray-300">Tanggal Selesai (test_end)</h5>
                                    <p class="mt-2 text-sm font-medium text-gray-800 dark:text-gray-100">
                                        @if(!empty($order->test_end))
                                            {{ Carbon::parse($order->test_end)->format('d M Y') }}
                                        @else
                                            -
                                        @endif
                                    </p>
                                </div>
                            </div>

                            {{-- invoice / info table (menampilkan semua kolom penting) --}}
                            <div class="mt-2 p-4 border border-gray-100 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-900/60">
                                <h5 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-3">Informasi Invoice / Detail Pesanan</h5>

                                <table class="w-full text-sm">
                                    <tbody>
                                        <tr>
                                            <td class="py-2 font-semibold w-44">ID (menaik)</td>
                                            <td class="py-2">{{ $order->id }}</td>
                                        </tr>
                                        <tr>
                                            <td class="py-2 font-semibold">user_id</td>
                                            <td class="py-2">{{ $order->user_id ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="py-2 font-semibold">order_id</td>
                                            <td class="py-2">{{ $order->order_id ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="py-2 font-semibold">provider_name</td>
                                            <td class="py-2">{{ $order->provider_name ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="py-2 font-semibold">customer_name</td>
                                            <td class="py-2">{{ $order->customer_name ?? ($order->user->name ?? '-') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="py-2 font-semibold">customer_contact</td>
                                            <td class="py-2">{{ $order->customer_contact ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="py-2 font-semibold">file_upload_path</td>
                                            <td class="py-2">
                                                @if(!empty($order->file_upload_path))
                                                    <a href="{{ Storage::url($order->file_upload_path) }}" target="_blank" class="underline">Lihat / Unduh</a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="py-2 font-semibold">amount</td>
                                            <td class="py-2">Rp {{ number_format($order->amount ?? 0,0,',','.') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="py-2 font-semibold">status</td>
                                            <td class="py-2">{{ $order->status ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="py-2 font-semibold">paid_at</td>
                                            <td class="py-2">{{ $order->paid_at ? Carbon::parse($order->paid_at)->format('d M Y H:i') : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="py-2 font-semibold">tanggal_masuk</td>
                                            <td class="py-2">{{ $order->tanggal_masuk ? Carbon::parse($order->tanggal_masuk)->format('d M Y') : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="py-2 font-semibold">hari</td>
                                            <td class="py-2">{{ $order->durasi_hari ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="py-2 font-semibold">tanggal_surat</td>
                                            <td class="py-2">{{ $order->tanggal_surat ? Carbon::parse($order->tanggal_surat)->format('d M Y') : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="py-2 font-semibold">no_surat</td>
                                            <td class="py-2">{{ $order->no_surat ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="py-2 font-semibold align-top">alamat_pengirim</td>
                                            <td class="py-2 whitespace-pre-wrap">{{ $order->alamat_pengirim ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="py-2 font-semibold align-top">perihal</td>
                                            <td class="py-2">{{ $order->perihal ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="py-2 font-semibold align-top">disposisi</td>
                                            <td class="py-2">{{ $order->disposisi ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="py-2 font-semibold align-top">deskripsi_paket_pekerjaan</td>
                                            <td class="py-2 whitespace-pre-wrap">{{ $order->deskripsi_paket_pekerjaan ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="py-2 font-semibold">qr_image_path</td>
                                            <td class="py-2">
                                                @if(!empty($order->qr_image_path))
                                                    <a href="{{ Storage::url($order->qr_image_path) }}" target="_blank" class="underline">Lihat QR</a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="py-2 font-semibold">created_at</td>
                                            <td class="py-2">{{ optional($order->created_at)->format('d M Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="py-2 font-semibold">updated_at</td>
                                            <td class="py-2">{{ optional($order->updated_at)->format('d M Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="py-2 font-semibold">test_start</td>
                                            <td class="py-2">{{ $order->test_start ? Carbon::parse($order->test_start)->format('d M Y') : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="py-2 font-semibold">test_end</td>
                                            <td class="py-2">{{ $order->test_end ? Carbon::parse($order->test_end)->format('d M Y') : '-' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-4">
                                <h5 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">Rincian Item</h5>
                                <div class="space-y-2">
                                    @foreach($order->items as $idx => $item)
                                        <details class="group bg-gray-50 dark:bg-gray-900/60 p-4 rounded-lg border border-gray-100 dark:border-gray-700">
                                            <summary class="flex items-center justify-between cursor-pointer">
                                                <div class="flex items-center gap-3 min-w-0">
                                                    <div class="w-9 h-9 rounded-full bg-purple-100 dark:bg-purple-900 flex items-center justify-center text-purple-700 dark:text-purple-300 shrink-0">
                                                        {{ strtoupper(substr($item->type ?? 'I',0,1)) }}
                                                    </div>
                                                    <div class="truncate">
                                                        <div class="text-sm font-medium text-gray-800 dark:text-gray-100 truncate">{{ $item->name ?? '-' }}</div>
                                                        <div class="text-xs text-gray-500 dark:text-gray-300"> {{ $item->type ?? '-' }} • Jumlah: {{ $item->quantity ?? 1 }}</div>
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
                                                @if(($item->type ?? '') === 'Sewa')
                                                    <p><strong>Periode:</strong>
                                                        {{ $item->rental_start ?? '-' }} — {{ $item->rental_end ?? '-' }}
                                                    </p>
                                                    <p><strong>Durasi hari:</strong>
                                                        @if(!empty($item->rental_start) && !empty($item->rental_end))
                                                            {{ \Carbon\Carbon::parse($item->rental_start)->diffInDays(\Carbon\Carbon::parse($item->rental_end)) + 1 }}
                                                        @else
                                                            -
                                                        @endif
                                                    </p>
                                                @else
                                                    <p>Item pengujian (tidak ada periode sewa).</p>
                                                @endif
                                            </div>
                                        </details>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- right: summary / admin actions -->
                        <div class="space-y-4">
                            <div class="p-4 rounded-lg border border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/60">
                                <h5 class="text-sm text-gray-500 dark:text-gray-300">Ringkasan</h5>
                                <div class="mt-3 space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600 dark:text-gray-300">Subtotal</span>
                                        <span class="font-semibold text-gray-800 dark:text-gray-100">Rp {{ number_format($order->amount ?? 0,0,',','.') }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600 dark:text-gray-300">Biaya layanan</span>
                                        <span class="text-gray-600 dark:text-gray-300">—</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600 dark:text-gray-300">Potongan</span>
                                        <span class="text-gray-600 dark:text-gray-300">—</span>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    {{-- tindakan admin: control status via resourceful update --}}
                                    @if($status === 'PAID')
                                        <div class="text-sm text-emerald-700 font-semibold">Pembayaran sudah diterima</div>
                                    @else
                                        <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" class="space-y-2">
                                            @csrf
                                            @method('PUT')

                                            <label class="text-xs text-gray-500 dark:text-gray-400">Ubah status</label>
                                            <div class="flex gap-2">
                                                <select name="status" class="px-3 py-2 border rounded-md bg-white dark:bg-gray-700 text-sm">
                                                    <option value="UNPAID" {{ $status === 'UNPAID' ? 'selected' : '' }}>UNPAID</option>
                                                    <option value="PENDING" {{ $status === 'PENDING' ? 'selected' : '' }}>PENDING</option>
                                                    <option value="PAID" {{ $status === 'PAID' ? 'selected' : '' }}>PAID</option>
                                                    <option value="EXPIRED" {{ $status === 'EXPIRED' ? 'selected' : '' }}>EXPIRED</option>
                                                    <option value="CANCELLED" {{ $status === 'CANCELLED' ? 'selected' : '' }}>CANCELLED</option>
                                                </select>

                                                <button type="submit" class="px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded text-sm">Simpan</button>
                                            </div>
                                        </form>

                                        <div class="flex gap-2 mt-2">
                                            {{-- Quick actions --}}
                                            @if($status !== 'PAID')
                                                <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" onsubmit="return confirm('Tandai pesanan ini sebagai LUNAS?')">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="PAID">
                                                    <button type="submit" class="w-full px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-sm">Mark as PAID</button>
                                                </form>
                                            @endif

                                            @if($status !== 'CANCELLED')
                                                <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" onsubmit="return confirm('Batalkan pesanan ini?')">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="CANCELLED">
                                                    <button type="submit" class="w-full px-3 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded text-sm">Cancel</button>
                                                </form>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="p-4 rounded-lg border border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/60 text-sm text-gray-600 dark:text-gray-300">
                                <h6 class="text-xs text-gray-500 dark:text-gray-400">Informasi</h6>
                                <p class="mt-2"><strong class="text-gray-700 dark:text-gray-100">User:</strong> {{ $order->user->name ?? '-' }}</p>
                                <p class="mt-1"><strong class="text-gray-700 dark:text-gray-100">Created at:</strong> {{ optional($order->created_at)->format('d M Y H:i') }}</p>
                                <p class="mt-1"><strong class="text-gray-700 dark:text-gray-100">Order Key:</strong> <span class="font-mono text-xs">{{ $order->id }}</span></p>

                                {{-- tombol tambahan admin --}}
                                <div class="mt-3 flex flex-col gap-2">
                                    <a href="{{ route('admin.orders.invoice', $order->id) }}" class="inline-flex items-center gap-2 px-3 py-2 border border-gray-200 dark:border-gray-700 rounded hover:bg-gray-50 dark:hover:bg-gray-800 text-sm text-gray-700 dark:text-gray-100 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v8m-4-4h8"/></svg>
                                        Download Invoice
                                    </a>

                                    {{-- Link ke halaman user jika ada --}}
                                    @if(!empty($order->user))
                                        <a href="{{ route('admin.users.show', $order->user->id) ?? '#' }}" class="inline-flex items-center gap-2 px-3 py-2 border border-gray-200 dark:border-gray-700 rounded hover:bg-gray-50 dark:hover:bg-gray-800 text-sm text-gray-700 dark:text-gray-100 transition">
                                            Lihat Profil User
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- actions at bottom --}}
                    <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            @if(in_array($status, ['UNPAID','PENDING']))
                                <a href="{{ route('payment.qris', $order->order_id) }}" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded">Bayar (QRIS)</a>
                            @endif

                            <a href="{{ route('admin.orders.index') }}" class="px-4 py-2 border border-gray-200 dark:border-gray-700 rounded hover:bg-gray-50 dark:hover:bg-gray-800 text-gray-700 dark:text-gray-100">Kembali</a>
                        </div>

                        <div class="text-sm text-gray-600 dark:text-gray-300">
                            Butuh bantuan? <a href="mailto:support@example.com" class="text-purple-600 hover:underline">Hubungi kami</a>
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
                setTimeout(() => { btn.innerHTML = prev; }, 1600);
            }).catch(()=> {
                alert('Gagal menyalin. Silakan salin manual.');
            });
        });
    </script>
</x-app-layout>
