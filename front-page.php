<?php
/**
 * NanoBoy — Accueil : article épinglé, flux récent et sidebar.
 *
 * @package NanoBoy
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$nanoboy_paged      = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
$nanoboy_sticky_ids = array_map( 'absint', (array) get_option( 'sticky_posts', array() ) );
$nanoboy_featured   = null;

if ( ! empty( $nanoboy_sticky_ids ) ) {
	$nanoboy_featured_query = new WP_Query(
		array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'post__in'            => $nanoboy_sticky_ids,
			'posts_per_page'      => 1,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	);

	$nanoboy_featured = $nanoboy_featured_query->posts[0] ?? null;
}

$nanoboy_posts_query = new WP_Query(
	array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'paged'               => $nanoboy_paged,
		'post__not_in'        => $nanoboy_featured instanceof WP_Post ? array( $nanoboy_featured->ID ) : array(),
		'ignore_sticky_posts' => true,
	)
);

get_header();
?>
<div class="container">
	<div class="layout">
		<main class="layout__main home-listing">
			<?php if ( 1 === $nanoboy_paged && $nanoboy_featured instanceof WP_Post ) : ?>
				<section class="home-listing__hero" aria-labelledby="nanoboy-featured-title">
					<h2 id="nanoboy-featured-title" class="home-listing__section-title">
						<?php esc_html_e( 'Featured article', 'nanoboy' ); ?>
					</h2>
					<?php get_template_part( 'parts/card-article', null, array( 'post' => $nanoboy_featured, 'featured' => true ) ); ?>
				</section>
			<?php endif; ?>

			<section class="home-listing__posts" aria-labelledby="nanoboy-latest-title">
				<h2 id="nanoboy-latest-title" class="home-listing__section-title">
					<?php esc_html_e( 'Latest articles', 'nanoboy' ); ?>
				</h2>

				<?php if ( $nanoboy_posts_query->have_posts() ) : ?>
					<div class="grid-cards">
						<?php
						$nanoboy_post_count = 0;

						while ( $nanoboy_posts_query->have_posts() ) :
							$nanoboy_posts_query->the_post();
							++$nanoboy_post_count;

							get_template_part( 'parts/card-article' );

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

					<?php nanoboy_pagination( $nanoboy_posts_query ); ?>
				<?php else : ?>
					<p class="listing-empty"><?php esc_html_e( 'No articles have been published yet.', 'nanoboy' ); ?></p>
				<?php endif; ?>
			</section>
		</main>

		<?php get_sidebar(); ?>
	</div>
</div>
<?php
wp_reset_postdata();
get_footer();
