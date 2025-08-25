<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Pembayaran QRIS
        </h2>
    </x-slot>

    {{-- Page background: light / dark friendly --}}
    <div class="py-12 bg-[#D9D9D9] dark:bg-gray-900 transition-colors duration-150">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 text-center">

            <p class="mb-6 text-lg text-gray-700 dark:text-gray-200">
                Silakan scan QR di bawah ini untuk membayar:
            </p>

            {{-- Card container (centered, responsive) --}}
            <div class="max-w-md mx-auto">
                <div class="bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-2xl shadow-md p-6">
                    {{-- QR content area (this akan di-update oleh JS) --}}
                    <div id="qr-content" class="flex flex-col items-center space-y-3">
                        @if(!empty($qrImage))
                            <img id="qr-image"
                                src="{{ $qrImage }}"
                                alt="QR Code"
                                class="w-48 h-48 object-contain rounded" />
                            <p class="text-sm">
                                @if($qrImageIsExternal)
                                    <a id="qr-download" href="{{ $qrImage }}" target="_blank" rel="noopener"
                                       class="underline text-blue-600 dark:text-blue-400">Buka QR di tab baru</a>
                                @else
                                    <a id="qr-download" href="{{ $qrImage }}" download
                                       class="underline text-blue-600 dark:text-blue-400">Download QR</a>
                                @endif
                            </p>
                        @elseif($order->status === 'UNPAID')
                            <p class="text-yellow-600 dark:text-yellow-400">QR belum tersedia, silakan generate.</p>
                        @else
                            <p class="text-yellow-600 dark:text-yellow-400">Tidak ada QR untuk status: {{ $order->status }}</p>
                        @endif
                    </div>

                    {{-- Small meta (order id, amount) tetap di dalam card --}}
                    <div class="mt-4 text-sm text-gray-600 dark:text-gray-300">
                        <p><strong>Order ID:</strong> {{ $order->order_id }}</p>
                        <p><strong>Jumlah:</strong> Rp {{ number_format($order->amount, 0, ',', '.') }}</p>
                        <p><strong>Status:</strong> <span id="order-status">{{ $order->status }}</span></p>
                    </div>

                    {{-- Actions (Generate / Cancel) --}}
                    <div class="mt-4 flex flex-col items-center gap-3">
                        @if($order->status === 'UNPAID')
                            <button id="generate-qris-btn"
                                    class="w-full sm:w-auto px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white disabled:opacity-50"
                                    type="button">
                                Generate QRIS
                            </button>
                        @endif

                        @if(in_array($order->status, ['UNPAID','PENDING']) && (auth()->id() === $order->user_id || (auth()->user() && method_exists(auth()->user(), 'isAdmin') && auth()->user()->isAdmin())))
                            <form id="cancel-form" action="{{ route('user.order.cancel', $order->order_id) }}" method="POST" class="w-full">
                                @csrf
                                <button type="button" id="cancel-btn" class="w-full sm:w-auto px-4 py-2 bg-red-600 hover:bg-red-700 rounded text-white">
                                    Batalkan Pesanan
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Generate QRIS script --}}
    @if($order->status === 'UNPAID')
    <script>
        (function () {
            const btn = document.getElementById('generate-qris-btn');
            const qrContent = document.getElementById('qr-content');
            const statusSpan = document.getElementById('order-status');

            if (!btn) return;

            btn.addEventListener('click', async function () {
                btn.disabled = true;
                const originalText = btn.textContent;
                btn.textContent = 'Generating...';

                try {
                    const res = await fetch("{{ route('payment.generateQris', $order->id) }}", {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({})
                    });

                    if (!res.ok) throw new Error('HTTP status ' + res.status);
                    const data = await res.json();

                    if (data.success) {
                        // Pilih sumber image: data.qrImageUrl (external) atau data.qrImage (local nama file)
                        if (data.qrImageUrl) {
                            qrContent.innerHTML = `
                                <img id="qr-image" src="${data.qrImageUrl}" alt="QR Code" class="w-48 h-48 object-contain rounded" />
                                <p class="text-sm"><a id="qr-download" href="${data.qrImageUrl}" target="_blank" rel="noopener" class="underline text-blue-600 dark:text-blue-400">Buka QR di tab baru</a></p>
                            `;
                        } else if (data.qrImage) {
                            // jika backend mengembalikan nama file yang tersimpan di storage (mis: 'qrs/123.png')
                            const qrUrl = "{{ asset('storage') }}/" + data.qrImage.replace(/^\/+/, '');
                            qrContent.innerHTML = `
                                <img id="qr-image" src="${qrUrl}" alt="QR Code" class="w-48 h-48 object-contain rounded" />
                                <p class="text-sm"><a id="qr-download" href="${qrUrl}" download class="underline text-blue-600 dark:text-blue-400">Download QR</a></p>
                            `;
                        } else {
                            // fallback: reload agar server state sinkron
                            window.location.reload();
                            return;
                        }

                        statusSpan.textContent = 'PENDING';
                        btn.disabled = true;
                        btn.textContent = 'QR Generated';
                    } else {
                        alert(data.message || 'Gagal generate QRIS');
                        btn.disabled = false;
                        btn.textContent = originalText;
                    }
                } catch (err) {
                    alert('Error: ' + err.message);
                    btn.disabled = false;
                    btn.textContent = originalText;
                }
            });
        })();
    </script>
    @endif

    {{-- Cancel script (AJAX with fallback) --}}
    <script>
        (function() {
            const cancelBtn = document.getElementById('cancel-btn');
            const cancelForm = document.getElementById('cancel-form');
            if (!cancelBtn || !cancelForm) return;

            cancelBtn.addEventListener('click', async function() {
                if (!confirm('Yakin ingin membatalkan pesanan ini?')) return;

                cancelBtn.disabled = true;
                const originalText = cancelBtn.textContent;
                cancelBtn.textContent = 'Membatalkan...';

                try {
                    const res = await fetch(cancelForm.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: new URLSearchParams(new FormData(cancelForm))
                    });

                    if (!res.ok) throw new Error('HTTP status ' + res.status);

                    let data;
                    try {
                        data = await res.json();
                    } catch (e) {
                        // bukan JSON → fallback ke normal submit
                        cancelForm.submit();
                        return;
                    }

                    if (data && data.success) {
                        window.location.reload();
                    } else {
                        alert(data.message || 'Gagal membatalkan pesanan');
                        cancelBtn.disabled = false;
                        cancelBtn.textContent = originalText;
                    }
                } catch (err) {
                    console.warn('AJAX cancel error, fallback to form submit:', err);
                    cancelForm.submit();
                }
            });
        })();
    </script>

</x-app-layout>
