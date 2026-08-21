<?php
/**
 * Template Name: Biblioteca (Plantilla de Libros)
 *
 * @package FCB
 */

get_header();
?>

<div class="site-content">
	<div class="container" style="padding-top: 40px; padding-bottom: 60px;">
		<header class="page-header text-center" style="margin-bottom: 45px;">
			<h1 class="page-title" style="margin-bottom: 12px;"><?php the_title(); ?></h1>
			<?php if ( has_excerpt() ) : ?>
				<div class="page-description" style="color: var(--fcb-muted); font-size: 1.1rem; max-width: 680px; margin: 0 auto;">
					<?php the_excerpt(); ?>
				</div>
			<?php endif; ?>
		</header>

		<div class="page-content">
			<?php the_content(); ?>

			<?php
			// Obtener categorías de libros
			$categories = get_terms( array(
				'taxonomy'   => 'categoria-libro',
				'hide_empty' => true,
			) );

			// Obtener todos los libros ordenados de más nuevo a más antiguo
			$books_query = new WP_Query( array(
				'post_type'      => 'libro',
				'posts_per_page' => -1,
				'post_status'    => 'publish',
				'orderby'        => 'date',
				'order'          => 'DESC',
			) );

			if ( $books_query->have_posts() ) :
				?>
				<!-- Barra de Controles Biblioteca (Filtros, Buscar, Vista) -->
				<div class="library-controls">
					<!-- Filtros por Categoría -->
					<div class="library-filters">
						<button class="lib-filter-btn active" data-category="all"><?php esc_html_e( 'Todos', 'fcb' ); ?></button>
						<?php foreach ( $categories as $cat ) : ?>
							<button class="lib-filter-btn" data-category="<?php echo esc_attr( $cat->slug ); ?>">
								<?php echo esc_html( $cat->name ); ?>
							</button>
						<?php endforeach; ?>
					</div>

					<!-- Búsqueda y Vista -->
					<div class="library-search-view">
						<div class="library-search-wrapper">
							<i data-lucide="search" class="search-icon"></i>
							<input type="text" id="library-search" placeholder="<?php esc_attr_e( 'Buscar por título...', 'fcb' ); ?>" />
						</div>
						
						<div class="library-view-toggle">
							<button id="view-grid-btn" class="view-btn active" title="<?php esc_attr_e( 'Vista cuadrícula', 'fcb' ); ?>">
								<i data-lucide="layout-grid"></i>
							</button>
							<button id="view-table-btn" class="view-btn" title="<?php esc_attr_e( 'Vista tabla', 'fcb' ); ?>">
								<i data-lucide="list"></i>
							</button>
						</div>
					</div>
				</div>

				<!-- Contenedor de Libros -->
				<div id="library-books-container" class="view-grid">
					<!-- Vista Cuadrícula (Grid) -->
					<div class="library-grid-view">
						<div class="cards-grid cards-grid--4">
							<?php
							while ( $books_query->have_posts() ) :
								$books_query->the_post();
								$pdf_url   = get_post_meta( get_the_ID(), '_fcb_libro_pdf', true );
								$ebook_url = get_post_meta( get_the_ID(), '_fcb_libro_ebook', true );
								$edition   = get_post_meta( get_the_ID(), '_fcb_libro_edition', true );
								$book_cats = wp_get_post_terms( get_the_ID(), 'categoria-libro', array( 'fields' => 'slugs' ) );
								$cats_data = ! empty( $book_cats ) ? implode( ' ', $book_cats ) : '';
								?>
								<article class="card card--book library-book-item" data-title="<?php echo esc_attr( strtolower( get_the_title() ) ); ?>" data-cats="<?php echo esc_attr( $cats_data ); ?>">
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
											<?php if ( $ebook_url ) : ?>
												<a href="<?php echo esc_url( $ebook_url ); ?>" class="btn-book-action btn-book-action--read" target="_blank">
													<i data-lucide="book-open"></i> <?php esc_html_e( 'Ver', 'fcb' ); ?>
												</a>
											<?php elseif ( $pdf_url ) : ?>
												<a href="<?php echo esc_url( $pdf_url ); ?>" class="btn-book-action btn-book-action--read" target="_blank">
													<i data-lucide="book-open"></i> <?php esc_html_e( 'Ver', 'fcb' ); ?>
												</a>
											<?php endif; ?>

											<?php if ( $pdf_url ) : ?>
												<a href="<?php echo esc_url( $pdf_url ); ?>" class="btn-book-action btn-book-action--download" download>
													<i data-lucide="download"></i> <?php esc_html_e( 'PDF', 'fcb' ); ?>
												</a>
											<?php endif; ?>
										</div>
									</div>
								</article>
							<?php endwhile; ?>
						</div>
					</div>

					<!-- Vista Tabla (Table) -->
					<div class="library-table-view">
						<div class="library-table-responsive">
							<table class="library-books-table">
								<thead>
									<tr>
										<th><?php esc_html_e( 'Portada', 'fcb' ); ?></th>
										<th><?php esc_html_e( 'Título', 'fcb' ); ?></th>
										<th><?php esc_html_e( 'Edición / Año', 'fcb' ); ?></th>
										<th class="text-right"><?php esc_html_e( 'Acciones', 'fcb' ); ?></th>
									</tr>
								</thead>
								<tbody>
									<?php
									$books_query->rewind_posts();
									while ( $books_query->have_posts() ) :
										$books_query->the_post();
										$pdf_url   = get_post_meta( get_the_ID(), '_fcb_libro_pdf', true );
										$ebook_url = get_post_meta( get_the_ID(), '_fcb_libro_ebook', true );
										$edition   = get_post_meta( get_the_ID(), '_fcb_libro_edition', true );
										$book_cats = wp_get_post_terms( get_the_ID(), 'categoria-libro', array( 'fields' => 'slugs' ) );
										$cats_data = ! empty( $book_cats ) ? implode( ' ', $book_cats ) : '';
										?>
										<tr class="library-book-item" data-title="<?php echo esc_attr( strtolower( get_the_title() ) ); ?>" data-cats="<?php echo esc_attr( $cats_data ); ?>">
											<td>
												<div class="table-book-cover">
													<?php
													$cover_url = get_post_meta( get_the_ID(), '_fcb_libro_cover_url', true );
													if ( has_post_thumbnail() ) :
														the_post_thumbnail( 'thumbnail' );
													elseif ( $cover_url ) :
														?>
														<img src="<?php echo esc_url( $cover_url ); ?>" alt="<?php the_title_attribute(); ?>" />
														<?php
													endif;
													?>
												</div>
											</td>
											<td>
												<strong class="table-book-title"><?php the_title(); ?></strong>
											</td>
											<td>
												<span class="table-book-edition"><?php echo esc_html( $edition ? $edition : '—' ); ?></span>
											</td>
											<td>
												<div class="card-book-actions card-book-actions--table text-right">
													<?php if ( $ebook_url ) : ?>
														<a href="<?php echo esc_url( $ebook_url ); ?>" class="btn-book-action btn-book-action--read" target="_blank">
															<i data-lucide="book-open"></i> <?php esc_html_e( 'Ver', 'fcb' ); ?>
														</a>
													<?php elseif ( $pdf_url ) : ?>
														<a href="<?php echo esc_url( $pdf_url ); ?>" class="btn-book-action btn-book-action--read" target="_blank">
															<i data-lucide="book-open"></i> <?php esc_html_e( 'Ver', 'fcb' ); ?>
														</a>
													<?php endif; ?>

													<?php if ( $pdf_url ) : ?>
														<a href="<?php echo esc_url( $pdf_url ); ?>" class="btn-book-action btn-book-action--download" download>
															<i data-lucide="download"></i> <?php esc_html_e( 'PDF', 'fcb' ); ?>
														</a>
													<?php endif; ?>
												</div>
											</td>
										</tr>
									<?php endwhile; ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>

				<!-- Script de control interactivo -->
				<script>
				document.addEventListener('DOMContentLoaded', function () {
					const searchInput = document.getElementById('library-search');
					const filterBtns = document.querySelectorAll('.lib-filter-btn');
					const viewGridBtn = document.getElementById('view-grid-btn');
					const viewTableBtn = document.getElementById('view-table-btn');
					const container = document.getElementById('library-books-container');
					
					let currentFilter = 'all';
					let searchQuery = '';

					// Búsqueda
					if (searchInput) {
						searchInput.addEventListener('input', function (e) {
							searchQuery = e.target.value.toLowerCase().trim();
							applyFilters();
						});
					}

					// Filtros de categoría
					filterBtns.forEach(btn => {
						btn.addEventListener('click', function () {
							filterBtns.forEach(b => b.classList.remove('active'));
							btn.classList.add('active');
							currentFilter = btn.getAttribute('data-category');
							applyFilters();
						});
					});

					// Toggles de vista
					if (viewGridBtn && viewTableBtn && container) {
						viewGridBtn.addEventListener('click', function () {
							viewGridBtn.classList.add('active');
							viewTableBtn.classList.remove('active');
							container.className = 'view-grid';
						});

						viewTableBtn.addEventListener('click', function () {
							viewTableBtn.classList.add('active');
							viewGridBtn.classList.remove('active');
							container.className = 'view-table';
						});
					}

					function applyFilters() {
						const items = document.querySelectorAll('.library-book-item');
						items.forEach(item => {
							const title = item.getAttribute('data-title');
							const cats = item.getAttribute('data-cats').split(' ');
							
							const matchesSearch = title.includes(searchQuery);
							const matchesCat = currentFilter === 'all' || cats.includes(currentFilter);

							if (matchesSearch && matchesCat) {
								item.style.display = '';
							} else {
								item.style.display = 'none';
							}
						});
					}
					
					// Re-inicializar Lucide icons en caso necesario
					if (typeof lucide !== 'undefined') {
						lucide.createIcons();
					}
				});
				</script>
				<?php
			else :
				?>
				<p class="text-center py-8" style="padding: 40px 0; color: var(--fcb-muted);"><?php esc_html_e( 'No hay libros disponibles en este momento.', 'fcb' ); ?></p>
				<?php
			endif;
			wp_reset_postdata();
			?>
		</div>
	</div>
</div>

<?php
get_footer();
