const XLSX = require('xlsx');
const wb = XLSX.readFile('c:\\Users\\viral\\Downloads\\Group Tracker 2026.xlsx', { cellDates: true });
const rows = XLSX.utils.sheet_to_json(wb.Sheets['Cancelled Bookings'], { header: 1, defval: '' });
console.log('R0 sections:', rows[0].map((c,i)=>c?i+':'+c:'').filter(Boolean).join(' | '));
console.log('R1 headers:');
rows[1].forEach((c, i) => { if (String(c).trim()) console.log(i, c); });
console.log('Data rows:', rows.slice(2).filter(r => String(r[0]).trim() && /\d/.test(String(r[0]))).length);
