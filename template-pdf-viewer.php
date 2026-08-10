<?php
/**
 * Visor de PDF personalizado de la Fundación.
 */

$pdf_url = isset( $_GET['file'] ) ? esc_url_raw( wp_unslash( $_GET['file'] ) ) : '';
$title   = isset( $_GET['title'] ) ? sanitize_text_field( wp_unslash( $_GET['title'] ) ) : __( 'Libro', 'fcb' );

if ( empty( $pdf_url ) ) {
	wp_safe_redirect( home_url( '/biblioteca/' ) );
	exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo esc_html( $title ); ?> - <?php esc_html_e( 'Visor de PDF', 'fcb' ); ?></title>
	<link rel="stylesheet" href="<?php echo esc_url( get_stylesheet_uri() ); ?>">
	<script src="https://unpkg.com/lucide@latest"></script>
	<style>
		body, html {
			margin: 0;
			padding: 0;
			height: 100%;
			overflow: hidden;
			background: #1a1e1b;
			color: #fff;
			font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
		}
		.pdf-viewer-container {
			display: flex;
			flex-direction: column;
			height: 100%;
		}
		.pdf-viewer-header {
			display: flex;
			align-items: center;
			justify-content: space-between;
			background: #252a26;
			border-bottom: 1px solid rgba(255, 255, 255, 0.08);
			padding: 12px 20px;
			z-index: 10;
			box-shadow: 0 4px 15px rgba(0,0,0,0.2);
		}
		.pdf-viewer-back {
			display: inline-flex;
			align-items: center;
			gap: 8px;
			color: #fff;
			text-decoration: none;
			font-weight: 600;
			font-size: 0.92rem;
			background: rgba(255, 255, 255, 0.06);
			padding: 8px 16px;
			border-radius: 6px;
			transition: background 0.2s ease;
		}
		.pdf-viewer-back:hover {
			background: rgba(255, 255, 255, 0.12);
		}
		.pdf-viewer-back svg {
			width: 16px;
			height: 16px;
		}
		.pdf-viewer-title {
			font-size: 1.1rem;
			font-weight: 700;
			margin: 0;
			white-space: nowrap;
			overflow: hidden;
			text-overflow: ellipsis;
			max-width: 50%;
			text-align: center;
		}
		.pdf-viewer-actions {
			display: flex;
			align-items: center;
			gap: 10px;
		}
		.pdf-viewer-btn {
			display: inline-flex;
			align-items: center;
			justify-content: center;
			width: 38px;
			height: 38px;
			border-radius: 6px;
			background: rgba(255, 255, 255, 0.06);
			color: #fff;
			border: 0;
			cursor: pointer;
			transition: all 0.2s ease;
			text-decoration: none;
		}
		.pdf-viewer-btn:hover {
			background: rgba(255, 255, 255, 0.12);
		}
		.pdf-viewer-btn--download {
			background: #0e943f;
		}
		.pdf-viewer-btn--download:hover {
			background: #0b7d34;
		}
		.pdf-viewer-btn svg {
			width: 18px;
			height: 18px;
		}
		.pdf-viewer-body {
			flex: 1;
			position: relative;
			background: #141715;
		}
		iframe, embed {
			width: 100%;
			height: 100%;
			border: 0;
		}
	</style>
</head>
<body>

	<div class="pdf-viewer-container">
		<header class="pdf-viewer-header">
			<a href="<?php echo esc_url( home_url( '/biblioteca/' ) ); ?>" class="pdf-viewer-back" onclick="if(history.length > 1){ event.preventDefault(); history.back(); } else { window.close(); }">
				<i data-lucide="arrow-left"></i>
				<span><?php esc_html_e( 'Volver', 'fcb' ); ?></span>
			</a>
			
			<h2 class="pdf-viewer-title"><?php echo esc_html( $title ); ?></h2>
			
			<div class="pdf-viewer-actions">
				<button id="fullscreen-btn" class="pdf-viewer-btn" title="<?php esc_attr_e( 'Pantalla completa', 'fcb' ); ?>">
					<i data-lucide="maximize-2"></i>
				</button>
				<a href="<?php echo esc_url( $pdf_url ); ?>" class="pdf-viewer-btn pdf-viewer-btn--download" title="<?php esc_attr_e( 'Descargar PDF', 'fcb' ); ?>" download>
					<i data-lucide="download"></i>
				</a>
			</div>
		</header>
		
		<div class="pdf-viewer-body">
			<iframe src="<?php echo esc_url( $pdf_url ); ?>#toolbar=1&navpanes=0&scrollbar=1" type="application/pdf"></iframe>
		</div>
	</div>

	<script>
		// Inicializar Lucide
		if (typeof lucide !== 'undefined') {
			lucide.createIcons();
		}

		// Pantalla completa interactiva
		const fullscreenBtn = document.getElementById('fullscreen-btn');
		fullscreenBtn.addEventListener('click', function () {
			if (!document.fullscreenElement) {
				document.documentElement.requestFullscreen().then(() => {
					fullscreenBtn.innerHTML = '<i data-lucide="minimize-2"></i>';
					lucide.createIcons();
				}).catch(err => {
					console.log("Error al activar pantalla completa: " + err.message);
				});
			} else {
				document.exitFullscreen().then(() => {
					fullscreenBtn.innerHTML = '<i data-lucide="maximize-2"></i>';
					lucide.createIcons();
				});
			}
		});
	</script>
</body>
</html>
