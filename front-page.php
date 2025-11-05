<?php
if ( ! defined('ABSPATH') ) { exit; }
get_header();
?>

<section id="top" class="hero hero--resto">
  <div class="container hero__inner">
    <div class="hero__copy">
      <span class="pill">Cuisine de saison • Auxerre</span>
      <h1>Le <span class="grad">Bourgogne</span> — bistronomie locale</h1>
      <p class="lead">Des produits frais, une carte courte qui change chaque semaine, une ambiance chaleureuse.</p>
      <div class="cta">
        <a href="#resa" class="btn btn--animated">Réserver une table</a>
        <a href="#carte" class="btn btn--ghost btn--animated">Voir la carte</a>
      </div>
      <ul class="badges">
        <li>⭐ 4,8/5 (1 245 avis)</li>
        <li>🥗 Végétarien friendly</li>
        <li>🍷 Sélection de vins</li>
      </ul>
    </div>
  </div>
  <div class="hero__bg">
    <picture>
      <source srcset="<?php echo get_template_directory_uri(); ?>/assets/img/hero-resto@x.webp" type="image/webp" />
      <img src="<?php echo get_template_directory_uri(); ?>/assets/img/hero-resto1.jpg" alt="Salle du restaurant" loading="eager" />
    </picture>
    <div class="hero__overlay"></div>
  </div>
  <div class="scroll-indicator">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M7 13L12 18L17 13M7 6L12 11L17 6" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
  </div>
</section>

<section id="carte" class="section-carte">
  <div class="container">
    <h2 class="section-title">La carte</h2>

    <!-- Onglets catégories (remplis par JS) -->
    <div class="nb-tabs" id="nb-tabs">
      <!-- Ex de fallback si JS off -->
      <button class="nb-tab is-active" data-cat="all">Tout</button>
    </div>

    <!-- Grille dynamique -->
    <div class="nb-carte-grid">
      <!-- Le JS injecte ici les cartes .nb-card rendues depuis le CPT -->
    </div>

    <!-- CTA unique -->
    <div class="nb-cta-center" id="nb-cta">
      <a href="#resa" class="nb-btn">Réserver une table</a>
    </div>

  </div>
</section>


<?php echo nb_output_galerie(['limit' => 6, 'columns' => 3]); ?>

<section id="avis" class="testimonials container">
  <h2>Avis clients</h2>
  <div class="testimonials-slider">
    <div class="quotes">
      <blockquote class="testimonial active">
        <div class="testimonial-content">
          <p>« Cuisine délicieuse et service impeccable. »</p>
          <cite>— Julie</cite>
          <div class="stars">★★★★★</div>
        </div>
        <div class="testimonial-avatar">
          <img src="<?php echo get_template_directory_uri(); ?>/assets/img/avatar1.jpg" alt="Julie" loading="lazy">
        </div>
      </blockquote>
      <blockquote class="testimonial">
        <div class="testimonial-content">
          <p>« Carte courte, produits frais, très belle sélection de vins. »</p>
          <cite>— Marc</cite>
          <div class="stars">★★★★★</div>
        </div>
        <div class="testimonial-avatar">
          <img src="<?php echo get_template_directory_uri(); ?>/assets/img/avatar2.jpg" alt="Marc" loading="lazy">
        </div>
      </blockquote>
      <blockquote class="testimonial">
        <div class="testimonial-content">
          <p>« Ambiance chaleureuse, on reviendra ! »</p>
          <cite>— Sarah</cite>
          <div class="stars">★★★★★</div>
        </div>
        <div class="testimonial-avatar">
          <img src="<?php echo get_template_directory_uri(); ?>/assets/img/avatar3.jpg" alt="Sarah" loading="lazy">
        </div>
      </blockquote>
    </div>
    <div class="testimonial-nav">
      <button class="testimonial-prev" aria-label="Témoignage précédent">&larr;</button>
      <div class="testimonial-dots">
        <span class="dot active" data-slide="0"></span>
        <span class="dot" data-slide="1"></span>
        <span class="dot" data-slide="2"></span>
      </div>
      <button class="testimonial-next" aria-label="Témoignage suivant">&rarr;</button>
    </div>
  </div>
</section>

<section id="resa" class="nb-resa">
  <h2>Réserver une table</h2>
  <p>Choisissez votre mode de réservation :</p>

  <div class="nb-resa-cta">
    <a class="btn btn-primary" href="tel:+33386000000">📞 Réserver par téléphone</a>
    <a class="btn btn-ghost" target="_blank" href="https://www.google.com/maps/place/Maison+Luma,+12+rue+des+Gourmets,+89000+Auxerre">📍 Itinéraire</a>
    <a class="btn btn-accent" href="#formulaire-resa">🗓️ Réserver en ligne</a>
  </div>

  <div id="formulaire-resa">
    <?php echo do_shortcode('[contact-form-7 id="335400b" title="Formulaire Contact"]'); ?>
  </div>

</section>

<section id="contact" class="infos container">
  <div class="infos__grid">
    <div>
      <h3>Horaires</h3>
      <ul class="hours">
        <li>Lun–Jeu : 12h–14h / 19h–22h</li>
        <li>Ven–Sam : 12h–14h / 19h–23h</li>
        <li>Dim : Fermé</li>
      </ul>
      <h3>Adresse</h3>
      <p>12 rue des Gourmets, 89000 Auxerre</p>
      <p>📞 03 86 00 00 00</p>
    </div>
    <div class="map">
      <iframe src="https://www.google.com/maps?q=Auxerre&output=embed" loading="lazy"></iframe>
    </div>
  </div>
</section>

<?php get_footer(); ?>
