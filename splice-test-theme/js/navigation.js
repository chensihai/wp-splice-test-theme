/**
 * Handle mobile navigation menu functionality
 */
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const mobileMenu = document.querySelector('.main-navigation');
    const menuItemsWithChildren = document.querySelectorAll('.menu-item-has-children');
    
    if (mobileMenuToggle && mobileMenu) {
        // Set initial ARIA states
        mobileMenuToggle.setAttribute('aria-expanded', 'false');
        mobileMenu.setAttribute('aria-hidden', 'true');
        
        // Initialize submenu ARIA attributes
        menuItemsWithChildren.forEach(item => {
            const submenu = item.querySelector('.sub-menu');
            if (submenu) {
                submenu.setAttribute('aria-hidden', 'true');
                const link = item.querySelector('a');
                link.setAttribute('aria-expanded', 'false');
                link.setAttribute('aria-haspopup', 'true');
            }
        });
        
        // Mobile menu toggle
        mobileMenuToggle.addEventListener('click', function() {
            mobileMenu.classList.toggle('menu-active');
            mobileMenuToggle.classList.toggle('is-active');
            
            // Toggle ARIA attributes
            const isExpanded = mobileMenu.classList.contains('menu-active');
            mobileMenuToggle.setAttribute('aria-expanded', isExpanded);
            mobileMenu.setAttribute('aria-hidden', !isExpanded);
        });

        // Submenu toggle for mobile
        menuItemsWithChildren.forEach(item => {
            const link = item.querySelector('a');
            const submenu = item.querySelector('.sub-menu');
            
            if (link && submenu) {
                link.addEventListener('click', function(e) {
                    // Only handle submenu toggle on mobile
                    if (window.innerWidth <= 768) {
                        e.preventDefault();
                        item.classList.toggle('active');
                        submenu.classList.toggle('active');
                        
                        // Update ARIA attributes
                        const isExpanded = submenu.classList.contains('active');
                        link.setAttribute('aria-expanded', isExpanded);
                        submenu.setAttribute('aria-hidden', !isExpanded);
                    }
                });
            }
        });

        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            if (!mobileMenu.contains(event.target) &&
                !mobileMenuToggle.contains(event.target) &&
                mobileMenu.classList.contains('menu-active')) {
                // Close main menu
                mobileMenu.classList.remove('menu-active');
                mobileMenuToggle.classList.remove('is-active');
                mobileMenuToggle.setAttribute('aria-expanded', 'false');
                mobileMenu.setAttribute('aria-hidden', 'true');
                
                // Close all submenus
                menuItemsWithChildren.forEach(item => {
                    const submenu = item.querySelector('.sub-menu');
                    const link = item.querySelector('a');
                    if (submenu && link) {
                        item.classList.remove('active');
                        submenu.classList.remove('active');
                        link.setAttribute('aria-expanded', 'false');
                        submenu.setAttribute('aria-hidden', 'true');
                    }
                });
            }
        });

        // Handle window resize
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                if (window.innerWidth > 768) {
                    // Reset mobile menu states on desktop
                    menuItemsWithChildren.forEach(item => {
                        const submenu = item.querySelector('.sub-menu');
                        if (submenu) {
                            item.classList.remove('active');
                            submenu.classList.remove('active');
                        }
                    });
                }
            }, 250);
        });
    }
});