<?php
/**
 * NanoBoy — Fragment d'emplacement publicitaire AdSense.
 *
 * Quand `format` est vide, data-ad-format et data-full-width-responsive sont
 * omis : AdSense sert alors la taille calculée de l'<ins> (fixée dans
 * ad-slot.css), conformément à la méthode Google « modifier le code d'annonce
 * responsive ». Les emplacements `desktop_only` ne poussent leur requête que
 * si la fenêtre est ≥ 64rem (seuil --nb-bp-lg, identique au masquage CSS).
 *
 * @package NanoBoy
 *
 * @var array<string, string|bool> $args Configuration préparée par nanoboy_ad_slot().
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$nanoboy_ad_location  = (string) ( $args['location'] ?? '' );
$nanoboy_ad_publisher = (string) ( $args['publisher'] ?? '' );
$nanoboy_ad_slot      = (string) ( $args['slot'] ?? '' );
$nanoboy_ad_style     = (string) ( $args['style'] ?? 'display:block' );
$nanoboy_ad_format    = (string) ( $args['format'] ?? '' );
$nanoboy_ad_layout    = (string) ( $args['layout'] ?? '' );
$nanoboy_ad_fwr       = ! empty( $args['full_width'] );
$nanoboy_ad_desktop   = ! empty( $args['desktop_only'] );
$nanoboy_ad_label     = (string) ( $args['label'] ?? __( 'Advertisement', 'nanoboy' ) );

if ( '' === $nanoboy_ad_location || '' === $nanoboy_ad_publisher || '' === $nanoboy_ad_slot ) {
	return;
}
?>
<aside
	class="ad-slot ad-slot--<?php echo esc_attr( str_replace( '_', '-', $nanoboy_ad_location ) ); ?>"
	aria-label="<?php echo esc_attr( $nanoboy_ad_label ); ?>">
	<ins
		class="adsbygoogle ad-slot__unit"
		style="<?php echo esc_attr( $nanoboy_ad_style ); ?>"
		data-ad-client="<?php echo esc_attr( $nanoboy_ad_publisher ); ?>"
		data-ad-slot="<?php echo esc_attr( $nanoboy_ad_slot ); ?>"
		<?php if ( '' !== $nanoboy_ad_format ) : ?>
			data-ad-format="<?php echo esc_attr( $nanoboy_ad_format ); ?>"
		<?php endif; ?>
		<?php if ( '' !== $nanoboy_ad_layout ) : ?>
			data-ad-layout="<?php echo esc_attr( $nanoboy_ad_layout ); ?>"
		<?php endif; ?>
		<?php if ( $nanoboy_ad_fwr ) : ?>
			data-full-width-responsive="true"
		<?php endif; ?>
	></ins>
	<?php if ( $nanoboy_ad_desktop ) : ?>
		<script>
			if ( window.matchMedia( '(min-width: 64rem)' ).matches ) {
				(window.adsbygoogle = window.adsbygoogle || []).push({});
			}
		</script>
	<?php else : ?>
		<script>
			(window.adsbygoogle = window.adsbygoogle || []).push({});
		</script>
	<?php endif; ?>
</aside>
