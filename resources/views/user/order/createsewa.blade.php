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
                    <form action="{{ route('user.order.storesewa') }}" method="POST" enctype="multipart/form-data" class="space-y-8" id="order-form">
                        @csrf

                        <!-- SECTION 1: INFORMASI PENYEDIA & PEMESAN -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 border-b border-gray-200 dark:border-gray-600 pb-2">
                                Informasi Penyedia & Pemesan
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="provider_name" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Nama Penyedia Alat <span class="text-red-500">*</span></label>
                                    <input type="text" id="provider_name" name="provider_name" required placeholder="Masukkan nama penyedia jasa/alat"
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
                        <div class="bg-blue-50 dark:bg-gray-700 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 border-b border-blue-200 dark:border-gray-600 pb-2">
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
                                    <div id="alat-wrapper" class="space-y-4"></div>
                                    <button type="button" id="add-alat" class="mt-3 inline-flex items-center px-3 py-2 text-sm font-medium text-blue-600 bg-blue-100 hover:bg-blue-200 dark:bg-blue-900 dark:text-blue-300 dark:hover:bg-blue-800 rounded-md transition">
                                        Tambah Alat Sewa
                                    </button>
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
                        <div class="bg-green-50 dark:bg-gray-700 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 border-b border-green-200 dark:border-gray-600 pb-2">
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
                        <div class="bg-yellow-50 dark:bg-gray-700 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 border-b border-yellow-200 dark:border-gray-600 pb-2">
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

                        <!-- Hidden field untuk amount -->
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

        let alatIndex = 0;
        const alatHarga = @json($alats); // format: { "Nama Alat": { harga: xxx, locked: true/false } }
        const alatList = Object.keys(alatHarga);

        const wrapper = document.getElementById('alat-wrapper');
        const addBtn = document.getElementById('add-alat');
        const totalHargaDisplay = document.getElementById('total-harga');
        const amountInput = document.getElementById('amount');

        function updateDropdownOptions() {
            const selects = wrapper.querySelectorAll('select');
            const selected = Array.from(selects).map(s => s.value);

            selects.forEach(select => {
                const currentValue = select.value;
                select.innerHTML = '';

                alatList.forEach(alat => {
                    const alreadySelected = selected.includes(alat) && alat !== currentValue;
                    const isLocked = !!(alatHarga[alat] && alatHarga[alat].locked);
                    if (alreadySelected) return;

                    const option = document.createElement('option');
                    option.value = alat;
                    option.textContent = `${alat} - Rp ${alatHarga[alat].harga.toLocaleString()}${isLocked ? ' (Nonaktif)' : ''}`;
                    if (isLocked) option.disabled = true;
                    if (alat === currentValue) option.selected = true;

                    select.appendChild(option);
                });
            });

            const unlockedAlats = alatList.filter(a => !alatHarga[a].locked);
            const availableUnselectedCount = unlockedAlats.filter(a => !selected.includes(a)).length;
            addBtn.disabled = availableUnselectedCount <= 0;
        }

        function updateTotalHarga() {
            let total = 0;
            document.querySelectorAll('input[id^="daterange-"]').forEach((input) => {
                const val = input.value || '';
                const [start, end] = val.split(' - ');
                if (start && end) {
                    const tglAwal = new Date(start);
                    const tglAkhir = new Date(end);
                    const selisihHari = Math.ceil((tglAkhir - tglAwal) / (1000 * 60 * 60 * 24)) + 1;

                    const alatSelect = input.parentElement.querySelector('select');
                    const alat = alatSelect.value;
                    const hargaPerHari = (alatHarga[alat] && alatHarga[alat].harga) ? alatHarga[alat].harga : 0;
                    total += selisihHari * hargaPerHari;
                }
            });
            totalHargaDisplay.textContent = `Rp ${total.toLocaleString()}`;
            amountInput.value = total; // update hidden amount field
        }

        function validateSelectedNotLocked() {
            const selects = wrapper.querySelectorAll('select');
            for (const s of selects) {
                const val = s.value;
                if (val && alatHarga[val] && alatHarga[val].locked) {
                    alert(`Alat "${val}" sudah dinonaktifkan. Silakan pilih alat lain.`);
                    s.value = ''; // clear selection
                    updateDropdownOptions();
                    updateTotalHarga();
                    return false;
                }
            }
            return true;
        }

        wrapper.addEventListener('change', function(e) {
            if (e.target && e.target.tagName === 'SELECT') {
                validateSelectedNotLocked();
            }
        });

        const form = document.getElementById('order-form');
        if (form) {
            form.addEventListener('submit', function(e) {
                if (!validateSelectedNotLocked()) {
                    e.preventDefault();
                    return false;
                }
                // pastikan total valid (jika kamu ingin wajib >0, bisa tambahkan cek)
                updateTotalHarga();
            });
        }

        async function checkAvailability(alat, start, end) {
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
                if (!res.ok) {
                    const err = await res.json().catch(()=>({}));
                    return { available: false, error: err.message || 'Gagal cek ketersediaan' };
                }
                const data = await res.json();
                return { available: !!data.available };
            } catch (e) {
                console.error('checkAvailability error', e);
                return { available: false, error: 'Network error' };
            }
        }

        function initLitepicker(id, input, alatSelect, single = false) {
            let picker;
            async function setLockedDates() {
                const alat = alatSelect.value;
                if (!alat) return;
                const res = await fetch(`/user/alats/booked-dates/${encodeURIComponent(alat)}`);
                const bookedDates = res.ok ? await res.json() : [];
                if (picker) picker.destroy();
                picker = new Litepicker({
                    element: input,
                    singleMode: single,
                    format: 'YYYY-MM-DD',
                    minDate: new Date(),
                    lockDays: bookedDates,
                    setup: (picker) => {
                        picker.on('selected', async () => {
                            updateTotalHarga();
                            if (single) return; // tidak cek availability untuk singleDate test_date
                            const val = input.value || '';
                            const [start, end] = val.split(' - ').map(s => s.trim());
                            const alat = alatSelect.value;
                            if (!alat || !start || !end) return;
                            const r = await checkAvailability(alat, start, end);
                            if (!r.available) {
                                alert(r.error || 'Tanggal yang dipilih untuk alat ini bentrok dengan pemesanan lain. Silakan pilih rentang lain.');
                                input.value = '';
                                try { picker.clearSelection(); } catch(e){/* ignore */ }
                                updateTotalHarga();
                            }
                        });
                    }
                });
            }
            alatSelect.addEventListener('change', setLockedDates);
            setLockedDates();
        }

        function addEntry() {
            const div = document.createElement('div');
            div.className = 'flex items-center gap-4 mb-2 alat-entry';

            const alatSelect = document.createElement('select');
            alatSelect.name = `sewa[${alatIndex}][alat]`;
            alatSelect.className = 'w-1/3 rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring focus:ring-blue-300';
            div.appendChild(alatSelect);

            const tglInput = document.createElement('input');
            tglInput.type = 'text';
            tglInput.name = `sewa[${alatIndex}][tanggal]`;
            tglInput.id = `daterange-${alatIndex}`;
            tglInput.placeholder = 'Pilih rentang tanggal sewa';
            tglInput.className = 'w-2/3 rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring focus:ring-blue-300';
            div.appendChild(tglInput);

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'remove-alat text-red-500 hover:text-red-700 text-xl leading-none font-bold';
            removeBtn.innerHTML = '&times;';
            removeBtn.addEventListener('click', () => {
                div.remove();
                updateDropdownOptions();
                updateTotalHarga();
            });
            div.appendChild(removeBtn);

            wrapper.appendChild(div);

            // isi opsi select (updateDropdownOptions akan menjaga agar tidak duplikat)
            updateDropdownOptions();

            // inisialisasi datepicker dengan referensi select (range mode)
            initLitepicker(`daterange-${alatIndex}`, tglInput, alatSelect, false);

            // ketika select berubah, update total (dan check handled by initLitepicker listener)
            alatSelect.addEventListener('change', () => {
                updateDropdownOptions();
                updateTotalHarga();
            });

            alatIndex++;
        }

        addBtn.addEventListener('click', addEntry);
        // tambahkan satu entri awal
        addEntry();

        // init single date picker untuk test_date
        (function initTestDate() {
            const input = document.getElementById('test_date');
            if (!input) return;
            new Litepicker({
                element: input,
                singleMode: true,
                format: 'YYYY-MM-DD',
                minDate: new Date()
            });
        })();
    </script>

</x-app-layout>
