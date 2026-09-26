const XLSX = require('xlsx');
const wb = XLSX.readFile('c:\\Users\\viral\\Downloads\\Group Tracker 2026.xlsx', { cellDates: true });
const ws = wb.Sheets["June'26"];
const rows = XLSX.utils.sheet_to_json(ws, { header: 1, defval: '' });
const row1 = rows[0];
const row2 = rows[1];
// show cols 38-55 headers and first data row values
const dataRow = rows.find((r, i) => i >= 2 && String(r[0]).trim() && /\d/.test(String(r[0])));
console.log('Sample block', dataRow[4]);
for (let i = 38; i <= 60; i++) {
  console.log(i, 'R1:', row1[i], '| R2:', row2[i], '| val:', dataRow[i]);
}
