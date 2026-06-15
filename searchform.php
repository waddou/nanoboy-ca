<?php
/**
 * NanoBoy — Formulaire de recherche.
 *
 * @package NanoBoy
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$nanoboy_search_id = wp_unique_id( 'nanoboy-search-field-' );
?>
<form class="search-form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="search-form__label" for="<?php echo esc_attr( $nanoboy_search_id ); ?>">
		<span class="screen-reader-text"><?php esc_html_e( 'Search for:', 'nanoboy' ); ?></span>
		<input
			id="<?php echo esc_attr( $nanoboy_search_id ); ?>"
			class="search-form__field"
			name="s"
			type="search"
			value="<?php echo esc_attr( get_search_query() ); ?>"
			placeholder="<?php esc_attr_e( 'Search…', 'nanoboy' ); ?>">
	</label>
	<button class="button search-form__submit" type="submit"><?php esc_html_e( 'Search', 'nanoboy' ); ?></button>
</form>
