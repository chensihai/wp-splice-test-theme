<?php
/* Template Name: Home (Custom) */
/* Template Post Type: page */
get_header(); ?>

<main>
  <section class="hero">
    <h1><?php echo esc_html(get_the_title()); ?></h1>
    <?php if (has_post_thumbnail()) the_post_thumbnail('large'); ?>
  </section>

  <section class="latest-posts">
    <h2>Latest Posts</h2>
    <?php
    $q = new WP_Query(['posts_per_page' => 5]);
    if ($q->have_posts()):
      echo '<ul>';
      while ($q->have_posts()): $q->the_post(); ?>
        <li>
          <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
          <p><?php echo esc_html(get_the_excerpt()); ?></p>
        </li>
      <?php endwhile;
      echo '</ul>';
      the_posts_pagination();
      wp_reset_postdata();
    else:
      echo '<p>No posts yet.</p>';
    endif;
    ?>
  </section>
</main>

<?php get_footer(); ?>
