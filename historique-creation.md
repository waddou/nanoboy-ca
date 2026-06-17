# Historique de création du thème nanoboy-ca

Récapitulatif chronologique des actions réalisées pour construire ce thème WordPress, reconstituées depuis l'historique git.

---

## Commit 1 — `dfe2333` · 15 juin 2026, 12h32
**feat: initialize NanoBoy CA theme**

Pose l'intégralité de la base du thème en une seule fois (91 fichiers, +10 834 lignes). Ce commit est un fork de `nanoboy-cp` déjà finalisé — il intègre d'emblée le fil d'Ariane, les design tokens A2 et les traductions Choix Assurances.

### Ce qui a été créé

**Documentation & IA**
- `.ai/README.md` — présentation du dossier IA
- `.ai/claude/CLAUDE.md` + répertoire de skills (frontend-design)
- `.ai/docs/` — captures UI, critique design, intégration AdSense, architecture, conventions, roadmap, plan de refactoring en 3 étapes, audit phase 9
- `CLAUDE.md` / `AGENTS.md` / `README.md` — guides pour Claude Code et les agents

**Structure WordPress**
- Templates principaux : `front-page.php`, `index.php`, `single.php`, `page.php`, `archive.php`, `search.php`, `404.php`, `comments.php`
- Fichiers socle : `header.php`, `footer.php`, `sidebar.php`, `searchform.php`, `functions.php`, `config.php`, `style.css`, `theme.json`

**Includes (`inc/`)**
- `setup.php` — enregistrement du thème
- `enqueue.php` — chargement CSS/JS
- `nav.php` — navigation
- `ads.php` — emplacements publicitaires
- `comments.php` — personnalisation commentaires
- `template-helpers.php` — fonctions utilitaires (inclut fil d'Ariane natif + support SEO)
- `term-meta.php` — métadonnées de taxonomie
- `widgets.php` + trois widgets : `category-posts`, `popular-posts`, `recent-posts`

**Parts (`parts/`)**
- `header-site.php`, `footer-site.php`, `ad-slot.php`, `breadcrumb.php`, `card-article.php`, `post-meta.php`, `related-posts.php`

**Architecture CSS (`src/css/`)**
- Couche 1 — Settings : `tokens.css` (design tokens), `breakpoints.css`
- Couche 2 — Base : `reset.css`, `elements.css`, `typography.css`
- Couche 3 — Layout : `container.css`, `grid.css`, `header.css`, `sidebar.css`, `footer.css`
- Couche 4 — Composants : `card.css`, `button.css`, `breadcrumb.css`, `ad-slot.css`, `comments.css`, `pagination.css`
- Couche 5 — Templates : `home.css`, `archive.css`, `single.css`, `404.css`
- Point d'entrée : `src/css/app.css`

**JavaScript (`src/js/`)**
- `app.js` — point d'entrée
- `modules/nav.js` — comportement navigation

**Assets**
- `assets/icons.svg` — sprite d'icônes SVG (avec icônes breadcrumb)
- `assets/img/favicon.ico`
- `screenshot.png` — aperçu thème

**Build & tooling**
- `package.json` / `package-lock.json`
- `scripts/make-pot.js` — génération du fichier `.pot` i18n
- `scripts/package-theme.js` — script de packaging
- `scripts/sync-translations.js` — synchronisation des traductions

**Internationalisation**
- Fichiers `fr_FR` et `en_US` (`.po` + `.mo`) + `nanoboy.pot` (adaptés Choix Assurances)

---

## Commit 2 — `4535f19` · 15 juin 2026, 12h37
**chore: regenerate Choix Assurances translations**

Regénération des binaires de traduction (2 fichiers).

- **`languages/en_US.mo`** + **`languages/fr_FR.mo`** — recompilation des fichiers `.mo` après ajustement des chaînes spécifiques à Choix Assurances

---

## Commit 3 — `59c2199` · 15 juin 2026, 14h03
**docs: define A2 blue immersive direction**

Documentation de la direction artistique avant implémentation (1 fichier, +43 lignes).

- **`.ai/docs/a2-blue-immersive-design.md`** — cahier des charges de la direction visuelle "A2 Blue Immersive" : palette bleue profonde, header immersif, mise en avant de l'article principal

---

## Commit 4 — `95aaeca` · 15 juin 2026, 14h03
**style: apply A2 immersive blue palette**

Application de la palette bleue immersive (3 fichiers, +35 / -34 lignes).

- **`src/css/1-settings/tokens.css`** — refonte des variables couleurs vers le bleu profond A2
- **`src/css/2-base/typography.css`** — ajustements typographiques liés à la nouvelle palette
- **`theme.json`** — mise à jour des couleurs dans la configuration WordPress

---

## Commit 5 — `6f136d0` · 15 juin 2026, 14h04
**style: add immersive header and featured story**

Mise en place du header immersif et de la mise en avant de l'article principal (4 fichiers, +53 / -20 lignes).

- **`src/css/3-layout/header.css`** — header avec fond bleu plein, effet immersif
- **`src/css/4-components/card.css`** — style "featured story" pour la carte principale en homepage
- **`src/css/5-templates/home.css`** — ajustement du layout homepage
- **`src/css/3-layout/footer.css`** — correction mineure footer

---

## Commit 6 — `b369f10` · 15 juin 2026, 14h05
**style: polish A2 content surfaces**

Finition des surfaces de contenu dans l'identité A2 (5 fichiers, +29 / -15 lignes).

- **`src/css/4-components/card.css`** — peaufinage des cartes d'articles
- **`src/css/4-components/button.css`** — boutons adaptés à la palette bleue
- **`src/css/3-layout/sidebar.css`** — sidebar harmonisée
- **`src/css/5-templates/archive.css`** — page archive polie
- **`src/css/5-templates/single.css`** — article individuel affiné

---

## Commit 7 — `8aacb27` · 15 juin 2026, 14h17
**feat: integrate Choix Assurances logo and favicon**

Intégration des assets visuels de la marque Choix Assurances (5 fichiers, +19 / -26 lignes).

- **`assets/img/logo.png`** — logo principal Choix Assurances
- **`assets/img/logo-header.png`** — variante logo pour le header
- **`assets/img/favicon.ico`** — favicon de la marque (remplacement, 101 Ko → 1,15 Ko)
- **`parts/header-site.php`** — intégration du logo dans le markup header
- **`src/css/3-layout/header.css`** — styles ajustés pour le nouveau logo

---

## Commit 8 — `3adec80` · 15 juin 2026, 14h17
**chore: refresh WordPress theme screenshot**

Mise à jour de la capture d'écran du thème (1 fichier).

- **`screenshot.png`** — nouvelle capture reflétant l'identité A2 Blue Immersive avec logo Choix Assurances (81 Ko → 83 Ko)

---

## Commit 9 — `c5de2ae` · 15 juin 2026, 14h20
**chore: configure Analytics and AdSense publisher**

Configuration des IDs de tracking (1 fichier, +2 / -2 lignes).

- **`config.php`** — renseignement de l'ID Google Analytics 4 et de l'ID publisher AdSense pour Choix Assurances

---

## Commit 10 — `2461f95` · 15 juin 2026, 14h29
**chore: configure AdSense slots**

Configuration des emplacements publicitaires spécifiques (1 fichier, +4 / -4 lignes).

- **`config.php`** — définition des IDs de slots AdSense (header, sidebar, in-content, footer)

---

## Commit 11 — `9cbd00f` · 15 juin 2026, 14h54
**feat: number practical guide listings**

Ajout de la numérotation des articles dans les listes (5 fichiers, +12 / -4 lignes).

- **`parts/card-article.php`** — affichage du numéro de position dans la liste
- **`archive.php`**, **`front-page.php`**, **`index.php`**, **`search.php`** — transmission du compteur aux templates de cartes

---

## Commit 12 — `aac992c` · 15 juin 2026, 14h56
**style: introduce M2 practical guide identity**

Nouvelle direction visuelle "M2 guide pratique" en remplacement de l'A2 (5 fichiers, +90 / -68 lignes).

- **`src/css/4-components/card.css`** — refonte majeure : layout en lignes horizontales style guide pratique
- **`src/css/1-settings/tokens.css`** — ajustement des tokens couleurs vers M2
- **`src/css/2-base/typography.css`** — adaptation typographique
- **`src/css/3-layout/grid.css`** — ajustement de la grille
- **`theme.json`** — mise à jour de la configuration couleurs

---

## Commit 13 — `20cb7ca` · 15 juin 2026, 14h57
**style: simplify M2 guide rows**

Simplification des lignes de guide M2 (1 fichier, +16 / -8 lignes).

- **`src/css/4-components/card.css`** — allègement du CSS des lignes, suppression des règles redondantes

---

## Commit 14 — `891f092` · 15 juin 2026, 15h02
**revert: remove numbered guide listings**

Abandon de la numérotation des articles (5 fichiers, +4 / -12 lignes).

- **`parts/card-article.php`** — suppression de l'affichage du numéro
- **`archive.php`**, **`front-page.php`**, **`index.php`**, **`search.php`** — retrait du passage du compteur

---

## Commit 15 — `89441ab` · 15 juin 2026, 15h02
**style: restore compact thumbnail cards**

Retour aux cartes compactes avec vignette (1 fichier, +28 / -35 lignes).

- **`src/css/4-components/card.css`** — abandon du style lignes M2, retour à des cartes compactes avec thumbnail et contenu condensé

---

## Commit 16 — `de97d6d` · 15 juin 2026, 15h03
**style: centralize content on a white canvas**

Recentrage du contenu sur fond blanc (3 fichiers, +9 / -11 lignes).

- **`src/css/3-layout/grid.css`** — centrage du contenu principal, fond blanc
- **`src/css/5-templates/archive.css`** — simplification de la page archive
- **`src/css/2-base/typography.css`** — retrait de règles typographiques superflues

---

## Synthèse

| # | Hash | Action principale |
|---|------|-------------------|
| 1 | `dfe2333` | Création complète de la base du thème (fork de nanoboy-cp) |
| 2 | `4535f19` | Recompilation des traductions Choix Assurances |
| 3 | `59c2199` | Documentation direction artistique A2 Blue Immersive |
| 4 | `95aaeca` | Application de la palette bleue immersive |
| 5 | `6f136d0` | Header immersif + mise en avant article principal |
| 6 | `b369f10` | Finition surfaces de contenu A2 |
| 7 | `8aacb27` | Intégration logo et favicon Choix Assurances |
| 8 | `3adec80` | Mise à jour screenshot thème |
| 9 | `c5de2ae` | Configuration GA4 + ID publisher AdSense |
| 10 | `2461f95` | Configuration des slots AdSense |
| 11 | `9cbd00f` | Numérotation des articles dans les listes |
| 12 | `aac992c` | Direction M2 guide pratique (lignes horizontales) |
| 13 | `20cb7ca` | Simplification des lignes M2 |
| 14 | `891f092` | Abandon de la numérotation (revert) |
| 15 | `89441ab` | Retour aux cartes compactes avec vignette |
| 16 | `de97d6d` | Recentrage contenu sur fond blanc |

### Phases de développement

1. **Initialisation** (commits 1–2) — fork de `nanoboy-cp`, adaptation aux traductions Choix Assurances
2. **Direction A2 Blue Immersive** (commits 3–8) — palette bleue profonde, header immersif, intégration de la marque
3. **Configuration** (commits 9–10) — mise en place GA4 et AdSense
4. **Expérimentation M2 guide pratique** (commits 11–16) — essai numérotation et lignes horizontales, puis retour à un style compact sur fond blanc
