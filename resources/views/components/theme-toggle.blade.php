<div x-data="{ isDark: false,
                init() {
                    try {
                    const theme = localStorage.getItem('theme');
                    const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                    this.isDark = (theme === 'dark' || (theme === null && prefersDark));
                    document.documentElement.classList.toggle('dark', this.isDark);
                    } catch (e) {}
                },
                toggle() {
                    this.isDark = !this.isDark;
                    document.documentElement.classList.toggle('dark', this.isDark);
                    localStorage.setItem('theme', this.isDark ? 'dark' : 'light');
                } }"
        x-init="init()"
        class="inline-flex items-center"
    >
    <button @click="toggle()"
            aria-label="Toggle theme"
            class="p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition"
    >
        <!-- moon icon when light (means klik -> dark) -->
        <template x-if="!isDark">
        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" />
        </svg>
        </template>

        <!-- sun icon when dark (means klik -> light) -->
        <template x-if="isDark">
        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 3v1M12 20v1M4.2 4.2l.7.7M18.1 18.1l.7.7M1 12h1M22 12h1M4.2 19.8l.7-.7M18.1 5.9l.7-.7" />
        </svg>
        </template>
    </button>
</div>
