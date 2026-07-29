@extends('guest.template')
@section('content')
<style>
    .lp-hero {
        position: relative;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        padding: 7.5rem 0 4.5rem;
        background:
            linear-gradient(105deg, rgba(7, 20, 40, 0.78) 0%, rgba(7, 20, 40, 0.55) 42%, rgba(7, 20, 40, 0.28) 100%),
            url('{{ asset('assets/img/rektorat-unila.png') }}') center / cover no-repeat;
        color: #fff;
        overflow: hidden;
        text-align: left;
    }

    .lp-hero__content {
        width: min(1120px, calc(100% - 2rem));
        margin: 0 auto;
        text-align: left;
        animation: lpFadeUp 0.9s ease both;
    }

    .lp-hero h1 {
        font-size: clamp(2.2rem, 5vw, 3.6rem);
        font-weight: 800;
        line-height: 1.1;
        margin: 0 0 0.65rem;
        letter-spacing: -0.03em;
        color: #fff;
        max-width: 18ch;
    }

    .lp-hero__subtitle {
        font-size: clamp(1.05rem, 2vw, 1.4rem);
        font-weight: 600;
        color: #c5d4f5;
        margin: 0 0 1rem;
        max-width: 28ch;
    }

    .lp-hero__desc {
        max-width: 34rem;
        color: rgba(255, 255, 255, 0.9);
        font-size: 1rem;
        line-height: 1.7;
        margin: 0;
    }

    .lp-section {
        padding: 4.5rem 0;
    }

    .lp-about {
        background: #fff;
    }

    .lp-about h2 {
        font-size: clamp(1.6rem, 3vw, 2.2rem);
        font-weight: 800;
        letter-spacing: -0.02em;
        margin-bottom: 1.25rem;
        color: var(--lp-ink);
    }

    .lp-about p {
        color: var(--lp-muted);
        line-height: 1.8;
        font-size: 1.02rem;
        margin-bottom: 1rem;
        max-width: 820px;
    }

    .lp-features {
        background: var(--lp-surface);
    }

    .lp-features__grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.15rem;
    }

    .lp-feature {
        background: #fff;
        border: 1px solid var(--lp-line);
        border-radius: var(--lp-radius);
        padding: 1.5rem 1.4rem;
        box-shadow: var(--lp-shadow);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        animation: lpFadeUp 0.7s ease both;
    }

    .lp-feature:hover {
        transform: translateY(-4px);
        box-shadow: 0 18px 40px rgba(11, 45, 92, 0.12);
    }

    .lp-feature__icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: var(--lp-blue-soft);
        color: var(--lp-blue);
        font-size: 1.4rem;
        margin-bottom: 1rem;
    }

    .lp-feature h3 {
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 0.55rem;
        color: var(--lp-ink);
    }

    .lp-feature p {
        margin: 0;
        color: var(--lp-muted);
        line-height: 1.65;
        font-size: 0.95rem;
    }

    .lp-feature--rps {
        background: linear-gradient(145deg, var(--lp-navy) 0%, #16457f 100%);
        border-color: transparent;
        color: #fff;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 100%;
    }

    .lp-feature--rps .lp-feature__icon {
        background: rgba(255, 255, 255, 0.14);
        color: #fff;
    }

    .lp-feature--rps h3,
    .lp-feature--rps p {
        color: #fff;
    }

    .lp-feature--rps p {
        opacity: 0.88;
        margin-bottom: 1.4rem;
    }

    .lp-feature--rps .lp-btn {
        align-self: flex-start;
        background: #fff;
        color: var(--lp-navy);
    }

    .lp-feature--rps .lp-btn:hover {
        background: #f0f5ff;
        color: var(--lp-navy);
    }

    .lp-news__head {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 2rem;
        flex-wrap: wrap;
    }

    .lp-news__eyebrow {
        color: var(--lp-blue);
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        margin-bottom: 0.4rem;
    }

    .lp-news__head h2 {
        font-size: clamp(1.6rem, 3vw, 2.1rem);
        font-weight: 800;
        margin: 0;
        letter-spacing: -0.02em;
    }

    .lp-news__all {
        color: var(--lp-navy);
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }

    .lp-news__all:hover {
        color: var(--lp-blue);
        text-decoration: none;
    }

    .lp-news__grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.25rem;
    }

    .lp-news-card {
        display: block;
        text-decoration: none;
        color: inherit;
        background: #fff;
        border: 1px solid var(--lp-line);
        border-radius: var(--lp-radius);
        overflow: hidden;
        box-shadow: var(--lp-shadow);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        height: 100%;
    }

    .lp-news-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 18px 40px rgba(11, 45, 92, 0.12);
        text-decoration: none;
        color: inherit;
    }

    .lp-news-card__media {
        height: 180px;
        overflow: hidden;
        background: #dbe4f0;
    }

    .lp-news-card__media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.45s ease;
    }

    .lp-news-card:hover .lp-news-card__media img {
        transform: scale(1.04);
    }

    .lp-news-card__body {
        padding: 1.25rem 1.3rem 1.4rem;
    }

    .lp-news-card__meta {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        flex-wrap: wrap;
        margin-bottom: 0.7rem;
    }

    .lp-news-card__badge {
        display: inline-flex;
        padding: 0.2rem 0.55rem;
        border-radius: 999px;
        background: var(--lp-blue-soft);
        color: var(--lp-blue);
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    .lp-news-card__date {
        color: var(--lp-muted);
        font-size: 0.82rem;
    }

    .lp-news-card__title {
        font-size: 1.05rem;
        font-weight: 700;
        margin-bottom: 0.55rem;
        line-height: 1.4;
        color: var(--lp-ink);
    }

    .lp-news-card__excerpt {
        color: var(--lp-muted);
        font-size: 0.92rem;
        line-height: 1.6;
        margin-bottom: 0.9rem;
    }

    .lp-news-card__more {
        color: var(--lp-navy);
        font-weight: 600;
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
    }

    .lp-empty {
        text-align: center;
        color: var(--lp-muted);
        padding: 2rem 0;
    }

    @keyframes lpFadeUp {
        from {
            opacity: 0;
            transform: translateY(18px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (min-width: 768px) {
        .lp-features__grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .lp-feature--rps {
            grid-column: span 2;
        }

        .lp-news__grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (min-width: 992px) {
        .lp-features__grid {
            grid-template-columns: repeat(6, 1fr);
        }

        .lp-feature {
            grid-column: span 2;
        }

        .lp-feature--rps {
            grid-column: span 2;
        }

        .lp-feature:nth-child(4) {
            grid-column: span 2;
        }
    }
</style>

<section class="lp-hero">
    <div class="lp-hero__content">
        <h1>Sistem Penjamin Mutu</h1>
        <p class="lp-hero__subtitle">Evaluasi Outcome Based Education</p>
        <p class="lp-hero__desc">
            Mewujudkan standar akademik unggul melalui pengelolaan data terintegrasi,
            pemantauan kurikulum yang akurat, dan pelaporan yang transparan.
        </p>
    </div>
</section>

<section class="lp-section lp-about">
    <div class="lp-container">
        <h2>Apa Itu Sistem Penjamin Mutu?</h2>
        <p>
            Sistem Penjamin Mutu adalah platform yang dirancang untuk membantu perguruan tinggi dalam mengelola dan memastikan kualitas akademik serta
            administrasi pendidikan. Sistem ini dikembangkan oleh Jurusan Ilmu Komputer Universitas Lampung dengan tujuan untuk meningkatkan transparansi, efisiensi, dan akurasi dalam pelaporan mutu pendidikan.
        </p>
        <p>
            Dalam pengembangannya, sistem ini kini telah dibuka untuk universitas lain yang ingin bergabung dalam sistem penjaminan mutu yang terintegrasi.
            Dengan fitur multi-universitas, setiap perguruan tinggi dapat memiliki kendali penuh terhadap data dan kebijakan mutu mereka sendiri.
        </p>
    </div>
</section>

<section class="lp-section lp-features" id="features">
    <div class="lp-container">
        <div class="lp-features__grid">
            <article class="lp-feature" style="animation-delay: 0.05s;">
                <div class="lp-feature__icon"><i class="mdi mdi-account-star"></i></div>
                <h3>Profil Lulusan</h3>
                <p>
                    Merumuskan kompetensi profesional, etika, dan kontribusi lulusan beberapa tahun setelah menyelesaikan studi.
                </p>
            </article>

            <article class="lp-feature" style="animation-delay: 0.12s;">
                <div class="lp-feature__icon"><i class="mdi mdi-shield-check"></i></div>
                <h3>CPL</h3>
                <p>
                    Capaian Pembelajaran Lulusan yang spesifik, terukur, dan selaras dengan profil lulusan program studi.
                </p>
            </article>

            <article class="lp-feature" style="animation-delay: 0.19s;">
                <div class="lp-feature__icon"><i class="mdi mdi-monitor-dashboard"></i></div>
                <h3>CPMK</h3>
                <p>
                    Capaian Pembelajaran Mata Kuliah sebagai tolok ukur kegiatan pembelajaran dan penilaian di tingkat MK.
                </p>
            </article>

            <article class="lp-feature" style="animation-delay: 0.26s;">
                <div class="lp-feature__icon"><i class="mdi mdi-book-open-page-variant"></i></div>
                <h3>Bahan Kajian</h3>
                <p>
                    Pengetahuan disiplin ilmu yang dipelajari dan dapat didemonstrasikan mahasiswa melalui proses pembelajaran.
                </p>
            </article>

            <article class="lp-feature lp-feature--rps" style="animation-delay: 0.33s;">
                <div>
                    <div class="lp-feature__icon"><i class="mdi mdi-file-document-outline"></i></div>
                    <h3>Rencana Pembelajaran Semester (RPS)</h3>
                    <p>
                        Dokumen program pembelajaran yang dirancang agar mahasiswa mencapai CPL pada setiap tahapan belajar
                        di mata kuliah terkait.
                    </p>
                </div>
                <a href="{{ route('rps.index') }}" class="lp-btn">Lihat Katalog RPS</a>
            </article>
        </div>
    </div>
</section>

<section class="lp-section" id="news">
    <div class="lp-container">
        <div class="lp-news__head">
            <div>
                <div class="lp-news__eyebrow">Update Terbaru</div>
                <h2>Informasi Terkini</h2>
            </div>
            <a class="lp-news__all" href="{{ route('berita.index') }}">
                Lihat Semua Berita <i class="mdi mdi-arrow-right"></i>
            </a>
        </div>

        <div class="lp-news__grid">
            @forelse ($beritas as $berita)
                <a href="{{ route('berita.show', $berita->slug) }}" class="lp-news-card">
                    <div class="lp-news-card__media">
                        <img src="{{ asset('storage/' . $berita->gambar) }}" alt="{{ $berita->judul }}">
                    </div>
                    <div class="lp-news-card__body">
                        <div class="lp-news-card__meta">
                            <span class="lp-news-card__badge">Akademik</span>
                            <span class="lp-news-card__date">{{ $berita->created_at->format('d M Y · H:i') }}</span>
                        </div>
                        <h3 class="lp-news-card__title">{{ $berita->judul }}</h3>
                        <p class="lp-news-card__excerpt">{{ strip_tags($berita->konten) }}</p>
                        <span class="lp-news-card__more">
                            Baca Selengkapnya <i class="mdi mdi-arrow-right"></i>
                        </span>
                    </div>
                </a>
            @empty
                <p class="lp-empty">Tidak ada berita tersedia.</p>
            @endforelse
        </div>
    </div>
</section>
@endsection
