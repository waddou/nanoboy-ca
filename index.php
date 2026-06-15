<?php
/**
 * NanoBoy — Template de listing de repli.
 *
 * @package NanoBoy
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$nanoboy_posts_page_id = (int) get_option( 'page_for_posts' );
$nanoboy_index_title    = 0 < $nanoboy_posts_page_id
	? get_the_title( $nanoboy_posts_page_id )
	: __( 'Latest articles', 'nanoboy' );

get_header();
?>
<div class="container">
	<div class="layout">
		<main class="layout__main">
			<header class="listing-header">
				<h1 class="listing-header__title"><?php echo esc_html( $nanoboy_index_title ); ?></h1>
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
				<p class="listing-empty"><?php esc_html_e( 'No articles were found.', 'nanoboy' ); ?></p>
			<?php endif; ?>
		</main>

		<?php get_sidebar(); ?>
	</div>
</div>
<?php
get_footer();
