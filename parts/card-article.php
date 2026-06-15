<?php
/**
 * NanoBoy — Carte article unique pour tous les listings.
 *
 * @package NanoBoy
 *
 * @var array<string, mixed> $args Arguments optionnels du fragment.
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$nanoboy_card_post     = get_post( $args['post'] ?? null );
$nanoboy_card_featured = ! empty( $args['featured'] );

if ( ! $nanoboy_card_post instanceof WP_Post ) {
	return;
}

$nanoboy_card_has_media = has_post_thumbnail( $nanoboy_card_post );
$nanoboy_card_class     = trim(
	'card'
	. ( $nanoboy_card_featured ? ' card--featured' : '' )
	. ( $nanoboy_card_has_media ? '' : ' card--no-media' )
);

// Hero : grand visuel 16:9 chargé en priorité. Listing : miniature 3:2 (300×200 max, @2x).
$nanoboy_card_image_size = $nanoboy_card_featured ? 'nanoboy-card' : 'nanoboy-card-thumb';
$nanoboy_card_image_args = $nanoboy_card_featured
	? array(
		'loading'       => 'eager',
		'fetchpriority' => 'high',
	)
	: array();

$nanoboy_card_categories = get_the_category( $nanoboy_card_post->ID );
$nanoboy_card_category   = $nanoboy_card_categories[0] ?? null;
$nanoboy_card_minutes    = nanoboy_reading_time( $nanoboy_card_post );
$nanoboy_card_number     = isset( $args['number'] ) ? absint( $args['number'] ) : 0;
?>
<article <?php post_class( $nanoboy_card_class, $nanoboy_card_post->ID ); ?>>
	<?php if ( ! $nanoboy_card_featured && 0 < $nanoboy_card_number ) : ?>
		<span class="card__number" aria-hidden="true"><?php echo esc_html( str_pad( (string) $nanoboy_card_number, 2, '0', STR_PAD_LEFT ) ); ?></span>
	<?php endif; ?>

	<?php if ( $nanoboy_card_has_media ) : ?>
		<a class="card__media" href="<?php echo esc_url( get_permalink( $nanoboy_card_post ) ); ?>" tabindex="-1" aria-hidden="true">
			<?php nanoboy_thumbnail( $nanoboy_card_post, $nanoboy_card_image_size, 'card__image', $nanoboy_card_image_args ); ?>
		</a>
	<?php endif; ?>

	<div class="card__body">
		<div class="card__meta">
			<?php if ( $nanoboy_card_category instanceof WP_Term ) : ?>
				<a class="card__category" href="<?php echo esc_url( get_category_link( $nanoboy_card_category ) ); ?>">
					<?php echo esc_html( $nanoboy_card_category->name ); ?>
				</a>
			<?php endif; ?>
			<time class="card__date" datetime="<?php echo esc_attr( get_the_date( DATE_W3C, $nanoboy_card_post ) ); ?>">
				<?php echo esc_html( get_the_date( '', $nanoboy_card_post ) ); ?>
			</time>
			<?php if ( 0 < $nanoboy_card_minutes ) : ?>
				<span class="card__reading-time">
					<?php
					printf(
						/* translators: %d: estimated reading time in minutes. */
						esc_html__( '%d min read', 'nanoboy' ),
						(int) $nanoboy_card_minutes
					);
					?>
				</span>
			<?php endif; ?>
		</div>

		<h2 class="card__title">
			<a class="card__title-link" href="<?php echo esc_url( get_permalink( $nanoboy_card_post ) ); ?>">
				<?php echo esc_html( get_the_title( $nanoboy_card_post ) ); ?>
			</a>
		</h2>

		<?php $nanoboy_card_excerpt = nanoboy_excerpt( $nanoboy_card_featured ? 40 : 24, $nanoboy_card_post ); ?>
		<?php if ( '' !== $nanoboy_card_excerpt ) : ?>
			<p class="card__excerpt"><?php echo esc_html( $nanoboy_card_excerpt ); ?></p>
		<?php endif; ?>
	</div>
</article>
