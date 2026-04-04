<!-- Voice Input Guide Modal -->
<div id="voiceInputGuideModal" class="modal" tabindex="-1" role="dialog" aria-labelledby="voiceInputGuideLabel" aria-hidden="true" style="display:none;">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="voiceInputGuideLabel">
          <i class="fa fa-microphone-alt"></i> Voice Input Guide
        </h5>
        <button type="button" class="close" aria-label="Close" onclick="document.getElementById('voiceInputGuideModal').style.display='none'">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <ul style="line-height:1.7;">
          <li><b>Say field content directly</b> (e.g., "Juan Dela Cruz")</li>
          <li><b>Symbols:</b> "at sign" → @, "slash" → /, "underscore" → _</li>
          <li><b>Spacing (email/username):</b> Say "spacebar" to insert a space</li>
          <li><b>Commands:</b></li>
          <ul>
            <li>"backspace" – delete last character</li>
            <li>"delete word" – delete last word</li>
            <li>"clear field" – clear all text</li>
            <li>"new line" – new line (textarea only)</li>
            <li>"select all" – select all text</li>
            <li>"stop listening" – stop voice input</li>
          </ul>
          <li><b>Suffixes:</b> "junior" → Jr, "the third" → III, etc.</li>
        </ul>
      </div>
    </div>
  </div>
</div>

<!-- Voice Input Guide Button (circle icon) -->
<style>
.voice-guide-btn {
  position: fixed;
  bottom: 110px;
  right: 32px;
  z-index: 1200;
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background: #fff;
  color: #7f0000;
  box-shadow: 0 2px 8px rgba(0,0,0,0.13);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 22px;
  cursor: pointer;
  border: 2px solid #7f0000;
  transition: background 0.2s, color 0.2s;
}
.voice-guide-btn:hover {
  background: #7f0000;
  color: #fff;
}
@media (max-width: 600px) {
  .voice-guide-btn { right: 12px; bottom: 90px; }
}
</style>
<div class="voice-guide-btn" title="Voice Input Guide" onclick="document.getElementById('voiceInputGuideModal').style.display='block'">
  <i class="fa fa-microphone-alt"></i>
</div>
<script>
// Simple modal close on outside click
window.addEventListener('click', function(e) {
  var modal = document.getElementById('voiceInputGuideModal');
  if (modal && e.target === modal) {
    modal.style.display = 'none';
  }
});
</script>
