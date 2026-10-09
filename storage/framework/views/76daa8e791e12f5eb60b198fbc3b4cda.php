<div id="contract-photos-section" class="contract-photos-section" data-contract-photos>
    <div class="section-title">Hotel Photos</div>
    <?php if($photos->isNotEmpty()): ?>
        <div class="contract-photos-grid">
            <?php $__currentLoopData = $photos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $photo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $photoSrc = ! empty($embedPhotos)
                        ? \App\Support\BookingContractHtml::photoDataUri($photo)
                        : route('group-bookings.contract.photos.show', [$enquiry, $photo], false);
                ?>
                <?php if($photoSrc): ?>
                    <div class="contract-photo-item">
                        <img
                            src="<?php echo e($photoSrc); ?>"
                            alt="<?php echo e($photo->original_name ?: 'Hotel photo'); ?>"
                            class="contract-photo-img"
                            contenteditable="false"
                        >
                    </div>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php else: ?>
        <p class="contract-photos-empty">No hotel photos attached yet. Use the upload panel above to add photos.</p>
    <?php endif; ?>
</div>
<?php /**PATH C:\wamp64\www\hotel_reservation_portal\resources\views/group-bookings/partials/contract-photos.blade.php ENDPATH**/ ?>