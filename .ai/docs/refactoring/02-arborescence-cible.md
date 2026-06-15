# NanoBoy — Arborescence cible & chaîne de build

> **Principe directeur :** structure **dev claire et modulaire** (chaque responsabilité dans son fichier) → **build** → **prod compacte** (2 assets minifiés chargés, zéro source servie).
> **Dernière révision :** 2026-06-08

---

## 1. Vue d'ensemble

```
nanoboy/
├── style.css            ← métadonnées du thème UNIQUEMENT (en-tête WP requis)
├── theme.json           ← design tokens (couleurs, espacements, typo)
├── functions.php        ← bootstrap léger : charge inc/*
├── config.php           ← réglages EN DUR (logo, GA4, AdSense, réseaux, layout)
├── screenshot.png
├── README.md
│
├── inc/                 ← modules PHP (logique) — préfixe nanoboy_
├── parts/               ← fragments de templates réutilisables (get_template_part)
├── *.php (racine)       ← templates WP (hiérarchie classique, obligatoires à la racine)
│
├── src/                 ← SOURCES DEV (jamais chargées en prod)
│   ├── css/             ← CSS natif moderne en couches
│   └── js/              ← modules ES
│
├── assets/              ← SORTIE BUILD (ce qui est servi)
│   ├── css/app.min.css  ← 1 seul bundle CSS minifié
│   ├── js/app.min.js    ← 1 seul bundle JS minifié
│   ├── img/             ← images du thème (logo, favicons…)
│   └── icons.svg        ← sprite SVG (remplace FontAwesome)
│
├── languages/           ← i18n : nanoboy.pot + fr_FR + en_US
│
├── package.json         ← scripts esbuild (dev / build)
└── .gitignore
```

> **Clé de la perf :** `style.css` ne contient **que** l'en-tête de métadonnées WP. Les vrais styles sont dans `assets/css/app.min.css`, **enqueué** explicitement. WordPress n'auto-charge pas `style.css` — on garde donc la maîtrise totale.

---

## 2. Détail DEV — `inc/` (modules PHP)

| Fichier | Responsabilité |
|---|---|
| `setup.php` | `add_theme_support`, tailles d'images, menus, `load_theme_textdomain` |
| `enqueue.php` | Enqueue `app.min.css` / `app.min.js` (versionnés par `filemtime`), gtag + AdSense en `async` |
| `template-helpers.php` | Remplaçants des `mts_*` : `nanoboy_thumbnail()`, `nanoboy_excerpt()`, `nanoboy_readmore()`, `nanoboy_breadcrumb()`, `nanoboy_pagination()` |
| `nav.php` | Enregistrement du menu + walker, logique du **burger mobile** |
| `term-meta.php` | Champ natif `titre-h1` sur catégories/tags (`add_term_meta`/`get_term_meta`) |
| ~~`seo.php`~~ | ❌ Supprimé — title, meta, Open Graph et schema JSON-LD délégués au plugin SEO |
| `ads.php` | Rendu des emplacements AdSense **avec conteneur à espace réservé (anti-CLS)** |
| `comments.php` *(callback)* | Rendu custom des commentaires |
| `widgets.php` | Enregistrement des sidebars + chargement de `widgets/` |
| `widgets/recent-posts.php` | Widget articles récents |
| `widgets/popular-posts.php` | Widget articles populaires |
| `widgets/category-posts.php` | Widget articles par catégorie |

> `functions.php` se contente de `require` chaque module de `inc/` (idéalement via une petite boucle ou un autoloader léger). Aucune logique métier dans `functions.php`.

## 3. Détail DEV — `parts/` (fragments réutilisables)

| Fichier | Utilisé par |
|---|---|
| `header-site.php` | `header.php` |
| `footer-site.php` | `footer.php` |
| `cookie-bar.php` | `footer.php` (barre cookies, JS vanilla) |
| `card-article.php` | accueil, archive, recherche (la **carte article unique**, factorisée) |
| `ad-slot.php` | accueil, archive, single (reçoit un id de slot + dimensions) |
| `post-meta.php` | single (date, auteur, catégorie) |
| `related-posts.php` | single |
| `breadcrumb.php` | single, page, archive |

> **Gain clé :** la carte article est écrite **une seule fois** (`card-article.php`) et réutilisée partout — fin de la duplication massive du thème actuel (index/archive recopiaient le même bloc).

## 4. Templates WP à la racine (obligatoire en thème classique)

`header.php` · `footer.php` · `sidebar.php` · `front-page.php` (accueil) · `index.php` (fallback) · `single.php` · `archive.php` · `page.php` · `search.php` · `searchform.php` · `404.php` · `comments.php`

> Chaque template est **mince** : il orchestre la boucle et appelle des `parts/`. Pas de HTML dupliqué, pas d'AdSense recopié inline.

---

## 5. Détail DEV — `src/css/` (CSS natif moderne, en couches)

```
src/css/
├── app.css                 ← ENTRÉE : déclare l'ordre des @layer + @import des partials
├── 1-settings/             ← @layer settings : custom properties (miroir de theme.json)
│   ├── tokens.css
│   └── breakpoints.css
├── 2-base/                 ← @layer base : reset moderne, typo, éléments HTML
│   ├── reset.css
│   ├── typography.css
│   └── elements.css
├── 3-layout/               ← @layer layout : conteneur boxed, grille 8/12, header, footer, sidebar
│   ├── container.css
│   ├── grid.css
│   ├── header.css
│   ├── footer.css
│   └── sidebar.css
├── 4-components/           ← @layer components : briques réutilisables
│   ├── card.css
│   ├── button.css
│   ├── pagination.css
│   ├── breadcrumb.css
│   ├── ad-slot.css         ← dimensions réservées anti-CLS
│   ├── cookie-bar.css
│   └── comments.css
└── 5-templates/            ← @layer templates : spécificités par écran
    ├── home.css
    ├── single.css
    ├── archive.css
    └── 404.css
```

- **`@layer settings, base, layout, components, templates;`** déclaré en tête de `app.css` → cascade maîtrisée, zéro guerre de spécificité.
- **CSS natif** : nesting, custom properties, `@layer`, `aspect-ratio`, propriétés logiques (`margin-inline`…). **Pas de SCSS.**
- **Mobile-first** : styles de base = mobile, `@media (min-width: …)` pour monter en largeur (boxed desktop).
- **RTL** : géré par propriétés logiques → **plus de `rtl.css`**.

## 6. Détail DEV — `src/js/` (vanilla, modules ES)

```
src/js/
├── app.js                  ← ENTRÉE : importe les modules nécessaires
└── modules/
    ├── nav.js              ← menu burger mobile (a11y : aria-expanded, focus)
    ├── cookie-bar.js       ← consentement, localStorage
    └── lightbox.js         ← OPTIONNEL (chargé seulement si un écran le justifie)
```

- **Zéro jQuery.** `app.js` est le seul point d'entrée, bundlé en `app.min.js`.
- Chargé en **`defer`** (non bloquant).

---

## 7. Chaîne de build (esbuild — outil unique)

`package.json` :

```json
{
  "scripts": {
    "dev":   "npm-run-all --parallel watch:*",
    "watch:css": "esbuild src/css/app.css --bundle --outfile=assets/css/app.min.css --watch",
    "watch:js":  "esbuild src/js/app.js  --bundle --outfile=assets/js/app.min.js  --watch",
    "build:css": "esbuild src/css/app.css --bundle --minify --target=chrome111,firefox113,safari16 --outfile=assets/css/app.min.css",
    "build:js":  "esbuild src/js/app.js  --bundle --minify --target=es2022 --outfile=assets/js/app.min.js",
    "build": "npm-run-all build:css build:js",
    "i18n": "wp i18n make-pot . languages/nanoboy.pot"
  }
}
```

- **esbuild** inline les `@import` CSS, abaisse le nesting selon `--target`, minifie CSS **et** JS. Un seul outil, binaire ultra-rapide.
- **Dev** : `npm run dev` → watch, sources lisibles, rebuild instantané.
- **Prod** : `npm run build` → `app.min.css` + `app.min.js` minifiés.
- **Cache-busting** : versionnage par `filemtime()` dans `enqueue.php` (noms de fichiers stables, pas de hash).
- *(Option future : remplacer esbuild-CSS par **lightningcss** si on veut l'autoprefixing automatique. Non nécessaire en CSS moderne ciblé.)*

---

## 8. Stratégie de chargement en PROD (compacte & rapide)

| Asset | Chargement |
|---|---|
| `assets/css/app.min.css` | 1 seul `<link>` dans `<head>`, versionné |
| `assets/js/app.min.js` | 1 seul `<script defer>` |
| `gtag.js` (GA4) | `async` |
| `adsbygoogle.js` | `async`, conteneurs à espace réservé |
| **Polices** | **aucune** (stack système) |
| **Icônes** | sprite `assets/icons.svg` inline (pas de FontAwesome) |

**Total requêtes thème en prod : 2 fichiers** (1 CSS + 1 JS) + images. Aucune source `src/` servie.

---

## 9. Mapping « ancien → nouveau »

| Ancien | Nouveau |
|---|---|
| `functions.php` (45 Ko monolithe) | `functions.php` (bootstrap) + `inc/*` |
| `functions/theme-actions.php` | `inc/template-helpers.php` ; SEO délégué au plugin |
| `functions/nav-menu.php` | `inc/nav.php` |
| `functions/contact-form.php` | ❌ supprimé |
| `functions/metaboxes.php` | ❌ supprimé (seul `titre-h1` → `inc/term-meta.php`) |
| `functions/widget-*.php` (13) | `inc/widgets/*` (3 conservés) |
| `functions/twitteroauth.php`, `plugin-activation.php`, `welcome-message.php` | ❌ supprimés |
| `options/`, `theme-options.php`, `theme-presets.php` | ❌ → `config.php` |
| `Tax-meta-class/` | ❌ supprimé |
| `woocommerce/` | ❌ supprimé |
| `index.php` (logique slider + featured) | `front-page.php` + `index.php` + `parts/card-article.php` |
| `single.php` | `single.php` + `parts/` (meta, related, ad-slot) |
| `style.scss` | ❌ → `src/css/**` (natif) |
| `style.css` (42 Ko) | en-tête métadonnées seul + `assets/css/app.min.css` |
| `css/*.css` (responsive, rtl, flexslider, fontawesome, prettyPhoto, woocommerce2) | ❌ → `src/css/**` réécrit ; icônes → sprite SVG |
| `js/*.js` (jQuery : flexslider, prettyPhoto, sticky, parallax, history, ajax, contact) | ❌ → `src/js/modules/*` (vanilla) |
| `js/dghpqhgv.php` | ❌ supprimé (fichier suspect) |
| `Tax-meta-class/`, `lang/` | ❌ / → `languages/` |

---

## 10. Ce qui n'est PAS livré en prod

`src/` · `package.json` · `node_modules/` · fichiers `.po` (seuls les `.mo` sont nécessaires au runtime) · `README.md` · `.ai/`.

> `.gitignore` exclut `node_modules/` et les bundles `assets/*.min.*`. Les bundles sont générés au déploiement ou en CI et ne sont pas commités.

---

## Décisions confirmées

- Les bundles `assets/*.min.*` ne sont pas commités; ils sont générés au déploiement ou en CI.
- La page contact et son formulaire custom sont supprimés.
- `npm-run-all` reste la dépendance d'orchestration des scripts de build.
