const fs = require('fs');
const files = ['dashboard.html', 'doctors.html', 'doctor-detail.html', 'provinces.html', 'events.html', 'activity.html', 'manage.html', 'reports.html'];

files.forEach(f => {
  const content = fs.readFileSync('c:/Users/Pongo/Downloads/pogi-spk-tracker/' + f, 'utf8');
  console.log('=== ' + f + ' ===');
  const buttonRegex = /<(button|a)[^>]*>([\s\S]*?)<\/\1>/gi;
  let match;
  while ((match = buttonRegex.exec(content)) !== null) {
    const full = match[0];
    const text = match[2].replace(/<[^>]+>/g, ' ').replace(/\s+/g, ' ').trim();
    if (/(pdf|print|cetak|unduh)/i.test(full)) {
      const lineNum = content.substring(0, match.index).split('\n').length;
      console.log(`  Line ${lineNum} [${match[1]}]: "${text}" | HTML: ${full.substring(0, 100).replace(/\n/g, ' ')}`);
    }
  }
});
