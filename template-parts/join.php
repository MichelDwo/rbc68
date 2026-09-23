<?php
/** Bloc d’inscription de l’accueil. */
defined( 'ABSPATH' ) || exit;
$season = rbc68_join_value( 'season' );
$documents = rbc68_join_documents();
?>
<section id="inscription" class="join-section">
  <h2 class="section-title"><?php echo esc_html( rbc68_join_value( 'title' ) ); ?></h2>
  <div class="inscription-container">
    <?php if ( rbc68_join_value( 'intro' ) ) : ?><p class="trial-highlight"><?php echo nl2br( esc_html( rbc68_join_value( 'intro' ) ) ); ?></p><?php endif; ?>
    <div class="join-paths">
      <div class="inscription-info">
        <h3><?php echo esc_html( rbc68_join_value( 'new_title' ) ); ?></h3>
        <p><?php echo nl2br( esc_html( rbc68_join_value( 'new_text' ) ) ); ?></p>
        <a class="join-action" href="#horaires">Voir les créneaux</a>
      </div>
      <div class="inscription-info">
        <h3><?php echo esc_html( rbc68_join_value( 'renew_title' ) ); ?></h3>
        <p><?php echo nl2br( esc_html( rbc68_join_value( 'renew_text' ) ) ); ?></p>
        <?php if ( rbc68_join_value( 'renew_url' ) && rbc68_join_value( 'renew_button' ) ) : ?>
          <a class="join-action" href="<?php echo esc_url( rbc68_join_value( 'renew_url' ) ); ?>"><?php echo esc_html( rbc68_join_value( 'renew_button' ) ); ?></a>
        <?php endif; ?>
      </div>
    </div>
    <?php if ( rbc68_join_value( 'dates' ) ) : ?><p class="join-dates"><?php echo nl2br( esc_html( rbc68_join_value( 'dates' ) ) ); ?></p><?php endif; ?>
    <div class="inscription-tarifs">
      <h3>Tarifs<?php if ( $season ) : ?> · <?php echo esc_html( $season ); ?><?php endif; ?></h3>
      <dl class="join-prices">
        <?php foreach ( array( 'mini' => array( 'Mini-bad', '70 €' ), 'youth' => array( 'Jeunes', '85 €' ), 'adults' => array( 'Adultes', '105 €' ) ) as $key => $group ) : ?>
          <div><dt><?php echo esc_html( $group[0] ); ?><?php if ( rbc68_availability_label( $key ) ) : ?> <span class="availability availability-full">Complet</span><?php endif; ?></dt><dd><?php echo esc_html( rbc68_mod( 'rbc68_price_' . $key, $group[1] ) ); ?></dd></div>
        <?php endforeach; ?>
      </dl>
      <?php if ( rbc68_join_value( 'payment' ) ) : ?><p><?php echo nl2br( esc_html( rbc68_join_value( 'payment' ) ) ); ?></p><?php endif; ?>
      <?php if ( rbc68_join_value( 'reference' ) ) : ?><p class="join-reference">Libellé du virement : <strong><?php echo esc_html( str_replace( '{saison}', $season, rbc68_join_value( 'reference' ) ) ); ?></strong></p><?php endif; ?>
    </div>
    <?php if ( $documents || rbc68_join_value( 'documents_intro' ) ) : ?>
      <div class="inscription-documents">
        <h3>Préparer mon inscription</h3>
        <?php if ( rbc68_join_value( 'documents_intro' ) ) : ?><p><?php echo nl2br( esc_html( rbc68_join_value( 'documents_intro' ) ) ); ?></p><?php endif; ?>
        <?php if ( $documents ) : ?><ul class="join-documents">
          <?php foreach ( $documents as $document ) : ?>
            <li><a href="<?php echo esc_url( $document['url'] ); ?>" download><?php echo esc_html( $document['label'] ); ?> <span aria-hidden="true">↓</span></a><?php if ( $document['note'] ) : ?><span class="join-document-note"><?php echo esc_html( $document['note'] ); ?></span><?php endif; ?></li>
          <?php endforeach; ?>
        </ul><?php endif; ?>
      </div>
    <?php endif; ?>
    <p class="join-help">Une question avant de commencer ? <a href="#contact">Contactez-nous</a></p>
  </div>
</section>
