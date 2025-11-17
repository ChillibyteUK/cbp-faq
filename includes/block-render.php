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
			'title'           => __( 'FAQ List', 'faq-blocks' ),
			'description'     => __( 'Display a list of FAQs from a selected category with FAQ Schema.', 'faq-blocks' ),
			'render_callback' => 'faq_blocks_render_faq_list_block',
			'category'        => 'common',
			'icon'            => 'editor-help',
			'keywords'        => array( 'faq', 'question', 'answer' ),
			'mode'            => 'edit',
			'supports'        => array(
				'align'  => true,
				'anchor' => true,
				'mode'   => false,
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
 */
function faq_blocks_render_faq_list_block( $block, $content = '', $is_preview = false, $post_id = 0 ) {
	global $faq_blocks_instance_count;
	$faq_blocks_instance_count++;
	$is_first_instance = ( 1 === $faq_blocks_instance_count );

	// Get the selected category.
	$category_id = get_field( 'faq_category' );

	if ( empty( $category_id ) ) {
		return;
	}

	// Query FAQs from the selected category.
	$args = array(
		'post_type'      => 'faq',
		'posts_per_page' => -1,
		'tax_query'      => array(
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

	// Only the first instance opens the FAQPage schema wrapper.
	if ( $is_first_instance ) {
		echo '<div class="faq-page-wrapper" itemscope itemtype="https://schema.org/FAQPage">';
	}

	// Start block output without FAQPage schema (it's in the wrapper above).
	?>
	<div id="<?php echo esc_attr( $block_id ); ?>" class="faq-list-block <?php echo esc_attr( $align_class ); ?>">
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
		?>
	</div>
	<?php

	// Reset post data.
	wp_reset_postdata();
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
	';
	wp_add_inline_style( 'wp-block-library', $css );
}
add_action( 'wp_enqueue_scripts', 'faq_blocks_add_inline_styles' );
add_action( 'admin_enqueue_scripts', 'faq_blocks_add_inline_styles' );
