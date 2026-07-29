@once
    <style>
        .badge-aptikom {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.35rem 0.85rem;
            border-radius: 999px;
            font-size: 0.82rem;
            font-weight: 600;
            line-height: 1.2;
            border: 1px solid transparent;
            white-space: nowrap;
        }

        .badge-aptikom--yes {
            background-color: #e3f2fd;
            color: #0d47a1;
            border-color: #64b5f6;
        }

        .badge-aptikom--no {
            background-color: #fff9c4;
            color: #f57f17;
            border-color: #ffeb3b;
        }

        .aptikom-picker {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 0.75rem;
        }

        .aptikom-picker__option {
            position: relative;
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
            padding: 0.9rem 1rem;
            border: 2px solid #dee2e6;
            border-radius: 0.75rem;
            cursor: pointer;
            transition: all 0.2s ease;
            background: #fff;
            margin-bottom: 0;
        }

        .aptikom-picker__option input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .aptikom-picker__option small {
            color: #6c757d;
            font-size: 0.78rem;
            line-height: 1.35;
        }

        .aptikom-picker__option--yes:hover,
        .aptikom-picker__option--yes.is-selected {
            border-color: #42a5f5;
            background: #e3f2fd;
            box-shadow: 0 0 0 0.15rem rgba(33, 150, 243, 0.15);
        }

        .aptikom-picker__option--no:hover,
        .aptikom-picker__option--no.is-selected {
            border-color: #ffeb3b;
            background: #fffde7;
            box-shadow: 0 0 0 0.15rem rgba(255, 235, 59, 0.25);
        }
    </style>
@endonce
