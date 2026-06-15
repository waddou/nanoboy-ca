<?php
/**
 * NanoBoy — Article seul.
 *
 * @package NanoBoy
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<div class="container">
	<div class="layout">
		<main class="layout__main">
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<?php get_template_part( 'parts/breadcrumb' ); ?>

				<article <?php post_class( 'entry entry--post' ); ?>>
					<header class="entry__header">
						<h1 class="entry__title"><?php echo esc_html( get_the_title() ); ?></h1>
						<?php get_template_part( 'parts/post-meta' ); ?>
					</header>

					<div class="entry__content">
						<?php
						// Pub pleine largeur à hauteur figée, avant le début du texte.
						nanoboy_ad_slot( 'article_top' );

						the_content();

						wp_link_pages(
							array(
								'before' => '<nav class="entry__pages" aria-label="' . esc_attr__( 'Article pages', 'nanoboy' ) . '">',
								'after'  => '</nav>',
							)
						);
						?>
					</div>

					<?php $nanoboy_tags = get_the_tag_list( '', ', ' ); ?>
					<?php if ( is_string( $nanoboy_tags ) && '' !== $nanoboy_tags ) : ?>
						<footer class="entry__footer">
							<span class="entry__tags-label"><?php esc_html_e( 'Tags:', 'nanoboy' ); ?></span>
							<?php echo wp_kses_post( $nanoboy_tags ); ?>
						</footer>
					<?php endif; ?>
				</article>

				<?php
				get_template_part( 'parts/related-posts' );

				if ( comments_open() || get_comments_number() ) {
					comments_template();
				}
			endwhile;
			?>
		</main>

		<?php get_sidebar(); ?>
	</div>
</div>
<?php
get_footer();
