<?php
/**
 * Pie del tema.
 *
 * @package fcb
 */
?>
</div><!-- #content -->

<footer class="site-footer" role="contentinfo">
	<div class="container site-footer__inner">
		<p class="site-footer__brand"><?php bloginfo( 'name' ); ?></p>

		<?php
		wp_nav_menu(
			array(
				'theme_location' => 'primary',
				'container'      => false,
				'depth'          => 1,
				'fallback_cb'    => false,
			)
		);
		?>

		<div class="site-footer__social">
			<a href="https://www.facebook.com/fundacionconradoblanco/" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
				<i data-lucide="facebook"></i>
			</a>
			<a href="https://www.youtube.com/@fundacionconradoblanco" target="_blank" rel="noopener noreferrer" aria-label="YouTube">
				<i data-lucide="youtube"></i>
			</a>
		</div>

		<p class="site-footer__copy">
			&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?> | ¿Te gusta la web? La hemos hecho los de <a href="https://inled.es" target="_blank" rel="noopener noreferrer" style="color: inherit; text-decoration: underline;">Inled Group</a>
		</p>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
