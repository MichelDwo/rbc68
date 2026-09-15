<?php
/**
 * Modèle d’une page.
 *
 * @package RBC68
 */
get_header();
?>
<main class="rbc68-content">
	<?php while ( have_posts() ) : the_post(); ?>
		<article <?php post_class( 'rbc68-article' ); ?>>
			<h1><?php the_title(); ?></h1>
			<div class="rbc68-article-content"><?php the_content(); ?></div>
		</article>
	<?php endwhile; ?>
</main>
<?php get_footer(); ?>

