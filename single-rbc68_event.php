<?php
/**
 * Fiche d’un événement.
 *
 * @package RBC68
 */
get_header();
?>
<main class="rbc68-content">
	<?php while ( have_posts() ) : the_post(); ?>
		<?php if ( post_password_required() ) { echo get_the_password_form(); continue; } ?>
		<?php
		$event_id = get_the_ID();
		$kind     = get_post_meta( $event_id, 'rbc68_event_kind', true );
		$team     = get_post_meta( $event_id, 'rbc68_event_team', true );
		$location = get_post_meta( $event_id, 'rbc68_event_location', true );
		$details  = get_post_meta( $event_id, 'rbc68_event_details', true );
		$map      = rbc68_event_map_embed_url( $event_id );
		$map_link = rbc68_event_map_link( $event_id );
		?>
		<article <?php post_class( 'rbc68-article rbc68-event-page' ); ?>>
			<p class="event-category event-category-<?php echo esc_attr( rbc68_event_kind( $event_id ) ); ?>"><?php echo esc_html( rbc68_event_kind_label( $event_id ) ); ?></p>
			<h1><?php the_title(); ?></h1>
			<p class="event-page-when"><strong><?php echo esc_html( rbc68_event_when_label( $event_id ) ); ?></strong></p>
			<?php if ( ! rbc68_event_is_past( $event_id ) ) : ?><p><a class="event-action" href="<?php echo esc_url( rbc68_calendar_url( $event_id ) ); ?>">Ajouter au calendrier</a></p><?php endif; ?>
			<?php if ( $team ) : ?><p><strong>Équipe concernée :</strong> <?php echo esc_html( $team ); ?></p><?php endif; ?>
			<?php if ( $location ) : ?><p><strong>Lieu :</strong> <?php echo esc_html( $location ); ?></p><?php endif; ?>
			<?php if ( $details ) : ?><p class="event-page-details"><?php echo esc_html( $details ); ?></p><?php endif; ?>
			<?php rbc68_event_content_display( $event_id ); ?>
			<?php if ( $map ) : ?>
				<div class="event-map"><iframe title="Carte du lieu de l’événement" loading="lazy" src="<?php echo esc_url( $map ); ?>"></iframe></div>
			<?php endif; ?>
			<?php if ( $map_link ) : ?><p><a class="event-action" href="<?php echo esc_url( $map_link ); ?>" target="_blank" rel="noopener">Ouvrir le lieu dans OpenStreetMap →</a></p><?php endif; ?>
			<div class="rbc68-article-content"><?php the_content(); ?></div>
		</article>
	<?php endwhile; ?>
</main>
<?php get_footer(); ?>
