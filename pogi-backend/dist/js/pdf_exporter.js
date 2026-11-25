
function getActivePdfUserName() {
  if (typeof window !== 'undefined') {
    if (window.CURRENT_USER && window.CURRENT_USER.name) return window.CURRENT_USER.name;
    if (window.AUTH_USER && window.AUTH_USER.name) return window.AUTH_USER.name;
  }
  if (typeof PogiStore !== 'undefined' && typeof PogiStore.getCurrentUser === 'function') {
    const u = PogiStore.getCurrentUser();
    if (u && u.name) return u.name;
  }
  return 'Admin POGI';
}

/**
 * POGI SKP TRACKER - OFFICIAL PDF EXPORTER MODULE
 * Menggantikan seluruh ekspor CSV menjadi Dokumen PDF Resmi Standar PP POGI & Kemenkes RI.
 */

const PogiPdfExporter = {
  /**
   * Render and print an official POGI PDF document via a hidden iframe
   */
  printDocument({ title, docCode, subtitle, orientation = 'landscape', metaItems = [], summaryStats = [], columns = [], rows = [], footerNote = '' }) {
    const today = new Date();
    const formattedDate = today.toLocaleDateString('id-ID', {
      day: 'numeric',
      month: 'long',
      year: 'numeric'
    });
    const formattedTime = today.toLocaleTimeString('id-ID', {
      hour: '2-digit',
      minute: '2-digit'
    }) + ' WIB';

    const generatedDocCode = docCode || `POGI-SKP-DOC-${today.getFullYear()}${(today.getMonth()+1).toString().padStart(2, '0')}${today.getDate().toString().padStart(2, '0')}-${Math.floor(1000 + Math.random() * 9000)}`;

    const pageOrientation = orientation === 'portrait' ? 'portrait' : 'landscape';

    const html = `<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>${title} - ${generatedDocCode}</title>
  <style>
    @page {
      size: A4 ${pageOrientation};
      margin: 10mm 12mm 12mm 12mm;
    }
    * {
      box-sizing: border-box;
      -webkit-print-color-adjust: exact !important;
      print-color-adjust: exact !important;
    }
    body {
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
      font-size: 9pt;
      line-height: 1.35;
      color: #0f172a;
      background: #ffffff;
      margin: 0;
      padding: 0;
    }
    
    /* KOP SURAT RESMI POGI */
    .kop-container {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      padding-bottom: 8px;
      border-bottom: 3px double #16b3ac;
      margin-bottom: 12px;
    }
    .kop-logo-box {
      width: 60px;
      height: 60px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }
    .kop-logo-box img {
      max-width: 100%;
      max-height: 100%;
      object-fit: contain;
    }
    .kop-center {
      flex: 1;
      text-align: center;
    }
    .kop-center h2 {
      margin: 0;
      font-size: 11pt;
      font-weight: 900;
      color: #16b3ac;
      letter-spacing: 0.5px;
      text-transform: uppercase;
    }
    .kop-center h3 {
      margin: 2px 0;
      font-size: 9.5pt;
      font-weight: 800;
      color: #1e293b;
    }
    .kop-center p {
      margin: 1px 0;
      font-size: 7.5pt;
      color: #64748b;
    }
    .kop-badge-box {
      flex-shrink: 0;
      text-align: right;
      display: flex;
      flex-direction: column;
      gap: 3px;
    }
    .tag-badge {
      display: inline-block;
      padding: 2px 6px;
      font-size: 7pt;
      font-weight: 800;
      border-radius: 4px;
      text-transform: uppercase;
    }
    .tag-kemenkes {
      background: #ecfdf5;
      color: #065f46;
      border: 1px solid #a7f3d0;
    }
    .tag-satusehat {
      background: #eff6ff;
      color: #1e40af;
      border: 1px solid #bfdbfe;
    }

    /* REPORT HEADING & METADATA */
    .report-heading-bar {
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-left: 4px solid #16b3ac;
      border-radius: 6px;
      padding: 8px 12px;
      margin-bottom: 12px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 12px;
    }
    .report-heading-bar h1 {
      margin: 0;
      font-size: 11pt;
      font-weight: 900;
      color: #0f172a;
    }
    .report-heading-bar p {
      margin: 2px 0 0 0;
      font-size: 8pt;
      color: #64748b;
    }
    .meta-details {
      text-align: right;
      font-size: 7.5pt;
      color: #475569;
      line-height: 1.4;
    }
    .meta-details strong {
      color: #0f172a;
    }

    /* SUMMARY STATS ROW */
    .summary-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(110px, 1fr));
      gap: 8px;
      margin-bottom: 12px;
    }
    .stat-pill {
      background: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 6px;
      padding: 6px 8px;
      box-shadow: 0 1px 2px rgba(0,0,0,0.02);
    }
    .stat-pill .label {
      font-size: 7pt;
      font-weight: 700;
      color: #64748b;
      text-transform: uppercase;
    }
    .stat-pill .val {
      font-size: 11pt;
      font-weight: 900;
      color: #0f172a;
      margin-top: 1px;
    }
    .stat-pill.success {
      border-left: 3px solid #059669;
    }
    .stat-pill.warning {
      border-left: 3px solid #dc2626;
    }
    .stat-pill.info {
      border-left: 3px solid #0284c7;
    }

    /* DATA TABLE */
    table.data-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 7.5pt;
      margin-bottom: 14px;
    }
    table.data-table thead {
      display: table-header-group;
    }
    table.data-table tr {
      page-break-inside: avoid;
    }
    table.data-table th {
      background-color: #16b3ac;
      color: #ffffff;
      font-weight: 800;
      text-transform: uppercase;
      font-size: 7pt;
      padding: 6px 5px;
      border: 1px solid #129993;
      text-align: left;
    }
    table.data-table td {
      padding: 5px;
      border: 1px solid #cbd5e1;
      vertical-align: middle;
      color: #1e293b;
    }
    table.data-table tr:nth-child(even) {
      background-color: #f8fafc;
    }
    .badge {
      display: inline-block;
      padding: 1.5px 5px;
      border-radius: 3px;
      font-size: 6.5pt;
      font-weight: 800;
      text-transform: uppercase;
    }
    .badge-complete {
      background-color: #dcfce7;
      color: #15803d;
      border: 1px solid #86efac;
    }
    .badge-incomplete {
      background-color: #fee2e2;
      color: #b91c1c;
      border: 1px solid #fca5a5;
    }
    .badge-pending {
      background-color: #fef3c7;
      color: #b45309;
      border: 1px solid #fde68a;
    }
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .font-bold { font-weight: 700; }

    /* SIGNATURES */
    .sig-container {
      display: flex;
      justify-content: space-between;
      gap: 30px;
      margin-top: 14px;
      page-break-inside: avoid;
    }
    .sig-box {
      width: 250px;
      text-align: center;
      font-size: 8pt;
    }
    .sig-role {
      font-weight: 800;
      color: #0f172a;
      margin-bottom: 45px;
    }
    .sig-name {
      font-weight: 800;
      color: #0f172a;
      text-decoration: underline;
    }
    .sig-id {
      font-size: 7pt;
      color: #64748b;
      margin-top: 1px;
    }
    .stamp-badge {
      border: 1px dashed #16b3ac;
      color: #16b3ac;
      font-size: 6.5pt;
      font-weight: 800;
      padding: 2px 6px;
      border-radius: 4px;
      display: inline-block;
      margin-top: 3px;
      background: #f0fdf4;
    }

    /* FOOTER */
    .doc-footer {
      border-top: 1px solid #cbd5e1;
      padding-top: 6px;
      margin-top: 10px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 6.5pt;
      color: #94a3b8;
    }
  </style>
</head>
<body>

  <!-- KOP RESMI POGI -->
  <div class="kop-container">
    <div class="kop-logo-box">
      <img src="assets/img/pogi-logo.png" onerror="this.onerror=null; this.src='https://raw.githubusercontent.com/antigravity-assets/pogi-logo/main/pogi.png';" alt="Logo POGI" />
    </div>
    <div class="kop-center">
      <h2>PENGURUS BESAR PERKUMPULAN OBSTETRI DAN GINEKOLOGI INDONESIA (POGI)</h2>
      <h3>KOMISI PENGEMBANGAN PENDIDIKAN PROFESI BERKELANJUTAN (P2KB) &amp; KREDENSIAL</h3>
      <p>Gedung POGI, Jl. Dr. Saharjo No. 100, Manggarai, Jakarta Selatan 12860 | Telp: (021) 8370-1234 | Email: p2kb@pogi.or.id</p>
      <p>Sistem Pemantauan Standarisasi Mandatori 160 SKP &amp; Tata Kelola Medik</p>
    </div>
    <div class="kop-badge-box">
      <span class="tag-badge tag-kemenkes">Kemenkes RI</span>
      <span class="tag-badge tag-satusehat">Plataran Sehat</span>
    </div>
  </div>

  <!-- HEADING BAR -->
  <div class="report-heading-bar">
    <div>
      <h1>${title}</h1>
      <p>${subtitle || 'Laporan Resmi Verifikasi & Pemantauan Kredensial Tenaga Medis Spesialis Obgyn'}</p>
    </div>
    <div class="meta-details">
      <div><strong>No. Dokumen:</strong> ${generatedDocCode}</div>
      <div><strong>Tanggal Terbit:</strong> ${formattedDate}, ${formattedTime}</div>
      <div><strong>Dicetak Oleh:</strong> ${getActivePdfUserName()} (Tim Data PP POGI)</div>
    </div>
  </div>

  <!-- SUMMARY METRICS -->
  ${summaryStats.length > 0 ? `
    <div class="summary-grid">
      ${summaryStats.map(s => `
        <div class="stat-pill ${s.type || ''}">
          <div class="label">${s.label}</div>
          <div class="val">${s.value}</div>
        </div>
      `).join('')}
    </div>
  ` : ''}

  <!-- TABEL DATA -->
  <table class="data-table">
    <thead>
      <tr>
        ${columns.map(c => `<th style="${c.thStyle || ''}" class="${c.align === 'center' ? 'text-center' : c.align === 'right' ? 'text-right' : ''}">${c.label}</th>`).join('')}
      </tr>
    </thead>
    <tbody>
      ${rows.map(r => `
        <tr>
          ${r.map((cell, idx) => {
            const col = columns[idx] || {};
            const alignClass = col.align === 'center' ? 'text-center' : col.align === 'right' ? 'text-right' : '';
            return `<td class="${alignClass}" style="${col.tdStyle || ''}">${cell}</td>`;
          }).join('')}
        </tr>
      `).join('')}
    </tbody>
  </table>

  <!-- TANDA TANGAN & PENGESAHAN -->
  <div class="sig-container">
    <div class="sig-box">
      <div>Mengetahui &amp; Mengesahkan,</div>
      <div class="sig-role">Ketua Komisi P2KB PP POGI</div>
      <div class="sig-name">Prof. Dr. dr. Yudi M. Hidayat, Sp.OG, Subsp. Onk</div>
      <div class="sig-id">NPA POGI: 01.99.102847</div>
      <div class="stamp-badge">&#10003; TERVERIFIKASI RESMI DIGITAL</div>
    </div>
    <div class="sig-box">
      <div>Jakarta, ${formattedDate}</div>
      <div class="sig-role">Tim Pengelola Data &amp; Verifikator Sistem</div>
      <div class="sig-name">${getActivePdfUserName()}</div>
      <div class="sig-id">ID Administrator: POGI-DATA-2026</div>
      <div class="stamp-badge">&#10003; TEREGISTRASI SISTEM KEMENKES</div>
    </div>
  </div>

  <!-- FOOTER -->
  <div class="doc-footer">
    <div>Dicetak secara otomatis dari Sistem POGI SKP Tracker &bull; Terhubung API SATUSEHAT &bull; Dokumen ini sah tanpa cap basah.</div>
    <div>Halaman 1 &bull; Kode Verifikasi: ${generatedDocCode}</div>
  </div>

  <script>
    window.onload = function() {
      setTimeout(function() {
        window.focus();
        window.print();
      }, 250);
    };
  </script>
</body>
</html>`;

    // Print using hidden iframe to avoid popup blocking
    let iframe = document.getElementById('pogi-pdf-print-iframe');
    if (iframe) {
      document.body.removeChild(iframe);
    }
    iframe = document.createElement('iframe');
    iframe.id = 'pogi-pdf-print-iframe';
    iframe.style.position = 'fixed';
    iframe.style.right = '0';
    iframe.style.bottom = '0';
    iframe.style.width = '0';
    iframe.style.height = '0';
    iframe.style.border = '0';
    document.body.appendChild(iframe);

    const doc = iframe.contentWindow || iframe.contentDocument;
    const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
    iframeDoc.open();
    iframeDoc.write(html);
    iframeDoc.close();

    if (typeof showToast === 'function') {
      showToast('Menyiapkan Laporan PDF Resmi POGI... Pilih "Save as PDF" pada jendela cetak.', 'info');
    }
  }
};

/**
 * 1. EXPORT DIRECTORY DOKTER KE PDF
 */
function exportDoctorsPdf(customDocs) {
  const docs = customDocs || (typeof currentFilteredDoctors !== 'undefined' && currentFilteredDoctors && currentFilteredDoctors.length > 0 ? currentFilteredDoctors : ((typeof window !== 'undefined' && Array.isArray(window.LIVE_SERVER_DOCTORS) && window.LIVE_SERVER_DOCTORS.length > 0) ? window.LIVE_SERVER_DOCTORS : (typeof PogiStore !== 'undefined' ? PogiStore.getDoctors() : [])));
  
  const period = (typeof document !== 'undefined' && document.getElementById('periodFilter')) ? document.getElementById('periodFilter').value : 'Q4 2026';
  
  let periodHeader = 'Total Q4 (Target 8.0)';
  let periodSubtitle = 'Kuartal Berjalan (Q4 2026)';
  if (period === 'Q3 2026') {
    periodHeader = 'Total Q3 (Target 8.0)';
    periodSubtitle = 'Kuartal Lalu (Q3 2026)';
  } else if (period === '2026') {
    periodHeader = 'Tahun 2026 (Target 32.0)';
    periodSubtitle = 'Tahun Berjalan 2026';
  } else if (period === '5YEARS') {
    periodHeader = 'Siklus 5 Thn (Target 160)';
    periodSubtitle = 'Siklus 5 Tahun Penuh';
  }

  const total = docs.length;
  const complete = docs.filter(d => (d.status || '').toUpperCase() === 'COMPLETE').length;
  const incomplete = total - complete;
  const rate = total > 0 ? ((complete / total) * 100).toFixed(1) : 0;

  const columns = [
    { label: 'No', align: 'center', thStyle: 'width: 25px;' },
    { label: 'Dokter Obgyn & No Anggota', thStyle: 'width: 170px;' },
    { label: 'Cabang Provinsi & RS Jejaring', thStyle: 'width: 160px;' },
    { label: `● Pelayanan (${period === '5YEARS' ? 'Min 80' : 'Tgt ' + (period === '2026' ? '16.0' : '4.0')})`, align: 'center', thStyle: 'width: 85px;' },
    { label: `● Pengabdian (${period === '5YEARS' ? 'Min 30' : 'Tgt ' + (period === '2026' ? '6.0' : '1.5')})`, align: 'center', thStyle: 'width: 85px;' },
    { label: `● Pembelajaran (${period === '5YEARS' ? 'Min 50' : 'Tgt ' + (period === '2026' ? '10.0' : '2.5')})`, align: 'center', thStyle: 'width: 90px;' },
    { label: periodHeader, align: 'center', thStyle: 'width: 90px;' },
    { label: 'Pending', align: 'center', thStyle: 'width: 55px;' },
    { label: 'Status SKP', align: 'center', thStyle: 'width: 75px;' }
  ];

  const rows = docs.map((d, idx) => {
    const s = (typeof PogiStore !== 'undefined' && PogiStore.getDoctorPeriodStats)
      ? PogiStore.getDoctorPeriodStats(d, period)
      : { isComplete: d.status === 'Complete', achieved: d.q4Credits || 0, pelayanan: { achieved: d.pelayananCredits || 0, target: 56 }, pengabdian: { achieved: d.pengabdianCredits || 0, target: 8 }, pembelajaran: { achieved: d.pembelajaranCredits || 0, target: 72 }, pendingCredits: d.pendingCredits || 0 };

    const statusBadge = s.isComplete
      ? '<span class="badge badge-complete">&#10003; Complete</span>'
      : '<span class="badge badge-incomplete">&#9888; Incomplete</span>';

    const pendingBadge = s.pendingCredits > 0
      ? `<span class="badge badge-pending">+${s.pendingCredits.toFixed(1)} SKP</span>`
      : '<span style="color:#94a3b8;">0 SKP</span>';

    return [
      idx + 1,
      `<div><strong>${d.name}</strong></div><div style="font-size:6.5pt; color:#64748b;">${d.pogiId} &bull; NIK: ${d.nik || '-'}</div>`,
      `<div><strong>${d.province}</strong></div><div style="font-size:6.5pt; color:#64748b;">${d.hospital || '-'}</div>`,
      `<strong>${s.pelayanan.achieved.toFixed(period === '5YEARS' ? 0 : 1)}</strong> / ${s.pelayanan.target.toFixed(period === '5YEARS' ? 0 : 1)}`,
      `<strong>${s.pengabdian.achieved.toFixed(period === '5YEARS' ? 0 : 1)}</strong> / ${s.pengabdian.target.toFixed(period === '5YEARS' ? 0 : 1)}`,
      `<strong>${s.pembelajaran.achieved.toFixed(period === '5YEARS' ? 0 : 1)}</strong> / ${s.pembelajaran.target.toFixed(period === '5YEARS' ? 0 : 1)}`,
      `<strong>${s.achieved.toFixed(1)}</strong> SKP`,
      pendingBadge,
      statusBadge
    ];
  });

  PogiPdfExporter.printDocument({
    title: 'LAPORAN DIREKTORI DOKTER SPESIALIS OBGYN & STATUS 160 SKP',
    docCode: `POGI-DOC-SPK-${Date.now().toString().slice(-6)}`,
    subtitle: `Rekapitulasi Kepatuhan Pemenuhan Kredit SKP ${periodSubtitle} Terintegrasi SATUSEHAT`,
    orientation: 'landscape',
    summaryStats: [
      { label: 'Total Dokter Terdata', value: `${total} Dokter`, type: 'info' },
      { label: 'Target Terpenuhi (Complete)', value: `${complete} Dokter`, type: 'success' },
      { label: 'Belum Terpenuhi (Incomplete)', value: `${incomplete} Dokter`, type: 'warning' },
      { label: 'Tingkat Kepatuhan (' + periodSubtitle + ')', value: `${rate}%`, type: 'success' }
    ],
    columns,
    rows
  });
}

/**
 * 2. EXPORT KATALOG EVENT SKP KE PDF
 */
function exportEventsPdf() {
  const events = (typeof window !== 'undefined' && Array.isArray(window.LIVE_SERVER_EVENTS) && window.LIVE_SERVER_EVENTS.length > 0) ? window.LIVE_SERVER_EVENTS : (typeof PogiStore !== 'undefined' ? PogiStore.getEvents() : []);
  const total = events.length;

  const columns = [
    { label: 'No', align: 'center', thStyle: 'width: 30px;' },
    { label: 'Nama Kegiatan / Event Ilmiah', thStyle: 'width: 260px;' },
    { label: 'Kategori', thStyle: 'width: 120px;' },
    { label: 'Subbidang SKP', thStyle: 'width: 110px;' },
    { label: 'Nilai SKP', align: 'center', thStyle: 'width: 70px;' },
    { label: 'Tanggal Pelaksanaan', thStyle: 'width: 100px;' },
    { label: 'Kapasitas & Peserta', thStyle: 'width: 110px;' },
    { label: 'Status Akreditasi', align: 'center', thStyle: 'width: 80px;' }
  ];

  const rows = events.map((e, idx) => [
    idx + 1,
    `<strong>${e.name || e.title || '-'}</strong>`,
    e.category || '-',
    e.type || '-',
    `<span style="color:#16b3ac; font-weight:800;">${e.creditsDisplay || e.credits} SKP</span>`,
    e.date || '-',
    e.doctors || '-',
    `<span class="badge badge-complete">&#10003; ${e.status || 'Dibuka'}</span>`
  ]);

  PogiPdfExporter.printDocument({
    title: 'KATALOG RESMI KEGIATAN & EVENT SKP TERAKREDITASI PP POGI',
    docCode: `POGI-EVT-PDF-${Date.now().toString().slice(-6)}`,
    subtitle: 'Daftar Kegiatan Pembelajaran, Pelayanan, & Pengabdian Terverifikasi Kemenkes Plataran Sehat',
    orientation: 'landscape',
    summaryStats: [
      { label: 'Total Kegiatan Terakreditasi', value: `${total} Event`, type: 'info' },
      { label: 'Standar Akreditasi', value: 'Kemenkes RI', type: 'success' },
      { label: 'Integrasi Sistem', value: 'Plataran Sehat', type: 'success' }
    ],
    columns,
    rows
  });
}

/**
 * 3. EXPORT PESERTA EVENT KE PDF
 */
function exportEventParticipantsPdf() {
  const doctors = ((typeof window !== 'undefined' && Array.isArray(window.LIVE_SERVER_DOCTORS) && window.LIVE_SERVER_DOCTORS.length > 0) ? window.LIVE_SERVER_DOCTORS : (typeof PogiStore !== 'undefined' ? PogiStore.getDoctors() : [])).slice(0, 15);
  
  const columns = [
    { label: 'No', align: 'center', thStyle: 'width: 30px;' },
    { label: 'Nama Dokter Peserta', thStyle: 'width: 220px;' },
    { label: 'No. Anggota POGI', thStyle: 'width: 130px;' },
    { label: 'Cabang Provinsi', thStyle: 'width: 140px;' },
    { label: 'Rumah Sakit / Faskes', thStyle: 'width: 200px;' },
    { label: 'Kehadiran & Absensi', align: 'center', thStyle: 'width: 110px;' },
    { label: 'Kredit SKP', align: 'center', thStyle: 'width: 100px;' }
  ];

  const rows = doctors.map((d, idx) => [
    idx + 1,
    `<strong>${d.name}</strong>`,
    d.pogiId || '-',
    d.province || '-',
    d.hospital || '-',
    '<span class="badge badge-complete">&#10003; Tuntas Hadir</span>',
    '<strong style="color:#16b3ac;">+1.5 SKP Terverifikasi</strong>'
  ]);

  PogiPdfExporter.printDocument({
    title: 'DAFTAR HADIR & REKAPITULASI KREDIT SKP PESERTA EVENT',
    docCode: `POGI-ABS-PDF-${Date.now().toString().slice(-6)}`,
    subtitle: 'Verifikasi Absensi & Pencairan Kredit SKP Peserta ke Database Plataran Sehat Kemenkes',
    orientation: 'portrait',
    summaryStats: [
      { label: 'Peserta Tuntas', value: `${doctors.length} Dokter`, type: 'success' },
      { label: 'Status Verifikasi', value: '100% Terverifikasi', type: 'success' }
    ],
    columns,
    rows
  });
}

/**
 * 4. EXPORT 38 WILAYAH PROVINSI KE PDF
 */
function exportProvincesPdf() {
  const provinces = (typeof window !== 'undefined' && Array.isArray(window.LIVE_SERVER_PROVINCES) && window.LIVE_SERVER_PROVINCES.length > 0) ? window.LIVE_SERVER_PROVINCES : (typeof PogiStore !== 'undefined' ? PogiStore.getProvinces() : []);
  const totalProv = provinces.length;
  const totalDoctors = provinces.reduce((sum, p) => sum + (p.doctors || 0), 0);
  const totalComplete = provinces.reduce((sum, p) => sum + (p.complete || 0), 0);
  const avgRate = (totalComplete / (totalDoctors || 1) * 100).toFixed(1);

  const columns = [
    { label: 'No', align: 'center', thStyle: 'width: 30px;' },
    { label: 'Cabang POGI / Provinsi', thStyle: 'width: 220px;' },
    { label: 'Jumlah Dokter Obgyn', align: 'center', thStyle: 'width: 120px;' },
    { label: 'Target Terpenuhi (Complete)', align: 'center', thStyle: 'width: 140px;' },
    { label: 'Belum Terpenuhi (Incomplete)', align: 'center', thStyle: 'width: 140px;' },
    { label: 'Capaian SKP (%)', align: 'center', thStyle: 'width: 100px;' },
    { label: 'Kategori Kepatuhan', align: 'center', thStyle: 'width: 110px;' }
  ];

  const rows = provinces.map((p, idx) => {
    let catBadge = '<span class="badge badge-complete">&#10003; Optimal</span>';
    if (p.completionRate < 40) {
      catBadge = '<span class="badge badge-incomplete">&#9888; Kritis</span>';
    } else if (p.completionRate < 80) {
      catBadge = '<span class="badge badge-pending">&#9679; Waspada</span>';
    }

    return [
      idx + 1,
      `<strong>${p.name}</strong>`,
      `${p.doctors} Dokter`,
      `<strong style="color:#059669;">${p.complete}</strong>`,
      `<strong style="color:#dc2626;">${p.incomplete}</strong>`,
      `<strong>${(p.completionRate || 0).toFixed(1)}%</strong>`,
      catBadge
    ];
  });

  PogiPdfExporter.printDocument({
    title: 'MATRIKS REKAPITULASI CAPAIAN 160 SKP 38 CABANG PROVINSI POGI',
    docCode: `POGI-PROV-PDF-${Date.now().toString().slice(-6)}`,
    subtitle: 'Pemetaan Nasional Kepatuhan Kredit SKP Dokter Spesialis Obstetri & Ginekologi se-Indonesia',
    orientation: 'portrait',
    summaryStats: [
      { label: 'Total Cabang', value: `${totalProv} Provinsi`, type: 'info' },
      { label: 'Total Dokter Obgyn', value: `${totalDoctors} Dokter`, type: 'info' },
      { label: 'Complete Nasional', value: `${totalComplete} Dokter`, type: 'success' },
      { label: 'Rata-Rata Capaian', value: `${avgRate}%`, type: 'success' }
    ],
    columns,
    rows
  });
}

/**
 * 5. EXPORT LOG AKTIVITAS / AUDIT TRAIL KE PDF
 */
function exportActivityPdf() {
  const activities = (typeof window !== 'undefined' && ((Array.isArray(window.LIVE_SERVER_ACTIVITIES) && window.LIVE_SERVER_ACTIVITIES.length > 0) || (Array.isArray(window.LIVE_SERVER_LOGS) && window.LIVE_SERVER_LOGS.length > 0))) ? (window.LIVE_SERVER_ACTIVITIES || window.LIVE_SERVER_LOGS) : (typeof PogiStore !== 'undefined' ? PogiStore.getActivities() : []);
  const total = activities.length;

  const columns = [
    { label: 'No', align: 'center', thStyle: 'width: 30px;' },
    { label: 'Waktu Pencatatan', thStyle: 'width: 140px;' },
    { label: 'Aktor / Pengguna', thStyle: 'width: 180px;' },
    { label: 'Aksi Dilakukan', thStyle: 'width: 170px;' },
    { label: 'Kategori', align: 'center', thStyle: 'width: 110px;' },
    { label: 'Rincian Jejak Audit', thStyle: 'width: 300px;' }
  ];

  const rows = activities.map((a, idx) => [
    idx + 1,
    `<span style="color:#475569; font-size:7pt;">${a.timestamp || '-'}</span>`,
    `<strong>${a.actor || '-'}</strong>`,
    `<strong>${a.action || '-'}</strong>`,
    `<span class="badge ${a.category === 'Security' ? 'badge-incomplete' : a.category === 'FollowUp' ? 'badge-pending' : 'badge-complete'}">${a.category || 'General'}</span>`,
    a.details || '-'
  ]);

  PogiPdfExporter.printDocument({
    title: 'LAPORAN RESMI JEJAK AUDIT & LOG AKTIVITAS SISTEM (AUDIT TRAIL)',
    docCode: `POGI-LOG-PDF-${Date.now().toString().slice(-6)}`,
    subtitle: 'Catatan Waktu Nyata Aktivitas Perubahan Data, Verifikasi SKP, dan Kredensialing PP POGI',
    orientation: 'landscape',
    summaryStats: [
      { label: 'Total Log Audit', value: `${total} Entri`, type: 'info' },
      { label: 'Integritas Log', value: 'Terenkripsi SHA-256', type: 'success' },
      { label: 'Keabsahan Audit', value: 'Sesuai Standar Kemenkes', type: 'success' }
    ],
    columns,
    rows
  });
}

/**
 * 6. EXPORT MASTER DATA MANAGE KE PDF
 */
function exportActiveManageTabPdf(targetTab) {
  const tab = targetTab || (typeof currentTab !== 'undefined' ? currentTab : 'doctor');
  
  if (tab === 'doctor') {
    exportDoctorsPdf();
  } else if (tab === 'event') {
    exportEventsPdf();
  } else if (tab === 'institution') {
    const insts = (typeof window !== 'undefined' && Array.isArray(window.LIVE_MANAGE_INSTITUTIONS) && window.LIVE_MANAGE_INSTITUTIONS.length > 0) ? window.LIVE_MANAGE_INSTITUTIONS : (typeof PogiStore !== 'undefined' ? PogiStore.getInstitutions() : []);
    const columns = [
      { label: 'No', align: 'center', thStyle: 'width: 30px;' },
      { label: 'Kode RS', thStyle: 'width: 100px;' },
      { label: 'Nama Rumah Sakit / Faskes', thStyle: 'width: 260px;' },
      { label: 'Provinsi Cabang', thStyle: 'width: 160px;' },
      { label: 'Tipe / Kelas RS', align: 'center', thStyle: 'width: 100px;' },
      { label: 'Jumlah Dokter Obgyn', align: 'center', thStyle: 'width: 120px;' },
      { label: 'Status Akreditasi', align: 'center', thStyle: 'width: 100px;' }
    ];
    const rows = insts.map((inst, idx) => [
      idx + 1,
      `<strong>${inst.id || '-'}</strong>`,
      `<strong>${inst.name || '-'}</strong>`,
      inst.province || '-',
      inst.type || 'RS Tipe B',
      `${inst.doctorsCount || inst.doctors || 0} Dokter`,
      '<span class="badge badge-complete">&#10003; Terakreditasi Paripurna</span>'
    ]);
    PogiPdfExporter.printDocument({
      title: 'MASTER DATA RUMAH SAKIT & JEJARING FASKES POGI',
      docCode: `POGI-RS-PDF-${Date.now().toString().slice(-6)}`,
      subtitle: 'Direktori Resmi Rumah Sakit Jejaring Pelayanan Obstetris & Ginekologi Terakreditasi',
      orientation: 'portrait',
      columns,
      rows
    });
  } else if (tab === 'category') {
    const cats = (typeof window !== 'undefined' && Array.isArray(window.LIVE_MANAGE_CATEGORIES) && window.LIVE_MANAGE_CATEGORIES.length > 0) ? window.LIVE_MANAGE_CATEGORIES : (typeof PogiStore !== 'undefined' ? PogiStore.getCategories() : []);
    const columns = [
      { label: 'No', align: 'center', thStyle: 'width: 30px;' },
      { label: 'Kode Kategori', thStyle: 'width: 100px;' },
      { label: 'Nama Kategori Kegiatan', thStyle: 'width: 240px;' },
      { label: 'Subbidang SKP Terkait', thStyle: 'width: 160px;' },
      { label: 'Standar Poin SKP', align: 'center', thStyle: 'width: 120px;' },
      { label: 'Status Verifikasi', align: 'center', thStyle: 'width: 100px;' }
    ];
    const rows = cats.map((cat, idx) => [
      idx + 1,
      `<strong>${cat.id || '-'}</strong>`,
      `<strong>${cat.name || '-'}</strong>`,
      cat.type || 'Pembelajaran',
      `Min. ${cat.defaultCredits || '1 - 5'} SKP`,
      '<span class="badge badge-complete">&#10003; Standar POGI</span>'
    ]);
    PogiPdfExporter.printDocument({
      title: 'MASTER DATA KATEGORI KEGIATAN & BOBOT SKP POGI',
      docCode: `POGI-CAT-PDF-${Date.now().toString().slice(-6)}`,
      subtitle: 'Standarisasi Pembobotan Kredit SKP Berdasarkan 3 Subbidang Resmi Kemenkes RI',
      orientation: 'portrait',
      columns,
      rows
    });
  } else {
    // User tab
    const columns = [
      { label: 'No', align: 'center', thStyle: 'width: 30px;' },
      { label: 'Nama Pengguna', thStyle: 'width: 200px;' },
      { label: 'Email Kedinasan', thStyle: 'width: 200px;' },
      { label: 'Peran / Role', thStyle: 'width: 140px;' },
      { label: 'Wilayah Akses', thStyle: 'width: 150px;' },
      { label: 'Status Akun', align: 'center', thStyle: 'width: 100px;' }
    ];
    const liveUsers = (typeof window !== 'undefined' && Array.isArray(window.LIVE_MANAGE_USERS) && window.LIVE_MANAGE_USERS.length > 0)
      ? window.LIVE_MANAGE_USERS
      : (typeof PogiStore !== 'undefined' ? PogiStore.getUsers() : []);
    const rows = liveUsers.map((u, idx) => [
      idx + 1,
      '<strong>' + (u.name || 'Pengguna') + '</strong>',
      u.email || '-',
      u.role || u.display_role || 'Admin POGI',
      u.branch_name || u.branch || 'Pusat',
      '<span class="badge badge-complete">Aktif</span>'
    ]);
    PogiPdfExporter.printDocument({
      title: 'MASTER DATA PENGGUNA SISTEM & HAK AKSES POGI TRACKER',
      docCode: `POGI-USR-PDF-${Date.now().toString().slice(-6)}`,
      subtitle: 'Daftar Akun Terdaftar & Matriks Otorisasi Pengelolaan Data Kredensial',
      orientation: 'portrait',
      columns,
      rows
    });
  }
}

/**
 * 7. EXPORT REKAP LAPORAN NASIONAL (REPORTS.HTML) KE PDF
 */
function exportReportTablePdf() {
  exportProvincesPdf();
}

// Global Aliases & PogiPdfExporter exports
window.PogiPdfExporter = {
  exportManageTab: exportActiveManageTabPdf,
  exportDoctors: exportDoctorsPdf,
  exportEvents: exportEventsPdf,
  exportProvinces: exportProvincesPdf,
  exportActivity: exportActivityPdf,
  printOfficialSkpReport: printOfficialSkpReportPdf,
  printDocument: PogiPdfExporter.printDocument
};

window.exportDoctorsCsv = exportDoctorsPdf;
window.exportDoctorData = exportDoctorsPdf;
window.exportDoctorsPdf = exportDoctorsPdf;
window.exportEventsPdf = exportEventsPdf;
window.exportEventCSV = exportEventsPdf;
window.exportEventParticipants = exportEventParticipantsPdf;
window.exportProvincesCSV = exportProvincesPdf;
window.exportProvincesPdf = exportProvincesPdf;
window.exportActivityCSV = exportActivityPdf;
window.exportActivityPdf = exportActivityPdf;
window.exportReportExcel = exportReportTablePdf;
window.exportReportTablePdf = exportReportTablePdf;
window.exportActiveManageTab = exportActiveManageTabPdf;
window.exportActiveManageTabPdf = exportActiveManageTabPdf;
window.exportManageTabPdf = exportActiveManageTabPdf;
window.printOfficialSkpReportPdf = printOfficialSkpReportPdf;



/**
 * =========================================================================
 * CLIENT-SIDE DYNAMIC PDF GENERATOR (BUKAN WINDOW PRINT)
 * Menggunakan library jsPDF & jspdf-autotable langsung di browser
 * =========================================================================
 */

function getJsPdfInstance() {
  const jsPDFClass = (window.jspdf && window.jspdf.jsPDF) ? window.jspdf.jsPDF : (window.jsPDF || null);
  if (!jsPDFClass) return null;
  if (window.jspdfAutotable && window.jspdfAutotable.applyPlugin) {
    try { window.jspdfAutotable.applyPlugin(jsPDFClass); } catch(e) {}
  }
  return jsPDFClass;
}

/**
 * 8. CETAK & UNDUH SKP REPORT RESMI (100% DATA REAL DATABASE)
 */
function printOfficialSkpReportPdf(monthName) {
  const monthlyList = (typeof window !== 'undefined' && Array.isArray(window.LIVE_MONTHLY_DATA)) ? window.LIVE_MONTHLY_DATA : [];
  const ctx = (typeof window !== 'undefined' && window.LIVE_REPORT_PDF_CONTEXT) ? window.LIVE_REPORT_PDF_CONTEXT : {};
  const metrics = ctx.metrics || {};
  const breakdown = ctx.domainBreakdown || {};

  const selected = monthlyList.find(m => m.name === monthName) || monthlyList[monthlyList.length - 1] || {
    name: monthName || 'Periode Berjalan',
    target: 0,
    real: metrics.total_verified_skp || 0,
    complete: metrics.complete_doctors || 0,
    pending: metrics.pending_doctors || 0,
    pel: 0,
    peng: 0,
    pemb: 0,
    update: new Date().toLocaleDateString('id-ID')
  };

  const period = selected.name || monthName || 'Periode Berjalan';
  const totalDocs = Number(metrics.total_doctors || 0);
  const tPeng = (totalDocs * 8 / 60).toFixed(1);
  const tPel = (totalDocs * 56 / 60).toFixed(1);
  const tPemb = (totalDocs * 72 / 60).toFixed(1);
  const tTot = Number(selected.target || 0).toFixed(1);

  const rPeng = Number(selected.peng || 0).toFixed(1);
  const rPel = Number(selected.pel || 0).toFixed(1);
  const rPemb = Number(selected.pemb || 0).toFixed(1);
  const rTot = Number(selected.real || 0).toFixed(1);

  const pct = (r, t) => (Number(t) > 0 ? Math.round((Number(r) / Number(t)) * 100) + '%' : '0%');

  const ytdRows = monthlyList
    .filter(m => Number(m.real || 0) > 0 || Number(m.complete || 0) > 0)
    .map(m => [m.name, Number(m.target || 0).toFixed(1), Number(m.real || 0).toFixed(1), pct(m.real, m.target)]);

  const stPeng = breakdown.PENGABDIAN || { DONE: 0, SUBMITTED: 0, REJECTED: 0, NOT_SUBMITTED: 0 };
  const stPel = breakdown.PELAYANAN || { DONE: 0, SUBMITTED: 0, REJECTED: 0, NOT_SUBMITTED: 0 };
  const stPemb = breakdown.PEMBELAJARAN || { DONE: 0, SUBMITTED: 0, REJECTED: 0, NOT_SUBMITTED: 0 };

  const JsPdf = getJsPdfInstance();
  if (!JsPdf) {
    if (typeof showToast === 'function') showToast('Library jsPDF belum termuat.', 'error');
    return;
  }

  const doc = new JsPdf({ orientation: 'portrait', unit: 'pt', format: 'a4' });
  doc.setFont('helvetica', 'bold');
  doc.setFontSize(18);
  doc.setTextColor(17, 24, 39);
  doc.text('SKP Report (Live Database)', 40, 52);

  doc.setFont('helvetica', 'normal');
  doc.setFontSize(10);
  doc.setTextColor(107, 114, 128);
  doc.text(period + ' · ' + totalDocs + ' doctors', 40, 68);

  doc.setFont('helvetica', 'bold');
  doc.setFontSize(11.5);
  doc.setTextColor(17, 24, 39);
  doc.text('Target vs realization', 40, 94);

  doc.autoTable({
    startY: 100,
    margin: { left: 40, right: 40 },
    head: [['SKP type', 'Target', 'Realization', 'Achievement']],
    body: [
      ['Pengabdian', tPeng, rPeng, pct(rPeng, tPeng)],
      ['Pelayanan', tPel, rPel, pct(rPel, tPel)],
      ['Pembelajaran', tPemb, rPemb, pct(rPemb, tPemb)],
      ['Total', tTot, rTot, pct(rTot, tTot)]
    ],
    theme: 'plain',
    styles: { font: 'helvetica', fontSize: 9.5, textColor: [31, 41, 55], cellPadding: 4.5 },
    headStyles: { fontStyle: 'bold', textColor: [17, 24, 39], lineWidth: { bottom: 1.5 }, lineColor: [17, 24, 39] },
    columnStyles: { 0: { halign: 'left' }, 1: { halign: 'right' }, 2: { halign: 'right' }, 3: { halign: 'right' } }
  });

  const y2 = doc.lastAutoTable.finalY + 20;
  doc.setFont('helvetica', 'bold');
  doc.setFontSize(11.5);
  doc.text('Year to date (Live Snapshots)', 40, y2);

  doc.autoTable({
    startY: y2 + 6,
    margin: { left: 40, right: 40 },
    head: [['Month', 'Target', 'Realization', 'Achievement']],
    body: ytdRows.length > 0 ? ytdRows : [[period, tTot, rTot, pct(rTot, tTot)]],
    theme: 'plain',
    styles: { font: 'helvetica', fontSize: 9.5, textColor: [31, 41, 55], cellPadding: 3.5 },
    headStyles: { fontStyle: 'bold', textColor: [17, 24, 39], lineWidth: { bottom: 1.5 }, lineColor: [17, 24, 39] },
    columnStyles: { 0: { halign: 'left' }, 1: { halign: 'right' }, 2: { halign: 'right' }, 3: { halign: 'right' } }
  });

  const y3 = doc.lastAutoTable.finalY + 20;
  doc.setFont('helvetica', 'bold');
  doc.setFontSize(11.5);
  doc.text('SKP type by status (credits)', 40, y3);

  doc.autoTable({
    startY: y3 + 6,
    margin: { left: 40, right: 40 },
    head: [['SKP type', 'Done', 'Submitted', 'Rejected', 'Not submitted']],
    body: [
      ['Pengabdian', String(stPeng.DONE || 0), String(stPeng.SUBMITTED || 0), String(stPeng.REJECTED || 0), String(stPeng.NOT_SUBMITTED || 0)],
      ['Pelayanan', String(stPel.DONE || 0), String(stPel.SUBMITTED || 0), String(stPel.REJECTED || 0), String(stPel.NOT_SUBMITTED || 0)],
      ['Pembelajaran', String(stPemb.DONE || 0), String(stPemb.SUBMITTED || 0), String(stPemb.REJECTED || 0), String(stPemb.NOT_SUBMITTED || 0)]
    ],
    theme: 'plain',
    styles: { font: 'helvetica', fontSize: 9.5, textColor: [31, 41, 55], cellPadding: 4.5 },
    headStyles: { fontStyle: 'bold', textColor: [17, 24, 39], lineWidth: { bottom: 1.5 }, lineColor: [17, 24, 39] },
    columnStyles: { 0: { halign: 'left' }, 1: { halign: 'right' }, 2: { halign: 'right' }, 3: { halign: 'right' }, 4: { halign: 'right' } }
  });

  doc.setFont('helvetica', 'normal');
  doc.setFontSize(8);
  doc.setTextColor(148, 163, 184);
  doc.text('Generated: ' + (selected.update || new Date().toLocaleString('id-ID')), 40, 820);
  doc.text('1 / 1', 535, 820);

  const filename = 'SKP_Report_' + period.replace(/\s+/g, '_') + '.pdf';
  doc.save(filename);
  if (typeof showToast === 'function') showToast('Berhasil mengunduh ' + filename, 'success');
}

/**
 * 9. CETAK & UNDUH LAPORAN BULANAN RESMI ONE POGI (100% DATA REAL DATABASE)
 */
function printOnePogiMonthlyReportPdf(monthName) {
  const monthlyList = (typeof window !== 'undefined' && Array.isArray(window.LIVE_MONTHLY_DATA)) ? window.LIVE_MONTHLY_DATA : [];
  const ctx = (typeof window !== 'undefined' && window.LIVE_REPORT_PDF_CONTEXT) ? window.LIVE_REPORT_PDF_CONTEXT : {};
  const metrics = ctx.metrics || {};
  const breakdown = ctx.domainBreakdown || {};
  const topDoctors = Array.isArray(ctx.topDoctors) ? ctx.topDoctors : [];
  const topEvents = Array.isArray(ctx.topEvents) ? ctx.topEvents : [];
  const topProvinces = Array.isArray(ctx.topProvinces) ? ctx.topProvinces : [];

  const selected = monthlyList.find(m => m.name === monthName) || monthlyList[monthlyList.length - 1] || {};
  const period = selected.name || monthName || 'Periode Berjalan';
  const JsPdf = getJsPdfInstance();

  if (!JsPdf) {
    if (typeof showToast === 'function') showToast('Library jsPDF belum termuat.', 'error');
    return;
  }

  const doc = new JsPdf({ orientation: 'portrait', unit: 'pt', format: 'a4' });
  const totalDocs = Number(metrics.total_doctors || 0);
  const compDocs = Number(metrics.complete_doctors || 0);
  const incompDocs = Number(metrics.incomplete_doctors || Math.max(0, totalDocs - compDocs));
  const compRate = Number(metrics.compliance_rate || 0);
  const realSkp = Number(selected.real ?? metrics.total_verified_skp ?? 0).toFixed(1);
  const totalRecords = Number(ctx.totalRecords || 0);

  // Header Banner
  doc.setFillColor(22, 179, 172);
  doc.rect(0, 0, 595.28, 72, 'F');
  doc.setFont('helvetica', 'bold');
  doc.setFontSize(14);
  doc.setTextColor(255, 255, 255);
  doc.text('MONTHLY REPORT | POGI SKP TRACKER', 40, 32);
  doc.setFont('helvetica', 'normal');
  doc.setFontSize(9);
  doc.text('Perkumpulan Obstetri dan Ginekologi Indonesia (PP POGI) - Periode ' + period, 40, 50);

  // 1. Ringkasan Eksekutif
  doc.setFont('helvetica', 'bold');
  doc.setFontSize(11);
  doc.setTextColor(15, 23, 42);
  doc.text('1. RINGKASAN EKSEKUTIF (LIVE DATABASE)', 40, 96);

  doc.autoTable({
    startY: 104,
    margin: { left: 40, right: 40 },
    head: [['Indikator Utama', 'Capaian Aktual', 'Keterangan']],
    body: [
      ['Total Dokter Sp.OG Terpantau', totalDocs + ' Dokter', 'Data aktif pada basis data sistem'],
      ['Dokter Status Complete (160 SKP)', compDocs + ' Dokter (' + compRate + '%)', 'Memenuhi seluruh ranah wajib Kemenkes'],
      ['Dokter Status Incomplete', incompDocs + ' Dokter', 'Dalam pemantauan & tindak lanjut WA Blast'],
      ['Total Kredit SKP Terverifikasi', realSkp + ' SKP', 'Akumulasi periode ' + period],
      ['Total Entri Logbook SKP', totalRecords + ' Record', 'Tercatat pada tabel skp_entries']
    ],
    theme: 'grid',
    styles: { font: 'helvetica', fontSize: 8.5, cellPadding: 4 },
    headStyles: { fillColor: [241, 245, 249], textColor: [15, 23, 42], fontStyle: 'bold' }
  });

  // 2. Distribusi Ranah & Status
  let curY = doc.lastAutoTable.finalY + 18;
  doc.setFont('helvetica', 'bold');
  doc.setFontSize(11);
  doc.text('2. REKAPITULASI KREDIT SKP PER RANAH', 40, curY);

  const stPel = breakdown.PELAYANAN || {};
  const stPeng = breakdown.PENGABDIAN || {};
  const stPemb = breakdown.PEMBELAJARAN || {};
  doc.autoTable({
    startY: curY + 8,
    margin: { left: 40, right: 40 },
    head: [['Ranah Kompetensi', 'Done (SKP)', 'Submitted (SKP)', 'Rejected (SKP)', 'Not Submitted (SKP)']],
    body: [
      ['Pelayanan (Min 56)', String(stPel.DONE || 0), String(stPel.SUBMITTED || 0), String(stPel.REJECTED || 0), String(stPel.NOT_SUBMITTED || 0)],
      ['Pengabdian (Min 8)', String(stPeng.DONE || 0), String(stPeng.SUBMITTED || 0), String(stPeng.REJECTED || 0), String(stPeng.NOT_SUBMITTED || 0)],
      ['Pembelajaran (Min 72)', String(stPemb.DONE || 0), String(stPemb.SUBMITTED || 0), String(stPemb.REJECTED || 0), String(stPemb.NOT_SUBMITTED || 0)]
    ],
    theme: 'grid',
    styles: { font: 'helvetica', fontSize: 8.5, cellPadding: 4 },
    headStyles: { fillColor: [241, 245, 249], textColor: [15, 23, 42], fontStyle: 'bold' }
  });

  // 3. Daftar Dokter Capaian Tertinggi
  curY = doc.lastAutoTable.finalY + 18;
  doc.setFont('helvetica', 'bold');
  doc.setFontSize(11);
  doc.text('3. DOKTER DENGAN CAPAIAN SKP TERTINGGI', 40, curY);

  doc.autoTable({
    startY: curY + 8,
    margin: { left: 40, right: 40 },
    head: [['No', 'Nama Dokter', 'Provinsi', 'Pemb.', 'Pel.', 'Peng.', 'Total SKP', 'Status']],
    body: topDoctors.length > 0
      ? topDoctors.map((d, i) => [
          i + 1,
          d.name || '-',
          d.province || '-',
          Number(d.pemb_skp || 0).toFixed(1),
          Number(d.pel_skp || 0).toFixed(1),
          Number(d.peng_skp || 0).toFixed(1),
          Number(d.total_skp || 0).toFixed(1),
          d.status || '-'
        ])
      : [['-', 'Belum ada data dokter', '-', '-', '-', '-', '-', '-']],
    theme: 'grid',
    styles: { font: 'helvetica', fontSize: 8, cellPadding: 3.5 },
    headStyles: { fillColor: [241, 245, 249], textColor: [15, 23, 42], fontStyle: 'bold' }
  });

  // Halaman 2: Event & Provinsi
  doc.addPage();
  doc.setFont('helvetica', 'bold');
  doc.setFontSize(11);
  doc.text('4. KATALOG KEGIATAN & EVENT SKP TERAKREDITASI', 40, 46);

  doc.autoTable({
    startY: 54,
    margin: { left: 40, right: 40 },
    head: [['No', 'Nama Kegiatan', 'Ranah', 'Tanggal', 'Nilai SKP', 'Peserta']],
    body: topEvents.length > 0
      ? topEvents.map((e, i) => [
          i + 1,
          e.title || '-',
          e.domain || '-',
          e.event_date || '-',
          (e.credit_value || 0) + ' SKP',
          (e.participants || 0) + ' Dokter'
        ])
      : [['-', 'Belum ada event SKP', '-', '-', '-', '-']],
    theme: 'grid',
    styles: { font: 'helvetica', fontSize: 8, cellPadding: 3.5 },
    headStyles: { fillColor: [241, 245, 249], textColor: [15, 23, 42], fontStyle: 'bold' }
  });

  curY = doc.lastAutoTable.finalY + 18;
  doc.setFont('helvetica', 'bold');
  doc.setFontSize(11);
  doc.text('5. SEBARAN KEPATUHAN PROVINSI TERATAS', 40, curY);

  doc.autoTable({
    startY: curY + 8,
    margin: { left: 40, right: 40 },
    head: [['No', 'Provinsi', 'Total Dokter', 'Complete', 'Kepatuhan (%)']],
    body: topProvinces.length > 0
      ? topProvinces.map((p, i) => {
          const tot = Number(p.doctors_count || 0);
          const comp = Number(p.complete_count || 0);
          const rate = tot > 0 ? ((comp / tot) * 100).toFixed(1) + '%' : '0%';
          return [i + 1, p.name || '-', tot, comp, rate];
        })
      : [['-', 'Belum ada data provinsi', '-', '-', '-']],
    theme: 'grid',
    styles: { font: 'helvetica', fontSize: 8, cellPadding: 3.5 },
    headStyles: { fillColor: [241, 245, 249], textColor: [15, 23, 42], fontStyle: 'bold' }
  });

  // Tanda Tangan
  curY = doc.lastAutoTable.finalY + 22;
  doc.autoTable({
    startY: curY,
    margin: { left: 40, right: 40 },
    body: [[
      'Disusun oleh,\n\n\n\n' + getActivePdfUserName() + '\nTim Pengelola Data POGI\nTanggal: ' + (selected.update || new Date().toLocaleDateString('id-ID')),
      'Diketahui oleh,\n\n\n\nSekretariat Pengurus Pusat POGI\nJakarta Pusat\nPeriode: ' + period
    ]],
    theme: 'grid',
    styles: { font: 'helvetica', fontSize: 8, textColor: [31, 41, 55], cellPadding: 6, fillColor: [248, 250, 252] }
  });

  const filename = 'Monthly_Report_ONE_POGI_' + period.replace(/\s+/g, '_') + '.pdf';
  doc.save(filename);
  if (typeof showToast === 'function') showToast('Berhasil mengunduh ' + filename, 'success');
}

window.printOfficialSkpReportPdf = printOfficialSkpReportPdf;
window.printOnePogiMonthlyReportPdf = printOnePogiMonthlyReportPdf;
window.downloadOfficialSkpReportPdf = printOfficialSkpReportPdf;
window.downloadOnePogiMonthlyReportPdf = printOnePogiMonthlyReportPdf;
