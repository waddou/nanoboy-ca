<?php
/**
 * NanoBoy — Pied du document.
 *
 * Referme le conteneur `#content` ouvert dans header.php, rend le pied de site
 * (parts/footer-site), puis appelle `wp_footer()` et ferme le document.
 *
 * @package NanoBoy
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
</div><!-- #content -->

<?php
get_template_part( 'parts/footer-site' );

wp_footer();
?>
</body>
</html>
