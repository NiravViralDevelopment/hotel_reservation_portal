/**
 * Export BW Group Tracker 2025.xlsx → JSON for Laravel import (column-accurate)
 */
const XLSX = require('xlsx');
const fs = require('fs');
const path = require('path');

const file = path.join(__dirname, '..', '..', 'storage', 'app', 'imports', 'bw-group-tracker-2025.xlsx');
const wb = XLSX.readFile(file, { cellDates: true, sheetStubs: true });

const COL = {
  arrival: 0, departure: 1, arrivalDay: 2, nights: 3, blockId: 4,
  client: 5, agency: 6, contact: 7, email: 8, status: 9,
  contractSent: 10, contractRecd: 11, savedDoc: 12, paymentTerm: 13,
  dueDate: 14, paymentStatus: 15, cxlPolicy: 16, cxlDueDate: 17, cxlDate: 18,
  commission: 19, singleRNs: 20, singleRate: 21, doubleRNs: 22, doubleRate: 23,
  tripleRNs: 24, tripleRate: 25, totalRNs: 26, totalRev: 27, bbRevenue: 28,
  dinnerRevenue: 29, nettRev: 30, mealPlan: 31, update: 32, roomingStatus: 33,
  invoiceStatus: 34, invoiceDate: 35, invoiceAmount: 36, commissionPayable: 37,
  operaCrossCheck: 39
};

function parseMoney(v) {
  if (v === undefined || v === null || v === '') return 0;
  const n = parseFloat(String(v).replace(/[£,\s]/g, ''));
  return isNaN(n) ? 0 : n;
}

function parseIntVal(v) {
  if (v === undefined || v === null || v === '') return 0;
  const n = parseInt(String(v).replace(/,/g, ''), 10);
  return isNaN(n) ? 0 : n;
}

function fmtDateIso(v) {
  if (!v) return null;
  if (v instanceof Date && !isNaN(v.getTime())) {
    return `${v.getFullYear()}-${String(v.getMonth() + 1).padStart(2, '0')}-${String(v.getDate()).padStart(2, '0')}`;
  }
  const s = String(v).trim();
  if (!s) return null;
  let m = s.match(/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/);
  if (m) return `${m[3]}-${m[2].padStart(2, '0')}-${m[1].padStart(2, '0')}`;
  m = s.match(/^(\d{1,2})-([A-Za-z]{3})-(\d{2,4})$/);
  if (m) {
    const months = { jan:0,feb:1,mar:2,apr:3,may:4,jun:5,jul:6,aug:7,sep:8,oct:9,nov:10,dec:11 };
    const mon = months[m[2].toLowerCase().slice(0, 3)];
    if (mon !== undefined) {
      let y = parseInt(m[3], 10);
      if (y < 100) y += 2000;
      return `${y}-${String(mon + 1).padStart(2, '0')}-${m[1].padStart(2, '0')}`;
    }
  }
  const parsed = Date.parse(s);
  if (!isNaN(parsed)) {
    const dt = new Date(parsed);
    return `${dt.getFullYear()}-${String(dt.getMonth() + 1).padStart(2, '0')}-${String(dt.getDate()).padStart(2, '0')}`;
  }
  return null;
}

function mapStatus(raw, cancelled = false) {
  if (cancelled) return 'Cancelled';
  const s = String(raw || '').trim().toUpperCase();
  if (!s) return 'Provisional';
  if (s === 'DEF' || s === 'D' || s === 'DEFINITE') return 'DEF';
  if (s === 'C' || s === 'CONFIRMED' || s === 'CONF') return 'Confirmed';
  if (s === 'P' || s === 'PROV' || s === 'PROVISIONAL') return 'Provisional';
  if (s === 'PEND' || s === 'PENDING') return 'Pending';
  if (s.includes('CANX') || s.includes('CANCEL')) return 'Cancelled';
  return raw;
}

function derivePaymentStatus(raw, rev) {
  if (!raw) return rev > 0 ? 'Partial' : 'Unpaid';
  const l = raw.toLowerCase();
  if (l.includes('recd') || l.includes('received') || l.includes('paid') || l.includes('charged')) {
    return 'Paid';
  }
  if (l.includes('pending') || l.includes('due') || l.includes('unpaid')) return 'Unpaid';
  return 'Partial';
}

function isMonthHeader(val) {
  return !val || /^Date of/i.test(val) || (/^(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)/i.test(val) && !/\d{2,}/.test(String(val).replace(/'/g, '')) && String(val).length < 12);
}

function isDataRow(row) {
  const arrival = String(row[COL.arrival] || '').trim();
  if (!arrival || arrival === 'Total' || isMonthHeader(arrival)) return false;
  if (!/\d/.test(arrival)) return false;
  return true;
}

function parseBookingRow(row, sheetName, cancelled = false) {
  const totalRNs = parseIntVal(row[COL.totalRNs]);
  const totalRev = parseMoney(row[COL.totalRev]);
  const singleRNs = parseIntVal(row[COL.singleRNs]);
  const doubleRNs = parseIntVal(row[COL.doubleRNs]);
  const tripleRNs = parseIntVal(row[COL.tripleRNs]);
  const meal = String(row[COL.mealPlan] || '').trim();
  const blockId = String(row[COL.blockId] || '').trim();
  return {
    blockId,
    arrival: fmtDateIso(row[COL.arrival]),
    departure: fmtDateIso(row[COL.departure]),
    arrivalDay: String(row[COL.arrivalDay] || '').trim(),
    nights: parseIntVal(row[COL.nights]) || 1,
    client: String(row[COL.client] || '').trim(),
    agencyRef: String(row[COL.agency] || '').trim(),
    contact: String(row[COL.contact] || '').trim(),
    email: String(row[COL.email] || '').trim(),
    status: mapStatus(row[COL.status], cancelled),
    contractSent: fmtDateIso(row[COL.contractSent]),
    contractRecd: fmtDateIso(row[COL.contractRecd]),
    savedDoc: String(row[COL.savedDoc] || '').trim(),
    paymentTerm: String(row[COL.paymentTerm] || '').trim(),
    dueDate: fmtDateIso(row[COL.dueDate]),
    paymentStatus: String(row[COL.paymentStatus] || '').trim(),
    paymentStatusDisplay: derivePaymentStatus(String(row[COL.paymentStatus] || '').trim(), totalRev),
    cxlPolicy: String(row[COL.cxlPolicy] || '').trim(),
    cxlDueDate: fmtDateIso(row[COL.cxlDueDate]),
    cxlDate: fmtDateIso(row[COL.cxlDate]),
    commission: String(row[COL.commission] || '').trim(),
    singleRNs, singleRate: parseMoney(row[COL.singleRate]),
    doubleRNs, doubleRate: parseMoney(row[COL.doubleRate]),
    tripleRNs, tripleRate: parseMoney(row[COL.tripleRate]),
    totalRNs,
    rooms: (singleRNs + doubleRNs + tripleRNs) || totalRNs,
    pax: singleRNs + doubleRNs * 2 + tripleRNs * 3 || Math.max(totalRNs, 1),
    revenue: totalRev,
    bbRevenue: parseMoney(row[COL.bbRevenue]),
    dinnerRevenue: parseMoney(row[COL.dinnerRevenue]),
    nettRev: parseMoney(row[COL.nettRev]),
    mealPlan: meal || 'BB',
    updateNotes: String(row[COL.update] || '').trim(),
    roomingStatus: String(row[COL.roomingStatus] || '').trim(),
    invoiceStatus: String(row[COL.invoiceStatus] || '').trim(),
    invoiceDate: fmtDateIso(row[COL.invoiceDate]),
    invoiceAmount: parseMoney(row[COL.invoiceAmount]),
    commissionPayable: parseMoney(row[COL.commissionPayable]),
    operaCrossCheck: String(row[COL.operaCrossCheck] || '').trim(),
    sheet: sheetName,
    cancelled,
    cityTax: cancelled ? parseMoney(row[33]) : 0,
    revenueLost: cancelled ? totalRev : null,
    cancellationReason: cancelled ? (String(row[COL.cxlPolicy] || '').trim() || 'Cancelled') : null
  };
}

const monthSheets = ["Jan'25","Feb'25","Mar'25","Apr'25","May'25","June'25","July'25","Aug'25","Sept'25","Oct'25","Nov'25","Dec'25"];
const bookings = [];
monthSheets.forEach((sheetName) => {
  const ws = wb.Sheets[sheetName];
  if (!ws) return;
  const rows = XLSX.utils.sheet_to_json(ws, { header: 1, defval: '', raw: false });
  rows.slice(2).forEach((row) => {
    if (!isDataRow(row)) return;
    const parsed = parseBookingRow(row, sheetName, false);
    if (!parsed.blockId || !parsed.arrival) return;
    bookings.push(parsed);
  });
});

const cancelledBookings = [];
if (wb.Sheets['Canx Bookings']) {
  const rows = XLSX.utils.sheet_to_json(wb.Sheets['Canx Bookings'], { header: 1, defval: '', raw: false });
  rows.slice(2).forEach((row) => {
    if (!isDataRow(row)) return;
    const parsed = parseBookingRow(row, 'Canx Bookings', true);
    if (!parsed.blockId || !parsed.arrival) return;
    cancelledBookings.push(parsed);
  });
}

// Enquiries 2025 layout:
// 0 Date, 1 Day, 2 Received on, 3 Nights, 4 Group Name, 5 Rooms/night,
// 6 Single, 7 Single Rate, 8 Double, 9 Double Rate, 10 Triple, 11 Triple Rate,
// 12 Basis, 13 Total Revenue, 14 Hold Option Date, 15 Notes, 16 REMARKS
function parseEnquirySheet(sheetName, forceStatus = 'new') {
  if (!wb.Sheets[sheetName]) return [];
  const yearMatch = sheetName.match(/20\d{2}/);
  const year = yearMatch ? parseInt(yearMatch[0], 10) : 2025;
  const rows = XLSX.utils.sheet_to_json(wb.Sheets[sheetName], { header: 1, defval: '', raw: false });
  const out = [];
  let seq = 0;
  rows.slice(1).forEach((row) => {
    const groupName = String(row[4] || '').trim();
    const dateRaw = String(row[0] || '').trim();
    if (!groupName) return;
    if (/^(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)[-']?\d{2}$/i.test(dateRaw)) return;
    seq++;
    const prefix = forceStatus === 'cancelled' ? 'CENQ' : 'ENQ';
    out.push({
      ref: `${prefix}-${year}-${String(seq).padStart(4, '0')}`,
      year,
      enquiryDate: fmtDateIso(row[0]) || fmtDateIso(row[2]),
      day: String(row[1] || '').trim(),
      nights: parseIntVal(row[3]) || 1,
      groupName,
      roomsPerNight: parseIntVal(row[5]),
      single: parseIntVal(row[6]),
      singleRate: parseMoney(row[7]),
      double: parseIntVal(row[8]),
      doubleRate: parseMoney(row[9]),
      triple: parseIntVal(row[10]),
      tripleRate: parseMoney(row[11]),
      basis: String(row[12] || 'BB').trim() || 'BB',
      totalRevenue: parseMoney(row[13]),
      optionDate: fmtDateIso(row[14]),
      email: '',
      remarks: [String(row[15] || '').trim(), String(row[16] || '').trim()].filter(Boolean).join(' | '),
      cxlPolicy: '',
      sourceSheet: sheetName,
      status: forceStatus
    });
  });
  return out;
}

const enquiries = [
  ...parseEnquirySheet('Enquiries 2025', 'new'),
  ...parseEnquirySheet('Enquiries 2026', 'new'),
  ...parseEnquirySheet('Canx Enq 2025', 'cancelled')
];

const contacts = [];
if (wb.Sheets['Contacts']) {
  const rows = XLSX.utils.sheet_to_json(wb.Sheets['Contacts'], { header: 1, defval: '', raw: false });
  rows.slice(1).forEach((r) => {
    const company = String(r[0] || '').trim();
    const name = String(r[1] || '').trim();
    if (!company && !name) return;
    contacts.push({
      company,
      name: name || company,
      position: String(r[2] || '').trim(),
      phone: String(r[3] || '').trim(),
      email: String(r[4] || '').trim(),
      address: String(r[5] || '').trim()
    });
  });
}

// BOB 2025 columns:
// 0 Month, 1 BoB2025, 2 BoB2024, 3 Var, 4 ADR2025, 5 ADR2024, 6 ADRVar,
// 7 RN2025, 8 RN2024, 9 Breakfast, 10 Dinner, 11 Dinner covers
const bob = [];
const monthIndex = {
  january: 1, february: 2, march: 3, april: 4, may: 5, june: 6,
  july: 7, august: 8, september: 9, october: 10, november: 11, december: 12
};
if (wb.Sheets['BOB']) {
  const rows = XLSX.utils.sheet_to_json(wb.Sheets['BOB'], { header: 1, defval: '', raw: false });
  rows.slice(1).forEach((r) => {
    const label = String(r[0] || '').trim();
    if (!label || /^total$/i.test(label)) return;
    const m = monthIndex[label.toLowerCase()];
    if (!m) return;
    bob.push({
      year: 2025,
      month: m,
      label,
      bobCurrent: parseMoney(r[1]),
      bobPrevious: parseMoney(r[2]),
      bobVariance: parseMoney(r[3]),
      adrCurrent: parseMoney(r[4]),
      adrPrevious: parseMoney(r[5]),
      adrVariance: parseMoney(r[6]),
      roomNightsCurrent: parseIntVal(r[7]),
      roomNightsPrevious: parseIntVal(r[8]),
      breakfastRevenue: parseMoney(r[9]),
      dinnerRevenue: parseMoney(r[10]),
      dinnerCovers: parseIntVal(r[11])
    });
  });
}

const out = {
  meta: {
    extracted: new Date().toISOString(),
    source: 'BW Group Tracker 2025.xlsx',
    hotelName: 'Brighton Harbour Hotel',
    hotelCode: 'BWH01',
    bookingRowCount: bookings.length,
    uniqueBlockCount: new Set(bookings.map((b) => b.blockId)).size,
    cancelledBookingCount: cancelledBookings.length,
    enquiryCount: enquiries.length,
    contactCount: contacts.length,
    bobCount: bob.length
  },
  bookings,
  cancelledBookings,
  enquiries,
  contacts,
  bob
};

const outPath = path.join(__dirname, '..', '..', 'storage', 'app', 'imports', 'bw-2025-data.json');
fs.writeFileSync(outPath, JSON.stringify(out));
console.log(JSON.stringify(out.meta, null, 2));
console.log('Enquiry sample:', JSON.stringify(enquiries[0] || null));
console.log('BOB sample:', JSON.stringify(bob[0] || null));
