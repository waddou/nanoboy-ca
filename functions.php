<?php
/**
 * NanoBoy — Bootstrap du thème.
 *
 * Ce fichier ne contient AUCUNE logique métier : il définit les constantes du
 * thème puis charge les réglages ({@see config.php}) et les modules de `inc/`.
 * Chaque module enregistre lui-même ses hooks.
 *
 * @package NanoBoy
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Version du thème (cache-busting des assets via filemtime, voir inc/enqueue.php). */
define( 'NANOBOY_VERSION', '1.0.0' );

/** Chemin absolu du dossier du thème, sans slash final. */
define( 'NANOBOY_DIR', get_template_directory() );

/** URL du dossier du thème, sans slash final. */
define( 'NANOBOY_URI', get_template_directory_uri() );

// Réglages en dur (marque, GA4, AdSense, réseaux, layout).
require_once NANOBOY_DIR . '/config.php';

/**
 * Modules de `inc/` à charger, dans l'ordre.
 *
 * Chaque entrée est un chemin relatif à `inc/` (sans extension). En phase
 * finale, un module absent doit provoquer une erreur explicite afin qu'un
 * déploiement incomplet ne passe pas silencieusement.
 *
 * @var string[]
 */
$nanoboy_modules = array(
	'setup',
	'enqueue',
	'template-helpers',
	'nav',
	'term-meta',
	'ads',
	'comments',
	'widgets',
);

foreach ( $nanoboy_modules as $nanoboy_module ) {
	$nanoboy_module_path = NANOBOY_DIR . '/inc/' . $nanoboy_module . '.php';
	require_once $nanoboy_module_path;
}

unset( $nanoboy_modules, $nanoboy_module, $nanoboy_module_path );
