<?php
/**
 * Template Name: Project Single
 * Template Post Type: project
 */
get_header();

while (have_posts()) : the_post(); ?>
    <article id="project-<?php the_ID(); ?>">
        <h1><?php the_title(); ?></h1>
        
        <dl class="project-details">
            <dt><?php esc_html_e('Start Date:', 'splice-test'); ?></dt>
            <dd><?php echo esc_html(get_post_meta(get_the_ID(), 'project_start_date', true)); ?></dd>
            
            <dt><?php esc_html_e('End Date:', 'splice-test'); ?></dt>
            <dd><?php echo esc_html(get_post_meta(get_the_ID(), 'project_end_date', true)); ?></dd>
            
            <dt><?php esc_html_e('Project URL:', 'splice-test'); ?></dt>
            <dd>
                <a href="<?php echo esc_url(get_post_meta(get_the_ID(), 'project_url', true)); ?>" target="_blank">
                    <?php echo esc_html(get_post_meta(get_the_ID(), 'project_url', true)); ?>
                </a>
            </dd>
        </dl>
        
        <div class="project-content">
            <?php the_content(); ?>
        </div>
    </article>
<?php endwhile;

get_footer();