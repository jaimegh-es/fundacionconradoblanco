<?php
/**
 * Entrada individual (noticias y convocatorias).
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
				<div class="entry-meta">
					<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
				</div>
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

			<div class="entry-links">
				<?php if ( 'post' === get_post_type() ) : ?>
					<a class="btn btn--white back-link" href="<?php echo esc_url( home_url( '/category/noticias/' ) ); ?>">&larr; <?php esc_html_e( 'Noticias', 'fcb' ); ?></a>
				<?php endif; ?>
			</div>
		</article>
	<?php endwhile; ?>
</div>

<?php
get_footer();
