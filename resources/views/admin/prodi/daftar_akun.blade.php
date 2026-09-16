@extends('admin.template')

@section('content')
@php
    $userOtoritas = auth()->user()->otoritas->otoritas ?? '';
    $univName = auth()->user()->universitas->nama ?? (optional($prodis->first())->fakultas->universitas->nama ?? 'UNIVERSITAS');
    $currentPrefix = $userOtoritas == 'Admin Universitas' ? 'admin-universitas.' : 'admin.';
@endphp

<style>
    .prodi-gate-banner {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        border-radius: 16px;
        padding: 1.5rem 2rem;
        color: #ffffff;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        position: relative;
        overflow: hidden;
    }

    .prodi-gate-banner::after {
        content: '';
        position: absolute;
        right: -30px;
        bottom: -30px;
        width: 180px;
        height: 180px;
        background: rgba(255, 255, 255, 0.04);
        border-radius: 50%;
        pointer-events: none;
    }

    .prodi-header-bar {
        background: #0284c7;
        color: #ffffff;
        padding: 12px 20px;
        border-radius: 12px 12px 0 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-weight: 700;
        letter-spacing: 0.5px;
        font-size: 0.95rem;
        box-shadow: 0 4px 12px rgba(2, 132, 199, 0.2);
    }

    .prodi-card-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(310px, 1fr));
        gap: 1.5rem;
    }

    /* APTIKOM Card Styling (Blue Theme) */
    .prodi-card-box.aptikom-card {
        background: linear-gradient(135deg, #1d4ed8 0%, #0284c7 100%) !important;
        color: #ffffff !important;
        box-shadow: 0 8px 24px rgba(2, 132, 199, 0.22) !important;
    }

    .prodi-card-box.aptikom-card:hover {
        transform: translateY(-5px) scale(1.01) !important;
        box-shadow: 0 16px 32px rgba(2, 132, 199, 0.35) !important;
    }

    /* NON-APTIKOM Card Styling (Yellow Theme) */
    .prodi-card-box.non-aptikom-card {
        background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%) !important;
        color: #ffffff !important;
        box-shadow: 0 8px 24px rgba(217, 119, 6, 0.22) !important;
    }

    .prodi-card-box.non-aptikom-card:hover {
        transform: translateY(-5px) scale(1.01) !important;
        box-shadow: 0 16px 32px rgba(217, 119, 6, 0.35) !important;
    }

    .prodi-card-box {
        border-radius: 14px !important;
        padding: 1.5rem !important;
        position: relative !important;
        overflow: hidden !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        min-height: 175px !important;
        display: flex !important;
        flex-direction: column !important;
        justify-content: space-between !important;
        text-decoration: none !important;
    }

    .prodi-card-box .bg-watermark-icon {
        position: absolute !important;
        right: 12px !important;
        bottom: 8px !important;
        font-size: 5.5rem !important;
        opacity: 0.22 !important;
        color: #ffffff !important;
        pointer-events: none !important;
        line-height: 1 !important;
    }

    .prodi-card-box .prodi-title {
        font-size: 1.25rem !important;
        font-weight: 800 !important;
        line-height: 1.3 !important;
        color: #ffffff !important;
        margin-bottom: 6px !important;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.15);
    }

    .prodi-card-box .prodi-sub {
        font-size: 0.85rem !important;
        opacity: 0.92 !important;
        color: rgba(255, 255, 255, 0.9) !important;
        margin-bottom: 12px !important;
    }

    .aptikom-badge-tag {
        display: inline-flex !important;
        align-items: center !important;
        gap: 5px !important;
        padding: 4px 12px !important;
        border-radius: 20px !important;
        font-size: 0.75rem !important;
        font-weight: 700 !important;
        letter-spacing: 0.5px !important;
        text-transform: uppercase !important;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.12) !important;
    }

    .badge-aptikom-blue {
        background: #ffffff !important;
        color: #1d4ed8 !important;
    }

    .badge-aptikom-yellow {
        background: #1e293b !important;
        color: #f59e0b !important;
    }

    .prodi-actions-row {
        margin-top: 14px;
        display: flex;
        align-items: center;
        gap: 10px;
        z-index: 2;
    }

    .btn-prodi-action {
        border-radius: 8px !important;
        font-weight: 600 !important;
        font-size: 0.82rem !important;
        padding: 6px 14px !important;
        transition: all 0.2s ease !important;
    }

    .btn-prodi-white {
        background: #ffffff !important;
        color: #1e293b !important;
        border: none !important;
    }

    .btn-prodi-white:hover {
        background: #f8fafc !important;
        transform: translateY(-1px) !important;
    }

    .btn-prodi-outline {
        background: rgba(255, 255, 255, 0.15) !important;
        color: #ffffff !important;
        border: 1px solid rgba(255, 255, 255, 0.4) !important;
        backdrop-filter: blur(4px);
    }

    .btn-prodi-outline:hover {
        background: rgba(255, 255, 255, 0.28) !important;
        color: #ffffff !important;
    }
</style>

<div class="col-12 grid-margin stretch-card">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            
            {{-- Grid of Prodi Cards --}}
            @if($prodis->isEmpty())
                <div class="text-center py-5">
                    <i class="mdi mdi-alert-circle-outline text-warning display-4"></i>
                    <h5 class="mt-3 text-secondary">Belum ada data Program Studi.</h5>
                    <p class="text-muted">Silakan tambah data prodi melalui menu Administrasi Prodi.</p>
                </div>
            @else
                <div class="prodi-card-grid">
                    @foreach($prodis as $prodi)
                        @php
                            $isAptikom = (bool) $prodi->is_aptikom;
                            $cardClass = $isAptikom ? 'aptikom-card' : 'non-aptikom-card';
                            $badgeClass = $isAptikom ? 'badge-aptikom-blue' : 'badge-aptikom-yellow';
                        @endphp
                        
                        <div class="prodi-card-box {{ $cardClass }}">
                            {{-- Mortarboard Icon Watermark --}}
                            <i class="mdi mdi-school bg-watermark-icon"></i>

                            <div>
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="aptikom-badge-tag {{ $badgeClass }}">
                                        @if($isAptikom)
                                            <i class="mdi mdi-certificate"></i> APTIKOM
                                        @else
                                            <i class="mdi mdi-domain"></i> NON-APTIKOM
                                        @endif
                                    </span>
                                    <span class="small opacity-75 fw-semibold" style="font-size: 0.78rem;">
                                        <i class="mdi mdi-account-group me-1"></i>{{ $prodi->users_count }} Akun
                                    </span>
                                </div>

                                <div class="prodi-title">{{ $prodi->nama }}</div>
                                <div class="prodi-sub">
                                    <i class="mdi mdi-domain me-1 opacity-75"></i> {{ optional($prodi->fakultas)->nama ?? 'Fakultas' }}
                                </div>
                            </div>

                            <div class="prodi-actions-row justify-content-end">
                                <a href="{{ route($currentPrefix . 'list-user', ['prodi_id' => $prodi->id]) }}" class="btn btn-prodi-action btn-prodi-white shadow-sm w-100 text-center justify-content-center">
                                    <i class="mdi mdi-account-settings me-1"></i> Kelola User
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>
</div>
@endsection
