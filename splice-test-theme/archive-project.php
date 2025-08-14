<?php get_header(); ?>
<main class="projects-archive">
  <h1>Projects</h1>
  <div class="cards">
    <?php if (have_posts()): while (have_posts()): the_post();
      $client = function_exists('get_field') ? get_field('client_name') : get_post_meta(get_the_ID(),'client_name',true);
      $start  = function_exists('get_field') ? get_field('start_date')  : get_post_meta(get_the_ID(),'start_date',true);
    ?>
      <article class="card">
        <a href="<?php the_permalink(); ?>">
          <?php if (has_post_thumbnail()) the_post_thumbnail('medium'); ?>
          <h2><?php the_title(); ?></h2>
          <p class="muted">
            <?php if ($client) echo esc_html($client) . ' · '; ?>
            <?php if ($start)  echo esc_html($start); ?>
          </p>
          <p><?php echo esc_html(get_the_excerpt()); ?></p>
        </a>
      </article>
    <?php endwhile; the_posts_pagination(); else: ?>
      <p>No projects yet.</p>
    <?php endif; ?>
  </div>
</main>
<?php get_footer(); ?>
