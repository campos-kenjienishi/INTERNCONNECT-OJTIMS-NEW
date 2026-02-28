@if(!empty($showTerms) && $showTerms)
<div id="termsModal" style="position:fixed; top:0; left:0; width:100%; height:100%; 
    background-color: rgba(0,0,0,0.5); z-index:10000; display:flex; align-items:center; justify-content:center;">
    
  <div style="background:white; width:500px; max-width:90%; border-radius:15px; 
              box-shadow:0 8px 25px rgba(0,0,0,0.3); font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; overflow:hidden;">
    
    <!-- Topbar / Title -->
    <div style="background:#8B0000; color:#FFD700; padding:18px; text-align:center; font-size:18px; font-weight:bold; letter-spacing:0.5px;">
        Terms and Conditions
    </div>

    <!-- Modal Content -->
    <div style="padding:25px;">
        <div style="max-height:220px; overflow-y:auto; border:1px solid #ddd; padding:15px; margin-bottom:20px; font-size:14px; line-height:1.6; color:#333; border-radius:8px; background:#fdfdfd;">
            <p>By clicking <strong>"I Agree"</strong>, you consent to the collection, use, and processing of your personal data for legitimate purposes related to this service.</p><br>
            <p>Your information will be handled in accordance with our Privacy Policy and in compliance with the Data Privacy Act of 2012.</p>
        </div>

        <button id="acceptBtn" style="width:100%; padding:14px 0; background:#8B0000; color:#FFD700; border:none; border-radius:8px; font-size:16px; cursor:pointer; font-weight:bold; transition: background 0.2s;">
            I Agree
        </button>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('termsModal');
    const acceptBtn = document.getElementById('acceptBtn');

    acceptBtn.addEventListener('click', () => {
        // Hide the modal
        modal.style.display = 'none';

        // Mark as accepted for this session
        $.post('{{ route("student.acceptTerms") }}', {
            _token: '{{ csrf_token() }}'
        });
    });
});
</script>
@endif