<?php
/**
 * The template for displaying the footer.
 *
 * @package WCUS_Demo
 * @subpackage Theme
 */

?>

	</div>

	<footer class="wcus-site__footer">
		<p>
			<?php
			echo esc_html( sprintf(
				/* translators: 1: Copyright symbol, 2: current year */
				__( '%1$s %2$s Zero Rights Reserved', 'wpcs-demo' ),
				'&copy;',
				date( 'Y' )
			) );
			?>
		</p>
	</footer>

</div>

<?php wp_footer(); ?>
</body>
</html>
