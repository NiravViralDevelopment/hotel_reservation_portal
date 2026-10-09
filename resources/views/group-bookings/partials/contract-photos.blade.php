<div id="contract-photos-section" class="contract-photos-section" data-contract-photos>
    <div class="section-title">Hotel Photos</div>
    @if ($photos->isNotEmpty())
        <div class="contract-photos-grid">
            @foreach ($photos as $photo)
                @php
                    $photoSrc = ! empty($embedPhotos)
                        ? \App\Support\BookingContractHtml::photoDataUri($photo)
                        : route('group-bookings.contract.photos.show', [$enquiry, $photo], false);
                @endphp
                @if ($photoSrc)
                    <div class="contract-photo-item">
                        <img
                            src="{{ $photoSrc }}"
                            alt="{{ $photo->original_name ?: 'Hotel photo' }}"
                            class="contract-photo-img"
                            contenteditable="false"
                        >
                    </div>
                @endif
            @endforeach
        </div>
    @else
        <p class="contract-photos-empty">No hotel photos attached yet. Use the upload panel above to add photos.</p>
    @endif
</div>
