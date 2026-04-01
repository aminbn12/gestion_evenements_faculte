import './bootstrap';

// Sidebar toggle for mobile
document.getElementById('sidebar-toggle')?.addEventListener('click', function() {
    document.getElementById('sidebar').classList.toggle('show');
});

// Sidebar toggle button (collapse/expand)
document.getElementById('sidebar-toggle-btn')?.addEventListener('click', function() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.querySelector('.main-content');
    const navbarCustom = document.querySelector('.navbar-custom');
    
    sidebar.classList.toggle('collapsed');
    
    // Update main content and navbar margin
    if (mainContent) {
        mainContent.classList.toggle('sidebar-collapsed');
    }
    if (navbarCustom) {
        navbarCustom.classList.toggle('sidebar-collapsed');
    }
    
    // Save state to localStorage
    localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
});

// Remove preload class after page load to enable transitions
window.addEventListener('load', function() {
    document.body.classList.remove('preload');
});

// Initialize Select2 safely (it requires jQuery, which might be loaded after this module)
document.addEventListener('DOMContentLoaded', function() {
    if (typeof $ !== 'undefined' && $.fn.select2) {
        $('.select2').select2({
            theme: 'bootstrap-5'
        });
    } else {
        // Wait briefly for jQuery/Select2
        setTimeout(() => {
            if (typeof $ !== 'undefined' && $.fn.select2) {
                $('.select2').select2({
                    theme: 'bootstrap-5'
                });
            }
        }, 500);
    }
});
