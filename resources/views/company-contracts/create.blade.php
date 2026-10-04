@extends('layouts.app')

@section('title', 'Add contract')
@section('page', 'companies')

@section('content')
  <div class="page-header mb-4">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-1">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('companies.index') }}">Companies</a></li>
        <li class="breadcrumb-item"><a href="{{ route('companies.contracts.index', $company) }}">Contracts</a></li>
        <li class="breadcrumb-item active">Add contract</li>
      </ol>
    </nav>
    <h1 class="page-title">Add contract</h1>
    <p class="page-subtitle mb-0">Upload a PDF contract for {{ $company->name }}. Fields marked <span class="text-danger">*</span> are required.</p>
  </div>

  <form method="POST" action="{{ route('companies.contracts.store', $company) }}" enctype="multipart/form-data" novalidate>
    @csrf
    <div class="card">
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-8">
            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
            <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" maxlength="255" placeholder="Enter contract title">
            @error('title')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-12">
            <label for="pdf" class="form-label">Upload PDF <span class="text-danger">*</span></label>
            <input type="file" name="pdf" id="pdf" accept="application/pdf,.pdf" class="form-control @error('pdf') is-invalid @enderror">
            <div class="form-text">PDF only, up to 10 MB.</div>
            @error('pdf')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-12" id="pdfPreview" hidden>
            <label class="form-label">PDF preview</label>
            <div id="pdfPreviewPages" class="border rounded bg-white p-2" style="height: 480px; overflow-y: auto; overflow-x: hidden;"></div>
          </div>
        </div>
      </div>
    </div>
    <div class="d-flex gap-2 mt-4">
      <button type="submit" class="btn btn-accent">Save contract</button>
      <a href="{{ route('companies.contracts.index', $company) }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
  </form>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
  (function () {
    var input = document.getElementById('pdf');
    var preview = document.getElementById('pdfPreview');
    var pages = document.getElementById('pdfPreviewPages');
    if (!input || !preview || !pages || !window.pdfjsLib) return;

    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

    function showMessage(text) {
      preview.hidden = false;
      pages.innerHTML = '<div class="text-secondary text-center py-5">' + text + '</div>';
    }

    function renderPdf(data) {
      preview.hidden = false;
      pages.innerHTML = '<div class="text-secondary text-center py-5">Loading preview...</div>';

      pdfjsLib.getDocument({ data: data }).promise.then(function (pdf) {
        pages.innerHTML = '';
        var chain = Promise.resolve();

        for (var pageNumber = 1; pageNumber <= pdf.numPages; pageNumber++) {
          (function (number) {
            chain = chain.then(function () {
              return pdf.getPage(number).then(function (page) {
                var width = pages.clientWidth || 800;
                var base = page.getViewport({ scale: 1 });
                var viewport = page.getViewport({ scale: width / base.width });
                var canvas = document.createElement('canvas');
                canvas.width = viewport.width;
                canvas.height = viewport.height;
                canvas.style.width = '100%';
                canvas.style.height = 'auto';
                canvas.style.display = 'block';
                canvas.style.marginBottom = '8px';
                canvas.style.background = '#fff';
                pages.appendChild(canvas);
                return page.render({ canvasContext: canvas.getContext('2d'), viewport: viewport }).promise;
              });
            });
          })(pageNumber);
        }

        return chain;
      }).catch(function () {
        showMessage('Could not preview this PDF.');
      });
    }

    input.addEventListener('change', function () {
      var file = input.files && input.files[0];
      if (!file) {
        preview.hidden = true;
        pages.innerHTML = '';
        return;
      }

      file.arrayBuffer().then(renderPdf).catch(function () {
        showMessage('Could not preview this PDF.');
      });
    });
  })();
</script>
@endpush
