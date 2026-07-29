<footer class="lp-footer">
    <style>
        .lp-footer {
            background: #eef2f7;
            border-top: 1px solid #e1e7f0;
            padding: 2rem 0;
            margin-top: 0;
        }

        .lp-footer__inner {
            width: min(1120px, calc(100% - 2rem));
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .lp-footer__brand {
            display: flex;
            align-items: center;
            gap: 0.85rem;
        }

        .lp-footer__brand img {
            height: 32px;
            width: auto;
            max-width: 140px;
            object-fit: contain;
            filter: brightness(0) saturate(100%);
            opacity: 0.85;
        }

        .lp-footer__brand span {
            color: #4b5870;
            font-size: 0.92rem;
            font-weight: 500;
        }

        .lp-footer__copy {
            color: #6b778c;
            font-size: 0.88rem;
            margin: 0;
        }
    </style>
    <div class="lp-footer__inner">
        <div class="lp-footer__brand">
            <img src="{{ asset('assets/img/eval-obe-logo.png') }}" alt="Evaluasi OBE">
            <span>Sistem Manajemen Mutu Perkuliahan</span>
        </div>
        <p class="lp-footer__copy">Copyright &copy; {{ date('Y') }} All rights reserved.</p>
    </div>
</footer>
</body>
</html>
