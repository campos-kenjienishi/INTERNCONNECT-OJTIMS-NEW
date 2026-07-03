<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Notarized MOA Submission Voucher</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --red-dark: #7f1d1d;
            --red: #991b1b;
            --gold-a: #fff6bf;
            --gold-b: #d89b1f;
            --ink: #1f2937;
            --muted: #6b7280;
            --line: #ead7d7;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 24px;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(180deg, #fff8f8 0%, #ffffff 100%);
            color: var(--ink);
        }

        .voucher-shell {
            max-width: 980px;
            margin: 0 auto;
            display: grid;
            gap: 20px;
        }

        .toolbar {
            display: flex;
            justify-content: flex-end;
        }

        .print-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
            border-radius: 999px;
            padding: 11px 18px;
            background: linear-gradient(135deg, #b91c1c, #7f1d1d);
            color: #fff;
            font-family: 'Poppins', sans-serif;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 10px 24px rgba(185, 28, 28, 0.18);
        }

        .page-card,
        .guide-card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 24px;
            box-shadow: 0 20px 50px rgba(127, 29, 29, 0.08);
        }

        .page-card {
            padding: 28px 30px;
        }

        .page-card h1 {
            margin: 0 0 10px;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.4px;
            color: var(--red-dark);
        }

        .page-card > p {
            margin: 0;
            font-size: 13px;
            color: var(--muted);
            line-height: 1.7;
        }

        .print-area {
            display: flex;
            justify-content: center;
            padding-top: 16px;
        }

        .coupon {
            width: 620px;
            height: 250px;
            display: grid;
            grid-template-columns: 28% 72%;
            border-radius: 4px;
            overflow: hidden;
            box-shadow: 0 16px 34px rgba(0, 0, 0, 0.14);
        }

        .coupon-left {
            position: relative;
            background: linear-gradient(180deg, #9d0606 0%, #8a0404 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .coupon-left::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                linear-gradient(90deg,
                    transparent 0 10%,
                    rgba(248, 215, 129, 0.9) 10% 10.8%,
                    transparent 10.8% 14.8%,
                    rgba(248, 215, 129, 0.9) 14.8% 15.6%,
                    transparent 15.6% 19.6%,
                    rgba(248, 215, 129, 0.9) 19.6% 20.4%,
                    transparent 20.4%);
        }

        .coupon-left img {
            position: relative;
            z-index: 1;
            width: 92px;
            height: 92px;
            object-fit: contain;
            filter: drop-shadow(0 6px 10px rgba(0,0,0,0.18));
        }

        .coupon-right {
            position: relative;
            background:
                radial-gradient(circle at left center, rgba(255, 255, 255, 0.85), transparent 34%),
                linear-gradient(90deg, var(--gold-a) 0%, #f2d16e 44%, var(--gold-b) 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 26px 28px;
        }

        .coupon-right::after {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(255,255,255,0.18) 0.8px, transparent 0.8px);
            background-size: 6px 6px;
            opacity: 0.22;
            pointer-events: none;
        }

        .coupon-copy {
            position: relative;
            z-index: 1;
            display: grid;
            justify-items: center;
            gap: 8px;
            width: 100%;
        }

        .coupon-small {
            font-size: 11px;
            color: #6f4b00;
            line-height: 1.45;
        }

        .coupon-brand {
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: #8a1010;
        }

        .coupon-title {
            font-size: 18px;
            font-weight: 800;
            line-height: 1.15;
            color: #8a0404;
            max-width: 300px;
        }

        .coupon-code {
            font-size: 40px;
            font-weight: 800;
            line-height: 1;
            letter-spacing: 1px;
            color: #8a0404;
            white-space: nowrap;
            max-width: 100%;
        }

        .guide-card {
            padding: 22px 24px;
            display: grid;
            gap: 14px;
        }

        .guide-card h2 {
            margin: 0;
            font-size: 17px;
            font-weight: 800;
        }

        .guide-card ol {
            margin: 0;
            padding-left: 20px;
            display: grid;
            gap: 10px;
            font-size: 13px;
            line-height: 1.7;
            color: #374151;
        }

        .guide-card strong {
            color: var(--red-dark);
        }

        .guide-note {
            padding: 14px 16px;
            border-radius: 14px;
            background: #fff6f6;
            border: 1px solid rgba(185, 28, 28, 0.12);
            font-size: 12.5px;
            color: #4b5563;
            line-height: 1.7;
        }

        @media (max-width: 640px) {
            body {
                padding: 14px;
            }

            .page-card,
            .guide-card {
                padding: 18px;
            }

            .coupon {
                width: 100%;
                max-width: 620px;
                height: 220px;
                grid-template-columns: 28% 72%;
            }

            .coupon-left img {
                width: 74px;
                height: 74px;
            }

            .coupon-title {
                font-size: 15px;
            }

            .coupon-code {
                font-size: 30px;
                letter-spacing: 0.5px;
            }
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }

            .toolbar,
            .page-card > h1,
            .page-card > p,
            .guide-card {
                display: none !important;
            }

            .page-card {
                border: none;
                box-shadow: none;
                border-radius: 0;
                padding: 0;
            }

            .print-area {
                padding: 0;
                margin: 0;
            }

            .coupon {
                width: 620px;
                height: 250px;
                margin: 0 auto;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    @php
        $voucher = $company->vouchers->sortByDesc('created_at')->first();
        $voucherCode = $voucher->filename ?? 'N/A';
    @endphp

    <div class="voucher-shell">
        <div class="toolbar">
            <button type="button" class="print-btn" onclick="window.print()">
                <i class="fa fa-print"></i> Print Voucher
            </button>
        </div>

        <div class="page-card">
            <h1>Notarized MOA Submission Voucher</h1>
            <p>This is the printable voucher template students should attach to the hardcopy notarized MOA when submitting to the coordinator.</p>

            <div class="print-area">
                <div class="coupon">
                    <div class="coupon-left">
                        <img src="/images/final-puptg_logo-ojtims_nbg.png" alt="PUP">
                    </div>
                    <div class="coupon-right">
                        <div class="coupon-copy">
                            <div class="coupon-small">Thank you for uploading! Here is your code:</div>
                            <div class="coupon-brand">InternConnect OJT IMS</div>
                            <div class="coupon-title">Notarized MOA Submission Voucher</div>
                            <div class="coupon-code">{{ $voucherCode }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="guide-card">
            <h2>What To Do With The Voucher</h2>
            <ol>
                <li><strong>Upload</strong> the notarized MOA in the system first.</li>
                <li><strong>Print</strong> the voucher using the template above.</li>
                <li><strong>Staple</strong> the printed voucher to the notarized MOA hardcopy, or bring it together with the document.</li>
                <li><strong>Submit</strong> the hardcopy notarized MOA to the coordinator.</li>
                <li><strong>Keep</strong> a copy or photo of the voucher for your own reference until the submission is confirmed.</li>
            </ol>

            <div class="guide-note">
                This voucher serves as a proof or certificate of submission in the system. The coordinator can use it to match the physical notarized MOA with the uploaded record.
            </div>
        </div>
    </div>
<script src="{{ asset('assets/js/voice-input.js') }}"></script>
</body>
</html>
