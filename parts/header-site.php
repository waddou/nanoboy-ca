<?php
/**
 * NanoBoy — Header de site (logo + navigation).
 *
 * Wordmark texte + tagline, sans image ni requête supplémentaire.
 * La marque est encapsulée dans un `<h1>` sur l'accueil et la 404 (marque =
 * titre principal), un `<h2>` ailleurs (le `<h1>` revient au contenu). La
 * navigation principale (parts + burger) est rendue par {@see nanoboy_primary_nav()}
 * et n'apparaît que si un menu est assigné à l'emplacement « primary ».
 *
 * @package NanoBoy
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$nanoboy_site_name = (string) nanoboy_config( 'brand.name', get_bloginfo( 'name' ) );
$nanoboy_tagline   = trim( (string) nanoboy_config( 'brand.tagline', get_bloginfo( 'description' ) ) );
$nanoboy_brand_h   = ( is_front_page() || is_404() ) ? 'h1' : 'h2';
?>
<header class="site-header" role="banner">
	<div class="container">
		<div class="site-header__inner">
			<<?php echo esc_attr( $nanoboy_brand_h ); ?> class="site-header__brand">
				<a class="site-header__logo-link" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
					<span class="site-header__wordmark"><?php echo esc_html( $nanoboy_site_name ); ?></span>
					<?php if ( '' !== $nanoboy_tagline ) : ?>
						<span class="site-header__tagline"><?php echo esc_html( $nanoboy_tagline ); ?></span>
					<?php endif; ?>
				</a>
			</<?php echo esc_attr( $nanoboy_brand_h ); ?>>

			<?php nanoboy_primary_nav(); ?>
		</div>
	</div>
</header>
