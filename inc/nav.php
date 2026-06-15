<?php
/**
 * NanoBoy — Navigation principale.
 *
 * Rend le menu `primary` via `wp_nav_menu`, accompagné d'un bouton burger
 * accessible (toggle géré en vanilla par src/js/modules/nav.js). Conformément
 * à la décision n°3 : le menu n'est rendu QUE si un menu est réellement
 * assigné à l'emplacement dans l'admin — aucun `fallback_cb`, donc jamais de
 * liste de pages auto.
 *
 * @package NanoBoy
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Affiche la navigation principale (burger + menu) si un menu est assigné.
 *
 * @return void
 */
function nanoboy_primary_nav(): void {
	if ( ! has_nav_menu( 'primary' ) ) {
		return;
	}
	?>
	<nav class="site-nav" aria-label="<?php esc_attr_e( 'Primary menu', 'nanoboy' ); ?>">
		<button
			class="site-nav__toggle"
			type="button"
			aria-expanded="false"
			aria-controls="primary-menu">
			<span class="site-nav__burger" aria-hidden="true"></span>
			<span class="screen-reader-text"><?php esc_html_e( 'Menu', 'nanoboy' ); ?></span>
		</button>
		<?php
		wp_nav_menu(
			array(
				'theme_location' => 'primary',
				'container'      => false,
				'menu_id'        => 'primary-menu',
				'menu_class'     => 'site-nav__list',
				'depth'          => 2,
				'fallback_cb'    => false,
			)
		);
		?>
	</nav>
	<?php
}
