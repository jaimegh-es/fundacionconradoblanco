<?php
/**
 * Fundación Conrado Blanco - funciones del tema.
 *
 * @package fcb
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'FCB_VERSION', '1.0.0' );

/**
 * Configuración base del tema.
 */
function fcb_setup() {
	load_theme_textdomain( 'fcb', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 110,
			'width'       => 220,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
	);

	register_nav_menus(
		array(
			'primary' => __( 'Menú principal', 'fcb' ),
		)
	);
}
add_action( 'after_setup_theme', 'fcb_setup' );

/**
 * Estilos y scripts.
 */
function fcb_assets() {
	wp_enqueue_style( 'fcb-fonts', 'https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700;800&display=swap', array(), null );
	wp_enqueue_style( 'fcb-style', get_stylesheet_uri(), array( 'fcb-fonts' ), filemtime( get_stylesheet_directory() . '/style.css' ) );
	wp_enqueue_script( 'lucide-icons', 'https://unpkg.com/lucide@latest', array(), null, true );
	wp_enqueue_script( 'fcb-nav', get_template_directory_uri() . '/assets/js/nav.js', array( 'lucide-icons' ), filemtime( get_stylesheet_directory() . '/assets/js/nav.js' ), true );
}
add_action( 'wp_enqueue_scripts', 'fcb_assets' );

/**
 * Marca HTML con clase .js antes del primer paint para que el CSS solo oculte
 * estados de animación cuando hay JavaScript disponible (evita FOUC sin JS).
 */
function fcb_js_flag() {
	echo '<script>document.documentElement.classList.add("js");</script>';
}
add_action( 'wp_head', 'fcb_js_flag', 1 );

/**
 * Personalizador: textos de portada, video y redes sociales.
 */
function fcb_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'fcb_home',
		array(
			'title'    => __( 'Portada', 'fcb' ),
			'priority' => 30,
		)
	);

	$wp_customize->add_section(
		'fcb_social',
		array(
			'title'    => __( 'Redes sociales', 'fcb' ),
			'priority' => 31,
		)
	);

	$text_fields = array(
		'fcb_hero_title'        => __( 'Título del hero', 'fcb' ),
		'fcb_hero_subtitle'     => __( 'Subtítulo del hero', 'fcb' ),
		'fcb_hero_btn_text'     => __( 'Texto del botón del hero', 'fcb' ),
		'fcb_hero_btn_url'      => __( 'URL del botón del hero', 'fcb' ),
		'fcb_section_concursos' => __( 'Título sección convocatorias', 'fcb' ),
		'fcb_section_noticias'  => __( 'Título sección noticias', 'fcb' ),
		'fcb_section_paginas'   => __( 'Título sección páginas', 'fcb' ),
	);

	foreach ( $text_fields as $id => $label ) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => '',
				'sanitize_callback' => 'textarea' === $label ? 'wp_kses_post' : 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			$id,
			array(
				'label'   => $label,
				'section' => 'fcb_home',
			)
		);
	}

	$wp_customize->add_setting(
		'fcb_hero_video',
		array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		)
	);
	$wp_customize->add_control(
		'fcb_hero_video',
		array(
			'label'       => __( 'URL del video de fondo del hero', 'fcb' ),
			'description' => __( 'Si se deja vacío se usa assets/video.mp4 del tema.', 'fcb' ),
			'section'     => 'fcb_home',
			'type'        => 'url',
		)
	);

	// Ajustes para la sección de Libros en portada
	$wp_customize->add_setting(
		'fcb_section_libros_show',
		array(
			'default'           => true,
			'sanitize_callback' => 'fcb_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'fcb_section_libros_show',
		array(
			'label'    => __( 'Mostrar sección de libros en portada', 'fcb' ),
			'section'  => 'fcb_home',
			'type'     => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'fcb_section_libros_title',
		array(
			'default'           => 'Biblioteca y Publicaciones',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'fcb_section_libros_title',
		array(
			'label'    => __( 'Título de la sección de libros', 'fcb' ),
			'section'  => 'fcb_home',
			'type'     => 'text',
		)
	);

	$wp_customize->add_setting(
		'fcb_section_libros_url',
		array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		)
	);
	$wp_customize->add_control(
		'fcb_section_libros_url',
		array(
			'label'    => __( 'URL de destino para ver todos los libros', 'fcb' ),
			'section'  => 'fcb_home',
			'type'     => 'url',
		)
	);

	$social_fields = array(
		'fcb_social_facebook'  => __( 'Facebook', 'fcb' ),
		'fcb_social_instagram' => __( 'Instagram', 'fcb' ),
		'fcb_social_youtube'   => __( 'YouTube', 'fcb' ),
		'fcb_social_x'         => __( 'X (Twitter)', 'fcb' ),
	);

	foreach ( $social_fields as $id => $label ) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => '',
				'sanitize_callback' => 'esc_url_raw',
			)
		);
		$wp_customize->add_control(
			$id,
			array(
				'label'   => $label,
				'section' => 'fcb_social',
				'type'    => 'url',
			)
		);
	}

	// Sección de Actualización de GitHub
	$wp_customize->add_section( 'fcb_github_update_section', array(
		'title'    => __( 'Actualizaciones del Tema', 'fcb' ),
		'priority' => 200,
	) );

	// Token de GitHub (opcional si es privado)
	$wp_customize->add_setting( 'fcb_github_token', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'fcb_github_token', array(
		'label'       => __( 'Token de Acceso Personal (GitHub)', 'fcb' ),
		'description' => __( 'Requerido solo si el repositorio git es privado.', 'fcb' ),
		'section'     => 'fcb_github_update_section',
		'type'        => 'password',
	) );

	// Botón de control personalizado para actualizar
	$wp_customize->add_setting( 'fcb_github_trigger_update', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( new FCB_Customize_Update_Control( $wp_customize, 'fcb_github_trigger_update', array(
		'section'  => 'fcb_github_update_section',
		'settings' => 'fcb_github_trigger_update',
	) ) );
}
add_action( 'customize_register', 'fcb_customize_register' );

/**
 * Extensión del resumen.
 */
function fcb_excerpt_length( $length ) {
	return 24;
}
add_filter( 'excerpt_length', 'fcb_excerpt_length' );

/**
 * Texto del hero con fallbacks al contenido del sitio.
 */
function fcb_hero_title() {
	$title = get_theme_mod( 'fcb_hero_title', '' );
	return $title ? $title : get_bloginfo( 'name' );
}

function fcb_hero_subtitle() {
	$subtitle = get_theme_mod( 'fcb_hero_subtitle', '' );
	return $subtitle ? $subtitle : get_bloginfo( 'description' );
}

function fcb_hero_button_text() {
	$text = get_theme_mod( 'fcb_hero_btn_text', '' );
	return $text ? $text : __( 'Ver convocatorias', 'fcb' );
}

/**
 * Divide un texto en palabras envueltas para la animación de entrada
 * (cada palabra sube con desenfoque de forma escalonada vía --wi).
 */
function fcb_split_words( $text ) {
	$words = preg_split( '/\s+/', trim( (string) $text ) );
	$words = array_values( array_filter( $words ) );

	if ( empty( $words ) ) {
		return '';
	}

	$html = '';
	foreach ( $words as $i => $word ) {
		$html .= '<span class="word"><span class="word-inner" style="--wi:' . (int) $i . '">' . esc_html( $word ) . '</span></span>';
		if ( $i < count( $words ) - 1 ) {
			$html .= ' ';
		}
	}

	return $html;
}

/**
 * Video de fondo del hero: ajuste personalizado o assets/video.mp4 del tema.
 */
function fcb_hero_video_url() {
	$custom = get_theme_mod( 'fcb_hero_video', '' );
	if ( $custom ) {
		return $custom;
	}

	$file = get_template_directory() . '/assets/video.mp4';
	if ( file_exists( $file ) ) {
		return get_template_directory_uri() . '/assets/video.mp4';
	}

	return '';
}

/**
 * Convocatorias: páginas específicas de los premios Conrado y Charo.
 */
function fcb_get_convocatorias() {
	$known_slugs = array(
		'premio-nacional-de-poesia-conrado-blanco-leon',
		'premio-nacional-de-poesia-infantil-charo-gonzalez',
	);

	$post_ids = array();

	$locations = get_nav_menu_locations();
	$menu_id   = isset( $locations['primary'] ) ? (int) $locations['primary'] : 0;

	if ( $menu_id ) {
		$menu_items = wp_get_nav_menu_items( $menu_id );
		if ( ! empty( $menu_items ) ) {
			// Encontrar el elemento de menú padre "Convocatorias" o "Concursos"
			$parent_id = 0;
			foreach ( $menu_items as $item ) {
				if ( strcasecmp( $item->title, 'Convocatorias' ) === 0 || strcasecmp( $item->title, 'Concursos' ) === 0 ) {
					$parent_id = $item->ID;
					break;
				}
			}

			if ( $parent_id ) {
				// Buscar elementos hijos en el menú que apunten a posts o páginas
				foreach ( $menu_items as $item ) {
					if ( (int) $item->menu_item_parent === $parent_id && 'post_type' === $item->type ) {
						$post_ids[] = (int) $item->object_id;
					}
				}
			}
		}
	}

	// Asegurar siempre los premios conocidos por slug (sin duplicados)
	$known = new WP_Query(
		array(
			'post_type'      => array( 'post', 'page' ),
			'post_status'    => 'publish',
			'post_name__in'  => $known_slugs,
			'orderby'        => 'post_name__in',
			'posts_per_page' => count( $known_slugs ),
			'no_found_rows'  => true,
		)
	);

	if ( $known->have_posts() ) {
		foreach ( $known->posts as $known_post ) {
			if ( ! in_array( (int) $known_post->ID, $post_ids, true ) ) {
				$post_ids[] = (int) $known_post->ID;
			}
		}
	}

	if ( empty( $post_ids ) ) {
		return $known;
	}

	return new WP_Query(
		array(
			'post_type'      => array( 'post', 'page' ),
			'post_status'    => 'publish',
			'post__in'       => $post_ids,
			'orderby'        => 'post__in',
			'posts_per_page' => count( $post_ids ),
			'no_found_rows'  => true,
		)
	);
}

function fcb_get_noticias() {
	$exclude_cat = get_category_by_slug( 'convocatorias' );
	$args = array(
		'posts_per_page'   => absint( get_theme_mod( 'fcb_noticias_count', 6 ) ),
		'no_found_rows'    => true,
	);
	if ( $exclude_cat ) {
		$args['category__not_in'] = array( $exclude_cat->term_id );
	}
	return new WP_Query( $args );
}

/**
 * Páginas principales para la sección de tarjetas: elementos de nivel superior
 * del menú primario de tipo página. Si no hay suficientes, busca por slug.
 */
function fcb_get_paginas_destacadas() {
	$items = array();
	$locations = get_nav_menu_locations();
	$menu_id   = isset( $locations['primary'] ) ? (int) $locations['primary'] : 0;
	if ( $menu_id ) {
		$items = wp_get_nav_menu_items( $menu_id );
	}

	$paginas = array();
	if ( ! empty( $items ) ) {
		foreach ( $items as $item ) {
			if ( (int) $item->menu_item_parent === 0 && 'page' === $item->object ) {
				$page = get_post( $item->object_id );
				if ( $page && 'publish' === $page->post_status ) {
					$paginas[] = $page;
				}
			}
		}
	}

	if ( count( $paginas ) < 3 ) {
		$slugs = array( 'la-fundacion', 'publicaciones', 'biblioteca', 'contacto' );
		$q     = new WP_Query(
			array(
				'post_type'           => 'page',
				'post_status'         => 'publish',
				'post_name__in'       => $slugs,
				'posts_per_page'      => 6,
				'orderby'             => 'post_name__in',
				'no_found_rows'       => true,
				'ignore_sticky_posts' => true,
			)
		);
		if ( $q->have_posts() ) {
			$paginas = $q->posts;
		}
	}

	return $paginas;
}

/**
 * Nombre de la primera categoría del post.
 */
function fcb_card_category( $post_id = null ) {
	$cats = get_the_category( $post_id );
	if ( empty( $cats ) ) {
		return '';
	}
	return $cats[0]->name;
}

/**
 * Redes sociales configuradas desde el personalizador.
 */
function fcb_social_links() {
	$networks = array(
		'facebook' => array(
			'label' => __( 'Facebook', 'fcb' ),
			'mod'   => 'fcb_social_facebook',
			'path'  => 'M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z',
		),
		'instagram' => array(
			'label' => __( 'Instagram', 'fcb' ),
			'mod'   => 'fcb_social_instagram',
			'path'  => 'M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881z',
		),
		'youtube' => array(
			'label' => __( 'YouTube', 'fcb' ),
			'mod'   => 'fcb_social_youtube',
			'path'  => 'M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z',
		),
		'x' => array(
			'label' => __( 'X (Twitter)', 'fcb' ),
			'mod'   => 'fcb_social_x',
			'path'  => 'M18.901 1.153h3.68l-8.04 9.19L24 22.846h-7.406l-5.8-7.584-6.638 7.584H.474l8.6-9.83L0 1.154h7.594l5.243 6.932ZM17.61 20.644h2.039L6.486 3.24H4.298Z',
		),
	);

	$links = array();
	foreach ( $networks as $key => $network ) {
		$url = get_theme_mod( $network['mod'], '' );
		if ( $url ) {
			$links[ $key ] = $network;
			$links[ $key ]['url'] = $url;
		}
	}

	return $links;
}



/**
 * Obtener subpáginas (elementos hijos del menú) asociadas a una página padre específica.
 */
function fcb_get_page_submenu_items( $page_id ) {
	$locations = get_nav_menu_locations();
	$menu_id   = isset( $locations['primary'] ) ? (int) $locations['primary'] : 0;
	if ( ! $menu_id ) {
		return array();
	}

	$items = wp_get_nav_menu_items( $menu_id );
	if ( empty( $items ) ) {
		return array();
	}

	// Buscar el ID del elemento del menú que corresponde a nuestro page_id
	$parent_db_id = 0;
	foreach ( $items as $item ) {
		if ( 'page' === $item->object && (int) $item->object_id === (int) $page_id ) {
			$parent_db_id = $item->ID;
			break;
		}
	}

	if ( ! $parent_db_id ) {
		return array();
	}

	// Buscar todos los elementos hijos del elemento de menú padre
	$children = array();
	foreach ( $items as $item ) {
		if ( (int) $item->menu_item_parent === (int) $parent_db_id ) {
			$children[] = $item;
		}
	}

	return $children;
}

/**
 * Sanitizar checkboxes del personalizador.
 */
function fcb_sanitize_checkbox( $checked ) {
	return ( ( isset( $checked ) && true === $checked ) ? true : false );
}

/**
 * Registrar Custom Post Type de Libros (Biblioteca).
 */
function fcb_register_books_cpt() {
	$labels = array(
		'name'               => _x( 'Libros', 'post type general name', 'fcb' ),
		'singular_name'      => _x( 'Libro', 'post type singular name', 'fcb' ),
		'menu_name'          => _x( 'Libros', 'admin menu', 'fcb' ),
		'name_admin_bar'     => _x( 'Libro', 'add new on admin bar', 'fcb' ),
		'add_new'            => _x( 'Añadir nuevo', 'book', 'fcb' ),
		'add_new_item'       => __( 'Añadir nuevo libro', 'fcb' ),
		'new_item'           => __( 'Nuevo libro', 'fcb' ),
		'edit_item'          => __( 'Editar libro', 'fcb' ),
		'view_item'          => __( 'Ver libro', 'fcb' ),
		'all_items'          => __( 'Todos los libros', 'fcb' ),
		'search_items'       => __( 'Buscar libros', 'fcb' ),
		'parent_item_colon'  => __( 'Libros padre:', 'fcb' ),
		'not_found'          => __( 'No se encontraron libros.', 'fcb' ),
		'not_found_in_trash' => __( 'No se encontraron libros en la papelera.', 'fcb' ),
	);

	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'libro' ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => 5,
		'menu_icon'          => 'dashicons-book-alt',
		'supports'           => array( 'title', 'thumbnail', 'excerpt' ),
		'show_in_rest'       => true,
	);

	register_post_type( 'libro', $args );

	// Registrar Taxonomía de Categorías de Libros
	register_taxonomy( 'categoria-libro', 'libro', array(
		'label'        => __( 'Categorías de Libros', 'fcb' ),
		'rewrite'      => array( 'slug' => 'categoria-libro' ),
		'hierarchical' => true,
		'show_in_rest' => true,
	) );
}
add_action( 'init', 'fcb_register_books_cpt' );

/**
 * Meta Box para detalles de Libros.
 */
function fcb_add_libro_meta_boxes() {
	add_meta_box(
		'fcb_libro_details',
		__( 'Detalles del Libro', 'fcb' ),
		'fcb_libro_meta_box_callback',
		'libro',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'fcb_add_libro_meta_boxes' );

function fcb_libro_meta_box_callback( $post ) {
	wp_nonce_field( 'fcb_save_libro_meta', 'fcb_libro_meta_nonce' );

	$pdf_url   = get_post_meta( $post->ID, '_fcb_libro_pdf', true );
	$ebook_url = get_post_meta( $post->ID, '_fcb_libro_ebook', true );
	$edition   = get_post_meta( $post->ID, '_fcb_libro_edition', true );
	$book_date = get_post_meta( $post->ID, '_fcb_libro_date', true );
	if ( empty( $book_date ) ) {
		$book_date = get_the_date( 'Y-m-d', $post->ID );
	}
	?>
	<p>
		<label for="fcb_libro_date"><strong>Fecha de Publicación del Libro (para ordenar de más nuevo a más antiguo):</strong></label><br />
		<input type="date" id="fcb_libro_date" name="fcb_libro_date" value="<?php echo esc_attr( $book_date ); ?>" class="widefat" />
		<span class="description" style="font-size:11px;">Indica el año o fecha de publicación real del libro. Los listados de la web se ordenarán por esta fecha.</span>
	</p>
	<p>
		<label for="fcb_libro_edition"><strong>Edición / Año / Categoría (Texto mostrado):</strong></label><br />
		<input type="text" id="fcb_libro_edition" name="fcb_libro_edition" value="<?php echo esc_attr( $edition ); ?>" class="widefat" placeholder="Ej: 2025 o 16ª Edición (2025)" />
	</p>
	<p>
		<label for="fcb_libro_pdf"><strong>URL del PDF (Descargar):</strong></label><br />
		<input type="url" id="fcb_libro_pdf" name="fcb_libro_pdf" value="<?php echo esc_url( $pdf_url ); ?>" class="widefat" placeholder="https://..." />
	</p>
	<p>
		<label for="fcb_libro_ebook"><strong>URL del eBook (Visualizar 3D / Enlace):</strong></label><br />
		<input type="url" id="fcb_libro_ebook" name="fcb_libro_ebook" value="<?php echo esc_url( $ebook_url ); ?>" class="widefat" placeholder="https://..." />
	</p>
	<?php
}

function fcb_save_libro_meta( $post_id ) {
	if ( ! isset( $_POST['fcb_libro_meta_nonce'] ) || ! wp_verify_nonce( $_POST['fcb_libro_meta_nonce'], 'fcb_save_libro_meta' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['fcb_libro_pdf'] ) ) {
		update_post_meta( $post_id, '_fcb_libro_pdf', esc_url_raw( $_POST['fcb_libro_pdf'] ) );
	}
	if ( isset( $_POST['fcb_libro_ebook'] ) ) {
		update_post_meta( $post_id, '_fcb_libro_ebook', esc_url_raw( $_POST['fcb_libro_ebook'] ) );
	}
	if ( isset( $_POST['fcb_libro_edition'] ) ) {
		$ed_val = sanitize_text_field( $_POST['fcb_libro_edition'] );
		update_post_meta( $post_id, '_fcb_libro_edition', $ed_val );
	}
	if ( ! empty( $_POST['fcb_libro_date'] ) ) {
		$clean_date = sanitize_text_field( $_POST['fcb_libro_date'] );
		update_post_meta( $post_id, '_fcb_libro_date', $clean_date );

		// Sincronizar post_date para que las consultas estándar de WP también ordenen fielmente
		remove_action( 'save_post', 'fcb_save_libro_meta' );
		wp_update_post( array(
			'ID'            => $post_id,
			'post_date'     => date( 'Y-m-d H:i:s', strtotime( $clean_date . ' 12:00:00' ) ),
			'post_date_gmt' => get_gmt_from_date( date( 'Y-m-d H:i:s', strtotime( $clean_date . ' 12:00:00' ) ) ),
		) );
		add_action( 'save_post', 'fcb_save_libro_meta' );
	}
}
add_action( 'save_post', 'fcb_save_libro_meta' );

/**
 * Registro de reglas de reescritura para el visor virtual de PDF.
 */
function fcb_pdf_viewer_rewrite_rule() {
	add_rewrite_rule( '^visor-pdf/?$', 'index.php?fcb_pdf_viewer=1', 'top' );
}
add_action( 'init', 'fcb_pdf_viewer_rewrite_rule' );

function fcb_pdf_viewer_query_vars( $vars ) {
	$vars[] = 'fcb_pdf_viewer';
	return $vars;
}
add_filter( 'query_vars', 'fcb_pdf_viewer_query_vars' );

/**
 * Regla de reescritura de respaldo para /files/ (solo alcanza WP si el
 * .htaccess lo permite; el hosting lo regenera, así que no es fiable). Las URLs
 * públicas usan wp-content/uploads, que sí se sirven de forma estática.
 */
function fcb_files_rewrite_rule() {
	add_rewrite_rule( '^files/(.+)$', 'index.php?fcb_file=$1', 'top' );
}
add_action( 'init', 'fcb_files_rewrite_rule' );

function fcb_files_query_vars( $vars ) {
	$vars[] = 'fcb_file';
	return $vars;
}
add_filter( 'query_vars', 'fcb_files_query_vars' );

/**
 * Las URLs de archivos usan siempre wp-content/uploads (se sirven de forma
 * estática y cacheable). Se mantiene la función como no-op para no romper
 * filtros previos de wp_get_attachment_url.
 */
function fcb_files_url( $url ) {
	return $url;
}

/**
 * No reescribe las URLs de los meta de libros: se devuelven tal cual desde la
 * BD (wp-content/uploads), que es la ruta que funciona de forma estática.
 */
function fcb_files_rewrite_metadata( $check, $object_id, $meta_key, $single ) {
	return $check;
}

if ( ! is_admin() ) {
	add_filter( 'get_post_metadata', 'fcb_files_rewrite_metadata', 10, 4 );
	add_filter( 'wp_get_attachment_url', 'fcb_files_url' );
}

/**
 * Sirve los archivos de /files/ desde wp-content/uploads con soporte Range
 * (imprescindible para que los visores de PDF funcionen correctamente).
 */
function fcb_serve_files() {
	$file_rel = get_query_var( 'fcb_file' );

	if ( '' === $file_rel ) {
		$path = isset( $_SERVER['REQUEST_URI'] ) ? (string) $_SERVER['REQUEST_URI'] : '';
		$path = (string) wp_parse_url( $path, PHP_URL_PATH );
		if ( preg_match( '#^/files/(.+)$#', $path, $m ) ) {
			$file_rel = $m[1];
		} else {
			return;
		}
	}

	$file_rel = ltrim( str_replace( '\\', '/', (string) $file_rel ), '/' );
	$file_rel = preg_replace( '#/+#', '/', $file_rel );
	$file_rel = rtrim( $file_rel, '/' ); // Eliminar barra final si la URL llega con trailing slash.

	if ( '' === $file_rel || false !== strpos( $file_rel, '..' ) || false !== strpos( $file_rel, "\0" ) ) {
		status_header( 404 );
		exit;
	}

	$uploads      = wp_upload_dir();
	$basedir_real = realpath( $uploads['basedir'] );
	$file         = realpath( $uploads['basedir'] . '/' . $file_rel );

	if ( false === $basedir_real || false === $file || 0 !== strpos( $file, $basedir_real . DIRECTORY_SEPARATOR ) || ! is_file( $file ) ) {
		status_header( 404 );
		exit;
	}

	$mime  = wp_check_filetype( $file );
	$size  = (int) filesize( $file );
	$range = isset( $_SERVER['HTTP_RANGE'] ) ? (string) $_SERVER['HTTP_RANGE'] : '';

	status_header( 200 );
	header( 'Content-Type: ' . ( ! empty( $mime['type'] ) ? $mime['type'] : 'application/octet-stream' ) );
	header( 'Content-Disposition: inline; filename="' . basename( $file ) . '"' );
	header( 'Content-Length: ' . $size );
	header( 'Cache-Control: public, max-age=31536000, immutable' );
	header( 'Accept-Ranges: bytes' );

	if ( '' !== $range && preg_match( '/bytes=(\d+)-(\d*)/', $range, $m ) ) {
		$start = (int) $m[1];
		$end   = ( '' !== $m[2] ) ? (int) $m[2] : $size - 1;
		if ( $start > $end || $start >= $size || $end >= $size ) {
			status_header( 416 );
			header( 'Content-Range: bytes */' . $size );
			exit;
		}
		status_header( 206 );
		header( 'Content-Range: bytes ' . $start . '-' . $end . '/' . $size );
		header( 'Content-Length: ' . ( $end - $start + 1 ) );
		$fp = @fopen( $file, 'rb' );
		if ( $fp ) {
			fseek( $fp, $start );
			$remaining = $end - $start + 1;
			while ( $remaining > 0 && ! feof( $fp ) ) {
				$chunk = min( 8192, $remaining );
				echo fread( $fp, $chunk );
				$remaining -= $chunk;
				flush();
			}
			fclose( $fp );
		}
		exit;
	}

	$fp = @fopen( $file, 'rb' );
	if ( $fp ) {
		while ( ! feof( $fp ) ) {
			echo fread( $fp, 8192 );
			flush();
		}
		fclose( $fp );
	}
	exit;
}
// Prioridad 1: sirve antes de redirect_canonical (prio 10), que en producción
// añadía la barra final (301) a /files/ y rompía el servido.
add_action( 'template_redirect', 'fcb_serve_files', 1 );

/**
 * Desactivado: ya no se redirige wp-content/uploads → /files/. Las URLs de
 * uploads se sirven de forma estática y cacheable, que es lo que funciona.
 */
function fcb_redirect_uploads_to_files() {
	return;
}
add_action( 'template_redirect', 'fcb_redirect_uploads_to_files', 1 );

/**
 * Limpia el bloque "FCB files" que versiones anteriores escribieron en el
 * .htaccess raíz: el redirect de servidor ya no se usa.
 */
function fcb_cleanup_files_htaccess() {
	$htaccess_file = trailingslashit( ABSPATH ) . '.htaccess';
	if ( ! file_exists( $htaccess_file ) || ! is_writable( $htaccess_file ) ) {
		return;
	}

	$contents = (string) file_get_contents( $htaccess_file );
	if ( false === strpos( $contents, '# BEGIN FCB files' ) ) {
		return;
	}

	$pattern = '/# BEGIN FCB files\s*.*?# END FCB files\s*\n?/s';
	$contents = preg_replace( $pattern, '', $contents );
	file_put_contents( $htaccess_file, $contents ); // phpcs:ignore
}
add_action( 'after_switch_theme', 'fcb_cleanup_files_htaccess' );

function fcb_pdf_viewer_template_redirect() {
	if ( get_query_var( 'fcb_pdf_viewer' ) ) {
		include get_template_directory() . '/template-pdf-viewer.php';
		exit;
	}
}
add_action( 'template_redirect', 'fcb_pdf_viewer_template_redirect' );

/**
 * Forzar la recarga de reglas de reescritura en activación del tema.
 */
function fcb_flush_rewrite_rules_on_switch() {
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'fcb_flush_rewrite_rules_on_switch' );

/**
 * Clase para el control personalizado de botón de actualización desde GitHub.
 */
if ( class_exists( 'WP_Customize_Control' ) ) {
	class FCB_Customize_Update_Control extends WP_Customize_Control {
		public $type = 'github_update_button';

		public function render_content() {
			?>
			<span class="customize-control-title"><?php esc_html_e( 'Actualizar Tema desde GitHub', 'fcb' ); ?></span>
			<span class="description customize-control-description">
				<?php esc_html_e( 'Haz clic para buscar, descargar y auto-aplicar la versión más reciente del repositorio de GitHub.', 'fcb' ); ?>
			</span>
			<button type="button" class="button button-primary" id="fcb-github-update-btn" style="margin-top: 10px; width: 100%;">
				<?php esc_html_e( 'Buscar actualizaciones', 'fcb' ); ?>
			</button>
			<div id="fcb-update-status" style="margin-top: 10px; font-weight: bold; font-size: 11px; line-height: 1.4;"></div>

			<script type="text/javascript">
			jQuery(document).ready(function($) {
				$('#fcb-github-update-btn').on('click', function(e) {
					e.preventDefault();
					var $btn = $('#fcb-github-update-btn');
					var $status = $('#fcb-update-status');

					if ($btn.hasClass('disabled')) {
						return;
					}

					$btn.addClass('disabled').text('<?php esc_html_e( 'Actualizando...', 'fcb' ); ?>');
					$status.css('color', '#666').text('<?php esc_html_e( 'Conectando con GitHub y descargando archivos...', 'fcb' ); ?>');

					$.post(ajaxurl, {
						action: 'fcb_trigger_github_update',
						nonce: '<?php echo wp_create_nonce( "fcb_github_update_nonce" ); ?>'
					}, function(response) {
						$btn.removeClass('disabled').text('<?php esc_html_e( 'Buscar actualizaciones', 'fcb' ); ?>');
						if (response.success) {
							$status.css('color', '#0e943f').text(response.data);
							setTimeout(function() {
								window.parent.location.reload();
							}, 2000);
						} else {
							$status.css('color', '#dc3232').text(response.data);
						}
					}).fail(function() {
						$btn.removeClass('disabled').text('<?php esc_html_e( 'Buscar actualizaciones', 'fcb' ); ?>');
						$status.css('color', '#dc3232').text('<?php esc_html_e( 'Error de red al actualizar.', 'fcb' ); ?>');
					});
				});
			});
			</script>
			<?php
		}
	}
}

/**
 * AJAX Handler para actualización manual de GitHub.
 */
function fcb_trigger_github_update_ajax() {
	check_ajax_referer( 'fcb_github_update_nonce', 'nonce' );

	if ( ! current_user_can( 'update_themes' ) ) {
		wp_send_json_error( __( 'Permisos insuficientes.', 'fcb' ) );
	}

	$result = fcb_execute_silent_update();

	if ( is_wp_error( $result ) ) {
		wp_send_json_error( $result->get_error_message() );
	}

	wp_send_json_success( __( '¡Tema actualizado con éxito! Recargando panel...', 'fcb' ) );
}
add_action( 'wp_ajax_fcb_trigger_github_update', 'fcb_trigger_github_update_ajax' );

/**
 * Ejecutar la descarga y sobreescritura de actualización de forma silenciosa.
 */
function fcb_execute_silent_update() {
	$repo_owner = 'jaimegh-es';
	$repo_name  = 'fundacionconradoblanco';
	$branch     = 'main';
	
	$token = get_theme_mod( 'fcb_github_token', '' );
	$url   = "https://api.github.com/repos/{$repo_owner}/{$repo_name}/zipball/{$branch}";

	$args = array(
		'timeout' => 120,
		'headers' => array(
			'User-Agent' => 'WordPress-Theme-Updater',
			'Accept'     => 'application/vnd.github.v3+json',
		),
	);

	if ( ! empty( $token ) ) {
		$args['headers']['Authorization'] = 'token ' . $token;
	}

	$response = wp_remote_get( $url, $args );

	if ( is_wp_error( $response ) ) {
		return $response;
	}

	$code = wp_remote_retrieve_response_code( $response );
	if ( $code !== 200 ) {
		return new WP_Error( 'http_error', sprintf( __( 'Error HTTP %d de GitHub. Verifica la conexión o el token.', 'fcb' ), $code ) );
	}

	$body = wp_remote_retrieve_body( $response );
	if ( empty( $body ) ) {
		return new WP_Error( 'empty_body', __( 'El archivo zip de actualización está vacío.', 'fcb' ) );
	}

	// Guardar temporalmente
	$temp_zip = wp_tempnam( 'theme_update_' );
	global $wp_filesystem;
	if ( empty( $wp_filesystem ) ) {
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		WP_Filesystem();
	}

	if ( ! $wp_filesystem->put_contents( $temp_zip, $body ) ) {
		return new WP_Error( 'write_error', __( 'No se pudo escribir el archivo zip temporal.', 'fcb' ) );
	}

	// Carpeta de extracción
	$temp_extract = get_temp_dir() . 'fcb_update_extract_' . time() . '/';
	wp_mkdir_p( $temp_extract );

	// Descomprimir
	$unzip_status = unzip_file( $temp_zip, $temp_extract );
	@unlink( $temp_zip );

	if ( is_wp_error( $unzip_status ) ) {
		$wp_filesystem->delete( $temp_extract, true );
		return $unzip_status;
	}

	// Buscar subdirectorio
	$subdirs = glob( $temp_extract . '*', GLOB_ONLYDIR );
	if ( empty( $subdirs ) ) {
		$wp_filesystem->delete( $temp_extract, true );
		return new WP_Error( 'invalid_zip', __( 'Estructura de zip de GitHub inválida.', 'fcb' ) );
	}

	$source_dir = $subdirs[0] . '/';
	$theme_dir  = get_template_directory() . '/';

	// Copiar archivos recursivamente
	$copy_status = copy_dir( $source_dir, $theme_dir );
	
	// Limpieza
	$wp_filesystem->delete( $temp_extract, true );

	if ( is_wp_error( $copy_status ) ) {
		return $copy_status;
	}

	flush_rewrite_rules();
	fcb_cleanup_files_htaccess();

	return true;
}

/**
 * Planificación de Cron Semanal para Actualización Automática.
 */
function fcb_schedule_weekly_update() {
	if ( ! wp_next_scheduled( 'fcb_weekly_github_update' ) ) {
		wp_schedule_event( time(), 'weekly', 'fcb_weekly_github_update' );
	}
}
add_action( 'wp', 'fcb_schedule_weekly_update' );

function fcb_run_weekly_github_update() {
	// Ejecutar la actualización en segundo plano
	fcb_execute_silent_update();
}
add_action( 'fcb_weekly_github_update', 'fcb_run_weekly_github_update' );

/**
 * Registrar una página de administración para la gestión de libros/publicaciones.
 */
function fcb_add_admin_management_page() {
	add_menu_page(
		__( 'Gestión de Biblioteca', 'fcb' ),
		__( 'Gestión Biblioteca', 'fcb' ),
		'manage_options',
		'fcb-gestion-biblioteca',
		'fcb_render_admin_management_page',
		'dashicons-book-alt',
		6
	);
}
add_action( 'admin_menu', 'fcb_add_admin_management_page' );

function fcb_render_admin_management_page() {
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Gestión de Biblioteca y Publicaciones', 'fcb' ); ?></h1>
		<p class="description">
			<?php esc_html_e( 'Desde este panel centralizado puedes administrar de forma real los libros, revistas y publicaciones de la Fundación Conrado Blanco.', 'fcb' ); ?>
		</p>

		<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-top: 20px;">
			<!-- Caja 1: Administrar Libros -->
			<div class="card" style="max-width: 100%; margin: 0; padding: 20px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
				<h2 style="margin-top: 0;"><span class="dashicons dashicons-admin-post" style="vertical-align: middle; margin-right: 5px;"></span> <?php esc_html_e( 'Libros y Publicaciones', 'fcb' ); ?></h2>
				<p><?php esc_html_e( 'Crea, edita o elimina los libros que aparecen tanto en la biblioteca principal como en la portada del sitio web.', 'fcb' ); ?></p>
				<p style="margin-top: 20px;">
					<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=libro' ) ); ?>" class="button button-primary"><?php esc_html_e( 'Ver todos los Libros', 'fcb' ); ?></a>
					<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=libro' ) ); ?>" class="button button-secondary"><?php esc_html_e( 'Añadir nuevo Libro', 'fcb' ); ?></a>
				</p>
			</div>

			<!-- Caja 2: Colecciones y Categorías -->
			<div class="card" style="max-width: 100%; margin: 0; padding: 20px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
				<h2 style="margin-top: 0;"><span class="dashicons dashicons-category" style="vertical-align: middle; margin-right: 5px;"></span> <?php esc_html_e( 'Colecciones y Categorías', 'fcb' ); ?></h2>
				<p><?php esc_html_e( 'Administra las colecciones (ej. Capiteles, Antologías, Revistas Charin) para organizar y filtrar las publicaciones en la web.', 'fcb' ); ?></p>
				<p style="margin-top: 20px;">
					<a href="<?php echo esc_url( admin_url( 'edit-tags.php?taxonomy=categoria-libro&post_type=libro' ) ); ?>" class="button button-primary"><?php esc_html_e( 'Gestionar Colecciones', 'fcb' ); ?></a>
				</p>
			</div>

			<!-- Caja 3: Mantenimiento de Portadas -->
			<div class="card" style="max-width: 100%; margin: 0; padding: 20px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
				<h2 style="margin-top: 0;"><span class="dashicons dashicons-hammer" style="vertical-align: middle; margin-right: 5px;"></span> <?php esc_html_e( 'Portadas desde PDF', 'fcb' ); ?></h2>
				<p><?php esc_html_e( 'Regenera las portadas de todos los libros existentes extrayendo la primera página de sus archivos PDF.', 'fcb' ); ?></p>
				<p style="margin-top: 20px;">
					<button type="button" id="fcb-regen-covers-btn" class="button button-primary"><?php esc_html_e( 'Regenerar todas', 'fcb' ); ?></button>
				</p>
				<div id="fcb-regen-status" style="margin-top: 10px; font-weight: bold; font-size: 11px; line-height: 1.4;"></div>
			</div>

			<!-- Caja 4: Actualizaciones del Tema -->
			<div class="card" style="max-width: 100%; margin: 0; padding: 20px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
				<h2 style="margin-top: 0;"><span class="dashicons dashicons-update" style="vertical-align: middle; margin-right: 5px;"></span> <?php esc_html_e( 'Actualizaciones Git', 'fcb' ); ?></h2>
				<p><?php esc_html_e( 'Comprueba y descarga la última versión del tema de la Fundación directamente desde el repositorio Git público.', 'fcb' ); ?></p>
				<p style="margin-top: 20px;">
					<a href="<?php echo esc_url( admin_url( 'customize.php?autofocus[section]=fcb_github_update_section' ) ); ?>" class="button button-primary"><?php esc_html_e( 'Ir a Actualizaciones', 'fcb' ); ?></a>
				</p>
			</div>

			<!-- Caja 5: Sincronizar desde 3D FlipBook -->
			<div class="card" style="max-width: 100%; margin: 0; padding: 20px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
				<h2 style="margin-top: 0;"><span class="dashicons dashicons-book" style="vertical-align: middle; margin-right: 5px;"></span> <?php esc_html_e( 'Importar desde 3D FlipBook', 'fcb' ); ?></h2>
				<p><?php esc_html_e( 'Detecta los nuevos libros creados en el plugin 3D FlipBook y los importa en masa a la biblioteca con su portada generada, URL de flipbook y URL del PDF.', 'fcb' ); ?></p>
				<p style="margin-top: 20px;">
					<button type="button" id="fcb-sync-flipbooks-btn" class="button button-primary"><?php esc_html_e( 'Importar Libros de 3D FlipBook', 'fcb' ); ?></button>
				</p>
				<div id="fcb-sync-flipbooks-status" style="margin-top: 10px; font-weight: bold; font-size: 11px; line-height: 1.4;"></div>
			</div>
		</div>

		<!-- Sección: Importar Libros desde JSON -->
		<div class="card" style="max-width: 100%; margin: 30px 0 0 0; padding: 25px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
			<h2 style="margin-top: 0;"><span class="dashicons dashicons-upload" style="vertical-align: middle; margin-right: 5px;"></span> <?php esc_html_e( 'Importar Libros mediante JSON', 'fcb' ); ?></h2>
			<p><?php esc_html_e( 'Puedes importar masivamente todos los libros pegando un array de objetos JSON estructurado. Los archivos PDF y las portadas se descargarán automáticamente a la biblioteca local de tu servidor WordPress.', 'fcb' ); ?></p>
			
			<div style="display: flex; gap: 25px; margin-top: 20px; flex-wrap: wrap;">
				<!-- Formulario de Entrada -->
				<div style="flex: 1.2; min-width: 320px;">
					<textarea id="fcb-json-import-textarea" style="width: 100%; height: 350px; font-family: monospace; font-size: 12px; border: 1px solid #ccc; border-radius: 6px; padding: 12px; background: #fafafa; box-sizing: border-box;" placeholder='[
  {
    "title": "Charín nº 15",
    "pdf": "https://url-al-pdf.pdf",
    "ebook": "https://url-al-flipbook.com",
    "cover": "https://url-a-la-portada.jpg",
    "edition": "2024",
    "cat": "Revistas infantiles y juveniles «Charin»"
  }
]'></textarea>
					<button type="button" id="fcb-json-import-btn" class="button button-primary" style="margin-top: 12px; padding: 6px 20px; font-size: 14px; height: auto;">
						<?php esc_html_e( 'Comenzar Importación', 'fcb' ); ?>
					</button>
					<div id="fcb-json-import-status" style="margin-top: 15px; font-weight: bold; font-size: 13px; line-height: 1.4;"></div>
				</div>

				<!-- Instrucciones de formato -->
				<div style="flex: 0.8; min-width: 280px; background: #fdfdfd; border: 1px solid #e2e2e2; border-radius: 6px; padding: 20px; font-size: 13px; line-height: 1.5; box-sizing: border-box;">
					<h3 style="margin-top: 0; border-bottom: 1px solid #eee; padding-bottom: 8px;"><span class="dashicons dashicons-editor-help" style="vertical-align: middle; margin-right: 5px;"></span><?php esc_html_e( 'Instrucciones del Formato JSON', 'fcb' ); ?></h3>
					<p><?php esc_html_e( 'El JSON debe estructurarse como un array de objetos con las siguientes claves:', 'fcb' ); ?></p>
					<ul style="list-style-type: disc; padding-left: 20px; margin: 10px 0;">
						<li><strong><code>title</code></strong>: <?php esc_html_e( 'Título del libro (Obligatorio)', 'fcb' ); ?></li>
						<li><strong><code>pdf</code></strong>: <?php esc_html_e( 'Enlace al archivo PDF (Se descargará y guardará en WordPress)', 'fcb' ); ?></li>
						<li><strong><code>ebook</code></strong>: <?php esc_html_e( 'Enlace al Flipbook / Libro 3D interactivo en producción', 'fcb' ); ?></li>
						<li><strong><code>cover</code></strong>: <?php esc_html_e( 'Enlace a la imagen de portada (Se asignará como imagen destacada)', 'fcb' ); ?></li>
						<li><strong><code>edition</code></strong>: <?php esc_html_e( 'Texto descriptivo o año de la edición (opcional)', 'fcb' ); ?></li>
						<li><strong><code>cat</code></strong>: <?php esc_html_e( 'Colección o categoría del libro (opcional)', 'fcb' ); ?></li>
					</ul>
					<p style="margin-top: 15px;">
						<?php printf( __( 'Tienes una guía detallada en formato Markdown dentro de tu tema: %s', 'fcb' ), '<a href="' . esc_url( get_template_directory_uri() . '/instrucciones-importacion.md' ) . '" target="_blank">instrucciones-importacion.md</a>' ); ?>
					</p>
				</div>
			</div>
		</div>

		<!-- Caja: Crear páginas de Actividades a partir de un PDF -->
		<div class="card" style="max-width: 100%; margin: 30px 0 0 0; padding: 25px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
			<h2 style="margin-top: 0;"><span class="dashicons dashicons-media-document" style="vertical-align: middle; margin-right: 5px;"></span> <?php esc_html_e( 'Crear página de Actividades a partir de PDF', 'fcb' ); ?></h2>
			<p><?php esc_html_e( 'Sube un archivo PDF o selecciónalo de los medios, se extraerá su texto y podrás aplicar reglas Regex (para eliminar cabeceras repetidas, numeración de página o marcas) antes de crear la página automáticamente.', 'fcb' ); ?></p>
			
			<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 25px; margin-top: 20px; align-items: start;">
				<div>
					<p style="margin-top: 0;">
						<label for="fcb-actividad-title"><strong><?php esc_html_e( 'Título de la Página (ej. Actividades 2026):', 'fcb' ); ?></strong></label><br/>
						<input type="text" id="fcb-actividad-title" class="widefat" placeholder="<?php esc_attr_e( 'Actividades 2026', 'fcb' ); ?>" />
					</p>
					<p>
						<label for="fcb-actividad-parent"><strong><?php esc_html_e( 'Página Superior / Padre (Opcional):', 'fcb' ); ?></strong></label><br/>
						<?php
						wp_dropdown_pages( array(
							'name'             => 'fcb-actividad-parent',
							'id'               => 'fcb-actividad-parent',
							'show_option_none' => __( '— Ninguna (Página principal) —', 'fcb' ),
							'option_none_value' => '0',
							'class'            => 'widefat',
						) );
						?>
					</p>
					<p>
						<label for="fcb-actividad-pdf-file"><strong><?php esc_html_e( 'Archivo PDF:', 'fcb' ); ?></strong></label><br/>
						<input type="file" id="fcb-actividad-pdf-file" accept=".pdf" class="widefat" style="margin-top: 4px;" />
					</p>
					<p style="margin-top: 15px;">
						<button type="button" id="fcb-actividad-extract-btn" class="button button-secondary" style="display: inline-flex; align-items: center; gap: 4px;">
							<span class="dashicons dashicons-search" style="font-size: 16px; width: 16px; height: 16px;"></span> <?php esc_html_e( 'Extraer Texto del PDF', 'fcb' ); ?>
						</button>
					</p>
				</div>

				<div>
					<p style="margin-top: 0;">
						<label for="fcb-actividad-regex"><strong><?php esc_html_e( 'Patrones Regex a Eliminar (uno por línea):', 'fcb' ); ?></strong></label><br/>
						<textarea id="fcb-actividad-regex" class="widefat" style="height: 110px; font-family: monospace; font-size: 12px;">/(?:página\s+)?\b\d+\s+(?:de|\/)\s+\d+\b/gi
/^.*memoria\s+de\s+actividades.*$/gim
/^\s*\d+\s*$/gm
/Fundación Conrado Blanco/gi</textarea>
						<span class="description" style="font-size: 11px;"><?php esc_html_e( 'Valores predeterminados para eliminar números de página (ej. "4 de 6", "Página 1 de 10"), números sueltos y encabezados repetidos de "Memoria de actividades".', 'fcb' ); ?></span>
					</p>
					<p style="margin-top: 10px;">
						<button type="button" id="fcb-actividad-apply-regex-btn" class="button button-secondary" style="display: inline-flex; align-items: center; gap: 4px;">
							<span class="dashicons dashicons-filter" style="font-size: 16px; width: 16px; height: 16px;"></span> <?php esc_html_e( 'Aplicar Filtros Regex al Texto', 'fcb' ); ?>
						</button>
					</p>
				</div>
			</div>

			<!-- Barra de Progreso -->
			<div id="fcb-actividad-progress-wrap" style="margin-top: 15px; display: none; max-width: 500px;">
				<div style="background: #e0e0e0; border-radius: 4px; overflow: hidden; height: 16px; position: relative;">
					<div id="fcb-actividad-progress-bar" style="background: #0073aa; width: 0%; height: 100%; transition: width 0.3s ease;"></div>
				</div>
				<div id="fcb-actividad-progress-text" style="font-size: 12px; color: #555; margin-top: 5px; font-weight: 600;">0%</div>
			</div>

			<div id="fcb-actividad-preview-wrap" style="margin-top: 20px; display: none;">
				<h3 style="margin-top: 0;"><?php esc_html_e( 'Vista previa del texto / contenido limpio:', 'fcb' ); ?></h3>
				<textarea id="fcb-actividad-text" class="widefat" style="height: 240px; font-family: sans-serif; font-size: 13px; padding: 10px;"></textarea>
				
				<div style="margin-top: 15px; background: #f9f9f9; padding: 15px; border-radius: 6px; border: 1px solid #eee;">
					<p style="margin: 0 0 12px 0;">
						<label style="display: inline-flex; align-items: center; gap: 6px; cursor: pointer;">
							<input type="checkbox" id="fcb-actividad-add-to-menu" value="1" checked />
							<strong><?php esc_html_e( 'Añadir automáticamente esta página al menú desplegable "Actividades" de la cabecera', 'fcb' ); ?></strong>
						</label>
					</p>
					<button type="button" id="fcb-actividad-create-page-btn" class="button button-primary" style="font-size: 14px; padding: 6px 20px; height: auto; display: inline-flex; align-items: center; gap: 6px;">
						<span class="dashicons dashicons-plus-alt2" style="font-size: 18px; width: 18px; height: 18px;"></span> <?php esc_html_e( 'Publicar Página de Actividades', 'fcb' ); ?>
					</button>
				</div>
			</div>
			<div id="fcb-actividad-status" style="margin-top: 15px; font-weight: bold; font-size: 13px; line-height: 1.4;"></div>
		</div>

		<!-- Caja 6: Eliminar libros duplicados -->
		<div class="card" style="max-width: 100%; margin: 30px 0 0 0; padding: 20px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
			<h2 style="margin-top: 0;"><span class="dashicons dashicons-trash" style="vertical-align: middle; margin-right: 5px;"></span> <?php esc_html_e( 'Eliminar libros duplicados', 'fcb' ); ?></h2>
			<p><?php esc_html_e( 'Busca los libros cuya visualización es un PDF (sin flipbook) y los duplicados por título aunque ambos tengan flipbook. Se conserva una única copia de cada libro.', 'fcb' ); ?></p>
			<p style="margin-top: 16px;">
				<button type="button" id="fcb-find-dupes-btn" class="button button-primary"><?php esc_html_e( 'Buscar duplicados', 'fcb' ); ?></button>
				<button type="button" id="fcb-delete-dupes-btn" class="button button-secondary" style="display: none;"><?php esc_html_e( 'Enviar a la papelera', 'fcb' ); ?></button>
			</p>
			<div id="fcb-dupes-status" style="margin-top: 10px; font-weight: bold; font-size: 12px; line-height: 1.4;"></div>
			<ul id="fcb-dupes-list" style="max-height: 260px; overflow: auto; margin-top: 12px; padding: 12px 12px 12px 28px; font-size: 12px; line-height: 1.6; background: #fafafa; border: 1px solid #eee; border-radius: 6px;"></ul>
		</div>
	</div>

	<script type="text/javascript">
	jQuery(document).ready(function($) {
		// Importación de JSON
		$('#fcb-json-import-btn').on('click', function(e) {
			e.preventDefault();
			var $btn = $(this);
			var $status = $('#fcb-json-import-status');
			var jsonData = $('#fcb-json-import-textarea').val().trim();

			if (jsonData === '') {
				alert('<?php esc_html_e( 'Por favor, pega el contenido JSON antes de iniciar.', 'fcb' ); ?>');
				return;
			}

			if (!confirm('<?php esc_html_e( '¿Estás seguro de que quieres importar estos libros? El proceso descargará los archivos y portadas locales, por lo que podría tardar unos minutos.', 'fcb' ); ?>')) {
				return;
			}

			$btn.addClass('disabled').text('<?php esc_html_e( 'Importando...', 'fcb' ); ?>');
			$status.css('color', '#666').html('<span class="spinner is-active" style="float:none; margin:0 5px 0 0; vertical-align:middle;"></span> <?php esc_html_e( 'Procesando el JSON y descargando los archivos multimedia al servidor...', 'fcb' ); ?>');

			$.post(ajaxurl, {
				action: 'fcb_import_books_from_json',
				json_data: jsonData,
				nonce: '<?php echo wp_create_nonce( "fcb_json_import_nonce" ); ?>'
			}, function(response) {
				$btn.removeClass('disabled').text('<?php esc_html_e( 'Comenzar Importación', 'fcb' ); ?>');
				if (response.success) {
					if (typeof response.data === 'object' && response.data.errors) {
						$status.css('color', '#df8a13').html(response.data.message + '<br/><br/><strong>Detalles de Advertencias:</strong><br/>' + response.data.errors.join('<br/>'));
					} else {
						$status.css('color', '#0e943f').text(response.data);
						$('#fcb-json-import-textarea').val('');
					}
				} else {
					$status.css('color', '#dc3232').text(response.data);
				}
			}).fail(function() {
				$btn.removeClass('disabled').text('<?php esc_html_e( 'Comenzar Importación', 'fcb' ); ?>');
				$status.css('color', '#dc3232').text('<?php esc_html_e( 'Error de red en el proceso de importación.', 'fcb' ); ?>');
			});
		});

		// Regeneración de Portadas
		$('#fcb-regen-covers-btn').on('click', function(e) {
			e.preventDefault();
			var $btn = $(this);
			var $status = $('#fcb-regen-status');

			if ($btn.hasClass('disabled')) {
				return;
			}

			if (!confirm('<?php esc_html_e( '¿Quieres regenerar las portadas de todos los libros desde sus PDFs? El proceso se ejecutará paso a paso para evitar errores de red.', 'fcb' ); ?>')) {
				return;
			}

			$btn.addClass('disabled').text('<?php esc_html_e( 'Preparando...', 'fcb' ); ?>');
			$status.css('color', '#666').html('<span class="spinner is-active" style="float:none; margin:0 5px 0 0; vertical-align:middle;"></span> <?php esc_html_e( 'Obteniendo listado de libros...', 'fcb' ); ?>');

			// Obtener IDs de libros
			$.post(ajaxurl, {
				action: 'fcb_get_book_ids_for_regen',
				nonce: '<?php echo wp_create_nonce( "fcb_regen_covers_nonce" ); ?>'
			}, function(response) {
				if (response.success) {
					var bookIds = response.data;
					if (bookIds.length === 0) {
						$status.css('color', '#df8a13').text('<?php esc_html_e( 'No hay libros registrados en la base de datos.', 'fcb' ); ?>');
						$btn.removeClass('disabled').text('<?php esc_html_e( 'Regenerar todas', 'fcb' ); ?>');
						return;
					}

					var total = bookIds.length;
					var processed = 0;
					var successCount = 0;
					var failCount = 0;
					var errors = [];

					$btn.text('<?php esc_html_e( 'Procesando...', 'fcb' ); ?>');

					function processNextBook() {
						if (processed >= total) {
							// Finalizado
							$btn.removeClass('disabled').text('<?php esc_html_e( 'Regenerar todas', 'fcb' ); ?>');
							var finishMsg = '<strong>' + '<?php esc_html_e( 'Proceso completado.', 'fcb' ); ?>' + '</strong> ' + 
								processed + ' ' + '<?php esc_html_e( 'libros analizados.', 'fcb' ); ?>' + ' ' + 
								successCount + ' ' + '<?php esc_html_e( 'exitosos', 'fcb' ); ?>' + ', ' + 
								failCount + ' ' + '<?php esc_html_e( 'fallidos.', 'fcb' ); ?>';
							if (errors.length > 0) {
								finishMsg += '<br/><br/><strong>' + '<?php esc_html_e( 'Detalles de fallos:', 'fcb' ); ?>' + '</strong><br/>' + errors.join('<br/>');
								$status.css('color', '#df8a13').html(finishMsg);
							} else {
								$status.css('color', '#0e943f').html(finishMsg);
							}
							return;
						}

						var currentId = bookIds[processed];
						processed++;
						$status.css('color', '#666').html('<span class="spinner is-active" style="float:none; margin:0 5px 0 0; vertical-align:middle;"></span> ' + 
							'<?php esc_html_e( 'Procesando libro', 'fcb' ); ?> ' + processed + ' ' + '<?php esc_html_e( 'de', 'fcb' ); ?> ' + total + '...');

						$.post(ajaxurl, {
							action: 'fcb_regenerate_single_cover',
							book_id: currentId,
							nonce: '<?php echo wp_create_nonce( "fcb_regen_covers_nonce" ); ?>'
						}, function(res) {
							if (res.success) {
								successCount++;
							} else {
								failCount++;
								errors.push(res.data);
							}
							processNextBook();
						}).fail(function() {
							failCount++;
							errors.push('ID ' + currentId + ': ' + '<?php esc_html_e( 'Error de red en esta petición.', 'fcb' ); ?>');
							processNextBook();
						});
					}

					processNextBook();

				} else {
					$status.css('color', '#dc3232').text(response.data);
					$btn.removeClass('disabled').text('<?php esc_html_e( 'Regenerar todas', 'fcb' ); ?>');
				}
			}).fail(function() {
				$status.css('color', '#dc3232').text('<?php esc_html_e( 'Error de red al inicializar la lista.', 'fcb' ); ?>');
				$btn.removeClass('disabled').text('<?php esc_html_e( 'Regenerar todas', 'fcb' ); ?>');
			});
		});

		// Eliminación de libros duplicados (visualización PDF)
		function fcbEsc(s) { return $('<div>').text(s || '').html(); }
		window.fcbDupes = [];

		$('#fcb-find-dupes-btn').on('click', function(e) {
			e.preventDefault();
			var $btn = $(this);
			var $del = $('#fcb-delete-dupes-btn');
			var $status = $('#fcb-dupes-status');
			var $list = $('#fcb-dupes-list');

			if ($btn.hasClass('disabled')) {
				return;
			}

			$btn.addClass('disabled').text('<?php esc_html_e( 'Buscando...', 'fcb' ); ?>');
			$status.css('color', '#666').html('<span class="spinner is-active" style="float:none; margin:0 5px 0 0; vertical-align:middle;"></span> <?php esc_html_e( 'Analizando libros...', 'fcb' ); ?>');
			$list.empty();
			$del.hide();

			$.post(ajaxurl, {
				action: 'fcb_find_duplicate_pdf_books',
				nonce: '<?php echo wp_create_nonce( "fcb_dupes_nonce" ); ?>'
			}, function(response) {
				$btn.removeClass('disabled').text('<?php esc_html_e( 'Buscar duplicados', 'fcb' ); ?>');
				if (response.success) {
					window.fcbDupes = response.data;
					if (window.fcbDupes.length === 0) {
						$status.css('color', '#0e943f').text('<?php esc_html_e( 'No se encontraron libros con visualización PDF (duplicados).', 'fcb' ); ?>');
						return;
					}
					$status.css('color', '#df8a13').text(window.fcbDupes.length + ' <?php esc_html_e( 'libros duplicados encontrados (PDF y flipbooks repetidos). Revisa la lista y envía a la papelera.', 'fcb' ); ?>');
					var rows = [];
					$.each(window.fcbDupes, function(i, b) {
						rows.push('<li>[' + b.id + '] <strong>' + fcbEsc(b.title) + '</strong> — ' + fcbEsc(b.reason));
					});
					$list.html(rows.join(''));
					$del.show();
				} else {
					$status.css('color', '#dc3232').text(response.data);
				}
			}).fail(function() {
				$btn.removeClass('disabled').text('<?php esc_html_e( 'Buscar duplicados', 'fcb' ); ?>');
				$status.css('color', '#dc3232').text('<?php esc_html_e( 'Error de red al analizar los libros.', 'fcb' ); ?>');
			});
		});

		$('#fcb-delete-dupes-btn').on('click', function(e) {
			e.preventDefault();
			var $btn = $(this);
			var $status = $('#fcb-dupes-status');
			var ids = (window.fcbDupes || []).map(function(b) { return b.id; });

			if (ids.length === 0) {
				return;
			}

			if (!confirm(ids.length + ' <?php esc_html_e( 'libros se enviarán a la papelera (se pueden recuperar). ¿Continuar?', 'fcb' ); ?>')) {
				return;
			}

			$btn.addClass('disabled').text('<?php esc_html_e( 'Borrando...', 'fcb' ); ?>');

			$.post(ajaxurl, {
				action: 'fcb_delete_duplicate_pdf_books',
				ids: ids,
				nonce: '<?php echo wp_create_nonce( "fcb_dupes_nonce" ); ?>'
			}, function(response) {
				if (response.success) {
					$status.css('color', '#0e943f').text(response.data.trashed + ' <?php esc_html_e( 'de', 'fcb' ); ?> ' + response.data.requested + ' <?php esc_html_e( 'libros enviados a la papelera.', 'fcb' ); ?>');
					$('#fcb-dupes-list').empty();
					window.fcbDupes = [];
				} else {
					$status.css('color', '#dc3232').text(response.data);
				}
				$btn.hide().removeClass('disabled');
			}).fail(function() {
				$btn.hide().removeClass('disabled');
				$status.css('color', '#dc3232').text('<?php esc_html_e( 'Error de red al enviar a la papelera.', 'fcb' ); ?>');
			});
		});

		// Sincronizar desde 3D FlipBook
		$('#fcb-sync-flipbooks-btn').on('click', function(e) {
			e.preventDefault();
			var $btn = $(this);
			var $status = $('#fcb-sync-flipbooks-status');

			if ($btn.hasClass('disabled')) {
				return;
			}

			if (!confirm('<?php esc_html_e( '¿Deseas buscar todos los libros de 3D FlipBook e importarlos a la Biblioteca con sus portadas, URLs de visor y PDFs?', 'fcb' ); ?>')) {
				return;
			}

			$btn.addClass('disabled').text('<?php esc_html_e( 'Sincronizando...', 'fcb' ); ?>');
			$status.css('color', '#666').html('<span class="spinner is-active" style="float:none; margin:0 5px 0 0; vertical-align:middle;"></span> <?php esc_html_e( 'Extrayendo flipbooks y registrando libros en la biblioteca...', 'fcb' ); ?>');

			$.post(ajaxurl, {
				action: 'fcb_sync_from_3d_flipbooks',
				nonce: '<?php echo wp_create_nonce( "fcb_sync_flipbooks_nonce" ); ?>'
			}, function(response) {
				$btn.removeClass('disabled').text('<?php esc_html_e( 'Importar Libros de 3D FlipBook', 'fcb' ); ?>');
				if (response.success) {
					var data = response.data;
					var msg = '<strong>' + data.imported + '</strong> <?php esc_html_e( 'nuevos libros importados.', 'fcb' ); ?> (' + data.existing + ' <?php esc_html_e( 'ya existían previamente.', 'fcb' ); ?>)';
					if (data.errors && data.errors.length > 0) {
						msg += '<br/><br/><strong>Advertencias:</strong><br/>' + data.errors.join('<br/>');
					}
					$status.css('color', '#0e943f').html(msg);
				} else {
					$status.css('color', '#dc3232').text(response.data);
				}
			}).fail(function() {
				$btn.removeClass('disabled').text('<?php esc_html_e( 'Importar Libros de 3D FlipBook', 'fcb' ); ?>');
				$status.css('color', '#dc3232').text('<?php esc_html_e( 'Error de red en la sincronización de FlipBooks.', 'fcb' ); ?>');
			});
		});

		// Extracción de Texto de PDF para Actividades con indicador de progreso
		var rawPdfText = '';

		$('#fcb-actividad-extract-btn').on('click', function(e) {
			e.preventDefault();
			var fileInput = $('#fcb-actividad-pdf-file')[0];
			var $status = $('#fcb-actividad-status');
			var $progWrap = $('#fcb-actividad-progress-wrap');
			var $progBar = $('#fcb-actividad-progress-bar');
			var $progText = $('#fcb-actividad-progress-text');

			if (!fileInput.files || fileInput.files.length === 0) {
				alert('<?php esc_html_e( 'Por favor selecciona un archivo PDF primero.', 'fcb' ); ?>');
				return;
			}

			var file = fileInput.files[0];
			var formData = new FormData();
			formData.append('action', 'fcb_extract_text_from_pdf');
			formData.append('pdf_file', file);
			formData.append('nonce', '<?php echo wp_create_nonce( "fcb_actividad_nonce" ); ?>');

			$progWrap.show();
			$progBar.css('width', '0%');
			$progText.text('Subiendo PDF (0%)...');
			$status.css('color', '#666').html('<span class="spinner is-active" style="float:none; margin:0 5px 0 0; vertical-align:middle;"></span> <?php esc_html_e( 'Procesando archivo PDF...', 'fcb' ); ?>');

			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: formData,
				contentType: false,
				processData: false,
				xhr: function() {
					var xhr = new window.XMLHttpRequest();
					xhr.upload.addEventListener("progress", function(evt) {
						if (evt.lengthComputable) {
							var percentComplete = Math.round((evt.loaded / evt.total) * 75);
							$progBar.css('width', percentComplete + '%');
							$progText.text('Subiendo PDF (' + percentComplete + '%)...');
						}
					}, false);
					return xhr;
				},
				success: function(response) {
					$progBar.css('width', '100%');
					$progText.text('Extracción completada (100%)');
					setTimeout(function() { $progWrap.fadeOut(); }, 1500);

					if (response.success) {
						rawPdfText = response.data.text;
						$('#fcb-actividad-text').val(rawPdfText);
						$('#fcb-actividad-preview-wrap').show();
						$status.css('color', '#0e943f').text('<?php esc_html_e( 'Texto extraído correctamente del PDF.', 'fcb' ); ?>');
					} else {
						$status.css('color', '#dc3232').text(response.data);
					}
				},
				error: function() {
					$progWrap.hide();
					$status.css('color', '#dc3232').text('<?php esc_html_e( 'Error de red al extraer texto del PDF.', 'fcb' ); ?>');
				}
			});
		});

		// Aplicar Regex
		$('#fcb-actividad-apply-regex-btn').on('click', function(e) {
			e.preventDefault();
			var text = $('#fcb-actividad-text').val();
			var regexRules = $('#fcb-actividad-regex').val().split('\n');

			$.each(regexRules, function(i, rule) {
				rule = rule.trim();
				if (!rule) return;

				try {
					var match = rule.match(/^\/(.+)\/([a-z]*)$/);
					var re;
					if (match) {
						re = new RegExp(match[1], match[2]);
					} else {
						re = new RegExp(rule, 'gi');
					}
					text = text.replace(re, '');
				} catch (err) {
					console.warn('Regex inválido: ' + rule, err);
				}
			});

			// Limpiar saltos de línea repetidos
			text = text.replace(/\n{3,}/g, '\n\n');
			$('#fcb-actividad-text').val(text);
			$('#fcb-actividad-status').css('color', '#0e943f').text('<?php esc_html_e( 'Reglas Regex aplicadas con éxito al texto.', 'fcb' ); ?>');
		});

		// Publicar Página de Actividades
		$('#fcb-actividad-create-page-btn').on('click', function(e) {
			e.preventDefault();
			var $btn = $(this);
			var $status = $('#fcb-actividad-status');
			var title = $('#fcb-actividad-title').val().trim();
			var content = $('#fcb-actividad-text').val().trim();
			var parent = $('#fcb-actividad-parent').val();
			var addToMenu = $('#fcb-actividad-add-to-menu').is(':checked') ? 1 : 0;

			if (!title) {
				alert('<?php esc_html_e( 'Por favor introduce un título para la página.', 'fcb' ); ?>');
				return;
			}

			if (!content) {
				alert('<?php esc_html_e( 'No hay texto para crear la página.', 'fcb' ); ?>');
				return;
			}

			$btn.addClass('disabled').text('<?php esc_html_e( 'Publicando...', 'fcb' ); ?>');
			$status.css('color', '#666').html('<span class="spinner is-active" style="float:none; margin:0 5px 0 0; vertical-align:middle;"></span> <?php esc_html_e( 'Creando página en WordPress e insertando en menú...', 'fcb' ); ?>');

			$.post(ajaxurl, {
				action: 'fcb_create_actividades_page',
				title: title,
				content: content,
				parent: parent,
				add_to_menu: addToMenu,
				nonce: '<?php echo wp_create_nonce( "fcb_actividad_nonce" ); ?>'
			}, function(response) {
				$btn.removeClass('disabled').text('<?php esc_html_e( 'Publicar Página de Actividades', 'fcb' ); ?>');
				if (response.success) {
					var msg = '<?php esc_html_e( '¡Página creada con éxito!', 'fcb' ); ?> <a href="' + response.data.url + '" target="_blank">' + response.data.title + ' (Ver en la web)</a> | <a href="' + response.data.edit_url + '" target="_blank"><?php esc_html_e( 'Editar en el admin', 'fcb' ); ?></a>';
					if (response.data.menu_added) {
						msg += '<br/><span style="color:#0073aa;">✓ ' + response.data.menu_added + '</span>';
					}
					$status.css('color', '#0e943f').html(msg);
				} else {
					$status.css('color', '#dc3232').text(response.data);
				}
			}).fail(function() {
				$btn.removeClass('disabled').text('<?php esc_html_e( 'Publicar Página de Actividades', 'fcb' ); ?>');
				$status.css('color', '#dc3232').text('<?php esc_html_e( 'Error de red al crear la página.', 'fcb' ); ?>');
			});
		});
	});
	</script>
	<?php
}

/**
 * AJAX Handler para importar libros desde JSON.
 */
function fcb_import_books_from_json_ajax() {
	check_ajax_referer( 'fcb_json_import_nonce', 'nonce' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( __( 'Permisos insuficientes.', 'fcb' ) );
	}

	$json_data = isset( $_POST['json_data'] ) ? wp_unslash( $_POST['json_data'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	if ( empty( $json_data ) ) {
		wp_send_json_error( __( 'El campo JSON está vacío.', 'fcb' ) );
	}

	$books = json_decode( $json_data, true );
	if ( json_last_error() !== JSON_ERROR_NONE ) {
		wp_send_json_error( sprintf( __( 'Error al decodificar JSON: %s', 'fcb' ), json_last_error_msg() ) );
	}

	if ( ! is_array( $books ) ) {
		wp_send_json_error( __( 'El JSON debe ser un array de objetos.', 'fcb' ) );
	}

	require_once( ABSPATH . 'wp-admin/includes/image.php' );
	require_once( ABSPATH . 'wp-admin/includes/file.php' );
	require_once( ABSPATH . 'wp-admin/includes/media.php' );

	$imported = 0;
	$errors   = array();

	// Cargar libros existentes para actualizar y evitar duplicados
	$existing_books = get_posts( array(
		'post_type'      => 'libro',
		'posts_per_page' => -1,
		'post_status'    => 'any',
	) );
	$existing_map = array();
	foreach ( $existing_books as $eb ) {
		$norm = fcb_normalize_title( $eb->post_title );
		$existing_map[ $norm ] = $eb->ID;
		$eb_url = get_post_meta( $eb->ID, '_fcb_libro_ebook', true );
		if ( ! empty( $eb_url ) ) {
			$existing_map[ trim( strtolower( $eb_url ), '/' ) ] = $eb->ID;
		}
	}

	foreach ( $books as $index => $book ) {
		$title = isset( $book['title'] ) ? sanitize_text_field( $book['title'] ) : '';
		if ( empty( $title ) ) {
			$errors[] = sprintf( __( 'Índice %d: El título es obligatorio.', 'fcb' ), $index );
			continue;
		}

		$norm_title = fcb_normalize_title( $title );
		$ebook_url  = isset( $book['ebook'] ) ? esc_url_raw( $book['ebook'] ) : '';
		$ebook_key  = ! empty( $ebook_url ) ? trim( strtolower( $ebook_url ), '/' ) : '';

		$post_id = 0;
		if ( isset( $existing_map[ $norm_title ] ) ) {
			$post_id = $existing_map[ $norm_title ];
		} elseif ( ! empty( $ebook_key ) && isset( $existing_map[ $ebook_key ] ) ) {
			$post_id = $existing_map[ $ebook_key ];
		}

		if ( $post_id > 0 ) {
			// Actualizar post existente
			wp_update_post( array(
				'ID'         => $post_id,
				'post_title' => $title,
			) );
		} else {
			// Crear nuevo post
			$post_id = wp_insert_post( array(
				'post_title'  => $title,
				'post_status' => 'publish',
				'post_type'   => 'libro',
			) );
			if ( is_wp_error( $post_id ) ) {
				$errors[] = sprintf( __( 'Error al insertar libro "%s": %s', 'fcb' ), $title, $post_id->get_error_message() );
				continue;
			}
			$existing_map[ $norm_title ] = $post_id;
			if ( ! empty( $ebook_key ) ) {
				$existing_map[ $ebook_key ] = $post_id;
			}
		}

		// Asignar PDF
		$pdf_url = isset( $book['pdf'] ) ? esc_url_raw( $book['pdf'] ) : '';
		$local_pdf_path = '';
		if ( ! empty( $pdf_url ) ) {
			$curr_pdf = get_post_meta( $post_id, '_fcb_libro_pdf', true );
			if ( empty( $curr_pdf ) || $curr_pdf !== $pdf_url ) {
				update_post_meta( $post_id, '_fcb_libro_pdf', $pdf_url );
			}
		}

		// Asignar eBook / Flipbook
		if ( ! empty( $ebook_url ) ) {
			update_post_meta( $post_id, '_fcb_libro_ebook', $ebook_url );
		}

		// Asignar Portada (Cover) solo si el libro no tiene una asignada
		if ( ! has_post_thumbnail( $post_id ) ) {
			$cover_assigned = false;
			if ( ! empty( $local_pdf_path ) ) {
				$pdf_cover_id = fcb_generate_cover_from_pdf( $local_pdf_path, $post_id );
				if ( ! is_wp_error( $pdf_cover_id ) ) {
					set_post_thumbnail( $post_id, $pdf_cover_id );
					$local_cover = wp_get_attachment_url( $pdf_cover_id );
					update_post_meta( $post_id, '_fcb_libro_cover_url', $local_cover );
					$cover_assigned = true;
				} else {
					$errors[] = sprintf( __( 'No se pudo generar portada desde PDF para "%s": %s', 'fcb' ), $title, $pdf_cover_id->get_error_message() );
				}
			}

			// Intentar descargar portada desde JSON si no se pudo generar desde el PDF
			if ( ! $cover_assigned ) {
				$cover_url = isset( $book['cover'] ) ? esc_url_raw( $book['cover'] ) : '';
				if ( ! empty( $cover_url ) ) {
					$desc_img_id = media_sideload_image( $cover_url, $post_id, null, 'id' );
					if ( ! is_wp_error( $desc_img_id ) ) {
						set_post_thumbnail( $post_id, $desc_img_id );
						$local_cover = wp_get_attachment_url( $desc_img_id );
						update_post_meta( $post_id, '_fcb_libro_cover_url', $local_cover );
					} else {
						update_post_meta( $post_id, '_fcb_libro_cover_url', $cover_url );
					}
				}
			}
		}

		// Asignar Edición y Fecha de Publicación
		$edition = isset( $book['edition'] ) ? sanitize_text_field( $book['edition'] ) : '';
		if ( ! empty( $edition ) ) {
			update_post_meta( $post_id, '_fcb_libro_edition', $edition );
			if ( preg_match( '/\b(19\d{2}|20\d{2})\b/', $edition, $ym ) ) {
				update_post_meta( $post_id, '_fcb_libro_date', $ym[1] . '-01-01' );
				wp_update_post( array(
					'ID'            => $post_id,
					'post_date'     => $ym[1] . '-01-01 12:00:00',
					'post_date_gmt' => get_gmt_from_date( $ym[1] . '-01-01 12:00:00' ),
				) );
			}
		} elseif ( preg_match( '/\b(19\d{2}|20\d{2})\b/', $title, $ym ) ) {
			update_post_meta( $post_id, '_fcb_libro_edition', $ym[1] );
			update_post_meta( $post_id, '_fcb_libro_date', $ym[1] . '-01-01' );
			wp_update_post( array(
				'ID'            => $post_id,
				'post_date'     => $ym[1] . '-01-01 12:00:00',
				'post_date_gmt' => get_gmt_from_date( $ym[1] . '-01-01 12:00:00' ),
			) );
		}

		// Asignar Categoría
		$cat = isset( $book['cat'] ) ? sanitize_text_field( $book['cat'] ) : '';
		if ( ! empty( $cat ) ) {
			$term = term_exists( $cat, 'categoria-libro' );
			if ( ! $term ) {
				$term = wp_insert_term( $cat, 'categoria-libro' );
			}
			if ( ! is_wp_error( $term ) ) {
				$term_id = is_array( $term ) ? $term['term_id'] : $term;
				wp_set_post_terms( $post_id, array( (int) $term_id ), 'categoria-libro' );
			}
		}

		$imported++;
	}

	if ( ! empty( $errors ) ) {
		wp_send_json_success( array(
			'message' => sprintf( __( 'Se han importado %d libros con éxito. Algunos tuvieron advertencias.', 'fcb' ), $imported ),
			'errors'  => $errors,
		) );
	} else {
		wp_send_json_success( sprintf( __( '¡Éxito! Se han importado los %d libros correctamente al panel.', 'fcb' ), $imported ) );
	}
}
add_action( 'wp_ajax_fcb_import_books_from_json', 'fcb_import_books_from_json_ajax' );

/**
 * Generar la portada de un libro usando la primera página de su PDF.
 *
 * @param string $pdf_path Ruta física al archivo PDF local.
 * @param int    $post_id  ID del post del libro al que se asociará la portada.
 * @return int|WP_Error ID del adjunto de la imagen creada o error.
 */
function fcb_generate_cover_from_pdf( $pdf_path, $post_id ) {
	if ( ! file_exists( $pdf_path ) ) {
		return new WP_Error( 'file_not_found', __( 'El archivo PDF no existe en el disco.', 'fcb' ) );
	}

	$pdf_filename = basename( $pdf_path );
	$image_filename = str_ireplace( '.pdf', '-portada.jpg', $pdf_filename );
	
	$temp_dir = get_temp_dir();
	$temp_image_path = $temp_dir . $image_filename;

	$cover_created = false;

	// Método 1: Intentar con Imagick si está disponible y cargado
	if ( class_exists( 'Imagick' ) ) {
		try {
			$imagick = new Imagick();
			$imagick->setResolution( 150, 150 );
			// [0] indica la primera página del PDF
			$imagick->readImage( $pdf_path . '[0]' );
			
			$imagick->setImageFormat( 'jpeg' );
			$imagick->setImageCompression( Imagick::COMPRESSION_JPEG );
			$imagick->setImageCompressionQuality( 85 );
			
			$imagick->writeImage( $temp_image_path );
			$imagick->clear();
			$imagick->destroy();
			
			if ( file_exists( $temp_image_path ) ) {
				$cover_created = true;
			}
		} catch ( Exception $e ) {
			// Imagick falló (ej: políticas restrictivas del servidor), continuaremos con Ghostscript
		}
	}

	// Método 2: Intentar con Ghostscript / pdftoppm si exec() está habilitado y permitido
	if ( ! $cover_created && fcb_is_exec_enabled() ) {
		$escaped_pdf = escapeshellarg( $pdf_path );
		$escaped_out = escapeshellarg( $temp_image_path );

		// Intentar ejecutar Ghostscript directamente
		$cmd = "gs -dNOPAUSE -sDEVICE=jpeg -dFirstPage=1 -dLastPage=1 -sOutputFile={$escaped_out} -r150 {$escaped_pdf} -c quit 2>&1";
		$output = array();
		$return_val = 0;
		exec( $cmd, $output, $return_val );

		if ( $return_val === 0 && file_exists( $temp_image_path ) ) {
			$cover_created = true;
		} else {
			// Intentar usar pdftoppm como alternativa si gs falla
			$cmd_alt = "pdftoppm -jpeg -f 1 -l 1 -r 150 {$escaped_pdf} " . escapeshellarg( $temp_dir . str_ireplace( '.pdf', '-portada', $pdf_filename ) ) . " 2>&1";
			exec( $cmd_alt, $output_alt, $return_val_alt );
			
			// pdftoppm añade "-1.jpg" o "-01.jpg" al final
			$generated_alt = $temp_dir . str_ireplace( '.pdf', '-portada-1.jpg', $pdf_filename );
			if ( ! file_exists( $generated_alt ) ) {
				$generated_alt = $temp_dir . str_ireplace( '.pdf', '-portada-01.jpg', $pdf_filename );
			}

			if ( file_exists( $generated_alt ) ) {
				rename( $generated_alt, $temp_image_path );
				$cover_created = true;
			}
		}
	}

	if ( ! $cover_created ) {
		return new WP_Error(
			'no_extraction_method',
			__( 'No se puede extraer la portada del PDF: Imagick no está disponible y la función PHP "exec" está desactivada en este servidor.', 'fcb' )
		);
	}

	require_once( ABSPATH . 'wp-admin/includes/image.php' );
	require_once( ABSPATH . 'wp-admin/includes/file.php' );
	require_once( ABSPATH . 'wp-admin/includes/media.php' );

	$file_array = array(
		'name'     => $image_filename,
		'tmp_name' => $temp_image_path,
	);

	$attachment_id = media_handle_sideload( $file_array, $post_id, get_the_title( $post_id ) );

	return $attachment_id;
}

/**
 * Comprobar si la función exec() está disponible y habilitada en php.ini.
 */
function fcb_is_exec_enabled() {
	if ( ! function_exists( 'exec' ) ) {
		return false;
	}
	$disabled = ini_get( 'disable_functions' );
	if ( ! empty( $disabled ) ) {
		$disabled_array = array_map( 'trim', explode( ',', $disabled ) );
		if ( in_array( 'exec', $disabled_array, true ) ) {
			return false;
		}
	}
	return true;
}

/**
 * AJAX Handler para obtener la lista de IDs de libros a procesar.
 */
function fcb_get_book_ids_for_regen_ajax() {
	check_ajax_referer( 'fcb_regen_covers_nonce', 'nonce' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( __( 'Permisos insuficientes.', 'fcb' ) );
	}

	$books = get_posts( array(
		'post_type'      => 'libro',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	) );

	wp_send_json_success( $books );
}
add_action( 'wp_ajax_fcb_get_book_ids_for_regen', 'fcb_get_book_ids_for_regen_ajax' );

/**
 * AJAX Handler para procesar la portada de un solo libro.
 */
function fcb_regenerate_single_cover_ajax() {
	check_ajax_referer( 'fcb_regen_covers_nonce', 'nonce' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( __( 'Permisos insuficientes.', 'fcb' ) );
	}

	$book_id = isset( $_POST['book_id'] ) ? intval( $_POST['book_id'] ) : 0;
	if ( ! $book_id ) {
		wp_send_json_error( __( 'ID de libro no válido.', 'fcb' ) );
	}

	$title   = get_the_title( $book_id );
	$pdf_url = get_post_meta( $book_id, '_fcb_libro_pdf', true );

	if ( empty( $pdf_url ) ) {
		wp_send_json_error( sprintf( __( '"%s": No tiene un PDF asociado.', 'fcb' ), $title ) );
	}

	$pdf_path = '';
	if ( strpos( $pdf_url, home_url() ) !== false ) {
		global $wpdb;
		$attachment_id = $wpdb->get_var( $wpdb->prepare(
			"SELECT ID FROM $wpdb->posts WHERE guid = %s AND post_type = 'attachment'",
			$pdf_url
		) );
		if ( $attachment_id ) {
			$pdf_path = get_attached_file( $attachment_id );
		}
	}

	if ( ! empty( $pdf_path ) && file_exists( $pdf_path ) ) {
		$pdf_cover_id = fcb_generate_cover_from_pdf( $pdf_path, $book_id );
		if ( ! is_wp_error( $pdf_cover_id ) ) {
			set_post_thumbnail( $book_id, $pdf_cover_id );
			$local_cover = wp_get_attachment_url( $pdf_cover_id );
			update_post_meta( $book_id, '_fcb_libro_cover_url', $local_cover );
			wp_send_json_success( sprintf( __( '"%s": Portada regenerada con éxito.', 'fcb' ), $title ) );
		} else {
			wp_send_json_error( sprintf( __( '"%s": %s', 'fcb' ), $title, $pdf_cover_id->get_error_message() ) );
		}
	} else {
		wp_send_json_error( sprintf( __( '"%s": Archivo PDF local no encontrado en el disco.', 'fcb' ), $title ) );
	}
}
add_action( 'wp_ajax_fcb_regenerate_single_cover', 'fcb_regenerate_single_cover_ajax' );

/**
 * Indica si la URL de visualización de un libro es un PDF (o no existe), es
 * decir, si es un duplicado que se debe eliminar.
 */
function fcb_is_pdf_visualization( $ebook_url ) {
	if ( empty( $ebook_url ) ) {
		return true;
	}
	$path = (string) wp_parse_url( $ebook_url, PHP_URL_PATH );
	return strtolower( substr( $path, -4 ) ) === '.pdf';
}

/**
 * Normaliza un título para comparar duplicados.
 */
function fcb_normalize_title( $title ) {
	$title = trim( (string) $title );
	$title = function_exists( 'mb_strtolower' ) ? mb_strtolower( $title, 'UTF-8' ) : strtolower( $title );
	return preg_replace( '/\s+/', ' ', $title );
}

/**
 * AJAX Handler: lista los libros duplicados (visualización PDF o mismo título).
 */
function fcb_find_duplicate_pdf_books_ajax() {
	check_ajax_referer( 'fcb_dupes_nonce', 'nonce' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( __( 'Permisos insuficientes.', 'fcb' ) );
	}

	$books = get_posts( array(
		'post_type'      => 'libro',
		'posts_per_page' => -1,
		'post_status'    => 'any',
		'orderby'        => 'title',
		'order'          => 'ASC',
	) );

	$dupes = array();
	$seen  = array();

	foreach ( $books as $book ) {
		$ebook  = get_post_meta( $book->ID, '_fcb_libro_ebook', true );
		$is_pdf = fcb_is_pdf_visualization( $ebook );
		$ntitle = fcb_normalize_title( $book->post_title );

		if ( $is_pdf ) {
			$dupes[] = array(
				'id'     => (int) $book->ID,
				'title'  => $book->post_title,
				'ebook'  => $ebook ? $ebook : '',
				'reason' => __( 'Visualización en PDF (sin flipbook)', 'fcb' ),
			);
			continue;
		}

		if ( '' !== $ntitle && isset( $seen[ $ntitle ] ) ) {
			$dupes[] = array(
				'id'     => (int) $book->ID,
				'title'  => $book->post_title,
				'ebook'  => $ebook,
				'reason' => sprintf( __( 'Duplicado por título (ambos con flipbook) — ya existe "%s"', 'fcb' ), get_the_title( $seen[ $ntitle ] ) ),
			);
		} elseif ( '' !== $ntitle ) {
			$seen[ $ntitle ] = $book->ID;
		}
	}

	wp_send_json_success( $dupes );
}
add_action( 'wp_ajax_fcb_find_duplicate_pdf_books', 'fcb_find_duplicate_pdf_books_ajax' );

/**
 * AJAX Handler: envía a la papelera los libros duplicados indicados.
 */
function fcb_delete_duplicate_pdf_books_ajax() {
	check_ajax_referer( 'fcb_dupes_nonce', 'nonce' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( __( 'Permisos insuficientes.', 'fcb' ) );
	}

	$ids = isset( $_POST['ids'] ) ? array_map( 'absint', (array) wp_unslash( $_POST['ids'] ) ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$ids = array_values( array_filter( $ids ) );

	if ( empty( $ids ) ) {
		wp_send_json_error( __( 'No se recibieron IDs de libros.', 'fcb' ) );
	}

	$trashed = 0;
	foreach ( $ids as $book_id ) {
		if ( 'libro' === get_post_type( $book_id ) && wp_trash_post( $book_id ) ) {
			$trashed++;
		}
	}

	wp_send_json_success( array(
		'trashed'   => $trashed,
		'requested' => count( $ids ),
	) );
}
add_action( 'wp_ajax_fcb_delete_duplicate_pdf_books', 'fcb_delete_duplicate_pdf_books_ajax' );

/**
 * AJAX Handler: Sincronizar e importar masivamente libros desde 3D FlipBook
 */
function fcb_sync_from_3d_flipbooks_ajax() {
	check_ajax_referer( 'fcb_sync_flipbooks_nonce', 'nonce' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( __( 'Permisos insuficientes.', 'fcb' ) );
	}

	require_once( ABSPATH . 'wp-admin/includes/image.php' );
	require_once( ABSPATH . 'wp-admin/includes/file.php' );
	require_once( ABSPATH . 'wp-admin/includes/media.php' );

	// Obtener todos los posts del CPT 3d-flip-book
	$flipbooks = get_posts( array(
		'post_type'      => '3d-flip-book',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
	) );

	if ( empty( $flipbooks ) ) {
		wp_send_json_error( __( 'No se encontraron publicaciones en 3D FlipBook.', 'fcb' ) );
	}

	$imported = 0;
	$existing = 0;
	$errors   = array();

	// Mapear libros existentes para evitar duplicados
	$existing_books = get_posts( array(
		'post_type'      => 'libro',
		'posts_per_page' => -1,
		'post_status'    => 'any',
	) );

	$existing_map = array();
	foreach ( $existing_books as $eb ) {
		$norm_title = fcb_normalize_title( $eb->post_title );
		$ebook_meta = get_post_meta( $eb->ID, '_fcb_libro_ebook', true );
		if ( $norm_title ) {
			$existing_map[ $norm_title ] = true;
		}
		if ( $ebook_meta ) {
			$existing_map[ rtrim( $ebook_meta, '/' ) ] = true;
		}
	}

	foreach ( $flipbooks as $fb ) {
		$title     = $fb->post_title;
		$norm_title = fcb_normalize_title( $title );
		$ebook_url = get_permalink( $fb->ID );
		$ebook_key = rtrim( $ebook_url, '/' );

		if ( isset( $existing_map[ $norm_title ] ) || isset( $existing_map[ $ebook_key ] ) ) {
			$existing++;
			continue;
		}

		// Obtener metadata de 3d-flip-book
		$fb_data = get_post_meta( $fb->ID, '3dfb_data', true );
		if ( is_string( $fb_data ) ) {
			$fb_data_unserialized = @unserialize( $fb_data );
			if ( $fb_data_unserialized !== false || $fb_data === 'b:0;' ) {
				$fb_data = $fb_data_unserialized;
			}
		}

		$pdf_url = '';
		$pdf_attachment_id = 0;

		if ( is_array( $fb_data ) ) {
			if ( isset( $fb_data['guid'] ) ) {
				$pdf_url = $fb_data['guid'];
			}
			if ( isset( $fb_data['post_ID'] ) && intval( $fb_data['post_ID'] ) > 0 ) {
				$pdf_attachment_id = intval( $fb_data['post_ID'] );
			}
		}

		if ( empty( $pdf_url ) && $pdf_attachment_id > 0 ) {
			$pdf_url = wp_get_attachment_url( $pdf_attachment_id );
		}

		// Crear el post tipo 'libro'
		$post_id = wp_insert_post( array(
			'post_title'  => $title,
			'post_status' => 'publish',
			'post_type'   => 'libro',
			'post_date'   => $fb->post_date,
		) );

		if ( is_wp_error( $post_id ) ) {
			$errors[] = sprintf( __( 'Error creando libro "%s": %s', 'fcb' ), $title, $post_id->get_error_message() );
			continue;
		}

		// Guardar URL de eBook / Flipbook
		update_post_meta( $post_id, '_fcb_libro_ebook', $ebook_url );

		// Guardar URL de PDF
		if ( ! empty( $pdf_url ) ) {
			update_post_meta( $post_id, '_fcb_libro_pdf', $pdf_url );
		}

		// Detectar edición y año a partir del título o URL del PDF
		$detected_year = '';
		if ( preg_match( '/\b(19\d{2}|20\d{2})\b/', $title . ' ' . $pdf_url, $year_match ) ) {
			$detected_year = $year_match[1];
		}
		if ( ! empty( $detected_year ) ) {
			update_post_meta( $post_id, '_fcb_libro_edition', $detected_year );
			update_post_meta( $post_id, '_fcb_libro_date', $detected_year . '-01-01' );
			wp_update_post( array(
				'ID'            => $post_id,
				'post_date'     => $detected_year . '-01-01 12:00:00',
				'post_date_gmt' => get_gmt_from_date( $detected_year . '-01-01 12:00:00' ),
			) );
		} else {
			update_post_meta( $post_id, '_fcb_libro_date', substr( $fb->post_date, 0, 10 ) );
		}

		// Obtener portada generada por flipbook o thumbnail
		$cover_assigned = false;
		$fb_thumb_id    = get_post_thumbnail_id( $fb->ID );
		if ( $fb_thumb_id ) {
			set_post_thumbnail( $post_id, $fb_thumb_id );
			$local_cover = wp_get_attachment_url( $fb_thumb_id );
			update_post_meta( $post_id, '_fcb_libro_cover_url', $local_cover );
			$cover_assigned = true;
		}

		// Si no tiene imagen destacada directa, buscar miniatura en 3dfb_thumbnail o generarla desde PDF
		if ( ! $cover_assigned && $pdf_attachment_id > 0 ) {
			$pdf_path = get_attached_file( $pdf_attachment_id );
			if ( $pdf_path && file_exists( $pdf_path ) ) {
				$generated_cover_id = fcb_generate_cover_from_pdf( $pdf_path, $post_id );
				if ( ! is_wp_error( $generated_cover_id ) ) {
					set_post_thumbnail( $post_id, $generated_cover_id );
					$local_cover = wp_get_attachment_url( $generated_cover_id );
					update_post_meta( $post_id, '_fcb_libro_cover_url', $local_cover );
					$cover_assigned = true;
				}
			}
		}

		// Asignar categorías de 3D FlipBook correspondientes a 'categoria-libro'
		$terms = wp_get_object_terms( $fb->ID, '3d-flip-book-category' );
		if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
			$target_term_ids = array();
			foreach ( $terms as $term_obj ) {
				$target_term = term_exists( $term_obj->name, 'categoria-libro' );
				if ( ! $target_term ) {
					$target_term = wp_insert_term( $term_obj->name, 'categoria-libro', array( 'slug' => $term_obj->slug ) );
				}
				if ( ! is_wp_error( $target_term ) ) {
					$target_term_ids[] = (int) ( is_array( $target_term ) ? $target_term['term_id'] : $target_term );
				}
			}
			if ( ! empty( $target_term_ids ) ) {
				wp_set_post_terms( $post_id, $target_term_ids, 'categoria-libro' );
			}
		}

		$existing_map[ $norm_title ] = true;
		$existing_map[ $ebook_key ]  = true;
		$imported++;
	}

	wp_send_json_success( array(
		'imported' => $imported,
		'existing' => $existing,
		'errors'   => $errors,
	) );
}
add_action( 'wp_ajax_fcb_sync_from_3d_flipbooks', 'fcb_sync_from_3d_flipbooks_ajax' );

/**
 * AJAX Handler: Extraer texto de un archivo PDF subido.
 */
function fcb_extract_text_from_pdf_ajax() {
	check_ajax_referer( 'fcb_actividad_nonce', 'nonce' );

	if ( ! current_user_can( 'edit_pages' ) ) {
		wp_send_json_error( __( 'Permisos insuficientes.', 'fcb' ) );
	}

	if ( ! isset( $_FILES['pdf_file'] ) || empty( $_FILES['pdf_file']['tmp_name'] ) ) {
		wp_send_json_error( __( 'No se ha recibido ningún archivo PDF.', 'fcb' ) );
	}

	$tmp_path = $_FILES['pdf_file']['tmp_name'];
	$filename = sanitize_file_name( $_FILES['pdf_file']['name'] );

	if ( strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) ) !== 'pdf' ) {
		wp_send_json_error( __( 'El archivo debe tener formato .pdf.', 'fcb' ) );
	}

	$text = '';

	// Método 1: Intentar con pdftotext (Poppler utilities)
	if ( fcb_is_exec_enabled() ) {
		$escaped_pdf = escapeshellarg( $tmp_path );
		$output = array();
		$return_val = 0;
		@exec( "pdftotext {$escaped_pdf} - 2>&1", $output, $return_val );
		if ( $return_val === 0 && ! empty( $output ) ) {
			$text = implode( "\n", $output );
		}
	}

	// Método 2: Parser PHP interno básico para PDFs con streams de texto descomprimidos
	if ( empty( $text ) && file_exists( $tmp_path ) ) {
		$content = file_get_contents( $tmp_path );
		if ( $content ) {
			// Buscar bloques de texto PDF (BT ... ET)
			preg_match_all( '/BT[\r\n\s]+(.*?)[\r\n\s]+ET/s', $content, $matches );
			if ( ! empty( $matches[1] ) ) {
				$extracted = array();
				foreach ( $matches[1] as $block ) {
					preg_match_all( '/\((.*?)\)\s*T[jJ]/s', $block, $t_matches );
					if ( ! empty( $t_matches[1] ) ) {
						$extracted[] = implode( ' ', $t_matches[1] );
					}
				}
				$text = implode( "\n\n", $extracted );
			}
		}
	}

	// Limpieza de caracteres de control nulos
	$text = str_replace( array( "\0", "\r" ), array( '', '' ), $text );

	if ( empty( trim( $text ) ) ) {
		wp_send_json_error( __( 'No se pudo extraer texto seleccionable del PDF (puede estar escaneado como imagen o protegido).', 'fcb' ) );
	}

	wp_send_json_success( array(
		'text' => trim( $text ),
	) );
}
add_action( 'wp_ajax_fcb_extract_text_from_pdf', 'fcb_extract_text_from_pdf_ajax' );

/**
 * AJAX Handler: Crear página de Actividades con el texto limpio.
 */
function fcb_create_actividades_page_ajax() {
	check_ajax_referer( 'fcb_actividad_nonce', 'nonce' );

	if ( ! current_user_can( 'edit_pages' ) ) {
		wp_send_json_error( __( 'Permisos insuficientes.', 'fcb' ) );
	}

	$title   = isset( $_POST['title'] ) ? sanitize_text_field( $_POST['title'] ) : '';
	$content = isset( $_POST['content'] ) ? wp_kses_post( wp_unslash( $_POST['content'] ) ) : '';
	$parent  = isset( $_POST['parent'] ) ? intval( $_POST['parent'] ) : 0;

	if ( empty( $title ) ) {
		wp_send_json_error( __( 'El título es obligatorio.', 'fcb' ) );
	}

	if ( empty( $content ) ) {
		wp_send_json_error( __( 'El contenido está vacío.', 'fcb' ) );
	}

	// Formatear párrafos si es texto plano
	if ( strpos( $content, '<p>' ) === false ) {
		$content = wpautop( $content );
	}

	$page_id = wp_insert_post( array(
		'post_title'   => $title,
		'post_content' => $content,
		'post_status'  => 'publish',
		'post_type'    => 'page',
		'post_parent'  => $parent,
	) );

	$add_to_menu = ! empty( $_POST['add_to_menu'] );
	$menu_msg    = '';

	if ( $add_to_menu ) {
		// Buscar menú asignado a 'primary'
		$locations = get_theme_mod( 'nav_menu_locations' );
		$menu_id   = isset( $locations['primary'] ) ? $locations['primary'] : 0;
		if ( ! $menu_id ) {
			$menu_obj = wp_get_nav_menu_object( 'Menú Principal' );
			if ( $menu_obj ) {
				$menu_id = $menu_obj->term_id;
			}
		}

		if ( $menu_id ) {
			// Buscar el elemento 'Actividades' en el menú
			$menu_items = wp_get_nav_menu_items( $menu_id );
			$actividades_item_id = 0;
			if ( ! empty( $menu_items ) ) {
				foreach ( $menu_items as $item ) {
					if ( strtolower( trim( $item->title ) ) === 'actividades' && intval( $item->menu_item_parent ) === 0 ) {
						$actividades_item_id = $item->ID;
						break;
					}
				}
			}

			// Añadir la nueva página como hijo de Actividades
			$item_id = wp_update_nav_menu_item( $menu_id, 0, array(
				'menu-item-title'     => $title,
				'menu-item-object-id' => $page_id,
				'menu-item-object'    => 'page',
				'menu-item-type'      => 'post_type',
				'menu-item-status'    => 'publish',
				'menu-item-parent-id' => $actividades_item_id,
			) );

			if ( ! is_wp_error( $item_id ) ) {
				$menu_msg = __( 'Añadida automáticamente al desplegable de Actividades de la cabecera.', 'fcb' );
			}
		}
	}

	wp_send_json_success( array(
		'id'         => $page_id,
		'title'      => get_the_title( $page_id ),
		'url'        => get_permalink( $page_id ),
		'edit_url'   => admin_url( 'post.php?post=' . $page_id . '&action=edit' ),
		'menu_added' => $menu_msg,
	) );
}
add_action( 'wp_ajax_fcb_create_actividades_page', 'fcb_create_actividades_page_ajax' );



