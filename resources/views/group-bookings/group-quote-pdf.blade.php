<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>{{ $filename }}</title>
  <style>
    @page { margin: 28px 32px; }
    body {
      margin: 0;
      padding: 0;
      color: #1f2933;
      font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
      font-size: 11px;
      line-height: 1.55;
    }
    .letter { width: 100%; }
    p { margin: 0 0 10px; }
    .greeting { margin-bottom: 14px; }
    .hotel-line { margin: 12px 0 16px; }
    .ref {
      font-weight: 700;
      margin: 4px 0 18px;
    }
    h2 {
      margin: 18px 0 8px;
      font-size: 12.5px;
      color: #02225a;
      text-transform: uppercase;
      letter-spacing: 0.02em;
      border-bottom: 1px solid #c5d0dc;
      padding-bottom: 4px;
    }
    table.rates {
      width: 100%;
      border-collapse: collapse;
      margin: 8px 0 10px;
    }
    table.rates th,
    table.rates td {
      border: 1px solid #b8c2cc;
      padding: 7px 8px;
      text-align: left;
      vertical-align: top;
      font-size: 10.5px;
    }
    table.rates th {
      background: #02225a;
      color: #fff;
      font-weight: 700;
    }
    .note {
      font-size: 10px;
      font-style: italic;
      color: #4b5563;
      margin: 0 0 14px;
    }
    ul.details {
      margin: 6px 0 12px 18px;
      padding: 0;
    }
    ul.details li {
      margin: 4px 0;
    }
    .section {
      margin-top: 16px;
    }
    .service-title {
      font-weight: 700;
      margin: 10px 0 2px;
    }
    .closing {
      margin-top: 18px;
    }
    .meta {
      margin-top: 28px;
      font-size: 9px;
      color: #6b7280;
    }
  </style>
</head>
<body>
  <div class="letter">
    <p class="greeting">Dear {{ $guestName }},</p>
    <p>I hope you are doing well.</p>
    <p>Thank you for your Enquiry.</p>
    <p class="hotel-line">Please find below quotation for the <strong>{{ $hotelName }}</strong>.</p>
    <p class="ref">Group Reference - {{ $groupReference }}</p>

    <h2>Accommodation Requirements and Rates</h2>
    <table class="rates">
      <thead>
        <tr>
          <th>Check In Date</th>
          <th>Check out Date</th>
          <th>Room Nights</th>
          <th>BB Rates</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($rateRows as $row)
          <tr>
            <td>{{ $row['check_in'] }}</td>
            <td>{{ $row['check_out'] }}</td>
            <td>{{ $row['nights'] }}</td>
            <td>{{ $row['rates'] }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
    @if ($showNoTripleNote)
      <p class="note">*We don’t have the triple rooms in our hotel.</p>
    @endif

    <h2>Quotation Details</h2>
    <ul class="details">
      <li><strong>Rooms Offered:</strong> {{ $roomsOffered }}</li>
      <li><strong>Room Type:</strong> {{ $roomType }}</li>
      <li><strong>Cancellation Policy:</strong> {{ $cancellationPolicy }}</li>
      <li><strong>Payment:</strong> {{ $paymentLabel }}</li>
      <li><strong>Option date:</strong> {{ $optionDate }}</li>
      <li><strong>Commissionable:</strong> {{ $commissionable }}</li>
    </ul>
    <p>{{ $basisNote }}</p>

    <div class="section">
      <h2>Additional Services</h2>

      <div class="service-title">Porterage</div>
      <ul class="details">
        <li>£1.5 per bag each way.</li>
      </ul>

      <div class="service-title">Parking</div>
      <ul class="details">
        <li>Parking is available on first come first serve basis and additional charge of £12 per car per night.</li>
        <li>Coach can be parked at £30 per night.</li>
      </ul>

      <div class="service-title">Meal</div>
      <ul class="details">
        <li>Dinner: 3 course meal at {{ $dinnerRate ?: '£30' }} per person.</li>
      </ul>
    </div>

    <p>Please note that the above rates are quoted for the specified dates and room requirements and are subject to availability until the booking is confirmed.</p>
    <p>If any Event is announced on the requested dates after the quote has been sent to the client, the hotel can apply the additional surcharge on the quoted rates. The surcharged amount will be at the discretion of the hotel.</p>
    <p class="closing">Should you require any further information or clarification, please do not hesitate to contact us.</p>

    <p class="meta">Generated on {{ $generatedOn }}</p>
  </div>
</body>
</html>
