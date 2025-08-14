<?php
/**
 * Template Name: Project Single
 * Template Post Type: project
 */
error_log('### SINGLE PROJECT TEMPLATE LOADED ###');
// if (function_exists('xdebug_break')) xdebug_break();

get_header();

// Start the WordPress loop
while (have_posts()) :
    the_post();

    // Get project meta data within the loop
    $start = get_post_meta(get_the_ID(), 'project_start_date', true);
    $end = get_post_meta(get_the_ID(), 'project_end_date', true);
    $url = get_post_meta(get_the_ID(), 'project_url', true);
    $client = get_post_meta(get_the_ID(), 'client_name', true);
    $stack = get_post_meta(get_the_ID(), 'tech_stack', true);
    $gallery = get_post_meta(get_the_ID(), 'gallery', true);
?>

<main class="project">
    <header class="project__hero">
        <h1><?php the_title(); ?></h1>
        <?php if (has_post_thumbnail()) : ?>
            <?php the_post_thumbnail('large', ['class' => 'project__cover']); ?>
        <?php endif; ?>
    </header>

    <section class="project__meta">
        <?php if ($client): ?><div><strong>Client:</strong> <?= esc_html($client) ?></div><?php endif; ?>
        <?php if ($start):  ?><div><strong>Start:</strong>  <?= esc_html($start)  ?></div><?php endif; ?>
        <?php if ($end):    ?><div><strong>End:</strong>    <?= esc_html($end)    ?></div><?php endif; ?>
        <?php if ($stack):  ?><div><strong>Stack:</strong>  <?= esc_html($stack)  ?></div><?php endif; ?>
        <?php if ($url):    ?><div><a class="btn" href="<?= esc_url($url) ?>" target="_blank" rel="noopener">Visit Project</a></div><?php endif; ?>
    </section>

    <article class="project__content">
        <?php the_content(); ?>
    </article>

    <?php if ($gallery && is_array($gallery)): ?>
    <section class="project__gallery">
        <?php foreach ($gallery as $img) :
            $src = is_array($img) ? $img['url'] : wp_get_attachment_image_url($img, 'large'); ?>
            <img src="<?= esc_url($src) ?>" alt="">
        <?php endforeach; ?>
    </section>
    <?php endif; ?>
</main>

<?php
endwhile; // End of the loop
get_footer();
