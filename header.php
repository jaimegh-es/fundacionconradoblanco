<?php
/**
 * Cabecera del tema.
 *
 * @package fcb
 */
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Saltar al contenido', 'fcb' ); ?></a>

<header class="site-header" role="banner">
	<div class="container site-header__inner">

		<div class="site-branding">
			<a class="custom-logo-link" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
				<img class="custom-logo" src="https://hosted.inled.es/logo.fundacionconradoblanco.sf_.png" alt="<?php bloginfo( 'name' ); ?>" />
			</a>
		</div>

		<button class="nav-toggle" aria-controls="primary-menu" aria-expanded="false">
			<span></span><span></span><span></span>
			<span class="screen-reader-text"><?php esc_html_e( 'Menú', 'fcb' ); ?></span>
		</button>

		<nav id="site-navigation" class="main-navigation" aria-label="<?php esc_attr_e( 'Menú principal', 'fcb' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'menu_id'        => 'primary-menu',
					'container'      => false,
					'fallback_cb'    => false,
					'depth'          => 3,
				)
			);
			?>
		</nav>

	</div>
</header>

<div id="content" class="site-content">
