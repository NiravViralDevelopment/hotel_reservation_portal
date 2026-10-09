<div class="document hotel-contract-document">
    <section class="page">
        <header class="header">
            <div class="logo">
                @if (! empty($hotelLogoUrl))
                    <img src="{{ $hotelLogoUrl }}" alt="{{ $hotelName }} logo" class="hotel-logo-img" contenteditable="false">
                @else
                    HOTEL
                @endif
                <small>GROUP CONTRACT</small>
            </div>
            <div class="hotel-address">{{ $hotelAddress }}</div>
        </header>

        <div class="legal-intro">
            The following represents a legally binding contract between “The Hotel” &amp; “The Client”
            as detailed below and including any agents acting on their behalf.
        </div>

        <table class="two-col">
            <tr>
                <th>The Hotel</th>
                <th>The Client</th>
            </tr>
            <tr>
                <td>
                    <strong>Company</strong><br>
                    <span class="field">{{ $hotelName }}</span>
                </td>
                <td>
                    <strong>Company / Name</strong><br>
                    <span class="field">{{ $clientName }}</span>
                </td>
            </tr>
            <tr>
                <td><strong>Address</strong><br><span>{{ $hotelStreet }}</span></td>
                <td><strong>Address</strong><br><span>{{ $clientAddress }}</span></td>
            </tr>
            <tr>
                <td><strong>Postcode</strong><br><span>{{ $hotelPostcode }}</span></td>
                <td><strong>Postcode</strong><br><span>{{ $clientPostcode }}</span></td>
            </tr>
            <tr>
                <td><strong>Contact</strong><br><span>{{ $hotelContact }}</span></td>
                <td><strong>Contact</strong><br><span>{{ $clientContact }}</span></td>
            </tr>
            <tr>
                <td><strong>Role</strong><br><span>{{ $hotelRole }}</span></td>
                <td><strong>Role</strong><br><span>{{ $clientRole }}</span></td>
            </tr>
            <tr>
                <td><strong>Phone</strong><br><span>{{ $hotelPhone }}</span></td>
                <td><strong>Phone</strong><br><span>{{ $clientPhone }}</span></td>
            </tr>
            <tr>
                <td><strong>Email</strong><br><span>{{ $hotelEmail }}</span></td>
                <td><strong>Email</strong><br><span>{{ $clientEmail }}</span></td>
            </tr>
        </table>

        <div class="notice">
            The below details represent the requirements the hotel has agreed to provide as requested by the
            client and shall enter definitively into force only after a copy of this proposal of services is
            signed, dated, and returned by the client to the hotel.
        </div>

        <div class="section-title">Group &amp; Commercial Details</div>

        <table class="requirements">
            <tr><td>Group Name</td><td>{{ $groupName }}</td></tr>
            <tr><td>Group Arrival Date</td><td>{{ $arrivalDate }}</td></tr>
            <tr><td>Total Rooms</td><td>{{ $totalRooms }}</td></tr>
            <tr><td>Number of Nights</td><td>{{ $nightsLabel }}</td></tr>
            <tr><td>Rate Per Room</td><td>{{ $rateLabel }}</td></tr>
            <tr><td>Preferred Payment</td><td>{{ $paymentLabel }}</td></tr>
            <tr><td>Cancellation Leeway</td><td>{{ $cancellationLabel }}</td></tr>
        </table>

        <p><strong>Tax &amp; Pricing:</strong> All prices detailed above are inclusive of VAT at the applicable rate. There are currently no other resort, city, or applicable taxes.</p>

        <ul>
            @foreach ($extras as $extra)
                <li>{{ $extra }}</li>
            @endforeach
        </ul>

        @include('group-bookings.partials.contract-photos', [
            'enquiry' => $enquiry,
            'photos' => $contractPhotos ?? collect(),
        ])

        <div class="footer">
            <strong>{{ $legalName }}</strong> ·
            Registered Office: <span>{{ $registeredOffice }}</span> ·
            Registered No: <span>{{ $regNumber }}</span> ·
            VAT No: <span>{{ $vatNumber }}</span>
        </div>
    </section>

    <section class="page">
        <header class="header">
            <div class="logo">
                @if (! empty($hotelLogoUrl))
                    <img src="{{ $hotelLogoUrl }}" alt="{{ $hotelName }} logo" class="hotel-logo-img" contenteditable="false">
                @else
                    HOTEL
                @endif
                <small>GROUP CONTRACT</small>
            </div>
            <div class="hotel-address">{{ $hotelAddress }}</div>
        </header>

        <div class="section-title">Cancellation Policy</div>
        <p>The following cancellation policy applies unless otherwise stated in writing by an authorized member of the Hotel team.</p>
        <p><strong>Group Cancellation:</strong> In the event of a group cancellation occurring <span>{{ $cancellationWindow }}</span> prior to arrival, liquidated damages of <span>100%</span> of the Room Night Revenue Commitment will be due, plus applicable taxes.</p>
        <p>All amendments must be sent to the main contact in writing prior to the release date outlined above. Without such notice, all services will be chargeable. The hotel must also confirm its acceptance of changes in writing.</p>

        <div class="section-title">No-Show / Cancellation Without Notice</div>
        <p>In the event of any no-show or cancellation without notice, the hotel reserves the right to invoice the client the full charge for 100% of the services reserved throughout the length of stay, as detailed in this agreement.</p>

        <div class="section-title">Final Rooming List</div>
        <p>The final rooming list must reach the hotel no later than <strong>10 days</strong> before the arrival date.</p>

        <div class="section-title">Availability of Rooms</div>
        <p>The hotel will endeavor to provide check-in from <strong>3:00 PM</strong> on the day of the group's arrival. Guests arriving before this time should be aware that early check-in cannot be guaranteed.</p>
        <p>Luggage can be stored free of charge at the hotel. Porterage can be organized at <strong>£1.50 per room each way</strong>.</p>
        <p>Check-out time for all guests is <strong>11:00 AM</strong> on the day of departure. Late departures may result in an additional charge per room.</p>

        <div class="section-title">Obligations to Client</div>
        <p>In extraordinary circumstances, the hotel reserves the right to offer partial or total accommodation to group members in a neighboring hotel of an equivalent category without price increase.</p>
        <p>Where this occurs, transfer expenses shall be borne by the hotel, which shall not be liable for payment of any other indemnity whatsoever.</p>

        <div class="notice">
            <strong>Important:</strong> Any amendment to this contract should be documented in writing and accepted by the authorized hotel representative.
        </div>

        <div class="footer">
            <strong>{{ $legalName }}</strong> ·
            Registered Office: <span>{{ $registeredOffice }}</span> ·
            Registered No: <span>{{ $regNumber }}</span> ·
            VAT No: <span>{{ $vatNumber }}</span>
        </div>
    </section>

    <section class="page">
        <header class="header">
            <div class="logo">
                @if (! empty($hotelLogoUrl))
                    <img src="{{ $hotelLogoUrl }}" alt="{{ $hotelName }} logo" class="hotel-logo-img" contenteditable="false">
                @else
                    HOTEL
                @endif
                <small>GROUP CONTRACT</small>
            </div>
            <div class="hotel-address">{{ $hotelAddress }}</div>
        </header>

        <div class="section-title">Group Payment Terms</div>
        <p>Hotel requires all group bookings to be secured by full payment no less than <strong>{{ $paymentWindow }}</strong> prior to arrival.</p>
        <p>The hotel accepts <span>major credit cards and bank transfers</span> as forms of payment. If paying by credit or debit card, the guest/client must complete the payment through the hotel's approved payment process.</p>
        <p>Failure to comply with the payment terms may result in the accommodation being released.</p>
        <p>All credit applications must be made upon confirmation of a group so that the necessary checks can be completed to ensure a swift and prompt payment process.</p>

        <div class="section-title">Bank Account Details</div>
        <p>If the hotel's bank account details change, the hotel will send an official communication. If you receive any communication relating to changes to these details, we strongly recommend contacting your sales or event management contact by phone to verify the accuracy before making any payment.</p>

        <div class="section-title">Jurisdiction</div>
        <p>Any dispute which cannot be settled on an amicable basis relating to the validity, interpretation, or performance of this contract shall be escalated to include legal action in the jurisdiction where the hotel is located.</p>

        <div class="notice">
            We thank you in advance for returning this document signed as soon as possible. If the hotel receives no response from the client within <strong>5 days</strong> regarding the expected return date, room reservations may no longer be guaranteed.
        </div>

        <div class="section-title">Approval &amp; Authorization</div>
        <div class="signature-row">
            <div class="signature-col">
                <div class="signature-col-title">Approved &amp; Authorized by the Hotel</div>
                <div class="signature-box signature-box--hotel">
                    @if (! empty($hotelSignatureUrl))
                        <img src="{{ $hotelSignatureUrl }}" alt="Hotel signature" class="signature-img" contenteditable="false">
                    @else
                        Hotel Signature
                    @endif
                </div>
                <div class="signature-date"><strong>Date:</strong> <span>{{ $hotelSignDate }}</span></div>
            </div>
            <div class="signature-col">
                <div class="signature-col-title">Approved &amp; Authorized by the Client</div>
                <div class="signature-box signature-box--client">Client Signature</div>
                <div class="signature-date"><strong>Date:</strong> <span>{{ $clientSignDate }}</span></div>
            </div>
        </div>

        <div class="section-title">Contract Notes</div>
        <p>{{ $contractNotes }}</p>

        <div class="footer">
            <strong>{{ $legalName }}</strong> ·
            Registered Office: <span>{{ $registeredOffice }}</span> ·
            Registered No: <span>{{ $regNumber }}</span> ·
            VAT No: <span>{{ $vatNumber }}</span>
        </div>
    </section>
</div>
