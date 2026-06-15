<?php
/**
 * NanoBoy — Widget articles récents.
 *
 * @package NanoBoy
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Affiche les articles publiés les plus récents.
 */
final class NanoBoy_Recent_Posts_Widget extends WP_Widget {

	/**
	 * Initialise le widget.
	 */
	public function __construct() {
		parent::__construct(
			'nanoboy-recent-posts',
			esc_html__( 'NanoBoy: Recent posts', 'nanoboy' ),
			array(
				'description' => esc_html__( 'Displays the most recently published posts.', 'nanoboy' ),
			)
		);
	}

	/**
	 * Rend le widget.
	 *
	 * @param array<string, string> $args     Arguments d'affichage WordPress.
	 * @param array<string, mixed>  $instance Réglages enregistrés.
	 * @return void
	 */
	public function widget( $args, $instance ): void {
		$title     = apply_filters( 'widget_title', $instance['title'] ?? '', $instance, $this->id_base );
		$post_count = nanoboy_widget_post_count( $instance['post_count'] ?? 5 );
		$show_date = ! empty( $instance['show_date'] );
		$query     = new WP_Query(
			array(
				'ignore_sticky_posts'    => true,
				'no_found_rows'          => true,
				'post_status'            => 'publish',
				'posts_per_page'         => $post_count,
				'has_password'           => false,
				'update_post_term_cache' => false,
			)
		);

		if ( ! $query->have_posts() ) {
			return;
		}

		echo wp_kses_post( $args['before_widget'] );
		if ( '' !== $title ) {
			echo wp_kses_post( $args['before_title'] . esc_html( $title ) . $args['after_title'] );
		}
		nanoboy_widget_posts_list( $query, $show_date );
		echo wp_kses_post( $args['after_widget'] );
	}

	/**
	 * Nettoie les réglages.
	 *
	 * @param array<string, mixed> $new_instance Nouvelles valeurs.
	 * @param array<string, mixed> $old_instance Anciennes valeurs.
	 * @return array<string, mixed>
	 */
	public function update( $new_instance, $old_instance ): array {
		return array(
			'title'      => sanitize_text_field( $new_instance['title'] ?? '' ),
			'post_count' => nanoboy_widget_post_count( $new_instance['post_count'] ?? 5 ),
			'show_date'  => ! empty( $new_instance['show_date'] ),
		);
	}

	/**
	 * Rend le formulaire d'administration.
	 *
	 * @param array<string, mixed> $instance Réglages enregistrés.
	 * @return void
	 */
	public function form( $instance ): void {
		$title      = $instance['title'] ?? esc_html__( 'Recent posts', 'nanoboy' );
		$post_count = nanoboy_widget_post_count( $instance['post_count'] ?? 5 );
		$show_date  = ! empty( $instance['show_date'] );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'nanoboy' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'post_count' ) ); ?>"><?php esc_html_e( 'Number of posts:', 'nanoboy' ); ?></label>
			<input class="tiny-text" id="<?php echo esc_attr( $this->get_field_id( 'post_count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post_count' ) ); ?>" type="number" min="1" max="10" value="<?php echo esc_attr( (string) $post_count ); ?>">
		</p>
		<p>
			<input class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_date' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_date' ) ); ?>" type="checkbox" <?php checked( $show_date ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_date' ) ); ?>"><?php esc_html_e( 'Display post date', 'nanoboy' ); ?></label>
		</p>
		<?php
	}
}
