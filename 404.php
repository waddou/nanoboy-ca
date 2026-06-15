<?php
/**
 * NanoBoy — Page introuvable.
 *
 * Le logo du header est le h1 unique de cette page. Le titre du contenu est
 * donc volontairement un h2.
 *
 * @package NanoBoy
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main class="error-404 container" role="main">
	<div class="error-404__content">
		<p class="error-404__code" aria-hidden="true">404</p>
		<h2 class="error-404__title"><?php esc_html_e( 'Page not found', 'nanoboy' ); ?></h2>
		<p class="error-404__message">
			<?php esc_html_e( 'The page you are looking for may have been moved, deleted, or never existed. Try a search to continue.', 'nanoboy' ); ?>
		</p>
		<?php get_search_form(); ?>
	</div>
</main>
<?php
get_footer();
