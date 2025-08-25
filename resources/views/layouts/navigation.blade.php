<nav x-data="{ open: false }" class="bg-[#F0F8FF] dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <img src="{{ asset('images/logobanyumas.png') }}" alt="Logo Banyumas" class="block h-15 w-14" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    @auth
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>

                        @if (Auth::user()->role === 'admin')
                            <x-nav-link href="{{ url('/admin/alat-sewa-types') }}" :active="request()->is('admin/alat-sewa-types*')">
                                {{ __('Kelola Alat Sewa') }}
                            </x-nav-link>
                            <x-nav-link href="{{ url('/admin/uji-types') }}" :active="request()->is('admin/uji-types*')">
                                {{ __('Kelola Jenis Uji') }}
                            </x-nav-link>
                        @else
                            <x-nav-link href="{{ url('/user/order') }}" :active="request()->is('user/order*')">
                                {{ __('History Pemesanan') }}
                            </x-nav-link>
                        @endif
                    @endauth

                    @guest
                        <div class="flex items-center space-x-2">
                            <div class="text-gray-700 dark:text-gray-300 font-semibold">
                                Peminjaman & Pengujian
                                <span class="text-sm font-normal text-gray-500 dark:text-gray-400">— Sistem Terintegrasi</span>
                            </div>
                        </div>
                    @endguest
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <x-theme-toggle />

                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <!-- Compact trigger -->
                            <button
                                class="inline-flex items-center px-2 py-1 border border-transparent text-sm leading-5 font-medium rounded-md
                                text-gray-500 dark:text-gray-400 bg-[#F0F8FF] dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300
                                focus:outline-none transition ease-in-out duration-150">
                                <div class="mr-2 text-sm">{{ Auth::user()->name }}</div>
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414
                                        1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <!-- Compact list: semua link pakai kelas sama, tanpa spacer besar -->
                            <div class="py-1">
                                @if (Auth::user()->role === 'admin')
                                    <x-dropdown-link href="{{ url('/admin/alat-sewa-types') }}" class="px-3 py-1 text-sm leading-tight">
                                        {{ __('Kelola Alat Sewa') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link href="{{ url('/admin/uji-types') }}" class="px-3 py-1 text-sm leading-tight">
                                        {{ __('Kelola Jenis Uji') }}
                                    </x-dropdown-link>
                                @else
                                    <x-dropdown-link href="{{ url('/user/order') }}" class="px-3 py-1 text-sm leading-tight">
                                        {{ __('History Pemesanan') }}
                                    </x-dropdown-link>
                                @endif

                                {{-- Profile / API Tokens (jika ada) --}}
                                @if (Route::has('profile.show') || Route::has('profile.edit') || Route::has('profile'))
                                    @if(Route::has('profile.show'))
                                        <x-dropdown-link :href="route('profile.show')" class="px-3 py-1 text-sm leading-tight">
                                            {{ __('Profile') }}
                                        </x-dropdown-link>
                                    @elseif(Route::has('profile.edit'))
                                        <x-dropdown-link :href="route('profile.edit')" class="px-3 py-1 text-sm leading-tight">
                                            {{ __('Edit Profile') }}
                                        </x-dropdown-link>
                                    @endif
                                @endif

                                @if (Route::has('api-tokens.index'))
                                    <x-dropdown-link :href="route('api-tokens.index')" class="px-3 py-1 text-sm leading-tight">
                                        {{ __('API Tokens') }}
                                    </x-dropdown-link>
                                @endif

                                <!-- small divider then logout (still compact) -->
                                <div class="border-t border-gray-100 dark:border-gray-700 mt-1 pt-1">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <x-dropdown-link :href="route('logout')" class="px-3 py-1 text-sm leading-tight"
                                            onclick="event.preventDefault(); this.closest('form').submit();">
                                            {{ __('Log Out') }}
                                        </x-dropdown-link>
                                    </form>
                                </div>
                            </div>
                        </x-slot>
                    </x-dropdown>
                @endauth

                @guest
                    <div class="space-x-4">
                        <a href="{{ route('login') }}" class="text-sm text-gray-700 dark:text-gray-300 hover:underline">
                            {{ __('Login') }}
                        </a>
                        @if(Route::has('register'))
                            <a href="{{ route('register') }}" class="text-sm text-gray-700 dark:text-gray-300 hover:underline">
                                {{ __('Register') }}
                            </a>
                        @endif
                    </div>
                @endguest
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500
                    dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100
                    dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu (compact) -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-0.5">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="py-1">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            @auth
                @if (Auth::user()->role === 'admin')
                    <x-responsive-nav-link href="{{ url('/admin/alat-sewa-types') }}" :active="request()->is('admin/alat-sewa-types*')" class="py-1">
                        {{ __('Kelola Alat Sewa') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ url('/admin/uji-types') }}" :active="request()->is('admin/uji-types*')" class="py-1">
                        {{ __('Kelola Jenis Uji') }}
                    </x-responsive-nav-link>
                @else
                    <x-responsive-nav-link href="{{ url('/user/order') }}" :active="request()->is('user/order*')" class="py-1">
                        {{ __('History Pemesanan') }}
                    </x-responsive-nav-link>
                @endif
            @endauth

            @guest
                <x-responsive-nav-link href="{{ route('login') }}" class="py-1">
                    {{ __('Login') }}
                </x-responsive-nav-link>
            @endguest
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-3 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-3">
                @auth
                    <div class="font-medium text-base text-gray-800 dark:text-gray-200">
                        {{ Auth::user()->name }}
                    </div>
                    <div class="font-medium text-sm text-gray-500">
                        {{ Auth::user()->email }}
                    </div>
                @endauth

                @guest
                    <div class="font-medium text-base text-gray-800 dark:text-gray-200">
                        Guest
                    </div>
                    <div class="font-medium text-sm text-gray-500">
                        Silakan login untuk melihat detail
                    </div>
                @endguest
            </div>

            <div class="px-3 mt-2">
                <x-theme-toggle />
            </div>

            <div class="mt-2 space-y-0.5 px-3">
                @if (Route::has('profile.show') || Route::has('profile.edit') || Route::has('profile'))
                    @if (Route::has('profile.show'))
                        <x-responsive-nav-link :href="route('profile.show')" class="py-1">
                            {{ __('Profile') }}
                        </x-responsive-nav-link>
                    @elseif(Route::has('profile.edit'))
                        <x-responsive-nav-link :href="route('profile.edit')" class="py-1">
                            {{ __('Edit Profile') }}
                        </x-responsive-nav-link>
                    @endif
                @endif

                @if (Route::has('api-tokens.index'))
                    <x-responsive-nav-link :href="route('api-tokens.index')" class="py-1">
                        {{ __('API Tokens') }}
                    </x-responsive-nav-link>
                @endif

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" class="py-1" onclick="event.preventDefault();
                                                this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
