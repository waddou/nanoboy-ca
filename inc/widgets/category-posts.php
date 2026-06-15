<?php
/**
 * NanoBoy — Widget articles par catégorie.
 *
 * @package NanoBoy
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Affiche les articles récents d'une catégorie sélectionnée.
 */
final class NanoBoy_Category_Posts_Widget extends WP_Widget {

	/**
	 * Initialise le widget.
	 */
	public function __construct() {
		parent::__construct(
			'nanoboy-category-posts',
			esc_html__( 'NanoBoy: Category posts', 'nanoboy' ),
			array(
				'description' => esc_html__( 'Displays recent posts from a selected category.', 'nanoboy' ),
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
		$category_id = absint( $instance['category_id'] ?? 0 );
		$category    = get_category( $category_id );

		if ( ! $category_id || ! $category instanceof WP_Term ) {
			return;
		}

		$title      = apply_filters( 'widget_title', $instance['title'] ?? $category->name, $instance, $this->id_base );
		$post_count = nanoboy_widget_post_count( $instance['post_count'] ?? 5 );
		$query      = new WP_Query(
			array(
				'cat'                     => $category_id,
				'ignore_sticky_posts'     => true,
				'no_found_rows'           => true,
				'post_status'             => 'publish',
				'posts_per_page'          => $post_count,
				'has_password'            => false,
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
		nanoboy_widget_posts_list( $query );
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
			'title'       => sanitize_text_field( $new_instance['title'] ?? '' ),
			'category_id' => absint( $new_instance['category_id'] ?? 0 ),
			'post_count'  => nanoboy_widget_post_count( $new_instance['post_count'] ?? 5 ),
		);
	}

	/**
	 * Rend le formulaire d'administration.
	 *
	 * @param array<string, mixed> $instance Réglages enregistrés.
	 * @return void
	 */
	public function form( $instance ): void {
		$title       = $instance['title'] ?? '';
		$category_id = absint( $instance['category_id'] ?? 0 );
		$post_count  = nanoboy_widget_post_count( $instance['post_count'] ?? 5 );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title (optional):', 'nanoboy' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'category_id' ) ); ?>"><?php esc_html_e( 'Category:', 'nanoboy' ); ?></label>
			<?php
			wp_dropdown_categories(
				array(
					'class'             => 'widefat',
					'hide_empty'        => false,
					'id'                => $this->get_field_id( 'category_id' ),
					'name'              => $this->get_field_name( 'category_id' ),
					'selected'          => $category_id,
					'show_option_none'  => esc_html__( 'Select a category', 'nanoboy' ),
					'option_none_value' => 0,
				)
			);
			?>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'post_count' ) ); ?>"><?php esc_html_e( 'Number of posts:', 'nanoboy' ); ?></label>
			<input class="tiny-text" id="<?php echo esc_attr( $this->get_field_id( 'post_count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post_count' ) ); ?>" type="number" min="1" max="10" value="<?php echo esc_attr( (string) $post_count ); ?>">
		</p>
		<?php
	}
}
