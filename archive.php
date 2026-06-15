<?php
/**
 * NanoBoy — Archives.
 *
 * @package NanoBoy
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$nanoboy_term        = get_queried_object();
$nanoboy_archive_h1  = $nanoboy_term instanceof WP_Term
	? nanoboy_term_h1( $nanoboy_term )
	: wp_strip_all_tags( get_the_archive_title() );
$nanoboy_description = get_the_archive_description();

get_header();
?>
<div class="container">
	<div class="layout">
		<main class="layout__main">
			<header class="listing-header">
				<h1 class="listing-header__title"><?php echo esc_html( $nanoboy_archive_h1 ); ?></h1>

				<?php if ( '' !== $nanoboy_description ) : ?>
					<div class="listing-header__description"><?php echo wp_kses_post( $nanoboy_description ); ?></div>
				<?php endif; ?>
			</header>

			<?php if ( have_posts() ) : ?>
				<div class="grid-cards">
					<?php
					$nanoboy_post_count = 0;

					while ( have_posts() ) :
						the_post();
						++$nanoboy_post_count;

						get_template_part( 'parts/card-article', null, array( 'number' => $nanoboy_post_count ) );

						// Pubs « cartes » aux positions 2 et 4 de la grille.
						if ( 1 === $nanoboy_post_count ) {
							nanoboy_ad_slot( 'list_top' );
						}

						if ( 2 === $nanoboy_post_count ) {
							nanoboy_ad_slot( 'list_in_feed' );
						}
					endwhile;
					?>
				</div>

				<?php nanoboy_pagination(); ?>
			<?php else : ?>
				<p class="listing-empty"><?php esc_html_e( 'No articles were found in this archive.', 'nanoboy' ); ?></p>
			<?php endif; ?>
		</main>

		<?php get_sidebar(); ?>
	</div>
</div>
<?php
get_footer();
