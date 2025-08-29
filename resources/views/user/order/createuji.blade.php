<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Form Pemesanan Pengujian
        </h2>
    </x-slot>

    <x-slot name="head">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/litepicker/dist/css/litepicker.css" />
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <form action="{{ route('user.order.storeuji') }}" method="POST" enctype="multipart/form-data" class="space-y-8" id="order-uji-form">
                        @csrf

                        <!-- SECTION 1: INFORMASI PENYEDIA & PEMESAN -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 border-b border-gray-200 dark:border-gray-600 pb-2">
                                Informasi Penyedia & Pemesan
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="provider_name" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Nama Penyedia Jasa <span class="text-red-500">*</span></label>
                                    <input type="text" id="provider_name" name="provider_name" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white"
                                        placeholder="Masukkan nama penyedia jasa" />
                                </div>

                                <div>
                                    <label for="customer_name" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Nama Pemesan <span class="text-red-500">*</span></label>
                                    <input type="text" id="customer_name" name="customer_name" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white"
                                        placeholder="Nama pelanggan / perusahaan" />
                                </div>

                                <div class="md:col-span-2">
                                    <label for="customer_contact" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Kontak Pemesan <span class="text-red-500">*</span></label>
                                    <input type="text" id="customer_contact" name="customer_contact" required placeholder="Nomor telepon atau email"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white" />
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 2: DETAIL PAKET & PENGUJIAN -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 border-b border-blue-200 dark:border-gray-600 pb-2">
                                Detail Paket & Pengujian
                            </h3>
                            <div class="space-y-6">
                                <div>
                                    <label for="pakets" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Nama Paket Pekerjaan <span class="text-red-500">*</span></label>
                                    <input type="text" id="pakets" name="pakets[]" placeholder="Contoh: Uji Nyali, Uji Kualitas Material, dll."
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white" />
                                </div>

                                <div>
                                    <label for="deskripsi_paket_pekerjaan" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Deskripsi Paket Pekerjaan <span class="text-red-500">*</span></label>
                                    <textarea id="deskripsi_paket_pekerjaan" name="deskripsi_paket_pekerjaan" rows="3" placeholder="Jelaskan detail pekerjaan yang akan dilakukan..."
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white"></textarea>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Jenis Uji yang Diperlukan <span class="text-red-500">*</span></label>
                                    <div id="uji-wrapper" class="space-y-3"></div>
                                    <button type="button" id="add-uji" class="mt-3 inline-flex items-center px-3 py-2 text-sm font-medium text-blue-600 bg-blue-100 hover:bg-blue-200 dark:bg-blue-900 dark:text-blue-300 dark:hover:bg-blue-800 rounded-md transition">
                                        Tambah Jenis Uji
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="test_date_display" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Tanggal Pengujian</label>
                                        {{-- tampilkan teks bahwa tanggal akan ditentukan admin --}}
                                        <input type="text" id="test_date_display" value="Tanggal pengujian akan ditentukan oleh pihak admin" readonly
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100 dark:bg-gray-700 dark:text-gray-300 cursor-not-allowed" />
                                        <p class="text-xs text-gray-500 mt-1">Tanggal pengujian akan dipilih oleh admin setelah permohonan diterima.</p>
                                    </div>

                                    <div>
                                        <label for="disposisi" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Disposisi/Bagian <span class="text-red-500">*</span></label>
                                        <select id="disposisi_display" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white" disabled aria-disabled="true">
                                            <option value="Alat Berat" selected>Laboratorium</option>
                                        </select>
                                        <input type="hidden" name="disposisi" value="Laboratorium">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 3: INFORMASI SURAT PERMOHONAN -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 border-b border-green-200 dark:border-gray-600 pb-2">
                                Informasi Surat Permohonan
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="file_upload" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Upload Surat Permohonan <span class="text-red-500">*</span></label>
                                    <input type="file" id="file_upload" name="file_upload" accept=".pdf,.doc,.docx"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:text-white" />
                                    <p class="text-xs text-gray-500 mt-1">Format: PDF/DOC/DOCX, maksimal 2MB</p>
                                </div>

                                <div>
                                    <label for="tanggal_surat" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Tanggal Surat <span class="text-red-500">*</span></label>
                                    <input type="date" id="tanggal_surat" name="tanggal_surat"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white">
                                </div>

                                <div>
                                    <label for="no_surat" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Nomor Surat <span class="text-red-500">*</span></label>
                                    <input type="text" id="no_surat" name="no_surat" placeholder="Contoh: 001/ABC/XII/2025"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white">
                                </div>

                                <div>
                                    <label for="perihal" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Perihal Surat <span class="text-red-500">*</span></label>
                                    <input type="text" id="perihal" name="perihal" placeholder="Permohonan pengujian material..."
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white">
                                </div>

                                <div class="md:col-span-2">
                                    <label for="alamat_pengirim" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Alamat Pengirim <span class="text-red-500">*</span></label>
                                    <textarea id="alamat_pengirim" name="alamat_pengirim" rows="2" placeholder="Alamat lengkap pengirim surat..."
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 4: RINGKASAN PESANAN -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 border-b border-yellow-200 dark:border-gray-600 pb-2">
                                Ringkasan Pesanan
                            </h3>
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-200">
                                        Total Biaya: <span id="total-harga" class="text-blue-600 dark:text-blue-400">Rp 0</span>
                                    </p>
                                    <p class="text-sm text-gray-500 mt-1">*Harga sudah termasuk semua jenis uji yang dipilih</p>
                                </div>
                                <button type="submit" class="px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200 shadow-lg">
                                    Kirim Pesanan
                                </button>
                            </div>
                        </div>

                        <!-- Hidden field untuk amount -->
                        <input type="hidden" name="amount" id="amount" value="0">
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/litepicker/dist/litepicker.js"></script>
    <script>
        // Data harga uji dari controller. Safe fallback ke array kosong.
        const rawUjiData = @json($ujis ?? []);
        // Normalisasi jadi ujiMap[id] = { name, price }
        const ujiMap = {};
        Object.keys(rawUjiData).forEach(k => {
            const v = rawUjiData[k];
            if (v && typeof v === 'object') {
                ujiMap[k] = { name: v.name ?? `Uji ${k}`, price: Number(v.price ?? 0) };
            } else {
                ujiMap[k] = { name: `Uji ${k}`, price: Number(v ?? 0) };
            }
        });

        const ujiList = Object.keys(ujiMap);
        let ujiIndex = 0;
        const ujiWrapper = document.getElementById('uji-wrapper');
        const addUjiBtn = document.getElementById('add-uji');
        const totalHargaDisplay = document.getElementById('total-harga');
        const amountInput = document.getElementById('amount'); // hidden input di form

        function formatRp(n){ return `Rp ${Number(n||0).toLocaleString('id-ID')}`; }

        // isi option select dan cegah duplicate pilihan
        function updateUjiDropdownOptions(){
            const rows = ujiWrapper.querySelectorAll('.uji-row');
            const selected = Array.from(rows).map(r => r.querySelector('select').value);

            rows.forEach(row => {
                const select = row.querySelector('select');
                const current = select.value;
                select.innerHTML = '<option value="">-- Pilih Jenis Uji --</option>';

                ujiList.forEach(id => {
                    const already = selected.includes(id) && id !== current;
                    if (!already){
                        const opt = document.createElement('option');
                        opt.value = id;
                        opt.textContent = `${ujiMap[id].name} - ${formatRp(ujiMap[id].price)}`;
                        if (id === current) opt.selected = true;
                        select.appendChild(opt);
                    }
                });
            });

            addUjiBtn.disabled = ujiWrapper.querySelectorAll('select').length >= ujiList.length;
            addUjiBtn.textContent = addUjiBtn.disabled ? 'Semua Jenis Uji Telah Dipilih' : 'Tambah Jenis Uji';
        }

        // hitung subtotal per baris dan total keseluruhan
        function updateTotalHarga(){
            let total = 0;
            const rows = ujiWrapper.querySelectorAll('.uji-row');
            rows.forEach(row => {
                const select = row.querySelector('select');
                const qtyInput = row.querySelector('input[type="number"]');
                const qty = Math.max(1, Number(qtyInput.value || 1));
                const id = select.value;
                const unit = id && ujiMap[id] ? Number(ujiMap[id].price) : 0;
                const subtotal = unit * qty;
                const subtotalEl = row.querySelector('.row-subtotal');
                if (subtotalEl) subtotalEl.textContent = formatRp(subtotal);
                total += subtotal;
            });

            totalHargaDisplay.textContent = formatRp(total);
            if (amountInput) amountInput.value = total; // hanya referensi, server tetap hitung ulang
        }

        // buat 1 baris select + qty + subtotal + remove
        function createUjiRow(idx){
            const row = document.createElement('div');
            row.className = 'uji-row flex items-center gap-3 p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600';
            row.dataset.index = idx;

            // select wrapper
            const selectWrap = document.createElement('div');
            selectWrap.className = 'flex-1';
            const select = document.createElement('select');
            select.name = `ujis[${idx}][id]`;
            select.className = 'w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white';
            select.addEventListener('change', () => { updateUjiDropdownOptions(); updateTotalHarga(); });
            selectWrap.appendChild(select);

            // qty controls (minus, input, plus)
            const qtyWrap = document.createElement('div');
            qtyWrap.className = 'flex items-center gap-2';

            const minus = document.createElement('button');
            minus.type = 'button';
            minus.className = 'px-2 py-1 rounded-md border';
            minus.textContent = '-';

            const qty = document.createElement('input');
            qty.type = 'number';
            qty.min = 1;
            qty.value = 1;
            qty.name = `ujis[${idx}][quantity]`;
            qty.className = 'w-20 text-center rounded-md border-gray-300 dark:bg-gray-700 dark:text-white';
            qty.addEventListener('input', () => {
                if (qty.value === '' || Number(qty.value) < 1) qty.value = 1;
                updateTotalHarga();
            });

            const plus = document.createElement('button');
            plus.type = 'button';
            plus.className = 'px-2 py-1 rounded-md border';
            plus.textContent = '+';

            minus.addEventListener('click', (e) => { e.preventDefault(); qty.value = Math.max(1, (Number(qty.value)||1)-1); updateTotalHarga(); });
            plus.addEventListener('click', (e) => { e.preventDefault(); qty.value = Math.max(1, (Number(qty.value)||0)+1); updateTotalHarga(); });

            qtyWrap.appendChild(minus);
            qtyWrap.appendChild(qty);
            qtyWrap.appendChild(plus);

            // subtotal display
            const subtotalEl = document.createElement('div');
            subtotalEl.className = 'row-subtotal w-36 text-right font-medium';
            subtotalEl.textContent = formatRp(0);

            // remove button
            const remove = document.createElement('button');
            remove.type = 'button';
            remove.className = 'w-10 h-10 flex items-center justify-center text-red-500 border rounded-md';
            remove.textContent = 'X';
            remove.title = 'Hapus';
            remove.addEventListener('click', () => {
                row.remove();
                updateUjiDropdownOptions();
                updateTotalHarga();
            });

            row.appendChild(selectWrap);
            row.appendChild(qtyWrap);
            row.appendChild(subtotalEl);
            row.appendChild(remove);

            return row;
        }

        function addUjiEntry(){
            const r = createUjiRow(ujiIndex++);
            ujiWrapper.appendChild(r);
            updateUjiDropdownOptions();
            updateTotalHarga();
        }

        // init
        addUjiBtn.addEventListener('click', addUjiEntry);
        addUjiEntry(); // 1 baris default

        // validasi form dan final confirm
        const form = document.getElementById('order-uji-form');
        if (form){
            form.addEventListener('submit', function(e){
                const rows = Array.from(ujiWrapper.querySelectorAll('.uji-row'));
                const valid = rows.some(r => r.querySelector('select').value);
                if (!valid){
                    alert('Pilih minimal 1 jenis uji!');
                    e.preventDefault();
                    return false;
                }
                updateTotalHarga();
                if (!confirm(`Konfirmasi pesanan dengan total biaya ${totalHargaDisplay.textContent}?`)){
                    e.preventDefault();
                    return false;
                }
            });
        }
    </script>
</x-app-layout>
