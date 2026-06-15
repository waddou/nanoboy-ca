'use strict';

const { WP_Pot } = require('wp-pot');

const pot = new WP_Pot({
  pot: {
    domain: 'nanoboy',
    package: 'NanoBoy',
    bugReport: '',
    lastTranslator: '',
    team: '',
  },
});

pot
  .parse([
    '**/*.php',
    '!node_modules/**',
    '!.ai/**',
    '!assets/**',
    '!dist/**',
    '!scripts/**',
  ])
  .writePot('languages/nanoboy.pot');

console.log('Generated languages/nanoboy.pot');
