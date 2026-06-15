<?php
/**
 * NanoBoy — Sidebar principale.
 *
 * Les widgets configurés dans l'administration sont prioritaires. Recherche
 * et archives mensuelles servent de repli quand la zone est vide.
 *
 * @package NanoBoy
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<aside class="sidebar layout__aside" aria-label="<?php esc_attr_e( 'Sidebar', 'nanoboy' ); ?>">
	<?php
	// Pub AdSense en dur, toujours en premier (desktop uniquement — masquée < 64rem).
	nanoboy_ad_slot( 'sidebar_top' );
	?>

	<?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>
		<?php dynamic_sidebar( 'sidebar-1' ); ?>
	<?php else : ?>
		<section class="sidebar__widget widget widget_search">
			<h2 class="sidebar__title"><?php esc_html_e( 'Search', 'nanoboy' ); ?></h2>
			<?php get_search_form(); ?>
		</section>

		<section class="sidebar__widget widget widget_archive">
			<h2 class="sidebar__title"><?php esc_html_e( 'Monthly archives', 'nanoboy' ); ?></h2>
			<ul role="list">
				<?php wp_get_archives( array( 'type' => 'monthly' ) ); ?>
			</ul>
		</section>
	<?php endif; ?>
</aside>
