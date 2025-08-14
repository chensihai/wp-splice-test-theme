<?php
/**
 * Footer template
 */
?>
    </main><!-- #main-content -->

    <footer class="site-footer">
        <div class="footer-content">
            <p>&copy; <?php echo esc_html(date('Y')); ?> <?php bloginfo('name'); ?></p>
            <?php wp_nav_menu(array(
                'theme_location' => 'footer',
                'menu_class'     => 'footer-menu',
                'depth'          => 1,
                'container'      => false
            )); ?>
        </div>
    </footer>

    <?php wp_footer(); ?>
</body>
</html>