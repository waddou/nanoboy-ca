# NanoBoy — Vision & objectifs de la refonte

> **Statut :** brouillon de travail · itératif
> **Dernière révision :** 2026-06-08
> **Nature du projet :** reconstruction *greenfield* d'un nouveau thème reproduisant l'identité visuelle de l'actuel — **pas** un refactoring incrémental de l'existant.

---

## 1. Contexte & problème

Le thème actuel (`ouvrir`) est basé sur MythemeShop et accumule une dette technique lourde :

- Base ancienne : widgets Google+ (service mort), Facebook Like Box, intégration Twitter OAuth, framework TGM Plugin Activation.
- `functions.php` monolithique (~45 Ko) avec **32 références WooCommerce** entremêlées.
- Stack JS entièrement **jQuery** (flexslider, prettyPhoto, sticky, parallax).
- Framework d'options custom SMOF (`options/`) dont dépend tout le thème via `of_get_option()`.
- Présence d'un fichier suspect `js/dghpqhgv.php` (nom aléatoire, vide) — signature typique de backdoor, à éliminer.

Désentrelacer ce legacy coûterait plus cher que repartir d'une base propre. **Décision : nouveau thème greenfield qui reproduit le visuel, sans réutiliser le code source d'origine.**

## 2. Vision NanoBoy

> Un thème WordPress classique, **ultra-rapide, sans CLS, sécurisé, sémantique et standard**, qui reproduit l'identité visuelle de l'actuel avec une base de code moderne, homogène et minimaliste.

## 3. Principes directeurs

| Axe | Engagement |
|---|---|
| **Performance** | Aucun CLS, assets minimaux, zéro dépendance superflue, JS vanilla |
| **Sécurité** | Échappement et sanitization systématiques, nonces, aucun appel distant côté serveur, aucun fichier orphelin |
| **Standards** | HTML5 sémantique valide, PHP moderne typé, CSS natif moderne |
| **Compatibilité** | WordPress 7.0, PHP 8.2+ |
| **Accessibilité** | WCAG 2.2 niveau AA (landmarks, labels, `alt`, contrastes définis dans les tokens) |
| **SEO** | Balisage sémantique ; title, Open Graph et schema JSON-LD délégués au plugin SEO |
| **i18n** | Bilingue **EN + FR** via gettext (`.po`/`.mo`) |
| **Maintenabilité** | Nomenclature homogène, structure dev lisible, build prod minimaliste |

## 4. Périmètre

### Dans le scope
- Templates WordPress classiques modernisés (header, footer, index, single, page, archive, search, 404, comments, sidebar).
- Design tokens via `theme.json`.
- Réglages de configuration **codés en dur** dans un `config.php` (logo, réseaux sociaux, analytics, layout).
- Chaînes d'interface **traduisibles** (EN source + FR).
- JS vanilla réduit au strict nécessaire (navigation/menu et fonctions indispensables).
- Chaîne de build : CSS/JS séparés en dev → 1 fichier minifié chacun en prod.

### Hors scope (non-goals)
- ❌ WooCommerce et toute fonctionnalité e-commerce — **suppression définitive**.
- ❌ Framework d'options `options/` (SMOF) et tout panneau d'administration de réglages.
- ❌ Widgets sociaux legacy (Google+, Facebook Like Box, Twitter/tweets).
- ❌ jQuery et ses plugins (flexslider, prettyPhoto, sticky, parallax).
- ❌ Twitter OAuth, TGM Plugin Activation, et tout appel à un service distant.
- ❌ Block theme / Full Site Editing (on reste sur un **thème classique** modernisé).
- ❌ `js/dghpqhgv.php` et tout fichier orphelin/suspect.

## 5. Décisions d'architecture

| # | Décision | Choix retenu |
|---|---|---|
| 1 | Type de thème | **Classique PHP modernisé** + `theme.json` pour les tokens (pas de FSE) |
| 2 | Remplacement de `options/` | **Tout en dur** dans `config.php` (pas de panneau d'options) |
| 3 | Langues | **Bilingue EN + FR** via gettext — *les réglages sont en dur, les textes restent traduisibles* |
| 4 | JavaScript | **Vanilla**, strict minimum (menu + indispensables), zéro jQuery |
| 5 | WooCommerce | **Supprimé définitivement** |
| 6 | Build | **CSS natif moderne** (nesting, `@layer`, custom properties) + **esbuild** (bundle + minify), npm scripts |
| 7 | Nomenclature | Fonctions `nanoboy_…()`, constantes `NANOBOY_…`, text domain `nanoboy`, CSS en **BEM** + tokens `--nb-…`, fichiers `kebab-case` |
| 8 | Vérification | **Revue de code** uniquement (pas de tests d'exécution réels à ce stade) |
| 9 | Champs custom | **Aucune metabox d'article ni `Tax-meta-class`.** On conserve **un seul** champ : `titre-h1` (H1 personnalisé par catégorie/tag, utilisé dans les archives), reimplémenté en **term meta natif WP** dans `inc/` |

### Structure de développement cible (proposition)

```
nanoboy/
├── theme.json              # design tokens
├── functions.php           # bootstrap minimal (require des modules)
├── config.php              # réglages en dur (logo, réseaux, analytics…)
├── inc/                    # modules PHP (setup, enqueue, nav, helpers…)
├── templates/ + *.php      # templates WordPress classiques
├── src/
│   ├── css/                # partials logiques (tokens, base, layout, components/)
│   └── js/                 # modules ES (navigation, …)
├── assets/                 # sortie build : style.min.css, main.min.js (prod)
└── lang/                   # nanoboy.pot, fr_FR, en_US
```

## 6. Critères de succès (vérifiables par revue de code)

- [ ] **0** référence à jQuery, WooCommerce, SMOF/`options/` dans le code final.
- [ ] **1 seul** CSS minifié + **1 seul** JS minifié chargés en prod.
- [ ] Toutes les sorties échappées (`esc_html`/`esc_attr`/`esc_url`/`wp_kses`).
- [ ] Tous les formulaires d'écriture/AJAX propres au thème protégés par **nonces** + sanitization des entrées.
- [ ] HTML5 sémantique, landmarks ARIA, `alt` sur les images — conforme **WCAG 2.2 AA**.
- [ ] PHP **8.2+** : `declare(strict_types=1)`, typage des signatures.
- [ ] `theme.json` présent, tokens cohérents avec les contrastes AA.
- [ ] Text domain unique `nanoboy`, chargé via `load_theme_textdomain`, `.po`/`.mo` EN + FR.
- [ ] Schema JSON-LD et Open Graph délégués au plugin SEO, sans microdata dupliquée dans le thème.
- [ ] Aucun appel réseau distant côté serveur, aucun fichier orphelin/suspect.
- [ ] Nomenclature 100 % homogène (préfixes, casse, conventions BEM).

## 7. Ce qu'on supprime explicitement

`options/` · `woocommerce/` · `Tax-meta-class/` · `functions/metaboxes.php` (metaboxes d'article — non utilisées dans les archives) · `theme-options.php` · `theme-presets.php` · `functions/twitteroauth.php` · `functions/plugin-activation.php` · `functions/welcome-message.php` · widgets `widget-googleplus`, `widget-fblikebox`, `widget-tweets`, `widget-ad125`, `widget-ad300` · `js/*.js` jQuery (flexslider, prettyPhoto, sticky, parallax, history, ajax, contact, customscript) · `js/dghpqhgv.php` · templates parallax (`page-parallax.php`, `singlepost-parallax.php`).

**Seul champ custom conservé :** `titre-h1` (term meta catégorie/tag) → réimplémenté en API native WP.

## 8. Contraintes & risques

- **Fidélité visuelle** : reproduire « proche » la structure visuelle sans réutiliser le code → nécessite une référence visuelle fiable *(voir À confirmer ci-dessous)*.
- **Suppression de fonctionnalités** : drop des sliders/parallax/widgets sociaux = perte de features visibles, assumée au profit de la perf.
- **Bilinguisme** : sans plugin multilingue, EN/FR via gettext gère l'interface mais pas le contenu éditorial bilingue (hors scope thème).

---

## Décisions confirmées (résiduelles résolues)

- **a — Langue source du code** : ✅ chaînes en **anglais** dans le code + traduction **FR** (`fr_FR.po`/`.mo`).
- **b — Slider / lightbox / parallax** : ✅ **supprimés**. Templates parallax retirés, pas de slider. Lightbox vanilla seulement si un écran le justifie réellement.
- **c — Référence visuelle** : reproduction depuis le **rendu actuel du thème** *(à ajuster si une maquette/site en ligne est fourni)*.
- **d — Réglages en dur vs bilingue** : ✅ réglages **en dur** (`config.php`), textes d'interface **traduisibles** (gettext).

## 9. Publicités AdSense — ✅ tranché

On **conserve les mêmes blocs AdSense en dur** (`ca-pub-9582901796643932` + slots existants), définis dans `config.php`. **Anti-CLS impératif** : chaque emplacement publicitaire reçoit un **conteneur à dimensions réservées** en CSS (`min-height` / `aspect-ratio`) afin que l'espace soit occupé avant le chargement du script `adsbygoogle.js`. Le script tiers est chargé en `async`. Aucun décalage de mise en page toléré.

## 10. Layout & largeur — ✅ tranché

- **Boxed centré**, mais **plus large que l'actuel** sur desktop pour maximiser le visuel above-the-fold (cible conteneur ~**1100–1200 px**, l'existant fait ~900–1000 px).
- **Grille contenu + sidebar** en accueil (8/12 + 4/12), avec une seule colonne de cartes article dans la zone principale.
- **Mobile-first** : la version mobile est la base, le boxed desktop est une montée en largeur progressive.
- Hero = article épinglé en tête (comme l'existant).

## 11. Assets en dur — à fournir

Marque, favicon, ID analytics et IDs AdSense sont **codés en dur** (`config.php` + fichiers dans `assets/`). Voir la liste des fichiers/formats requis dans la section « Assets requis » ci-dessous.

## 12. Assets

> Objectif : netteté, poids minimal, **zéro CLS** (toutes les dimensions connues à l'avance), zéro requête tierce inutile.

### Assets actuels

| Asset | Fichier existant | Dimensions | Usage NanoBoy |
|---|---|---|---|
| **Wordmark** | Texte depuis `config.php` | Intrinsèque | « Compte Pro » bleu + tagline foncée |
| **Favicon** | `assets/img/favicon.ico` | 16×16 à 256×256 | Icône multi-résolution Compte Pro |

### À fournir / créer plus tard (optionnel, non bloquant)

| Asset | Format idéal | Statut |
|---|---|---|
| **Image OG par défaut** | JPG/PNG **1200×630** | À créer — pour Open Graph / SEO partages |
| **Icônes PWA** | PNG **192×192** & **512×512** | Optionnel — si web manifest |
| **ID Analytics** | GA4 | ✅ **`G-DZ67REDQ6G`** — codé en dur dans `config.php`, `gtag.js` en `async` |
| **IDs AdSense** | publisher + slots | Connus (`ca-pub-9582901796643932` + slots) ; hauteur cible par emplacement à définir |
| **Police** | stack système (reco) ou `.woff2` | Reco : **stack système** (zéro requête, zéro CLS) |
