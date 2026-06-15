<?php
/**
 * NanoBoy — Rendu des commentaires.
 *
 * @package NanoBoy
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Affiche un commentaire dans la liste native WordPress.
 *
 * @param WP_Comment         $comment Commentaire rendu.
 * @param array<string,mixed> $args    Arguments de wp_list_comments().
 * @param int                $depth   Profondeur du commentaire.
 * @return void
 */
function nanoboy_comment( WP_Comment $comment, array $args, int $depth ): void {
	$GLOBALS['comment'] = $comment;

	$tag = ! empty( $args['style'] ) && 'div' === $args['style'] ? 'div' : 'li';
	?>
	<<?php echo esc_attr( $tag ); ?> id="comment-<?php comment_ID(); ?>" <?php comment_class( 'comment-item' ); ?>>
		<article id="div-comment-<?php comment_ID(); ?>" class="comment-item__body">
			<header class="comment-item__header">
				<div class="comment-item__avatar">
					<?php echo wp_kses_post( get_avatar( $comment, 48, '', '', array( 'loading' => 'lazy' ) ) ); ?>
				</div>
				<div>
					<p class="comment-item__author"><?php echo wp_kses_post( get_comment_author_link( $comment ) ); ?></p>
					<a class="comment-item__permalink" href="<?php echo esc_url( get_comment_link( $comment ) ); ?>">
						<time datetime="<?php echo esc_attr( get_comment_date( DATE_W3C, $comment ) ); ?>">
							<?php echo esc_html( get_comment_date( '', $comment ) . ' ' . get_comment_time( '', false, true, $comment ) ); ?>
						</time>
					</a>
				</div>
			</header>

			<?php if ( '0' === $comment->comment_approved ) : ?>
				<p class="comment-item__moderation"><?php esc_html_e( 'Your comment is awaiting moderation.', 'nanoboy' ); ?></p>
			<?php endif; ?>

			<div class="comment-item__content">
				<?php comment_text( $comment ); ?>
			</div>

			<div class="comment-item__actions">
				<?php
				comment_reply_link(
					array_merge(
						$args,
						array(
							'add_below' => 'div-comment',
							'depth'     => $depth,
							'max_depth' => $args['max_depth'] ?? 5,
						)
					)
				);
				?>
				<?php edit_comment_link( esc_html__( 'Edit', 'nanoboy' ), '<span class="comment-item__edit">', '</span>' ); ?>
			</div>
		</article>
	<?php
}
