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

---

*Thème développé avec ❤️ pour une expérience web durable et performante.*
