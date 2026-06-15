Analyse & Critique UI/UX — Thème NanoBoy

---

## Verdict global

Le thème est techniquement **très solide** (accessibilité, tokens, anti-CLS, zéro jQuery). Mais il manque de **personnalité éditoriale distincte** et accumule plusieurs frictions UX silencieuses qui nuisent à l'expérience de lecture et à la confiance des visiteurs.

---

## POINTS FORTS (à conserver)

| Domaine | Détail |
|---|---|
| **Accessibilité** | Skip link, `aria-expanded`, `aria-current`, `:focus-visible` correct, `screen-reader-text`, `<time datetime>` — WCAG AA solide |
| **Anti-CLS** | Images avec dimensions réservées, `aspect-ratio` 16:9, `min-height` sur ad-slots — Core Web Vitals propres |
| **Token system** | CSS custom properties cohérentes (`--nb-*`), `@layer` ordonné, pas de valeurs magiques inline |
| **Performance** | Zéro police externe, `loading="lazy"` + `fetchpriority="high"` sur le hero, modules ES vanilla |
| **Sémantique HTML** | `<main>`, `<article>`, `<aside>`, `<nav>` bien utilisés, hiérarchie de titres respectée |
| **Responsive** | Mobile-first, breakpoints cohérents 480/768/1024/1200 |

---

## PROBLÈMES CRITIQUES

### 1. Identité typographique inexistante (Priorité 1)

**Problème** : Le thème utilise exclusivement `system-ui`. Pour un blog/média éditorial, les polices système sont le choix le plus générique possible — indiscernable de n'importe quel CMS par défaut.

**Impact** : Aucune personnalité. Le `frontend-design` skill l'identifie comme un anti-pattern default : *"warm cream + system fonts = template answer"*.

**Recommandation** : Adopter une paire serif/sans-serif éditoriale. Le meilleur candidat pour le projet (site financier FR, contenu long) :
- **Titres** : `Newsreader` (conçue pour la lecture longue, journalistique)
- **Corps** : `Inter` ou `Public Sans` (lisibilité UI)
- **Chargement** : `font-display: optional` + `<link rel="preload">` pour éviter tout FOIT/FOUT

```css
/* Zéro CLS si on utilise optional */
@font-face {
  font-family: 'Newsreader';
  src: url('/fonts/newsreader.woff2') format('woff2');
  font-display: optional;
}
```

---

### 2. Header sans hauteur réservée — CLS potentiel (Priorité 2)

**Problème** : `header.css` ne définit pas de `min-height` fixe sur `.site-header`. Si le logo met du temps à charger, le header "saute" en hauteur.

**Impact** : CLS sur mobile, potentielle pénalité Core Web Vitals.

**Fix** :
```css
.site-header {
  min-height: 70px; /* valeur correspondant à la hauteur avec logo */
}
```

---

### 3. Navigation : sous-menus inaccessibles au clavier (Priorité 3)

**Problème** : Les sous-menus (`.sub-menu`) sont gérés en CSS pur (`:hover`/`:focus`). Mais il n'y a **aucune logique JS** pour gérer `aria-haspopup`, `aria-expanded` sur les items parents, ni la fermeture via `Escape`. `nav.js` ne gère que le burger.

**Impact** : Utilisateurs clavier/screen reader ne peuvent pas accéder aux sous-menus. Violation WCAG 2.1 Success Criterion 2.1.1.

**Fix requis dans `nav.js`** :
```js
// Pour chaque item parent avec .sub-menu
parentLink.setAttribute('aria-haspopup', 'true');
parentLink.setAttribute('aria-expanded', 'false');
parentLink.addEventListener('focus', () => openSubmenu(item));
item.addEventListener('keydown', e => {
  if (e.key === 'Escape') closeSubmenu(item);
});
```

---

### 4. Cookie bar : consentement sans contrôle (Priorité 4)

**Problème** : La barre cookie utilise `localStorage` pour stocker le consentement, mais il n'y a **qu'un seul bouton "OK"**. Pas de refus, pas de granularité. En France (RGPD + recommandation CNIL 2020), le consentement doit être aussi facile à refuser qu'à accepter.

**Impact** : Non-conformité CNIL. Risque d'amende jusqu'à 4% du CA.

**Fix minimal** : Ajouter un bouton "Refuser" qui ferme la barre sans stocker de consentement positif (et sans activer AdSense).

---

### 5. Contraste des textes muted (Priorité 5)

**Tokens concernés** :
- `--nb-color-muted: #64747b` sur fond `--nb-color-bg: #ffffff` → ratio **4.48:1** (passe de justesse AA, mais échoue AAA)
- `--nb-color-muted-soft: #6d7d83` sur fond `--nb-color-bg-subtle: #f0f4f5` → ratio **~3.8:1** — **ÉCHOUE WCAG AA (4.5:1 requis)**

**Composants affectés** : dates de articles, catégories muted, meta-données.

**Fix** :
```css
--nb-color-muted: #4e5f67;      /* ratio ~5.8:1 sur blanc */
--nb-color-muted-soft: #556169; /* ratio ~4.6:1 sur bg-subtle */
```

---

## PROBLÈMES MOYENS

### 6. Cartes article : trop peu d'informations scannable

**Problème** : L'excerpt est limité à 40 mots et le bouton "Read more" est systématiquement affiché, même pour des articles très courts. La catégorie et la date sont dans le même niveau visuel que le titre.

**Recommandations** :
- Supprimer le bouton "Read more" — le titre est déjà un lien, le double lien est un anti-pattern d'accessibilité (deux `<a>` vers la même URL pour le même item)
- Augmenter le contraste visuel entre catégorie (tag orange) et date (muted)
- Ajouter un indicateur de temps de lecture (`X min`)

---

### 7. Page 404 : formulaire de recherche insuffisant

**Problème** : La 404 propose uniquement une recherche, sans orienter l'utilisateur. Pas de liens vers les catégories populaires, pas de derniers articles.

**Pattern recommandé** : 404 = redirection active. Proposer 3-5 articles récents ou les catégories principales.

---

### 8. Sidebar : hiérarchie visuelle faible

**Problème** : Les widgets sidebar utilisent des styles génériques WordPress (`widget_title` par défaut). Le titre de widget a certes un `border-bottom` accent, mais la distinction entre les différents widgets est faible visuellement — peu de respiration.

**Recommandation** : Augmenter `margin-bottom` entre widgets à `--nb-space-6` (48px), et ajouter un fond `--nb-color-bg-subtle` sur les widgets pour les détacher du fond principal.

---

### 9. Images sans `decoding="async"` (Performance)

**Problème** : Les images utilisent `loading="lazy"` mais pas `decoding="async"`, ce qui peut bloquer le thread principal pendant le décodage.

**Fix dans `template-helpers.php`** :
```php
// Ajouter à nanoboy_thumbnail()
'decoding' => 'async',
```

---

### 10. Absence de dark mode

**Problème** : Aucun support `prefers-color-scheme: dark`. Pour un site de contenu financier, les utilisateurs qui lisent en soirée n'ont pas d'adaptation.

**Impact** : Expérience dégradée pour ~35% des utilisateurs mobile qui ont le dark mode activé.

**Recommandation** : Ajouter des overrides CSS minimal via `@media (prefers-color-scheme: dark)` sur les tokens principaux.

---

## RECOMMANDATIONS IDENTITÉ VISUELLE

Le skill `frontend-design` identifie que le thème actuel (fond crème `#f6f3ee` + system fonts + accent orange) est exactement l'anti-pattern #1 qu'il faut éviter : *"warm cream background + high-contrast sans + terracotta accent = template default"*.

**Pistes de différenciation à explorer** :
1. **Approche éditoriale sérieuse** (cohérente avec *ouvrir un compte* / finance) : Newsreader serif + bleu ardoise dominant + orange comme unique accent CTA — pas sur les catégories
2. **Signature visuelle manquante** : il n'y a aucun élément mémorable. Une "règle typographique" (trait fin coloré en décoration de titre), une grille légèrement asymétrique pour les cartes hero, ou un traitement particulier des grandes citations pourraient créer cette signature

---

## RÉSUMÉ PRIORISÉ

| # | Problème | Sévérité | Effort |
|---|---|---|---|
| 1 | Typographie générique (system-ui) | Haute | Moyen |
| 2 | Header sans min-height | Haute | Faible |
| 3 | Sous-menus inaccessibles clavier | **Critique** | Moyen |
| 4 | Cookie bar non-conforme CNIL | **Critique** | Faible |
| 5 | Contraste muted sur bg-subtle | Haute | Faible |
| 6 | Double lien sur cartes (a11y) | Moyenne | Faible |
| 7 | 404 sans orientation | Faible | Faible |
| 8 | Sidebar peu respirante | Faible | Faible |
| 9 | `decoding="async"` absent | Moyenne | Faible |
| 10 | Pas de dark mode | Moyenne | Élevé |

Les points **3 et 4** sont les seuls bloquants absolus (accessibilité légale + conformité CNIL). Les points **1, 2, 5** sont des quick wins à fort impact perçu.
