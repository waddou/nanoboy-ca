<?php
/**
 * NanoBoy — Chargement des assets.
 *
 * Enqueue les deux seuls bundles servis en prod (`app.min.css` + `app.min.js`,
 * versionnés par `filemtime`) et injecte les scripts tiers (GA4, AdSense) en
 * chargement `async` non bloquant.
 *
 * @package NanoBoy
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Calcule une version de cache-busting basée sur la date de modification.
 *
 * Retombe sur NANOBOY_VERSION si le fichier buildé n'existe pas encore.
 *
 * @param string $relative_path Chemin relatif au thème, ex. « assets/css/app.min.css ».
 * @return string
 */
function nanoboy_asset_version( string $relative_path ): string {
	$absolute = NANOBOY_DIR . '/' . ltrim( $relative_path, '/' );
	$mtime    = is_readable( $absolute ) ? filemtime( $absolute ) : false;

	return false !== $mtime ? (string) $mtime : NANOBOY_VERSION;
}

/**
 * Enqueue le CSS et le JS du thème.
 *
 * @return void
 */
function nanoboy_enqueue_assets(): void {
	$css_rel = 'assets/css/app.min.css';
	$js_rel  = 'assets/js/app.min.js';

	wp_enqueue_style(
		'nanoboy-app',
		NANOBOY_URI . '/' . $css_rel,
		array(),
		nanoboy_asset_version( $css_rel )
	);

	wp_enqueue_script(
		'nanoboy-app',
		NANOBOY_URI . '/' . $js_rel,
		array(),
		nanoboy_asset_version( $js_rel ),
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);

	// Fil de discussion : commentaires imbriqués (cœur WP, chargé à la demande).
	if ( is_singular() && comments_open() && (bool) get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'nanoboy_enqueue_assets' );

/**
 * Enqueue Google Analytics 4 (gtag.js) en async.
 *
 * @return void
 */
function nanoboy_enqueue_analytics(): void {
	$ga4_id = (string) nanoboy_config( 'analytics.ga4_id', '' );

	if ( '' === $ga4_id ) {
		return;
	}

	wp_enqueue_script(
		'nanoboy-gtag',
		'https://www.googletagmanager.com/gtag/js?id=' . rawurlencode( $ga4_id ),
		array(),
		null,
		array( 'strategy' => 'async' )
	);

	$inline = sprintf(
		'window.dataLayer = window.dataLayer || [];' .
		'function gtag(){dataLayer.push(arguments);}' .
		"gtag('js', new Date());" .
		"gtag('config', '%s');",
		esc_js( $ga4_id )
	);

	wp_add_inline_script( 'nanoboy-gtag', $inline, 'after' );
}
add_action( 'wp_enqueue_scripts', 'nanoboy_enqueue_analytics' );

/**
 * Enqueue le script AdSense (adsbygoogle.js) en async.
 *
 * @return void
 */
function nanoboy_enqueue_adsense(): void {
	$publisher = (string) nanoboy_config( 'ads.publisher_id', '' );

	if ( '' === $publisher ) {
		return;
	}

	wp_enqueue_script(
		'nanoboy-adsbygoogle',
		'https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=' . rawurlencode( $publisher ),
		array(),
		null,
		array( 'strategy' => 'async' )
	);
}
add_action( 'wp_enqueue_scripts', 'nanoboy_enqueue_adsense' );

/**
 * Ajoute l'attribut `crossorigin` à la balise AdSense (requis par Google).
 *
 * @param string $tag    Balise <script> complète.
 * @param string $handle Handle du script.
 * @return string
 */
function nanoboy_script_crossorigin( string $tag, string $handle ): string {
	if ( 'nanoboy-adsbygoogle' === $handle && ! str_contains( $tag, 'crossorigin' ) ) {
		$tag = str_replace( ' src=', ' crossorigin="anonymous" src=', $tag );
	}

	if ( 'nanoboy-adsbygoogle' === $handle ) {
		$tag = str_replace( ' data-wp-strategy="async"', '', $tag );
	}

	return $tag;
}
add_filter( 'script_loader_tag', 'nanoboy_script_crossorigin', 10, 2 );
