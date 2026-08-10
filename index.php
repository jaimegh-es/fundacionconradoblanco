<?php
/**
 * Listado por defecto (blog / fallback).
 *
 * @package fcb
 */

get_header();
?>

<div class="container blog-list">
	<?php if ( have_posts() ) : ?>

		<?php if ( ! is_front_page() && ! is_home() ) : ?>
			<header class="page-header">
				<h1 class="entry-title"><?php single_post_title(); ?></h1>
				<?php the_archive_description( '<p class="taxonomy-description">', '</p>' ); ?>
			</header>
		<?php endif; ?>

		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article <?php post_class( 'entry' ); ?>>
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="entry-thumb">
						<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'medium_large' ); ?></a>
					</div>
				<?php endif; ?>
				<div class="entry-body">
					<header class="entry-header">
						<h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<div class="entry-meta">
							<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
						</div>
					</header>
					<div class="entry-content">
						<?php the_excerpt(); ?>
					</div>
				</div>
			</article>
		<?php endwhile; ?>

		<?php the_posts_pagination(); ?>

	<?php else : ?>
		<p><?php esc_html_e( 'Aún no hay contenido publicado.', 'fcb' ); ?></p>
	<?php endif; ?>
</div>

<?php
get_footer();
