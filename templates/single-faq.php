<?php
/**
 * Template for displaying single FAQ posts
 *
 * This is a fallback template used when the active theme doesn't provide
 * a single-faq.php template. To customize, copy this file to your theme
 * directory as single-faq.php and modify as needed.
 *
 * @package FAQ_Blocks
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="main" class="site-main faq-content">

	<?php
	while ( have_posts() ) :
		the_post();
		?>

		<article id="post-<?php the_ID(); ?>" <?php post_class( 'faq-single' ); ?>>

			<header class="entry-header">
				<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
			</header>

			<div class="entry-content">
				<?php
				the_content();

				wp_link_pages(
					array(
						'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'faq-blocks' ),
						'after'  => '</div>',
					)
				);
				?>
			</div>

			<?php
			// Display FAQ categories if they exist.
			$categories = get_the_terms( get_the_ID(), 'faq_category' );
			if ( $categories && ! is_wp_error( $categories ) ) :
				?>
				<div class="entry-footer">
					<div class="faq-categories">
						<span class="faq-categories-label"><?php esc_html_e( 'Categories:', 'faq-blocks' ); ?></span>
						<?php
						$category_links = array();
						foreach ( $categories as $category ) {
							$category_links[] = sprintf(
								'<a href="%s">%s</a>',
								esc_url( get_term_link( $category ) ),
								esc_html( $category->name )
							);
						}
						echo implode( ', ', $category_links ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						?>
					</div>
				</div>
			<?php endif; ?>

		</article>

	<?php endwhile; ?>

</main>

<?php
get_footer();
