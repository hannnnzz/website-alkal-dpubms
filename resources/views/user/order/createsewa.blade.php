<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Form Pemesanan Sewa Alat
        </h2>
    </x-slot>

    <x-slot name="head">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/litepicker/dist/css/litepicker.css" />
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded">
                            <strong>Ada kesalahan:</strong>
                            <ul class="mt-2 list-disc list-inside">
                                @foreach ($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('user.order.storesewa') }}" method="POST" enctype="multipart/form-data" class="space-y-8" id="order-sewa-form">
                        @csrf

                        <!-- SECTION 1: INFORMASI PENYEDIA & PEMESAN -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 border-b border-gray-200 dark:border-gray-600 pb-2">
                                Informasi Penyedia & Pemesan
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="provider_name" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Nama Penyedia Alat <span class="text-red-500">*</span></label>
                                    <input type="text" id="provider_name" name="provider_name" required placeholder="Masukkan nama penyedia alat"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white">
                                </div>

                                <div>
                                    <label for="customer_name" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Nama Pemesan <span class="text-red-500">*</span></label>
                                    <input type="text" id="customer_name" name="customer_name" required placeholder="Nama pelanggan / perusahaan"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white">
                                </div>

                                <div class="md:col-span-2">
                                    <label for="customer_contact" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Kontak Pemesan <span class="text-red-500">*</span></label>
                                    <input type="text" id="customer_contact" name="customer_contact" required placeholder="Nomor telepon atau email"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white">
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 2: DETAIL PAKET & SEWA ALAT -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 border-b border-gray-200 dark:border-gray-600 pb-2">
                                Detail Paket & Sewa Alat
                            </h3>
                            <div class="space-y-6">
                                <div>
                                    <label for="pakets" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Nama Paket Pekerjaan <span class="text-red-500">*</span></label>
                                    <input type="text" id="pakets" name="pakets[]" placeholder="Contoh: Perkerasan Jalan, Jembatan, Pembangunan Gedung, dll."
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white">
                                </div>

                                <div>
                                    <label for="deskripsi_paket_pekerjaan" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Deskripsi Paket Pekerjaan <span class="text-red-500">*</span></label>
                                    <textarea id="deskripsi_paket_pekerjaan" name="deskripsi_paket_pekerjaan" rows="3" placeholder="Jelaskan detail pekerjaan yang akan dilakukan..."
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white"></textarea>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Sewa Alat & Periode <span class="text-red-500">*</span></label>
                                    <div id="alat-wrapper" class="space-y-3"></div>
                                    <button type="button" id="add-alat" class="mt-3 inline-flex items-center px-3 py-2 text-sm font-medium text-blue-600 bg-blue-100 hover:bg-blue-200 dark:bg-blue-900 dark:text-blue-300 dark:hover:bg-blue-800 rounded-md transition">
                                        Tambah Alat Sewa
                                    </button>
                                    <p class="text-xs text-gray-500 mt-2">Isikan lokasinya (mis. "Proyek Jl. Merdeka KM 3") untuk tiap alat.</p>
                                </div>

                                <div>
                                    <label for="disposisi" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Disposisi/Bagian <span class="text-red-500">*</span></label>
                                    <select id="disposisi_display" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white" disabled aria-disabled="true">
                                        <option value="Alat Berat" selected>Alat Berat</option>
                                    </select>
                                    <input type="hidden" name="disposisi" value="Alat Berat">
                                </div>

                            </div>
                        </div>

                        <!-- SECTION 3: INFORMASI SURAT PERMOHONAN -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 border-b border-gray-200 dark:border-gray-600 pb-2">
                                Informasi Surat Permohonan
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="file_upload" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Upload Surat Permohonan <span class="text-red-500">*</span></label>
                                    <input type="file" id="file_upload" name="file_upload" accept=".pdf,.doc,.docx"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:text-white">
                                    <p class="text-xs text-gray-500 mt-1">Format: PDF/DOC/DOCX, maksimal 2MB</p>
                                </div>

                                <div>
                                    <label for="tanggal_surat" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Tanggal Surat <span class="text-red-500">*</span></label>
                                    <input type="date" id="tanggal_surat" name="tanggal_surat"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white">
                                </div>

                                <div>
                                    <label for="no_surat" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Nomor Surat <span class="text-red-500">*</span></label>
                                    <input type="text" id="no_surat" name="no_surat" placeholder="Contoh: 001/ABC/XII/2024"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-white">
                                </div>

                                <div>
                                    <label for="perihal" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Perihal Surat <span class="text-red-500">*</span></label>
                                    <input type="text" id="perihal" name="perihal" placeholder="Permohonan sewa alat berat..."
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
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 border-b border-gray-200 dark:border-gray-600 pb-2">
                                Ringkasan Pesanan
                            </h3>
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-200">
                                        Total Biaya: <span id="total-harga" class="text-blue-600 dark:text-blue-400">Rp 0</span>
                                    </p>
                                    <p class="text-sm text-gray-500 mt-1">*Harga berdasarkan durasi sewa dan jenis alat yang dipilih</p>
                                </div>
                                <button type="submit" class="px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200 shadow-lg">
                                    Kirim Pesanan
                                </button>
                            </div>
                        </div>

                        <input type="hidden" name="amount" id="amount" value="0">
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/litepicker/dist/litepicker.js"></script>
    <script>
        // URL & token untuk AJAX
        const CHECK_AVAIL_URL = "{{ route('user.alats.checkAvailability') }}";
        const CSRF_TOKEN = "{{ csrf_token() }}";

        // Data harga alat dari controller (format: { "nama": { harga: number, locked: bool } })
        const alatHarga = @json($alats ?? []);
        const alatList = Object.keys(alatHarga);
        let alatIndex = 0;

        const wrapper = document.getElementById('alat-wrapper');
        const addBtn = document.getElementById('add-alat');
        const totalHargaDisplay = document.getElementById('total-harga');
        const amountInput = document.getElementById('amount');

        function formatRp(n){ return `Rp ${Number(n||0).toLocaleString('id-ID')}`; }

        function updateDropdownOptions(){
            const rows = wrapper.querySelectorAll('.sewa-row');
            const selected = Array.from(rows).map(r => (r.querySelector('select')?.value) || '');

            rows.forEach(row => {
                const select = row.querySelector('select');
                const current = select.value;
                select.innerHTML = '<option value="">-- Pilih Alat --</option>';

                alatList.forEach(alat => {
                    const info = alatHarga[alat] || { harga: 0, locked: false };
                    const already = selected.includes(alat) && alat !== current;
                    if (already) return;
                    const opt = document.createElement('option');
                    opt.value = alat;
                    opt.textContent = `${alat} - ${formatRp(info.harga)}` + (info.locked ? ' (Nonaktif)' : '');
                    if (info.locked) opt.disabled = true;
                    if (alat === current) opt.selected = true;
                    select.appendChild(opt);
                });
            });

            const unlocked = alatList.filter(a => !(alatHarga[a] && alatHarga[a].locked));
            const availableUnselectedCount = unlocked.filter(a => !selected.includes(a)).length;
            addBtn.disabled = availableUnselectedCount <= 0;
            addBtn.textContent = addBtn.disabled ? 'Semua Alat Telah Dipilih' : 'Tambah Alat Sewa';
        }

        function updateTotalHarga(){
            let total = 0;
            const rows = wrapper.querySelectorAll('.sewa-row');

            rows.forEach(row => {
                const select = row.querySelector('select');
                const dateInput = row.querySelector('input[type="text"].daterange');
                const subtotalEl = row.querySelector('.row-subtotal');
                const alat = select?.value;
                const harga = alat && alatHarga[alat] ? Number(alatHarga[alat].harga || 0) : 0;
                let subtotal = 0;

                if (dateInput && dateInput.value){
                    const val = dateInput.value;
                    const parts = val.split(' - ').map(s => s.trim());
                    if (parts.length === 2 && parts[0] && parts[1]){
                        const d1 = new Date(parts[0]);
                        const d2 = new Date(parts[1]);
                        if (!isNaN(d1) && !isNaN(d2)){
                            // hitung hari termasuk kedua ujung (inclusive)
                            const diff = Math.ceil((d2 - d1) / (1000*60*60*24)) + 1;
                            if (diff > 0) subtotal = diff * harga;
                        }
                    }
                }

                subtotalEl.textContent = formatRp(subtotal);
                total += subtotal;
            });

            totalHargaDisplay.textContent = formatRp(total);
            if (amountInput) amountInput.value = total;
        }

        function validateSelectedNotLocked(){
            const selects = wrapper.querySelectorAll('select');
            for (const s of selects){
                const val = s.value;
                if (val && alatHarga[val] && alatHarga[val].locked){
                    alert(`Alat "${val}" sudah dinonaktifkan. Silakan pilih alat lain.`);
                    s.value = '';
                    updateDropdownOptions();
                    updateTotalHarga();
                    return false;
                }
            }
            return true;
        }

        async function checkAvailability(alat, start, end){
            try {
                const res = await fetch(CHECK_AVAIL_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ alat, start, end })
                });
                if (!res.ok){
                    const err = await res.json().catch(()=>({}));
                    return { available: false, error: err.message || 'Gagal cek ketersediaan' };
                }
                const data = await res.json();
                return { available: !!data.available, error: data.message || null };
            } catch (e) {
                console.error('checkAvailability error', e);
                return { available: false, error: 'Network error' };
            }
        }

        // Ambil tanggal yang sudah dibooking (untuk lockDays)
        async function fetchBookedDates(alat){
            try {
                const res = await fetch(`/user/alats/booked-dates/${encodeURIComponent(alat)}`);
                if (!res.ok) return [];
                const d = await res.json();
                return Array.isArray(d) ? d : [];
            } catch (e) {
                console.error(e);
                return [];
            }
        }

        function initLitepicker(id, input, alatSelect, single = false){
            let picker = null;

            async function setLockedDates(){
                const alat = alatSelect.value;
                const bookedDates = alat ? (await fetchBookedDates(alat)) : [];
                if (picker) try { picker.destroy(); } catch(e){}
                picker = new Litepicker({
                    element: input,
                    singleMode: single,
                    format: 'YYYY-MM-DD',
                    minDate: new Date(),
                    lockDays: bookedDates,
                    setup: (p) => {
                        p.on('selected', async () => {
                            updateTotalHarga();
                            if (single) return;
                            const val = input.value || '';
                            const [start, end] = val.split(' - ').map(s=>s.trim());
                            const alat = alatSelect.value;
                            if (!alat || !start || !end) return;
                            const r = await checkAvailability(alat, start, end);
                            if (!r.available){
                                alert(r.error || 'Tanggal yang dipilih bentrok dengan pemesanan lain. Silakan pilih rentang lain.');
                                input.value = '';
                                try { p.clearSelection(); } catch(e){}
                                updateTotalHarga();
                            }
                        });
                    }
                });
            }

            alatSelect.addEventListener('change', setLockedDates);
            setLockedDates();
        }

        function createSewaRow(idx){
            const row = document.createElement('div');
            row.className = 'sewa-row flex items-center gap-3 p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600';

            // select alat (flex-1)
            const selectWrap = document.createElement('div');
            selectWrap.className = 'flex-1';
            const select = document.createElement('select');
            select.name = `sewa[${idx}][alat]`;
            select.className = 'w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white';
            select.addEventListener('change', () => { updateDropdownOptions(); updateTotalHarga(); });
            selectWrap.appendChild(select);

            // lokasi (w-1/4)
            const lokasiWrap = document.createElement('div');
            lokasiWrap.className = 'w-1/4';
            const lokasi = document.createElement('input');
            lokasi.type = 'text';
            lokasi.name = `sewa[${idx}][lokasi]`;
            lokasi.placeholder = 'Lokasi alat / lokasi kerja';
            lokasi.className = 'w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white';
            lokasiWrap.appendChild(lokasi);

            // tanggal range (w-1/3)
            const dateWrap = document.createElement('div');
            dateWrap.className = 'w-1/3';
            const dateInput = document.createElement('input');
            dateInput.type = 'text';
            dateInput.name = `sewa[${idx}][tanggal]`;
            dateInput.className = 'w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white daterange';
            dateInput.placeholder = 'Pilih rentang tanggal sewa';
            dateWrap.appendChild(dateInput);

            // subtotal display
            const subtotal = document.createElement('div');
            subtotal.className = 'row-subtotal w-36 text-right font-medium';
            subtotal.textContent = formatRp(0);

            // remove button
            const remove = document.createElement('button');
            remove.type = 'button';
            remove.className = 'w-10 h-10 flex items-center justify-center text-red-500 border rounded-md';
            remove.textContent = 'X';
            remove.title = 'Hapus';
            remove.addEventListener('click', () => {
                row.remove();
                updateDropdownOptions();
                updateTotalHarga();
            });

            row.appendChild(selectWrap);
            row.appendChild(lokasiWrap);
            row.appendChild(dateWrap);
            row.appendChild(subtotal);
            row.appendChild(remove);

            return { row, select, dateInput: dateInput };
        }

        function addAlatEntry(){
            const idx = alatIndex++;
            const { row, select, dateInput } = createSewaRow(idx);
            wrapper.appendChild(row);

            updateDropdownOptions();

            // inisialisasi litepicker untuk row ini (mengunci tanggal sesuai alat)
            initLitepicker(`daterange-${idx}`, dateInput, select, false);

            // listener perubahan tanggal (total otomatis diupdate oleh picker selected handler, tapi juga safety)
            dateInput.addEventListener('input', updateTotalHarga);

            updateTotalHarga();
        }

        addBtn.addEventListener('click', addAlatEntry);

        // buat 1 baris default
        addAlatEntry();

        // ketika wrapper berubah, pengecekan locked pilihan
        wrapper.addEventListener('change', function(e){
            if (e.target && e.target.tagName === 'SELECT'){
                validateSelectedNotLocked();
            }
        });

        // validasi form saat submit
        const form = document.getElementById('order-sewa-form');
        if (form){
            form.addEventListener('submit', function(e){
                const rows = Array.from(wrapper.querySelectorAll('.sewa-row'));
                const valid = rows.some(r => r.querySelector('select')?.value);
                if (!valid){
                    alert('Pilih minimal 1 alat untuk disewa!');
                    e.preventDefault();
                    return false;
                }
                if (!validateSelectedNotLocked()){
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
