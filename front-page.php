<?php
/**
 * Page d’accueil one-page.
 *
 * @package RBC68
 */
get_header();
$rbc68_contact_status = isset( $_GET['contact'] ) ? sanitize_key( wp_unslash( $_GET['contact'] ) ) : '';
$rbc68_hero_image_id  = absint( get_theme_mod( 'rbc68_hero_image' ) );
$rbc68_club_image_id  = absint( get_theme_mod( 'rbc68_club_image' ) );
?>
<main id="contenu">
<!-- Introduction compacte -->
  <section id="accueil" class="hero<?php echo $rbc68_hero_image_id ? ' hero-has-image' : ''; ?>">
    <div class="hero-inner">
      <div class="hero-copy">
        <h1><?php echo esc_html( rbc68_mod( 'rbc68_hero_title', 'Riedisheim Badminton Club' ) ); ?></h1>
        <p><?php echo esc_html( rbc68_mod( 'rbc68_hero_text', 'Badminton loisir et compétition pour les adultes, les jeunes et le mini-bad, au complexe sportif C.M.C.A.S de Rixheim.' ) ); ?></p>
        <div class="hero-actions">
          <a href="#inscription" class="cta-button">Nous rejoindre</a>
          <a href="#horaires" class="cta-button cta-button-secondary">Voir les créneaux</a>
        </div>
      </div>
      <?php if ( $rbc68_hero_image_id ) : ?>
        <figure class="hero-visual">
          <?php echo wp_get_attachment_image( $rbc68_hero_image_id, 'large', false, array( 'class' => 'hero-photo' ) ); ?>
        </figure>
      <?php endif; ?>
    </div>
  </section>

  <!-- Les informations recherchées le plus souvent -->
  <section id="essentiel" class="essentials" aria-labelledby="essentiel-title">
    <div class="section-heading-inline">
      <div>
        <p class="eyebrow">L’essentiel en un coup d’œil</p>
        <h2 id="essentiel-title">Les informations pratiques</h2>
      </div>
    </div>
    <div class="essentials-grid">
      <article class="essential-card essential-location">
        <span class="essential-icon" aria-hidden="true">⌖</span>
        <div>
          <h3>Où jouer ?</h3>
          <p><?php echo esc_html( rbc68_mod( 'rbc68_address', 'Complexe sportif C.M.C.A.S, chemin de Brunstatt, 68170 Rixheim' ) ); ?></p>
          <p><a href="#contact">Voir la carte de la salle →</a></p>
          <div class="essential-links"><a href="<?php echo esc_url( rbc68_mod( 'rbc68_map_link', 'https://www.google.com/maps/dir/?api=1&destination=47.7331928%2C7.3777678' ) ); ?>" target="_blank" rel="noopener">Itinéraire</a><a href="<?php echo esc_url( rbc68_mod( 'rbc68_room_info', '#club' ) ); ?>">Infos sur la salle</a></div>
        </div>
      </article>
      <article class="essential-card">
        <span class="essential-icon" aria-hidden="true">◷</span>
        <div><h3>Quand ?</h3><dl class="essential-groups"><div><dt>Adultes <?php if ( rbc68_availability_label( 'adults' ) ) : ?><span class="availability availability-full">Complet</span><?php endif; ?></dt><dd><?php echo esc_html( rbc68_mod( 'rbc68_when_adults', 'Mardi, mercredi, vendredi et dimanche' ) ); ?></dd></div><div><dt>Jeunes <?php if ( rbc68_availability_label( 'youth' ) ) : ?><span class="availability availability-full">Complet</span><?php endif; ?></dt><dd><?php echo esc_html( rbc68_mod( 'rbc68_when_youth', 'Mardi, vendredi et dimanche' ) ); ?></dd></div><div><dt>Mini-bad <?php if ( rbc68_availability_label( 'mini' ) ) : ?><span class="availability availability-full">Complet</span><?php endif; ?></dt><dd><?php echo esc_html( rbc68_mod( 'rbc68_when_mini', 'Vendredi et dimanche' ) ); ?></dd></div></dl><a href="#horaires">Tous les horaires</a></div>
      </article>
      <article class="essential-card">
        <span class="essential-icon" aria-hidden="true">€</span>
        <div><h3>Combien ?</h3><dl class="essential-prices"><div><dt>Mini-bad</dt><dd><?php echo esc_html( rbc68_mod( 'rbc68_price_mini', '70 €' ) ); ?></dd></div><div><dt>Jeunes</dt><dd><?php echo esc_html( rbc68_mod( 'rbc68_price_youth', '85 €' ) ); ?></dd></div><div><dt>Adultes</dt><dd><?php echo esc_html( rbc68_mod( 'rbc68_price_adults', '105 €' ) ); ?></dd></div></dl><a href="#inscription">Tarifs et documents</a></div>
      </article>
      <article class="essential-card essential-card-accent">
        <span class="essential-icon" aria-hidden="true">✓</span>
        <div><h3>Envie de jouer ?</h3><p><?php echo esc_html( rbc68_mod( 'rbc68_join_text', 'Nouvelles inscriptions sur place, auprès du responsable de salle. Reprise le 1er septembre 2026.' ) ); ?></p><a href="#inscription">Nous rejoindre</a></div>
      </article>
    </div>
  </section>

  <!-- Signalétique pour les visiteurs réguliers et occasionnels -->
  <section id="en-ce-moment" class="spotlight" aria-labelledby="spotlight-title">
    <div class="spotlight-label"><span></span><h2 id="spotlight-title">En ce moment</h2></div>
    <div class="spotlight-grid">
      <article class="spotlight-panel spotlight-events">
        <div class="spotlight-panel-heading"><span class="spotlight-type">Prochains événements</span><a href="#calendrier">Tout le calendrier →</a></div>
        <div class="spotlight-event-list">
          <?php $rbc68_upcoming = rbc68_get_upcoming_events( 3 ); ?>
          <?php if ( $rbc68_upcoming ) : ?>
            <?php foreach ( $rbc68_upcoming as $rbc68_event ) : ?>
              <?php $rbc68_event_date = get_post_meta( $rbc68_event->ID, 'rbc68_event_date', true ); ?>
              <a href="<?php echo esc_url( rbc68_event_article_url( $rbc68_event->ID ) ); ?>" class="spotlight-event-row"><time datetime="<?php echo esc_attr( $rbc68_event_date ); ?>"><?php echo esc_html( rbc68_event_when_label( $rbc68_event->ID ) ); ?></time><strong><?php echo esc_html( get_the_title( $rbc68_event ) ); ?> <span class="event-category-inline event-category-<?php echo esc_attr( rbc68_event_kind( $rbc68_event->ID ) ); ?>"><?php echo esc_html( rbc68_event_kind_label( $rbc68_event->ID ) ); ?></span></strong><span aria-hidden="true">→</span></a>
            <?php endforeach; ?>
          <?php else : ?>
            <p class="spotlight-empty">Les prochains événements seront annoncés ici.</p>
          <?php endif; ?>
        </div>
      </article>
      <?php
      $rbc68_latest = new WP_Query(
        array(
          'post_type'           => 'post',
          'post_status'         => 'publish',
          'posts_per_page'      => 1,
          'ignore_sticky_posts' => true,
        )
      );
      ?>
      <?php if ( $rbc68_latest->have_posts() ) : $rbc68_latest->the_post(); ?>
        <article class="spotlight-panel spotlight-news">
          <span class="spotlight-type">Dernière actualité</span>
          <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
          <p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 24, '…' ) ); ?></p>
          <a class="spotlight-news-link" href="<?php the_permalink(); ?>"><?php echo esc_html( get_the_date() ); ?> · Lire l’article →</a>
        </article>
        <?php wp_reset_postdata(); ?>
      <?php else : ?>
        <article class="spotlight-panel spotlight-news">
          <span class="spotlight-type">Actualités du club</span>
          <h3>Les nouvelles du RBC68 paraîtront ici</h3>
          <p>Résultats, rendez-vous et vie du club : retrouvez ici les dernières nouvelles.</p>
          <a class="spotlight-news-link" href="#actualites">Consulter les actualités →</a>
        </article>
      <?php endif; ?>
    </div>
  </section>

  <?php ob_start(); ?>
  <!-- Le Club -->
  <section id="club">
    <h2 class="section-title">Le Club</h2>
    <div class="club-card">
      <div class="club-intro<?php echo $rbc68_club_image_id ? ' club-intro-has-image' : ''; ?>">
        <div class="club-copy">
          <p>
            Le <strong>Riedisheim Badminton Club (RBC68)</strong> a été créé le 3 mars 2016 suite à la restructuration de l'ASCAR.
            L'association est affiliée à la <strong>Fédération Française de Badminton</strong>, à la Ligue Régionale et au Comité Départemental.
          </p>
          <p>
            <strong>Lieu de pratique :</strong> <a href="https://www.google.com/maps/place/47.7331928,7.3777678" target="_blank" rel="noopener noreferrer">Complexe sportif C.M.C.A.S, chemin de Brunstatt, 68170 Rixheim</a>
          </p>
        </div>
        <?php if ( $rbc68_club_image_id ) : ?>
          <figure class="club-visual">
            <?php echo wp_get_attachment_image( $rbc68_club_image_id, 'large', false, array( 'class' => 'club-photo' ) ); ?>
          </figure>
        <?php endif; ?>
      </div>
      
      <?php $rbc68_members = rbc68_get_committee(); ?>
      <?php if ( $rbc68_members ) : ?>
      <div class="club-committee">
        <h3>Comité</h3>
        <ul class="club-committee-grid">
          <?php foreach ( $rbc68_members as $rbc68_member ) : ?>
            <?php $rbc68_member_role = get_post_meta( $rbc68_member->ID, 'rbc68_member_role', true ); ?>
            <li class="committee-card">
              <strong><?php echo esc_html( trim( get_post_meta( $rbc68_member->ID, 'rbc68_member_first_name', true ) . ' ' . $rbc68_member->post_title ) ); ?></strong>
              <?php if ( '' !== $rbc68_member_role ) : ?>
                <span><?php echo esc_html( $rbc68_member_role ); ?></span>
              <?php endif; ?>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
      <?php endif; ?>
    </div>
  </section>

  <?php $rbc68_section_club = ob_get_clean(); ob_start(); ?>
  <!-- ======================================== -->
  <!-- INSCRIPTION - Modalités d'inscription -->
  <!-- ======================================== -->
  <section id="inscription">
    <h2 class="section-title">Nous rejoindre</h2>
    
    <div class="inscription-container">
      <!-- Informations générales -->
      <div class="inscription-info">
        <h3>Comment nous rejoindre ?</h3>
        <p>
          <strong>Nouvelles inscriptions :</strong> Les inscriptions ont lieu <strong>sur place auprès du responsable de salle</strong>, 
          au début ou à la fin de chaque entraînement.
        </p>
        <p style="margin-top: 1rem;">
          <strong>Réinscription :</strong> Les personnes déjà inscrites la saison précédente peuvent se réinscrire directement 
          via la <a href="https://licence.ffbad.org/" target="_blank" style="color: var(--primary); text-decoration: none; font-weight: 600;">plateforme en ligne de la Fédération Française de Badminton</a>.
        </p>
        <p style="margin-top: 1rem;">
          <strong>Date limite :</strong> 25 septembre 2026
        </p>
        <p style="margin-top: 1rem;">
          <strong>Reprise des entraînements :</strong> 1er septembre 2026
        </p>
        <p class="trial-highlight" style="margin-top: 1rem;"><strong>Deux séances d’essai gratuites</strong> sont proposées avant de finaliser votre inscription.</p>
      </div>
      
      <!-- Tarifs -->
      <div class="inscription-tarifs">
        <h3>Tarifs Saison 2026-2027</h3>
        <div class="tarifs-grid">
          <div class="tarif-card">
            <h4>Section Mini-Bad</h4>
            <?php if ( rbc68_availability_label( 'mini' ) ) : ?><p class="availability availability-full">Complet</p><?php endif; ?>
            <p class="tarif-price">70 €</p>
            <p class="tarif-payment">
              Par chèque à l'ordre du <strong>Riedisheim Badminton Club</strong> 
              ou par virement bancaire avec l'intitulé :
            </p>
            <p class="tarif-reference">"Inscription 2026-2027 + Nom Prénom de l'Enfant"</p>
          </div>
          <div class="tarif-card">
            <h4>Section Jeune</h4>
            <?php if ( rbc68_availability_label( 'youth' ) ) : ?><p class="availability availability-full">Complet</p><?php endif; ?>
            <p class="tarif-price">85 €</p>
            <p class="tarif-payment">
              Par chèque à l'ordre du <strong>Riedisheim Badminton Club</strong> 
              ou par virement bancaire avec l'intitulé :
            </p>
            <p class="tarif-reference">"Inscription 2026-2027 + Nom Prénom de l'Enfant"</p>
          </div>
          <div class="tarif-card">
            <h4>Section Adulte</h4>
            <?php if ( rbc68_availability_label( 'adults' ) ) : ?><p class="availability availability-full">Complet</p><?php endif; ?>
            <p class="tarif-price">105 €</p>
            <p class="tarif-payment">
              Par chèque à l'ordre du <strong>Riedisheim Badminton Club</strong> 
              ou par virement bancaire avec l'intitulé :
            </p>
            <p class="tarif-reference">"Inscription saison 2026-2027 + votre Nom Prénom"</p>
          </div>
        </div>
        <p style="margin-top: 1.5rem; font-size: 0.9rem; color: var(--gray);">
          <strong>Note :</strong> Le RIB est disponible sur demande auprès des responsables du club.
        </p>
      </div>
      
      <!-- Documents à fournir -->
      <div class="inscription-documents">
        <h3>Documents à fournir</h3>
        <ul>
          <li>Fiche d'inscription complétée (disponible sur place)</li>
          <li>Certificat médical de non-contre-indication à la pratique du badminton</li>
          <li>Pour les mineurs : autorisation parentale</li>
          <li>Règlement de la cotisation (chèque ou justificatif de virement)</li>
        </ul>
      </div>
    </div>
  </section>

  <?php $rbc68_section_join = ob_get_clean(); ob_start(); ?>
  <!-- Horaires -->
  <section id="horaires">
    <h2 class="section-title">Nos Horaires</h2>
    <div class="tabs">
      <button class="tab-button active" data-tab="adulte" aria-controls="adulte" aria-selected="true">Adulte</button>
      <button class="tab-button" data-tab="jeune" aria-controls="jeune" aria-selected="false">Jeune</button>
      <button class="tab-button" data-tab="mini" aria-controls="mini" aria-selected="false">Mini-Bad</button>
    </div>
    <div class="schedule">
      <table class="schedule-table" id="adulte">
        <thead>
          <tr>
            <th>Jour</th>
            <th>Horaire</th>
            <th>Lieu</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Mardi</td>
            <td>20h00 - 22h30</td>
            <td>C.M.C.A.S Rixheim</td>
          </tr>
          <tr>
            <td>Mercredi</td>
            <td>20h00 - 22h30</td>
            <td>C.M.C.A.S Rixheim</td>
          </tr>
          <tr>
            <td>Vendredi</td>
            <td>20h00 - 22h30</td>
            <td>C.M.C.A.S Rixheim</td>
          </tr>
          <tr>
            <td>Dimanche</td>
            <td>09h00 - 12h00</td>
            <td>C.M.C.A.S Rixheim</td>
          </tr>
        </tbody>
      </table>
      <table class="schedule-table" id="jeune" style="display: none;">
        <thead>
          <tr>
            <th>Jour</th>
            <th>Horaire</th>
            <th>Lieu</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Mardi</td>
            <td>18h00 - 20h00</td>
            <td>C.M.C.A.S Rixheim</td>
          </tr>
          <tr>
            <td>Vendredi</td>
            <td>18h00 - 20h00</td>
            <td>C.M.C.A.S Rixheim</td>
          </tr>
          <tr>
            <td>Dimanche</td>
            <td>09h00 - 12h00</td>
            <td>C.M.C.A.S Rixheim</td>
          </tr>
        </tbody>
      </table>
      <table class="schedule-table" id="mini" style="display: none;">
        <thead>
          <tr>
            <th>Jour</th>
            <th>Horaire</th>
            <th>Lieu</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Vendredi</td>
            <td>17h30 - 19h30</td>
            <td>C.M.C.A.S Rixheim</td>
          </tr>
          <tr>
            <td>Dimanche</td>
            <td>09h00 - 12h00</td>
            <td>C.M.C.A.S Rixheim</td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>

  <?php $rbc68_section_schedule = ob_get_clean(); ob_start(); ?>
  <!-- ======================================== -->
  <!-- CALENDRIER - Événements de la saison -->
  <!-- ======================================== -->
  <section id="calendrier">
    <h2 class="section-title">Évènements</h2>
    <div style="background: var(--card-bg); padding: 2rem; border-radius: 0.75rem; box-shadow: var(--shadow); color: var(--text-color);">
      <div class="events-list">
        <?php
        $rbc68_calendar = new WP_Query(
          array(
            'post_type'      => 'rbc68_event',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'meta_key'       => 'rbc68_event_date',
            'orderby'        => 'meta_value',
            'order'          => 'ASC',
          )
        );
        $rbc68_groups = array( 'upcoming' => 'À venir', 'past' => 'Passés — saison en cours' );
        $rbc68_archives = array();
        foreach ( $rbc68_calendar->posts as $event ) {
          $group = rbc68_event_archive_group( $event->ID );
          if ( is_numeric( $group ) ) {
            $rbc68_archives[$group] = $group . '–' . ( (int) $group + 1 );
          }
        }
        krsort( $rbc68_archives );
        $rbc68_groups += $rbc68_archives;
        $rbc68_selected = isset( $_GET['evenements'] ) && is_string( $_GET['evenements'] ) ? sanitize_key( wp_unslash( $_GET['evenements'] ) ) : 'upcoming';
        if ( ! isset( $rbc68_groups[$rbc68_selected] ) ) { $rbc68_selected = 'upcoming'; }
        $rbc68_shown = 0;
        ?>
        <nav class="event-archive-tabs" aria-label="Période des événements">
          <?php foreach ( $rbc68_groups as $group => $label ) : ?>
            <a href="<?php echo esc_url( add_query_arg( 'evenements', $group, home_url( '/' ) ) . '#calendrier' ); ?>"<?php if ( (string) $group === $rbc68_selected ) : ?> aria-current="page"<?php endif; ?>><?php echo esc_html( $label ); ?></a>
          <?php endforeach; ?>
        </nav>
        <?php if ( $rbc68_calendar->have_posts() ) : ?>
          <?php while ( $rbc68_calendar->have_posts() ) : $rbc68_calendar->the_post(); ?>
            <?php
            $rbc68_event_id = get_the_ID();
            if ( rbc68_event_archive_group( $rbc68_event_id ) !== $rbc68_selected ) { continue; }
            ++$rbc68_shown;
            $rbc68_start    = get_post_meta( $rbc68_event_id, 'rbc68_event_date', true );
            $rbc68_end      = get_post_meta( $rbc68_event_id, 'rbc68_event_end_date', true );
            $rbc68_kind     = get_post_meta( $rbc68_event_id, 'rbc68_event_kind', true );
            $rbc68_team     = get_post_meta( $rbc68_event_id, 'rbc68_event_team', true );
            $rbc68_location = get_post_meta( $rbc68_event_id, 'rbc68_event_location', true );
            $rbc68_details  = get_post_meta( $rbc68_event_id, 'rbc68_event_details', true );
            $rbc68_map      = rbc68_event_map_embed_url( $rbc68_event_id );
            $rbc68_map_link = rbc68_event_map_link( $rbc68_event_id );
            $rbc68_past     = rbc68_event_is_past( $rbc68_event_id );
            ?>
            <article class="event-item<?php echo $rbc68_past ? ' event-past' : ''; ?>">
              <div class="event-main">
                <div class="event-date">📅 <?php echo esc_html( rbc68_event_when_label( $rbc68_event_id ) ); ?></div>
                <h3 class="event-title"><?php the_title(); ?></h3>
                <p class="event-category event-category-<?php echo esc_attr( rbc68_event_kind( $rbc68_event_id ) ); ?>"><?php echo esc_html( rbc68_event_kind_label( $rbc68_event_id ) ); ?><?php echo $rbc68_team && 'interclub' === rbc68_event_kind( $rbc68_event_id ) ? ' · Équipe ' . esc_html( $rbc68_team ) : ''; ?></p>
                <?php if ( $rbc68_location ) : ?><p class="event-location"><strong>Lieu :</strong> <?php echo esc_html( $rbc68_location ); ?></p><?php endif; ?>
                <?php if ( $rbc68_details ) : ?><p class="event-details"><?php echo esc_html( $rbc68_details ); ?></p><?php endif; ?>
                <div class="event-actions">
                  <?php if ( $rbc68_map_link ) : ?><a href="<?php echo esc_url( $rbc68_map_link ); ?>" target="_blank" rel="noopener">Itinéraire →</a><?php endif; ?>
                  <?php if ( get_post_meta( $rbc68_event_id, 'rbc68_event_article_url', true ) ) : ?><a href="<?php echo esc_url( rbc68_event_article_url( $rbc68_event_id ) ); ?>">Article dédié →</a><?php endif; ?>
                  <?php if ( ! $rbc68_past ) : ?><a class="event-calendar-link" href="<?php echo esc_url( rbc68_calendar_url( $rbc68_event_id ) ); ?>">Ajouter au calendrier</a><?php endif; ?>
                </div>
              </div>
              <?php if ( $rbc68_map ) : ?><div class="event-map"><iframe title="Carte du lieu de <?php echo esc_attr( get_the_title() ); ?>" loading="lazy" src="<?php echo esc_url( $rbc68_map ); ?>"></iframe></div><?php endif; ?>
            </article>
          <?php endwhile; wp_reset_postdata(); ?>
        <?php endif; ?>
        <?php if ( ! $rbc68_shown ) : ?><p>Aucun événement dans cette période.</p><?php endif; ?>
        </div>
      </div>
  </section>

  <?php $rbc68_section_calendar = ob_get_clean(); ob_start(); ?>
  <!-- Galerie Photo -->
  <section id="galerie">
    <h2 class="section-title">Galerie Photo</h2>
    <div class="gallery">
      <div class="gallery-item" data-index="0">
        <img src="<?php echo esc_attr( rbc68_gallery_image( 1, rbc68_placeholder_image( 'Terrains de badminton', '#182d52', '#fefefe', 400, 300 ) ) ); ?>" alt="Terrains de badminton" class="gallery-img">
        <div class="gallery-overlay">Voir la photo</div>
      </div>
     <div class="gallery-item" data-index="1">
        <img src="<?php echo esc_attr( rbc68_gallery_image( 2, rbc68_placeholder_image( 'Équipements', '#5f708c', '#fefefe', 400, 300 ) ) ); ?>" alt="Équipements" class="gallery-img">
        <div class="gallery-overlay">Voir la photo</div>
      </div>
    </div>
  </section>

  <!-- Lightbox -->
  <div class="lightbox" id="lightbox" role="dialog" aria-modal="true" aria-label="Agrandissement de la photo">
    <button class="lightbox-close" id="lightbox-close" aria-label="Fermer">&times;</button>
    <button class="lightbox-nav prev" id="lightbox-prev" aria-label="Photo précédente">&#10094;</button>
    <div class="lightbox-content">
      <img src="" alt="" class="lightbox-img" id="lightbox-img">
    </div>
    <button class="lightbox-nav next" id="lightbox-next" aria-label="Photo suivante">&#10095;</button>
  </div>

  <?php $rbc68_section_gallery = ob_get_clean(); ob_start(); ?>
  <!-- ======================================== -->
  <!-- Actualités / Blog -->
  <section id="actualites">
    <h2 class="section-title">Actualités du Club</h2>
    <div class="blog-grid">
      <?php
      $rbc68_news = new WP_Query(
        array(
          'post_type'           => 'post',
          'post_status'         => 'publish',
          'posts_per_page'      => 3,
          'ignore_sticky_posts' => true,
        )
      );
      ?>
      <?php if ( $rbc68_news->have_posts() ) : ?>
        <?php while ( $rbc68_news->have_posts() ) : $rbc68_news->the_post(); ?>
          <article <?php post_class( 'blog-card' ); ?>>
            <a href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr( get_the_title() ); ?>">
              <?php if ( has_post_thumbnail() ) : ?>
                <?php the_post_thumbnail( 'large', array( 'class' => 'blog-img' ) ); ?>
              <?php else : ?>
                <img src="<?php echo esc_attr( rbc68_placeholder_image( get_the_title(), '#182d52', '#fefefe', 400, 200 ) ); ?>" alt="" class="blog-img">
              <?php endif; ?>
            </a>
            <div class="blog-content">
              <div class="blog-date"><?php echo esc_html( get_the_date() ); ?></div>
              <h3 class="blog-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
              <div class="blog-excerpt"><?php the_excerpt(); ?></div>
              <a href="<?php the_permalink(); ?>" class="blog-link">Lire la suite →</a>
            </div>
          </article>
        <?php endwhile; ?>
        <?php wp_reset_postdata(); ?>
      <?php else : ?>
        <p class="rbc68-no-posts">Les prochaines actualités du club paraîtront ici.</p>
      <?php endif; ?>
    </div>
  </section>

  <?php $rbc68_section_news = ob_get_clean(); ob_start(); ?>
  <!-- ======================================== -->
  <!-- CONTACT - Section avec formulaire fonctionnel -->
  <!-- ======================================== -->
  <section id="contact">
    <h2 class="section-title">Contact</h2>
    <div class="contact-container">
      <!-- Informations de contact -->
      <div class="contact-info">
        <h3>Nos coordonnées</h3>
        <p><strong>Adresse :</strong></p>
        <p><?php echo esc_html( rbc68_mod( 'rbc68_address', 'Complexe sportif C.M.C.A.S, chemin de Brunstatt, 68170 Rixheim' ) ); ?></p>
        
        <p><a href="<?php echo esc_url( rbc68_mod( 'rbc68_map_link', 'https://www.google.com/maps/dir/?api=1&destination=47.7331928%2C7.3777678' ) ); ?>" target="_blank" rel="noopener">Calculer un itinéraire →</a></p>
        <div class="contact-location-map">
          <iframe title="Localiser la salle du RBC68" loading="lazy" src="<?php echo esc_url( rbc68_mod( 'rbc68_map_embed', 'https://www.openstreetmap.org/export/embed.html?bbox=7.3705%2C47.7295%2C7.3850%2C47.7370&layer=mapnik&marker=47.7331928%2C7.3777678' ) ); ?>"></iframe>
        </div>
        
        <p style="margin-top: 1rem;"><strong>Email :</strong> <a href="mailto:<?php echo esc_attr( rbc68_mod( 'rbc68_email', 'contact@rbc68.fr' ) ); ?>" style="color: var(--primary); text-decoration: none;"><?php echo esc_html( rbc68_mod( 'rbc68_email', 'contact@rbc68.fr' ) ); ?></a></p>
        <p style="margin-top: 1rem;"><strong>Réseaux sociaux :</strong></p>
        <div class="contact-social">
          <a href="<?php echo esc_url( rbc68_mod( 'rbc68_facebook', 'https://facebook.com/p/Riedisheim-Badminton-Club-100024060309053/' ) ); ?>" class="social-link" target="_blank" rel="noopener noreferrer">
            <svg class="social-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
            Facebook
          </a>
          <a href="<?php echo esc_url( rbc68_mod( 'rbc68_instagram', 'https://instagram.com/rbc68/' ) ); ?>" class="social-link" target="_blank" rel="noopener noreferrer">
            <svg class="social-icon" viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.85s-.011 3.584-.069 4.85c-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07s-3.584-.012-4.85-.07c-3.252-.148-4.771-1.691-4.919-4.919-.058-1.265-.069-1.645-.069-4.85s.011-3.584.069-4.85c.149-3.225 1.664-4.771 4.919-4.919C8.416 2.175 8.796 2.163 12 2.163m0-2.163C8.74 0 8.333.011 7.053.069 2.695.287.287 2.695.069 7.053.011 8.333 0 8.74 0 12s.011 3.667.069 4.947c.228 4.358 2.636 7.88 6.983 8.108 1.234.058 1.624.069 4.947.069s3.713-.011 4.947-.069c4.347-.228 7.87-.636 8.108-6.983.058-1.28.069-1.687.069-4.947s-.011-3.667-.069-4.947C21.713 2.695 18.287.287 13.947.069 12.667.011 12.26 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.88 1.44 1.44 0 000-2.88z"/></svg>
            Instagram
          </a>
        </div>
      </div>
      
      <!-- Formulaire de contact -->
      <div class="contact-form-container">
        <h3>Envoyez-nous un message</h3>
        <form id="contactForm" class="contact-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
          <input type="hidden" name="action" value="rbc68_contact">
          <?php wp_nonce_field( 'rbc68_contact', 'rbc68_contact_nonce' ); ?>
          <div class="rbc68-honeypot" aria-hidden="true">
            <label for="website">Ne pas remplir ce champ</label>
            <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
          </div>
          <!-- Champ Nom -->
          <div class="form-group">
            <label for="name">Nom et Prénom *</label>
            <input type="text" id="name" name="name" required placeholder="Votre nom complet">
            <span class="error-message" id="nameError"></span>
          </div>
          
          <!-- Champ Email -->
          <div class="form-group">
            <label for="email">Adresse Email *</label>
            <input type="email" id="email" name="email" required placeholder="votre@email.com">
            <span class="error-message" id="emailError"></span>
          </div>
          
          <!-- Champ Téléphone -->
          <div class="form-group">
            <label for="phone">Téléphone</label>
            <input type="tel" id="phone" name="phone" placeholder="06 12 34 56 78">
            <span class="error-message" id="phoneError"></span>
          </div>
          
          <!-- Champ Sujet -->
          <div class="form-group">
            <label for="subject">Sujet *</label>
            <select id="subject" name="subject" required>
              <option value="" disabled selected>Sélectionnez un sujet</option>
              <option value="inscription">Nous rejoindre</option>
              <option value="renseignement">Demande de renseignement</option>
              <option value="tournoi">Information sur les tournois</option>
              <option value="autre">Autre</option>
            </select>
            <span class="error-message" id="subjectError"></span>
          </div>
          
          <!-- Champ Message -->
          <div class="form-group">
            <label for="message">Votre message *</label>
            <textarea id="message" name="message" rows="5" required placeholder="Décrivez votre demande..."></textarea>
            <span class="error-message" id="messageError"></span>
          </div>
          
          <!-- Checkbox RGPD -->
          <div class="form-group checkbox-group">
            <input type="checkbox" id="consent" name="consent" required>
            <label for="consent">J'accepte que mes données soient utilisées pour me recontacter *</label>
            <span class="error-message" id="consentError"></span>
          </div>
          
          <!-- Bouton de soumission -->
          <button type="submit" class="submit-button">
            <span>Envoyer le message</span>
            <span class="button-loader" style="display: none;">⏳</span>
          </button>
          
          <!-- Messages de feedback -->
          <div id="formSuccess" class="form-message success" role="status" aria-live="polite"<?php echo ( 'success' === $rbc68_contact_status ) ? '' : ' style="display: none;"'; ?>>
            ✅ Votre message a été envoyé avec succès ! Nous vous répondrons dans les plus brefs délais.
          </div>
          <div id="formError" class="form-message error" role="alert"<?php echo ( 'error' === $rbc68_contact_status ) ? '' : ' style="display: none;"'; ?>>
            ❌ Une erreur est survenue. Veuillez réessayer plus tard.
          </div>
        </form>
        <p style="margin-top: 1rem; font-size: 0.9rem; color: var(--gray); text-align: right;">
          * Les champs marqués d'un astérisque sont obligatoires.
        </p>
      </div>
    </div>
  </section>

  <?php
  $rbc68_section_contact = ob_get_clean();
  echo $rbc68_section_schedule; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
  echo $rbc68_section_calendar; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
  echo $rbc68_section_news; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
  echo $rbc68_section_club; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
  echo $rbc68_section_contact; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
  echo $rbc68_section_join; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
  echo $rbc68_section_gallery; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
  ?>
  <!-- Footer -->
</main>
<?php get_footer(); ?>
