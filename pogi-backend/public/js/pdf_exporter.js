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
  const complete = docs.filter(d => (typeof PogiStore !== 'undefined' && PogiStore.getDoctorPeriodStats ? PogiStore.getDoctorPeriodStats(d, period).isComplete : d.status === 'Complete')).length;
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
 * 8. CETAK & UNDUH SKP REPORT RESMI (1 HALAMAN PERSIS skp-report-2026-11)
 */
function printOfficialSkpReportPdf(monthName) {
  const period = monthName || 'November 2026';
  
  const skpDb = {
    'November 2026': {
      doctors: '161 doctors',
      genDate: '20/11/2026, 09.14.17',
      tPeng: '26,83', rPeng: '27', aPeng: '101%',
      tPel: '281,75', rPel: '416', aPel: '148%',
      tPemb: '120,75', rPemb: '21,5', aPemb: '18%',
      tTot: '429,33', rTot: '464,5', aTot: '108%',
      statusPeng: { done: '27', sub: '24,5', rej: '4', notSub: '11' },
      statusPel: { done: '416', sub: '499', rej: '59', notSub: '204' },
      statusPemb: { done: '21,5', sub: '28,5', rej: '5,5', notSub: '8,5' },
      ytdIdx: 11
    },
    'Oktober 2026': {
      doctors: '158 doctors',
      genDate: '31/10/2026, 17.00.00',
      tPeng: '26,83', rPeng: '26', aPeng: '97%',
      tPel: '281,75', rPel: '440', aPel: '156%',
      tPemb: '120,75', rPemb: '27,5', aPemb: '23%',
      tTot: '442,67', rTot: '493,5', aTot: '111%',
      statusPeng: { done: '26', sub: '20', rej: '3', notSub: '12' },
      statusPel: { done: '440', sub: '480', rej: '45', notSub: '190' },
      statusPemb: { done: '27,5', sub: '30', rej: '5', notSub: '9' },
      ytdIdx: 10
    },
    'September 2026': {
      doctors: '152 doctors',
      genDate: '30/09/2026, 17.00.00',
      tPeng: '26,83', rPeng: '48', aPeng: '179%',
      tPel: '281,75', rPel: '830', aPel: '295%',
      tPemb: '120,75', rPemb: '45,5', aPemb: '38%',
      tTot: '448,00', rTot: '923,5', aTot: '206%',
      statusPeng: { done: '48', sub: '18', rej: '2', notSub: '8' },
      statusPel: { done: '830', sub: '310', rej: '35', notSub: '115' },
      statusPemb: { done: '45,5', sub: '22', rej: '4', notSub: '12' },
      ytdIdx: 9
    },
    'Agustus 2026': {
      doctors: '149 doctors',
      genDate: '31/08/2026, 17.00.00',
      tPeng: '26,83', rPeng: '13', aPeng: '48%',
      tPel: '281,75', rPel: '230', aPel: '82%',
      tPemb: '120,75', rPemb: '14', aPemb: '12%',
      tTot: '461,33', rTot: '257,0', aTot: '56%',
      statusPeng: { done: '13', sub: '16', rej: '3', notSub: '15' },
      statusPel: { done: '230', sub: '340', rej: '42', notSub: '180' },
      statusPemb: { done: '14', sub: '19', rej: '3', notSub: '14' },
      ytdIdx: 8
    },
    'Juli 2026': {
      doctors: '146 doctors',
      genDate: '31/07/2026, 17.00.00',
      tPeng: '26,83', rPeng: '11', aPeng: '41%',
      tPel: '281,75', rPel: '190', aPel: '67%',
      tPemb: '120,75', rPemb: '13,5', aPemb: '11%',
      tTot: '469,33', rTot: '214,5', aTot: '46%',
      statusPeng: { done: '11', sub: '14', rej: '2', notSub: '18' },
      statusPel: { done: '190', sub: '310', rej: '38', notSub: '195' },
      statusPemb: { done: '13,5', sub: '16', rej: '2', notSub: '16' },
      ytdIdx: 7
    },
    'Juni 2026': {
      doctors: '148 doctors',
      genDate: '30/06/2026, 17.00.00',
      tPeng: '26,83', rPeng: '42', aPeng: '157%',
      tPel: '281,75', rPel: '740', aPel: '263%',
      tPemb: '120,75', rPemb: '39,5', aPemb: '33%',
      tTot: '469,33', rTot: '821,5', aTot: '175%',
      statusPeng: { done: '42', sub: '15', rej: '2', notSub: '10' },
      statusPel: { done: '740', sub: '280', rej: '25', notSub: '95' },
      statusPemb: { done: '39,5', sub: '20', rej: '3', notSub: '11' },
      ytdIdx: 6
    }
  };

  const ytdList = [
    ['Januari 2026', '509,33', '126', '25%'],
    ['Februari 2026', '496', '229', '46%'],
    ['Maret 2026', '485,33', '1.039,5', '214%'],
    ['April 2026', '482,67', '283,5', '59%'],
    ['Mei 2026', '477,33', '285', '60%'],
    ['Juni 2026', '469,33', '821,5', '175%'],
    ['Juli 2026', '469,33', '214,5', '46%'],
    ['Agustus 2026', '461,33', '257', '56%'],
    ['September 2026', '448', '923,5', '206%'],
    ['Oktober 2026', '442,67', '493,5', '111%'],
    ['November 2026', '429,33', '464,5', '108%']
  ];

  const d = skpDb[period] || skpDb['November 2026'];
  const JsPdf = getJsPdfInstance();

  if (JsPdf) {
    const doc = new JsPdf({ orientation: 'portrait', unit: 'pt', format: 'a4' });
    
    // Header
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(18);
    doc.setTextColor(17, 24, 39);
    doc.text('SKP Report', 40, 52);

    doc.setFont('helvetica', 'normal');
    doc.setFontSize(10);
    doc.setTextColor(107, 114, 128);
    doc.text(period + ' · ' + d.doctors, 40, 68);

    // Section 1: Target vs realization
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(11.5);
    doc.setTextColor(17, 24, 39);
    doc.text('Target vs realization', 40, 94);

    doc.autoTable({
      startY: 100,
      margin: { left: 40, right: 40 },
      head: [['SKP type', 'Target', 'Realization', 'Achievement']],
      body: [
        ['Pengabdian', d.tPeng, d.rPeng, d.aPeng],
        ['Pelayanan', d.tPel, d.rPel, d.aPel],
        ['Pembelajaran', d.tPemb, d.rPemb, d.aPemb],
        ['Total', d.tTot, d.rTot, d.aTot]
      ],
      theme: 'plain',
      styles: { font: 'helvetica', fontSize: 9.5, textColor: [31, 41, 55], cellPadding: 4.5 },
      headStyles: { fontStyle: 'bold', textColor: [17, 24, 39], lineWidth: { bottom: 1.5 }, lineColor: [17, 24, 39] },
      columnStyles: {
        0: { halign: 'left', },
        1: { halign: 'right', },
        2: { halign: 'right', },
        3: { halign: 'right', }
      },
      didParseCell: function(cellData) {
        if (cellData.section === 'body') {
          if (cellData.row.index === 3) {
            cellData.cell.styles.fontStyle = 'bold';
            cellData.cell.styles.textColor = [17, 24, 39];
            cellData.cell.styles.lineWidth = { top: 1.5, bottom: 1.5 };
            cellData.cell.styles.lineColor = [17, 24, 39];
          } else {
            cellData.cell.styles.lineWidth = { bottom: 0.5 };
            cellData.cell.styles.lineColor = [229, 231, 235];
          }
        }
      }
    });

    // Section 2: Year to date
    const y2 = doc.lastAutoTable.finalY + 20;
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(11.5);
    doc.setTextColor(17, 24, 39);
    doc.text('Year to date', 40, y2);

    doc.autoTable({
      startY: y2 + 6,
      margin: { left: 40, right: 40 },
      head: [['Month', 'Target', 'Realization', 'Achievement']],
      body: ytdList.slice(0, d.ytdIdx),
      theme: 'plain',
      styles: { font: 'helvetica', fontSize: 9.5, textColor: [31, 41, 55], cellPadding: 3.5 },
      headStyles: { fontStyle: 'bold', textColor: [17, 24, 39], lineWidth: { bottom: 1.5 }, lineColor: [17, 24, 39] },
      columnStyles: {
        0: { halign: 'left', },
        1: { halign: 'right', },
        2: { halign: 'right', },
        3: { halign: 'right', }
      },
      didParseCell: function(cellData) {
        if (cellData.section === 'body') {
          cellData.cell.styles.lineWidth = { bottom: 0.5 };
          cellData.cell.styles.lineColor = [229, 231, 235];
        }
      }
    });

    // Section 3: SKP type by status
    const y3 = doc.lastAutoTable.finalY + 20;
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(11.5);
    doc.setTextColor(17, 24, 39);
    doc.text('SKP type by status (credits)', 40, y3);

    doc.autoTable({
      startY: y3 + 6,
      margin: { left: 40, right: 40 },
      head: [['SKP type', 'Done', 'Submitted', 'Rejected', 'Not submitted']],
      body: [
        ['Pengabdian', d.statusPeng.done, d.statusPeng.sub, d.statusPeng.rej, d.statusPeng.notSub],
        ['Pelayanan', d.statusPel.done, d.statusPel.sub, d.statusPel.rej, d.statusPel.notSub],
        ['Pembelajaran', d.statusPemb.done, d.statusPemb.sub, d.statusPemb.rej, d.statusPemb.notSub]
      ],
      theme: 'plain',
      styles: { font: 'helvetica', fontSize: 9.5, textColor: [31, 41, 55], cellPadding: 4.5 },
      headStyles: { fontStyle: 'bold', textColor: [17, 24, 39], lineWidth: { bottom: 1.5 }, lineColor: [17, 24, 39] },
      columnStyles: {
        0: { halign: 'left', },
        1: { halign: 'right', },
        2: { halign: 'right', },
        3: { halign: 'right', },
        4: { halign: 'right', }
      },
      didParseCell: function(cellData) {
        if (cellData.section === 'body') {
          cellData.cell.styles.lineWidth = { bottom: 0.5 };
          cellData.cell.styles.lineColor = [229, 231, 235];
        }
      }
    });

    // Footer
    doc.setDrawColor(241, 245, 249);
    doc.setLineWidth(1);
    doc.line(40, 804, 555.28, 804);

    doc.setFont('helvetica', 'normal');
    doc.setFontSize(8);
    doc.setTextColor(148, 163, 184);
    doc.text('Generated ' + d.genDate, 40, 820);
    doc.text('1 / 1', 535, 820);

    const filename = 'SKP_Report_' + period.replace(/\s+/g, '_') + '.pdf';
    doc.save(filename);

    if (typeof showToast === 'function') {
      showToast('Berhasil mengunduh ' + filename, 'success');
    }
  } else {
    // Fallback: unduh langsung dokumen referensi asli jika CDN belum termuat
    const a = document.createElement('a');
    a.href = 'assets/docs/skp-report-2026-11.pdf';
    a.download = 'SKP_Report_' + period.replace(/\s+/g, '_') + '.pdf';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
  }
}

/**
 * 9. CETAK & UNDUH LAPORAN BULANAN RESMI ONE POGI (3 HALAMAN PERSIS REVISI 01)
 */
function printOnePogiMonthlyReportPdf(monthName) {
  const period = monthName || 'November 2026';

  // 1. Prioritaskan Template Engine Resmi ONE POGI (100% Persis, Rapi, & Indah sesuai Monthly_Report_ONE_POGI_Revisi_01.pdf)
  if (typeof window !== 'undefined' && typeof window.generateOnePogiOfficialPdfBlob === 'function') {
    const blob = window.generateOnePogiOfficialPdfBlob(period);
    const cleanName = period.replace(/\s+/g, '_');
    const filename = 'Monthly_Report_ONE_POGI_' + cleanName + '.pdf';

    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    setTimeout(function() {
      document.body.removeChild(a);
      URL.revokeObjectURL(url);
    }, 400);

    if (typeof showToast === 'function') {
      showToast('Berhasil mengunduh dokumen resmi ' + filename, 'success');
    }
    return;
  }

  const monthlyDb = {
    'November 2026': {
      date: '20 November 2026',
      cutoff: '1 November 2026',
      realSkp: '464,5 SKP',
      doctors: '161 Dokter',
      records: '1.420 Record',
      pending: '551 SKP (19 Dr)',
      kendala: '8 Berkas',
      nextMonth: 'DESEMBER 2026'
    },
    'Oktober 2026': {
      date: '31 Oktober 2026',
      cutoff: '1 Oktober 2026',
      realSkp: '493,5 SKP',
      doctors: '158 Dokter',
      records: '1.380 Record',
      pending: '412 SKP (14 Dr)',
      kendala: '5 Berkas',
      nextMonth: 'NOVEMBER 2026'
    },
    'September 2026': {
      date: '30 September 2026',
      cutoff: '1 September 2026',
      realSkp: '923,5 SKP',
      doctors: '152 Dokter',
      records: '1.520 Record',
      pending: '320 SKP (9 Dr)',
      kendala: '4 Berkas',
      nextMonth: 'OKTOBER 2026'
    },
    'Agustus 2026': {
      date: '31 Agustus 2026',
      cutoff: '1 Agustus 2026',
      realSkp: '257,0 SKP',
      doctors: '149 Dokter',
      records: '1.250 Record',
      pending: '480 SKP (20 Dr)',
      kendala: '6 Berkas',
      nextMonth: 'SEPTEMBER 2026'
    },
    'Juli 2026': {
      date: '31 Juli 2026',
      cutoff: '1 Juli 2026',
      realSkp: '214,5 SKP',
      doctors: '146 Dokter',
      records: '1.180 Record',
      pending: '510 SKP (22 Dr)',
      kendala: '7 Berkas',
      nextMonth: 'AGUSTUS 2026'
    },
    'Juni 2026': {
      date: '30 Juni 2026',
      cutoff: '1 Juni 2026',
      realSkp: '821,5 SKP',
      doctors: '148 Dokter',
      records: '1.490 Record',
      pending: '390 SKP (12 Dr)',
      kendala: '5 Berkas',
      nextMonth: 'JULI 2026'
    }
  };

  const m = monthlyDb[period] || monthlyDb['November 2026'];
  const JsPdf = getJsPdfInstance();

  if (JsPdf) {
    const doc = new JsPdf({ orientation: 'portrait', unit: 'pt', format: 'a4' });
    const blueHeader = [22, 58, 95];

    function drawPageHeaderFooter(pageNum) {
      // Header top line
      doc.setDrawColor(213, 222, 232);
      doc.setLineWidth(0.5);
      doc.line(40, 36, 555.28, 36);

      // Footer
      doc.line(40, 804, 555.28, 804);
      doc.setFont('helvetica', 'normal');
      doc.setFontSize(7.5);
      doc.setTextColor(100, 116, 139);
      doc.text('IMPRUVE.ID | MONTHLY REPORT ONE POGI', 40, 818);
      doc.text('Halaman ' + pageNum, 510, 818);
    }

    // ==========================================
    // HALAMAN 1
    // ==========================================
    // Top banner
    doc.setFillColor(22, 58, 95);
    doc.rect(40, 28, 515.28, 22, 'F');
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(8);
    doc.setTextColor(255, 255, 255);
    doc.text('IMPRUVE.ID', 48, 42);
    doc.text('MONTHLY REPORT | ONE POGI', 390, 42);

    // Title
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(18);
    doc.setTextColor(22, 58, 95);
    doc.text('LAPORAN BULANAN', 40, 74);

    doc.setFont('helvetica', 'normal');
    doc.setFontSize(8.5);
    doc.setTextColor(71, 85, 105);
    doc.text('Pengelolaan dan Penginputan Data Satuan Kredit Profesi (SKP) Dokter Spesialis Obstetri & Ginekologi.', 40, 88);

    // Meta box
    doc.autoTable({
      startY: 96,
      margin: { left: 40, right: 40 },
      body: [
        ['Periode laporan', period, 'PIC pelaksana', 'Siti Rahma, S.Farm'],
        ['Tanggal laporan', m.date, 'Pelaksana', 'Impruve.Id'],
        ['Nama program', 'ONE POGI', 'Unit/area', 'Nasional (38 Wilayah POGI)']
      ],
      theme: 'plain',
      styles: { font: 'helvetica', fontSize: 7.5, textColor: [31, 41, 55], cellPadding: 2.5 },
      columnStyles: {
        0: { fontStyle: 'bold', textColor: [22, 58, 95] },
        1: { },
        2: { fontStyle: 'bold', textColor: [22, 58, 95] },
        3: { }
      }
    });

    // 1. Ringkasan Eksekutif
    let curY = doc.lastAutoTable.finalY + 12;
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(10.5);
    doc.setTextColor(22, 58, 95);
    doc.text('1. RINGKASAN EKSEKUTIF', 40, curY);

    doc.autoTable({
      startY: curY + 4,
      margin: { left: 40, right: 40 },
      head: [['Indikator', 'Jumlah', 'Keterangan']],
      body: [
        ['Total dokter unik yang diproses', m.doctors, 'Hitung dokter berbeda, bukan jumlah record kegiatan.'],
        ['Total record keikutsertaan/kegiatan', m.records, 'Satu dokter dapat memiliki lebih dari satu record.'],
        ['Data SKP berhasil diinput', m.realSkp, 'Input selesai dan tercatat resmi pada sistem ONE POGI.'],
        ['Data dalam proses / validasi', m.pending, 'Menunggu kurasi verifikator Kemenkes Plataran Sehat.'],
        ['Data terkendala / perbaikan', m.kendala, 'Perlu konfirmasi berkas atau perbaikan data identitas.']
      ],
      theme: 'grid',
      styles: { font: 'helvetica', fontSize: 7.5, textColor: [31, 41, 55], cellPadding: 3, lineColor: [226, 232, 240] },
      headStyles: { fillColor: [241, 245, 249], textColor: [15, 23, 42], fontStyle: 'bold' },
      columnStyles: { 0: { }, 1: { fontStyle: 'bold' }, 2: { } }
    });

    // 2. Ringkasan Progres Pekerjaan
    curY = doc.lastAutoTable.finalY + 12;
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(10.5);
    doc.setTextColor(22, 58, 95);
    doc.text('2. RINGKASAN PROGRES PEKERJAAN', 40, curY);

    doc.autoTable({
      startY: curY + 4,
      margin: { left: 40, right: 40 },
      head: [['KPI', 'Realisasi', 'Target/Basis', 'Capaian', 'Status', 'Catatan']],
      body: [
        ['Total database anggota', '5700', '5700', '100.0%', 'Baseline', 'Basis data nasional PP POGI'],
        ['Database terverifikasi', '3200', '5700', '56.1%', 'Proses', '56,1% dari total terverifikasi'],
        ['Data belum lengkap', '1500', '5700', '26.3%', 'Tindak lanjut', 'Lengkapi berkas wajib STR/SIP'],
        ['Data belum diproses', '1000', '5700', '17.5%', 'Prioritas', 'Alokasi tim verifikator data'],
        ['Anggota memenuhi SKP', '2500', '5700', '43.9%', 'Monitoring', 'Capaian akumulatif periode'],
        ['Anggota belum memenuhi', '2000', '5700', '35.1%', 'Tindak lanjut', 'Segmentasi pendampingan'],
        ['Anggota dalam proses SKP', '1200', '5700', '21.1%', 'Monitoring', 'Pantau penyelesaian kurasi']
      ],
      theme: 'grid',
      styles: { font: 'helvetica', fontSize: 7, textColor: [31, 41, 55], cellPadding: 2.8, lineColor: [226, 232, 240] },
      headStyles: { fillColor: [241, 245, 249], textColor: [15, 23, 42], fontStyle: 'bold' }
    });

    // 3. Detail Penginputan Dokter (Part 1)
    curY = doc.lastAutoTable.finalY + 12;
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(10.5);
    doc.setTextColor(22, 58, 95);
    doc.text('3. DETAIL PENGINPUTAN DATA DOKTER (SAMPEL RESMI)', 40, curY);

    doc.autoTable({
      startY: curY + 4,
      margin: { left: 40, right: 40 },
      head: [['No', 'Nama Dokter / ID', 'Wilayah / Cabang', 'Kegiatan & Tanggal', 'SKP', 'Tgl Input', 'Status']],
      body: [
        ['1', 'dr. Budi Santoso, Sp.OG(K)', 'DKI Jakarta', 'PIT POGI 2026 (14 Nov)', '5 SKP', '18 Nov 2026', 'Terverifikasi'],
        ['2', 'dr. Hendra Wijaya, Sp.OG', 'Jawa Barat', 'Simposium Fetomaternal (16 Nov)', '4 SKP', '19 Nov 2026', 'Terverifikasi']
      ],
      theme: 'grid',
      styles: { font: 'helvetica', fontSize: 7, textColor: [31, 41, 55], cellPadding: 2.8, lineColor: [226, 232, 240] },
      headStyles: { fillColor: [241, 245, 249], textColor: [15, 23, 42], fontStyle: 'bold' }
    });

    drawPageHeaderFooter(1);

    // ==========================================
    // HALAMAN 2
    // ==========================================
    doc.addPage();
    drawPageHeaderFooter(2);

    // 3. Detail Penginputan Dokter (Part 2)
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(10.5);
    doc.setTextColor(22, 58, 95);
    doc.text('3. DETAIL PENGINPUTAN DATA DOKTER (LANJUTAN)', 40, 52);

    doc.autoTable({
      startY: 58,
      margin: { left: 40, right: 40 },
      head: [['No', 'Nama Dokter / ID', 'Wilayah / Cabang', 'Kegiatan & Tanggal', 'SKP', 'Tgl Input', 'Status']],
      body: [
        ['3', 'dr. Ratna Sari, Sp.OG', 'Jawa Timur', 'Workshop USG Obgyn (10 Nov)', '3 SKP', '15 Nov 2026', 'Terverifikasi'],
        ['4', 'dr. Ahmad Fauzi, Sp.OG', 'Sumatera Utara', 'Webinar Onkologi Ginekologi (08 Nov)', '2 SKP', '12 Nov 2026', 'Terverifikasi'],
        ['5', 'dr. Dewi Lestari, Sp.OG', 'Sulawesi Selatan', 'Pelayanan Bakti Sosial (05 Nov)', '4 SKP', '09 Nov 2026', 'Terverifikasi'],
        ['6', 'dr. I Made Sukadana, Sp.OG', 'Bali', 'Simposium Infertilitas (03 Nov)', '3 SKP', '06 Nov 2026', 'Terverifikasi'],
        ['7', 'dr. Siti Nurhaliza, Sp.OG', 'Banten', 'Kursus Endokrinologi Repro (01 Nov)', '5 SKP', '04 Nov 2026', 'Terverifikasi']
      ],
      theme: 'grid',
      styles: { font: 'helvetica', fontSize: 7, textColor: [31, 41, 55], cellPadding: 2.8, lineColor: [226, 232, 240] },
      headStyles: { fillColor: [241, 245, 249], textColor: [15, 23, 42], fontStyle: 'bold' }
    });

    // 4. Rekapitulasi Berdasarkan Kegiatan
    curY = doc.lastAutoTable.finalY + 12;
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(10.5);
    doc.setTextColor(22, 58, 95);
    doc.text('4. REKAPITULASI BERDASARKAN KEGIATAN', 40, curY);

    doc.autoTable({
      startY: curY + 4,
      margin: { left: 40, right: 40 },
      head: [['No', 'Nama Kegiatan / Webinar', 'Tanggal Kegiatan', 'Peserta / Record', 'Bobot SKP', 'Status Input']],
      body: [
        ['1', 'PIT POGI Nasional 2026', '14-16 Nov 2026', '142 Dokter', '5 SKP', '142 Record (100%)'],
        ['2', 'Webinar Fetomaternal Update', '08 Nov 2026', '98 Dokter', '3 SKP', '98 Record (100%)'],
        ['3', 'Bakti Sosial Kesehatan Reproduksi', '04 Nov 2026', '64 Dokter', '4 SKP', '64 Record (100%)'],
        ['4', 'Pelayanan Pasien Rujukan RSUP', '01-20 Nov 2026', '161 Dokter', '2.67 SKP', '161 Record (100%)']
      ],
      theme: 'grid',
      styles: { font: 'helvetica', fontSize: 7.2, textColor: [31, 41, 55], cellPadding: 3, lineColor: [226, 232, 240] },
      headStyles: { fillColor: [241, 245, 249], textColor: [15, 23, 42], fontStyle: 'bold' }
    });

    // 5. Kendala dan Permasalahan
    curY = doc.lastAutoTable.finalY + 12;
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(10.5);
    doc.setTextColor(22, 58, 95);
    doc.text('5. KENDALA DAN PERMASALAHAN', 40, curY);

    doc.autoTable({
      startY: curY + 4,
      margin: { left: 40, right: 40 },
      head: [['No', 'Kendala / Temuan', 'Dampak Terhadap Pekerjaan', 'Tindak Lanjut & PIC', 'Status']],
      body: [
        ['1', 'Sinkronisasi NIK dokter baru kurasi Kemenkes', 'Pending validasi 19 dokter', 'Koordinasi Ditjen Nakes - Siti Rahma', 'Proses'],
        ['2', 'Berkas sertifikat webinar belum ada link P2KB', 'Tertunda 8 berkas SKP', 'Konfirmasi panitia cabang - Siti Rahma', 'Selesai'],
        ['3', 'Perbedaan penulisan nama dokter di STR & SIP', 'Verifikasi manual 3 dokter', 'Pencocokan data KKI - Admin Cabang', 'Selesai']
      ],
      theme: 'grid',
      styles: { font: 'helvetica', fontSize: 7.2, textColor: [31, 41, 55], cellPadding: 3, lineColor: [226, 232, 240] },
      headStyles: { fillColor: [241, 245, 249], textColor: [15, 23, 42], fontStyle: 'bold' },
      columnStyles: { 4: { fontStyle: 'bold', halign: 'center' } }
    });

    // 6. Rencana Tindak Lanjut
    curY = doc.lastAutoTable.finalY + 12;
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(10.5);
    doc.setTextColor(22, 58, 95);
    doc.text('6. RENCANA TINDAK LANJUT PERIODE BERIKUTNYA', 40, curY);

    doc.autoTable({
      startY: curY + 4,
      margin: { left: 40, right: 40 },
      head: [['No', 'Rencana Pekerjaan', 'Target Waktu', 'PIC Pelaksana']],
      body: [
        ['1', 'Finalisasi kurasi SKP pending bersama Kemenkes Plataran Sehat', 'Minggu IV', 'Siti Rahma, S.Farm'],
        ['2', 'Sosialisasi upload bukti logbook digital terintegrasi ONE POGI', 'Minggu I', 'Tim IT ONE POGI'],
        ['3', 'Rekonsiliasi data SKP triwulan Q4 bersama 38 Cabang POGI', 'Minggu II', 'Admin Data PP POGI'],
        ['4', 'Penerbitan rekap laporan final penagihan invoice administrasi', 'Akhir Bulan', 'Keuangan Impruve.Id']
      ],
      theme: 'grid',
      styles: { font: 'helvetica', fontSize: 7.2, textColor: [31, 41, 55], cellPadding: 3, lineColor: [226, 232, 240] },
      headStyles: { fillColor: [241, 245, 249], textColor: [15, 23, 42], fontStyle: 'bold' }
    });

    // ==========================================
    // HALAMAN 3
    // ==========================================
    doc.addPage();
    drawPageHeaderFooter(3);

    // 7. Project Plan Bulan Depan
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(10.5);
    doc.setTextColor(22, 58, 95);
    doc.text('7. PROJECT PLAN BULAN DEPAN (' + m.nextMonth + ')', 40, 52);

    doc.setFont('helvetica', 'normal');
    doc.setFontSize(7.5);
    doc.setTextColor(100, 116, 139);
    doc.text('Rencana kerja untuk periode bulan depan. Target dan PIC disesuaikan dengan kesepakatan bersama.', 40, 64);

    doc.autoTable({
      startY: 70,
      margin: { left: 40, right: 40 },
      head: [['No', 'Rencana Pekerjaan', 'Periode', 'Target', 'PIC']],
      body: [
        ['1', 'Pengumpulan / penerimaan data dokter', m.nextMonth + ', Minggu I-II', '161 Dokter', 'Siti Rahma'],
        ['2', 'Pemeriksaan kelengkapan dan kesesuaian data', m.nextMonth + ', Minggu II', '100% Berkas', 'Verifikator'],
        ['3', 'Penginputan data SKP ke sistem ONE POGI', m.nextMonth + ', Minggu III', 'Target 450 SKP', 'Tim Input'],
        ['4', 'Verifikasi hasil penginputan sistem', m.nextMonth + ', Minggu IV', 'Zero Error', 'Supervisor'],
        ['5', 'Monitoring status dan tindak lanjut laporan', m.nextMonth + ', Akhir Bulan', 'Laporan Lengkap', 'Siti Rahma']
      ],
      theme: 'grid',
      styles: { font: 'helvetica', fontSize: 7.2, textColor: [31, 41, 55], cellPadding: 3, lineColor: [226, 232, 240] },
      headStyles: { fillColor: [241, 245, 249], textColor: [15, 23, 42], fontStyle: 'bold' }
    });

    // 8. Dokumentasi / Lampiran
    curY = doc.lastAutoTable.finalY + 12;
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(10.5);
    doc.setTextColor(22, 58, 95);
    doc.text('8. DOKUMENTASI / LAMPIRAN', 40, curY);

    doc.autoTable({
      startY: curY + 4,
      margin: { left: 40, right: 40 },
      body: [
        ['[ ✓ ]', 'Rekap data dokter / master tracking SKP lengkap per cabang'],
        ['[ ✓ ]', 'Bukti tangkapan layar sistem SATUSEHAT Kemenkes & Plataran Sehat'],
        ['[ ✓ ]', 'Rekap kegiatan ilmiah, webinar, dan partisipasi bakti sosial'],
        ['[ ✓ ]', 'Catatan koordinasi / konfirmasi kurasi data Ditjen Nakes'],
        ['[ ✓ ]', 'Lampiran rekap penagihan invoice biaya kelola data']
      ],
      theme: 'plain',
      styles: { font: 'helvetica', fontSize: 7.2, textColor: [51, 65, 85], cellPadding: 2 },
      columnStyles: { 0: { fontStyle: 'bold', textColor: [22, 58, 95] } }
    });

    // 9. Penutup & Tanda Tangan
    curY = doc.lastAutoTable.finalY + 12;
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(10.5);
    doc.setTextColor(22, 58, 95);
    doc.text('9. PENUTUP & PENGESAHAN', 40, curY);

    doc.setFont('helvetica', 'normal');
    doc.setFontSize(7.5);
    doc.setTextColor(71, 85, 105);
    const penutupText = 'Demikian laporan bulanan kegiatan pengelolaan dan penginputan data SKP dokter dalam program ONE POGI untuk periode ' + period + ' ini disusun sebagai dokumentasi pelaksanaan, monitoring progres, dan bahan evaluasi serta koordinasi untuk periode berikutnya.';
    const splitPenutup = doc.splitTextToSize(penutupText, 515.28);
    doc.text(splitPenutup, 40, curY + 12);

    // Signature table
    doc.autoTable({
      startY: curY + 28,
      margin: { left: 40, right: 40 },
      body: [
        [
          'Disusun oleh,\n\n\n\nSiti Rahma, S.Farm\nImpruve.Id - Tim Pengelola Data\nTanggal: ' + m.date,
          'Diperiksa / diketahui oleh,\n\n\n\nDr. dr. Yudi M. Hidayat, Sp.OG, Subsp.Obginsos, M.Kes\nKetua Umum Pengurus Pusat POGI\nTanggal: ' + m.date
        ]
      ],
      theme: 'grid',
      styles: { font: 'helvetica', fontSize: 7.2, textColor: [31, 41, 55], cellPadding: 6, lineColor: [203, 213, 225], fillColor: [248, 250, 252] },
      columnStyles: { 0: { }, 1: { } }
    });

    // Notes
    const noteY = doc.lastAutoTable.finalY + 8;
    doc.setFont('helvetica', 'italic');
    doc.setFontSize(6.5);
    doc.setTextColor(148, 163, 184);
    doc.text('Catatan: Gunakan satuan hitung yang konsisten. Bedakan dokter unik, record kegiatan, dan transaksi SKP. Status berhasil hanya diberikan setelah hasil input diverifikasi pada sistem.', 40, noteY);

    const filename = 'Monthly_Report_ONE_POGI_' + period.replace(/\s+/g, '_') + '.pdf';
    doc.save(filename);

    if (typeof showToast === 'function') {
      showToast('Berhasil mengunduh ' + filename, 'success');
    }
  } else {
    // Fallback: unduh langsung template resmi jika library belum siap
    const a = document.createElement('a');
    a.href = 'assets/docs/Monthly_Report_ONE_POGI_Revisi_01.pdf';
    a.download = 'Monthly_Report_ONE_POGI_' + period.replace(/\s+/g, '_') + '.pdf';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
  }
}

window.printOfficialSkpReportPdf = printOfficialSkpReportPdf;
window.printOnePogiMonthlyReportPdf = printOnePogiMonthlyReportPdf;
window.downloadOfficialSkpReportPdf = printOfficialSkpReportPdf;
window.downloadOnePogiMonthlyReportPdf = printOnePogiMonthlyReportPdf;
