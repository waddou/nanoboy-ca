'use strict';

const fs = require('fs');
const path = require('path');

const ROOT = path.resolve(__dirname, '..');
const THEME_SLUG = 'nanoboy-ca';
const DIST = path.join(ROOT, 'dist');
const OUTPUT = path.join(DIST, THEME_SLUG);

const ROOT_FILES = [
  'style.css',
  'theme.json',
  'screenshot.png',
];

const RUNTIME_DIRS = [
  'inc',
  'parts',
];

const REQUIRED_FILES = [
  'style.css',
  'theme.json',
  'functions.php',
  'assets/css/app.min.css',
  'assets/js/app.min.js',
];

main();

function main() {
  for (const relativePath of REQUIRED_FILES) {
    assertFile(relativePath);
  }

  fs.rmSync(DIST, { force: true, recursive: true });
  fs.mkdirSync(OUTPUT, { recursive: true });

  copyRootPhpFiles();

  for (const relativePath of ROOT_FILES) {
    copyFile(relativePath);
  }

  for (const relativePath of RUNTIME_DIRS) {
    copyDirectory(relativePath);
  }

  copyDirectory('assets', isProductionAsset);
  copyLanguages();

  const files = listFiles(OUTPUT);
  const totalBytes = files.reduce((sum, file) => sum + fs.statSync(file).size, 0);

  console.log(
    `Production package created: ${path.relative(ROOT, OUTPUT)} ` +
      `(${files.length} files, ${formatBytes(totalBytes)})`
  );
}

function assertFile(relativePath) {
  const source = path.join(ROOT, relativePath);

  if (!fs.statSync(source, { throwIfNoEntry: false })?.isFile()) {
    throw new Error(`Required production file is missing: ${relativePath}`);
  }
}

function copyRootPhpFiles() {
  const phpFiles = fs
    .readdirSync(ROOT, { withFileTypes: true })
    .filter((entry) => entry.isFile() && entry.name.endsWith('.php'))
    .map((entry) => entry.name);

  for (const relativePath of phpFiles) {
    copyFile(relativePath);
  }
}

function copyLanguages() {
  const languageDir = path.join(ROOT, 'languages');

  if (!fs.statSync(languageDir, { throwIfNoEntry: false })?.isDirectory()) {
    return;
  }

  for (const entry of fs.readdirSync(languageDir, { withFileTypes: true })) {
    if (entry.isFile() && entry.name.endsWith('.mo')) {
      copyFile(path.join('languages', entry.name));
    }
  }
}

function copyDirectory(relativePath, include = () => true) {
  const source = path.join(ROOT, relativePath);

  if (!fs.statSync(source, { throwIfNoEntry: false })?.isDirectory()) {
    throw new Error(`Required production directory is missing: ${relativePath}`);
  }

  for (const entry of fs.readdirSync(source, { withFileTypes: true })) {
    const childPath = path.join(relativePath, entry.name);

    if (entry.isDirectory()) {
      copyDirectory(childPath, include);
    } else if (entry.isFile() && include(childPath)) {
      copyFile(childPath);
    }
  }
}

function copyFile(relativePath) {
  const source = path.join(ROOT, relativePath);
  const destination = path.join(OUTPUT, relativePath);

  assertFile(relativePath);
  fs.mkdirSync(path.dirname(destination), { recursive: true });
  fs.copyFileSync(source, destination);
}

function isProductionAsset(relativePath) {
  const filename = path.basename(relativePath);

  return filename !== '.gitkeep' && !filename.endsWith('.map');
}

function listFiles(directory) {
  return fs.readdirSync(directory, { withFileTypes: true }).flatMap((entry) => {
    const childPath = path.join(directory, entry.name);

    return entry.isDirectory() ? listFiles(childPath) : [childPath];
  });
}

function formatBytes(bytes) {
  if (bytes < 1024) {
    return `${bytes} B`;
  }

  return `${(bytes / 1024).toFixed(1)} KiB`;
}
