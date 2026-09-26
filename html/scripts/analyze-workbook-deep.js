const XLSX = require('xlsx');

const file = 'c:\\Users\\viral\\Downloads\\Group Tracker 2026.xlsx';
const wb = XLSX.readFile(file, { cellDates: true, sheetStubs: true });

// Parse row 2 as sub-headers for monthly sheets
function parseMonthlySheet(name) {
  const ws = wb.Sheets[name];
  const rows = XLSX.utils.sheet_to_json(ws, { header: 1, defval: '', raw: false });
  if (rows.length < 2) return [];
  const row1 = rows[0];
  const row2 = rows[1];
  const headers = row2.map((h, i) => {
    const v = String(h).trim();
    if (v) return v;
    // inherit section from row1
    let section = '';
    for (let j = i; j >= 0; j--) {
      if (String(row1[j]).trim()) { section = String(row1[j]).trim(); break; }
    }
    return section ? `${section} [col${i}]` : `Col${i}`;
  });
  return { headers, rows: rows.slice(2) };
}

// Get full header map from June'26 (largest sample)
const june = parseMonthlySheet("June'26");
console.log('=== JUNE 26 FULL COLUMN MAP ===');
june.headers.forEach((h, i) => {
  if (String(h).trim()) console.log(`${i}: ${h}`);
});

// Aggregate all bookings from monthly sheets
const monthSheets = wb.SheetNames.filter((n) => n.includes("'26") || n.includes('2026'));
let allBookings = [];
monthSheets.forEach((sheetName) => {
  if (sheetName.startsWith('Enquiries') || sheetName.includes('Cancelled') || sheetName === 'BOB' || sheetName.includes('Contacts')) return;
  const { headers, rows } = parseMonthlySheet(sheetName);
  rows.forEach((row) => {
    if (!row.some((c) => String(c).trim())) return;
    const arrival = String(row[0] || '').trim();
    if (!arrival || arrival === 'Total' || arrival.includes('Date of')) return;
    const obj = { sheet: sheetName };
    headers.forEach((h, i) => {
      if (row[i] !== undefined && String(row[i]).trim()) obj[h] = String(row[i]).trim();
    });
    allBookings.push(obj);
  });
});
console.log('\n=== BOOKING COUNT BY MONTH ===');
monthSheets.forEach((s) => {
  if (s.startsWith('Enquiries') || s.includes('Cancelled') || s === 'BOB' || s.includes('Contacts')) return;
  const n = allBookings.filter((b) => b.sheet === s).length;
  console.log(`${s}: ${n}`);
});
console.log(`Total booking rows: ${allBookings.length}`);

// Sample clients, agencies, block ids
const clients = new Set();
const agencies = new Set();
const contacts = new Set();
const statuses = new Set();
const mealPlans = new Set();
allBookings.forEach((b) => {
  if (b.Client) clients.add(b.Client);
  if (b.Agency) agencies.add(b.Agency);
  if (b['Agency Ref']) agencies.add(b['Agency Ref']);
  if (b.Contact) contacts.add(b.Contact);
  if (b.Status) statuses.add(b.Status);
  if (b.Meal) mealPlans.add(b.Meal);
  if (b['Meal Plan']) mealPlans.add(b['Meal Plan']);
});
console.log('\n=== UNIQUE CLIENTS (sample) ===');
console.log([...clients].slice(0, 40).join('\n'));
console.log(`\nTotal clients: ${clients.size}`);
console.log('\n=== UNIQUE AGENCY REFS (sample) ===');
console.log([...agencies].slice(0, 30).join('\n'));
console.log(`Total agency refs: ${agencies.size}`);
console.log('\n=== STATUSES ===', [...statuses]);
console.log('\n=== MEAL PLANS ===', [...mealPlans]);

// BOB sheet
console.log('\n=== BOB SHEET ===');
const bob = XLSX.utils.sheet_to_json(wb.Sheets['BOB'], { header: 1, defval: '', raw: false });
bob.slice(0, 15).forEach((r, i) => console.log(i, r.filter((c) => c).join(' | ')));

// Enquiries
['Enquiries 2026', 'Enquiries 2027'].forEach((name) => {
  console.log(`\n=== ${name} ===`);
  const rows = XLSX.utils.sheet_to_json(wb.Sheets[name], { header: 1, defval: '', raw: false });
  rows.slice(0, 8).forEach((r, i) => console.log(i, r.filter((c) => c).slice(0, 20).join(' | ')));
  console.log('Total rows:', rows.filter((r) => r.some((c) => c)).length);
});

// Cancelled
['Cancelled Bookings', 'Cancelled Enquiries'].forEach((name) => {
  console.log(`\n=== ${name} ===`);
  const rows = XLSX.utils.sheet_to_json(wb.Sheets[name], { header: 1, defval: '', raw: false });
  rows.slice(0, 8).forEach((r, i) => console.log(i, r.filter((c) => c).slice(0, 20).join(' | ')));
  console.log('Total rows:', rows.filter((r) => r.some((c) => c)).length);
});

// Contacts
console.log('\n=== Contacts & Legends ===');
const contactsSheet = XLSX.utils.sheet_to_json(wb.Sheets['Contacts & Legends'], { header: 1, defval: '', raw: false });
contactsSheet.slice(0, 25).forEach((r, i) => console.log(i, r.filter((c) => c).join(' | ')));
