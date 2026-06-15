<?php
/**
 * NanoBoy — Articles liés par catégorie.
 *
 * @package NanoBoy
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$nanoboy_related_post = get_post( $args['post'] ?? null );

if ( ! $nanoboy_related_post instanceof WP_Post ) {
	return;
}

$nanoboy_related_categories = wp_get_post_categories( $nanoboy_related_post->ID );

if ( is_wp_error( $nanoboy_related_categories ) || empty( $nanoboy_related_categories ) ) {
	return;
}

$nanoboy_related_query = new WP_Query(
	array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => 3,
		'post__not_in'        => array( $nanoboy_related_post->ID ),
		'category__in'        => array_map( 'absint', $nanoboy_related_categories ),
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	)
);

if ( ! $nanoboy_related_query->have_posts() ) {
	return;
}
?>
<section class="related-posts" aria-labelledby="nanoboy-related-title">
	<h2 id="nanoboy-related-title" class="related-posts__title"><?php esc_html_e( 'Related articles', 'nanoboy' ); ?></h2>

	<div class="related-posts__grid">
		<?php while ( $nanoboy_related_query->have_posts() ) : ?>
			<?php $nanoboy_related_query->the_post(); ?>
			<article <?php post_class( 'related-posts__item' ); ?>>
				<?php if ( has_post_thumbnail() ) : ?>
					<a class="related-posts__media" href="<?php echo esc_url( get_permalink() ); ?>" tabindex="-1" aria-hidden="true">
						<?php nanoboy_thumbnail( get_the_ID(), 'nanoboy-related', 'related-posts__image' ); ?>
					</a>
				<?php endif; ?>

				<h3 class="related-posts__item-title">
					<a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a>
				</h3>
			</article>
		<?php endwhile; ?>
	</div>
</section>
<?php
wp_reset_postdata();
