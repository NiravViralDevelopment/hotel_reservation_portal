/**
 * Extract Group Tracker 2026 workbook data for static HTML population
 */
const XLSX = require('xlsx');
const fs = require('fs');
const path = require('path');

const file = 'c:\\Users\\viral\\Downloads\\Group Tracker 2026.xlsx';
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
  updatesExtra: 38, operaCrossCheck: 39
};

function parseRow(row, sheetName) {
  const totalRNs = parseIntVal(row[COL.totalRNs]);
  const totalRev = parseMoney(row[COL.totalRev]);
  const doubleRNs = parseIntVal(row[COL.doubleRNs]);
  const adr = totalRNs > 0
    ? Math.round((totalRev / totalRNs) * 100) / 100
    : parseMoney(row[COL.doubleRate]) || parseMoney(row[COL.singleRate]);
  const meal = String(row[COL.mealPlan] || '').trim();
  return {
    ref: String(row[COL.blockId] || '').trim() || `TMP-${sheetName}-${Math.random()}`,
    blockId: String(row[COL.blockId] || '').trim(),
    arrival: fmtDate(row[COL.arrival]),
    departure: fmtDate(row[COL.departure]),
    arrivalDay: String(row[COL.arrivalDay] || '').trim(),
    nights: parseIntVal(row[COL.nights]),
    client: String(row[COL.client] || '').trim(),
    agency: String(row[COL.agency] || '').trim(),
    contact: String(row[COL.contact] || '').trim(),
    email: String(row[COL.email] || '').trim(),
    status: String(row[COL.status] || 'DEF').trim(),
    contractSent: fmtDate(row[COL.contractSent]),
    contractRecd: fmtDate(row[COL.contractRecd]),
    savedDoc: String(row[COL.savedDoc] || '').trim() === 'X',
    paymentTerm: String(row[COL.paymentTerm] || '').trim(),
    dueDate: fmtDate(row[COL.dueDate]),
    paymentStatus: String(row[COL.paymentStatus] || '').trim(),
    cxlPolicy: String(row[COL.cxlPolicy] || '').trim(),
    cxlDueDate: fmtDate(row[COL.cxlDueDate]),
    cxlDate: fmtDate(row[COL.cxlDate]),
    commission: String(row[COL.commission] || '').trim(),
    singleRNs: parseIntVal(row[COL.singleRNs]),
    singleRate: parseMoney(row[COL.singleRate]),
    doubleRNs: parseIntVal(row[COL.doubleRNs]),
    doubleRate: parseMoney(row[COL.doubleRate]),
    tripleRNs: parseIntVal(row[COL.tripleRNs]),
    tripleRate: parseMoney(row[COL.tripleRate]),
    totalRNs,
    rooms: parseIntVal(row[COL.singleRNs]) + doubleRNs + parseIntVal(row[COL.tripleRNs]) || totalRNs,
    pax: parseIntVal(row[COL.singleRNs]) + doubleRNs * 2 + parseIntVal(row[COL.tripleRNs]) * 3 || totalRNs * 2,
    revenue: totalRev,
    bbRevenue: parseMoney(row[COL.bbRevenue]),
    dinnerRevenue: parseMoney(row[COL.dinnerRevenue]),
    nettRev: parseMoney(row[COL.nettRev]),
    mealPlan: meal || 'BB',
    update: String(row[COL.update] || '').trim(),
    roomingStatus: String(row[COL.roomingStatus] || '').trim(),
    invoiceStatus: String(row[COL.invoiceStatus] || '').trim(),
    invoiceDate: fmtDate(row[COL.invoiceDate]),
    invoiceAmount: parseMoney(row[COL.invoiceAmount]),
    commissionPayable: String(row[COL.commissionPayable] || '').trim(),
    updatesExtra: parseMoney(row[COL.updatesExtra]),
    operaCrossCheckRate: parseMoney(row[COL.operaCrossCheck]),
    adr,
    sheet: sheetName,
    group: String(row[COL.agency] || row[COL.client] || '').trim(),
    bookingStatus: String(row[COL.status] || 'DEF').trim() === 'DEF' ? 'Confirmed' : String(row[COL.status] || '').trim(),
    paymentStatusDisplay: derivePaymentStatus(String(row[COL.paymentStatus] || '').trim(), totalRev),
    created: fmtDate(row[COL.contractSent]) || fmtDate(row[COL.contractRecd])
  };
}

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

function fmtDate(v) {
  if (!v) return '';
  if (v instanceof Date) {
    return v.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
  }
  return String(v).trim();
}

function isMonthHeader(val) {
  return !val || /^Date of/i.test(val) || /^(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)/i.test(val) && val.length < 10 && !/\d/.test(val);
}

function isDataRow(row) {
  const arrival = String(row[COL.arrival] || '').trim();
  if (!arrival || arrival === 'Total' || isMonthHeader(arrival)) return false;
  if (!/\d/.test(arrival)) return false;
  return true;
}

const monthSheets = [
  "Jan'26", "Feb'26", "March'26", "April'26", "May'26", "June'26",
  'July 2026', 'Aug 2026', 'Sept 2026', 'Oct 2026', 'Nov 2026', 'Dec 2026'
];

let bookings = [];
monthSheets.forEach((sheetName) => {
  const ws = wb.Sheets[sheetName];
  if (!ws) return;
  const rows = XLSX.utils.sheet_to_json(ws, { header: 1, defval: '', raw: false });
  rows.slice(2).forEach((row) => {
    if (!isDataRow(row)) return;
    bookings.push(parseRow(row, sheetName));
  });
});

function derivePaymentStatus(raw, rev) {
  if (!raw) return rev > 0 ? 'Partial' : 'Unpaid';
  const l = raw.toLowerCase();
  if (l.includes('recd') && !l.includes('£') && raw.length < 20) return 'Paid';
  if (l.includes('recd') || l.includes('received')) {
    const due = parseMoney(raw.split('recd')[0]);
    if (due > 0 && rev > due) return 'Partial';
    return 'Paid';
  }
  if (l.includes('pending') || l.includes('due')) return 'Unpaid';
  return 'Partial';
}

// Contacts
const contactsRows = XLSX.utils.sheet_to_json(wb.Sheets['Contacts & Legends'], { header: 1, defval: '', raw: false });
const contacts = contactsRows.slice(1).filter((r) => r[0]).map((r) => ({
  company: String(r[0]).trim(),
  name: String(r[1]).trim(),
  position: String(r[2]).trim(),
  phone: String(r[3]).trim(),
  email: String(r[4]).trim(),
  agency: String(r[0]).trim(),
  country: 'United Kingdom',
  notes: ''
}));

// BOB — all 16 columns
const bobRows = XLSX.utils.sheet_to_json(wb.Sheets['BOB'], { header: 1, defval: '', raw: false });
const bob = bobRows.slice(1).filter((r) => r[0] && r[0] !== 'Total').map((r) => ({
  month: String(r[0]).trim(),
  bob2026: parseMoney(r[1]),
  bob2025: parseMoney(r[2]),
  bobVariance: parseMoney(r[3]),
  stlyBob2025: parseMoney(r[4]),
  adr2026: parseMoney(r[5]),
  adr2025: parseMoney(r[6]),
  adrVariance: parseMoney(r[7]),
  adrStly2025: parseMoney(r[8]),
  roomNights2026: parseIntVal(r[9]),
  roomNights2025: parseIntVal(r[10]),
  stlyRoomNights2025: parseIntVal(r[11]),
  stlyBreakfastRevenue: parseMoney(r[12]),
  breakfast2026: parseMoney(r[13]),
  dinner2026: parseMoney(r[14]),
  totalDinnerCovers: parseMoney(r[15]),
  // legacy aliases
  revenue: parseMoney(r[1]),
  adr: parseMoney(r[5]),
  roomNights: parseIntVal(r[9]),
  breakfast: parseMoney(r[13]),
  dinner: parseMoney(r[14])
}));
const bobTotal = bobRows.find((r) => r[0] === 'Total');

function isEnquiryRow(row) {
  const group = String(row[3] || '').trim();
  if (group) return true;
  const dateCell = String(row[0] || '').trim();
  if (!dateCell) return false;
  if (/^(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)[-']?\d{2}$/i.test(dateCell)) return false;
  return row.slice(4).some((c) => String(c || '').trim());
}

// Enquiries — all 17 columns (Enquiries 2026 + Enquiries 2027)
function parseEnquiries(sheetName) {
  const year = sheetName.includes('2027') ? '2027' : '2026';
  const rows = XLSX.utils.sheet_to_json(wb.Sheets[sheetName], { header: 1, defval: '', raw: false });
  const out = [];
  let seq = 0;
  rows.slice(1).forEach((row) => {
    if (!isEnquiryRow(row)) return;
    seq++;
    out.push({
      ref: `ENQ-${year}-${String(seq).padStart(4, '0')}`,
      sourceSheet: sheetName,
      year,
      date: fmtDate(row[0]) || String(row[0]).trim(),
      day: String(row[1] || '').trim(),
      nights: parseIntVal(row[2]),
      groupName: String(row[3] || '').trim(),
      group: String(row[3] || '').trim(),
      roomsPerNight: parseIntVal(row[4]),
      single: parseIntVal(row[5]),
      singleRate: parseMoney(row[6]),
      double: parseIntVal(row[7]),
      doubleRate: parseMoney(row[8]),
      triple: parseIntVal(row[9]),
      tripleRate: parseMoney(row[10]),
      basis: String(row[11] || 'BB').trim(),
      revenue: parseMoney(row[12]),
      totalRevenue: parseMoney(row[12]),
      cxlPolicy: String(row[13] || '').trim(),
      optionDate: fmtDate(row[14]),
      email: String(row[15] || '').trim(),
      remarks: String(row[16] || '').trim(),
      status: 'Enquiry'
    });
  });
  return out;
}

const enquiries = [...parseEnquiries('Enquiries 2026'), ...parseEnquiries('Enquiries 2027')];

// Cancelled Bookings — full 40 columns (sheet-specific layout from col 33)
const CXL_COL = {
  arrival: 0, departure: 1, arrivalDay: 2, nights: 3, blockId: 4,
  client: 5, agency: 6, contact: 7, email: 8, status: 9,
  contractSent: 10, contractRecd: 11, savedDoc: 12, paymentTerm: 13,
  dueDate: 14, paymentStatus: 15, cxlPolicy: 16, cxlDueDate: 17, cxlDate: 18,
  commission: 19, singleRNs: 20, singleRate: 21, doubleRNs: 22, doubleRate: 23,
  tripleRNs: 24, tripleRate: 25, totalRNs: 26, totalRev: 27, bbRevenue: 28,
  dinnerRevenue: 29, nettRev: 30, mealPlan: 31, update: 32,
  cityTax: 33, rooming: 34, invoiceStatus: 35, invoiceDate: 36, invoiceAmount: 37,
  commissionPayable: 38, commissionPayable2: 39
};

function parseCancelledRow(row) {
  const totalRNs = parseIntVal(row[CXL_COL.totalRNs]);
  const totalRev = parseMoney(row[CXL_COL.totalRev]);
  const doubleRNs = parseIntVal(row[CXL_COL.doubleRNs]);
  const adr = totalRNs > 0
    ? Math.round((totalRev / totalRNs) * 100) / 100
    : parseMoney(row[CXL_COL.doubleRate]) || parseMoney(row[CXL_COL.singleRate]);
  return {
    ref: String(row[CXL_COL.blockId] || '').trim(),
    blockId: String(row[CXL_COL.blockId] || '').trim(),
    arrival: fmtDate(row[CXL_COL.arrival]),
    departure: fmtDate(row[CXL_COL.departure]),
    arrivalDay: String(row[CXL_COL.arrivalDay] || '').trim(),
    nights: parseIntVal(row[CXL_COL.nights]),
    client: String(row[CXL_COL.client] || '').trim(),
    agency: String(row[CXL_COL.agency] || '').trim(),
    contact: String(row[CXL_COL.contact] || '').trim(),
    email: String(row[CXL_COL.email] || '').trim(),
    status: String(row[CXL_COL.status] || '').trim(),
    contractSent: fmtDate(row[CXL_COL.contractSent]),
    contractRecd: fmtDate(row[CXL_COL.contractRecd]),
    savedDoc: String(row[CXL_COL.savedDoc] || '').trim() === 'X',
    paymentTerm: String(row[CXL_COL.paymentTerm] || '').trim(),
    dueDate: fmtDate(row[CXL_COL.dueDate]),
    paymentStatus: String(row[CXL_COL.paymentStatus] || '').trim(),
    cxlPolicy: String(row[CXL_COL.cxlPolicy] || '').trim(),
    cxlDueDate: fmtDate(row[CXL_COL.cxlDueDate]),
    cxlDate: fmtDate(row[CXL_COL.cxlDate]),
    commission: String(row[CXL_COL.commission] || '').trim(),
    singleRNs: parseIntVal(row[CXL_COL.singleRNs]),
    singleRate: parseMoney(row[CXL_COL.singleRate]),
    doubleRNs: parseIntVal(row[CXL_COL.doubleRNs]),
    doubleRate: parseMoney(row[CXL_COL.doubleRate]),
    tripleRNs: parseIntVal(row[CXL_COL.tripleRNs]),
    tripleRate: parseMoney(row[CXL_COL.tripleRate]),
    totalRNs,
    revenue: totalRev,
    revenueLost: totalRev,
    bbRevenue: parseMoney(row[CXL_COL.bbRevenue]),
    dinnerRevenue: parseMoney(row[CXL_COL.dinnerRevenue]),
    nettRev: parseMoney(row[CXL_COL.nettRev]),
    mealPlan: String(row[CXL_COL.mealPlan] || '').trim() || 'BB',
    update: String(row[CXL_COL.update] || '').trim(),
    cityTax: parseMoney(row[CXL_COL.cityTax]),
    rooming: String(row[CXL_COL.rooming] || '').trim(),
    invoiceStatus: String(row[CXL_COL.invoiceStatus] || '').trim(),
    invoiceDate: fmtDate(row[CXL_COL.invoiceDate]),
    invoiceAmount: parseMoney(row[CXL_COL.invoiceAmount]),
    commissionPayable: String(row[CXL_COL.commissionPayable] || '').trim(),
    commissionPayable2: String(row[CXL_COL.commissionPayable2] || '').trim(),
    adr,
    group: String(row[CXL_COL.agency] || row[CXL_COL.client] || '').trim(),
    cancelledDate: fmtDate(row[CXL_COL.cxlDate]) || fmtDate(row[CXL_COL.cxlDueDate]),
    reason: String(row[CXL_COL.cxlPolicy] || '').trim(),
    sheet: 'Cancelled Bookings'
  };
}

const cxlRows = XLSX.utils.sheet_to_json(wb.Sheets['Cancelled Bookings'], { header: 1, defval: '', raw: false });
const cancelledBookings = [];
cxlRows.slice(2).forEach((row) => {
  if (!isDataRow(row)) return;
  cancelledBookings.push(parseCancelledRow(row));
});

const blockIds = new Set(bookings.map((b) => b.blockId).filter(Boolean));

const out = {
  meta: {
    extracted: new Date().toISOString(),
    source: 'Group Tracker 2026.xlsx',
    bookingRowCount: bookings.length,
    uniqueBlockCount: blockIds.size,
    cancelledBookingCount: cancelledBookings.length,
    enquiryCount: enquiries.length,
    monthSheets
  },
  bookings,
  contacts,
  bob,
  bobTotal: bobTotal ? {
    month: 'Total',
    bob2026: parseMoney(bobTotal[1]),
    bob2025: parseMoney(bobTotal[2]),
    bobVariance: parseMoney(bobTotal[3]),
    stlyBob2025: parseMoney(bobTotal[4]),
    adr2026: parseMoney(bobTotal[5]),
    adr2025: parseMoney(bobTotal[6]),
    adrVariance: parseMoney(bobTotal[7]),
    adrStly2025: parseMoney(bobTotal[8]),
    roomNights2026: parseIntVal(bobTotal[9]),
    roomNights2025: parseIntVal(bobTotal[10]),
    stlyRoomNights2025: parseIntVal(bobTotal[11]),
    stlyBreakfastRevenue: parseMoney(bobTotal[12]),
    breakfast2026: parseMoney(bobTotal[13]),
    dinner2026: parseMoney(bobTotal[14]),
    totalDinnerCovers: parseMoney(bobTotal[15]),
    revenue: parseMoney(bobTotal[1]),
    adr: parseMoney(bobTotal[5]),
    roomNights: parseIntVal(bobTotal[9]),
    breakfast: parseMoney(bobTotal[13]),
    dinner: parseMoney(bobTotal[14])
  } : null,
  enquiries,
  cancelledBookings
};

const outPath = path.join(__dirname, '..', 'assets', 'js', 'excel-data.json');
const jsPath = path.join(__dirname, '..', 'assets', 'js', 'excel-data.js');
fs.writeFileSync(outPath, JSON.stringify(out, null, 2));
fs.writeFileSync(jsPath, '/** Auto-generated from Group Tracker 2026.xlsx — do not edit manually */\nconst EXCEL_DATA = ' + JSON.stringify(out) + ';\n');
console.log('Exported:', outPath);
console.log('Exported:', jsPath);
console.log('Booking rows:', bookings.length, '| Unique blocks:', blockIds.size);
console.log('Contacts:', contacts.length, '| Enquiries:', enquiries.length, '| Cancelled:', cancelledBookings.length);
