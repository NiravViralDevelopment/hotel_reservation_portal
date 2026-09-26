const XLSX = require('xlsx');
const file = 'c:/wamp64/www/hotel_reservation_portal/storage/app/imports/bw-group-tracker-2025.xlsx';
console.log('start');
const wb = XLSX.readFile(file, { bookSheets: true });
console.log('sheets:', wb.SheetNames.length);
wb.SheetNames.forEach((n) => console.log('-', n));
