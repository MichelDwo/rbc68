<?php
/**
 * Page introuvable.
 *
 * @package RBC68
 */
get_header();
?>
<main class="rbc68-content">
	<article class="rbc68-article">
		<h1>Page introuvable</h1>
		<p>La page demandée n’existe pas ou a été déplacée.</p>
		<p><a class="cta-button" href="<?php echo esc_url( home_url( '/' ) ); ?>">Retour à l’accueil</a></p>
	</article>
</main>
<?php get_footer(); ?>

