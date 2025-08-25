<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Form Pemesanan Layanan Pengujian
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
        // Data harga uji (contoh: { "1": 250000, "2": 350000, ... })
        const ujiHarga = @json($ujis);
        const ujiList = Object.keys(ujiHarga);

        let ujiIndex = 0;
        const ujiWrapper = document.getElementById('uji-wrapper');
        const addUjiBtn = document.getElementById('add-uji');
        const totalHargaDisplay = document.getElementById('total-harga');
        const amountInput = document.getElementById('amount');

        function updateUjiDropdownOptions() {
            const selects = ujiWrapper.querySelectorAll('select');
            const selected = Array.from(selects).map(s => s.value);

            selects.forEach(select => {
                const currentValue = select.value;
                select.innerHTML = '<option value="">-- Pilih Jenis Uji --</option>';

                ujiList.forEach(uji => {
                    const alreadySelected = selected.includes(uji) && uji !== currentValue;
                    if (!alreadySelected) {
                        const option = document.createElement('option');
                        option.value = uji;
                        option.textContent = `${uji} - Rp ${Number(ujiHarga[uji]).toLocaleString('id-ID')}`;
                        if (uji === currentValue) option.selected = true;
                        select.appendChild(option);
                    }
                });
            });

            addUjiBtn.disabled = ujiWrapper.querySelectorAll('select').length >= ujiList.length;
            addUjiBtn.textContent = addUjiBtn.disabled ? 'Semua Jenis Uji Telah Dipilih' : 'Tambah Jenis Uji';
        }

        function updateTotalHarga() {
            let total = 0;
            ujiWrapper.querySelectorAll('select').forEach(select => {
                const uji = select.value;
                if (uji && ujiHarga[uji]) total += Number(ujiHarga[uji]);
            });
            totalHargaDisplay.textContent = `Rp ${total.toLocaleString('id-ID')}`;
            amountInput.value = total;
        }

        function addUjiEntry() {
            const div = document.createElement('div');
            div.className = 'flex items-center gap-3 p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600';

            const selectWrapper = document.createElement('div');
            selectWrapper.className = 'flex-1';

            const select = document.createElement('select');
            select.name = `ujis[${ujiIndex}]`;
            select.className = 'w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring focus:ring-blue-300';
            select.addEventListener('change', () => {
                updateUjiDropdownOptions();
                updateTotalHarga();
            });

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'flex-shrink-0 w-10 h-10 flex items-center justify-center text-red-500 hover:text-white hover:bg-red-500 border border-red-300 hover:border-red-500 rounded-lg transition duration-200';
            removeBtn.innerHTML = 'X';
            removeBtn.title = 'Hapus jenis uji ini';
            removeBtn.addEventListener('click', () => {
                div.remove();
                updateUjiDropdownOptions();
                updateTotalHarga();
            });

            selectWrapper.appendChild(select);
            div.appendChild(selectWrapper);
            div.appendChild(removeBtn);
            ujiWrapper.appendChild(div);

            updateUjiDropdownOptions();
            updateTotalHarga();

            ujiIndex++;
        }

        addUjiBtn.addEventListener('click', addUjiEntry);
        addUjiEntry(); // default 1 input

        // NOTE: Litepicker init removed because user no longer sets test_date here.

        // Validasi sebelum submit (tidak lagi memaksa test_date)
        document.getElementById('order-uji-form').addEventListener('submit', function(e) {
            // Pastikan minimal 1 uji dipilih dan valid
            const selectedTests = Array.from(ujiWrapper.querySelectorAll('select')).filter(s => s.value);
            if (selectedTests.length === 0) {
                alert('Pilih minimal 1 jenis uji!');
                e.preventDefault();
                return false;
            }

            updateTotalHarga();

            // Konfirmasi sebelum submit
            const total = totalHargaDisplay.textContent;
            if (!confirm(`Konfirmasi pesanan dengan total biaya ${total}?`)) {
                e.preventDefault();
                return false;
            }
        });
    </script>
</x-app-layout>
