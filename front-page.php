<?php
/**
 * Page d’accueil one-page.
 *
 * @package RBC68
 */
get_header();
$rbc68_contact_status = isset( $_GET['contact'] ) ? sanitize_key( wp_unslash( $_GET['contact'] ) ) : '';
?>
<main id="contenu">
<!-- Hero Section -->
  <section id="accueil" class="hero">
    <h1><?php echo esc_html( rbc68_mod( 'rbc68_hero_title', 'Riedisheim Badminton Club' ) ); ?></h1>
    <p><?php echo esc_html( rbc68_mod( 'rbc68_hero_text', 'Découvrez la passion du badminton dans une ambiance conviviale et sportive. Ouvert à tous les niveaux, du débutant au compétiteur.' ) ); ?></p>
    <a href="#inscription" class="cta-button">Rejoindre le club</a>
  </section>

  <!-- Le Club -->
  <section id="club">
    <h2 class="section-title">Le Club</h2>
    <div style="background: var(--card-bg); padding: 2rem; border-radius: 0.75rem; box-shadow: var(--shadow); text-align: center; color: var(--text-color);">
      <p style="font-size: 1.1rem; line-height: 1.8; max-width: 800px; margin: 0 auto;">
        Le <strong>Riedisheim Badminton Club (RBC68)</strong> a été créé le 3 mars 2016 suite à la restructuration de l'ASCAR.
        L'association est affiliée à la <strong>Fédération Française de Badminton</strong>, à la Ligue Régionale et au Comité Départemental.
      </p>
      <p style="margin-top: 1.5rem; font-size: 1.1rem;">
        <strong>Lieu de pratique :</strong> <a href="https://www.google.com/maps/place/47.7331928,7.3777678" target="_blank" style="color: var(--primary); text-decoration: none;">Complexe sportif C.M.C.A.S, chemin de Brunstatt, 68170 Rixheim</a>
      </p>
      
      <!-- Organigramme -->
      <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
        <h3 style="color: var(--primary); margin-bottom: 1rem;">Organigramme</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; text-align: left;">
          <div class="organigramme-card" style="padding: 1rem;">
            <strong>Président :</strong> Julien GEIGER
          </div>
          <div class="organigramme-card" style="padding: 1rem;">
            <strong>Vice-Président :</strong> Yves WALTER
          </div>
          <div class="organigramme-card" style="padding: 1rem;">
            <strong>Secrétaire :</strong> Rachel BAUMANN
          </div>
          <div class="organigramme-card" style="padding: 1rem;">
            <strong>Trésorier :</strong> Jean-Christophe DENNI
          </div>
          <div class="organigramme-card" style="padding: 1rem;">
            <strong>Directeur Technique :</strong> Pierre NOTTER
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ======================================== -->
  <!-- INSCRIPTION - Modalités d'inscription -->
  <!-- ======================================== -->
  <section id="inscription">
    <h2 class="section-title">Modalités d'inscription</h2>
    
    <div class="inscription-container">
      <!-- Informations générales -->
      <div class="inscription-info">
        <h3>Comment s'inscrire ?</h3>
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
      </div>
      
      <!-- Tarifs -->
      <div class="inscription-tarifs">
        <h3>Tarifs Saison 2026-2027</h3>
        <div class="tarifs-grid">
          <div class="tarif-card">
            <h4>Section Mini-Bad</h4>
            <p class="tarif-price">70 €</p>
            <p class="tarif-payment">
              Par chèque à l'ordre du <strong>Riedisheim Badminton Club</strong> 
              ou par virement bancaire avec l'intitulé :
            </p>
            <p class="tarif-reference">"Inscription 2026-2027 + Nom Prénom de l'Enfant"</p>
          </div>
          <div class="tarif-card">
            <h4>Section Jeune</h4>
            <p class="tarif-price">85 €</p>
            <p class="tarif-payment">
              Par chèque à l'ordre du <strong>Riedisheim Badminton Club</strong> 
              ou par virement bancaire avec l'intitulé :
            </p>
            <p class="tarif-reference">"Inscription 2026-2027 + Nom Prénom de l'Enfant"</p>
          </div>
          <div class="tarif-card">
            <h4>Section Adulte</h4>
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

  <!-- ======================================== -->
  <!-- CALENDRIER - Événements de la saison -->
  <!-- ======================================== -->
  <section id="calendrier">
    <h2 class="section-title">Évènements saison 2026-2027</h2>
    <div style="background: var(--card-bg); padding: 2rem; border-radius: 0.75rem; box-shadow: var(--shadow); color: var(--text-color);">
      <div class="events-list">
          <div class="event-item" role="button" tabindex="0" data-title="Démarrage de la saison (section jeune et adulte)" data-start="20260901" data-end="20260902" aria-label="Télécharger cet événement au format calendrier">
            <div class="event-date">📅 1er septembre 2026</div>
            <div class="event-title">Démarrage de la saison (section jeune et adulte)</div>
          </div>
          <div class="event-item" role="button" tabindex="0" data-title="Démarrage de la saison (section adulte compétiteurs)" data-start="20260902" data-end="20260903" aria-label="Télécharger cet événement au format calendrier">
            <div class="event-date">📅 2 septembre 2026</div>
            <div class="event-title">Démarrage de la saison (section adulte compétiteurs)</div>
          </div>
          <div class="event-item" role="button" tabindex="0" data-title="Réunion de rentrée avec section jeune et mini-bad + Démarrage mini-bad" data-start="20260904" data-end="20260905" aria-label="Télécharger cet événement au format calendrier">
            <div class="event-date">📅 4 septembre 2026</div>
            <div class="event-title">Réunion de rentrée avec section jeune et mini-bad + Démarrage mini-bad</div>
          </div>
          <div class="event-item" role="button" tabindex="0" data-title="Journées d'Automne et des Associations" data-start="20260905" data-end="20260907" aria-label="Télécharger cet événement au format calendrier">
            <div class="event-date">📅 5-6 septembre 2026</div>
            <div class="event-title">Journées d'Automne et des Associations</div>
          </div>
          <div class="event-item" role="button" tabindex="0" data-title="Octobre rose" data-start="20261002" data-end="20261003" aria-label="Télécharger cet événement au format calendrier">
            <div class="event-date">📅 2 octobre 2026</div>
            <div class="event-title">Octobre rose</div>
          </div>
          <div class="event-item" role="button" tabindex="0" data-title="Halloween" data-start="20261031" data-end="20261101" aria-label="Télécharger cet événement au format calendrier">
            <div class="event-date">📅 31 octobre 2026</div>
            <div class="event-title">Halloween</div>
          </div>
          <div class="event-item" role="button" tabindex="0" data-title="Assemblée Générale (date à définir)" data-start="20261115" data-end="20261202" aria-label="Télécharger cet événement au format calendrier">
            <div class="event-date">📅 Mi-novembre à début décembre 2026</div>
            <div class="event-title">Assemblée Générale (date à définir)</div>
          </div>
          <div class="event-item" role="button" tabindex="0" data-title="Fête/rassemblement de fin d'année" data-start="20261218" data-end="20261219" aria-label="Télécharger cet événement au format calendrier">
            <div class="event-date">📅 18 décembre 2026</div>
            <div class="event-title">Fête/rassemblement de fin d'année</div>
          </div>
          <div class="event-item" role="button" tabindex="0" data-title="Stage Club" data-start="20270207" data-end="20270208" aria-label="Télécharger cet événement au format calendrier">
            <div class="event-date">📅 7 février 2027</div>
            <div class="event-title">Stage Club</div>
          </div>
          <div class="event-item" role="button" tabindex="0" data-title="Bad brunch" data-start="20270321" data-end="20270322" aria-label="Télécharger cet événement au format calendrier">
            <div class="event-date">📅 21 mars 2027</div>
            <div class="event-title">Bad brunch</div>
          </div>
          <div class="event-item" role="button" tabindex="0" data-title="Événement parents/jeunes" data-start="20270605" data-end="20270606" aria-label="Télécharger cet événement au format calendrier">
            <div class="event-date">📅 5 juin 2027</div>
            <div class="event-title">Événement parents/jeunes</div>
          </div>
          <div class="event-item" role="button" tabindex="0" data-title="Fête de fin de saison" data-start="20270627" data-end="20270628" aria-label="Télécharger cet événement au format calendrier">
            <div class="event-date">📅 27 juin 2027</div>
            <div class="event-title">Fête de fin de saison</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Galerie Photo -->
  <section id="galerie">
    <h2 class="section-title">Galerie Photo</h2>
    <div class="gallery">
      <div class="gallery-item" data-index="0">
        <img src="<?php echo esc_attr( rbc68_gallery_image( 1, rbc68_placeholder_image( 'Terrains de badminton', '#e0f2fe', '#0284c7', 400, 300 ) ) ); ?>" alt="Terrains de badminton" class="gallery-img">
        <div class="gallery-overlay">Voir la photo</div>
      </div>
     <div class="gallery-item" data-index="1">
        <img src="<?php echo esc_attr( rbc68_gallery_image( 2, rbc68_placeholder_image( 'Équipements', '#f3e8ff', '#7c3aed', 400, 300 ) ) ); ?>" alt="Équipements" class="gallery-img">
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
                <img src="<?php echo esc_attr( rbc68_placeholder_image( get_the_title(), '#e0f2fe', '#0284c7', 400, 200 ) ); ?>" alt="" class="blog-img">
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
        
        <!-- Mini-carte OpenStreetMap -->
        <div class="map-container" style="margin-top: 1rem; border-radius: 0.5rem; box-shadow: var(--shadow);">
          <iframe
            title="Carte du Complexe sportif C.M.C.A.S"
            width="100%" 
            height="200" 
            style="border: none; border-radius: 0.5rem;" 
            loading="lazy" 
            referrerpolicy="no-referrer-when-downgrade" 
            src="https://www.openstreetmap.org/export/embed.html?bbox=7.37,47.73,7.38,47.74&layer=mapnik&marker=47.7331928,7.3777678">
          </iframe>
          <a href="https://www.openstreetmap.org/#map=17/47.7331928/7.3777678" style="display: block; text-align: right; font-size: 0.8rem; color: var(--gray); text-decoration: none; padding: 0.3rem 0.5rem; background: rgba(0,0,0,0.05);">
            © OpenStreetMap contributors
          </a>
        </div>
        
        <p style="margin-top: 1rem;"><strong>Email :</strong> <a href="mailto:<?php echo esc_attr( rbc68_mod( 'rbc68_email', 'contact@rbc68.fr' ) ); ?>" style="color: var(--primary); text-decoration: none;"><?php echo esc_html( rbc68_mod( 'rbc68_email', 'contact@rbc68.fr' ) ); ?></a></p>
        <p style="margin-top: 1rem;"><strong>Réseaux sociaux :</strong></p>
        <div class="contact-social">
          <a href="<?php echo esc_url( rbc68_mod( 'rbc68_facebook', 'https://facebook.com/p/Riedisheim-Badminton-Club-100024060309053/' ) ); ?>" class="social-link" target="_blank" rel="noopener noreferrer">
            <svg class="social-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
            Facebook
          </a>
          <a href="<?php echo esc_url( rbc68_mod( 'rbc68_instagram', 'https://instagram.com/rbc68/' ) ); ?>" class="social-link" target="_blank" rel="noopener noreferrer">
            <svg class="social-icon" viewBox="0 0 24 24" width="24" height="24"><defs><linearGradient id="instagramGradient" x1="0%" y1="0%" x2="100%" y2="100%">
<stop offset="0%" style="stop-color:#F09438;stop-opacity:1" />
<stop offset="25%" style="stop-color:#E65C81;stop-opacity:1" />
<stop offset="50%" style="stop-color:#C42799;stop-opacity:1" />
<stop offset="75%" style="stop-color:#8E44AD;stop-opacity:1" />
<stop offset="100%" style="stop-color:#4B79A1;stop-opacity:1" />
</linearGradient></defs><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.85s-.011 3.584-.069 4.85c-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07s-3.584-.012-4.85-.07c-3.252-.148-4.771-1.691-4.919-4.919-.058-1.265-.069-1.645-.069-4.85s.011-3.584.069-4.85c.149-3.225 1.664-4.771 4.919-4.919C8.416 2.175 8.796 2.163 12 2.163m0-2.163C8.74 0 8.333.011 7.053.069 2.695.287.287 2.695.069 7.053.011 8.333 0 8.74 0 12s.011 3.667.069 4.947c.228 4.358 2.636 7.88 6.983 8.108 1.234.058 1.624.069 4.947.069s3.713-.011 4.947-.069c4.347-.228 7.87-.636 8.108-6.983.058-1.28.069-1.687.069-4.947s-.011-3.667-.069-4.947C21.713 2.695 18.287.287 13.947.069 12.667.011 12.26 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.88 1.44 1.44 0 000-2.88z" fill="url(#instagramGradient)"/></svg>
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
              <option value="inscription">Inscription au club</option>
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

  <!-- Footer -->
</main>
<?php get_footer(); ?>
