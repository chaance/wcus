<?php
/**
 * The template for displaying the footer.
 *
 * @package WCUS_Demo
 * @subpackage Theme
 */

?>

	</div><!-- .site__content-area -->

	<footer class="site__footer">
		<p>
			<?php
			echo esc_html(
				sprintf(
					/* translators: 1: Copyright symbol, 2: current year */
					__( '%1$s %2$s Zero Rights Reserved', 'wpcs-demo' ),
					'&copy;',
					date( 'Y' )
				)
			);
			?>
		</p>
	</footer><!-- .site__footer -->

</div><!-- .site -->

<?php wp_footer(); ?>
</body>
</html>
