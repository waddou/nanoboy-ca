<?php
/**
 * NanoBoy — Widget articles populaires.
 *
 * @package NanoBoy
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Affiche une sélection manuelle de 3 à 6 articles, choisis par leur ID,
 * dans l'ordre saisi. Aucun compteur de vues ni tracking.
 */
final class NanoBoy_Popular_Posts_Widget extends WP_Widget {

	private const MIN_POSTS = 3;
	private const MAX_POSTS = 6;

	/**
	 * Initialise le widget.
	 */
	public function __construct() {
		parent::__construct(
			'nanoboy-popular-posts',
			esc_html__( 'NanoBoy: Popular posts', 'nanoboy' ),
			array(
				'description' => esc_html__( 'Displays a hand-picked selection of 3 to 6 posts.', 'nanoboy' ),
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
		$title    = (string) apply_filters( 'widget_title', $instance['title'] ?? $this->default_title(), $instance, $this->id_base );
		$post_ids = array_slice( $this->parse_post_ids( $instance['post_ids'] ?? '' ), 0, self::MAX_POSTS );

		if ( count( $post_ids ) < self::MIN_POSTS ) {
			return;
		}

		$query = new WP_Query(
			array(
				'post__in'               => $post_ids,
				'post_type'              => 'post',
				'orderby'                => 'post__in',
				'posts_per_page'         => self::MAX_POSTS,
				'ignore_sticky_posts'    => true,
				'no_found_rows'          => true,
				'post_status'            => 'publish',
				'has_password'           => false,
				'update_post_term_cache' => false,
			)
		);

		if ( $query->post_count < self::MIN_POSTS ) {
			return;
		}

		echo wp_kses_post( $args['before_widget'] );
		if ( '' !== $title ) {
			echo wp_kses_post( $args['before_title'] . esc_html( $title ) . $args['after_title'] );
		}
		nanoboy_widget_posts_list( $query, false, false );
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
		$post_ids = array_slice( $this->parse_post_ids( $new_instance['post_ids'] ?? '' ), 0, self::MAX_POSTS );
		$title     = is_string( $new_instance['title'] ?? null )
			? sanitize_text_field( wp_unslash( $new_instance['title'] ) )
			: '';

		return array(
			'title'    => $title,
			'post_ids' => implode( ', ', $post_ids ),
		);
	}

	/**
	 * Rend le formulaire d'administration.
	 *
	 * @param array<string, mixed> $instance Réglages enregistrés.
	 * @return void
	 */
	public function form( $instance ): void {
		$title           = $instance['title'] ?? $this->default_title();
		$post_ids        = array_slice( $this->parse_post_ids( $instance['post_ids'] ?? '' ), 0, self::MAX_POSTS );
		$published_posts = $this->get_published_posts( $post_ids );
		$published_count = count( $published_posts );
		$help_id         = $this->get_field_id( 'post_ids_help' );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'nanoboy' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'post_ids' ) ); ?>"><?php esc_html_e( 'Posts to display:', 'nanoboy' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'post_ids' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post_ids' ) ); ?>" type="text" aria-describedby="<?php echo esc_attr( $help_id ); ?>" placeholder="12, 34, 56" value="<?php echo esc_attr( implode( ', ', $post_ids ) ); ?>">
		</p>
		<p class="description" id="<?php echo esc_attr( $help_id ); ?>">
			<?php esc_html_e( 'Enter 3 to 6 unique post IDs separated by commas, in the desired display order. Only the first 6 IDs are saved.', 'nanoboy' ); ?>
			<br>
			<?php esc_html_e( 'You can find an ID in the post editor URL, for example post=123.', 'nanoboy' ); ?>
		</p>

		<?php if ( array() !== $post_ids ) : ?>
			<p><strong><?php esc_html_e( 'Selected posts:', 'nanoboy' ); ?></strong></p>
			<ol>
				<?php foreach ( $post_ids as $post_id ) : ?>
					<?php
					$post = $published_posts[ $post_id ] ?? null;
					?>
					<li>
						<?php if ( $post instanceof WP_Post ) : ?>
							<?php echo esc_html( sprintf( '#%d — %s', $post_id, get_the_title( $post ) ) ); ?>
						<?php else : ?>
							<?php
							echo esc_html(
								sprintf(
									/* translators: %d: post ID. */
									__( '#%d — post not found or not published.', 'nanoboy' ),
									$post_id
								)
							);
							?>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ol>
		<?php endif; ?>

		<p class="description">
			<?php
			echo esc_html(
				sprintf(
					/* translators: 1: number of valid published posts, 2: maximum number of posts. */
					_n(
						'%1$d published post ready to display (maximum: %2$d).',
						'%1$d published posts ready to display (maximum: %2$d).',
						$published_count,
						'nanoboy'
					),
					$published_count,
					self::MAX_POSTS
				)
			);
			?>
		</p>

		<?php if ( $published_count < self::MIN_POSTS ) : ?>
			<p class="description">
				<strong>
				<?php
				echo esc_html(
					sprintf(
						/* translators: %d: minimum number of posts. */
						__( 'Add at least %d published posts: the widget stays hidden until this minimum is reached.', 'nanoboy' ),
						self::MIN_POSTS
					)
				);
				?>
				</strong>
			</p>
		<?php endif; ?>
		<?php
	}

	/**
	 * Retourne le titre proposé par défaut.
	 *
	 * @return string
	 */
	private function default_title(): string {
		return __( 'Popular articles to discover', 'nanoboy' );
	}

	/**
	 * Retourne les articles publiés indexés par leur ID.
	 *
	 * @param array<int, int> $post_ids IDs à vérifier.
	 * @return array<int, WP_Post>
	 */
	private function get_published_posts( array $post_ids ): array {
		$posts = array();

		foreach ( $post_ids as $post_id ) {
			$post = get_post( $post_id );

			if (
				$post instanceof WP_Post
				&& 'post' === $post->post_type
				&& 'publish' === $post->post_status
				&& '' === $post->post_password
			) {
				$posts[ $post_id ] = $post;
			}
		}

		return $posts;
	}

	/**
	 * Transforme une liste d'IDs séparés par des virgules en tableau d'entiers
	 * uniques et positifs, dans l'ordre de saisie.
	 *
	 * @param mixed $value Valeur brute.
	 * @return array<int, int>
	 */
	private function parse_post_ids( mixed $value ): array {
		if ( ! is_string( $value ) || '' === trim( $value ) ) {
			return array();
		}

		$ids = array();

		foreach ( explode( ',', wp_unslash( $value ) ) as $candidate ) {
			$candidate = trim( $candidate );

			if ( 1 !== preg_match( '/^[1-9][0-9]*$/', $candidate ) ) {
				continue;
			}

			$post_id = absint( $candidate );

			if ( ! in_array( $post_id, $ids, true ) ) {
				$ids[] = $post_id;
			}
		}

		return $ids;
	}
}
