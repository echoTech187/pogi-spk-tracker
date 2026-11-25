const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');

const backendDir = path.resolve(__dirname, '..');
const rootDir = path.resolve(backendDir, '..');
const distDir = path.join(backendDir, 'dist');
const rootDistDir = path.join(rootDir, 'dist');
const publicDir = path.join(backendDir, 'public');

console.log('[prepare-dist] Preparing dist directory for Vercel deployment...');

// 1. Try running export_dist.php if PHP is available to refresh HTML from Blade views
try {
  const exportScript = path.join(__dirname, 'export_dist.php');
  if (fs.existsSync(exportScript)) {
    console.log('[prepare-dist] Running Blade view export via PHP...');
    execSync(`php "${exportScript}"`, { stdio: 'inherit' });
  }
} catch (e) {
  console.log('[prepare-dist] PHP export skipped or not available:', e.message);
}

// Ensure dist directory exists
if (!fs.existsSync(distDir)) {
  fs.mkdirSync(distDir, { recursive: true });
}

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

// 2. Sync public assets (css, js, assets, images) to dist
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

// 3. Ensure index.html exists
const indexFile = path.join(distDir, 'index.html');
const dashFile = path.join(distDir, 'dashboard.html');
if (!fs.existsSync(indexFile) && fs.existsSync(dashFile)) {
  fs.copyFileSync(dashFile, indexFile);
}

// 4. Mirror dist to root dist so root-level Vercel builds also find it
try {
  copyDirSync(distDir, rootDistDir);
} catch (e) {
  // ignore
}

console.log('[prepare-dist] Successfully prepared dist directory with all pages and assets!');
