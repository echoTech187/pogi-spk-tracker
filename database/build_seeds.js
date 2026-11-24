const fs = require('fs');
const crypto = require('crypto');
const { PogiStore, INITIAL_PROVINCES } = require('../js/pogi_state.js');

function escapeSql(str) {
  if (str === null || str === undefined) return 'NULL';
  return "'" + String(str).replace(/'/g, "''") + "'";
}

function pad(num, size) {
  let s = num + '';
  while (s.length < size) s = '0' + s;
  return s;
}

// RFC 9562 Compliant UUIDv7 Generator
// Format: xxxxxxxx-xxxx-7xxx-yxxx-xxxxxxxxxxxx
// - 48-bit Unix timestamp in milliseconds
// - 4-bit Version = 0x7
// - 12-bit Entity domain counter
// - 2-bit Variant = 10xx (0x8)
// - 62-bit Monotonic sequence counter
function makeUUIDv7(entityTypeInt, seqInt, timeOffsetMs = 0) {
  const ts = BigInt(1732432200000 + timeOffsetMs);
  const tsHex = ts.toString(16).padStart(12, '0');
  const timeHigh = tsHex.slice(0, 8);
  const timeMid = tsHex.slice(8, 12);
  const verRandA = '7' + (entityTypeInt & 0x0fff).toString(16).padStart(3, '0');
  const varRandB = '8' + ((seqInt >> 12) & 0x0fff).toString(16).padStart(3, '0');
  const nodeHex = (BigInt(seqInt) & 0xffffffffffffn).toString(16).padStart(12, '0');
  return `${timeHigh}-${timeMid}-${verRandA}-${varRandB}-${nodeHex}`;
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
-- Zero-Error Execution Guarantee & RFC 9562 Compliant UUIDv7 Primary Keys
-- ============================================================================

-- Disable triggers and foreign key checks during bulk loading for zero-error execution
SET session_replication_role = 'replica';

BEGIN;

-- ----------------------------------------------------------------------------
-- 1. SEED PROVINCES (38 Provinsi Indonesia)
-- ----------------------------------------------------------------------------
`;

// 1. Provinces & Branches
const provSqlRows = [];
const branchSqlRows = [];
const provNameToId = {};
const provIdToBranchUuid = {};

INITIAL_PROVINCES.forEach((p, idx) => {
  const meta = PROVINCE_MAP[p.name] || { id: pad(idx + 1, 2), island: 'Lainnya', lat: 0, lng: 0 };
  provNameToId[p.name.toLowerCase()] = meta.id;
  provSqlRows.push(`(${escapeSql(meta.id)}, ${escapeSql(p.name)}, ${escapeSql(meta.island)}, ${meta.lat}, ${meta.lng}, NOW())`);

  const branchUuid = makeUUIDv7(1, parseInt(meta.id, 10));
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
  const uuid = makeUUIDv7(2, idx + 1);
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
    const uuid = makeUUIDv7(2, instCounter++);
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
const defaultPassHash = '$argon2id$v=19$m=65536,t=3,p=4$c29tZXNhbHQ$pogiSecurePasswordHash2026';

const SEED_USERS = [
  { id: makeUUIDv7(3, 1), name: 'Dodi Firmansyah', email: 'super@example.com', role: 'SUPER_ADMIN', branch: null },
  { id: makeUUIDv7(3, 2), name: 'Bagus Pranoto, M.Kes', email: 'admin.pusat@pogi.or.id', role: 'ADMIN_PUSAT', branch: null },
  { id: makeUUIDv7(3, 3), name: 'Siti Rahma, S.Farm', email: 'sekretariat.pusat@pogi.or.id', role: 'ADMIN_POGI', branch: null },
  { id: makeUUIDv7(3, 4), name: 'dr. Firman, Sp.OG', email: 'sekretariat.jabar@pogi.or.id', role: 'ADMIN_CABANG', branch: jabarBranchUuid },
  { id: makeUUIDv7(3, 5), name: 'dr. Cut Simatupang, Sp.OG', email: 'cut.simatupang@pogi.or.id', role: 'DOKTER', branch: jabarBranchUuid },
  { id: makeUUIDv7(3, 6), name: 'Rina Kartika', email: 'user@example.com', role: 'ADMIN_POGI', branch: null },
  { id: makeUUIDv7(3, 7), name: 'Dimas Saputra', email: 'user2@example.com', role: 'ADMIN_POGI', branch: null },
  { id: makeUUIDv7(3, 8), name: 'Wahyu Setiawan', email: 'user3@example.com', role: 'ADMIN_PUSAT', branch: null },
  { id: makeUUIDv7(3, 9), name: 'Melati Sihombing', email: 'user4@example.com', role: 'ADMIN_CABANG', branch: jabarBranchUuid },
  { id: makeUUIDv7(3, 10), name: 'Fikri Ramadhan', email: 'user5@example.com', role: 'ADMIN_POGI', branch: null },
  { id: makeUUIDv7(3, 11), name: 'Ayu Lestari', email: 'user6@example.com', role: 'ADMIN_CABANG', branch: jabarBranchUuid },
  { id: makeUUIDv7(3, 12), name: 'Dewi Lestari', email: 'dewi.lestari@pogi.or.id', role: 'ADMIN_POGI', branch: null }
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
  const uuid = makeUUIDv7(4, idx + 1);
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
`;

// SKP Events
const rawEvents = PogiStore.getEvents();
const eventSqlRows = [];
const eventIdToUuid = {};

rawEvents.forEach((ev, idx) => {
  const uuid = makeUUIDv7(5, idx + 1);
  eventIdToUuid[ev.id] = uuid;
  const catUuid = catNameToUuid[(ev.category || '').toLowerCase()] || catSqlRows[0].match(/'([^']+)'/)[1];
  const credits = typeof ev.credits === 'number' ? ev.credits : 1.5;
  const evTitle = escapeSql(ev.name || ev.title);
  const evOrg = escapeSql('PP POGI & Kolegium Obgyn');
  const evAkred = escapeSql('KMK-AKRED-2026-' + (idx + 101));
  const evPs = escapeSql('PS-COURSE-' + (idx + 501));
  eventSqlRows.push(`('${uuid}', '${catUuid}', ${evTitle}, ${evOrg}, '2026-12-19', ${credits.toFixed(2)}, ${evAkred}, ${evPs}, NOW())`);
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
  'siti rahma': SEED_USERS[2].id,
  'rina kartika': SEED_USERS[5].id,
  'dimas saputra': SEED_USERS[6].id,
  'wahyu setiawan': SEED_USERS[7].id,
  'melati sihombing': SEED_USERS[8].id,
  'fikri ramadhan': SEED_USERS[9].id,
  'ayu lestari': SEED_USERS[10].id,
  'dewi lestari': SEED_USERS[11].id
};

const doctorSqlRows = [];
const periodSqlRows = [];
const skpEntrySqlRows = [];
const docFileSqlRows = [];

docs.forEach((d, idx) => {
  const docNum = idx + 1;
  const docUuid = makeUUIDv7(6, docNum);
  const periodUuid = makeUUIDv7(7, docNum);

  // Province & branch
  const provId = provNameToId[(d.province || '').toLowerCase()] || '31';
  const branchUuid = provIdToBranchUuid[provId];

  // Institution
  const hKey = (d.hospital || '').toLowerCase().trim();
  const inst = instMap.get(hKey);
  const instUuid = inst ? inst.id : Array.from(instMap.values())[0].id;

  // PIC User
  const picKey = (d.assignedTo || '').toLowerCase().trim();
  const picUuid = picNameToUserUuid[picKey] || SEED_USERS[2].id;

  // Doctor User Link for doc-001 (dr. Cut Simatupang)
  const userLink = (docNum === 1 || d.name.toLowerCase().includes('cut')) ? `'${SEED_USERS[4].id}'` : 'NULL';

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
  const entryUuid1 = makeUUIDv7(8, (docNum - 1) * 4 + 1);
  const entryUuid2 = makeUUIDv7(8, (docNum - 1) * 4 + 2);
  const entryUuid3 = makeUUIDv7(8, (docNum - 1) * 4 + 3);

  const catUuidPemb = catNameToUuid['workshop usg fetomaternal terakreditasi'] || catSqlRows[0].match(/'([^']+)'/)[1];
  const catUuidPel = catNameToUuid['operasi sesar / sectio caesarea'] || catSqlRows[1].match(/'([^']+)'/)[1];
  const catUuidPeng = catNameToUuid['bakti sosial'] || catSqlRows[2].match(/'([^']+)'/)[1];

  skpEntrySqlRows.push(`('${entryUuid1}', '${docUuid}', '${periodUuid}', '${rawEvents[0] ? eventIdToUuid[rawEvents[0].id] : 'NULL'}', '${catUuidPemb}', 'PEMBELAJARAN', 'Partisipasi Simposium & Workshop Maternal', '2026-10-15', ${pembAch.toFixed(2)}, 'DONE', '${picUuid}', NOW() - interval '5 days', NULL, 'KEMENKES-REF-01', NOW(), NOW())`);
  skpEntrySqlRows.push(`('${entryUuid2}', '${docUuid}', '${periodUuid}', NULL, '${catUuidPel}', 'PELAYANAN', 'Logbook Tindakan & Pelayanan Klinis Pasien Obgyn', '2026-11-01', ${pelAch.toFixed(2)}, 'DONE', '${picUuid}', NOW() - interval '4 days', NULL, 'KEMENKES-REF-02', NOW(), NOW())`);
  skpEntrySqlRows.push(`('${entryUuid3}', '${docUuid}', '${periodUuid}', NULL, '${catUuidPeng}', 'PENGABDIAN', 'Bakti Sosial & Penyuluhan Deteksi Dini Kanker Serviks', '2026-11-10', ${pengAch.toFixed(2)}, 'DONE', '${picUuid}', NOW() - interval '3 days', NULL, 'KEMENKES-REF-03', NOW(), NOW())`);

  // If pending credits exist, create pending entry
  const pending = typeof d.pendingCredits === 'number' ? d.pendingCredits : 0;
  if (pending > 0) {
    const entryUuid4 = makeUUIDv7(8, (docNum - 1) * 4 + 4);
    skpEntrySqlRows.push(`('${entryUuid4}', '${docUuid}', '${periodUuid}', NULL, '${catUuidPemb}', 'PEMBELAJARAN', 'Pengajuan Verifikasi Bukti Fisik Webinar Obgyn', '2026-11-18', ${pending.toFixed(2)}, 'NOT_SUBMITTED', NULL, NULL, NULL, NULL, NOW(), NOW())`);
  }

  // Seed 1 Vault Document per doctor
  const docFileUuid = makeUUIDv7(9, docNum);
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
('${makeUUIDv7(10, 1)}', 2026, 11, '2026-11-01 00:00:00+07', 200, 68, 132, 18450.00, 342.50, 'pogi-medcred-vault/reports/monthly_report_pogi_2026_11.pdf', 'e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855', 4521400, TRUE, '${SEED_USERS[0].id}', NOW() - interval '23 days'),
('${makeUUIDv7(10, 2)}', 2026, 10, '2026-10-01 00:00:00+07', 200, 52, 148, 16210.00, 480.00, 'pogi-medcred-vault/reports/monthly_report_pogi_2026_10.pdf', 'c2b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b812', 4381900, TRUE, '${SEED_USERS[0].id}', NOW() - interval '54 days')
ON CONFLICT (period_year, period_month) DO UPDATE SET
  total_complete_doctors = EXCLUDED.total_complete_doctors,
  total_incomplete_doctors = EXCLUDED.total_incomplete_doctors;

-- ----------------------------------------------------------------------------
-- 11. SEED WA BLAST CAMPAIGNS & MESSAGE QUEUE
-- ----------------------------------------------------------------------------
INSERT INTO wa_blast_campaigns (id, title, trigger_type, template_type, message_template, target_filters, total_recipients, successful_sends, failed_sends, created_by_user_id, created_at)
VALUES
('${makeUUIDv7(11, 1)}', 'Peringatan H-7 Cutoff Bulanan 1 Desember 2026', 'CRON_H_MINUS_7', 'CUTOFF_H7', 'Yth. Dr. {name}, mengingatkan cutoff SKP bulanan POGI tersisa 7 hari lagi. Mohon segera melengkapi kekurangan target kuartal Anda.', '{"status": "INCOMPLETE"}', 28, 28, 0, '${SEED_USERS[2].id}', NOW() - interval '2 days')
ON CONFLICT (id) DO UPDATE SET
  total_recipients = EXCLUDED.total_recipients;

INSERT INTO wa_message_queue (id, campaign_id, doctor_id, recipient_phone, recipient_name, message_body, status, retry_count, jitter_ms, scheduled_at, sent_at, failure_reason, created_at)
VALUES
('${makeUUIDv7(12, 1)}', '${makeUUIDv7(11, 1)}', '${doctorSqlRows[0].match(/'([^']+)'/)[1]}', '+6285223876894', 'Nurul Anggraini', 'Yth. Dr. Nurul Anggraini, Sp.OG, mengingatkan cutoff SKP bulanan POGI tersisa 7 hari lagi.', 'SENT', 0, 1850, NOW() - interval '2 days', NOW() - interval '2 days', NULL, NOW() - interval '2 days')
ON CONFLICT (id) DO NOTHING;

-- ----------------------------------------------------------------------------
-- 12. SEED AUDIT LOGS & VERIFIER PERFORMANCE LOGS
-- ----------------------------------------------------------------------------
INSERT INTO audit_logs (id, user_id, action, entity_name, entity_id, old_values, new_values, ip_address, user_agent, created_at)
VALUES
('${makeUUIDv7(13, 1)}', '${SEED_USERS[2].id}', 'APPROVE_CREDITS', 'skp_entries', '${makeUUIDv7(8, 1)}', '{"status": "NOT_SUBMITTED"}', '{"status": "DONE", "credits": 28.0}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', NOW() - interval '1 hour')
ON CONFLICT (id) DO NOTHING;

INSERT INTO verifier_performance_logs (id, verifier_user_id, doctor_id, skp_entry_id, document_id, action_type, turnaround_time_seconds, created_at)
VALUES
('${makeUUIDv7(14, 1)}', '${SEED_USERS[2].id}', '${doctorSqlRows[0].match(/'([^']+)'/)[1]}', '${makeUUIDv7(8, 1)}', '${makeUUIDv7(9, 1)}', 'VERIFIED', 180, NOW() - interval '1 hour')
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
fs.writeFileSync('database/seeds.sql', sql, 'utf8');
console.log('Successfully generated seeds.sql and database/seeds.sql!');
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
