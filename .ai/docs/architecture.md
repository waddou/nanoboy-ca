# NanoBoy — Architecture technique

> Vue d'ensemble du fonctionnement du thème : bootstrap, modules, templates, build, chargement prod.
> Détail exhaustif de l'arborescence : voir `refactoring/02-arborescence-cible.md`.
> **Dernière révision :** 2026-06-08

---

## 1. Type de thème

**Thème classique PHP modernisé** (hiérarchie de templates WordPress standard) + `theme.json` pour les
design tokens. **Pas de FSE / block theme.** Cible : **WordPress 7.0, PHP 8.2+**.

## 2. Bootstrap & chargement

```
WordPress
  └─ functions.php          ← bootstrap léger, AUCUNE logique métier
       └─ require inc/*.php  ← chaque module enregistre ses hooks
```

- `functions.php` ne fait que définir les constantes (`NANOBOY_VERSION`, `NANOBOY_DIR`, `NANOBOY_URI`) et
  `require` les modules de `inc/`.
- `config.php` contient **tous les réglages en dur** (marque, GA4 `G-DZ67REDQ6G`, AdSense
  `ca-pub-9582901796643932` + slots, réseaux, layout). Remplace intégralement l'ancien framework `options/` (SMOF).
- Aucun panneau d'administration de réglages. Pour changer un réglage → on édite `config.php`.

## 3. Modules `inc/` (logique PHP)

| Fichier | Responsabilité |
|---|---|
| `setup.php` | `add_theme_support`, image sizes, menus, `load_theme_textdomain` |
| `enqueue.php` | Enqueue des 2 bundles (versionnés `filemtime`), `gtag.js` + `adsbygoogle.js` en `async` |
| `template-helpers.php` | `nanoboy_thumbnail()`, `nanoboy_excerpt()`, `nanoboy_readmore()`, `nanoboy_breadcrumb()`, `nanoboy_pagination()` |
| `nav.php` | Menu `wp_nav_menu` + walker + logique burger mobile |
| `term-meta.php` | **Seul champ custom conservé** : `titre-h1` sur catégories/tags (term meta natif WP) |
| ~~`seo.php`~~ | ❌ **Supprimé** — SEO délégué au plugin Yoast/RankMath (title, meta, Open Graph, schema JSON-LD). Cf. décision n°7. |
| `ads.php` | Rendu des emplacements AdSense **avec conteneur à espace réservé (anti-CLS)** |
| `comments.php` *(callback)* | Rendu custom des commentaires |
| `widgets.php` | Enregistrement des sidebars + chargement de `inc/widgets/*` |
| `widgets/recent-posts.php` · `popular-posts.php` · `category-posts.php` | 3 widgets conservés |

## 4. Templates & fragments

- **Templates racine** (obligatoires en thème classique) : `header.php`, `footer.php`, `sidebar.php`,
  `front-page.php`, `index.php`, `single.php`, `archive.php`, `page.php`,
  `search.php`, `searchform.php`, `404.php`, `comments.php`. *(pas de `page-contact.php` — décision tranchée)*
- Chaque template est **mince** : orchestre la boucle et appelle des `parts/` via `get_template_part()`.
- La **sidebar (4/12) est affichée en accueil** (décision tranchée).
- **Fragments `parts/`** : `header-site.php`, `footer-site.php`, `cookie-bar.php`, `card-article.php`
  (carte article **factorisée**, écrite une seule fois), `ad-slot.php`, `post-meta.php`,
  `related-posts.php`, `breadcrumb.php`. *(pas de `social-share.php` — décision tranchée)*
- **Navigation** : `wp_nav_menu` rendu **uniquement si un menu est assigné** dans l'admin (aucun `fallback_cb`).

## 5. Sources & build

- **`src/css/`** : CSS natif moderne en **couches `@layer`** (settings → base → layout → components → templates).
  Nesting, custom properties, `aspect-ratio`, propriétés logiques (RTL gratuit, pas de `rtl.css`). Mobile-first.
- **`src/js/`** : modules ES vanilla (`nav.js`, `cookie-bar.js`, `lightbox.js` optionnel). Point d'entrée `app.js`.
- **Build (esbuild)** : `src/css/app.css` → `assets/css/app.min.css` ; `src/js/app.js` → `assets/js/app.min.js`.
  `npm run dev` (watch) / `npm run build` (minify). Voir `refactoring/02` §7.

## 6. Chargement en PROD

| Asset | Chargement |
|---|---|
| `assets/css/app.min.css` | 1 `<link>` dans `<head>`, versionné `filemtime` |
| `assets/js/app.min.js` | 1 `<script defer>` |
| `gtag.js` (GA4) | `async` |
| `adsbygoogle.js` | `async` + conteneurs à espace réservé |
| Polices | **aucune** — stack système |
| Icônes | sprite `assets/icons.svg` inline (pas de FontAwesome) |

**Total thème prod : 2 fichiers** (1 CSS + 1 JS) + images. Aucune source `src/` servie.

> Les bundles `app.min.*` sont **buildés au déploiement/CI** et **non commités** (cf. `.gitignore`).

## 7. Ce qui est explicitement supprimé

`options/` (SMOF) · `woocommerce/` · `Tax-meta-class/` · metaboxes d'article · widgets sociaux legacy ·
jQuery + plugins (flexslider, prettyPhoto, sticky, parallax) · Twitter OAuth · TGM Plugin Activation ·
`js/dghpqhgv.php` (fichier suspect) · templates parallax · Google Fonts externes.
Mapping complet : `refactoring/02` §9.
