<?php
/** Choix du parcours, puis du groupe ; utilisable sans JavaScript. */
defined( 'ABSPATH' ) || exit;
$s = rbc68_join_settings();
$reference = str_replace( array( '{saison}', '{saison_courte}' ), array( $s['season'], $s['short_season'] ), $s['reference'] );
?>
<section id="inscription" class="join-flow">
  <h2 class="section-title">Nous rejoindre</h2>
  <?php if ( $s['intro'] ) : ?><p class="trial-highlight"><?php echo esc_html( $s['intro'] ); ?> <a href="#horaires">Voir les créneaux</a></p><?php endif; ?>
  <p class="join-flow-intro">Saison <?php echo esc_html( $s['season'] ); ?> · Choisissez votre situation.</p>
  <div class="join-paths">
    <details class="join-choice">
      <summary><span><strong>Je renouvelle ma licence</strong><span class="join-hint">Déjà membre en <?php echo esc_html( $s['previous'] ); ?></span></span></summary>
      <div class="join-choice-body">
        <p><?php echo nl2br( esc_html( $s['renew_text'] ) ); ?></p>
        <?php if ( $s['renew_url'] ) : ?><p><a class="join-primary" href="<?php echo esc_url( $s['renew_url'] ); ?>" target="_blank" rel="noopener noreferrer">Renouveler sur MyFFBaD ↗</a></p><?php endif; ?>
        <?php if ( $s['licence_help'] ) : ?><details class="join-help"><summary>Où trouver mon numéro de licence ?</summary><ol>
          <?php foreach ( preg_split( '/\R/u', $s['licence_help'] ) as $step ) : if ( ! trim( $step ) ) { continue; } ?><li><?php echo esc_html( $step ); ?></li><?php endforeach; ?>
        </ol></details><?php endif; ?>
        <?php if ( $s['renew_docs'] ) : ?><h3>Documents utiles</h3><ul class="join-doc-links"><?php foreach ( $s['renew_docs'] as $doc ) : ?><li><a href="<?php echo esc_url( $doc['url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $doc['label'] ); ?> ↗</a></li><?php endforeach; ?></ul><?php endif; ?>
      </div>
    </details>
    <details class="join-choice">
      <summary><span><strong>Je m’inscris pour la première fois</strong><span class="join-hint">Nouveau membre en <?php echo esc_html( $s['season'] ); ?></span></span></summary>
      <div class="join-choice-body">
        <p><?php echo nl2br( esc_html( $s['new_text'] ) ); ?></p>
        <div class="join-groups">
          <?php foreach ( $s['groups'] as $key => $group ) : ?>
          <details class="join-group">
            <summary><span><strong><?php echo esc_html( $group['label'] ); ?></strong><span class="join-hint"><?php echo esc_html( $group['ages'] ); ?></span><?php if ( rbc68_availability_label( $key ) ) : ?><span class="availability availability-full">Complet</span><?php endif; ?></span><span class="join-price"><?php echo esc_html( $group['price'] ); ?></span></summary>
            <div class="join-group-body">
              <?php if ( $group['note'] ) : ?><p><?php echo nl2br( esc_html( $group['note'] ) ); ?></p><?php endif; ?>
              <h3>1. Préparer les documents</h3>
              <?php if ( $s['documents_text'] ) : ?><p><?php echo nl2br( esc_html( $s['documents_text'] ) ); ?></p><?php endif; ?>
              <?php if ( $s['medical'] ) : ?><p><?php echo nl2br( esc_html( $s['medical'] ) ); ?></p><?php endif; ?>
              <?php if ( $group['docs'] ) : ?><ul class="join-doc-links">
                <?php foreach ( $group['docs'] as $doc ) : ?><li><a href="<?php echo esc_url( $doc['url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $doc['label'] ); ?> ↗</a></li><?php endforeach; ?>
              </ul><?php endif; ?>
              <h3>2. Régler la cotisation · <?php echo esc_html( $group['price'] ); ?></h3>
              <p><?php echo nl2br( esc_html( $s['payment'] ) ); ?></p>
              <?php if ( $reference ) : ?><p>Libellé du virement : <strong><?php echo esc_html( $reference ); ?></strong></p><?php endif; ?>
              <?php if ( $s['rib_url'] ) : ?><p><a href="<?php echo esc_url( $s['rib_url'] ); ?>" target="_blank" rel="noopener noreferrer">Consulter le RIB ↗</a></p><?php elseif ( $s['rib_note'] ) : ?><p><?php echo esc_html( $s['rib_note'] ); ?></p><?php endif; ?>
              <?php if ( $s['transfer_note'] ) : ?><p><?php echo nl2br( esc_html( $s['transfer_note'] ) ); ?></p><?php endif; ?>
              <h3>3. Remettre le dossier</h3>
              <p><?php echo nl2br( esc_html( $s['handover'] ) ); ?></p>
              <?php if ( $s['deadline'] ) : ?><p class="join-deadline"><strong>Avant le <?php echo esc_html( $s['deadline'] ); ?>.</strong></p><?php endif; ?>
            </div>
          </details>
          <?php endforeach; ?>
        </div>
        <?php if ( $s['deadline'] ) : ?><p class="join-deadline">Dossier complet à remettre avant le <strong><?php echo esc_html( $s['deadline'] ); ?></strong>.</p><?php endif; ?>
      </div>
    </details>
  </div>
  <p class="join-contact">Besoin d’aide ? <a href="#contact">Contactez le club</a>.</p>
</section>
