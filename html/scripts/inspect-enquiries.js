const XLSX = require('xlsx');
const wb = XLSX.readFile('c:\\Users\\viral\\Downloads\\Group Tracker 2026.xlsx', { cellDates: true });
['Enquiries 2026', 'Enquiries 2027'].forEach((name) => {
  const rows = XLSX.utils.sheet_to_json(wb.Sheets[name], { header: 1, defval: '', raw: false });
  console.log('\n===', name, '===');
  console.log('Header:', rows[0].map((c, i) => (c ? i + ':' + c : '')).filter(Boolean).join(' | '));
  console.log('First 8 rows col0-3:');
  rows.slice(1, 9).forEach((r, i) => console.log(i + 1, r[0], '|', r[1], '|', r[2], '|', r[3]));
  let n = 0;
  rows.slice(1).forEach((r) => {
    const g = String(r[3] || '').trim();
    if (g) n++;
  });
  console.log('Rows with Group Name:', n);
});
