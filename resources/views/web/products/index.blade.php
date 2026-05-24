@extends('web.layouts.app')

@section('title', 'Products')

@section('content')
<div class="container my-5">
    <div class="row">
        <!-- Sidebar Filters -->
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-header  text-white" style="background-color: #dc8423">
                    <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filters</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('products.index') }}">
                        <!-- Search -->
                        <div class="mb-3">
                            <label class="form-label">Search</label>
                            <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search products...">
                        </div>

                        <!-- Category Filter -->
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-select">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Prescription Filter -->
                        <div class="mb-3">
                            <label class="form-label">Prescription</label>
                            <select name="prescription" class="form-select">
                                <option value="">All Products</option>
                                <option value="not_required" {{ request('prescription') == 'not_required' ? 'selected' : '' }}>OTC (No Prescription)</option>
                                <option value="required" {{ request('prescription') == 'required' ? 'selected' : '' }}>Prescription Required</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-2"></i>Apply Filters
                        </button>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary w-100 mt-2">
                            Clear
                        </a>
                    </form>
                </div>
            </div>
        </div>

        <!-- Products -->
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Products</h2>
                <div>
                    <label class="form-label me-2">Sort:</label>
                    <select class="form-select d-inline-block" style="width: auto;" onchange="window.location.href='{{ route('products.index') }}?sort=' + this.value + '&{{ http_build_query(request()->except('sort')) }}'">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                        <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name: A-Z</option>
                    </select>
                </div>
            </div>

            @if($products->count() > 0)
                <div id="productsGrid" class="row g-4">
                    @include('web.products.partials.product-cards', ['products' => $products])
                </div>

                <div id="loadMoreWrap" class="mt-4 text-center {{ $products->hasMorePages() ? '' : 'd-none' }}">
                    <button type="button" id="loadMoreBtn" class="btn btn-primary px-5" data-next-page="{{ $products->currentPage() + 1 }}">
                        <i class="fas fa-plus-circle me-2"></i>Load More
                    </button>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                    <h4>No products found</h4>
                    <p class="text-muted">Try adjusting your filters</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var loadMoreBtn = document.getElementById('loadMoreBtn');
    var productsGrid = document.getElementById('productsGrid');
    var loadMoreWrap = document.getElementById('loadMoreWrap');

    if (!loadMoreBtn || !productsGrid) return;

    loadMoreBtn.addEventListener('click', function () {
        var btn = this;
        var nextPage = btn.dataset.nextPage;
        if (!nextPage || btn.disabled) return;

        var url = new URL(window.location.href);
        url.searchParams.set('page', nextPage);

        btn.disabled = true;
        var originalHtml = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Loading...';

        fetch(url.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
        })
            .then(function (response) {
                if (!response.ok) throw new Error('Failed to load products');
                return response.json();
            })
            .then(function (data) {
                if (data.html) {
                    productsGrid.insertAdjacentHTML('beforeend', data.html);
                }
                if (data.has_more && data.next_page) {
                    btn.dataset.nextPage = data.next_page;
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                } else {
                    loadMoreWrap.classList.add('d-none');
                }
            })
            .catch(function () {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
                alert('Could not load more products. Please try again.');
            });
    });
})();
</script>
@endpush
