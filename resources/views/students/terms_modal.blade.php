@php
    $showTerms = !Session::has('termsAccepted');
@endphp

@if($showTerms)
<style>
    #termsModalOverlay {
        position: fixed;
        top: 0; left: 0;
        width: 100vw; height: 100vh;
        background: rgba(0, 0, 0, 0.45);
        backdrop-filter: blur(3px);
        z-index: 10000;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Poppins', sans-serif;
        padding: 20px;
    }

    .terms-card {
        background: #ffffff;
        width: 520px;
        max-width: 100%;
        border-radius: 16px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.25);
        overflow: hidden;
        animation: termsPopIn 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    @keyframes termsPopIn {
        from { opacity: 0; transform: translateY(20px) scale(0.96); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }

    .terms-card-header {
        background: linear-gradient(135deg, #800000 0%, #b30000 100%);
        color: #ffffff;
        padding: 24px 28px;
        text-align: center;
    }

    .terms-card-header .logo-badge {
        width: 54px;
        height: 54px;
        object-fit: contain;
        margin-bottom: 10px;
        filter: drop-shadow(0 4px 10px rgba(0,0,0,0.25));
    }

    .terms-card-header h2 {
        font-size: 19px;
        font-weight: 700;
        margin: 0 0 4px;
        color: #ffffff;
    }

    .terms-card-header p {
        font-size: 12px;
        color: rgba(255, 255, 255, 0.85);
        margin: 0;
    }

    .terms-card-body {
        padding: 24px 28px;
        color: #475569;
        font-size: 13.5px;
        line-height: 1.65;
    }

    .terms-card-body p {
        margin-bottom: 12px;
    }

    .terms-card-body p:last-child {
        margin-bottom: 0;
    }

    .terms-brand-name {
        color: #800000;
        font-weight: 700;
    }

    .terms-scroll-area {
        max-height: 140px;
        overflow-y: auto;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 14px 16px;
        margin-top: 14px;
        font-size: 12.5px;
        color: #64748b;
    }

    .terms-checkbox-box {
        background: #fff5f5;
        border: 1.5px solid #fecaca;
        border-radius: 10px;
        padding: 14px 18px;
        margin-top: 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        user-select: none;
        transition: border-color 0.2s;
    }

    .terms-checkbox-box input[type="checkbox"] {
        width: 20px !important;
        min-width: 20px !important;
        max-width: 20px !important;
        height: 20px !important;
        accent-color: #800000 !important;
        cursor: pointer !important;
        flex-shrink: 0 !important;
        margin: 0 !important;
        padding: 0 !important;
        box-shadow: none !important;
    }

    .terms-checkbox-box span {
        font-size: 13.5px !important;
        color: #800000 !important;
        font-weight: 600 !important;
        cursor: pointer !important;
        flex: 1 1 auto !important;
        white-space: normal !important;
        word-break: break-word !important;
    }

    .terms-alert-notice {
        display: none;
        color: #dc2626;
        font-size: 12px;
        font-weight: 600;
        margin-top: 8px;
    }

    .terms-card-footer {
        padding: 16px 28px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        background: #ffffff;
    }

    .btn-terms-decline {
        background: transparent;
        color: #64748b;
        border: 1px solid #cbd5e1;
        padding: 10px 22px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 13.5px;
        text-decoration: none;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-terms-decline:hover {
        background: #f1f5f9;
        color: #334155;
    }

    .btn-terms-continue {
        background: #e2e8f0 !important;
        color: #94a3b8 !important;
        border: none !important;
        padding: 10px 26px !important;
        border-radius: 8px !important;
        font-weight: 600 !important;
        font-size: 13.5px !important;
        cursor: not-allowed !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        opacity: 0.65 !important;
        transition: background 0.2s ease, opacity 0.2s ease, color 0.2s ease, box-shadow 0.2s ease !important;
        user-select: none !important;
        min-height: unset !important;
        width: auto !important;
    }

    .btn-terms-continue.tc-unlocked {
        background: linear-gradient(135deg, #800000, #b30000) !important;
        color: #ffffff !important;
        cursor: pointer !important;
        opacity: 1 !important;
        box-shadow: 0 4px 14px rgba(128, 0, 0, 0.35) !important;
    }

    .btn-terms-continue.tc-unlocked:hover {
        background: linear-gradient(135deg, #990000, #cc0000) !important;
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(128, 0, 0, 0.45) !important;
    }

    /* Mobile Responsiveness */
    @media (max-width: 576px) {
        #termsModalOverlay {
            padding: 10px;
            align-items: center;
        }

        .terms-card {
            width: 100%;
            max-height: 92vh;
            display: flex;
            flex-direction: column;
            border-radius: 14px;
        }

        .terms-card-header {
            padding: 16px 18px;
        }

        .terms-card-header .logo-badge {
            width: 42px;
            height: 42px;
            margin-bottom: 6px;
        }

        .terms-card-header h2 {
            font-size: 16px;
        }

        .terms-card-header p {
            font-size: 11px;
        }

        .terms-card-body {
            padding: 16px 18px;
            font-size: 12.5px;
            overflow-y: auto;
        }

        .terms-scroll-area {
            max-height: 100px;
            padding: 10px 12px;
            font-size: 11.5px;
        }

        .terms-checkbox-box {
            padding: 10px 12px;
            gap: 10px;
            margin-top: 12px;
        }

        .terms-checkbox-box input[type="checkbox"] {
            width: 18px !important;
            min-width: 18px !important;
            max-width: 18px !important;
            height: 18px !important;
        }

        .terms-checkbox-box span {
            font-size: 12px !important;
            line-height: 1.35 !important;
        }

        .terms-card-footer {
            padding: 12px 18px 16px;
            gap: 8px;
            flex-direction: column-reverse;
        }

        .btn-terms-decline,
        .btn-terms-continue {
            width: 100% !important;
            padding: 10px 14px !important;
            font-size: 13px !important;
            text-align: center;
        }
    }
</style>

<div id="termsModalOverlay">
    <div class="terms-card">
        <div class="terms-card-header">
            <img src="/images/final-puptg_logo-ojtims_nbg.png" alt="InternConnect Logo" class="logo-badge">
            <h2>Terms & Privacy Notice</h2>
            <p>InternConnect — OJT Information Management System</p>
        </div>
        <div class="terms-card-body">
            <p>
                Welcome to <span class="terms-brand-name">InternConnect</span>. Before continuing to your dashboard, please review and accept our terms of service and data privacy guidelines.
            </p>

            <div class="terms-scroll-area">
                <p><strong>Data Privacy & System Usage:</strong> By using InternConnect, you consent to the processing of your academic, training, and profile information for University OJT management purposes in accordance with the Data Privacy Act of 2012.</p>
                <p>You may view the official <a href="https://www.pup.edu.ph/terms/" target="_blank" rel="noopener noreferrer" style="color:#800000; font-weight:600;">Terms of Use</a> and <a href="https://www.pup.edu.ph/privacy/" target="_blank" rel="noopener noreferrer" style="color:#800000; font-weight:600;">Privacy Statement</a> anytime.</p>
            </div>

            <div class="terms-checkbox-box" id="termsCheckboxCard">
                <input type="checkbox" id="agreeTermsCheck">
                <span>I accept the Terms of Use &amp; Privacy Statement</span>
            </div>
            <div class="terms-alert-notice" id="termsAlertNotice">
                <i class="fas fa-exclamation-circle me-1"></i> Please check the box above before continuing.
            </div>
        </div>
        <div class="terms-card-footer">
            <a href="{{ url('/logout') }}" class="btn-terms-decline">Decline &amp; Exit</a>
            <div
                id="btnTermsContinue"
                role="button"
                tabindex="0"
                class="btn-terms-continue"
                data-accept-url="{{ route('student.acceptTerms') }}"
                data-csrf="{{ csrf_token() }}"
            >
                Accept &amp; Continue
            </div>
        </div>
    </div>
</div>

<script src="{{ vasset('js/terms-modal.js') }}"></script>
@endif