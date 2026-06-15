<?php
/**
 * NanoBoy — Liste et formulaire de commentaires.
 *
 * @package NanoBoy
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( post_password_required() ) {
	return;
}
?>
<section id="comments" class="comments-area">
	<?php if ( have_comments() ) : ?>
		<h2 class="comments-area__title">
			<?php
			printf(
				/* translators: %s: number of comments. */
				esc_html( _n( '%s comment', '%s comments', get_comments_number(), 'nanoboy' ) ),
				esc_html( number_format_i18n( get_comments_number() ) )
			);
			?>
		</h2>

		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'callback'    => 'nanoboy_comment',
					'style'       => 'ol',
					'avatar_size' => 48,
					'short_ping'  => true,
				)
			);
			?>
		</ol>

		<?php the_comments_pagination(); ?>
	<?php endif; ?>

	<?php if ( ! comments_open() && get_comments_number() ) : ?>
		<p class="comments-area__closed"><?php esc_html_e( 'Comments are closed.', 'nanoboy' ); ?></p>
	<?php endif; ?>

	<?php
	comment_form(
		array(
			'class_submit'         => 'button',
			'title_reply_before'   => '<h2 id="reply-title" class="comment-reply-title">',
			'title_reply_after'    => '</h2>',
			'comment_notes_before' => '<p class="comment-notes">' . esc_html__( 'Your email address will not be published.', 'nanoboy' ) . '</p>',
		)
	);
	?>
</section>
