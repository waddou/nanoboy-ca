<?php
/**
 * NanoBoy — Pied de site (3 colonnes widgets + barre copyright).
 *
 * Les 3 zones widgets (`footer-1..3`) sont enregistrées dans inc/widgets.php.
 * Tant qu'elles sont vides, rien ne s'affiche. La barre basse porte le
 * copyright et les liens réseaux sociaux renseignés dans config.php (rendus
 * uniquement s'ils existent).
 *
 * @package NanoBoy
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$nanoboy_brand_domain = (string) nanoboy_config( 'brand.domain', 'choix-assurances.fr' );
$nanoboy_socials = array_filter( (array) nanoboy_config( 'social', array() ) );
$nanoboy_social_icons = array( 'facebook', 'twitter', 'instagram', 'linkedin', 'youtube' );
?>
<footer class="site-footer" role="contentinfo">
	<?php
	$nanoboy_has_widgets = is_active_sidebar( 'footer-1' )
		|| is_active_sidebar( 'footer-2' )
		|| is_active_sidebar( 'footer-3' );

	if ( $nanoboy_has_widgets ) :
		?>
		<div class="container">
			<div class="site-footer__widgets">
				<?php
				for ( $nanoboy_i = 1; $nanoboy_i <= 3; $nanoboy_i++ ) :
					$nanoboy_sidebar_id = 'footer-' . $nanoboy_i;

					if ( ! is_active_sidebar( $nanoboy_sidebar_id ) ) {
						continue;
					}
					?>
					<div class="site-footer__col">
						<?php dynamic_sidebar( $nanoboy_sidebar_id ); ?>
					</div>
					<?php
				endfor;
				?>
			</div>
		</div>
		<?php
	endif;
	?>

	<div class="site-footer__bar">
		<div class="container site-footer__bar-inner">
			<p class="site-footer__copy">
				<?php
				printf(
					/* translators: 1: Current year, 2: Website domain. */
					esc_html__( '© %1$s %2$s. All rights reserved.', 'nanoboy' ),
					esc_html( wp_date( 'Y' ) ),
					esc_html( $nanoboy_brand_domain )
				);
				?>
			</p>
			<p class="site-footer__disclaimer">
				<?php
				printf(
					/* translators: %s: Website domain. */
					esc_html__( '%s is an independent information website. Our guides are provided for informational purposes and do not constitute financial, legal or administrative advice.', 'nanoboy' ),
					esc_html( $nanoboy_brand_domain )
				);
				?>
			</p>

			<?php if ( ! empty( $nanoboy_socials ) ) : ?>
				<nav class="site-footer__social" aria-label="<?php esc_attr_e( 'Social links', 'nanoboy' ); ?>">
					<ul class="site-footer__social-list" role="list">
						<?php foreach ( $nanoboy_socials as $nanoboy_network => $nanoboy_url ) : ?>
							<?php
							$nanoboy_network = (string) $nanoboy_network;

							if ( ! in_array( $nanoboy_network, $nanoboy_social_icons, true ) ) {
								continue;
							}
							?>
							<li>
								<a
									class="site-footer__social-link"
									href="<?php echo esc_url( $nanoboy_url ); ?>"
									rel="noopener noreferrer"
									target="_blank">
									<svg class="site-footer__social-icon" width="20" height="20" aria-hidden="true" focusable="false">
										<use href="<?php echo esc_url( NANOBOY_URI . '/assets/icons.svg#' . $nanoboy_network ); ?>"></use>
									</svg>
									<span class="screen-reader-text"><?php echo esc_html( ucfirst( $nanoboy_network ) ); ?></span>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</nav>
			<?php endif; ?>
		</div>
	</div>
</footer>
