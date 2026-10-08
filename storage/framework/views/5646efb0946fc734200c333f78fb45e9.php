<div class="document hotel-contract-document">
    <section class="page">
        <header class="header">
            <div class="logo">HOTEL <small>GROUP CONTRACT</small></div>
            <div class="hotel-address"><?php echo e($hotelAddress); ?></div>
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
                    <span class="field"><?php echo e($hotelName); ?></span>
                </td>
                <td>
                    <strong>Company / Name</strong><br>
                    <span class="field"><?php echo e($clientName); ?></span>
                </td>
            </tr>
            <tr>
                <td><strong>Address</strong><br><span><?php echo e($hotelStreet); ?></span></td>
                <td><strong>Address</strong><br><span><?php echo e($clientAddress); ?></span></td>
            </tr>
            <tr>
                <td><strong>Postcode</strong><br><span><?php echo e($hotelPostcode); ?></span></td>
                <td><strong>Postcode</strong><br><span><?php echo e($clientPostcode); ?></span></td>
            </tr>
            <tr>
                <td><strong>Contact</strong><br><span><?php echo e($hotelContact); ?></span></td>
                <td><strong>Contact</strong><br><span><?php echo e($clientContact); ?></span></td>
            </tr>
            <tr>
                <td><strong>Role</strong><br><span><?php echo e($hotelRole); ?></span></td>
                <td><strong>Role</strong><br><span><?php echo e($clientRole); ?></span></td>
            </tr>
            <tr>
                <td><strong>Phone</strong><br><span><?php echo e($hotelPhone); ?></span></td>
                <td><strong>Phone</strong><br><span><?php echo e($clientPhone); ?></span></td>
            </tr>
            <tr>
                <td><strong>Email</strong><br><span><?php echo e($hotelEmail); ?></span></td>
                <td><strong>Email</strong><br><span><?php echo e($clientEmail); ?></span></td>
            </tr>
        </table>

        <div class="notice">
            The below details represent the requirements the hotel has agreed to provide as requested by the
            client and shall enter definitively into force only after a copy of this proposal of services is
            signed, dated, and returned by the client to the hotel.
        </div>

        <div class="section-title">Group &amp; Commercial Details</div>

        <table class="requirements">
            <tr><td>Group Name</td><td><?php echo e($groupName); ?></td></tr>
            <tr><td>Group Arrival Date</td><td><?php echo e($arrivalDate); ?></td></tr>
            <tr><td>Total Rooms</td><td><?php echo e($totalRooms); ?></td></tr>
            <tr><td>Number of Nights</td><td><?php echo e($nightsLabel); ?></td></tr>
            <tr><td>Rate Per Room</td><td><?php echo e($rateLabel); ?></td></tr>
            <tr><td>Preferred Payment</td><td><?php echo e($paymentLabel); ?></td></tr>
            <tr><td>Cancellation Leeway</td><td><?php echo e($cancellationLabel); ?></td></tr>
        </table>

        <p><strong>Tax &amp; Pricing:</strong> All prices detailed above are inclusive of VAT at the applicable rate. There are currently no other resort, city, or applicable taxes.</p>

        <ul>
            <?php $__currentLoopData = $extras; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $extra): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($extra); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>

        <div class="footer">
            <strong><?php echo e($legalName); ?></strong> ·
            Registered Office: <span><?php echo e($registeredOffice); ?></span> ·
            Registered No: <span><?php echo e($regNumber); ?></span> ·
            VAT No: <span><?php echo e($vatNumber); ?></span>
        </div>
    </section>

    <section class="page">
        <header class="header">
            <div class="logo">HOTEL <small>GROUP CONTRACT</small></div>
            <div class="hotel-address"><?php echo e($hotelAddress); ?></div>
        </header>

        <div class="section-title">Cancellation Policy</div>
        <p>The following cancellation policy applies unless otherwise stated in writing by an authorized member of the Hotel team.</p>
        <p><strong>Group Cancellation:</strong> In the event of a group cancellation occurring <span><?php echo e($cancellationWindow); ?></span> prior to arrival, liquidated damages of <span>100%</span> of the Room Night Revenue Commitment will be due, plus applicable taxes.</p>
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
            <strong><?php echo e($legalName); ?></strong> ·
            Registered Office: <span><?php echo e($registeredOffice); ?></span> ·
            Registered No: <span><?php echo e($regNumber); ?></span> ·
            VAT No: <span><?php echo e($vatNumber); ?></span>
        </div>
    </section>

    <section class="page">
        <header class="header">
            <div class="logo">HOTEL <small>GROUP CONTRACT</small></div>
            <div class="hotel-address"><?php echo e($hotelAddress); ?></div>
        </header>

        <div class="section-title">Group Payment Terms</div>
        <p>Hotel requires all group bookings to be secured by full payment no less than <strong><?php echo e($paymentWindow); ?></strong> prior to arrival.</p>
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
        <table class="signature-table">
            <tr>
                <th>Approved &amp; Authorized by the Hotel</th>
                <th>Approved &amp; Authorized by the Client</th>
            </tr>
            <tr>
                <td>
                    <strong>Name:</strong> <span><?php echo e($hotelSignName); ?></span><br>
                    <strong>Title:</strong> <span><?php echo e($hotelSignTitle); ?></span>
                    <div class="signature-box">Hotel Signature</div>
                </td>
                <td>
                    <strong>Name:</strong> <span><?php echo e($clientSignName); ?></span><br>
                    <strong>Title:</strong> <span><?php echo e($clientSignTitle); ?></span>
                    <div class="signature-box">Client Signature</div>
                </td>
            </tr>
            <tr>
                <td><strong>Date:</strong> <span><?php echo e($hotelSignDate); ?></span></td>
                <td><strong>Date:</strong> <span><?php echo e($clientSignDate); ?></span></td>
            </tr>
        </table>

        <div class="section-title">Contract Notes</div>
        <p><?php echo e($contractNotes); ?></p>

        <div class="footer">
            <strong><?php echo e($legalName); ?></strong> ·
            Registered Office: <span><?php echo e($registeredOffice); ?></span> ·
            Registered No: <span><?php echo e($regNumber); ?></span> ·
            VAT No: <span><?php echo e($vatNumber); ?></span>
        </div>
    </section>
</div>
<?php /**PATH C:\wamp64\www\hotel_reservation_portal\resources\views/group-bookings/partials/hotel-contract-document.blade.php ENDPATH**/ ?>