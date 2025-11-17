<?php
/**
 * Custom Post Type and Taxonomy Registration
 *
 * @package FAQ_Blocks
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the FAQ custom post type.
 *
 * @return void
 */
function faq_blocks_register_post_type() {
	// Get the dynamic slug from ACF options, default to 'faqs'.
	$faq_slug = get_field( 'faq_slug', 'option' );
	if ( empty( $faq_slug ) ) {
		$faq_slug = 'faqs';
	}

	$labels = array(
		'name'                  => _x( 'FAQs', 'Post Type General Name', 'faq-blocks' ),
		'singular_name'         => _x( 'FAQ', 'Post Type Singular Name', 'faq-blocks' ),
		'menu_name'             => __( 'FAQs', 'faq-blocks' ),
		'name_admin_bar'        => __( 'FAQ', 'faq-blocks' ),
		'archives'              => __( 'FAQ Archives', 'faq-blocks' ),
		'attributes'            => __( 'FAQ Attributes', 'faq-blocks' ),
		'parent_item_colon'     => __( 'Parent FAQ:', 'faq-blocks' ),
		'all_items'             => __( 'All FAQs', 'faq-blocks' ),
		'add_new_item'          => __( 'Add New FAQ', 'faq-blocks' ),
		'add_new'               => __( 'Add New', 'faq-blocks' ),
		'new_item'              => __( 'New FAQ', 'faq-blocks' ),
		'edit_item'             => __( 'Edit FAQ', 'faq-blocks' ),
		'update_item'           => __( 'Update FAQ', 'faq-blocks' ),
		'view_item'             => __( 'View FAQ', 'faq-blocks' ),
		'view_items'            => __( 'View FAQs', 'faq-blocks' ),
		'search_items'          => __( 'Search FAQ', 'faq-blocks' ),
		'not_found'             => __( 'Not found', 'faq-blocks' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'faq-blocks' ),
		'featured_image'        => __( 'Featured Image', 'faq-blocks' ),
		'set_featured_image'    => __( 'Set featured image', 'faq-blocks' ),
		'remove_featured_image' => __( 'Remove featured image', 'faq-blocks' ),
		'use_featured_image'    => __( 'Use as featured image', 'faq-blocks' ),
		'insert_into_item'      => __( 'Insert into FAQ', 'faq-blocks' ),
		'uploaded_to_this_item' => __( 'Uploaded to this FAQ', 'faq-blocks' ),
		'items_list'            => __( 'FAQs list', 'faq-blocks' ),
		'items_list_navigation' => __( 'FAQs list navigation', 'faq-blocks' ),
		'filter_items_list'     => __( 'Filter FAQs list', 'faq-blocks' ),
	);

	$args = array(
		'label'               => __( 'FAQ', 'faq-blocks' ),
		'description'         => __( 'Frequently Asked Questions', 'faq-blocks' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 20,
		'menu_icon'           => 'dashicons-editor-help',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => false,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
		'show_in_rest'        => true,
		'rewrite'             => array( 'slug' => $faq_slug ),
	);

	register_post_type( 'faq', $args );
}
add_action( 'init', 'faq_blocks_register_post_type' );

/**
 * Register the FAQ Category taxonomy.
 *
 * @return void
 */
function faq_blocks_register_taxonomy() {
	$labels = array(
		'name'                       => _x( 'FAQ Categories', 'Taxonomy General Name', 'faq-blocks' ),
		'singular_name'              => _x( 'FAQ Category', 'Taxonomy Singular Name', 'faq-blocks' ),
		'menu_name'                  => __( 'Categories', 'faq-blocks' ),
		'all_items'                  => __( 'All Categories', 'faq-blocks' ),
		'parent_item'                => __( 'Parent Category', 'faq-blocks' ),
		'parent_item_colon'          => __( 'Parent Category:', 'faq-blocks' ),
		'new_item_name'              => __( 'New Category Name', 'faq-blocks' ),
		'add_new_item'               => __( 'Add New Category', 'faq-blocks' ),
		'edit_item'                  => __( 'Edit Category', 'faq-blocks' ),
		'update_item'                => __( 'Update Category', 'faq-blocks' ),
		'view_item'                  => __( 'View Category', 'faq-blocks' ),
		'separate_items_with_commas' => __( 'Separate categories with commas', 'faq-blocks' ),
		'add_or_remove_items'        => __( 'Add or remove categories', 'faq-blocks' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'faq-blocks' ),
		'popular_items'              => __( 'Popular Categories', 'faq-blocks' ),
		'search_items'               => __( 'Search Categories', 'faq-blocks' ),
		'not_found'                  => __( 'Not Found', 'faq-blocks' ),
		'no_terms'                   => __( 'No categories', 'faq-blocks' ),
		'items_list'                 => __( 'Categories list', 'faq-blocks' ),
		'items_list_navigation'      => __( 'Categories list navigation', 'faq-blocks' ),
	);

	$args = array(
		'labels'            => $labels,
		'hierarchical'      => true,
		'public'            => true,
		'show_ui'           => true,
		'show_admin_column' => true,
		'show_in_nav_menus' => true,
		'show_tagcloud'     => true,
		'show_in_rest'      => true,
		'rewrite'           => array( 'slug' => 'faq-category' ),
	);

	register_taxonomy( 'faq_category', array( 'faq' ), $args );
}
add_action( 'init', 'faq_blocks_register_taxonomy' );

/**
 * Flush rewrite rules when FAQ slug is updated.
 *
 * @param mixed $value The new value.
 * @param mixed $post_id The post ID (options page).
 * @param array $field The field array.
 *
 * @return mixed
 */
function faq_blocks_flush_rewrites_on_slug_change( $value, $post_id, $field ) {
	// Check if this is the faq_slug field.
	if ( 'faq_slug' === $field['name'] ) {
		// Re-register the post type with the new slug.
		faq_blocks_register_post_type();
		// Flush rewrite rules.
		flush_rewrite_rules();
	}

	return $value;
}
add_filter( 'acf/update_value', 'faq_blocks_flush_rewrites_on_slug_change', 10, 3 );
