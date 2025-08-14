<?php
/**
 * Projects Archive Template
 */
get_header(); ?>

<header class="page-header">
    <h1 class="page-title"><?php post_type_archive_title(); ?></h1>
</header>

<?php while (have_posts()) : the_post(); ?>
    <article <?php post_class(); ?>>
        <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
        <div class="project-meta">
            <p><?php esc_html_e('Duration:', 'splice-test'); ?> 
                <?php echo esc_html(get_post_meta(get_the_ID(), 'project_start_date', true)); ?> -
                <?php echo esc_html(get_post_meta(get_the_ID(), 'project_end_date', true)); ?>
            </p>
        </div>
    </article>
<?php endwhile;

get_footer();