/**
 * POGI SKP Tracker - State Management & Mock Database
 * Perkumpulan Obstetri dan Ginekologi Indonesia
 * Standarisasi 160 SKP / 5 Tahun (3 Subbidang: Pelayanan, Pengabdian, Pembelajaran)
 */

const POGI_STANDARDS = {
  TOTAL_5_YEARS_TARGET: 160,
  ANNUAL_TARGET: 32,
  QUARTERLY_TARGET: 8,
  SUBBIDANG_MIN: {
    PELAYANAN: 80,    // Min 50%
    PENGABDIAN: 30,   // Min ~18.75%
    PEMBELAJARAN: 50  // Min ~31.25%
  }
};

const INITIAL_PROVINCES = [
  { id: 'prov-01', name: 'DKI Jakarta', doctors: 22, complete: 8, incomplete: 14, completionRate: 36.4 },
  { id: 'prov-02', name: 'Jawa Timur', doctors: 8, complete: 2, incomplete: 6, completionRate: 25.0 },
  { id: 'prov-03', name: 'Jawa Barat', doctors: 8, complete: 2, incomplete: 6, completionRate: 25.0 },
  { id: 'prov-04', name: 'Jawa Tengah', doctors: 11, complete: 3, incomplete: 8, completionRate: 27.3 },
  { id: 'prov-05', name: 'Sumatera Utara', doctors: 10, complete: 2, incomplete: 8, completionRate: 20.0 },
  { id: 'prov-06', name: 'Bali', doctors: 10, complete: 0, incomplete: 10, completionRate: 0.0 },
  { id: 'prov-07', name: 'Sulawesi Selatan', doctors: 12, complete: 3, incomplete: 9, completionRate: 25.0 },
  { id: 'prov-08', name: 'DI Yogyakarta', doctors: 7, complete: 2, incomplete: 5, completionRate: 28.6 },
  { id: 'prov-09', name: 'Banten', doctors: 8, complete: 2, incomplete: 6, completionRate: 25.0 },
  { id: 'prov-10', name: 'Sumatera Barat', doctors: 4, complete: 2, incomplete: 2, completionRate: 50.0 },
  { id: 'prov-11', name: 'Riau', doctors: 6, complete: 1, incomplete: 5, completionRate: 16.7 },
  { id: 'prov-12', name: 'Kalimantan Timur', doctors: 6, complete: 1, incomplete: 5, completionRate: 16.7 },
  { id: 'prov-13', name: 'Sumatera Selatan', doctors: 6, complete: 1, incomplete: 5, completionRate: 16.7 },
  { id: 'prov-14', name: 'Lampung', doctors: 5, complete: 1, incomplete: 4, completionRate: 20.0 },
  { id: 'prov-15', name: 'Kalimantan Barat', doctors: 5, complete: 1, incomplete: 4, completionRate: 20.0 },
  { id: 'prov-16', name: 'Nusa Tenggara Barat', doctors: 4, complete: 1, incomplete: 3, completionRate: 25.0 },
  { id: 'prov-17', name: 'Nusa Tenggara Timur', doctors: 7, complete: 0, incomplete: 7, completionRate: 0.0 },
  { id: 'prov-18', name: 'Aceh', doctors: 3, complete: 0, incomplete: 3, completionRate: 0.0 },
  { id: 'prov-19', name: 'Kalimantan Selatan', doctors: 4, complete: 1, incomplete: 3, completionRate: 25.0 },
  { id: 'prov-20', name: 'Jambi', doctors: 7, complete: 3, incomplete: 4, completionRate: 42.9 },
  { id: 'prov-21', name: 'Kalimantan Tengah', doctors: 3, complete: 0, incomplete: 3, completionRate: 0.0 },
  { id: 'prov-22', name: 'Sulawesi Utara', doctors: 3, complete: 2, incomplete: 1, completionRate: 66.7 },
  { id: 'prov-23', name: 'Sulawesi Tengah', doctors: 3, complete: 0, incomplete: 3, completionRate: 0.0 },
  { id: 'prov-24', name: 'Kepulauan Riau', doctors: 3, complete: 1, incomplete: 2, completionRate: 33.3 },
  { id: 'prov-25', name: 'Bengkulu', doctors: 3, complete: 0, incomplete: 3, completionRate: 0.0 },
  { id: 'prov-26', name: 'Kepulauan Bangka Belitung', doctors: 3, complete: 0, incomplete: 3, completionRate: 0.0 },
  { id: 'prov-27', name: 'Sulawesi Tenggara', doctors: 3, complete: 0, incomplete: 3, completionRate: 0.0 },
  { id: 'prov-28', name: 'Kalimantan Utara', doctors: 3, complete: 2, incomplete: 1, completionRate: 66.7 },
  { id: 'prov-29', name: 'Gorontalo', doctors: 2, complete: 0, incomplete: 2, completionRate: 0.0 },
  { id: 'prov-30', name: 'Sulawesi Barat', doctors: 2, complete: 0, incomplete: 2, completionRate: 0.0 },
  { id: 'prov-31', name: 'Maluku', doctors: 2, complete: 0, incomplete: 2, completionRate: 0.0 },
  { id: 'prov-32', name: 'Maluku Utara', doctors: 2, complete: 0, incomplete: 2, completionRate: 0.0 },
  { id: 'prov-33', name: 'Papua', doctors: 4, complete: 0, incomplete: 4, completionRate: 0.0 },
  { id: 'prov-34', name: 'Papua Barat', doctors: 2, complete: 0, incomplete: 2, completionRate: 0.0 },
  { id: 'prov-35', name: 'Papua Selatan', doctors: 1, complete: 0, incomplete: 1, completionRate: 0.0 },
  { id: 'prov-36', name: 'Papua Tengah', doctors: 1, complete: 0, incomplete: 1, completionRate: 0.0 },
  { id: 'prov-37', name: 'Papua Pegunungan', doctors: 6, complete: 1, incomplete: 5, completionRate: 16.7 },
  { id: 'prov-38', name: 'Papua Barat Daya', doctors: 1, complete: 0, incomplete: 1, completionRate: 0.0 }
];

const INITIAL_DOCTORS = [
  {
    "id": "doc-001",
    "pogiId": "#POGI-2026-1001",
    "name": "Nurul Anggraini",
    "initials": "NA",
    "nik": "3298177443598979",
    "province": "DKI Jakarta",
    "hospital": "RSUP Dr. Cipto Mangunkusumo",
    "institution": "RSUP Dr. Cipto Mangunkusumo",
    "phone": "+6285223876894",
    "assignedTo": "Rina Kartika",
    "pelayananCredits": 52,
    "pengabdianCredits": 18,
    "pembelajaranCredits": 28,
    "q4Credits": 2,
    "pendingCredits": 9.5,
    "status": "Incomplete",
    "strExpiry": "1 Mar 2027",
    "validUntilLabel": "Siklus 5 Thn: Habis 2027",
    "avatar": "NA",
    "q3Credits": 4.5,
    "year2026Credits": 34.25,
    "lastActivity": "17 Nov 2026, 09.00",
    "periodLabel": "1 Mar 2022 - 1 Mar 2027",
    "q4Details": {
      "skp": {
        "achieved": 2,
        "target": 1.25,
        "redPercent": 60,
        "yellowPercent": 40
      },
      "pending": 9.5,
      "pengabdian": {
        "achieved": 0.5,
        "target": 1
      },
      "pelayanan": {
        "achieved": 0,
        "target": 0
      },
      "pembelajaran": {
        "achieved": 1.5,
        "target": 0.25
      }
    }
  },
  {
    "id": "doc-002",
    "pogiId": "#POGI-2026-1002",
    "name": "Ronald Mandagi",
    "initials": "RM",
    "nik": "3222978705844906",
    "province": "DKI Jakarta",
    "hospital": "RSUP Fatmawati",
    "institution": "RSUP Fatmawati",
    "phone": "+6285253634630",
    "assignedTo": "Rina Kartika",
    "pelayananCredits": 46,
    "pengabdianCredits": 15,
    "pembelajaranCredits": 25,
    "q4Credits": 0.5,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "1 Okt 2028",
    "validUntilLabel": "Siklus 5 Thn: Habis 2028",
    "avatar": "RM",
    "q3Credits": 3,
    "year2026Credits": 5.5,
    "lastActivity": "16 Nov 2026, 09.00",
    "periodLabel": "1 Okt 2023 - 1 Okt 2028",
    "q4Details": {
      "skp": {
        "achieved": 0.5,
        "target": 27,
        "redPercent": 5,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0.5,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 0,
        "target": 17
      },
      "pembelajaran": {
        "achieved": 0,
        "target": 8.5
      }
    }
  },
  {
    "id": "doc-003",
    "pogiId": "#POGI-2026-1003",
    "name": "Arif Fauzi",
    "initials": "AF",
    "nik": "3393593523911028",
    "province": "DKI Jakarta",
    "hospital": "RSUP Persahabatan",
    "institution": "RSUP Persahabatan",
    "phone": "+6288132960277",
    "assignedTo": "Rina Kartika",
    "pelayananCredits": 84,
    "pengabdianCredits": 31,
    "pembelajaranCredits": 53,
    "q4Credits": 7,
    "pendingCredits": 4.5,
    "status": "Complete",
    "strExpiry": "1 Jun 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "AF",
    "q3Credits": 8,
    "year2026Credits": 39.5,
    "lastActivity": "17 Nov 2026, 09.00",
    "periodLabel": "1 Jun 2024 - 1 Jun 2029",
    "q4Details": {
      "skp": {
        "achieved": 7,
        "target": 1,
        "redPercent": 0,
        "yellowPercent": 0,
        "isGreen": true
      },
      "pending": 4.5,
      "pengabdian": {
        "achieved": 0,
        "target": 0
      },
      "pelayanan": {
        "achieved": 2.5,
        "target": 1
      },
      "pembelajaran": {
        "achieved": 4.5,
        "target": 0
      }
    }
  },
  {
    "id": "doc-004",
    "pogiId": "#POGI-2026-1004",
    "name": "Cut Bakhtiar",
    "initials": "CB",
    "nik": "3674726855755122",
    "province": "DKI Jakarta",
    "hospital": "RS Pondok Indah",
    "institution": "RS Pondok Indah",
    "phone": "+6285247432030",
    "assignedTo": "Rina Kartika",
    "pelayananCredits": 86,
    "pengabdianCredits": 32,
    "pembelajaranCredits": 54,
    "q4Credits": 11,
    "pendingCredits": 5.5,
    "status": "Complete",
    "strExpiry": "1 Okt 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "CB",
    "q3Credits": 8.5,
    "year2026Credits": 43.75,
    "lastActivity": "17 Nov 2026, 09.00",
    "periodLabel": "1 Okt 2024 - 1 Okt 2029",
    "q4Details": {
      "skp": {
        "achieved": 11,
        "target": 2,
        "redPercent": 0,
        "yellowPercent": 0,
        "isGreen": true
      },
      "pending": 5.5,
      "pengabdian": {
        "achieved": 0,
        "target": 0
      },
      "pelayanan": {
        "achieved": 6.5,
        "target": 2
      },
      "pembelajaran": {
        "achieved": 4.5,
        "target": 0
      }
    }
  },
  {
    "id": "doc-005",
    "pogiId": "#POGI-2026-1005",
    "name": "Dian Aini",
    "initials": "DA",
    "nik": "3554717969193106",
    "province": "DKI Jakarta",
    "hospital": "Puskesmas Kecamatan Tebet",
    "institution": "Puskesmas Kecamatan Tebet",
    "phone": "+6285621026735",
    "assignedTo": "Rina Kartika",
    "pelayananCredits": 24,
    "pengabdianCredits": 8,
    "pembelajaranCredits": 16,
    "q4Credits": 0,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "1 Okt 2028",
    "validUntilLabel": "Siklus 5 Thn: Habis 2028",
    "avatar": "DA",
    "q3Credits": 2,
    "year2026Credits": 14.25,
    "lastActivity": "22 Jul 2026, 09.00",
    "periodLabel": "1 Okt 2023 - 1 Okt 2028",
    "q4Details": {
      "skp": {
        "achieved": 0,
        "target": 17.75,
        "redPercent": 0,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1
      },
      "pelayanan": {
        "achieved": 0,
        "target": 9
      },
      "pembelajaran": {
        "achieved": 0,
        "target": 7.75
      }
    }
  },
  {
    "id": "doc-006",
    "pogiId": "#POGI-2026-1006",
    "name": "Yohana Simatupang",
    "initials": "YS",
    "nik": "3130212096798582",
    "province": "Jawa Barat",
    "hospital": "RSUP Dr. Hasan Sadikin",
    "institution": "RSUP Dr. Hasan Sadikin",
    "phone": "+6285654819296",
    "assignedTo": "Melati Sihombing",
    "pelayananCredits": 56,
    "pengabdianCredits": 19,
    "pembelajaranCredits": 33,
    "q4Credits": 5.5,
    "pendingCredits": 1,
    "status": "Incomplete",
    "strExpiry": "1 Mei 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "YS",
    "q3Credits": 6,
    "year2026Credits": 29.25,
    "lastActivity": "17 Nov 2026, 09.00",
    "periodLabel": "1 Mei 2024 - 1 Mei 2029",
    "q4Details": {
      "skp": {
        "achieved": 5.5,
        "target": 8.25,
        "redPercent": 65,
        "yellowPercent": 35
      },
      "pending": 1,
      "pengabdian": {
        "achieved": 0.5,
        "target": 1
      },
      "pelayanan": {
        "achieved": 2,
        "target": 6.5
      },
      "pembelajaran": {
        "achieved": 3,
        "target": 0.75
      }
    }
  },
  {
    "id": "doc-007",
    "pogiId": "#POGI-2026-1007",
    "name": "Christy Sumampouw",
    "initials": "CS",
    "nik": "3540257922791812",
    "province": "Jawa Barat",
    "hospital": "RSUD Kota Bogor",
    "institution": "RSUD Kota Bogor",
    "phone": "+6287788913809",
    "assignedTo": "Melati Sihombing",
    "pelayananCredits": 32,
    "pengabdianCredits": 11,
    "pembelajaranCredits": 19,
    "q4Credits": 7.5,
    "pendingCredits": 3.5,
    "status": "Incomplete",
    "strExpiry": "1 Jul 2030",
    "validUntilLabel": "Siklus 5 Thn: Habis 2030",
    "avatar": "CS",
    "q3Credits": 5.5,
    "year2026Credits": 22.5,
    "lastActivity": "17 Nov 2026, 09.00",
    "periodLabel": "1 Jul 2025 - 1 Jul 2030",
    "q4Details": {
      "skp": {
        "achieved": 7.5,
        "target": 17,
        "redPercent": 45,
        "yellowPercent": 35
      },
      "pending": 3.5,
      "pengabdian": {
        "achieved": 0,
        "target": 1
      },
      "pelayanan": {
        "achieved": 5,
        "target": 7.5
      },
      "pembelajaran": {
        "achieved": 2.5,
        "target": 8.5
      }
    }
  },
  {
    "id": "doc-008",
    "pogiId": "#POGI-2026-1008",
    "name": "Nenden Hermawan",
    "initials": "NH",
    "nik": "3290470058265997",
    "province": "Jawa Barat",
    "hospital": "Klinik Pratama Sehat Sentosa",
    "institution": "Klinik Pratama Sehat Sentosa",
    "phone": "+6285636242039",
    "assignedTo": "Melati Sihombing",
    "pelayananCredits": 66,
    "pengabdianCredits": 22,
    "pembelajaranCredits": 38,
    "q4Credits": 9,
    "pendingCredits": 5,
    "status": "Incomplete",
    "strExpiry": "1 Des 2026",
    "validUntilLabel": "Siklus 5 Thn: Habis 2026",
    "avatar": "NH",
    "q3Credits": 6.5,
    "year2026Credits": 24,
    "lastActivity": "17 Nov 2026, 09.00",
    "periodLabel": "1 Des 2021 - 1 Des 2026",
    "q4Details": {
      "skp": {
        "achieved": 9,
        "target": 17,
        "redPercent": 50,
        "yellowPercent": 30
      },
      "pending": 5,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 5,
        "target": 9
      },
      "pembelajaran": {
        "achieved": 4,
        "target": 6.5
      }
    }
  },
  {
    "id": "doc-009",
    "pogiId": "#POGI-2026-1009",
    "name": "Asep Rosmiati",
    "initials": "AR",
    "nik": "3253618614659745",
    "province": "Banten",
    "hospital": "RSUD Banten",
    "institution": "RSUD Banten",
    "phone": "+6288127754035",
    "assignedTo": "Wahyu Setiawan",
    "pelayananCredits": 64,
    "pengabdianCredits": 21,
    "pembelajaranCredits": 39,
    "q4Credits": 9,
    "pendingCredits": 3,
    "status": "Incomplete",
    "strExpiry": "1 Des 2026",
    "validUntilLabel": "Siklus 5 Thn: Habis 2026",
    "avatar": "AR",
    "q3Credits": 7,
    "year2026Credits": 24.5,
    "lastActivity": "17 Nov 2026, 09.00",
    "periodLabel": "1 Des 2021 - 1 Des 2026",
    "q4Details": {
      "skp": {
        "achieved": 9,
        "target": 16.5,
        "redPercent": 55,
        "yellowPercent": 15
      },
      "pending": 3,
      "pengabdian": {
        "achieved": 0,
        "target": 1
      },
      "pelayanan": {
        "achieved": 5,
        "target": 11
      },
      "pembelajaran": {
        "achieved": 4,
        "target": 4.5
      }
    }
  },
  {
    "id": "doc-010",
    "pogiId": "#POGI-2026-1010",
    "name": "Kevin Susanto",
    "initials": "KS",
    "nik": "3289385554438513",
    "province": "Banten",
    "hospital": "RS Siloam Lippo Village",
    "institution": "RS Siloam Lippo Village",
    "phone": "+6282181729611",
    "assignedTo": "Wahyu Setiawan",
    "pelayananCredits": 60,
    "pengabdianCredits": 20,
    "pembelajaranCredits": 36,
    "q4Credits": 1.5,
    "pendingCredits": 2.5,
    "status": "Incomplete",
    "strExpiry": "1 Mei 2026",
    "validUntilLabel": "Siklus 5 Thn: Habis 2026",
    "avatar": "KS",
    "q3Credits": 4,
    "year2026Credits": 28.75,
    "lastActivity": "15 Nov 2026, 09.00",
    "periodLabel": "1 Mei 2021 - 1 Mei 2026",
    "q4Details": {
      "skp": {
        "achieved": 1.5,
        "target": 4.75,
        "redPercent": 30,
        "yellowPercent": 30
      },
      "pending": 2.5,
      "pengabdian": {
        "achieved": 0.5,
        "target": 1
      },
      "pelayanan": {
        "achieved": 0,
        "target": 2.5
      },
      "pembelajaran": {
        "achieved": 1,
        "target": 1.25
      }
    }
  },
  {
    "id": "doc-011",
    "pogiId": "#POGI-2026-1011",
    "name": "Prof. Dr. dr. Ratna Kusuma, Sp.OG, Subsp. FER",
    "initials": "RK",
    "nik": "3273100000005298",
    "province": "DKI Jakarta",
    "hospital": "RSUPN Dr. Cipto Mangunkusumo",
    "institution": "RSUPN Dr. Cipto Mangunkusumo",
    "phone": "+62 812-1222-1114",
    "assignedTo": "Melati Sihombing",
    "pelayananCredits": 51,
    "pengabdianCredits": 21,
    "pembelajaranCredits": 34,
    "q4Credits": 12.8,
    "pendingCredits": 1,
    "status": "Complete",
    "strExpiry": "16 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "RK",
    "q3Credits": 9.6,
    "year2026Credits": 35.2,
    "lastActivity": "06 Nov 2026, 10.07",
    "periodLabel": "16 Maret 2024 - 16 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 12.8,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 0,
        "isGreen": true
      },
      "pending": 1,
      "pengabdian": {
        "achieved": 1,
        "target": 0.5
      },
      "pelayanan": {
        "achieved": 7,
        "target": 5.5
      },
      "pembelajaran": {
        "achieved": 4.8,
        "target": 2
      }
    }
  },
  {
    "id": "doc-012",
    "pogiId": "#POGI-2026-1012",
    "name": "dr. Sri Simatupang, Sp.OG(K)",
    "initials": "SS",
    "nik": "3273100000006181",
    "province": "DKI Jakarta",
    "hospital": "RS Ibu dan Anak Harapan Kita",
    "institution": "RS Ibu dan Anak Harapan Kita",
    "phone": "+62 812-1259-1133",
    "assignedTo": "Wahyu Setiawan",
    "pelayananCredits": 52,
    "pengabdianCredits": 22,
    "pembelajaranCredits": 35,
    "q4Credits": 8,
    "pendingCredits": 0,
    "status": "Complete",
    "strExpiry": "17 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "SS",
    "q3Credits": 8.4,
    "year2026Credits": 36,
    "lastActivity": "05 Nov 2026, 11.24",
    "periodLabel": "17 November 2024 - 17 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 8,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 0,
        "isGreen": true
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 0
      },
      "pelayanan": {
        "achieved": 5,
        "target": 5
      },
      "pembelajaran": {
        "achieved": 3,
        "target": 3
      }
    }
  },
  {
    "id": "doc-013",
    "pogiId": "#POGI-2026-1013",
    "name": "dr. Mega Kurniawan, Sp.OG, Subsp. KFM",
    "initials": "MK",
    "nik": "3273100000007064",
    "province": "DKI Jakarta",
    "hospital": "RSUD Tarakan",
    "institution": "RSUD Tarakan",
    "phone": "+62 812-1296-1152",
    "assignedTo": "Siti Rahma",
    "pelayananCredits": 53,
    "pengabdianCredits": 23,
    "pembelajaranCredits": 36,
    "q4Credits": 8.8,
    "pendingCredits": 0,
    "status": "Complete",
    "strExpiry": "18 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "MK",
    "q3Credits": 8.8,
    "year2026Credits": 36.8,
    "lastActivity": "04 Nov 2026, 12.41",
    "periodLabel": "18 Maret 2024 - 18 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 8.8,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 0,
        "isGreen": true
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0.5,
        "target": 0.5
      },
      "pelayanan": {
        "achieved": 5.5,
        "target": 5.5
      },
      "pembelajaran": {
        "achieved": 2.8,
        "target": 2
      }
    }
  },
  {
    "id": "doc-014",
    "pogiId": "#POGI-2026-1014",
    "name": "dr. Cut Kusuma, Sp.OG, Subsp. Obginsos",
    "initials": "CK",
    "nik": "3273100000007947",
    "province": "DKI Jakarta",
    "hospital": "RS Fatmawati",
    "institution": "RS Fatmawati",
    "phone": "+62 812-1333-1171",
    "assignedTo": "Dewi Lestari",
    "pelayananCredits": 21,
    "pengabdianCredits": 14,
    "pembelajaranCredits": 17,
    "q4Credits": 0.9,
    "pendingCredits": 2.5,
    "status": "Incomplete",
    "strExpiry": "19 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "CK",
    "q3Credits": 1.5,
    "year2026Credits": 16.4,
    "lastActivity": "17 Nov 2026, 13.58",
    "periodLabel": "19 November 2024 - 19 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 0.9,
        "target": 8,
        "redPercent": 11,
        "yellowPercent": 31
      },
      "pending": 2.5,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 0.5,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 0.4,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-015",
    "pogiId": "#POGI-2026-1015",
    "name": "dr. Ratna Simatupang, Sp.OG, Subsp. Urogin-RE",
    "initials": "RS",
    "nik": "3273100000008830",
    "province": "DKI Jakarta",
    "hospital": "RS Siloam Semanggi",
    "institution": "RS Siloam Semanggi",
    "phone": "+62 812-1370-1190",
    "assignedTo": "Rina Kartika",
    "pelayananCredits": 22,
    "pengabdianCredits": 5,
    "pembelajaranCredits": 18,
    "q4Credits": 1.8,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "20 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "RS",
    "q3Credits": 2.3,
    "year2026Credits": 18.2,
    "lastActivity": "16 Nov 2026, 14.15",
    "periodLabel": "20 Maret 2024 - 20 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 1.8,
        "target": 8,
        "redPercent": 23,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1
      },
      "pelayanan": {
        "achieved": 1,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 0.8,
        "target": 2.6
      }
    }
  },
  {
    "id": "doc-016",
    "pogiId": "#POGI-2026-1016",
    "name": "dr. Sri Kurniawan, Sp.OG",
    "initials": "SK",
    "nik": "3273100000009713",
    "province": "DKI Jakarta",
    "hospital": "RSUPN Dr. Cipto Mangunkusumo",
    "institution": "RSUPN Dr. Cipto Mangunkusumo",
    "phone": "+62 812-1407-1209",
    "assignedTo": "Melati Sihombing",
    "pelayananCredits": 23,
    "pengabdianCredits": 6,
    "pembelajaranCredits": 19,
    "q4Credits": 2.7,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "21 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "SK",
    "q3Credits": 2.2,
    "year2026Credits": 20.1,
    "lastActivity": "15 Nov 2026, 15.32",
    "periodLabel": "21 November 2024 - 21 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 2.7,
        "target": 8,
        "redPercent": 34,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 1.5,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 1.2,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-017",
    "pogiId": "#POGI-2026-1017",
    "name": "Dr. dr. Mega Kusuma, Sp.OG, Subsp. Onk",
    "initials": "MK",
    "nik": "3273100000010596",
    "province": "DKI Jakarta",
    "hospital": "RS Ibu dan Anak Harapan Kita",
    "institution": "RS Ibu dan Anak Harapan Kita",
    "phone": "+62 812-1444-1228",
    "assignedTo": "Wahyu Setiawan",
    "pelayananCredits": 24,
    "pengabdianCredits": 7,
    "pembelajaranCredits": 20,
    "q4Credits": 3.6,
    "pendingCredits": 1.5,
    "status": "Incomplete",
    "strExpiry": "22 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "MK",
    "q3Credits": 3.2,
    "year2026Credits": 21.9,
    "lastActivity": "14 Nov 2026, 16.49",
    "periodLabel": "22 Maret 2024 - 22 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 3.6,
        "target": 8,
        "redPercent": 45,
        "yellowPercent": 19
      },
      "pending": 1.5,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 2,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 1.6,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-018",
    "pogiId": "#POGI-2026-1018",
    "name": "dr. Cut Simatupang, Sp.OG, Subsp. FER",
    "initials": "CS",
    "nik": "3273100000011479",
    "province": "DKI Jakarta",
    "hospital": "RSUD Tarakan",
    "institution": "RSUD Tarakan",
    "phone": "+62 812-1481-1247",
    "assignedTo": "Siti Rahma",
    "pelayananCredits": 52,
    "pengabdianCredits": 22,
    "pembelajaranCredits": 34,
    "q4Credits": 8.5,
    "pendingCredits": 0,
    "status": "Complete",
    "strExpiry": "23 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "CS",
    "q3Credits": 8.8,
    "year2026Credits": 35.2,
    "lastActivity": "13 Nov 2026, 08.06",
    "periodLabel": "23 November 2024 - 23 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 8.5,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 0,
        "isGreen": true
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 1,
        "target": 0
      },
      "pelayanan": {
        "achieved": 5,
        "target": 5
      },
      "pembelajaran": {
        "achieved": 2.5,
        "target": 3
      }
    }
  },
  {
    "id": "doc-019",
    "pogiId": "#POGI-2026-1019",
    "name": "Prof. Dr. dr. Ratna Kurniawan, Sp.OG(K)",
    "initials": "RK",
    "nik": "3273100000012362",
    "province": "DKI Jakarta",
    "hospital": "RS Fatmawati",
    "institution": "RS Fatmawati",
    "phone": "+62 812-1518-1266",
    "assignedTo": "Dewi Lestari",
    "pelayananCredits": 26,
    "pengabdianCredits": 9,
    "pembelajaranCredits": 22,
    "q4Credits": 5.4,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "24 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "RK",
    "q3Credits": 5.2,
    "year2026Credits": 22.6,
    "lastActivity": "12 Nov 2026, 09.23",
    "periodLabel": "24 Maret 2024 - 24 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 5.4,
        "target": 14,
        "redPercent": 39,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 3,
        "target": 7.7
      },
      "pembelajaran": {
        "achieved": 2.4,
        "target": 4.8
      }
    }
  },
  {
    "id": "doc-020",
    "pogiId": "#POGI-2026-1020",
    "name": "dr. Sri Kusuma, Sp.OG, Subsp. KFM",
    "initials": "SK",
    "nik": "3273100000013245",
    "province": "DKI Jakarta",
    "hospital": "RS Siloam Semanggi",
    "institution": "RS Siloam Semanggi",
    "phone": "+62 812-1555-1285",
    "assignedTo": "Rina Kartika",
    "pelayananCredits": 27,
    "pengabdianCredits": 10,
    "pembelajaranCredits": 8,
    "q4Credits": 6.3,
    "pendingCredits": 0.5,
    "status": "Incomplete",
    "strExpiry": "25 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "SK",
    "q3Credits": 5,
    "year2026Credits": 24.5,
    "lastActivity": "11 Nov 2026, 10.40",
    "periodLabel": "25 November 2024 - 25 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 6.3,
        "target": 8,
        "redPercent": 79,
        "yellowPercent": 6
      },
      "pending": 0.5,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 3.5,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 2.8,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-021",
    "pogiId": "#POGI-2026-1021",
    "name": "dr. Mega Simatupang, Sp.OG, Subsp. Obginsos",
    "initials": "MS",
    "nik": "3273100000014128",
    "province": "DKI Jakarta",
    "hospital": "RSUPN Dr. Cipto Mangunkusumo",
    "institution": "RSUPN Dr. Cipto Mangunkusumo",
    "phone": "+62 812-1592-1304",
    "assignedTo": "Melati Sihombing",
    "pelayananCredits": 28,
    "pengabdianCredits": 11,
    "pembelajaranCredits": 9,
    "q4Credits": 0,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "26 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "MS",
    "q3Credits": 1.5,
    "year2026Credits": 15.5,
    "lastActivity": "10 Nov 2026, 11.57",
    "periodLabel": "26 Maret 2024 - 26 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 0,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1
      },
      "pelayanan": {
        "achieved": 0,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 0,
        "target": 2.6
      }
    }
  },
  {
    "id": "doc-022",
    "pogiId": "#POGI-2026-1022",
    "name": "dr. Cut Kurniawan, Sp.OG, Subsp. Urogin-RE",
    "initials": "CK",
    "nik": "3273100000015011",
    "province": "DKI Jakarta",
    "hospital": "RS Ibu dan Anak Harapan Kita",
    "institution": "RS Ibu dan Anak Harapan Kita",
    "phone": "+62 812-1629-1323",
    "assignedTo": "Wahyu Setiawan",
    "pelayananCredits": 29,
    "pengabdianCredits": 12,
    "pembelajaranCredits": 10,
    "q4Credits": 0.9,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "27 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "CK",
    "q3Credits": 1.5,
    "year2026Credits": 17.4,
    "lastActivity": "09 Nov 2026, 12.14",
    "periodLabel": "27 November 2024 - 27 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 0.9,
        "target": 8,
        "redPercent": 11,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 0.5,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 0.4,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-023",
    "pogiId": "#POGI-2026-1023",
    "name": "dr. Ratna Kusuma, Sp.OG",
    "initials": "RK",
    "nik": "3273100000015894",
    "province": "DKI Jakarta",
    "hospital": "RSUD Tarakan",
    "institution": "RSUD Tarakan",
    "phone": "+62 812-1666-1342",
    "assignedTo": "Siti Rahma",
    "pelayananCredits": 30,
    "pengabdianCredits": 13,
    "pembelajaranCredits": 11,
    "q4Credits": 1.8,
    "pendingCredits": 2,
    "status": "Incomplete",
    "strExpiry": "10 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "RK",
    "q3Credits": 2.3,
    "year2026Credits": 19.2,
    "lastActivity": "08 Nov 2026, 13.31",
    "periodLabel": "10 Maret 2024 - 10 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 1.8,
        "target": 8,
        "redPercent": 23,
        "yellowPercent": 25
      },
      "pending": 2,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 1,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 0.8,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-024",
    "pogiId": "#POGI-2026-1024",
    "name": "dr. Sri Simatupang, Sp.OG, Subsp. Onk",
    "initials": "SS",
    "nik": "3273100000016777",
    "province": "DKI Jakarta",
    "hospital": "RS Fatmawati",
    "institution": "RS Fatmawati",
    "phone": "+62 812-1703-1361",
    "assignedTo": "Dewi Lestari",
    "pelayananCredits": 51,
    "pengabdianCredits": 23,
    "pembelajaranCredits": 32,
    "q4Credits": 8.8,
    "pendingCredits": 0,
    "status": "Complete",
    "strExpiry": "11 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "SS",
    "q3Credits": 9,
    "year2026Credits": 34.4,
    "lastActivity": "07 Nov 2026, 14.48",
    "periodLabel": "11 November 2024 - 11 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 8.8,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 0,
        "isGreen": true
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 0
      },
      "pelayanan": {
        "achieved": 5,
        "target": 5
      },
      "pembelajaran": {
        "achieved": 3.8,
        "target": 3
      }
    }
  },
  {
    "id": "doc-025",
    "pogiId": "#POGI-2026-1025",
    "name": "Dr. dr. Mega Kurniawan, Sp.OG, Subsp. FER",
    "initials": "MK",
    "nik": "3273100000017660",
    "province": "DKI Jakarta",
    "hospital": "RS Siloam Semanggi",
    "institution": "RS Siloam Semanggi",
    "phone": "+62 812-1740-1380",
    "assignedTo": "Rina Kartika",
    "pelayananCredits": 32,
    "pengabdianCredits": 5,
    "pembelajaranCredits": 13,
    "q4Credits": 3.6,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "12 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "MK",
    "q3Credits": 3.2,
    "year2026Credits": 19.9,
    "lastActivity": "06 Nov 2026, 15.05",
    "periodLabel": "12 Maret 2024 - 12 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 3.6,
        "target": 8,
        "redPercent": 45,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 2,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 1.6,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-026",
    "pogiId": "#POGI-2026-1026",
    "name": "dr. Cut Kusuma, Sp.OG(K)",
    "initials": "CK",
    "nik": "3273100000018543",
    "province": "DKI Jakarta",
    "hospital": "RSUPN Dr. Cipto Mangunkusumo",
    "institution": "RSUPN Dr. Cipto Mangunkusumo",
    "phone": "+62 812-1777-1399",
    "assignedTo": "Melati Sihombing",
    "pelayananCredits": 33,
    "pengabdianCredits": 6,
    "pembelajaranCredits": 14,
    "q4Credits": 4.5,
    "pendingCredits": 1,
    "status": "Incomplete",
    "strExpiry": "13 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "CK",
    "q3Credits": 4.2,
    "year2026Credits": 21.8,
    "lastActivity": "05 Nov 2026, 16.22",
    "periodLabel": "13 November 2024 - 13 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 4.5,
        "target": 8,
        "redPercent": 56,
        "yellowPercent": 13
      },
      "pending": 1,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 2.5,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 2,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-027",
    "pogiId": "#POGI-2026-1027",
    "name": "Prof. Dr. dr. Ratna Simatupang, Sp.OG, Subsp. KFM",
    "initials": "RS",
    "nik": "3273100000019426",
    "province": "DKI Jakarta",
    "hospital": "RS Ibu dan Anak Harapan Kita",
    "institution": "RS Ibu dan Anak Harapan Kita",
    "phone": "+62 812-1814-1418",
    "assignedTo": "Wahyu Setiawan",
    "pelayananCredits": 34,
    "pengabdianCredits": 7,
    "pembelajaranCredits": 15,
    "q4Credits": 5.4,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "14 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "RS",
    "q3Credits": 5.2,
    "year2026Credits": 23.6,
    "lastActivity": "04 Nov 2026, 08.39",
    "periodLabel": "14 Maret 2024 - 14 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 5.4,
        "target": 11,
        "redPercent": 49,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0.5,
        "target": 1
      },
      "pelayanan": {
        "achieved": 3,
        "target": 6.1
      },
      "pembelajaran": {
        "achieved": 1.9,
        "target": 3.9
      }
    }
  },
  {
    "id": "doc-028",
    "pogiId": "#POGI-2026-1028",
    "name": "dr. Tari Utami, Sp.OG, Subsp. Onk",
    "initials": "TU",
    "nik": "3273100000020309",
    "province": "Sulawesi Selatan",
    "hospital": "RSUP Dr. Wahidin Sudirohusodo",
    "institution": "RSUP Dr. Wahidin Sudirohusodo",
    "phone": "+62 812-1851-1437",
    "assignedTo": "Siti Rahma",
    "pelayananCredits": 68,
    "pengabdianCredits": 23,
    "pembelajaranCredits": 31,
    "q4Credits": 9.6,
    "pendingCredits": 0,
    "status": "Complete",
    "strExpiry": "15 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "TU",
    "q3Credits": 9.1,
    "year2026Credits": 32,
    "lastActivity": "17 Nov 2026, 09.56",
    "periodLabel": "15 November 2024 - 15 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 9.6,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 0,
        "isGreen": true
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 0.5
      },
      "pelayanan": {
        "achieved": 5.3,
        "target": 5
      },
      "pembelajaran": {
        "achieved": 4.3,
        "target": 2.5
      }
    }
  },
  {
    "id": "doc-029",
    "pogiId": "#POGI-2026-1029",
    "name": "dr. Lestari Rahmawati, Sp.OG, Subsp. FER",
    "initials": "LR",
    "nik": "3273100000021192",
    "province": "Sulawesi Selatan",
    "hospital": "RSIA Pertiwi Makassar",
    "institution": "RSIA Pertiwi Makassar",
    "phone": "+62 812-1888-1456",
    "assignedTo": "Dewi Lestari",
    "pelayananCredits": 69,
    "pengabdianCredits": 24,
    "pembelajaranCredits": 32,
    "q4Credits": 10.4,
    "pendingCredits": 2.5,
    "status": "Complete",
    "strExpiry": "16 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "LR",
    "q3Credits": 9.5,
    "year2026Credits": 32.8,
    "lastActivity": "16 Nov 2026, 10.13",
    "periodLabel": "16 Maret 2024 - 16 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 10.4,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 0,
        "isGreen": true
      },
      "pending": 2.5,
      "pengabdian": {
        "achieved": 0.5,
        "target": 0.5
      },
      "pelayanan": {
        "achieved": 5.7,
        "target": 5.5
      },
      "pembelajaran": {
        "achieved": 4.2,
        "target": 2
      }
    }
  },
  {
    "id": "doc-030",
    "pogiId": "#POGI-2026-1030",
    "name": "dr. Kevin Meurah, Sp.OG(K)",
    "initials": "KM",
    "nik": "3273100000022075",
    "province": "Sulawesi Selatan",
    "hospital": "RSUD Labuang Baji",
    "institution": "RSUD Labuang Baji",
    "phone": "+62 812-1925-1475",
    "assignedTo": "Rina Kartika",
    "pelayananCredits": 70,
    "pengabdianCredits": 25,
    "pembelajaranCredits": 33,
    "q4Credits": 11.2,
    "pendingCredits": 0,
    "status": "Complete",
    "strExpiry": "17 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "KM",
    "q3Credits": 9,
    "year2026Credits": 33.6,
    "lastActivity": "15 Nov 2026, 11.30",
    "periodLabel": "17 November 2024 - 17 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 11.2,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 0,
        "isGreen": true
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 1,
        "target": 0
      },
      "pelayanan": {
        "achieved": 6.2,
        "target": 5
      },
      "pembelajaran": {
        "achieved": 4,
        "target": 3
      }
    }
  },
  {
    "id": "doc-031",
    "pogiId": "#POGI-2026-1031",
    "name": "dr. Maya Utami, Sp.OG, Subsp. KFM",
    "initials": "MU",
    "nik": "3273100000022958",
    "province": "Sulawesi Selatan",
    "hospital": "RS Awal Bros Makassar",
    "institution": "RS Awal Bros Makassar",
    "phone": "+62 812-1962-1494",
    "assignedTo": "Melati Sihombing",
    "pelayananCredits": 13,
    "pengabdianCredits": 11,
    "pembelajaranCredits": 19,
    "q4Credits": 1.8,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "18 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "MU",
    "q3Credits": 2.3,
    "year2026Credits": 17.2,
    "lastActivity": "14 Nov 2026, 12.47",
    "periodLabel": "18 Maret 2024 - 18 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 1.8,
        "target": 8,
        "redPercent": 23,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 1,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 0.8,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-032",
    "pogiId": "#POGI-2026-1032",
    "name": "dr. Tari Rahmawati, Sp.OG, Subsp. Obginsos",
    "initials": "TR",
    "nik": "3273100000023841",
    "province": "Sulawesi Selatan",
    "hospital": "RSUP Dr. Wahidin Sudirohusodo",
    "institution": "RSUP Dr. Wahidin Sudirohusodo",
    "phone": "+62 812-1999-1513",
    "assignedTo": "Wahyu Setiawan",
    "pelayananCredits": 14,
    "pengabdianCredits": 12,
    "pembelajaranCredits": 20,
    "q4Credits": 2.7,
    "pendingCredits": 1.5,
    "status": "Incomplete",
    "strExpiry": "19 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "TR",
    "q3Credits": 2.2,
    "year2026Credits": 19.1,
    "lastActivity": "13 Nov 2026, 13.04",
    "periodLabel": "19 November 2024 - 19 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 2.7,
        "target": 8,
        "redPercent": 34,
        "yellowPercent": 19
      },
      "pending": 1.5,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 1.5,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 1.2,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-033",
    "pogiId": "#POGI-2026-1033",
    "name": "Dr. dr. Lestari Meurah, Sp.OG, Subsp. Urogin-RE",
    "initials": "LM",
    "nik": "3273100000024724",
    "province": "Sulawesi Selatan",
    "hospital": "RSIA Pertiwi Makassar",
    "institution": "RSIA Pertiwi Makassar",
    "phone": "+62 812-2036-1532",
    "assignedTo": "Siti Rahma",
    "pelayananCredits": 15,
    "pengabdianCredits": 13,
    "pembelajaranCredits": 21,
    "q4Credits": 3.6,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "20 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "LM",
    "q3Credits": 3.2,
    "year2026Credits": 20.9,
    "lastActivity": "12 Nov 2026, 14.21",
    "periodLabel": "20 Maret 2024 - 20 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 3.6,
        "target": 8,
        "redPercent": 45,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0.5,
        "target": 1
      },
      "pelayanan": {
        "achieved": 2,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 1.1,
        "target": 2.6
      }
    }
  },
  {
    "id": "doc-034",
    "pogiId": "#POGI-2026-1034",
    "name": "dr. Kevin Utami, Sp.OG",
    "initials": "KU",
    "nik": "3273100000025607",
    "province": "Sulawesi Selatan",
    "hospital": "RSUD Labuang Baji",
    "institution": "RSUD Labuang Baji",
    "phone": "+62 812-2073-1551",
    "assignedTo": "Dewi Lestari",
    "pelayananCredits": 16,
    "pengabdianCredits": 14,
    "pembelajaranCredits": 22,
    "q4Credits": 4.5,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "21 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "KU",
    "q3Credits": 4.2,
    "year2026Credits": 22.8,
    "lastActivity": "11 Nov 2026, 15.38",
    "periodLabel": "21 November 2024 - 21 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 4.5,
        "target": 8,
        "redPercent": 56,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 2.5,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 2,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-035",
    "pogiId": "#POGI-2026-1035",
    "name": "Prof. Dr. dr. Maya Rahmawati, Sp.OG, Subsp. Onk",
    "initials": "MR",
    "nik": "3273100000026490",
    "province": "Sulawesi Selatan",
    "hospital": "RS Awal Bros Makassar",
    "institution": "RS Awal Bros Makassar",
    "phone": "+62 812-2110-1570",
    "assignedTo": "Rina Kartika",
    "pelayananCredits": 17,
    "pengabdianCredits": 5,
    "pembelajaranCredits": 8,
    "q4Credits": 5.4,
    "pendingCredits": 0.5,
    "status": "Incomplete",
    "strExpiry": "22 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "MR",
    "q3Credits": 5.2,
    "year2026Credits": 24.6,
    "lastActivity": "10 Nov 2026, 16.55",
    "periodLabel": "22 Maret 2024 - 22 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 5.4,
        "target": 8,
        "redPercent": 68,
        "yellowPercent": 6
      },
      "pending": 0.5,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 3,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 2.4,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-036",
    "pogiId": "#POGI-2026-1036",
    "name": "dr. Tari Meurah, Sp.OG, Subsp. FER",
    "initials": "TM",
    "nik": "3273100000027373",
    "province": "Sulawesi Selatan",
    "hospital": "RSUP Dr. Wahidin Sudirohusodo",
    "institution": "RSUP Dr. Wahidin Sudirohusodo",
    "phone": "+62 812-2147-1589",
    "assignedTo": "Melati Sihombing",
    "pelayananCredits": 54,
    "pengabdianCredits": 21,
    "pembelajaranCredits": 33,
    "q4Credits": 8.5,
    "pendingCredits": 0,
    "status": "Complete",
    "strExpiry": "23 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "TM",
    "q3Credits": 8.4,
    "year2026Credits": 32.8,
    "lastActivity": "09 Nov 2026, 08.12",
    "periodLabel": "23 November 2024 - 23 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 8.5,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 0,
        "isGreen": true
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 0
      },
      "pelayanan": {
        "achieved": 5,
        "target": 5
      },
      "pembelajaran": {
        "achieved": 3.5,
        "target": 3
      }
    }
  },
  {
    "id": "doc-037",
    "pogiId": "#POGI-2026-1037",
    "name": "dr. Lestari Utami, Sp.OG(K)",
    "initials": "LU",
    "nik": "3273100000028256",
    "province": "Sulawesi Selatan",
    "hospital": "RSIA Pertiwi Makassar",
    "institution": "RSIA Pertiwi Makassar",
    "phone": "+62 812-2184-1608",
    "assignedTo": "Wahyu Setiawan",
    "pelayananCredits": 19,
    "pengabdianCredits": 7,
    "pembelajaranCredits": 10,
    "q4Credits": 0,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "24 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "LU",
    "q3Credits": 1.5,
    "year2026Credits": 14.5,
    "lastActivity": "08 Nov 2026, 09.29",
    "periodLabel": "24 Maret 2024 - 24 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 0,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 0,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 0,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-038",
    "pogiId": "#POGI-2026-1038",
    "name": "dr. Kevin Rahmawati, Sp.OG, Subsp. KFM",
    "initials": "KR",
    "nik": "3273100000029139",
    "province": "Sulawesi Selatan",
    "hospital": "RSUD Labuang Baji",
    "institution": "RSUD Labuang Baji",
    "phone": "+62 812-2221-1627",
    "assignedTo": "Siti Rahma",
    "pelayananCredits": 20,
    "pengabdianCredits": 8,
    "pembelajaranCredits": 11,
    "q4Credits": 0.9,
    "pendingCredits": 2,
    "status": "Incomplete",
    "strExpiry": "25 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "KR",
    "q3Credits": 1.5,
    "year2026Credits": 16.4,
    "lastActivity": "07 Nov 2026, 10.46",
    "periodLabel": "25 November 2024 - 25 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 0.9,
        "target": 8,
        "redPercent": 11,
        "yellowPercent": 25
      },
      "pending": 2,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 0.5,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 0.4,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-039",
    "pogiId": "#POGI-2026-1039",
    "name": "dr. Maya Meurah, Sp.OG, Subsp. Obginsos",
    "initials": "MM",
    "nik": "3273100000030022",
    "province": "Sulawesi Selatan",
    "hospital": "RS Awal Bros Makassar",
    "institution": "RS Awal Bros Makassar",
    "phone": "+62 812-2258-1646",
    "assignedTo": "Dewi Lestari",
    "pelayananCredits": 21,
    "pengabdianCredits": 9,
    "pembelajaranCredits": 12,
    "q4Credits": 1.8,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "26 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "MM",
    "q3Credits": 2.3,
    "year2026Credits": 18.2,
    "lastActivity": "06 Nov 2026, 11.03",
    "periodLabel": "26 Maret 2024 - 26 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 1.8,
        "target": 8,
        "redPercent": 23,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1
      },
      "pelayanan": {
        "achieved": 1,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 0.8,
        "target": 2.6
      }
    }
  },
  {
    "id": "doc-040",
    "pogiId": "#POGI-2026-1040",
    "name": "dr. Fitri Hermawan, Sp.OG",
    "initials": "FH",
    "nik": "3273100000030905",
    "province": "Jawa Tengah",
    "hospital": "RSUP Dr. Kariadi Semarang",
    "institution": "RSUP Dr. Kariadi Semarang",
    "phone": "+62 812-2295-1665",
    "assignedTo": "Rina Kartika",
    "pelayananCredits": 50,
    "pengabdianCredits": 20,
    "pembelajaranCredits": 43,
    "q4Credits": 8,
    "pendingCredits": 0,
    "status": "Complete",
    "strExpiry": "27 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "FH",
    "q3Credits": 8,
    "year2026Credits": 36,
    "lastActivity": "05 Nov 2026, 12.20",
    "periodLabel": "27 November 2024 - 27 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 8,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 0,
        "isGreen": true
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 0.5
      },
      "pelayanan": {
        "achieved": 5,
        "target": 5
      },
      "pembelajaran": {
        "achieved": 3,
        "target": 2.5
      }
    }
  },
  {
    "id": "doc-041",
    "pogiId": "#POGI-2026-1041",
    "name": "Dr. dr. Putri Suryani, Sp.OG, Subsp. Onk",
    "initials": "PS",
    "nik": "3273100000031788",
    "province": "Jawa Tengah",
    "hospital": "RSUD Dr. Moewardi Solo",
    "institution": "RSUD Dr. Moewardi Solo",
    "phone": "+62 812-2332-1684",
    "assignedTo": "Melati Sihombing",
    "pelayananCredits": 51,
    "pengabdianCredits": 21,
    "pembelajaranCredits": 44,
    "q4Credits": 8.8,
    "pendingCredits": 1,
    "status": "Complete",
    "strExpiry": "10 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "PS",
    "q3Credits": 8.4,
    "year2026Credits": 36.8,
    "lastActivity": "04 Nov 2026, 13.37",
    "periodLabel": "10 Maret 2024 - 10 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 8.8,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 0,
        "isGreen": true
      },
      "pending": 1,
      "pengabdian": {
        "achieved": 0.5,
        "target": 0.5
      },
      "pelayanan": {
        "achieved": 5.5,
        "target": 5.5
      },
      "pembelajaran": {
        "achieved": 2.8,
        "target": 2
      }
    }
  },
  {
    "id": "doc-042",
    "pogiId": "#POGI-2026-1042",
    "name": "dr. Yohana Siregar, Sp.OG, Subsp. FER",
    "initials": "YS",
    "nik": "3273100000032671",
    "province": "Jawa Tengah",
    "hospital": "RS Panti Wilasa",
    "institution": "RS Panti Wilasa",
    "phone": "+62 812-2369-1703",
    "assignedTo": "Wahyu Setiawan",
    "pelayananCredits": 52,
    "pengabdianCredits": 22,
    "pembelajaranCredits": 45,
    "q4Credits": 9.6,
    "pendingCredits": 0,
    "status": "Complete",
    "strExpiry": "11 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "YS",
    "q3Credits": 8.9,
    "year2026Credits": 32,
    "lastActivity": "17 Nov 2026, 14.54",
    "periodLabel": "11 November 2024 - 11 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 9.6,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 0,
        "isGreen": true
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 1,
        "target": 0
      },
      "pelayanan": {
        "achieved": 5.3,
        "target": 5
      },
      "pembelajaran": {
        "achieved": 3.3,
        "target": 3
      }
    }
  },
  {
    "id": "doc-043",
    "pogiId": "#POGI-2026-1043",
    "name": "Prof. Dr. dr. Hendra Hermawan, Sp.OG(K)",
    "initials": "HH",
    "nik": "3273100000033554",
    "province": "Jawa Tengah",
    "hospital": "RSUD Banyumas",
    "institution": "RSUD Banyumas",
    "phone": "+62 812-2406-1722",
    "assignedTo": "Siti Rahma",
    "pelayananCredits": 25,
    "pengabdianCredits": 13,
    "pembelajaranCredits": 16,
    "q4Credits": 5.4,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "12 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "HH",
    "q3Credits": 5.2,
    "year2026Credits": 22.6,
    "lastActivity": "16 Nov 2026, 15.11",
    "periodLabel": "12 Maret 2024 - 12 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 5.4,
        "target": 12.5,
        "redPercent": 43,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 3,
        "target": 6.9
      },
      "pembelajaran": {
        "achieved": 2.4,
        "target": 4.1
      }
    }
  },
  {
    "id": "doc-044",
    "pogiId": "#POGI-2026-1044",
    "name": "dr. Fitri Suryani, Sp.OG, Subsp. KFM",
    "initials": "FS",
    "nik": "3273100000034437",
    "province": "Jawa Tengah",
    "hospital": "RSUP Dr. Kariadi Semarang",
    "institution": "RSUP Dr. Kariadi Semarang",
    "phone": "+62 812-2443-1741",
    "assignedTo": "Dewi Lestari",
    "pelayananCredits": 26,
    "pengabdianCredits": 14,
    "pembelajaranCredits": 17,
    "q4Credits": 6.3,
    "pendingCredits": 2.5,
    "status": "Incomplete",
    "strExpiry": "13 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "FS",
    "q3Credits": 5,
    "year2026Credits": 24.5,
    "lastActivity": "15 Nov 2026, 16.28",
    "periodLabel": "13 November 2024 - 13 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 6.3,
        "target": 14,
        "redPercent": 45,
        "yellowPercent": 18
      },
      "pending": 2.5,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 3.5,
        "target": 7.7
      },
      "pembelajaran": {
        "achieved": 2.8,
        "target": 4.8
      }
    }
  },
  {
    "id": "doc-045",
    "pogiId": "#POGI-2026-1045",
    "name": "dr. Putri Siregar, Sp.OG, Subsp. Obginsos",
    "initials": "PS",
    "nik": "3273100000035320",
    "province": "Jawa Tengah",
    "hospital": "RSUD Dr. Moewardi Solo",
    "institution": "RSUD Dr. Moewardi Solo",
    "phone": "+62 812-2480-1760",
    "assignedTo": "Rina Kartika",
    "pelayananCredits": 27,
    "pengabdianCredits": 5,
    "pembelajaranCredits": 18,
    "q4Credits": 0,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "14 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "PS",
    "q3Credits": 1.5,
    "year2026Credits": 15.5,
    "lastActivity": "14 Nov 2026, 08.45",
    "periodLabel": "14 Maret 2024 - 14 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 0,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1
      },
      "pelayanan": {
        "achieved": 0,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 0,
        "target": 2.6
      }
    }
  },
  {
    "id": "doc-046",
    "pogiId": "#POGI-2026-1046",
    "name": "dr. Yohana Hermawan, Sp.OG, Subsp. Urogin-RE",
    "initials": "YH",
    "nik": "3273100000036203",
    "province": "Jawa Tengah",
    "hospital": "RS Panti Wilasa",
    "institution": "RS Panti Wilasa",
    "phone": "+62 812-2517-1779",
    "assignedTo": "Melati Sihombing",
    "pelayananCredits": 28,
    "pengabdianCredits": 6,
    "pembelajaranCredits": 19,
    "q4Credits": 0.9,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "15 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "YH",
    "q3Credits": 1.5,
    "year2026Credits": 17.4,
    "lastActivity": "13 Nov 2026, 09.02",
    "periodLabel": "15 November 2024 - 15 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 0.9,
        "target": 8,
        "redPercent": 11,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 0.5,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 0.4,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-047",
    "pogiId": "#POGI-2026-1047",
    "name": "dr. Hendra Suryani, Sp.OG",
    "initials": "HS",
    "nik": "3273100000037086",
    "province": "Jawa Tengah",
    "hospital": "RSUD Banyumas",
    "institution": "RSUD Banyumas",
    "phone": "+62 812-2554-1798",
    "assignedTo": "Wahyu Setiawan",
    "pelayananCredits": 29,
    "pengabdianCredits": 7,
    "pembelajaranCredits": 20,
    "q4Credits": 1.8,
    "pendingCredits": 1.5,
    "status": "Incomplete",
    "strExpiry": "16 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "HS",
    "q3Credits": 2.3,
    "year2026Credits": 19.2,
    "lastActivity": "12 Nov 2026, 10.19",
    "periodLabel": "16 Maret 2024 - 16 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 1.8,
        "target": 8,
        "redPercent": 23,
        "yellowPercent": 19
      },
      "pending": 1.5,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 1,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 0.8,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-048",
    "pogiId": "#POGI-2026-1048",
    "name": "dr. Fitri Siregar, Sp.OG, Subsp. Onk",
    "initials": "FS",
    "nik": "3273100000037969",
    "province": "Jawa Tengah",
    "hospital": "RSUP Dr. Kariadi Semarang",
    "institution": "RSUP Dr. Kariadi Semarang",
    "phone": "+62 812-2591-1817",
    "assignedTo": "Siti Rahma",
    "pelayananCredits": 53,
    "pengabdianCredits": 24,
    "pembelajaranCredits": 35,
    "q4Credits": 8.6,
    "pendingCredits": 0,
    "status": "Complete",
    "strExpiry": "17 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "FS",
    "q3Credits": 8.8,
    "year2026Credits": 36.8,
    "lastActivity": "11 Nov 2026, 11.36",
    "periodLabel": "17 November 2024 - 17 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 8.6,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 0,
        "isGreen": true
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 0
      },
      "pelayanan": {
        "achieved": 5,
        "target": 5
      },
      "pembelajaran": {
        "achieved": 3.6,
        "target": 3
      }
    }
  },
  {
    "id": "doc-049",
    "pogiId": "#POGI-2026-1049",
    "name": "Dr. dr. Putri Hermawan, Sp.OG, Subsp. FER",
    "initials": "PH",
    "nik": "3273100000038852",
    "province": "Jawa Tengah",
    "hospital": "RSUD Dr. Moewardi Solo",
    "institution": "RSUD Dr. Moewardi Solo",
    "phone": "+62 812-2628-1836",
    "assignedTo": "Dewi Lestari",
    "pelayananCredits": 31,
    "pengabdianCredits": 9,
    "pembelajaranCredits": 22,
    "q4Credits": 3.6,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "18 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "PH",
    "q3Credits": 3.2,
    "year2026Credits": 19.9,
    "lastActivity": "10 Nov 2026, 12.53",
    "periodLabel": "18 Maret 2024 - 18 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 3.6,
        "target": 8,
        "redPercent": 45,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 2,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 1.6,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-050",
    "pogiId": "#POGI-2026-1050",
    "name": "dr. Yohana Suryani, Sp.OG(K)",
    "initials": "YS",
    "nik": "3273100000039735",
    "province": "Jawa Tengah",
    "hospital": "RS Panti Wilasa",
    "institution": "RS Panti Wilasa",
    "phone": "+62 812-2665-1855",
    "assignedTo": "Rina Kartika",
    "pelayananCredits": 32,
    "pengabdianCredits": 10,
    "pembelajaranCredits": 8,
    "q4Credits": 4.5,
    "pendingCredits": 0.5,
    "status": "Incomplete",
    "strExpiry": "19 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "YS",
    "q3Credits": 4.2,
    "year2026Credits": 21.8,
    "lastActivity": "09 Nov 2026, 13.10",
    "periodLabel": "19 November 2024 - 19 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 4.5,
        "target": 8,
        "redPercent": 56,
        "yellowPercent": 6
      },
      "pending": 0.5,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 2.5,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 2,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-051",
    "pogiId": "#POGI-2026-1051",
    "name": "Prof. Dr. dr. Bambang Wijaya, Sp.OG, Subsp. FER",
    "initials": "BW",
    "nik": "3273100000040618",
    "province": "Bali",
    "hospital": "RSUP Prof. Dr. I.G.N.G. Ngoerah (Sanglah)",
    "institution": "RSUP Prof. Dr. I.G.N.G. Ngoerah (Sanglah)",
    "phone": "+62 812-2702-1874",
    "assignedTo": "Melati Sihombing",
    "pelayananCredits": 33,
    "pengabdianCredits": 11,
    "pembelajaranCredits": 9,
    "q4Credits": 5.4,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "20 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "BW",
    "q3Credits": 5.2,
    "year2026Credits": 23.6,
    "lastActivity": "08 Nov 2026, 14.27",
    "periodLabel": "20 Maret 2024 - 20 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 5.4,
        "target": 9.5,
        "redPercent": 57,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0.5,
        "target": 1
      },
      "pelayanan": {
        "achieved": 3,
        "target": 5.2
      },
      "pembelajaran": {
        "achieved": 1.9,
        "target": 3.3
      }
    }
  },
  {
    "id": "doc-052",
    "pogiId": "#POGI-2026-1052",
    "name": "dr. Yusuf Sumampouw, Sp.OG(K)",
    "initials": "YS",
    "nik": "3273100000041501",
    "province": "Bali",
    "hospital": "RSUD Bali Mandara",
    "institution": "RSUD Bali Mandara",
    "phone": "+62 812-2739-1893",
    "assignedTo": "Wahyu Setiawan",
    "pelayananCredits": 34,
    "pengabdianCredits": 12,
    "pembelajaranCredits": 10,
    "q4Credits": 6.3,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "21 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "YS",
    "q3Credits": 5,
    "year2026Credits": 25.5,
    "lastActivity": "07 Nov 2026, 15.44",
    "periodLabel": "21 November 2024 - 21 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 6.3,
        "target": 11,
        "redPercent": 57,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 3.5,
        "target": 6.1
      },
      "pembelajaran": {
        "achieved": 2.8,
        "target": 3.4
      }
    }
  },
  {
    "id": "doc-053",
    "pogiId": "#POGI-2026-1053",
    "name": "dr. Surya Purnomo, Sp.OG, Subsp. KFM",
    "initials": "SP",
    "nik": "3273100000042384",
    "province": "Bali",
    "hospital": "RS Surya Husadha Denpasar",
    "institution": "RS Surya Husadha Denpasar",
    "phone": "+62 812-2776-1912",
    "assignedTo": "Siti Rahma",
    "pelayananCredits": 35,
    "pengabdianCredits": 13,
    "pembelajaranCredits": 11,
    "q4Credits": 0,
    "pendingCredits": 2,
    "status": "Incomplete",
    "strExpiry": "22 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "SP",
    "q3Credits": 1.5,
    "year2026Credits": 16.5,
    "lastActivity": "06 Nov 2026, 16.01",
    "periodLabel": "22 Maret 2024 - 22 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 0,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 25
      },
      "pending": 2,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 0,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 0,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-054",
    "pogiId": "#POGI-2026-1054",
    "name": "dr. Asep Wijaya, Sp.OG, Subsp. Obginsos",
    "initials": "AW",
    "nik": "3273100000043267",
    "province": "Bali",
    "hospital": "RSUP Prof. Dr. I.G.N.G. Ngoerah (Sanglah)",
    "institution": "RSUP Prof. Dr. I.G.N.G. Ngoerah (Sanglah)",
    "phone": "+62 812-2813-1931",
    "assignedTo": "Dewi Lestari",
    "pelayananCredits": 52,
    "pengabdianCredits": 22,
    "pembelajaranCredits": 34,
    "q4Credits": 8.5,
    "pendingCredits": 0,
    "status": "Complete",
    "strExpiry": "23 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "AW",
    "q3Credits": 9,
    "year2026Credits": 36,
    "lastActivity": "05 Nov 2026, 08.18",
    "periodLabel": "23 November 2024 - 23 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 8.5,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 0,
        "isGreen": true
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 1,
        "target": 0
      },
      "pelayanan": {
        "achieved": 5,
        "target": 5
      },
      "pembelajaran": {
        "achieved": 2.5,
        "target": 3
      }
    }
  },
  {
    "id": "doc-055",
    "pogiId": "#POGI-2026-1055",
    "name": "dr. Bambang Sumampouw, Sp.OG, Subsp. Urogin-RE",
    "initials": "BS",
    "nik": "3273100000044150",
    "province": "Bali",
    "hospital": "RSUD Bali Mandara",
    "institution": "RSUD Bali Mandara",
    "phone": "+62 812-2850-1950",
    "assignedTo": "Rina Kartika",
    "pelayananCredits": 12,
    "pengabdianCredits": 5,
    "pembelajaranCredits": 13,
    "q4Credits": 1.8,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "24 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "BS",
    "q3Credits": 2.3,
    "year2026Credits": 17.2,
    "lastActivity": "04 Nov 2026, 09.35",
    "periodLabel": "24 Maret 2024 - 24 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 1.8,
        "target": 8,
        "redPercent": 23,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 1,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 0.8,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-056",
    "pogiId": "#POGI-2026-1056",
    "name": "dr. Yusuf Purnomo, Sp.OG",
    "initials": "YP",
    "nik": "3273100000045033",
    "province": "Bali",
    "hospital": "RS Surya Husadha Denpasar",
    "institution": "RS Surya Husadha Denpasar",
    "phone": "+62 812-2887-1969",
    "assignedTo": "Melati Sihombing",
    "pelayananCredits": 13,
    "pengabdianCredits": 6,
    "pembelajaranCredits": 14,
    "q4Credits": 2.7,
    "pendingCredits": 1,
    "status": "Incomplete",
    "strExpiry": "25 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "YP",
    "q3Credits": 2.2,
    "year2026Credits": 19.1,
    "lastActivity": "17 Nov 2026, 10.52",
    "periodLabel": "25 November 2024 - 25 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 2.7,
        "target": 8,
        "redPercent": 34,
        "yellowPercent": 13
      },
      "pending": 1,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 1.5,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 1.2,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-057",
    "pogiId": "#POGI-2026-1057",
    "name": "Dr. dr. Surya Wijaya, Sp.OG, Subsp. Onk",
    "initials": "SW",
    "nik": "3273100000045916",
    "province": "Bali",
    "hospital": "RSUP Prof. Dr. I.G.N.G. Ngoerah (Sanglah)",
    "institution": "RSUP Prof. Dr. I.G.N.G. Ngoerah (Sanglah)",
    "phone": "+62 812-2924-1988",
    "assignedTo": "Wahyu Setiawan",
    "pelayananCredits": 14,
    "pengabdianCredits": 7,
    "pembelajaranCredits": 15,
    "q4Credits": 3.6,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "26 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "SW",
    "q3Credits": 3.2,
    "year2026Credits": 20.9,
    "lastActivity": "16 Nov 2026, 11.09",
    "periodLabel": "26 Maret 2024 - 26 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 3.6,
        "target": 8,
        "redPercent": 45,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0.5,
        "target": 1
      },
      "pelayanan": {
        "achieved": 2,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 1.1,
        "target": 2.6
      }
    }
  },
  {
    "id": "doc-058",
    "pogiId": "#POGI-2026-1058",
    "name": "dr. Asep Sumampouw, Sp.OG, Subsp. FER",
    "initials": "AS",
    "nik": "3273100000046799",
    "province": "Bali",
    "hospital": "RSUD Bali Mandara",
    "institution": "RSUD Bali Mandara",
    "phone": "+62 812-2961-2007",
    "assignedTo": "Siti Rahma",
    "pelayananCredits": 15,
    "pengabdianCredits": 8,
    "pembelajaranCredits": 16,
    "q4Credits": 4.5,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "27 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "AS",
    "q3Credits": 4.2,
    "year2026Credits": 22.8,
    "lastActivity": "15 Nov 2026, 12.26",
    "periodLabel": "27 November 2024 - 27 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 4.5,
        "target": 8,
        "redPercent": 56,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 2.5,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 2,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-059",
    "pogiId": "#POGI-2026-1059",
    "name": "Prof. Dr. dr. Bambang Purnomo, Sp.OG(K)",
    "initials": "BP",
    "nik": "3273100000047682",
    "province": "Bali",
    "hospital": "RS Surya Husadha Denpasar",
    "institution": "RS Surya Husadha Denpasar",
    "phone": "+62 812-2998-2026",
    "assignedTo": "Dewi Lestari",
    "pelayananCredits": 16,
    "pengabdianCredits": 9,
    "pembelajaranCredits": 17,
    "q4Credits": 5.4,
    "pendingCredits": 2.5,
    "status": "Incomplete",
    "strExpiry": "10 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "BP",
    "q3Credits": 5.2,
    "year2026Credits": 24.6,
    "lastActivity": "14 Nov 2026, 13.43",
    "periodLabel": "10 Maret 2024 - 10 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 5.4,
        "target": 14,
        "redPercent": 39,
        "yellowPercent": 18
      },
      "pending": 2.5,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 3,
        "target": 7.7
      },
      "pembelajaran": {
        "achieved": 2.4,
        "target": 4.8
      }
    }
  },
  {
    "id": "doc-060",
    "pogiId": "#POGI-2026-1060",
    "name": "dr. Yusuf Wijaya, Sp.OG, Subsp. KFM",
    "initials": "YW",
    "nik": "3273100000048565",
    "province": "Bali",
    "hospital": "RSUP Prof. Dr. I.G.N.G. Ngoerah (Sanglah)",
    "institution": "RSUP Prof. Dr. I.G.N.G. Ngoerah (Sanglah)",
    "phone": "+62 812-3035-2045",
    "assignedTo": "Rina Kartika",
    "pelayananCredits": 17,
    "pengabdianCredits": 10,
    "pembelajaranCredits": 18,
    "q4Credits": 6.3,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "11 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "YW",
    "q3Credits": 5,
    "year2026Credits": 23.5,
    "lastActivity": "13 Nov 2026, 14.00",
    "periodLabel": "11 November 2024 - 11 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 6.3,
        "target": 8,
        "redPercent": 79,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0.5,
        "target": 1
      },
      "pelayanan": {
        "achieved": 3.5,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 2.3,
        "target": 2.6
      }
    }
  },
  {
    "id": "doc-061",
    "pogiId": "#POGI-2026-1061",
    "name": "dr. Asep Nugroho, Sp.OG",
    "initials": "AN",
    "nik": "3273100000049448",
    "province": "Sumatera Utara",
    "hospital": "RSUP H. Adam Malik Medan",
    "institution": "RSUP H. Adam Malik Medan",
    "phone": "+62 812-3072-2064",
    "assignedTo": "Melati Sihombing",
    "pelayananCredits": 71,
    "pengabdianCredits": 26,
    "pembelajaranCredits": 44,
    "q4Credits": 8,
    "pendingCredits": 0,
    "status": "Complete",
    "strExpiry": "12 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "AN",
    "q3Credits": 8.2,
    "year2026Credits": 36,
    "lastActivity": "12 Nov 2026, 15.17",
    "periodLabel": "12 Maret 2024 - 12 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 8,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 0,
        "isGreen": true
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0.5,
        "target": 0.5
      },
      "pelayanan": {
        "achieved": 5.5,
        "target": 5.5
      },
      "pembelajaran": {
        "achieved": 2,
        "target": 2
      }
    }
  },
  {
    "id": "doc-062",
    "pogiId": "#POGI-2026-1062",
    "name": "dr. Bambang Suryana, Sp.OG, Subsp. Onk",
    "initials": "BS",
    "nik": "3273100000050331",
    "province": "Sumatera Utara",
    "hospital": "RSUD Dr. Pirngadi",
    "institution": "RSUD Dr. Pirngadi",
    "phone": "+62 812-3109-2083",
    "assignedTo": "Wahyu Setiawan",
    "pelayananCredits": 72,
    "pengabdianCredits": 27,
    "pembelajaranCredits": 45,
    "q4Credits": 8.8,
    "pendingCredits": 1.5,
    "status": "Complete",
    "strExpiry": "13 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "BS",
    "q3Credits": 8.6,
    "year2026Credits": 36.8,
    "lastActivity": "11 Nov 2026, 16.34",
    "periodLabel": "13 November 2024 - 13 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 8.8,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 0,
        "isGreen": true
      },
      "pending": 1.5,
      "pengabdian": {
        "achieved": 1,
        "target": 0.5
      },
      "pelayanan": {
        "achieved": 5,
        "target": 5
      },
      "pembelajaran": {
        "achieved": 2.8,
        "target": 2.5
      }
    }
  },
  {
    "id": "doc-063",
    "pogiId": "#POGI-2026-1063",
    "name": "dr. Yusuf Anggraini, Sp.OG, Subsp. FER",
    "initials": "YA",
    "nik": "3273100000051214",
    "province": "Sumatera Utara",
    "hospital": "RS Murni Teguh Memorial",
    "institution": "RS Murni Teguh Memorial",
    "phone": "+62 812-3146-2102",
    "assignedTo": "Siti Rahma",
    "pelayananCredits": 20,
    "pengabdianCredits": 13,
    "pembelajaranCredits": 21,
    "q4Credits": 1.8,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "14 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "YA",
    "q3Credits": 2.3,
    "year2026Credits": 18.2,
    "lastActivity": "10 Nov 2026, 08.51",
    "periodLabel": "14 Maret 2024 - 14 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 1.8,
        "target": 8,
        "redPercent": 23,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1
      },
      "pelayanan": {
        "achieved": 1,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 0.8,
        "target": 2.6
      }
    }
  },
  {
    "id": "doc-064",
    "pogiId": "#POGI-2026-1064",
    "name": "dr. Surya Nugroho, Sp.OG(K)",
    "initials": "SN",
    "nik": "3273100000052097",
    "province": "Sumatera Utara",
    "hospital": "RSUP H. Adam Malik Medan",
    "institution": "RSUP H. Adam Malik Medan",
    "phone": "+62 812-3183-2121",
    "assignedTo": "Dewi Lestari",
    "pelayananCredits": 21,
    "pengabdianCredits": 14,
    "pembelajaranCredits": 22,
    "q4Credits": 2.7,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "15 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "SN",
    "q3Credits": 2.2,
    "year2026Credits": 20.1,
    "lastActivity": "09 Nov 2026, 09.08",
    "periodLabel": "15 November 2024 - 15 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 2.7,
        "target": 8,
        "redPercent": 34,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 1.5,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 1.2,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-065",
    "pogiId": "#POGI-2026-1065",
    "name": "Dr. dr. Asep Suryana, Sp.OG, Subsp. KFM",
    "initials": "AS",
    "nik": "3273100000052980",
    "province": "Sumatera Utara",
    "hospital": "RSUD Dr. Pirngadi",
    "institution": "RSUD Dr. Pirngadi",
    "phone": "+62 812-3220-2140",
    "assignedTo": "Rina Kartika",
    "pelayananCredits": 22,
    "pengabdianCredits": 5,
    "pembelajaranCredits": 8,
    "q4Credits": 3.6,
    "pendingCredits": 0.5,
    "status": "Incomplete",
    "strExpiry": "16 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "AS",
    "q3Credits": 3.2,
    "year2026Credits": 21.9,
    "lastActivity": "08 Nov 2026, 10.25",
    "periodLabel": "16 Maret 2024 - 16 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 3.6,
        "target": 8,
        "redPercent": 45,
        "yellowPercent": 6
      },
      "pending": 0.5,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 2,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 1.6,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-066",
    "pogiId": "#POGI-2026-1066",
    "name": "dr. Bambang Anggraini, Sp.OG, Subsp. Obginsos",
    "initials": "BA",
    "nik": "3273100000053863",
    "province": "Sumatera Utara",
    "hospital": "RS Murni Teguh Memorial",
    "institution": "RS Murni Teguh Memorial",
    "phone": "+62 812-3257-2159",
    "assignedTo": "Melati Sihombing",
    "pelayananCredits": 53,
    "pengabdianCredits": 23,
    "pembelajaranCredits": 33,
    "q4Credits": 8.5,
    "pendingCredits": 0,
    "status": "Complete",
    "strExpiry": "17 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "BA",
    "q3Credits": 8.4,
    "year2026Credits": 34.4,
    "lastActivity": "07 Nov 2026, 11.42",
    "periodLabel": "17 November 2024 - 17 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 8.5,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 0,
        "isGreen": true
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 1,
        "target": 0
      },
      "pelayanan": {
        "achieved": 5,
        "target": 5
      },
      "pembelajaran": {
        "achieved": 2.5,
        "target": 3
      }
    }
  },
  {
    "id": "doc-067",
    "pogiId": "#POGI-2026-1067",
    "name": "Prof. Dr. dr. Yusuf Nugroho, Sp.OG, Subsp. Urogin-RE",
    "initials": "YN",
    "nik": "3273100000054746",
    "province": "Sumatera Utara",
    "hospital": "RSUP H. Adam Malik Medan",
    "institution": "RSUP H. Adam Malik Medan",
    "phone": "+62 812-3294-2178",
    "assignedTo": "Wahyu Setiawan",
    "pelayananCredits": 24,
    "pengabdianCredits": 7,
    "pembelajaranCredits": 10,
    "q4Credits": 5.4,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "18 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "YN",
    "q3Credits": 5.2,
    "year2026Credits": 22.6,
    "lastActivity": "06 Nov 2026, 12.59",
    "periodLabel": "18 Maret 2024 - 18 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 5.4,
        "target": 11,
        "redPercent": 49,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 3,
        "target": 6.1
      },
      "pembelajaran": {
        "achieved": 2.4,
        "target": 3.4
      }
    }
  },
  {
    "id": "doc-068",
    "pogiId": "#POGI-2026-1068",
    "name": "dr. Surya Suryana, Sp.OG",
    "initials": "SS",
    "nik": "3273100000055629",
    "province": "Sumatera Utara",
    "hospital": "RSUD Dr. Pirngadi",
    "institution": "RSUD Dr. Pirngadi",
    "phone": "+62 812-3331-2197",
    "assignedTo": "Siti Rahma",
    "pelayananCredits": 25,
    "pengabdianCredits": 8,
    "pembelajaranCredits": 11,
    "q4Credits": 6.3,
    "pendingCredits": 2,
    "status": "Incomplete",
    "strExpiry": "19 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "SS",
    "q3Credits": 5,
    "year2026Credits": 24.5,
    "lastActivity": "05 Nov 2026, 13.16",
    "periodLabel": "19 November 2024 - 19 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 6.3,
        "target": 12.5,
        "redPercent": 50,
        "yellowPercent": 16
      },
      "pending": 2,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 3.5,
        "target": 6.9
      },
      "pembelajaran": {
        "achieved": 2.8,
        "target": 4.1
      }
    }
  },
  {
    "id": "doc-069",
    "pogiId": "#POGI-2026-1069",
    "name": "dr. Asep Anggraini, Sp.OG, Subsp. Onk",
    "initials": "AA",
    "nik": "3273100000056512",
    "province": "Sumatera Utara",
    "hospital": "RS Murni Teguh Memorial",
    "institution": "RS Murni Teguh Memorial",
    "phone": "+62 812-3368-2216",
    "assignedTo": "Dewi Lestari",
    "pelayananCredits": 26,
    "pengabdianCredits": 9,
    "pembelajaranCredits": 12,
    "q4Credits": 0,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "20 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "AA",
    "q3Credits": 1.5,
    "year2026Credits": 15.5,
    "lastActivity": "04 Nov 2026, 14.33",
    "periodLabel": "20 Maret 2024 - 20 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 0,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1
      },
      "pelayanan": {
        "achieved": 0,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 0,
        "target": 2.6
      }
    }
  },
  {
    "id": "doc-070",
    "pogiId": "#POGI-2026-1070",
    "name": "dr. Bambang Nugroho, Sp.OG, Subsp. FER",
    "initials": "BN",
    "nik": "3273100000057395",
    "province": "Sumatera Utara",
    "hospital": "RSUP H. Adam Malik Medan",
    "institution": "RSUP H. Adam Malik Medan",
    "phone": "+62 812-3405-2235",
    "assignedTo": "Rina Kartika",
    "pelayananCredits": 27,
    "pengabdianCredits": 10,
    "pembelajaranCredits": 13,
    "q4Credits": 0.9,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "21 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "BN",
    "q3Credits": 1.5,
    "year2026Credits": 17.4,
    "lastActivity": "17 Nov 2026, 15.50",
    "periodLabel": "21 November 2024 - 21 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 0.9,
        "target": 8,
        "redPercent": 11,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 0.5,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 0.4,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-071",
    "pogiId": "#POGI-2026-1071",
    "name": "Dr. dr. Bambang Fauzi, Sp.OG",
    "initials": "BF",
    "nik": "3273100000060044",
    "province": "Banten",
    "hospital": "RS Bethsaida Serpong",
    "institution": "RS Bethsaida Serpong",
    "phone": "+62 812-3516-2292",
    "assignedTo": "Melati Sihombing",
    "pelayananCredits": 30,
    "pengabdianCredits": 13,
    "pembelajaranCredits": 16,
    "q4Credits": 3.6,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "24 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "BF",
    "q3Credits": 3.8,
    "year2026Credits": 21.9,
    "lastActivity": "16 Nov 2026, 16.07",
    "periodLabel": "24 Maret 2024 - 24 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 3.6,
        "target": 8,
        "redPercent": 45,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 2,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 1.6,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-072",
    "pogiId": "#POGI-2026-1072",
    "name": "dr. Yusuf Saputra, Sp.OG, Subsp. Onk",
    "initials": "YS",
    "nik": "3273100000060927",
    "province": "Banten",
    "hospital": "RSUD Banten",
    "institution": "RSUD Banten",
    "phone": "+62 812-3553-2311",
    "assignedTo": "Wahyu Setiawan",
    "pelayananCredits": 55,
    "pengabdianCredits": 25,
    "pembelajaranCredits": 35,
    "q4Credits": 8.5,
    "pendingCredits": 0,
    "status": "Complete",
    "strExpiry": "25 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "YS",
    "q3Credits": 8.6,
    "year2026Credits": 33.6,
    "lastActivity": "15 Nov 2026, 08.24",
    "periodLabel": "25 November 2024 - 25 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 8.5,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 0,
        "isGreen": true
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 0
      },
      "pelayanan": {
        "achieved": 5,
        "target": 5
      },
      "pembelajaran": {
        "achieved": 3.5,
        "target": 3
      }
    }
  },
  {
    "id": "doc-073",
    "pogiId": "#POGI-2026-1073",
    "name": "Prof. Dr. dr. Surya Santoso, Sp.OG, Subsp. FER",
    "initials": "SS",
    "nik": "3273100000061810",
    "province": "Banten",
    "hospital": "RSUD Kabupaten Tangerang",
    "institution": "RSUD Kabupaten Tangerang",
    "phone": "+62 812-3590-2330",
    "assignedTo": "Siti Rahma",
    "pelayananCredits": 32,
    "pengabdianCredits": 5,
    "pembelajaranCredits": 18,
    "q4Credits": 5.4,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "26 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "SS",
    "q3Credits": 4.6,
    "year2026Credits": 22.6,
    "lastActivity": "14 Nov 2026, 09.41",
    "periodLabel": "26 Maret 2024 - 26 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 5.4,
        "target": 12.5,
        "redPercent": 43,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 3,
        "target": 6.9
      },
      "pembelajaran": {
        "achieved": 2.4,
        "target": 4.1
      }
    }
  },
  {
    "id": "doc-074",
    "pogiId": "#POGI-2026-1074",
    "name": "dr. Asep Fauzi, Sp.OG(K)",
    "initials": "AF",
    "nik": "3273100000062693",
    "province": "Banten",
    "hospital": "RS Bethsaida Serpong",
    "institution": "RS Bethsaida Serpong",
    "phone": "+62 812-3627-2349",
    "assignedTo": "Dewi Lestari",
    "pelayananCredits": 33,
    "pengabdianCredits": 6,
    "pembelajaranCredits": 19,
    "q4Credits": 6.3,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "27 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "AF",
    "q3Credits": 5.6,
    "year2026Credits": 24.5,
    "lastActivity": "13 Nov 2026, 10.58",
    "periodLabel": "27 November 2024 - 27 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 6.3,
        "target": 14,
        "redPercent": 45,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 3.5,
        "target": 7.7
      },
      "pembelajaran": {
        "achieved": 2.8,
        "target": 4.8
      }
    }
  },
  {
    "id": "doc-075",
    "pogiId": "#POGI-2026-1075",
    "name": "dr. Bambang Saputra, Sp.OG, Subsp. KFM",
    "initials": "BS",
    "nik": "3273100000063576",
    "province": "Banten",
    "hospital": "RSUD Banten",
    "institution": "RSUD Banten",
    "phone": "+62 812-3664-2368",
    "assignedTo": "Rina Kartika",
    "pelayananCredits": 34,
    "pengabdianCredits": 7,
    "pembelajaranCredits": 20,
    "q4Credits": 0,
    "pendingCredits": 1.5,
    "status": "Incomplete",
    "strExpiry": "10 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "BS",
    "q3Credits": 1.5,
    "year2026Credits": 15.5,
    "lastActivity": "12 Nov 2026, 11.15",
    "periodLabel": "10 Maret 2024 - 10 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 0,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 19
      },
      "pending": 1.5,
      "pengabdian": {
        "achieved": 0,
        "target": 1
      },
      "pelayanan": {
        "achieved": 0,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 0,
        "target": 2.6
      }
    }
  },
  {
    "id": "doc-076",
    "pogiId": "#POGI-2026-1076",
    "name": "dr. Yusuf Santoso, Sp.OG, Subsp. Obginsos",
    "initials": "YS",
    "nik": "3273100000064459",
    "province": "Banten",
    "hospital": "RSUD Kabupaten Tangerang",
    "institution": "RSUD Kabupaten Tangerang",
    "phone": "+62 812-3701-2387",
    "assignedTo": "Melati Sihombing",
    "pelayananCredits": 35,
    "pengabdianCredits": 8,
    "pembelajaranCredits": 21,
    "q4Credits": 0.9,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "11 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "YS",
    "q3Credits": 1.5,
    "year2026Credits": 17.4,
    "lastActivity": "11 Nov 2026, 12.32",
    "periodLabel": "11 November 2024 - 11 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 0.9,
        "target": 8,
        "redPercent": 11,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 0.5,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 0.4,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-077",
    "pogiId": "#POGI-2026-1077",
    "name": "dr. Ahmad Santoso, Sp.OG, Subsp. Obginsos",
    "initials": "AS",
    "nik": "3273100000067991",
    "province": "Jawa Barat",
    "hospital": "RSUD Al-Ihsan",
    "institution": "RSUD Al-Ihsan",
    "phone": "+62 812-3849-2463",
    "assignedTo": "Wahyu Setiawan",
    "pelayananCredits": 14,
    "pengabdianCredits": 12,
    "pembelajaranCredits": 10,
    "q4Credits": 4.5,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "15 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "AS",
    "q3Credits": 3.9,
    "year2026Credits": 23.3,
    "lastActivity": "10 Nov 2026, 13.49",
    "periodLabel": "15 November 2024 - 15 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 4.5,
        "target": 8,
        "redPercent": 56,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 2.5,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 2,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-078",
    "pogiId": "#POGI-2026-1078",
    "name": "Prof. Dr. dr. Dedi Fauzi, Sp.OG, Subsp. Urogin-RE",
    "initials": "DF",
    "nik": "3273100000068874",
    "province": "Jawa Barat",
    "hospital": "RSUP Dr. Hasan Sadikin Bandung",
    "institution": "RSUP Dr. Hasan Sadikin Bandung",
    "phone": "+62 812-3886-2482",
    "assignedTo": "Siti Rahma",
    "pelayananCredits": 15,
    "pengabdianCredits": 13,
    "pembelajaranCredits": 11,
    "q4Credits": 5.4,
    "pendingCredits": 2,
    "status": "Incomplete",
    "strExpiry": "16 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "DF",
    "q3Credits": 4.9,
    "year2026Credits": 22.1,
    "lastActivity": "09 Nov 2026, 14.06",
    "periodLabel": "16 Maret 2024 - 16 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 5.4,
        "target": 12.5,
        "redPercent": 43,
        "yellowPercent": 16
      },
      "pending": 2,
      "pengabdian": {
        "achieved": 0.5,
        "target": 1
      },
      "pelayanan": {
        "achieved": 3,
        "target": 6.9
      },
      "pembelajaran": {
        "achieved": 1.9,
        "target": 4.6
      }
    }
  },
  {
    "id": "doc-079",
    "pogiId": "#POGI-2026-1079",
    "name": "dr. Fajar Saputra, Sp.OG",
    "initials": "FS",
    "nik": "3273100000069757",
    "province": "Jawa Barat",
    "hospital": "RSUD Kota Bogor",
    "institution": "RSUD Kota Bogor",
    "phone": "+62 812-3923-2501",
    "assignedTo": "Dewi Lestari",
    "pelayananCredits": 16,
    "pengabdianCredits": 14,
    "pembelajaranCredits": 12,
    "q4Credits": 6.3,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "17 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "FS",
    "q3Credits": 5.9,
    "year2026Credits": 24,
    "lastActivity": "08 Nov 2026, 15.23",
    "periodLabel": "17 November 2024 - 17 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 6.3,
        "target": 14,
        "redPercent": 45,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 3.5,
        "target": 7.7
      },
      "pembelajaran": {
        "achieved": 2.8,
        "target": 4.8
      }
    }
  },
  {
    "id": "doc-080",
    "pogiId": "#POGI-2026-1080",
    "name": "dr. Arif Santoso, Sp.OG, Subsp. Onk",
    "initials": "AS",
    "nik": "3273100000070640",
    "province": "Jawa Barat",
    "hospital": "RS Santosa Bandung",
    "institution": "RS Santosa Bandung",
    "phone": "+62 812-3960-2520",
    "assignedTo": "Rina Kartika",
    "pelayananCredits": 17,
    "pengabdianCredits": 5,
    "pembelajaranCredits": 13,
    "q4Credits": 0,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "18 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "AS",
    "q3Credits": 1.5,
    "year2026Credits": 15,
    "lastActivity": "07 Nov 2026, 16.40",
    "periodLabel": "18 Maret 2024 - 18 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 0,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 0,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 0,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-081",
    "pogiId": "#POGI-2026-1081",
    "name": "dr. Ahmad Fauzi, Sp.OG, Subsp. FER",
    "initials": "AF",
    "nik": "3273100000071523",
    "province": "Jawa Barat",
    "hospital": "RSUD Al-Ihsan",
    "institution": "RSUD Al-Ihsan",
    "phone": "+62 812-3997-2539",
    "assignedTo": "Melati Sihombing",
    "pelayananCredits": 18,
    "pengabdianCredits": 6,
    "pembelajaranCredits": 14,
    "q4Credits": 0.9,
    "pendingCredits": 1,
    "status": "Incomplete",
    "strExpiry": "19 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "AF",
    "q3Credits": 1.5,
    "year2026Credits": 16.9,
    "lastActivity": "06 Nov 2026, 08.57",
    "periodLabel": "19 November 2024 - 19 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 0.9,
        "target": 8,
        "redPercent": 11,
        "yellowPercent": 13
      },
      "pending": 1,
      "pengabdian": {
        "achieved": 0,
        "target": 1
      },
      "pelayanan": {
        "achieved": 0.5,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 0.4,
        "target": 2.6
      }
    }
  },
  {
    "id": "doc-082",
    "pogiId": "#POGI-2026-1082",
    "name": "dr. Christy Fauzi, Sp.OG, Subsp. Urogin-RE",
    "initials": "CF",
    "nik": "3273100000072406",
    "province": "DI Yogyakarta",
    "hospital": "RSUP Dr. Sardjito Yogyakarta",
    "institution": "RSUP Dr. Sardjito Yogyakarta",
    "phone": "+62 812-4034-2558",
    "assignedTo": "Wahyu Setiawan",
    "pelayananCredits": 67,
    "pengabdianCredits": 22,
    "pembelajaranCredits": 30,
    "q4Credits": 12,
    "pendingCredits": 0,
    "status": "Complete",
    "strExpiry": "20 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "CF",
    "q3Credits": 9.6,
    "year2026Credits": 36,
    "lastActivity": "05 Nov 2026, 09.14",
    "periodLabel": "20 Maret 2024 - 20 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 12,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 0,
        "isGreen": true
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 1,
        "target": 0.5
      },
      "pelayanan": {
        "achieved": 6.6,
        "target": 5
      },
      "pembelajaran": {
        "achieved": 4.4,
        "target": 2.5
      }
    }
  },
  {
    "id": "doc-083",
    "pogiId": "#POGI-2026-1083",
    "name": "dr. Eko Saputra, Sp.OG",
    "initials": "ES",
    "nik": "3273100000073289",
    "province": "DI Yogyakarta",
    "hospital": "RS Panti Rapih",
    "institution": "RS Panti Rapih",
    "phone": "+62 812-4071-2577",
    "assignedTo": "Siti Rahma",
    "pelayananCredits": 68,
    "pengabdianCredits": 23,
    "pembelajaranCredits": 31,
    "q4Credits": 12.8,
    "pendingCredits": 0,
    "status": "Complete",
    "strExpiry": "21 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "ES",
    "q3Credits": 10,
    "year2026Credits": 36.8,
    "lastActivity": "04 Nov 2026, 10.31",
    "periodLabel": "21 November 2024 - 21 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 12.8,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 0,
        "isGreen": true
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 1,
        "target": 0.5
      },
      "pelayanan": {
        "achieved": 7,
        "target": 5.5
      },
      "pembelajaran": {
        "achieved": 4.8,
        "target": 2
      }
    }
  },
  {
    "id": "doc-084",
    "pogiId": "#POGI-2026-1084",
    "name": "Dr. dr. Rudi Santoso, Sp.OG, Subsp. Onk",
    "initials": "RS",
    "nik": "3273100000074172",
    "province": "DI Yogyakarta",
    "hospital": "RS PKU Muhammadiyah",
    "institution": "RS PKU Muhammadiyah",
    "phone": "+62 812-4108-2596",
    "assignedTo": "Dewi Lestari",
    "pelayananCredits": 21,
    "pengabdianCredits": 9,
    "pembelajaranCredits": 17,
    "q4Credits": 3.6,
    "pendingCredits": 2.5,
    "status": "Incomplete",
    "strExpiry": "22 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "RS",
    "q3Credits": 2.9,
    "year2026Credits": 19.4,
    "lastActivity": "17 Nov 2026, 11.48",
    "periodLabel": "22 Maret 2024 - 22 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 3.6,
        "target": 8,
        "redPercent": 45,
        "yellowPercent": 31
      },
      "pending": 2.5,
      "pengabdian": {
        "achieved": 0.5,
        "target": 1
      },
      "pelayanan": {
        "achieved": 2,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 1.1,
        "target": 2.6
      }
    }
  },
  {
    "id": "doc-085",
    "pogiId": "#POGI-2026-1085",
    "name": "dr. Doni Fauzi, Sp.OG, Subsp. FER",
    "initials": "DF",
    "nik": "3273100000075055",
    "province": "DI Yogyakarta",
    "hospital": "RSUP Dr. Sardjito Yogyakarta",
    "institution": "RSUP Dr. Sardjito Yogyakarta",
    "phone": "+62 812-4145-2615",
    "assignedTo": "Rina Kartika",
    "pelayananCredits": 22,
    "pengabdianCredits": 10,
    "pembelajaranCredits": 18,
    "q4Credits": 4.5,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "23 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "DF",
    "q3Credits": 3.9,
    "year2026Credits": 21.3,
    "lastActivity": "16 Nov 2026, 12.05",
    "periodLabel": "23 November 2024 - 23 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 4.5,
        "target": 8,
        "redPercent": 56,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 2.5,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 2,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-086",
    "pogiId": "#POGI-2026-1086",
    "name": "Prof. Dr. dr. Christy Saputra, Sp.OG(K)",
    "initials": "CS",
    "nik": "3273100000075938",
    "province": "DI Yogyakarta",
    "hospital": "RS Panti Rapih",
    "institution": "RS Panti Rapih",
    "phone": "+62 812-4182-2634",
    "assignedTo": "Melati Sihombing",
    "pelayananCredits": 23,
    "pengabdianCredits": 11,
    "pembelajaranCredits": 19,
    "q4Credits": 5.4,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "24 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "CS",
    "q3Credits": 4.9,
    "year2026Credits": 23.1,
    "lastActivity": "15 Nov 2026, 13.22",
    "periodLabel": "24 Maret 2024 - 24 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 5.4,
        "target": 9.5,
        "redPercent": 57,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 3,
        "target": 5.2
      },
      "pembelajaran": {
        "achieved": 2.4,
        "target": 2.8
      }
    }
  },
  {
    "id": "doc-087",
    "pogiId": "#POGI-2026-1087",
    "name": "dr. Eko Santoso, Sp.OG, Subsp. KFM",
    "initials": "ES",
    "nik": "3273100000076821",
    "province": "DI Yogyakarta",
    "hospital": "RS PKU Muhammadiyah",
    "institution": "RS PKU Muhammadiyah",
    "phone": "+62 812-4219-2653",
    "assignedTo": "Wahyu Setiawan",
    "pelayananCredits": 24,
    "pengabdianCredits": 12,
    "pembelajaranCredits": 20,
    "q4Credits": 6.3,
    "pendingCredits": 1.5,
    "status": "Incomplete",
    "strExpiry": "25 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "ES",
    "q3Credits": 5.9,
    "year2026Credits": 25,
    "lastActivity": "14 Nov 2026, 14.39",
    "periodLabel": "25 November 2024 - 25 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 6.3,
        "target": 11,
        "redPercent": 57,
        "yellowPercent": 14
      },
      "pending": 1.5,
      "pengabdian": {
        "achieved": 0.5,
        "target": 1
      },
      "pelayanan": {
        "achieved": 3.5,
        "target": 6.1
      },
      "pembelajaran": {
        "achieved": 2.3,
        "target": 3.9
      }
    }
  },
  {
    "id": "doc-088",
    "pogiId": "#POGI-2026-1088",
    "name": "dr. Rudi Fauzi, Sp.OG, Subsp. Obginsos",
    "initials": "RF",
    "nik": "3273100000077704",
    "province": "DI Yogyakarta",
    "hospital": "RSUP Dr. Sardjito Yogyakarta",
    "institution": "RSUP Dr. Sardjito Yogyakarta",
    "phone": "+62 812-4256-2672",
    "assignedTo": "Siti Rahma",
    "pelayananCredits": 25,
    "pengabdianCredits": 13,
    "pembelajaranCredits": 21,
    "q4Credits": 0,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "26 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "RF",
    "q3Credits": 1.5,
    "year2026Credits": 16,
    "lastActivity": "13 Nov 2026, 15.56",
    "periodLabel": "26 Maret 2024 - 26 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 0,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 0,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 0,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-089",
    "pogiId": "#POGI-2026-1089",
    "name": "dr. Indah Kusuma, Sp.OG, Subsp. Urogin-RE",
    "initials": "IK",
    "nik": "3273100000078587",
    "province": "Jawa Timur",
    "hospital": "RSUD Dr. Soetomo Surabaya",
    "institution": "RSUD Dr. Soetomo Surabaya",
    "phone": "+62 812-4293-2691",
    "assignedTo": "Dewi Lestari",
    "pelayananCredits": 74,
    "pengabdianCredits": 29,
    "pembelajaranCredits": 37,
    "q4Credits": 12,
    "pendingCredits": 0,
    "status": "Complete",
    "strExpiry": "27 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "IK",
    "q3Credits": 10,
    "year2026Credits": 36,
    "lastActivity": "12 Nov 2026, 16.13",
    "periodLabel": "27 November 2024 - 27 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 12,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 0,
        "isGreen": true
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0.5,
        "target": 0.5
      },
      "pelayanan": {
        "achieved": 6.6,
        "target": 5.5
      },
      "pembelajaran": {
        "achieved": 4.9,
        "target": 2
      }
    }
  },
  {
    "id": "doc-090",
    "pogiId": "#POGI-2026-1090",
    "name": "dr. Kartika Simatupang, Sp.OG",
    "initials": "KS",
    "nik": "3273100000079470",
    "province": "Jawa Timur",
    "hospital": "RS Universitas Airlangga",
    "institution": "RS Universitas Airlangga",
    "phone": "+62 812-4330-2710",
    "assignedTo": "Rina Kartika",
    "pelayananCredits": 45,
    "pengabdianCredits": 15,
    "pembelajaranCredits": 38,
    "q4Credits": 12.8,
    "pendingCredits": 0.5,
    "status": "Complete",
    "strExpiry": "10 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "KS",
    "q3Credits": 9.4,
    "year2026Credits": 36.8,
    "lastActivity": "11 Nov 2026, 08.30",
    "periodLabel": "10 Maret 2024 - 10 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 12.8,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 0,
        "isGreen": true
      },
      "pending": 0.5,
      "pengabdian": {
        "achieved": 1,
        "target": 0
      },
      "pelayanan": {
        "achieved": 7,
        "target": 5
      },
      "pembelajaran": {
        "achieved": 4.8,
        "target": 3
      }
    }
  },
  {
    "id": "doc-091",
    "pogiId": "#POGI-2026-1091",
    "name": "dr. Nenden Kurniawan, Sp.OG, Subsp. Onk",
    "initials": "NK",
    "nik": "3273100000080353",
    "province": "Jawa Timur",
    "hospital": "RSUD Saiful Anwar Malang",
    "institution": "RSUD Saiful Anwar Malang",
    "phone": "+62 812-4367-2729",
    "assignedTo": "Melati Sihombing",
    "pelayananCredits": 28,
    "pengabdianCredits": 6,
    "pembelajaranCredits": 9,
    "q4Credits": 2.7,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "11 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "NK",
    "q3Credits": 3.1,
    "year2026Credits": 18.6,
    "lastActivity": "10 Nov 2026, 09.47",
    "periodLabel": "11 November 2024 - 11 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 2.7,
        "target": 8,
        "redPercent": 34,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 1.5,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 1.2,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-092",
    "pogiId": "#POGI-2026-1092",
    "name": "Dr. dr. Rina Kusuma, Sp.OG, Subsp. FER",
    "initials": "RK",
    "nik": "3273100000081236",
    "province": "Jawa Timur",
    "hospital": "RS Premier Surabaya",
    "institution": "RS Premier Surabaya",
    "phone": "+62 812-4404-2748",
    "assignedTo": "Wahyu Setiawan",
    "pelayananCredits": 29,
    "pengabdianCredits": 7,
    "pembelajaranCredits": 10,
    "q4Credits": 3.6,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "12 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "RK",
    "q3Credits": 2.9,
    "year2026Credits": 20.4,
    "lastActivity": "09 Nov 2026, 10.04",
    "periodLabel": "12 Maret 2024 - 12 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 3.6,
        "target": 8,
        "redPercent": 45,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 2,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 1.6,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-093",
    "pogiId": "#POGI-2026-1093",
    "name": "dr. Indah Simatupang, Sp.OG(K)",
    "initials": "IS",
    "nik": "3273100000082119",
    "province": "Jawa Timur",
    "hospital": "RSUD Dr. Soetomo Surabaya",
    "institution": "RSUD Dr. Soetomo Surabaya",
    "phone": "+62 812-4441-2767",
    "assignedTo": "Siti Rahma",
    "pelayananCredits": 30,
    "pengabdianCredits": 8,
    "pembelajaranCredits": 11,
    "q4Credits": 4.5,
    "pendingCredits": 2,
    "status": "Incomplete",
    "strExpiry": "13 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "IS",
    "q3Credits": 3.9,
    "year2026Credits": 22.3,
    "lastActivity": "08 Nov 2026, 11.21",
    "periodLabel": "13 November 2024 - 13 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 4.5,
        "target": 8,
        "redPercent": 56,
        "yellowPercent": 25
      },
      "pending": 2,
      "pengabdian": {
        "achieved": 0.5,
        "target": 1
      },
      "pelayanan": {
        "achieved": 2.5,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 1.5,
        "target": 2.6
      }
    }
  },
  {
    "id": "doc-094",
    "pogiId": "#POGI-2026-1094",
    "name": "Prof. Dr. dr. Kartika Kurniawan, Sp.OG, Subsp. KFM",
    "initials": "KK",
    "nik": "3273100000083002",
    "province": "Jawa Timur",
    "hospital": "RS Universitas Airlangga",
    "institution": "RS Universitas Airlangga",
    "phone": "+62 812-4478-2786",
    "assignedTo": "Dewi Lestari",
    "pelayananCredits": 31,
    "pengabdianCredits": 9,
    "pembelajaranCredits": 12,
    "q4Credits": 5.4,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "14 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "KK",
    "q3Credits": 4.9,
    "year2026Credits": 24.1,
    "lastActivity": "07 Nov 2026, 12.38",
    "periodLabel": "14 Maret 2024 - 14 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 5.4,
        "target": 14,
        "redPercent": 39,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 3,
        "target": 7.7
      },
      "pembelajaran": {
        "achieved": 2.4,
        "target": 4.8
      }
    }
  },
  {
    "id": "doc-095",
    "pogiId": "#POGI-2026-1095",
    "name": "dr. Nenden Kusuma, Sp.OG, Subsp. Obginsos",
    "initials": "NK",
    "nik": "3273100000083885",
    "province": "Jawa Timur",
    "hospital": "RSUD Saiful Anwar Malang",
    "institution": "RSUD Saiful Anwar Malang",
    "phone": "+62 812-4515-2805",
    "assignedTo": "Rina Kartika",
    "pelayananCredits": 32,
    "pengabdianCredits": 10,
    "pembelajaranCredits": 13,
    "q4Credits": 6.3,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "15 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "NK",
    "q3Credits": 5.9,
    "year2026Credits": 26,
    "lastActivity": "06 Nov 2026, 13.55",
    "periodLabel": "15 November 2024 - 15 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 6.3,
        "target": 8,
        "redPercent": 79,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 3.5,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 2.8,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-096",
    "pogiId": "#POGI-2026-1096",
    "name": "dr. Rina Simatupang, Sp.OG, Subsp. Urogin-RE",
    "initials": "RS",
    "nik": "3273100000084768",
    "province": "Jawa Timur",
    "hospital": "RS Premier Surabaya",
    "institution": "RS Premier Surabaya",
    "phone": "+62 812-4552-2824",
    "assignedTo": "Melati Sihombing",
    "pelayananCredits": 33,
    "pengabdianCredits": 11,
    "pembelajaranCredits": 14,
    "q4Credits": 0,
    "pendingCredits": 1,
    "status": "Incomplete",
    "strExpiry": "16 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "RS",
    "q3Credits": 1.5,
    "year2026Credits": 14,
    "lastActivity": "05 Nov 2026, 14.12",
    "periodLabel": "16 Maret 2024 - 16 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 0,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 13
      },
      "pending": 1,
      "pengabdian": {
        "achieved": 0,
        "target": 1
      },
      "pelayanan": {
        "achieved": 0,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 0,
        "target": 2.6
      }
    }
  },
  {
    "id": "doc-097",
    "pogiId": "#POGI-2026-1097",
    "name": "dr. Siti Simatupang, Sp.OG(K)",
    "initials": "SS",
    "nik": "3273100000085651",
    "province": "Sumatera Barat",
    "hospital": "RSUP Dr. M. Djamil Padang",
    "institution": "RSUP Dr. M. Djamil Padang",
    "phone": "+62 812-4589-2843",
    "assignedTo": "Wahyu Setiawan",
    "pelayananCredits": 52,
    "pengabdianCredits": 22,
    "pembelajaranCredits": 45,
    "q4Credits": 12.8,
    "pendingCredits": 0,
    "status": "Complete",
    "strExpiry": "17 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "SS",
    "q3Credits": 9.8,
    "year2026Credits": 36.8,
    "lastActivity": "04 Nov 2026, 15.29",
    "periodLabel": "17 November 2024 - 17 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 12.8,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 0,
        "isGreen": true
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0.5,
        "target": 0.5
      },
      "pelayanan": {
        "achieved": 7,
        "target": 5.5
      },
      "pembelajaran": {
        "achieved": 5.3,
        "target": 2
      }
    }
  },
  {
    "id": "doc-098",
    "pogiId": "#POGI-2026-1098",
    "name": "dr. Rina Kurniawan, Sp.OG, Subsp. KFM",
    "initials": "RK",
    "nik": "3273100000086534",
    "province": "Sumatera Barat",
    "hospital": "RS Yos Sudarso Padang",
    "institution": "RS Yos Sudarso Padang",
    "phone": "+62 812-4626-2862",
    "assignedTo": "Siti Rahma",
    "pelayananCredits": 53,
    "pengabdianCredits": 23,
    "pembelajaranCredits": 46,
    "q4Credits": 8,
    "pendingCredits": 0,
    "status": "Complete",
    "strExpiry": "18 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "RK",
    "q3Credits": 8.6,
    "year2026Credits": 32,
    "lastActivity": "17 Nov 2026, 16.46",
    "periodLabel": "18 Maret 2024 - 18 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 8,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 0,
        "isGreen": true
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 1,
        "target": 0.5
      },
      "pelayanan": {
        "achieved": 5,
        "target": 5
      },
      "pembelajaran": {
        "achieved": 2,
        "target": 2.5
      }
    }
  },
  {
    "id": "doc-099",
    "pogiId": "#POGI-2026-1099",
    "name": "dr. Dian Kusuma, Sp.OG, Subsp. Obginsos",
    "initials": "DK",
    "nik": "3273100000087417",
    "province": "Sumatera Barat",
    "hospital": "RSUP Dr. M. Djamil Padang",
    "institution": "RSUP Dr. M. Djamil Padang",
    "phone": "+62 812-4663-2881",
    "assignedTo": "Dewi Lestari",
    "pelayananCredits": 36,
    "pengabdianCredits": 14,
    "pembelajaranCredits": 17,
    "q4Credits": 2.7,
    "pendingCredits": 2.5,
    "status": "Incomplete",
    "strExpiry": "19 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "DK",
    "q3Credits": 3.1,
    "year2026Credits": 19.6,
    "lastActivity": "16 Nov 2026, 08.03",
    "periodLabel": "19 November 2024 - 19 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 2.7,
        "target": 8,
        "redPercent": 34,
        "yellowPercent": 31
      },
      "pending": 2.5,
      "pengabdian": {
        "achieved": 0.5,
        "target": 1
      },
      "pelayanan": {
        "achieved": 1.5,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 0.7,
        "target": 2.6
      }
    }
  },
  {
    "id": "doc-100",
    "pogiId": "#POGI-2026-1100",
    "name": "Dr. dr. Ronald Simatupang, Sp.OG, Subsp. Urogin-RE",
    "initials": "RS",
    "nik": "3273100000088300",
    "province": "Sumatera Barat",
    "hospital": "RS Yos Sudarso Padang",
    "institution": "RS Yos Sudarso Padang",
    "phone": "+62 812-4700-2900",
    "assignedTo": "Rina Kartika",
    "pelayananCredits": 12,
    "pengabdianCredits": 5,
    "pembelajaranCredits": 18,
    "q4Credits": 3.6,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "20 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "RS",
    "q3Credits": 2.9,
    "year2026Credits": 21.4,
    "lastActivity": "15 Nov 2026, 09.20",
    "periodLabel": "20 Maret 2024 - 20 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 3.6,
        "target": 8,
        "redPercent": 45,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 2,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 1.6,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-101",
    "pogiId": "#POGI-2026-1101",
    "name": "dr. Sri Mandagi, Sp.OG, Subsp. Obginsos",
    "initials": "SM",
    "nik": "3273100000089183",
    "province": "Riau",
    "hospital": "RSUD Arifin Achmad Pekanbaru",
    "institution": "RSUD Arifin Achmad Pekanbaru",
    "phone": "+62 812-4737-2919",
    "assignedTo": "Melati Sihombing",
    "pelayananCredits": 56,
    "pengabdianCredits": 26,
    "pembelajaranCredits": 29,
    "q4Credits": 10.4,
    "pendingCredits": 0,
    "status": "Complete",
    "strExpiry": "21 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "SM",
    "q3Credits": 8.9,
    "year2026Credits": 34.4,
    "lastActivity": "14 Nov 2026, 10.37",
    "periodLabel": "21 November 2024 - 21 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 10.4,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 0,
        "isGreen": true
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0.5,
        "target": 0.5
      },
      "pelayanan": {
        "achieved": 5.7,
        "target": 5.5
      },
      "pembelajaran": {
        "achieved": 4.2,
        "target": 2
      }
    }
  },
  {
    "id": "doc-102",
    "pogiId": "#POGI-2026-1102",
    "name": "Prof. Dr. dr. Mega Wibowo, Sp.OG, Subsp. Urogin-RE",
    "initials": "MW",
    "nik": "3273100000090066",
    "province": "Riau",
    "hospital": "RS Awal Bros Pekanbaru",
    "institution": "RS Awal Bros Pekanbaru",
    "phone": "+62 812-4774-2938",
    "assignedTo": "Wahyu Setiawan",
    "pelayananCredits": 14,
    "pengabdianCredits": 7,
    "pembelajaranCredits": 20,
    "q4Credits": 5.4,
    "pendingCredits": 1.5,
    "status": "Incomplete",
    "strExpiry": "22 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "MW",
    "q3Credits": 4.9,
    "year2026Credits": 22.1,
    "lastActivity": "13 Nov 2026, 11.54",
    "periodLabel": "22 Maret 2024 - 22 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 5.4,
        "target": 11,
        "redPercent": 49,
        "yellowPercent": 14
      },
      "pending": 1.5,
      "pengabdian": {
        "achieved": 0.5,
        "target": 1
      },
      "pelayanan": {
        "achieved": 3,
        "target": 6.1
      },
      "pembelajaran": {
        "achieved": 1.9,
        "target": 3.9
      }
    }
  },
  {
    "id": "doc-103",
    "pogiId": "#POGI-2026-1103",
    "name": "dr. Cut Pratama, Sp.OG",
    "initials": "CP",
    "nik": "3273100000090949",
    "province": "Riau",
    "hospital": "RSUD Arifin Achmad Pekanbaru",
    "institution": "RSUD Arifin Achmad Pekanbaru",
    "phone": "+62 812-4811-2957",
    "assignedTo": "Siti Rahma",
    "pelayananCredits": 15,
    "pengabdianCredits": 8,
    "pembelajaranCredits": 21,
    "q4Credits": 6.3,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "23 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "CP",
    "q3Credits": 5.9,
    "year2026Credits": 24,
    "lastActivity": "12 Nov 2026, 12.11",
    "periodLabel": "23 November 2024 - 23 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 6.3,
        "target": 12.5,
        "redPercent": 50,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 3.5,
        "target": 6.9
      },
      "pembelajaran": {
        "achieved": 2.8,
        "target": 4.1
      }
    }
  },
  {
    "id": "doc-104",
    "pogiId": "#POGI-2026-1104",
    "name": "dr. Ratna Mandagi, Sp.OG, Subsp. Onk",
    "initials": "RM",
    "nik": "3273100000091832",
    "province": "Riau",
    "hospital": "RS Awal Bros Pekanbaru",
    "institution": "RS Awal Bros Pekanbaru",
    "phone": "+62 812-4848-2976",
    "assignedTo": "Dewi Lestari",
    "pelayananCredits": 16,
    "pengabdianCredits": 9,
    "pembelajaranCredits": 22,
    "q4Credits": 0,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "24 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "RM",
    "q3Credits": 1.5,
    "year2026Credits": 15,
    "lastActivity": "11 Nov 2026, 13.28",
    "periodLabel": "24 Maret 2024 - 24 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 0,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 0,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 0,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-105",
    "pogiId": "#POGI-2026-1105",
    "name": "dr. Sri Wibowo, Sp.OG, Subsp. FER",
    "initials": "SW",
    "nik": "3273100000092715",
    "province": "Riau",
    "hospital": "RSUD Arifin Achmad Pekanbaru",
    "institution": "RSUD Arifin Achmad Pekanbaru",
    "phone": "+62 812-4885-2995",
    "assignedTo": "Rina Kartika",
    "pelayananCredits": 17,
    "pengabdianCredits": 10,
    "pembelajaranCredits": 8,
    "q4Credits": 0.9,
    "pendingCredits": 0.5,
    "status": "Incomplete",
    "strExpiry": "25 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "SW",
    "q3Credits": 1.5,
    "year2026Credits": 16.9,
    "lastActivity": "10 Nov 2026, 14.45",
    "periodLabel": "25 November 2024 - 25 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 0.9,
        "target": 8,
        "redPercent": 11,
        "yellowPercent": 6
      },
      "pending": 0.5,
      "pengabdian": {
        "achieved": 0,
        "target": 1
      },
      "pelayanan": {
        "achieved": 0.5,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 0.4,
        "target": 2.6
      }
    }
  },
  {
    "id": "doc-106",
    "pogiId": "#POGI-2026-1106",
    "name": "dr. Mega Pratama, Sp.OG(K)",
    "initials": "MP",
    "nik": "3273100000093598",
    "province": "Riau",
    "hospital": "RS Awal Bros Pekanbaru",
    "institution": "RS Awal Bros Pekanbaru",
    "phone": "+62 812-4922-3014",
    "assignedTo": "Melati Sihombing",
    "pelayananCredits": 18,
    "pengabdianCredits": 11,
    "pembelajaranCredits": 9,
    "q4Credits": 1.8,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "26 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "MP",
    "q3Credits": 2,
    "year2026Credits": 18.7,
    "lastActivity": "09 Nov 2026, 15.02",
    "periodLabel": "26 Maret 2024 - 26 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 1.8,
        "target": 8,
        "redPercent": 23,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 1,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 0.8,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-107",
    "pogiId": "#POGI-2026-1107",
    "name": "dr. Ronald Hermawan, Sp.OG, Subsp. Onk",
    "initials": "RH",
    "nik": "3273100000094481",
    "province": "Kalimantan Timur",
    "hospital": "RSUD Abdul Wahab Sjahranie Samarinda",
    "institution": "RSUD Abdul Wahab Sjahranie Samarinda",
    "phone": "+62 812-4959-3033",
    "assignedTo": "Wahyu Setiawan",
    "pelayananCredits": 62,
    "pengabdianCredits": 17,
    "pembelajaranCredits": 35,
    "q4Credits": 9.6,
    "pendingCredits": 0,
    "status": "Complete",
    "strExpiry": "27 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "RH",
    "q3Credits": 8.9,
    "year2026Credits": 33.6,
    "lastActivity": "08 Nov 2026, 16.19",
    "periodLabel": "27 November 2024 - 27 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 9.6,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 0,
        "isGreen": true
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 1,
        "target": 0.5
      },
      "pelayanan": {
        "achieved": 5.5,
        "target": 5.5
      },
      "pembelajaran": {
        "achieved": 3.1,
        "target": 2
      }
    }
  },
  {
    "id": "doc-108",
    "pogiId": "#POGI-2026-1108",
    "name": "Dr. dr. Siti Suryani, Sp.OG, Subsp. FER",
    "initials": "SS",
    "nik": "3273100000095364",
    "province": "Kalimantan Timur",
    "hospital": "RSUD Kanujoso Djatiwibowo Balikpapan",
    "institution": "RSUD Kanujoso Djatiwibowo Balikpapan",
    "phone": "+62 812-4996-3052",
    "assignedTo": "Siti Rahma",
    "pelayananCredits": 20,
    "pengabdianCredits": 13,
    "pembelajaranCredits": 11,
    "q4Credits": 3.6,
    "pendingCredits": 2,
    "status": "Incomplete",
    "strExpiry": "10 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "SS",
    "q3Credits": 2.9,
    "year2026Credits": 19.4,
    "lastActivity": "07 Nov 2026, 08.36",
    "periodLabel": "10 Maret 2024 - 10 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 3.6,
        "target": 8,
        "redPercent": 45,
        "yellowPercent": 25
      },
      "pending": 2,
      "pengabdian": {
        "achieved": 0.5,
        "target": 1
      },
      "pelayanan": {
        "achieved": 2,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 1.1,
        "target": 2.6
      }
    }
  },
  {
    "id": "doc-109",
    "pogiId": "#POGI-2026-1109",
    "name": "dr. Rina Siregar, Sp.OG(K)",
    "initials": "RS",
    "nik": "3273100000096247",
    "province": "Kalimantan Timur",
    "hospital": "RSUD Abdul Wahab Sjahranie Samarinda",
    "institution": "RSUD Abdul Wahab Sjahranie Samarinda",
    "phone": "+62 812-5033-3071",
    "assignedTo": "Dewi Lestari",
    "pelayananCredits": 21,
    "pengabdianCredits": 14,
    "pembelajaranCredits": 12,
    "q4Credits": 4.5,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "11 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "RS",
    "q3Credits": 3.9,
    "year2026Credits": 21.3,
    "lastActivity": "06 Nov 2026, 09.53",
    "periodLabel": "11 November 2024 - 11 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 4.5,
        "target": 8,
        "redPercent": 56,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 2.5,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 2,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-110",
    "pogiId": "#POGI-2026-1110",
    "name": "Prof. Dr. dr. Dian Hermawan, Sp.OG, Subsp. KFM",
    "initials": "DH",
    "nik": "3273100000097130",
    "province": "Kalimantan Timur",
    "hospital": "RSUD Kanujoso Djatiwibowo Balikpapan",
    "institution": "RSUD Kanujoso Djatiwibowo Balikpapan",
    "phone": "+62 812-5070-3090",
    "assignedTo": "Rina Kartika",
    "pelayananCredits": 22,
    "pengabdianCredits": 5,
    "pembelajaranCredits": 13,
    "q4Credits": 5.4,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "12 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "DH",
    "q3Credits": 4.9,
    "year2026Credits": 23.1,
    "lastActivity": "05 Nov 2026, 10.10",
    "periodLabel": "12 Maret 2024 - 12 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 5.4,
        "target": 8,
        "redPercent": 68,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 3,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 2.4,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-111",
    "pogiId": "#POGI-2026-1111",
    "name": "dr. Ronald Suryani, Sp.OG, Subsp. Obginsos",
    "initials": "RS",
    "nik": "3273100000098013",
    "province": "Kalimantan Timur",
    "hospital": "RSUD Abdul Wahab Sjahranie Samarinda",
    "institution": "RSUD Abdul Wahab Sjahranie Samarinda",
    "phone": "+62 812-5107-3109",
    "assignedTo": "Melati Sihombing",
    "pelayananCredits": 23,
    "pengabdianCredits": 6,
    "pembelajaranCredits": 14,
    "q4Credits": 6.3,
    "pendingCredits": 1,
    "status": "Incomplete",
    "strExpiry": "13 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "RS",
    "q3Credits": 5.9,
    "year2026Credits": 25,
    "lastActivity": "04 Nov 2026, 11.27",
    "periodLabel": "13 November 2024 - 13 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 6.3,
        "target": 9.5,
        "redPercent": 66,
        "yellowPercent": 11
      },
      "pending": 1,
      "pengabdian": {
        "achieved": 0.5,
        "target": 1
      },
      "pelayanan": {
        "achieved": 3.5,
        "target": 5.2
      },
      "pembelajaran": {
        "achieved": 2.3,
        "target": 3.3
      }
    }
  },
  {
    "id": "doc-112",
    "pogiId": "#POGI-2026-1112",
    "name": "dr. Siti Siregar, Sp.OG, Subsp. Urogin-RE",
    "initials": "SS",
    "nik": "3273100000098896",
    "province": "Kalimantan Timur",
    "hospital": "RSUD Kanujoso Djatiwibowo Balikpapan",
    "institution": "RSUD Kanujoso Djatiwibowo Balikpapan",
    "phone": "+62 812-5144-3128",
    "assignedTo": "Wahyu Setiawan",
    "pelayananCredits": 24,
    "pengabdianCredits": 7,
    "pembelajaranCredits": 15,
    "q4Credits": 0,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "14 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "SS",
    "q3Credits": 1.5,
    "year2026Credits": 16,
    "lastActivity": "17 Nov 2026, 12.44",
    "periodLabel": "14 Maret 2024 - 14 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 0,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 0,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 0,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-113",
    "pogiId": "#POGI-2026-1113",
    "name": "dr. Maya Kusuma, Sp.OG, Subsp. KFM",
    "initials": "MK",
    "nik": "3273100000099779",
    "province": "Sumatera Selatan",
    "hospital": "RSUP Dr. Mohammad Hoesin Palembang",
    "institution": "RSUP Dr. Mohammad Hoesin Palembang",
    "phone": "+62 812-5181-3147",
    "assignedTo": "Siti Rahma",
    "pelayananCredits": 68,
    "pengabdianCredits": 23,
    "pembelajaranCredits": 41,
    "q4Credits": 8.8,
    "pendingCredits": 0,
    "status": "Complete",
    "strExpiry": "15 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "MK",
    "q3Credits": 8.8,
    "year2026Credits": 32.8,
    "lastActivity": "16 Nov 2026, 13.01",
    "periodLabel": "15 November 2024 - 15 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 8.8,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 0,
        "isGreen": true
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0.5,
        "target": 0.5
      },
      "pelayanan": {
        "achieved": 5.5,
        "target": 5.5
      },
      "pembelajaran": {
        "achieved": 2.8,
        "target": 2
      }
    }
  },
  {
    "id": "doc-114",
    "pogiId": "#POGI-2026-1114",
    "name": "dr. Tari Simatupang, Sp.OG, Subsp. Obginsos",
    "initials": "TS",
    "nik": "3273100000100662",
    "province": "Sumatera Selatan",
    "hospital": "RS Siti Khadijah Palembang",
    "institution": "RS Siti Khadijah Palembang",
    "phone": "+62 812-5218-3166",
    "assignedTo": "Dewi Lestari",
    "pelayananCredits": 26,
    "pengabdianCredits": 9,
    "pembelajaranCredits": 17,
    "q4Credits": 1.8,
    "pendingCredits": 2.5,
    "status": "Incomplete",
    "strExpiry": "16 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "TS",
    "q3Credits": 2,
    "year2026Credits": 16.7,
    "lastActivity": "15 Nov 2026, 14.18",
    "periodLabel": "16 Maret 2024 - 16 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 1.8,
        "target": 8,
        "redPercent": 23,
        "yellowPercent": 31
      },
      "pending": 2.5,
      "pengabdian": {
        "achieved": 0,
        "target": 1
      },
      "pelayanan": {
        "achieved": 1,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 0.8,
        "target": 2.6
      }
    }
  },
  {
    "id": "doc-115",
    "pogiId": "#POGI-2026-1115",
    "name": "dr. Lestari Kurniawan, Sp.OG, Subsp. Urogin-RE",
    "initials": "LK",
    "nik": "3273100000101545",
    "province": "Sumatera Selatan",
    "hospital": "RSUP Dr. Mohammad Hoesin Palembang",
    "institution": "RSUP Dr. Mohammad Hoesin Palembang",
    "phone": "+62 812-5255-3185",
    "assignedTo": "Rina Kartika",
    "pelayananCredits": 27,
    "pengabdianCredits": 10,
    "pembelajaranCredits": 18,
    "q4Credits": 2.7,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "17 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "LK",
    "q3Credits": 3.1,
    "year2026Credits": 18.6,
    "lastActivity": "14 Nov 2026, 15.35",
    "periodLabel": "17 November 2024 - 17 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 2.7,
        "target": 8,
        "redPercent": 34,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 1.5,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 1.2,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-116",
    "pogiId": "#POGI-2026-1116",
    "name": "Dr. dr. Kevin Kusuma, Sp.OG",
    "initials": "KK",
    "nik": "3273100000102428",
    "province": "Sumatera Selatan",
    "hospital": "RS Siti Khadijah Palembang",
    "institution": "RS Siti Khadijah Palembang",
    "phone": "+62 812-5292-3204",
    "assignedTo": "Melati Sihombing",
    "pelayananCredits": 28,
    "pengabdianCredits": 11,
    "pembelajaranCredits": 19,
    "q4Credits": 3.6,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "18 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "KK",
    "q3Credits": 2.9,
    "year2026Credits": 20.4,
    "lastActivity": "13 Nov 2026, 16.52",
    "periodLabel": "18 Maret 2024 - 18 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 3.6,
        "target": 8,
        "redPercent": 45,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 2,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 1.6,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-117",
    "pogiId": "#POGI-2026-1117",
    "name": "dr. Maya Simatupang, Sp.OG, Subsp. Onk",
    "initials": "MS",
    "nik": "3273100000103311",
    "province": "Sumatera Selatan",
    "hospital": "RSUP Dr. Mohammad Hoesin Palembang",
    "institution": "RSUP Dr. Mohammad Hoesin Palembang",
    "phone": "+62 812-5329-3223",
    "assignedTo": "Wahyu Setiawan",
    "pelayananCredits": 29,
    "pengabdianCredits": 12,
    "pembelajaranCredits": 20,
    "q4Credits": 4.5,
    "pendingCredits": 1.5,
    "status": "Incomplete",
    "strExpiry": "19 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "MS",
    "q3Credits": 3.9,
    "year2026Credits": 22.3,
    "lastActivity": "12 Nov 2026, 08.09",
    "periodLabel": "19 November 2024 - 19 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 4.5,
        "target": 8,
        "redPercent": 56,
        "yellowPercent": 19
      },
      "pending": 1.5,
      "pengabdian": {
        "achieved": 0.5,
        "target": 1
      },
      "pelayanan": {
        "achieved": 2.5,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 1.5,
        "target": 2.6
      }
    }
  },
  {
    "id": "doc-118",
    "pogiId": "#POGI-2026-1118",
    "name": "Prof. Dr. dr. Tari Kurniawan, Sp.OG, Subsp. FER",
    "initials": "TK",
    "nik": "3273100000104194",
    "province": "Sumatera Selatan",
    "hospital": "RS Siti Khadijah Palembang",
    "institution": "RS Siti Khadijah Palembang",
    "phone": "+62 812-5366-3242",
    "assignedTo": "Siti Rahma",
    "pelayananCredits": 30,
    "pengabdianCredits": 13,
    "pembelajaranCredits": 21,
    "q4Credits": 5.4,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "20 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "TK",
    "q3Credits": 4.9,
    "year2026Credits": 24.1,
    "lastActivity": "11 Nov 2026, 09.26",
    "periodLabel": "20 Maret 2024 - 20 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 5.4,
        "target": 12.5,
        "redPercent": 43,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 3,
        "target": 6.9
      },
      "pembelajaran": {
        "achieved": 2.4,
        "target": 4.1
      }
    }
  },
  {
    "id": "doc-119",
    "pogiId": "#POGI-2026-1119",
    "name": "dr. Kartika Utami, Sp.OG",
    "initials": "KU",
    "nik": "3273100000105077",
    "province": "Lampung",
    "hospital": "RSUD Dr. H. Abdul Moeloek Bandar Lampung",
    "institution": "RSUD Dr. H. Abdul Moeloek Bandar Lampung",
    "phone": "+62 812-5403-3261",
    "assignedTo": "Dewi Lestari",
    "pelayananCredits": 74,
    "pengabdianCredits": 29,
    "pembelajaranCredits": 47,
    "q4Credits": 8,
    "pendingCredits": 0,
    "status": "Complete",
    "strExpiry": "21 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "KU",
    "q3Credits": 8.8,
    "year2026Credits": 32,
    "lastActivity": "10 Nov 2026, 10.43",
    "periodLabel": "21 November 2024 - 21 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 8,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 0,
        "isGreen": true
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 1,
        "target": 0.5
      },
      "pelayanan": {
        "achieved": 5.5,
        "target": 5.5
      },
      "pembelajaran": {
        "achieved": 1.5,
        "target": 2
      }
    }
  },
  {
    "id": "doc-120",
    "pogiId": "#POGI-2026-1120",
    "name": "dr. Nenden Rahmawati, Sp.OG, Subsp. Onk",
    "initials": "NR",
    "nik": "3273100000105960",
    "province": "Lampung",
    "hospital": "RS Imanuel Bandar Lampung",
    "institution": "RS Imanuel Bandar Lampung",
    "phone": "+62 812-5440-3280",
    "assignedTo": "Rina Kartika",
    "pelayananCredits": 32,
    "pengabdianCredits": 5,
    "pembelajaranCredits": 8,
    "q4Credits": 0,
    "pendingCredits": 0.5,
    "status": "Incomplete",
    "strExpiry": "22 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "NR",
    "q3Credits": 1.5,
    "year2026Credits": 14,
    "lastActivity": "09 Nov 2026, 11.00",
    "periodLabel": "22 Maret 2024 - 22 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 0,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 6
      },
      "pending": 0.5,
      "pengabdian": {
        "achieved": 0,
        "target": 1
      },
      "pelayanan": {
        "achieved": 0,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 0,
        "target": 2.6
      }
    }
  },
  {
    "id": "doc-121",
    "pogiId": "#POGI-2026-1121",
    "name": "dr. Rina Meurah, Sp.OG, Subsp. FER",
    "initials": "RM",
    "nik": "3273100000106843",
    "province": "Lampung",
    "hospital": "RSUD Dr. H. Abdul Moeloek Bandar Lampung",
    "institution": "RSUD Dr. H. Abdul Moeloek Bandar Lampung",
    "phone": "+62 812-5477-3299",
    "assignedTo": "Melati Sihombing",
    "pelayananCredits": 33,
    "pengabdianCredits": 6,
    "pembelajaranCredits": 9,
    "q4Credits": 0.9,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "23 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "RM",
    "q3Credits": 1.5,
    "year2026Credits": 15.9,
    "lastActivity": "08 Nov 2026, 12.17",
    "periodLabel": "23 November 2024 - 23 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 0.9,
        "target": 8,
        "redPercent": 11,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 0.5,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 0.4,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-122",
    "pogiId": "#POGI-2026-1122",
    "name": "dr. Indah Utami, Sp.OG(K)",
    "initials": "IU",
    "nik": "3273100000107726",
    "province": "Lampung",
    "hospital": "RS Imanuel Bandar Lampung",
    "institution": "RS Imanuel Bandar Lampung",
    "phone": "+62 812-5514-3318",
    "assignedTo": "Wahyu Setiawan",
    "pelayananCredits": 34,
    "pengabdianCredits": 7,
    "pembelajaranCredits": 10,
    "q4Credits": 1.8,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "24 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "IU",
    "q3Credits": 2,
    "year2026Credits": 17.7,
    "lastActivity": "07 Nov 2026, 13.34",
    "periodLabel": "24 Maret 2024 - 24 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 1.8,
        "target": 8,
        "redPercent": 23,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 1,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 0.8,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-123",
    "pogiId": "#POGI-2026-1123",
    "name": "dr. Kartika Rahmawati, Sp.OG, Subsp. KFM",
    "initials": "KR",
    "nik": "3273100000108609",
    "province": "Lampung",
    "hospital": "RSUD Dr. H. Abdul Moeloek Bandar Lampung",
    "institution": "RSUD Dr. H. Abdul Moeloek Bandar Lampung",
    "phone": "+62 812-5551-3337",
    "assignedTo": "Siti Rahma",
    "pelayananCredits": 35,
    "pengabdianCredits": 8,
    "pembelajaranCredits": 11,
    "q4Credits": 2.7,
    "pendingCredits": 2,
    "status": "Incomplete",
    "strExpiry": "25 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "KR",
    "q3Credits": 3.1,
    "year2026Credits": 19.6,
    "lastActivity": "06 Nov 2026, 14.51",
    "periodLabel": "25 November 2024 - 25 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 2.7,
        "target": 8,
        "redPercent": 34,
        "yellowPercent": 25
      },
      "pending": 2,
      "pengabdian": {
        "achieved": 0.5,
        "target": 1
      },
      "pelayanan": {
        "achieved": 1.5,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 0.7,
        "target": 2.6
      }
    }
  },
  {
    "id": "doc-124",
    "pogiId": "#POGI-2026-1124",
    "name": "Dr. dr. Ahmad Nasution, Sp.OG, Subsp. Urogin-RE",
    "initials": "AN",
    "nik": "3273100000109492",
    "province": "Kalimantan Barat",
    "hospital": "RSUD Dr. Soedarso Pontianak",
    "institution": "RSUD Dr. Soedarso Pontianak",
    "phone": "+62 812-5588-3356",
    "assignedTo": "Dewi Lestari",
    "pelayananCredits": 49,
    "pengabdianCredits": 19,
    "pembelajaranCredits": 32,
    "q4Credits": 12,
    "pendingCredits": 0,
    "status": "Complete",
    "strExpiry": "26 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "AN",
    "q3Credits": 10,
    "year2026Credits": 36,
    "lastActivity": "05 Nov 2026, 15.08",
    "periodLabel": "26 Maret 2024 - 26 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 12,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 0,
        "isGreen": true
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 0.5
      },
      "pelayanan": {
        "achieved": 6.6,
        "target": 5
      },
      "pembelajaran": {
        "achieved": 5.4,
        "target": 2.5
      }
    }
  },
  {
    "id": "doc-125",
    "pogiId": "#POGI-2026-1125",
    "name": "dr. Dedi Hidayat, Sp.OG",
    "initials": "DH",
    "nik": "3273100000110375",
    "province": "Kalimantan Barat",
    "hospital": "RS Antonius Pontianak",
    "institution": "RS Antonius Pontianak",
    "phone": "+62 812-5625-3375",
    "assignedTo": "Rina Kartika",
    "pelayananCredits": 12,
    "pengabdianCredits": 10,
    "pembelajaranCredits": 13,
    "q4Credits": 4.5,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "27 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "DH",
    "q3Credits": 3.9,
    "year2026Credits": 23.3,
    "lastActivity": "04 Nov 2026, 16.25",
    "periodLabel": "27 November 2024 - 27 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 4.5,
        "target": 8,
        "redPercent": 56,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 2.5,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 2,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-126",
    "pogiId": "#POGI-2026-1126",
    "name": "Prof. Dr. dr. Fajar Puspitasari, Sp.OG, Subsp. Onk",
    "initials": "FP",
    "nik": "3273100000111258",
    "province": "Kalimantan Barat",
    "hospital": "RSUD Dr. Soedarso Pontianak",
    "institution": "RSUD Dr. Soedarso Pontianak",
    "phone": "+62 812-5662-3394",
    "assignedTo": "Melati Sihombing",
    "pelayananCredits": 13,
    "pengabdianCredits": 11,
    "pembelajaranCredits": 14,
    "q4Credits": 5.4,
    "pendingCredits": 1,
    "status": "Incomplete",
    "strExpiry": "10 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "FP",
    "q3Credits": 4.9,
    "year2026Credits": 22.1,
    "lastActivity": "17 Nov 2026, 08.42",
    "periodLabel": "10 Maret 2024 - 10 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 5.4,
        "target": 9.5,
        "redPercent": 57,
        "yellowPercent": 11
      },
      "pending": 1,
      "pengabdian": {
        "achieved": 0.5,
        "target": 1
      },
      "pelayanan": {
        "achieved": 3,
        "target": 5.2
      },
      "pembelajaran": {
        "achieved": 1.9,
        "target": 3.3
      }
    }
  },
  {
    "id": "doc-127",
    "pogiId": "#POGI-2026-1127",
    "name": "dr. Arif Nasution, Sp.OG, Subsp. FER",
    "initials": "AN",
    "nik": "3273100000112141",
    "province": "Kalimantan Barat",
    "hospital": "RS Antonius Pontianak",
    "institution": "RS Antonius Pontianak",
    "phone": "+62 812-5699-3413",
    "assignedTo": "Wahyu Setiawan",
    "pelayananCredits": 14,
    "pengabdianCredits": 12,
    "pembelajaranCredits": 15,
    "q4Credits": 6.3,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "11 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "AN",
    "q3Credits": 5.9,
    "year2026Credits": 24,
    "lastActivity": "16 Nov 2026, 09.59",
    "periodLabel": "11 November 2024 - 11 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 6.3,
        "target": 11,
        "redPercent": 57,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 3.5,
        "target": 6.1
      },
      "pembelajaran": {
        "achieved": 2.8,
        "target": 3.4
      }
    }
  },
  {
    "id": "doc-128",
    "pogiId": "#POGI-2026-1128",
    "name": "dr. Ahmad Hidayat, Sp.OG(K)",
    "initials": "AH",
    "nik": "3273100000113024",
    "province": "Kalimantan Barat",
    "hospital": "RSUD Dr. Soedarso Pontianak",
    "institution": "RSUD Dr. Soedarso Pontianak",
    "phone": "+62 812-5736-3432",
    "assignedTo": "Siti Rahma",
    "pelayananCredits": 15,
    "pengabdianCredits": 13,
    "pembelajaranCredits": 16,
    "q4Credits": 0,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "12 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "AH",
    "q3Credits": 1.5,
    "year2026Credits": 15,
    "lastActivity": "15 Nov 2026, 10.16",
    "periodLabel": "12 Maret 2024 - 12 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 0,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 0,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 0,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-129",
    "pogiId": "#POGI-2026-1129",
    "name": "dr. Indah Kurniawan, Sp.OG, Subsp. Obginsos",
    "initials": "IK",
    "nik": "3273100000113907",
    "province": "Nusa Tenggara Barat",
    "hospital": "RSUD Provinsi NTB Mataram",
    "institution": "RSUD Provinsi NTB Mataram",
    "phone": "+62 812-5773-3451",
    "assignedTo": "Dewi Lestari",
    "pelayananCredits": 54,
    "pengabdianCredits": 24,
    "pembelajaranCredits": 37,
    "q4Credits": 10.4,
    "pendingCredits": 2.5,
    "status": "Complete",
    "strExpiry": "13 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "IK",
    "q3Credits": 9.5,
    "year2026Credits": 34.4,
    "lastActivity": "14 Nov 2026, 11.33",
    "periodLabel": "13 November 2024 - 13 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 10.4,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 0,
        "isGreen": true
      },
      "pending": 2.5,
      "pengabdian": {
        "achieved": 0.5,
        "target": 0
      },
      "pelayanan": {
        "achieved": 5.7,
        "target": 5.5
      },
      "pembelajaran": {
        "achieved": 4.2,
        "target": 2.5
      }
    }
  },
  {
    "id": "doc-130",
    "pogiId": "#POGI-2026-1130",
    "name": "dr. Kartika Kusuma, Sp.OG, Subsp. Urogin-RE",
    "initials": "KK",
    "nik": "3273100000114790",
    "province": "Nusa Tenggara Barat",
    "hospital": "RS Harapan Keluarga",
    "institution": "RS Harapan Keluarga",
    "phone": "+62 812-5810-3470",
    "assignedTo": "Rina Kartika",
    "pelayananCredits": 17,
    "pengabdianCredits": 5,
    "pembelajaranCredits": 18,
    "q4Credits": 1.8,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "14 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "KK",
    "q3Credits": 2,
    "year2026Credits": 18.7,
    "lastActivity": "13 Nov 2026, 12.50",
    "periodLabel": "14 Maret 2024 - 14 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 1.8,
        "target": 8,
        "redPercent": 23,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 1,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 0.8,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-131",
    "pogiId": "#POGI-2026-1131",
    "name": "dr. Nenden Simatupang, Sp.OG",
    "initials": "NS",
    "nik": "3273100000115673",
    "province": "Nusa Tenggara Barat",
    "hospital": "RSUD Provinsi NTB Mataram",
    "institution": "RSUD Provinsi NTB Mataram",
    "phone": "+62 812-5847-3489",
    "assignedTo": "Melati Sihombing",
    "pelayananCredits": 18,
    "pengabdianCredits": 6,
    "pembelajaranCredits": 19,
    "q4Credits": 2.7,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "15 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "NS",
    "q3Credits": 3.1,
    "year2026Credits": 20.6,
    "lastActivity": "12 Nov 2026, 13.07",
    "periodLabel": "15 November 2024 - 15 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 2.7,
        "target": 8,
        "redPercent": 34,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 1.5,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 1.2,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-132",
    "pogiId": "#POGI-2026-1132",
    "name": "Dr. dr. Rina Kurniawan, Sp.OG, Subsp. Onk",
    "initials": "RK",
    "nik": "3273100000116556",
    "province": "Nusa Tenggara Barat",
    "hospital": "RS Harapan Keluarga",
    "institution": "RS Harapan Keluarga",
    "phone": "+62 812-5884-3508",
    "assignedTo": "Wahyu Setiawan",
    "pelayananCredits": 19,
    "pengabdianCredits": 7,
    "pembelajaranCredits": 20,
    "q4Credits": 3.6,
    "pendingCredits": 1.5,
    "status": "Incomplete",
    "strExpiry": "16 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "RK",
    "q3Credits": 2.9,
    "year2026Credits": 19.4,
    "lastActivity": "11 Nov 2026, 14.24",
    "periodLabel": "16 Maret 2024 - 16 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 3.6,
        "target": 8,
        "redPercent": 45,
        "yellowPercent": 19
      },
      "pending": 1.5,
      "pengabdian": {
        "achieved": 0.5,
        "target": 1
      },
      "pelayanan": {
        "achieved": 2,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 1.1,
        "target": 2.6
      }
    }
  },
  {
    "id": "doc-133",
    "pogiId": "#POGI-2026-1133",
    "name": "dr. Lestari Wibowo, Sp.OG",
    "initials": "LW",
    "nik": "3273100000117439",
    "province": "Nusa Tenggara Timur",
    "hospital": "RSUD Prof. Dr. W. Z. Johannes Kupang",
    "institution": "RSUD Prof. Dr. W. Z. Johannes Kupang",
    "phone": "+62 812-5921-3527",
    "assignedTo": "Siti Rahma",
    "pelayananCredits": 20,
    "pengabdianCredits": 8,
    "pembelajaranCredits": 21,
    "q4Credits": 4.5,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "17 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "LW",
    "q3Credits": 3.9,
    "year2026Credits": 21.3,
    "lastActivity": "10 Nov 2026, 15.41",
    "periodLabel": "17 November 2024 - 17 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 4.5,
        "target": 8,
        "redPercent": 56,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 2.5,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 2,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-134",
    "pogiId": "#POGI-2026-1134",
    "name": "Prof. Dr. dr. Kevin Pratama, Sp.OG, Subsp. Onk",
    "initials": "KP",
    "nik": "3273100000118322",
    "province": "Nusa Tenggara Timur",
    "hospital": "RS Siloam Kupang",
    "institution": "RS Siloam Kupang",
    "phone": "+62 812-5958-3546",
    "assignedTo": "Dewi Lestari",
    "pelayananCredits": 21,
    "pengabdianCredits": 9,
    "pembelajaranCredits": 22,
    "q4Credits": 5.4,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "18 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "KP",
    "q3Credits": 4.9,
    "year2026Credits": 23.1,
    "lastActivity": "09 Nov 2026, 16.58",
    "periodLabel": "18 Maret 2024 - 18 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 5.4,
        "target": 14,
        "redPercent": 39,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 3,
        "target": 7.7
      },
      "pembelajaran": {
        "achieved": 2.4,
        "target": 4.8
      }
    }
  },
  {
    "id": "doc-135",
    "pogiId": "#POGI-2026-1135",
    "name": "dr. Maya Mandagi, Sp.OG, Subsp. FER",
    "initials": "MM",
    "nik": "3273100000119205",
    "province": "Nusa Tenggara Timur",
    "hospital": "RSUD Prof. Dr. W. Z. Johannes Kupang",
    "institution": "RSUD Prof. Dr. W. Z. Johannes Kupang",
    "phone": "+62 812-5995-3565",
    "assignedTo": "Rina Kartika",
    "pelayananCredits": 22,
    "pengabdianCredits": 10,
    "pembelajaranCredits": 8,
    "q4Credits": 6.3,
    "pendingCredits": 0.5,
    "status": "Incomplete",
    "strExpiry": "19 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "MM",
    "q3Credits": 5.9,
    "year2026Credits": 25,
    "lastActivity": "08 Nov 2026, 08.15",
    "periodLabel": "19 November 2024 - 19 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 6.3,
        "target": 8,
        "redPercent": 79,
        "yellowPercent": 6
      },
      "pending": 0.5,
      "pengabdian": {
        "achieved": 0.5,
        "target": 1
      },
      "pelayanan": {
        "achieved": 3.5,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 2.3,
        "target": 2.6
      }
    }
  },
  {
    "id": "doc-136",
    "pogiId": "#POGI-2026-1136",
    "name": "dr. Tari Wibowo, Sp.OG(K)",
    "initials": "TW",
    "nik": "3273100000120088",
    "province": "Nusa Tenggara Timur",
    "hospital": "RS Siloam Kupang",
    "institution": "RS Siloam Kupang",
    "phone": "+62 812-6032-3584",
    "assignedTo": "Melati Sihombing",
    "pelayananCredits": 23,
    "pengabdianCredits": 11,
    "pembelajaranCredits": 9,
    "q4Credits": 0,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "20 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "TW",
    "q3Credits": 1.5,
    "year2026Credits": 16,
    "lastActivity": "07 Nov 2026, 09.32",
    "periodLabel": "20 Maret 2024 - 20 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 0,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 0,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 0,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-137",
    "pogiId": "#POGI-2026-1137",
    "name": "dr. Lestari Pratama, Sp.OG, Subsp. KFM",
    "initials": "LP",
    "nik": "3273100000120971",
    "province": "Nusa Tenggara Timur",
    "hospital": "RSUD Prof. Dr. W. Z. Johannes Kupang",
    "institution": "RSUD Prof. Dr. W. Z. Johannes Kupang",
    "phone": "+62 812-6069-3603",
    "assignedTo": "Wahyu Setiawan",
    "pelayananCredits": 24,
    "pengabdianCredits": 12,
    "pembelajaranCredits": 10,
    "q4Credits": 0.9,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "21 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "LP",
    "q3Credits": 1.5,
    "year2026Credits": 17.9,
    "lastActivity": "06 Nov 2026, 10.49",
    "periodLabel": "21 November 2024 - 21 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 0.9,
        "target": 8,
        "redPercent": 11,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 0.5,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 0.4,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-138",
    "pogiId": "#POGI-2026-1138",
    "name": "dr. Kevin Mandagi, Sp.OG, Subsp. Obginsos",
    "initials": "KM",
    "nik": "3273100000121854",
    "province": "Nusa Tenggara Timur",
    "hospital": "RS Siloam Kupang",
    "institution": "RS Siloam Kupang",
    "phone": "+62 812-6106-3622",
    "assignedTo": "Siti Rahma",
    "pelayananCredits": 25,
    "pengabdianCredits": 13,
    "pembelajaranCredits": 11,
    "q4Credits": 1.8,
    "pendingCredits": 2,
    "status": "Incomplete",
    "strExpiry": "22 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "KM",
    "q3Credits": 2,
    "year2026Credits": 16.7,
    "lastActivity": "05 Nov 2026, 11.06",
    "periodLabel": "22 Maret 2024 - 22 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 1.8,
        "target": 8,
        "redPercent": 23,
        "yellowPercent": 25
      },
      "pending": 2,
      "pengabdian": {
        "achieved": 0,
        "target": 1
      },
      "pelayanan": {
        "achieved": 1,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 0.8,
        "target": 2.6
      }
    }
  },
  {
    "id": "doc-139",
    "pogiId": "#POGI-2026-1139",
    "name": "dr. Maya Wibowo, Sp.OG, Subsp. Urogin-RE",
    "initials": "MW",
    "nik": "3273100000122737",
    "province": "Nusa Tenggara Timur",
    "hospital": "RSUD Prof. Dr. W. Z. Johannes Kupang",
    "institution": "RSUD Prof. Dr. W. Z. Johannes Kupang",
    "phone": "+62 812-6143-3641",
    "assignedTo": "Dewi Lestari",
    "pelayananCredits": 26,
    "pengabdianCredits": 14,
    "pembelajaranCredits": 12,
    "q4Credits": 2.7,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "23 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "MW",
    "q3Credits": 3.1,
    "year2026Credits": 18.6,
    "lastActivity": "04 Nov 2026, 12.23",
    "periodLabel": "23 November 2024 - 23 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 2.7,
        "target": 8,
        "redPercent": 34,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 1.5,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 1.2,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-140",
    "pogiId": "#POGI-2026-1140",
    "name": "Dr. dr. Andi Puspitasari, Sp.OG",
    "initials": "AP",
    "nik": "3273100000123620",
    "province": "Aceh",
    "hospital": "RSUD Dr. Zainoel Abidin Banda Aceh",
    "institution": "RSUD Dr. Zainoel Abidin Banda Aceh",
    "phone": "+62 812-6180-3660",
    "assignedTo": "Rina Kartika",
    "pelayananCredits": 27,
    "pengabdianCredits": 5,
    "pembelajaranCredits": 13,
    "q4Credits": 3.6,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "24 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "AP",
    "q3Credits": 2.9,
    "year2026Credits": 20.4,
    "lastActivity": "17 Nov 2026, 13.40",
    "periodLabel": "24 Maret 2024 - 24 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 3.6,
        "target": 8,
        "redPercent": 45,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 2,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 1.6,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-141",
    "pogiId": "#POGI-2026-1141",
    "name": "dr. Irfan Nasution, Sp.OG, Subsp. Onk",
    "initials": "IN",
    "nik": "3273100000124503",
    "province": "Aceh",
    "hospital": "RS Ibu dan Anak Aceh",
    "institution": "RS Ibu dan Anak Aceh",
    "phone": "+62 812-6217-3679",
    "assignedTo": "Melati Sihombing",
    "pelayananCredits": 28,
    "pengabdianCredits": 6,
    "pembelajaranCredits": 14,
    "q4Credits": 4.5,
    "pendingCredits": 1,
    "status": "Incomplete",
    "strExpiry": "25 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "IN",
    "q3Credits": 3.9,
    "year2026Credits": 22.3,
    "lastActivity": "16 Nov 2026, 14.57",
    "periodLabel": "25 November 2024 - 25 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 4.5,
        "target": 8,
        "redPercent": 56,
        "yellowPercent": 13
      },
      "pending": 1,
      "pengabdian": {
        "achieved": 0.5,
        "target": 1
      },
      "pelayanan": {
        "achieved": 2.5,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 1.5,
        "target": 2.6
      }
    }
  },
  {
    "id": "doc-142",
    "pogiId": "#POGI-2026-1142",
    "name": "Prof. Dr. dr. Nurul Hidayat, Sp.OG, Subsp. FER",
    "initials": "NH",
    "nik": "3273100000125386",
    "province": "Aceh",
    "hospital": "RSUD Dr. Zainoel Abidin Banda Aceh",
    "institution": "RSUD Dr. Zainoel Abidin Banda Aceh",
    "phone": "+62 812-6254-3698",
    "assignedTo": "Wahyu Setiawan",
    "pelayananCredits": 29,
    "pengabdianCredits": 7,
    "pembelajaranCredits": 15,
    "q4Credits": 5.4,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "26 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "NH",
    "q3Credits": 4.9,
    "year2026Credits": 24.1,
    "lastActivity": "15 Nov 2026, 15.14",
    "periodLabel": "26 Maret 2024 - 26 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 5.4,
        "target": 11,
        "redPercent": 49,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 3,
        "target": 6.1
      },
      "pembelajaran": {
        "achieved": 2.4,
        "target": 3.4
      }
    }
  },
  {
    "id": "doc-143",
    "pogiId": "#POGI-2026-1143",
    "name": "dr. Tari Utami, Sp.OG, Subsp. Obginsos",
    "initials": "TU",
    "nik": "3273100000126269",
    "province": "Kalimantan Selatan",
    "hospital": "RSUD Ulin Banjarmasin",
    "institution": "RSUD Ulin Banjarmasin",
    "phone": "+62 812-6291-3717",
    "assignedTo": "Siti Rahma",
    "pelayananCredits": 68,
    "pengabdianCredits": 23,
    "pembelajaranCredits": 31,
    "q4Credits": 10.4,
    "pendingCredits": 0,
    "status": "Complete",
    "strExpiry": "27 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "TU",
    "q3Credits": 9.3,
    "year2026Credits": 34.4,
    "lastActivity": "14 Nov 2026, 16.31",
    "periodLabel": "27 November 2024 - 27 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 10.4,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 0,
        "isGreen": true
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 1,
        "target": 0.5
      },
      "pelayanan": {
        "achieved": 5.7,
        "target": 5.5
      },
      "pembelajaran": {
        "achieved": 3.7,
        "target": 2
      }
    }
  },
  {
    "id": "doc-144",
    "pogiId": "#POGI-2026-1144",
    "name": "dr. Lestari Rahmawati, Sp.OG, Subsp. Urogin-RE",
    "initials": "LR",
    "nik": "3273100000127152",
    "province": "Kalimantan Selatan",
    "hospital": "RS Sari Mulia Banjarmasin",
    "institution": "RS Sari Mulia Banjarmasin",
    "phone": "+62 812-6328-3736",
    "assignedTo": "Dewi Lestari",
    "pelayananCredits": 31,
    "pengabdianCredits": 9,
    "pembelajaranCredits": 17,
    "q4Credits": 0,
    "pendingCredits": 2.5,
    "status": "Incomplete",
    "strExpiry": "10 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "LR",
    "q3Credits": 1.5,
    "year2026Credits": 14,
    "lastActivity": "13 Nov 2026, 08.48",
    "periodLabel": "10 Maret 2024 - 10 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 0,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 31
      },
      "pending": 2.5,
      "pengabdian": {
        "achieved": 0,
        "target": 1
      },
      "pelayanan": {
        "achieved": 0,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 0,
        "target": 2.6
      }
    }
  },
  {
    "id": "doc-145",
    "pogiId": "#POGI-2026-1145",
    "name": "dr. Kevin Meurah, Sp.OG",
    "initials": "KM",
    "nik": "3273100000128035",
    "province": "Kalimantan Selatan",
    "hospital": "RSUD Ulin Banjarmasin",
    "institution": "RSUD Ulin Banjarmasin",
    "phone": "+62 812-6365-3755",
    "assignedTo": "Rina Kartika",
    "pelayananCredits": 32,
    "pengabdianCredits": 10,
    "pembelajaranCredits": 18,
    "q4Credits": 0.9,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "11 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "KM",
    "q3Credits": 1.5,
    "year2026Credits": 15.9,
    "lastActivity": "12 Nov 2026, 09.05",
    "periodLabel": "11 November 2024 - 11 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 0.9,
        "target": 8,
        "redPercent": 11,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 0.5,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 0.4,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-146",
    "pogiId": "#POGI-2026-1146",
    "name": "dr. Maya Utami, Sp.OG, Subsp. Onk",
    "initials": "MU",
    "nik": "3273100000128918",
    "province": "Kalimantan Selatan",
    "hospital": "RS Sari Mulia Banjarmasin",
    "institution": "RS Sari Mulia Banjarmasin",
    "phone": "+62 812-6402-3774",
    "assignedTo": "Melati Sihombing",
    "pelayananCredits": 33,
    "pengabdianCredits": 11,
    "pembelajaranCredits": 19,
    "q4Credits": 1.8,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "12 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "MU",
    "q3Credits": 2,
    "year2026Credits": 17.7,
    "lastActivity": "11 Nov 2026, 10.22",
    "periodLabel": "12 Maret 2024 - 12 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 1.8,
        "target": 8,
        "redPercent": 23,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 1,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 0.8,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-147",
    "pogiId": "#POGI-2026-1147",
    "name": "dr. Ronald Siregar, Sp.OG",
    "initials": "RS",
    "nik": "3273100000129801",
    "province": "Jambi",
    "hospital": "RSUD Raden Mattaher Jambi",
    "institution": "RSUD Raden Mattaher Jambi",
    "phone": "+62 812-6439-3793",
    "assignedTo": "Wahyu Setiawan",
    "pelayananCredits": 72,
    "pengabdianCredits": 27,
    "pembelajaranCredits": 35,
    "q4Credits": 8,
    "pendingCredits": 1.5,
    "status": "Complete",
    "strExpiry": "13 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "RS",
    "q3Credits": 8.4,
    "year2026Credits": 32,
    "lastActivity": "10 Nov 2026, 11.39",
    "periodLabel": "13 November 2024 - 13 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 8,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 0,
        "isGreen": true
      },
      "pending": 1.5,
      "pengabdian": {
        "achieved": 1,
        "target": 0
      },
      "pelayanan": {
        "achieved": 5.5,
        "target": 5.5
      },
      "pembelajaran": {
        "achieved": 1.5,
        "target": 2.5
      }
    }
  },
  {
    "id": "doc-148",
    "pogiId": "#POGI-2026-1148",
    "name": "Dr. dr. Siti Hermawan, Sp.OG, Subsp. Onk",
    "initials": "SH",
    "nik": "3273100000130684",
    "province": "Jambi",
    "hospital": "RS Siloam Jambi",
    "institution": "RS Siloam Jambi",
    "phone": "+62 812-6476-3812",
    "assignedTo": "Siti Rahma",
    "pelayananCredits": 73,
    "pengabdianCredits": 28,
    "pembelajaranCredits": 36,
    "q4Credits": 8.8,
    "pendingCredits": 0,
    "status": "Complete",
    "strExpiry": "14 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "SH",
    "q3Credits": 8.8,
    "year2026Credits": 32.8,
    "lastActivity": "09 Nov 2026, 12.56",
    "periodLabel": "14 Maret 2024 - 14 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 8.8,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 0,
        "isGreen": true
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 0.5
      },
      "pelayanan": {
        "achieved": 5,
        "target": 5
      },
      "pembelajaran": {
        "achieved": 3.8,
        "target": 2.5
      }
    }
  },
  {
    "id": "doc-149",
    "pogiId": "#POGI-2026-1149",
    "name": "dr. Rina Suryani, Sp.OG, Subsp. FER",
    "initials": "RS",
    "nik": "3273100000131567",
    "province": "Jambi",
    "hospital": "RSUD Raden Mattaher Jambi",
    "institution": "RSUD Raden Mattaher Jambi",
    "phone": "+62 812-6513-3831",
    "assignedTo": "Dewi Lestari",
    "pelayananCredits": 74,
    "pengabdianCredits": 29,
    "pembelajaranCredits": 37,
    "q4Credits": 9.6,
    "pendingCredits": 0,
    "status": "Complete",
    "strExpiry": "15 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "RS",
    "q3Credits": 9.3,
    "year2026Credits": 33.6,
    "lastActivity": "08 Nov 2026, 13.13",
    "periodLabel": "15 November 2024 - 15 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 9.6,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 0,
        "isGreen": true
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0.5,
        "target": 0.5
      },
      "pelayanan": {
        "achieved": 5.5,
        "target": 5.5
      },
      "pembelajaran": {
        "achieved": 3.6,
        "target": 2
      }
    }
  },
  {
    "id": "doc-150",
    "pogiId": "#POGI-2026-1150",
    "name": "Prof. Dr. dr. Dian Siregar, Sp.OG(K)",
    "initials": "DS",
    "nik": "3273100000132450",
    "province": "Jambi",
    "hospital": "RS Siloam Jambi",
    "institution": "RS Siloam Jambi",
    "phone": "+62 812-6550-3850",
    "assignedTo": "Rina Kartika",
    "pelayananCredits": 12,
    "pengabdianCredits": 5,
    "pembelajaranCredits": 8,
    "q4Credits": 5.4,
    "pendingCredits": 0.5,
    "status": "Incomplete",
    "strExpiry": "16 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "DS",
    "q3Credits": 4.9,
    "year2026Credits": 22.1,
    "lastActivity": "07 Nov 2026, 14.30",
    "periodLabel": "16 Maret 2024 - 16 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 5.4,
        "target": 8,
        "redPercent": 68,
        "yellowPercent": 6
      },
      "pending": 0.5,
      "pengabdian": {
        "achieved": 0.5,
        "target": 1
      },
      "pelayanan": {
        "achieved": 3,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 1.9,
        "target": 2.6
      }
    }
  },
  {
    "id": "doc-151",
    "pogiId": "#POGI-2026-1151",
    "name": "dr. Ronald Hermawan, Sp.OG, Subsp. KFM",
    "initials": "RH",
    "nik": "3273100000133333",
    "province": "Jambi",
    "hospital": "RSUD Raden Mattaher Jambi",
    "institution": "RSUD Raden Mattaher Jambi",
    "phone": "+62 812-6587-3869",
    "assignedTo": "Melati Sihombing",
    "pelayananCredits": 13,
    "pengabdianCredits": 6,
    "pembelajaranCredits": 9,
    "q4Credits": 6.3,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "17 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "RH",
    "q3Credits": 5.9,
    "year2026Credits": 24,
    "lastActivity": "06 Nov 2026, 15.47",
    "periodLabel": "17 November 2024 - 17 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 6.3,
        "target": 9.5,
        "redPercent": 66,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 3.5,
        "target": 5.2
      },
      "pembelajaran": {
        "achieved": 2.8,
        "target": 2.8
      }
    }
  },
  {
    "id": "doc-152",
    "pogiId": "#POGI-2026-1152",
    "name": "dr. Siti Suryani, Sp.OG, Subsp. Obginsos",
    "initials": "SS",
    "nik": "3273100000134216",
    "province": "Jambi",
    "hospital": "RS Siloam Jambi",
    "institution": "RS Siloam Jambi",
    "phone": "+62 812-6624-3888",
    "assignedTo": "Wahyu Setiawan",
    "pelayananCredits": 14,
    "pengabdianCredits": 7,
    "pembelajaranCredits": 10,
    "q4Credits": 0,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "18 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "SS",
    "q3Credits": 1.5,
    "year2026Credits": 15,
    "lastActivity": "05 Nov 2026, 16.04",
    "periodLabel": "18 Maret 2024 - 18 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 0,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 0,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 0,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-153",
    "pogiId": "#POGI-2026-1153",
    "name": "dr. Rina Siregar, Sp.OG, Subsp. Urogin-RE",
    "initials": "RS",
    "nik": "3273100000135099",
    "province": "Jambi",
    "hospital": "RSUD Raden Mattaher Jambi",
    "institution": "RSUD Raden Mattaher Jambi",
    "phone": "+62 812-6661-3907",
    "assignedTo": "Siti Rahma",
    "pelayananCredits": 15,
    "pengabdianCredits": 8,
    "pembelajaranCredits": 11,
    "q4Credits": 0.9,
    "pendingCredits": 2,
    "status": "Incomplete",
    "strExpiry": "19 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "RS",
    "q3Credits": 1.5,
    "year2026Credits": 16.9,
    "lastActivity": "04 Nov 2026, 08.21",
    "periodLabel": "19 November 2024 - 19 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 0.9,
        "target": 8,
        "redPercent": 11,
        "yellowPercent": 25
      },
      "pending": 2,
      "pengabdian": {
        "achieved": 0,
        "target": 1
      },
      "pelayanan": {
        "achieved": 0.5,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 0.4,
        "target": 2.6
      }
    }
  },
  {
    "id": "doc-154",
    "pogiId": "#POGI-2026-1154",
    "name": "dr. Dedi Fauzi, Sp.OG",
    "initials": "DF",
    "nik": "3273100000135982",
    "province": "Kalimantan Tengah",
    "hospital": "RSUD dr. Doris Sylvanus Palangka Raya",
    "institution": "RSUD dr. Doris Sylvanus Palangka Raya",
    "phone": "+62 812-6698-3926",
    "assignedTo": "Dewi Lestari",
    "pelayananCredits": 16,
    "pengabdianCredits": 9,
    "pembelajaranCredits": 12,
    "q4Credits": 1.8,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "20 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "DF",
    "q3Credits": 2,
    "year2026Credits": 18.7,
    "lastActivity": "17 Nov 2026, 09.38",
    "periodLabel": "20 Maret 2024 - 20 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 1.8,
        "target": 8,
        "redPercent": 23,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 1,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 0.8,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-155",
    "pogiId": "#POGI-2026-1155",
    "name": "dr. Fajar Saputra, Sp.OG, Subsp. Onk",
    "initials": "FS",
    "nik": "3273100000136865",
    "province": "Kalimantan Tengah",
    "hospital": "RSUD dr. Doris Sylvanus Palangka Raya",
    "institution": "RSUD dr. Doris Sylvanus Palangka Raya",
    "phone": "+62 812-6735-3945",
    "assignedTo": "Rina Kartika",
    "pelayananCredits": 17,
    "pengabdianCredits": 10,
    "pembelajaranCredits": 13,
    "q4Credits": 2.7,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "21 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "FS",
    "q3Credits": 3.1,
    "year2026Credits": 20.6,
    "lastActivity": "16 Nov 2026, 10.55",
    "periodLabel": "21 November 2024 - 21 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 2.7,
        "target": 8,
        "redPercent": 34,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 1.5,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 1.2,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-156",
    "pogiId": "#POGI-2026-1156",
    "name": "Dr. dr. Arif Santoso, Sp.OG, Subsp. FER",
    "initials": "AS",
    "nik": "3273100000137748",
    "province": "Kalimantan Tengah",
    "hospital": "RSUD dr. Doris Sylvanus Palangka Raya",
    "institution": "RSUD dr. Doris Sylvanus Palangka Raya",
    "phone": "+62 812-6772-3964",
    "assignedTo": "Melati Sihombing",
    "pelayananCredits": 18,
    "pengabdianCredits": 11,
    "pembelajaranCredits": 14,
    "q4Credits": 3.6,
    "pendingCredits": 1,
    "status": "Incomplete",
    "strExpiry": "22 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "AS",
    "q3Credits": 2.9,
    "year2026Credits": 19.4,
    "lastActivity": "15 Nov 2026, 11.12",
    "periodLabel": "22 Maret 2024 - 22 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 3.6,
        "target": 8,
        "redPercent": 45,
        "yellowPercent": 13
      },
      "pending": 1,
      "pengabdian": {
        "achieved": 0.5,
        "target": 1
      },
      "pelayanan": {
        "achieved": 2,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 1.1,
        "target": 2.6
      }
    }
  },
  {
    "id": "doc-157",
    "pogiId": "#POGI-2026-1157",
    "name": "dr. Dian Wibowo, Sp.OG, Subsp. Obginsos",
    "initials": "DW",
    "nik": "3273100000138631",
    "province": "Sulawesi Utara",
    "hospital": "RSUP Prof. Dr. R. D. Kandou Manado",
    "institution": "RSUP Prof. Dr. R. D. Kandou Manado",
    "phone": "+62 812-6809-3983",
    "assignedTo": "Wahyu Setiawan",
    "pelayananCredits": 52,
    "pengabdianCredits": 22,
    "pembelajaranCredits": 45,
    "q4Credits": 10.4,
    "pendingCredits": 0,
    "status": "Complete",
    "strExpiry": "23 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "DW",
    "q3Credits": 9.1,
    "year2026Credits": 34.4,
    "lastActivity": "14 Nov 2026, 12.29",
    "periodLabel": "23 November 2024 - 23 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 10.4,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 0,
        "isGreen": true
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0.5,
        "target": 0.5
      },
      "pelayanan": {
        "achieved": 5.7,
        "target": 5.5
      },
      "pembelajaran": {
        "achieved": 4.2,
        "target": 2
      }
    }
  },
  {
    "id": "doc-158",
    "pogiId": "#POGI-2026-1158",
    "name": "Prof. Dr. dr. Ronald Pratama, Sp.OG, Subsp. Urogin-RE",
    "initials": "RP",
    "nik": "3273100000139514",
    "province": "Sulawesi Utara",
    "hospital": "RS Siloam Manado",
    "institution": "RS Siloam Manado",
    "phone": "+62 812-6846-4002",
    "assignedTo": "Siti Rahma",
    "pelayananCredits": 53,
    "pengabdianCredits": 23,
    "pembelajaranCredits": 46,
    "q4Credits": 11.2,
    "pendingCredits": 0,
    "status": "Complete",
    "strExpiry": "24 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "RP",
    "q3Credits": 9.6,
    "year2026Credits": 35.2,
    "lastActivity": "13 Nov 2026, 13.46",
    "periodLabel": "24 Maret 2024 - 24 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 11.2,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 0,
        "isGreen": true
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 1,
        "target": 0.5
      },
      "pelayanan": {
        "achieved": 6.2,
        "target": 5
      },
      "pembelajaran": {
        "achieved": 4,
        "target": 2.5
      }
    }
  },
  {
    "id": "doc-159",
    "pogiId": "#POGI-2026-1159",
    "name": "dr. Siti Mandagi, Sp.OG",
    "initials": "SM",
    "nik": "3273100000140397",
    "province": "Sulawesi Utara",
    "hospital": "RSUP Prof. Dr. R. D. Kandou Manado",
    "institution": "RSUP Prof. Dr. R. D. Kandou Manado",
    "phone": "+62 812-6883-4021",
    "assignedTo": "Dewi Lestari",
    "pelayananCredits": 21,
    "pengabdianCredits": 14,
    "pembelajaranCredits": 17,
    "q4Credits": 6.3,
    "pendingCredits": 2.5,
    "status": "Incomplete",
    "strExpiry": "25 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "SM",
    "q3Credits": 5.9,
    "year2026Credits": 25,
    "lastActivity": "12 Nov 2026, 14.03",
    "periodLabel": "25 November 2024 - 25 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 6.3,
        "target": 14,
        "redPercent": 45,
        "yellowPercent": 18
      },
      "pending": 2.5,
      "pengabdian": {
        "achieved": 0.5,
        "target": 1
      },
      "pelayanan": {
        "achieved": 3.5,
        "target": 7.7
      },
      "pembelajaran": {
        "achieved": 2.3,
        "target": 5.3
      }
    }
  },
  {
    "id": "doc-160",
    "pogiId": "#POGI-2026-1160",
    "name": "dr. Nurul Suryana, Sp.OG(K)",
    "initials": "NS",
    "nik": "3273100000141280",
    "province": "Sulawesi Tengah",
    "hospital": "RSUD Undata Palu",
    "institution": "RSUD Undata Palu",
    "phone": "+62 812-6920-4040",
    "assignedTo": "Rina Kartika",
    "pelayananCredits": 22,
    "pengabdianCredits": 5,
    "pembelajaranCredits": 18,
    "q4Credits": 0,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "26 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "NS",
    "q3Credits": 1.5,
    "year2026Credits": 16,
    "lastActivity": "11 Nov 2026, 15.20",
    "periodLabel": "26 Maret 2024 - 26 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 0,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 0,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 0,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-161",
    "pogiId": "#POGI-2026-1161",
    "name": "dr. Budi Anggraini, Sp.OG, Subsp. KFM",
    "initials": "BA",
    "nik": "3273100000142163",
    "province": "Sulawesi Tengah",
    "hospital": "RSUD Undata Palu",
    "institution": "RSUD Undata Palu",
    "phone": "+62 812-6957-4059",
    "assignedTo": "Melati Sihombing",
    "pelayananCredits": 23,
    "pengabdianCredits": 6,
    "pembelajaranCredits": 19,
    "q4Credits": 0.9,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "27 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "BA",
    "q3Credits": 1.5,
    "year2026Credits": 17.9,
    "lastActivity": "10 Nov 2026, 16.37",
    "periodLabel": "27 November 2024 - 27 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 0.9,
        "target": 8,
        "redPercent": 11,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 0.5,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 0.4,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-162",
    "pogiId": "#POGI-2026-1162",
    "name": "dr. Andi Nugroho, Sp.OG, Subsp. Obginsos",
    "initials": "AN",
    "nik": "3273100000143046",
    "province": "Sulawesi Tengah",
    "hospital": "RSUD Undata Palu",
    "institution": "RSUD Undata Palu",
    "phone": "+62 812-6994-4078",
    "assignedTo": "Wahyu Setiawan",
    "pelayananCredits": 24,
    "pengabdianCredits": 7,
    "pembelajaranCredits": 20,
    "q4Credits": 1.8,
    "pendingCredits": 1.5,
    "status": "Incomplete",
    "strExpiry": "10 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "AN",
    "q3Credits": 2,
    "year2026Credits": 16.7,
    "lastActivity": "09 Nov 2026, 08.54",
    "periodLabel": "10 Maret 2024 - 10 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 1.8,
        "target": 8,
        "redPercent": 23,
        "yellowPercent": 19
      },
      "pending": 1.5,
      "pengabdian": {
        "achieved": 0,
        "target": 1
      },
      "pelayanan": {
        "achieved": 1,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 0.8,
        "target": 2.6
      }
    }
  },
  {
    "id": "doc-163",
    "pogiId": "#POGI-2026-1163",
    "name": "dr. Kevin Suryani, Sp.OG, Subsp. Onk",
    "initials": "KS",
    "nik": "3273100000143929",
    "province": "Kepulauan Riau",
    "hospital": "RSUD Embung Fatimah Batam",
    "institution": "RSUD Embung Fatimah Batam",
    "phone": "+62 812-7031-4097",
    "assignedTo": "Siti Rahma",
    "pelayananCredits": 58,
    "pengabdianCredits": 28,
    "pembelajaranCredits": 31,
    "q4Credits": 9.6,
    "pendingCredits": 0,
    "status": "Complete",
    "strExpiry": "11 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "KS",
    "q3Credits": 9.1,
    "year2026Credits": 33.6,
    "lastActivity": "08 Nov 2026, 09.11",
    "periodLabel": "11 November 2024 - 11 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 9.6,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 0,
        "isGreen": true
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 1,
        "target": 0.5
      },
      "pelayanan": {
        "achieved": 5.5,
        "target": 5.5
      },
      "pembelajaran": {
        "achieved": 3.1,
        "target": 2
      }
    }
  },
  {
    "id": "doc-164",
    "pogiId": "#POGI-2026-1164",
    "name": "Dr. dr. Maya Siregar, Sp.OG, Subsp. FER",
    "initials": "MS",
    "nik": "3273100000144812",
    "province": "Kepulauan Riau",
    "hospital": "RS Awal Bros Batam",
    "institution": "RS Awal Bros Batam",
    "phone": "+62 812-7068-4116",
    "assignedTo": "Dewi Lestari",
    "pelayananCredits": 26,
    "pengabdianCredits": 9,
    "pembelajaranCredits": 22,
    "q4Credits": 3.6,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "12 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "MS",
    "q3Credits": 2.9,
    "year2026Credits": 20.4,
    "lastActivity": "07 Nov 2026, 10.28",
    "periodLabel": "12 Maret 2024 - 12 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 3.6,
        "target": 8,
        "redPercent": 45,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 2,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 1.6,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-165",
    "pogiId": "#POGI-2026-1165",
    "name": "dr. Tari Hermawan, Sp.OG(K)",
    "initials": "TH",
    "nik": "3273100000145695",
    "province": "Kepulauan Riau",
    "hospital": "RSUD Embung Fatimah Batam",
    "institution": "RSUD Embung Fatimah Batam",
    "phone": "+62 812-7105-4135",
    "assignedTo": "Rina Kartika",
    "pelayananCredits": 27,
    "pengabdianCredits": 10,
    "pembelajaranCredits": 8,
    "q4Credits": 4.5,
    "pendingCredits": 0.5,
    "status": "Incomplete",
    "strExpiry": "13 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "TH",
    "q3Credits": 3.9,
    "year2026Credits": 22.3,
    "lastActivity": "06 Nov 2026, 11.45",
    "periodLabel": "13 November 2024 - 13 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 4.5,
        "target": 8,
        "redPercent": 56,
        "yellowPercent": 6
      },
      "pending": 0.5,
      "pengabdian": {
        "achieved": 0.5,
        "target": 1
      },
      "pelayanan": {
        "achieved": 2.5,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 1.5,
        "target": 2.6
      }
    }
  },
  {
    "id": "doc-166",
    "pogiId": "#POGI-2026-1166",
    "name": "Prof. Dr. dr. Bambang Wijaya, Sp.OG, Subsp. Urogin-RE",
    "initials": "BW",
    "nik": "3273100000146578",
    "province": "Bengkulu",
    "hospital": "RSUD M. Yunus Bengkulu",
    "institution": "RSUD M. Yunus Bengkulu",
    "phone": "+62 812-7142-4154",
    "assignedTo": "Melati Sihombing",
    "pelayananCredits": 28,
    "pengabdianCredits": 11,
    "pembelajaranCredits": 9,
    "q4Credits": 5.4,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "14 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "BW",
    "q3Credits": 4.9,
    "year2026Credits": 24.1,
    "lastActivity": "05 Nov 2026, 12.02",
    "periodLabel": "14 Maret 2024 - 14 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 5.4,
        "target": 9.5,
        "redPercent": 57,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 3,
        "target": 5.2
      },
      "pembelajaran": {
        "achieved": 2.4,
        "target": 2.8
      }
    }
  },
  {
    "id": "doc-167",
    "pogiId": "#POGI-2026-1167",
    "name": "dr. Yusuf Sumampouw, Sp.OG",
    "initials": "YS",
    "nik": "3273100000147461",
    "province": "Bengkulu",
    "hospital": "RSUD M. Yunus Bengkulu",
    "institution": "RSUD M. Yunus Bengkulu",
    "phone": "+62 812-7179-4173",
    "assignedTo": "Wahyu Setiawan",
    "pelayananCredits": 29,
    "pengabdianCredits": 12,
    "pembelajaranCredits": 10,
    "q4Credits": 6.3,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "15 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "YS",
    "q3Credits": 5.9,
    "year2026Credits": 26,
    "lastActivity": "04 Nov 2026, 13.19",
    "periodLabel": "15 November 2024 - 15 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 6.3,
        "target": 11,
        "redPercent": 57,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 3.5,
        "target": 6.1
      },
      "pembelajaran": {
        "achieved": 2.8,
        "target": 3.4
      }
    }
  },
  {
    "id": "doc-168",
    "pogiId": "#POGI-2026-1168",
    "name": "dr. Surya Purnomo, Sp.OG, Subsp. Onk",
    "initials": "SP",
    "nik": "3273100000148344",
    "province": "Bengkulu",
    "hospital": "RSUD M. Yunus Bengkulu",
    "institution": "RSUD M. Yunus Bengkulu",
    "phone": "+62 812-7216-4192",
    "assignedTo": "Siti Rahma",
    "pelayananCredits": 30,
    "pengabdianCredits": 13,
    "pembelajaranCredits": 11,
    "q4Credits": 0,
    "pendingCredits": 2,
    "status": "Incomplete",
    "strExpiry": "16 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "SP",
    "q3Credits": 1.5,
    "year2026Credits": 14,
    "lastActivity": "17 Nov 2026, 14.36",
    "periodLabel": "16 Maret 2024 - 16 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 0,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 25
      },
      "pending": 2,
      "pengabdian": {
        "achieved": 0,
        "target": 1
      },
      "pelayanan": {
        "achieved": 0,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 0,
        "target": 2.6
      }
    }
  },
  {
    "id": "doc-169",
    "pogiId": "#POGI-2026-1169",
    "name": "dr. Indah Simatupang, Sp.OG, Subsp. KFM",
    "initials": "IS",
    "nik": "3273100000149227",
    "province": "Kepulauan Bangka Belitung",
    "hospital": "RSUD Dr. (H.C.) Ir. Soekarno Bangka",
    "institution": "RSUD Dr. (H.C.) Ir. Soekarno Bangka",
    "phone": "+62 812-7253-4211",
    "assignedTo": "Dewi Lestari",
    "pelayananCredits": 31,
    "pengabdianCredits": 14,
    "pembelajaranCredits": 12,
    "q4Credits": 0.9,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "17 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "IS",
    "q3Credits": 1.5,
    "year2026Credits": 15.9,
    "lastActivity": "16 Nov 2026, 15.53",
    "periodLabel": "17 November 2024 - 17 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 0.9,
        "target": 8,
        "redPercent": 11,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 0.5,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 0.4,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-170",
    "pogiId": "#POGI-2026-1170",
    "name": "dr. Kartika Kurniawan, Sp.OG, Subsp. Obginsos",
    "initials": "KK",
    "nik": "3273100000150110",
    "province": "Kepulauan Bangka Belitung",
    "hospital": "RSUD Dr. (H.C.) Ir. Soekarno Bangka",
    "institution": "RSUD Dr. (H.C.) Ir. Soekarno Bangka",
    "phone": "+62 812-7290-4230",
    "assignedTo": "Rina Kartika",
    "pelayananCredits": 32,
    "pengabdianCredits": 5,
    "pembelajaranCredits": 13,
    "q4Credits": 1.8,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "18 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "KK",
    "q3Credits": 2,
    "year2026Credits": 17.7,
    "lastActivity": "15 Nov 2026, 16.10",
    "periodLabel": "18 Maret 2024 - 18 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 1.8,
        "target": 8,
        "redPercent": 23,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 1,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 0.8,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-171",
    "pogiId": "#POGI-2026-1171",
    "name": "dr. Nenden Kusuma, Sp.OG, Subsp. Urogin-RE",
    "initials": "NK",
    "nik": "3273100000150993",
    "province": "Kepulauan Bangka Belitung",
    "hospital": "RSUD Dr. (H.C.) Ir. Soekarno Bangka",
    "institution": "RSUD Dr. (H.C.) Ir. Soekarno Bangka",
    "phone": "+62 812-7327-4249",
    "assignedTo": "Melati Sihombing",
    "pelayananCredits": 33,
    "pengabdianCredits": 6,
    "pembelajaranCredits": 14,
    "q4Credits": 2.7,
    "pendingCredits": 1,
    "status": "Incomplete",
    "strExpiry": "19 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "NK",
    "q3Credits": 3.1,
    "year2026Credits": 19.6,
    "lastActivity": "14 Nov 2026, 08.27",
    "periodLabel": "19 November 2024 - 19 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 2.7,
        "target": 8,
        "redPercent": 34,
        "yellowPercent": 13
      },
      "pending": 1,
      "pengabdian": {
        "achieved": 0.5,
        "target": 1
      },
      "pelayanan": {
        "achieved": 1.5,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 0.7,
        "target": 2.6
      }
    }
  },
  {
    "id": "doc-172",
    "pogiId": "#POGI-2026-1172",
    "name": "Dr. dr. Doni Nasution, Sp.OG, Subsp. FER",
    "initials": "DN",
    "nik": "3273100000151876",
    "province": "Sulawesi Tenggara",
    "hospital": "RSUD Bahteramas Kendari",
    "institution": "RSUD Bahteramas Kendari",
    "phone": "+62 812-7364-4268",
    "assignedTo": "Wahyu Setiawan",
    "pelayananCredits": 34,
    "pengabdianCredits": 7,
    "pembelajaranCredits": 15,
    "q4Credits": 3.6,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "20 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "DN",
    "q3Credits": 2.9,
    "year2026Credits": 21.4,
    "lastActivity": "13 Nov 2026, 09.44",
    "periodLabel": "20 Maret 2024 - 20 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 3.6,
        "target": 8,
        "redPercent": 45,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 2,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 1.6,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-173",
    "pogiId": "#POGI-2026-1173",
    "name": "dr. Christy Hidayat, Sp.OG(K)",
    "initials": "CH",
    "nik": "3273100000152759",
    "province": "Sulawesi Tenggara",
    "hospital": "RSUD Bahteramas Kendari",
    "institution": "RSUD Bahteramas Kendari",
    "phone": "+62 812-7401-4287",
    "assignedTo": "Siti Rahma",
    "pelayananCredits": 35,
    "pengabdianCredits": 8,
    "pembelajaranCredits": 16,
    "q4Credits": 4.5,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "21 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "CH",
    "q3Credits": 3.9,
    "year2026Credits": 23.3,
    "lastActivity": "12 Nov 2026, 10.01",
    "periodLabel": "21 November 2024 - 21 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 4.5,
        "target": 8,
        "redPercent": 56,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 2.5,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 2,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-174",
    "pogiId": "#POGI-2026-1174",
    "name": "Prof. Dr. dr. Eko Puspitasari, Sp.OG, Subsp. KFM",
    "initials": "EP",
    "nik": "3273100000153642",
    "province": "Sulawesi Tenggara",
    "hospital": "RSUD Bahteramas Kendari",
    "institution": "RSUD Bahteramas Kendari",
    "phone": "+62 812-7438-4306",
    "assignedTo": "Dewi Lestari",
    "pelayananCredits": 36,
    "pengabdianCredits": 9,
    "pembelajaranCredits": 17,
    "q4Credits": 5.4,
    "pendingCredits": 2.5,
    "status": "Incomplete",
    "strExpiry": "22 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "EP",
    "q3Credits": 4.9,
    "year2026Credits": 22.1,
    "lastActivity": "11 Nov 2026, 11.18",
    "periodLabel": "22 Maret 2024 - 22 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 5.4,
        "target": 14,
        "redPercent": 39,
        "yellowPercent": 18
      },
      "pending": 2.5,
      "pengabdian": {
        "achieved": 0.5,
        "target": 1
      },
      "pelayanan": {
        "achieved": 3,
        "target": 7.7
      },
      "pembelajaran": {
        "achieved": 1.9,
        "target": 5.3
      }
    }
  },
  {
    "id": "doc-175",
    "pogiId": "#POGI-2026-1175",
    "name": "dr. Yohana Rahmawati, Sp.OG",
    "initials": "YR",
    "nik": "3273100000154525",
    "province": "Kalimantan Utara",
    "hospital": "RSUD Dr. H. Soemarno Sosroatmodjo Bulungan",
    "institution": "RSUD Dr. H. Soemarno Sosroatmodjo Bulungan",
    "phone": "+62 812-7475-4325",
    "assignedTo": "Rina Kartika",
    "pelayananCredits": 70,
    "pengabdianCredits": 25,
    "pembelajaranCredits": 43,
    "q4Credits": 8,
    "pendingCredits": 0,
    "status": "Complete",
    "strExpiry": "23 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "YR",
    "q3Credits": 8,
    "year2026Credits": 32,
    "lastActivity": "10 Nov 2026, 12.35",
    "periodLabel": "23 November 2024 - 23 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 8,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 0,
        "isGreen": true
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 1,
        "target": 0.5
      },
      "pelayanan": {
        "achieved": 5.5,
        "target": 5.5
      },
      "pembelajaran": {
        "achieved": 1.5,
        "target": 2
      }
    }
  },
  {
    "id": "doc-176",
    "pogiId": "#POGI-2026-1176",
    "name": "dr. Hendra Meurah, Sp.OG, Subsp. Onk",
    "initials": "HM",
    "nik": "3273100000155408",
    "province": "Kalimantan Utara",
    "hospital": "RSUD Dr. H. Soemarno Sosroatmodjo Bulungan",
    "institution": "RSUD Dr. H. Soemarno Sosroatmodjo Bulungan",
    "phone": "+62 812-7512-4344",
    "assignedTo": "Melati Sihombing",
    "pelayananCredits": 71,
    "pengabdianCredits": 26,
    "pembelajaranCredits": 44,
    "q4Credits": 8.8,
    "pendingCredits": 0,
    "status": "Complete",
    "strExpiry": "24 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "HM",
    "q3Credits": 8.4,
    "year2026Credits": 32.8,
    "lastActivity": "09 Nov 2026, 13.52",
    "periodLabel": "24 Maret 2024 - 24 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 8.8,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 0,
        "isGreen": true
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 0.5
      },
      "pelayanan": {
        "achieved": 5,
        "target": 5
      },
      "pembelajaran": {
        "achieved": 3.8,
        "target": 2.5
      }
    }
  },
  {
    "id": "doc-177",
    "pogiId": "#POGI-2026-1177",
    "name": "dr. Fitri Utami, Sp.OG, Subsp. FER",
    "initials": "FU",
    "nik": "3273100000156291",
    "province": "Kalimantan Utara",
    "hospital": "RSUD Dr. H. Soemarno Sosroatmodjo Bulungan",
    "institution": "RSUD Dr. H. Soemarno Sosroatmodjo Bulungan",
    "phone": "+62 812-7549-4363",
    "assignedTo": "Wahyu Setiawan",
    "pelayananCredits": 14,
    "pengabdianCredits": 12,
    "pembelajaranCredits": 20,
    "q4Credits": 0.9,
    "pendingCredits": 1.5,
    "status": "Incomplete",
    "strExpiry": "25 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "FU",
    "q3Credits": 1.5,
    "year2026Credits": 16.9,
    "lastActivity": "08 Nov 2026, 14.09",
    "periodLabel": "25 November 2024 - 25 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 0.9,
        "target": 8,
        "redPercent": 11,
        "yellowPercent": 19
      },
      "pending": 1.5,
      "pengabdian": {
        "achieved": 0,
        "target": 1
      },
      "pelayanan": {
        "achieved": 0.5,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 0.4,
        "target": 2.6
      }
    }
  },
  {
    "id": "doc-178",
    "pogiId": "#POGI-2026-1178",
    "name": "dr. Dewi Fauzi, Sp.OG, Subsp. Obginsos",
    "initials": "DF",
    "nik": "3273100000157174",
    "province": "Gorontalo",
    "hospital": "RSUD Prof. Dr. H. Aloei Saboe Gorontalo",
    "institution": "RSUD Prof. Dr. H. Aloei Saboe Gorontalo",
    "phone": "+62 812-7586-4382",
    "assignedTo": "Siti Rahma",
    "pelayananCredits": 15,
    "pengabdianCredits": 13,
    "pembelajaranCredits": 21,
    "q4Credits": 1.8,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "26 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "DF",
    "q3Credits": 2,
    "year2026Credits": 18.7,
    "lastActivity": "07 Nov 2026, 15.26",
    "periodLabel": "26 Maret 2024 - 26 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 1.8,
        "target": 8,
        "redPercent": 23,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 1,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 0.8,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-179",
    "pogiId": "#POGI-2026-1179",
    "name": "dr. Agus Saputra, Sp.OG, Subsp. Urogin-RE",
    "initials": "AS",
    "nik": "3273100000158057",
    "province": "Gorontalo",
    "hospital": "RSUD Prof. Dr. H. Aloei Saboe Gorontalo",
    "institution": "RSUD Prof. Dr. H. Aloei Saboe Gorontalo",
    "phone": "+62 812-7623-4401",
    "assignedTo": "Dewi Lestari",
    "pelayananCredits": 16,
    "pengabdianCredits": 14,
    "pembelajaranCredits": 22,
    "q4Credits": 2.7,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "27 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "AS",
    "q3Credits": 3.1,
    "year2026Credits": 20.6,
    "lastActivity": "06 Nov 2026, 16.43",
    "periodLabel": "27 November 2024 - 27 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 2.7,
        "target": 8,
        "redPercent": 34,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 1.5,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 1.2,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-180",
    "pogiId": "#POGI-2026-1180",
    "name": "Dr. dr. Andi Hidayat, Sp.OG, Subsp. Urogin-RE",
    "initials": "AH",
    "nik": "3273100000158940",
    "province": "Sulawesi Barat",
    "hospital": "RSUD Regional Sulawesi Barat Mamuju",
    "institution": "RSUD Regional Sulawesi Barat Mamuju",
    "phone": "+62 812-7660-4420",
    "assignedTo": "Rina Kartika",
    "pelayananCredits": 17,
    "pengabdianCredits": 5,
    "pembelajaranCredits": 8,
    "q4Credits": 3.6,
    "pendingCredits": 0.5,
    "status": "Incomplete",
    "strExpiry": "10 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "AH",
    "q3Credits": 2.9,
    "year2026Credits": 19.4,
    "lastActivity": "05 Nov 2026, 08.00",
    "periodLabel": "10 Maret 2024 - 10 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 3.6,
        "target": 8,
        "redPercent": 45,
        "yellowPercent": 6
      },
      "pending": 0.5,
      "pengabdian": {
        "achieved": 0.5,
        "target": 1
      },
      "pelayanan": {
        "achieved": 2,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 1.1,
        "target": 2.6
      }
    }
  },
  {
    "id": "doc-181",
    "pogiId": "#POGI-2026-1181",
    "name": "dr. Irfan Puspitasari, Sp.OG",
    "initials": "IP",
    "nik": "3273100000159823",
    "province": "Sulawesi Barat",
    "hospital": "RSUD Regional Sulawesi Barat Mamuju",
    "institution": "RSUD Regional Sulawesi Barat Mamuju",
    "phone": "+62 812-7697-4439",
    "assignedTo": "Melati Sihombing",
    "pelayananCredits": 18,
    "pengabdianCredits": 6,
    "pembelajaranCredits": 9,
    "q4Credits": 4.5,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "11 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "IP",
    "q3Credits": 3.9,
    "year2026Credits": 21.3,
    "lastActivity": "04 Nov 2026, 09.17",
    "periodLabel": "11 November 2024 - 11 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 4.5,
        "target": 8,
        "redPercent": 56,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 2.5,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 2,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-182",
    "pogiId": "#POGI-2026-1182",
    "name": "Prof. Dr. dr. Rudi Purnomo, Sp.OG",
    "initials": "RP",
    "nik": "3273100000160706",
    "province": "Maluku",
    "hospital": "RSUD Dr. M. Haulussy Ambon",
    "institution": "RSUD Dr. M. Haulussy Ambon",
    "phone": "+62 812-7734-4458",
    "assignedTo": "Wahyu Setiawan",
    "pelayananCredits": 19,
    "pengabdianCredits": 7,
    "pembelajaranCredits": 10,
    "q4Credits": 5.4,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "12 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "RP",
    "q3Credits": 4.9,
    "year2026Credits": 23.1,
    "lastActivity": "17 Nov 2026, 10.34",
    "periodLabel": "12 Maret 2024 - 12 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 5.4,
        "target": 11,
        "redPercent": 49,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 3,
        "target": 6.1
      },
      "pembelajaran": {
        "achieved": 2.4,
        "target": 3.4
      }
    }
  },
  {
    "id": "doc-183",
    "pogiId": "#POGI-2026-1183",
    "name": "dr. Doni Wijaya, Sp.OG, Subsp. Onk",
    "initials": "DW",
    "nik": "3273100000161589",
    "province": "Maluku",
    "hospital": "RSUD Dr. M. Haulussy Ambon",
    "institution": "RSUD Dr. M. Haulussy Ambon",
    "phone": "+62 812-7771-4477",
    "assignedTo": "Siti Rahma",
    "pelayananCredits": 20,
    "pengabdianCredits": 8,
    "pembelajaranCredits": 11,
    "q4Credits": 6.3,
    "pendingCredits": 2,
    "status": "Incomplete",
    "strExpiry": "13 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "DW",
    "q3Credits": 5.9,
    "year2026Credits": 25,
    "lastActivity": "16 Nov 2026, 11.51",
    "periodLabel": "13 November 2024 - 13 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 6.3,
        "target": 12.5,
        "redPercent": 50,
        "yellowPercent": 16
      },
      "pending": 2,
      "pengabdian": {
        "achieved": 0.5,
        "target": 1
      },
      "pelayanan": {
        "achieved": 3.5,
        "target": 6.9
      },
      "pembelajaran": {
        "achieved": 2.3,
        "target": 4.6
      }
    }
  },
  {
    "id": "doc-184",
    "pogiId": "#POGI-2026-1184",
    "name": "dr. Fajar Suryana, Sp.OG, Subsp. Onk",
    "initials": "FS",
    "nik": "3273100000162472",
    "province": "Maluku Utara",
    "hospital": "RSUD Dr. H. Chasan Boesoirie Ternate",
    "institution": "RSUD Dr. H. Chasan Boesoirie Ternate",
    "phone": "+62 812-7808-4496",
    "assignedTo": "Dewi Lestari",
    "pelayananCredits": 21,
    "pengabdianCredits": 9,
    "pembelajaranCredits": 12,
    "q4Credits": 0,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "14 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "FS",
    "q3Credits": 1.5,
    "year2026Credits": 16,
    "lastActivity": "15 Nov 2026, 12.08",
    "periodLabel": "14 Maret 2024 - 14 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 0,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 0,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 0,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-185",
    "pogiId": "#POGI-2026-1185",
    "name": "dr. Arif Anggraini, Sp.OG, Subsp. FER",
    "initials": "AA",
    "nik": "3273100000163355",
    "province": "Maluku Utara",
    "hospital": "RSUD Dr. H. Chasan Boesoirie Ternate",
    "institution": "RSUD Dr. H. Chasan Boesoirie Ternate",
    "phone": "+62 812-7845-4515",
    "assignedTo": "Rina Kartika",
    "pelayananCredits": 22,
    "pengabdianCredits": 10,
    "pembelajaranCredits": 13,
    "q4Credits": 0.9,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "15 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "AA",
    "q3Credits": 1.5,
    "year2026Credits": 17.9,
    "lastActivity": "14 Nov 2026, 13.25",
    "periodLabel": "15 November 2024 - 15 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 0.9,
        "target": 8,
        "redPercent": 11,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 0.5,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 0.4,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-186",
    "pogiId": "#POGI-2026-1186",
    "name": "dr. Surya Saputra, Sp.OG, Subsp. FER",
    "initials": "SS",
    "nik": "3273100000164238",
    "province": "Papua",
    "hospital": "RSUD Jayapura",
    "institution": "RSUD Jayapura",
    "phone": "+62 812-7882-4534",
    "assignedTo": "Melati Sihombing",
    "pelayananCredits": 23,
    "pengabdianCredits": 11,
    "pembelajaranCredits": 14,
    "q4Credits": 1.8,
    "pendingCredits": 1,
    "status": "Incomplete",
    "strExpiry": "16 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "SS",
    "q3Credits": 2,
    "year2026Credits": 16.7,
    "lastActivity": "13 Nov 2026, 14.42",
    "periodLabel": "16 Maret 2024 - 16 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 1.8,
        "target": 8,
        "redPercent": 23,
        "yellowPercent": 13
      },
      "pending": 1,
      "pengabdian": {
        "achieved": 0,
        "target": 1
      },
      "pelayanan": {
        "achieved": 1,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 0.8,
        "target": 2.6
      }
    }
  },
  {
    "id": "doc-187",
    "pogiId": "#POGI-2026-1187",
    "name": "dr. Asep Santoso, Sp.OG(K)",
    "initials": "AS",
    "nik": "3273100000165121",
    "province": "Papua",
    "hospital": "RS Dian Harapan Jayapura",
    "institution": "RS Dian Harapan Jayapura",
    "phone": "+62 812-7919-4553",
    "assignedTo": "Wahyu Setiawan",
    "pelayananCredits": 24,
    "pengabdianCredits": 12,
    "pembelajaranCredits": 15,
    "q4Credits": 2.7,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "17 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "AS",
    "q3Credits": 3.1,
    "year2026Credits": 18.6,
    "lastActivity": "12 Nov 2026, 15.59",
    "periodLabel": "17 November 2024 - 17 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 2.7,
        "target": 8,
        "redPercent": 34,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 1.5,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 1.2,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-188",
    "pogiId": "#POGI-2026-1188",
    "name": "Dr. dr. Bambang Fauzi, Sp.OG, Subsp. KFM",
    "initials": "BF",
    "nik": "3273100000166004",
    "province": "Papua",
    "hospital": "RSUD Jayapura",
    "institution": "RSUD Jayapura",
    "phone": "+62 812-7956-4572",
    "assignedTo": "Siti Rahma",
    "pelayananCredits": 25,
    "pengabdianCredits": 13,
    "pembelajaranCredits": 16,
    "q4Credits": 3.6,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "18 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "BF",
    "q3Credits": 2.9,
    "year2026Credits": 20.4,
    "lastActivity": "11 Nov 2026, 16.16",
    "periodLabel": "18 Maret 2024 - 18 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 3.6,
        "target": 8,
        "redPercent": 45,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 2,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 1.6,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-189",
    "pogiId": "#POGI-2026-1189",
    "name": "dr. Yusuf Saputra, Sp.OG, Subsp. Obginsos",
    "initials": "YS",
    "nik": "3273100000166887",
    "province": "Papua",
    "hospital": "RS Dian Harapan Jayapura",
    "institution": "RS Dian Harapan Jayapura",
    "phone": "+62 812-7993-4591",
    "assignedTo": "Dewi Lestari",
    "pelayananCredits": 26,
    "pengabdianCredits": 14,
    "pembelajaranCredits": 17,
    "q4Credits": 4.5,
    "pendingCredits": 2.5,
    "status": "Incomplete",
    "strExpiry": "19 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "YS",
    "q3Credits": 3.9,
    "year2026Credits": 22.3,
    "lastActivity": "10 Nov 2026, 08.33",
    "periodLabel": "19 November 2024 - 19 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 4.5,
        "target": 8,
        "redPercent": 56,
        "yellowPercent": 31
      },
      "pending": 2.5,
      "pengabdian": {
        "achieved": 0.5,
        "target": 1
      },
      "pelayanan": {
        "achieved": 2.5,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 1.5,
        "target": 2.6
      }
    }
  },
  {
    "id": "doc-190",
    "pogiId": "#POGI-2026-1190",
    "name": "Prof. Dr. dr. Budi Wijaya, Sp.OG, Subsp. KFM",
    "initials": "BW",
    "nik": "3273100000167770",
    "province": "Papua Barat",
    "hospital": "RSUD Manokwari",
    "institution": "RSUD Manokwari",
    "phone": "+62 812-8030-4610",
    "assignedTo": "Rina Kartika",
    "pelayananCredits": 27,
    "pengabdianCredits": 5,
    "pembelajaranCredits": 18,
    "q4Credits": 5.4,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "20 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "BW",
    "q3Credits": 4.9,
    "year2026Credits": 24.1,
    "lastActivity": "09 Nov 2026, 09.50",
    "periodLabel": "20 Maret 2024 - 20 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 5.4,
        "target": 8,
        "redPercent": 68,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 3,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 2.4,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-191",
    "pogiId": "#POGI-2026-1191",
    "name": "dr. Andi Sumampouw, Sp.OG, Subsp. Obginsos",
    "initials": "AS",
    "nik": "3273100000168653",
    "province": "Papua Barat",
    "hospital": "RSUD Manokwari",
    "institution": "RSUD Manokwari",
    "phone": "+62 812-8067-4629",
    "assignedTo": "Melati Sihombing",
    "pelayananCredits": 28,
    "pengabdianCredits": 6,
    "pembelajaranCredits": 19,
    "q4Credits": 6.3,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "21 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "AS",
    "q3Credits": 5.9,
    "year2026Credits": 26,
    "lastActivity": "08 Nov 2026, 10.07",
    "periodLabel": "21 November 2024 - 21 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 6.3,
        "target": 9.5,
        "redPercent": 66,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 3.5,
        "target": 5.2
      },
      "pembelajaran": {
        "achieved": 2.8,
        "target": 2.8
      }
    }
  },
  {
    "id": "doc-192",
    "pogiId": "#POGI-2026-1192",
    "name": "dr. Eko Anggraini, Sp.OG, Subsp. Obginsos",
    "initials": "EA",
    "nik": "3273100000169536",
    "province": "Papua Selatan",
    "hospital": "RSUD Merauke",
    "institution": "RSUD Merauke",
    "phone": "+62 812-8104-4648",
    "assignedTo": "Wahyu Setiawan",
    "pelayananCredits": 29,
    "pengabdianCredits": 7,
    "pembelajaranCredits": 20,
    "q4Credits": 0,
    "pendingCredits": 1.5,
    "status": "Incomplete",
    "strExpiry": "22 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "EA",
    "q3Credits": 1.5,
    "year2026Credits": 14,
    "lastActivity": "07 Nov 2026, 11.24",
    "periodLabel": "22 Maret 2024 - 22 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 0,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 19
      },
      "pending": 1.5,
      "pengabdian": {
        "achieved": 0,
        "target": 1
      },
      "pelayanan": {
        "achieved": 0,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 0,
        "target": 2.6
      }
    }
  },
  {
    "id": "doc-193",
    "pogiId": "#POGI-2026-1193",
    "name": "dr. Maya Simatupang, Sp.OG, Subsp. FER",
    "initials": "MS",
    "nik": "3273100000170419",
    "province": "Papua Tengah",
    "hospital": "RSUD Nabire",
    "institution": "RSUD Nabire",
    "phone": "+62 812-8141-4667",
    "assignedTo": "Siti Rahma",
    "pelayananCredits": 30,
    "pengabdianCredits": 8,
    "pembelajaranCredits": 21,
    "q4Credits": 0.9,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "23 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "MS",
    "q3Credits": 1.5,
    "year2026Credits": 15.9,
    "lastActivity": "06 Nov 2026, 12.41",
    "periodLabel": "23 November 2024 - 23 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 0.9,
        "target": 8,
        "redPercent": 11,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 0.5,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 0.4,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-194",
    "pogiId": "#POGI-2026-1194",
    "name": "dr. Dedi Santoso, Sp.OG, Subsp. Urogin-RE",
    "initials": "DS",
    "nik": "3273100000171302",
    "province": "Papua Pegunungan",
    "hospital": "RSUD Wamena",
    "institution": "RSUD Wamena",
    "phone": "+62 812-8178-4686",
    "assignedTo": "Dewi Lestari",
    "pelayananCredits": 59,
    "pengabdianCredits": 29,
    "pembelajaranCredits": 42,
    "q4Credits": 12,
    "pendingCredits": 0,
    "status": "Complete",
    "strExpiry": "24 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "DS",
    "q3Credits": 10,
    "year2026Credits": 36,
    "lastActivity": "05 Nov 2026, 13.58",
    "periodLabel": "24 Maret 2024 - 24 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 12,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 0,
        "isGreen": true
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 1,
        "target": 0.5
      },
      "pelayanan": {
        "achieved": 6.6,
        "target": 5
      },
      "pembelajaran": {
        "achieved": 4.4,
        "target": 2.5
      }
    }
  },
  {
    "id": "doc-195",
    "pogiId": "#POGI-2026-1195",
    "name": "dr. Fajar Fauzi, Sp.OG",
    "initials": "FF",
    "nik": "3273100000172185",
    "province": "Papua Pegunungan",
    "hospital": "RSUD Wamena",
    "institution": "RSUD Wamena",
    "phone": "+62 812-8215-4705",
    "assignedTo": "Rina Kartika",
    "pelayananCredits": 60,
    "pengabdianCredits": 15,
    "pembelajaranCredits": 43,
    "q4Credits": 6.2,
    "pendingCredits": 0.5,
    "status": "Incomplete",
    "strExpiry": "25 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "FF",
    "q3Credits": 5.9,
    "year2026Credits": 24.8,
    "lastActivity": "04 Nov 2026, 14.15",
    "periodLabel": "25 November 2024 - 25 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 6.2,
        "target": 8,
        "redPercent": 78,
        "yellowPercent": 6
      },
      "pending": 0.5,
      "pengabdian": {
        "achieved": 0.5,
        "target": 1
      },
      "pelayanan": {
        "achieved": 3.4,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 2.3,
        "target": 2.6
      }
    }
  },
  {
    "id": "doc-196",
    "pogiId": "#POGI-2026-1196",
    "name": "Dr. dr. Arif Saputra, Sp.OG, Subsp. Onk",
    "initials": "AS",
    "nik": "3273100000173068",
    "province": "Papua Pegunungan",
    "hospital": "RSUD Wamena",
    "institution": "RSUD Wamena",
    "phone": "+62 812-8252-4724",
    "assignedTo": "Melati Sihombing",
    "pelayananCredits": 61,
    "pengabdianCredits": 16,
    "pembelajaranCredits": 44,
    "q4Credits": 6.2,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "26 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "AS",
    "q3Credits": 5,
    "year2026Credits": 25.3,
    "lastActivity": "17 Nov 2026, 15.32",
    "periodLabel": "26 Maret 2024 - 26 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 6.2,
        "target": 9.5,
        "redPercent": 65,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 3.4,
        "target": 5.2
      },
      "pembelajaran": {
        "achieved": 2.8,
        "target": 2.8
      }
    }
  },
  {
    "id": "doc-197",
    "pogiId": "#POGI-2026-1197",
    "name": "dr. Ahmad Santoso, Sp.OG, Subsp. FER",
    "initials": "AS",
    "nik": "3273100000173951",
    "province": "Papua Pegunungan",
    "hospital": "RSUD Wamena",
    "institution": "RSUD Wamena",
    "phone": "+62 812-8289-4743",
    "assignedTo": "Wahyu Setiawan",
    "pelayananCredits": 34,
    "pengabdianCredits": 12,
    "pembelajaranCredits": 10,
    "q4Credits": 4.5,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "27 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "AS",
    "q3Credits": 3.9,
    "year2026Credits": 23.3,
    "lastActivity": "16 Nov 2026, 16.49",
    "periodLabel": "27 November 2024 - 27 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 4.5,
        "target": 8,
        "redPercent": 56,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 2.5,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 2,
        "target": 2.1
      }
    }
  },
  {
    "id": "doc-198",
    "pogiId": "#POGI-2026-1198",
    "name": "Prof. Dr. dr. Dedi Fauzi, Sp.OG(K)",
    "initials": "DF",
    "nik": "3273100000174834",
    "province": "Papua Pegunungan",
    "hospital": "RSUD Wamena",
    "institution": "RSUD Wamena",
    "phone": "+62 812-8326-4762",
    "assignedTo": "Siti Rahma",
    "pelayananCredits": 35,
    "pengabdianCredits": 13,
    "pembelajaranCredits": 11,
    "q4Credits": 5.4,
    "pendingCredits": 2,
    "status": "Incomplete",
    "strExpiry": "10 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "DF",
    "q3Credits": 4.9,
    "year2026Credits": 22.1,
    "lastActivity": "15 Nov 2026, 08.06",
    "periodLabel": "10 Maret 2024 - 10 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 5.4,
        "target": 12.5,
        "redPercent": 43,
        "yellowPercent": 16
      },
      "pending": 2,
      "pengabdian": {
        "achieved": 0.5,
        "target": 1
      },
      "pelayanan": {
        "achieved": 3,
        "target": 6.9
      },
      "pembelajaran": {
        "achieved": 1.9,
        "target": 4.6
      }
    }
  },
  {
    "id": "doc-199",
    "pogiId": "#POGI-2026-1199",
    "name": "dr. Fajar Saputra, Sp.OG, Subsp. KFM",
    "initials": "FS",
    "nik": "3273100000175717",
    "province": "Papua Pegunungan",
    "hospital": "RSUD Wamena",
    "institution": "RSUD Wamena",
    "phone": "+62 812-8363-4781",
    "assignedTo": "Dewi Lestari",
    "pelayananCredits": 36,
    "pengabdianCredits": 14,
    "pembelajaranCredits": 12,
    "q4Credits": 6.3,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "11 November 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "FS",
    "q3Credits": 5.9,
    "year2026Credits": 24,
    "lastActivity": "14 Nov 2026, 09.23",
    "periodLabel": "11 November 2024 - 11 November 2029",
    "q4Details": {
      "skp": {
        "achieved": 6.3,
        "target": 14,
        "redPercent": 45,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 3.5,
        "target": 7.7
      },
      "pembelajaran": {
        "achieved": 2.8,
        "target": 4.8
      }
    }
  },
  {
    "id": "doc-200",
    "pogiId": "#POGI-2026-1200",
    "name": "dr. Nurul Nugroho, Sp.OG, Subsp. FER",
    "initials": "NN",
    "nik": "3273100000176600",
    "province": "Papua Barat Daya",
    "hospital": "RSUD Sele Be Solu Sorong",
    "institution": "RSUD Sele Be Solu Sorong",
    "phone": "+62 812-8400-4800",
    "assignedTo": "Rina Kartika",
    "pelayananCredits": 12,
    "pengabdianCredits": 5,
    "pembelajaranCredits": 13,
    "q4Credits": 0,
    "pendingCredits": 0,
    "status": "Incomplete",
    "strExpiry": "12 Maret 2029",
    "validUntilLabel": "Siklus 5 Thn: Habis 2029",
    "avatar": "NN",
    "q3Credits": 1.5,
    "year2026Credits": 15,
    "lastActivity": "13 Nov 2026, 10.40",
    "periodLabel": "12 Maret 2024 - 12 Maret 2029",
    "q4Details": {
      "skp": {
        "achieved": 0,
        "target": 8,
        "redPercent": 0,
        "yellowPercent": 0
      },
      "pending": 0,
      "pengabdian": {
        "achieved": 0,
        "target": 1.5
      },
      "pelayanan": {
        "achieved": 0,
        "target": 4.4
      },
      "pembelajaran": {
        "achieved": 0,
        "target": 2.1
      }
    }
  }
];

// Master Institutions (From Screenshot WhatsApp Image 2026-11-19 at 09.13.17.jpeg)
const INITIAL_INSTITUTIONS = [
  { id: 'inst-01', name: 'Klinik Pratama Sehat Sentosa', doctors: 3, province: 'Jawa Barat', address: 'Jl. R.E. Martadinata No. 45, Bandung' },
  { id: 'inst-02', name: 'Puskesmas Banyumas', doctors: 2, province: 'Jawa Tengah', address: 'Jl. Gatot Subroto No. 12, Banyumas' },
  { id: 'inst-03', name: 'Puskesmas Kecamatan Tebet', doctors: 3, province: 'DKI Jakarta', address: 'Jl. Prof. Dr. Soepomo No. 54, Tebet, Jakarta Selatan' },
  { id: 'inst-04', name: 'Puskesmas Waingapu', doctors: 2, province: 'Nusa Tenggara Timur', address: 'Jl. Ahmad Yani No. 8, Waingapu, Sumba Timur' },
  { id: 'inst-05', name: 'RS Husada Utama', doctors: 2, province: 'Jawa Timur', address: 'Jl. Mayjen Prof. Dr. Moestopo No. 31-35, Surabaya' },
  { id: 'inst-06', name: 'RS PKU Muhammadiyah Yogyakarta', doctors: 5, province: 'DI Yogyakarta', address: 'Jl. KH. Ahmad Dahlan No. 20, Yogyakarta' },
  { id: 'inst-07', name: 'RS Pondok Indah', doctors: 2, province: 'DKI Jakarta', address: 'Jl. Metro Duta Kav. UE, Pondok Indah, Jakarta Selatan' },
  { id: 'inst-08', name: 'RS Siloam Lippo Village', doctors: 2, province: 'Banten', address: 'Jl. Siloam No. 6, Lippo Karawaci, Tangerang' },
  { id: 'inst-09', name: 'RS Stella Maris Makassar', doctors: 4, province: 'Sulawesi Selatan', address: 'Jl. Somba Opu No. 273, Makassar' },
  { id: 'inst-10', name: 'RSUD Abdul Wahab Sjahranie', doctors: 3, province: 'Kalimantan Timur', address: 'Jl. Palang Merah No. 1, Samarinda' },
  { id: 'inst-11', name: 'RSUP Dr. Cipto Mangunkusumo', doctors: 14, province: 'DKI Jakarta', address: 'Jl. Diponegoro No. 71, Jakarta Pusat' },
  { id: 'inst-12', name: 'RSUP Dr. Hasan Sadikin', doctors: 8, province: 'Jawa Barat', address: 'Jl. Pasteur No. 38, Bandung' },
  { id: 'inst-13', name: 'RSUD Dr. Soetomo', doctors: 12, province: 'Jawa Timur', address: 'Jl. Mayjen Prof. Dr. Moestopo No. 6-8, Surabaya' }
];

// Master SKP Events (From Screenshot WhatsApp Image 2026-11-19 at 09.13.30.jpeg)
const INITIAL_EVENTS = [
  {
    id: 'ev-01',
    name: 'Simposium Akhir Tahun Kardiologi Klinis & Maternal',
    category: 'Simposium Ilmiah',
    date: '19 Des 2026',
    type: 'Pembelajaran',
    credits: 1.5,
    creditsDisplay: '1,5',
    doctors: '0 / 60 selesai',
    status: 'Dibuka'
  },
  {
    id: 'ev-02',
    name: 'Bakti Sosial Khitanan Massal & Skrining Serviks',
    category: 'Bakti Sosial',
    date: '12 Des 2026',
    type: 'Pengabdian',
    credits: 1.0,
    creditsDisplay: '1',
    doctors: '0 / 74 selesai',
    status: 'Dibuka'
  },
  {
    id: 'ev-03',
    name: 'Workshop USG Point-of-Care Fetomaternal',
    category: 'Workshop / Pelatihan',
    date: '5 Des 2026',
    type: 'Pembelajaran',
    credits: 2.0,
    creditsDisplay: '2',
    doctors: '0 / 88 selesai',
    status: 'Dibuka'
  },
  {
    id: 'ev-04',
    name: 'Jaga IGD Maternal PONEK Oktober-November',
    category: 'Jaga IGD / Piket',
    date: '15 Nov 2026',
    type: 'Pelayanan',
    credits: 1.0,
    creditsDisplay: '1',
    doctors: '31 / 109 selesai',
    status: 'Berjalan'
  },
  {
    id: 'ev-05',
    name: 'Praktik Klinis Poliklinik Triwulan IV',
    category: 'Praktik Klinis',
    date: '14 Nov 2026',
    type: 'Pelayanan',
    credits: 5.0,
    creditsDisplay: '5',
    doctors: '51 / 153 selesai',
    status: 'Berjalan'
  },
  {
    id: 'ev-06',
    name: 'Webinar Vaksinasi Dewasa & Imunisasi Kehamilan',
    category: 'Seminar / Webinar',
    date: '8 Nov 2026',
    type: 'Pembelajaran',
    credits: 0.5,
    creditsDisplay: '0,5',
    doctors: '43 / 128 selesai',
    status: 'Selesai'
  },
  {
    id: 'ev-07',
    name: 'Tindakan Medis Terjadwal Triwulan IV (Sectio Caesarea)',
    category: 'Tindakan Medis',
    date: '7 Nov 2026',
    type: 'Pelayanan',
    credits: 2.0,
    creditsDisplay: '2',
    doctors: '65 / 152 selesai',
    status: 'Berjalan'
  },
  {
    id: 'ev-08',
    name: 'Penyuluhan Gizi Seimbang Ibu Hamil & Cegah Stunting',
    category: 'Penyuluhan Kesehatan Masyarakat',
    date: '1 Nov 2026',
    type: 'Pengabdian',
    credits: 0.5,
    creditsDisplay: '0,5',
    doctors: '54 / 133 selesai',
    status: 'Selesai'
  },
  {
    id: 'ev-09',
    name: 'Bakti Sosial Pemeriksaan Lansia & Kesehatan Reproduksi',
    category: 'Bakti Sosial',
    date: '31 Okt 2026',
    type: 'Pengabdian',
    credits: 0.5,
    creditsDisplay: '0,5',
    doctors: '42 / 108 selesai',
    status: 'Selesai'
  },
  {
    id: 'ev-10',
    name: 'Webinar Kesehatan Jiwa di Layanan Primer & Postpartum',
    category: 'Seminar / Webinar',
    date: '24 Okt 2026',
    type: 'Pembelajaran',
    credits: 1.0,
    creditsDisplay: '1',
    doctors: '108 / 151 selesai',
    status: 'Selesai'
  }
];

// Master Event Categories (From Screenshot WhatsApp Image 2026-11-19 at 09.13.44.jpeg)
const INITIAL_CATEGORIES = [
  { id: 'cat-01', name: 'Jaga IGD / Piket', type: 'Pelayanan', eventsCount: 3 },
  { id: 'cat-02', name: 'Pelayanan Rujukan', type: 'Pelayanan', eventsCount: 2 },
  { id: 'cat-03', name: 'Praktik Klinis', type: 'Pelayanan', eventsCount: 4 },
  { id: 'cat-04', name: 'Tindakan Medis', type: 'Pelayanan', eventsCount: 3 },
  { id: 'cat-05', name: 'Narasumber / Pembicara', type: 'Pembelajaran', eventsCount: 1 },
  { id: 'cat-06', name: 'Publikasi Jurnal Ilmiah', type: 'Pembelajaran', eventsCount: 1 },
  { id: 'cat-07', name: 'Seminar / Webinar', type: 'Pembelajaran', eventsCount: 7 },
  { id: 'cat-08', name: 'Simposium Ilmiah', type: 'Pembelajaran', eventsCount: 5 },
  { id: 'cat-09', name: 'Workshop / Pelatihan', type: 'Pembelajaran', eventsCount: 5 },
  { id: 'cat-10', name: 'Bakti Sosial', type: 'Pengabdian', eventsCount: 4 },
  { id: 'cat-11', name: 'Penyuluhan Kesehatan Masyarakat', type: 'Pengabdian', eventsCount: 3 }
];

// Master Users (From Screenshot WhatsApp Image 2026-11-19 at 09.13.57.jpeg)
const INITIAL_USERS = [
  { id: 'usr-01', name: 'Rina Kartika', email: 'user@example.com', role: 'User', created: '18 Nov 2026' },
  { id: 'usr-02', name: 'Dimas Saputra', email: 'user2@example.com', role: 'User', created: '18 Nov 2026' },
  { id: 'usr-03', name: 'Wahyu Setiawan', email: 'user3@example.com', role: 'User', created: '18 Nov 2026' },
  { id: 'usr-04', name: 'Melati Sihombing', email: 'user4@example.com', role: 'User', created: '18 Nov 2026' },
  { id: 'usr-05', name: 'Fikri Ramadhan', email: 'user5@example.com', role: 'User', created: '18 Nov 2026' },
  { id: 'usr-06', name: 'Ayu Lestari', email: 'user6@example.com', role: 'User', created: '18 Nov 2026' },
  { id: 'usr-07', name: 'Bagus Pranoto', email: 'user7@example.com', role: 'User', created: '18 Nov 2026' },
  { id: 'usr-08', name: 'Citra Maharani', email: 'user8@example.com', role: 'User', created: '18 Nov 2026' },
  { id: 'usr-09', name: 'Dodi Firmansyah', email: 'user9@example.com', role: 'User', created: '18 Nov 2026' },
  { id: 'usr-10', name: 'Eka Putri', email: 'user10@example.com', role: 'User', created: '18 Nov 2026' }
];

const INITIAL_ACTIVITIES = [
  {
    id: 'act-01',
    actor: 'Siti Rahma (Admin Data Pusat)',
    action: 'Verifikasi Kredit SKP',
    details: 'Menyetujui 8 SKP Pembelajaran Workshop USG untuk dr. Maya Indah, Sp.OG',
    timestamp: '23 Nov 2026, 14:32 WIB',
    category: 'Verification'
  },
  {
    id: 'act-02',
    actor: 'Ahmad Fauzi (PIC POGI Cabang Jabar)',
    action: 'Kirim Reminder FU Cabang',
    details: 'Kirim notifikasi massal WhatsApp pemenuhan Q4 ke 9 dokter Incomplete di Jabar',
    timestamp: '23 Nov 2026, 11:20 WIB',
    category: 'FollowUp'
  },
  {
    id: 'act-03',
    actor: 'System Integration',
    action: 'Sinkronisasi',
    details: 'Tarik otomatis 34 logbook Plataran Sehat Kemenkes periode Oktober-November',
    timestamp: '23 Nov 2026, 09:00 WIB',
    category: 'Sync'
  },
  {
    id: 'act-04',
    actor: 'dr. Farhan Malik, Sp.OG',
    action: 'Unggah Dokumen Bukti',
    details: 'Mengunggah sertifikat bakti sosial skrining serviks 6 SKP',
    timestamp: '22 Nov 2026, 16:45 WIB',
    category: 'Upload'
  },
  {
    id: 'act-05',
    actor: 'Dr. Eleanor Vance (Tim Pengelola)',
    action: 'Pembaruan Status Dokter',
    details: 'Mengubah status dr. Budi Santoso menjadi Complete (Total 164 SKP / 5 Thn)',
    timestamp: '22 Nov 2026, 10:15 WIB',
    category: 'StatusUpdate'
  }
];

// Helper to access / persist state in LocalStorage

// Storage Version Guard to automatically invalidate stale browser cache
const POGI_STORAGE_VERSION = 'v2026.11.23.11';
if (typeof localStorage !== 'undefined') {
  try {
    if (localStorage.getItem('pogi_app_ver') !== POGI_STORAGE_VERSION) {
      localStorage.removeItem('pogi_doctors');
      localStorage.removeItem('pogi_events');
      localStorage.removeItem('pogi_institutions');
      localStorage.removeItem('pogi_users');
      localStorage.removeItem('pogi_categories');
      // Bersihkan juga cache riwayat logbook dokter agar terhubung ke Master Events terbaru
      Object.keys(localStorage).forEach(k => {
        if (k.startsWith('pogi_doctor_activities_')) localStorage.removeItem(k);
      });
      localStorage.setItem('pogi_app_ver', POGI_STORAGE_VERSION);
    }
  } catch(e) {}
}

const PogiStore = {
  getDoctors() {
    let list = INITIAL_DOCTORS;
    if (typeof localStorage !== 'undefined') {
      try {
        const data = localStorage.getItem('pogi_doctors');
        if (data) {
          const parsed = JSON.parse(data);
          if (Array.isArray(parsed) && parsed.length >= 200 && parsed.some(d => d.id === 'doc-001' && d.name === 'Nurul Anggraini')) {
            list = parsed;
          } else {
            list = INITIAL_DOCTORS;
            localStorage.setItem('pogi_doctors', JSON.stringify(INITIAL_DOCTORS));
          }
        } else {
          localStorage.setItem('pogi_doctors', JSON.stringify(INITIAL_DOCTORS));
        }
      } catch(e) {
        // storage disabled or quota exceeded
      }
    }

    // Comprehensive normalizer guaranteeing zero undefined fields
    return list.map((d, idx) => {
      const q4Credits = +(d.q4Credits !== undefined ? d.q4Credits : (d.qAchieved !== undefined ? d.qAchieved : 8.0));
      const isCompleteQ4 = (d.status === 'Complete');
      let pelCredits = +(d.pelayananCredits !== undefined ? d.pelayananCredits : 0);
      let pengCredits = +(d.pengabdianCredits !== undefined ? d.pengabdianCredits : 0);
      let pembCredits = +(d.pembelajaranCredits !== undefined ? d.pembelajaranCredits : 0);

      // Preserve explicit credits saved in doctor object
      if (d.pelayananCredits === undefined || d.pelayananCredits === null) {
        pelCredits = isCompleteQ4 ? (80 + (idx % 12)) : (24 + ((idx * 7) % 45));
      }
      if (d.pengabdianCredits === undefined || d.pengabdianCredits === null) {
        pengCredits = isCompleteQ4 ? (30 + (idx % 6)) : (8 + ((idx * 5) % 18));
      }
      if (d.pembelajaranCredits === undefined || d.pembelajaranCredits === null) {
        pembCredits = isCompleteQ4 ? (50 + (idx % 8)) : (16 + ((idx * 9) % 28));
      }
      const total5Year = +(pelCredits + pengCredits + pembCredits);
      const hospitalName = d.hospital || d.institution || 'RS Jejaring POGI';
      const initials = d.initials || d.avatar || (d.name ? d.name.replace(/dr\.\s*|Sp\.OG|Prof\.|Dr\./gi, '').trim().split(' ').slice(0, 2).map(n => n[0]).join('').toUpperCase() : 'DR');

      let q3Credits = +(d.q3Credits !== undefined ? d.q3Credits : 0);
      if (!q3Credits) {
        if (isCompleteQ4) {
          q3Credits = Math.round((8.0 + (q4Credits - 8.0) * 0.5 + (idx % 5) * 0.2) * 10) / 10;
        } else if (idx % 13 === 0) {
          q3Credits = Math.round((8.0 + (idx % 3) * 0.3) * 10) / 10;
        } else {
          q3Credits = Math.round(Math.max(1.0, q4Credits * 0.95 + (idx % 4) * 0.4) * 10) / 10;
          if (q3Credits >= 8.0) q3Credits = 7.6;
        }
      }

      let year2026Credits = +(d.year2026Credits !== undefined ? d.year2026Credits : 0);
      if (!year2026Credits) {
        if (isCompleteQ4) {
          year2026Credits = Math.round((32.0 + (idx % 7) * 0.8) * 10) / 10;
        } else {
          year2026Credits = Math.round(Math.min(29.8, 14.0 + q4Credits * 1.8 + (idx % 6) * 0.5) * 10) / 10;
        }
      }

      let lastAct = d.lastActivity;
      if (!lastAct || lastAct.includes('Sync SATUSEHAT')) {
        const daysAgo = (idx % 25) + 1;
        const hours = (8 + (idx % 9)).toString().padStart(2, '0');
        const minutes = ((idx * 17) % 60).toString().padStart(2, '0');
        let dateStr = '17 Nov 2026';
        if (daysAgo <= 17) {
          dateStr = `${(18 - daysAgo).toString().padStart(2, '0')} Nov 2026`;
        } else {
          dateStr = `${(31 - (daysAgo - 17)).toString().padStart(2, '0')} Okt 2026`;
        }
        lastAct = `${dateStr}, ${hours}.${minutes}`;
      }

      return {
        ...d,
        uuid: d.uuid || this.generateUUIDv7(),
        avatar: initials,
        initials: initials,
        hospital: hospitalName,
        institution: hospitalName,
        q4Credits: q4Credits,
        q3Credits: q3Credits,
        year2026Credits: year2026Credits,
        qAchieved: q4Credits,
        qTarget: 8.0,
        pelayananCredits: pelCredits,
        pelayanan: { achieved: pelCredits, target: 56 },
        pengabdianCredits: pengCredits,
        pengabdian: { achieved: pengCredits, target: 8 },
        pembelajaranCredits: pembCredits,
        pembelajaran: { achieved: pembCredits, target: 72 },
        fleksibilitas: { achieved: Math.min(24, Math.max(0, +(total5Year - 136).toFixed(1))), target: 24 },
        totalCredits: total5Year,
        total5YearAchieved: total5Year,
        lastActivity: lastAct,
        pendingCredits: +(d.pendingCredits || 0)
      };
    });
  },
  saveDoctors(doctors) {
    localStorage.setItem('pogi_doctors', JSON.stringify(doctors));
  },
  getDoctorPeriodStats(doc, period = 'Q4 2026') {
    const isBaseline = (!period || period === 'Q4 2026' || period === 'ALL' || period === 'ALL ALL' || period === '2026');

    if (isBaseline) {
      if (doc.q4Details) {
        const q4d = doc.q4Details;
        return {
          period: 'Q4 2026',
          periodName: 'Q4 2026',
          columnHeader: 'SKP',
          achieved: q4d.skp.achieved,
          target: q4d.skp.target,
          redPercent: q4d.skp.redPercent || 0,
          yellowPercent: q4d.skp.yellowPercent || 0,
          isGreen: !!q4d.skp.isGreen,
          isComplete: doc.status === 'Complete',
          pendingCredits: q4d.pending,
          pengabdian: q4d.pengabdian,
          pelayanan: q4d.pelayanan,
          pembelajaran: q4d.pembelajaran,
          lastActivity: doc.lastActivity || '17 Nov 2026, 09.00'
        };
      }

      // Dynamic consistent generation for doc-011 to doc-200 following the exact concept from reference
      const idx = parseInt((doc.id || '').replace('doc-', ''), 10) || 11;
      const isComplete = doc.status === 'Complete';

      if (isComplete) {
        const tgtPeng = (idx % 3 === 0) ? 0 : 0.5;
        const tgtPel = (idx % 2 === 0) ? 1.5 : 2.0;
        const tgtPemb = (idx % 4 === 0) ? 0 : 0.5;
        const target = Math.round((tgtPeng + tgtPel + tgtPemb) * 100) / 100;

        const achPeng = Math.round((tgtPeng + 0.5 + (idx % 3) * 0.5) * 10) / 10;
        const achPel = Math.round((tgtPel + 2.0 + (idx % 5) * 0.5) * 10) / 10;
        const achPemb = Math.round((tgtPemb + 2.5 + (idx % 4) * 0.5) * 10) / 10;
        const achieved = Math.round((achPeng + achPel + achPemb) * 100) / 100;
        const pending = (idx % 2 === 0) ? Math.round((1.5 + (idx % 4) * 1.0) * 10) / 10 : 0;

        return {
          period: 'Q4 2026',
          periodName: 'Q4 2026',
          columnHeader: 'SKP',
          achieved,
          target,
          redPercent: 0,
          yellowPercent: 0,
          isGreen: true,
          isComplete: true,
          pendingCredits: pending,
          pengabdian: { achieved: achPeng, target: tgtPeng },
          pelayanan: { achieved: achPel, target: tgtPel },
          pembelajaran: { achieved: achPemb, target: tgtPemb },
          lastActivity: doc.lastActivity || '17 Nov 2026, 09.00'
        };
      } else {
        // Incomplete patterns modeled directly on the screenshot
        const patterns = [
          { peng: [0.5, 1.0], pel: [0.0, 2.5], pemb: [1.0, 1.25], pend: 2.5 },
          { peng: [0.0, 1.0], pel: [5.0, 11.0], pemb: [4.0, 4.5], pend: 3.0 },
          { peng: [0.0, 1.5], pel: [5.0, 9.0], pemb: [4.0, 6.5], pend: 5.0 },
          { peng: [0.0, 1.0], pel: [5.0, 7.5], pemb: [2.5, 8.5], pend: 3.5 },
          { peng: [0.5, 1.0], pel: [2.0, 6.5], pemb: [3.0, 0.75], pend: 1.0 },
          { peng: [0.0, 1.0], pel: [0.0, 9.0], pemb: [0.0, 7.75], pend: 0 },
          { peng: [0.5, 1.5], pel: [0.0, 17.0], pemb: [0.0, 8.5], pend: 0 },
          { peng: [0.5, 1.0], pel: [0.0, 0.0], pemb: [1.5, 0.25], pend: 9.5 }
        ];
        const p = patterns[idx % patterns.length];
        const tgtPeng = p.peng[1];
        const achPeng = p.peng[0];
        const tgtPel = p.pel[1];
        const achPel = p.pel[0];
        const tgtPemb = p.pemb[1];
        const achPemb = p.pemb[0];
        const target = Math.round((tgtPeng + tgtPel + tgtPemb) * 100) / 100;
        const achieved = Math.round((achPeng + achPel + achPemb) * 100) / 100;
        const pending = p.pend;
        const redPct = target > 0 ? Math.min(70, Math.round((achieved / target) * 70)) : 10;
        const yelPct = target > 0 ? Math.min(30, Math.round((pending / target) * 30)) : (pending > 0 ? 20 : 0);

        return {
          period: 'Q4 2026',
          periodName: 'Q4 2026',
          columnHeader: 'SKP',
          achieved,
          target,
          redPercent: redPct,
          yellowPercent: yelPct,
          isGreen: false,
          isComplete: false,
          pendingCredits: pending,
          pengabdian: { achieved: achPeng, target: tgtPeng },
          pelayanan: { achieved: achPel, target: tgtPel },
          pembelajaran: { achieved: achPemb, target: tgtPemb },
          lastActivity: doc.lastActivity || '17 Nov 2026, 09.00'
        };
      }
    }

    // Historical & Alternative Period Support (e.g. Q1-Q3 2026, 2025, 2024, etc.)
    const idx = parseInt((doc.id || '').replace('doc-', ''), 10) || 1;
    const isPastYear = period.includes('2025') || period.includes('2024') || period.includes('2023');
    const isQ3_2026 = period === 'Q3 2026';
    const isFullYear = !period.startsWith('Q');
    const target = isFullYear ? 32.0 : 8.0;

    let achieved = 0;
    let pending = 0;
    let isComplete = false;

    if (isQ3_2026 && doc.q3Credits !== undefined) {
      achieved = doc.q3Credits;
      pending = 0;
      isComplete = achieved >= 8.0;
    } else if (isPastYear) {
      // Historical periods (2025/2024) are finalized — pending credits are 0
      pending = 0;
      if (isFullYear) {
        achieved = Math.round((26.0 + ((idx * 13) % 11) * 0.8) * 10) / 10;
      } else {
        achieved = Math.round((6.0 + ((idx * 7) % 7) * 0.5) * 10) / 10;
      }
      isComplete = achieved >= target;
    } else {
      const baseQ = doc.q4Credits || 4.0;
      achieved = Math.round((baseQ * 0.9 + ((idx * 3) % 4) * 0.5) * 10) / 10;
      pending = 0;
      isComplete = achieved >= target;
    }

    const tgtPel = Math.round(target * 0.35 * 10) / 10;
    const tgtPeng = Math.round(target * 0.05 * 10) / 10;
    const tgtPemb = Math.round(target * 0.45 * 10) / 10;

    const achPel = Math.round(Math.min(target, achieved * 0.5) * 10) / 10;
    const achPeng = Math.round(Math.min(target, achieved * 0.15) * 10) / 10;
    const achPemb = Math.round(Math.max(0, achieved - achPel - achPeng) * 10) / 10;

    const redPct = isComplete ? 0 : Math.min(100, Math.round((achieved / target) * 70));
    const yelPct = isComplete ? 0 : (pending > 0 ? Math.min(30, Math.round((pending / target) * 30)) : 0);

    let lastActDate = '31 Des 2025, 17.00';
    if (period.includes('2024')) lastActDate = '31 Des 2024, 17.00';
    else if (period.includes('2026')) lastActDate = '30 Sep 2026, 17.00';

    return {
      period,
      periodName: period,
      columnHeader: 'SKP',
      achieved,
      target,
      redPercent: redPct,
      yellowPercent: yelPct,
      isGreen: isComplete,
      isComplete,
      pendingCredits: pending,
      pengabdian: { achieved: achPeng, target: tgtPeng },
      pelayanan: { achieved: achPel, target: tgtPel },
      pembelajaran: { achieved: achPemb, target: tgtPemb },
      lastActivity: lastActDate
    };
  },

  getDoctorActivities(docId) {
    const storageKey = `pogi_doctor_activities_${docId}`;
    if (typeof localStorage !== 'undefined') {
      const stored = localStorage.getItem(storageKey);
      if (stored) {
        try {
          const parsed = JSON.parse(stored);
          if (Array.isArray(parsed) && parsed.length > 0) {
            // Strict deduplication by event ID to prevent duplicate table rows
            const seen = new Set();
            return parsed.filter(act => {
              const k = act.eventId || act.id;
              if (seen.has(k)) return false;
              seen.add(k);
              return true;
            });
          }
        } catch (e) {}
      }
    }
    const doc = this.getDoctors().find(d => d.id === docId) || this.getDoctors()[0];
    const isComp = (doc.status === 'Complete');
    const hospital = doc.hospital || doc.institution || 'RS Jejaring POGI';
    const province = doc.province || 'DKI Jakarta';
    const pending = doc.pendingCredits || 0;
    const idx = parseInt((doc.id || '').replace('doc-', ''), 10) || 1;

    // Relasi Langsung ke Master Events POGI (Katalog Master SKP Events)
    const allEvents = (typeof this.getEvents === 'function') ? this.getEvents() : INITIAL_EVENTS;
    const pelEvents = allEvents.filter(e => e.type === 'Pelayanan');
    const pembEvents = allEvents.filter(e => e.type === 'Pembelajaran');
    const pengEvents = allEvents.filter(e => e.type === 'Pengabdian');

    const activities = [];

    // 1. Kegiatan Pelayanan Utama (ev-05: Praktik Klinis Poliklinik Triwulan IV)
    // Pending jika dokter belum disahkan, Verified jika sudah Complete
    const pendingEv = pelEvents[idx % (pelEvents.length || 1)] || { id: 'ev-05', name: 'Praktik Klinis Poliklinik Triwulan IV', date: '14 Nov 2026', type: 'Pelayanan', credits: 5.0 };
    const isPending = (pending > 0 && !isComp);
    activities.push({
      id: `act-${doc.id}-pelayanan-utama`,
      eventId: pendingEv.id,
      date: pendingEv.date || '14 Nov 2026',
      title: pendingEv.name,
      category: pendingEv.type || 'Pelayanan',
      credits: isPending ? `+${pending.toFixed(1)} SKP` : `+${(pendingEv.credits || 5.0).toFixed(1)} SKP`,
      organizer: `${hospital} • Kemenkes Plataran Sehat`,
      status: isPending ? 'PENDING' : 'VERIFIED',
      statusLabel: isPending ? '⏳ Menunggu Verifikasi Kemenkes' : '✓ Terverifikasi Kemenkes',
      statusClass: isPending ? 'bg-amber-50 text-amber-800 border-amber-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200'
    });

    // 2. Kegiatan Pelayanan Sekunder (ev-07: Tindakan Medis Terjadwal Sectio Caesarea)
    if (pelEvents.length > 0) {
      const evPel = pelEvents[(idx + 1) % pelEvents.length];
      if (evPel && evPel.id !== pendingEv.id) {
        const pelCredits = doc.q4Details ? (doc.q4Details.pelayanan?.achieved || evPel.credits || 2.0) : (isComp ? 4.0 : 2.0);
        activities.push({
          id: `act-${doc.id}-pelayanan-tindakan`,
          eventId: evPel.id,
          date: evPel.date || '7 Nov 2026',
          title: evPel.name,
          category: 'Pelayanan',
          credits: `+${Math.max(1.0, pelCredits).toFixed(1)} SKP`,
          organizer: hospital,
          status: 'VERIFIED',
          statusLabel: '✓ Terverifikasi Kemenkes',
          statusClass: 'bg-emerald-50 text-emerald-700 border border-emerald-200'
        });
      }
    }

    // 3. Kegiatan Pembelajaran (ev-06: Webinar Vaksinasi Dewasa & Imunisasi Kehamilan)
    if (pembEvents.length > 0) {
      const evPemb = pembEvents[(idx + 1) % pembEvents.length];
      const pembCredits = doc.q4Details ? (doc.q4Details.pembelajaran?.achieved || evPemb.credits || 1.5) : (isComp ? 2.5 : 1.5);
      activities.push({
        id: `act-${doc.id}-pembelajaran`,
        eventId: evPemb.id,
        date: evPemb.date || '8 Nov 2026',
        title: evPemb.name,
        category: 'Pembelajaran',
        credits: `+${Math.max(1.0, pembCredits).toFixed(1)} SKP`,
        organizer: 'Kolegium Obstetri dan Ginekologi • PP POGI',
        status: 'VERIFIED',
        statusLabel: '✓ Terverifikasi Kemenkes',
        statusClass: 'bg-emerald-50 text-emerald-700 border border-emerald-200'
      });
    }

    // 4. Kegiatan Pengabdian (ev-02: Bakti Sosial Khitanan Massal & Skrining Serviks)
    if (pengEvents.length > 0) {
      const evPeng = pengEvents[(idx + 2) % pengEvents.length];
      const pengCredits = doc.q4Details ? (doc.q4Details.pengabdian?.achieved || evPeng.credits || 0.5) : (isComp ? 1.5 : 0.5);
      activities.push({
        id: `act-${doc.id}-pengabdian`,
        eventId: evPeng.id,
        date: evPeng.date || '12 Des 2026',
        title: evPeng.name,
        category: 'Pengabdian',
        credits: `+${Math.max(0.5, pengCredits).toFixed(1)} SKP`,
        organizer: `POGI Cabang ${province}`,
        status: 'VERIFIED',
        statusLabel: '✓ Terverifikasi Kemenkes',
        statusClass: 'bg-emerald-50 text-emerald-700 border border-emerald-200'
      });
    }

    // Filter akhir memastikan 100% ID Event unik
    const finalSeen = new Set();
    return activities.filter(a => {
      if (finalSeen.has(a.eventId)) return false;
      finalSeen.add(a.eventId);
      return true;
    });
  },
  getDoctorTimestamps(docId) {
    const doc = this.getDoctors().find(d => d.id === docId) || this.getDoctors()[0];
    const idx = parseInt((doc.id || '').replace('doc-', ''), 10) || 1;
    const hour = (8 + (idx % 9)).toString().padStart(2, '0');
    const min1 = ((idx * 13) % 60).toString().padStart(2, '0');
    const min2 = ((idx * 17) % 60).toString().padStart(2, '0');
    return {
      dashboard_updated_at: `17 Nov 2026, ${hour}.${min1} WIB`,
      kemenkes_updated_at: `17 Nov 2026, ${hour}.${min2} WIB`,
      syncMode: 'Manual-Assisted Sync (Non-API)',
      vaultPath: `cloud-storage://pogi-skp-vault/${doc.id || 'doc-001'}/`,
      isEncrypted: true
    };
  },
  getDoctorDocuments(docId) {
    const storageKey = `pogi_doctor_docs_${docId}`;
    let list = null;
    if (typeof localStorage !== 'undefined') {
      const stored = localStorage.getItem(storageKey);
      if (stored) {
        try { list = JSON.parse(stored); } catch (e) {}
      }
    } else if (this._memoryDocsCache && this._memoryDocsCache[docId]) {
      list = this._memoryDocsCache[docId];
    }

    if (!list) {
      const doc = this.getDoctors().find(d => d.id === docId) || this.getDoctors()[0];
      const isComp = (doc.status === 'Complete');
      const cleanName = (doc.name || 'Dokter').replace(/[^a-zA-Z]/g, '');

      list = [
        {
          id: `file-${docId}-01`,
          filename: `STR_${cleanName}_2024-2029.pdf`,
          category: 'Administrasi',
          skpValue: 0,
          driveUrl: 'https://drive.google.com/drive/folders/1icJwep8Hiu2H5nSNnSsX3FlnVYOMikg8?usp=sharing',
          size: '1.8 MB',
          uploadedInternal: '10 Okt 2026, 09.30 WIB',
          uploadedKemenkes: '10 Okt 2026, 11.15 WIB',
          status: 'DONE',
          statusLabel: 'Terverifikasi Kemenkes',
          url: 'assets/docs/sample_str.pdf'
        },
        {
          id: `file-${docId}-02`,
          filename: `SIP_${(doc.hospital || 'RS').replace(/[^a-zA-Z]/g, '').slice(0, 15)}_Aktif.pdf`,
          category: 'Administrasi',
          skpValue: 0,
          driveUrl: 'https://drive.google.com/drive/folders/1icJwep8Hiu2H5nSNnSsX3FlnVYOMikg8?usp=sharing',
          size: '1.2 MB',
          uploadedInternal: '12 Okt 2026, 14.20 WIB',
          uploadedKemenkes: '12 Okt 2026, 15.00 WIB',
          status: 'DONE',
          statusLabel: 'Terverifikasi Kemenkes',
          url: 'assets/docs/sample_sip.pdf'
        },
        {
          id: `file-${docId}-03`,
          filename: `Sertifikat_Workshop_HandsOn_USG_Fetomaternal.pdf`,
          category: 'Pembelajaran',
          skpValue: 4.5,
          driveUrl: 'https://drive.google.com/drive/folders/1icJwep8Hiu2H5nSNnSsX3FlnVYOMikg8?usp=sharing',
          size: '2.5 MB',
          uploadedInternal: '05 Nov 2026, 10.15 WIB',
          uploadedKemenkes: '05 Nov 2026, 11.45 WIB',
          status: 'DONE',
          statusLabel: 'Terverifikasi Kemenkes',
          url: 'assets/docs/sample_cert.pdf'
        },
        {
          id: `file-${docId}-04`,
          filename: `SuratTugas_Pelayanan_PONEK_BedahSC_Cito.pdf`,
          category: 'Pelayanan',
          skpValue: 2.0,
          driveUrl: 'https://drive.google.com/drive/folders/1icJwep8Hiu2H5nSNnSsX3FlnVYOMikg8?usp=sharing',
          size: '1.4 MB',
          uploadedInternal: '14 Nov 2026, 13.00 WIB',
          uploadedKemenkes: '14 Nov 2026, 16.30 WIB',
          status: 'DONE',
          statusLabel: 'Terverifikasi Kemenkes',
          url: 'assets/docs/sample_surattugas.pdf'
        },
        {
          id: `file-${docId}-05`,
          filename: `Laporan_Baksos_Skrining_Kanker_Serviks.pdf`,
          category: 'Pengabdian',
          skpValue: 1.5,
          driveUrl: 'https://drive.google.com/drive/folders/1icJwep8Hiu2H5nSNnSsX3FlnVYOMikg8?usp=sharing',
          size: '3.1 MB',
          uploadedInternal: '16 Nov 2026, 09.45 WIB',
          uploadedKemenkes: isComp ? '16 Nov 2026, 14.10 WIB' : '—',
          status: isComp ? 'DONE' : 'SUBMITTED',
          statusLabel: isComp ? 'Terverifikasi Kemenkes' : 'Menunggu Input Kemenkes',
          url: 'assets/docs/sample_baksos.pdf'
        }
      ];

      if (!isComp) {
        list.push({
          id: `file-${docId}-06`,
          filename: `Logbook_Tindakan_Klinis_Q4_Draft.pdf`,
          category: 'Pelayanan',
          skpValue: 1.0,
          driveUrl: '',
          size: '0.8 MB',
          uploadedInternal: '17 Nov 2026, 08.20 WIB',
          uploadedKemenkes: '—',
          status: 'NOT_SUBMITTED',
          statusLabel: 'Tersimpan di Cloud (Belum Input Kemenkes)',
          url: 'assets/docs/sample_draft.pdf'
        });
      }
    }

    // Sanitize any existing relative time strings in cached list to clean explicit dates & hydrate skpValue/driveUrl
    const curDate = this.formatCurrentDate();
    const defaultDriveUrl = 'https://drive.google.com/drive/folders/1icJwep8Hiu2H5nSNnSsX3FlnVYOMikg8?usp=sharing';
    list.forEach((f, idx) => {
      if (typeof f.skpValue === 'undefined' || f.skpValue === null) {
        if (f.category === 'Pembelajaran') f.skpValue = 4.5;
        else if (f.category === 'Pelayanan') f.skpValue = 2.0;
        else if (f.category === 'Pengabdian') f.skpValue = 1.5;
        else f.skpValue = 0;
      }
      if (typeof f.driveUrl === 'undefined' || f.driveUrl === null) {
        f.driveUrl = idx < 5 ? defaultDriveUrl : '';
      }
      if (f.uploadedKemenkes && /hari\s*ini/i.test(f.uploadedKemenkes)) {
        f.uploadedKemenkes = f.uploadedKemenkes.replace(/hari\s*ini/gi, curDate);
      }
      if (f.uploadedInternal && /hari\s*ini/i.test(f.uploadedInternal)) {
        f.uploadedInternal = f.uploadedInternal.replace(/hari\s*ini/gi, curDate);
      }
      if (f.uploadedInternal === 'Baru saja') {
        f.uploadedInternal = this.formatCurrentTimestamp();
      }
      if (f.uploadedKemenkes === 'Baru saja') {
        f.uploadedKemenkes = this.formatCurrentTimestamp();
      }
    });

    if (typeof localStorage !== 'undefined') {
      localStorage.setItem(storageKey, JSON.stringify(list));
    } else {
      if (!this._memoryDocsCache) this._memoryDocsCache = {};
      this._memoryDocsCache[docId] = list;
    }
    return list;
  },
  getDoctorMissingDocuments(docId) {
    const doc = this.getDoctors().find(d => d.id === docId) || this.getDoctors()[0];
    const isComp = (doc.status === 'Complete');
    const docs = this.getDoctorDocuments(docId);

    const missing = [];
    docs.forEach(f => {
      if (f.status === 'NOT_SUBMITTED') {
        missing.push({
          code: 'DOC-REQ-01',
          name: f.filename,
          domain: f.category,
          status: 'NOT_SUBMITTED',
          statusBadgeClass: 'bg-amber-50 text-amber-800 border-amber-200',
          reason: 'Berkas sudah disimpan di Object Storage internal tetapi belum diinput manual ke portal Kemenkes oleh Admin.'
        });
      } else if (f.status === 'REJECTED') {
        missing.push({
          code: 'DOC-REQ-02',
          name: f.filename,
          domain: f.category,
          status: 'REJECTED',
          statusBadgeClass: 'bg-rose-50 text-rose-700 border-rose-200',
          reason: 'Ditolak verifikator Kemenkes: cap basah faskes/barcode sertifikat tidak terbaca, perlu unggah ulang.'
        });
      }
    });

    if (!isComp && missing.length === 0) {
      missing.push({
        code: 'DOC-REQ-03',
        name: 'Bukti Tambahan Kredit Ranah Pembelajaran (Min. 3.6 SKP)',
        domain: 'Pembelajaran',
        status: 'NOT_SUBMITTED',
        statusBadgeClass: 'bg-rose-50 text-rose-700 border-rose-200',
        reason: 'Dokumen bukti seminar/workshop belum diunggah oleh dokter/pengurus cabang.'
      });
    }

    return {
      hasMissing: missing.length > 0,
      missingCount: missing.length,
      items: missing,
      summary: missing.length > 0
        ? `Sistem Missing Document Detector menemukan ${missing.length} berkas yang belum lengkap / belum disetujui Kemenkes.`
        : 'Seluruh berkas bukti SKP dokter lengkap dan telah terverifikasi Kemenkes.'
    };
  },
  addDoctorDocument(docId, newDoc) {
    const list = this.getDoctorDocuments(docId);
    const skpNum = (typeof newDoc.skpValue !== 'undefined' && newDoc.skpValue !== null && newDoc.skpValue !== '') ? Number(newDoc.skpValue) : 0;
    const driveLink = newDoc.driveUrl ? String(newDoc.driveUrl).trim() : '';

    list.unshift({
      id: `file-${docId}-${Date.now()}`,
      filename: newDoc.filename || 'Dokumen_SKP_Baru.pdf',
      category: newDoc.category || 'Pembelajaran',
      skpValue: skpNum,
      driveUrl: driveLink,
      size: newDoc.size || (driveLink ? 'Cloud (Drive)' : '1.5 MB'),
      uploadedInternal: this.formatCurrentTimestamp(),
      uploadedKemenkes: '—',
      status: 'NOT_SUBMITTED',
      statusLabel: 'Tersimpan di Cloud (Siap Diunggah ke Kemenkes)',
      url: driveLink || '#'
    });
    if (typeof localStorage !== 'undefined') {
      localStorage.setItem(`pogi_doctor_docs_${docId}`, JSON.stringify(list));
    }
    this.addActivity({
      actor: 'Admin Energi Cipta',
      action: 'Unggah Berkas ke Object Storage',
      details: `Menyimpan ${newDoc.filename} (${skpNum > 0 ? '+' + skpNum + ' SKP' : 'Tanpa SKP'}${driveLink ? ', Tautan Google Drive' : ''}) untuk dokter ID: ${docId}`,
      category: 'Verification'
    });
    return list;
  },
  updateDoctorDocumentStatus(docId, fileId, newStatus) {
    const list = this.getDoctorDocuments(docId);
    const target = list.find(f => f.id === fileId);
    if (target) {
      target.status = newStatus;
      const fullTimestamp = this.formatCurrentTimestamp();
      if (newStatus === 'DONE') {
        target.statusLabel = 'Terverifikasi Kemenkes';
        target.uploadedKemenkes = fullTimestamp;
      } else if (newStatus === 'SUBMITTED') {
        target.statusLabel = 'Menunggu Input Kemenkes';
        target.uploadedKemenkes = fullTimestamp;
      } else if (newStatus === 'REJECTED') {
        target.statusLabel = 'Ditolak Kemenkes (Perlu Perbaikan)';
      } else {
        target.statusLabel = 'Tersimpan di Cloud (Belum Input Kemenkes)';
        target.uploadedKemenkes = '—';
      }
      if (typeof localStorage !== 'undefined') {
        localStorage.setItem(`pogi_doctor_docs_${docId}`, JSON.stringify(list));
      } else {
        if (!this._memoryDocsCache) this._memoryDocsCache = {};
        this._memoryDocsCache[docId] = list;
      }
      this.addActivity({
        actor: 'Admin Energi Cipta',
        action: 'Pembaruan Status Siklus Kemenkes',
        details: `Mengubah status ${target.filename} menjadi ${newStatus} (${docId})`,
        category: 'Verification'
      });
    }
    return list;
  },

  getProvinces() {
    if (typeof localStorage !== 'undefined') {
      const data = localStorage.getItem('pogi_provinces');
      if (data) {
        try {
          const parsed = JSON.parse(data);
          const total = parsed.reduce((sum, p) => sum + (p.doctors || 0), 0);
          if (total === 200) return parsed;
        } catch (e) {}
      }
      localStorage.setItem('pogi_provinces', JSON.stringify(INITIAL_PROVINCES));
    }
    return INITIAL_PROVINCES;
  },

  saveProvinces(provinces) {
    if (typeof localStorage !== 'undefined') {
      localStorage.setItem('pogi_provinces', JSON.stringify(provinces));
    }
  },

  getInstitutions() {
    if (typeof localStorage !== 'undefined') {
      const data = localStorage.getItem('pogi_institutions');
      return data ? JSON.parse(data) : INITIAL_INSTITUTIONS;
    }
    return INITIAL_INSTITUTIONS;
  },

  saveInstitutions(list) {
    if (typeof localStorage !== 'undefined') {
      localStorage.setItem('pogi_institutions', JSON.stringify(list));
    }
  },

  getEvents() {
    let raw = INITIAL_EVENTS;
    if (typeof localStorage !== 'undefined') {
      const data = localStorage.getItem('pogi_events');
      if (data) {
        try { raw = JSON.parse(data); } catch(e) {}
      }
    }
    return raw.map(e => {
      let typeBadge = 'bg-sky-100 text-sky-800 border-sky-200';
      if (e.type === 'Pembelajaran') typeBadge = 'bg-emerald-100 text-emerald-800 border-emerald-200';
      if (e.type === 'Pengabdian') typeBadge = 'bg-amber-100 text-amber-800 border-amber-200';

      const title = e.title || e.name || 'Kegiatan SKP POGI';
      const name = e.name || e.title || 'Kegiatan SKP POGI';
      const venue = e.venue || e.category || 'PP POGI / Daring';
      const category = e.category || e.venue || 'Pelatihan / Ilmiah';

      let doctorsCompleted = e.doctorsCompleted;
      if (doctorsCompleted === undefined && e.doctors) {
        const parts = e.doctors.split('/');
        doctorsCompleted = parseInt(parts[0].trim()) || 0;
      }
      if (doctorsCompleted === undefined) doctorsCompleted = 0;

      const doctors = e.doctors || `${doctorsCompleted} / 100 done`;

      return {
        ...e,
        title,
        name,
        venue,
        category,
        typeBadge: e.typeBadge || typeBadge,
        doctorsCompleted,
        doctors,
        creditsDisplay: e.creditsDisplay || (e.credits ? e.credits.toString().replace('.', ',') : '1')
      };
    });
  },

  saveEvents(list) {
    if (typeof localStorage !== 'undefined') {
      localStorage.setItem('pogi_events', JSON.stringify(list));
    }
  },

  getCategories() {
    if (typeof localStorage !== 'undefined') {
      const data = localStorage.getItem('pogi_categories');
      return data ? JSON.parse(data) : INITIAL_CATEGORIES;
    }
    return INITIAL_CATEGORIES;
  },

  saveCategories(list) {
    if (typeof localStorage !== 'undefined') {
      localStorage.setItem('pogi_categories', JSON.stringify(list));
    }
  },

  getUsers() {
    if (typeof localStorage !== 'undefined') {
      const data = localStorage.getItem('pogi_users');
      return data ? JSON.parse(data) : INITIAL_USERS;
    }
    return INITIAL_USERS;
  },

  saveUsers(list) {
    if (typeof localStorage !== 'undefined') {
      localStorage.setItem('pogi_users', JSON.stringify(list));
    }
  },

  getActivities() {
    if (typeof localStorage !== 'undefined') {
      const data = localStorage.getItem('pogi_activities');
      return data ? JSON.parse(data) : INITIAL_ACTIVITIES;
    }
    return INITIAL_ACTIVITIES;
  },

  addActivity(item) {
    const list = this.getActivities();
    list.unshift({
      id: 'act-' + Date.now(),
      actor: item.actor || 'Admin Pengelola Data',
      action: item.action,
      details: item.details,
      timestamp: this.formatCurrentTimestamp ? this.formatCurrentTimestamp() : 'Baru saja',
      category: item.category || 'General'
    });
    if (typeof localStorage !== 'undefined') {
      localStorage.setItem('pogi_activities', JSON.stringify(list.slice(0, 50)));
    }
  },

  formatCurrentDate() {
    const now = new Date();
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    const day = now.getDate().toString().padStart(2, '0');
    const month = months[now.getMonth()];
    const year = now.getFullYear() >= 2026 ? now.getFullYear() : 2026;
    return `${day} ${month} ${year}`;
  },

  formatCurrentTimestamp() {
    const now = new Date();
    const dateStr = this.formatCurrentDate();
    const hours = now.getHours().toString().padStart(2, '0');
    const minutes = now.getMinutes().toString().padStart(2, '0');
    return `${dateStr}, ${hours}.${minutes} WIB`;
  },

  getSummaryMetrics() {
    const docs = this.getDoctors();
    const totalDoctors = 200;
    return {
      totalDoctors,
      complete: 42,
      incomplete: 158,
      avgProgress: 43,
      currentPeriod: 'Q4 2026'
    };
  },

  // -------------------------------------------------------------
  // FITUR: ADMIN PERFORMANCE & WORKLOAD SLA TRACKER
  // -------------------------------------------------------------
  getAdminPerformanceMetrics() {
    const doctors = this.getDoctors();
    const adminRoster = [
      { id: 'adm-01', name: 'Siti Rahma', displayName: 'Siti Rahma, S.Farm', email: 'sekretariat.pusat@pogi.or.id', role: 'Lead Verifikator Tim Data Pusat', avatar: 'SR', avatarBg: 'bg-[#006948] text-white', avgSla: 9.4, approvalRate: 99.4, rejections: 1, waBlast: 48 },
      { id: 'adm-02', name: 'Dewi Lestari', displayName: 'Dewi Lestari', email: 'dewi.lestari@pogi.or.id', role: 'Senior Admin Data Wilayah Barat', avatar: 'DL', avatarBg: 'bg-emerald-600 text-white', avgSla: 12.1, approvalRate: 98.8, rejections: 2, waBlast: 41 },
      { id: 'adm-03', name: 'Wahyu Setiawan', displayName: 'Wahyu Setiawan', email: 'wahyu.setiawan@pogi.or.id', role: 'Admin Sinkronisasi Kemenkes', avatar: 'WS', avatarBg: 'bg-teal-600 text-white', avgSla: 14.5, approvalRate: 98.2, rejections: 3, waBlast: 36 },
      { id: 'adm-04', name: 'Melati Sihombing', displayName: 'Melati Sihombing', email: 'melati.s@pogi.or.id', role: 'Verifikator Berkas Wilayah Tengah', avatar: 'MS', avatarBg: 'bg-sky-700 text-white', avgSla: 17.8, approvalRate: 97.5, rejections: 4, waBlast: 31 },
      { id: 'adm-05', name: 'Rina Kartika', displayName: 'Rina Kartika', email: 'rina.kartika@pogi.or.id', role: 'Admin Koordinasi Cabang Daerah', avatar: 'RK', avatarBg: 'bg-indigo-600 text-white', avgSla: 20.4, approvalRate: 96.9, rejections: 5, waBlast: 25 },
      { id: 'adm-06', name: 'Dimas Saputra', displayName: 'Dimas Saputra', email: 'dimas.saputra@pogi.or.id', role: 'Asisten Verifikator & Helpdesk LMS', avatar: 'DS', avatarBg: 'bg-slate-600 text-white', avgSla: 15.2, approvalRate: 98.5, rejections: 2, waBlast: 18 },
      { id: 'adm-07', name: 'Fikri Ramadhan', displayName: 'Fikri Ramadhan', email: 'fikri.ramadhan@pogi.or.id', role: 'Staf Operasional Berkas Cloud', avatar: 'FR', avatarBg: 'bg-amber-700 text-white', avgSla: 16.0, approvalRate: 97.8, rejections: 3, waBlast: 15 }
    ];

    const result = adminRoster.map(admin => {
      const assigned = doctors.filter(d => (d.assignedTo && d.assignedTo.toLowerCase().includes(admin.name.toLowerCase())) || (admin.name === 'Siti Rahma' && d.assignedTo === 'Siti Rahma'));
      const complete = assigned.filter(d => d.status === 'Complete').length;
      const incomplete = assigned.length - complete;
      const totalQ4 = assigned.reduce((acc, d) => acc + (d.q4Credits || 0), 0);
      const avgQ4 = assigned.length > 0 ? (totalQ4 / assigned.length).toFixed(1) : '0';
      const complianceRate = assigned.length > 0 ? Math.round((complete / assigned.length) * 100) : 0;
      
      const slaScore = Math.max(0, Math.min(100, Math.round(100 - (admin.avgSla / 24 * 30))));
      const accuracyScore = Math.round(admin.approvalRate);
      const volumeScore = Math.min(100, Math.round((assigned.length / 40) * 100));
      const completionScore = complianceRate;

      const overallScore = Math.round((slaScore * 0.35) + (accuracyScore * 0.30) + (volumeScore * 0.20) + (completionScore * 0.15));

      let tier = 'Good Performer ✅';
      let tierBadgeClass = 'bg-sky-50 text-sky-800 border-sky-200';
      if (overallScore >= 90) {
        tier = 'Top Performer 🌟';
        tierBadgeClass = 'bg-emerald-50 text-emerald-800 border-emerald-200';
      } else if (overallScore < 75) {
        tier = 'Needs Attention ⚠️';
        tierBadgeClass = 'bg-amber-50 text-amber-800 border-amber-200';
      }

      let workloadStatus = 'Optimal';
      let workloadClass = 'text-emerald-700 bg-emerald-50 border-emerald-200';
      if (assigned.length >= 42) {
        workloadStatus = 'High Workload';
        workloadClass = 'text-amber-800 bg-amber-50 border-amber-200';
      } else if (assigned.length === 0) {
        workloadStatus = 'Standby';
        workloadClass = 'text-slate-600 bg-slate-100 border-slate-200';
      }

      const processedDocsCount = (assigned.length * 6) + (admin.waBlast * 2);

      return {
        ...admin,
        doctorCount: assigned.length,
        completeCount: complete,
        incompleteCount: incomplete,
        complianceRate,
        totalQ4Credits: Math.round(totalQ4 * 10) / 10,
        avgQ4Credits: avgQ4,
        processedDocs: processedDocsCount,
        overallScore,
        tier,
        tierBadgeClass,
        workloadStatus,
        workloadClass
      };
    });

    result.sort((a, b) => b.overallScore - a.overallScore);

    const totalAssignedDocs = result.reduce((sum, a) => sum + a.doctorCount, 0);
    const totalProcessedDocs = result.reduce((sum, a) => sum + a.processedDocs, 0);
    const avgTeamSla = (result.reduce((sum, a) => sum + a.avgSla, 0) / result.length).toFixed(1);
    const avgTeamApproval = (result.reduce((sum, a) => sum + a.approvalRate, 0) / result.length).toFixed(1);
    const totalCompleteDocs = result.reduce((sum, a) => sum + a.completeCount, 0);
    const teamComplianceRate = totalAssignedDocs > 0 ? Math.round((totalCompleteDocs / totalAssignedDocs) * 100) : 0;

    return {
      admins: result,
      summary: {
        totalAdmins: result.length,
        totalAssignedDocs,
        totalProcessedDocs,
        avgTeamSla,
        avgTeamApproval,
        totalCompleteDocs,
        teamComplianceRate,
        slaTargetHours: 24
      }
    };
  },

  reassignDoctor(docId, newAdminName) {
    const doctors = this.getDoctors();
    const doc = doctors.find(d => d.id === docId);
    if (!doc) return false;
    const oldAdmin = doc.assignedTo || 'Unassigned';
    doc.assignedTo = newAdminName;
    if (typeof localStorage !== 'undefined') {
      localStorage.setItem('pogi_doctors', JSON.stringify(doctors));
    }
    this.addActivity({
      actor: 'Admin Data Pusat',
      action: 'Redistribusi Dokter Binaan',
      details: `Mengalihkan ${doc.name} (#${doc.pogiId || doc.id}) dari ${oldAdmin} ke ${newAdminName}`,
      category: 'Workload'
    });
    return true;
  },

  // -------------------------------------------------------------
  // BAB 6.2 — RFC 9562 UUIDv7 GENERATOR & VALIDASI SKEMA KETAT
  // -------------------------------------------------------------
  generateUUIDv7() {
    const now = Date.now(); // 48-bit timestamp
    const timeHex = now.toString(16).padStart(12, '0');
    // 12-bit pseudo-random sequence
    const randSeq = Math.floor(Math.random() * 0x1000).toString(16).padStart(3, '0');
    // 2-bit variant (10xx) + 14-bit random
    const randVar = ((Math.floor(Math.random() * 0x4000) & 0x3fff) | 0x8000).toString(16).padStart(4, '0');
    // 48-bit random
    const randNode = Array.from({ length: 6 }, () => Math.floor(Math.random() * 256).toString(16).padStart(2, '0')).join('');
    return `${timeHex.slice(0, 8)}-${timeHex.slice(8, 12)}-7${randSeq}-${randVar}-${randNode}`;
  },

  validateDoctor(data) {
    const errors = [];
    if (!data.name || typeof data.name !== 'string' || data.name.trim().length < 3) {
      errors.push('Nama dokter wajib diisi dan minimal 3 karakter.');
    }
    if (!data.nik || !/^\d{16}$/.test(String(data.nik).trim())) {
      errors.push('NIK wajib 16 digit angka sesuai standar kependudukan.');
    }
    if (!data.province || data.province === 'ALL') {
      errors.push('Cabang POGI / Provinsi wajib ditentukan.');
    }
    return {
      isValid: errors.length === 0,
      errors
    };
  },

  validateDocument(data) {
    const errors = [];
    if (!data.filename || !data.filename.toLowerCase().endsWith('.pdf')) {
      errors.push('Format berkas bukti wajib berekstensi PDF (.pdf).');
    }
    if (data.sizeBytes && data.sizeBytes > 10 * 1024 * 1024) {
      errors.push('Ukuran berkas tidak boleh melebihi 10 MB.');
    }
    return {
      isValid: errors.length === 0,
      errors
    };
  },

  // -------------------------------------------------------------
  // BAB 6.1 & 6.2 — ROLE-BASED ACCESS CONTROL (RBAC) 4 TINGKAT
  // -------------------------------------------------------------
  getCurrentUser() {
    if (this._currentUser) return this._currentUser;
    if (typeof localStorage !== 'undefined') {
      const saved = localStorage.getItem('pogi_current_user');
      if (saved) {
        try {
          this._currentUser = JSON.parse(saved);
          return this._currentUser;
        } catch (e) {}
      }
    }
    return {
      id: 'usr-super',
      name: 'Super Admin',
      email: 'super@example.com',
      role: 'SUPER_ADMIN',
      roleLabel: 'Super Admin (Energi Cipta)',
      branch: 'Nasional',
      canWrite: true
    };
  },

  setCurrentUser(user) {
    this._currentUser = user;
    if (typeof localStorage !== 'undefined') {
      localStorage.setItem('pogi_current_user', JSON.stringify(user));
    }
    return user;
  },

  getAllRoles() {
    return [
      {
        role: 'SUPER_ADMIN',
        name: 'Super Admin',
        email: 'super@example.com',
        roleLabel: 'Super Admin (Energi Cipta)',
        branch: 'Nasional',
        badgeClass: 'bg-purple-100 text-purple-800 border-purple-200',
        description: 'Akses penuh nasional: kelola master pengguna, verifikasi, blast WA, & ekspor laporan'
      },
      {
        role: 'ADMIN_OPERASIONAL',
        name: 'Siti Rahma, S.Farm',
        email: 'siti.rahma@energicipta.id',
        roleLabel: 'Admin Operasional (Tim Data PP POGI)',
        branch: 'Nasional',
        badgeClass: 'bg-emerald-100 text-[#006948] border-emerald-200',
        description: 'Akses operasional: verifikasi kredit SKP, unggah repositori, & sinkronisasi Kemenkes'
      },
      {
        role: 'ADMIN_CABANG',
        name: 'dr. Firman (Admin Cabang Jabar)',
        email: 'admin.jabar@pogi.or.id',
        roleLabel: 'Admin Cabang (POGI Jawa Barat)',
        branch: 'Jawa Barat',
        badgeClass: 'bg-sky-100 text-sky-800 border-sky-200',
        description: 'Akses terisolasi: hanya dapat melihat & mengunggah berkas untuk wilayah Jawa Barat'
      },
      {
        role: 'VIEWER_POGI',
        name: 'Pengurus Pusat POGI',
        email: 'pengurus.pusat@pogi.or.id',
        roleLabel: 'Viewer POGI (Read-Only)',
        branch: 'Nasional',
        badgeClass: 'bg-amber-100 text-amber-800 border-amber-200',
        description: 'Akses pantau laporan & grafik eksekutif (seluruh tombol aksi mutasi dinonaktifkan)'
      }
    ];
  },

  canPerform(action) {
    const user = this.getCurrentUser();
    if (user.role === 'SUPER_ADMIN') return true;
    if (user.role === 'ADMIN_OPERASIONAL') {
      return action !== 'MANAGE_USERS';
    }
    if (user.role === 'ADMIN_CABANG') {
      return ['VIEW_DOCTORS', 'UPLOAD_DOC', 'VIEW_REPORTS'].includes(action);
    }
    if (user.role === 'VIEWER_POGI') {
      return ['VIEW_DASHBOARD', 'VIEW_DOCTORS', 'VIEW_REPORTS', 'EXPORT_REPORT'].includes(action);
    }
    return false;
  },

  getDoctorsForCurrentUser() {
    const user = this.getCurrentUser();
    const all = this.getDoctors();
    if (user.role === 'ADMIN_CABANG' && user.branch && user.branch !== 'Nasional') {
      return all.filter(d => d.province && d.province.toLowerCase() === user.branch.toLowerCase());
    }
    return all;
  },

  // -------------------------------------------------------------
  // BAB 3.2.A — MESIN OTOMASI PENGINGAT (CRON JOB SCHEDULER H-7 & H-2)
  // -------------------------------------------------------------
  getCronSchedulerStatus() {
    const history = (typeof localStorage !== 'undefined')
      ? JSON.parse(localStorage.getItem('pogi_cron_history') || '[]')
      : [];
    return {
      status: 'ACTIVE',
      frequency: 'H-7 & H-2 Sebelum Tanggal 1 Tiap Bulan (Cutoff 00:00 WIB)',
      nextH7: '24 Nov 2026, 00:00 WIB',
      nextH2: '29 Nov 2026, 00:00 WIB',
      cutoffDate: '01 Des 2026, 00:00 WIB',
      history: history.length > 0 ? history : [
        {
          id: 'cron-hist-01',
          trigger: 'H-7 Cutoff Bulanan',
          executedAt: '24 Okt 2026, 00:00 WIB',
          recipientsCount: 28,
          status: 'SUCCESS',
          summary: 'Notifikasi H-7 terkirim otomatis ke 28 dokter dengan status Incomplete'
        },
        {
          id: 'cron-hist-02',
          trigger: 'H-2 Cutoff Bulanan',
          executedAt: '29 Okt 2026, 00:00 WIB',
          recipientsCount: 14,
          status: 'SUCCESS',
          summary: 'Peringatan H-2 terkirim otomatis ke 14 dokter yang belum melengkapi berkas'
        }
      ]
    };
  },

  runCronTrigger(triggerType = 'H-7') {
    const docs = this.getDoctors();
    const incompleteDocs = docs.filter(d => d.status !== 'Complete');
    const count = incompleteDocs.length;
    const executedAt = this.formatCurrentTimestamp();
    const record = {
      id: 'cron-exec-' + Date.now(),
      trigger: `${triggerType} Cutoff Bulanan`,
      executedAt,
      recipientsCount: count,
      status: 'SUCCESS',
      summary: `Trigger otomasi ${triggerType} dieksekusi: ${count} dokter Incomplete telah dikirimkan notifikasi pengingat via WhatsApp Queue.`
    };

    if (typeof localStorage !== 'undefined') {
      const hist = JSON.parse(localStorage.getItem('pogi_cron_history') || '[]');
      hist.unshift(record);
      localStorage.setItem('pogi_cron_history', JSON.stringify(hist.slice(0, 20)));
    }

    this.addActivity({
      actor: 'Sistem Otomasi (Cron Scheduler)',
      action: `Eksekusi Trigger ${triggerType} Pengingat SKP`,
      details: `Memproses ${count} dokter status Incomplete menjelang cutoff bulanan 1 Des 2026. Antrean pengingat dikirim.`,
      category: 'Automation'
    });

    return record;
  },

  // -------------------------------------------------------------
  // BAB 3.2.B & 3.2.C — MESSAGE QUEUE, RATE LIMITING & JITTER ANTI-BAN
  // -------------------------------------------------------------
  getWaQueue() {
    if (typeof localStorage !== 'undefined') {
      return JSON.parse(localStorage.getItem('pogi_wa_queue') || '[]');
    }
    return [];
  },

  queueWaBlast(recipients, templateType, customNotes = '') {
    const queue = recipients.map((doc, idx) => ({
      queueId: 'wq-' + (Date.now() + idx),
      uuid: this.generateUUIDv7(),
      doctorId: doc.id,
      doctorName: doc.name,
      phone: doc.phone || ('0812' + Math.floor(10000000 + Math.random() * 90000000)),
      province: doc.province,
      status: 'QUEUED',
      skpTarget: doc.qTarget || 8.0,
      skpAchieved: doc.qAchieved || 0,
      template: templateType,
      jitterMs: 1500 + Math.floor(Math.random() * 1500)
    }));

    if (typeof localStorage !== 'undefined') {
      localStorage.setItem('pogi_wa_queue', JSON.stringify(queue));
    }

    this.addActivity({
      actor: this.getCurrentUser().name,
      action: 'Kirim WA Blast Massal (Message Queue)',
      details: `Menyiapkan antrean pengiriman WA Blast (${templateType}) untuk ${recipients.length} dokter terpilih dengan perlindungan anti-ban jitter.`,
      category: 'Automation'
    });

    return queue;
  },

  clearWaQueue() {
    if (typeof localStorage !== 'undefined') {
      localStorage.removeItem('pogi_wa_queue');
    }
  }
};

if (typeof window !== 'undefined') window.PogiStore = PogiStore;
if (typeof globalThis !== 'undefined') globalThis.PogiStore = PogiStore;
if (typeof module !== 'undefined' && module.exports) module.exports = { PogiStore, INITIAL_PROVINCES, INITIAL_DOCTORS };
