<?php
/**
 * Modèle de liste des contenus.
 *
 * @package RBC68
 */
get_header();
?>
<main class="rbc68-content">
	<section>
		<h1 class="section-title"><?php bloginfo( 'name' ); ?></h1>
		<div class="blog-grid">
			<?php if ( have_posts() ) : ?>
				<?php while ( have_posts() ) : the_post(); ?>
					<article <?php post_class( 'blog-card' ); ?>>
						<?php if ( has_post_thumbnail() ) : ?>
							<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'large', array( 'class' => 'blog-img' ) ); ?></a>
						<?php endif; ?>
						<div class="blog-content">
							<div class="blog-date"><?php echo esc_html( get_the_date() ); ?></div>
							<h2 class="blog-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
							<div class="blog-excerpt"><?php the_excerpt(); ?></div>
							<a href="<?php the_permalink(); ?>" class="blog-link">Lire la suite →</a>
						</div>
					</article>
				<?php endwhile; ?>
			<?php else : ?>
				<p class="rbc68-no-posts">Aucun contenu publié pour le moment.</p>
			<?php endif; ?>
		</div>
		<?php the_posts_pagination(); ?>
	</section>
</main>
<?php get_footer(); ?>

