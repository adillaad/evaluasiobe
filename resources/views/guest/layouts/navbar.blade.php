<style>
    .lp-nav {
        position: fixed;
        inset: 0 0 auto 0;
        z-index: 1040;
        background: rgba(255, 255, 255, 0.92);
        backdrop-filter: blur(12px);
        border-bottom: 1px solid rgba(11, 45, 92, 0.06);
        transition: box-shadow 0.25s ease, background 0.25s ease;
    }

    .lp-nav.is-scrolled {
        box-shadow: 0 8px 24px rgba(11, 45, 92, 0.08);
        background: rgba(255, 255, 255, 0.98);
    }

    .lp-nav__inner {
        width: min(1120px, calc(100% - 2rem));
        margin: 0 auto;
        min-height: 72px;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        gap: 1rem;
    }

    .lp-nav__brand {
        flex-shrink: 0;
        margin-right: auto;
    }

    .lp-nav__brand img {
        height: 34px;
        width: auto;
        max-width: 160px;
        object-fit: contain;
        filter: brightness(0) saturate(100%);
    }

    .lp-nav__right {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 1.5rem;
        margin-left: auto;
    }

    .lp-nav__links {
        display: none;
        align-items: center;
        gap: 1.75rem;
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .lp-nav__links a {
        color: #334155;
        text-decoration: none;
        font-size: 0.95rem;
        font-weight: 500;
        position: relative;
        padding: 0.25rem 0;
        transition: color 0.2s ease;
    }

    .lp-nav__links a:hover,
    .lp-nav__links a.active {
        color: var(--lp-navy);
    }

    .lp-nav__links a.active::after {
        content: "";
        position: absolute;
        left: 0;
        right: 0;
        bottom: -0.35rem;
        height: 2px;
        background: var(--lp-navy);
        border-radius: 999px;
    }

    .lp-nav__actions {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .lp-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        border-radius: 999px;
        padding: 0.65rem 1.25rem;
        font-weight: 600;
        font-size: 0.92rem;
        text-decoration: none;
        border: none;
        transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
    }

    .lp-btn:hover {
        transform: translateY(-1px);
        text-decoration: none;
    }

    .lp-btn--primary {
        background: var(--lp-navy);
        color: #fff;
        box-shadow: 0 8px 20px rgba(11, 45, 92, 0.22);
    }

    .lp-btn--primary:hover {
        background: var(--lp-navy-deep);
        color: #fff;
    }

    .lp-btn--ghost {
        background: transparent;
        color: var(--lp-navy);
        border: 1px solid rgba(11, 45, 92, 0.18);
    }

    .lp-nav__toggle {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        border: 1px solid var(--lp-line);
        border-radius: 12px;
        background: #fff;
        color: var(--lp-navy);
        cursor: pointer;
    }

    .lp-nav__mobile {
        display: none;
        flex-direction: column;
        gap: 0.25rem;
        padding: 0.75rem 1rem 1rem;
        border-top: 1px solid var(--lp-line);
        background: #fff;
    }

    .lp-nav__mobile.show {
        display: flex;
    }

    .lp-nav__mobile a {
        padding: 0.85rem 0.75rem;
        color: var(--lp-ink);
        text-decoration: none;
        border-radius: 10px;
        font-weight: 500;
    }

    .lp-nav__mobile a:hover,
    .lp-nav__mobile a.active {
        background: var(--lp-blue-soft);
        color: var(--lp-navy);
    }

    @media (min-width: 992px) {
        .lp-nav__links {
            display: flex;
        }

        .lp-nav__toggle {
            display: none;
        }

        .lp-nav__mobile {
            display: none !important;
        }
    }
</style>

<header class="lp-nav" id="lpNav">
    <div class="lp-nav__inner">
        <a class="lp-nav__brand" href="{{ route('home') }}" aria-label="Evaluasi OBE">
            <img src="{{ asset('assets/img/eval-obe-logo.png') }}" alt="Evaluasi OBE">
        </a>

        <div class="lp-nav__right">
            <ul class="lp-nav__links">
                <li><a class="{{ Route::is('home') ? 'active' : '' }}" href="{{ route('home') }}">Beranda</a></li>
                <li><a class="{{ Route::is('berita.*') ? 'active' : '' }}" href="{{ route('berita.index') }}">Berita</a></li>
                <li><a class="{{ Route::is('rps.index') ? 'active' : '' }}" href="{{ route('rps.index') }}">RPS</a></li>
                @if (Auth::check())
                    <li><a class="{{ Route::is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a></li>
                @else
                    <li><a class="{{ Route::is('register-universitas.index') ? 'active' : '' }}" href="{{ route('register-universitas.index') }}">Bergabung</a></li>
                @endif
            </ul>

            <div class="lp-nav__actions">
                @if (Auth::check())
                    <a class="lp-btn lp-btn--ghost d-none d-lg-inline-flex" href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
                @else
                    <a class="lp-btn lp-btn--primary" href="{{ route('login') }}">Login</a>
                @endif
                <button class="lp-nav__toggle" type="button" id="lpNavToggle" aria-label="Menu">
                    <i class="mdi mdi-menu" style="font-size: 1.35rem;"></i>
                </button>
            </div>
        </div>
    </div>

    <div class="lp-nav__mobile" id="lpMobileNav">
        <a class="{{ Route::is('home') ? 'active' : '' }}" href="{{ route('home') }}">Beranda</a>
        <a class="{{ Route::is('berita.*') ? 'active' : '' }}" href="{{ route('berita.index') }}">Berita</a>
        <a class="{{ Route::is('rps.index') ? 'active' : '' }}" href="{{ route('rps.index') }}">RPS</a>
        @if (Auth::check())
            <a class="{{ Route::is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
            <a href="{{ route('logout') }}"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
        @else
            <a class="{{ Route::is('register-universitas.index') ? 'active' : '' }}" href="{{ route('register-universitas.index') }}">Bergabung</a>
            <a class="{{ Route::is('login') ? 'active' : '' }}" href="{{ route('login') }}">Login</a>
        @endif
    </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const nav = document.getElementById('lpNav');
    const toggle = document.getElementById('lpNavToggle');
    const mobile = document.getElementById('lpMobileNav');

    const onScroll = () => {
        if (!nav) return;
        nav.classList.toggle('is-scrolled', window.scrollY > 8);
    };

    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });

    if (toggle && mobile) {
        toggle.addEventListener('click', () => mobile.classList.toggle('show'));
        mobile.querySelectorAll('a').forEach((link) => {
            link.addEventListener('click', () => mobile.classList.remove('show'));
        });
    }
});
</script>
