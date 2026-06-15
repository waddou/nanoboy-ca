# AI Assistant Context — Thème « NanoBoy »

Dossier de référence pour Claude Code et tout assistant IA travaillant sur le thème **NanoBoy**,
conçu pour le média professionnel **Compte-Pro.info** sur une base de code moderne, rapide,
sécurisée et standard.

> **Identité :** noir + bleu `#1e6091`, fond quadrillé, surfaces claires et wordmark texte.
> **Dernière révision :** 2026-06-13

## Environnements

| Environnement | URL |
|---|---|
| **Local** (dev / tests Claude Code & Codex) | http://choix-assurances.local/ |
| **Production** | https://choix-assurances.fr/ |

> Utiliser `http://choix-assurances.local/` pour tous les tests locaux.

## Contenu

| Chemin | Description |
|---|---|
| `docs/refactoring/01-vision-objectifs.md` | Vision, principes, périmètre, décisions d'architecture (source de vérité) |
| `docs/refactoring/02-arborescence-cible.md` | Arborescence cible, mapping ancien→nouveau, chaîne de build |
| `docs/refactoring/03-inventaire-visuel.md` | Inventaire écran par écran, tokens visuels, emplacements pubs |
| `docs/architecture.md` | Vue d'ensemble technique de la cible (bootstrap, modules, build, chargement prod) |
| `docs/conventions.md` | Conventions de code : nommage PHP/CSS/JS, BEM, tokens, i18n, sécurité |
| `docs/roadmap.md` | **Plan d'exécution étape par étape** — l'ordre dans lequel construire le thème |
| `claude/CLAUDE.md` | Instructions projet (copie de référence du `CLAUDE.md` racine) |
| `claude/skills/` | Skills Claude Code spécifiques au projet |

## Ordre de lecture recommandé

1. `docs/refactoring/01-vision-objectifs.md` — *pourquoi* et *quoi*.
2. `docs/refactoring/02-arborescence-cible.md` — *où* va chaque chose.
3. `docs/refactoring/03-inventaire-visuel.md` — *à quoi* ça doit ressembler.
4. `docs/architecture.md` + `docs/conventions.md` — *comment* on code.
5. `docs/roadmap.md` — *dans quel ordre* on exécute.

## Principes non négociables

- **0** jQuery, **0** WooCommerce, **0** SMOF/`options/`, **0** appel réseau distant, **0** fichier orphelin.
- **2 assets** chargés en prod : `assets/css/app.min.css` + `assets/js/app.min.js`.
- Sorties **échappées**, entrées **sanitizées**, formulaires/AJAX protégés par **nonces**.
- PHP **8.2+** typé (`declare(strict_types=1)`), HTML5 sémantique **WCAG 2.2 AA**.
- Préfixes : fonctions `nanoboy_…`, constantes `NANOBOY_…`, text domain `nanoboy`, CSS tokens `--nb-…` (BEM).
- **Anti-CLS impératif** : toutes les dimensions connues à l'avance (images, pubs, header).
