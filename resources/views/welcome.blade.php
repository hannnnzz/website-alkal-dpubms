<x-app-layout>
    <div class="max-w-7xl mx-auto px-6 py-16">
        <!-- Hero -->
        <section class="grid grid-cols-1 md:grid-cols-2 gap-10 items-center">
            <div>
                <h1 class="text-4xl md:text-5xl font-extrabold leading-tight text-gray-900 dark:text-white">Sistem Peminjaman Alat & Pengujian</h1>
                <p class="mt-4 text-gray-600 dark:text-gray-300 text-lg">Sewa alat, booking pengujian, unggah dokumen, dan bayar dengan QRIS — semua tersentralisasi untuk operasional yang lebih cepat dan transparan.</p>

                <div class="mt-6 flex gap-3 flex-wrap">
                    <a href="{{ route('user.order.createuji') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-lg bg-indigo-600 text-white shadow hover:opacity-95">Pesan Sekarang</a>
                    <a href="{{ route('user.order.createsewa') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-lg border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200">Lihat Daftar Alat</a>
                </div>

                <div class="mt-6 text-sm text-gray-500 dark:text-gray-400">
                    Butuh bantuan?
                    <a href="https://wa.me/6281234567890" target="_blank" class="underline">
                        hubungi admin
                    </a>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-md border dark:border-gray-700 transition-colors">
                <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-3 border rounded-lg bg-gray-50 dark:bg-gray-900 border-gray-100 dark:border-gray-700">
                        <div class="text-xs text-gray-500 dark:text-gray-400">Transaksi</div>
                        <div class="text-lg font-medium text-gray-800 dark:text-gray-100">QRIS & Midtrans</div>
                    </div>
                    <div class="p-3 border rounded-lg bg-gray-50 dark:bg-gray-900 border-gray-100 dark:border-gray-700">
                        <div class="text-xs text-gray-500 dark:text-gray-400">Dokumen</div>
                        <div class="text-lg font-medium text-gray-800 dark:text-gray-100">Upload & Verifikasi</div>
                    </div>
                    <div class="p-3 border rounded-lg bg-gray-50 dark:bg-gray-900 border-gray-100 dark:border-gray-700">
                        <div class="text-xs text-gray-500 dark:text-gray-400">Otomasi</div>
                        <div class="text-lg font-medium text-gray-800 dark:text-gray-100">Perhitungan Harga Otomatis</div>
                    </div>
                    <div class="p-3 border rounded-lg bg-gray-50 dark:bg-gray-900 border-gray-100 dark:border-gray-700">
                        <div class="text-xs text-gray-500 dark:text-gray-400">Riwayat</div>
                        <div class="text-lg font-medium text-gray-800 dark:text-gray-100">Tracking Pesanan</div>
                    </div>
                </div>

                <div class="mt-6 text-xs text-gray-500 dark:text-gray-400">  </div>
            </div>
        </section>

        <!-- Features -->
        <section class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow border dark:border-gray-700 transition-colors">
                <h3 class="font-semibold text-gray-900 dark:text-gray-100">Pemesanan Mudah</h3>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Form pemesanan yang sama baik untuk pengujian maupun sewa alat, termasuk upload file dan input detail pekerjaan.</p>
            </div>

            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow border dark:border-gray-700 transition-colors">
                <h3 class="font-semibold text-gray-900 dark:text-gray-100">Pembayaran Terintegrasi</h3>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Generate QRIS sekali, simpan gambar QR, dan verifikasi pembayaran melalui Midtrans.</p>
            </div>

            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow border dark:border-gray-700 transition-colors">
                <h3 class="font-semibold text-gray-900 dark:text-gray-100">Manajemen & Dashboard</h3>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Panel admin untuk mengontrol pesananan dan melihat riwayat lengkap.</p>
            </div>
        </section>
    </div>

    <footer class="border-t dark:border-gray-800 py-6 mt-12 bg-white dark:bg-gray-900 transition-colors">
        <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="text-sm text-gray-600 dark:text-gray-400">
                © {{ date('Y') }} Sistem Peminjaman & Pengujian. Made & Reserved by
                <a href="https://github.com/hannnnzz/" target="_blank" class="underline">
                    DivaDhys
                </a>.
            </div>
        </div>
    </footer>

    <!-- Theme script (toggle only <html> class) -->
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const storageKey = 'theme';
        const className = 'dark';
        const themeToggle = document.getElementById('theme-toggle');
        const iconLight = document.getElementById('theme-toggle-light-icon');
        const iconDark = document.getElementById('theme-toggle-dark-icon');
        const metaThemeColor = document.getElementById('meta-theme-color');

        function setIcons(theme) {
            if (!iconLight || !iconDark) return;
            if (theme === 'dark') {
                iconDark.classList.remove('hidden');
                iconLight.classList.add('hidden');
            } else {
                iconDark.classList.add('hidden');
                iconLight.classList.remove('hidden');
            }
        }

        function applyTheme(theme) {
            document.documentElement.classList.toggle(className, theme === 'dark');
            if (metaThemeColor) metaThemeColor.setAttribute('content', theme === 'dark' ? '#0f172a' : '#ffffff');
            setIcons(theme);
        }

        try {
            const saved = localStorage.getItem(storageKey);
            if (saved === 'dark' || saved === 'light') {
                applyTheme(saved);
            } else {
                applyTheme('light');
            }
        } catch (e) {
            applyTheme('light');
        }

        if (themeToggle) {
            themeToggle.addEventListener('click', () => {
                const isDark = document.documentElement.classList.contains(className);
                const newTheme = isDark ? 'light' : 'dark';
                try { localStorage.setItem(storageKey, newTheme); } catch (e) {}
                applyTheme(newTheme);
            });
        }

        window.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'j') {
                e.preventDefault();
                themeToggle && themeToggle.click();
            }
        });
    });
    </script>
</x-app-layout>
