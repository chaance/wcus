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
	<header class="wcus-site__header">
		<a href="#primary-content-area" id="skip-content"><?php _e( 'Skip to main content', 'wcus-demo' ); ?></a>
		<a class="wcus-site__header-logo-link" href="/">
			<img
				class="wcus-site__header-logo"
				alt="<?php esc_attr_e( 'Company logo', 'wcus-demo' ); ?>"
				src="<?php echo esc_url( WCUS_DEMO_TEMPLATE_URL . '/images/logo.svg' ); ?>"
			/>
		</a>
		<div class="wcus-site-header__nav-wrapper">
			<button class="wcus-site-header__nav-trigger" id="js-header-nav-toggle" aria-expanded="false" aria-haspopup="true" tabindex="-1">
				<span class="screen-reader-text"><?php echo __( 'Toggle menu', 'wcus-demo' ); ?></span>
			</button>
			<nav class="wcus-site-header__nav" id="js-header-nav">
				<?php
				wcus_demo_bem_nav_menu( [
					'block_class'    => 'wcus-header-nav',
					'theme_location' => 'main',
				] );
				?>
			</nav>
		</div>
	</header>
	<div id="primary-content-area" class="wcus-site__content-area">
