const XLSX = require('xlsx');
const wb = XLSX.readFile('c:\\Users\\viral\\Downloads\\Group Tracker 2026.xlsx', { cellDates: true });
['May\'26', "June'26", 'Aug 2026'].forEach((name) => {
  const rows = XLSX.utils.sheet_to_json(wb.Sheets[name], { header: 1, defval: '', raw: true });
  const r0 = rows[0], r1 = rows[1];
  const dateCols = [];
  for (let i = 0; i < Math.min(r0.length, r1.length, 422); i++) {
    const a = String(r0[i] ?? '');
    const b = String(r1[i] ?? '');
    if (/\d{1,2}[\/-]\d|Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec/i.test(a + b) && i > 38) {
      dateCols.push({ i, r0: a.slice(0, 30), r1: b.slice(0, 30) });
    }
  }
  console.log('\n' + name + ' date-like cols after 38:', dateCols.length);
  dateCols.slice(0, 8).forEach((c) => console.log(' ', c.i, c.r0, '|', c.r1));
  // row with max filled cols after 38
  let best = null, bestN = 0;
  rows.slice(2).forEach((r) => {
    let n = 0;
    for (let i = 39; i < r.length; i++) if (r[i] !== '' && r[i] != null) n++;
    if (n > bestN) { bestN = n; best = r; }
  });
  if (best) {
    console.log('  richest row block', best[4], 'filled cols 39+:', bestN);
    for (let i = 38; i < 50; i++) if (best[i] !== '' && best[i] != null) console.log('   ', i, best[i]);
  }
});
