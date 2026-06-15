# NanoBoy — Plan d'exécution (roadmap)

> Ordre dans lequel construire le thème. Chaque phase est livrable et vérifiable par **revue de code**
> (pas de tests d'exécution réels à ce stade — cf. décision n°8).
> Cocher au fur et à mesure. **Dernière révision :** 2026-06-09

---

## Phase 0 — Préparation *(terminée)*

- [x] Création de l'arborescence de dossiers cible.
- [x] Documentation globale dans `.ai/` (README, architecture, conventions, roadmap).
- [x] Copie des docs de refactoring (01/02/03) comme source de vérité.
- [x] `CLAUDE.md` racine du thème + `.gitignore`.

## Phase 1 — Fondations du thème (bootstrap) *(terminée)*

- [x] `style.css` — **en-tête de métadonnées WP uniquement** (Theme Name: NanoBoy, version, text domain `nanoboy`…).
- [x] `functions.php` — constantes (`NANOBOY_VERSION`, `NANOBOY_DIR`, `NANOBOY_URI`) + `require` des modules `inc/`.
- [x] `config.php` — réglages en dur : marque Compte Pro, GA4 `G-DZ67REDQ6G`, AdSense `ca-pub-9582901796643932` + slots, réseaux, layout (~1100-1200px).
- [x] `inc/setup.php` — `add_theme_support`, image sizes, menus, `load_theme_textdomain`.
- [x] `inc/enqueue.php` — enqueue des 2 bundles (versionnés `filemtime`), gtag + AdSense en `async`.

## Phase 2 — Design tokens & CSS de base *(terminée)*

- [x] `theme.json` — tokens (palette §0 de l'inventaire, espacements, typo stack système).
- [x] `src/css/app.css` — déclaration des `@layer` + imports.
- [x] `1-settings/` — `tokens.css` (miroir theme.json), `breakpoints.css`.
- [x] `2-base/` — `reset.css`, `typography.css`, `elements.css`.
- [x] `3-layout/` — `container.css` (boxed), `grid.css` (8/12 + 4/12), `header.css`, `footer.css`, `sidebar.css`.

## Phase 3 — Chrome global (présent partout) *(terminée)*

- [x] `parts/header-site.php` + `header.php` — wordmark texte bleu + tagline foncée, `<h1>/<h2>` SEO.
- [x] `inc/nav.php` + `parts/` — menu `wp_nav_menu` rendu **uniquement si un menu est assigné** dans l'admin (pas de `fallback_cb`) + burger mobile vanilla (a11y).
- [x] `parts/footer-site.php` + `footer.php` — 3 colonnes widgets + barre copyright.
- [x] `parts/cookie-bar.php` + `src/js/modules/cookie-bar.js` — bandeau cookies vanilla + `localStorage`.
- [x] `src/js/app.js` + `modules/nav.js` — point d'entrée JS.

## Phase 4 — Helpers, pubs & composants réutilisables *(terminée)*

- [x] `inc/template-helpers.php` — `nanoboy_thumbnail/excerpt/readmore/breadcrumb/pagination`.
- [x] `inc/ads.php` + `parts/ad-slot.php` + `4-components/ad-slot.css` — emplacements AdSense **anti-CLS** (espace réservé).
- [x] `parts/card-article.php` + `4-components/card.css` — **carte article unique** (miniature + titre + extrait + lien).
- [x] `4-components/` — `button.css`, `pagination.css`, `breadcrumb.css`.

## Phase 5 — Templates de listing *(terminée)*

- [x] `front-page.php` — hero (article épinglé) + pub haut (slot 8689361629) + liste cartes + pub in-feed (slot 5396983870) + pagination + **sidebar 4/12 affichée**.
- [x] `index.php` — fallback.
- [x] `archive.php` — `<h1>` = `titre-h1` (term meta) sinon titre catégorie/tag, description, pubs, liste, pagination.
- [x] `inc/term-meta.php` — champ natif `titre-h1` sur catégories/tags.
- [x] `search.php` + `searchform.php` — résultats / message vide.
- [x] `5-templates/home.css`, `archive.css`.

## Phase 6 — Article seul & page *(terminée)*

- [x] `single.php` + `parts/post-meta.php`, `breadcrumb.php`, `related-posts.php`, `ad-slot.php`. *(pas de partage social)*
- [x] ~~`inc/seo.php`~~ — ❌ **supprimé** : SEO délégué au plugin Yoast/RankMath (title, meta, OG, schema JSON-LD). Cf. décision n°7.
- [x] `comments.php` + `inc/comments.php` (callback) + `4-components/comments.css`.
- [x] `page.php`. *(pas de `page-contact.php`)*
- [x] `5-templates/single.css`.

## Phase 7 — Sidebar, widgets & 404 *(terminée)*

- [x] `sidebar.php` + `inc/widgets.php` — sidebars natives (recherche, archives mensuelles).
- [x] `inc/widgets/recent-posts.php`, `popular-posts.php`, `category-posts.php`.
- [x] `404.php` + `5-templates/404.css` — message FR, formulaire de recherche, marque en `<h1>`.

## Phase 8 — i18n, build & finitions *(terminée)*

- [x] `assets/icons.svg` — sprite SVG (remplace FontAwesome).
- [x] `package.json` — scripts esbuild (dev/build/i18n).
- [x] `languages/` — `nanoboy.pot` + `fr_FR.po/.mo` + `en_US`.
- [x] `screenshot.png`, `README.md` racine.
- [x] Build initial → `assets/css/app.min.css` + `assets/js/app.min.js` *(générés au déploiement/CI, **non commités** — cf. `.gitignore`)*.

## Phase 9 — Revue finale (critères de succès) *(terminée)*

Vérifier chaque point de `refactoring/01` §6 :
- [x] 0 jQuery / WooCommerce / SMOF dans le code.
- [x] 1 CSS min + 1 JS min chargés en prod.
- [x] Toutes sorties échappées, formulaires d'écriture/AJAX propres au thème avec nonces.
- [x] WCAG 2.2 AA vérifiable statiquement (landmarks, alt, contrastes).
- [x] PHP 8.2+ typé, `declare(strict_types=1)`.
- [x] `theme.json` cohérent, text domain `nanoboy` chargé, `.po/.mo` EN+FR.
- [x] Schema/OG délégués au plugin SEO (pas de microdata dupliquée), 0 appel distant côté serveur, 0 fichier orphelin.
- [x] Nomenclature 100 % homogène.

Rapport détaillé : [`phase-9-audit.md`](phase-9-audit.md).

---

## Décisions tranchées (2026-06-08)

1. **Build** : ❌ **non commité**. Les bundles `assets/*.min.*` sont générés au déploiement/CI et ignorés par git (`.gitignore`).
2. **Page contact** : ❌ **supprimée**. Pas de `page-contact.php` ni `inc/contact.php`.
3. **Menu de navigation** : `wp_nav_menu` rendu **uniquement si un menu est assigné** à l'emplacement dans l'admin. Aucun `fallback_cb` (pas de liste de pages auto).
4. **Boutons de partage social** : ❌ **supprimés**. Pas de `parts/social-share.php` ni `social-share.css`.
5. **Sidebar en accueil** : ✅ **affichée** (4/12).
6. **Articles liés** : ✅ conservés (bloc en bas d'article).
7. **SEO** : délégué à un **plugin** (Yoast SEO **ou** RankMath). ❌ Pas de `inc/seo.php`. Le thème garde `add_theme_support('title-tag')` (compatible) et un **HTML5 sémantique** sans microdata `itemprop` (le plugin produit le schema JSON-LD + Open Graph). Le helper `nanoboy_breadcrumb()` **auto-détecte** le plugin (`yoast_breadcrumb()` / `rank_math_the_breadcrumbs()`) avec fallback maison.
