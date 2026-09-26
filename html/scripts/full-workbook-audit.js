/**
 * FULL workbook audit — every sheet, row, column, merge, hidden, formula, comment.
 * Source of truth: Group Tracker 2026.xlsx
 */
const XLSX = require('xlsx');
const fs = require('fs');
const path = require('path');

const file = 'c:\\Users\\viral\\Downloads\\Group Tracker 2026.xlsx';
const outFile = path.join(__dirname, '..', 'scripts', 'workbook-audit-report.json');

const wb = XLSX.readFile(file, {
  cellDates: true,
  sheetStubs: true,
  bookVBA: true,
  cellFormula: true,
  cellNF: true,
  cellStyles: true,
});

function colLetter(c) {
  let s = '';
  c++;
  while (c > 0) {
    const m = (c - 1) % 26;
    s = String.fromCharCode(65 + m) + s;
    c = Math.floor((c - 1) / 26);
  }
  return s;
}

function cellAddr(r, c) {
  return colLetter(c) + (r + 1);
}

function getHiddenRows(ws) {
  const hidden = [];
  if (ws['!rows']) {
    ws['!rows'].forEach((row, i) => {
      if (row && row.hidden) hidden.push(i + 1);
    });
  }
  return hidden;
}

function getHiddenCols(ws) {
  const hidden = [];
  if (ws['!cols']) {
    ws['!cols'].forEach((col, i) => {
      if (col && col.hidden) hidden.push({ index: i, letter: colLetter(i) });
    });
  }
  return hidden;
}

function getMerges(ws) {
  return (ws['!merges'] || []).map((m) => ({
    range: XLSX.utils.encode_range(m),
    start: cellAddr(m.s.r, m.s.c),
    end: cellAddr(m.e.r, m.e.c),
    rows: m.e.r - m.s.r + 1,
    cols: m.e.c - m.s.c + 1,
  }));
}

function getComments(ws) {
  const comments = [];
  Object.keys(ws).forEach((key) => {
    if (key[0] === '!') return;
    const cell = ws[key];
    if (cell && cell.c) {
      comments.push({
        addr: key,
        text: (cell.c || []).map((c) => c.t).join(' '),
      });
    }
  });
  return comments;
}

function getFormulas(ws, range) {
  const formulas = [];
  for (let r = range.s.r; r <= range.e.r; r++) {
    for (let c = range.s.c; c <= range.e.c; c++) {
      const addr = cellAddr(r, c);
      const cell = ws[addr];
      if (cell && cell.f) {
        formulas.push({ addr, formula: cell.f, value: cell.v });
      }
    }
  }
  return formulas;
}

function countNonEmptyRows(rows) {
  return rows.filter((r) => r.some((c) => String(c ?? '').trim() !== '')).length;
}

function detectHeaderRows(rows, maxScan = 5) {
  const candidates = [];
  for (let i = 0; i < Math.min(rows.length, maxScan); i++) {
    const nonEmpty = rows[i].filter((c) => String(c ?? '').trim() !== '').length;
    if (nonEmpty >= 2) {
      candidates.push({ rowIndex: i + 1, nonEmptyCount: nonEmpty, cells: rows[i] });
    }
  }
  return candidates;
}

function buildColumnMap(rows, headerRowCount) {
  const row1 = rows[0] || [];
  const row2 = rows[1] || [];
  const useDualHeader = headerRowCount >= 2 && countNonEmptyRows([row2]) >= 5;

  if (useDualHeader) {
    const maxLen = Math.max(row1.length, row2.length, ...rows.slice(0, 3).map((r) => r.length));
    const headers = [];
    for (let i = 0; i < maxLen; i++) {
      const h2 = String(row2[i] ?? '').trim();
      const h1 = String(row1[i] ?? '').trim();
      let section = h1;
      if (!section) {
        for (let j = i; j >= 0; j--) {
          if (String(row1[j] ?? '').trim()) {
            section = String(row1[j]).trim();
            break;
          }
        }
      }
      if (h2) headers.push(h2);
      else if (section) headers.push(`${section} [col${i}]`);
      else headers.push(`Col${i + 1}`);
    }
    return { headerRows: 2, headers, headerRow1: row1, headerRow2: row2 };
  }

  const headerIdx = detectHeaderRows(rows)[0]?.rowIndex - 1 || 0;
  const headers = (rows[headerIdx] || []).map((h, i) => {
    const v = String(h ?? '').trim();
    return v || `Col${i + 1}`;
  });
  return { headerRows: 1, headers, headerRow1: rows[headerIdx] || [] };
}

const report = {
  file,
  analysedAt: new Date().toISOString(),
  sheetCount: wb.SheetNames.length,
  sheetNames: wb.SheetNames,
  sheets: {},
};

wb.SheetNames.forEach((name) => {
  const ws = wb.Sheets[name];
  const range = ws['!ref'] ? XLSX.utils.decode_range(ws['!ref']) : { s: { r: 0, c: 0 }, e: { r: 0, c: 0 } };
  const rows = XLSX.utils.sheet_to_json(ws, { header: 1, defval: '', raw: false });
  const hiddenRows = getHiddenRows(ws);
  const hiddenCols = getHiddenCols(ws);
  const merges = getMerges(ws);
  const comments = getComments(ws);
  const formulas = getFormulas(ws, range);

  const isMonthlyBooking =
    /'26$/.test(name) ||
    /2026$/.test(name) ||
    ['Jan\'26', 'Feb\'26', "March'26", "April'26", "May'26", "June'26"].includes(name);

  const colMap = isMonthlyBooking
    ? buildColumnMap(rows, 2)
    : buildColumnMap(rows, 1);

  const dataStartRow = colMap.headerRows;
  const dataRows = rows.slice(dataStartRow);
  const nonEmptyDataRows = dataRows.filter((r) => r.some((c) => String(c ?? '').trim() !== ''));

  // Monthly sheets: exclude totals/header repeats
  let validDataRows = nonEmptyDataRows.length;
  if (isMonthlyBooking) {
    validDataRows = dataRows.filter((r) => {
      const arrival = String(r[0] ?? '').trim();
      if (!arrival) return false;
      if (arrival === 'Total') return false;
      if (/^Date of/i.test(arrival)) return false;
      if (!/\d/.test(arrival)) return false;
      return true;
    }).length;
  }

  // Unique block ids for monthly
  let uniqueBlockIds = null;
  if (isMonthlyBooking) {
    const ids = new Set();
    dataRows.forEach((r) => {
      const arrival = String(r[0] ?? '').trim();
      if (!arrival || arrival === 'Total' || /^Date of/i.test(arrival) || !/\d/.test(arrival)) return;
      const id = String(r[4] ?? '').trim();
      if (id) ids.add(id);
    });
    uniqueBlockIds = ids.size;
  }

  report.sheets[name] = {
    dimensions: {
      rows: range.e.r + 1,
      cols: range.e.c + 1,
      ref: ws['!ref'] || 'A1',
    },
    totalArrayRows: rows.length,
    nonEmptyRows: countNonEmptyRows(rows),
    headerRowCount: colMap.headerRows,
    columnCount: colMap.headers.length,
    columns: colMap.headers.map((h, i) => ({ index: i, letter: colLetter(i), name: h })),
    headerRow1: colMap.headerRow1,
    headerRow2: colMap.headerRow2 || null,
    dataRowCount: validDataRows,
    rawNonEmptyAfterHeader: nonEmptyDataRows.length,
    uniqueBlockIds,
    hiddenRows: { count: hiddenRows.length, rows: hiddenRows.slice(0, 50), truncated: hiddenRows.length > 50 },
    hiddenCols: { count: hiddenCols.length, cols: hiddenCols },
    merges: { count: merges.length, ranges: merges.slice(0, 30), truncated: merges.length > 30 },
    formulas: { count: formulas.length, samples: formulas.slice(0, 20), truncated: formulas.length > 20 },
    comments: { count: comments.length, items: comments },
    last5DataRows: nonEmptyDataRows.slice(-5).map((r) =>
      colMap.headers
        .map((h, i) => (String(r[i] ?? '').trim() ? { field: h, value: String(r[i]).trim().slice(0, 120) } : null))
        .filter(Boolean)
        .slice(0, 12)
    ),
  };
});

fs.writeFileSync(outFile, JSON.stringify(report, null, 2));

// Console summary
console.log('='.repeat(70));
console.log('FULL WORKBOOK AUDIT — Group Tracker 2026.xlsx');
console.log('='.repeat(70));
console.log('Sheets:', wb.SheetNames.length);
console.log('Report written to:', outFile);
console.log('');

wb.SheetNames.forEach((name) => {
  const s = report.sheets[name];
  console.log('-'.repeat(70));
  console.log(`SHEET: "${name}"`);
  console.log(`  Dimensions: ${s.dimensions.rows} rows × ${s.dimensions.cols} cols (${s.dimensions.ref})`);
  console.log(`  Header rows: ${s.headerRowCount} | Columns: ${s.columnCount}`);
  console.log(`  Data rows (valid): ${s.dataRowCount} | Raw non-empty after header: ${s.rawNonEmptyAfterHeader}`);
  if (s.uniqueBlockIds != null) console.log(`  Unique Block Ids: ${s.uniqueBlockIds}`);
  console.log(`  Hidden rows: ${s.hiddenRows.count} | Hidden cols: ${s.hiddenCols.count}`);
  console.log(`  Merged ranges: ${s.merges.count} | Formulas: ${s.formulas.count} | Comments: ${s.comments.count}`);
  console.log('  ALL COLUMNS:');
  s.columns.forEach((c) => console.log(`    [${c.index}] ${c.letter}: ${c.name}`));
});

console.log('\n' + '='.repeat(70));
console.log('MODULE MAPPING SUMMARY');
console.log('='.repeat(70));
const mapping = [
  { module: 'Group Bookings (monthly register)', sheets: wb.SheetNames.filter((n) => /'26|2026/.test(n) && !/Enquir|Cancel|BOB|Contact/i.test(n)) },
  { module: 'Dashboard / Revenue (BOB)', sheets: ['BOB'] },
  { module: 'Enquiries', sheets: ['Enquiries 2026', 'Enquiries 2027'] },
  { module: 'Cancelled Bookings', sheets: ['Cancelled Bookings'] },
  { module: 'Cancelled Enquiries', sheets: ['Cancelled Enquiries'] },
  { module: 'Contacts', sheets: ['Contacts & Legends'] },
];
mapping.forEach((m) => {
  let total = 0;
  m.sheets.forEach((sn) => {
    if (report.sheets[sn]) total += report.sheets[sn].dataRowCount;
  });
  console.log(`${m.module}: ${m.sheets.join(', ')} → ${total} Excel data rows`);
});
