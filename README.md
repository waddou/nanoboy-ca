# NanoBoy CA

NanoBoy CA est un thème WordPress classique conçu pour `choix-assurances.fr`. Il vise
une base légère et maintenable : PHP 8.2+, CSS natif moderne, JavaScript vanilla,
aucune dépendance à jQuery et SEO délégué à Yoast SEO ou Rank Math.

## Prérequis

- WordPress 7.0+
- PHP 8.2+
- Node.js 22+ et npm
- WP-CLI pour régénérer le catalogue POT

## Développement

```bash
npm install
npm run dev
```

`npm run dev` surveille les sources de `src/` et reconstruit les bundles dans
`assets/css/app.min.css` et `assets/js/app.min.js`.

## Build de production

```bash
npm ci
npm run build
```

Les deux bundles minifiés sont générés au déploiement et ne sont pas versionnés.
Le thème les charge avec une version calculée par `filemtime()`.

## Paquet de production

```bash
npm run package
```

Cette commande reconstruit les bundles puis recrée `dist/nanoboy-ca/` avec
uniquement les fichiers nécessaires au thème en production. Envoyer le contenu
de ce dossier vers `wp-content/themes/nanoboy-ca/` avec FileZilla.

## Traductions

Toutes les chaînes sources sont écrites en anglais avec le text domain
`nanoboy`. Pour régénérer le modèle de traduction :

```bash
npm run i18n
```

Les catalogues maintenus sont :

- `languages/nanoboy.pot`
- `languages/fr_FR.po` et `languages/fr_FR.mo`
- `languages/en_US.po` et `languages/en_US.mo`

## Configuration

Les réglages statiques sont centralisés dans `config.php` : identité de marque,
largeur de contenu, identifiants Analytics/AdSense, emplacements publicitaires et liens
sociaux. Aucun panneau d’options n’est fourni.

## Déploiement

Le paquet livré doit contenir le code PHP, `style.css`, `theme.json`, les
fichiers de `assets/` générés et les catalogues `.mo`. Les sources et outils de
développement (`src/`, `.ai/`, `node_modules/`, `package.json`, fichiers `.po`
et ce README) peuvent être exclus du paquet de production.
