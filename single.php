<?php
/**
 * Modèle d’un article.
 *
 * @package RBC68
 */
get_header();
?>
<main class="rbc68-content">
	<?php while ( have_posts() ) : the_post(); ?>
		<article <?php post_class( 'rbc68-article' ); ?>>
			<h1><?php the_title(); ?></h1>
			<p class="rbc68-article-meta"><?php echo esc_html( get_the_date() ); ?></p>
			<?php if ( has_post_thumbnail() ) : ?><?php the_post_thumbnail( 'large' ); ?><?php endif; ?>
			<div class="rbc68-article-content"><?php the_content(); ?></div>
		</article>
	<?php endwhile; ?>
</main>
<?php get_footer(); ?>

