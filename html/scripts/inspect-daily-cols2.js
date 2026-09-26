const XLSX = require('xlsx');
const wb = XLSX.readFile('c:\\Users\\viral\\Downloads\\Group Tracker 2026.xlsx', { cellDates: true });
const ws = wb.Sheets["June'26"];
const rows = XLSX.utils.sheet_to_json(ws, { header: 1, defval: '', raw: true });
const row0 = rows[0];
const row1 = rows[1];
let dailyStart = -1;
for (let i = 40; i < row0.length; i++) {
  const h0 = row0[i];
  const h1 = row1[i];
  if (h0 || h1) {
    if (dailyStart < 0) dailyStart = i;
    if (i < dailyStart + 15) console.log(i, 'R0:', h0, 'R1:', h1);
  }
}
console.log('dailyStart', dailyStart, 'total cols', row0.length);
// find cols with any data in first 10 data rows
const counts = {};
rows.slice(2, 12).forEach((r) => {
  for (let i = 40; i < r.length; i++) {
    if (r[i] !== '' && r[i] != null) counts[i] = (counts[i] || 0) + 1;
  }
});
console.log('cols with data 40+:', Object.keys(counts).slice(0, 30).map(k => k + ':' + counts[k]).join(', '));
