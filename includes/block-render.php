<?php
/**
 * ACF Block Registration and Render Functions
 *
 * @package FAQ_Blocks
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register ACF Blocks.
 *
 * @return void
 */
function faq_blocks_register_acf_blocks() {
	// Check if ACF function exists.
	if ( ! function_exists( 'acf_register_block_type' ) ) {
		return;
	}

	// Register FAQ Block.
	acf_register_block_type(
		array(
			'name'            => 'faq-list',
			'title'           => __( 'CBP FAQ List', 'faq-blocks' ),
			'description'     => __( 'Display a list of FAQs from a selected category with FAQ Schema.', 'faq-blocks' ),
			'render_callback' => 'faq_blocks_render_faq_list_block',
			'category'        => 'common',
			'icon'            => 'editor-help',
			'keywords'        => array( 'faq', 'question', 'answer' ),
			'mode'            => 'edit',
			'supports'        => array(
				'align'           => true,
				'anchor'          => true,
				'mode'            => false,
				'customClassName' => true,
			),
		)
	);

	// Register FAQ Tabs Block.
	acf_register_block_type(
		array(
			'name'            => 'faq-tabs',
			'title'           => __( 'CBP FAQ Tabs', 'faq-blocks' ),
			'description'     => __( 'Display FAQs in tabs by category with FAQ Schema.', 'faq-blocks' ),
			'render_callback' => 'faq_blocks_render_faq_tabs_block',
			'category'        => 'common',
			'icon'            => 'index-card',
			'keywords'        => array( 'faq', 'tabs', 'category' ),
			'mode'            => 'edit',
			'supports'        => array(
				'align'           => true,
				'anchor'          => true,
				'mode'            => false,
				'customClassName' => true,
			),
		)
	);
}
add_action( 'acf/init', 'faq_blocks_register_acf_blocks' );

/**
 * Register ACF Fields for FAQ Block.
 *
 * @return void
 */
function faq_blocks_register_block_fields() {
	// Check if ACF function exists.
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'                   => 'group_faq_block',
			'title'                 => __( 'FAQ List Block', 'faq-blocks' ),
			'fields'                => array(
				array(
					'key'           => 'field_faq_category',
					'label'         => __( 'FAQ Category', 'faq-blocks' ),
					'name'          => 'faq_category',
					'type'          => 'taxonomy',
					'instructions'  => __( 'Select a category to display FAQs from.', 'faq-blocks' ),
					'required'      => 1,
					'taxonomy'      => 'faq_category',
					'field_type'    => 'select',
					'allow_null'    => 0,
					'add_term'      => 0,
					'save_terms'    => 0,
					'load_terms'    => 0,
					'return_format' => 'id',
					'multiple'      => 0,
				),
				array(
					'key'           => 'field_faq_show_title',
					'label'         => __( 'Show Title', 'faq-blocks' ),
					'name'          => 'show_title',
					'type'          => 'true_false',
					'instructions'  => __( 'Display category name as title above FAQ list.', 'faq-blocks' ),
					'required'      => 0,
					'default_value' => 0,
					'ui'            => 1,
					'ui_on_text'    => __( 'Yes', 'faq-blocks' ),
					'ui_off_text'   => __( 'No', 'faq-blocks' ),
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'block',
						'operator' => '==',
						'value'    => 'acf/faq-list',
					),
				),
			),
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen'        => '',
			'active'                => true,
			'description'           => '',
		)
	);

	// FAQ Tabs Block fields.
	acf_add_local_field_group(
		array(
			'key'                   => 'group_faq_tabs_block',
			'title'                 => __( 'FAQ Tabs Block', 'faq-blocks' ),
			'fields'                => array(
				array(
					'key'           => 'field_faq_categories',
					'label'         => __( 'FAQ Categories', 'faq-blocks' ),
					'name'          => 'faq_categories',
					'type'          => 'taxonomy',
					'instructions'  => __( 'Select categories to display as tabs.', 'faq-blocks' ),
					'required'      => 1,
					'taxonomy'      => 'faq_category',
					'field_type'    => 'checkbox',
					'allow_null'    => 0,
					'add_term'      => 0,
					'save_terms'    => 0,
					'load_terms'    => 0,
					'return_format' => 'id',
					'multiple'      => 1,
				),
				array(
					'key'           => 'field_faq_include_search',
					'label'         => __( 'Include Search?', 'faq-blocks' ),
					'name'          => 'include_search',
					'type'          => 'true_false',
					'instructions'  => __( 'Add a search box to filter FAQs within the active tab.', 'faq-blocks' ),
					'required'      => 0,
					'default_value' => 0,
					'ui'            => 1,
					'ui_on_text'    => __( 'Yes', 'faq-blocks' ),
					'ui_off_text'   => __( 'No', 'faq-blocks' ),
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'block',
						'operator' => '==',
						'value'    => 'acf/faq-tabs',
					),
				),
			),
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen'        => '',
			'active'                => true,
			'description'           => '',
		)
	);
}
add_action( 'acf/init', 'faq_blocks_register_block_fields' );

/**
 * Track FAQ block instances on the page.
 *
 * @var int
 */
global $faq_blocks_instance_count;
if ( ! isset( $faq_blocks_instance_count ) ) {
	$faq_blocks_instance_count = 0;
}

/**
 * Render the FAQ List Block.
 *
 * @param array  $block The block settings and attributes.
 * @param string $content The block inner HTML (empty).
 * @param bool   $is_preview True during AJAX preview.
 * @param int    $post_id The post ID this block is saved to.
 *
 * @return void
 *
 * @phpcs:disable Generic.CodeAnalysis.UnusedFunctionParameter
 */
function faq_blocks_render_faq_list_block( $block, $content = '', $is_preview = false, $post_id = 0 ) {
	global $faq_blocks_instance_count;
	++$faq_blocks_instance_count;
	$is_first_instance = ( 1 === $faq_blocks_instance_count );

	// Get the selected category.
	$category_id = get_field( 'faq_category' );
	$show_title  = get_field( 'show_title' );

	if ( empty( $category_id ) ) {
		return;
	}

	// Get category term for title.
	$category_term = get_term( $category_id, 'faq_category' );

	// Query FAQs from the selected category.
	$args = array(
		'post_type'      => 'faq',
		'posts_per_page' => -1,
		'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			array(
				'taxonomy' => 'faq_category',
				'field'    => 'term_id',
				'terms'    => $category_id,
			),
		),
		'orderby'        => 'title',
		'order'          => 'ASC',
	);

	$faq_query = new WP_Query( $args );

	if ( ! $faq_query->have_posts() ) {
		return;
	}

	// Generate block ID for anchor support.
	$block_id = 'faq-list-' . ( ! empty( $block['anchor'] ) ? $block['anchor'] : $block['id'] );

	// Get block alignment class.
	$align_class = ! empty( $block['align'] ) ? 'align' . $block['align'] : '';

	// Get custom CSS classes.
	$custom_class = ! empty( $block['className'] ) ? $block['className'] : '';

	// Only the first instance opens the FAQPage schema wrapper.
	if ( $is_first_instance ) {
		echo '<div class="faq-page-wrapper" itemscope itemtype="https://schema.org/FAQPage">';
	}

	// Start block output without FAQPage schema (it's in the wrapper above).
	?>
	<div id="<?php echo esc_attr( $block_id ); ?>" class="faq-list-block <?php echo esc_attr( $align_class ); ?> <?php echo esc_attr( $custom_class ); ?>">
		<?php
		if ( $show_title && $category_term && ! is_wp_error( $category_term ) ) {
			?>
			<h2 class="faq-list-title"><?php echo esc_html( $category_term->name ); ?> FAQs</h2>
			<?php
		}

		while ( $faq_query->have_posts() ) {
			$faq_query->the_post();

			// Get the FAQ excerpt field.
			$faq_excerpt = get_field( 'faq_excerpt', get_the_ID() );

			// If no excerpt, fall back to post content.
			if ( empty( $faq_excerpt ) ) {
				$faq_excerpt = get_the_content();
			}
			?>
			<div class="faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
				<h3 class="faq-question" itemprop="name">
					<a href="<?php echo esc_url( get_permalink() ); ?>">
						<?php the_title(); ?>
					</a>
				</h3>
				<div class="faq-answer" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
					<div itemprop="text">
						<?php echo wp_kses_post( $faq_excerpt ); ?>
					</div>
				</div>
			</div>
			<?php
		}
		?>
	</div>
	<?php

	// Reset post data.
	wp_reset_postdata();
}

/**
 * Render the FAQ Tabs Block.
 *
 * @param array  $block The block settings and attributes.
 * @param string $content The block inner HTML (empty).
 * @param bool   $is_preview True during AJAX preview.
 * @param int    $post_id The post ID this block is saved to.
 *
 * @return void
 *
 * @phpcs:disable Generic.CodeAnalysis.UnusedFunctionParameter
 */
function faq_blocks_render_faq_tabs_block( $block, $content = '', $is_preview = false, $post_id = 0 ) {
	global $faq_blocks_instance_count;
	++$faq_blocks_instance_count;
	$is_first_instance = ( 1 === $faq_blocks_instance_count );

	// Get the selected categories.
	$category_ids   = get_field( 'faq_categories' );
	$include_search = get_field( 'include_search' );

	if ( empty( $category_ids ) || ! is_array( $category_ids ) ) {
		return;
	}

	// Generate block ID for anchor support.
	$block_id = 'faq-tabs-' . ( ! empty( $block['anchor'] ) ? $block['anchor'] : $block['id'] );

	// Get block alignment class.
	$align_class = ! empty( $block['align'] ) ? 'align' . $block['align'] : '';

	// Get custom CSS classes.
	$custom_class = ! empty( $block['className'] ) ? $block['className'] : '';

	// Only the first instance opens the FAQPage schema wrapper.
	if ( $is_first_instance ) {
		echo '<div class="faq-page-wrapper" itemscope itemtype="https://schema.org/FAQPage">';
	}

	// Start block output.
	?>
	<div id="<?php echo esc_attr( $block_id ); ?>" class="faq-tabs-block <?php echo esc_attr( $align_class ); ?> <?php echo esc_attr( $custom_class ); ?>" data-has-search="<?php echo esc_attr( $include_search ? 'true' : 'false' ); ?>">
		<div class="faq-tabs-nav">
			<?php
			$first = true;
			foreach ( $category_ids as $cat_id ) {
				$term = get_term( $cat_id, 'faq_category' );
				if ( $term && ! is_wp_error( $term ) ) {
					$active_class = $first ? ' active' : '';
					?>
					<button class="faq-tab-button<?php echo esc_attr( $active_class ); ?>" 
							data-tab="tab-<?php echo esc_attr( $cat_id ); ?>"
							data-term-name="<?php echo esc_attr( $term->name ); ?>">
						<?php echo esc_html( $term->name ); ?>
					</button>
					<?php
					$first = false;
				}
			}
			?>
		</div>

		<?php if ( $include_search ) : ?>
			<div class="faq-search-wrapper">
				<input type="text" class="faq-search-input" placeholder="<?php esc_attr_e( 'Search FAQs...', 'faq-blocks' ); ?>" />
			</div>
		<?php endif; ?>

		<div class="faq-tabs-content">
			<?php
			$first = true;
			foreach ( $category_ids as $cat_id ) {
				$term = get_term( $cat_id, 'faq_category' );
				if ( ! $term || is_wp_error( $term ) ) {
					continue;
				}

				// Query FAQs for this category.
				$args = array(
					'post_type'      => 'faq',
					'posts_per_page' => -1,
					'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
						array(
							'taxonomy' => 'faq_category',
							'field'    => 'term_id',
							'terms'    => $cat_id,
						),
					),
					'orderby'        => 'title',
					'order'          => 'ASC',
				);

				$faq_query = new WP_Query( $args );

				if ( ! $faq_query->have_posts() ) {
					continue;
				}

				$active_class = $first ? ' active' : '';
				?>
				<div class="faq-tab-content<?php echo esc_attr( $active_class ); ?>" 
					id="tab-<?php echo esc_attr( $cat_id ); ?>">
					<?php
					while ( $faq_query->have_posts() ) {
						$faq_query->the_post();

						// Get the FAQ excerpt field.
						$faq_excerpt = get_field( 'faq_excerpt', get_the_ID() );

						// If no excerpt, fall back to post content.
						if ( empty( $faq_excerpt ) ) {
							$faq_excerpt = get_the_content();
						}
						?>
						<div class="faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
							<h3 class="faq-question" itemprop="name">
								<a href="<?php echo esc_url( get_permalink() ); ?>">
									<?php the_title(); ?>
								</a>
							</h3>
							<div class="faq-answer" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
								<div itemprop="text">
									<?php echo wp_kses_post( $faq_excerpt ); ?>
								</div>
							</div>
						</div>
						<?php
					}
					wp_reset_postdata();
					?>
				</div>
				<?php
				$first = false;
			}
			?>
		</div>
	</div>
	<?php
}

/**
 * Add FAQ Schema to single FAQ posts via JSON-LD.
 *
 * @return void
 */
function faq_blocks_add_single_faq_schema() {
	// Only on single FAQ posts.
	if ( ! is_singular( 'faq' ) ) {
		return;
	}

	// Use the full post content for single FAQ pages.
	$faq_content = get_the_content();

	// Strip HTML tags for schema.
	$answer_text = wp_strip_all_tags( $faq_content );

	// Build JSON-LD schema.
	$schema = array(
		'@context'   => 'https://schema.org',
		'@type'      => 'FAQPage',
		'mainEntity' => array(
			array(
				'@type'          => 'Question',
				'name'           => get_the_title(),
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => $answer_text,
				),
			),
		),
	);

	// Output JSON-LD in head.
	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}
add_action( 'wp_head', 'faq_blocks_add_single_faq_schema' );

/**
 * Add schema class to body.
 *
 * @param array $classes Body classes.
 *
 * @return array
 */
function faq_blocks_add_schema_body_class( $classes ) {
	if ( is_singular( 'faq' ) ) {
		$classes[] = 'faq-single-page';
	}
	return $classes;
}
add_filter( 'body_class', 'faq_blocks_add_schema_body_class' );

/**
 * Close the FAQ Page schema wrapper after content.
 *
 * @return void
 */
function faq_blocks_close_schema_wrapper() {
	global $faq_blocks_instance_count;

	// Only close if we opened a wrapper (i.e., at least one block was rendered).
	if ( isset( $faq_blocks_instance_count ) && $faq_blocks_instance_count > 0 ) {
		echo '</div><!-- .faq-page-wrapper -->';
		// Reset counter for next page load.
		$faq_blocks_instance_count = 0;
	}
}
add_action( 'wp_footer', 'faq_blocks_close_schema_wrapper', 999 );

/**
 * Add inline styles for FAQ blocks.
 *
 * @return void
 */
function faq_blocks_add_inline_styles() {
	// Register a dummy stylesheet handle for our inline styles.
	wp_register_style( 'faq-blocks-styles', false, array(), FAQ_BLOCKS_VERSION );
	wp_enqueue_style( 'faq-blocks-styles' );

	$css = '
		.faq-page-wrapper {
			display: contents;
		}
		.faq-list-block {
			margin: 2em 0;
		}
		.faq-item {
			margin-bottom: 2em;
			padding-bottom: 1.5em;
			border-bottom: 1px solid #e0e0e0;
		}
		.faq-item:last-child {
			border-bottom: none;
		}
		.faq-question {
			margin-top: 0;
			margin-bottom: 0.5em;
			font-size: 1.2em;
		}
		.faq-question a {
			text-decoration: none;
			color: inherit;
		}
		.faq-question a:hover {
			color: #0073aa;
		}
		.faq-answer {
			color: #666;
			line-height: 1.6;
		}
		.faq-tabs-block {
			margin: 2em 0;
		}
		.faq-search-wrapper {
			margin-bottom: 1.5em;
		}
		.faq-search-input {
			width: 100%;
			max-width: 100%;
			padding: 0.75em 1em;
			font-size: 1em;
			border: 2px solid #e0e0e0;
			border-radius: 4px;
			transition: border-color 0.3s ease;
		}
		.faq-search-input:focus {
			outline: none;
			border-color: #0073aa;
		}
		.faq-no-results {
			padding: 1em;
			background: #f5f5f5;
			border-left: 3px solid #d63638;
			color: #666;
			font-style: italic;
		}
		.faq-tabs-nav {
			display: flex;
			gap: 0.5em;
			margin-bottom: 1.5em;
			border-bottom: 2px solid #e0e0e0;
		}
		.faq-tab-button {
			padding: 0.75em 1.5em;
			background: transparent;
			border: none;
			border-bottom: 3px solid transparent;
			cursor: pointer;
			font-size: 1em;
			font-weight: 500;
			color: #666;
			transition: all 0.3s ease;
		}
		.faq-tab-button:hover {
			color: #0073aa;
			background: #f5f5f5;
		}
		.faq-tab-button.active {
			color: #0073aa;
			border-bottom-color: #0073aa;
		}
		.faq-tabs-content {
			position: relative;
		}
		.faq-tab-content {
			display: none;
		}
		.faq-tab-content.active {
			display: block;
		}
	';

	// Add custom CSS from options page.
	$custom_css = get_field( 'faq_custom_css', 'option' );
	if ( ! empty( $custom_css ) ) {
		// Strip HTML tags and normalize whitespace.
		$custom_css = wp_strip_all_tags( $custom_css );
		// Replace literal \n with actual newlines in case they were copy-pasted.
		$custom_css = str_replace( '\n', "\n", $custom_css );
		$css       .= "\n" . $custom_css;
	}

	wp_add_inline_style( 'faq-blocks-styles', $css );
}
add_action( 'wp_enqueue_scripts', 'faq_blocks_add_inline_styles' );
add_action( 'admin_enqueue_scripts', 'faq_blocks_add_inline_styles' );

/**
 * Enqueue JavaScript for FAQ tabs.
 *
 * @return void
 */
function faq_blocks_enqueue_scripts() {
	$script = "
	(function() {
		function initFAQTabs() {
			const tabBlocks = document.querySelectorAll('.faq-tabs-block');
			
			tabBlocks.forEach(function(block) {
				const buttons = block.querySelectorAll('.faq-tab-button');
				const searchInput = block.querySelector('.faq-search-input');
				
				// Update search placeholder based on active tab
				function updateSearchPlaceholder(termName) {
					if (searchInput) {
						searchInput.placeholder = 'Search ' + termName + ' FAQs...';
					}
				}
				
				// Set initial placeholder
				const activeButton = block.querySelector('.faq-tab-button.active');
				if (activeButton) {
					updateSearchPlaceholder(activeButton.getAttribute('data-term-name'));
				}
				
				// Tab switching
				buttons.forEach(function(button) {
					button.addEventListener('click', function(e) {
						e.preventDefault();
						const tabId = this.getAttribute('data-tab');
						const termName = this.getAttribute('data-term-name');
						const parentBlock = this.closest('.faq-tabs-block');
						
						// Remove active class from all buttons and tabs
						parentBlock.querySelectorAll('.faq-tab-button').forEach(function(btn) {
							btn.classList.remove('active');
						});
						parentBlock.querySelectorAll('.faq-tab-content').forEach(function(content) {
							content.classList.remove('active');
						});
						
						// Add active class to clicked button and corresponding tab
						this.classList.add('active');
						const targetTab = parentBlock.querySelector('#' + tabId);
						if (targetTab) {
							targetTab.classList.add('active');
						}
						
						// Update search placeholder
						updateSearchPlaceholder(termName);
						
						// Reset search when switching tabs
						if (searchInput) {
							searchInput.value = '';
							filterFAQs(parentBlock, '');
						}
					});
				});
				
				// Search functionality
				if (searchInput) {
					searchInput.addEventListener('input', function() {
						const searchTerm = this.value.toLowerCase();
						filterFAQs(block, searchTerm);
					});
				}
			});
		}
		
		function filterFAQs(block, searchTerm) {
			const activeTab = block.querySelector('.faq-tab-content.active');
			if (!activeTab) return;
			
			const faqItems = activeTab.querySelectorAll('.faq-item');
			let visibleCount = 0;
			
			faqItems.forEach(function(item) {
				const question = item.querySelector('.faq-question').textContent.toLowerCase();
				const answer = item.querySelector('.faq-answer').textContent.toLowerCase();
				
				if (searchTerm === '' || question.includes(searchTerm) || answer.includes(searchTerm)) {
					item.style.display = '';
					visibleCount++;
				} else {
					item.style.display = 'none';
				}
			});
			
			// Show/hide no results message
			let noResults = activeTab.querySelector('.faq-no-results');
			if (visibleCount === 0 && searchTerm !== '') {
				if (!noResults) {
					noResults = document.createElement('p');
					noResults.className = 'faq-no-results';
					noResults.textContent = 'No FAQs found matching your search.';
					activeTab.appendChild(noResults);
				}
				noResults.style.display = 'block';
			} else if (noResults) {
				noResults.style.display = 'none';
			}
		}
		
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', initFAQTabs);
		} else {
			initFAQTabs();
		}
	})();
	";

	wp_register_script( 'faq-blocks-tabs', '', array(), FAQ_BLOCKS_VERSION, true );
	wp_enqueue_script( 'faq-blocks-tabs' );
	wp_add_inline_script( 'faq-blocks-tabs', $script );
}
add_action( 'wp_enqueue_scripts', 'faq_blocks_enqueue_scripts' );
add_action( 'admin_enqueue_scripts', 'faq_blocks_enqueue_scripts' );
