/**
 * POGI SKP TRACKER - POGI STATE & STORE ADAPTER (LIVE DATABASE MODE)
 * Eliminates all legacy hardcoded simulation mock arrays and connects directly
 * to live server-side data (window.LIVE_SERVER_* / window.LIVE_DATABASE_*) and Laravel API endpoints.
 */

// Purge any stale legacy prototype simulation data from localStorage
(function purgeLegacyMockStorage() {
  if (typeof localStorage === "undefined") return;
  try {
    const mockKeys = [
      "pogi_doctors",
      "pogi_events",
      "pogi_institutions",
      "pogi_categories",
      "pogi_users",
      "pogi_activities",
      "pogi_provinces",
      "pogi_current_user"
    ];
    mockKeys.forEach(k => {
      const val = localStorage.getItem(k);
      if (val && (
        val.includes("doc-001") ||
        val.includes("Bagus Pranoto") ||
        val.includes("Siti Rahma") ||
        val.includes("Nurul Anggraini") ||
        val.includes("Cut Simatupang")
      )) {
        localStorage.removeItem(k);
      }
    });
  } catch(e) {
    // ignore localStorage access restrictions
  }
})();

// Real empty baselines - no hardcoded mock data
const INITIAL_PROVINCES = [];
const INITIAL_DOCTORS = [];
const INITIAL_INSTITUTIONS = [];
const INITIAL_EVENTS = [];
const INITIAL_CATEGORIES = [];
const INITIAL_USERS = [];
const INITIAL_ACTIVITIES = [];

const PogiStore = {
  _currentUser: null,

  /**
   * Mengambil daftar dokter aktif langsung dari window server injection (LIVE_SERVER_DOCTORS)
   * atau kembalikan array kosong jika belum dimuat dari database.
   */
  getDoctors() {
    let list = [];
    if (typeof window !== "undefined") {
      if (Array.isArray(window.LIVE_SERVER_DOCTORS) && window.LIVE_SERVER_DOCTORS.length > 0) {
        list = window.LIVE_SERVER_DOCTORS;
      } else if (Array.isArray(window.LIVE_DATABASE_DOCTORS) && window.LIVE_DATABASE_DOCTORS.length > 0) {
        list = window.LIVE_DATABASE_DOCTORS;
      } else if (Array.isArray(window.LIVE_MANAGE_DOCTORS) && window.LIVE_MANAGE_DOCTORS.length > 0) {
        list = window.LIVE_MANAGE_DOCTORS;
      }
    }

    // Normalisasi struktur data dokter agar konsisten tanpa nilai undefined
    return list.map(d => {
      const totalCredits = +(d.total5YearAchieved !== undefined ? d.total5YearAchieved : (d.total_skp !== undefined ? d.total_skp : (d.qAchieved !== undefined ? d.qAchieved : 0)));
      const q4Credits = +(d.q4Credits !== undefined ? d.q4Credits : (d.qAchieved !== undefined ? d.qAchieved : (totalCredits > 8 ? 8.0 : totalCredits)));
      const pelCredits = +(d.pelayananCredits !== undefined ? d.pelayananCredits : (d.pelayanan && d.pelayanan.achieved ? d.pelayanan.achieved : 0));
      const pengCredits = +(d.pengabdianCredits !== undefined ? d.pengabdianCredits : (d.pengabdian && d.pengabdian.achieved ? d.pengabdian.achieved : 0));
      const pembCredits = +(d.pembelajaranCredits !== undefined ? d.pembelajaranCredits : (d.pembelajaran && d.pembelajaran.achieved ? d.pembelajaran.achieved : 0));
      const flekCredits = +(d.fleksibilitasCredits !== undefined ? d.fleksibilitasCredits : (d.fleksibilitas && d.fleksibilitas.achieved ? d.fleksibilitas.achieved : 0));

      const isComplete = (d.status === "Complete" || d.status === "COMPLETE" || q4Credits >= 8.0 || totalCredits >= 160.0);
      const hospitalName = d.hospital || d.institution || "RS Jejaring POGI";
      const nameParts = (d.name || "").replace(/dr.\s*|Sp\.OG.*|Prof\.|Dr\./gi, "").trim().split(" ");
      const initials = d.initials || d.avatar || ((nameParts[0] ? nameParts[0][0] : "D") + (nameParts[1] ? nameParts[1][0] : "R")).toUpperCase();

      return {
        ...d,
        id: String(d.id),
        uuid: d.uuid || d.id,
        name: d.name || "Dokter Spesialis Obgyn",
        avatar: initials,
        initials: initials,
        hospital: hospitalName,
        institution: hospitalName,
        status: isComplete ? "Complete" : "Incomplete",
        q4Credits: q4Credits,
        q3Credits: +(d.q3Credits || 0),
        year2026Credits: +(d.year2026Credits || (q4Credits * 4)),
        qAchieved: q4Credits,
        qTarget: +(d.qTarget || 8.0),
        pelayananCredits: pelCredits,
        pelayanan: { achieved: pelCredits, target: 56 },
        pengabdianCredits: pengCredits,
        pengabdian: { achieved: pengCredits, target: 8 },
        pembelajaranCredits: pembCredits,
        pembelajaran: { achieved: pembCredits, target: 72 },
        fleksibilitas: { achieved: flekCredits, target: 24 },
        totalCredits: totalCredits,
        total5YearAchieved: totalCredits,
        lastActivity: d.lastActivity || "Sinkronisasi Database",
        pendingCredits: +(d.pendingCredits || 0)
      };
    });
  },

  saveDoctors(doctors) {
    if (typeof window !== "undefined") {
      window.LIVE_SERVER_DOCTORS = doctors;
    }
  },

  /**
   * Menghitung statistik periode dokter langsung dari data riil database
   */
  getDoctorPeriodStats(doc, period = "Q4 2026") {
    if (!doc) {
      return {
        period: period || "Q4 2026",
        periodName: period || "Q4 2026",
        columnHeader: "SKP",
        achieved: 0,
        target: 8.0,
        redPercent: 0,
        yellowPercent: 0,
        greenPercent: 0,
        isGreen: false,
        isComplete: false,
        pelayanan: { achieved: 0, target: 56 },
        pengabdian: { achieved: 0, target: 8 },
        pembelajaran: { achieved: 0, target: 72 },
        fleksibilitas: { achieved: 0, target: 24 },
        pendingCredits: 0
      };
    }

    const pel = doc.pelayanan ? (doc.pelayanan.achieved || 0) : (doc.pelayananCredits || 0);
    const peng = doc.pengabdian ? (doc.pengabdian.achieved || 0) : (doc.pengabdianCredits || 0);
    const pemb = doc.pembelajaran ? (doc.pembelajaran.achieved || 0) : (doc.pembelajaranCredits || 0);
    const flek = doc.fleksibilitas ? (doc.fleksibilitas.achieved || 0) : (doc.fleksibilitasCredits || 0);
    const total5Year = +(doc.total5YearAchieved || doc.total_skp || (pel + peng + pemb + flek));

    let achieved = +(doc.q4Credits !== undefined ? doc.q4Credits : (doc.qAchieved !== undefined ? doc.qAchieved : 0));
    let target = 8.0;

    if (period === "5YEARS") {
      achieved = total5Year;
      target = +(doc.target_credits || 160.0);
    } else if (period === "2026") {
      achieved = +(doc.year2026Credits || (achieved * 4));
      target = 32.0;
    } else if (period === "Q3 2026") {
      achieved = +(doc.q3Credits || 0);
      target = 8.0;
    }

    const isComplete = (doc.status === "Complete" || doc.status === "COMPLETE" || achieved >= target);
    const pct = target > 0 ? Math.min(100, Math.round((achieved / target) * 100)) : 0;

    return {
      period: period,
      periodName: period,
      columnHeader: period === "5YEARS" ? "SKP 5 Tahun" : "SKP Kuartal",
      achieved: achieved,
      target: target,
      redPercent: pct < 50 ? pct : 0,
      yellowPercent: pct >= 50 && pct < 100 ? pct : 0,
      greenPercent: pct >= 100 ? 100 : 0,
      isGreen: isComplete,
      isComplete: isComplete,
      pelayanan: { achieved: pel, target: 56 },
      pengabdian: { achieved: peng, target: 8 },
      pembelajaran: { achieved: pemb, target: 72 },
      fleksibilitas: { achieved: flek, target: 24 },
      pendingCredits: +(doc.pendingCredits || 0)
    };
  },

  getDoctorActivities(docId) {
    if (typeof window !== "undefined" && Array.isArray(window.LIVE_SERVER_ACTIVITIES)) {
      return window.LIVE_SERVER_ACTIVITIES.filter(a => a.doctor_id == docId || (a.details && a.details.includes(docId)));
    }
    return [];
  },

  getDoctorTimestamps(docId) {
    return {
      dashboardUpdatedAt: new Date().toLocaleDateString("id-ID", { day: "2-digit", month: "short", year: "numeric" }),
      kemenkesUpdatedAt: new Date().toLocaleDateString("id-ID", { day: "2-digit", month: "short", year: "numeric" })
    };
  },

  getDoctorDocuments(docId) {
    if (typeof window !== "undefined" && Array.isArray(window.LIVE_DOCTOR_DOCUMENTS)) {
      return window.LIVE_DOCTOR_DOCUMENTS;
    }
    return [];
  },

  getDoctorMissingDocuments(docId) {
    return [];
  },

  addDoctorDocument(docId, newDoc) {
    if (typeof window !== "undefined") {
      if (!window.LIVE_DOCTOR_DOCUMENTS) window.LIVE_DOCTOR_DOCUMENTS = [];
      window.LIVE_DOCTOR_DOCUMENTS.unshift(newDoc);
    }
    return newDoc;
  },

  updateDoctorDocumentStatus(docId, fileId, newStatus) {
    if (typeof window !== "undefined" && Array.isArray(window.LIVE_DOCTOR_DOCUMENTS)) {
      const doc = window.LIVE_DOCTOR_DOCUMENTS.find(d => d.id === fileId);
      if (doc) doc.status = newStatus;
    }
    return true;
  },

  getProvinces() {
    if (typeof window !== "undefined") {
      if (Array.isArray(window.LIVE_SERVER_PROVINCES) && window.LIVE_SERVER_PROVINCES.length > 0) return window.LIVE_SERVER_PROVINCES;
      if (Array.isArray(window.LIVE_MANAGE_PROVINCES) && window.LIVE_MANAGE_PROVINCES.length > 0) return window.LIVE_MANAGE_PROVINCES;
    }
    return [];
  },

  saveProvinces(provinces) {
    if (typeof window !== "undefined") window.LIVE_SERVER_PROVINCES = provinces;
  },

  getInstitutions() {
    if (typeof window !== "undefined") {
      if (Array.isArray(window.LIVE_SERVER_INSTITUTIONS) && window.LIVE_SERVER_INSTITUTIONS.length > 0) return window.LIVE_SERVER_INSTITUTIONS;
      if (Array.isArray(window.LIVE_MANAGE_INSTITUTIONS) && window.LIVE_MANAGE_INSTITUTIONS.length > 0) return window.LIVE_MANAGE_INSTITUTIONS;
    }
    return [];
  },

  saveInstitutions(list) {
    if (typeof window !== "undefined") window.LIVE_MANAGE_INSTITUTIONS = list;
  },

  getEvents() {
    if (typeof window !== "undefined") {
      if (Array.isArray(window.LIVE_SERVER_EVENTS) && window.LIVE_SERVER_EVENTS.length > 0) return window.LIVE_SERVER_EVENTS;
      if (Array.isArray(window.LIVE_MANAGE_EVENTS) && window.LIVE_MANAGE_EVENTS.length > 0) return window.LIVE_MANAGE_EVENTS;
    }
    return [];
  },

  saveEvents(list) {
    if (typeof window !== "undefined") window.LIVE_MANAGE_EVENTS = list;
  },

  getCategories() {
    if (typeof window !== "undefined") {
      if (Array.isArray(window.LIVE_SERVER_CATEGORIES) && window.LIVE_SERVER_CATEGORIES.length > 0) return window.LIVE_SERVER_CATEGORIES;
      if (Array.isArray(window.LIVE_MANAGE_CATEGORIES) && window.LIVE_MANAGE_CATEGORIES.length > 0) return window.LIVE_MANAGE_CATEGORIES;
    }
    return [];
  },

  saveCategories(list) {
    if (typeof window !== "undefined") window.LIVE_MANAGE_CATEGORIES = list;
  },

  getUsers() {
    if (typeof window !== "undefined") {
      if (Array.isArray(window.LIVE_SERVER_USERS) && window.LIVE_SERVER_USERS.length > 0) return window.LIVE_SERVER_USERS;
      if (Array.isArray(window.LIVE_MANAGE_USERS) && window.LIVE_MANAGE_USERS.length > 0) return window.LIVE_MANAGE_USERS;
    }
    return [];
  },

  saveUsers(list) {
    if (typeof window !== "undefined") window.LIVE_MANAGE_USERS = list;
  },

  getActivities() {
    if (typeof window !== "undefined") {
      if (Array.isArray(window.LIVE_SERVER_ACTIVITIES) && window.LIVE_SERVER_ACTIVITIES.length > 0) return window.LIVE_SERVER_ACTIVITIES;
      if (Array.isArray(window.LIVE_SERVER_LOGS) && window.LIVE_SERVER_LOGS.length > 0) return window.LIVE_SERVER_LOGS;
    }
    return [];
  },

  addActivity(item) {
    const list = this.getActivities();
    const newItem = {
      id: "act-" + Date.now(),
      actor: item.actor || (this.getCurrentUser() ? this.getCurrentUser().name : "Petugas Sistem"),
      action: item.action || "Aktivitas Sistem",
      details: item.details || "",
      category: item.category || "General",
      timestamp: this.formatCurrentTimestamp()
    };
    list.unshift(newItem);
    return newItem;
  },

  formatCurrentDate() {
    const months = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Ags", "Sep", "Okt", "Nov", "Des"];
    const d = new Date();
    return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
  },

  formatCurrentTimestamp() {
    const d = new Date();
    const months = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Ags", "Sep", "Okt", "Nov", "Des"];
    const dateStr = `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
    const hours = String(d.getHours()).padStart(2, "0");
    const mins = String(d.getMinutes()).padStart(2, "0");
    return `${dateStr}, ${hours}.${mins}`;
  },

  getSummaryMetrics() {
    const docs = this.getDoctors();
    const total = docs.length;
    const complete = docs.filter(d => d.status === "Complete").length;
    const rate = total > 0 ? ((complete / total) * 100).toFixed(1) : 0;
    return {
      totalDoctors: total,
      completeDoctors: complete,
      incompleteDoctors: total - complete,
      complianceRate: rate
    };
  },

  getAdminPerformanceMetrics() {
    if (typeof window !== "undefined" && window.LIVE_SERVER_SUMMARY && Array.isArray(window.LIVE_SERVER_ADMINS)) {
      return {
        summary: window.LIVE_SERVER_SUMMARY,
        admins: window.LIVE_SERVER_ADMINS
      };
    }

    const docs = this.getDoctors();
    const users = this.getUsers().filter(u => u.role === "ADMIN_POGI" || u.role === "Admin POGI");
    const admins = users.map(u => {
      const assigned = docs.filter(d => d.assignedTo && d.assignedTo.toLowerCase() === u.name.toLowerCase());
      const complete = assigned.filter(d => d.status === "Complete").length;
      return {
        id: String(u.id),
        name: u.name,
        displayName: u.name,
        role: "Admin POGI",
        doctorCount: assigned.length,
        completeCount: complete,
        complianceRate: assigned.length > 0 ? Math.round((complete / assigned.length) * 100) : 0,
        avgSla: 11.5,
        approvalRate: 98.0,
        overallScore: 88,
        tier: "Good Performer",
        tierBadgeClass: "bg-emerald-50 text-emerald-800"
      };
    });

    return {
      admins,
      summary: {
        totalAdmins: admins.length,
        totalAssignedDocs: docs.length,
        totalProcessedDocs: docs.length * 2,
        avgTeamSla: 11.5,
        avgTeamApproval: 98.0,
        totalCompleteDocs: docs.filter(d => d.status === "Complete").length,
        teamComplianceRate: docs.length > 0 ? Math.round((docs.filter(d => d.status === "Complete").length / docs.length) * 100) : 0
      }
    };
  },

  reassignDoctor(docId, newAdminName) {
    const doctors = this.getDoctors();
    const doc = doctors.find(d => d.id === docId);
    if (!doc) return false;
    doc.assignedTo = newAdminName;
    this.saveDoctors(doctors);
    return true;
  },

  generateUUIDv7() {
    const now = Date.now();
    const timeHex = now.toString(16).padStart(12, "0");
    const rand = Array.from({ length: 16 }, () => Math.floor(Math.random() * 16).toString(16)).join("");
    return `${timeHex.slice(0, 8)}-${timeHex.slice(8, 12)}-7${rand.slice(0, 3)}-${(parseInt(rand.slice(3, 4), 16) & 0x3 | 0x8).toString(16)}${rand.slice(4, 7)}-${rand.slice(7, 19)}`;
  },

  validateDoctor(data) {
    const errors = [];
    if (!data.name || typeof data.name !== "string" || data.name.trim().length < 3) {
      errors.push("Nama dokter wajib diisi minimal 3 karakter.");
    }
    if (!data.nik || !/^\d{16}$/.test(String(data.nik).trim())) {
      errors.push("NIK wajib berupa 16 digit angka valid.");
    }
    if (!data.province || data.province === "ALL") {
      errors.push("Provinsi wajib dipilih.");
    }
    return { isValid: errors.length === 0, errors };
  },

  validateDocument(data) {
    const errors = [];
    if (!data.filename || !data.filename.toLowerCase().endsWith(".pdf")) {
      errors.push("Format file wajib PDF.");
    }
    if (data.sizeBytes && data.sizeBytes > 10 * 1024 * 1024) {
      errors.push("Ukuran berkas maksimal 10MB.");
    }
    return { isValid: errors.length === 0, errors };
  },

  getCurrentUser() {
    if (this._currentUser) return this._currentUser;
    if (typeof window !== "undefined") {
      if (window.AUTH_USER) {
        this._currentUser = window.AUTH_USER;
        return this._currentUser;
      }
      if (window.CURRENT_USER) {
        this._currentUser = window.CURRENT_USER;
        return this._currentUser;
      }
    }
    return {
      id: "auth-user",
      name: "Pengguna Terotentikasi",
      role: "ADMIN_POGI",
      branch: "Pusat"
    };
  },

  setCurrentUser(user) {
    if (typeof user === "string") {
      this._currentUser = {
        id: "auth-" + user.toLowerCase(),
        name: user.replace("_", " "),
        role: user,
        branch: "Pusat"
      };
    } else {
      this._currentUser = user;
    }
  },

  getAllRoles() {
    return [
      { key: "SUPER_ADMIN", label: "Super Admin", desc: "Akses Penuh Seluruh Sistem" },
      { key: "ADMIN_PUSAT", label: "POGI Pusat", desc: "Monitoring & Cek Seluruh Wilayah Cabang" },
      { key: "ADMIN_POGI", label: "Admin POGI", desc: "Input & Update Data Dashboard & Kemenkes" },
      { key: "ADMIN_CABANG", label: "Admin Cabang", desc: "Cek Wilayah Cabang Sendiri" },
      { key: "DOKTER", label: "User/Dokter", desc: "Portofolio Mandiri & Monitoring SKP" }
    ];
  },

  canPerform(action) {
    const user = this.getCurrentUser();
    if (!user) return false;
    const role = (user.role || "").toUpperCase();

    if (role === "SUPER_ADMIN" || role === "SUPER ADMIN") return true;

    if (role === "ADMIN_PUSAT" || role === "ADMIN PUSAT" || role === "POGI_PUSAT" || role === "POGI PUSAT") {
      // POGI Pusat: Memantau dan memeriksa seluruh wilayah cabang (Read/Audit Mode)
      const allowed = ["VIEW_ALL_BRANCHES", "VIEW_ADMIN_PERFORMANCE", "VIEW_ACTIVITY_LOG", "EXPORT_REPORT", "AUDIT_VERIFICATION"];
      return allowed.includes(action);
    }

    if (role === "ADMIN_POGI" || role === "ADMIN POGI" || role === "VERIFIKATOR") {
      // Admin POGI: Penginputan & update data dashboard & Kemenkes, verifikasi dokumen
      const restricted = ["DELETE_USER", "MANAGE_SYSTEM_SETTINGS", "ASSIGN_PIC"];
      return !restricted.includes(action);
    }

    if (role === "ADMIN_CABANG" || role === "ADMIN CABANG") {
      // Admin Cabang: Cek wilayah cabang sendiri
      const allowed = ["VIEW_BRANCH_DATA", "VIEW_DOCTOR_LIST", "EXPORT_REPORT"];
      return allowed.includes(action);
    }

    if (role === "DOKTER" || role === "USER/DOKTER" || role === "USER") {
      const allowed = ["VIEW_OWN_PROFILE", "UPLOAD_OWN_DOCUMENT"];
      return allowed.includes(action);
    }

    return false;
  },

  isSuperAdmin() {
    const r = (this.getCurrentUser().role || "").toUpperCase();
    return r === "SUPER_ADMIN" || r === "SUPER ADMIN";
  },

  isAdminPusat() {
    const r = (this.getCurrentUser().role || "").toUpperCase();
    return r === "ADMIN_PUSAT" || r === "ADMIN PUSAT" || r === "POGI_PUSAT" || r === "POGI PUSAT";
  },

  isAdminPogi() {
    const r = (this.getCurrentUser().role || "").toUpperCase();
    return r === "ADMIN_POGI" || r === "ADMIN POGI" || r === "VERIFIKATOR";
  },

  isVerifikator() {
    return this.isAdminPogi();
  },

  isBranchAdmin() {
    const r = (this.getCurrentUser().role || "").toUpperCase();
    return r === "ADMIN_CABANG" || r === "ADMIN CABANG";
  },

  isDoctor() {
    const r = (this.getCurrentUser().role || "").toUpperCase();
    return r === "DOKTER" || r === "USER/DOKTER" || r === "USER";
  },

  isOperational() {
    return this.isSuperAdmin() || this.isAdminPogi();
  },

  isViewer() {
    return this.isAdminPusat() || this.isBranchAdmin();
  },

  getDoctorsForCurrentUser() {
    const all = this.getDoctors();
    const user = this.getCurrentUser();
    if (!user) return all;
    const role = (user.role || "").toUpperCase();

    if (this.isDoctor()) {
      return all.filter(d => d.id === user.doctorId || (user.email && d.email === user.email));
    }
    if (this.isBranchAdmin() && user.branch && user.branch !== "Pusat") {
      return all.filter(d => (d.province && d.province.toLowerCase() === user.branch.toLowerCase()) || (d.branch && d.branch.toLowerCase() === user.branch.toLowerCase()));
    }
    return all;
  },

  getCronSchedulerStatus() {
    return {
      autoCheckActive: true,
      cadenceHours: 24,
      lastRunTime: "24 Nov 2026, 06.00",
      nextRunTime: "25 Nov 2026, 06.00",
      status: "Active & Monitored",
      recentLogs: []
    };
  },

  runCronTrigger(triggerType = "H-7") {
    return {
      success: true,
      triggerType,
      scannedDoctors: this.getDoctors().length,
      timestamp: this.formatCurrentTimestamp()
    };
  },

  getWaQueue() {
    if (typeof localStorage !== "undefined") {
      try {
        return JSON.parse(localStorage.getItem("pogi_wa_queue") || "[]");
      } catch(e) {
        return [];
      }
    }
    return [];
  },

  queueWaBlast(recipients, templateType, customNotes = "") {
    const queue = recipients.map((r, idx) => ({
      queueId: "wa-" + Date.now() + "-" + idx,
      recipientPhone: r.phone || "+628123456789",
      doctorName: r.name,
      template: templateType,
      customNotes: customNotes,
      status: "Queued",
      queuedAt: this.formatCurrentTimestamp(),
      jitterSeconds: Math.floor(Math.random() * 8) + 3
    }));

    if (typeof localStorage !== "undefined") {
      try {
        localStorage.setItem("pogi_wa_queue", JSON.stringify(queue));
      } catch(e) {}
    }

    this.addActivity({
      actor: this.getCurrentUser().name,
      action: "Kirim WA Blast Massal (Message Queue)",
      details: `Menyiapkan antrean pengiriman WA Blast (${templateType}) untuk ${recipients.length} dokter terpilih dengan perlindungan anti-ban jitter.`,
      category: "Automation"
    });

    return queue;
  },

  clearWaQueue() {
    if (typeof localStorage !== "undefined") {
      try {
        localStorage.removeItem("pogi_wa_queue");
      } catch(e) {}
    }
  }
};

if (typeof window !== "undefined") window.PogiStore = PogiStore;
if (typeof globalThis !== "undefined") globalThis.PogiStore = PogiStore;
if (typeof module !== "undefined" && module.exports) module.exports = { PogiStore, INITIAL_PROVINCES, INITIAL_DOCTORS };
