<?php
/**
 * NB Landing — SEO (version SAFE)
 * Objectif : balises simples, zéro risque de casse, zéro lourdeur.
 */
if (!defined('ABSPATH')) exit;

/* ----------------------------------------------------------------
 * 1) Meta description (léger, contextuel, échappé)
 * ---------------------------------------------------------------- */
function nb_seo_meta_description_safe() {
  if (is_admin() || is_404()) return;

  $desc = '';

  if (is_front_page()) {
    $desc = 'Maison Luma, restaurant bistronomique à Auxerre : cuisine de saison, produits locaux, ambiance chaleureuse. Réservation en ligne.';
  } elseif (is_singular()) {
    // essaye l’excerpt sinon 20 mots du contenu
    $excerpt = get_the_excerpt();
    $desc = $excerpt ? $excerpt : wp_trim_words(wp_strip_all_tags(get_the_content(null, false, get_the_ID())), 20);
  } elseif (is_category() || is_tag() || is_tax()) {
    $term  = get_queried_object();
    $name  = $term && !is_wp_error($term) ? $term->name : '';
    $desc  = $name ? 'Découvrez nos contenus “' . $name . '”.' : get_bloginfo('description');
  } elseif (is_search()) {
    $q = get_search_query();
    $desc = $q ? 'Résultats de recherche pour “' . $q . '”.' : get_bloginfo('description');
  } else {
    $desc = get_bloginfo('description');
  }

  if ($desc) {
    echo '<meta name="description" content="' . esc_attr(wp_strip_all_tags($desc)) . '">' . "\n";
  }
}
add_action('wp_head', 'nb_seo_meta_description_safe', 1);

/* ----------------------------------------------------------------
 * 2) Canonical (simple, sans conflit)
 *    Si un plugin SEO est présent, il gérera déjà la balise.
 * ---------------------------------------------------------------- */
function nb_seo_canonical_safe() {
  if (is_admin() || is_404()) return;

  // Si un autre plugin sort déjà un canonical, on ne double pas.
  global $wp_filter;
  if (!empty($wp_filter['wp_head'])) {
    ob_start();
    do_action('wp_head');
    $head = ob_get_clean();
    if (strpos($head, 'rel="canonical"') !== false) return;
  }

  $url = '';
  if (function_exists('wp_get_canonical_url')) {
    $url = wp_get_canonical_url();
  }
  if (!$url) {
    if (is_front_page())        $url = home_url('/');
    elseif (is_singular())      $url = get_permalink();
    elseif (is_category() || is_tag() || is_tax()) $url = get_term_link(get_queried_object());
    elseif (is_author())        $url = get_author_posts_url(get_queried_object_id());
    elseif (is_search())        $url = get_search_link();
  }

  if ($url && !is_wp_error($url)) {
    echo '<link rel="canonical" href="' . esc_url($url) . '">' . "\n";
  }
}
add_action('wp_head', 'nb_seo_canonical_safe', 2);

/* ----------------------------------------------------------------
 * 3) Open Graph / Twitter (très léger, fallback propres)
 * ---------------------------------------------------------------- */
function nb_seo_open_graph_safe() {
  if (is_admin() || is_404()) return;

  $site_name   = get_bloginfo('name');
  $default_img = get_template_directory_uri() . '/assets/img/hero-resto1.jpg';
  $title       = wp_get_document_title();
  $desc        = ''; // même logique que meta description (évite la duplication de code)
  if (is_front_page()) {
    $desc = 'Cuisine de saison, produits locaux, ambiance chaleureuse. Réservation en ligne.';
  } elseif (is_singular()) {
    $excerpt = get_the_excerpt();
    $desc = $excerpt ? $excerpt : wp_trim_words(wp_strip_all_tags(get_the_content(null, false, get_the_ID())), 20);
  } else {
    $desc = get_bloginfo('description');
  }

  $img = (is_singular() && has_post_thumbnail()) ? get_the_post_thumbnail_url(get_the_ID(), 'large') : $default_img;
  $url = (is_singular() ? get_permalink() : home_url(add_query_arg(null, null)));

  echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
  echo '<meta property="og:description" content="' . esc_attr(wp_strip_all_tags($desc)) . '">' . "\n";
  echo '<meta property="og:image" content="' . esc_url($img) . '">' . "\n";
  echo '<meta property="og:url" content="' . esc_url($url) . '">' . "\n";
  echo '<meta property="og:type" content="' . (is_singular() ? 'article' : 'website') . '">' . "\n";
  echo '<meta property="og:site_name" content="' . esc_attr($site_name) . '">' . "\n";
  echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
}
add_action('wp_head', 'nb_seo_open_graph_safe', 3);

/* ----------------------------------------------------------------
 * 4) Structured Data (ultra sobre, pas de fausses notes)
 *    - Restaurant en page d’accueil (sans fake reviews/ratings)
 *    - Fil d’Ariane sur contenus simples
 * ---------------------------------------------------------------- */
function nb_seo_structured_data_safe() {
  if (is_admin() || is_404()) return;

  if (is_front_page()) {
    $data = [
      '@context' => 'https://schema.org',
      '@type'    => 'Restaurant',
      'name'     => 'Maison Luma',
      'url'      => home_url('/'),
      'description' => 'Cuisine de saison • Bistronomie locale',
      'image'    => get_template_directory_uri() . '/assets/img/hero-resto1.jpg',
      'address'  => [
        '@type'           => 'PostalAddress',
        'addressLocality' => 'Auxerre',
        'postalCode'      => '89000',
        'addressCountry'  => 'FR'
      ],
      'servesCuisine' => 'French',
      'priceRange'    => '€€',
      'acceptsReservations' => true
    ];
    echo '<script type="application/ld+json">' . wp_json_encode($data) . '</script>' . "\n";
  }

  if (is_singular()) {
    $crumb = [
      '@context' => 'https://schema.org',
      '@type'    => 'BreadcrumbList',
      'itemListElement' => [
        [
          '@type'    => 'ListItem',
          'position' => 1,
          'name'     => 'Accueil',
          'item'     => home_url('/')
        ],
        [
          '@type'    => 'ListItem',
          'position' => 2,
          'name'     => get_the_title(),
          'item'     => get_permalink()
        ],
      ]
    ];
    echo '<script type="application/ld+json">' . wp_json_encode($crumb) . '</script>' . "\n";
  }
}
add_action('wp_head', 'nb_seo_structured_data_safe', 4);

/* ----------------------------------------------------------------
 * 5) Sitemap : on NE FAIT RIEN
 *    WP ≥ 5.5 expose déjà /wp-sitemap.xml
 *    (Évite de fabriquer un sitemap custom qui peut entrer en conflit)
 * ---------------------------------------------------------------- */

/* ----------------------------------------------------------------
 * 6) robots.txt : on peut simplement ajouter l’URL du sitemap natif
 * ---------------------------------------------------------------- */
function nb_seo_robots_txt_safe($output) {
  $output .= "\n# Sitemap (natif WP)\n";
  $output .= 'Sitemap: ' . home_url('/wp-sitemap.xml') . "\n";
  return $output;
}
add_filter('robots_txt', 'nb_seo_robots_txt_safe', 20, 1);

/* ----------------------------------------------------------------
 * 7) Google Analytics : mieux via un plugin (ex. Site Kit)
 *    -> On ne sort rien ici pour éviter double tracking / CSP
 * ---------------------------------------------------------------- */
