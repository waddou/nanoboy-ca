<?php
/**
 * NanoBoy — Réglages EN DUR du thème.
 *
 * Remplace intégralement l'ancien framework d'options (SMOF). Aucun panneau
 * d'administration : pour changer un réglage, on édite ce fichier.
 *
 * Accès via {@see nanoboy_config()} avec une clé en notation pointée, ex. :
 *   nanoboy_config( 'ads.slots.list_top' );
 *   nanoboy_config( 'analytics.ga4_id' );
 *
 * @package NanoBoy
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Retourne l'ensemble des réglages du thème.
 *
 * Le tableau est construit une seule fois puis mis en cache statique.
 *
 * @return array<string, mixed>
 */
function nanoboy_config_all(): array {
	static $config = null;

	if ( null !== $config ) {
		return $config;
	}

	$config = array(

		// Identité de marque affichée par le thème.
		'brand' => array(
			'name'    => 'Choix Assurances',
			'domain'  => 'choix-assurances.fr',
			'tagline' => 'Choisir et comparer ses assurances',
		),

		// Mise en page (boxed centré).
		'layout' => array(
			'container_max'    => 1200, // px — conteneur cible ~1100–1200.
			'content_columns'  => 8,    // grille 8/12.
			'sidebar_columns'  => 4,    // grille 4/12.
		),

		// Analytics — Google Analytics 4 (gtag, chargé en async).
		'analytics' => array(
			'ga4_id' => 'G-HXLT8RG722',
		),

		// Régie publicitaire — Google AdSense (adsbygoogle.js en async).
		// Rendu et dimensions réservées (anti-CLS) : inc/ads.php + ad-slot.css.
		'ads' => array(
			'publisher_id' => 'ca-pub-9582901796643932',
			'slots'        => array(
				'sidebar_top'  => '8020999981', // 1ᵉʳ widget sidebar (desktop seul) — 250×250 puis 336×280.
				'list_top'     => '5147424639', // Listes (accueil, archives), 2ᵉ carte — in-article fluid 280px.
				'list_in_feed' => '4540919976', // Listes (accueil, archives), 4ᵉ carte — in-article fluid 280px.
				'article_top'  => '7323340661', // Haut d'article (single) — pleine largeur, hauteur 280px.
			),
		),

		// Réseaux sociaux (liens de profil — pas de SDK tiers).
		'social' => array(
			'facebook'  => '',
			'twitter'   => '',
			'instagram' => '',
			'linkedin'  => '',
			'youtube'   => '',
		),
	);

	return $config;
}

/**
 * Lit un réglage du thème par clé en notation pointée.
 *
 * @param string $key     Clé, ex. « ads.slots.list_top ».
 * @param mixed  $default Valeur de repli si la clé est absente.
 * @return mixed
 */
function nanoboy_config( string $key, mixed $default = null ): mixed {
	$value = nanoboy_config_all();

	foreach ( explode( '.', $key ) as $segment ) {
		if ( ! is_array( $value ) || ! array_key_exists( $segment, $value ) ) {
			return $default;
		}
		$value = $value[ $segment ];
	}

	return $value;
}
