const fs = require('fs');
const path = require('path');

const pages = [
  { file: 'dashboard.html', view: 'dashboard.blade.php' },
  { file: 'doctors.html', view: 'doctors.blade.php' },
  { file: 'doctor-detail.html', view: 'doctor-detail.blade.php' },
  { file: 'provinces.html', view: 'provinces.blade.php' },
  { file: 'events.html', view: 'events.blade.php' },
  { file: 'activity.html', view: 'activity.blade.php' },
  { file: 'reports.html', view: 'reports.blade.php' },
  { file: 'admin-performance.html', view: 'admin-performance.blade.php' },
  { file: 'manage.html', view: 'manage.blade.php' },
  { file: 'index.html', view: 'login.blade.php' }
];

const targetDir = path.join(__dirname, '..', 'pogi-backend', 'resources', 'views');

pages.forEach(p => {
  const srcPath = path.join(__dirname, '..', p.file);
  if (fs.existsSync(srcPath)) {
    let content = fs.readFileSync(srcPath, 'utf8');
    // Normalize assets
    content = content.replace(/href="css\//g, 'href="/css/');
    content = content.replace(/src="css\//g, 'src="/css/');
    content = content.replace(/href="js\//g, 'href="/js/');
    content = content.replace(/src="js\//g, 'src="/js/');
    content = content.replace(/src="assets\//g, 'src="/assets/');
    content = content.replace(/href="assets\//g, 'href="/assets/');

    // Normalize routes
    content = content.replace(/href="dashboard\.html"/g, 'href="/dashboard"');
    content = content.replace(/href="doctors\.html(\?[^"]*)?"/g, 'href="/doctors$1"');
    content = content.replace(/href="doctor-detail\.html(\?[^"]*)?"/g, 'href="/doctor-detail$1"');
    content = content.replace(/href="provinces\.html(\?[^"]*)?"/g, 'href="/provinces$1"');
    content = content.replace(/href="events\.html(\?[^"]*)?"/g, 'href="/events$1"');
    content = content.replace(/href="activity\.html(\?[^"]*)?"/g, 'href="/activity$1"');
    content = content.replace(/href="reports\.html(\?[^"]*)?"/g, 'href="/reports$1"');
    content = content.replace(/href="admin-performance\.html(\?[^"]*)?"/g, 'href="/admin-performance$1"');
    content = content.replace(/href="manage\.html(\?[^"]*)?"/g, 'href="/manage$1"');
    content = content.replace(/href="index\.html(\?[^"]*)?"/g, 'href="/login$1"');

    // JS navigation redirects
    content = content.replace(/window\.location\.href\s*=\s*'dashboard\.html'/g, "window.location.href='/dashboard'");
    content = content.replace(/window\.location\.href\s*=\s*'doctors\.html'/g, "window.location.href='/doctors'");
    content = content.replace(/window\.location\.href\s*=\s*'index\.html'/g, "window.location.href='/login'");

    fs.writeFileSync(path.join(targetDir, p.view), content, 'utf8');
    console.log('✓ Successfully created:', p.view);
  }
});
