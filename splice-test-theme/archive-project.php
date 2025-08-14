<?php get_header(); ?>
<main class="projects-archive">
  <h1>Projects</h1>
  
  <!-- Project Filters -->
  <div class="project-filters">
    <form id="project-filter-form" class="filter-form" method="get">
      <?php wp_nonce_field('project_filter_nonce', 'project_filter_nonce'); ?>
      <div class="date-filters">
        <div class="filter-group">
          <label for="start_date">Start Date:</label>
          <input type="date" id="start_date" name="start_date"
                 value="<?php echo esc_attr(isset($_GET['start_date']) ? $_GET['start_date'] : ''); ?>">
        </div>
        <div class="filter-group">
          <label for="end_date">End Date:</label>
          <input type="date" id="end_date" name="end_date"
                 value="<?php echo esc_attr(isset($_GET['end_date']) ? $_GET['end_date'] : ''); ?>">
        </div>
        <button type="submit" class="filter-submit">Apply Filters</button>
        <button type="reset" class="filter-reset">Reset</button>
      </div>
    </form>
  </div>

  <div class="cards">
    <?php
    // Enqueue project filters script
    wp_enqueue_script('project-filters', get_template_directory_uri() . '/js/project-filters.js', array(), '1.0', true);

    // Add loading animation styles
    wp_add_inline_style('splice-test-theme-style', '
      .filtering { opacity: 0.5; pointer-events: none; }
      .filtering * { cursor: wait !important; }
    ');

    // Modify query based on date filters
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    $args = array(
      'post_type' => 'project',
      'posts_per_page' => 12,
      'paged' => $paged,
      'meta_query' => array()
    );

    // Format dates for query
    $filter_start = !empty($_GET['start_date']) ? sanitize_text_field($_GET['start_date']) : '';
    $filter_end = !empty($_GET['end_date']) ? sanitize_text_field($_GET['end_date']) : '';

    // Add date filters if set
    // Build meta query for date filtering
    if ($filter_start && $filter_end) {
      // A project overlaps with the date range if:
      // Project's start date is before or equal to filter end date AND
      // Project's end date is after or equal to filter start date
      $args['meta_query'] = array(
        'relation' => 'AND',
        array(
          'key' => 'project_start_date',
          'value' => $filter_end,
          'compare' => '<=',
          'type' => 'DATE'
        ),
        array(
          'key' => 'project_end_date',
          'value' => $filter_start,
          'compare' => '>=',
          'type' => 'DATE'
        )
      );
    } elseif ($filter_start) {
      // Show projects that end on or after the start date
      $args['meta_query'] = array(
        array(
          'key' => 'project_end_date',
          'value' => $filter_start,
          'compare' => '>=',
          'type' => 'DATE'
        )
      );
    } elseif ($filter_end) {
      // Show projects that start on or before the end date
      $args['meta_query'] = array(
        array(
          'key' => 'project_start_date',
          'value' => $filter_end,
          'compare' => '<=',
          'type' => 'DATE'
        )
      );
    }
    
    // Debug output
    if (isset($_GET['debug'])) {
      echo '<div style="background: #f5f5f5; padding: 10px; margin: 10px 0; font-family: monospace;">';
      echo '<strong>Filter Parameters:</strong><br>';
      echo 'Start Date: ' . esc_html($filter_start) . '<br>';
      echo 'End Date: ' . esc_html($filter_end) . '<br>';
      echo '<strong>Query Arguments:</strong><br>';
      echo '<pre>' . print_r($args, true) . '</pre>';
      echo '</div>';
    }

    // Debug output
    if (isset($_GET['debug'])) {
      echo '<div style="background: #f5f5f5; padding: 10px; margin: 10px 0; font-family: monospace;">';
      echo '<strong>DEBUG OUTPUT</strong><br>';
      
      // Show all projects and their dates first
      echo '<strong>All Projects:</strong><br>';
      $all_projects = get_posts(array('post_type' => 'project', 'posts_per_page' => -1));
      foreach ($all_projects as $project) {
        $start = get_post_meta($project->ID, 'project_start_date', true);
        $end = get_post_meta($project->ID, 'project_end_date', true);
        echo sprintf(
          "Project: %s (ID: %d)<br>Start: %s<br>End: %s<br><br>",
          esc_html($project->post_title),
          $project->ID,
          esc_html($start),
          esc_html($end)
        );
      }
      
      echo '<strong>Filter Parameters:</strong><br>';
      echo 'Start Date: ' . esc_html($filter_start) . '<br>';
      echo 'End Date: ' . esc_html($filter_end) . '<br>';
      echo '<strong>Query Arguments:</strong><br>';
      echo '<pre>' . print_r($args, true) . '</pre>';
      
      $query = new WP_Query($args);
      
      echo '<strong>SQL Query:</strong><br>';
      echo '<pre>' . esc_html($query->request) . '</pre>';
      echo '<strong>Found Posts:</strong> ' . $query->found_posts . '<br>';
      echo '</div>';
    } else {
      $query = new WP_Query($args);
    }

    if ($query->have_posts()): ?>
      <div class="project-grid">
        <?php while ($query->have_posts()): $query->the_post();
          // Get post meta
          $client = get_post_meta(get_the_ID(), 'client_name', true);
          $start_date = get_post_meta(get_the_ID(), 'project_start_date', true);
          $end_date = get_post_meta(get_the_ID(), 'project_end_date', true);
          
          // Debug output - show all meta data
          if (isset($_GET['debug'])) {
            echo '<div style="background: #f5f5f5; padding: 10px; margin: 10px 0; font-family: monospace;">';
            echo '<strong>Post ID:</strong> ' . get_the_ID() . '<br>';
            echo '<strong>All Meta Data:</strong><br>';
            $meta = get_post_meta(get_the_ID());
            foreach ($meta as $key => $values) {
              foreach ($values as $value) {
                echo esc_html($key) . ': ' . esc_html($value) . '<br>';
              }
            }
            echo '</div>';
          }
          
          // Format dates for display (if they exist)
          $formatted_start = $start_date ? date('Y-m-d', strtotime($start_date)) : '';
          $formatted_end = $end_date ? date('Y-m-d', strtotime($end_date)) : '';
          
          // Format dates for display in the meta section
          $display_start = $start_date ? date('F j, Y', strtotime($start_date)) : '';
          $display_end = $end_date ? date('F j, Y', strtotime($end_date)) : '';
        ?>
          <article class="project-card" data-start="<?php echo esc_attr($formatted_start); ?>" data-end="<?php echo esc_attr($formatted_end); ?>">
            <a href="<?php the_permalink(); ?>" class="project-link">
              <div class="project-image">
                <?php if (has_post_thumbnail()): ?>
                  <?php the_post_thumbnail('medium', array('class' => 'project-thumbnail')); ?>
                <?php else: ?>
                  <div class="project-thumbnail-placeholder"></div>
                <?php endif; ?>
              </div>
              <div class="project-content">
                <h2 class="project-title"><?php the_title(); ?></h2>
                <?php if ($client || $start_date): ?>
                  <p class="project-meta">
                    <?php if ($client): ?>
                      <span class="project-client"><?php echo esc_html($client); ?></span>
                    <?php endif; ?>
                    <?php if ($client && $start_date): echo ' · '; endif; ?>
                    <?php if ($start_date): ?>
                      <span class="project-date">
                        <?php echo esc_html($display_start); ?>
                        <?php if ($display_end && $display_end !== $display_start): ?>
                          - <?php echo esc_html($display_end); ?>
                        <?php endif; ?>
                      </span>
                    <?php endif; ?>
                  </p>
                <?php endif; ?>
                <div class="project-excerpt"><?php the_excerpt(); ?></div>
              </div>
            </a>
          </article>
    <?php
      endwhile;
      // Reset post data
      wp_reset_postdata();
      
      // Custom pagination
      echo '<div class="pagination">';
      echo paginate_links(array(
        'total' => $query->max_num_pages,
        'current' => $paged,
        'prev_text' => '&laquo; Previous',
        'next_text' => 'Next &raquo;'
      ));
      echo '</div>';
    else: ?>
      <p>No projects found matching your criteria.</p>
    <?php endif; ?>
  </div>
</main>

<style>
/* Project Grid Layout */
.project-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 2rem;
  margin: 2rem 0;
}

/* Project Card Styles */
.project-card {
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  overflow: hidden;
}

.project-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.project-link {
  text-decoration: none;
  color: inherit;
  display: block;
}

.project-image {
  position: relative;
  padding-top: 56.25%; /* 16:9 aspect ratio */
  background: #f8f9fa;
}

.project-thumbnail {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.project-thumbnail-placeholder {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: #e9ecef;
}

.project-content {
  padding: 1.5rem;
}

.project-title {
  margin: 0 0 0.5rem;
  font-size: 1.25rem;
  color: #212529;
  line-height: 1.4;
}

.project-meta {
  font-size: 0.875rem;
  color: #6c757d;
  margin: 0 0 1rem;
}

.project-excerpt {
  font-size: 0.9375rem;
  color: #495057;
  line-height: 1.6;
  margin: 0;
}

/* Filter Styles */
.project-filters {
  background: white;
  padding: 1.5rem;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  margin-bottom: 2rem;
}

.filter-form {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  align-items: flex-end;
}

.filter-group {
  flex: 1;
  min-width: 200px;
}

.filter-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: #212529;
}

.filter-group input[type="date"] {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #dee2e6;
  border-radius: 4px;
  font-size: 1rem;
}

.filter-submit,
.filter-reset {
  padding: 0.5rem 1.5rem;
  border: none;
  border-radius: 4px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

.filter-submit {
  background: #0d6efd;
  color: white;
}

.filter-submit:hover {
  background: #0b5ed7;
}

.filter-reset {
  background: #6c757d;
  color: white;
}

.filter-reset:hover {
  background: #5c636a;
}

/* Pagination Styles */
.pagination {
  margin-top: 2rem;
  display: flex;
  justify-content: center;
  gap: 0.5rem;
}

.pagination a,
.pagination span {
  padding: 0.5rem 1rem;
  border: 1px solid #dee2e6;
  border-radius: 4px;
  color: #0d6efd;
  text-decoration: none;
  transition: all 0.2s ease;
}

.pagination a:hover {
  background: #e9ecef;
}

.pagination .current {
  background: #0d6efd;
  color: white;
  border-color: #0d6efd;
}

/* Responsive Design */
@media (max-width: 768px) {
  .project-grid {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }

  .filter-form {
    flex-direction: column;
  }

  .filter-group {
    width: 100%;
  }

  .filter-submit,
  .filter-reset {
    width: 100%;
  }
}
</style>
<?php get_footer(); ?>

