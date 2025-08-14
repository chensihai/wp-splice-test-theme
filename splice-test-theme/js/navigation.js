/**
 * Handle mobile navigation menu functionality
 */
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const mobileMenu = document.querySelector('.main-navigation');
    
    if (mobileMenuToggle && mobileMenu) {
        // Set initial ARIA states
        mobileMenuToggle.setAttribute('aria-expanded', 'false');
        mobileMenu.setAttribute('aria-hidden', 'true');
        
        mobileMenuToggle.addEventListener('click', function() {
            mobileMenu.classList.toggle('menu-active');
            mobileMenuToggle.classList.toggle('is-active');
            
            // Toggle ARIA attributes
            const isExpanded = mobileMenu.classList.contains('menu-active');
            mobileMenuToggle.setAttribute('aria-expanded', isExpanded);
            mobileMenu.setAttribute('aria-hidden', !isExpanded);
        });

        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            if (!mobileMenu.contains(event.target) && 
                !mobileMenuToggle.contains(event.target) && 
                mobileMenu.classList.contains('menu-active')) {
                mobileMenu.classList.remove('menu-active');
                mobileMenuToggle.classList.remove('is-active');
                mobileMenuToggle.setAttribute('aria-expanded', 'false');
                mobileMenu.setAttribute('aria-hidden', 'true');
            }
        });
    }
});