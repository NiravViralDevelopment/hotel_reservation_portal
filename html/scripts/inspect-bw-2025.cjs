const XLSX = require('xlsx');
const file = 'c:\\Users\\viral\\Downloads\\BW Group Tracker 2025.xlsx';
const wb = XLSX.readFile(file, { cellDates: true, sheetStubs: true });
console.log(JSON.stringify({
  sheets: wb.SheetNames,
  counts: wb.SheetNames.map((name) => {
    const ws = wb.Sheets[name];
    const rows = XLSX.utils.sheet_to_json(ws, { header: 1, defval: '' });
    const header = (rows[0] || []).map((h, i) => String(h || '').trim() || `Col${i + 1}`).filter((h, i) => h !== `Col${i + 1}` || i < 5);
    return { name, rows: rows.length, cols: rows[0] ? rows[0].length : 0, headerSample: (rows[0] || []).slice(0, 12).map(String) };
  })
}, null, 2));
