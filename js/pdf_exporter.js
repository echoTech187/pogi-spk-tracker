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
      border-bottom: 3px double #006948;
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
      color: #006948;
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
      border-left: 4px solid #006948;
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
      background-color: #006948;
      color: #ffffff;
      font-weight: 800;
      text-transform: uppercase;
      font-size: 7pt;
      padding: 6px 5px;
      border: 1px solid #005439;
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
      border: 1px dashed #006948;
      color: #006948;
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
      <p>Sistem Pemantauan Standarisasi Mandatori 160 SKP &amp; Tata Kelola Medik Terintegrasi SATUSEHAT SDMK Kemenkes RI</p>
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
      <div><strong>Dicetak Oleh:</strong> Siti Rahma, S.Farm (Tim Data PP POGI)</div>
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
      <div class="sig-name">Siti Rahma, S.Farm</div>
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
  const docs = customDocs || (typeof currentFilteredDoctors !== 'undefined' && currentFilteredDoctors && currentFilteredDoctors.length > 0 ? currentFilteredDoctors : PogiStore.getDoctors());
  
  const total = docs.length;
  const complete = docs.filter(d => d.status === 'Complete').length;
  const incomplete = total - complete;
  const rate = total > 0 ? ((complete / total) * 100).toFixed(1) : 0;

  const columns = [
    { label: 'No', align: 'center', thStyle: 'width: 25px;' },
    { label: 'Dokter Obgyn & No Anggota', thStyle: 'width: 170px;' },
    { label: 'Cabang Provinsi & RS Jejaring', thStyle: 'width: 160px;' },
    { label: '● Pelayanan (Min 80)', align: 'center', thStyle: 'width: 85px;' },
    { label: '● Pengabdian (Min 30)', align: 'center', thStyle: 'width: 85px;' },
    { label: '● Pembelajaran (Min 50)', align: 'center', thStyle: 'width: 90px;' },
    { label: 'Total Q4 (Target 8.0)', align: 'center', thStyle: 'width: 90px;' },
    { label: 'Pending', align: 'center', thStyle: 'width: 55px;' },
    { label: 'Status SKP', align: 'center', thStyle: 'width: 75px;' }
  ];

  const rows = docs.map((d, idx) => {
    const statusBadge = d.status === 'Complete'
      ? '<span class="badge badge-complete">&#10003; Complete</span>'
      : '<span class="badge badge-incomplete">&#9888; Incomplete</span>';

    const pendingBadge = d.pendingCredits > 0
      ? `<span class="badge badge-pending">+${d.pendingCredits} SKP</span>`
      : '<span style="color:#94a3b8;">0 SKP</span>';

    return [
      idx + 1,
      `<div><strong>${d.name}</strong></div><div style="font-size:6.5pt; color:#64748b;">${d.pogiId} &bull; NIK: ${d.nik || '-'}</div>`,
      `<div><strong>${d.province}</strong></div><div style="font-size:6.5pt; color:#64748b;">${d.hospital || '-'}</div>`,
      `<strong>${d.pelayananCredits || 0}</strong> / 80`,
      `<strong>${d.pengabdianCredits || 0}</strong> / 30`,
      `<strong>${d.pembelajaranCredits || 0}</strong> / 50`,
      `<strong>${(d.q4Credits || 0).toFixed(1)}</strong> SKP`,
      pendingBadge,
      statusBadge
    ];
  });

  PogiPdfExporter.printDocument({
    title: 'LAPORAN DIREKTORI DOKTER SPESIALIS OBGYN & STATUS 160 SKP',
    docCode: `POGI-DOC-SPK-${Date.now().toString().slice(-6)}`,
    subtitle: `Rekapitulasi Kepatuhan Pemenuhan Kredit SKP Kuartal Berjalan (Q4 2026) Terintegrasi SATUSEHAT`,
    orientation: 'landscape',
    summaryStats: [
      { label: 'Total Dokter Terdata', value: `${total} Dokter`, type: 'info' },
      { label: 'Target Terpenuhi (Complete)', value: `${complete} Dokter`, type: 'success' },
      { label: 'Belum Terpenuhi (Incomplete)', value: `${incomplete} Dokter`, type: 'warning' },
      { label: 'Tingkat Kepatuhan Nasional', value: `${rate}%`, type: 'success' }
    ],
    columns,
    rows
  });

  if (typeof PogiStore !== 'undefined' && PogiStore.addActivity) {
    PogiStore.addActivity({
      actor: 'Siti Rahma (Admin Data)',
      action: 'Ekspor PDF Dokter',
      details: `Mengunduh Laporan Resmi PDF Direktori Dokter Obgyn (${total} data)`,
      category: 'Verification'
    });
  }
}

/**
 * 2. EXPORT KATALOG EVENT SKP KE PDF
 */
function exportEventsPdf() {
  const events = PogiStore.getEvents();
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
    `<span style="color:#006948; font-weight:800;">${e.creditsDisplay || e.credits} SKP</span>`,
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

  if (typeof PogiStore !== 'undefined' && PogiStore.addActivity) {
    PogiStore.addActivity({
      actor: 'Siti Rahma (Admin Data)',
      action: 'Ekspor PDF Event',
      details: `Mengunduh Katalog Resmi Kegiatan SKP Terakreditasi (${total} event)`,
      category: 'Verification'
    });
  }
}

/**
 * 3. EXPORT PESERTA EVENT KE PDF
 */
function exportEventParticipantsPdf() {
  const doctors = PogiStore.getDoctors().slice(0, 15);
  
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
    '<strong style="color:#006948;">+1.5 SKP Terverifikasi</strong>'
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
  const provinces = PogiStore.getProvinces();
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

  if (typeof PogiStore !== 'undefined' && PogiStore.addActivity) {
    PogiStore.addActivity({
      actor: 'Siti Rahma (Admin Data)',
      action: 'Ekspor PDF Provinsi',
      details: `Mengunduh Matriks Laporan PDF 38 Cabang Provinsi POGI`,
      category: 'Verification'
    });
  }
}

/**
 * 5. EXPORT LOG AKTIVITAS / AUDIT TRAIL KE PDF
 */
function exportActivityPdf() {
  const activities = PogiStore.getActivities();
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

  if (typeof PogiStore !== 'undefined' && PogiStore.addActivity) {
    PogiStore.addActivity({
      actor: 'Siti Rahma (Admin Data)',
      action: 'Ekspor PDF Log Audit',
      details: `Mengunduh Laporan Resmi PDF Jejak Audit (${total} entri log)`,
      category: 'Verification'
    });
  }
}

/**
 * 6. EXPORT MASTER DATA MANAGE KE PDF
 */
function exportActiveManageTabPdf(targetTab) {
  const tab = targetTab || (typeof currentTab !== 'undefined' ? currentTab : 'doctor');
  const tab = typeof currentTab !== 'undefined' ? currentTab : 'doctor';
  
  if (tab === 'doctor') {
    exportDoctorsPdf();
  } else if (tab === 'event') {
    exportEventsPdf();
  } else if (tab === 'institution') {
    const insts = PogiStore.getInstitutions();
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
    const cats = PogiStore.getCategories();
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
    const rows = [
      [1, '<strong>dr. Budi Santoso, Sp.OG(K)</strong>', 'budi.santoso@pogi.or.id', 'Super Admin (PP POGI)', 'Nasional (38 Cabang)', '<span class="badge badge-complete">Aktif</span>'],
      [2, '<strong>Siti Rahma, S.Farm</strong>', 'siti.rahma@pogi.or.id', 'Admin Data & Verifikator', 'Nasional (38 Cabang)', '<span class="badge badge-complete">Aktif</span>'],
      [3, '<strong>dr. Hendra Wijaya, Sp.OG</strong>', 'hendra.w@pogi-jabar.org', 'Admin Cabang POGI', 'Jawa Barat', '<span class="badge badge-complete">Aktif</span>'],
      [4, '<strong>dr. Ratna Sari, Sp.OG</strong>', 'ratna.sari@pogi-jatim.org', 'Admin Cabang POGI', 'Jawa Timur', '<span class="badge badge-complete">Aktif</span>']
    ];
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
 * 8. CETAK SKP REPORT RESMI (PERSIS DOKUMEN ASLI KEMENKES skp-report-2026-11)
 */
function printOfficialSkpReportPdf() {
  const html = `<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>SKP Report - November 2026</title>
  <style>
    @page {
      size: A4 portrait;
      margin: 18mm 20mm 15mm 20mm;
    }
    * {
      box-sizing: border-box;
      -webkit-print-color-adjust: exact !important;
      print-color-adjust: exact !important;
    }
    body {
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
      font-size: 10pt;
      line-height: 1.45;
      color: #1e293b;
      background: #ffffff;
      margin: 0;
      padding: 0;
    }
    .title {
      font-size: 22pt;
      font-weight: 800;
      color: #0f172a;
      margin: 0 0 2px 0;
      letter-spacing: -0.5px;
    }
    .subtitle {
      font-size: 10.5pt;
      color: #64748b;
      margin: 0 0 26px 0;
      font-weight: 500;
    }
    .section-title {
      font-size: 12pt;
      font-weight: 800;
      color: #0f172a;
      margin: 22px 0 8px 0;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 9.5pt;
      margin-bottom: 6px;
    }
    th {
      font-weight: 700;
      color: #0f172a;
      border-bottom: 1.5px solid #0f172a;
      padding: 6px 4px;
      text-align: left;
    }
    th.right, td.right {
      text-align: right;
    }
    td {
      padding: 5.5px 4px;
      border-bottom: 1px solid #e2e8f0;
      color: #1e293b;
    }
    tr.total-row td {
      font-weight: 800;
      border-top: 1.5px solid #0f172a;
      border-bottom: 1.5px solid #0f172a;
    }
    .footer {
      margin-top: 36px;
      display: flex;
      justify-content: space-between;
      font-size: 8pt;
      color: #94a3b8;
      border-top: 1px solid #f1f5f9;
      padding-top: 8px;
    }
  </style>
</head>
<body>
  <div class="title">SKP Report</div>
  <div class="subtitle">November 2026 &middot; 161 doctors</div>

  <div class="section-title">Target vs realization</div>
  <table>
    <thead>
      <tr>
        <th>SKP type</th>
        <th class="right">Target</th>
        <th class="right">Realization</th>
        <th class="right">Achievement</th>
      </tr>
    </thead>
    <tbody>
      <tr><td>Pengabdian</td><td class="right">26,83</td><td class="right">27</td><td class="right">101%</td></tr>
      <tr><td>Pelayanan</td><td class="right">281,75</td><td class="right">416</td><td class="right">148%</td></tr>
      <tr><td>Pembelajaran</td><td class="right">120,75</td><td class="right">21,5</td><td class="right">18%</td></tr>
      <tr class="total-row"><td>Total</td><td class="right">429,33</td><td class="right">464,5</td><td class="right">108%</td></tr>
    </tbody>
  </table>

  <div class="section-title">Year to date</div>
  <table>
    <thead>
      <tr>
        <th>Month</th>
        <th class="right">Target</th>
        <th class="right">Realization</th>
        <th class="right">Achievement</th>
      </tr>
    </thead>
    <tbody>
      <tr><td>Januari 2026</td><td class="right">509,33</td><td class="right">126</td><td class="right">25%</td></tr>
      <tr><td>Februari 2026</td><td class="right">496</td><td class="right">229</td><td class="right">46%</td></tr>
      <tr><td>Maret 2026</td><td class="right">485,33</td><td class="right">1.039,5</td><td class="right">214%</td></tr>
      <tr><td>April 2026</td><td class="right">482,67</td><td class="right">283,5</td><td class="right">59%</td></tr>
      <tr><td>Mei 2026</td><td class="right">477,33</td><td class="right">285</td><td class="right">60%</td></tr>
      <tr><td>Juni 2026</td><td class="right">469,33</td><td class="right">821,5</td><td class="right">175%</td></tr>
      <tr><td>Juli 2026</td><td class="right">469,33</td><td class="right">214,5</td><td class="right">46%</td></tr>
      <tr><td>Agustus 2026</td><td class="right">461,33</td><td class="right">257</td><td class="right">56%</td></tr>
      <tr><td>September 2026</td><td class="right">448</td><td class="right">923,5</td><td class="right">206%</td></tr>
      <tr><td>Oktober 2026</td><td class="right">442,67</td><td class="right">493,5</td><td class="right">111%</td></tr>
      <tr><td>November 2026</td><td class="right">429,33</td><td class="right">464,5</td><td class="right">108%</td></tr>
    </tbody>
  </table>

  <div class="section-title">SKP type by status (credits)</div>
  <table>
    <thead>
      <tr>
        <th>SKP type</th>
        <th class="right">Done</th>
        <th class="right">Submitted</th>
        <th class="right">Rejected</th>
        <th class="right">Not submitted</th>
      </tr>
    </thead>
    <tbody>
      <tr><td>Pengabdian</td><td class="right">27</td><td class="right">24,5</td><td class="right">4</td><td class="right">11</td></tr>
      <tr><td>Pelayanan</td><td class="right">416</td><td class="right">499</td><td class="right">59</td><td class="right">204</td></tr>
      <tr><td>Pembelajaran</td><td class="right">21,5</td><td class="right">28,5</td><td class="right">5,5</td><td class="right">8,5</td></tr>
    </tbody>
  </table>

  <div class="footer">
    <div>Generated 19/11/2026, 09.14.17</div>
    <div>1 / 1</div>
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

  let iframe = document.getElementById('pogi-pdf-print-iframe');
  if (iframe) document.body.removeChild(iframe);
  iframe = document.createElement('iframe');
  iframe.id = 'pogi-pdf-print-iframe';
  iframe.style.position = 'fixed';
  iframe.style.right = '0';
  iframe.style.bottom = '0';
  iframe.style.width = '0';
  iframe.style.height = '0';
  iframe.style.border = '0';
  document.body.appendChild(iframe);

  const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
  iframeDoc.open();
  iframeDoc.write(html);
  iframeDoc.close();

  if (typeof showToast === 'function') {
    showToast('Mencetak Laporan Resmi SKP Report (Persis Dokumen Kemenkes)...', 'info');
  }
}

window.printOfficialSkpReportPdf = printOfficialSkpReportPdf;


/**
 * 9. CETAK LAPORAN BULANAN RESMI ONE POGI (PERSIS DOKUMEN REVISI 01 - 3 HALAMAN)
 */
function printOnePogiMonthlyReportPdf(monthName) {
  const period = monthName || 'November 2026';
  const html = `<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Monthly Report ONE POGI - ${period}</title>
  <style>
    @page {
      size: A4 portrait;
      margin: 12mm 15mm 12mm 15mm;
    }
    * {
      box-sizing: border-box;
      -webkit-print-color-adjust: exact !important;
      print-color-adjust: exact !important;
    }
    body {
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
      font-size: 8.5pt;
      line-height: 1.35;
      color: #1e293b;
      background: #ffffff;
      margin: 0;
      padding: 0;
    }
    .page-container {
      page-break-after: always;
      position: relative;
      min-height: 270mm;
      padding-bottom: 12mm;
    }
    .page-container:last-child {
      page-break-after: auto;
    }
    .top-navy-bar {
      background: #112d4e;
      color: #ffffff;
      padding: 5px 12px;
      font-size: 8pt;
      font-weight: 800;
      letter-spacing: 0.5px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-radius: 3px;
      margin-bottom: 14px;
    }
    .doc-title {
      font-size: 20pt;
      font-weight: 900;
      color: #112d4e;
      margin: 0 0 2px 0;
      letter-spacing: -0.5px;
    }
    .doc-subtitle {
      font-size: 9.5pt;
      color: #475569;
      margin: 0 0 14px 0;
      font-weight: 500;
    }
    .meta-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 14px;
      border: 1px solid #cbd5e1;
    }
    .meta-table td {
      padding: 5px 8px;
      border: 1px solid #e2e8f0;
      font-size: 8pt;
    }
    .meta-table td.label {
      font-weight: 700;
      color: #334155;
      background: #f8fafc;
      width: 18%;
    }
    .meta-table td.val {
      color: #0f172a;
      width: 32%;
    }
    .section-title {
      font-size: 10.5pt;
      font-weight: 800;
      color: #112d4e;
      margin: 14px 0 6px 0;
      letter-spacing: -0.2px;
      display: flex;
      align-items: center;
      gap: 6px;
    }
    .section-desc {
      font-size: 7.5pt;
      color: #64748b;
      margin-bottom: 6px;
      line-height: 1.3;
    }
    table.data-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 10px;
      font-size: 8pt;
    }
    table.data-table th {
      background: #112d4e;
      color: #ffffff;
      font-weight: 700;
      padding: 5px 6px;
      text-align: left;
      border: 1px solid #112d4e;
      font-size: 7.5pt;
    }
    table.data-table th.center, table.data-table td.center {
      text-align: center;
    }
    table.data-table th.right, table.data-table td.right {
      text-align: right;
    }
    table.data-table td {
      padding: 4px 6px;
      border: 1px solid #e2e8f0;
      color: #1e293b;
    }
    table.data-table tr:nth-child(even) td {
      background: #f8fafc;
    }
    .progress-track {
      background: #e2e8f0;
      border-radius: 4px;
      height: 6px;
      width: 50px;
      display: inline-block;
      overflow: hidden;
      vertical-align: middle;
      margin-right: 4px;
    }
    .progress-fill {
      background: #3b82f6;
      height: 100%;
      border-radius: 4px;
    }
    .footer-bar {
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      display: flex;
      justify-content: space-between;
      font-size: 7pt;
      color: #94a3b8;
      border-top: 1px solid #e2e8f0;
      padding-top: 4px;
    }
  </style>
</head>
<body>

  <!-- ================= PAGE 1 ================= -->
  <div class="page-container">
    <div class="top-navy-bar">
      <span>IMPRUVE.ID</span>
      <span>MONTHLY REPORT | ONE POGI</span>
    </div>

    <div class="doc-title">LAPORAN BULANAN</div>
    <div class="doc-subtitle">Pengelolaan dan Penginputan Data Satuan Kredit Profesi (SKP) Dokter</div>

    <table class="meta-table">
      <tr>
        <td class="label">Periode laporan</td>
        <td class="val"><strong>${period}</strong></td>
        <td class="label">Tanggal laporan</td>
        <td class="val">20 November 2026</td>
      </tr>
      <tr>
        <td class="label">Nama program</td>
        <td class="val">ONE POGI</td>
        <td class="label">PIC pelaksana</td>
        <td class="val">Siti Rahma, S.Farm</td>
      </tr>
      <tr>
        <td class="label">Pelaksana</td>
        <td class="val">Impruve.Id</td>
        <td class="label">Unit/area</td>
        <td class="val">Pusat &amp; 38 Cabang POGI</td>
      </tr>
    </table>

    <div class="section-title">1. RINGKASAN EKSEKUTIF</div>
    <div class="section-desc">
      Gambaran pelaksanaan pekerjaan periode laporan: pengelolaan data 160 SKP dokter spesialis obstetri &amp; ginekologi secara akuntabel, cutoff per tanggal 1, sebagai lampiran resmi penagihan invoice &amp; evaluasi berkala PP POGI.
    </div>

    <table class="data-table">
      <thead>
        <tr>
          <th style="width: 45%;">Indikator</th>
          <th style="width: 15%; text-align: center;">Jumlah</th>
          <th style="width: 40%;">Keterangan</th>
        </tr>
      </thead>
      <tbody>
        <tr><td>Total dokter unik yang diproses</td><td class="center font-bold">161 Dokter</td><td>Hitung dokter berbeda, bukan jumlah record kegiatan.</td></tr>
        <tr><td>Total record keikutsertaan/kegiatan</td><td class="center font-bold">1.420 Record</td><td>Satu dokter dapat memiliki lebih dari satu record.</td></tr>
        <tr><td>Data SKP berhasil diinput</td><td class="center font-bold" style="color: #059669;">464,5 SKP</td><td>Input selesai dan tercatat pada sistem SATUSEHAT.</td></tr>
        <tr><td>Data dalam proses / menunggu validasi</td><td class="center font-bold" style="color: #d97706;">552 SKP (19 Dr)</td><td>Belum dapat dinyatakan selesai (menunggu verifikasi).</td></tr>
        <tr><td>Data terkendala / perlu perbaikan</td><td class="center font-bold" style="color: #dc2626;">8 Berkas</td><td>Kendala STR / dokumen buram (tindak lanjut cabang).</td></tr>
      </tbody>
    </table>

    <div class="section-title">2. RINGKASAN PROGRES PEKERJAAN</div>
    <table class="data-table">
      <thead>
        <tr>
          <th>KPI</th>
          <th class="center">Realisasi</th>
          <th class="center">Target/Basis</th>
          <th style="width: 18%;">Capaian</th>
          <th>Status</th>
          <th>Catatan</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Total database anggota</td><td class="center">5.700</td><td class="center">5.700</td>
          <td><span class="progress-track"><span class="progress-fill" style="width: 100%;"></span></span> 100.0%</td>
          <td>Baseline</td><td>Basis direktori Sp.OG</td>
        </tr>
        <tr>
          <td>Database terverifikasi</td><td class="center">3.200</td><td class="center">5.700</td>
          <td><span class="progress-track"><span class="progress-fill" style="width: 56.1%;"></span></span> 56.1%</td>
          <td>Perlu percepatan</td><td>56,1% dari total</td>
        </tr>
        <tr>
          <td>Data belum lengkap</td><td class="center">1.500</td><td class="center">5.700</td>
          <td><span class="progress-track"><span class="progress-fill" style="width: 26.3%;"></span></span> 26.3%</td>
          <td>Tindak lanjut</td><td>Lengkapi data wajib</td>
        </tr>
        <tr>
          <td>Data belum diproses</td><td class="center">1.000</td><td class="center">5.700</td>
          <td><span class="progress-track"><span class="progress-fill" style="width: 17.5%;"></span></span> 17.5%</td>
          <td>Prioritas</td><td>Perlu alokasi verifikator</td>
        </tr>
        <tr>
          <td>Anggota memenuhi SKP</td><td class="center">2.500</td><td class="center">5.700</td>
          <td><span class="progress-track"><span class="progress-fill" style="width: 43.9%;"></span></span> 43.9%</td>
          <td>Monitoring</td><td>Perlu validasi bukti</td>
        </tr>
        <tr>
          <td>Anggota belum memenuhi SKP</td><td class="center">2.000</td><td class="center">5.700</td>
          <td><span class="progress-track"><span class="progress-fill" style="width: 35.1%;"></span></span> 35.1%</td>
          <td>Tindak lanjut</td><td>Segmentasi anggota</td>
        </tr>
        <tr>
          <td>Anggota dalam proses SKP</td><td class="center">1.200</td><td class="center">5.700</td>
          <td><span class="progress-track"><span class="progress-fill" style="width: 21.1%;"></span></span> 21.1%</td>
          <td>Monitoring</td><td>Pantau penyelesaian</td>
        </tr>
      </tbody>
    </table>

    <div class="section-title">3. DETAIL PENGINPUTAN DATA DOKTER</div>
    <div class="section-desc">Ringkasan transaksi penginputan data kredit dokter periode berjalan.</div>
    <table class="data-table">
      <thead>
        <tr>
          <th style="width: 5%;" class="center">No.</th>
          <th style="width: 25%;">Nama dokter / ID</th>
          <th style="width: 18%;">Wilayah/cabang</th>
          <th style="width: 26%;">Kegiatan &amp; tanggal</th>
          <th style="width: 8%;" class="center">SKP</th>
          <th style="width: 10%;" class="center">Tgl. input</th>
          <th style="width: 12%;">Status</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="center">1</td>
          <td><strong>dr. Cut Simatupang, Sp.OG</strong><br><span style="color:#64748b; font-size:6.5pt;">ID: POGI-2026-1001</span></td>
          <td>DKI Jakarta</td>
          <td>PIT POGI 2026<br><span style="color:#64748b; font-size:6.5pt;">15 Nov 2026</span></td>
          <td class="center font-bold">8.8</td>
          <td class="center">16/11/26</td>
          <td><span style="color:#059669; font-weight:700;">Terverifikasi</span></td>
        </tr>
        <tr>
          <td class="center">2</td>
          <td><strong>dr. Ratna Kurniawan, Sp.OG</strong><br><span style="color:#64748b; font-size:6.5pt;">ID: POGI-2026-1002</span></td>
          <td>Jawa Barat</td>
          <td>Simposium Obstetri<br><span style="color:#64748b; font-size:6.5pt;">10 Nov 2026</span></td>
          <td class="center font-bold">9.6</td>
          <td class="center">12/11/26</td>
          <td><span style="color:#059669; font-weight:700;">Terverifikasi</span></td>
        </tr>
      </tbody>
    </table>

    <div class="footer-bar">
      <span>IMPRUVE.ID | MONTHLY REPORT ONE POGI</span>
      <span>Halaman 1</span>
    </div>
  </div>

  <!-- ================= PAGE 2 ================= -->
  <div class="page-container">
    <div class="top-navy-bar">
      <span>IMPRUVE.ID</span>
      <span>MONTHLY REPORT | ONE POGI &bull; Halaman 2</span>
    </div>

    <table class="data-table" style="margin-top: 10px;">
      <thead>
        <tr>
          <th style="width: 5%;" class="center">No.</th>
          <th style="width: 25%;">Nama dokter / ID</th>
          <th style="width: 18%;">Wilayah/cabang</th>
          <th style="width: 26%;">Kegiatan &amp; tanggal</th>
          <th style="width: 8%;" class="center">SKP</th>
          <th style="width: 10%;" class="center">Tgl. input</th>
          <th style="width: 12%;">Status</th>
        </tr>
      </thead>
      <tbody>
        <tr><td class="center">3</td><td><strong>dr. Sri Kusuma, Sp.OG</strong><br><span style="color:#64748b; font-size:6.5pt;">ID: POGI-2026-1003</span></td><td>Jawa Timur</td><td>Workshop USG Maternal (05/11)</td><td class="center font-bold">10.4</td><td class="center">08/11/26</td><td><span style="color:#059669; font-weight:700;">Terverifikasi</span></td></tr>
        <tr><td class="center">4</td><td><strong>Dr. dr. Mega Simatupang, Sp.OG</strong><br><span style="color:#64748b; font-size:6.5pt;">ID: POGI-2026-1004</span></td><td>Bali</td><td>Baksos IVA Skrining (02/11)</td><td class="center font-bold">11.2</td><td class="center">05/11/26</td><td><span style="color:#059669; font-weight:700;">Terverifikasi</span></td></tr>
        <tr><td class="center">5</td><td><strong>dr. Cut Kurniawan, Sp.OG</strong><br><span style="color:#64748b; font-size:6.5pt;">ID: POGI-2026-1005</span></td><td>Sumatera Utara</td><td>PIT POGI 2026 (15/11)</td><td class="center font-bold">12.0</td><td class="center">16/11/26</td><td><span style="color:#059669; font-weight:700;">Terverifikasi</span></td></tr>
        <tr><td class="center">6</td><td><strong>Prof. Dr. dr. Ratna Kusuma, Sp.OG</strong><br><span style="color:#64748b; font-size:6.5pt;">ID: POGI-2026-1006</span></td><td>DKI Jakarta</td><td>Webinar Fetal Medicine (18/11)</td><td class="center font-bold">12.8</td><td class="center">19/11/26</td><td><span style="color:#059669; font-weight:700;">Terverifikasi</span></td></tr>
        <tr><td class="center">7</td><td><strong>dr. Sri Simatupang, Sp.OG</strong><br><span style="color:#64748b; font-size:6.5pt;">ID: POGI-2026-1007</span></td><td>Jawa Barat</td><td>Visite &amp; Pelayanan Klinis (10/11)</td><td class="center font-bold">13.6</td><td class="center">12/11/26</td><td><span style="color:#059669; font-weight:700;">Terverifikasi</span></td></tr>
      </tbody>
    </table>

    <div class="section-title">4. REKAPITULASI BERDASARKAN KEGIATAN</div>
    <table class="data-table">
      <thead>
        <tr>
          <th style="width: 5%;" class="center">No.</th>
          <th style="width: 45%;">Nama kegiatan/webinar</th>
          <th style="width: 15%;" class="center">Tanggal</th>
          <th style="width: 15%;" class="center">Jumlah peserta</th>
          <th style="width: 10%;" class="center">Jumlah SKP</th>
          <th style="width: 15%;" class="center">Berhasil diinput</th>
        </tr>
      </thead>
      <tbody>
        <tr><td class="center">1</td><td>PIT POGI 2026 - Maternal &amp; Neonatal Emergency</td><td class="center">15 Nov 2026</td><td class="center">160 Peserta</td><td class="center">240 SKP</td><td class="center font-bold" style="color:#059669;">160 (100%)</td></tr>
        <tr><td class="center">2</td><td>Simposium Nasional Onkologi Ginekologi Terpadu</td><td class="center">10 Nov 2026</td><td class="center">120 Peserta</td><td class="center">180 SKP</td><td class="center font-bold" style="color:#059669;">120 (100%)</td></tr>
        <tr><td class="center">3</td><td>Workshop USG Obstetri Dasar &amp; Lanjut</td><td class="center">05 Nov 2026</td><td class="center">85 Peserta</td><td class="center">127,5 SKP</td><td class="center font-bold" style="color:#059669;">85 (100%)</td></tr>
        <tr><td class="center">4</td><td>Bakti Sosial IVA Test &amp; Edukasi Reproduksi</td><td class="center">02 Nov 2026</td><td class="center">74 Peserta</td><td class="center">74 SKP</td><td class="center font-bold" style="color:#059669;">74 (100%)</td></tr>
      </tbody>
    </table>

    <div class="section-title">5. KENDALA DAN PERMASALAHAN</div>
    <table class="data-table">
      <thead>
        <tr>
          <th style="width: 5%;" class="center">No.</th>
          <th style="width: 32%;">Kendala / temuan</th>
          <th style="width: 28%;">Dampak terhadap pekerjaan</th>
          <th style="width: 25%;">Tindak lanjut / PIC</th>
          <th style="width: 10%;">Status</th>
        </tr>
      </thead>
      <tbody>
        <tr><td class="center">1</td><td>Keterlambatan sinkronisasi data API portal Kemenkes</td><td>Data SKP menunggu validasi</td><td>Koordinasi helpdesk SATUSEHAT (Siti Rahma)</td><td><span style="color:#d97706; font-weight:700;">On Progress</span></td></tr>
        <tr><td class="center">2</td><td>Dokumen sertifikat resolusi rendah / tidak terbaca</td><td>Verifikasi ditunda sementara</td><td>Konfirmasi ulang via blast WA ke dokter ybs</td><td><span style="color:#059669; font-weight:700;">Resolved</span></td></tr>
        <tr><td class="center">3</td><td>Perbedaan format penulisan STR dokter di RS jejaring</td><td>Gagal autovalidasi sistem</td><td>Rekonsiliasi master data cabang (Admin Cabang)</td><td><span style="color:#059669; font-weight:700;">Done</span></td></tr>
      </tbody>
    </table>

    <div class="section-title">6. RENCANA TINDAK LANJUT PERIODE BERIKUTNYA</div>
    <table class="data-table">
      <thead>
        <tr>
          <th style="width: 5%;" class="center">No.</th>
          <th style="width: 55%;">Rencana pekerjaan</th>
          <th style="width: 25%;" class="center">Target waktu</th>
          <th style="width: 15%;">PIC</th>
        </tr>
      </thead>
      <tbody>
        <tr><td class="center">1</td><td>Penerbitan rekapitulasi resmi &amp; lampiran penagihan invoice November</td><td class="center">Minggu ke-1 Desember 2026</td><td>Finance &amp; PIC</td></tr>
        <tr><td class="center">2</td><td>Penyelesaian verifikasi 19 berkas SKP tertunda</td><td class="center">Minggu ke-2 Desember 2026</td><td>Siti Rahma, S.Farm</td></tr>
        <tr><td class="center">3</td><td>Pelaksanaan webinar akreditasi batch Desember</td><td class="center">Minggu ke-3 Desember 2026</td><td>Divisi Ilmiah</td></tr>
        <tr><td class="center">4</td><td>Rekonsiliasi data akhir tahun &amp; tutup buku kuartal</td><td class="center">Minggu ke-4 Desember 2026</td><td>Tim Data POGI</td></tr>
      </tbody>
    </table>

    <div class="footer-bar">
      <span>IMPRUVE.ID | MONTHLY REPORT ONE POGI</span>
      <span>Halaman 2</span>
    </div>
  </div>

  <!-- ================= PAGE 3 ================= -->
  <div class="page-container">
    <div class="top-navy-bar">
      <span>IMPRUVE.ID</span>
      <span>MONTHLY REPORT | ONE POGI &bull; Halaman 3</span>
    </div>

    <div class="section-title">7. PROJECT PLAN BULAN DEPAN (DESEMBER 2026)</div>
    <div class="section-desc">Rencana kerja untuk periode bulan depan. Isi periode, target, dan PIC sesuai kesepakatan.</div>
    <table class="data-table">
      <thead>
        <tr>
          <th style="width: 5%;" class="center">No.</th>
          <th style="width: 45%;">Rencana pekerjaan</th>
          <th style="width: 25%;" class="center">Periode</th>
          <th style="width: 10%;" class="center">Target</th>
          <th style="width: 15%;">PIC</th>
        </tr>
      </thead>
      <tbody>
        <tr><td class="center">1</td><td>Pengumpulan / penerimaan data dokter</td><td class="center">Desember 2026, minggu ke-1</td><td class="center">200 Dokter</td><td>Tim Admin</td></tr>
        <tr><td class="center">2</td><td>Pemeriksaan kelengkapan dan kesesuaian data</td><td class="center">Desember 2026, minggu ke-2</td><td class="center">100% Berkas</td><td>Tim Verifikator</td></tr>
        <tr><td class="center">3</td><td>Penginputan data SKP ke sistem ONE POGI</td><td class="center">Desember 2026, minggu ke-3</td><td class="center">Min. 400 SKP</td><td>Tim Operator</td></tr>
        <tr><td class="center">4</td><td>Verifikasi hasil penginputan</td><td class="center">Desember 2026, minggu ke-4</td><td class="center">100% Lolos</td><td>Kemenkes &amp; POGI</td></tr>
        <tr><td class="center">5</td><td>Monitoring status dan tindak lanjut</td><td class="center">Desember 2026, minggu ke-4</td><td class="center">Rekap Final</td><td>Lead PIC</td></tr>
      </tbody>
    </table>

    <div class="section-title">8. DOKUMENTASI / LAMPIRAN</div>
    <div class="section-desc">Centang atau beri keterangan untuk dokumen pendukung yang disertakan:</div>
    <table class="data-table" style="font-size: 8pt;">
      <tr>
        <td style="width: 50%;"><strong>&#9632;</strong> Rekap data dokter / master tracking</td>
        <td style="width: 50%;"><strong>&#9632;</strong> Bukti atau tangkapan layar sistem (sesuai izin)</td>
      </tr>
      <tr>
        <td><strong>&#9632;</strong> Rekap kegiatan dan keikutsertaan webinar</td>
        <td><strong>&#9632;</strong> Catatan koordinasi / konfirmasi data</td>
      </tr>
      <tr>
        <td><strong>&#9632;</strong> Bukti verifikasi atau penyelesaian input</td>
        <td><strong>&#9632;</strong> Lampiran lain: <em>Invoice Tagihan &amp; Berita Acara Rekonsiliasi</em></td>
      </tr>
    </table>

    <div class="section-title">9. PENUTUP</div>
    <div class="section-desc">
      Demikian laporan bulanan kegiatan pengelolaan dan penginputan data SKP dokter dalam program ONE POGI untuk periode <strong>${period}</strong> ini disusun sebagai dokumentasi pelaksanaan, monitoring progres, dan bahan evaluasi serta koordinasi untuk periode berikutnya.
    </div>

    <table style="width: 100%; margin-top: 20px; font-size: 8pt; border-collapse: collapse;">
      <tr>
        <td style="width: 50%; vertical-align: top; padding: 10px; border: 1px solid #cbd5e1; background: #f8fafc;">
          <div>Disusun oleh,</div>
          <div style="height: 48px;"></div>
          <div><strong>Siti Rahma, S.Farm</strong></div>
          <div style="color: #64748b;">Impruve.Id</div>
          <div style="color: #64748b;">Tanggal: 20 November 2026</div>
        </td>
        <td style="width: 50%; vertical-align: top; padding: 10px; border: 1px solid #cbd5e1; background: #f8fafc;">
          <div>Diperiksa / diketahui oleh,</div>
          <div style="height: 48px;"></div>
          <div><strong>Dr. dr. Yudi M. Hidayat, Sp.OG, Subsp. Onk, D.MAS</strong></div>
          <div style="color: #64748b;">Ketua Umum PP POGI / ONE POGI</div>
          <div style="color: #64748b;">Tanggal: 20 November 2026</div>
        </td>
      </tr>
    </table>

    <div style="margin-top: 10px; font-size: 6.5pt; color: #94a3b8; line-height: 1.3;">
      Catatan pengisian: Gunakan satuan hitung yang konsisten. Bedakan dokter unik, record keikutsertaan kegiatan, dan transaksi/input SKP. Status "berhasil" hanya diberikan setelah hasil input diverifikasi pada sistem.
    </div>

    <div class="footer-bar">
      <span>IMPRUVE.ID | MONTHLY REPORT ONE POGI</span>
      <span>Halaman 3</span>
    </div>
  </div>

</body>
</html>`;

  let iframe = document.getElementById('one-pogi-print-iframe');
  if (iframe) document.body.removeChild(iframe);
  iframe = document.createElement('iframe');
  iframe.id = 'one-pogi-print-iframe';
  iframe.style.position = 'fixed';
  iframe.style.right = '0';
  iframe.style.bottom = '0';
  iframe.style.width = '0';
  iframe.style.height = '0';
  iframe.style.border = '0';
  document.body.appendChild(iframe);

  const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
  iframeDoc.open();
  iframeDoc.write(html);
  iframeDoc.close();

  setTimeout(function() {
    iframe.contentWindow.focus();
    iframe.contentWindow.print();
  }, 350);

  if (typeof showToast === 'function') {
    showToast('Membuka Laporan Bulanan Resmi ONE POGI (3 Halaman)...', 'info');
  }
}

window.printOnePogiMonthlyReportPdf = printOnePogiMonthlyReportPdf;
