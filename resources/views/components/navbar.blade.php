@php
    use App\Support\AptikomTheme;

    // --- Ambil otoritas pengguna ---
    $userOtoritas = auth()->user()->otoritas->otoritas ?? 'Guest';
    $currentPrefix = '';

    // --- Prefix route berdasarkan otoritas ---
    $penjaminMutuPrefixes = [
        'Penjamin Mutu Universitas' => 'penjamin-mutu.universitas.',
        'Penjamin Mutu Fakultas' => 'penjamin-mutu.fakultas.',
        'Penjamin Mutu Program Studi' => 'penjamin-mutu.program-studi.',
    ];

    if (array_key_exists($userOtoritas, $penjaminMutuPrefixes)) {
        $currentPrefix = $penjaminMutuPrefixes[$userOtoritas];
    } elseif ($userOtoritas && $userOtoritas !== 'Guest') {
        $currentPrefix = str_replace(' ', '-', strtolower($userOtoritas)) . '.';
    } else {
        $currentPrefix = 'admin.';
    }

    // --- Warna tema: biru (Aptikom) / kuning (Non Aptikom) berdasarkan prodi user ---
    $themeColor = AptikomTheme::resolve(auth()->user(), $themeColor ?? null);
    $navbarLogoDark = AptikomTheme::isColorDark($themeColor);
@endphp
<nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex align-items-top flex-row"
    style="background-color: {{ $themeColor }}">
    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start navbar-themed-section"
        style="background-color: {{ $themeColor }}">
        <div class="me-3">
            <button class="navbar-toggler navbar-toggler align-self-center themed-text" type="button"
                data-bs-toggle="minimize">
                <span class="icon-menu"></span>
            </button>
        </div>
        <div>
            <a class="navbar-brand brand-logo themed-text d-flex align-items-center" href="{{ route($currentPrefix . 'home') }}">
                <x-app-logo :dark="$navbarLogoDark" />
            </a>
            <span class="navbar-brand brand-logo-mini">
                <x-app-logo :dark="$navbarLogoDark" style="max-height:39px;" />
            </span>
        </div>
    </div>
    <div class="navbar-menu-wrapper d-flex align-items-top navbar-themed-section"
        style="background-color: {{ $themeColor }}">
        <ul class="navbar-nav">
            <li class="nav-item font-weight-semibold d-none d-lg-block ms-0">
                <span>
                    <h1 class="welcome-text themed-text mb-1">Selamat Datang, <span
                            class="fw-bold themed-text text-capitalize">
                            {{ auth()->user()->name }}
                            @if ($userOtoritas === 'Admin Universitas')
                                <a class="text-decoration-none" href="{{ route('admin-universitas.theme.edit') }}">
                                    <i class="typcn typcn-cog themed-text" style="font-size: 30px;"></i>
                                </a>
                            @endif
                        </span>
                    </h1>
                    <p class="themed-text" style="font-size: 0.9em; margin-top: -5px;">
                        {{ auth()->user()->otoritas->nama_otoritas
                            ? $userOtoritas . ' (' . auth()->user()->otoritas->nama_otoritas . ')'
                            : $userOtoritas }}
                    </p>
                    <h3 class="welcome-sub-text themed-text mt-0">Apa kabar hari ini?</h3>
                </span>
            </li>
        </ul>
        <ul class="navbar-nav ms-auto">
            <li class="nav-item d-none d-lg-block">
                <div id="datepicker-popup" class="input-group date datepicker navbar-date-picker">
                    <span class="input-group-addon input-group-prepend border-right">
                        <span class="icon-calendar input-group-text calendar-icon"></span>
                    </span>
                    <input style="background-color:white" disabled="disabled" type="text" class="form-control">
                </div>
            </li>
            {{-- User dropdown untuk desktop --}}
            <li class="nav-item dropdown d-none d-lg-block user-dropdown">
                <a class="nav-link" id="UserDropdown" href="#" aria-expanded="false">
                    <img class="img-xs rounded-circle" src="{{ asset('/assets/img/pp/' . auth()->user()->img) }}"
                        alt="Profile image">
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
                    <div class="dropdown-header text-center">
                        <img class="img-md rounded-circle" src="{{ asset('/assets/img/pp/' . auth()->user()->img) }}"
                            alt="Profile image" style="width:41px; height:40px;">
                        <p class="mb-1 mt-3 font-weight-semibold">{{ auth()->user()->name }}</p>
                        <p class="fw-light text-muted mb-0">{{ auth()->user()->email }}</p>
                    </div>
                    <a href="{{ route('profile') }}" class="dropdown-item"><i
                            class="dropdown-item-icon mdi mdi-account-outline text-primary me-2"></i>My Profile</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a href="{{ route('logout') }}" class="dropdown-item"
                            onclick="event.preventDefault(); this.closest('form').submit();" style="color:black">
                            <i class="dropdown-item-icon mdi mdi-power text-primary me-2"></i>
                            {{ __('Log Out') }}
                        </a>
                    </form>
                </div>
            </li>
            {{-- User dropdown untuk mobile --}}
            <li class="nav-item dropdown d-block d-lg-none user-dropdown-mobile">
                <a class="nav-link" id="UserDropdownMobile" href="#" aria-expanded="false">
                    <img class="img-xs rounded-circle" src="{{ asset('/assets/img/pp/' . auth()->user()->img) }}"
                        alt="Profile image">
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdownMobile">
                    <div class="dropdown-header text-center">
                        <img class="img-md rounded-circle" src="{{ asset('/assets/img/pp/' . auth()->user()->img) }}"
                            alt="Profile image" style="width:41px; height:40px;">
                        <p class="mb-1 mt-3 font-weight-semibold">{{ auth()->user()->name }}</p>
                        <p class="fw-light text-muted mb-0">{{ auth()->user()->email }}</p>
                    </div>
                    <a href="{{ route('profile') }}" class="dropdown-item"><i
                            class="dropdown-item-icon mdi mdi-account-outline text-primary me-2"></i>My Profile</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a href="{{ route('logout') }}" class="dropdown-item"
                            onclick="event.preventDefault(); this.closest('form').submit();" style="color:black">
                            <i class="dropdown-item-icon mdi mdi-power text-primary me-2"></i>
                            {{ __('Log Out') }}
                        </a>
                    </form>
                </div>
            </li>
        </ul>
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
            data-bs-toggle="offcanvas">
            <span class="mdi mdi-menu"></span>
        </button>
    </div>
</nav>
@push('scripts')
    <script>
        // Utility functions for color handling
        const colorUtils = {
            // Convert hex color to RGB
            hexToRgb(hex) {
                const r = parseInt(hex.slice(1, 3), 16);
                const g = parseInt(hex.slice(3, 5), 16);
                const b = parseInt(hex.slice(5, 7), 16);
                return {
                    r,
                    g,
                    b
                };
            },

            // Check if color is dark using YIQ formula
            isColorDark(hexColor) {
                const {
                    r,
                    g,
                    b
                } = this.hexToRgb(hexColor);
                const yiq = ((r * 299) + (g * 587) + (b * 114)) / 1000;
                return yiq < 128;
            }
        };

        // Function to update text colors based on background
        function updateNavbarTextColors(color) {
            const themedSections = document.querySelectorAll('.navbar-themed-section');
            const textColor = colorUtils.isColorDark(color) ? '#FFFFFF' : '#000000';

            themedSections.forEach(section => {
                const themedTexts = section.querySelectorAll('.themed-text');
                themedTexts.forEach(element => {
                    element.style.color = textColor;
                });
            });
        }

        // Convert RGB color to hex
        function rgbToHex(rgb) {
            const rgbValues = rgb.match(/\d+/g);
            const r = parseInt(rgbValues[0]);
            const g = parseInt(rgbValues[1]);
            const b = parseInt(rgbValues[2]);

            return '#' + [r, g, b].map(x => {
                const hex = x.toString(16);
                return hex.length === 1 ? '0' + hex : hex;
            }).join('');
        }

        // Function to update theme colors
        function onThemeColorChange(newColor) {
            const themedSections = document.querySelectorAll('.navbar-themed-section');
            themedSections.forEach(section => {
                section.style.backgroundColor = newColor;
            });
            updateNavbarTextColors(newColor);
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Get the current background color from the navbar
            const navbarSection = document.querySelector('.navbar-themed-section');
            if (navbarSection) {
                const backgroundColor = window.getComputedStyle(navbarSection).backgroundColor;
                const hexColor = rgbToHex(backgroundColor);
                updateNavbarTextColors(hexColor);
            }

            // Only set up color input listener if we're on the theme edit page
            const colorInput = document.getElementById('theme_color');
            if (colorInput) {
                colorInput.addEventListener('input', function(e) {
                    const selectedColor = e.target.value;
                    requestAnimationFrame(() => {
                        onThemeColorChange(selectedColor);
                    });
                });
            }

            // Fungsi reusable untuk menangani dropdown
            const setupDropdown = (triggerId) => {
                const trigger = document.getElementById(triggerId);
                if (!trigger) return; // Keluar jika elemen tidak ada

                const dropdownMenu = trigger.nextElementSibling;

                // Event saat trigger di-klik
                trigger.addEventListener('click', function(e) {
                    e.preventDefault();
                    // Toggle (tampilkan/sembunyikan) menu dropdown
                    dropdownMenu.classList.toggle('show');
                });
            };

            // Menangani event klik di luar dropdown untuk menutupnya
            document.addEventListener('click', function(e) {
                // Cari semua dropdown yang sedang aktif
                const openDropdowns = document.querySelectorAll('.dropdown-menu.show');
                openDropdowns.forEach(dropdown => {
                    // Dapatkan trigger dari dropdown menu
                    const trigger = dropdown.previousElementSibling;
                    // Tutup jika klik terjadi di luar area trigger DAN di luar area menu itu sendiri
                    if (!trigger.contains(e.target) && !dropdown.contains(e.target)) {
                        dropdown.classList.remove('show');
                    }
                });
            });

            // Terapkan fungsi pada kedua dropdown (desktop dan mobile)
            setupDropdown('UserDropdown');
            setupDropdown('UserDropdownMobile');
        });
    </script>
@endpush
