{{--
    Per Page Selector Component
    Use in any view that has pagination
    
    Usage:
    <x-per-page-selector perPage="$perPage" />
--}}
@props(['perPage' => 15, 'route' => null])

<div class="per-page-selector d-flex align-items-center">
    <label for="per_page" class="me-2 mb-0 text-muted small">Show:</label>
    <select name="per_page" id="per_page" class="form-select form-select-sm" style="width: auto;" onchange="if(this.value){ @if($route) window.location='{{ $route }}?per_page='+this.value; @else this.form.submit(); @endif }">
        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
        <option value="15" {{ $perPage == 15 ? 'selected' : '' }}>15</option>
        <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
    </select>
    <span class="ms-2 text-muted small">entries</span>
</div>

<style>
.per-page-selector select.form-select {
    border-radius: 4px;
    cursor: pointer;
}
.per-page-selector select.form-select:hover {
    border-color: #adb5bd;
}
.per-page-selector select.form-select:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.1rem rgba(13, 110, 253, 0.25);
}
</style>
