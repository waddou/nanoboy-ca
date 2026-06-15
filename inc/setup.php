<?php
/**
 * NanoBoy — Configuration du thème.
 *
 * Déclare les fonctionnalités supportées, les tailles d'images, les
 * emplacements de menus et charge le text domain pour l'i18n.
 *
 * @package NanoBoy
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enregistre les supports de thème, tailles d'images et menus.
 *
 * @return void
 */
function nanoboy_setup(): void {
	// Laisse WordPress et le plugin SEO actif gérer la balise <title>.
	add_theme_support( 'title-tag' );

	// Miniatures d'articles (utilisées par les cartes et les articles liés).
	add_theme_support( 'post-thumbnails' );

	// Flux RSS automatiques dans <head>.
	add_theme_support( 'automatic-feed-links' );

	// Marquage HTML5 pour les éléments générés par le cœur.
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
			'navigation-widgets',
		)
	);

	// Embeds responsives (anti-CLS sur les contenus oEmbed).
	add_theme_support( 'responsive-embeds' );

	// Tailles d'images dédiées (recadrées, dimensions réservées anti-CLS).
	set_post_thumbnail_size( 768, 432, true );          // 16:9 — taille par défaut.
	add_image_size( 'nanoboy-card', 768, 432, true );   // 16:9 — hero accueil.
	add_image_size( 'nanoboy-card-thumb', 600, 400, true ); // 3:2 — miniature listing (300×200 max, @2x).
	add_image_size( 'nanoboy-related', 360, 240, true ); // Grille d'articles liés.

	// Emplacement de menu unique (rendu seulement si un menu y est assigné).
	register_nav_menus(
		array(
			'primary' => esc_html__( 'Primary menu', 'nanoboy' ),
		)
	);
}
add_action( 'after_setup_theme', 'nanoboy_setup' );

/**
 * Charge le text domain du thème pour les traductions.
 *
 * @return void
 */
function nanoboy_load_textdomain(): void {
	load_theme_textdomain( 'nanoboy', NANOBOY_DIR . '/languages' );
}
add_action( 'after_setup_theme', 'nanoboy_load_textdomain' );

/**
 * Définit la largeur de contenu utilisée par le cœur (oEmbed, images).
 *
 * @return void
 */
function nanoboy_content_width(): void {
	$GLOBALS['content_width'] = (int) nanoboy_config( 'layout.container_max', 1200 );
}
add_action( 'after_setup_theme', 'nanoboy_content_width', 0 );

/**
 * Déclare la favicon historique du site.
 *
 * @return void
 */
function nanoboy_favicon(): void {
	?>
	<link rel="icon" href="<?php echo esc_url( NANOBOY_URI . '/assets/img/favicon.ico' ); ?>" type="image/x-icon">
	<?php
}
add_action( 'wp_head', 'nanoboy_favicon', 5 );
