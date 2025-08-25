<!DOCTYPE html>
<html>
<head>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <style>
    /* tambahkan styling sederhana */
    body { font-family: sans-serif; padding: 2rem; }
    #qrcode img { max-width: 250px; }
  </style>
</head>
<body>
  <h1>Bayar via QRIS (Midtrans)</h1>
  <label>Jumlah (Rp):</label>
  <input id="amount" type="number" value="50000"><br><br>
  <button id="payBtn">Buat QRIS</button>

  <div id="qrcode" style="margin-top:20px;"></div>

  <script>
    const token = document.querySelector('meta[name="csrf-token"]').content;
    document.getElementById('payBtn').onclick = async () => {
      const amount = document.getElementById('amount').value;
      try {
        const res = await fetch('/create-payment', {
          method: 'POST',
          headers: {
            'Content-Type':'application/json',
            'X-CSRF-TOKEN': token
          },
          body: JSON.stringify({amount})
        });
        if (!res.ok) {
          const errText = await res.text();
          throw new Error('Gagal membuat QRIS: ' + errText);
        }
        const data = await res.json();
        document.getElementById('qrcode').innerHTML =
          `<img src="${data.qr_url}" alt="QRIS">`;
        // polling status
        const iv = setInterval(async () => {
          const s = await fetch(`/api/order-status/${data.orderId}`);
          const o = await s.json();
          if (o.status==='PAID') {
            clearInterval(iv);
            window.location = '/success';
          }
        }, 5000);
      } catch (e) {
        alert(e.message);
      }
    };
  </script>
</body>
</html>
