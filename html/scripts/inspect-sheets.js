const XLSX = require('xlsx');
const wb = XLSX.readFile('c:\\Users\\viral\\Downloads\\Group Tracker 2026.xlsx', { cellDates: true });

function dualHeaders(ws) {
  const rows = XLSX.utils.sheet_to_json(ws, { header: 1, defval: '' });
  const row1 = rows[0] || [];
  const row2 = rows[1] || [];
  const maxLen = Math.max(row1.length, row2.length);
  const headers = [];
  for (let i = 0; i < maxLen; i++) {
    const h2 = String(row2[i] ?? '').trim();
    let section = String(row1[i] ?? '').trim();
    if (!section) {
      for (let j = i; j >= 0; j--) {
        if (String(row1[j] ?? '').trim()) {
          section = String(row1[j]).trim();
          break;
        }
      }
    }
    if (h2) headers.push({ i, section, name: h2 });
    else if (section && i <= 40) headers.push({ i, section, name: `[${section}]` });
  }
  return { rows, headers: headers.filter((h) => h.i <= 45) };
}

const june = dualHeaders(wb.Sheets["June'26"]);
console.log('=== JUNE 26 — ALL FIELD COLUMNS (0-38) ===');
june.headers.forEach((h) => console.log(`${h.i}: ${h.section} → ${h.name}`));

const monthSheets = [
  "Jan'26", "Feb'26", "March'26", "April'26", "May'26", "June'26",
  'July 2026', 'Aug 2026', 'Sept 2026', 'Oct 2026', 'Nov 2026', 'Dec 2026'
];
let totalRows = 0;
const blockIds = new Set();
monthSheets.forEach((name) => {
  const { rows } = dualHeaders(wb.Sheets[name]);
  let n = 0;
  rows.slice(2).forEach((r) => {
    const a = String(r[0] ?? '').trim();
    if (!a || a === 'Total' || /^Date of/i.test(a) || !/\d/.test(a)) return;
    n++;
    if (r[4]) blockIds.add(String(r[4]).trim());
  });
  totalRows += n;
  console.log(`${name}: ${n} rows`);
});
console.log(`TOTAL booking rows: ${totalRows}, unique Block Ids: ${blockIds.size}`);

console.log('\n=== BOB ===');
const bob = XLSX.utils.sheet_to_json(wb.Sheets.BOB, { header: 1, defval: '' });
bob.forEach((r, i) => console.log(i, r.map((c, j) => (c ? j + ':' + c : '')).filter(Boolean).join(' | ')));

console.log('\n=== CANCELLED BOOKINGS headers ===');
const cb = XLSX.utils.sheet_to_json(wb.Sheets['Cancelled Bookings'], { header: 1, defval: '' });
console.log('R0:', cb[0].filter((c) => c).join(' | '));
console.log('R1:', cb[1].filter((c) => c).join(' | '));
let cbCount = 0;
cb.slice(2).forEach((r) => {
  const a = String(r[0] ?? '').trim();
  if (a && /\d/.test(a) && a !== 'Total') cbCount++;
});
console.log('Data rows:', cbCount);

console.log('\n=== CANCELLED ENQUIRIES headers ===');
const ce = XLSX.utils.sheet_to_json(wb.Sheets['Cancelled Enquiries'], { header: 1, defval: '' });
console.log('R0:', ce[0].filter((c) => c).join(' | '));
console.log('R1:', ce[1].filter((c) => c).join(' | '));
let ceCount = ce.slice(1).filter((r) => r.some((c) => String(c).trim())).length;
console.log('Data rows:', ceCount);

console.log('\n=== ENQUIRIES 2026 headers ===');
const e26 = XLSX.utils.sheet_to_json(wb.Sheets['Enquiries 2026'], { header: 1, defval: '' });
console.log(e26[0].map((c, i) => (c ? i + ':' + c : '')).filter(Boolean).join(' | '));
console.log('Data rows:', e26.slice(1).filter((r) => String(r[0]).trim() && /\d/.test(String(r[0]))).length);

console.log('\n=== ENQUIRIES 2027 headers ===');
const e27 = XLSX.utils.sheet_to_json(wb.Sheets['Enquiries 2027'], { header: 1, defval: '' });
console.log(e27[0].map((c, i) => (c ? i + ':' + c : '')).filter(Boolean).join(' | '));
console.log('Data rows:', e27.slice(1).filter((r) => String(r[0]).trim() && /\d/.test(String(r[0]))).length);

console.log('\n=== CONTACTS & LEGENDS ===');
const ct = XLSX.utils.sheet_to_json(wb.Sheets['Contacts & Legends'], { header: 1, defval: '' });
console.log('Headers:', ct[0].filter((c) => c).join(' | '));
console.log('Data rows:', ct.slice(1).filter((r) => String(r[0]).trim()).length);
