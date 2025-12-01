<?php
/**
 * ACF Options Page and Fields
 *
 * @package FAQ_Blocks
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register ACF Options Page for FAQ Settings.
 *
 * @return void
 */
function faq_blocks_register_options_page() {
	// Check if ACF function exists.
	if ( ! function_exists( 'acf_add_options_page' ) ) {
		return;
	}

	acf_add_options_page(
		array(
			'page_title' => __( 'FAQ Settings', 'faq-blocks' ),
			'menu_title' => __( 'FAQ Settings', 'faq-blocks' ),
			'menu_slug'  => 'faq-settings',
			'capability' => 'manage_options',
			'parent'     => 'edit.php?post_type=faq',
			'icon_url'   => 'dashicons-admin-settings',
			'position'   => false,
			'redirect'   => false,
		)
	);
}
add_action( 'acf/init', 'faq_blocks_register_options_page' );

/**
 * Register ACF Fields for FAQ Settings.
 *
 * @return void
 */
function faq_blocks_register_acf_fields() {
	// Check if ACF function exists.
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	// FAQ Settings fields.
	acf_add_local_field_group(
		array(
			'key'                   => 'group_faq_settings',
			'title'                 => __( 'FAQ Settings', 'faq-blocks' ),
			'fields'                => array(
				array(
					'key'           => 'field_faq_slug',
					'label'         => __( 'FAQ Slug', 'faq-blocks' ),
					'name'          => 'faq_slug',
					'type'          => 'text',
					'instructions'  => __( 'The URL slug for FAQ posts. Change this to customize the FAQ permalink structure. Default: faqs', 'faq-blocks' ),
					'required'      => 0,
					'default_value' => 'faqs',
					'placeholder'   => 'faqs',
				),
				array(
					'key'           => 'field_faq_custom_css',
					'label'         => __( 'Custom CSS', 'faq-blocks' ),
					'name'          => 'faq_custom_css',
					'type'          => 'textarea',
					'instructions'  => __( 'Add custom CSS to style your FAQ blocks. Do not include &lt;style&gt; tags.', 'faq-blocks' ),
					'required'      => 0,
					'default_value' => '',
					'placeholder'   => '.faq-list-block {\n    /* Your custom styles */\n}',
					'rows'          => 10,
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'faq-settings',
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

	// FAQ Excerpt field for FAQ post type.
	acf_add_local_field_group(
		array(
			'key'                   => 'group_faq_fields',
			'title'                 => __( 'FAQ Details', 'faq-blocks' ),
			'fields'                => array(
				array(
					'key'           => 'field_faq_excerpt',
					'label'         => __( 'FAQ Answer (Short)', 'faq-blocks' ),
					'name'          => 'faq_excerpt',
					'type'          => 'wysiwyg',
					'instructions'  => __( 'A short answer for the FAQ. This will be displayed in FAQ lists and blocks.', 'faq-blocks' ),
					'required'      => 0,
					'default_value' => '',
					'tabs'          => 'all',
					'toolbar'       => 'basic',
					'media_upload'  => 0,
					'delay'         => 1,
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'faq',
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
add_action( 'acf/init', 'faq_blocks_register_acf_fields' );
