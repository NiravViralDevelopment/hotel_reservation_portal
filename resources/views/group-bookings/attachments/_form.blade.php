@php
  $currentKind = (isset($attachment) && $attachment->canPreview()) ? $attachment->previewKind() : null;
  $currentUrl = $currentKind ? $attachment->fileUrl($enquiry) : '';
  $currentName = isset($attachment) ? ($attachment->original_name ?: 'Attachment') : '';
@endphp

<div class="row g-3">
  <div class="col-md-6">
    <label for="title" class="form-label">Title</label>
    <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $attachment->title ?? '') }}" maxlength="255">
    @error('title')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
  </div>
  <div class="col-md-6">
    <label for="date" class="form-label">Date</label>
    <input type="date" name="date" id="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date', isset($attachment) ? $attachment->attachment_date?->format('Y-m-d') : '') }}">
    @error('date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
  </div>
  <div class="col-12">
    <label for="feedback" class="form-label">Feedback</label>
    <textarea name="feedback" id="feedback" rows="4" class="form-control @error('feedback') is-invalid @enderror" maxlength="5000">{{ old('feedback', $attachment->feedback ?? '') }}</textarea>
    @error('feedback')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
  </div>
  <div class="col-12">
    <label for="attachment" class="form-label">Attachment</label>
    @if (isset($attachment) && $attachment->hasFile())
      <div class="form-text mb-2">
        Current file:
        <a href="{{ $attachment->fileUrl($enquiry) }}" target="_blank" rel="noopener">{{ $currentName }}</a>
      </div>
    @endif
    <input type="file" name="attachment" id="attachment" class="form-control @error('attachment') is-invalid @enderror">
    <div class="form-text">
      Any file, up to 10 MB. PDF and image files show a preview below.
      @if (isset($attachment))
        Leave this blank to keep the current file.
      @endif
    </div>
    @error('attachment')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror

    <div
      id="attachmentPreview"
      class="attachment-preview mt-3"
      @if ($currentUrl === '') hidden @endif
      data-current-url="{{ $currentUrl }}"
      data-current-kind="{{ $currentKind }}"
      data-current-name="{{ $currentName }}"
    >
      <div id="attachmentPreviewLabel" class="small text-secondary mb-2">{{ $currentName }}</div>
      <img
        id="attachmentPreviewImage"
        alt="{{ $currentName !== '' ? $currentName : 'Attachment preview' }}"
        @if ($currentKind === 'image') src="{{ $currentUrl }}" @else hidden @endif
      >
      <iframe
        id="attachmentPreviewPdf"
        title="PDF preview"
        @if ($currentKind === 'pdf') src="{{ $currentUrl }}" @else hidden @endif
      ></iframe>
      <div id="attachmentPreviewOther" class="text-secondary small" hidden></div>
    </div>
  </div>
</div>

@once
  @push('styles')
    <style>
      .attachment-preview {
        border: 1px solid var(--bs-border-color, #dee2e6);
        border-radius: 0.5rem;
        background: #f8f9fa;
        padding: 0.75rem;
        max-width: 720px;
      }
      .attachment-preview img {
        display: block;
        max-width: 100%;
        max-height: 420px;
        object-fit: contain;
        margin: 0 auto;
        background: #fff;
        border-radius: 0.375rem;
      }
      .attachment-preview iframe {
        display: block;
        width: 100%;
        height: 480px;
        border: 0;
        background: #fff;
        border-radius: 0.375rem;
      }
    </style>
  @endpush

  @push('scripts')
    <script>
      (function () {
        var input = document.getElementById('attachment');
        var box = document.getElementById('attachmentPreview');
        var image = document.getElementById('attachmentPreviewImage');
        var pdf = document.getElementById('attachmentPreviewPdf');
        var other = document.getElementById('attachmentPreviewOther');
        var label = document.getElementById('attachmentPreviewLabel');
        if (!input || !box || !image || !pdf || !other || !label) return;

        var currentUrl = box.getAttribute('data-current-url') || '';
        var currentKind = box.getAttribute('data-current-kind') || '';
        var currentName = box.getAttribute('data-current-name') || '';
        var objectUrl = null;

        function hideParts() {
          image.hidden = true;
          pdf.hidden = true;
          other.hidden = true;
          image.removeAttribute('src');
          pdf.removeAttribute('src');
          other.textContent = '';
        }

        function show(url, kind, name) {
          hideParts();
          label.textContent = name || '';
          label.hidden = !name;

          if (kind !== 'image' && kind !== 'pdf') {
            if (name) {
              other.hidden = false;
              other.textContent = 'Preview is available for PDF and image files. Selected: ' + name;
              box.hidden = false;
            } else {
              box.hidden = true;
            }
            return;
          }

          box.hidden = false;
          if (kind === 'image') {
            image.hidden = false;
            image.alt = name || 'Attachment preview';
            image.src = url;
            return;
          }

          pdf.hidden = false;
          pdf.src = url;
        }

        function kindFromFile(file) {
          var type = (file.type || '').toLowerCase();
          var name = (file.name || '').toLowerCase();
          if (type.indexOf('image/') === 0 || /\.(png|jpe?g|gif|webp|bmp|svg)$/.test(name)) return 'image';
          if (type === 'application/pdf' || /\.pdf$/.test(name)) return 'pdf';
          return 'other';
        }

        input.addEventListener('change', function () {
          if (objectUrl) {
            URL.revokeObjectURL(objectUrl);
            objectUrl = null;
          }

          var file = input.files && input.files[0];
          if (!file) {
            if (currentUrl) {
              show(currentUrl, currentKind, currentName);
            } else {
              hideParts();
              label.textContent = '';
              box.hidden = true;
            }
            return;
          }

          var kind = kindFromFile(file);
          if (kind === 'image' || kind === 'pdf') {
            objectUrl = URL.createObjectURL(file);
            show(objectUrl, kind, file.name);
            return;
          }

          show('', 'other', file.name);
        });
      })();
    </script>
  @endpush
@endonce
