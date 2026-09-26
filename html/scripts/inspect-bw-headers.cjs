const fs = require('fs');
const XLSX = require('xlsx');
const path = require('path');
const file = path.join(__dirname, '..', '..', 'storage', 'app', 'imports', 'bw-group-tracker-2025.xlsx');
const wb = XLSX.readFile(file, { cellDates: true });

function dumpSheet(name, maxRows = 4) {
  const ws = wb.Sheets[name];
  if (!ws) { console.log('missing', name); return; }
  const rows = XLSX.utils.sheet_to_json(ws, { header: 1, defval: '', raw: false });
  console.log('\n===', name, 'rows=', rows.length, '===');
  rows.slice(0, maxRows).forEach((r, i) => {
    console.log('R'+i+':', r.slice(0, 18).map((c, idx) => `[${idx}]${String(c).slice(0,40)}`).join(' | '));
  });
}

dumpSheet("Jan'25", 3);
dumpSheet('Enquiries 2025', 5);
dumpSheet('Canx Bookings', 3);
dumpSheet('Contacts', 4);
dumpSheet('BOB', 3);

const data = JSON.parse(fs.readFileSync(path.join(__dirname, '..', '..', 'storage', 'app', 'imports', 'bw-2025-data.json'), 'utf8'));
const statuses = {};
data.bookings.forEach((b) => { statuses[b.status] = (statuses[b.status] || 0) + 1; });
console.log('\nBooking statuses:', statuses);
const cStatuses = {};
data.cancelledBookings.forEach((b) => { cStatuses[b.status] = (cStatuses[b.status] || 0) + 1; });
console.log('Cancelled statuses:', cStatuses);
console.log('Agencies sample:', [...new Set(data.bookings.map(b => b.agency).filter(Boolean))].slice(0, 20));
console.log('Clients sample:', [...new Set(data.bookings.map(b => b.client).filter(Boolean))].slice(0, 20));
