/* ==========================================================================
   DEPRECATED: Forwarding to centralized public/js/darkmode.js
   ========================================================================== */
(function() {
    // If darkmode.js is already loaded, do nothing
    if (window.DarkModeManager) return;
    
    // Otherwise load public/js/darkmode.js dynamically
    var script = document.createElement('script');
    script.src = '/js/darkmode.js';
    script.async = false;
    document.head.appendChild(script);
})();
