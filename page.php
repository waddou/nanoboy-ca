<?php
/**
 * NanoBoy — Page standard.
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

				<article <?php post_class( 'entry entry--page' ); ?>>
					<header class="entry__header">
						<h1 class="entry__title"><?php echo esc_html( get_the_title() ); ?></h1>
					</header>

					<div class="entry__content">
						<?php
						the_content();

						wp_link_pages(
							array(
								'before' => '<nav class="entry__pages" aria-label="' . esc_attr__( 'Page sections', 'nanoboy' ) . '">',
								'after'  => '</nav>',
							)
						);
						?>
					</div>
				</article>

				<?php
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
