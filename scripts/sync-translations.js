'use strict';

/**
 * Synchronise les fichiers de traduction.
 *
 * Étapes (à exécuter après `make-pot.js`) :
 *   1. Fusionne `languages/nanoboy.pot` dans chaque `languages/*.po`
 *      (ajoute les nouvelles chaînes, conserve les traductions existantes,
 *       supprime les chaînes obsolètes).
 *   2. Compile chaque `*.po` en `*.mo` — le SEUL format lu par WordPress.
 *
 * Sans cette étape, une chaîne ajoutée au code reste « non traduite » même
 * après `npm run i18n`, car le `.pot` seul n'est jamais chargé par WordPress.
 */

const fs = require('fs');
const path = require('path');

const LANG_DIR = path.join(__dirname, '..', 'languages');
const POT_PATH = path.join(LANG_DIR, 'nanoboy.pot');

main().catch((err) => {
  console.error(err);
  process.exit(1);
});

async function main() {
  // gettext-parser v9 est livré en ESM uniquement → import() dynamique.
  const gettextParser = (await import('gettext-parser')).default;

  const pot = gettextParser.po.parse(fs.readFileSync(POT_PATH));
  const potContexts = pot.translations;

  const poFiles = fs
    .readdirSync(LANG_DIR)
    .filter((file) => file.endsWith('.po'));

  for (const file of poFiles) {
    const poPath = path.join(LANG_DIR, file);
    const po = gettextParser.po.parse(fs.readFileSync(poPath));
    const merged = { charset: po.charset, headers: po.headers, translations: {} };

    let added = 0;
    for (const [context, entries] of Object.entries(potContexts)) {
      merged.translations[context] = {};
      for (const [msgid, potEntry] of Object.entries(entries)) {
        const existing = po.translations[context] && po.translations[context][msgid];
        if (existing) {
          // Conserve la traduction, mais reprend références/commentaires du .pot.
          merged.translations[context][msgid] = {
            ...existing,
            comments: potEntry.comments,
          };
        } else {
          // Nouvelle chaîne : msgstr vide (à traduire), structure héritée du .pot.
          merged.translations[context][msgid] = potEntry;
          if (msgid !== '') added += 1;
        }
      }
    }

    fs.writeFileSync(poPath, gettextParser.po.compile(merged));

    const moPath = poPath.replace(/\.po$/, '.mo');
    fs.writeFileSync(moPath, gettextParser.mo.compile(merged));

    const untranslated = countUntranslated(merged);
    console.log(
      `${file} → ${path.basename(moPath)}` +
        ` (+${added} nouvelle(s), ${untranslated} non traduite(s))`
    );
  }
}

function countUntranslated(data) {
  let count = 0;
  for (const entries of Object.values(data.translations)) {
    for (const [msgid, entry] of Object.entries(entries)) {
      if (msgid === '') continue;
      if (!entry.msgstr || entry.msgstr.every((s) => s === '')) count += 1;
    }
  }
  return count;
}
