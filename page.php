<?php
/**
 * Página individual.
 *
 * @package fcb
 */

get_header();
?>

<div class="container blog-list">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<article <?php post_class( 'entry' ); ?>>
			<header class="entry-header">
				<h1 class="entry-title"><?php the_title(); ?></h1>
			</header>

			<?php if ( has_post_thumbnail() ) : ?>
				<div class="entry-thumb"><?php the_post_thumbnail( 'medium_large' ); ?></div>
			<?php endif; ?>

			<div class="entry-content">
				<?php
				the_content();
				wp_link_pages();
				?>
			</div>
		</article>
	<?php endwhile; ?>
</div>

<?php
get_footer();
