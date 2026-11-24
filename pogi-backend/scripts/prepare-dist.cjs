const fs = require('fs');
const path = require('path');

const backendDir = path.resolve(__dirname, '..');
const rootDir = path.resolve(backendDir, '..');
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

// 2. Copy root HTML pages and assets to dist
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
  const rootFilePath = path.join(rootDir, file);
  if (fs.existsSync(rootFilePath)) {
    fs.copyFileSync(rootFilePath, path.join(distDir, file));
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
