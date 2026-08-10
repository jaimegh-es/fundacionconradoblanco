<?php
/**
 * Template Name: Noticias
 *
 * Página para mostrar todas las noticias (todas las entradas excepto la categoría "convocatorias").
 *
 * @package fcb
 */

get_header();
?>

<div class="container site-content-inner" style="margin-top: 40px; margin-bottom: 60px;">
	<header class="page-header" style="text-align: center; margin-bottom: 40px;">
		<h1 class="entry-title" style="font-size: clamp(2rem, 4vw, 2.8rem); font-weight: 800; color: var(--fcb-ink);"><?php esc_html_e( 'Noticias y Actualidad', 'fcb' ); ?></h1>
		<p class="taxonomy-description" style="color: var(--fcb-muted); font-size: 1.05rem; margin-top: 10px;"><?php esc_html_e( 'Toda la actualidad, novedades y actividades de la Fundación Conrado Blanco.', 'fcb' ); ?></p>
	</header>

	<div class="archive-content">
		<?php
		$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
		$exclude_cat = get_category_by_slug( 'convocatorias' );
		
		$args = array(
			'post_type'      => 'post',
			'posts_per_page' => 12,
			'paged'          => $paged,
		);
		if ( $exclude_cat ) {
			$args['category__not_in'] = array( $exclude_cat->term_id );
		}

		$noticias_query = new WP_Query( $args );

		if ( $noticias_query->have_posts() ) :
			?>
			<div class="cards-grid cards-grid--3">
				<?php
				while ( $noticias_query->have_posts() ) :
					$noticias_query->the_post();
					?>
					<article <?php post_class( 'card card--news' ); ?>>
						<?php if ( has_post_thumbnail() ) : ?>
							<div class="card-thumb">
								<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'medium_large' ); ?></a>
							</div>
						<?php endif; ?>
						<div class="card-body">
							<span class="card-date" style="font-size: 0.85rem; color: var(--fcb-muted); font-weight: 600;"><?php echo get_the_date(); ?></span>
							<h3 class="card-title" style="font-size: 1.2rem; margin: 5px 0;"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
							<p class="card-excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 20 ) ); ?></p>
							<span class="card-cta" style="font-weight: 700; color: var(--fcb-red); font-size: 0.9rem; margin-top: auto; display: inline-block;"><?php esc_html_e( 'Leer más →', 'fcb' ); ?></span>
						</div>
					</article>
				<?php endwhile; ?>
			</div>

			<div class="navigation pagination" style="margin-top: 50px; display: flex; justify-content: center; gap: 8px;">
				<?php
				echo paginate_links( array(
					'base'      => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
					'format'    => '?paged=%#%',
					'current'   => max( 1, $paged ),
					'total'     => $noticias_query->max_num_pages,
					'prev_text' => __( '« Anterior', 'fcb' ),
					'next_text' => __( 'Siguiente »', 'fcb' ),
					'type'      => 'plain',
				) );
				?>
			</div>
			<?php
			wp_reset_postdata();
		else :
			?>
			<p style="text-align: center; color: var(--fcb-muted);"><?php esc_html_e( 'No hay noticias publicadas en este momento.', 'fcb' ); ?></p>
			<?php
		endif;
		?>
	</div>
</div>

<?php
get_footer();
