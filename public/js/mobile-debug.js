/**
 * Mobile View Testing & Debugging
 * Open browser console (F12) and paste the commands below to test
 */

// ========== TEST 1: Check if CSS is loaded ==========
console.log('🔍 TEST 1: Checking CSS...');
const link = document.querySelector('link[href*="dashboard-global.css"]');
if (link) {
    console.log('✅ CSS link found:', link.href);
} else {
    console.error('❌ CSS not linked! Make sure dashboard-global.css is included in <head>');
}

// ========== TEST 2: Check viewport width ==========
console.log('\n🔍 TEST 2: Checking viewport...');
console.log('📱 Window width:', window.innerWidth);
console.log('📱 Window height:', window.innerHeight);
console.log('📱 Is mobile?:', window.innerWidth <= 900);

// ========== TEST 3: Check sidebar element ==========
console.log('\n🔍 TEST 3: Checking sidebar...');
const sidebar = document.getElementById('sidebar');
if (sidebar) {
    console.log('✅ Sidebar found:', sidebar);
    console.log('   Classes:', sidebar.className);
    console.log('   Computed display:', getComputedStyle(sidebar).display);
    console.log('   Computed transform:', getComputedStyle(sidebar).transform);
} else {
    console.error('❌ Sidebar element not found!');
}

// ========== TEST 4: Check overlay ==========
console.log('\n🔍 TEST 4: Checking overlay...');
const overlay = document.getElementById('sidebarOverlay');
if (overlay) {
    console.log('✅ Overlay found:', overlay);
    console.log('   Classes:', overlay.className);
} else {
    console.error('❌ Overlay element not found!');
}

// ========== TEST 5: Check menu toggle button ==========
console.log('\n🔍 TEST 5: Checking menu toggle button...');
const menuToggle = document.getElementById('menuToggle');
if (menuToggle) {
    console.log('✅ Menu toggle found:', menuToggle);
    console.log('   Classes:', menuToggle.className);
} else {
    console.error('❌ Menu toggle button not found!');
}

// ========== TEST 6: Manually test sidebar toggle ==========
console.log('\n🔍 TEST 6: Manual toggle test...');
function testSidebarToggle() {
    if (!sidebar) {
        console.error('❌ Cannot test: sidebar not found');
        return;
    }
    
    console.log('🔄 Attempting to toggle sidebar...');
    sidebar.classList.toggle('mobile-open');
    overlay.classList.toggle('active');
    
    console.log('✅ Toggled! Sidebar classes now:', sidebar.className);
    console.log('   Overlay classes now:', overlay.className);
    console.log('   Sidebar transform:', getComputedStyle(sidebar).transform);
    console.log('   Overlay background:', getComputedStyle(overlay).backgroundColor);
    
    // Toggle back
    setTimeout(() => {
        console.log('🔄 Toggling back...');
        sidebar.classList.toggle('mobile-open');
        overlay.classList.toggle('active');
    }, 2000);
}
testSidebarToggle();

// ========== TEST 7: Check event listeners ==========
console.log('\n🔍 TEST 7: Testing menu toggle click listener...');
if (menuToggle) {
    console.log('📌 Click the hamburger menu button now...');
    menuToggle.style.border = '3px solid lime';
    console.log('   Menu button highlighted in LIME for 5 seconds');
    setTimeout(() => {
        menuToggle.style.border = '';
    }, 5000);
} else {
    console.error('❌ Cannot highlight: menu toggle not found');
}

// ========== TEST 8: Check media query ==========
console.log('\n🔍 TEST 8: Checking media queries...');
const mobileQuery = window.matchMedia('(max-width: 900px)');
console.log('📱 Mobile media query (max-width: 900px) matches:', mobileQuery.matches);

const tabletQuery = window.matchMedia('(max-width: 991px)');
console.log('📱 Tablet media query (max-width: 991px) matches:', tabletQuery.matches);

// ========== SUMMARY ==========
console.log('\n' + '='.repeat(50));
console.log('📋 TEST SUMMARY:');
console.log('='.repeat(50));

let allGood = true;

if (!link) {
    console.error('❌ CSS not linked');
    allGood = false;
}

if (!sidebar) {
    console.error('❌ Sidebar element missing');
    allGood = false;
}

if (!overlay) {
    console.error('❌ Overlay element missing');
    allGood = false;
}

if (!menuToggle) {
    console.error('❌ Menu toggle button missing');
    allGood = false;
}

if (allGood) {
    console.log('✅ All tests passed! Mobile view should be working.');
    console.log('💡 If sidebar still doesn\'t show when clicking menu:');
    console.log('   1. Try hard refresh: Ctrl+Shift+R (or Cmd+Shift+R)');
    console.log('   2. Clear browser cache');
    console.log('   3. Check Console for JavaScript errors (red messages)');
} else {
    console.log('❌ Some tests failed. Check errors above.');
}

console.log('\n💡 Pro tip: Type "testSidebarToggle()" in console to manually test sidebar');
console.log('='.repeat(50));
