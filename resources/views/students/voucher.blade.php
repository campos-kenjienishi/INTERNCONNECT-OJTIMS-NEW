<!DOCTYPE html>
<html lang="en">
<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Notarized MOA Submission Voucher</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ vasset('css/student/voucher.css') }}">
    <link rel="stylesheet" href="{{ vasset('css/darkmode.css') }}">
    <script src="{{ vasset('js/darkmode.js') }}"></script>
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
            <p>This is the official submission voucher confirming your uploaded Notarized MOA. You can show this digital voucher (or a screenshot) to the OJT Coordinator when submitting your physical document.</p>

            <div class="print-area">
                <div class="coupon">
                    <div class="coupon-left">
                        <img src="{{ vasset('images/final-puptg_logo-ojtims_nbg.png') }}" alt="PUP">
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
                <li><strong>Upload</strong> your notarized MOA in the system first.</li>
                <li><strong>Present</strong> this voucher code (or a screenshot on your phone) to your OJT Coordinator.</li>
                <li><strong>Submit</strong> the physical hardcopy of your Notarized MOA to the coordinator for instant verification.</li>
                <li><strong>Keep</strong> a copy or photo of your voucher code for your reference until the submission is confirmed.</li>
                <li><em>(Optional)</em> You may also print this voucher if physical attachment is requested.</li>
            </ol>

            <div class="guide-note">
                This voucher serves as verified proof of submission in the system. The OJT Coordinator can instantly look up and verify this voucher code on their dashboard.
            </div>
        </div>
    </div>
</body>
</html>