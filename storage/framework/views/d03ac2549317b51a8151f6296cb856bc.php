<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo e($filename); ?></title>
    <style>
        @page { margin: 18px 16px; }
        body {
            margin: 0;
            padding: 0;
            color: #20262d;
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            font-size: 10.5px;
            line-height: 1.45;
        }
        .document { width: 100%; }
        .page {
            page-break-after: always;
            padding: 8px 4px 4px;
        }
        .page:last-child { page-break-after: auto; }
        .header { text-align: center; margin-bottom: 14px; }
        .logo {
            color: #17365d;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: -1px;
        }
        .logo .hotel-logo-img {
            display: block;
            max-height: 56px;
            max-width: 200px;
            margin: 0 auto 4px;
        }
        .logo small {
            display: block;
            font-size: 9px;
            font-weight: 700;
            margin-top: 2px;
        }
        .hotel-address { font-size: 10px; color: #444; }
        .legal-intro {
            margin: 12px 0 14px;
            text-align: center;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0 16px;
        }
        th, td {
            border: 1px solid #b8c2cc;
            padding: 6px 7px;
            vertical-align: top;
            font-size: 10px;
            text-align: left;
        }
        th {
            background: #17365d;
            color: #fff;
            font-size: 10.5px;
            font-weight: 700;
        }
        .two-col th {
            text-align: center;
            background: #eaf0f6;
            color: #17365d;
            font-size: 11px;
        }
        .requirements td:first-child {
            width: 31%;
            font-weight: 700;
            background: #f8fafc;
        }
        .section-title {
            margin: 14px 0 8px;
            padding: 7px 9px;
            background: #17365d;
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .notice {
            padding: 9px 11px;
            background: #f7f9fb;
            border-left: 4px solid #2d5f8b;
            margin: 10px 0 14px;
            font-size: 10px;
        }
        p { font-size: 10px; margin: 7px 0; }
        ul { font-size: 10px; margin: 6px 0 12px 18px; padding: 0; }
        li { margin: 4px 0; }
        .signature-row {
            width: 100%;
            margin: 12px 0 16px;
        }
        .signature-col {
            width: 48%;
            display: inline-block;
            vertical-align: top;
            border: 1px solid #b8c2cc;
            margin-right: 2%;
        }
        .signature-col:last-child { margin-right: 0; }
        .signature-col-title {
            background: #eef2f6;
            color: #17365d;
            font-size: 10px;
            font-weight: 700;
            padding: 7px 8px;
            border-bottom: 1px solid #b8c2cc;
            text-align: center;
        }
        .signature-box {
            height: 120px;
            padding: 8px;
            border-bottom: 1px dashed #aab3bc;
            text-align: center;
            color: #8a929a;
            font-size: 9px;
        }
        .signature-box .signature-img {
            max-width: 95%;
            max-height: 105px;
        }
        .signature-date {
            padding: 7px 8px;
            font-size: 10px;
            border-top: 1px solid #d8dde2;
        }
        .footer {
            margin-top: 12px;
            padding-top: 5px;
            border-top: 1px solid #d8dde2;
            text-align: center;
            color: #6b737c;
            font-size: 8px;
        }
        .field { color: #111; font-weight: 700; }
        .contract-photos-section { margin: 12px 0 14px; }
        .contract-photos-grid { width: 100%; }
        .contract-photo-item {
            width: 48%;
            display: inline-block;
            vertical-align: top;
            border: 1px solid #b8c2cc;
            background: #f8fafc;
            padding: 5px;
            margin: 0 1% 8px 0;
            text-align: center;
        }
        .contract-photo-img {
            max-width: 100%;
            max-height: 150px;
        }
        .contract-photos-empty {
            font-size: 10px;
            color: #6b737c;
            margin: 6px 0 0;
        }
        .contract-add-row,
        .contract-remove-row { display: none !important; }
    </style>
</head>
<body>
<?php echo $contractHtml; ?>

</body>
</html>
<?php /**PATH C:\wamp64\www\hotel_reservation_portal\resources\views/group-bookings/contract-pdf.blade.php ENDPATH**/ ?>