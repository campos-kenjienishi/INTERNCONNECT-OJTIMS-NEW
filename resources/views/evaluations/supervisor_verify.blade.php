<x-evaluation-shell role="public" title="Verify Evaluation Access - InternConnect" pageTitle="Verify Evaluation Access" pageSubtitle="One quick email check before completing the supervisor evaluation.">
    <div class="card-shell section-gap" style="max-width:620px;margin:0 auto;">
        <div class="card-header-shell">
            <h2><span class="header-icon"><i class="fa fa-shield-alt" aria-hidden="true"></i></span> Secure Supervisor Verification</h2>
        </div>
        <div class="card-body-shell">
            @if(session('success'))
                <div class="flash-alert success" role="status">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="flash-alert error" role="alert">{{ $errors->first() }}</div>
            @endif
            @if(session('error'))
                <div class="flash-alert error" role="alert">{{ session('error') }}</div>
            @endif
            <div class="panel-note">To protect the student's evaluation, verify access to the supervisor email recorded on this invitation. No account is required.</div>
            <p class="form-hint">Request a six-digit code, then enter it below in this browser. Codes expire within 10 minutes. Check your spam folder if the email does not appear.</p>
            <form method="POST" action="{{ route('evaluation.verification.send', ['token' => $token]) }}" class="section-gap">
                @csrf
                <button type="submit" class="btn-eval btn-eval-outline"><i class="fa fa-envelope" aria-hidden="true"></i> Send / Resend Code</button>
                <p class="form-hint">Wait at least 60 seconds between requests. Up to three emails per invitation every 10 minutes.</p>
            </form>
            <form method="POST" action="{{ route('evaluation.verification.verify', ['token' => $token]) }}" class="section-gap">
                @csrf
                <label for="supervisorVerificationCode" class="form-label-shell">Verification code</label>
                <input id="supervisorVerificationCode" name="code" class="form-control-shell" type="text" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{6}" minlength="6" maxlength="6" placeholder="Enter six-digit code" required>
                <div class="stacked-actions section-gap">
                    <button type="submit" class="btn-eval btn-eval-primary"><i class="fa fa-check-circle" aria-hidden="true"></i> Verify and Continue</button>
                </div>
                <p class="form-hint">Verification lasts up to 30 minutes in this browser. Never share your code with anyone.</p>
            </form>
        </div>
    </div>
</x-evaluation-shell>
