# NanoBoy — Conventions de code

> Règles de nommage, style et sécurité applicables à **tout** le code du thème.
> **Dernière révision :** 2026-06-08

---

## 1. Nomenclature (homogène à 100 %)

| Élément | Convention | Exemple |
|---|---|---|
| Fonctions PHP | `nanoboy_` + snake_case | `nanoboy_thumbnail()` |
| Constantes PHP | `NANOBOY_` + UPPER_SNAKE | `NANOBOY_VERSION`, `NANOBOY_DIR` |
| Hooks/handles | préfixe `nanoboy-` | `wp_enqueue_style( 'nanoboy-app', … )` |
| Text domain | `nanoboy` | `__( 'Read more', 'nanoboy' )` |
| Fichiers | kebab-case | `card-article.php`, `template-helpers.php` |
| Classes CSS | **BEM** | `.card`, `.card__title`, `.card--featured` |
| Tokens CSS | `--nb-` | `--nb-color-accent`, `--nb-space-3` |
| Modules JS | kebab-case | `nav.js`, `cookie-bar.js` |

> Aucune trace des anciens préfixes : **0** `mts_`, **0** `of_get_option`, **0** text domain `mythemeshop`.

## 2. PHP

- `declare(strict_types=1);` en tête de **chaque** fichier PHP.
- Typage des signatures (params + retour) partout où c'est possible (PHP 8.2+).
- Garde anti-accès direct en tête de chaque fichier inclus :
  `if ( ! defined( 'ABSPATH' ) ) { exit; }`
- Pas de logique métier dans `functions.php` (require uniquement).
- Réglages **en dur** dans `config.php` — jamais de panneau d'options.

## 3. Sécurité (non négociable)

- **Sortie** : tout echo passe par `esc_html()` / `esc_attr()` / `esc_url()` / `wp_kses_post()`.
- **Entrée** : `sanitize_text_field()`, `absint()`, `wp_unslash()` selon le type.
- **Formulaires d'écriture / AJAX propres au thème** : `wp_nonce_field()` + `check_ajax_referer()` / `wp_verify_nonce()`. Les formulaires natifs WordPress (recherche GET, commentaires, widgets) conservent la protection du cœur.
- **Traductions échappées** : préférer `esc_html__()` / `esc_attr__()` à `__()` en sortie directe.
- **Aucun** appel réseau distant côté serveur. Les scripts navigateur GA4/AdSense explicitement configurés restent autorisés. **Aucun** fichier orphelin/suspect.

## 4. CSS

- **CSS natif moderne** : `@layer`, nesting, custom properties, `aspect-ratio`, propriétés logiques
  (`margin-inline`, `padding-block`…). **Pas de SCSS, pas de `rtl.css`.**
- Ordre des couches déclaré en tête de `app.css` :
  `@layer settings, base, layout, components, templates;`
- **Mobile-first** : styles de base = mobile ; `@media (min-width: …)` pour monter en largeur.
- Tokens uniquement via `--nb-*` (miroir de `theme.json`). Pas de valeurs magiques en dur dans les composants.
- **Anti-CLS** : toute image/pub/conteneur a des dimensions réservées (`width`+`height`, `aspect-ratio`, `min-height`).

## 5. JavaScript

- **Vanilla uniquement**, modules ES. **0** jQuery.
- Strict minimum : navigation/burger, barre cookies, (lightbox si réellement justifiée).
- Chargé en `defer`. Accessibilité : `aria-expanded`, gestion du focus.

## 6. Accessibilité (WCAG 2.2 AA)

- Landmarks ARIA (`<header>`, `<nav>`, `<main>`, `<aside>`, `<footer>`), `alt` sur toutes les images.
- Contrastes validés AA (attention aux liens bleus `#0274be` et accent rouge `#ee210b` sur blanc).
- Navigation clavier complète, focus visibles.

## 7. SEO

- HTML5 sémantique sans microdata Schema.org dupliquée.
- `<h1>` unique par page (logo en `<h1>` sur accueil/404, `<h2>` ailleurs).
- Title, Open Graph, meta description et schema JSON-LD délégués à Yoast SEO ou RankMath.

## 8. i18n

- **Source des chaînes : anglais** dans le code → traduction **FR** (`fr_FR.po`/`.mo`).
- `load_theme_textdomain( 'nanoboy', … )` dans `inc/setup.php`.
- `.pot` généré via `wp i18n make-pot`. Seuls les `.mo` sont nécessaires au runtime.

## 9. Build & assets

- 2 bundles servis en prod uniquement : `app.min.css` + `app.min.js`.
- Cache-busting par `filemtime()` (noms de fichiers stables, pas de hash).
- `src/`, `package.json`, `node_modules/`, `.po`, `.ai/` ne sont **pas** livrés en prod.
