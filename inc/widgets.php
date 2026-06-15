<?php
/**
 * NanoBoy — Zones de widgets et widgets personnalisés.
 *
 * @package NanoBoy
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once NANOBOY_DIR . '/inc/widgets/recent-posts.php';
require_once NANOBOY_DIR . '/inc/widgets/popular-posts.php';
require_once NANOBOY_DIR . '/inc/widgets/category-posts.php';

/**
 * Enregistre la sidebar principale et les trois colonnes du footer.
 *
 * @return void
 */
function nanoboy_register_sidebars(): void {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Main sidebar', 'nanoboy' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Widgets displayed beside the main content.', 'nanoboy' ),
			'before_widget' => '<section id="%1$s" class="sidebar__widget widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="sidebar__title">',
			'after_title'   => '</h2>',
		)
	);

	for ( $column = 1; $column <= 3; $column++ ) {
		register_sidebar(
			array(
				/* translators: %d: footer column number. */
				'name'          => sprintf( esc_html__( 'Footer column %d', 'nanoboy' ), $column ),
				'id'            => 'footer-' . $column,
				/* translators: %d: footer column number. */
				'description'   => sprintf( esc_html__( 'Widgets displayed in footer column %d.', 'nanoboy' ), $column ),
				'before_widget' => '<section id="%1$s" class="site-footer__widget widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h2 class="site-footer__title">',
				'after_title'   => '</h2>',
			)
		);
	}
}
add_action( 'widgets_init', 'nanoboy_register_sidebars' );

/**
 * Enregistre les widgets propres au thème.
 *
 * @return void
 */
function nanoboy_register_widgets(): void {
	register_widget( NanoBoy_Recent_Posts_Widget::class );
	register_widget( NanoBoy_Popular_Posts_Widget::class );
	register_widget( NanoBoy_Category_Posts_Widget::class );
}
add_action( 'widgets_init', 'nanoboy_register_widgets' );

/**
 * Rend une liste homogène d'articles dans un widget.
 *
 * La requête reçue est réinitialisée après rendu pour éviter toute fuite vers
 * la boucle principale.
 *
 * @param WP_Query $query          Requête contenant les articles à afficher.
 * @param bool     $show_date      Afficher ou non la date de publication.
 * @param bool     $show_thumbnail Afficher ou non la miniature.
 * @return void
 */
function nanoboy_widget_posts_list( WP_Query $query, bool $show_date = false, bool $show_thumbnail = true ): void {
	if ( ! $query->have_posts() ) {
		return;
	}
	?>
	<ul class="widget-posts" role="list">
		<?php
		while ( $query->have_posts() ) :
			$query->the_post();
			?>
			<li class="widget-posts__item">
				<?php if ( $show_thumbnail && has_post_thumbnail() ) : ?>
					<a class="widget-posts__media" href="<?php echo esc_url( get_permalink() ); ?>" tabindex="-1" aria-hidden="true">
						<?php
						echo wp_kses_post(
							get_the_post_thumbnail(
								get_the_ID(),
								'thumbnail',
								array(
									'class'   => 'widget-posts__image',
									'loading' => 'lazy',
									'alt'     => '',
								)
							)
						);
						?>
					</a>
				<?php endif; ?>

				<div class="widget-posts__content">
					<h3 class="widget-posts__title">
						<a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a>
					</h3>
					<?php if ( $show_date ) : ?>
						<time class="widget-posts__date" datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>">
							<?php echo esc_html( get_the_date() ); ?>
						</time>
					<?php endif; ?>
				</div>
			</li>
			<?php
		endwhile;
		?>
	</ul>
	<?php
	wp_reset_postdata();
}

/**
 * Retourne un nombre d'articles borné pour les réglages de widgets.
 *
 * @param mixed $value Valeur brute.
 * @return int
 */
function nanoboy_widget_post_count( mixed $value ): int {
	return min( 10, max( 1, absint( $value ) ) );
}
