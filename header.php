<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php wp_head(); ?>

  <!-- Critical CSS inlined for above-the-fold content -->
  <style>
    /* Critical CSS for hero section and navigation */
    .nb-nav { position: fixed; top: 0; left: 0; right: 0; background: rgba(11, 18, 32, 0.95); backdrop-filter: blur(10px); z-index: 1000; padding: 1rem 0; }
    .nb-nav-inner { display: flex; align-items: center; justify-content: space-between; max-width: 1200px; margin: 0 auto; padding: 0 1rem; }
    .nb-brand { font-size: 1.5rem; font-weight: bold; color: #F7F8FA; text-decoration: none; }
    .nb-grad { background: linear-gradient(135deg, #F59E0B, #EF4444); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
    .nb-nav-toggle { display: none; background: none; border: none; cursor: pointer; padding: 0.5rem; }
    .nb-nav-toggle span { display: block; width: 25px; height: 3px; background: #F7F8FA; margin: 5px 0; transition: 0.3s; }
    .nb-menu { display: flex; list-style: none; margin: 0; padding: 0; gap: 2rem; }
    .nb-menu li a { color: #F7F8FA; text-decoration: none; padding: 0.5rem 0; transition: color 0.3s; }
    .nb-menu li a:hover { color: #F59E0B; }

    /* Hero section critical styles */
    .nb-hero { min-height: 100vh; display: flex; align-items: center; background: linear-gradient(135deg, rgba(11, 18, 32, 0.8), rgba(233, 78, 27, 0.1)), url('<?php echo esc_url(get_option("nbcore_hero_bg_url")); ?>') center/cover no-repeat; }
    .nb-hero-content { text-align: center; color: #F7F8FA; max-width: 800px; margin: 0 auto; padding: 2rem; }
    .nb-hero h1 { font-size: clamp(2.5rem, 5vw, 4rem); margin-bottom: 1rem; }
    .nb-hero p { font-size: 1.25rem; margin-bottom: 2rem; opacity: 0.9; }
    .nb-btn { display: inline-block; background: #F59E0B; color: #0B1220; padding: 1rem 2rem; text-decoration: none; border-radius: 16px; font-weight: bold; transition: transform 0.3s; }
    .nb-btn:hover { transform: translateY(-2px); }

    @media (max-width: 768px) {
      .nb-menu { display: none; }
      .nb-nav-toggle { display: block; }
      .nb-nav-toggle[aria-expanded="true"] + .nb-menu-wrap .nb-menu { display: flex; flex-direction: column; position: absolute; top: 100%; left: 0; right: 0; background: rgba(11, 18, 32, 0.95); padding: 1rem; }
    }
  </style>
</head>

<body <?php body_class(); ?> id="top">
<?php wp_body_open(); ?>

<header class="nb-nav" role="banner">
  <div class="nb-nav-inner container">

    <!-- Logo / Nom de l’enseigne -->
    <a class="nb-brand" href="<?php echo esc_url(home_url('/')); ?>#top">
      <?php
      $logo_url = get_option('nbcore_logo_url');
      if ($logo_url) {
        echo '<img src="' . esc_url($logo_url) . '" alt="' . get_bloginfo('name') . '" class="nb-logo">';
      } else {
        echo 'Maison <span class="nb-grad">Luma</span>';
      }
      ?>
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
