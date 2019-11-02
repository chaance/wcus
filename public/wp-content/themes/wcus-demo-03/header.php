<?php
/**
 * The template for displaying the header.
 *
 * @package WCUS_Demo
 * @subpackage Theme
 */
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta http-equiv="x-ua-compatible" content="ie=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="profile" href="http://gmpg.org/xfn/11">

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="js-site-wrapper" class="wcus-site">

	<?php get_template_part( 'parts/layout/site-header' ); ?>

	<div id="primary-content-area" class="wcus-site__content-area">
