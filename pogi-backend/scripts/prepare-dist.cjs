const fs = require('fs');
const path = require('path');

const backendDir = path.resolve(__dirname, '..');
const rootDir = path.resolve(backendDir, '..');
const prototypeDir = path.join(rootDir, 'prototype');
const distDir = path.join(backendDir, 'dist');
const publicDir = path.join(backendDir, 'public');

console.log('[prepare-dist] Preparing dist directory for Vercel deployment...');

// Ensure dist directory exists and is clean
if (fs.existsSync(distDir)) {
  fs.rmSync(distDir, { recursive: true, force: true });
}
fs.mkdirSync(distDir, { recursive: true });

// Helper to copy recursively
function copyDirSync(src, dest) {
  if (!fs.existsSync(src)) return;
  fs.mkdirSync(dest, { recursive: true });
  const entries = fs.readdirSync(src, { withFileTypes: true });
  for (const entry of entries) {
    const srcPath = path.join(src, entry.name);
    const destPath = path.join(dest, entry.name);
    if (entry.isDirectory()) {
      copyDirSync(srcPath, destPath);
    } else {
      fs.copyFileSync(srcPath, destPath);
    }
  }
}

// 1. Copy public assets (css, js, assets, build) to dist
if (fs.existsSync(publicDir)) {
  const publicEntries = fs.readdirSync(publicDir, { withFileTypes: true });
  for (const entry of publicEntries) {
    if (entry.name === 'index.php' || entry.name === '.htaccess') continue;
    const srcPath = path.join(publicDir, entry.name);
    const destPath = path.join(distDir, entry.name);
    if (entry.isDirectory()) {
      copyDirSync(srcPath, destPath);
    } else {
      fs.copyFileSync(srcPath, destPath);
    }
  }
}

// 2. Also ensure prototype css/js/assets are synced to dist if present
if (fs.existsSync(prototypeDir)) {
  ['assets', 'css', 'js'].forEach(dirName => {
    const pSub = path.join(prototypeDir, dirName);
    const dSub = path.join(distDir, dirName);
    if (fs.existsSync(pSub)) {
      copyDirSync(pSub, dSub);
    }
  });
}

// 3. Copy HTML pages (checks prototype/ first, then rootDir)
const htmlFiles = [
  'index.html',
  'dashboard.html',
  'doctors.html',
  'provinces.html',
  'events.html',
  'activity.html',
  'reports.html',
  'admin-performance.html',
  'manage.html',
  'doctor-detail.html'
];

for (const file of htmlFiles) {
  const protoPath = path.join(prototypeDir, file);
  const rootPath = path.join(rootDir, file);
  if (fs.existsSync(protoPath)) {
    fs.copyFileSync(protoPath, path.join(distDir, file));
  } else if (fs.existsSync(rootPath)) {
    fs.copyFileSync(rootPath, path.join(distDir, file));
  }
}

// Ensure dist/index.html exists
if (!fs.existsSync(path.join(distDir, 'index.html'))) {
  const dashFile = path.join(distDir, 'dashboard.html');
  if (fs.existsSync(dashFile)) {
    fs.copyFileSync(dashFile, path.join(distDir, 'index.html'));
  }
}

console.log('[prepare-dist] Successfully created dist directory with all assets and pages for Vercel!');
