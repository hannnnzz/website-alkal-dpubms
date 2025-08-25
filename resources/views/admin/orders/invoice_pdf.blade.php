{{-- resources/views/admin/orders/invoice.blade.php --}}
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Invoice - {{ $order->order_id }}</title>

<style>
    /* --- Base --- */
    :root{
        --bg:#f5f7fb;
        --card:#ffffff;
        --muted:#6b7280;
        --accent:#0f172a;
        --brand:#1f6feb;
        --success:#16a34a;
        --danger:#dc2626;
        --line:#e6e9ee;
    }
    html,body{height:100%}
    body {
        font-family: "Helvetica Neue", Arial, Helvetica, sans-serif;
        color: var(--accent);
        margin: 0;
        padding: 24px;
        background: var(--bg);
        -webkit-font-smoothing:antialiased;
    }
    .container {
        max-width: 940px;
        margin: 0 auto;
        background: var(--card);
        padding: 28px;
        border-radius: 10px;
        box-shadow: 0 6px 18px rgba(10,20,40,0.06);
        border: 1px solid rgba(15,23,42,0.03);
    }

    /* --- Header --- */
    .invoice-header { display:flex; gap:20px; justify-content:space-between; align-items:flex-start; margin-bottom:18px; }
    .brand { display:flex; gap:14px; align-items:center; }
    .brand img { width:82px; height:82px; object-fit:contain; border-radius:8px; background:#fff; padding:6px; border:1px solid var(--line); }
    .brand h1 { font-size:20px; margin:0; letter-spacing: -0.3px; color:var(--accent); }
    .meta { color:var(--muted); font-size:13px; margin-top:6px; }

    /* --- Recipient / Billing --- */
    .grid { display:flex; gap:20px; align-items:flex-start; margin-top:10px; }
    .box { background: #fbfcfe; padding:14px; border-radius:8px; border:1px solid var(--line); flex:1; }
    .box h3 { margin:0 0 6px 0; font-size:14px; color:var(--accent); }
    .detail { font-size:13px; color:var(--muted); line-height:1.5; }

    /* --- Table --- */
    .table-container {
        overflow-x: auto;
        margin-top: 14px;
        border: 1px solid var(--line);
        border-radius: 8px;
        background: #fff;
    }

    table {
        width:100%;
        border-collapse:collapse;
        font-size:12px;
        table-layout: fixed;
    }

    thead th {
        text-align:left;
        font-weight:700;
        font-size:11px;
        padding:8px 4px;
        background:#fbfdff;
        border-bottom:1px solid var(--line);
        color:var(--muted);
        word-wrap: break-word;
        overflow: hidden;
    }

    tbody td {
        padding:8px 4px;
        border-bottom:1px solid rgba(230,233,238,0.9);
        vertical-align:top;
        word-wrap: break-word;
        overflow: hidden;
        font-size: 11px;
        line-height: 1.3;
    }

    tbody tr:last-child td { border-bottom: none; }

    /* Column sizing - Fixed width untuk print */
    .col-item { width: 40%; }
    .col-period { width: 18%; }
    .col-qty { width: 8%; text-align: center; }
    .col-unit { width: 17%; text-align: right; }
    .col-total { width: 17%; text-align: right; }

    .text-right { text-align:right; }
    .text-center { text-align:center; }
    .muted { color:var(--muted); font-size:13px; }

    /* --- Summary card --- */
    .summary { width:360px; padding:12px; border-radius:8px; border:1px solid var(--line); background:#fff; }
    .sum-row { display:flex; justify-content:space-between; padding:6px 0; color:var(--muted); font-size:13px; }
    .sum-total { display:flex; justify-content:space-between; font-weight:700; padding-top:8px; border-top:1px dashed var(--line); margin-top:8px; font-size:16px; }

    /* --- Status badge --- */
    .badge { display:inline-block; padding:6px 10px; border-radius:999px; font-size:12px; color:#fff; }
    .badge.pending { background:#f59e0b; }
    .badge.paid { background:var(--success); }
    .badge.cancel { background:var(--danger); }
    .small { font-size:12px; color:var(--muted); }

    /* --- Print styles --- */
    @media print {
        body {
            background:#fff;
            padding:0;
            font-size: 11px;
            margin: 0;
        }
        .container {
            box-shadow:none;
            border-radius:0;
            padding:10mm;
            margin:0;
            border:none;
            max-width: none;
            width: 100%;
        }
        .table-container {
            overflow: visible;
            border: 1px solid #ccc;
            margin-top: 10px;
        }
        table {
            font-size: 9px;
            width: 100%;
            table-layout: fixed;
        }
        thead th {
            font-size: 8px;
            padding: 4px 2px;
            line-height: 1.2;
        }
        tbody td {
            padding: 4px 2px;
            font-size: 9px;
            line-height: 1.2;
        }
        .col-item { width: 42%; }
        .col-period { width: 16%; }
        .col-qty { width: 8%; }
        .col-unit { width: 17%; }
        .col-total { width: 17%; }
        .invoice-header {
            margin-bottom: 10px;
        }
        .brand h1 {
            font-size: 16px;
        }
        .brand img {
            width: 60px;
            height: 60px;
        }
        .summary {
            width: 280px;
            font-size: 10px;
        }
        .grid {
            gap: 10px;
        }
        .box {
            padding: 8px;
        }
        .qr-wrap {
            width: 120px;
        }
        .qr {
            width: 100px;
            height: 100px;
        }
        a { text-decoration:none; color:inherit; }
    }

    /* --- Responsive --- */
    @media (max-width:880px) {
        .invoice-header { flex-direction:column; gap:12px; }
        .grid { flex-direction:column; }
        .summary { width:100%; }
        .qr-wrap { width:100%; text-align:left; }

        .table-container {
            overflow-x: auto;
        }

        table {
            min-width: 500px;
            table-layout: auto;
        }
    }

    @media (max-width:600px) {
        body {
            padding: 12px;
        }
        .container {
            padding: 16px;
        }

        table {
            font-size: 10px;
            min-width: 480px;
        }

        thead th {
            font-size: 9px;
            padding: 6px 3px;
        }

        tbody td {
            padding: 6px 3px;
            font-size: 10px;
        }
    }
</style>
</head>
<body>
<div class="container">
    {{-- Header --}}
    <div class="invoice-header">
        <div class="brand">
            <div>
                <h1>{{ $order->provider_name ?? 'Nama Perusahaan / Lab' }}</h1>
                <div class="meta">
                    {{ $order->provider_address ?? '' }}
                    <div class="muted">{{ $order->provider_contact ?? '' }}</div>
                </div>
            </div>
        </div>

        <div style="text-align:right;">
            <div style="margin-bottom:8px;">
                <div class="small">Invoice</div>
                <div style="font-size:18px;font-weight:700;">{{ $order->order_id ?? '-' }}</div>
                <div class="muted small">{{ optional($order->created_at)->format('d M Y H:i') }}</div>
            </div>

            {{-- status badge --}}
            @php
                $s = strtoupper($order->status ?? '');
                $badgeClass = $s === 'PAID' ? 'paid' : ($s === 'CANCELLED' ? 'cancel' : 'pending');
            @endphp
            <div style="margin-top:8px;">
                <span class="badge {{ $badgeClass }}">{{ $s ?: '-' }}</span>
            </div>
        </div>
    </div>

    {{-- Billing / Ship / Summary row --}}
    <div class="grid" style="align-items:flex-start;">
        <div class="box">
            <h3>Billing To</h3>
            <div class="detail">
                <strong>{{ $order->customer_name ?? ($order->user->name ?? '-') }}</strong><br>
                {{ $order->customer_contact ?? '-' }}<br>
                @if(!empty($order->customer_address)) {{ $order->customer_address }} @endif
            </div>

            <div style="margin-top:12px;">
                <h3>Order Info</h3>
                <div class="muted small">
                    <div>Order ID: <strong>{{ $order->order_id }}</strong></div>
                    <div>Tanggal Pemesanan: {{ optional($order->created_at)->format('d M Y H:i') }}</div>
                    @if(!empty($order->test_start))
                        <div>Periode Tes: {{ \Carbon\Carbon::parse($order->test_start)->format('d M Y') }} @if(!empty($order->test_end)) — {{ \Carbon\Carbon::parse($order->test_end)->format('d M Y') }} @endif</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Items table --}}
    <div class="table-container">
        <table role="table" aria-label="Rincian item">
            <thead>
                <tr>
                    <th class="col-item">Item & Deskripsi</th>
                    <th class="col-period">Periode / Info</th>
                    <th class="col-qty text-center">Jumlah Hari</th>
                    <th class="col-unit text-right">Harga Satuan</th>
                    <th class="col-total text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($order->items as $item)
                    @php
                        $qty = $item->quantity ?? 1;
                        $price = (float) ($item->price ?? 0);
                        $unit = $qty ? ($price / $qty) : $price;
                    @endphp
                    <tr>
                        <td class="col-item">
                            <strong>{{ $item->name ?? '-' }}</strong>
                            @if(!empty($item->description))
                                <div class="muted small">{{ $item->description }}</div>
                            @endif
                            @if(!empty($item->alat))
                                <div class="muted small">Alat: {{ $item->alat->name ?? $item->alat_id }}</div>
                            @endif
                        </td>
                        <td class="col-period">
                            @if(strtolower($item->type ?? '') === 'sewa' && ($item->rental_start || $item->rental_end))
                                <div class="muted small">
                                    {{ $item->rental_start ? \Carbon\Carbon::parse($item->rental_start)->format('d M Y') : '-' }}
                                    —
                                    {{ $item->rental_end ? \Carbon\Carbon::parse($item->rental_end)->format('d M Y') : '-' }}
                                </div>
                            @else
                                <div class="muted small">{{ $item->type ?? '-' }}</div>
                            @endif
                        </td>
                        <td class="col-qty text-center">{{ $qty }}</td>
                        <td class="col-unit text-right">Rp {{ number_format($unit,0,',','.') }}</td>
                        <td class="col-total text-right">Rp {{ number_format($price,0,',','.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="muted">Tidak ada item untuk order ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Notes & Footer --}}
    <div style="display:flex; justify-content:space-between; gap:20px; margin-top:22px; align-items:flex-start;">
        <div style="flex:1;">
            <div class="notes">
                <strong>Catatan</strong>
                <div class="muted" style="margin-top:6px;">
                    Terima kasih telah melakukan pemesanan. Simpan invoice ini sebagai bukti pembayaran.
                    {{-- jika ada instruksi bank atau syarat pembayaran, tambahkan di sini --}}
                    @if(!empty($order->notes)) <div style="margin-top:8px;">{{ $order->notes }}</div> @endif
                </div>
            </div>
        </div>
        <br>
        <div style="text-align:left; width:220px;">
            <div class="small muted">Dicetak: {{ \Carbon\Carbon::now()->format('d M Y H:i') }}</div>
            @if(!empty($order->order_id))
                <div class="small muted">Referensi: {{ $order->order_id }}</div>
            @endif
        </div>
    </div>
</div>

<script>
    // auto-print jika route contains /print atau query print=1
    if (window.location.pathname.includes('/invoice/print') || new URLSearchParams(window.location.search).get('print') === '1') {
        setTimeout(()=>{ window.print(); }, 300);
    }
</script>
</body>
</html>
