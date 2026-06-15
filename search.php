<?php
/**
 * NanoBoy — Résultats de recherche.
 *
 * @package NanoBoy
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<div class="container">
	<div class="layout">
		<main class="layout__main">
			<header class="listing-header">
				<h1 class="listing-header__title">
					<?php
					printf(
						/* translators: %s: search query. */
						esc_html__( 'Search results for: %s', 'nanoboy' ),
						esc_html( get_search_query() )
					);
					?>
				</h1>
			</header>

			<?php if ( have_posts() ) : ?>
				<div class="grid-cards">
					<?php
					while ( have_posts() ) :
						the_post();
						get_template_part( 'parts/card-article' );
					endwhile;
					?>
				</div>

				<?php nanoboy_pagination(); ?>
			<?php else : ?>
				<div class="listing-empty">
					<p><?php esc_html_e( 'No results matched your search. Try different keywords.', 'nanoboy' ); ?></p>
					<?php get_search_form(); ?>
				</div>
			<?php endif; ?>
		</main>

		<?php get_sidebar(); ?>
	</div>
</div>
<?php
get_footer();
