<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
</main>
<footer class="site-footer">
  <div class="container footer-inner">
    <div class="footer-content">
      <div class="footer-brand">
        <a href="<?php echo esc_url(home_url('/')); ?>#top">
          Maison <span class="nb-grad">Luma</span>
        </a>
        <p>Cuisine de saison • Auxerre</p>
      </div>
      <div class="footer-section">
        <h4>Navigation</h4>
        <ul>
          <li><a href="#carte">Menu</a></li>
          <li><a href="#galerie">Galerie</a></li>
          <li><a href="#avis">Avis</a></li>
          <li><a href="#resa">Réserver</a></li>
        </ul>
      </div>
      <div class="footer-section">
        <h4>Contact</h4>
        <ul>
          <li><a href="tel:+33386000000">📞 03 86 00 00 00</a></li>
          <li><a href="mailto:contact@maisonluma.fr">✉️ Contact</a></li>
          <li><a href="https://www.instagram.com/maisonluma" target="_blank">📷 Instagram</a></li>
          <li><a href="https://www.facebook.com/maisonluma" target="_blank">📘 Facebook</a></li>
        </ul>
      </div>
      <div class="footer-section">
        <h4>Horaires</h4>
        <ul>
          <li>Lun–Jeu : 12h–14h / 19h–22h</li>
          <li>Ven–Sam : 12h–14h / 19h–23h</li>
          <li>Dim : Fermé</li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <p>© 2025 <?php bloginfo('name'); ?> — Tous droits réservés.</p>
    </div>
  </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
