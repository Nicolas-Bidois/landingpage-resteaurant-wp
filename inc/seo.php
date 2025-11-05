<?php
/**
 * SEO optimizations for NB Landing theme
 */

// Dynamic meta descriptions
function nb_add_meta_description() {
    $description = '';

    if ( is_front_page() ) {
        $description = 'Découvrez Maison Luma, restaurant bistronomique à Auxerre. Cuisine de saison, produits frais locaux, ambiance chaleureuse. Réservation en ligne.';
    } elseif ( is_singular() ) {
        $description = get_the_excerpt() ?: wp_trim_words( get_the_content(), 20 );
    } elseif ( is_category() || is_tag() ) {
        $description = get_the_archive_description() ?: 'Découvrez nos plats ' . single_term_title( '', false ) . ' au restaurant Maison Luma.';
    } elseif ( is_search() ) {
        $description = 'Résultats de recherche pour "' . get_search_query() . '" sur le site de Maison Luma.';
    }

    if ( $description ) {
        echo '<meta name="description" content="' . esc_attr( wp_strip_all_tags( $description ) ) . '">' . "\n";
    }
}
add_action( 'wp_head', 'nb_add_meta_description', 1 );

// Add canonical tags
function nb_add_canonical_tags() {
    $canonical_url = '';

    if ( is_front_page() ) {
        $canonical_url = home_url( '/' );
    } elseif ( is_singular() ) {
        $canonical_url = get_permalink();
    } elseif ( is_category() || is_tag() || is_tax() ) {
        $canonical_url = get_term_link( get_queried_object() );
    } elseif ( is_author() ) {
        $canonical_url = get_author_posts_url( get_queried_object_id() );
    } elseif ( is_search() ) {
        $canonical_url = get_search_link();
    }

    if ( $canonical_url && ! is_wp_error( $canonical_url ) ) {
        echo '<link rel="canonical" href="' . esc_url( $canonical_url ) . '">' . "\n";
    }
}
add_action( 'wp_head', 'nb_add_canonical_tags', 2 );

// Open Graph tags for social sharing
function nb_add_open_graph_tags() {
    if ( is_front_page() ) {
        $title = 'Maison Luma - Bistronomie Locale à Auxerre';
        $description = 'Cuisine de saison, produits frais locaux, ambiance chaleureuse. Réservation en ligne.';
        $image = get_template_directory_uri() . '/assets/img/hero-resto1.jpg';
        $url = home_url('/');
    } elseif ( is_singular() ) {
        $title = get_the_title() . ' - Maison Luma';
        $description = get_the_excerpt() ?: wp_trim_words( get_the_content(), 20 );
        $image = get_the_post_thumbnail_url( get_the_ID(), 'large' ) ?: get_template_directory_uri() . '/assets/img/hero-resto1.jpg';
        $url = get_permalink();
    } else {
        $title = wp_get_document_title();
        $description = get_bloginfo( 'description' );
        $image = get_template_directory_uri() . '/assets/img/hero-resto1.jpg';
        $url = home_url( add_query_arg( NULL, NULL ) );
    }

    echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
    echo '<meta property="og:description" content="' . esc_attr( wp_strip_all_tags( $description ) ) . '">' . "\n";
    echo '<meta property="og:image" content="' . esc_url( $image ) . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
    echo '<meta property="og:type" content="' . ( is_front_page() ? 'website' : 'article' ) . '">' . "\n";
    echo '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '">' . "\n";
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
}
add_action( 'wp_head', 'nb_add_open_graph_tags', 3 );

// Enhanced structured data for restaurant with Schema.org (with caching)
function nb_add_structured_data() {
    if ( is_front_page() ) {
        // Try to get cached structured data
        $structured_data = get_transient( 'nb_structured_data_restaurant' );

        if ( false === $structured_data ) {
            $structured_data = array(
                '@context' => 'https://schema.org',
                '@type' => 'Restaurant',
                'name' => 'Maison Luma',
                'description' => 'Cuisine de saison • Bistronomie locale',
                'url' => home_url('/'),
                'logo' => get_option('nbcore_logo_url') ?: get_template_directory_uri() . '/assets/img/logo.png',
                'image' => get_option('nbcore_hero_bg_url') ?: get_template_directory_uri() . '/assets/img/hero-bg.jpg',
                'address' => array(
                    '@type' => 'PostalAddress',
                    'streetAddress' => '12 rue des Gourmets',
                    'addressLocality' => 'Auxerre',
                    'postalCode' => '89000',
                    'addressCountry' => 'FR'
                ),
                'telephone' => '+33386000000',
                'email' => 'contact@maisonluma.fr',
                'servesCuisine' => 'French',
                'priceRange' => '€€',
                'openingHours' => array(
                    'Mo-Fr 12:00-14:00',
                    'Mo-Fr 19:00-22:00',
                    'Sa 12:00-14:00',
                    'Sa 19:00-22:00',
                    'Su 12:00-14:00'
                ),
                'aggregateRating' => array(
                    '@type' => 'AggregateRating',
                    'ratingValue' => '4.8',
                    'reviewCount' => '1245',
                    'bestRating' => '5',
                    'worstRating' => '1'
                ),
                'review' => array(
                    array(
                        '@type' => 'Review',
                        'author' => array('@type' => 'Person', 'name' => 'Client satisfait'),
                        'reviewRating' => array('@type' => 'Rating', 'ratingValue' => '5'),
                        'reviewBody' => 'Expérience exceptionnelle, cuisine raffinée.'
                    )
                ),
                'hasMenu' => array(
                    '@type' => 'Menu',
                    'name' => 'Carte Maison Luma',
                    'url' => home_url('/#carte')
                ),
                'acceptsReservations' => true,
                'paymentAccepted' => 'Cash, Credit Card',
                'currenciesAccepted' => 'EUR'
            );

            // Cache for 24 hours
            set_transient( 'nb_structured_data_restaurant', $structured_data, DAY_IN_SECONDS );
        }

        echo '<script type="application/ld+json">' . wp_json_encode( $structured_data ) . '</script>' . "\n";
    }

    // Add breadcrumb structured data
    if ( ! is_front_page() && is_singular() ) {
        $breadcrumb_data = array(
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => array(
                array(
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => 'Accueil',
                    'item' => home_url('/')
                ),
                array(
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => get_the_title(),
                    'item' => get_permalink()
                )
            )
        );
        echo '<script type="application/ld+json">' . wp_json_encode( $breadcrumb_data ) . '</script>' . "\n";
    }
}
add_action( 'wp_head', 'nb_add_structured_data', 4 );

// Generate enhanced XML sitemap with images
function nb_generate_sitemap() {
    if ( isset( $_GET['sitemap'] ) && $_GET['sitemap'] === 'xml' ) {
        header( 'Content-Type: application/xml; charset=utf-8' );

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";

        // Homepage
        echo "\t<url>\n";
        echo "\t\t<loc>" . esc_url( home_url( '/' ) ) . "</loc>\n";
        echo "\t\t<lastmod>" . date( 'Y-m-d' ) . "</lastmod>\n";
        echo "\t\t<changefreq>weekly</changefreq>\n";
        echo "\t\t<priority>1.0</priority>\n";
        
        // Add hero image
        $hero_image = get_option('nbcore_hero_bg_url') ?: get_template_directory_uri() . '/assets/img/hero-resto1.jpg';
        echo "\t\t<image:image>\n";
        echo "\t\t\t<image:loc>" . esc_url( $hero_image ) . "</image:loc>\n";
        echo "\t\t\t<image:title>Maison Luma - Restaurant</image:title>\n";
        echo "\t\t</image:image>\n";
        echo "\t</url>\n";

        // Pages
        $pages = get_pages();
        foreach ( $pages as $page ) {
            echo "\t<url>\n";
            echo "\t\t<loc>" . esc_url( get_permalink( $page->ID ) ) . "</loc>\n";
            echo "\t\t<lastmod>" . date( 'Y-m-d', strtotime( $page->post_modified ) ) . "</lastmod>\n";
            echo "\t\t<changefreq>monthly</changefreq>\n";
            echo "\t\t<priority>0.8</priority>\n";
            
            // Add featured image if exists
            if ( has_post_thumbnail( $page->ID ) ) {
                $image_url = get_the_post_thumbnail_url( $page->ID, 'large' );
                echo "\t\t<image:image>\n";
                echo "\t\t\t<image:loc>" . esc_url( $image_url ) . "</image:loc>\n";
                echo "\t\t\t<image:title>" . esc_html( get_the_title( $page->ID ) ) . "</image:title>\n";
                echo "\t\t</image:image>\n";
            }
            echo "\t</url>\n";
        }

        // Posts
        $posts = get_posts( array( 'numberposts' => -1, 'post_status' => 'publish' ) );
        foreach ( $posts as $post ) {
            echo "\t<url>\n";
            echo "\t\t<loc>" . esc_url( get_permalink( $post->ID ) ) . "</loc>\n";
            echo "\t\t<lastmod>" . date( 'Y-m-d', strtotime( $post->post_modified ) ) . "</lastmod>\n";
            echo "\t\t<changefreq>weekly</changefreq>\n";
            echo "\t\t<priority>0.6</priority>\n";
            
            // Add featured image if exists
            if ( has_post_thumbnail( $post->ID ) ) {
                $image_url = get_the_post_thumbnail_url( $post->ID, 'large' );
                echo "\t\t<image:image>\n";
                echo "\t\t\t<image:loc>" . esc_url( $image_url ) . "</image:loc>\n";
                echo "\t\t\t<image:title>" . esc_html( get_the_title( $post->ID ) ) . "</image:title>\n";
                echo "\t\t</image:image>\n";
            }
            echo "\t</url>\n";
        }

        // Custom post types (plats)
        $plats = get_posts( array( 'post_type' => 'plat', 'numberposts' => -1, 'post_status' => 'publish' ) );
        foreach ( $plats as $plat ) {
            echo "\t<url>\n";
            echo "\t\t<loc>" . esc_url( get_permalink( $plat->ID ) ) . "</loc>\n";
            echo "\t\t<lastmod>" . date( 'Y-m-d', strtotime( $plat->post_modified ) ) . "</lastmod>\n";
            echo "\t\t<changefreq>weekly</changefreq>\n";
            echo "\t\t<priority>0.7</priority>\n";
            
            // Add featured image if exists
            if ( has_post_thumbnail( $plat->ID ) ) {
                $image_url = get_the_post_thumbnail_url( $plat->ID, 'large' );
                echo "\t\t<image:image>\n";
                echo "\t\t\t<image:loc>" . esc_url( $image_url ) . "</image:loc>\n";
                echo "\t\t\t<image:title>" . esc_html( get_the_title( $plat->ID ) ) . "</image:title>\n";
                echo "\t\t</image:image>\n";
            }
            echo "\t</url>\n";
        }

        echo '</urlset>' . "\n";
        exit;
    }
}
add_action( 'init', 'nb_generate_sitemap' );

// Add robots.txt enhancement
function nb_enhance_robots_txt( $output ) {
    $output .= "\n\n# Sitemap\n";
    $output .= "Sitemap: " . home_url( '/?sitemap=xml' ) . "\n";
    return $output;
}
add_filter( 'robots_txt', 'nb_enhance_robots_txt' );

// Google Analytics integration (to be activated when site goes live)
function nb_add_google_analytics() {
    $ga_id = get_option( 'nbcore_google_analytics_id', '' );
    
    if ( ! empty( $ga_id ) && ! is_admin() ) {
        ?>
        <!-- Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr( $ga_id ); ?>"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '<?php echo esc_js( $ga_id ); ?>', {
                'anonymize_ip': true,
                'cookie_flags': 'SameSite=None;Secure'
            });
        </script>
        <?php
    }
}
add_action( 'wp_head', 'nb_add_google_analytics', 10 );
