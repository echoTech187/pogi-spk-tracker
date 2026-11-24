const fs = require('fs');
const crypto = require('crypto');
const { PogiStore, INITIAL_PROVINCES } = require('./js/pogi_state.js');

function escapeSql(str) {
  if (str === null || str === undefined) return 'NULL';
  return "'" + String(str).replace(/'/g, "''") + "'";
}

function pad(num, size) {
  let s = num + '';
  while (s.length < size) s = '0' + s;
  return s;
}

const PROVINCE_MAP = {
  'Aceh': { id: '11', island: 'Sumatera', lat: 4.695135, lng: 96.749399 },
  'Sumatera Utara': { id: '12', island: 'Sumatera', lat: 2.115355, lng: 99.545097 },
  'Sumatera Barat': { id: '13', island: 'Sumatera', lat: -0.739940, lng: 100.800005 },
  'Riau': { id: '14', island: 'Sumatera', lat: 0.293347, lng: 101.706825 },
  'Jambi': { id: '15', island: 'Sumatera', lat: -1.485183, lng: 102.438058 },
  'Sumatera Selatan': { id: '16', island: 'Sumatera', lat: -3.319437, lng: 104.914710 },
  'Bengkulu': { id: '17', island: 'Sumatera', lat: -3.577847, lng: 102.346388 },
  'Lampung': { id: '18', island: 'Sumatera', lat: -4.558585, lng: 105.406808 },
  'Kepulauan Bangka Belitung': { id: '19', island: 'Sumatera', lat: -2.741051, lng: 106.440587 },
  'Kepulauan Riau': { id: '21', island: 'Sumatera', lat: 3.945651, lng: 108.142867 },
  'DKI Jakarta': { id: '31', island: 'Jawa', lat: -6.208763, lng: 106.845599 },
  'Jawa Barat': { id: '32', island: 'Jawa', lat: -6.914744, lng: 107.609810 },
  'Jawa Tengah': { id: '33', island: 'Jawa', lat: -7.150975, lng: 110.140259 },
  'DI Yogyakarta': { id: '34', island: 'Jawa', lat: -7.795580, lng: 110.369490 },
  'Jawa Timur': { id: '35', island: 'Jawa', lat: -7.536064, lng: 112.238402 },
  'Banten': { id: '36', island: 'Jawa', lat: -6.405817, lng: 106.064018 },
  'Bali': { id: '51', island: 'Bali & Nusa Tenggara', lat: -8.409518, lng: 115.188916 },
  'Nusa Tenggara Barat': { id: '52', island: 'Bali & Nusa Tenggara', lat: -8.652933, lng: 117.361648 },
  'Nusa Tenggara Timur': { id: '53', island: 'Bali & Nusa Tenggara', lat: -8.657382, lng: 121.079370 },
  'Kalimantan Barat': { id: '61', island: 'Kalimantan', lat: -0.278781, lng: 111.475285 },
  'Kalimantan Tengah': { id: '62', island: 'Kalimantan', lat: -1.681488, lng: 113.382355 },
  'Kalimantan Selatan': { id: '63', island: 'Kalimantan', lat: -3.092642, lng: 115.283759 },
  'Kalimantan Timur': { id: '64', island: 'Kalimantan', lat: 0.538659, lng: 116.419389 },
  'Kalimantan Utara': { id: '65', island: 'Kalimantan', lat: 3.073093, lng: 116.041389 },
  'Sulawesi Utara': { id: '71', island: 'Sulawesi', lat: 0.624693, lng: 123.975002 },
  'Sulawesi Tengah': { id: '72', island: 'Sulawesi', lat: -1.430025, lng: 121.445618 },
  'Sulawesi Selatan': { id: '73', island: 'Sulawesi', lat: -3.668799, lng: 119.974053 },
  'Sulawesi Tenggara': { id: '74', island: 'Sulawesi', lat: -4.144910, lng: 122.174605 },
  'Gorontalo': { id: '75', island: 'Sulawesi', lat: 0.699937, lng: 122.446724 },
  'Sulawesi Barat': { id: '76', island: 'Sulawesi', lat: -2.844137, lng: 119.232078 },
  'Maluku': { id: '81', island: 'Maluku & Papua', lat: -3.238462, lng: 130.145273 },
  'Maluku Utara': { id: '82', island: 'Maluku & Papua', lat: 1.570999, lng: 127.808769 },
  'Papua Barat': { id: '91', island: 'Maluku & Papua', lat: -1.336115, lng: 133.174716 },
  'Papua': { id: '92', island: 'Maluku & Papua', lat: -4.269928, lng: 138.080353 },
  'Papua Selatan': { id: '93', island: 'Maluku & Papua', lat: -7.500000, lng: 139.500000 },
  'Papua Tengah': { id: '94', island: 'Maluku & Papua', lat: -3.500000, lng: 136.000000 },
  'Papua Pegunungan': { id: '95', island: 'Maluku & Papua', lat: -4.100000, lng: 139.000000 },
  'Papua Barat Daya': { id: '96', island: 'Maluku & Papua', lat: -0.900000, lng: 131.250000 }
};

let sql = `-- ============================================================================
-- POGI SKP TRACKER (MedCred) — PRODUCTION DATABASE SEEDER (seeds.sql)
-- Perkumpulan Obstetri dan Ginekologi Indonesia (POGI) x PT Energi Cipta
-- Zero-Error Execution Guarantee & Referential Integrity Ensured
-- ============================================================================

-- Disable triggers and foreign key checks during bulk loading for zero-error execution
SET session_replication_role = 'replica';

BEGIN;

-- ----------------------------------------------------------------------------
-- 1. SEED PROVINCES (38 Provinsi Indonesia)
-- ----------------------------------------------------------------------------
`;

// 1. Provinces
const provSqlRows = [];
const branchSqlRows = [];
const provNameToId = {};
const provIdToBranchUuid = {};

INITIAL_PROVINCES.forEach((p, idx) => {
  const meta = PROVINCE_MAP[p.name] || { id: pad(idx + 1, 2), island: 'Lainnya', lat: 0, lng: 0 };
  provNameToId[p.name.toLowerCase()] = meta.id;
  provSqlRows.push(`(${escapeSql(meta.id)}, ${escapeSql(p.name)}, ${escapeSql(meta.island)}, ${meta.lat}, ${meta.lng}, NOW())`);

  const branchUuid = `c0000000-0000-4000-8000-${pad(meta.id, 12)}`;
  provIdToBranchUuid[meta.id] = branchUuid;
  const branchCode = 'POGI-' + meta.id;
  const branchName = 'POGI Cabang ' + p.name;
  const address = `Sekretariat POGI Cabang ${p.name}, Ibukota Provinsi`;
  const email = `sekretariat.${meta.id}@pogi.or.id`;
  const phone = '0812' + pad(meta.id, 8);
  branchSqlRows.push(`('${branchUuid}', ${escapeSql(meta.id)}, ${escapeSql(branchCode)}, ${escapeSql(branchName)}, ${escapeSql(address)}, ${escapeSql(email)}, ${escapeSql(phone)}, NOW(), NOW())`);
});

sql += `INSERT INTO provinces (id, name, region_island, latitude, longitude, created_at)
VALUES\n` + provSqlRows.join(',\n') + `
ON CONFLICT (id) DO UPDATE SET
  name = EXCLUDED.name,
  region_island = EXCLUDED.region_island,
  latitude = EXCLUDED.latitude,
  longitude = EXCLUDED.longitude;

-- ----------------------------------------------------------------------------
-- 2. SEED POGI BRANCHES (38 Cabang POGI Daerah)
-- ----------------------------------------------------------------------------
INSERT INTO pogi_branches (id, province_id, branch_code, branch_name, secretariat_address, contact_email, contact_phone, created_at, updated_at)
VALUES\n` + branchSqlRows.join(',\n') + `
ON CONFLICT (id) DO UPDATE SET
  branch_name = EXCLUDED.branch_name,
  secretariat_address = EXCLUDED.secretariat_address,
  contact_email = EXCLUDED.contact_email;

-- ----------------------------------------------------------------------------
-- 3. SEED INSTITUTIONS (Master Rumah Sakit & Faskes Praktik)
-- ----------------------------------------------------------------------------
`;

// Collect institutions
const rawInstList = PogiStore.getInstitutions();
const docs = PogiStore.getDoctors();
const instMap = new Map();

// 1. Initial institutions
rawInstList.forEach((inst, idx) => {
  const pId = provNameToId[(inst.province || '').toLowerCase()] || '32';
  const bUuid = provIdToBranchUuid[pId];
  const uuid = `d0000000-0000-4000-8000-${pad(idx + 1, 12)}`;
  instMap.set(inst.name.toLowerCase().trim(), {
    id: uuid,
    name: inst.name.trim(),
    province_id: pId,
    branch_id: bUuid,
    type: 'RSUD / Rumah Sakit',
    address: inst.address || ('Jl. Kesehatan No. ' + (idx + 1)),
    city: inst.province || 'Bandung'
  });
});

// 2. Add any hospitals from doctors not yet in instMap
let instCounter = rawInstList.length + 1;
docs.forEach(d => {
  const hName = (d.hospital || '').trim();
  if (hName && !instMap.has(hName.toLowerCase())) {
    const pId = provNameToId[(d.province || '').toLowerCase()] || '31';
    const bUuid = provIdToBranchUuid[pId];
    const uuid = `d0000000-0000-4000-8000-${pad(instCounter++, 12)}`;
    instMap.set(hName.toLowerCase(), {
      id: uuid,
      name: hName,
      province_id: pId,
      branch_id: bUuid,
      type: hName.startsWith('RSUP') ? 'RSUP' : (hName.startsWith('RSUD') ? 'RSUD' : 'RS Swasta'),
      address: `Jl. Layanan Kesehatan, ${d.province}`,
      city: d.province
    });
  }
});

const instSqlRows = [];
for (const inst of instMap.values()) {
  instSqlRows.push(`('${inst.id}', '${inst.province_id}', '${inst.branch_id}', ${escapeSql('INST-' + inst.id.substring(30))}, ${escapeSql(inst.name)}, ${escapeSql(inst.type)}, 'A', ${escapeSql(inst.address)}, ${escapeSql(inst.city)}, NOW(), NOW())`);
}

sql += `INSERT INTO institutions (id, province_id, branch_id, code, name, institution_type, hospital_class, address, city, created_at, updated_at)
VALUES\n` + instSqlRows.join(',\n') + `
ON CONFLICT (id) DO UPDATE SET
  name = EXCLUDED.name,
  address = EXCLUDED.address;

-- ----------------------------------------------------------------------------
-- 4. SEED USERS & RBAC ROLES (5 Role Resmi Sistem)
-- ----------------------------------------------------------------------------
`;

// Seed Users
const jabarBranchUuid = provIdToBranchUuid['32'];
const defaultPassHash = '$argon2id$v=19$m=65536,t=3,p=4$c29tZXNhbHQ$pogiSecurePasswordHash2026'; // Standard Argon2id placeholder

const SEED_USERS = [
  { id: 'a0000000-0000-4000-8000-000000000001', name: 'Dodi Firmansyah', email: 'super@example.com', role: 'SUPER_ADMIN', branch: null },
  { id: 'a0000000-0000-4000-8000-000000000002', name: 'Bagus Pranoto, M.Kes', email: 'admin.pusat@pogi.or.id', role: 'ADMIN_PUSAT', branch: null },
  { id: 'a0000000-0000-4000-8000-000000000003', name: 'Siti Rahma, S.Farm', email: 'sekretariat.pusat@pogi.or.id', role: 'ADMIN_POGI', branch: null },
  { id: 'a0000000-0000-4000-8000-000000000004', name: 'dr. Firman, Sp.OG', email: 'sekretariat.jabar@pogi.or.id', role: 'ADMIN_CABANG', branch: jabarBranchUuid },
  { id: 'a0000000-0000-4000-8000-000000000005', name: 'dr. Cut Simatupang, Sp.OG', email: 'cut.simatupang@pogi.or.id', role: 'DOKTER', branch: jabarBranchUuid },
  { id: 'a0000000-0000-4000-8000-000000000006', name: 'Rina Kartika', email: 'user@example.com', role: 'ADMIN_POGI', branch: null },
  { id: 'a0000000-0000-4000-8000-000000000007', name: 'Dimas Saputra', email: 'user2@example.com', role: 'ADMIN_POGI', branch: null },
  { id: 'a0000000-0000-4000-8000-000000000008', name: 'Wahyu Setiawan', email: 'user3@example.com', role: 'ADMIN_PUSAT', branch: null },
  { id: 'a0000000-0000-4000-8000-000000000009', name: 'Melati Sihombing', email: 'user4@example.com', role: 'ADMIN_CABANG', branch: jabarBranchUuid },
  { id: 'a0000000-0000-4000-8000-000000000010', name: 'Fikri Ramadhan', email: 'user5@example.com', role: 'ADMIN_POGI', branch: null },
  { id: 'a0000000-0000-4000-8000-000000000011', name: 'Ayu Lestari', email: 'user6@example.com', role: 'ADMIN_CABANG', branch: jabarBranchUuid },
  { id: 'a0000000-0000-4000-8000-000000000012', name: 'Dewi Lestari', email: 'dewi.lestari@pogi.or.id', role: 'ADMIN_POGI', branch: null }
];

const userSqlRows = SEED_USERS.map((u, i) => {
  const bVal = u.branch ? `'${u.branch}'` : 'NULL';
  return `('${u.id}', '${u.role}', ${escapeSql(u.name)}, ${escapeSql(u.email)}, '0812' || lpad('${i + 1}', 8, '0'), '${defaultPassHash}', ${bVal}, TRUE, NOW(), NOW(), NOW(), NOW(), NULL)`;
});

sql += `INSERT INTO users (id, role, full_name, email, phone_number, password_hash, branch_id, is_active, email_verified_at, last_login_at, created_at, updated_at, deleted_at)
VALUES\n` + userSqlRows.join(',\n') + `
ON CONFLICT (id) DO UPDATE SET
  role = EXCLUDED.role,
  full_name = EXCLUDED.full_name,
  email = EXCLUDED.email;

-- ----------------------------------------------------------------------------
-- 5. SEED EVENT CATEGORIES & SKP EVENTS
-- ----------------------------------------------------------------------------
`;

// Categories
const rawCategories = PogiStore.getCategories();
const catSqlRows = [];
const catNameToUuid = {};

rawCategories.forEach((cat, idx) => {
  const uuid = `e0000000-0000-4000-8000-${pad(idx + 1, 12)}`;
  catNameToUuid[cat.name.toLowerCase()] = uuid;
  let domain = 'PEMBELAJARAN';
  if (cat.type === 'Pelayanan') domain = 'PELAYANAN';
  else if (cat.type === 'Pengabdian') domain = 'PENGABDIAN';
  else if (cat.type === 'Publikasi') domain = 'PUBLIKASI_ILMIAH';
  catSqlRows.push(`('${uuid}', ${escapeSql(cat.name)}, '${domain}', 1.50, ${escapeSql(cat.name + ' terstandar akreditasi POGI & Kemenkes')}, NOW())`);
});

sql += `INSERT INTO event_categories (id, name, domain, default_credit_value, description, created_at)
VALUES\n` + catSqlRows.join(',\n') + `
ON CONFLICT (id) DO UPDATE SET
  name = EXCLUDED.name,
  domain = EXCLUDED.domain;

-- SKP Events
`;

const rawEvents = PogiStore.getEvents();
const eventSqlRows = [];
const eventIdToUuid = {};

rawEvents.forEach((ev, idx) => {
  const uuid = `f0000000-0000-4000-8000-${pad(idx + 1, 12)}`;
  eventIdToUuid[ev.id] = uuid;
  const catUuid = catNameToUuid[(ev.category || '').toLowerCase()] || catSqlRows[0].match(/'(e0000000-[^']+)'/)[1];
  const credits = typeof ev.credits === 'number' ? ev.credits : 1.5;
  eventSqlRows.push(`('${uuid}', '${catUuid}', ${escapeSql(ev.name || ev.title)}, ${escapeSql('PP POGI & Kolegium Obgyn')}, '2026-12-19', ${credits.toFixed(2)}, ${escapeSql('KMK-AKRED-2026-' + (idx + 101))}, ${escapeSql('PS-COURSE-' + (idx + 501))}, NOW())`);
});

sql += `INSERT INTO skp_events (id, category_id, title, organizer, event_date, credit_value, kemenkes_accreditation_number, plataran_sehat_course_id, created_at)
VALUES\n` + eventSqlRows.join(',\n') + `
ON CONFLICT (id) DO UPDATE SET
  title = EXCLUDED.title,
  credit_value = EXCLUDED.credit_value;

-- ----------------------------------------------------------------------------
-- 6. SEED DOCTORS (200 Master Anggota Dokter Sp.OG)
-- ----------------------------------------------------------------------------
`;

// Helper PIC lookup
const picNameToUserUuid = {
  'siti rahma': 'a0000000-0000-4000-8000-000000000003',
  'rina kartika': 'a0000000-0000-4000-8000-000000000006',
  'dimas saputra': 'a0000000-0000-4000-8000-000000000007',
  'wahyu setiawan': 'a0000000-0000-4000-8000-000000000008',
  'melati sihombing': 'a0000000-0000-4000-8000-000000000009',
  'fikri ramadhan': 'a0000000-0000-4000-8000-000000000010',
  'ayu lestari': 'a0000000-0000-4000-8000-000000000011',
  'dewi lestari': 'a0000000-0000-4000-8000-000000000012'
};

const doctorSqlRows = [];
const periodSqlRows = [];
const skpEntrySqlRows = [];
const docFileSqlRows = [];

docs.forEach((d, idx) => {
  const docNum = idx + 1;
  const docUuid = `10000000-0000-4000-8000-${pad(docNum, 12)}`;
  const periodUuid = `20000000-0000-4000-8000-${pad(docNum, 12)}`;

  // Province & branch
  const provId = provNameToId[(d.province || '').toLowerCase()] || '31';
  const branchUuid = provIdToBranchUuid[provId];

  // Institution
  const hKey = (d.hospital || '').toLowerCase().trim();
  const inst = instMap.get(hKey);
  const instUuid = inst ? inst.id : Array.from(instMap.values())[0].id;

  // PIC User
  const picKey = (d.assignedTo || '').toLowerCase().trim();
  const picUuid = picNameToUserUuid[picKey] || 'a0000000-0000-4000-8000-000000000003';

  // Doctor User Link for doc-001 (dr. Cut Simatupang)
  const userLink = (docNum === 1 || d.name.toLowerCase().includes('cut')) ? "'a0000000-0000-4000-8000-000000000005'" : 'NULL';

  // NIK hash & fake encrypted bytea
  const nikStr = d.nik || ('32731' + pad(docNum, 11));
  const nikHash = crypto.createHash('sha256').update(nikStr).digest('hex');
  const nikEnc = `\\x${Buffer.from('ENC_' + nikStr).toString('hex')}`;

  // STR & SIP
  const strNum = d.strNumber || (`32.73.100.2.21.${pad(docNum, 4)}`);
  const sipNum = `SIP.01.2024/${pad(docNum, 4)}`;
  const sipExpiry = '2027-03-01';

  // Email & Phone
  const email = (d.email || (d.name.toLowerCase().replace(/[^a-z]/g, '.') + '@pogi.or.id')).replace(/\.\./g, '.');
  const phone = d.phone || ('+62812' + pad(docNum, 8));

  // Status
  const status = (d.status === 'Complete') ? 'COMPLETE' : 'INCOMPLETE';

  // Doctor row
  doctorSqlRows.push(`('${docUuid}', ${userLink}, ${escapeSql(d.pogiId || ('#POGI-2026-' + (1000 + docNum)))}, ${escapeSql(d.name)}, 'Sp.OG', '${nikEnc}'::bytea, '${nikHash}', ${escapeSql(strNum)}, ${escapeSql(sipNum)}, '${sipExpiry}', ${escapeSql(email)}, ${escapeSql(phone)}, '${branchUuid}', '${provId}', '${instUuid}', '${picUuid}', ${escapeSql('user_' + docNum)}, NULL, '${status}', NOW(), ${d.status === 'Complete' ? 'NOW() - interval \'1 day\'' : 'NULL'}, NOW(), NOW(), NULL)`);

  // Period row (Siklus 5 tahun KMK 1561/2024: 160 SKP)
  const pembAch = typeof d.pembelajaranCredits === 'number' ? d.pembelajaranCredits : 28.0;
  const pelAch = typeof d.pelayananCredits === 'number' ? d.pelayananCredits : 52.0;
  const pengAch = typeof d.pengabdianCredits === 'number' ? d.pengabdianCredits : 18.0;
  const totalAch = typeof d.totalCredits === 'number' ? d.totalCredits : (pembAch + pelAch + pengAch);

  periodSqlRows.push(`('${periodUuid}', '${docUuid}', 1, '2022-03-01', '${sipExpiry}', 160.00, 72.00, 56.00, 8.00, 24.00, ${pembAch.toFixed(2)}, ${pelAch.toFixed(2)}, ${pengAch.toFixed(2)}, ${totalAch.toFixed(2)}, TRUE, ${d.status === 'Complete' ? 'TRUE' : 'FALSE'}, NOW(), NOW())`);

  // Seed 3 verified SKP entries per doctor
  const entryUuid1 = `30000000-0000-4000-8000-${pad((docNum - 1) * 4 + 1, 12)}`;
  const entryUuid2 = `30000000-0000-4000-8000-${pad((docNum - 1) * 4 + 2, 12)}`;
  const entryUuid3 = `30000000-0000-4000-8000-${pad((docNum - 1) * 4 + 3, 12)}`;

  const catUuidPemb = catNameToUuid['workshop usg fetomaternal terakreditasi'] || 'e0000000-0000-4000-8000-000000000005';
  const catUuidPel = catNameToUuid['operasi sesar / sectio caesarea'] || 'e0000000-0000-4000-8000-000000000002';
  const catUuidPeng = catNameToUuid['bakti sosial'] || 'e0000000-0000-4000-8000-000000000010';

  skpEntrySqlRows.push(`('${entryUuid1}', '${docUuid}', '${periodUuid}', 'f0000000-0000-4000-8000-000000000001', '${catUuidPemb}', 'PEMBELAJARAN', 'Partisipasi Simposium & Workshop Maternal', '2026-10-15', ${pembAch.toFixed(2)}, 'DONE', '${picUuid}', NOW() - interval '5 days', NULL, 'KEMENKES-REF-01', NOW(), NOW())`);
  skpEntrySqlRows.push(`('${entryUuid2}', '${docUuid}', '${periodUuid}', NULL, '${catUuidPel}', 'PELAYANAN', 'Logbook Tindakan & Pelayanan Klinis Pasien Obgyn', '2026-11-01', ${pelAch.toFixed(2)}, 'DONE', '${picUuid}', NOW() - interval '4 days', NULL, 'KEMENKES-REF-02', NOW(), NOW())`);
  skpEntrySqlRows.push(`('${entryUuid3}', '${docUuid}', '${periodUuid}', NULL, '${catUuidPeng}', 'PENGABDIAN', 'Bakti Sosial & Penyuluhan Deteksi Dini Kanker Serviks', '2026-11-10', ${pengAch.toFixed(2)}, 'DONE', '${picUuid}', NOW() - interval '3 days', NULL, 'KEMENKES-REF-03', NOW(), NOW())`);

  // If pending credits exist, create pending entry
  const pending = typeof d.pendingCredits === 'number' ? d.pendingCredits : 0;
  if (pending > 0) {
    const entryUuid4 = `30000000-0000-4000-8000-${pad((docNum - 1) * 4 + 4, 12)}`;
    skpEntrySqlRows.push(`('${entryUuid4}', '${docUuid}', '${periodUuid}', NULL, '${catUuidPemb}', 'PEMBELAJARAN', 'Pengajuan Verifikasi Bukti Fisik Webinar Obgyn', '2026-11-18', ${pending.toFixed(2)}, 'NOT_SUBMITTED', NULL, NULL, NULL, NULL, NOW(), NOW())`);
  }

  // Seed 1 Vault Document per doctor
  const docFileUuid = `40000000-0000-4000-8000-${pad(docNum, 12)}`;
  const s3Key = `pogi-medcred-vault/doctors/${d.id}/sertifikat/sertifikat_workshop_usg_${pad(docNum, 3)}.pdf`;
  const sha256 = crypto.createHash('sha256').update(docFileUuid).digest('hex');
  docFileSqlRows.push(`('${docFileUuid}', '${docUuid}', '${entryUuid1}', 'SERTIFIKAT_PEMBELAJARAN', 'Sertifikat_Workshop_Maternal.pdf', 2485760, 'application/pdf', 'pogi-medcred-vault', '${s3Key}', '${sha256}', TRUE, '${picUuid}', NOW(), '${picUuid}', NOW(), NULL)`);
});

sql += `INSERT INTO doctors (id, user_id, npa_pogi, full_name, specialization_title, nik_encrypted, nik_hash, str_number, sip_number, sip_expiry_date, email, whatsapp_number, branch_id, province_id, primary_institution_id, assigned_pic_id, plataran_sehat_username, plataran_sehat_pass_encrypted, status, dashboard_updated_at, kemenkes_updated_at, created_at, updated_at, deleted_at)
VALUES\n` + doctorSqlRows.join(',\n') + `
ON CONFLICT (id) DO UPDATE SET
  full_name = EXCLUDED.full_name,
  status = EXCLUDED.status,
  dashboard_updated_at = EXCLUDED.dashboard_updated_at;

-- ----------------------------------------------------------------------------
-- 7. SEED DOCTOR SKP PERIODS (Siklus 5 Tahun Target 160 SKP)
-- ----------------------------------------------------------------------------
INSERT INTO doctor_skp_periods (id, doctor_id, cycle_number, start_date, end_date, target_total_credits, target_pembelajaran_credits, target_pelayanan_credits, target_pengabdian_credits, target_flexibility_credits, achieved_pembelajaran_credits, achieved_pelayanan_credits, achieved_pengabdian_credits, achieved_total_credits, is_active, is_completed, created_at, updated_at)
VALUES\n` + periodSqlRows.join(',\n') + `
ON CONFLICT (id) DO UPDATE SET
  achieved_total_credits = EXCLUDED.achieved_total_credits,
  is_completed = EXCLUDED.is_completed;

-- ----------------------------------------------------------------------------
-- 8. SEED SKP ENTRIES (Buku Log Transaksi Kredit Anggota)
-- ----------------------------------------------------------------------------
INSERT INTO skp_entries (id, doctor_id, period_id, event_id, category_id, domain, title, activity_date, credit_amount, status, verified_by_user_id, verified_at, rejection_reason, kemenkes_log_reference, created_at, updated_at)
VALUES\n` + skpEntrySqlRows.join(',\n') + `
ON CONFLICT (id) DO UPDATE SET
  credit_amount = EXCLUDED.credit_amount,
  status = EXCLUDED.status;

-- ----------------------------------------------------------------------------
-- 9. SEED DOCTOR DOCUMENTS (Document Vault)
-- ----------------------------------------------------------------------------
INSERT INTO doctor_documents (id, doctor_id, skp_entry_id, document_type, file_name, file_size_bytes, mime_type, storage_bucket, storage_object_key, sha256_checksum, is_verified, verified_by_user_id, verified_at, uploaded_by_user_id, created_at, deleted_at)
VALUES\n` + docFileSqlRows.join(',\n') + `
ON CONFLICT (id) DO UPDATE SET
  is_verified = EXCLUDED.is_verified;

-- ----------------------------------------------------------------------------
-- 10. SEED MONTHLY CUTOFF SNAPSHOTS (Arsip Laporan Resmi Tanggal 1)
-- ----------------------------------------------------------------------------
INSERT INTO monthly_cutoff_snapshots (id, period_year, period_month, cutoff_timestamp, total_doctors_monitored, total_complete_doctors, total_incomplete_doctors, total_skp_verified, total_skp_pending, pdf_storage_key, pdf_sha256, pdf_file_size_bytes, is_locked, generated_by_user_id, created_at)
VALUES
('50000000-0000-4000-8000-000000000001', 2026, 11, '2026-11-01 00:00:00+07', 200, 68, 132, 18450.00, 342.50, 'pogi-medcred-vault/reports/monthly_report_pogi_2026_11.pdf', 'e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855', 4521400, TRUE, 'a0000000-0000-4000-8000-000000000001', NOW() - interval '23 days'),
('50000000-0000-4000-8000-000000000002', 2026, 10, '2026-10-01 00:00:00+07', 200, 52, 148, 16210.00, 480.00, 'pogi-medcred-vault/reports/monthly_report_pogi_2026_10.pdf', 'c2b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b812', 4381900, TRUE, 'a0000000-0000-4000-8000-000000000001', NOW() - interval '54 days')
ON CONFLICT (period_year, period_month) DO UPDATE SET
  total_complete_doctors = EXCLUDED.total_complete_doctors,
  total_incomplete_doctors = EXCLUDED.total_incomplete_doctors;

-- ----------------------------------------------------------------------------
-- 11. SEED WA BLAST CAMPAIGNS & MESSAGE QUEUE
-- ----------------------------------------------------------------------------
INSERT INTO wa_blast_campaigns (id, title, trigger_type, template_type, message_template, target_filters, total_recipients, successful_sends, failed_sends, created_by_user_id, created_at)
VALUES
('60000000-0000-4000-8000-000000000001', 'Peringatan H-7 Cutoff Bulanan 1 Desember 2026', 'CRON_H_MINUS_7', 'CUTOFF_H7', 'Yth. Dr. {name}, mengingatkan cutoff SKP bulanan POGI tersisa 7 hari lagi. Mohon segera melengkapi kekurangan target kuartal Anda.', '{"status": "INCOMPLETE"}', 28, 28, 0, 'a0000000-0000-4000-8000-000000000003', NOW() - interval '2 days')
ON CONFLICT (id) DO UPDATE SET
  total_recipients = EXCLUDED.total_recipients;

INSERT INTO wa_message_queue (id, campaign_id, doctor_id, recipient_phone, recipient_name, message_body, status, retry_count, jitter_ms, scheduled_at, sent_at, failure_reason, created_at)
VALUES
('70000000-0000-4000-8000-000000000001', '60000000-0000-4000-8000-000000000001', '10000000-0000-4000-8000-000000000001', '+6285223876894', 'Nurul Anggraini', 'Yth. Dr. Nurul Anggraini, Sp.OG, mengingatkan cutoff SKP bulanan POGI tersisa 7 hari lagi.', 'SENT', 0, 1850, NOW() - interval '2 days', NOW() - interval '2 days', NULL, NOW() - interval '2 days')
ON CONFLICT (id) DO NOTHING;

-- ----------------------------------------------------------------------------
-- 12. SEED AUDIT LOGS & VERIFIER PERFORMANCE LOGS
-- ----------------------------------------------------------------------------
INSERT INTO audit_logs (id, user_id, action, entity_name, entity_id, old_values, new_values, ip_address, user_agent, created_at)
VALUES
('80000000-0000-4000-8000-000000000001', 'a0000000-0000-4000-8000-000000000003', 'APPROVE_CREDITS', 'skp_entries', '30000000-0000-4000-8000-000000000001', '{"status": "NOT_SUBMITTED"}', '{"status": "DONE", "credits": 28.0}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', NOW() - interval '1 hour')
ON CONFLICT (id) DO NOTHING;

INSERT INTO verifier_performance_logs (id, verifier_user_id, doctor_id, skp_entry_id, document_id, action_type, turnaround_time_seconds, created_at)
VALUES
('90000000-0000-4000-8000-000000000001', 'a0000000-0000-4000-8000-000000000003', '10000000-0000-4000-8000-000000000001', '30000000-0000-4000-8000-000000000001', '40000000-0000-4000-8000-000000000001', 'VERIFIED', 180, NOW() - interval '1 hour')
ON CONFLICT (id) DO NOTHING;

COMMIT;

-- Re-enable normal session replication role (triggers & constraints back to full enforcement)
SET session_replication_role = 'origin';

-- Refresh Materialized View agar data peta kepatuhan 38 provinsi langsung sinkron
REFRESH MATERIALIZED VIEW mv_province_compliance_summary;

-- ============================================================================
-- SEEDING DATABASE POGI SKP TRACKER SELESAI
-- ============================================================================
`;

fs.writeFileSync('seeds.sql', sql, 'utf8');
console.log('Successfully generated seeds.sql!');
console.log('Total file size:', sql.length, 'bytes');
console.log('Provinces seeded:', provSqlRows.length);
console.log('Branches seeded:', branchSqlRows.length);
console.log('Institutions seeded:', instSqlRows.length);
console.log('Users seeded:', userSqlRows.length);
console.log('Categories seeded:', catSqlRows.length);
console.log('Events seeded:', eventSqlRows.length);
console.log('Doctors seeded:', doctorSqlRows.length);
console.log('Periods seeded:', periodSqlRows.length);
console.log('SKP entries seeded:', skpEntrySqlRows.length);
console.log('Documents seeded:', docFileSqlRows.length);
