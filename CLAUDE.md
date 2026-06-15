# Thème WordPress « NanoBoy » — Instructions pour Claude Code

## Contexte

Thème WordPress **classique modernisé** conçu pour le média d'information `choix-assurances.fr`.
Identité visuelle B2B moderne noir + bleu `#1e6091`, fond quadrillé et surfaces claires.

Documentation complète dans `.ai/` :
- Vision & décisions : `.ai/docs/refactoring/01-vision-objectifs.md`
- Arborescence & build : `.ai/docs/refactoring/02-arborescence-cible.md`
- Inventaire visuel : `.ai/docs/refactoring/03-inventaire-visuel.md`
- Architecture : `.ai/docs/architecture.md`
- Conventions : `.ai/docs/conventions.md`
- **Plan d'exécution : `.ai/docs/roadmap.md`** ← suivre cet ordre

## Règles importantes (non négociables)

- Préfixes : fonctions `nanoboy_…`, constantes `NANOBOY_…`, handles `nanoboy-…`, text domain `nanoboy`.
- CSS en **BEM** + tokens `--nb-…`. Fichiers en `kebab-case`.
- PHP **8.2+** : `declare(strict_types=1)` + typage. Garde `ABSPATH` en tête des includes.
- **Sécurité** : tout echo échappé (`esc_*` / `wp_kses_post`), entrées sanitizées, nonces sur formulaires/AJAX.
- **Anti-CLS** : dimensions réservées pour images, pubs et header. Zéro décalage toléré.
- **0** jQuery, **0** WooCommerce, **0** `options/` (SMOF), **0** appel réseau distant, **0** fichier orphelin.
- Réglages **en dur** dans `config.php` — jamais de panneau d'options.
- `functions.php` = bootstrap uniquement (`require inc/*`). Toute la logique va dans `inc/`.

## Stack

- PHP 8.2+ (WordPress 7.0), thème classique + `theme.json` (tokens).
- **CSS natif moderne** (`@layer`, nesting, custom properties) — pas de SCSS.
- **JavaScript vanilla** (modules ES) — pas de jQuery.
- Build : **esbuild** → `assets/css/app.min.css` + `assets/js/app.min.js` (2 assets en prod).
- i18n : gettext, source EN + traduction FR.

## Tests locaux

- URL du site pour les tests Codex et Claude Code : `http://choix-assurances.local/`.

## Skills disponibles

Les skills Claude Code pour ce projet sont dans `.ai/claude/skills/`.
