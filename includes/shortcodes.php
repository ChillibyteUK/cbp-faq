<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Ensure assets only enqueue when shortcodes are used.
 */
function faq_blocks_maybe_enqueue_assets() {
	static $done = false;
	if ( $done ) return;
	$done = true;

	// Reuse the plugin's existing enqueue functions if they exist.
	if ( function_exists( 'faq_blocks_add_inline_styles' ) ) {
		faq_blocks_add_inline_styles();
	}
	if ( function_exists( 'faq_blocks_enqueue_scripts' ) ) {
		faq_blocks_enqueue_scripts();
	}
}

/**
 * Open FAQPage schema wrapper once per page (shared with your existing footer closer).
 */
function faq_blocks_maybe_open_schema_wrapper( $schema_enabled = true ) {
	if ( ! $schema_enabled ) return;

	global $faq_blocks_instance_count;
	if ( ! isset( $faq_blocks_instance_count ) ) {
		$faq_blocks_instance_count = 0;
	}

	$faq_blocks_instance_count++;
	if ( $faq_blocks_instance_count === 1 ) {
		echo '<div class="faq-page-wrapper" itemscope itemtype="https://schema.org/FAQPage">';
	}
}

/**
 * Shortcode: [faq_category id="223" title="1" schema="1" class="" anchor=""]
 */
add_shortcode( 'faq_category', function( $atts ) {

	$atts = shortcode_atts( [
		'id'     => 0,      // term_id
		'title'  => 0,      // show category title
		'schema' => 1,      // wrap in FAQPage schema (first instance only)
		'class'  => '',
		'anchor' => '',
		'orderby'=> 'title',
		'order'  => 'ASC',
	], $atts, 'faq_category' );

	$term_id = absint( $atts['id'] );
	if ( ! $term_id ) return '';

	faq_blocks_maybe_enqueue_assets();

	$schema_enabled = (string) $atts['schema'] === '1' || $atts['schema'] === 1 || $atts['schema'] === true;

	ob_start();

	// open schema wrapper (only first instance)
	faq_blocks_maybe_open_schema_wrapper( $schema_enabled );

	$term = get_term( $term_id, 'faq_category' );
	if ( ! $term || is_wp_error( $term ) ) {
		return '';
	}

	$args = [
		'post_type'      => 'faq',
		'posts_per_page' => -1,
		'tax_query'      => [
			[
				'taxonomy' => 'faq_category',
				'field'    => 'term_id',
				'terms'    => $term_id,
			],
		],
		'orderby'        => sanitize_key( $atts['orderby'] ),
		'order'          => strtoupper( $atts['order'] ) === 'DESC' ? 'DESC' : 'ASC',
	];

	$q = new WP_Query( $args );
	if ( ! $q->have_posts() ) {
		wp_reset_postdata();
		return '';
	}

	$block_id = $atts['anchor'] ? sanitize_title( $atts['anchor'] ) : 'faq-list-' . $term_id . '-' . wp_rand( 1000, 9999 );
	$custom_class = sanitize_html_class( $atts['class'] );

	?>
	<div id="<?php echo esc_attr( $block_id ); ?>" class="faq-list-block <?php echo esc_attr( $custom_class ); ?>">
		<?php if ( (int) $atts['title'] === 1 ) : ?>
			<h2 class="faq-list-title"><?php echo esc_html( $term->name ); ?> FAQs</h2>
		<?php endif; ?>

		<?php while ( $q->have_posts() ) : $q->the_post(); ?>
			<?php
			$faq_excerpt = function_exists('get_field') ? get_field( 'faq_excerpt', get_the_ID() ) : '';
			if ( empty( $faq_excerpt ) ) {
				$faq_excerpt = get_the_content();
			}
			?>
			<div class="faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
				<h3 class="faq-question" itemprop="name">
					<a href="<?php echo esc_url( get_permalink() ); ?>"><?php the_title(); ?></a>
				</h3>
				<div class="faq-answer" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
					<div itemprop="text"><?php echo wp_kses_post( $faq_excerpt ); ?></div>
				</div>
			</div>
		<?php endwhile; ?>
	</div>
	<?php

	wp_reset_postdata();
	return ob_get_clean();
} );

/**
 * Shortcode: [faq_tabs ids="223,224,225" search="1" schema="1" class="" anchor=""]
 */
add_shortcode( 'faq_tabs', function( $atts ) {

	$atts = shortcode_atts( [
		'ids'    => '',   // comma-separated term_ids
		'search' => 0,
		'schema' => 1,
		'class'  => '',
		'anchor' => '',
    	'orderby' => 'menu_order',
		'order'  => 'ASC',
	], $atts, 'faq_tabs' );

	$raw = trim( (string) $atts['ids'] );
	if ( $raw === '' ) return '';

	$term_ids = array_values( array_filter( array_map( 'absint', preg_split( '/\s*,\s*/', $raw ) ) ) );
	if ( empty( $term_ids ) ) return '';

	faq_blocks_maybe_enqueue_assets();

	$include_search = (string) $atts['search'] === '1' || $atts['search'] === 1 || $atts['search'] === true;
	$schema_enabled = (string) $atts['schema'] === '1' || $atts['schema'] === 1 || $atts['schema'] === true;

	ob_start();

	// open schema wrapper (only first instance)
	faq_blocks_maybe_open_schema_wrapper( $schema_enabled );

	$block_id = $atts['anchor'] ? sanitize_title( $atts['anchor'] ) : 'faq-tabs-' . implode('-', $term_ids) . '-' . wp_rand( 1000, 9999 );
	$custom_class = sanitize_html_class( $atts['class'] );

	?>
	<style>
	/* Question row */
	.faq-toggle {
	  width: 100%;
	  display: flex;
	  align-items: center;
	  justify-content: space-between;
	  gap: 12px;
	  background: none;
	  border: 0;
	  padding: 0;
	  text-align: left;
	  cursor: pointer;
	  font: inherit;
	}

	/* Title text */
	.faq-title {
	  flex: 1;
	  font-weight: 600;
	}

	/* Chevron icon */
	.faq-chevron {
	  width: 10px;
	  height: 10px;
	  border-right: 2px solid currentColor;
	  border-bottom: 2px solid currentColor;
	  transform: rotate(45deg); /* right-facing by default */
	  transition: transform 0.25s ease;
	  margin-left: 10px;
	}

	/* Rotate when open (down arrow) */
	.faq-toggle[aria-expanded="true"] .faq-chevron {
	  transform: rotate(-135deg);
	}

	/* Optional nicer spacing */
	.faq-item {
	  border-bottom: 1px solid #e5e7eb;
	  padding: 14px 0;
	  margin-bottom: 0;
	}

	.faq-answer {
	  margin-top: 10px;
	}
	</style>
	<div id="<?php echo esc_attr( $block_id ); ?>"
	     class="faq-tabs-block <?php echo esc_attr( $custom_class ); ?>"
	     data-has-search="<?php echo esc_attr( $include_search ? 'true' : 'false' ); ?>">

		<div class="faq-tabs-nav">
			<?php
			$first = true;
			foreach ( $term_ids as $term_id ) {
				$term = get_term( $term_id, 'faq_category' );
				if ( ! $term || is_wp_error( $term ) ) continue;

				$active = $first ? ' active' : '';
				?>
				<button class="faq-tab-button<?php echo esc_attr( $active ); ?>"
				        data-tab="tab-<?php echo esc_attr( $term_id ); ?>"
				        data-term-name="<?php echo esc_attr( $term->name ); ?>">
					<?php echo esc_html( $term->name ); ?>
				</button>
				<?php
				$first = false;
			}
			?>
		</div>

		<?php if ( $include_search ) : ?>
			<div class="faq-search-wrapper">
				<input type="text" class="faq-search-input" placeholder="<?php echo esc_attr__( 'Search FAQs...', 'faq-blocks' ); ?>" />
			</div>
		<?php endif; ?>

		<div class="faq-tabs-content">
			<?php
			$first = true;

			foreach ( $term_ids as $term_id ) {
				$term = get_term( $term_id, 'faq_category' );
				if ( ! $term || is_wp_error( $term ) ) continue;

				$args = [
					'post_type'      => 'faq',
					'posts_per_page' => -1,
					'tax_query'      => [
						[
							'taxonomy' => 'faq_category',
							'field'    => 'term_id',
							'terms'    => $term_id,
      						'include_children' => false, //
						],
					],
					'orderby'        => sanitize_key( $atts['orderby'] ),
					'order'          => strtoupper( $atts['order'] ) === 'DESC' ? 'DESC' : 'ASC',
				];

				$q = new WP_Query( $args );
				if ( ! $q->have_posts() ) {
					wp_reset_postdata();
					continue;
				}

				$active = $first ? ' active' : '';
				?>
				<div class="faq-tab-content<?php echo esc_attr( $active ); ?>" id="tab-<?php echo esc_attr( $term_id ); ?>">
					<?php while ( $q->have_posts() ) : $q->the_post(); ?>
						<?php
						$faq_excerpt = function_exists('get_field') ? get_field( 'faq_excerpt', get_the_ID() ) : '';
						if ( empty( $faq_excerpt ) ) {
							$faq_excerpt = get_the_content();
						}

						$branch = $GLOBALS['ap_branch_faq_branch'] ?? null;
						$post_slug = get_post_field('post_name', get_the_ID());

						if ( $branch ) {
        					$faq_url = home_url('/branch/' . $branch->post_name . '/faqs/' . $post_slug );
						} else {
        					$faq_url = home_url('/faqs/' . $post_slug );
						}
						?>
						<div class="faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
							<?php $item_id = 'faq-' . get_the_ID() . '-' . wp_rand(1000,9999); ?>

							<h3 class="faq-question" itemprop="name">
							  <button
							    class="faq-toggle text-light"
							    type="button"
							    aria-expanded="false"
							    aria-controls="<?php echo esc_attr($item_id); ?>"
							  >
							    <span class="faq-title"><?php the_title(); ?></span>
							    <span class="faq-chevron" aria-hidden="true"></span>
							  </button>
							</h3>

							<div
							  id="<?php echo esc_attr($item_id); ?>"
							  class="faq-answer"
							  itemscope
							  itemprop="acceptedAnswer"
							  itemtype="https://schema.org/Answer"
							  hidden
							>
							  <div itemprop="text" class="text-light">
							  	<?php echo wp_kses_post( $faq_excerpt ); ?><br><a class="mt-1 d-block" href="<?php echo esc_url( $faq_url ); ?>">Read Full Answer</a>
							  </div>
							</div>
						</div>
					<?php endwhile; ?>
				</div>
				<?php

				wp_reset_postdata();
				$first = false;
			}
			?>
		</div>
	</div>
	<script>
	document.addEventListener('click', function (e) {
	  const btn = e.target.closest('.faq-toggle');
	  if (!btn) return;

	  const item = btn.closest('.faq-item');
	  const tab = btn.closest('.faq-tab-content') || document;
	  const panelId = btn.getAttribute('aria-controls');
	  const panel = panelId ? document.getElementById(panelId) : null;
	  if (!panel) return;

	  const isOpen = btn.getAttribute('aria-expanded') === 'true';

	  // Close all other items in this tab (accordion behaviour)
	  tab.querySelectorAll('.faq-toggle[aria-expanded="true"]').forEach(otherBtn => {
	    if (otherBtn === btn) return;
	    otherBtn.setAttribute('aria-expanded', 'false');
	    const otherPanel = document.getElementById(otherBtn.getAttribute('aria-controls'));
	    if (otherPanel) otherPanel.hidden = true;
	  });

	  // Toggle this one
	  btn.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
	  panel.hidden = isOpen;
	});
	</script>
	<?php

	return ob_get_clean();
} );

/**
 * Shortcode: [faq_item id="123" schema="1" class="" anchor="" link="1"]
 * - id: FAQ post ID
 * - schema: include Question/Answer microdata (FAQPage wrapper is handled by your existing open/close logic)
 * - class: extra CSS class on wrapper
 * - anchor: custom HTML id
 * - link: wrap title in permalink (1) or plain text (0)
 */
add_shortcode( 'faq_item', function( $atts ) {

	$atts = shortcode_atts( [
		'id'     => 0,   // post ID
		'schema' => 1,
		'class'  => '',
		'anchor' => '',
		'link'   => 1,
	], $atts, 'faq_item' );

	$post_id = absint( $atts['id'] );
	if ( ! $post_id ) return '';

	$post = get_post( $post_id );
	if ( ! $post || $post->post_type !== 'faq' || $post->post_status !== 'publish' ) {
		return '';
	}

	// Enqueue CSS/JS only if shortcode is used.
	faq_blocks_maybe_enqueue_assets();

	$schema_enabled = (string) $atts['schema'] === '1' || $atts['schema'] === 1 || $atts['schema'] === true;
	$link_title     = (string) $atts['link'] === '1' || $atts['link'] === 1 || $atts['link'] === true;

	ob_start();

	// Open FAQPage schema wrapper once per page (only if schema enabled)
	faq_blocks_maybe_open_schema_wrapper( $schema_enabled );

	$block_id     = $atts['anchor'] ? sanitize_title( $atts['anchor'] ) : 'faq-item-' . $post_id . '-' . wp_rand( 1000, 9999 );
	$custom_class = sanitize_html_class( $atts['class'] );

	// Build answer/excerpt
	$faq_excerpt = function_exists( 'get_field' ) ? get_field( 'faq_excerpt', $post_id ) : '';
	if ( empty( $faq_excerpt ) ) {
		$faq_excerpt = apply_filters( 'the_content', $post->post_content );
	} else {
		// Keep consistent with your other shortcodes (safe HTML output)
		$faq_excerpt = wp_kses_post( $faq_excerpt );
	}

	$title = get_the_title( $post_id );
	$perma = get_permalink( $post_id );

	?>
	<div id="<?php echo esc_attr( $block_id ); ?>" class="faq-item-block <?php echo esc_attr( $custom_class ); ?>">
		<?php if ( $schema_enabled ) : ?>
			<div class="faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
				<h3 class="faq-question" itemprop="name">
					<?php if ( $link_title ) : ?>
						<a href="<?php echo esc_url( $perma ); ?>"><?php echo esc_html( $title ); ?></a>
					<?php else : ?>
						<?php echo esc_html( $title ); ?>
					<?php endif; ?>
				</h3>
				<div class="faq-answer" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
					<div itemprop="text"><?php echo $faq_excerpt; ?></div>
				</div>
			</div>
		<?php else : ?>
			<div class="faq-item">
				<h3 class="faq-question">
					<?php if ( $link_title ) : ?>
						<a href="<?php echo esc_url( $perma ); ?>"><?php echo esc_html( $title ); ?></a>
					<?php else : ?>
						<?php echo esc_html( $title ); ?>
					<?php endif; ?>
				</h3>
				<div class="faq-answer">
					<?php echo $faq_excerpt; ?>
				</div>
			</div>
		<?php endif; ?>
	</div>
	<?php

	return ob_get_clean();
} );