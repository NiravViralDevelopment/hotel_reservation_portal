@props(['paginator'])

@if ($paginator instanceof \Illuminate\Contracts\Pagination\Paginator && $paginator->hasPages())
  <div class="table-footer">
    <span>
      Showing <strong>{{ $paginator->firstItem() ?? 0 }}</strong> to
      <strong>{{ $paginator->lastItem() ?? 0 }}</strong> of
      <strong>{{ $paginator->total() }}</strong> entries
    </span>
    {{ $paginator->links('pagination::bootstrap-5') }}
  </div>
@elseif ($paginator instanceof \Illuminate\Contracts\Pagination\Paginator)
  <div class="table-footer">
    <span>
      Showing <strong>{{ $paginator->count() }}</strong> of
      <strong>{{ $paginator->total() }}</strong> entries
    </span>
  </div>
@endif
