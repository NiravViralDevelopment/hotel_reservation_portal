const XLSX = require('xlsx');
const path = require('path');

const file = 'c:\\Users\\viral\\Downloads\\Group Tracker 2026.xlsx';
const wb = XLSX.readFile(file, { cellDates: true, sheetStubs: true });

console.log('=== WORKBOOK: Group Tracker 2026 ===\n');
console.log('Sheet count:', wb.SheetNames.length);
console.log('Sheet names:', JSON.stringify(wb.SheetNames, null, 2));
console.log('');

wb.SheetNames.forEach((name) => {
  const ws = wb.Sheets[name];
  const range = XLSX.utils.decode_range(ws['!ref'] || 'A1:A1');
  const rows = XLSX.utils.sheet_to_json(ws, { header: 1, defval: '', raw: false });
  console.log(`\n${'='.repeat(60)}`);
  console.log(`SHEET: "${name}"`);
  console.log(`Dimensions: ${range.e.r + 1} rows x ${range.e.c + 1} cols`);
  console.log(`${'='.repeat(60)}`);

  // Find header row (first row with 3+ non-empty cells)
  let headerRowIdx = 0;
  for (let i = 0; i < Math.min(rows.length, 20); i++) {
    const nonEmpty = rows[i].filter((c) => String(c).trim()).length;
    if (nonEmpty >= 3) {
      headerRowIdx = i;
      break;
    }
  }

  const headers = rows[headerRowIdx].map((h, i) => {
    const v = String(h).trim();
    return v || `Col${i + 1}`;
  });

  console.log('\nHeaders (row ' + (headerRowIdx + 1) + '):');
  headers.forEach((h, i) => {
    if (h && h !== `Col${i + 1}`) console.log(`  [${i}] ${h}`);
  });

  const dataRows = rows.slice(headerRowIdx + 1).filter((r) => r.some((c) => String(c).trim()));
  console.log(`\nData rows (non-empty): ${dataRows.length}`);

  console.log('\nFirst 5 data rows (sample):');
  dataRows.slice(0, 5).forEach((row, ri) => {
    const pairs = headers
      .map((h, i) => (row[i] !== undefined && String(row[i]).trim() ? `${h}=${String(row[i]).trim().slice(0, 60)}` : null))
      .filter(Boolean)
      .slice(0, 15);
    console.log(`  Row ${ri + 1}: ${pairs.join(' | ')}`);
  });

  // Unique values for key columns (first 12 headers)
  console.log('\nDistinct values (sample columns):');
  headers.slice(0, 12).forEach((h, colIdx) => {
    const vals = new Set();
    dataRows.forEach((r) => {
      const v = String(r[colIdx] || '').trim();
      if (v) vals.add(v);
    });
    if (vals.size > 0 && vals.size <= 30) {
      console.log(`  ${h}: [${[...vals].slice(0, 20).join('; ')}${vals.size > 20 ? '...' : ''}] (${vals.size} unique)`);
    } else if (vals.size > 30) {
      console.log(`  ${h}: ${vals.size} unique values`);
    }
  });
});
