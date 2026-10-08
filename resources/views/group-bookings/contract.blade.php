@extends('layouts.app')

@section('title', 'Booking contract')
@section('page', 'group-bookings')

@push('styles')
<style>
  .gb-contract .gb-section-title {
    font-size: 0.9375rem;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  .gb-contract .gb-section-title i { color: var(--brand-accent); }
  .gb-contract .contract-frame {
    width: 100%;
    min-height: 720px;
    border: 1px solid var(--bs-border-color, #dee2e6);
    border-radius: 0.375rem;
    background: #f8f9fa;
  }
  .gb-contract .gb-meta { font-size: 0.8125rem; color: var(--text-muted); }
  .gb-contract .contract-paper-wrap {
    background: #e9ecef;
    border-radius: 0.5rem;
    padding: 1rem;
  }
  .gb-contract .contract-paper {
    background: #fff;
    color: #1f2937;
    max-width: 820px;
    margin: 0 auto;
    min-height: 720px;
    padding: 2rem 2.25rem;
    border-radius: 0.25rem;
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.12);
    outline: none;
    overflow: auto;
  }
  .gb-contract .contract-paper:focus {
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.2), 0 8px 24px rgba(15, 23, 42, 0.12);
  }
  .gb-contract .contract-paper h1 {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0 0 0.75rem;
  }
  .gb-contract .contract-paper h2 {
    font-size: 1.05rem;
    font-weight: 700;
    margin: 1.35rem 0 0.55rem;
    border-bottom: 1px solid #dbe1e8;
    padding-bottom: 0.25rem;
  }
  .gb-contract .contract-paper p,
  .gb-contract .contract-paper li {
    font-size: 0.95rem;
    line-height: 1.55;
    margin-bottom: 0.65rem;
  }
  .gb-contract .contract-paper .contract-lead {
    color: #4b5563;
    font-size: 0.9rem;
  }
  .gb-contract .contract-paper table {
    width: 100%;
    border-collapse: collapse;
    margin: 0.4rem 0 0.8rem;
    font-size: 0.92rem;
  }
  .gb-contract .contract-paper th,
  .gb-contract .contract-paper td {
    border: 1px solid #d7dde5;
    padding: 0.45rem 0.6rem;
    vertical-align: top;
  }
  .gb-contract .contract-paper th {
    width: 34%;
    background: #f5f7fa;
    font-weight: 600;
    text-align: left;
  }
  .gb-contract .editor-hint {
    font-size: 0.8125rem;
    color: var(--text-muted);
  }
</style>
@endpush

@section('content')
@php
  $selectedHotelId = old('hotel_id', $selectedHotel?->id ?? $enquiry->hotel_id);
  $previewUrl = $selectedHotel
    ? route('group-bookings.contract.hotel-preview', ['enquiry' => $enquiry, 'hotel_id' => $selectedHotel->id])
    : null;
  $isPdf = $selectedHotel && $selectedHotel->hasDocument() && (
      str_contains(strtolower((string) $selectedHotel->document_mime_type), 'pdf')
      || str_ends_with(strtolower((string) $selectedHotel->document_original_name), '.pdf')
  );
@endphp

<div class="gb-contract">
  <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('group-bookings.index') }}">Group Bookings</a></li>
          <li class="breadcrumb-item"><a href="{{ route('group-bookings.show', $enquiry) }}">{{ $enquiry->block_id ?: 'Booking' }}</a></li>
          <li class="breadcrumb-item active">Contract</li>
        </ol>
      </nav>
      <h1 class="page-title">Hotel contract</h1>
      <p class="page-subtitle mb-0">
        Hotel PDF paragraphs load into an editable document — change any wording, then save.
        You can also replace the whole PDF. Hotel master file stays untouched.
      </p>
    </div>
    <div class="d-flex flex-wrap gap-2">
      <a href="{{ route('group-bookings.show', $enquiry) }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Back
      </a>
      <a href="{{ route('group-bookings.contract', ['enquiry' => $enquiry, 'hotel_id' => $selectedHotelId, 'reload_html' => 1]) }}" class="btn btn-outline-secondary btn-sm" title="Reload editable text from the hotel PDF">
        <i class="bi bi-arrow-clockwise"></i> Reload from hotel PDF
      </a>
      @if ($enquiry->hasBookingContract())
        <a href="{{ route('group-bookings.contract.download', $enquiry) }}" class="btn btn-outline-secondary btn-sm">
          <i class="bi bi-download"></i> Download booking PDF
        </a>
      @endif
    </div>
  </div>

  <form method="POST" action="{{ route('group-bookings.contract.update', $enquiry) }}" enctype="multipart/form-data" id="gbContractForm">
    @csrf
    <input type="hidden" name="booking_contract_html" id="booking_contract_html" value="">

    <div class="row g-4">
      <div class="col-lg-8">
        <div class="card mb-3">
          <div class="card-body">
            <div class="row g-3 align-items-end">
              <div class="col-md-7">
                <label for="hotel_id" class="form-label">Hotel <span class="text-danger">*</span></label>
                <select name="hotel_id" id="hotel_id" class="form-select select2 @error('hotel_id') is-invalid @enderror" data-placeholder="Select hotel">
                  <option value="">Select hotel</option>
                  @foreach ($hotels as $hotel)
                    <option
                      value="{{ $hotel->id }}"
                      @selected((string) $selectedHotelId === (string) $hotel->id)
                      data-preview-url="{{ route('group-bookings.contract.hotel-preview', ['enquiry' => $enquiry, 'hotel_id' => $hotel->id]) }}"
                      data-has-document="{{ $hotel->hasDocument() ? '1' : '0' }}"
                      data-is-pdf="{{ $hotel->hasDocument() && (str_contains(strtolower((string) $hotel->document_mime_type), 'pdf') || str_ends_with(strtolower((string) $hotel->document_original_name), '.pdf')) ? '1' : '0' }}"
                      data-file-name="{{ $hotel->document_original_name }}"
                      data-reload-url="{{ route('group-bookings.contract', ['enquiry' => $enquiry, 'hotel_id' => $hotel->id, 'reload_html' => 1]) }}"
                    >{{ $hotel->name }}{{ $hotel->hasDocument() ? '' : ' (no contract)' }}</option>
                  @endforeach
                </select>
                @error('hotel_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-5">
                <label for="booking_contract_file" class="form-label">Replace whole PDF</label>
                <input
                  type="file"
                  name="booking_contract_file"
                  id="booking_contract_file"
                  accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                  class="form-control @error('booking_contract_file') is-invalid @enderror"
                >
                @error('booking_contract_file')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="form-text mt-2">Hotel master PDF is never changed. Replace PDF only updates this booking’s copy.</div>
          </div>
        </div>

        <div class="card">
          <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tab-html-btn" data-bs-toggle="tab" data-bs-target="#tab-html" type="button" role="tab">
                  <i class="bi bi-file-earmark-text"></i> Editable contract
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-pdf-btn" data-bs-toggle="tab" data-bs-target="#tab-pdf" type="button" role="tab">
                  <i class="bi bi-file-earmark-pdf"></i> Hotel PDF
                </button>
              </li>
            </ul>
          </div>
          <div class="card-body">
            <div class="tab-content">
              <div class="tab-pane fade show active" id="tab-html" role="tabpanel">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                  <div class="editor-hint">PDF paragraphs appear below — click any paragraph to edit it.</div>
                  <div class="d-flex flex-wrap gap-2 align-items-center">
                    <button type="button" class="btn btn-outline-primary btn-sm" id="loadPdfTextBtn">
                      <i class="bi bi-file-text"></i> Load paragraphs from hotel PDF
                    </button>
                    <div class="btn-group btn-group-sm" role="group" aria-label="Format">
                      <button type="button" class="btn btn-outline-secondary" data-cmd="bold" title="Bold"><i class="bi bi-type-bold"></i></button>
                      <button type="button" class="btn btn-outline-secondary" data-cmd="italic" title="Italic"><i class="bi bi-type-italic"></i></button>
                      <button type="button" class="btn btn-outline-secondary" data-cmd="underline" title="Underline"><i class="bi bi-type-underline"></i></button>
                      <button type="button" class="btn btn-outline-secondary" data-cmd="insertUnorderedList" title="Bullets"><i class="bi bi-list-ul"></i></button>
                    </div>
                  </div>
                </div>
                <div id="pdfTextStatus" class="small text-secondary mb-2" hidden></div>
                <template id="bookingSummaryTemplate">{!! $bookingSummaryHtml ?? '' !!}</template>
                <div class="contract-paper-wrap">
                  <div
                    id="contractEditor"
                    class="contract-paper"
                    contenteditable="true"
                    role="textbox"
                    aria-label="Editable booking contract"
                    data-auto-load-pdf="{{ ! empty($autoLoadPdfText) && $isPdf ? '1' : '0' }}"
                  >{!! $contractHtml !!}</div>
                </div>
                @error('booking_contract_html')<div class="invalid-feedback d-block mt-2">{{ $message }}</div>@enderror
              </div>

              <div class="tab-pane fade" id="tab-pdf" role="tabpanel">
                <div id="contractPreviewWrap">
                  @if ($selectedHotel && $selectedHotel->hasDocument() && $isPdf)
                    <iframe class="contract-frame" id="contractFrame" src="{{ $previewUrl }}" title="Hotel contract preview"></iframe>
                  @elseif ($selectedHotel && $selectedHotel->hasDocument())
                    <div class="alert alert-info mb-0">
                      This hotel contract is not a PDF preview.
                      <a href="{{ $previewUrl }}" target="_blank" rel="noopener">Open / download {{ $selectedHotel->document_original_name }}</a>
                    </div>
                  @else
                    <div class="alert alert-warning mb-0">
                      No contract PDF on this hotel yet. Upload one under Hotels → Edit, or use “Replace whole PDF” above.
                    </div>
                  @endif
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="card mb-3">
          <div class="card-header">
            <h2 class="gb-section-title"><i class="bi bi-info-circle"></i> Group booking</h2>
          </div>
          <div class="card-body">
            <div class="row g-2 small">
              <div class="col-6"><span class="text-secondary">Block ID</span><div class="fw-semibold">{{ $enquiry->block_id ?: '—' }}</div></div>
              <div class="col-6"><span class="text-secondary">Client</span><div class="fw-semibold">{{ $enquiry->client ?: '—' }}</div></div>
              <div class="col-6"><span class="text-secondary">Arrival</span><div class="fw-semibold">{{ $enquiry->check_in?->format('d M Y') ?: '—' }}</div></div>
              <div class="col-6"><span class="text-secondary">Departure</span><div class="fw-semibold">{{ $enquiry->check_out?->format('d M Y') ?: '—' }}</div></div>
              <div class="col-6"><span class="text-secondary">Hotel</span><div class="fw-semibold">{{ $selectedHotel?->name ?: '—' }}</div></div>
              <div class="col-6"><span class="text-secondary">Total Rev</span><div class="fw-semibold">{{ $enquiry->total_revenue !== null ? '£'.number_format((float) $enquiry->total_revenue, 2) : '—' }}</div></div>
            </div>
          </div>
        </div>

        <div class="card mb-3">
          <div class="card-header">
            <h2 class="gb-section-title"><i class="bi bi-pencil-square"></i> Quick fields</h2>
          </div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-12">
                <label for="contract_sent_on" class="form-label">Contract Sent On</label>
                <input type="date" name="contract_sent_on" id="contract_sent_on" class="form-control" value="{{ old('contract_sent_on', optional($enquiry->contract_sent_on)->format('Y-m-d')) }}">
              </div>
              <div class="col-12">
                <label for="contract_received_on" class="form-label">Contract Recd On</label>
                <input type="date" name="contract_received_on" id="contract_received_on" class="form-control" value="{{ old('contract_received_on', optional($enquiry->contract_received_on)->format('Y-m-d')) }}">
              </div>
              <div class="col-12">
                <label for="saved_to_doc" class="form-label">Saved to Doc</label>
                <input type="text" name="saved_to_doc" id="saved_to_doc" class="form-control" value="{{ old('saved_to_doc', $enquiry->saved_to_doc) }}" maxlength="255">
              </div>
              <div class="col-12">
                <label for="payment_term" class="form-label">Payment Term</label>
                <select name="payment_term" id="payment_term" class="form-select">
                  <option value="">—</option>
                  <option value="Pre Arrival" @selected(old('payment_term', $enquiry->payment_term) === 'Pre Arrival')>Pre Arrival</option>
                  <option value="Post Departure" @selected(old('payment_term', $enquiry->payment_term) === 'Post Departure')>Post Departure</option>
                </select>
              </div>
              <div class="col-12">
                <label for="payment_term_days" class="form-label">Payment Term Days</label>
                <input type="text" name="payment_term_days" id="payment_term_days" inputmode="numeric" class="form-control" value="{{ old('payment_term_days', $enquiry->payment_term_days) }}" maxlength="3">
              </div>
              <div class="col-12">
                <label for="payment_due_date" class="form-label">Due Date</label>
                <input type="date" name="payment_due_date" id="payment_due_date" class="form-control" value="{{ old('payment_due_date', optional($enquiry->payment_due_date)->format('Y-m-d')) }}">
              </div>
              <div class="col-12">
                <label for="payment_status" class="form-label">Payment Status</label>
                <input type="text" name="payment_status" id="payment_status" class="form-control" value="{{ old('payment_status', $enquiry->payment_status) }}" maxlength="255">
              </div>
              <div class="col-12">
                <label for="cxl_policy" class="form-label">CXL Policy</label>
                <input type="text" name="cxl_policy" id="cxl_policy" class="form-control" value="{{ old('cxl_policy', $enquiry->cxl_policy) }}" maxlength="255">
              </div>
              <div class="col-12">
                <label for="cxl_due_date" class="form-label">CXL Due Date</label>
                <input type="date" name="cxl_due_date" id="cxl_due_date" class="form-control" value="{{ old('cxl_due_date', optional($enquiry->cxl_due_date)->format('Y-m-d')) }}">
              </div>
              <div class="col-12">
                <label for="booking_contract_notes" class="form-label">Internal notes</label>
                <textarea name="booking_contract_notes" id="booking_contract_notes" rows="3" class="form-control" maxlength="5000">{{ old('booking_contract_notes', $enquiry->booking_contract_notes) }}</textarea>
              </div>
            </div>
          </div>
        </div>

        @if ($enquiry->hasBookingContract())
          <div class="alert alert-success small">
            Booking PDF copy saved
            @if ($enquiry->booking_contract_saved_at)
              on {{ $enquiry->booking_contract_saved_at->format('d M Y H:i') }}
            @endif
            — {{ $enquiry->booking_contract_original_name }}
          </div>
        @endif

        @can('update', $enquiry)
          @unless ($enquiry->is_cancel)
            <button type="submit" class="btn btn-accent w-100">
              <i class="bi bi-check-lg"></i> Save editable contract
            </button>
          @endunless
        @endcan
      </div>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
  (function () {
    var form = document.getElementById('gbContractForm');
    var editor = document.getElementById('contractEditor');
    var htmlInput = document.getElementById('booking_contract_html');
    var select = document.getElementById('hotel_id');
    var wrap = document.getElementById('contractPreviewWrap');
    var loadBtn = document.getElementById('loadPdfTextBtn');
    var statusEl = document.getElementById('pdfTextStatus');

    if (window.pdfjsLib) {
      pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    }

    document.querySelectorAll('[data-cmd]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        document.execCommand(btn.getAttribute('data-cmd'), false, null);
        if (editor) editor.focus();
      });
    });

    if (form && editor && htmlInput) {
      form.addEventListener('submit', function () {
        htmlInput.value = editor.innerHTML;
      });
    }

    function setStatus(text, isError) {
      if (!statusEl) return;
      statusEl.hidden = !text;
      statusEl.textContent = text || '';
      statusEl.className = 'small mb-2 ' + (isError ? 'text-danger' : 'text-secondary');
    }

    function escapeHtml(value) {
      return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
    }

    function selectedOption() {
      if (!select || select.selectedIndex < 0) return null;
      return select.options[select.selectedIndex] || null;
    }

    function splitParagraphs(pageText) {
      return String(pageText || '')
        .replace(/\r/g, '')
        .split(/\n{2,}|\n(?=\s*[A-Z0-9•\-–]|\s*$)/)
        .map(function (chunk) {
          return chunk.replace(/[ \t]+\n/g, ' ').replace(/\n/g, ' ').replace(/\s+/g, ' ').trim();
        })
        .filter(function (chunk) {
          return chunk.length > 0;
        });
    }

    function bookingSummaryHtml() {
      var tpl = document.getElementById('bookingSummaryTemplate');
      return tpl ? tpl.innerHTML : '';
    }

    function buildHtmlFromPages(pages, fileName) {
      var summary = bookingSummaryHtml();
      var html = '<div class="contract-doc-inner">';
      html += '<h1>Hotel Contract</h1>';
      html += '<p class="contract-lead">Paragraphs loaded from <strong>' + escapeHtml(fileName || 'hotel PDF') + '</strong>. Click any paragraph to edit. Saving updates this booking only.</p>';

      if (summary) {
        html += '<div class="contract-booking-summary">' + summary + '</div>';
      }

      pages.forEach(function (page, index) {
        html += '<h2>Page ' + (index + 1) + '</h2>';
        if (!page.length) {
          html += '<p><em>(No extractable text on this page)</em></p>';
          return;
        }
        page.forEach(function (paragraph) {
          html += '<p>' + escapeHtml(paragraph) + '</p>';
        });
      });

      html += '</div>';
      return html;
    }

    function extractPdfParagraphs(url) {
      if (!window.pdfjsLib) {
        return Promise.reject(new Error('PDF reader failed to load.'));
      }

      return pdfjsLib.getDocument({ url: url, withCredentials: true }).promise.then(function (pdf) {
        var chain = Promise.resolve([]);
        for (var pageNumber = 1; pageNumber <= pdf.numPages; pageNumber++) {
          (function (number) {
            chain = chain.then(function (pages) {
              return pdf.getPage(number).then(function (page) {
                return page.getTextContent().then(function (content) {
                  var lines = [];
                  var current = '';
                  var lastY = null;

                  content.items.forEach(function (item) {
                    var text = item.str || '';
                    var y = item.transform && item.transform.length ? item.transform[5] : null;
                    if (lastY !== null && y !== null && Math.abs(lastY - y) > 8) {
                      if (current.trim()) lines.push(current.trim());
                      current = text;
                    } else {
                      current += (current && !/\s$/.test(current) && text && !/^\s/.test(text) ? ' ' : '') + text;
                    }
                    lastY = y;
                  });
                  if (current.trim()) lines.push(current.trim());

                  pages.push(splitParagraphs(lines.join('\n')));
                  return pages;
                });
              });
            });
          })(pageNumber);
        }
        return chain;
      });
    }

    function loadPdfText(forceConfirm) {
      var option = selectedOption();
      if (!option || !option.value) {
        setStatus('Select a hotel first.', true);
        return;
      }
      if (option.getAttribute('data-has-document') !== '1' || option.getAttribute('data-is-pdf') !== '1') {
        setStatus('Selected hotel has no PDF contract to load.', true);
        return;
      }

      if (forceConfirm && !confirm('Load paragraphs from the hotel PDF into the editor? Current unsaved text will be replaced.')) {
        return;
      }

      var url = option.getAttribute('data-preview-url');
      var fileName = option.getAttribute('data-file-name') || 'hotel PDF';
      setStatus('Reading PDF paragraphs…');
      if (loadBtn) loadBtn.disabled = true;

      extractPdfParagraphs(url).then(function (pages) {
        var total = pages.reduce(function (sum, page) { return sum + page.length; }, 0);
        if (!total) {
          setStatus('No text could be extracted from this PDF (it may be scanned/image-only).', true);
          return;
        }
        if (editor) {
          editor.innerHTML = buildHtmlFromPages(pages, fileName);
          editor.focus();
        }
        setStatus('Loaded ' + total + ' paragraph(s) from ' + pages.length + ' page(s). Edit any text, then save.');
      }).catch(function () {
        setStatus('Could not read text from this PDF. Open the Hotel PDF tab or try Replace whole PDF.', true);
      }).finally(function () {
        if (loadBtn) loadBtn.disabled = false;
      });
    }

    if (loadBtn) {
      loadBtn.addEventListener('click', function () {
        loadPdfText(true);
      });
    }

    if (editor && editor.getAttribute('data-auto-load-pdf') === '1') {
      loadPdfText(false);
    }

    function renderPreview() {
      if (!select || !wrap) return;
      var option = selectedOption();
      if (!option || !option.value) {
        wrap.innerHTML = '<div class="alert alert-warning mb-0">Select a hotel to view its contract PDF.</div>';
        return;
      }

      var hasDocument = option.getAttribute('data-has-document') === '1';
      var isPdf = option.getAttribute('data-is-pdf') === '1';
      var previewUrl = option.getAttribute('data-preview-url');
      var fileName = option.getAttribute('data-file-name') || 'contract';

      if (!hasDocument) {
        wrap.innerHTML = '<div class="alert alert-warning mb-0">No contract PDF on this hotel yet. Upload one under Hotels → Edit, or use Replace whole PDF.</div>';
        return;
      }

      if (isPdf) {
        wrap.innerHTML = '<iframe class="contract-frame" id="contractFrame" src="' + previewUrl + '" title="Hotel contract preview"></iframe>';
        return;
      }

      wrap.innerHTML = '<div class="alert alert-info mb-0">This hotel contract is not a PDF preview. <a href="' + previewUrl + '" target="_blank" rel="noopener">Open / download ' + fileName + '</a></div>';
    }

    function onHotelChange() {
      renderPreview();
      var option = selectedOption();
      var reloadUrl = option && option.getAttribute('data-reload-url');
      if (!reloadUrl) return;
      if (confirm('Reload editable paragraphs from this hotel’s PDF? Unsaved text edits will be replaced.')) {
        window.location.href = reloadUrl;
      }
    }

    if (select) {
      select.addEventListener('change', onHotelChange);
      if (window.jQuery) {
        window.jQuery(select).on('select2:select', onHotelChange);
      }
    }
  })();
</script>
@endpush
