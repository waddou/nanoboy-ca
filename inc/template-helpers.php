<?php
/**
 * NanoBoy — Helpers de templates réutilisables.
 *
 * @package NanoBoy
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Affiche la miniature d'un article avec ses dimensions réservées.
 *
 * @param WP_Post|int|null    $post       Article ciblé, ou article courant.
 * @param string              $size       Taille d'image WordPress.
 * @param string              $class      Classe CSS optionnelle.
 * @param array<string,mixed> $attributes Attributs HTML à remplacer ou ajouter.
 * @return void
 */
function nanoboy_thumbnail(
	WP_Post|int|null $post = null,
	string $size = 'nanoboy-card',
	string $class = '',
	array $attributes = array()
): void {
	$post = get_post( $post );

	if ( ! $post instanceof WP_Post || ! has_post_thumbnail( $post ) ) {
		return;
	}

	$attributes = array_merge(
		array(
			'class'    => trim( $class ),
			'loading'  => 'lazy',
			'decoding' => 'async',
		),
		$attributes
	);

	echo wp_kses_post( get_the_post_thumbnail( $post, $size, $attributes ) );
}

/**
 * Retourne un extrait nettoyé et limité en nombre de mots.
 *
 * @param int              $word_count Nombre maximal de mots.
 * @param WP_Post|int|null $post       Article ciblé, ou article courant.
 * @return string
 */
function nanoboy_excerpt( int $word_count = 40, WP_Post|int|null $post = null ): string {
	$post = get_post( $post );

	if ( ! $post instanceof WP_Post ) {
		return '';
	}

	$text = has_excerpt( $post ) ? $post->post_excerpt : $post->post_content;
	$text = strip_shortcodes( $text );
	$text = excerpt_remove_blocks( $text );
	$text = wp_strip_all_tags( $text, true );

	return wp_trim_words( $text, max( 1, $word_count ), '…' );
}

/**
 * Retourne le temps de lecture estimé d'un article, en minutes.
 *
 * @param WP_Post|int|null $post           Article ciblé, ou article courant.
 * @param int              $words_per_min  Vitesse de lecture (mots/minute).
 * @return int Minutes (minimum 1), ou 0 si l'article est introuvable.
 */
function nanoboy_reading_time( WP_Post|int|null $post = null, int $words_per_min = 200 ): int {
	$post = get_post( $post );

	if ( ! $post instanceof WP_Post ) {
		return 0;
	}

	$text  = wp_strip_all_tags( strip_shortcodes( $post->post_content ), true );
	$words = preg_split( '/\s+/u', trim( $text ), -1, PREG_SPLIT_NO_EMPTY );
	$count = is_array( $words ) ? count( $words ) : 0;

	if ( 0 === $count ) {
		return 0;
	}

	return max( 1, (int) ceil( $count / max( 1, $words_per_min ) ) );
}

/**
 * Retourne le balisage d'une icône du fil d'Ariane depuis le sprite du thème.
 *
 * @param string $name  Identifiant du symbole dans assets/icons.svg.
 * @param string $class Classe CSS de l'icône.
 * @return string
 */
function nanoboy_breadcrumb_icon( string $name, string $class ): string {
	$allowed_icons = array( 'home', 'chevron-right' );

	if ( ! in_array( $name, $allowed_icons, true ) ) {
		return '';
	}

	return sprintf(
		'<svg class="%1$s" width="16" height="16" aria-hidden="true" focusable="false"><use href="%2$s"></use></svg>',
		esc_attr( $class ),
		esc_url( NANOBOY_URI . '/assets/icons.svg#' . $name )
	);
}

/**
 * Retourne le balisage autorisé pour les icônes du fil d'Ariane.
 *
 * @return array<string,array<string,bool>>
 */
function nanoboy_breadcrumb_icon_allowed_html(): array {
	return array(
		'span' => array(
			'class' => true,
		),
		'svg' => array(
			'aria-hidden' => true,
			'class'       => true,
			'focusable'   => true,
			'height'      => true,
			'width'       => true,
		),
		'use' => array(
			'href' => true,
		),
	);
}

/**
 * Retourne l'icône d'accueil accessible du fil d'Ariane.
 *
 * @return string
 */
function nanoboy_breadcrumb_home_icon(): string {
	return sprintf(
		'<span class="screen-reader-text">%1$s</span>%2$s',
		esc_html( get_bloginfo( 'name' ) ),
		nanoboy_breadcrumb_icon( 'home', 'breadcrumb__icon breadcrumb__icon--home' )
	);
}

/**
 * Retourne le chevron séparateur du fil d'Ariane.
 *
 * @return string
 */
function nanoboy_breadcrumb_separator_icon(): string {
	return nanoboy_breadcrumb_icon( 'chevron-right', 'breadcrumb__icon breadcrumb__icon--separator' );
}

/**
 * Vérifie si une URL correspond à la page d'accueil.
 *
 * @param string $url URL à comparer.
 * @return bool
 */
function nanoboy_breadcrumb_is_home_url( string $url ): bool {
	return untrailingslashit( $url ) === untrailingslashit( home_url( '/' ) );
}

/**
 * Remplace le lien d'accueil généré par Yoast par l'icône du thème.
 *
 * @param string              $link       Balisage du lien généré.
 * @param array<string,mixed> $breadcrumb Données du niveau courant.
 * @return string
 */
function nanoboy_yoast_breadcrumb_home_icon( string $link, array $breadcrumb ): string {
	$url = $breadcrumb['url'] ?? '';

	if ( ! is_string( $url ) || ! nanoboy_breadcrumb_is_home_url( $url ) ) {
		return $link;
	}

	$home_link = preg_replace_callback(
		'/(<a\b[^>]*>).*?(<\/a>)/is',
		static fn( array $matches ): string => $matches[1] . nanoboy_breadcrumb_home_icon() . $matches[2],
		$link,
		1
	);

	return is_string( $home_link ) ? $home_link : $link;
}
add_filter( 'wpseo_breadcrumb_single_link', 'nanoboy_yoast_breadcrumb_home_icon', 10, 2 );

/**
 * Remplace le séparateur Yoast par le chevron du sprite.
 *
 * @param string $separator Séparateur configuré dans Yoast.
 * @return string
 */
function nanoboy_yoast_breadcrumb_separator_icon( string $separator ): string {
	unset( $separator );

	return nanoboy_breadcrumb_separator_icon();
}
add_filter( 'wpseo_breadcrumb_separator', 'nanoboy_yoast_breadcrumb_separator_icon' );

/**
 * Harmonise le HTML du fil d'Ariane Rank Math avec les icônes du thème.
 *
 * @param string              $html   Balisage du fil d'Ariane.
 * @param array<int,mixed>    $crumbs Niveaux générés par Rank Math.
 * @return string
 */
function nanoboy_rank_math_breadcrumb_icons( string $html, array $crumbs ): string {
	$home_url = $crumbs[0][1] ?? '';

	if ( is_string( $home_url ) && nanoboy_breadcrumb_is_home_url( $home_url ) ) {
		$home_html = preg_replace_callback(
			'/(<a\b[^>]*>).*?(<\/a>)/is',
			static fn( array $matches ): string => $matches[1] . nanoboy_breadcrumb_home_icon() . $matches[2],
			$html,
			1
		);

		if ( is_string( $home_html ) ) {
			$html = $home_html;
		}
	}

	$with_chevrons = preg_replace_callback(
		'/(<span\b[^>]*class=(["\'])[^"\']*\bseparator\b[^"\']*\2[^>]*>).*?(<\/span>)/is',
		static fn( array $matches ): string => $matches[1] . nanoboy_breadcrumb_separator_icon() . $matches[3],
		$html
	);

	return is_string( $with_chevrons ) ? $with_chevrons : $html;
}
add_filter( 'rank_math/frontend/breadcrumb/html', 'nanoboy_rank_math_breadcrumb_icons', 10, 2 );

/**
 * Affiche le fil d'Ariane du plugin SEO actif, avec un fallback maison.
 *
 * @return void
 */
function nanoboy_breadcrumb(): void {
	if ( function_exists( 'yoast_breadcrumb' ) ) {
		yoast_breadcrumb(
			'<nav class="breadcrumb breadcrumb--yoast" aria-label="' . esc_attr__( 'Breadcrumb', 'nanoboy' ) . '">',
			'</nav>'
		);
		return;
	}

	if ( function_exists( 'rank_math_the_breadcrumbs' ) ) {
		?>
		<div class="breadcrumb breadcrumb--rank-math">
			<?php rank_math_the_breadcrumbs(); ?>
		</div>
		<?php
		return;
	}

	if ( is_front_page() ) {
		return;
	}

	$items = array(
		array(
			'label' => get_bloginfo( 'name' ),
			'url'   => home_url( '/' ),
		),
	);

	if ( is_singular( 'post' ) ) {
		$categories = get_the_category();
		$category   = ! empty( $categories ) ? $categories[0] : null;

		if ( $category instanceof WP_Term ) {
			$category_url = get_category_link( $category );

			if ( ! is_wp_error( $category_url ) ) {
				$items[] = array(
					'label' => $category->name,
					'url'   => $category_url,
				);
			}
		}
	} elseif ( is_page() ) {
		$post = get_post();

		if ( $post instanceof WP_Post ) {
			foreach ( array_reverse( get_post_ancestors( $post ) ) as $ancestor_id ) {
				$items[] = array(
					'label' => get_the_title( $ancestor_id ),
					'url'   => get_permalink( $ancestor_id ),
				);
			}
		}
	}

	$items[] = array(
		'label' => nanoboy_breadcrumb_current_label(),
		'url'   => '',
	);

	$last_item_index = count( $items ) - 1;
	?>
	<nav class="breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'nanoboy' ); ?>">
		<ol class="breadcrumb__list">
			<?php foreach ( $items as $index => $item ) : ?>
				<li class="breadcrumb__item">
					<?php if ( '' !== $item['url'] ) : ?>
						<a
							class="<?php echo esc_attr( 'breadcrumb__link' . ( 0 === $index ? ' breadcrumb__link--home' : '' ) ); ?>"
							href="<?php echo esc_url( $item['url'] ); ?>"
						>
							<?php if ( 0 === $index ) : ?>
								<?php echo wp_kses( nanoboy_breadcrumb_home_icon(), nanoboy_breadcrumb_icon_allowed_html() ); ?>
							<?php else : ?>
								<?php echo esc_html( $item['label'] ); ?>
							<?php endif; ?>
						</a>
					<?php else : ?>
						<span aria-current="page"><?php echo esc_html( $item['label'] ); ?></span>
					<?php endif; ?>
					<?php if ( $index < $last_item_index ) : ?>
						<?php echo wp_kses( nanoboy_breadcrumb_separator_icon(), nanoboy_breadcrumb_icon_allowed_html() ); ?>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ol>
	</nav>
	<?php
}

/**
 * Retourne le libellé courant du fallback de fil d'Ariane.
 *
 * @return string
 */
function nanoboy_breadcrumb_current_label(): string {
	if ( is_singular() ) {
		return get_the_title();
	}

	if ( is_search() ) {
		return sprintf(
			/* translators: %s: search query. */
			__( 'Search results for: %s', 'nanoboy' ),
			get_search_query()
		);
	}

	if ( is_404() ) {
		return __( 'Page not found', 'nanoboy' );
	}

	return wp_strip_all_tags( get_the_archive_title() );
}

/**
 * Affiche la pagination d'une requête, ou de la requête principale.
 *
 * @param WP_Query|null $query Requête ciblée, ou null pour la requête principale.
 * @return void
 */
function nanoboy_pagination( ?WP_Query $query = null ): void {
	$current_page = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
	$main_query   = $GLOBALS['wp_query'] ?? null;
	$total_pages  = $query instanceof WP_Query
		? (int) $query->max_num_pages
		: ( $main_query instanceof WP_Query ? (int) $main_query->max_num_pages : 1 );

	$links = paginate_links(
		array(
			'current'   => $current_page,
			'total'     => $total_pages,
			'type'      => 'list',
			'prev_text' => esc_html__( 'Newer posts', 'nanoboy' ),
			'next_text' => esc_html__( 'Older posts', 'nanoboy' ),
		)
	);

	if ( ! is_string( $links ) || '' === $links ) {
		return;
	}
	?>
	<nav class="pagination" aria-label="<?php esc_attr_e( 'Posts pagination', 'nanoboy' ); ?>">
		<?php echo wp_kses_post( $links ); ?>
	</nav>
	<?php
}
