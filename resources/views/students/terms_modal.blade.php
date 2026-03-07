@if(!empty($showTerms) && $showTerms)

<style>
    #termsModal {
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(0,0,0,0.65);
        backdrop-filter: blur(4px);
        z-index: 10000;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Poppins', sans-serif;
        animation: fadeInOverlay 0.3s ease;
    }

    @keyframes fadeInOverlay {
        from { opacity: 0; }
        to   { opacity: 1; }
    }

    .terms-modal-box {
        background: #fff;
        width: 520px;
        max-width: 92%;
        border-radius: 20px;
        box-shadow: 0 24px 60px rgba(127,0,0,0.3);
        overflow: hidden;
        animation: slideUpModal 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    @keyframes slideUpModal {
        from { opacity: 0; transform: translateY(40px) scale(0.96); }
        to   { opacity: 1; transform: translateY(0)   scale(1);    }
    }

    .terms-modal-header {
        background: linear-gradient(135deg, #7f0000 0%, #b91c1c 50%, #dc2626 100%);
        padding: 28px 28px 24px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .terms-modal-header::before {
        content: '';
        position: absolute;
        top: -40px; right: -40px;
        width: 140px; height: 140px;
        border-radius: 50%;
        background: rgba(255,255,255,0.06);
        pointer-events: none;
    }

    .terms-modal-header::after {
        content: '';
        position: absolute;
        bottom: -30px; left: -30px;
        width: 100px; height: 100px;
        border-radius: 50%;
        background: rgba(255,255,255,0.04);
        pointer-events: none;
    }

    .terms-modal-icon {
        width: 54px; height: 54px;
        background: rgba(255,255,255,0.15);
        border: 2px solid rgba(255,255,255,0.25);
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 14px;
        position: relative; z-index: 1;
    }

    .terms-modal-icon i {
        font-size: 22px;
        color: #fca5a5;
    }

    .terms-modal-header h2 {
        font-size: 18px;
        font-weight: 800;
        color: #fff;
        margin: 0 0 5px;
        letter-spacing: -0.3px;
        position: relative; z-index: 1;
    }

    .terms-modal-header p {
        font-size: 12px;
        color: rgba(255,255,255,0.6);
        margin: 0;
        position: relative; z-index: 1;
    }

    .terms-modal-body {
        padding: 24px 26px;
    }

    .terms-scroll-box {
        max-height: 200px;
        overflow-y: auto;
        background: #fafafa;
        border: 1px solid #f0f0f0;
        border-left: 3px solid #dc2626;
        border-radius: 12px;
        padding: 16px 18px;
        margin-bottom: 20px;
        font-size: 13px;
        line-height: 1.8;
        color: #555;
        scrollbar-width: thin;
        scrollbar-color: #dc2626 #f5f5f5;
    }

    .terms-scroll-box::-webkit-scrollbar {
        width: 5px;
    }

    .terms-scroll-box::-webkit-scrollbar-track {
        background: #f5f5f5;
        border-radius: 10px;
    }

    .terms-scroll-box::-webkit-scrollbar-thumb {
        background: #dc2626;
        border-radius: 10px;
    }

    .terms-scroll-box p {
        margin-bottom: 10px;
        font-size: 13px;
        color: #555;
    }

    .terms-scroll-box p:last-child { margin-bottom: 0; }

    .terms-scroll-box strong { color: #1a1a1a; }

    .terms-highlight {
        background: #fff5f5;
        border: 1px solid #fecaca;
        border-radius: 8px;
        padding: 10px 13px;
        margin-bottom: 18px;
        font-size: 12px;
        color: #991b1b;
        display: flex;
        align-items: flex-start;
        gap: 8px;
        line-height: 1.6;
    }

    .terms-highlight i {
        color: #dc2626;
        font-size: 13px;
        margin-top: 1px;
        flex-shrink: 0;
    }

    .terms-links {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        color: #999;
        margin-bottom: 18px;
        flex-wrap: wrap;
    }

    .terms-links a {
        color: #dc2626;
        text-decoration: none;
        font-weight: 600;
        transition: color 0.2s;
    }

    .terms-links a:hover { color: #991b1b; text-decoration: underline; }
    .terms-links .sep { color: #ddd; }

    .terms-modal-footer {
        display: flex;
        gap: 10px;
    }

    .btn-terms-agree {
        flex: 1;
        padding: 13px 0;
        background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
        color: #fff;
        border: none;
        border-radius: 12px;
        font-family: 'Poppins', sans-serif;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.25s;
        box-shadow: 0 4px 14px rgba(220,38,38,0.35);
    }

    .btn-terms-agree:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(220,38,38,0.4);
    }

    .btn-terms-agree:active {
        transform: translateY(0);
    }

    .btn-terms-view {
        padding: 13px 16px;
        background: #fff;
        color: #dc2626;
        border: 1.5px solid #fecaca;
        border-radius: 12px;
        font-family: 'Poppins', sans-serif;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 7px;
        transition: all 0.2s;
        white-space: nowrap;
        text-decoration: none;
    }

    .btn-terms-view:hover {
        background: #fee2e2;
        border-color: #dc2626;
        color: #dc2626;
    }

    .terms-modal-divider {
        height: 1px;
        background: #f5f5f5;
        margin: 0 26px 20px;
    }
</style>

<div id="termsModal">
    <div class="terms-modal-box">

        <!-- Header -->
        <div class="terms-modal-header">
            <div class="terms-modal-icon">
                <i class="fa fa-shield-alt"></i>
            </div>
            <h2>Terms & Privacy Notice</h2>
            <p>Please read and accept before continuing</p>
        </div>

        <!-- Body -->
        <div class="terms-modal-body">

            <!-- Scrollable content -->
            <div class="terms-scroll-box">
                <p>By clicking <strong>"I Agree"</strong>, you consent to the collection, use, and processing of your personal data for legitimate purposes related to the InternConnect OJT Information Management System.</p>
                <p>Your information will be handled in accordance with our Privacy Statement and in full compliance with the <strong>Data Privacy Act of 2012 (Republic Act No. 10173)</strong>.</p>
                <p>Data collected includes your personal details, academic records, OJT-related documents, and system usage logs — used solely for OJT administration and University operations.</p>
                <p>You have the right to access, correct, and object to the processing of your personal data. For concerns, contact the PUP Data Privacy Officer at <strong>dataprivacy@pup.edu.ph</strong>.</p>
            </div>

            <!-- Highlight notice -->
            <div class="terms-highlight">
                <i class="fa fa-exclamation-circle"></i>
                <span>Continued use of InternConnect constitutes your acceptance of these terms. You may review the full documents anytime via the footer links.</span>
            </div>

            <!-- Full document links -->
            <div class="terms-links">
                <i class="fa fa-file-alt" style="color:#dc2626; font-size:12px;"></i>
                Read the full:
                <a href="{{ url('/terms') }}" target="_blank">Terms of Use</a>
                <span class="sep">|</span>
                <a href="{{ url('/privacy') }}" target="_blank">Privacy Statement</a>
            </div>

            <!-- Buttons -->
            <div class="terms-modal-footer">
                <a href="{{ url('/terms') }}" target="_blank" class="btn-terms-view">
                    <i class="fa fa-eye"></i> View Full
                </a>
                <button id="acceptBtn" class="btn-terms-agree">
                    <i class="fa fa-check-circle"></i> I Agree & Continue
                </button>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal    = document.getElementById('termsModal');
    const acceptBtn = document.getElementById('acceptBtn');

    acceptBtn.addEventListener('click', () => {
        // Animate out
        modal.style.transition = 'opacity 0.3s ease';
        modal.style.opacity = '0';
        setTimeout(() => { modal.style.display = 'none'; }, 300);

        // Mark as accepted
        $.post('{{ route("student.acceptTerms") }}', {
            _token: '{{ csrf_token() }}'
        });
    });
});
</script>

@endif