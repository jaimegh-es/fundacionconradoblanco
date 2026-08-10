<?php
/**
 * Portada: hero, convocatorias, noticias y enlaces a páginas.
 *
 * @package fcb
 */

get_header();

$convocatorias = fcb_get_convocatorias();
$noticias      = fcb_get_noticias();
$paginas       = fcb_get_paginas_destacadas();
$video_url     = fcb_hero_video_url();
$social        = fcb_social_links();

$btn_url = get_theme_mod( 'fcb_hero_btn_url', '' );
if ( ! $btn_url && $convocatorias->have_posts() ) {
	$convocatorias->the_post();
	$btn_url = get_permalink();
	wp_reset_postdata();
}
?>

<section class="hero">
	<?php if ( $video_url ) : ?>
		<video class="hero-video" autoplay muted loop playsinline preload="metadata">
			<source src="<?php echo esc_url( $video_url ); ?>" type="video/mp4">
		</video>
		<div class="hero-overlay" aria-hidden="true"></div>
	<?php endif; ?>

	<div class="container hero-inner">
		<div class="hero-logo">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<img src="https://hosted.inled.es/logo.fundacionconradoblanco.sf_.png" alt="<?php bloginfo( 'name' ); ?>" />
			</a>
		</div>

		<h1 class="hero-title"><?php echo esc_html( fcb_hero_title() ); ?></h1>
		<p class="hero-subtitle"><?php echo esc_html( fcb_hero_subtitle() ); ?></p>

		<?php if ( $btn_url || ! empty( $social ) ) : ?>
			<div class="hero-actions">
				<?php if ( $btn_url ) : ?>
					<a class="btn btn--red" href="<?php echo esc_url( $btn_url ); ?>"><?php echo esc_html( fcb_hero_button_text() ); ?></a>
				<?php endif; ?>

				<?php if ( ! empty( $social ) ) : ?>
					<ul class="hero-social">
						<?php foreach ( $social as $key => $net ) : ?>
							<li>
								<a href="<?php echo esc_url( $net['url'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $net['label'] ); ?>">
									<svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor" aria-hidden="true" focusable="false">
										<path d="<?php echo esc_attr( $net['path'] ); ?>"></path>
									</svg>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</section>

<?php if ( $convocatorias->have_posts() ) : ?>
	<section class="section section--alt" id="convocatorias">
		<div class="container">
			<header class="section-head">
				<p class="section-kicker"><?php esc_html_e( 'Premios de poesía', 'fcb' ); ?></p>
				<h2 class="section-title"><?php echo esc_html( get_theme_mod( 'fcb_section_concursos', 'Convocatorias' ) ); ?></h2>
			</header>

			<div class="cards-grid cards-grid--2">
				<?php
				while ( $convocatorias->have_posts() ) :
					$convocatorias->the_post();
					?>
					<article <?php post_class( 'card card--concurso' ); ?>>
						<div class="card-body">
							<h3 class="card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
							<p class="card-excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 22 ) ); ?></p>
							<span class="card-cta"><?php esc_html_e( 'Ver bases →', 'fcb' ); ?></span>
						</div>
					</article>
				<?php endwhile; ?>
			</div>
		</div>
	</section>
	<?php
	wp_reset_postdata();
endif;
?>

<?php if ( $noticias->have_posts() ) : ?>
	<section class="section" id="noticias">
		<div class="container">
			<header class="section-head section-head--row">
				<div>
					<p class="section-kicker"><?php esc_html_e( 'Actualidad', 'fcb' ); ?></p>
					<h2 class="section-title"><?php echo esc_html( get_theme_mod( 'fcb_section_noticias', 'Noticias' ) ); ?></h2>
				</div>
				<a class="text-link" href="<?php echo esc_url( home_url( '/category/noticias/' ) ); ?>"><?php esc_html_e( 'Ver todas', 'fcb' ); ?> →</a>
			</header>

			<div class="cards-grid cards-grid--3">
				<?php
				while ( $noticias->have_posts() ) :
					$noticias->the_post();
					?>
					<article <?php post_class( 'card card--news' ); ?>>
						<?php if ( has_post_thumbnail() ) : ?>
							<div class="card-thumb">
								<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'medium_large' ); ?></a>
							</div>
						<?php endif; ?>
						<div class="card-body">
							<h3 class="card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
							<p class="card-excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 20 ) ); ?></p>
							<span class="card-cta"><?php esc_html_e( 'Leer más →', 'fcb' ); ?></span>
						</div>
					</article>
				<?php endwhile; ?>
			</div>
		</div>
	</section>
	<?php
	wp_reset_postdata();
endif;
?>

<?php if ( ! empty( $paginas ) ) : ?>
	<section class="section section--alt" id="la-fundacion">
		<div class="container">
			<header class="section-head">
				<p class="section-kicker"><?php esc_html_e( 'Conócenos', 'fcb' ); ?></p>
				<h2 class="section-title"><?php echo esc_html( get_theme_mod( 'fcb_section_paginas', 'La Fundación' ) ); ?></h2>
			</header>

			<div class="cards-grid cards-grid--4">
				<?php foreach ( $paginas as $page ) : ?>
					<?php
					$excerpt  = get_the_excerpt( $page );
					$subpages = fcb_get_page_submenu_items( $page->ID );
					?>
					<article class="card card--page <?php echo ! empty( $subpages ) ? 'has-subpages' : ''; ?>">
						<div class="card-body">
							<h3 class="card-title"><a href="<?php echo esc_url( get_permalink( $page ) ); ?>"><?php echo esc_html( get_the_title( $page ) ); ?></a></h3>
							
							<?php if ( ! empty( $subpages ) ) : ?>
								<ul class="card-subpages-list">
									<?php foreach ( $subpages as $subpage ) : ?>
										<li><a href="<?php echo esc_url( $subpage->url ); ?>"><?php echo esc_html( $subpage->title ); ?></a></li>
									<?php endforeach; ?>
								</ul>
							<?php else : ?>
								<?php if ( $excerpt ) : ?>
									<p class="card-excerpt"><?php echo esc_html( wp_trim_words( $excerpt, 16 ) ); ?></p>
								<?php endif; ?>
							<?php endif; ?>

							<span class="card-cta"><?php esc_html_e( 'Explorar →', 'fcb' ); ?></span>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
<?php endif; ?>

	<?php
	// Sección de Libros en portada (si está activa en Customizer)
	$show_libros = get_theme_mod( 'fcb_section_libros_show', true );
	if ( $show_libros ) :
		$libros_query = new WP_Query( array(
			'post_type'      => 'libro',
			'posts_per_page' => 4,
			'post_status'    => 'publish',
		) );

		if ( $libros_query->have_posts() ) :
			$section_title = get_theme_mod( 'fcb_section_libros_title', __( 'Biblioteca y Publicaciones', 'fcb' ) );
			$view_all_url  = get_theme_mod( 'fcb_section_libros_url', home_url( '/biblioteca/' ) );
			?>
			<section class="section" id="libros-portada">
				<div class="container">
					<header class="section-head section-head--row">
						<div>
							<p class="section-kicker"><?php esc_html_e( 'Colecciones', 'fcb' ); ?></p>
							<h2 class="section-title"><?php echo esc_html( $section_title ); ?></h2>
						</div>
						<a class="text-link" href="<?php echo esc_url( $view_all_url ); ?>"><?php esc_html_e( 'Ver biblioteca', 'fcb' ); ?> →</a>
					</header>

					<div class="cards-grid cards-grid--4">
						<?php
						while ( $libros_query->have_posts() ) :
							$libros_query->the_post();
							$pdf_url   = get_post_meta( get_the_ID(), '_fcb_libro_pdf', true );
							$ebook_url = get_post_meta( get_the_ID(), '_fcb_libro_ebook', true );
							$edition   = get_post_meta( get_the_ID(), '_fcb_libro_edition', true );
							?>
							<article class="card card--book">
								<div class="card-book-cover">
									<?php
									$cover_url = get_post_meta( get_the_ID(), '_fcb_libro_cover_url', true );
									if ( has_post_thumbnail() ) :
										the_post_thumbnail( 'medium' );
									elseif ( $cover_url ) :
										?>
										<img src="<?php echo esc_url( $cover_url ); ?>" alt="<?php the_title_attribute(); ?>" />
										<?php
									endif;
									?>
								</div>
								<div class="card-body">
									<h3 class="card-title"><?php the_title(); ?></h3>
									<?php if ( $edition ) : ?>
										<p class="card-book-edition"><?php echo esc_html( $edition ); ?></p>
									<?php endif; ?>
									
									<div class="card-book-actions">
										<?php if ( $pdf_url ) : ?>
											<a href="<?php echo esc_url( add_query_arg( array( 'file' => urlencode( $pdf_url ), 'title' => urlencode( get_the_title() ) ), home_url( '/visor-pdf/' ) ) ); ?>" class="btn-book-action btn-book-action--read">
												<i data-lucide="book-open"></i> <?php esc_html_e( 'Ver', 'fcb' ); ?>
											</a>
											<a href="<?php echo esc_url( $pdf_url ); ?>" class="btn-book-action btn-book-action--download" download>
												<i data-lucide="download"></i> <?php esc_html_e( 'Descargar', 'fcb' ); ?>
											</a>
										<?php endif; ?>
									</div>
								</div>
							</article>
						<?php endwhile; ?>
					</div>
				</div>
			</section>
			<?php
			wp_reset_postdata();
		endif;
	endif;
	?>

<section class="section" id="comunidad">
	<div class="container">
		<header class="section-head">
			<p class="section-kicker"><?php esc_html_e( 'Redes Sociales', 'fcb' ); ?></p>
			<h2 class="section-title"><?php esc_html_e( 'Comunidad y Multimedia', 'fcb' ); ?></h2>
		</header>

		<div class="comunidad-grid">
			<div class="comunidad-column youtube-column">
				<h3 class="comunidad-subtitle">
					<svg viewBox="0 0 24 24" width="24" height="24" fill="var(--fcb-red)" style="display:inline-block; vertical-align:middle; margin-right:8px;"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
					Últimos Videos
				</h3>
				<div id="youtube-feed" class="youtube-feed-list">
					<div class="feed-loading">Cargando videos...</div>
				</div>
			</div>
			
			<div class="comunidad-column facebook-column">
				<h3 class="comunidad-subtitle">
					<svg viewBox="0 0 24 24" width="24" height="24" fill="var(--fcb-green)" style="display:inline-block; vertical-align:middle; margin-right:8px;"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
					Publicaciones de Facebook
				</h3>
				<div class="facebook-feed-container">
					<iframe src="https://www.facebook.com/plugins/page.php?href=https%3A%2F%2Fwww.facebook.com%2Ffundacionconradoblanco%2F&tabs=timeline&width=500&height=500&small_header=true&adapt_container_width=true&hide_cover=false&show_facepile=true" width="100%" height="500" style="border:none;overflow:hidden;border-radius:14px;" scrolling="no" frameborder="0" allowfullscreen="true" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"></iframe>
				</div>
			</div>
		</div>
	</div>
</section>

<script>
document.addEventListener("DOMContentLoaded", function() {
	const ytFeed = document.getElementById("youtube-feed");
	const channelId = "UCnUwBTXcKoNd55YP977t9Dg";
	const rssUrl = `https://www.youtube.com/feeds/videos.xml?channel_id=${channelId}`;
	const apiUrl = `https://api.rss2json.com/v1/api.json?rss_url=${encodeURIComponent(rssUrl)}`;

	fetch(apiUrl)
		.then(response => {
			if (!response.ok) throw new Error("Error cargando feed");
			return response.json();
		})
		.then(data => {
			if (data.status !== "ok" || !data.items || data.items.length === 0) {
				throw new Error("Formato inválido");
			}
			ytFeed.innerHTML = "";
			const items = data.items.slice(0, 3);
			items.forEach(item => {
				const card = document.createElement("div");
				card.className = "youtube-video-card";
				card.innerHTML = `
					<a href="${item.link}" target="_blank" rel="noopener noreferrer" class="yt-card-link">
						<div class="yt-thumb-wrapper">
							<img src="${item.thumbnail}" alt="${item.title}" class="yt-thumb" />
							<span class="yt-play-btn">▶</span>
						</div>
						<div class="yt-card-info">
							<h4 class="yt-video-title">${item.title}</h4>
							<span class="yt-video-date">${new Date(item.pubDate).toLocaleDateString()}</span>
						</div>
					</a>
				`;
				ytFeed.appendChild(card);
			});
		})
		.catch(error => {
			console.error("Error fetching YouTube feed:", error);
			ytFeed.innerHTML = `
				<div class="feed-error">
					<p>No se pudieron cargar los últimos videos automáticamente.</p>
					<a class="btn btn--red" href="https://www.youtube.com/@fundacionconradoblanco" target="_blank" rel="noopener noreferrer">Ir al canal de YouTube</a>
				</div>
			`;
		});
});
</script>

<?php
get_footer();
