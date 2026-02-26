<?php // phpcs:disable WordPress.Files.FileName.InvalidClassFileName
/**
 * Plugin Name: FAQ Blocks
 * Plugin URI: https://github.com/ChillibyteUK/cbp-faq
 * Description: A reusable WordPress plugin that registers a custom FAQ post type with taxonomy, ACF fields, and a dynamic Gutenberg block with FAQ Schema support.
 * Version: 1.0.1
 * Author: ChillibyteUK - DS
 * Author URI: https://github.com/ChillibyteUK
 * Text Domain: faq-blocks
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package FAQ_Blocks
 *
 * phpcs:disable Universal.Files.SeparateFunctionsFromOO.Mixed
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants.
define( 'FAQ_BLOCKS_VERSION', '1.0.0' );
define( 'FAQ_BLOCKS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'FAQ_BLOCKS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Main plugin class.
 */
class FAQ_Blocks_Plugin {

	/**
	 * Instance of this class.
	 *
	 * @var FAQ_Blocks_Plugin
	 */
	private static $instance = null;

	/**
	 * Get the singleton instance.
	 *
	 * @return FAQ_Blocks_Plugin
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		$this->load_dependencies();
		$this->init_hooks();
	}

	/**
	 * Load required files.
	 *
	 * @return void
	 */
	private function load_dependencies() {
		require_once FAQ_BLOCKS_PLUGIN_DIR . 'includes/cpt-taxonomy.php';
		require_once FAQ_BLOCKS_PLUGIN_DIR . 'includes/options-page.php';
		require_once FAQ_BLOCKS_PLUGIN_DIR . 'includes/block-render.php';
		require_once FAQ_BLOCKS_PLUGIN_DIR . 'includes/template-loader.php';
	}

	/**
	 * Initialize WordPress hooks.
	 *
	 * @return void
	 */
	private function init_hooks() {
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		add_filter( 'wpseo_schema_webpage', array( $this, 'maybe_remove_yoast_breadcrumb_schema' ) );
	}

	/**
	 * Load plugin textdomain.
	 *
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain(
			'faq-blocks',
			false,
			dirname( plugin_basename( __FILE__ ) ) . '/languages'
		);
	}

	/**
	 * Remove Yoast breadcrumb schema on single FAQ pages.
	 *
	 * @param array $data WebPage schema data.
	 * @return array
	 */
	public function maybe_remove_yoast_breadcrumb_schema( $data ) {
		if ( is_singular( 'faq' ) && isset( $data['breadcrumb'] ) ) {
			unset( $data['breadcrumb'] );
		}

		return $data;
	}
}

/**
 * Plugin activation hook.
 *
 * @return void
 */
function faq_blocks_activate() {
	// Register CPT and taxonomy.
	faq_blocks_register_post_type();
	faq_blocks_register_taxonomy();

	// Flush rewrite rules.
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'faq_blocks_activate' );

/**
 * Plugin deactivation hook.
 *
 * @return void
 */
function faq_blocks_deactivate() {
	// Flush rewrite rules.
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'faq_blocks_deactivate' );

/**
 * Initialize the plugin.
 */
FAQ_Blocks_Plugin::get_instance();
