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
</head>
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
<script src="{{ vasset('assets/js/voice-input.js') }}"></script>
</body>
</html>

</body>
</html>