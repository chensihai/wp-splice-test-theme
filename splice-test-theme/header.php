<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<header class="site-header">
    <div class="header-content">
        <div class="site-branding">
            <?php if (has_custom_logo()) : ?>
                <?php the_custom_logo(); ?>
            <?php else : ?>
                <a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
                    <h1 class="site-title"><?php echo esc_html(get_bloginfo('name')); ?></h1>
                </a>
            <?php endif; ?>
        </div>

        <div class="responsive-nav-wrapper">
            <nav class="main-navigation nav-menu" aria-label="<?php echo esc_attr_x('Main Navigation', 'Navigation label', 'splice-test'); ?>">
                <div class="main-nav-container">
                    <?php wp_nav_menu(array(
                        'theme_location' => 'primary',
                        'menu_class'     => 'primary-menu',
                        'container'      => 'div',
                        'depth'          => 3,
                        'fallback_cb'    => 'wp_page_menu',
                    )); ?>
                </div>
            </nav>
            <button class="mobile-menu-toggle"
                    aria-label="<?php echo esc_attr_x('Toggle mobile menu', 'Mobile menu button label', 'splice-test'); ?>"
                    data-nonce="<?php echo esc_attr(wp_create_nonce('mobile_menu_toggle')); ?>">
                <span class="screen-reader-text"><?php echo esc_html_x('Menu', 'Mobile menu text', 'splice-test'); ?></span>
                <span class="hamburger-bar"></span>
                <span class="hamburger-bar"></span>
                <span class="hamburger-bar"></span>
            </button>
        </div>
    </div>
</header>

<main id="main-content">