# 🍽️ Restaurant Landing Page — Thème WordPress sur mesure

Projet réalisé pour créer une landing page optimisée pour un restaurant, avec un thème WordPress personnalisé respectant les principes du Green Code et offrant une expérience utilisateur fluide.

## 🧩 Scénario du projet

L'objectif était de développer un site WordPress performant et éco-conçu pour présenter un restaurant :

- **Design fidèle** aux maquettes fournies (versions desktop et mobile)
- **Performance optimisée** avec des images compressées et un JavaScript modulaire
- **Administration simple** via le back-office WordPress natif

Le restaurant peut ainsi :
- Présenter son menu, ses avis clients et sa galerie photos
- Permettre aux visiteurs de réserver via des formulaires intégrés
- Gérer facilement les contenus et menus depuis WordPress

## ⚙️ Stack technique

- **WordPress** (thème développé from scratch)
- **PHP**, **HTML5**, **CSS3**, **JavaScript** (ES6)
- **Plugin personnalisé** nb-core pour les fonctionnalités avancées (CPT, shortcodes, etc.)
- **Contact Form 7** pour les formulaires de réservation
- **Git / GitHub** pour la gestion de version
- **Figma** pour la conception visuelle

## 🚀 Fonctionnalités principales

- **FAQ accordéon** avec fermeture automatique des autres sections
- **Navigation responsive** avec menu burger mobile
- **Slider de témoignages** avec navigation par points et flèches
- **Galerie photos dynamique** avec filtres et modales
- **Formulaires de réservation** intégrés avec validation
- **Animations fluides** pour les interactions utilisateur
- **Optimisation Green Code** : images compressées, lazy loading, JavaScript modulaire
- **Design responsive** complet (desktop, tablette, mobile)

## 🧠 Ce que j'ai appris

- Création d'un thème WordPress modulaire et maintenable
- Utilisation de `functions.php` pour enregistrer scripts, menus et hooks
- Gestion d'interactions JavaScript natives (accordéon, sliders, modales)
- Intégration de plugins personnalisés pour étendre les fonctionnalités
- Application de bonnes pratiques d'éco-conception web
- Optimisation des performances JavaScript par séparation en modules

## 📸 Aperçus

### Version desktop
![Restaurant Landing Page Desktop](assets/img/Screenshot-langingpage-nb.png)

### Version mobile
*Capture réalisée en navigation privée sur Mozila DevTools *

## 📦 Installation locale (pour test)

1. **Cloner ce dépôt**
   ```bash
   git clone https://github.com/Nicolas-Bidois/landingpage-resteaurant-wp.git
   ```

2. **Installer une instance WordPress locale** (Local by Flywheel, Laragon, XAMPP, etc.)

3. **Copier le dossier du thème** dans `wp-content/themes/nb-landing/`

4. **Activer le thème** nb-landing depuis l'administration WordPress

5. **Installer les plugins nécessaires** :
   - nb-core (plugin personnalisé fourni)
   - Contact Form 7

## 🌿 Principes Green Code appliqués

- **Compression systématique** des images (.jpg / .webp)
- **Lazy loading** sur les galeries et images
- **Scripts JavaScript modulaires** chargés conditionnellement pour réduire la taille du bundle initial
- **Requêtes optimisées** pour minimiser l'impact environnemental
- **Animations CSS** natives plutôt que JavaScript pour de meilleures performances

## 🔌 Hooks disponibles

Le thème nb-landing offre plusieurs hooks personnalisés pour étendre ses fonctionnalités.

### Filtres (Filters)

#### `nb_generate_custom_css`
Permet de modifier ou d'étendre les variables CSS personnalisées générées depuis les options du plugin nb-core.

**Utilisation :**
```php
add_filter( 'nb_generate_custom_css', function( $css ) {
    $css .= "
    .custom-element {
        background: var(--nb-primary);
    }
    ";
    return $css;
});
```

#### `nb_add_lazy_loading`
Appliqué au contenu des posts et aux images mises en avant pour ajouter le lazy loading et le support WebP.

**Utilisation :**
```php
add_filter( 'the_content', 'nb_add_lazy_loading' );
add_filter( 'post_thumbnail_html', 'nb_add_lazy_loading' );
```

#### `nb_remove_query_strings`
Supprime les chaînes de requête des ressources statiques pour améliorer la mise en cache.

**Utilisation :**
```php
add_filter( 'script_loader_src', 'nb_remove_query_strings', 15, 1 );
add_filter( 'style_loader_src', 'nb_remove_query_strings', 15, 1 );
```

#### `nb_sanitize_form_input`
Fonction de sanitization pour les entrées de formulaire.

**Utilisation :**
```php
$clean_input = nb_sanitize_form_input( $_POST['user_input'] );
```

#### `nb_custom_excerpt_more`
Personnalise le texte "Lire la suite" avec des attributs d'accessibilité.

**Utilisation :**
```php
add_filter( 'excerpt_more', 'nb_custom_excerpt_more' );
```

### Actions (Actions)

#### `nb_add_structured_data`
Ajoute les données structurées Schema.org pour le restaurant et les breadcrumbs.

**Hook :** `wp_head` (priorité 4)

**Utilisation :**
```php
// Les données sont mises en cache pendant 24h
// Pour forcer la régénération :
delete_transient( 'nb_structured_data_restaurant' );
```

#### `nb_add_meta_description`
Génère automatiquement les meta descriptions pour toutes les pages.

**Hook :** `wp_head` (priorité 1)

#### `nb_add_canonical_tags`
Ajoute automatiquement les balises canonical pour éviter le contenu dupliqué.

**Hook :** `wp_head` (priorité 2)

#### `nb_add_open_graph_tags`
Génère les balises Open Graph pour le partage sur les réseaux sociaux.

**Hook :** `wp_head` (priorité 3)

#### `nb_add_google_analytics`
Intègre Google Analytics si l'ID est configuré dans les options nb-core.

**Hook :** `wp_head` (priorité 10)

**Configuration :**
```php
// Dans l'admin WordPress : Réglages > NB Core
// Ou par code :
update_option( 'nbcore_google_analytics_id', 'G-XXXXXXXXXX' );
```

#### `nb_add_security_headers`
Ajoute les en-têtes de sécurité HTTP.

**Hook :** `send_headers`

**En-têtes ajoutés :**
- `X-Content-Type-Options: nosniff`
- `X-Frame-Options: SAMEORIGIN`
- `X-XSS-Protection: 1; mode=block`
- `Referrer-Policy: strict-origin-when-cross-origin`

#### `nb_add_skip_links`
Ajoute les liens de navigation rapide pour l'accessibilité.

**Hook :** `wp_body_open`

#### `nb_add_preload_headers`
Ajoute les en-têtes de préchargement pour les ressources critiques.

**Hook :** `send_headers`

### AJAX Endpoints

#### `wp_ajax_load_menu` / `wp_ajax_nopriv_load_menu`
Charge dynamiquement les éléments du menu par catégorie.

**Utilisation JavaScript :**
```javascript
fetch(nb_ajax.ajax_url, {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({
        action: 'load_menu',
        nonce: nb_ajax.nonce,
        category: 'plats'
    })
})
.then(response => response.json())
.then(data => console.log(data));
```

### Hooks WordPress natifs utilisés

Le thème utilise également ces hooks WordPress standards :

- `after_switch_theme` - Nettoyage lors de l'activation
- `switch_theme` - Nettoyage lors de la désactivation
- `wp_enqueue_scripts` - Chargement des scripts et styles
- `authenticate` - Limitation des tentatives de connexion
- `wp_nav_menu_args` - Ajout d'attributs ARIA à la navigation
- `wp_get_attachment_image_attributes` - Vérification des attributs alt
- `robots_txt` - Amélioration du fichier robots.txt

## 🏗️ Architecture technique

### Structure modulaire

```
nb-landing/
├── inc/
│   ├── setup.php          # Configuration du thème
│   ├── performance.php    # Optimisations de performance
│   ├── seo.php           # Optimisations SEO
│   ├── security.php      # Sécurité et sanitization
│   ├── accessibility.php # Accessibilité WCAG
│   └── patterns.php      # Block patterns WordPress
├── assets/
│   ├── css/
│   │   └── main.css      # Styles principaux
│   └── js/
│       ├── main.js       # Script principal
│       ├── menu.js       # Gestion du menu
│       ├── navigation.js # Navigation mobile
│       ├── testimonials.js # Slider témoignages
│       └── utils.js      # Utilitaires
└── parts/                # Template parts réutilisables
```

### Optimisations de performance

- **Lazy loading** automatique des images
- **Support WebP** avec fallback
- **Minification** détectée automatiquement (production vs développement)
- **Cache busting** avec versioning basé sur filemtime
- **Preload headers** pour les ressources critiques
- **Transient caching** pour les données structurées (24h)
- **Suppression des query strings** des ressources statiques

### SEO avancé

- **Sitemap XML** dynamique avec images : `/?sitemap=xml`
- **Balises canonical** automatiques
- **Meta descriptions** dynamiques par type de page
- **Open Graph** complet pour les réseaux sociaux
- **Données structurées** Schema.org (Restaurant, Breadcrumb)
- **Google Analytics** avec anonymisation IP et cookies sécurisés

### Sécurité

- **Headers HTTP** de sécurité
- **Sanitization** systématique des entrées
- **Nonces AJAX** pour les requêtes
- **Limitation** des tentatives de connexion (5 max, lockout 15min)
- **Désactivation** de l'édition de fichiers dans l'admin
- **Désactivation** de XML-RPC

### Accessibilité WCAG 2.1

- **Skip links** pour la navigation au clavier
- **Attributs ARIA** sur tous les éléments interactifs
- **Focus styles** visibles et cohérents
- **Support** du mode contraste élevé
- **Support** du mode mouvement réduit
- **Alt text** automatique sur les images
- **Hiérarchie** des titres respectée

## 🧪 Tests et validation

### Checklist de test

#### Performance
- [ ] Temps de chargement < 3s (PageSpeed Insights)
- [ ] Score Performance > 90 (Lighthouse)
- [ ] Images en lazy loading
- [ ] WebP détecté et utilisé
- [ ] CSS/JS minifiés en production

#### SEO
- [ ] Sitemap accessible : `/?sitemap=xml`
- [ ] Meta descriptions présentes sur toutes les pages
- [ ] Balises canonical correctes
- [ ] Open Graph tags présents
- [ ] Données structurées valides (Google Rich Results Test)
- [ ] robots.txt contient le sitemap

#### Accessibilité
- [ ] Navigation au clavier fonctionnelle (Tab, Enter, Esc)
- [ ] Skip links visibles au focus
- [ ] Contraste des couleurs suffisant (WCAG AA)
- [ ] Attributs ARIA présents
- [ ] Alt text sur toutes les images
- [ ] Score Accessibilité > 90 (Lighthouse)

#### Sécurité
- [ ] Headers de sécurité présents (SecurityHeaders.com)
- [ ] Formulaires protégés par nonce
- [ ] Limitation des tentatives de connexion active
- [ ] Pas de failles XSS/CSRF

#### Fonctionnalités
- [ ] Menu burger mobile fonctionnel
- [ ] Accordéon FAQ avec fermeture automatique
- [ ] Slider témoignages avec navigation
- [ ] Galerie photos avec filtres et modales
- [ ] Formulaires de réservation opérationnels
- [ ] Chargement AJAX du menu par catégorie

### Commandes de test

```bash
# Vérifier la structure des fichiers
ls -la inc/

# Tester le sitemap
curl http://localhost/?sitemap=xml

# Vérifier les permissions
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;

# Analyser les performances
npm run build  # Si vous avez un build process
```

### Outils recommandés

- **PageSpeed Insights** : https://pagespeed.web.dev/
- **Lighthouse** : Intégré dans Chrome DevTools
- **WAVE** : Extension pour tester l'accessibilité
- **Google Rich Results Test** : https://search.google.com/test/rich-results
- **SecurityHeaders.com** : https://securityheaders.com/

---

*Thème développé avec ❤️ pour une expérience web durable et performante.*
