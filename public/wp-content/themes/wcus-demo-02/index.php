<?php
/**
 * The main template file.
 *
 * @package WCUS_Demo
 * @subpackage Theme
 */

get_header();
?>

<div class="wcus-page-index__outer-wrapper">

	<main id="main" class="wcus-page-index__main">

		<div class="wcus-page-index__content-wrapper">
			<?php
			if ( have_posts() ) :
				while ( have_posts() ) :
					the_post();
					the_content();
				endwhile;
			endif;
			?>
		</div>

	</main>

</div>

<?php get_footer(); ?>
