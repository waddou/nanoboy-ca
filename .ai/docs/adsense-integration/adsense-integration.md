# Intégration Google AdSense — méthode, règles & anti-CLS

> Statut : implémenté (juin 2026). Éditeur : `ca-pub-9582901796643932`.
> Fichiers concernés : `config.php`, `inc/ads.php`, `inc/enqueue.php`,
> `parts/ad-slot.php`, `src/css/4-components/ad-slot.css`,
> `src/css/1-settings/tokens.css`, templates (`sidebar.php`, `single.php`,
> `archive.php`, `index.php`, `front-page.php`).

## 1. Principe directeur : zéro CLS

Le CLS (Cumulative Layout Shift) survient quand un élément apparaît ou change
de taille **après** le premier rendu et pousse le contenu. Les pubs en sont la
cause n° 1. La parade unique du thème :

**Chaque créneau publicitaire a des dimensions figées en CSS AVANT la réponse
AdSense.** La hauteur ne dépend jamais de la pub servie :

- pub servie → elle remplit exactement l'espace réservé ;
- pub non servie (unfilled) → l'espace réservé reste visible (cadre
  « PUBLICITÉ ») mais **rien ne bouge**.

On n'utilise jamais `min-height` seul (une pub plus haute que le minimum
décalerait le contenu) : toujours une hauteur **fixe**.

## 2. Chargement du script (méthode choisie : un seul appel global)

`adsbygoogle.js` est chargé **une seule fois pour tout le site**, dans le
`<head>`, en `async` + `crossorigin="anonymous"`, via
`nanoboy_enqueue_adsense()` dans `inc/enqueue.php` :

```html
<script async crossorigin="anonymous"
  src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-9582901796643932"></script>
```

Pourquoi cette méthode plutôt qu'un script par pub :

- c'est la recommandation officielle Google (le script est unique, chaque
  `<ins>` ne fait qu'un `push({})` dans la file) ;
- un seul téléchargement, mis en cache, non bloquant (`async`) ;
- le `client=` dans l'URL permet à Google d'initialiser plus tôt.

Chaque emplacement émet ensuite son couple `<ins class="adsbygoogle">` +
`<script>(adsbygoogle = window.adsbygoogle || []).push({});</script>` —
**un seul `push()` par `<ins>`**, jamais plus (sinon erreur `TagError`).

## 3. Architecture (tout en dur, zéro panneau d'options)

```
config.php            → IDs : publisher + slots (la seule chose à éditer pour changer un ID)
inc/ads.php           → config de rendu par emplacement (format, layout, style, desktop_only)
parts/ad-slot.php     → fragment HTML unique (markup <ins> + push)
ad-slot.css           → dimensions réservées (anti-CLS) par variante .ad-slot--<location>
tokens.css            → tokens --nb-ad-* (hauteurs/largeurs réservées)
templates             → appellent nanoboy_ad_slot( '<location>' ) à l'endroit voulu
```

Ajouter / déplacer une pub = ajouter un slot dans `config.php`, une entrée
dans `inc/ads.php`, une variante CSS dimensionnée, et un appel
`nanoboy_ad_slot()` dans le template. Rien d'autre.

## 4. Les emplacements

| Emplacement    | Slot         | Où / position                                          | Code AdSense                          | Espace réservé                            | Mobile (< 64rem)            |
| -------------- | ------------ | ------------------------------------------------------ | ------------------------------------- | ----------------------------------------- | --------------------------- |
| `sidebar_top`  | `7716025455` | Sidebar, **1ᵉʳ widget en dur** (avant les widgets WP)  | Dimensionné en CSS (sans format)       | 250×250 (1024–1199px) ; 336×280 (≥1200px) | **Masqué + aucune requête** |
| `list_top`     | `8689361629` | Accueil + archives, **2ᵉ carte** de la grille          | In-article fluid, `height:280px` inline | 280px + cadre = 344px figés               | Affiché (280px figés)       |
| `list_in_feed` | `5396983870` | Accueil + archives, **4ᵉ carte** de la grille          | In-article fluid, `height:280px` inline | 280px + cadre = 344px figés               | Affiché (280px figés)       |
| `article_top`  | `9155485236` | Single, **dans `.entry__content`, avant le texte**     | Dimensionné en CSS (sans format)       | pleine largeur × 280px + cadre = 344px    | Affiché (280px figés)       |
| `article_end`  | `8908675069` | Single, après le contenu                               | Responsive auto + full-width           | ratio 680/260, max 680px                  | Affiché                     |

### 4.1 Sidebar (`sidebar_top`) — desktop uniquement

- Inséré **en dur** dans `sidebar.php`, toujours premier élément de
  `<aside class="sidebar">`, avant `dynamic_sidebar()` : il ne peut pas être
  supprimé/déplacé depuis l'admin.
- **Sans marge ni padding** (`margin:0; padding:0`, pas de cadre ni de label) :
  la pub est flush en haut de la colonne ; le rythme avec les widgets suivants
  vient du `gap` du flex de `.sidebar`.
- La colonne 4/12 fait ~289px de large à 1024px et ~347px à 1200px. La taille
  servie suit donc deux paliers (tailles standards à fort taux de remplissage) :
  - `≥ 64rem` : **250×250** (carré) ;
  - `≥ 75rem` : **336×280** (grand rectangle).
- **Mobile/tablette (< 64rem) : la pub n'existe pas.** Double verrou :
  1. CSS `display:none` (autorisé par Google pour masquer une annonce par
     media query) ;
  2. le `push()` est conditionné par
     `window.matchMedia('(min-width: 64rem)').matches` → **aucune requête
     publicitaire n'est envoyée sur mobile** (pas d'impression fantôme, pas
     d'erreur console `availableWidth=0`).

### 4.2 Listes accueil/archives (`list_top`, `list_in_feed`)

- La grille `.grid-cards` est en 1 colonne : les pubs sont insérées comme des
  cartes, après la 1ʳᵉ et la 2ᵉ carte → elles occupent les **positions 2 et
  4** de la liste (carte, pub, carte, pub, carte…).
- Code **in-article fluid** avec hauteur figée **inline**
  (`style="display:block;text-align:center;height:280px"`) : le format fluid
  adapte sa créative à la hauteur imposée — c'est le réglage déjà éprouvé en
  production sur l'ancien thème.
- Le conteneur `.ad-slot--list-top/--list-in-feed` est lui-même figé à
  `280px + 48px (bande « PUBLICITÉ ») + 16px = 344px`, margin 0 (le rythme
  vertical vient du `gap` de la grille, comme entre deux cartes).

### 4.3 Haut d'article (`article_top`)

- Rendu **premier enfant de `.entry__content`**, avant `the_content()` :
  littéralement « avant le début du texte », dans le cadre blanc du contenu.
- Pleine largeur du contenu (`inline-size:100%`), hauteur figée à **280px**
  via CSS (le conteneur fait 344px avec la bande et le retrait bas).
- L'`<ins>` n'a pas de `data-ad-format` : AdSense lit la taille calculée
  (~678×280 sur desktop, ~300×280 sur mobile) et sert la créative standard la
  plus grande qui rentre, centrée. Hauteur garantie constante → zéro CLS.
- L'espacement sous la pub est géré par le flux du contenu
  (`.entry__content > * + *`), pas par une marge propre.

## 5. Règles Google appliquées (à ne pas casser)

1. **Un seul `adsbygoogle.js` par page**, `async` + `crossorigin="anonymous"`.
2. **Un seul `push({})` par `<ins>`** ; le `<ins>` doit être visible dans le
   DOM au moment du push (sauf garde `matchMedia`, cf. sidebar).
3. **Taille fixée en CSS ⇒ retirer `data-ad-format` et
   `data-full-width-responsive`** : c'est la méthode officielle « modifier le
   code d'annonce responsive » (support Google). Garder `data-ad-format="auto"`
   avec une hauteur verrouillée provoquerait des créatives tronquées
   (violation de règlement) ou des décalages.
4. **Masquer une annonce par media query (`display:none`) est autorisé** ;
   on y ajoute la garde `matchMedia` pour éviter la requête inutile.
5. **Ne jamais rogner une annonce** (`overflow` qui coupe une créative servie
   plus grande que le cadre) — d'où des tailles demandées = tailles réservées.
6. Les conteneurs portent un label « PUBLICITÉ » (bande au-dessus du créneau,
   jamais superposée à l'annonce) — conforme et plus clair pour le lecteur.
7. Pas plus de pubs que de contenu sur une page (équilibre actuel : 2 pubs
   pour ~10 cartes en liste, 2 pubs par article + sidebar desktop).

## 6. Pourquoi ça ne bouge pas (récap anti-CLS)

| Créneau        | Ce qui est figé                              | Quand                       |
| -------------- | --------------------------------------------- | --------------------------- |
| `sidebar_top`  | `block-size` 250px / 280px (paliers media)    | Au premier rendu CSS        |
| `list_*`       | `block-size: 344px` conteneur + `height:280px` inline sur l'`<ins>` | Au premier rendu CSS / HTML |
| `article_top`  | `block-size: 344px` conteneur + 280px l'`<ins>` | Au premier rendu CSS        |
| `article_end`  | `aspect-ratio: 680/260`, max 680px            | Au premier rendu CSS        |

Les hauteurs vivent dans `tokens.css` : `--nb-ad-height` (280px),
`--nb-ad-sidebar-sm` (250px), `--nb-ad-sidebar-width/height` (336/280px).

## 7. Checklist mise en production

- [ ] **`ads.txt`** à la racine du domaine avec la ligne :
      `google.com, pub-9582901796643932, DIRECT, f08c47fec0942fa0`
- [ ] Le site (domaine final) est **approuvé** dans le compte AdSense.
- [ ] Les 5 slots existent bien dans le compte (7716025455, 8689361629,
      5396983870, 9155485236, 8908675069).
- [ ] Consentement RGPD : activer le message CMP de Google (AdSense → message
      de consentement) ou brancher une CMP certifiée — obligatoire en UE.
- [ ] Après mise en ligne, vérifier sur une page réelle :
      - `document.querySelectorAll('ins.adsbygoogle')` → chaque `<ins>` passe
        à `data-ad-status="filled"` (ou `unfilled`, acceptable au début) ;
      - aucune erreur `adsbygoogle.push() error` en console ;
      - sur mobile (< 1024px), l'`<ins>` de la sidebar reste sans
        `data-ad-status` (aucune requête) ;
      - PageSpeed Insights / Search Console : **CLS ≈ 0** sur mobile et desktop.
- [ ] Le remplissage peut prendre quelques heures/jours après la mise en
      ligne d'un nouveau domaine : des cadres « PUBLICITÉ » vides au début
      sont normaux (et ne provoquent aucun décalage).

## 8. Comportements attendus / limites connues

- **En local (`choix-assurances.local`), les pubs ne se remplissent jamais**
  (domaine non approuvé) : on ne peut vérifier que le markup, les dimensions
  réservées et l'absence d'erreurs JS.
- Si AdSense ne sert rien (`unfilled`), le cadre réservé reste affiché. C'est
  un choix délibéré : la stabilité visuelle prime sur la récupération de
  l'espace (recollapser le créneau créerait exactement le CLS qu'on combat).
- La pub sidebar choisit sa taille au chargement (250×250 ou 336×280 selon la
  fenêtre) ; un redimensionnement de fenêtre ne redemande pas d'annonce —
  comportement standard AdSense, sans impact CLS au chargement.
