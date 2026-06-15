<?php
/**
 * NanoBoy — Emplacements publicitaires AdSense.
 *
 * Le script adsbygoogle.js est chargé UNE seule fois en async (inc/enqueue.php) ;
 * chaque emplacement ne fait que pousser son <ins> dans la file. Chaque créneau
 * a des dimensions figées AVANT la réponse AdSense (anti-CLS), réservées dans
 * src/css/4-components/ad-slot.css.
 *
 * @package NanoBoy
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Retourne la configuration de rendu d'un emplacement publicitaire.
 *
 * Clés de rendu consommées par parts/ad-slot.php :
 * - style        : attribut style de l'<ins> (les unités in-article y figent leur hauteur) ;
 * - format       : data-ad-format — '' = attribut omis, la taille est alors lue dans le
 *                  CSS (méthode Google « modifier le code d'annonce responsive ») ;
 * - layout       : data-ad-layout ('' = omis) ;
 * - full_width   : ajoute data-full-width-responsive="true" ;
 * - desktop_only : créneau masqué en CSS sous 64rem ET push JS conditionné au même
 *                  seuil via matchMedia → zéro requête publicitaire sur mobile.
 *
 * @param string $location Nom de l'emplacement.
 * @return array<string, string|bool>|null
 */
function nanoboy_ad_config( string $location ): ?array {
	$locations = array(
		// Sidebar, 1ᵉʳ widget (desktop uniquement) — taille fixée en CSS : 250×250 puis 336×280.
		'sidebar_top'  => array(
			'style'        => 'display:inline-block',
			'format'       => '',
			'layout'       => '',
			'full_width'   => false,
			'desktop_only' => true,
			'label'        => __( 'Advertisement at the top of the sidebar', 'nanoboy' ),
		),
		// Listes (accueil, archives) — 2ᵉ carte de la grille, in-article fluid à hauteur figée.
		'list_top'     => array(
			'style'        => 'display:block;text-align:center;height:280px',
			'format'       => 'fluid',
			'layout'       => 'in-article',
			'full_width'   => false,
			'desktop_only' => false,
			'label'        => __( 'Advertisement in the posts list', 'nanoboy' ),
		),
		// Listes (accueil, archives) — 4ᵉ carte de la grille, in-article fluid à hauteur figée.
		'list_in_feed' => array(
			'style'        => 'display:block;text-align:center;height:280px',
			'format'       => 'fluid',
			'layout'       => 'in-article',
			'full_width'   => false,
			'desktop_only' => false,
			'label'        => __( 'Second advertisement in the posts list', 'nanoboy' ),
		),
		// Haut d'article (single) — pleine largeur du contenu, hauteur figée en CSS (280px).
		'article_top'  => array(
			'style'        => 'display:block',
			'format'       => '',
			'layout'       => '',
			'full_width'   => false,
			'desktop_only' => false,
			'label'        => __( 'Advertisement before the article content', 'nanoboy' ),
		),
	);

	if ( ! isset( $locations[ $location ] ) ) {
		return null;
	}

	$slot      = (string) nanoboy_config( 'ads.slots.' . $location, '' );
	$publisher = (string) nanoboy_config( 'ads.publisher_id', '' );

	if ( '' === $slot || '' === $publisher ) {
		return null;
	}

	return array_merge(
		$locations[ $location ],
		array(
			'location'  => $location,
			'publisher' => $publisher,
			'slot'      => $slot,
		)
	);
}

/**
 * Affiche un emplacement publicitaire nommé.
 *
 * @param string $location Nom de l'emplacement.
 * @return void
 */
function nanoboy_ad_slot( string $location ): void {
	$config = nanoboy_ad_config( $location );

	if ( null === $config ) {
		return;
	}

	get_template_part( 'parts/ad-slot', null, $config );
}
