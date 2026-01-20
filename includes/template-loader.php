<?php
/**
 * Template Loader
 *
 * @package FAQ_Blocks
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Load custom template for single FAQ posts if theme doesn't provide one.
 *
 * @param string $template The path of the template to include.
 * @return string
 */
function faq_blocks_template_loader( $template ) {
	// Only proceed if we're viewing a single FAQ post.
	if ( ! is_singular( 'faq' ) ) {
		return $template;
	}

	// Check if plugin template is disabled in settings.
	$use_plugin_template = get_field( 'faq_use_plugin_template', 'option' );
	if ( empty( $use_plugin_template ) ) {
		// Plugin template is disabled, use theme's default.
		return $template;
	}

	// Check if theme has single-faq.php - if so, use it.
	$theme_template = locate_template( array( 'single-faq.php' ) );
	if ( $theme_template ) {
		return $theme_template;
	}

	// Use plugin's fallback template.
	$plugin_template = FAQ_BLOCKS_PLUGIN_DIR . 'templates/single-faq.php';
	if ( file_exists( $plugin_template ) ) {
		return $plugin_template;
	}

	// Fallback to default template.
	return $template;
}
add_filter( 'template_include', 'faq_blocks_template_loader', 99 );
