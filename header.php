<?php
/**
 * En-tête du thème.
 *
 * @package RBC68
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<header class="site-header">
	<div class="nav-container">
		<a href="<?php echo esc_url( home_url( '/#accueil' ) ); ?>" class="logo-link" aria-label="<?php esc_attr_e( 'Accueil', 'rbc68' ); ?>">
			<img src="<?php echo esc_url( rbc68_logo_url() ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" class="logo-img">
		</a>
		<ul class="nav-links">
			<li><a href="<?php echo esc_url( home_url( '/#accueil' ) ); ?>">Accueil</a></li>
			<li><a href="<?php echo esc_url( home_url( '/#club' ) ); ?>">Le Club</a></li>
			<li><a href="<?php echo esc_url( home_url( '/#inscription' ) ); ?>">Inscription</a></li>
			<li><a href="<?php echo esc_url( home_url( '/#horaires' ) ); ?>">Horaires</a></li>
			<li><a href="<?php echo esc_url( home_url( '/#calendrier' ) ); ?>">Évènements</a></li>
			<li><a href="<?php echo esc_url( home_url( '/#galerie' ) ); ?>">Galerie</a></li>
			<li><a href="<?php echo esc_url( home_url( '/#actualites' ) ); ?>">Actualités</a></li>
			<li><a href="<?php echo esc_url( home_url( '/#contact' ) ); ?>">Contact</a></li>
		</ul>
		<button class="hamburger" type="button" aria-expanded="false" aria-controls="mobile-menu"><span class="screen-reader-text">Ouvrir le menu</span>☰</button>
		<button class="theme-toggle" type="button" aria-label="Basculer entre les modes clair et sombre">🌓</button>
	</div>
	<nav class="mobile-menu" id="mobile-menu" aria-label="Navigation mobile">
		<ul>
			<li><a href="<?php echo esc_url( home_url( '/#accueil' ) ); ?>">Accueil</a></li>
			<li><a href="<?php echo esc_url( home_url( '/#club' ) ); ?>">Le Club</a></li>
			<li><a href="<?php echo esc_url( home_url( '/#inscription' ) ); ?>">Inscription</a></li>
			<li><a href="<?php echo esc_url( home_url( '/#horaires' ) ); ?>">Horaires</a></li>
			<li><a href="<?php echo esc_url( home_url( '/#calendrier' ) ); ?>">Évènements</a></li>
			<li><a href="<?php echo esc_url( home_url( '/#galerie' ) ); ?>">Galerie</a></li>
			<li><a href="<?php echo esc_url( home_url( '/#actualites' ) ); ?>">Actualités</a></li>
			<li><a href="<?php echo esc_url( home_url( '/#contact' ) ); ?>">Contact</a></li>
		</ul>
	</nav>
</header>

