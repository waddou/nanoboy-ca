# NanoBoy — Inventaire visuel (écrans à reproduire)

> **Source :** identité Compte-Pro.info + structure des templates actuels.
> **Objectif :** lister, écran par écran et zone par zone, le rendu cible. Chaque zone note les **emplacements pubs** et les **points anti-CLS**.
> **Dernière révision :** 2026-06-08

---

## 0. Tokens visuels globaux (design system)

### Palette (extraite de `style.css`)

| Token | Valeur | Usage |
|---|---|---|
| `--nb-color-accent` | `#1e6091` | Marque, titres/accents, survols |
| `--nb-color-link` | `#1e6091` | Liens |
| `--nb-color-text` | `#33414b` | Texte courant |
| `--nb-color-text-strong` | `#101820` | Titres |
| `--nb-color-bg` | `#f4f8fb` | Fond quadrillé |
| `--nb-color-surface` | `#fff` | Cartes et widgets |
| `--nb-color-border` | `#cbdbe6` | Bordures, séparateurs |
| `--nb-color-muted` | `#52616d` | Texte secondaire, métadonnées |
| `--nb-color-mark` | `#dbeafe` | Surlignage `<mark>` |

> Contraste à valider WCAG 2.2 AA lors de l'intégration (notamment liens bleus et accent rouge sur blanc).

### Typographie

- **Actuel :** 3 Google Fonts externes (Nunito, Work Sans, Lato) → **supprimées** (requête tierce + CLS).
- **NanoBoy :** **stack système** (zéro requête, zéro CLS). Si une police de marque est imposée plus tard → `.woff2` auto-hébergé avec `font-display: swap` + `size-adjust`.

### Layout

- **Boxed centré**, conteneur cible **~1100–1200 px** (plus large que l'actuel ~900–1000).
- Grille contenu/sidebar : **8/12 + 4/12** (l'actuel met `c-4-12` sur l'aside).
- **Mobile-first** : 1 colonne empilée → sidebar passe sous le contenu.

---

## 1. Chrome global (présent sur tous les écrans)

### 1.1 Header
- **Marque texte** « Compte Pro » en bleu `#1e6091`, accompagnée d'une tagline gris foncé. `<h1>` sur accueil/404, `<h2>` ailleurs.
- **Navigation** : menu principal minimal (le live n'affiche pas de gros menu horizontal). À brancher sur un `wp_nav_menu` propre + **menu burger mobile** en vanilla JS.
- ❌ Supprimés : variantes `regular_header` / `logo_in_nav_header`, sticky-nav optionnel, zone `widget-header` ad-code. → **un seul header**, simple et fixe en structure.
- **Anti-CLS** : header à hauteur stable.

### 1.2 Footer
- **Zone widgets haut** : 3 colonnes (catégories, description du site, liens utiles) — conforme au live.
- **Barre copyright** en bas.
- ❌ Simplifié : on fige **3 colonnes** (pas l'option 4), via zones de widgets natives.

### 1.3 Barre cookies
- Bandeau FR en bas (texte actuel conservé), bouton « OK ».
- **NanoBoy :** réécrit en **vanilla JS** (l'actuel utilise `doAccept()`), avec stockage `localStorage`, sans dépendance. Hauteur réservée pour ne pas pousser le contenu (anti-CLS) ou positionné en overlay `fixed`.

---

## 2. Accueil (`front-page` / home)

Ordre vertical :
1. **Hero = article épinglé** en tête (image + titre + accroche). *(L'actuel a un flexslider optionnel → **supprimé**, remplacé par le post épinglé/mis en avant.)*
2. **Pub AdSense haut** — `slot 8689361629` (in-article fluid). **Conteneur à `min-height` réservé**.
3. **Liste d'articles** (1 colonne) : carte = miniature + `<h2>` titre lien + extrait (~40 mots) + lien « Plus d'informations ».
4. **Pub AdSense in-feed** après le 2ᵉ article — `slot 5396983870`. **Espace réservé**.
5. **Pagination** (« Articles anciens » / « Nouveaux articles »).
6. Sidebar 4/12 (voir §8) + footer.

- **Anti-CLS** : chaque miniature en taille fixe (`aspect-ratio`/`width`+`height`) ; les 2 emplacements pubs en conteneurs dimensionnés.
- ❌ Supprimés : flexslider, sections « featured categories » multiples paramétrables (simplifiées en un flux unique d'articles récents).

---

## 3. Article seul (`single.php`)

Ordre vertical :
1. **Fil d'Ariane** (breadcrumb) — conservé, balisage propre.
2. **`<h1>` titre**.
3. **Pub haut d'article** (`mts_posttop_adcode`) — conditionnelle par ancienneté ; **espace réservé**.
4. **Contenu** HTML5 sémantique, sans microdata dupliquée (schema JSON-LD délégué au plugin SEO).
5. **Pub bas d'article** (`mts_postend_adcode` / `slot 8908675069`, 680×260) — **espace réservé**.
6. **Tags**.
7. **Articles liés** (`mts_related_posts`) — grille de vignettes.
8. **Commentaires**.
9. Sidebar 4/12 + footer.

- **Anti-CLS** : images du contenu et vignettes liées dimensionnées ; emplacements pubs réservés.

---

## 4. Archive catégorie / tag (`archive.php`)

1. **`<h1>`** = champ custom **`titre-h1`** (term meta) si défini, sinon titre de catégorie/tag. *(seul champ custom conservé — cf. fichier 01)*
2. **Description** de catégorie/tag (`.cat-desc`).
3. **Pub AdSense haut** — `slot 8689361629`. **Espace réservé**.
4. **Liste d'articles** (même carte qu'accueil) + **pub in-feed** après le 2ᵉ (`slot 5396983870`).
5. **Pagination**.
6. Sidebar + footer.

---

## 5. Page standard (`page.php`)

- `<h1>` titre + contenu + (commentaires si activés).
- Sidebar selon réglage (`mts_custom_sidebar`).
- ❌ Templates `page-parallax.php` / `singlepost-parallax.php` **supprimés**.

---

## 6. Recherche (`search.php`)

- Titre « Résultats de recherche pour : … ».
- Liste de résultats (carte article) ou message « aucun résultat » + formulaire de recherche.
- Sidebar + footer.

---

## 7. Erreur 404 (`404.php`)

- `<h1>` 404, message FR, formulaire de recherche, éventuellement liens populaires.
- Header avec marque en `<h1>` (cf. §1.1).

---

## 8. Sidebar (`sidebar.php`)

- Colonne **4/12**, conditionnelle (peut être masquée → `mts_nosidebar`).
- Widgets par défaut : **Recherche**, **Archives mensuelles**, ~~Meta~~ *(le bloc « Meta » login/register est inutile en front public → **supprimé**)*.
- Widgets à conserver (depuis la liste actuelle) : à arbitrer dans le fichier `02` — pressentis : **articles récents**, **articles populaires**, **articles par catégorie**, **articles liés**, **recherche**. ❌ Abandonnés : Google+, Facebook Like Box, Tweets, bannières ad125/ad300.

---

## 9. Récapitulatif des emplacements publicitaires (AdSense en dur)

| Emplacement | Slot | Écrans | Réservation anti-CLS |
|---|---|---|---|
| Haut de liste | `8689361629` (in-article fluid) | Accueil, archives | `min-height` conteneur |
| In-feed (après 2ᵉ article) | `5396983870` (in-article fluid) | Accueil, archives | `min-height` conteneur |
| Bas d'article | `8908675069` (680×260) | Single | conteneur 680×260 (responsive : ratio) |
| Haut d'article / fin d'article | via `config.php` | Single | conditionnel par ancienneté + espace réservé |

> Publisher : `ca-pub-9582901796643932`. Script `adsbygoogle.js` en `async`. **Aucun décalage de mise en page toléré.**

---

## 10. Synthèse des suppressions visuelles

- ❌ Flexslider (accueil) → remplacé par post épinglé.
- ❌ Effets parallax (templates dédiés).
- ❌ prettyPhoto lightbox jQuery → lightbox vanilla **seulement si un écran le justifie**.
- ❌ Polices Google externes → stack système.
- ❌ Variantes de header multiples, sticky-nav optionnel.
- ❌ Widgets sociaux legacy + bannières pub widgetisées.
- ❌ Bloc « Meta » de la sidebar.

---

## Décisions confirmées

- Le menu principal est administré dans WordPress et rendu uniquement lorsqu'un menu est assigné.
- Les boutons de partage social sont supprimés.
- Le bloc d'articles liés est conservé en bas d'article.
- La sidebar 4/12 est affichée sur l'accueil.
