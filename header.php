<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?> id="top">
<?php wp_body_open(); ?>

<header class="nb-nav" role="banner">
  <div class="nb-nav-inner container">

    <!-- Logo / Nom de l’enseigne -->
    <a class="nb-brand" href="<?php echo esc_url(home_url('/')); ?>#top">
      Maison <span class="nb-grad">Luma</span>
    </a>

    <!-- Bouton burger (mobile) -->
    <button class="nb-nav-toggle" aria-expanded="false" aria-controls="primary-menu" aria-label="Ouvrir le menu">
      <span></span><span></span><span></span>
    </button>

    <!-- Navigation principale -->
    <nav class="nb-menu-wrap" aria-label="Navigation principale">
      <?php
      wp_nav_menu([
        'theme_location' => 'primary',
        'menu_id'        => 'primary-menu',
        'container'      => false,
        'menu_class'     => 'nb-menu',
        'fallback_cb'    => function() {
          // Menu par défaut si aucun menu n’est créé dans l’admin
          echo '<ul id="primary-menu" class="nb-menu">
                  <li><a href="#carte">Menu</a></li>
                  <li><a href="#galerie">Galerie</a></li>
                  <li><a href="#avis">Avis</a></li>
                  <li><a href="#resa">Réserver</a></li>
                  <li><a href="#contact">Contact</a></li>
                </ul>';
        },
      ]);
      ?>
    </nav>
  </div>
</header>

<main class="site-main">
