/**
 * Handle mobile navigation menu functionality
 */
document.addEventListener('DOMContentLoaded', function() {
    // Safely query DOM elements
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const mobileMenu = document.querySelector('.main-navigation');
    const menuItemsWithChildren = document.querySelectorAll('.menu-item-has-children');
    
    // Verify required elements exist
    if (!mobileMenuToggle || !mobileMenu) {
        console.error('Required navigation elements not found');
        return;
    }

    // Get nonce for security
    const nonce = mobileMenuToggle.getAttribute('data-nonce');
    if (!nonce) {
        console.error('Security token not found');
        return;
    }

    /**
     * Safely toggle element classes
     * @param {HTMLElement} element - The element to toggle classes on
     * @param {string[]} classNames - Array of class names to toggle
     * @returns {boolean} - Success status
     */
    function safeToggleClasses(element, classNames) {
        if (!element || !Array.isArray(classNames)) return false;
        try {
            classNames.forEach(className => {
                if (typeof className === 'string' && className.length > 0) {
                    element.classList.toggle(className);
                }
            });
            return true;
        } catch (error) {
            console.error('Error toggling classes:', error);
            return false;
        }
    }

    /**
     * Safely set ARIA attributes
     * @param {HTMLElement} element - The element to set attributes on
     * @param {Object} attributes - Key-value pairs of attributes
     */
    function safeSetAriaAttributes(element, attributes) {
        if (!element || typeof attributes !== 'object') return;
        try {
            Object.entries(attributes).forEach(([key, value]) => {
                if (typeof key === 'string' && key.startsWith('aria-')) {
                    element.setAttribute(key, value);
                }
            });
        } catch (error) {
            console.error('Error setting ARIA attributes:', error);
        }
    }
    
    // Set initial ARIA states
    safeSetAriaAttributes(mobileMenuToggle, {
        'aria-expanded': 'false'
    });
    safeSetAriaAttributes(mobileMenu, {
        'aria-hidden': 'true'
    });
    
    // Initialize submenu ARIA attributes
    menuItemsWithChildren.forEach(item => {
        const submenu = item.querySelector('.sub-menu');
        const link = item.querySelector('a');
        if (submenu && link) {
            safeSetAriaAttributes(submenu, {
                'aria-hidden': 'true'
            });
            safeSetAriaAttributes(link, {
                'aria-expanded': 'false',
                'aria-haspopup': 'true'
            });
        }
    });
        
    /**
     * Handle AJAX request for menu toggle
     * @param {number} menuId - The ID of the menu being toggled
     * @param {boolean} isActive - The new state of the menu
     * @returns {Promise} - Resolution of the AJAX request
     */
    async function handleMenuToggle(menuId, isActive) {
        try {
            const formData = new FormData();
            formData.append('action', 'splice_menu_toggle');
            formData.append('nonce', spliceNavigation.nonce);
            formData.append('menu_id', menuId);
            formData.append('is_active', isActive);

            const response = await fetch(spliceNavigation.ajaxurl, {
                method: 'POST',
                credentials: 'same-origin',
                body: formData
            });

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            const data = await response.json();
            if (!data.success) {
                throw new Error(data.data || 'Unknown error occurred');
            }

            return data;
        } catch (error) {
            console.error('Error handling menu toggle:', error);
            return false;
        }
    }

    // Mobile menu toggle
    mobileMenuToggle.addEventListener('click', async function(e) {
        e.preventDefault();
        
        const success = safeToggleClasses(mobileMenu, ['menu-active']) &&
                       safeToggleClasses(mobileMenuToggle, ['is-active']);
        
        if (success) {
            const isExpanded = mobileMenu.classList.contains('menu-active');
            const menuId = parseInt(mobileMenu.dataset.menuId || '0', 10);

            // Update ARIA attributes
            safeSetAriaAttributes(mobileMenuToggle, {
                'aria-expanded': isExpanded.toString()
            });
            safeSetAriaAttributes(mobileMenu, {
                'aria-hidden': (!isExpanded).toString()
            });

            // Send AJAX request to track menu state
            await handleMenuToggle(menuId, isExpanded);
        }
    });

    // Submenu toggle for mobile
    menuItemsWithChildren.forEach(item => {
        const link = item.querySelector('a');
        const submenu = item.querySelector('.sub-menu');
        
        if (link && submenu) {
            link.addEventListener('click', async function(e) {
                // Only handle submenu toggle on mobile
                if (window.innerWidth <= 768) {
                    e.preventDefault();
                    
                    const success = safeToggleClasses(item, ['active']) &&
                                  safeToggleClasses(submenu, ['active']);
                    
                    if (success) {
                        const isExpanded = submenu.classList.contains('active');
                        const menuId = parseInt(submenu.dataset.menuId || '0', 10);

                        safeSetAriaAttributes(link, {
                            'aria-expanded': isExpanded.toString()
                        });
                        safeSetAriaAttributes(submenu, {
                            'aria-hidden': (!isExpanded).toString()
                        });

                        // Send AJAX request to track submenu state
                        await handleMenuToggle(menuId, isExpanded);
                    }
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
            safeToggleClasses(mobileMenu, ['menu-active']);
            safeToggleClasses(mobileMenuToggle, ['is-active']);
            safeSetAriaAttributes(mobileMenuToggle, {
                'aria-expanded': 'false'
            });
            safeSetAriaAttributes(mobileMenu, {
                'aria-hidden': 'true'
            });
            
            // Close all submenus
            menuItemsWithChildren.forEach(item => {
                const submenu = item.querySelector('.sub-menu');
                const link = item.querySelector('a');
                if (submenu && link) {
                    safeToggleClasses(item, ['active']);
                    safeToggleClasses(submenu, ['active']);
                    safeSetAriaAttributes(link, {
                        'aria-expanded': 'false'
                    });
                    safeSetAriaAttributes(submenu, {
                        'aria-hidden': 'true'
                    });
                }
            });
        }
    });

    // Handle window resize with debounce
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            try {
                if (window.innerWidth > 768) {
                    // Reset mobile menu states on desktop
                    menuItemsWithChildren.forEach(item => {
                        const submenu = item.querySelector('.sub-menu');
                        const link = item.querySelector('a');
                        if (submenu && link) {
                            safeToggleClasses(item, ['active']);
                            safeToggleClasses(submenu, ['active']);
                            safeSetAriaAttributes(link, {
                                'aria-expanded': 'false'
                            });
                            safeSetAriaAttributes(submenu, {
                                'aria-hidden': 'true'
                            });
                        }
                    });
                }
            } catch (error) {
                console.error('Error handling resize:', error);
            }
        }, 250);
    });
});