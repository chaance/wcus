<?php
/**
 * The site footer layout part.
 *
 * @package WCUS_Demo
 * @subpackage Theme
 */
?>

<footer class="wcus-site-footer">
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
