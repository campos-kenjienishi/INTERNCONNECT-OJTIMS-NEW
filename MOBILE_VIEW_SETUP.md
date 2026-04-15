# Global Mobile View Setup - InternConnect OJTIMS

## Overview
A comprehensive mobile-first responsive design system has been added to all dashboards in the InternConnect OJTIMS application. This includes global CSS utilities, JavaScript mobile handlers, and responsive breakpoints.

## Files Added/Modified

### 1. **Global Mobile CSS** 
📄 `public/css/dashboard-global.css`
- Complete mobile styling system
- Over 1000+ lines of responsive CSS utilities
- Mobile breakpoints at 768px, 992px, 1200px, 1400px
- Dark mode support for mobile
- Landscape orientation handling

### 2. **Mobile JavaScript Utilities**
📄 `public/js/mobile-utils.js`
- Sidebar toggle functionality with overlay
- Mobile modal handling
- Table optimization for small screens
- Form optimization for mobile input
- Touch gesture support (swipe to close sidebar)
- Dropdown/menu handling
- Notification system

### 3. **Updated Dashboard Files**
The following dashboard files now include the global mobile CSS and JavaScript:

#### OJT Coordinator Dashboards:
- ✅ `resources/views/ojtCoordinator/dashboard.blade.php`
- ✅ `resources/views/ojtCoordinator/companies.blade.php`

#### Professor Dashboards:
- ✅ `resources/views/professor/home.blade.php`
- ✅ `resources/views/professor/allStudents.blade.php`

#### Student Dashboards:
- ✅ `resources/views/students/student_home.blade.php`
- ✅ `resources/views/students/companiesup.blade.php`

## Features

### 📱 Responsive Layout
- **Mobile (< 768px)**: Single column layout, full-width elements, optimized touch targets
- **Tablet (768px - 991px)**: Collapsible sidebar, adaptive grids
- **Desktop (≥ 992px)**: Full layout with fixed sidebar

### 🎯 Mobile-Optimized Components
- **Sidebar drawer**: Slides from left on mobile, click overlay to close
- **Card layouts**: Stack vertically on mobile
- **Forms**: Full-width inputs optimized for touch
- **Tables**: Horizontal scrolling with wrapped content
- **Buttons**: Minimum 40px height for touch targets
- **Modal dialogs**: Slide up from bottom (iOS-style)

### 🌙 Dark Mode Support
- Fully styled for dark mode on mobile
- Variables for theme colors
- Seamless light/dark transitions

### ⌨️ Input Optimization
- Auto-disable autocorrect/autocapitalize for sensitive fields
- Number inputs with proper input modes
- Larger font sizes (16px) to prevent zoom on focus
- Proper spacing for touch interaction

### 👆 Touch & Gesture Support
- Swipe right (from left edge) to open sidebar
- Swipe left to close sidebar
- Prevent accidental zoom on double-tap
- Touch-friendly spacing and hit areas

### 🔔 Mobile Notifications
- Toast-style notifications
- Customizable duration and type (success, warning, danger, info)
- Auto-dismiss with smooth animations

## CSS Classes & Utilities

### Visibility Utilities
```html
<!-- Only show on mobile -->
<div class="mobile-only">Mobile content</div>

<!-- Only show on desktop -->
<div class="desktop-only">Desktop content</div>

<!-- Hide on mobile -->
<div class="hide-mobile">Hidden on Mobile</div>
```

### Spacing
```html
<!-- Margin Bottom -->
<div class="mb-0">No margin</div>
<div class="mb-1">4px margin</div>
<div class="mb-2">8px margin</div>
<div class="mb-3">12px margin</div>

<!-- Padding -->
<div class="p-3">12px padding</div>
```

### Typography
```html
<!-- Text sizing -->
<p class="text-sm">Small text (12px)</p>
<p class="text-lg">Large text (16px)</p>

<!-- Text utilities -->
<p class="text-center">Centered text</p>
<p class="text-muted">Muted text</p>
<p class="text-bold">Bold text</p>
```

### Layout Grid
```html
<!-- Single column on mobile -->
<div class="grid">
  <div class="grid-2">Column item</div>
  <div class="grid-2">Column item</div>
</div>
```

## JavaScript Utilities

### Sidebar Toggle
```javascript
// The sidebar automatically toggles on mobile when:
// 1. User clicks hamburger menu (.sidebar-toggle, .menu-toggle, .hamburger)
// 2. User clicks overlay
// 3. User swipes left/right
// 4. User clicks a nav item
```

### Mobile Notifications
```javascript
// Show a notification
window.showMobileNotification('Message text', 'success', 3000);

// Types: 'success', 'warning', 'danger', 'info'
// Duration: milliseconds
```

### Device Detection
```javascript
// Check device type
if (window.isMobile()) { }           // < 768px
if (window.isTablet()) { }           // 768px - 991px
if (window.isLandscape()) { }        // Landscape orientation
```

### Dark Mode Toggle
```javascript
// Toggle dark mode
window.toggleDarkMode();             // Toggle
window.toggleDarkMode(true);         // Enable
window.toggleDarkMode(false);        // Disable

// Dark mode preference is saved to localStorage
```

## Responsive Breakpoints

| Device | Width | CSS | JavaScript |
|--------|-------|-----|------------|
| Mobile | < 768px | `@media (max-width: 767px)` | `window.isMobile()` |
| Tablet | 768px - 991px | `@media (min-width: 768px) and (max-width: 991px)` | `window.isTablet()` |
| Desktop Small | 992px - 1199px | `@media (min-width: 992px)` | - |
| Desktop Large | ≥ 1200px | `@media (min-width: 1200px)` | - |

## CSS Variables

```css
:root {
    --mobile-breakpoint: 768px;
    --tablet-breakpoint: 992px;
    --lg-breakpoint: 1200px;
    --xl-breakpoint: 1400px;
    
    /* Color scheme */
    --red: #dc2626;
    --red-dark: #991b1b;
    --red-deeper: #7f0000;
    
    /* Layout */
    --sidebar-w: 260px;
    --sidebar-w-collapsed: 70px;
    --topbar-h: 64px;
}
```

## Mobile Sidebar Implementation

The sidebar on mobile works as follows:

1. **Hidden by default**: `transform: translateX(-100%)`
2. **Hamburger toggle**: Clicking the hamburger menu transforms to `translateX(0)`
3. **Overlay**: Semi-transparent overlay appears behind sidebar
4. **Click to close**: Clicking overlay closes sidebar
5. **Swipe support**: Swipe left closes, swipe right from edge opens
6. **Auto-close on item click**: Nav item clicks close the sidebar

## Form Optimization

Mobile forms automatically:
- Disable autocorrect and spell-check for regular text inputs
- Use appropriate input modes (numeric for numbers, email for email, etc.)
- Set minimum font size to 16px to prevent zoom on focus
- Add proper touch-friendly spacing
- Enable horizontal scrolling for table inputs

## Testing on Mobile

### Browser DevTools
1. Open Chrome/Firefox DevTools (F12)
2. Click "Toggle device toolbar" (Ctrl+Shift+M)
3. Select different device presets
4. Test sidebar toggle, form inputs, and responsive layouts

### Real Device Testing
```bash
# Connect to local server
# On PC: ipconfig getifaddr en0 (Mac/Linux) or ipconfig (Windows)
# On phone: Open http://<your-ip>:8000
```

### Common Breakpoints to Test
- iPhone 12/13: 390x844
- iPhone SE: 375x667
- iPad: 768x1024
- Galaxy S21: 360x800
- Pixel 6: 412x915

## Best Practices

### 1. Sidebar Interactions
```html
<!-- Add class data attributes for auto-detection -->
<button class="sidebar-toggle">
    <i class="fas fa-bars"></i>
</button>

<!-- Or use data attribute -->
<button data-toggle="sidebar">Menu</button>
```

### 2. Mobile Forms
```html
<!-- Use proper input types -->
<input type="email" placeholder="Email">
<input type="tel" placeholder="Phone">
<input type="number" inputmode="numeric" placeholder="Amount">
```

### 3. Touch Targets
```css
/* Mobile touch targets should be at least 44x44px */
button {
    min-height: 44px;
    padding: 12px 16px;
}
```

### 4. Performance
- Minimize use of fixed positioning on mobile
- Use `will-change` sparingly
- Optimize images for mobile (different sizes)
- Lazy load images below the fold

## Troubleshooting

### Sidebar not responding to clicks
- Ensure `.sidebar-toggle` class or `[data-toggle="sidebar"]` exists
- Check if `.sidebar` element is present
- Verify `mobile-utils.js` is loaded

### Dark mode not persisting
- Clear browser cache/storage
- Check localStorage: Open DevTools > Application > LocalStorage
- Ensure `dark-mode.js` is loaded after `mobile-utils.js`

### Forms not optimized on mobile
- Verify correct input types used (`type="email"`, `type="tel"`, etc.)
- Check `inputmode` attributes (`inputmode="numeric"`, etc.)
- Ensure minimum font size is 16px

### Sidebar overlay not showing
- Check `.sidebar-overlay` element exists
- Verify CSS media query is active (DevTools, check computed styles)
- Ensure z-index is high enough (default 1999)

## Future Enhancements

Potential improvements to consider:
1. Progressive Web App (PWA) features
2. Offline support with Service Workers
3. Native app wrapper (Capacitor/Cordova)
4. Bottom navigation bar for iOS-style navigation
5. Haptic feedback for touch interactions
6. Pull-to-refresh for lists
7. Swipe-to-delete for list items

## Support

For questions or issues with the mobile view implementation:
1. Check browser console for JavaScript errors
2. Verify all files are properly linked
3. Test in private/incognito mode to rule out cache issues
4. Check DevTools network tab for failed resources
5. Test on real device if possible

---

**Last Updated**: April 14, 2026
**Version**: 1.0
