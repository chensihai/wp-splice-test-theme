<?php
/* Template Name: Blog Index */
/* Template Post Type: page */
get_header(); ?>

<main>
  <h1><?php echo esc_html(get_the_title()); ?></h1>

  <?php
  // Paginated posts query
  $paged = max(1, get_query_var('paged'));
  $q = new WP_Query([
    'post_type'      => 'post',
    'posts_per_page' => 10,
    'paged'          => $paged,
  ]);

  if ($q->have_posts()):
    while ($q->have_posts()): $q->the_post(); ?>
      <article>
        <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
        <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date()); ?></time>
        <div><?php the_excerpt(); ?></div>
      </article>
    <?php endwhile;

    the_posts_pagination([
      'mid_size'  => 2,
      'prev_text' => '« Prev',
      'next_text' => 'Next »',
    ]);
    wp_reset_postdata();
  else:
    echo '<p>No posts found.</p>';
  endif;
  ?>
</main>

<?php get_footer(); ?>
