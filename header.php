<?php
/**
 * En-tête du thème.
 *
 * @package RBC68
 */
?><!doctype html>
<html <?php language_attributes(); ?> data-theme-preference="dark">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<script>try{document.documentElement.dataset.themePreference=localStorage.getItem('rbc68-theme')||'dark'}catch(e){document.documentElement.dataset.themePreference='dark'}</script>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php $rbc68_header_events = rbc68_get_upcoming_events( 1 ); ?>
<header class="site-header">
	<div class="nav-container">
		<a href="<?php echo esc_url( home_url( '/#accueil' ) ); ?>" class="logo-link" aria-label="<?php esc_attr_e( 'Accueil', 'rbc68' ); ?>">
			<img src="<?php echo esc_url( rbc68_logo_url() ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" class="logo-img">
		</a>
		<ul class="nav-links">
			<li><a href="<?php echo esc_url( home_url( '/#essentiel' ) ); ?>">Infos pratiques</a></li>
			<li><a href="<?php echo esc_url( home_url( '/#horaires' ) ); ?>">Horaires</a></li>
			<li><a href="<?php echo esc_url( home_url( '/#calendrier' ) ); ?>">Évènements<?php if ( $rbc68_header_events ) : ?> <span class="nav-notice">À venir</span><?php endif; ?></a></li>
			<li><a href="<?php echo esc_url( home_url( '/#actualites' ) ); ?>">Actualités<?php if ( wp_count_posts()->publish > 0 ) : ?><span class="nav-dot"><span class="screen-reader-text"> — nouveau contenu</span></span><?php endif; ?></a></li>
			<li><a href="<?php echo esc_url( home_url( '/#club' ) ); ?>">Le club</a></li>
			<li><a href="<?php echo esc_url( home_url( '/#contact' ) ); ?>">Contact</a></li>
			<li><a href="<?php echo esc_url( home_url( '/#inscription' ) ); ?>" class="nav-cta">Nous rejoindre</a></li>
		</ul>
		<button class="hamburger" type="button" aria-expanded="false" aria-controls="mobile-menu"><span class="screen-reader-text">Ouvrir le menu</span>☰</button>
		<button class="theme-toggle" type="button" role="switch" aria-checked="true" aria-label="Utiliser le mode sombre"><span class="theme-toggle-sun" aria-hidden="true">☀</span><span class="theme-toggle-track" aria-hidden="true"><span></span></span><span class="theme-toggle-moon" aria-hidden="true">☾</span></button>
	</div>
	<nav class="mobile-menu" id="mobile-menu" aria-label="Navigation mobile">
		<ul>
			<li><a href="<?php echo esc_url( home_url( '/#essentiel' ) ); ?>">Infos pratiques</a></li>
			<li><a href="<?php echo esc_url( home_url( '/#horaires' ) ); ?>">Horaires</a></li>
			<li><a href="<?php echo esc_url( home_url( '/#calendrier' ) ); ?>">Évènements<?php echo $rbc68_header_events ? ' — à venir' : ''; ?></a></li>
			<li><a href="<?php echo esc_url( home_url( '/#actualites' ) ); ?>">Actualités</a></li>
			<li><a href="<?php echo esc_url( home_url( '/#club' ) ); ?>">Le club</a></li>
			<li><a href="<?php echo esc_url( home_url( '/#contact' ) ); ?>">Contact</a></li>
			<li><a href="<?php echo esc_url( home_url( '/#inscription' ) ); ?>">Nous rejoindre</a></li>
		</ul>
	</nav>
</header>
<?php
$rbc68_announcement_text  = trim( rbc68_mod( 'rbc68_announcement_text', '' ) );
$rbc68_announcement_link  = rbc68_mod( 'rbc68_announcement_link', '' );
$rbc68_announcement_label = rbc68_mod( 'rbc68_announcement_label', 'En savoir plus' );
if ( $rbc68_announcement_text ) :
	?>
	<aside class="announcement-bar" aria-label="Information importante">
		<div class="announcement-inner">
			<strong>Information</strong>
			<span><?php echo esc_html( $rbc68_announcement_text ); ?></span>
			<?php if ( $rbc68_announcement_link ) : ?>
				<a href="<?php echo esc_url( $rbc68_announcement_link ); ?>"><?php echo esc_html( $rbc68_announcement_label ); ?> →</a>
			<?php endif; ?>
		</div>
	</aside>
<?php endif; ?>
