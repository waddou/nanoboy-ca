<?php
/**
 * NanoBoy — En-tête du document.
 *
 * Ouvre le document HTML (`<head>` + `wp_head()`), le `<body>`, le lien
 * d'évitement (skip link) puis le header de site (parts/header-site) et le
 * conteneur de contenu `#content` refermé dans footer.php.
 *
 * @package NanoBoy
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'nanoboy' ); ?></a>

<?php get_template_part( 'parts/header-site' ); ?>

<div id="content" class="site-content">
