# Correction CLS — Choix Assurances — 8 septembre 2026

## Livrable

Branche `fix/cls-ad-reservations`, thème `nanoboy-ca`.
ZIP installable : `dist/nanoboy-ca-cls.zip` (généré, non versionné).
La production n'a pas été modifiée par les outils de cette tâche.

## Causes reproduites

- Accueil mobile : les annonces fluid font passer leur conteneur de 280 à 250 puis 0 px. AdSense ajoute aux ancêtres `height: auto !important` puis, si nécessaire, `min-height: 0px !important`.
- Sidebar : deux widgets Texte WordPress contiennent des annonces sans réservation ; les unités passent de 0 à 600 px, puis certaines se vident. Les widgets suivants bougent.
- Article mobile : lorsque l'unité devient vide (0×0), le centrage dans la grille déplace son iframe de 140 px verticalement et, selon le CSS, de 106 px horizontalement.
- Le thème utilise une pile de polices système. Pas de correction de polices nécessaire.

## Changement livré

La première piste de la grille publicitaire réserve la hauteur avant le chargement. Elle résiste aux modifications `height` et `min-height` observées, sans écrire sur les annonces. Les unités du contenu restent ancrées en haut à gauche ; les rectangles de sidebar restent centrés horizontalement.

Les conteneurs Texte/HTML de sidebar qui contiennent directement une unité AdSense réservent 600 px, plus les espacements déjà existants du widget. Ce minimum laisse grandir un contenu plus haut. Les widgets ordinaires ne sont pas concernés. Le token est déclaré dans `tokens.css` et `theme.json`.

Conséquence voulue : une annonce vide conserve son espace. Deux widgets publicitaires vides peuvent donc laisser deux grandes zones blanches dans la sidebar. Retirer un widget inutile depuis WordPress est une autre décision éditoriale ; aucun widget n'a été supprimé ici.

Les identifiants, formats publicitaires et scripts sont conservés. Aucune modification de configuration AdSense, de PHP, de JavaScript ou de polices.

## Validation

Tests sur les réponses de production, avec substitution du seul fichier CSS dans un contexte Playwright neuf. Aucun WordPress local utilisé.

| Page | Avant mobile | Avant ordinateur | Après mobile | Après ordinateur |
|---|---:|---:|---:|---:|
| Accueil | 0,1428 | 0,1353 | 0 | 0 |
| Article « durée de validité attestation de droits » | 0,0285 | 0,0902 | 0 à 0,0905 selon chargement | 0 |

Les mutations publicitaires ciblées ne déplacent plus le contenu. Sur un chargement mobile corrigé, un décalage tardif supplémentaire de 0,0905 a impliqué le header, le contenu et l'interface d'une publicité automatique. Les deux chargements instrumentés suivants étaient à 0. Le mécanisme exact de cet événement intermittent n'a pas été reproduit : ne pas annoncer un CLS global nul ni attribuer ce résultat aux polices.

Ces mesures concernent les premières secondes de chargement, sans défilement ni interaction, et ne remplacent pas les données terrain. Les publicités automatiques ailleurs dans la page restent variables.

Le test de régression `scripts/check-cls.playwright.js` échouait avant correction. Il passe après correction : 106 contrôles, 8 cas (375, 768, 1024, 1440 px × accueil/article). Il utilise le HTML réel, bloque AdSense uniquement dans le test, puis rejoue les mutations de taille constatées. Il couvre les deux emplacements de liste, l'article, les widgets de sidebar visibles, le centrage et le débordement horizontal.

Pour exécuter ce callback avec `browser_run_code`, remplacer `/* INLINE_THEME_CSS */ ""` par `JSON.stringify(contenu de assets/css/app.min.css)` après `npm ci --ignore-scripts` et `npm run build`. Le callback n'a pas besoin de fichier servi sur localhost.

Build réussi ; `git diff --check` réussi ; revue indépendante du code sans défaut bloquant. Les bundles sont générés et non commités, conformément aux conventions du dépôt.

## Installation

1. Sauvegarder le thème actuellement installé sur le serveur.
2. Dans WordPress : Apparence → Thèmes → Ajouter → Téléverser, choisir `dist/nanoboy-ca-cls.zip`, puis remplacer la version installée du thème.
3. Purger le cache du site et du CDN éventuel.
4. Refaire un contrôle sur l'accueil et un article, mobile et ordinateur.

Le paquet contient le thème complet et les bundles compilés. Il ne contient ni sources de développement, ni dépendances npm, ni rapport de test.

## Publicité automatique intermittente : diagnostic côté AdSense

Si un saut persiste après installation, tester temporairement sans annonces ancrées dans AdSense → Annonces → modifier choix-assurances.fr → Formats en superposition. Retester plusieurs chargements. Si cela supprime le saut, ajuster ce format ; sinon rétablir le réglage initial et capturer un nouveau relevé. Cette expérience n'a pas été faite sur le compte AdSense et n'est pas une correction garantie.

Google permet aussi de choisir la position « En bas uniquement » et de désactiver les annonces ancrées dynamiques dans les paramètres avancés : [réglages officiels](https://support.google.com/adsense/answer/9305577?hl=fr). Ne pas modifier tous les formats en même temps, pour conserver un diagnostic interprétable.

## Retour arrière

Réinstaller la sauvegarde du thème, puis purger les caches. Aucun changement de base de données n'est requis par cette correction.
