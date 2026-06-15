<?php
/**
 * NanoBoy — Métadonnées d'un article.
 *
 * @package NanoBoy
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$nanoboy_meta_post = get_post( $args['post'] ?? null );

if ( ! $nanoboy_meta_post instanceof WP_Post ) {
	return;
}

$nanoboy_author_id   = (int) $nanoboy_meta_post->post_author;
$nanoboy_author_name = get_the_author_meta( 'display_name', $nanoboy_author_id );
$nanoboy_categories  = get_the_category_list( ', ', '', $nanoboy_meta_post->ID );
$nanoboy_minutes     = nanoboy_reading_time( $nanoboy_meta_post );
?>
<div class="post-meta">
	<span class="post-meta__date">
		<?php esc_html_e( 'Published on', 'nanoboy' ); ?>
		<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C, $nanoboy_meta_post ) ); ?>">
			<?php echo esc_html( get_the_date( '', $nanoboy_meta_post ) ); ?>
		</time>
	</span>

	<?php if ( '' !== $nanoboy_author_name ) : ?>
		<span class="post-meta__author">
			<?php esc_html_e( 'by', 'nanoboy' ); ?>
			<a href="<?php echo esc_url( get_author_posts_url( $nanoboy_author_id ) ); ?>">
				<?php echo esc_html( $nanoboy_author_name ); ?>
			</a>
		</span>
	<?php endif; ?>

	<?php if ( is_string( $nanoboy_categories ) && '' !== $nanoboy_categories ) : ?>
		<span class="post-meta__categories">
			<?php esc_html_e( 'in', 'nanoboy' ); ?>
			<?php echo wp_kses_post( $nanoboy_categories ); ?>
		</span>
	<?php endif; ?>

	<?php if ( 0 < $nanoboy_minutes ) : ?>
		<span class="post-meta__reading-time">
			<?php
			printf(
				/* translators: %d: estimated reading time in minutes. */
				esc_html__( '%d min read', 'nanoboy' ),
				(int) $nanoboy_minutes
			);
			?>
		</span>
	<?php endif; ?>
</div>
