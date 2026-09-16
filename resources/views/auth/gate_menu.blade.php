<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Evaluasi OBE</title>
    <link rel="shortcut icon" href="{{ asset('/assets/img/eval-obe-logo.png') }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('/assets/template/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/template/vendors/css/vendor.bundle.base.css') }}">
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif !important;
        }

        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            min-height: 100vh;
            background-color: #0f172a;
            background-image: linear-gradient(rgba(15, 23, 42, 0.55), rgba(15, 23, 42, 0.55)), url('{{ asset("assets/img/rektorat-unila-baru.jpg") }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .gate-wrapper {
            width: 100%;
            max-width: 960px;
            margin: 30px 20px;
            z-index: 10;
        }

        .gate-container {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .gate-header-bar {
            background-color: #0052cc;
            background: linear-gradient(90deg, #0040a8 0%, #0052cc 100%);
            color: #ffffff;
            padding: 18px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .gate-header-title {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .gate-header-nav {
            display: flex;
            align-items: center;
            gap: 16px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .gate-header-nav button {
            color: #ffffff;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 0;
            font-size: 0.85rem;
            font-weight: 500;
            transition: opacity 0.2s ease;
        }

        .gate-header-nav button:hover {
            opacity: 0.8;
        }

        .gate-body {
            padding: 32px 36px 44px 36px;
            min-height: 380px;
        }

        .gate-page-title {
            margin-bottom: 24px;
        }

        .gate-page-title h2 {
            font-size: 1.35rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0 0 4px 0;
            display: inline-block;
        }

        .gate-page-title span {
            font-size: 0.88rem;
            color: #64748b;
            margin-left: 10px;
            font-weight: 400;
        }

        .prodi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 18px;
        }

        .prodi-card-form {
            margin: 0;
            padding: 0;
            height: 100%;
        }

        .prodi-card {
            border-radius: 8px;
            padding: 16px 20px;
            height: 125px;
            min-height: 125px;
            position: relative;
            cursor: pointer;
            transition: transform 0.18s ease, box-shadow 0.18s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border: none;
            width: 100%;
            text-align: left;
        }

        .prodi-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        }

        /* APTIKOM Card (SOLID ROYAL BLUE) */
        .prodi-card.aptikom-blue {
            background-color: #0052cc;
            background: linear-gradient(135deg, #0052cc 0%, #0040a8 100%);
            border: 1px solid #0040a8;
            color: #ffffff;
        }

        /* NON-APTIKOM Card (SOLID AMBER GOLD) */
        .prodi-card.non-aptikom-yellow {
            background-color: #d97706;
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
            border: 1px solid #b45309;
            color: #ffffff;
        }

        .prodi-card-header {
            z-index: 2;
        }

        .prodi-card-title {
            font-size: 1.05rem;
            font-weight: 700;
            margin: 0 0 4px 0;
            line-height: 1.3;
            color: #ffffff;
        }

        .prodi-card-sub {
            font-size: 0.78rem;
            margin: 0;
            color: rgba(255, 255, 255, 0.92);
            line-height: 1.3;
            display: flex;
            align-items: flex-start;
            gap: 4px;
        }

        .prodi-card-footer {
            z-index: 2;
            margin-top: 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .badge-aptikom-tag {
            font-size: 0.68rem;
            font-weight: 700;
            padding: 3px 9px;
            border-radius: 5px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            display: inline-flex;
            align-items: center;
            gap: 3px;
        }

        .tag-blue {
            background: rgba(255, 255, 255, 0.22);
            color: #ffffff;
        }

        .tag-yellow {
            background: rgba(0, 0, 0, 0.2);
            color: #ffffff;
        }

        .btn-select-modul {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 4px 11px;
            background: #ffffff;
            border-radius: 5px;
            display: inline-flex;
            align-items: center;
            gap: 3px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: background 0.15s ease;
        }

        .aptikom-blue .btn-select-modul {
            color: #0040a8;
        }

        .non-aptikom-yellow .btn-select-modul {
            color: #b45309;
        }

        .btn-select-modul:hover {
            background: #f8fafc;
        }

        @media (max-width: 768px) {
            .gate-header-bar {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }
            .gate-body {
                padding: 20px;
            }
            .prodi-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

@php
    $authUser = auth()->user();
    $univName = 'Universitas Lampung';
    if ($authUser && $authUser->universitas) {
        $univName = $authUser->universitas->nama;
    }
@endphp

<div class="gate-wrapper">
    <div class="gate-container">
        
        {{-- Header Bar --}}
        <div class="gate-header-bar">
            <div class="gate-header-title">
                <img src="{{ asset('assets/img/eval-obe-logo.png') }}" alt="Logo" style="height: 48px; width: auto; filter: brightness(0) invert(1);" onerror="this.style.display='none'">
            </div>
            <div class="gate-header-nav">
                <form action="{{ route('logout') }}" method="POST" class="d-inline m-0 p-0">
                    @csrf
                    <button type="submit" style="color: #ffffff; background: transparent; border: none; font-size: 0.9rem; font-weight: 500; cursor: pointer;">
                        <i class="mdi mdi-logout me-1"></i> Logout
                    </button>
                </form>
            </div>
        </div>

        {{-- Body Content --}}
        <div class="gate-body">
            <div class="gate-page-title">
                <h2>Daftar Prodi</h2>
                <span>Silakan pilih prodi di bawah ini</span>
            </div>

            @if($prodis->isEmpty())
                <div class="text-center py-5">
                    <i class="mdi mdi-information-outline text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-2">Tidak ada daftar Program Studi yang terhubung dengan akun Anda.</p>
                </div>
            @else
                <div class="prodi-grid">
                    @foreach($prodis as $prodi)
                        @php
                            $isAptikom = (bool) $prodi->is_aptikom;
                            $cardColorClass = $isAptikom ? 'aptikom-blue' : 'non-aptikom-yellow';
                            $tagClass = $isAptikom ? 'tag-blue' : 'tag-yellow';
                        @endphp
                        
                        <form action="{{ route('profile.switch-prodi') }}" method="POST" class="prodi-card-form">
                            @csrf
                            <input type="hidden" name="prodi_id" value="{{ $prodi->id }}">
                            <button type="submit" class="prodi-card {{ $cardColorClass }}">
                                <div class="prodi-card-header">
                                    <div class="prodi-card-title">{{ $prodi->nama }}</div>
                                    <div class="prodi-card-sub">
                                        <i class="mdi mdi-office-building mt-0.5"></i> {{ optional($prodi->fakultas)->nama ?? 'Fakultas' }}
                                    </div>
                                </div>

                                <div class="prodi-card-footer">
                                    <span class="badge-aptikom-tag {{ $tagClass }}">
                                        @if($isAptikom)
                                            <i class="mdi mdi-certificate"></i> APTIKOM
                                        @else
                                            <i class="mdi mdi-domain"></i> NON-APTIKOM
                                        @endif
                                    </span>
                                    <span class="btn-select-modul">
                                        Pilih Prodi <i class="mdi mdi-chevron-right"></i>
                                    </span>
                                </div>
                            </button>
                        </form>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</div>

</body>
</html>
